<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLossReport;
use Illuminate\Http\Request;

class AssetLossReportController extends Controller
{
    /**
     * Store a new loss report for an asset (PA-28→32).
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'incident_location'    => 'required|string|max:500',
            'loss_date'            => 'required|date',
            'loss_method'          => 'required|string|in:hilang,curi,musnah,other',
            'last_officer'         => 'nullable|string|max:255',
            'police_report_no'     => 'nullable|string|max:255',
            'current_value'        => 'required|numeric|min:0',
            'action_type'          => 'nullable|string|in:surcharge,write_off',
            'write_off_value'      => 'nullable|numeric|min:0',
            'surcharge_amount'     => 'nullable|numeric|min:0',
            'applied_date'         => 'nullable|date',
            'investigation_summary' => 'nullable|string|max:10000',
            'approval_reference'   => 'nullable|string|max:255',
            'status'               => 'nullable|string|in:under_investigation,committee_review,approved,closed',
            'notes'                => 'nullable|string|max:5000',
        ]);

        $lossReport = $asset->lossReports()->create($validated);

        return redirect()->back()->with('success', 'Loss report submitted successfully.');
    }

    /**
     * Update the specified loss report.
     */
    public function update(Request $request, Asset $asset, AssetLossReport $lossReport)
    {
        $validated = $request->validate([
            'incident_location'    => 'required|string|max:500',
            'loss_date'            => 'required|date',
            'loss_method'          => 'required|string|in:hilang,curi,musnah,other',
            'last_officer'         => 'nullable|string|max:255',
            'police_report_no'     => 'nullable|string|max:255',
            'current_value'        => 'required|numeric|min:0',
            'action_type'          => 'nullable|string|in:surcharge,write_off',
            'write_off_value'      => 'nullable|numeric|min:0',
            'surcharge_amount'     => 'nullable|numeric|min:0',
            'applied_date'         => 'nullable|date',
            'investigation_summary' => 'nullable|string|max:10000',
            'approval_reference'   => 'nullable|string|max:255',
            'status'               => 'nullable|string|in:under_investigation,committee_review,approved,closed',
            'notes'                => 'nullable|string|max:5000',
        ]);

        $lossReport->update($validated);

        return redirect()->back()->with('success', 'Loss report updated successfully.');
    }

    /**
     * Remove the specified loss report.
     */
    public function destroy(Asset $asset, AssetLossReport $lossReport)
    {
        $lossReport->delete();

        return redirect()->back()->with('success', 'Loss report deleted successfully.');
    }
}
