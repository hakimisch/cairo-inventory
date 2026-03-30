<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Receiving;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return Inertia::render('Assets/Index', [
            'assets'     => Asset::latest()->get(),
            'totalValue' => Asset::sum('purchase_price'),
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

        // Enforce classification by price (RM1000 threshold)
        $validated['asset_type'] = $validated['purchase_price'] > 1000 ? 'fixed_asset' : 'inventory';

        Asset::create($validated);
        return redirect()->back();
    }

    public function acceptReceiving(Request $request, Receiving $receiving)
    {
        $request->validate([
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $receiving->update(['status' => 'accepted']);

        // Auto-classify by price
        $price             = $receiving->unit_price ?? 0.00;
        $enforcedAssetType = $price > 1000 ? 'fixed_asset' : 'inventory';

        // Handle photo upload
        $imageUrl = null;
        if ($request->hasFile('photo')) {
            $path     = $request->file('photo')->store('asset_photos', 'public');
            $imageUrl = '/storage/' . $path;
        }

        // ─── Single asset creation (fixed duplicate bug) ─────────────────
        $newAsset = Asset::create([
            'asset_tag'            => $this->generateAssetTag(),
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
            'image_url'            => $imageUrl,
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
        $receiving->update(['status' => 'rejected']);

        Asset::create([
            'asset_tag'        => 'REJ-' . $receiving->receive_no,
            'name'             => $receiving->item_description,
            'category'         => 'Rejected',
            'asset_type'       => 'inventory',
            'purchase_price'   => 0.00,
            'location'         => 'Receiving Bay',
            'status'           => 'disposed',
            'supplier_name'    => $receiving->supplier_name,
            'supplier_address' => $receiving->supplier_address,
            'po_reference'     => $receiving->purchase_order_no,
            'rejection_reason' => $request->reason ?? 'Spesifikasi tidak menepati pesanan / Kerosakan fizikal',
        ]);

        return redirect()->route('receivings.index')
            ->with('success', 'Barangan telah ditolak. Sila muat turun KEW.PA-2.');
    }

    private function generateAssetTag(): string
    {
        $year  = date('Y');
        $count = Asset::whereYear('created_at', $year)->count() + 1;
        return "CAIRO/{$year}/H/" . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    public function receivingIndex()
    {
        return Inertia::render('Assets/ReceivingIndex', [
            'receivings' => Receiving::latest()->get(),
        ]);
    }

    public function createReceiving()
    {
        return Inertia::render('Assets/CreateReceiving');
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

        $todayCount = Receiving::whereDate('created_at', now())->count() + 1;
        $validated['receive_no']  = 'RC-' . now()->format('Ymd') . '-' . str_pad($todayCount, 3, '0', STR_PAD_LEFT);
        $validated['status']      = 'pending';
        $validated['total_price'] = $validated['unit_price'] * $validated['quantity_received'];

        Receiving::create($validated);

        return redirect()->route('receivings.index')
            ->with('success', 'Rekod penerimaan KEW.PA-1A berjaya didaftar.');
    }

    public function storePlacement(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'custodian_name' => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'is_lokasi_luar' => 'boolean',
            'assigned_date'  => 'required|date',
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
        $asset->load('placements');
        return Inertia::render('Assets/Kewpa2', ['asset' => $asset]);
    }

    public function kewpa3(Asset $asset)
    {
        $asset->load('placements',);
        return Inertia::render('Assets/Kewpa3', ['asset' => $asset]);
    }

    public function downloadKewpa1(Receiving $receiving)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa1', ['receiving' => $receiving])
            ->format('a4')->name("KEW-PA-1A-{$receiving->receive_no}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    // Local Windows Development Path
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    // AWS / Linux Production Path
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
                    // Local Windows Development Path
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    // AWS / Linux Production Path
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }

    public function downloadKewpa3(Asset $asset)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa3', ['asset' => $asset])
            ->format('a4')->name("KEW-PA-3-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($b) {
                if (PHP_OS_FAMILY === 'Windows') {
                    // Local Windows Development Path
                    $b->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe');
                } else {
                    // AWS / Linux Production Path
                    $b->noSandbox()
                      ->setChromePath(collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first() ?? '/usr/bin/google-chrome')
                      ->setIncludePath('$PATH:/usr/local/bin:/usr/bin');
                }
                $b->setTimeout(120);
            });
    }
}