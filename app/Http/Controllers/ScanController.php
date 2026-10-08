<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\MasterItem;
use App\Models\Asset;
use Illuminate\Http\Request;

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
