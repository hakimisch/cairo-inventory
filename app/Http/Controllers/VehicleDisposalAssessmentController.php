<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\VehicleDisposalAssessment;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class VehicleDisposalAssessmentController extends Controller
{
    /**
     * List all vehicle disposal assessments (standalone index for KEW.PA-16).
     */
    public function indexAll(Request $request)
    {
        $records = VehicleDisposalAssessment::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                      ->orWhere('asset_tag', 'ILIKE', "%{$search}%");
                })
                ->orWhere('plate_no', 'ILIKE', "%{$search}%")
                ->orWhere('chassis_no', 'ILIKE', "%{$search}%")
                ->orWhere('assessor_name', 'ILIKE', "%{$search}%");
            })
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa16Index', [
            'records' => $records,
            'filters' => $request->only(['search', 'status']),
            'assets'  => Asset::select('id', 'name', 'asset_tag')->orderBy('name')->get(),
        ]);
    }

    /**
     * Display the vehicle disposal assessment form / view.
     */
    public function index(Asset $asset)
    {
        $assessment = $asset->vehicleDisposalAssessment;

        return inertia('Assets/Kewpa16', [
            'asset'      => $asset->load('vehicleDisposalAssessment'),
            'assessment' => $assessment,
        ]);
    }

    /**
     * Store or update the vehicle disposal assessment.
     */
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'plate_no'          => 'required|string|max:20',
            'chassis_no'        => 'nullable|string|max:50',
            'engine_no'         => 'nullable|string|max:50',
            'vehicle_brand'     => 'nullable|string|max:100',
            'vehicle_model'     => 'nullable|string|max:100',
            'vehicle_year'      => 'nullable|integer|min:1900|max:2099',
            'road_tax_expiry'   => 'nullable|date',
            'engine_capacity'   => 'nullable|string|max:20',
            'fuel_type'         => 'nullable|string|max:50',
            'vehicle_color'     => 'nullable|string|max:50',
            'condition_report'  => 'nullable|string',
            'estimated_value'   => 'nullable|numeric|min:0',
            'assessment_date'   => 'nullable|date',
            'assessor_name'     => 'nullable|string|max:255',
            'assessor_position' => 'nullable|string|max:255',
            'recommendation'    => 'nullable|string',
            'status'            => 'nullable|string|in:draft,submitted,approved,rejected',
            'notes'             => 'nullable|string',
        ]);

        $validated['asset_id'] = $asset->id;

        VehicleDisposalAssessment::updateOrCreate(
            ['asset_id' => $asset->id],
            $validated
        );

        return redirect()->back()->with('success', 'Vehicle disposal assessment saved.');
    }

    /**
     * Destroy the vehicle disposal assessment.
     */
    public function destroy(Asset $asset)
    {
        $assessment = $asset->vehicleDisposalAssessment;

        if ($assessment) {
            $assessment->delete();
        }

        return redirect()->back()->with('success', 'Vehicle disposal assessment removed.');
    }

    /**
     * Show the KEW.PA-16 form view.
     */
    public function kewpa16(Asset $asset)
    {
        $assessment = $asset->vehicleDisposalAssessment;

        if (!$assessment) {
            return redirect()->route('assets.vehicle-disposal.index', $asset)
                ->with('error', 'Please complete the vehicle disposal assessment first.');
        }

        return inertia('Assets/Kewpa16', [
            'asset'      => $asset->load('vehicleDisposalAssessment'),
            'assessment' => $assessment,
        ]);
    }

    /**
     * Download KEW.PA-16 PDF.
     */
    public function downloadKewpa16(Asset $asset)
    {
        $assessment = $asset->vehicleDisposalAssessment;

        if (!$assessment) {
            return redirect()->back()->with('error', 'No vehicle disposal assessment found for this asset.');
        }

        $asset->load('vehicleDisposalAssessment');

        return Pdf::view('pdfs.kewpa16', [
            'asset'      => $asset,
            'assessment' => $assessment,
        ])
        ->format('a4')
        ->name("KEW-PA-16-{$asset->asset_tag}.pdf")
        ->withBrowsershot(function ($browsershot) {
            $browsershot->margins(15, 10, 15, 10)
                        ->showBrowserHeaderAndFooter()
                        ->addHeader('<span style="font-size:10px;">KEW.PA-16 — Perakuan Pelupusan Kenderaan</span>')
                        ->addFooter('<span style="font-size:8px;">Page {page_number} of {total_pages}</span>');
        })
        ->download();
    }
}
