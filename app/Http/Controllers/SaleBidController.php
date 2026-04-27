<?php

namespace App\Http\Controllers;

use App\Models\DisposalSale;
use App\Models\DisposalSaleItem;
use App\Models\SaleBid;
use Illuminate\Http\Request;

class SaleBidController extends Controller
{
    /**
     * Display bids for a sale item.
     */
    public function index(DisposalSale $sale, DisposalSaleItem $item)
    {
        $bids = $item->saleBids()->orderBy('bid_amount', 'desc')->get();
        return inertia('DisposalSales/Items/Bids/Index', [
            'sale'  => $sale,
            'item'  => $item,
            'bids'  => $bids,
        ]);
    }

    /**
     * Store a new bid.
     */
    public function store(Request $request, DisposalSale $sale, DisposalSaleItem $item)
    {
        $validated = $request->validate([
            'bidder_name'    => 'required|string|max:255',
            'bidder_ic'      => 'nullable|string|max:20',
            'bidder_phone'   => 'nullable|string|max:20',
            'bidder_address' => 'nullable|string',
            'bid_amount'     => 'required|numeric|min:0',
            'bid_date'       => 'nullable|date',
            'deposit_paid'   => 'nullable|boolean',
            'deposit_amount' => 'nullable|numeric|min:0',
            'status'         => 'nullable|string|in:pending,accepted,rejected,paid,completed',
            'notes'          => 'nullable|string',
        ]);

        $validated['disposal_sale_item_id'] = $item->id;

        SaleBid::create($validated);

        return redirect()->back()->with('success', 'Bid recorded.');
    }

    /**
     * Update a bid.
     */
    public function update(Request $request, DisposalSale $sale, DisposalSaleItem $item, SaleBid $bid)
    {
        $validated = $request->validate([
            'bidder_name'     => 'required|string|max:255',
            'bidder_ic'       => 'nullable|string|max:20',
            'bidder_phone'    => 'nullable|string|max:20',
            'bidder_address'  => 'nullable|string',
            'bid_amount'      => 'required|numeric|min:0',
            'bid_date'        => 'nullable|date',
            'deposit_paid'    => 'nullable|boolean',
            'deposit_amount'  => 'nullable|numeric|min:0',
            'is_winner'       => 'nullable|boolean',
            'award_date'      => 'nullable|date',
            'payment_date'    => 'nullable|date',
            'payment_received' => 'nullable|boolean',
            'status'          => 'nullable|string|in:pending,accepted,rejected,paid,completed',
            'notes'           => 'nullable|string',
        ]);

        $bid->update($validated);

        return redirect()->back()->with('success', 'Bid updated.');
    }

    /**
     * Delete a bid.
     */
    public function destroy(DisposalSale $sale, DisposalSaleItem $item, SaleBid $bid)
    {
        $bid->delete();
        return redirect()->back()->with('success', 'Bid removed.');
    }
}
