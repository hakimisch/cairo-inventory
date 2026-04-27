<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Asset;
use App\Models\Receiving;
use App\Models\AssetTransfer;
use App\Models\DamageReport;
use App\Models\AssetPlacement;
use App\Models\AssetInspection;
use App\Models\AssetMaintenance;
use App\Models\AssetDisposal;
use App\Models\AssetLossReport;
use App\Models\VehicleDisposalAssessment;
use App\Models\DisposalSale;
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

        // 3. KEW.PA Form Quick-Access Counts
        $kewpaCounts = [
            'pending_receivings'    => Receiving::where('status', 'pending')->count(),
            'total_transfers'       => AssetTransfer::count(),
            'total_damage_reports'  => DamageReport::count(),
            'total_placements'      => AssetPlacement::count(),
            'total_inspections'     => AssetInspection::count(),
            'total_maintenances'    => AssetMaintenance::count(),
            'total_vehicle_disposals' => VehicleDisposalAssessment::count(),
            'total_disposals'       => AssetDisposal::count(),
            'total_loss_reports'    => AssetLossReport::count(),
            'active_disposal_sales' => DisposalSale::whereIn('status', ['active', 'open'])->count(),
        ];

        // 4. Chart Data (Categories & Types)
        $chartData = Asset::select('category', DB::raw('count(*) as total'))->groupBy('category')->get();
        $assetTypeChart = Asset::select('asset_type', DB::raw('count(*) as total'))->groupBy('asset_type')->get();

        // 5. Campus Data
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
            'kewpaCounts'     => $kewpaCounts,
            'chartData'       => $chartData,
            'assetTypeChart'  => $assetTypeChart,
            'campusChart'     => $campusChart,
            'campusStats'     => $campusStats,
            'highValueAssets' => Asset::orderByDesc('purchase_price')->take(5)->get(),
        ]);
    }
}