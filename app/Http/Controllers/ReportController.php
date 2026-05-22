<?php
  
namespace App\Http\Controllers;
  
use App\Models\Asset;
use App\Models\AssetDisposal;
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

    // ─── KEW.PA-7 — Laporan Kedudukan Aset ────────────────────────────────────
    public function kewpa7(Request $request)
    {
        $location   = $request->input('location');
        $reportDate = $request->input('report_date', now()->format('Y-m-d'));

        $query = Asset::query();
        if ($location) {
            $query->where('location', $location);
        }
        $assets = $query->orderBy('asset_tag')->get();

        return Inertia::render('Reports/Kewpa7', [
            'assets'     => $assets,
            'location'   => $location,
            'reportDate' => $reportDate,
        ]);
    }

    public function downloadKewpa7(Request $request)
    {
        $location   = $request->input('location');
        $reportDate = $request->input('report_date', now()->format('Y-m-d'));

        $query = Asset::query();
        if ($location) {
            $query->where('location', $location);
        }
        $assets = $query->orderBy('asset_tag')->get();

        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa7', [
            'assets'      => $assets,
            'location'    => $location,
            'report_date' => $reportDate,
        ])
            ->format('a4')->name("KEW-PA-7-{$reportDate}.pdf")
            ->withBrowsershot(fn($b) => $this->browsershot($b));
    }

    // ─── KEW.PA-12 — Perakuan Pemeriksaan Tahunan Aset ────────────────────────
    public function kewpa12(Request $request)
    {
        $year       = $request->input('year', now()->year);
        $reportDate = $request->input('report_date', now()->format('Y-m-d'));

        $assets = Asset::with('inspections')
            ->whereYear('created_at', $year)
            ->orWhereYear('received_date', $year)
            ->orderBy('asset_tag')
            ->get()
            ->map(function ($asset) {
                $latestInsp = $asset->inspections->sortByDesc('inspection_date')->first();
                $asset->latest_inspection_status = $latestInsp?->status;
                return $asset;
            });

        $inspected    = $assets->filter(fn($a) => $a->latest_inspection_status !== null)->count();
        $notInspected = $assets->filter(fn($a) => $a->latest_inspection_status === null)->count();
        $damagedLost  = $assets->filter(fn($a) => in_array($a->latest_inspection_status, ['rosak', 'hilang', 'damaged', 'lost']))->count();

        return Inertia::render('Reports/Kewpa12', [
            'assets'     => $assets,
            'year'       => $year,
            'reportDate' => $reportDate,
            'summary'    => [
                'inspected'     => $inspected,
                'not_inspected' => $notInspected,
                'damaged_lost'  => $damagedLost,
                'total'         => $assets->count(),
            ],
        ]);
    }

    public function downloadKewpa12(Request $request)
    {
        $year       = $request->input('year', now()->year);
        $reportDate = $request->input('report_date', now()->format('Y-m-d'));

        $assets = Asset::with('inspections')
            ->whereYear('created_at', $year)
            ->orWhereYear('received_date', $year)
            ->orderBy('asset_tag')
            ->get()
            ->map(function ($asset) {
                $latestInsp = $asset->inspections->sortByDesc('inspection_date')->first();
                $asset->latest_inspection_status = $latestInsp?->status;
                return $asset;
            });

        $inspected    = $assets->filter(fn($a) => $a->latest_inspection_status !== null)->count();
        $notInspected = $assets->filter(fn($a) => $a->latest_inspection_status === null)->count();
        $damagedLost  = $assets->filter(fn($a) => in_array($a->latest_inspection_status, ['rosak', 'hilang', 'damaged', 'lost']))->count();

        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa12', [
            'assets'      => $assets,
            'year'        => $year,
            'report_date' => $reportDate,
            'summary'     => [
                'inspected'     => $inspected,
                'not_inspected' => $notInspected,
                'damaged_lost'  => $damagedLost,
                'total'         => $assets->count(),
            ],
        ])
            ->format('a4')->name("KEW-PA-12-{$year}.pdf")
            ->withBrowsershot(fn($b) => $this->browsershot($b));
    }

    // ─── KEW.PA-20 — Laporan Pelupusan Aset Tahunan ───────────────────────────
    public function kewpa20(Request $request)
    {
        $year       = $request->input('year', now()->year);
        $reportDate = $request->input('report_date', now()->format('Y-m-d'));

        $disposals = AssetDisposal::with('asset')
            ->whereYear('created_at', $year)
            ->orWhereYear('disposal_date', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Reports/Kewpa20', [
            'disposals'  => $disposals,
            'year'       => $year,
            'reportDate' => $reportDate,
        ]);
    }

    public function downloadKewpa20(Request $request)
    {
        $year       = $request->input('year', now()->year);
        $reportDate = $request->input('report_date', now()->format('Y-m-d'));

        $disposals = AssetDisposal::with('asset')
            ->whereYear('created_at', $year)
            ->orWhereYear('disposal_date', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa20', [
            'disposals'   => $disposals,
            'year'        => $year,
            'report_date' => $reportDate,
        ])
            ->format('a4')->name("KEW-PA-20-{$year}.pdf")
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