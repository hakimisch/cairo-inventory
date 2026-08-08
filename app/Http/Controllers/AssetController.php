<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetPlacement;
use App\Models\Receiving;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return Inertia::render('Assets/Index', [
            'assets'     => Asset::latest()->get(),
            'trashed'    => Asset::onlyTrashed()->latest()->get(),
            'totalValue' => Asset::sum('purchase_price'),
            'stats'      => [
                'total'       => Asset::withTrashed()->count(),
                'active'      => Asset::where('status', 'active')->count(),
                'repair'      => Asset::where('status', 'repair')->count(),
                'disposed'    => Asset::where('status', 'disposed')->count(),
                'fixed_asset' => Asset::where('asset_type', 'fixed_asset')->count(),
                'inventory'   => Asset::where('asset_type', 'inventory')->count(),
                'trashed'     => Asset::onlyTrashed()->count(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_tag'      => 'required|unique:assets',
            'name'           => 'required|string|max:255',
            'category'       => 'required|string',
            'asset_type'     => 'required|in:fixed_asset,inventory',
            'campus'         => 'nullable|in:utm_kl,utm_jb,other',
            'purchase_price' => 'required|numeric|min:0',
            'location'       => 'required|string',
            'status'         => 'required|in:active,repair,disposed',
        ]);

        // Enforce classification by price (RM2000 threshold — KEW.PA-2/PA-3 boundary)
        $validated['asset_type'] = $validated['purchase_price'] >= config('cairo.asset_threshold', 2000) ? 'fixed_asset' : 'inventory';

        Asset::create($validated);
        return redirect()->back();
    }

    public function acceptReceiving(Request $request, Receiving $receiving)
    {
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $receiving->update(['status' => 'accepted']);

        // Auto-classify by price (RM2000 threshold — KEW.PA-2/PA-3 boundary)
        $price             = $receiving->unit_price ?? 0.00;
        $enforcedAssetType = $price >= 2000 ? 'fixed_asset' : 'inventory';

        // ─── Single asset creation ──────────────────────────────────────────
        $newAsset = Asset::create([
            'asset_tag'            => app(\App\Services\AssetTagGenerator::class)->assetTag(),
            'name'                 => $receiving->item_description,
            'category'             => $request->category ?? 'General',
            'asset_type'           => $enforcedAssetType,
            'campus'               => $request->campus ?? 'utm_jb',
            'purchase_price'       => $price,
            'location'             => $request->location ?? 'Main Lab',
            'custodian_name'       => $request->custodian_name,
            'status'               => 'active',
            'warranty_period'      => $request->warranty_period,
            'warranty_expiry'      => $request->warranty_expiry,
            'supplier_name'        => $receiving->supplier_name,
            'supplier_address'     => $receiving->supplier_address,
            'po_reference'         => $receiving->purchase_order_no,
            'do_reference'         => $receiving->delivery_order_no,
            'received_date'        => now(),
            'model'                => $request->model,
            'brand'                => $request->brand,
            'serial_number'        => $request->serial_number,
            'saga_id'              => $request->saga_id,
            'budget_vot'           => $request->budget_vot,
            'requires_maintenance' => $request->boolean('requires_maintenance'),
        ]);

        // ─── Handle photo upload via Spatie Media Library ───────────────────
        if ($request->hasFile('photo')) {
            $newAsset->addMediaFromRequest('photo')
                     ->toMediaCollection('asset_photos');
        }

        // Initial placement record
        $newAsset->placements()->create([
            'custodian_name' => $request->custodian_name ?? 'Belum Ditetapkan',
            'location'       => $request->location ?? 'Main Lab',
            'assigned_date'  => now(),
            'is_lokasi_luar' => false,
        ]);

        return redirect()->route('assets.index');
    }

    public function rejectReceiving(Request $request, Receiving $receiving)
    {
        // Rejection records the reason on the Receiving row — no asset is
        // created (previously a zero-value 'disposed' REJ- asset polluted
        // the registry and KEW.PA reports).
        $receiving->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->reason ?? 'Spesifikasi tidak menepati pesanan / Kerosakan fizikal',
            'rejected_at'      => now(),
        ]);

        return redirect()->route('receivings.index')
            ->with('success', 'Barangan telah ditolak. Rekod penerimaan ditandakan sebagai Ditolak.');
    }

    public function receivingIndex()
    {
        return Inertia::render('Assets/ReceivingIndex', [
            'receivings' => Receiving::latest()->get(),
            'trashed'    => Receiving::onlyTrashed()->latest()->get(),
            'stats'      => [
                'total'    => Receiving::withTrashed()->count(),
                'pending'  => Receiving::where('status', 'pending')->count(),
                'accepted' => Receiving::where('status', 'accepted')->count(),
                'rejected' => Receiving::where('status', 'rejected')->count(),
                'trashed'  => Receiving::onlyTrashed()->count(),
            ],
        ]);
    }

    public function storeReceiving(Request $request)
    {
        $validated = $request->validate([
            'supplier_name'     => 'required|string|max:255',
            'supplier_address'  => 'required|string',
            'purchase_order_no' => 'required|string|max:100',
            'delivery_order_no' => 'required|string|max:100',
            'invoice_no'        => 'required|string|max:100',
            'item_description'  => 'required|string',
            'quantity_ordered'  => 'required|integer|min:1',
            'quantity_received' => 'required|integer|min:1',
            'unit_price'        => 'required|numeric|min:0',
        ]);

        $validated['receive_no']  = app(\App\Services\AssetTagGenerator::class)->receiveNo();
        $validated['status']      = 'pending';
        $validated['total_price'] = $validated['unit_price'] * $validated['quantity_received'];

        Receiving::create($validated);

        return redirect()->route('receivings.index')
            ->with('success', 'Rekod penerimaan KEW.PA-1A berjaya didaftar.');
    }

    public function updateReceiving(Request $request, Receiving $receiving)
    {
        if ($receiving->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya rekod berstatus "Menunggu" boleh dikemaskini.');
        }

        $validated = $request->validate([
            'supplier_name'     => 'required|string|max:255',
            'supplier_address'  => 'required|string',
            'purchase_order_no' => 'required|string|max:100',
            'delivery_order_no' => 'required|string|max:100',
            'invoice_no'        => 'required|string|max:100',
            'item_description'  => 'required|string',
            'quantity_ordered'  => 'required|integer|min:1',
            'quantity_received' => 'required|integer|min:1',
            'unit_price'        => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:5000',
            'damage_description' => 'nullable|string|max:5000',
        ]);

        $validated['total_price'] = $validated['unit_price'] * $validated['quantity_received'];

        $receiving->update($validated);

        return redirect()->back()->with('success', 'Rekod penerimaan dikemaskini.');
    }

    public function destroyReceiving(Receiving $receiving)
    {
        if ($receiving->status !== 'pending') {
            return redirect()->back()->with('error', 'Hanya rekod berstatus "Menunggu" boleh dipadam.');
        }

        $receiving->delete(); // soft delete

        return redirect()->route('receivings.index')->with('success', 'Rekod penerimaan dipadam dan dipindahkan ke Tong.');
    }

    /**
     * Restore a soft-deleted receiving record.
     */
    public function restoreReceiving($id)
    {
        $receiving = Receiving::withTrashed()->findOrFail($id);
        $receiving->restore();

        activity()
            ->performedOn($receiving)
            ->log('restored');

        return redirect()->back()->with('success', "Penerimaan {$receiving->receive_no} telah dipulihkan.");
    }

    public function storePlacement(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'custodian_name'     => 'required|string|max:255',
            'staff_id'           => 'nullable|string|max:100',
            'borrower_phone'     => 'nullable|string|max:20',    // NEW — PA-9A
            'matric_no'          => 'nullable|string|max:50',    // NEW — PA-9A
            'authorizer_name'    => 'nullable|string|max:255',   // NEW — PA-9A
            'location'           => 'required|string|max:255',
            'quantity_placed'    => 'nullable|integer|min:1',
            'specific_serial_no' => 'nullable|string|max:255',
            'is_lokasi_luar'     => 'boolean',
            'assigned_date'      => 'required|date',
        ]);

        $asset->placements()->create($validated);

        $asset->update([
            'location'       => $validated['location'],
            'custodian_name' => $validated['custodian_name'],
        ]);

        return redirect()->back();
    }

    public function kewpa1(Receiving $receiving)
    {
        return Inertia::render('Assets/Kewpa1', ['receiving' => $receiving]);
    }

    public function kewpa2(Asset $asset)
    {
        $asset->load([
            'placements',
            'inspections',
            'upgrades',
            'maintenances',
            'disposals',
            'lossReports',
            'transfers',
        ]);
        return Inertia::render('Assets/Kewpa2', ['asset' => $asset]);
    }

    public function kewpa3(Asset $asset)
    {
        $asset->load([
            'placements',
            'inspections',
            'maintenances',
            'disposals',
            'lossReports',
            'transfers',
        ]);
        return Inertia::render('Assets/Kewpa3', ['asset' => $asset]);
    }

    public function downloadKewpa1(Receiving $receiving)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa1', ['receiving' => $receiving])
            ->format('a4')->name("KEW-PA-1A-{$receiving->receive_no}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    public function downloadKewpa2(Asset $asset)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa2', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-2-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    /**
     * Show the edit form for an asset.
     */
    public function edit(Asset $asset)
    {
        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
        ]);
    }

    /**
     * Update the specified asset.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_tag'            => 'required|unique:assets,asset_tag,' . $asset->id,
            'name'                 => 'required|string|max:255',
            'category'             => 'required|string',
            'sub_category'         => 'nullable|string|max:255',
            'asset_type'           => 'required|in:fixed_asset,inventory',
            'campus'               => 'required|in:utm_kl,utm_jb,other',
            'purchase_price'       => 'required|numeric|min:0',
            'location'             => 'required|string|max:255',
            'status'               => 'required|in:active,repair,disposed',
            'quantity'             => 'nullable|integer|min:1',
            'unit_of_measure'      => 'nullable|string|max:50',
            'national_code'        => 'nullable|string|max:255',
            'supplier_name'        => 'nullable|string|max:255',
            'supplier_address'     => 'nullable|string',
            'po_reference'         => 'nullable|string|max:100',
            'do_reference'         => 'nullable|string|max:100',
            'warranty_period'      => 'nullable|string|max:255',
            'warranty_expiry'      => 'nullable|date',
            'received_date'        => 'nullable|date',
            'rejection_reason'     => 'nullable|string',
            'receiver_name'        => 'nullable|string|max:255',
            'custodian_name'       => 'nullable|string|max:255',
            'model'                => 'nullable|string|max:255',
            'brand'                => 'nullable|string|max:255',
            'serial_number'        => 'nullable|string|max:255',
            'requires_maintenance' => 'boolean',
            'saga_id'              => 'nullable|string|max:255',
            'voucher_no'           => 'nullable|string|max:255',
            'budget_vot'           => 'nullable|string|max:255',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.edit', $asset)
            ->with('success', 'Aset berjaya dikemaskini.');
    }

    /**
     * Remove the specified asset (soft delete).
     */
    public function destroy(Asset $asset)
    {
        $name = $asset->name;
        $tag  = $asset->asset_tag;

        // Count related records for the confirmation message
        $relatedCount = $asset->placements()->count()
            + $asset->inspections()->count()
            + $asset->maintenances()->count()
            + $asset->transfers()->count()
            + $asset->disposals()->count()
            + $asset->lossReports()->count()
            + $asset->damageReports()->count()
            + $asset->upgrades()->count();

        $asset->delete(); // soft delete — asset still in DB with deleted_at set

        return redirect()->route('assets.index')
            ->with('success', "Aset {$name} ({$tag}) telah dipadam dan dipindahkan ke Tong. ({$relatedCount} rekod berkaitan.)");
    }

    /**
     * Restore a soft-deleted asset.
     */
    public function restore($id)
    {
        $asset = Asset::withTrashed()->findOrFail($id);
        $asset->restore();

        activity()
            ->performedOn($asset)
            ->log('restored');

        $tag = $asset->asset_tag;
        return redirect()->back()->with('success', "Aset {$tag} telah dipulihkan.");
    }

    /**
     * Restore a soft-deleted asset placement.
     */
    public function restorePlacement(Asset $asset, $placement)
    {
        $placement = AssetPlacement::withTrashed()->findOrFail($placement);
        $placement->restore();
        return redirect()->back()->with('success', 'Rekod telah dipulihkan.');
    }

    public function downloadKewpa3(Asset $asset)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa3', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-3-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    public function downloadLabel(Asset $asset)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.label', ['asset' => $asset])
            ->format('a4')->name("Label-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    // ── Phase 4: New View & Download Methods ─────────────────────────────────

    /**
     * KEW.PA-6 — Daftar Pergerakan Aset (Movement Register)
     */
    public function kewpa6(Asset $asset)
    {
        $asset->load(['placements', 'transfers']);
        return Inertia::render('Assets/Kewpa6', ['asset' => $asset]);
    }

    /**
     * Download KEW.PA-6 PDF
     */
    public function downloadKewpa6(Asset $asset)
    {
        $asset->load(['placements', 'transfers']);
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa6', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-6-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    /**
     * Display a listing of all asset loans/placements (PA-9A).
     */
    public function kewpa9aIndex(Request $request)
    {
        $placements = AssetPlacement::with('asset')
            ->when($request->search, function ($q, $search) {
                $q->whereHas('asset', fn($q) => $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('asset_tag', 'ILIKE', "%{$search}%"))
                  ->orWhere('custodian_name', 'ILIKE', "%{$search}%")
                  ->orWhere('location', 'ILIKE', "%{$search}%")
                  ->orWhere('borrower_phone', 'ILIKE', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return inertia('Assets/Kewpa9aIndex', [
            'records' => $placements,
            'filters' => $request->only(['search']),
            'trashed' => AssetPlacement::onlyTrashed()->latest()->get(),
        ]);
    }

    /**
     * KEW.PA-9A — Borang Pinjaman Aset (Loan Form)
     */
    public function kewpa9a(Asset $asset, ?AssetPlacement $placement = null)
    {
        $asset->load('placements');
        // Use the latest placement if none specified
        if (!$placement || !$placement->exists) {
            $placement = $asset->placements()->latest()->first();
        }
        return Inertia::render('Assets/Kewpa9a', [
            'asset'     => $asset,
            'placement' => $placement,
        ]);
    }

    /**
     * Download KEW.PA-9A PDF
     */
    public function downloadKewpa9a(Asset $asset, ?AssetPlacement $placement = null)
    {
        $asset->load('placements');
        if (!$placement || !$placement->exists) {
            $placement = $asset->placements()->latest()->first();
        }
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa9a', [
            'asset'     => $asset,
            'placement' => $placement,
        ])
            ->format('a4')->name("KEW-PA-9A-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    /**
     * KEW.PA-10 — Laporan Pemeriksaan Aset (Inspection Report)
     */
    public function kewpa10(Asset $asset)
    {
        $asset->load('inspections');
        return Inertia::render('Assets/Kewpa10', ['asset' => $asset]);
    }

    /**
     * Download KEW.PA-10 PDF
     */
    public function downloadKewpa10(Asset $asset)
    {
        $asset->load('inspections');
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa10', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-10-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }
}
