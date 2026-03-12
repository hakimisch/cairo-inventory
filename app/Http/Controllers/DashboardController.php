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
        // Now you can use DB::raw() directly
        $chartData = Asset::select('category', DB::raw('sum(purchase_price) as total'))
            ->groupBy('category')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_value' => Asset::sum('purchase_price'),
                'total_count' => Asset::count(),
                'active_count' => Asset::where('status', 'active')->count(),
                'repair_count' => Asset::where('status', 'repair')->count(),
            ],
            'chartData' => $chartData,
            'highValueAssets' => \App\Models\Asset::orderBy('purchase_price', 'desc')
            ->take(5)
            ->get()
        ]);
    }
}

