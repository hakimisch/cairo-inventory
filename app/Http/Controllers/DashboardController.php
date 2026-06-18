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
        // Uses fuzzy name matching: strip common titles, compare individual name parts
        $userName = $user->name;
        $nameParts = preg_split('/\s+/', trim(preg_replace('/^(Ts\.|Dr\.|Prof\.|Ir\.|Hj\.|Hajah)\s*/i', '', $userName)));
        $myAssets = Asset::where(function ($q) use ($userName, $nameParts) {
            // Exact match first
            $q->where('custodian_name', $userName);
            // Fallback: partial word match (strip titles)
            foreach ($nameParts as $part) {
                if (strlen($part) > 2) {
                    $q->orWhere('custodian_name', 'like', '%' . $part . '%');
                }
            }
        })->orderByDesc('created_at')->get();

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