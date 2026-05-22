<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetInspection;
use Illuminate\Http\Request;

class AssetInspectionController extends Controller
{
    /**
     * Display a listing of all asset inspections (PA-10).
     */
    public function index(Request $request)
    {
        $inspections = AssetInspection::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"))
                  ->orWhere('inspector_name', 'ILIKE', "%{$search}%")
                  ->orWhere('notes', 'ILIKE', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('inspection_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa10Index', [
            'records' => $inspections,
            'filters' => $request->only(['search', 'status']),
            'assets'  => Asset::select('id', 'name', 'asset_tag')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'status'          => 'required|string|max:255',
            'inspector_name'  => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
        ]);

        $validated['inspector_name'] ??= $request->user()->name;

        $asset->inspections()->create($validated);

        return redirect()->back()->with('success', 'Rekod pemeriksaan berjaya ditambah.');
    }

    /**
     * Update the specified inspection.
     */
    public function update(Request $request, Asset $asset, AssetInspection $inspection)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'status'          => 'required|string|max:255',
            'inspector_name'  => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
        ]);

        $inspection->update($validated);

        return redirect()->back()->with('success', 'Rekod pemeriksaan berjaya dikemaskini.');
    }

    /**
     * Remove the specified inspection.
     */
    public function destroy(Asset $asset, AssetInspection $inspection)
    {
        $inspection->delete();

        return redirect()->back()->with('success', 'Rekod pemeriksaan berjaya dipadam.');
    }

    // ─── PA-11: Inventory Inspection Report ─────────────────────────────

    /**
     * Show KEW.PA-11 Inventory Inspection Report page.
     */
    public function kewpa11()
    {
        $inspections = AssetInspection::with('asset')
            ->orderBy('inspection_date', 'desc')
            ->get();

        return inertia('Inspections/Kewpa11', ['inspections' => $inspections]);
    }

    /**
     * Download KEW.PA-11 Inventory Inspection Report as PDF.
     */
    public function downloadKewpa11()
    {
        $inspections = AssetInspection::with('asset')
            ->orderBy('inspection_date', 'desc')
            ->get();

        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa11', ['inspections' => $inspections])
            ->format('a4')->name('KEW-PA-11-Inventory-Inspection.pdf')
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
