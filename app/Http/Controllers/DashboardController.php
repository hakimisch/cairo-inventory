<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Asset;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Safety catch: redirect admins to their specific dashboard
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        // Fetch assets assigned to this specific user
        $myAssets = Asset::where('custodian_name', $user->name)
                         ->orderByDesc('created_at')
                         ->get();

        $myStats = [
            'total'     => $myAssets->count(),
            'fixed'     => $myAssets->where('asset_type', 'fixed_asset')->count(),
            'inventory' => $myAssets->where('asset_type', 'inventory')->count(),
            'value'     => $myAssets->sum('purchase_price'),
        ];

        return Inertia::render('Dashboard', [
            'myAssets' => $myAssets,
            'myStats'  => $myStats,
        ]);
    }
}