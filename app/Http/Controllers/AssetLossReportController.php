<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLossReport;
use Illuminate\Http\Request;

class AssetLossReportController extends Controller
{
    /**
     * Display a listing of all loss reports (PA-28→32).
     */
    public function index(Request $request)
    {
        $lossReports = AssetLossReport::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"))
                  ->orWhere('police_report_no', 'ILIKE', "%{$search}%")
                  ->orWhere('incident_location', 'ILIKE', "%{$search}%")
                  ->orWhere('approval_reference', 'ILIKE', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa28Index', [
            'records' => $lossReports,
            'filters' => $request->only(['search', 'status']),
            'assets'  => \App\Models\Asset::select('id', 'name', 'asset_tag')->orderBy('name')->get(),
        ]);
    }

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

    /**
     * Download KEW.PA-28→32 — Laporan Kehilangan Aset (PDF)
     */
    public function downloadKewpa28(Asset $asset)
    {
        $asset->load('lossReports');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa28', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-28-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }
}
