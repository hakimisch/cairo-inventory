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
            'asset_tag'     => 'required|unique:assets',
            'name'          => 'required|string|max:255',
            'category'      => 'required|string',
            'asset_type'    => 'required|in:fixed_asset,inventory',
            'campus'        => 'nullable|in:utm_kl,utm_jb,other',
            'purchase_price'=> 'required|numeric|min:0',
            'location'      => 'required|string',
            'status'        => 'required|in:active,repair,disposed',
        ]);
 
        // Force the classification before creating the record
        $validated['asset_type'] = $validated['purchase_price'] > 1000 ? 'fixed_asset' : 'inventory';

        Asset::create($validated);
 
        return redirect()->back();
    }
 
    public function acceptReceiving(Request $request, Receiving $receiving)
    {
        $receiving->update(['status' => 'accepted']);

        // 1. Capture the price
        $price = $request->unit_price ?? 0.00;

        // 2. Force the classification based on the > RM1000 rule
        $enforcedAssetType = $price > 1000 ? 'fixed_asset' : 'inventory';
 
        Asset::create([
            'asset_tag'       => $this->generateAssetTag(),
            'name'            => $receiving->item_description,
            'category'        => $request->category ?? 'General',
            'asset_type'      => $enforcedAssetType, // Overrides the frontend selection
            'campus'          => $request->campus ?? 'utm_jb',
            'purchase_price'  => $price,
            'location'        => $request->location ?? 'Main Lab',
            'status'          => 'active',
            'warranty_expiry' => $request->warranty_expiry,
            'supplier_name'   => $receiving->supplier_name,
            'supplier_address'=> $receiving->supplier_address,
            'po_reference'    => $receiving->purchase_order_no,
            'received_date'   => now(),
        ]);
 
        return redirect()->route('assets.index');
    }
 
    public function rejectReceiving(Request $request, Receiving $receiving)
    {
        $receiving->update(['status' => 'rejected']);
 
        Asset::create([
            'asset_tag'       => 'REJ-' . $receiving->receive_no,
            'name'            => $receiving->item_description,
            'category'        => 'Rejected',
            'asset_type'      => 'inventory',
            'purchase_price'  => 0.00,
            'location'        => 'Receiving Bay',
            'status'          => 'disposed',
            'supplier_name'   => $receiving->supplier_name,
            'supplier_address'=> $receiving->supplier_address,
            'po_reference'    => $receiving->purchase_order_no,
            'rejection_reason'=> $request->reason ?? 'Spesifikasi tidak menepati pesanan / Kerosakan fizikal',
        ]);
 
        return redirect()->route('receivings.index')->with('success', 'Barangan telah ditolak. Sila muat turun KEW.PA-2.');
    }
 
    private function generateAssetTag()
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
            'item_description'  => 'required|string',
            'quantity_ordered'  => 'required|integer|min:1',
            'quantity_received' => 'required|integer|min:1',
        ]);
 
        $todayCount = Receiving::whereDate('created_at', now())->count() + 1;
        $validated['receive_no'] = 'RC-' . now()->format('Ymd') . '-' . str_pad($todayCount, 3, '0', STR_PAD_LEFT);
        $validated['status'] = 'pending';
 
        Receiving::create($validated);
 
        return redirect()->route('receivings.index')->with('success', 'Rekod penerimaan KEW.PA-1 berjaya didaftar.');
    }
 
    public function kewpa1(Receiving $receiving)
    {
        return Inertia::render('Assets/Kewpa1', ['receiving' => $receiving]);
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
                $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();
                $browsershot->noSandbox()
                    ->setChromePath($chromePath ?? '/usr/bin/google-chrome')
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
                $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();
                $browsershot->noSandbox()
                    ->setChromePath($chromePath ?? '/usr/bin/google-chrome')
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
                $chromePath = collect(glob(storage_path('puppeteer/chrome/linux-*/chrome-linux64/chrome')))->first();
                $browsershot->noSandbox()
                    ->setChromePath($chromePath ?? '/usr/bin/google-chrome')
                    ->setIncludePath('$PATH:/usr/local/bin:/usr/bin')
                    ->setTimeout(120);
            });
    }
}
 