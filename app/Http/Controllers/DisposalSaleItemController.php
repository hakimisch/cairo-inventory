<?php

namespace App\Http\Controllers;

use App\Models\DisposalSale;
use App\Models\DisposalSaleItem;
use Illuminate\Http\Request;

class DisposalSaleItemController extends Controller
{
    /**
     * Display items for a sale.
     */
    public function index(DisposalSale $sale)
    {
        $items = $sale->disposalSaleItems()->with('asset')->get();
        return inertia('DisposalSales/Items/Index', [
            'sale'  => $sale,
            'items' => $items,
        ]);
    }

    /**
     * Store a new sale item.
     */
    public function store(Request $request, DisposalSale $sale)
    {
        $validated = $request->validate([
            'asset_id'        => 'nullable|exists:assets,id',
            'item_description' => 'nullable|string',
            'quantity'        => 'nullable|integer|min:1',
            'reserve_price'   => 'nullable|numeric|min:0',
            'estimated_value' => 'nullable|numeric|min:0',
            'lot_number'      => 'nullable|string|max:50',
            'status'          => 'nullable|string|in:available,sold,unsold,withdrawn',
            'notes'           => 'nullable|string',
        ]);

        $validated['disposal_sale_id'] = $sale->id;

        DisposalSaleItem::create($validated);

        return redirect()->back()->with('success', 'Sale item added.');
    }

    /**
     * Update a sale item.
     */
    public function update(Request $request, DisposalSale $sale, DisposalSaleItem $item)
    {
        $validated = $request->validate([
            'asset_id'        => 'nullable|exists:assets,id',
            'item_description' => 'nullable|string',
            'quantity'        => 'nullable|integer|min:1',
            'reserve_price'   => 'nullable|numeric|min:0',
            'estimated_value' => 'nullable|numeric|min:0',
            'lot_number'      => 'nullable|string|max:50',
            'status'          => 'nullable|string|in:available,sold,unsold,withdrawn',
            'notes'           => 'nullable|string',
        ]);

        $item->update($validated);

        return redirect()->back()->with('success', 'Sale item updated.');
    }

    /**
     * Delete a sale item.
     */
    public function destroy(DisposalSale $sale, DisposalSaleItem $item)
    {
        $item->delete();
        return redirect()->back()->with('success', 'Sale item removed.');
    }
}
