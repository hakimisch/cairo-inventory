<?php

namespace App\Http\Controllers;

use App\Models\MasterItem;
use App\Models\Receiving;
use App\Models\Asset;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VerificationController extends Controller
{
    /**
     * Unified verification dashboard — shows ALL master_items.
     * Filter by status: belum_diterima | diterima | verified | asset
     */
    public function index(Request $request)
    {
        $search    = $request->search;
        $catFilter = $request->category;
        $statusFilter = $request->status; // belum_diterima | diterima | verified
        $dateFrom  = $request->date_from;
        $dateTo    = $request->date_to;
        $perPage   = 30;

        $query = MasterItem::with('supplier', 'asset');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('item_no', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_numbers', 'ILIKE', "%{$search}%")
                  ->orWhere('category', 'ILIKE', "%{$search}%");
            });
        }

        if ($catFilter) {
            $query->where('category', $catFilter);
        }

        if ($statusFilter === 'verified') {
            $query->where('status', 'verified');
        } elseif ($statusFilter === 'diterima') {
            $query->where('status', 'diterima');
        } elseif ($statusFilter === 'belum_diterima') {
            $query->where('status', 'belum_diterima');
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $items = $query->orderBy('item_no')
            ->paginate($perPage)
            ->through(function ($m) {
                return [
                    'id'            => $m->id,
                    'item_no'       => $m->item_no,
                    'category'      => $m->category,
                    'name'          => $m->name,
                    'brand'         => $m->brand,
                    'serial_numbers'=> $m->serial_numbers,
                    'quantity'      => $m->quantity,
                    'unit_price'    => (float) $m->unit_price,
                    'total_price'   => (float) $m->total_price,
                    'supplier'      => $m->supplier?->name ?? 'N/A',
                    'status'        => $m->status,
                    'asset_type'    => $m->asset_type,
                    'created_at'    => $m->created_at?->toISOString(),
                    'received_date' => $m->received_date?->format('Y-m-d H:i'),
                    'verified_at'   => $m->verified_at?->toISOString(),
                    'has_asset'     => $m->relationLoaded('asset') && $m->asset !== null,
                    'asset_id'      => $m->asset?->id,
                    'asset_tag'     => $m->asset?->asset_tag,
                    'custodian_name'=> $m->asset?->custodian_name,
                    'location'      => $m->asset?->location,
                    'model'         => $m->model,
                    'specification' => $m->specification,
                ];
            });

        // Aggregated stats
        $stats = [
            'total'          => MasterItem::count(),
            'belum_diterima' => MasterItem::where('status', 'belum_diterima')->count(),
            'diterima'       => MasterItem::where('status', 'diterima')->count(),
            'verified'       => MasterItem::where('status', 'verified')->count(),
            'with_asset'     => MasterItem::whereHas('asset')->count(),
        ];

        $categories = MasterItem::select('category')->distinct()->orderBy('category')->pluck('category');

        // ── Registry data (master items + assets for Registry tab) ─────────
        $registryMaster = MasterItem::with('supplier')
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('item_no', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('category', 'ILIKE', "%{$search}%")
                  ->orWhere('specification', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_numbers', 'ILIKE', "%{$search}%")
                  ->orWhere('do_no', 'ILIKE', "%{$search}%");
            }))
            ->latest('item_no')
            ->get()
            ->map(fn($m) => [
                'id'           => 'mi-'.$m->id,
                'source'       => $m->status === 'diterima' ? 'DO' : 'Master',
                'item_no'      => $m->item_no,
                'category'     => $m->category,
                'name'         => $m->name,
                'specification'=> $m->specification,
                'brand'        => $m->brand,
                'serial_no'    => $m->serial_numbers,
                'unit_price'   => (float) $m->unit_price,
                'total_price'  => (float) $m->total_price,
                'sodo_bayaran' => $m->sodo_bayaran,
                'quantity'     => $m->quantity,
                'supplier'     => $m->supplier?->name ?? 'N/A',
                'status'       => $m->status,
                'received_date'=> $m->received_date?->format('Y-m-d H:i'),
                'verified_at'  => $m->verified_at?->toISOString(),
                'created_at'   => $m->created_at?->toISOString(),
                'do_no'        => $m->do_no,
                'url'          => null,
            ]);

        $registryAssets = Asset::query()
            ->when($search, fn($q) => $q->where(function($q2) use ($search) {
                $q2->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('asset_tag', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_number', 'ILIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'ILIKE', "%{$search}%");
            }))
            ->latest()
            ->get()
            ->map(fn($a) => [
                'id'           => 'asset-'.$a->id,
                'source'       => 'Asset',
                'item_no'      => $a->asset_tag,
                'category'     => $a->category,
                'name'         => $a->name,
                'specification'=> null,
                'brand'        => $a->brand,
                'serial_no'    => $a->serial_number,
                'unit_price'   => (float) $a->purchase_price,
                'total_price'  => null,
                'sodo_bayaran' => null,
                'quantity'     => $a->quantity ?? 1,
                'supplier'     => $a->supplier_name ?? 'N/A',
                'status'       => $a->status,
                'received_date'=> $a->received_date?->format('Y-m-d H:i'),
                'verified_at'  => null,
                'created_at'   => $a->created_at?->toISOString(),
                'do_no'        => $a->po_reference,
                'url'          => "/assets/{$a->id}/kewpa2",
            ]);

        $registryAll = collect()
            ->merge($registryMaster)
            ->merge($registryAssets)
            ->sortByDesc('created_at')
            ->values();

        $registryCounts = [
            'total'          => $registryAll->count(),
            'master'         => $registryMaster->count(),
            'diterima'       => $registryMaster->where('status', 'diterima')->count(),
            'belum_diterima' => $registryMaster->where('status', 'belum_diterima')->count(),
            'assets'         => $registryAssets->count(),
        ];

        return Inertia::render('Verification/Index', [
            'items'           => $items,
            'stats'           => $stats,
            'categories'      => $categories,
            'filters'         => $request->only(['search', 'category', 'status', 'date_from', 'date_to', 'scan', 'highlight']),
            'registry_items'  => $registryAll,
            'registry_counts' => $registryCounts,
        ]);
    }

    /**
     * Mark a master item as received (belum_diterima → diterima).
     * Does NOT create assets — that happens at verify time.
     */
    public function receive(Request $request, MasterItem $masterItem)
    {
        $request->validate([
            'received_date'             => 'nullable|date',
            'do_no'                     => 'nullable|string|max:255',
            'purchase_order_no'         => 'nullable|string|max:100',
            'quantity_received'         => 'nullable|integer|min:1',
            'receiver_name'             => 'nullable|string|max:255',
            'receiver_position'         => 'nullable|string|max:255',
            'technical_officer_name'    => 'nullable|string|max:255',
            'technical_officer_position'=> 'nullable|string|max:255',
            'damage_description'        => 'nullable|string|max:5000',
            'notes'                     => 'nullable|string|max:5000',
            'invoice_no'                => 'nullable|string|max:100',
        ]);

        if ($masterItem->status !== 'belum_diterima') {
            return back()->withErrors(['item' => 'This item has already been received.']);
        }

        DB::transaction(function () use ($request, $masterItem) {
            $masterItem->update([
                'status'        => 'diterima',
                'received_date' => $request->received_date ?? now(),
                'do_no'         => $request->do_no ?? $masterItem->do_no,
                'invoice_no'    => $request->invoice_no,
            ]);

            // Create PA-1 Receiving record immediately at receive time
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
                'master_item_id'            => $masterItem->id,
                'receive_no'                => $receiveNo,
                'supplier_name'             => $masterItem->supplier?->name ?? 'N/A',
                'supplier_address'          => $masterItem->supplier?->address,
                'delivery_order_no'         => $request->do_no ?? $masterItem->do_no ?? 'N/A',
                'purchase_order_no'         => $request->purchase_order_no ?? 'N/A',
                'invoice_no'                => $request->invoice_no,
                'item_description'          => $masterItem->name,
                'quantity_ordered'          => $masterItem->quantity,
                'quantity_received'         => $request->quantity_received ?? $masterItem->quantity,
                'unit_price'                => $masterItem->unit_price,
                'total_price'               => $masterItem->total_price,
                'status'                    => 'accepted',
                'receiver_name'             => $request->receiver_name,
                'receiver_position'         => $request->receiver_position,
                'technical_officer_name'    => $request->technical_officer_name,
                'technical_officer_position'=> $request->technical_officer_position,
                'damage_description'        => $request->damage_description,
                'notes'                     => $request->notes,
            ]);
        });

        return redirect()->back()->with('success', "{$masterItem->name} — received and PA-1 created.");
    }

    /**
     * Verify a master item → mark as verified, create PA-1 (Receiving),
     * then auto-create PA-2 or PA-3 (Asset) based on unit_price.
     */
    public function verify(Request $request, MasterItem $masterItem)
    {
        $request->validate([
            'serial_numbers' => 'nullable|string',
            'received_date'  => 'nullable|date',
            'location'       => 'nullable|string|max:255',
            'custodian_name' => 'nullable|string|max:255',
            'warranty_expiry'=> 'nullable|date',
            'budget_vot'     => 'nullable|string|max:100',
            'voucher_no'     => 'nullable|string|max:100',
        ]);

        // Don't re-verify already-verified items, and don't bypass receive step
        if ($masterItem->status === 'verified') {
            return back()->withErrors(['item' => 'This item is already verified.']);
        }
        if ($masterItem->status !== 'diterima') {
            return back()->withErrors(['item' => 'This item must be received before verification.']);
        }

        DB::transaction(function () use ($request, $masterItem) {
            $user = auth()->user();
            $threshold = 2000; // RM 2,000 — PA-2 vs PA-3 boundary

            // 1. Update master item
            $masterItem->update([
                'status'        => 'verified',
                'verified_at'   => now(),
                'verified_by'   => $user->id,
                'asset_type'    => $masterItem->unit_price >= $threshold ? 'fixed_asset' : 'inventory',
                'serial_numbers'=> $request->serial_numbers ?? $masterItem->serial_numbers,
            ]);

            // 2. Parse serial numbers — use the request value or fall back to stored
            $serials = collect(explode(',', $request->serial_numbers ?? $masterItem->serial_numbers ?? ''))
                ->map(fn($s) => trim($s))
                ->filter(fn($s) => $s !== '')
                ->values()
                ->toArray();

            $assetType = $masterItem->unit_price >= $threshold ? 'fixed_asset' : 'inventory';
            $commonLocation = $request->location ?? 'TBD';
            $custodianName = $request->custodian_name;

            // Atomically reserve asset tags to prevent race conditions
            $tagLock = Cache::lock('asset-tag', 10);
            $tagLock->block(5);
            try {
                $today = date('Ymd');
                $maxSeq = DB::table('assets')
                    ->where('asset_tag', 'LIKE', "AST-{$today}-%")
                    ->max(DB::raw("CAST(SUBSTR(asset_tag, 14) AS INTEGER)"));

                $seqNo = ($maxSeq ?? 0) + 1;

                if (count($serials) > 1) {
                    // Per-S/N split: one asset per serial number
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
                            'budget_vot'       => $request->budget_vot,
                            'voucher_no'       => $request->voucher_no,
                            'status'           => 'active',
                            'supplier_name'    => $masterItem->supplier?->name,
                            'received_date'    => $masterItem->received_date,
                            'warranty_expiry'  => $request->warranty_expiry,
                            'verified_by_scan' => false,
                        ]);
                    }
                } else {
                    // Single asset (with or without a serial number)
                    $assetTag = "AST-{$today}-" . str_pad($seqNo, 6, '0', STR_PAD_LEFT);

                    Asset::create([
                        'master_item_id'   => $masterItem->id,
                        'asset_tag'        => $assetTag,
                        'name'             => $masterItem->name,
                        'category'         => $masterItem->category,
                        'brand'            => $masterItem->brand,
                        'serial_number'    => $serials[0] ?? $request->serial_numbers ?? $masterItem->serial_numbers,
                        'asset_type'       => $assetType,
                        'purchase_price'   => $masterItem->unit_price,
                        'unit_price'       => $masterItem->unit_price,
                        'quantity'         => $masterItem->quantity,
                        'location'         => $commonLocation,
                        'custodian_name'   => $custodianName,
                        'budget_vot'       => $request->budget_vot,
                        'voucher_no'       => $request->voucher_no,
                        'status'           => 'active',
                        'supplier_name'    => $masterItem->supplier?->name,
                        'received_date'    => $masterItem->received_date,
                        'warranty_expiry'  => $request->warranty_expiry,
                        'verified_by_scan' => false,
                    ]);
                }
            } finally {
                $tagLock->release();
            }
        });

        return redirect()->back()->with('success', "{$masterItem->name} — verified successfully.");
    }

    /**
     * Quick summary stats (for dashboard widget / AJAX).
     */
    public function stats()
    {
        return response()->json([
            'total'          => MasterItem::count(),
            'belum_diterima' => MasterItem::where('status', 'belum_diterima')->count(),
            'diterima'       => MasterItem::where('status', 'diterima')->count(),
            'verified'       => MasterItem::where('status', 'verified')->count(),
            'with_asset'     => MasterItem::whereHas('asset')->count(),
            'total_value'    => (float) MasterItem::sum('total_price'),
        ]);
    }
}
