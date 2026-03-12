<?php

namespace App\Http\Controllers;

use Spatie\LaravelPdf\Facades\Pdf;
use App\Models\Asset;
use App\Models\Receiving;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return Inertia::render('Assets/Index', [
            'assets' => Asset::latest()->get(),
            'totalValue' => Asset::sum('purchase_price'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_tag' => 'required|unique:assets',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'purchase_price' => 'required|numeric|min:0',
            'location' => 'required|string',
            'status' => 'required|in:active,repair,disposed',
        ]);

        \App\Models\Asset::create($validated);

        return redirect()->back(); // Inertia will automatically refresh the 'assets' prop
    }

    public function acceptReceiving(Request $request, Receiving $receiving)
    {
        $receiving->update(['status' => 'accepted']);

        Asset::create([
            'asset_tag' => $this->generateAssetTag(),
            'name' => $receiving->item_description,
            'category' => $request->category ?? 'General',
            'purchase_price' => $request->unit_price ?? 0.00, // Captures input from Modal
            'location' => $request->location ?? 'Main Lab',
            'status' => 'active',
            'supplier_name' => $receiving->supplier_name, // Maps from KEW.PA-1
            'supplier_address' => $receiving->supplier_address, // Maps from KEW.PA-1
            'po_reference' => $receiving->purchase_order_no,
            'received_date' => now(),
        ]);

        return redirect()->route('assets.index');
    }

    public function rejectReceiving(Request $request, Receiving $receiving)
    {
        // Update the receiving record to 'rejected'
        $receiving->update(['status' => 'rejected']);

        // Create a temporary "Rejected Asset" record to hold the rejection metadata
        $asset = Asset::create([
            'asset_tag' => 'REJ-' . $receiving->receive_no,
            'name' => $receiving->item_description,
            'category' => 'Rejected',
            'purchase_price' => 0.00,
            'location' => 'Receiving Bay',
            'status' => 'disposed',
            'supplier_name' => $receiving->supplier_name,
            'supplier_address' => $receiving->supplier_address,
            'po_reference' => $receiving->purchase_order_no,
            'rejection_reason' => $request->reason ?? 'Spesifikasi tidak menepati pesanan / Kerosakan fizikal',
        ]);

        return redirect()->route('receivings.index')->with('success', 'Barangan telah ditolak. Sila muat turun KEW.PA-2.');
    }

    // This method MUST be inside the AssetController class
    private function generateAssetTag()
    {
        $year = date('Y');
        $count = Asset::whereYear('created_at', $year)->count() + 1;
        // Government format: CAIRO/YEAR/H/NUMBER [cite: 6, 23]
        return "CAIRO/{$year}/H/" . str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    public function receivingIndex()
    {
        return Inertia::render('Assets/ReceivingIndex', [
            'receivings' => \App\Models\Receiving::latest()->get()
        ]);
    }

    public function createReceiving()
    {
        return Inertia::render('Assets/CreateReceiving');
    }

    public function storeReceiving(Request $request)
    {
        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'required|string',
            'purchase_order_no' => 'required|string|max:100',
            'delivery_order_no' => 'required|string|max:100',
            'item_description' => 'required|string',
            'quantity_ordered' => 'required|integer|min:1',
            'quantity_received' => 'required|integer|min:1',
        ]);

        // Format: RC-YYYYMMDD-COUNT
        $todayCount = Receiving::whereDate('created_at', now())->count() + 1;
        $validated['receive_no'] = 'RC-' . now()->format('Ymd') . '-' . str_pad($todayCount, 3, '0', STR_PAD_LEFT);
        $validated['status'] = 'pending';

        Receiving::create($validated);

        return redirect()->route('receivings.index')->with('success', 'Rekod penerimaan KEW.PA-1 berjaya didaftar.');
    }

    public function kewpa1(Receiving $receiving)
    {
        return Inertia::render('Assets/Kewpa1', [
            'receiving' => $receiving
        ]);
    }

    public function kewpa2(Asset $asset)
    {
        return Inertia::render('Assets/Kewpa2', ['asset' => $asset]);
    }

    public function kewpa3(Asset $asset)
    {
        return Inertia::render('Assets/Kewpa3', ['asset' => $asset]);
    }

    public function downloadKewpa1(Receiving $receiving)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa1', ['receiving' => $receiving])
            ->format('a4')
            ->name("KEW-PA-1-{$receiving->receive_no}.pdf")
            ->withBrowsershot(function ($browsershot) {
                // Find the dynamic path to the chrome executable
                $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();

                $browsershot->noSandbox()
                    ->setChromePath($chromePath ?? '/usr/bin/google-chrome') // Fallback if not found
                    ->setIncludePath('$PATH:/usr/local/bin:/usr/bin')
                    ->setTimeout(120);
            });
    }

    public function downloadKewpa2(Asset $asset)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa2', ['asset' => $asset])
            ->format('a4')
            ->name("KEW-PA-2-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($browsershot) {
                // Find the dynamic path to the chrome executable
                $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();

                $browsershot->noSandbox()
                    ->setChromePath($chromePath ?? '/usr/bin/google-chrome') // Fallback if not found
                    ->setIncludePath('$PATH:/usr/local/bin:/usr/bin')
                    ->setTimeout(120);
            });
    }

    public function downloadKewpa3(Asset $asset)
    {
        return \Spatie\LaravelPdf\Facades\Pdf::view('pdfs.kewpa3', ['asset' => $asset])
            ->format('a4')
            ->name("KEW-PA-3-{$asset->asset_tag}.pdf")
            ->withBrowsershot(function ($browsershot) {
            // Find the dynamic path to the chrome executable
                $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();

                $browsershot->noSandbox()
                    ->setChromePath($chromePath ?? '/usr/bin/google-chrome') // Fallback if not found
                    ->setIncludePath('$PATH:/usr/local/bin:/usr/bin')
                    ->setTimeout(120);
            });
    }

}