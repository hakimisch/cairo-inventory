<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetUpgrade;
use Illuminate\Http\Request;

class AssetUpgradeController extends Controller
{
    /**
     * Store a new upgrade record for an asset.
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'date'            => 'required|date',
            'description'     => 'required|string|max:255',
            'warranty_period' => 'nullable|string|max:255',
            'cost'            => 'required|numeric|min:0',
        ]);

        $asset->upgrades()->create($validated);

        return redirect()->back()->with('success', 'Rekod naiktaraf berjaya ditambah.');
    }

    /**
     * Update the specified upgrade record.
     */
    public function update(Request $request, Asset $asset, AssetUpgrade $upgrade)
    {
        $validated = $request->validate([
            'date'            => 'required|date',
            'description'     => 'required|string|max:255',
            'warranty_period' => 'nullable|string|max:255',
            'cost'            => 'required|numeric|min:0',
        ]);

        $upgrade->update($validated);

        return redirect()->back()->with('success', 'Rekod naiktaraf berjaya dikemaskini.');
    }

    /**
     * Remove the specified upgrade record.
     */
    public function destroy(Asset $asset, AssetUpgrade $upgrade)
    {
        $upgrade->delete();

        return redirect()->back()->with('success', 'Rekod naiktaraf berjaya dipadam.');
    }
}
