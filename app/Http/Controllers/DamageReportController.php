<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\DamageReport;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class DamageReportController extends Controller
{
    /**
     * Display a listing of all damage reports (PA-9).
     */
    public function index(Request $request)
    {
        $damageReports = DamageReport::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%"))
                  ->orWhere('reported_by', 'like', "%{$search}%")
                  ->orWhere('damage_description', 'like', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa9Index', [
            'damageReports' => $damageReports,
            'filters'       => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'damage_date'               => 'required|date',
            'last_user'                 => 'required|string|max:255',
            'previous_maintenance_cost' => 'nullable|numeric|min:0',
            'damage_description'        => 'required|string',
        ]);

        $validated['reported_by'] = $request->user()->name;
        $validated['previous_maintenance_cost'] = $validated['previous_maintenance_cost'] ?? 0;

        $asset->damageReports()->create($validated);

        // Auto-update the asset status to 'repair' so the Admin Dashboard catches it
        $asset->update(['status' => 'repair']);

        return redirect()->back()->with('success', 'Aduan kerosakan berjaya direkodkan.');
    }

    public function downloadKewpa9(DamageReport $damageReport)
    {
        $damageReport->load('asset');

        return Pdf::view('pdfs.kewpa9', ['report' => $damageReport])
            ->format('a4')
            ->name("KEW-PA-9-{$damageReport->asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();
                    $b->noSandbox()
                      ->setChromePath($chromePath ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                return $b->setTimeout(120);
            });
    }
}
