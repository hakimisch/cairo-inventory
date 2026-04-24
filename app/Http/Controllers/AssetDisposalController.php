<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDisposal;
use Illuminate\Http\Request;

class AssetDisposalController extends Controller
{
    /**
     * Store a new disposal request for an asset (PA-17/18/19).
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'request_reason'     => 'required|string|max:5000',
            'committee_decision' => 'nullable|string|max:5000',
            'disposal_method'    => 'nullable|string|in:Tanam,Bakar,Tenggelam,Jualan,Pindahan',
            'disposal_date'      => 'nullable|date',
            'approval_reference' => 'nullable|string|max:255',
            'status'             => 'nullable|string|in:draft,committee_review,approved,completed,cancelled',
            'notes'              => 'nullable|string|max:5000',
        ]);

        $disposal = $asset->disposals()->create($validated);

        return redirect()->back()->with('success', 'Disposal request submitted successfully.');
    }

    /**
     * Update the specified disposal record.
     */
    public function update(Request $request, Asset $asset, AssetDisposal $disposal)
    {
        $validated = $request->validate([
            'request_reason'     => 'required|string|max:5000',
            'committee_decision' => 'nullable|string|max:5000',
            'disposal_method'    => 'nullable|string|in:Tanam,Bakar,Tenggelam,Jualan,Pindahan',
            'disposal_date'      => 'nullable|date',
            'approval_reference' => 'nullable|string|max:255',
            'status'             => 'nullable|string|in:draft,committee_review,approved,completed,cancelled',
            'notes'              => 'nullable|string|max:5000',
        ]);

        $disposal->update($validated);

        return redirect()->back()->with('success', 'Disposal record updated successfully.');
    }

    /**
     * Remove the specified disposal record.
     */
    public function destroy(Asset $asset, AssetDisposal $disposal)
    {
        $disposal->delete();

        return redirect()->back()->with('success', 'Disposal record deleted successfully.');
    }
}
