<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetTransfer;
use Illuminate\Http\Request;

class AssetTransferController extends Controller
{
    /**
     * Store a new transfer/movement record for an asset (PA-6).
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'from_location'  => 'nullable|string|max:500',
            'to_location'    => 'required|string|max:500',
            'from_custodian' => 'nullable|string|max:255',
            'to_custodian'   => 'required|string|max:255',
            'transfer_date'  => 'required|date',
            'reference_no'   => 'nullable|string|max:255',
            'status'         => 'nullable|string|in:pending,approved,completed,cancelled',
            'reason'         => 'nullable|string|max:5000',
            'notes'          => 'nullable|string|max:5000',
        ]);

        $transfer = $asset->transfers()->create($validated);

        return redirect()->back()->with('success', 'Asset transfer recorded successfully.');
    }

    /**
     * Update the specified transfer record.
     */
    public function update(Request $request, Asset $asset, AssetTransfer $transfer)
    {
        $validated = $request->validate([
            'from_location'  => 'nullable|string|max:500',
            'to_location'    => 'required|string|max:500',
            'from_custodian' => 'nullable|string|max:255',
            'to_custodian'   => 'required|string|max:255',
            'transfer_date'  => 'required|date',
            'reference_no'   => 'nullable|string|max:255',
            'status'         => 'nullable|string|in:pending,approved,completed,cancelled',
            'reason'         => 'nullable|string|max:5000',
            'notes'          => 'nullable|string|max:5000',
        ]);

        $transfer->update($validated);

        return redirect()->back()->with('success', 'Asset transfer updated successfully.');
    }

    /**
     * Remove the specified transfer record.
     */
    public function destroy(Asset $asset, AssetTransfer $transfer)
    {
        $transfer->delete();

        return redirect()->back()->with('success', 'Asset transfer deleted successfully.');
    }
}
