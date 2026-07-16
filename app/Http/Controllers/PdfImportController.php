<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Models\MasterItem;
use App\Services\OcrService;

class PdfImportController extends Controller
{
    /**
     * Show the PDF import upload page.
     */
    public function index()
    {
        return inertia('PdfImport/Index', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Upload and OCR a PDF, return preview data.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file'      => 'required|file|mimes:pdf|max:102400',
            'type'      => 'required|in:auto,po,do,quotation',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('ocr-temp', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $json = $this->runOcr($fullPath, $request->input('type', 'auto'));

            if (!$json) {
                return back()->withErrors(['file' => 'OCR extraction failed. Ensure Tesseract is installed on the server.']);
            }

            $data = json_decode($json, true);
            if (!$data) {
                return back()->withErrors(['file' => 'Failed to parse OCR output.']);
            }

            session()->flash('ocr_preview', $data);
            session()->flash('ocr_file', $path);
            session()->flash('ocr_type', $request->input('type'));

            return inertia('PdfImport/Index', [
                'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
                'preview'   => $data,
                'file_name' => $file->getClientOriginalName(),
            ]);

        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'OCR error: ' . $e->getMessage()]);
        } finally {
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    /**
     * Confirm the OCR preview and import data into master_items.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.description'      => 'required|string',
            'items.*.item_code'        => 'nullable|string',
            'items.*.brand'            => 'nullable|string',
            'items.*.model'            => 'nullable|string',
            'items.*.category'         => 'nullable|string',
            'items.*.quantity_ordered' => 'nullable|integer|min:1',
            'items.*.unit'             => 'nullable|string',
            'items.*.unit_price'       => 'nullable|numeric|min:0',
            'items.*.serial_number'    => 'nullable|string',
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'supplier_name' => 'nullable|string|max:255',
        ]);

        $items = $request->items;
        $supplierId = $request->supplier_id;
        $supplierName = $request->supplier_name;

        $supplier = null;
        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
        } elseif ($supplierName) {
            $supplier = Supplier::firstOrCreate(
                ['name' => $supplierName],
                ['is_active' => true]
            );
        }

        $count = $this->importAsMasterItems($items, $supplier);

        return redirect()->route('pdf-import.index')
            ->with('success', "{$count} items imported into master equipment list.");
    }

    /**
     * Import items into master_items register.
     */
    private function importAsMasterItems(array $items, ?Supplier $supplier): int
    {
        $count = 0;
        $existingCount = MasterItem::count();

        foreach ($items as $i => $item) {
            $unitPrice = !empty($item['unit_price']) ? (float) $item['unit_price'] : 0;

            MasterItem::create([
                'item_no'       => $item['item_code'] ?? ($existingCount + $i + 1),
                'category'      => mb_substr($item['category'] ?? 'Uncategorized', 0, 255),
                'name'          => mb_substr($item['description'] ?? '(no name)', 0, 1000),
                'brand'         => isset($item['brand']) ? mb_substr($item['brand'], 0, 255) : null,
                'serial_numbers'=> $item['serial_number'] ?? null,
                'unit_price'    => $unitPrice,
                'total_price'   => $unitPrice * max(1, (int) ($item['quantity_ordered'] ?? 1)),
                'sodo_bayaran'  => null,
                'supplier_id'   => $supplier?->id,
                'quantity'      => (int) ($item['quantity_ordered'] ?? 1),
                'status'        => 'belum_diterima',
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Run the cairo-ocr Python script.
     */
    private function runOcr(string $pdfPath, string $type): ?string
    {
        return app(OcrService::class)->runOcr($pdfPath, $type);
    }
}
