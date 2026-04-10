<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Asset;
use App\Models\Receiving;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Core Stats
        $stats = [
            'total_count'       => Asset::count(),
            'total_value'       => Asset::sum('purchase_price'),
            'active_count'      => Asset::where('status', 'active')->count(),
            'repair_count'      => Asset::where('status', 'repair')->count(),
            'fixed_asset_count' => Asset::where('asset_type', 'fixed_asset')->count(),
            'fixed_asset_value' => Asset::where('asset_type', 'fixed_asset')->sum('purchase_price'),
            'inventory_count'   => Asset::where('asset_type', 'inventory')->count(),
            'inventory_value'   => Asset::where('asset_type', 'inventory')->sum('purchase_price'),
        ];

        // 2. Admin Action Alerts
        $adminAlerts = [
            'pending_receivings' => Receiving::where('status', 'pending')->count(),
            'maintenance_needed' => Asset::where('requires_maintenance', true)->count(),
            'expiring_warranties'=> Asset::whereNotNull('warranty_expiry')
                                    ->whereBetween('warranty_expiry', [now(), now()->addDays(90)])
                                    ->count(),
        ];

        // 3. Chart Data (Categories & Types)
        $chartData = Asset::select('category', DB::raw('count(*) as total'))->groupBy('category')->get();
        $assetTypeChart = Asset::select('asset_type', DB::raw('count(*) as total'))->groupBy('asset_type')->get();

        // 4. Campus Data
        $campusChart = Asset::select('campus', DB::raw('sum(purchase_price) as total'))->whereNotNull('campus')->groupBy('campus')->get();
        
        $campusStats = [];
        foreach (['utm_kl', 'utm_jb'] as $c) {
            $campusStats[$c] = [
                'label'           => $c === 'utm_kl' ? 'UTM Kuala Lumpur' : 'UTM Johor Bahru',
                'count'           => Asset::where('campus', $c)->count(),
                'value'           => Asset::where('campus', $c)->sum('purchase_price'),
                'fixed_count'     => Asset::where('campus', $c)->where('asset_type', 'fixed_asset')->count(),
                'inventory_count' => Asset::where('campus', $c)->where('asset_type', 'inventory')->count(),
                'active_count'    => Asset::where('campus', $c)->where('status', 'active')->count(),
                'top_assets'      => Asset::where('campus', $c)->orderByDesc('purchase_price')->take(3)->get(),
            ];
        }

        // Render the renamed AdminDashboard file
        return Inertia::render('Admin/AdminDashboard', [
            'stats'           => $stats,
            'adminAlerts'     => $adminAlerts,
            'chartData'       => $chartData,
            'assetTypeChart'  => $assetTypeChart,
            'campusChart'     => $campusChart,
            'campusStats'     => $campusStats,
            'highValueAssets' => Asset::orderByDesc('purchase_price')->take(5)->get(),
        ]);
    }
}