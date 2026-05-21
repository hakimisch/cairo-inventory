<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetTransfer;
use Illuminate\Http\Request;

class AssetTransferController extends Controller
{
    /**
     * Display a listing of all asset transfers (PA-6).
     */
    public function index(Request $request)
    {
        $transfers = AssetTransfer::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"))
                  ->orWhere('reference_no', 'ILIKE', "%{$search}%")
                  ->orWhere('from_location', 'ILIKE', "%{$search}%")
                  ->orWhere('to_location', 'ILIKE', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa6Index', [
            'records' => $transfers,
            'filters' => $request->only(['search', 'status']),
            'assets'  => \App\Models\Asset::select('id', 'name', 'asset_tag')->orderBy('name')->get(),
        ]);
    }

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
