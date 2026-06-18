<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\DeliveryOrder;
use App\Models\DoLineItem;

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
            'file'      => 'required|file|mimes:pdf|max:102400', // 100MB
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

            // Store preview in session for confirmation step
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
            // Clean up temp file
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }

    /**
     * Confirm the OCR preview and import data.
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
            'document_type' => 'required|string|in:po,do,quotation',
            'reference'     => 'nullable|string|max:255',
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'supplier_name' => 'nullable|string|max:255',
        ]);

        $items = $request->items;
        $docType = $request->document_type;
        $reference = $request->reference;
        $supplierId = $request->supplier_id;
        $supplierName = $request->supplier_name;
        $userId = auth()->id();

        $supplier = null;
        if ($supplierId) {
            $supplier = Supplier::find($supplierId);
        } elseif ($supplierName) {
            $supplier = Supplier::firstOrCreate(
                ['name' => $supplierName],
                ['is_active' => true]
            );
        }

        if ($docType === 'po' || $docType === 'quotation') {
            $record = $this->importAsPurchaseOrder($items, $supplier, $reference, $userId);
            $type = 'Purchase Order';
        } elseif ($docType === 'do') {
            $record = $this->importAsDeliveryOrder($items, $supplier, $reference, $userId);
            $type = 'Delivery Order';
        } else {
            return back()->withErrors(['document_type' => 'Invalid document type.']);
        }

        return redirect()->route('pdf-import.index')
            ->with('success', "{$type} #{$record->reference} created with {$record->count} items.");
    }

    /**
     * Import items as a Purchase Order (from PO or quotation).
     */
    private function importAsPurchaseOrder(array $items, ?Supplier $supplier, ?string $reference, int $userId): object
    {
        $po = PurchaseOrder::create([
            'po_no'       => $reference ?? 'IMPORT-' . now()->format('YmdHis'),
            'supplier_id' => $supplier?->id,
            'status'      => 'pending',
            'created_by'  => $userId,
        ]);

        foreach ($items as $item) {
            $unitPrice = !empty($item['unit_price']) ? (float) $item['unit_price'] : null;
            $totalPrice = $unitPrice && !empty($item['quantity_ordered'])
                ? $unitPrice * (int) $item['quantity_ordered']
                : null;

            $po->items()->create([
                'item_code'        => $item['item_code'] ?? null,
                'description'      => $item['description'],
                'brand'            => $item['brand'] ?? null,
                'model'            => $item['model'] ?? null,
                'category'         => $item['category'] ?? null,
                'quantity_ordered' => (int) ($item['quantity_ordered'] ?? 1),
                'unit'             => $item['unit'] ?? 'unit',
                'unit_price'       => $unitPrice,
                'total_price'      => $totalPrice,
                'status'           => 'pending',
            ]);
        }

        return (object) [
            'reference' => $po->po_no,
            'count'     => count($items),
        ];
    }

    /**
     * Import items as a Delivery Order.
     */
    private function importAsDeliveryOrder(array $items, ?Supplier $supplier, ?string $reference, int $userId): object
    {
        $do = DeliveryOrder::create([
            'do_no'       => $reference ?? 'IMPORT-' . now()->format('YmdHis'),
            'supplier_id' => $supplier?->id,
            'status'      => 'pending',
            'created_by'  => $userId,
        ]);

        foreach ($items as $item) {
            $do->lineItems()->create([
                'item_code'        => $item['item_code'] ?? null,
                'description'      => $item['description'],
                'brand'            => $item['brand'] ?? null,
                'model'            => $item['model'] ?? null,
                'serial_number'    => $item['serial_number'] ?? null,
                'category'         => $item['category'] ?? null,
                'quantity_ordered' => (int) ($item['quantity_ordered'] ?? 1),
                'unit'             => $item['unit'] ?? 'unit',
                'status'           => 'pending',
            ]);
        }

        return (object) [
            'reference' => $do->do_no,
            'count'     => count($items),
        ];
    }

    /**
     * Run the cairo-ocr Python script.
     */
    private function runOcr(string $pdfPath, string $type): ?string
    {
        $pythonCmd = 'python3';
        $winPython = '/mnt/c/Users/cairo/AppData/Local/Programs/Python/Python314/python.exe';

        if (file_exists($winPython)) {
            $pythonCmd = '"' . $winPython . '"';
        }

        $ocrScript = base_path('bin/cairo-ocr');
        $cmd = "{$pythonCmd} \"{$ocrScript}\" "
             . escapeshellarg($pdfPath)
             . " --type {$type} --format json 2>/dev/null";

        return shell_exec($cmd);
    }
}
