<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DoLineItem;
use App\Models\Supplier;
use App\Models\Asset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryOrderController extends Controller
{
    /**
     * Display a paginated, searchable list of Delivery Orders.
     */
    public function index(Request $request)
    {
        $query = DeliveryOrder::with('supplier', 'creator')
            ->withCount('lineItems')
            ->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('do_no', 'ILIKE', "%{$search}%")
                  ->orWhere('po_reference', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $deliveryOrders = $query->paginate(20)
            ->withQueryString()
            ->through(function ($do) {
                $lineItems = $do->lineItems;
                return [
                    'id'                => $do->id,
                    'do_no'             => $do->do_no,
                    'supplier_name'     => $do->supplier?->name ?? 'N/A',
                    'supplier_id'       => $do->supplier_id,
                    'ack_date'          => $do->ack_date?->toDateString(),
                    'po_reference'      => $do->po_reference,
                    'status'            => $do->status,
                    'line_items_count'  => $do->line_items_count,
                    'received_count'    => $lineItems->where('status', 'received')->count(),
                    'created_by_name'   => $do->creator?->name ?? 'System',
                    'created_at'        => $do->created_at?->toISOString(),
                ];
            });

        return inertia('DeliveryOrders/Index', [
            'deliveryOrders' => $deliveryOrders,
            'filters'        => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new Delivery Order.
     */
    public function create()
    {
        return inertia('DeliveryOrders/Create', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created Delivery Order.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'do_no'        => 'required|string|max:100|unique:delivery_orders',
            'supplier_id'  => 'required|exists:suppliers,id',
            'ack_date'     => 'nullable|date',
            'po_reference' => 'nullable|string|max:100',
            'sales_rep'    => 'nullable|string|max:100',
            'terms'        => 'nullable|string|max:100',
            'total_pages'  => 'nullable|integer|min:1',
            'notes'        => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'pending';

        $do = DeliveryOrder::create($validated);

        // Handle document upload via Spatie Media Library
        if ($request->hasFile('document')) {
            $do->addMedia($request->file('document'))
               ->toMediaCollection('do_documents');
        }

        return redirect()->route('delivery-orders.show', $do)
            ->with('success', 'Delivery Order created successfully.');
    }

    /**
     * Display the specified Delivery Order with its line items.
     */
    public function show(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load(['supplier', 'creator', 'lineItems' => function ($q) {
            $q->latest();
        }]);

        $totalItems = $deliveryOrder->lineItems->sum('quantity_ordered');
        $receivedItems = $deliveryOrder->lineItems->sum('quantity_received');
        $progress = $totalItems > 0 ? round(($receivedItems / $totalItems) * 100) : 0;

        return inertia('DeliveryOrders/Show', [
            'deliveryOrder' => [
                'id'             => $deliveryOrder->id,
                'do_no'          => $deliveryOrder->do_no,
                'supplier_name'  => $deliveryOrder->supplier?->name ?? 'N/A',
                'supplier_id'    => $deliveryOrder->supplier_id,
                'ack_date'       => $deliveryOrder->ack_date?->toDateString(),
                'po_reference'   => $deliveryOrder->po_reference,
                'sales_rep'      => $deliveryOrder->sales_rep,
                'terms'          => $deliveryOrder->terms,
                'total_pages'    => $deliveryOrder->total_pages,
                'status'         => $deliveryOrder->status,
                'notes'          => $deliveryOrder->notes,
                'created_by_name'=> $deliveryOrder->creator?->name ?? 'System',
                'created_at'     => $deliveryOrder->created_at?->toISOString(),
                'updated_at'     => $deliveryOrder->updated_at?->toISOString(),
                'documents'      => $deliveryOrder->getMedia('do_documents')->map(fn ($m) => [
                    'id'   => $m->id,
                    'name' => $m->name,
                    'url'  => $m->getUrl(),
                    'size' => $m->size,
                ]),
                'line_items'     => $deliveryOrder->lineItems->map(fn ($item) => [
                    'id'                => $item->id,
                    'category'          => $item->category,
                    'item_code'         => $item->item_code,
                    'description'       => $item->description,
                    'brand'             => $item->brand,
                    'model'             => $item->model,
                    'serial_number'     => $item->serial_number,
                    'quantity_ordered'  => $item->quantity_ordered,
                    'quantity_received' => $item->quantity_received,
                    'unit'              => $item->unit,
                    'status'            => $item->status,
                    'scanner_name'      => $item->scanner?->name ?? null,
                    'scanned_at'        => $item->scanned_at?->toISOString(),
                    'notes'             => $item->notes,
                ]),
                'total_ordered'  => $totalItems,
                'total_received' => $receivedItems,
                'progress_pct'   => $progress,
            ],
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified Delivery Order.
     */
    public function edit(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('supplier');
        return inertia('DeliveryOrders/Edit', [
            'deliveryOrder' => $deliveryOrder,
            'suppliers'     => Supplier::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified Delivery Order.
     */
    public function update(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'do_no'        => 'required|string|max:100|unique:delivery_orders,do_no,' . $deliveryOrder->id,
            'supplier_id'  => 'required|exists:suppliers,id',
            'ack_date'     => 'nullable|date',
            'po_reference' => 'nullable|string|max:100',
            'sales_rep'    => 'nullable|string|max:100',
            'terms'        => 'nullable|string|max:100',
            'total_pages'  => 'nullable|integer|min:1',
            'status'       => 'nullable|in:pending,partial,complete,verified',
            'notes'        => 'nullable|string',
        ]);

        $deliveryOrder->update($validated);

        // Handle document upload
        if ($request->hasFile('document')) {
            $deliveryOrder->clearMediaCollection('do_documents');
            $deliveryOrder->addMedia($request->file('document'))
               ->toMediaCollection('do_documents');
        }

        return redirect()->route('delivery-orders.show', $deliveryOrder)
            ->with('success', 'Delivery Order updated successfully.');
    }

    /**
     * Remove the specified Delivery Order.
     */
    public function destroy(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->clearMediaCollection('do_documents');
        $deliveryOrder->delete();

        return redirect()->route('delivery-orders.index')
            ->with('success', 'Delivery Order deleted successfully.');
    }

    /**
     * Verification Dashboard — aggregated stats across all DOs.
     */
    public function dashboard()
    {
        $deliveryOrders = DeliveryOrder::with('supplier')
            ->withCount('lineItems')
            ->withSum('lineItems', 'quantity_ordered')
            ->withSum('lineItems', 'quantity_received')
            ->latest()
            ->get()
            ->map(fn ($do) => [
                'id'               => $do->id,
                'do_no'            => $do->do_no,
                'supplier_name'    => $do->supplier?->name ?? 'N/A',
                'ack_date'         => $do->ack_date?->toDateString(),
                'po_reference'     => $do->po_reference,
                'status'           => $do->status,
                'line_items_count' => $do->line_items_count,
                'total_ordered'    => (int) ($do->line_items_sum_quantity_ordered ?? 0),
                'total_received'   => (int) ($do->line_items_sum_quantity_received ?? 0),
                'progress_pct'     => $do->line_items_sum_quantity_ordered > 0
                    ? round(($do->line_items_sum_quantity_received / $do->line_items_sum_quantity_ordered) * 100)
                    : 0,
                'created_at'       => $do->created_at?->toISOString(),
            ]);

        $totals = [
            'total_dos'        => $deliveryOrders->count(),
            'total_ordered'    => $deliveryOrders->sum('total_ordered'),
            'total_received'   => $deliveryOrders->sum('total_received'),
            'overall_progress' => $deliveryOrders->sum('total_ordered') > 0
                ? round(($deliveryOrders->sum('total_received') / $deliveryOrders->sum('total_ordered')) * 100)
                : 0,
            'pending_count'    => $deliveryOrders->whereIn('status', ['pending', 'partial'])->count(),
            'complete_count'   => $deliveryOrders->where('status', 'complete')->count(),
            'verified_count'   => $deliveryOrders->where('status', 'verified')->count(),
        ];

        return inertia('DeliveryOrders/Verification', [
            'deliveryOrders' => $deliveryOrders,
            'totals'         => $totals,
        ]);
    }

    /**
     * Show the batch import form for a Delivery Order.
     */
    public function batchImportForm(DeliveryOrder $deliveryOrder)
    {
        $deliveryOrder->load('supplier');
        return inertia('DeliveryOrders/BatchImport', [
            'deliveryOrder' => [
                'id'            => $deliveryOrder->id,
                'do_no'         => $deliveryOrder->do_no,
                'supplier_name' => $deliveryOrder->supplier?->name ?? 'N/A',
                'po_reference'  => $deliveryOrder->po_reference,
            ],
        ]);
    }

    /**
     * Batch import line items from pasted/copied DO data.
     */
    public function batchImport(Request $request, DeliveryOrder $deliveryOrder)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.description'      => 'required|string',
            'items.*.category'         => 'nullable|string',
            'items.*.item_code'        => 'nullable|string',
            'items.*.brand'            => 'nullable|string',
            'items.*.model'            => 'nullable|string',
            'items.*.serial_number'    => 'nullable|string',
            'items.*.quantity_ordered' => 'nullable|integer|min:1',
            'items.*.unit'             => 'nullable|string',
        ]);

        foreach ($request->items as $item) {
            $deliveryOrder->lineItems()->create([
                'category'         => $item['category'] ?? null,
                'item_code'        => $item['item_code'] ?? null,
                'description'      => $item['description'],
                'brand'            => $item['brand'] ?? null,
                'model'            => $item['model'] ?? null,
                'serial_number'    => $item['serial_number'] ?? null,
                'quantity_ordered' => $item['quantity_ordered'] ?? 1,
                'unit'             => $item['unit'] ?? 'unit',
                'status'           => 'pending',
            ]);
        }

        // Update DO status if needed
        if ($deliveryOrder->status === 'pending') {
            $deliveryOrder->update(['status' => 'partial']);
        }

        return redirect()->route('delivery-orders.show', $deliveryOrder)
            ->with('success', count($request->items) . ' line items imported successfully.');
    }

    /**
     * Verify a line item — mark as received and optionally create an Asset.
     */
    public function verifyLineItem(Request $request, DeliveryOrder $deliveryOrder, DoLineItem $lineItem)
    {
        if ($lineItem->delivery_order_id !== $deliveryOrder->id) {
            abort(404);
        }

        $request->validate([
            'create_asset' => 'nullable|boolean',
        ]);

        $lineItem->update([
            'status'           => 'received',
            'quantity_received' => $lineItem->quantity_ordered,
            'scan_user_id'     => auth()->id(),
            'scanned_at'       => now(),
        ]);

        // Optionally create an Asset from the verified line item
        if ($request->boolean('create_asset')) {
            $asset = Asset::create([
                'asset_tag'        => 'AST-' . strtoupper(substr(md5(uniqid()), 0, 8)),
                'name'             => $lineItem->description,
                'category'         => $lineItem->category ?? 'General',
                'brand'            => $lineItem->brand,
                'model'            => $lineItem->model,
                'serial_number'    => $lineItem->serial_number,
                'supplier_name'    => $deliveryOrder->supplier?->name,
                'do_reference'     => $deliveryOrder->do_no,
                'po_reference'     => $deliveryOrder->po_reference,
                'do_line_item_id'  => $lineItem->id,
                'verified_by_scan' => true,
                'scan_verified_at' => now(),
                'asset_type'       => 'inventory',
                'status'           => 'active',
                'location'         => 'Pending Assignment',
                'purchase_price'   => 0,
            ]);

            return redirect()->route('delivery-orders.show', $deliveryOrder)
                ->with('success', "Line item verified. Asset #{$asset->asset_tag} created.");
        }

        // Update DO status if all items are received
        $totalOrdered = $deliveryOrder->lineItems()->sum('quantity_ordered');
        $totalReceived = $deliveryOrder->lineItems()->sum('quantity_received');
        if ($totalReceived >= $totalOrdered) {
            $deliveryOrder->update(['status' => 'complete']);
        }

        return redirect()->route('delivery-orders.show', $deliveryOrder)
            ->with('success', 'Line item marked as received.');
    }

    /**
     * Get verification statistics for a DO.
     */
    public function verificationStats(DeliveryOrder $deliveryOrder)
    {
        $lineItems = $deliveryOrder->lineItems;
        $total = $lineItems->sum('quantity_ordered');
        $received = $lineItems->sum('quantity_received');
        $pending = $total - $received;

        return response()->json([
            'total'    => $total,
            'received' => $received,
            'pending'  => $pending,
            'progress' => $total > 0 ? round(($received / $total) * 100) : 0,
            'status'   => $deliveryOrder->status,
        ]);
    }
}
