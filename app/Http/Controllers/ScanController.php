<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\MasterItem;
use App\Models\Asset;
use App\Models\Receiving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ScanController extends Controller
{
    /**
     * Display the scanner page.
     */
    public function index()
    {
        $recentScans = Scan::with('scanner')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($s) => [
                'id'            => $s->id,
                'serial_number' => $s->serial_number,
                'scan_type'     => $s->scan_type,
                'result'        => $s->result,
                'scanner_name'  => $s->scanner?->name ?? 'System',
                'scanned_at'    => $s->scanned_at?->toISOString(),
            ]);

        return inertia('Scanner/Index', [
            'recentScans' => $recentScans,
        ]);
    }

    /**
     * Look up a serial number against all master items.
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'serial_number' => 'required|string|max:255',
        ]);

        $serialNumber = trim($request->serial_number);

        // 1. Search master_items by serial_numbers field
        $matches = MasterItem::where(function ($q) use ($serialNumber) {
                $q->where('serial_numbers', $serialNumber)
                  ->orWhere('serial_numbers', 'LIKE', "%{$serialNumber}%");
            })
            ->with('supplier')
            ->orderByRaw("CASE status
                WHEN 'verified' THEN 3
                WHEN 'diterima' THEN 2
                WHEN 'belum_diterima' THEN 1
                ELSE 0 END DESC")
            ->orderBy('id', 'DESC')
            ->get()
            ->map(fn ($item) => [
                'id'              => $item->id,
                'item_no'         => $item->item_no,
                'name'            => $item->name,
                'brand'           => $item->brand,
                'serial_numbers'  => $item->serial_numbers,
                'quantity'        => $item->quantity,
                'unit_price'      => (float) $item->unit_price,
                'supplier_name'   => $item->supplier?->name ?? 'N/A',
                'status'          => $item->status,
                'category'        => $item->category,
                'has_asset'       => $item->asset()->exists(),
            ]);

        // 2. Check if this S/N exists in assets (by serial_number OR asset_tag)
        $existingAsset = Asset::where('serial_number', $serialNumber)
            ->orWhere('asset_tag', $serialNumber)
            ->first();

        // 3. If scanned value looks like an asset tag (AST-...) also try matching
        //    directly in case step 1 didn't catch it
        if (str_starts_with($serialNumber, 'AST-') && !$existingAsset) {
            $existingAsset = Asset::where('asset_tag', $serialNumber)->first();
        }

        return response()->json([
            'serial_number'  => $serialNumber,
            'matches'        => $matches,
            'match_count'    => $matches->count(),
            'existing_asset' => $existingAsset ? [
                'id'        => $existingAsset->id,
                'asset_tag' => $existingAsset->asset_tag,
                'name'      => $existingAsset->name,
                'status'    => $existingAsset->status,
            ] : null,
        ]);
    }

    /**
     * Record a scan result.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'serial_number'  => 'required|string|max:255',
            'result'         => 'required|in:match,mismatch,new,duplicate',
            'master_item_id' => 'nullable|exists:master_items,id',
            'asset_id'       => 'nullable|exists:assets,id',
            'location'       => 'nullable|string|max:255',
            'notes'          => 'nullable|string',
        ]);

        $scan = Scan::create([
            'serial_number'   => $request->serial_number,
            'scan_type'       => 'receive',
            'scanner_user_id' => auth()->id(),
            'scanned_at'      => now(),
            'master_item_id'  => $request->master_item_id,
            'asset_id'        => $request->asset_id,
            'location'        => $request->location,
            'result'          => $request->result,
            'notes'           => $request->notes,
        ]);

        $response = [
            'scan' => [
                'id'            => $scan->id,
                'serial_number' => $scan->serial_number,
                'result'        => $scan->result,
                'scanned_at'    => $scan->scanned_at->toISOString(),
            ],
            'message' => $this->resultMessage($request->result),
        ];

        return response()->json($response);
    }

    /**
     * Get scan history with pagination.
     */
    public function history(Request $request)
    {
        $query = Scan::with('scanner', 'masterItem', 'asset')
            ->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where('serial_number', 'ILIKE', "%{$search}%");
        }

        if ($request->result) {
            $query->where('result', $request->result);
        }

        $scans = $query->paginate(30)
            ->withQueryString()
            ->through(fn ($s) => [
                'id'             => $s->id,
                'serial_number'  => $s->serial_number,
                'scan_type'      => $s->scan_type,
                'result'         => $s->result,
                'scanner_name'   => $s->scanner?->name ?? 'System',
                'scanned_at'     => $s->scanned_at?->toISOString(),
                'item_no'        => $s->masterItem?->item_no,
                'item_name'      => $s->masterItem?->name,
                'asset_tag'      => $s->asset?->asset_tag,
                'location'       => $s->location,
            ]);

        return inertia('Scanner/History', [
            'scans'   => $scans,
            'filters' => $request->only(['search', 'result']),
        ]);
    }

    /**
     * Verify a master item directly from a scan result.
     * Creates PA-2/PA-3 (Asset) — PA-1 is already created at receive time.
     */
    public function verifyFromScan(Request $request, MasterItem $masterItem)
    {
        if ($masterItem->status !== 'diterima') {
            return response()->json([
                'success' => false,
                'message' => $masterItem->status === 'belum_diterima'
                    ? 'This item has not been received yet. Scan to receive first.'
                    : 'This item is already verified.',
            ], 422);
        }

        $request->validate([
            'location'       => 'nullable|string|max:255',
            'custodian_name' => 'nullable|string|max:255',
            'warranty_expiry'=> 'nullable|date',
        ]);

        $threshold = 2000;
        $user = auth()->user();

        DB::transaction(function () use ($masterItem, $request, $threshold, $user) {
            // 1. Update master item
            $assetType = $masterItem->unit_price >= $threshold ? 'fixed_asset' : 'inventory';
            $masterItem->update([
                'status'        => 'verified',
                'verified_at'   => now(),
                'verified_by'   => $user->id,
                'asset_type'    => $assetType,
            ]);

            // 2. Parse serial numbers from master item
            $serials = collect(explode(',', $masterItem->serial_numbers ?? ''))
                ->map(fn($s) => trim($s))
                ->filter(fn($s) => $s !== '')
                ->values()
                ->toArray();

            $commonLocation = $request->location ?? 'TBD';
            $custodianName = $request->custodian_name;

            // 3. Create assets with atomic tag generation
            $tagLock = Cache::lock('asset-tag', 10);
            $tagLock->block(5);
            try {
                $today = date('Ymd');
                $maxSeq = DB::table('assets')
                    ->where('asset_tag', 'LIKE', "AST-{$today}-%")
                    ->max(DB::raw("CAST(SUBSTRING(asset_tag FROM 'AST-[0-9]{8}-([0-9]+)') AS INTEGER)"));

                $seqNo = ($maxSeq ?? 0) + 1;

                if (count($serials) > 1) {
                    foreach ($serials as $sn) {
                        $assetTag = "AST-{$today}-" . str_pad($seqNo, 6, '0', STR_PAD_LEFT);
                        $seqNo++;

                        Asset::create([
                            'master_item_id'   => $masterItem->id,
                            'asset_tag'        => $assetTag,
                            'name'             => $masterItem->name,
                            'category'         => $masterItem->category,
                            'brand'            => $masterItem->brand,
                            'serial_number'    => $sn,
                            'asset_type'       => $assetType,
                            'purchase_price'   => $masterItem->unit_price,
                            'unit_price'       => $masterItem->unit_price,
                            'quantity'         => 1,
                            'location'         => $commonLocation,
                            'custodian_name'   => $custodianName,
                            'status'           => 'active',
                            'supplier_name'    => $masterItem->supplier?->name,
                            'received_date'    => $masterItem->received_date,
                            'warranty_expiry'  => $request->warranty_expiry,
                            'verified_by_scan' => true,
                            'scan_verified_at' => now(),
                        ]);
                    }
                } else {
                    $assetTag = "AST-{$today}-" . str_pad($seqNo, 6, '0', STR_PAD_LEFT);

                    Asset::create([
                        'master_item_id'   => $masterItem->id,
                        'asset_tag'        => $assetTag,
                        'name'             => $masterItem->name,
                        'category'         => $masterItem->category,
                        'brand'            => $masterItem->brand,
                        'serial_number'    => $serials[0] ?? $masterItem->serial_numbers,
                        'asset_type'       => $assetType,
                        'purchase_price'   => $masterItem->unit_price,
                        'unit_price'       => $masterItem->unit_price,
                        'quantity'         => $masterItem->quantity,
                        'location'         => $commonLocation,
                        'custodian_name'   => $custodianName,
                        'status'           => 'active',
                        'supplier_name'    => $masterItem->supplier?->name,
                        'received_date'    => $masterItem->received_date,
                        'warranty_expiry'  => $request->warranty_expiry,
                        'verified_by_scan' => true,
                        'scan_verified_at' => now(),
                    ]);
                }
            } finally {
                $tagLock->release();
            }
        });

        return response()->json([
            'success'   => true,
            'message'   => "{$masterItem->name} — verified and asset created.",
            'asset_tag' => 'AST-' . date('Ymd') . '-' . str_pad(Asset::whereDate('created_at', today())->count(), 6, '0', STR_PAD_LEFT),
            'item_no'   => $masterItem->item_no,
            'name'      => $masterItem->name,
        ]);
    }

    /**
     * Quick receive from scanner — creates PA-1 immediately.
     */
    public function receiveFromScan(Request $request, MasterItem $masterItem)
    {
        if ($masterItem->status !== 'belum_diterima') {
            return response()->json([
                'success' => false,
                'message' => 'This item has already been received.',
            ], 422);
        }

        $request->validate([
            'receiver_name'  => 'nullable|string|max:255',
            'received_date'  => 'nullable|date',
            'do_no'          => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $masterItem) {
            $masterItem->update([
                'status'        => 'diterima',
                'received_date' => $request->received_date ?? now(),
                'do_no'         => $request->do_no ?? $masterItem->do_no,
            ]);

            $recvLock = Cache::lock('receiving-number', 10);
            $recvLock->block(5);
            try {
                $receiveNo = 'REC-' . date('Ymd') . '-' . str_pad(
                    Receiving::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT
                );
            } finally {
                $recvLock->release();
            }

            Receiving::create([
                'master_item_id'    => $masterItem->id,
                'receive_no'        => $receiveNo,
                'supplier_name'     => $masterItem->supplier?->name ?? 'N/A',
                'supplier_address'  => $masterItem->supplier?->address,
                'delivery_order_no' => $request->do_no ?? $masterItem->do_no ?? 'N/A',
                'purchase_order_no' => 'N/A',
                'item_description'  => $masterItem->name,
                'quantity_ordered'  => $masterItem->quantity,
                'quantity_received' => $masterItem->quantity,
                'unit_price'        => $masterItem->unit_price,
                'total_price'       => $masterItem->total_price,
                'status'            => 'accepted',
                'receiver_name'     => $request->receiver_name,
            ]);
        });

        return response()->json([
            'success'    => true,
            'message'    => "{$masterItem->name} — received and PA-1 created.",
            'item_no'    => $masterItem->item_no,
            'name'       => $masterItem->name,
            'status'     => 'diterima',
            'receive_no' => 'REC-' . date('Ymd') . '-' . str_pad(
                Receiving::whereDate('created_at', today())->count(), 4, '0', STR_PAD_LEFT
            ),
        ]);
    }

    private function resultMessage(string $result): string
    {
        return match ($result) {
            'match'     => 'Match found in master equipment list!',
            'mismatch'  => 'No matching item found for this serial number.',
            'duplicate' => 'This serial number has already been scanned.',
            'new'       => 'New serial number recorded.',
            default     => 'Scan recorded.',
        };
    }
}
