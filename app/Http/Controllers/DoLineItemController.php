<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DoLineItem;
use Illuminate\Http\Request;

class DoLineItemController extends Controller
{
    /**
     * Store a newly created line item on a Delivery Order.
     */
    public function store(Request $request, DeliveryOrder $deliveryOrder)
    {
        $validated = $request->validate([
            'description'      => 'required|string',
            'category'         => 'nullable|string',
            'item_code'        => 'nullable|string',
            'brand'            => 'nullable|string',
            'model'            => 'nullable|string',
            'serial_number'    => 'nullable|string',
            'quantity_ordered' => 'nullable|integer|min:1',
            'unit'             => 'nullable|string',
            'notes'            => 'nullable|string',
        ]);

        $validated['quantity_ordered'] = $validated['quantity_ordered'] ?? 1;
        $validated['status'] = 'pending';

        $lineItem = $deliveryOrder->lineItems()->create($validated);

        // Update DO status to partial if it was pending
        if ($deliveryOrder->status === 'pending') {
            $deliveryOrder->update(['status' => 'partial']);
        }

        return redirect()->back()
            ->with('success', 'Line item added successfully.');
    }

    /**
     * Update the specified line item.
     */
    public function update(Request $request, DeliveryOrder $deliveryOrder, DoLineItem $lineItem)
    {
        if ($lineItem->delivery_order_id !== $deliveryOrder->id) {
            abort(404);
        }

        $validated = $request->validate([
            'description'      => 'required|string',
            'category'         => 'nullable|string',
            'item_code'        => 'nullable|string',
            'brand'            => 'nullable|string',
            'model'            => 'nullable|string',
            'serial_number'    => 'nullable|string',
            'quantity_ordered' => 'nullable|integer|min:1',
            'unit'             => 'nullable|string',
            'status'           => 'nullable|in:pending,received,shortage,damaged,verified,no_serial',
            'has_serial'       => 'nullable|boolean',
            'notes'            => 'nullable|string',
        ]);

        $lineItem->update($validated);

        return redirect()->back()
            ->with('success', 'Line item updated successfully.');
    }

    /**
     * Remove the specified line item.
     */
    public function destroy(DeliveryOrder $deliveryOrder, DoLineItem $lineItem)
    {
        if ($lineItem->delivery_order_id !== $deliveryOrder->id) {
            abort(404);
        }

        $lineItem->delete();

        return redirect()->back()
            ->with('success', 'Line item deleted successfully.');
    }
}
