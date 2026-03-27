<?php
 
namespace App\Http\Controllers;
 
use App\Models\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
 
class DashboardController extends Controller
{
    public function index()
    {
        // ── Category chart ──────────────────────────────────────────────────
        $chartData = Asset::select('category', DB::raw('sum(purchase_price) as total'))
            ->groupBy('category')
            ->get();
 
        // ── Asset type split ────────────────────────────────────────────────
        $fixedAssetCount = Asset::where('asset_type', 'fixed_asset')->count();
        $inventoryCount  = Asset::where('asset_type', 'inventory')->count();
        $fixedAssetValue = Asset::where('asset_type', 'fixed_asset')->sum('purchase_price');
        $inventoryValue  = Asset::where('asset_type', 'inventory')->sum('purchase_price');
 
        $assetTypeChart = Asset::select('asset_type', DB::raw('sum(purchase_price) as total'))
            ->whereNotNull('asset_type')
            ->groupBy('asset_type')
            ->get();
 
        // ── Campus / location breakdown ─────────────────────────────────────
        $campusLabels = [
            'utm_kl' => 'UTM Kuala Lumpur',
            'utm_jb' => 'UTM Johor Bahru',
            'other'  => 'Lain-lain',
        ];
 
        $campusStats = [];
        foreach (['utm_kl', 'utm_jb', 'other'] as $campus) {
            $campusStats[$campus] = [
                'label'           => $campusLabels[$campus],
                'count'           => Asset::where('campus', $campus)->count(),
                'value'           => Asset::where('campus', $campus)->sum('purchase_price'),
                'active_count'    => Asset::where('campus', $campus)->where('status', 'active')->count(),
                'fixed_count'     => Asset::where('campus', $campus)->where('asset_type', 'fixed_asset')->count(),
                'inventory_count' => Asset::where('campus', $campus)->where('asset_type', 'inventory')->count(),
                'top_assets'      => Asset::where('campus', $campus)
                                         ->orderBy('purchase_price', 'desc')
                                         ->take(3)
                                         ->get(['id', 'name', 'asset_tag', 'purchase_price', 'asset_type', 'status']),
            ];
        }
 
        // Campus chart (for doughnut)
        $campusChart = collect($campusStats)
            ->filter(fn($s) => $s['count'] > 0)
            ->map(fn($s, $k) => ['campus' => $k, 'label' => $s['label'], 'total' => $s['value']])
            ->values();
 
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_value'       => Asset::sum('purchase_price'),
                'total_count'       => Asset::count(),
                'active_count'      => Asset::where('status', 'active')->count(),
                'repair_count'      => Asset::where('status', 'repair')->count(),
                'fixed_asset_count' => $fixedAssetCount,
                'inventory_count'   => $inventoryCount,
                'fixed_asset_value' => $fixedAssetValue,
                'inventory_value'   => $inventoryValue,
            ],
            'chartData'       => $chartData,
            'assetTypeChart'  => $assetTypeChart,
            'campusChart'     => $campusChart,
            'campusStats'     => $campusStats,
            'highValueAssets' => Asset::orderBy('purchase_price', 'desc')->take(5)->get(),
        ]);
    }
}