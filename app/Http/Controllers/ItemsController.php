<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\DoLineItem;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $sourceFilter = $request->source; // 'po', 'do', 'asset'
        $statusFilter = $request->status;
        $perPage = 30;

        // ── 1. Purchase Order Items ───────────────────────────────────────────
        $poQuery = PurchaseOrderItem::with('purchaseOrder.supplier');
        if ($search) {
            $poQuery->where(function ($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                  ->orWhere('item_code', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%");
            });
        }

        $poItems = $poQuery->latest()->get()->map(fn ($i) => [
            'id'          => 'po-' . $i->id,
            'source'      => 'PO',
            'source_type' => 'purchase_order',
            'source_id'   => $i->purchase_order_id,
            'source_no'   => $i->purchaseOrder?->po_no ?? 'N/A',
            'description' => $i->description,
            'brand'       => $i->brand,
            'model'       => $i->model,
            'serial_no'   => null,
            'quantity'    => $i->quantity_ordered,
            'unit'        => $i->unit,
            'supplier_id' => $i->purchaseOrder?->supplier_id,
            'supplier'    => $i->purchaseOrder?->supplier?->name ?? 'N/A',
            'status'      => $i->status,
            'has_serial'  => null,
            'url'         => $i->purchase_order_id ? "/purchase-orders/{$i->purchase_order_id}" : null,
            'created_at'  => $i->created_at?->toISOString(),
        ]);

        // ── 2. DO Line Items ──────────────────────────────────────────────────
        $doQuery = DoLineItem::with('deliveryOrder.supplier');
        if ($search) {
            $doQuery->where(function ($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                  ->orWhere('item_code', 'ILIKE', "%{$search}%")
                  ->orWhere('brand', 'ILIKE', "%{$search}%")
                  ->orWhere('model', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_number', 'ILIKE', "%{$search}%");
            });
        }

        $doItems = $doQuery->latest()->get()->map(fn ($i) => [
            'id'          => 'do-' . $i->id,
            'source'      => 'DO',
            'source_type' => 'delivery_order',
            'source_id'   => $i->delivery_order_id,
            'source_no'   => $i->deliveryOrder?->do_no ?? 'N/A',
            'description' => $i->description,
            'brand'       => $i->brand,
            'model'       => $i->model,
            'serial_no'   => $i->serial_number,
            'quantity'    => $i->quantity_ordered,
            'unit'        => $i->unit,
            'supplier_id' => $i->deliveryOrder?->supplier_id,
            'supplier'    => $i->deliveryOrder?->supplier?->name ?? 'N/A',
            'status'      => $i->status,
            'has_serial'  => $i->has_serial,
            'url'         => $i->delivery_order_id ? "/delivery-orders/{$i->delivery_order_id}" : null,
            'created_at'  => $i->created_at?->toISOString(),
        ]);

        // ── 3. Assets (PA-2/PA-3) ─────────────────────────────────────────────
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
            'id'          => 'asset-' . $a->id,
            'source'      => 'Asset',
            'source_type' => 'asset',
            'source_id'   => $a->id,
            'source_no'   => $a->asset_tag,
            'description' => $a->name,
            'brand'       => $a->brand,
            'model'       => $a->model,
            'serial_no'   => $a->serial_number,
            'quantity'    => $a->quantity,
            'unit'        => $a->unit_of_measure,
            'supplier_id' => null,
            'supplier'    => $a->supplier_name ?? 'N/A',
            'status'      => $a->status,
            'has_serial'  => $a->serial_number ? true : null,
            'url'         => "/assets/{$a->id}/kewpa2",
            'created_at'  => $a->created_at?->toISOString(),
        ]);

        // ── 4. Merge & Sort ────────────────────────────────────────────────────
        $allItems = collect()
            ->merge($poItems)
            ->merge($doItems)
            ->merge($assetItems);

        // Apply filters
        if ($sourceFilter) {
            $allItems = $allItems->where('source', $sourceFilter);
        }
        if ($statusFilter) {
            $allItems = $allItems->where('status', $statusFilter);
        }

        // Sort by created_at desc
        $allItems = $allItems->sortByDesc('created_at')->values();

        // Paginate manually
        $page = $request->page ?? 1;
        $total = $allItems->count();
        $items = $allItems->forPage($page, $perPage)->values();

        // ── 5. Aggregate counts ───────────────────────────────────────────────
        $counts = [
            'total'  => $allItems->count(),
            'po'     => $poItems->count(),
            'do'     => $doItems->count(),
            'assets' => $assetItems->count(),
        ];

        return Inertia::render('Items/Index', [
            'items'      => $items,
            'counts'     => $counts,
            'pagination' => [
                'current_page' => (int) $page,
                'per_page'     => $perPage,
                'total'        => $total,
                'last_page'    => max(1, (int) ceil($total / $perPage)),
            ],
            'filters' => $request->only(['search', 'source', 'status']),
        ]);
    }
}
