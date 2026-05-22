<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetLossReport;
use App\Models\FinalLossReport;
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

        $oldStatus = $lossReport->status;
        $lossReport->update($validated);

        // Fire event if status changed
        if ($lossReport->wasChanged('status')) {
            event(new \App\Events\LossReportStatusChanged($lossReport->fresh(), $oldStatus, $lossReport->status));
        }

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
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    // ─── PA-29: Appointment Letter for Loss Investigation Committee ──

    /**
     * Download KEW.PA-29 — Surat Pelantikan JK Penyiasat Kehilangan.
     */
    public function downloadKewpa29(AssetLossReport $lossReport)
    {
        $lossReport->load('asset', 'committeeAppointments.user');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa29', ['lossReport' => $lossReport])
            ->format('a4')->name("KEW-PA-29-{$lossReport->asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    // ─── PA-30: Final Loss Investigation Report ──────────────────────────────

    /**
     * Show the KEW.PA-30 Final Loss Report page.
     */
    public function kewpa30(Asset $asset, AssetLossReport $lossReport)
    {
        $lossReport->load('asset', 'finalLossReport');
        return inertia('Assets/Kewpa30', [
            'asset'      => $asset,
            'lossReport' => $lossReport,
        ]);
    }

    /**
     * Store or update the PA-30 Final Loss Investigation Report.
     */
    public function storeFinalReport(Request $request, Asset $asset, AssetLossReport $lossReport)
    {
        $validated = $request->validate([

            // Section 1: Asset Details
            'asset_tag_no'              => 'nullable|string|max:255',
            'asset_description'         => 'nullable|string|max:255',
            'asset_category'            => 'nullable|string|max:255',
            'asset_serial_no'           => 'nullable|string|max:255',
            'asset_location_registered' => 'nullable|string|max:500',
            'last_custodian'            => 'nullable|string|max:255',

            // Section 2: Loss Description
            'incident_description'        => 'nullable|string|max:5000',
            'incident_date'               => 'nullable|date',
            'incident_time'               => 'nullable|string|max:20',
            'incident_location_details'   => 'nullable|string|max:500',
            'incident_circumstances'      => 'nullable|string|max:5000',

            // Section 3: Police Findings
            'police_investigation_findings' => 'nullable|string|max:10000',
            'police_officer_name'           => 'nullable|string|max:255',
            'police_station'                => 'nullable|string|max:255',
            'police_report_ref'             => 'nullable|string|max:255',

            // Section 4: Witness Statements
            'witness_1_statement' => 'nullable|string|max:5000',
            'witness_1_name'      => 'nullable|string|max:255',
            'witness_2_statement' => 'nullable|string|max:5000',
            'witness_2_name'      => 'nullable|string|max:255',

            // Section 5: Procedural Compliance
            'complied_with_procedures'  => 'nullable|boolean',
            'procedural_compliance_notes' => 'nullable|string|max:5000',
            'procedural_gaps'           => 'nullable|string|max:5000',

            // Section 6: Prevention Steps
            'prevention_actions_taken'    => 'nullable|string|max:5000',
            'prevention_recommendations'  => 'nullable|string|max:5000',

            // Section 7: Investigation Summary
            'investigation_conclusion'    => 'nullable|string|max:10000',

            // Section 8: Recommendation
            'recommended_action'         => 'nullable|string|in:gantian_setara,surcaj,tatatertib,hapuskira',
            'recommendation_rationale'   => 'nullable|string|max:5000',
        ]);

        $validated['asset_loss_report_id'] = $lossReport->id;

        FinalLossReport::updateOrCreate(
            ['asset_loss_report_id' => $lossReport->id],
            $validated
        );

        return redirect()->back()->with('success', 'PA-30 Final Loss Report saved successfully.');
    }

    /**
     * Download KEW.PA-30 Final Loss Report as PDF.
     */
    public function downloadKewpa30(Asset $asset, AssetLossReport $lossReport)
    {
        $lossReport->load('asset', 'finalLossReport');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa30', ['lossReport' => $lossReport])
            ->format('a4')->name("KEW-PA-30-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    // ─── PA-31: Write-off Certificate (Sijil Hapuskira) ────────────────

    /**
     * Show KEW.PA-31 Write-off Certificate page.
     */
    public function kewpa31(Request $request)
    {
        $lossReports = AssetLossReport::with('asset')
            ->where('action_type', 'write_off')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"));
            })
            ->orderBy('loss_date', 'desc')
            ->get();

        return inertia('Assets/Kewpa31', ['lossReports' => $lossReports]);
    }

    /**
     * Download KEW.PA-31 Write-off Certificate as PDF.
     */
    public function downloadKewpa31(AssetLossReport $lossReport)
    {
        $lossReport->load('asset');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa31', ['lossReport' => $lossReport])
            ->format('a4')->name("KEW-PA-31-{$lossReport->asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    // ─── PA-32: Annual Loss Action Report ───────────────────────────────

    /**
     * Show KEW.PA-32 Annual Loss Action Report page.
     */
    public function kewpa32(Request $request)
    {
        $year = $request->year ?? date('Y');

        $lossReports = AssetLossReport::with('asset')
            ->whereYear('loss_date', $year)
            ->orderBy('loss_date', 'desc')
            ->get();

        $summary = [
            'total_reports'   => $lossReports->count(),
            'total_value'     => $lossReports->sum('current_value'),
            'by_action'       => $lossReports->groupBy('action_type')->map(fn($g) => [
                'count' => $g->count(),
                'value' => $g->sum('current_value'),
            ]),
            'by_status'       => $lossReports->groupBy('status')->map(fn($g) => $g->count()),
        ];

        // Available years for the filter
        $years = AssetLossReport::selectRaw('EXTRACT(YEAR FROM loss_date) as yr')
            ->distinct()
            ->orderBy('yr', 'desc')
            ->pluck('yr')
            ->toArray();

        if (empty($years)) {
            $years = [date('Y')];
        }

        return inertia('Assets/Kewpa32', [
            'lossReports' => $lossReports,
            'summary'     => $summary,
            'year'        => (int) $year,
            'years'       => $years,
        ]);
    }

    /**
     * Download KEW.PA-32 Annual Loss Action Report as PDF.
     */
    public function downloadKewpa32(Request $request)
    {
        $year = $request->year ?? date('Y');

        $lossReports = AssetLossReport::with('asset')
            ->whereYear('loss_date', $year)
            ->orderBy('loss_date', 'desc')
            ->get();

        $summary = [
            'total_reports'   => $lossReports->count(),
            'total_value'     => $lossReports->sum('current_value'),
            'by_action'       => $lossReports->groupBy('action_type')->map(fn($g) => [
                'count' => $g->count(),
                'value' => $g->sum('current_value'),
            ]),
            'by_status'       => $lossReports->groupBy('status')->map(fn($g) => $g->count()),
        ];

        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa32', [
            'lossReports' => $lossReports,
            'summary'     => $summary,
            'year'        => $year,
        ])
            ->format('a4')->name("KEW-PA-32-{$year}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }
}
