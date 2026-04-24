<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    /**
     * Store a new maintenance record for an asset (PA-13/14).
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_date' => 'required|date',
            'description'      => 'required|string|max:5000',
            'contract_no'      => 'nullable|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'cost'             => 'nullable|numeric|min:0',
            'status'           => 'nullable|string|in:pending,in_progress,completed',
            'notes'            => 'nullable|string|max:5000',
        ]);

        $maintenance = $asset->maintenances()->create($validated);

        return redirect()->back()->with('success', 'Maintenance record added successfully.');
    }

    /**
     * Update the specified maintenance record.
     */
    public function update(Request $request, Asset $asset, AssetMaintenance $maintenance)
    {
        $validated = $request->validate([
            'maintenance_date' => 'required|date',
            'description'      => 'required|string|max:5000',
            'contract_no'      => 'nullable|string|max:255',
            'company_name'     => 'nullable|string|max:255',
            'cost'             => 'nullable|numeric|min:0',
            'status'           => 'nullable|string|in:pending,in_progress,completed',
            'notes'            => 'nullable|string|max:5000',
        ]);

        $maintenance->update($validated);

        return redirect()->back()->with('success', 'Maintenance record updated successfully.');
    }

    /**
     * Remove the specified maintenance record.
     */
    public function destroy(Asset $asset, AssetMaintenance $maintenance)
    {
        $maintenance->delete();

        return redirect()->back()->with('success', 'Maintenance record deleted successfully.');
    }
}
