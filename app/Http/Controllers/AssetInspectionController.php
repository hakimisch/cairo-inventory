<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetInspectionController extends Controller
{
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