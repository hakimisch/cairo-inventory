<?php

namespace App\Http\Controllers;

use App\Models\AssetDisposal;
use App\Models\DisposalSale;
use Illuminate\Http\Request;
use Spatie\LaravelPdf\Facades\Pdf;

class DisposalSaleController extends Controller
{
    /**
     * Display a listing of disposal sales.
     */
    public function index(Request $request)
    {
        $sales = DisposalSale::with('assetDisposal.asset')
            ->when($request->sale_type, fn($q, $v) => $q->where('sale_type', $v))
            ->when($request->search, fn($q, $v) => $q->where(function($q) use ($v) {
                $q->where('sale_reference', 'ILIKE', "%{$v}%")
                  ->orWhere('sale_officer', 'ILIKE', "%{$v}%")
                  ->orWhere('sale_location', 'ILIKE', "%{$v}%");
            }))
            ->when($request->status, fn($q, $v) => $q->where('status', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $typeCounts = [
            'total'      => DisposalSale::count(),
            'Tawaran'    => DisposalSale::where('sale_type', 'Tawaran')->count(),
            'Sebutharga' => DisposalSale::where('sale_type', 'Sebutharga')->count(),
            'Lelongan'   => DisposalSale::where('sale_type', 'Lelongan')->count(),
        ];

        return inertia('DisposalSales/Index', [
            'sales' => $sales,
            'filters' => $request->only(['sale_type', 'search', 'status']),
            'typeCounts' => $typeCounts,
            'disposals' => \App\Models\AssetDisposal::with('asset')
                ->select('id', 'asset_id', 'disposal_method', 'disposal_date')
                ->orderBy('id', 'desc')
                ->get(),
        ]);
    }

    /**
     * Show a single disposal sale with items and bids.
     */
    public function show(DisposalSale $sale)
    {
        $sale->load([
            'assetDisposal.asset',
            'disposalSaleItems.asset',
            'disposalSaleItems.saleBids',
        ]);

        return inertia('DisposalSales/Show', [
            'sale'   => $sale,
            'assets' => \App\Models\Asset::select('id', 'name', 'asset_tag')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a new disposal sale.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_disposal_id' => 'required|exists:asset_disposals,id',
            'sale_type'         => 'required|string|in:Tawaran,Sebutharga,Lelongan',
            'sale_reference'    => 'required|string|max:100|unique:disposal_sales,sale_reference',
            'sale_date'         => 'nullable|date',
            'sale_location'     => 'nullable|string|max:255',
            'viewing_date_start'  => 'nullable|date',
            'viewing_date_end'    => 'nullable|date|after_or_equal:viewing_date_start',
            'closing_datetime'    => 'nullable|date',
            'sealed_envelope_ref' => 'nullable|string|max:255',
            'tender_box_address'  => 'nullable|string',
            'bid_validity_days'   => 'nullable|integer|min:1|max:365',
            'sale_officer'      => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'terms_conditions'  => 'nullable|string',
            'deposit_required'  => 'nullable|numeric|min:0',
            'status'            => 'nullable|string|in:draft,active,completed,cancelled',
            'notes'             => 'nullable|string',
            'signatures'        => 'nullable|json',
        ]);

        DisposalSale::create($validated);

        return redirect()->back()->with('success', 'Disposal sale created.');
    }

    /**
     * Update a disposal sale.
     */
    public function update(Request $request, DisposalSale $sale)
    {
        $validated = $request->validate([
            'sale_type'         => 'required|string|in:Tawaran,Sebutharga,Lelongan',
            'sale_reference'    => 'required|string|max:100|unique:disposal_sales,sale_reference,' . $sale->id,
            'sale_date'         => 'nullable|date',
            'sale_location'     => 'nullable|string|max:255',
            'viewing_date_start'  => 'nullable|date',
            'viewing_date_end'    => 'nullable|date|after_or_equal:viewing_date_start',
            'closing_datetime'    => 'nullable|date',
            'sealed_envelope_ref' => 'nullable|string|max:255',
            'tender_box_address'  => 'nullable|string',
            'bid_validity_days'   => 'nullable|integer|min:1|max:365',
            'sale_officer'      => 'nullable|string|max:255',
            'description'       => 'nullable|string',
            'terms_conditions'  => 'nullable|string',
            'deposit_required'  => 'nullable|numeric|min:0',
            'sale_status'       => 'nullable|string|in:open,closed,cancelled,awarded',
            'decision_date'     => 'nullable|date',
            'decision_notes'    => 'nullable|string',
            'report_date'       => 'nullable|date',
            'report_notes'      => 'nullable|string',
            'certificate_date'  => 'nullable|date',
            'certificate_type'  => 'nullable|string|in:Tawaran,Sebutharga,Lelongan,Pelupusan,Lupus',
            'certificate_reference' => 'nullable|string|max:100',
            'status'            => 'nullable|string|in:draft,active,completed,cancelled',
            'notes'             => 'nullable|string',
            'signatures'        => 'nullable|json',
        ]);

        $sale->update($validated);

        return redirect()->back()->with('success', 'Disposal sale updated.');
    }

    /**
     * Delete a disposal sale.
     */
    public function destroy(DisposalSale $sale)
    {
        $sale->delete();
        return redirect()->route('disposal-sales.index')->with('success', 'Disposal sale deleted.');
    }

    // ─── KEW.PA Form Views ────────────────────────────────────────────────────

    public function kewpa21(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset']);
        return inertia('DisposalSales/Kewpa21', ['sale' => $sale]);
    }

    public function kewpa22(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset']);
        return inertia('DisposalSales/Kewpa22', ['sale' => $sale]);
    }

    public function kewpa23(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset']);
        return inertia('DisposalSales/Kewpa23', ['sale' => $sale]);
    }

    public function kewpa24(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset', 'disposalSaleItems.saleBids']);
        return inertia('DisposalSales/Kewpa24', ['sale' => $sale]);
    }

    public function kewpa25(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset', 'disposalSaleItems.saleBids']);
        return inertia('DisposalSales/Kewpa25', ['sale' => $sale]);
    }

    public function kewpa26(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset', 'disposalSaleItems.saleBids']);
        return inertia('DisposalSales/Kewpa26', ['sale' => $sale]);
    }

    public function kewpa27(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset', 'disposalSaleItems.saleBids']);
        return inertia('DisposalSales/Kewpa27', ['sale' => $sale]);
    }

    public function kewpa27a(DisposalSale $sale)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset', 'disposalSaleItems.saleBids']);
        return inertia('DisposalSales/Kewpa27a', ['sale' => $sale]);
    }

    // ─── PDF Downloads ────────────────────────────────────────────────────────

    private function downloadKewpaPdf(string $view, DisposalSale $sale, string $formNumber)
    {
        $sale->load(['assetDisposal.asset', 'disposalSaleItems.asset', 'disposalSaleItems.saleBids']);

        return Pdf::view($view, ['sale' => $sale])
            ->format('a4')
            ->name("KEW-PA-{$formNumber}-{$sale->sale_reference}.pdf")
            ->withBrowsershot(function ($browsershot) {
                $browsershot->margins(15, 10, 15, 10)
                            ->showBrowserHeaderAndFooter()
                            ->addHeader('<span style="font-size:10px;">KEW.PA — Disposal Sale Document</span>')
                            ->addFooter('<span style="font-size:8px;">Page {page_number} of {total_pages}</span>');
            })
            ->download();
    }

    public function downloadKewpa21(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa21', $sale, '21'); }
    public function downloadKewpa22(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa22', $sale, '22'); }
    public function downloadKewpa23(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa23', $sale, '23'); }
    public function downloadKewpa24(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa24', $sale, '24'); }
    public function downloadKewpa25(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa25', $sale, '25'); }
    public function downloadKewpa26(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa26', $sale, '26'); }
    public function downloadKewpa27(DisposalSale $sale)  { return $this->downloadKewpaPdf('pdfs.kewpa27', $sale, '27'); }
    public function downloadKewpa27a(DisposalSale $sale) { return $this->downloadKewpaPdf('pdfs.kewpa27a', $sale, '27A'); }
}
