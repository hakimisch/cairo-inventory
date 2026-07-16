<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $search    = $request->search;
        $catFilter = $request->category;   // filter by lab category
        $srcFilter = $request->source;     // 'diterima' | 'belum_diterima'
        $dateFrom  = $request->date_from;  // date range filter — created_at >= date_from
        $dateTo    = $request->date_to;    // date range filter — created_at <= date_to
        $perPage   = 30;

        // ── 1. Master Items (primary source — all 156 items) ──────────────────
        $query = MasterItem::with('supplier');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('item_no', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('category', 'ILIKE', "%{$search}%")
                  ->orWhere('specification', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_numbers', 'ILIKE', "%{$search}%")
                  ->orWhere('do_no', 'ILIKE', "%{$search}%");
            });
        }

        if ($catFilter) {
            $query->where('category', $catFilter);
        }

        if ($srcFilter === 'diterima') {
            $query->where('status', 'diterima');
        } elseif ($srcFilter === 'belum_diterima') {
            $query->where('status', 'belum_diterima');
        }

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }
        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $masterItems = $query->latest('item_no')->get()->map(fn ($m) => [
            // ── Identification ────────────────────────────────────────────────
            'id'          => 'mi-' . $m->id,
            'source'      => $m->status === 'diterima' ? 'DO' : 'Master',
            'source_type' => 'master_item',
            'source_id'   => $m->id,

            // ── Excel fields ──────────────────────────────────────────────────
            'item_no'      => $m->item_no,
            'category'     => $m->category,
            'name'         => $m->name,
            'specification'=> $m->specification,
            'brand'        => $m->brand,
            'model'        => null,                // not in Excel, kept for compatibility
            'serial_no'    => $m->serial_numbers,
            'unit_price'   => (float) $m->unit_price,
            'total_price'  => (float) $m->total_price,
            'sodo_bayaran' => $m->sodo_bayaran,

            // ── Quantities ────────────────────────────────────────────────────
            'quantity'     => $m->quantity,
            'unit'         => 'unit',

            // ── Supplier ──────────────────────────────────────────────────────
            'supplier_id'  => $m->supplier_id,
            'supplier'     => $m->supplier?->name ?? 'N/A',

            // ── Delivery info ─────────────────────────────────────────────────
            'status'        => $m->status,
            'received_date' => $m->received_date?->format('Y-m-d H:i'),
            'verified_at'   => $m->verified_at?->toISOString(),
            'created_at'    => $m->created_at?->toISOString(),
            'updated_at'    => $m->updated_at?->toISOString(),
            'do_no'         => $m->do_no,

            // ── Link ──────────────────────────────────────────────────────────
            'url' => null,    // Belum Diterima items have no DO yet

            // ── Meta ──────────────────────────────────────────────────────────
            'has_serial' => $m->serial_numbers ? true : ($m->serial_numbers === null ? null : false),
        ]);

        // ── 2. Assets (PA-2/PA-3) ─────────────────────────────────────────────
        $assetQuery = Asset::query();
        if ($search) {
            $assetQuery->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                  ->orWhere('asset_tag', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_number', 'ILIKE', "%{$search}%")
                  ->orWhere('supplier_name', 'ILIKE', "%{$search}%");
            });
        }

        $assetItems = $assetQuery->latest()->get()->map(fn ($a) => [
            'id'           => 'asset-' . $a->id,
            'source'       => 'Asset',
            'source_type'  => 'asset',
            'source_id'    => $a->id,

            // ── Excel fields ──────────────────────────────────────────────────
            'item_no'      => $a->asset_tag,     // not the same, but keeps shape
            'category'     => $a->category,
            'name'         => $a->name,
            'specification'=> null,
            'brand'        => $a->brand,
            'model'        => $a->model,
            'serial_no'    => $a->serial_number,
            'unit_price'   => (float) $a->purchase_price,
            'total_price'  => null,
            'sodo_bayaran' => null,

            // ── Quantities ────────────────────────────────────────────────────
            'quantity'     => $a->quantity ?? 1,
            'unit'         => $a->unit_of_measure ?? 'unit',

            // ── Supplier ──────────────────────────────────────────────────────
            'supplier_id'  => null,
            'supplier'     => $a->supplier_name ?? 'N/A',

            // ── Delivery info ─────────────────────────────────────────────────
            'status'        => $a->status,
            'received_date' => $a->received_date?->format('Y-m-d H:i'),
            'verified_at'   => null,
            'do_no'         => $a->po_reference,

            // ── Link ──────────────────────────────────────────────────────────
            'url' => "/assets/{$a->id}/kewpa2",

            // ── Meta ──────────────────────────────────────────────────────────
            'has_serial' => $a->serial_number ? true : null,
            'created_at' => $a->created_at?->toISOString(),
            'updated_at' => $a->updated_at?->toISOString(),
        ]);

        // ── 4. Merge & Sort ────────────────────────────────────────────────────
        // Master items (all 156) + Assets (PA-2/3)
        $allItems = collect()
            ->merge($masterItems)
            ->merge($assetItems);

        // Category filter (applies after merge)
        if ($catFilter) {
            $allItems = $allItems->where('category', $catFilter);
        }

        $allItems = $allItems->sortByDesc('created_at')->values();

        // Paginate
        $page = $request->page ?? 1;
        $total = $allItems->count();
        $items = $allItems->forPage($page, $perPage)->values();

        // ── 5. Aggregate counts ───────────────────────────────────────────────
        $counts = [
            'total'         => $allItems->count(),
            'master'        => $masterItems->count(),
            'diterima'      => $masterItems->where('status', 'diterima')->count(),
            'belum_diterima'=> $masterItems->where('status', 'belum_diterima')->count(),
            'assets'        => $assetItems->count(),
        ];

        // Unique categories for the filter dropdown
        $categories = MasterItem::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return Inertia::render('Items/Index', [
            'items'       => $items,
            'counts'      => $counts,
            'categories'  => $categories,
            'pagination'  => [
                'current_page' => (int) $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => max(1, (int) ceil($total / $perPage)),
            ],
            'filters' => $request->only(['search', 'category', 'source', 'date_from', 'date_to']),
        ]);
    }
}
