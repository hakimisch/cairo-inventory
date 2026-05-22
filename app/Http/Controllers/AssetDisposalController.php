<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\CommitteeAppointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetDisposalController extends Controller
{
    /**
     * Display a listing of all asset disposals (PA-17/18/19).
     */
    public function index(Request $request)
    {
        $disposals = AssetDisposal::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"))
                  ->orWhere('approval_reference', 'ILIKE', "%{$search}%")
                  ->orWhere('disposal_method', 'ILIKE', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa17Index', [
            'records' => $disposals,
            'filters' => $request->only(['search', 'status']),
            'assets'  => \App\Models\Asset::select('id', 'name', 'asset_tag')->orderBy('name')->get(),
        ]);
    }

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

    /**
     * Download KEW.PA-17/18/19 — Laporan Pelupusan Aset (PDF)
     */
    public function downloadKewpa17(Asset $asset)
    {
        $asset->load('disposals');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa17', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-17-{$asset->asset_tag}.pdf")
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

    // ─── PA-18: Destruction Certificate (Sijil Pemusnahan) ────────────

    /**
     * Show KEW.PA-18 Destruction Certificate page.
     */
    public function kewpa18()
    {
        $disposals = AssetDisposal::with('asset')
            ->whereIn('disposal_method', ['Tanam', 'Bakar', 'Tenggelam'])
            ->orderBy('disposal_date', 'desc')
            ->get();

        return inertia('Disposals/Kewpa18', ['disposals' => $disposals]);
    }

    /**
     * Download KEW.PA-18 Destruction Certificate as PDF.
     */
    public function downloadKewpa18(AssetDisposal $disposal)
    {
        $disposal->load('asset');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa18', ['disposal' => $disposal])
            ->format('a4')->name("KEW-PA-18-{$disposal->asset->asset_tag}.pdf")
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

    // ─── PA-19: Disposal Certificate (Sijil Pelupusan) ────────────────

    /**
     * Show KEW.PA-19 Disposal Certificate page.
     */
    public function kewpa19()
    {
        $disposals = AssetDisposal::with('asset')
            ->orderBy('disposal_date', 'desc')
            ->get();

        return inertia('Disposals/Kewpa19', ['disposals' => $disposals]);
    }

    /**
     * Download KEW.PA-19 Disposal Certificate as PDF.
     */
    public function downloadKewpa19(AssetDisposal $disposal)
    {
        $disposal->load('asset', 'committeeAppointments.user');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa19', ['disposal' => $disposal])
            ->format('a4')->name("KEW-PA-19-{$disposal->asset->asset_tag}.pdf")
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
