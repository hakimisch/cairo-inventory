<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetInspection;
use Illuminate\Http\Request;

class AssetInspectionController extends Controller
{
    /**
     * Display a listing of all asset inspections (PA-10).
     */
    public function index(Request $request)
    {
        $inspections = AssetInspection::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"))
                  ->orWhere('inspector_name', 'ILIKE', "%{$search}%")
                  ->orWhere('notes', 'ILIKE', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('inspection_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa10Index', [
            'records' => $inspections,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'inspection_date' => 'required|date',
            'status'          => 'required|string|max:255',
            'notes'           => 'nullable|string',
        ]);

        // Automatically assign the logged-in user as the inspector
        $validated['inspector_name'] = $request->user()->name;

        $asset->inspections()->create($validated);

        return redirect()->back()->with('success', 'Rekod pemeriksaan berjaya ditambah.');
    }
}
