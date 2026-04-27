<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetMaintenance;
use Illuminate\Http\Request;

class AssetMaintenanceController extends Controller
{
    /**
     * Display a listing of all maintenance records (PA-13/14).
     */
    public function index(Request $request)
    {
        $maintenances = AssetMaintenance::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%"))
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('contract_no', 'like', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('maintenance_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa13Index', [
            'maintenances' => $maintenances,
            'filters'      => $request->only(['search', 'status']),
        ]);
    }

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

    /**
     * Download KEW.PA-13/14 — Rekod Penyelenggaraan Aset (PDF)
     */
    public function downloadKewpa13(Asset $asset)
    {
        $maintenances = $asset->maintenances()->orderBy('maintenance_date', 'desc')->get();
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa13', [
            'asset'        => $asset,
            'maintenances' => $maintenances,
        ])
            ->format('a4')->name("KEW-PA-13-{$asset->asset_tag}.pdf")
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
