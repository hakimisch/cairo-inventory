<?php
 
namespace App\Http\Controllers;
 
use App\Models\Asset;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
 
class ReportController extends Controller
{
    // ─── KEW.PA-4 — Senarai Harta Tetap ──────────────────────────────────────
    public function kewpa4(Request $request)
    {
        $year     = $request->integer('year', now()->year);
        $location = $request->input('location', ''); // FIX: Use input() to return a raw string, not a Stringable object
 
        $query = Asset::where('asset_type', 'fixed_asset')
                      ->whereYear('received_date', $year)
                      ->orderBy('location')
                      ->orderBy('name');
 
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }
        
        // Fetch once to save database performance
        $assets = $query->get([
            'id', 'asset_tag', 'national_code', 'name', 'location',
            'received_date', 'purchase_price', 'status',
        ]);
 
        return Inertia::render('Reports/Kewpa4', [
            'assets'      => $assets,
            'year'        => $year,
            'location'    => $location,
            'years'       => $this->availableYears(),
            'locations'   => Asset::whereNotNull('location')->distinct()->pluck('location')->values()->toArray(),
            'totalValue'  => $assets->sum('purchase_price'),
            'totalCount'  => $assets->count(),
        ]);
    }
 
    // ─── KEW.PA-5 — Senarai Inventori ────────────────────────────────────────
    public function kewpa5(Request $request)
    {
        $year     = $request->integer('year', now()->year);
        $location = $request->input('location', ''); // FIX: Use input()
 
        $query = Asset::where('asset_type', 'inventory')
                      ->whereYear('received_date', $year)
                      ->orderBy('location')
                      ->orderBy('name');
 
        if ($location) {
            $query->where('location', 'like', "%{$location}%");
        }
        
        $assets = $query->get([
            'id', 'asset_tag', 'national_code', 'name', 'category',
            'location', 'received_date', 'purchase_price', 'status',
        ]);
 
        return Inertia::render('Reports/Kewpa5', [
            'assets'     => $assets,
            'year'       => $year,
            'location'   => $location,
            'years'      => $this->availableYears(),
            'locations'  => Asset::whereNotNull('location')->distinct()->pluck('location')->values()->toArray(),
            'totalValue' => $assets->sum('purchase_price'),
            'totalCount' => $assets->count(),
        ]);
    }
 
    // ─── KEW.PA-8 — Laporan Tahunan ──────────────────────────────────────────
    public function kewpa8(Request $request)
    {
        $year = $request->integer('year', now()->year);
 
        // Group by location (Unit/Makmal) — one row per unique location
        $rows = Asset::whereYear('received_date', $year)
            ->select('location',
                DB::raw("SUM(CASE WHEN asset_type = 'fixed_asset' THEN 1 ELSE 0 END) as fixed_count"),
                DB::raw("SUM(CASE WHEN asset_type = 'fixed_asset' THEN purchase_price ELSE 0 END) as fixed_value"),
                DB::raw("SUM(CASE WHEN asset_type = 'inventory' THEN 1 ELSE 0 END) as inventory_count"),
                DB::raw("SUM(CASE WHEN asset_type = 'inventory' THEN purchase_price ELSE 0 END) as inventory_value")
            )
            ->groupBy('location')
            ->orderBy('location')
            ->get();
 
        return Inertia::render('Reports/Kewpa8', [
            'rows'       => $rows,
            'year'       => $year,
            'years'      => $this->availableYears(),
            'totals'     => [
                'fixed_count'     => $rows->sum('fixed_count'),
                'fixed_value'     => $rows->sum('fixed_value'),
                'inventory_count' => $rows->sum('inventory_count'),
                'inventory_value' => $rows->sum('inventory_value'),
            ],
        ]);
    }
 
    // ─── PDF downloads ────────────────────────────────────────────────────────
    public function downloadKewpa4(Request $request)
    {
        $year   = $request->integer('year', now()->year);
        $assets = Asset::where('asset_type', 'fixed_asset')
                       ->whereYear('received_date', $year)
                       ->orderBy('location')->orderBy('name')->get();
 
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa4', [
            'assets' => $assets, 'year' => $year,
        ])->format('a4')->name("KEW-PA-4-{$year}.pdf")
          ->withBrowsershot(fn($b) => $this->browsershot($b));
    }
 
    public function downloadKewpa5(Request $request)
    {
        $year   = $request->integer('year', now()->year);
        $assets = Asset::where('asset_type', 'inventory')
                       ->whereYear('received_date', $year)
                       ->orderBy('location')->orderBy('name')->get();
 
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa5', [
            'assets' => $assets, 'year' => $year,
        ])->format('a4')->name("KEW-PA-5-{$year}.pdf")
          ->withBrowsershot(fn($b) => $this->browsershot($b));
    }
 
    public function downloadKewpa8(Request $request)
    {
        $year = $request->integer('year', now()->year);
        $rows = Asset::whereYear('received_date', $year)
            ->select('location',
                DB::raw("SUM(CASE WHEN asset_type = 'fixed_asset' THEN 1 ELSE 0 END) as fixed_count"),
                DB::raw("SUM(CASE WHEN asset_type = 'fixed_asset' THEN purchase_price ELSE 0 END) as fixed_value"),
                DB::raw("SUM(CASE WHEN asset_type = 'inventory' THEN 1 ELSE 0 END) as inventory_count"),
                DB::raw("SUM(CASE WHEN asset_type = 'inventory' THEN purchase_price ELSE 0 END) as inventory_value")
            )
            ->groupBy('location')->orderBy('location')->get();
 
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa8', [
            'rows' => $rows, 'year' => $year,
        ])->format('a4')->name("KEW-PA-8-{$year}.pdf")
          ->withBrowsershot(fn($b) => $this->browsershot($b));
    }
 
    // ─── Helpers ──────────────────────────────────────────────────────────────
    private function availableYears(): array
    {
        return \App\Models\Asset::whereNotNull('received_date')
            ->selectRaw('EXTRACT(YEAR FROM received_date) as year')
            ->groupByRaw('EXTRACT(YEAR FROM received_date)')
            ->orderByRaw('EXTRACT(YEAR FROM received_date) DESC') // FIX: Safer Postgres ordering
            ->pluck('year')
            ->toArray(); // FIX: Must explicitly convert Laravel Collection to Array to satisfy return type
    }
 
    private function browsershot($b)
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
        } else {
            $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();
            $b->noSandbox()
              ->setChromePath($chromePath ?? '/usr/bin/google-chrome')
              ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
        }
        return $b->setTimeout(120);
    }
}