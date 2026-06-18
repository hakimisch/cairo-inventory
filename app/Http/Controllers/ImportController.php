<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\DeliveryOrder;
use App\Models\DoLineItem;

class ImportController extends Controller
{
    /**
     * Show the unified import page.
     */
    public function index()
    {
        return inertia('Import/Index', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Preview: accept PDF, CSV, Excel, or pasted text → return extracted items.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file'       => 'nullable|file|mimes:pdf,csv,txt,xls,xlsx|max:102400',
            'pasted'     => 'nullable|string',
            'type_hint'  => 'nullable|in:auto,po,do,quotation',
        ]);

        $items = [];
        $metadata = [];
        $sourceType = null;

        // Case 1: File upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'pdf') {
                $result = $this->parsePdf($file);
                $items = $result['items'];
                $metadata = $result['metadata'];
                $sourceType = 'pdf';
            } elseif (in_array($ext, ['csv', 'txt'])) {
                $items = $this->parseCsv($file);
                $sourceType = 'csv';
            } elseif (in_array($ext, ['xls', 'xlsx'])) {
                $items = $this->parseExcel($file);
                $sourceType = 'excel';
            }

        // Case 2: Pasted text
        } elseif ($request->filled('pasted')) {
            $items = $this->parsePastedText($request->pasted);
            $sourceType = 'pasted';
        }

        if (empty($items)) {
            return back()->withErrors(['file' => 'No items could be extracted from the provided data.']);
        }

        session()->flash('import_preview', [
            'items'      => $items,
            'metadata'   => $metadata ?? [],
            'raw_text'   => ($sourceType === 'pdf' && isset($result)) ? ($result['raw_text'] ?? null) : null,
            'confidence' => ($sourceType === 'pdf' && isset($result)) ? ($result['confidence'] ?? null) : null,
            'source'     => $sourceType,
            'file_name'  => $request->hasFile('file') ? $file->getClientOriginalName() : null,
        ]);

        return inertia('Import/Index', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'preview'   => [
                'items'      => $items,
                'metadata'   => $metadata ?? [],
                'raw_text'   => ($sourceType === 'pdf' && isset($result)) ? ($result['raw_text'] ?? null) : null,
                'confidence' => ($sourceType === 'pdf' && isset($result)) ? ($result['confidence'] ?? null) : null,
                'source'     => $sourceType,
                'file_name'  => $request->hasFile('file') ? $file->getClientOriginalName() : null,
                'item_count' => count($items),
            ],
        ]);
    }

    /**
     * Confirm import: save items as PO or DO.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'items'           => 'required|array|min:1',
            'items.*.description'      => 'required|string',
            'items.*.item_code'        => 'nullable|string',
            'items.*.brand'            => 'nullable|string',
            'items.*.model'            => 'nullable|string',
            'items.*.serial_number'    => 'nullable|string',
            'items.*.category'         => 'nullable|string',
            'items.*.quantity_ordered' => 'nullable|integer|min:1',
            'items.*.unit'             => 'nullable|string',
            'items.*.unit_price'       => 'nullable|numeric|min:0',
            'import_as'     => 'required|in:po,do',
            'reference'     => 'nullable|string|max:255',
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'supplier_name' => 'nullable|string|max:255',
        ]);

        $items = $request->items;
        $importAs = $request->import_as;
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

        if ($importAs === 'po') {
            $record = $this->importAsPo($items, $supplier, $reference, $userId);
            $type = 'Purchase Order';
        } else {
            $record = $this->importAsDo($items, $supplier, $reference, $userId);
            $type = 'Delivery Order';
        }

        return redirect()->route('import.index')
            ->with('success', "{$type} #{$record->reference} created with {$record->count} items.");
    }

    // ─── Importers ─────────────────────────────────────────────────────────

    private function importAsPo(array $items, ?Supplier $supplier, ?string $reference, int $userId): object
    {
        $po = PurchaseOrder::create([
            'po_no'       => $reference ?? 'IMPORT-' . now()->format('YmdHis'),
            'supplier_id' => $supplier?->id,
            'status'      => 'pending',
            'created_by'  => $userId,
        ]);

        foreach ($items as $item) {
            $unitPrice = !empty($item['unit_price']) ? (float) $item['unit_price'] : null;
            $po->items()->create([
                'item_code'        => $item['item_code'] ?? null,
                'description'      => $item['description'],
                'brand'            => $item['brand'] ?? null,
                'model'            => $item['model'] ?? null,
                'category'         => $item['category'] ?? null,
                'quantity_ordered' => (int) ($item['quantity_ordered'] ?? 1),
                'unit'             => $item['unit'] ?? 'unit',
                'unit_price'       => $unitPrice,
                'total_price'      => $unitPrice ? $unitPrice * (int) ($item['quantity_ordered'] ?? 1) : null,
                'status'           => 'pending',
            ]);
        }

        return (object) ['reference' => $po->po_no, 'count' => count($items)];
    }

    private function importAsDo(array $items, ?Supplier $supplier, ?string $reference, int $userId): object
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
                'has_serial'       => $item['has_serial'] ?? !empty($item['serial_number']),
                'status'           => 'pending',
            ]);
        }

        return (object) ['reference' => $do->do_no, 'count' => count($items)];
    }

    // ─── Parsers ───────────────────────────────────────────────────────────

    private function parsePdf($file): array
    {
        $path = $file->store('import-temp', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $json = $this->runOcr($fullPath);
            $data = json_decode($json, true);

            return [
                'items'      => $data['items'] ?? [],
                'raw_text'   => $data['raw_text'] ?? null,
                'confidence' => $data['confidence'] ?? null,
                'metadata'   => [
                    'reference' => $data['reference'] ?? null,
                    'supplier'  => $data['supplier'] ?? null,
                    'date'      => $data['date'] ?? null,
                    'total'     => $data['total_amount'] ?? null,
                    'type'      => $data['document_type'] ?? 'unknown',
                ],
            ];
        } finally {
            if (file_exists($fullPath)) @unlink($fullPath);
        }
    }

    private function parseCsv($file): array
    {
        $rows = array_map('str_getcsv', file($file->getRealPath()));
        if (empty($rows)) return [];

        $header = array_map('strtolower', $rows[0]);
        $data = array_slice($rows, 1);

        // Map common column names
        $fieldMap = [
            'description' => ['description', 'item', 'name', 'item description', 'product', 'perkara', 'butiran'],
            'item_code'   => ['item_code', 'code', 'item code', 'no', 'bil', 'number', 'part_no', 'part number'],
            'brand'       => ['brand', 'jenama', 'manufacturer', 'make', 'merk'],
            'model'       => ['model', 'type', 'tipe', 'part'],
            'serial'      => ['serial', 'serial_number', 'serial number', 'sn', 's/n', 'no siri', 'no.siri'],
            'category'    => ['category', 'kategori', 'jenis', 'section', 'category'],
            'qty'         => ['quantity', 'qty', 'kuantiti', 'quantity_ordered', 'ordered', 'jumlah'],
            'unit'        => ['unit', 'unit_of_measure', 'uom', 'measure'],
            'price'       => ['price', 'unit_price', 'harga', 'harga seunit', 'cost', 'amount'],
        ];

        $items = [];
        foreach ($data as $row) {
            $item = [];
            foreach ($fieldMap as $field => $aliases) {
                $idx = $this->findColumnIndex($header, $aliases);
                if ($idx !== null && isset($row[$idx]) && trim($row[$idx]) !== '') {
                    $item[$field] = trim($row[$idx]);
                }
            }
            if (!empty($item['description']) || !empty($item['item_code'])) {
                $items[] = [
                    'description'      => $item['description'] ?? $item['item_code'] ?? '',
                    'item_code'        => $item['item_code'] ?? null,
                    'brand'            => $item['brand'] ?? null,
                    'model'            => $item['model'] ?? null,
                    'serial_number'    => $item['serial'] ?? null,
                    'category'         => $item['category'] ?? null,
                    'quantity_ordered' => (int) ($item['qty'] ?? 1),
                    'unit'             => $item['unit'] ?? 'unit',
                    'unit_price'       => $this->parsePrice($item['price'] ?? null),
                ];
            }
        }

        return $items;
    }

    private function parseExcel($file): array
    {
        // Fallback: try reading as CSV first (many .xlsx files have embedded CSV)
        // For proper Excel parsing, PhpSpreadsheet should be installed
        try {
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
                // Reuse CSV parser logic
                $csvPath = tempnam(sys_get_temp_dir(), 'csv') . '.csv';
                $fp = fopen($csvPath, 'w');
                foreach ($rows as $row) {
                    fputcsv($fp, $row);
                }
                fclose($fp);
                $result = $this->parseCsv(new \Illuminate\Http\UploadedFile($csvPath, 'temp.csv', 'text/csv', null, true));
                @unlink($csvPath);
                return $result;
            }
        } catch (\Exception $e) {
            // Fall through to CSV attempt
        }

        // Try reading as CSV since some .xlsx files are actually CSV
        return $this->parseCsv($file);
    }

    private function parsePastedText(string $text): array
    {
        // Try parsing as CSV first (tab or comma separated)
        $lines = explode("\n", trim($text));
        if (count($lines) >= 2) {
            $csvPath = tempnam(sys_get_temp_dir(), 'csv') . '.csv';
            file_put_contents($csvPath, $text);
            $items = $this->parseCsv(new \Illuminate\Http\UploadedFile($csvPath, 'paste.csv', 'text/csv', null, true));
            @unlink($csvPath);
            if (!empty($items)) return $items;
        }

        // Fallback: line-by-line parsing (one item per line)
        $items = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || strlen($line) < 3) continue;
            $items[] = [
                'description'      => $line,
                'quantity_ordered' => 1,
                'unit'             => 'unit',
            ];
        }

        return $items;
    }

    // ─── Helpers ───────────────────────────────────────────────────────────

    private function findColumnIndex(array $header, array $aliases): ?int
    {
        foreach ($header as $idx => $col) {
            $col = trim(strtolower($col));
            foreach ($aliases as $alias) {
                if ($col === $alias || str_contains($col, $alias)) {
                    return $idx;
                }
            }
        }
        return null;
    }

    private function parsePrice(?string $value): ?float
    {
        if (!$value) return null;
        $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '', $value));
        return $cleaned !== '' ? (float) $cleaned : null;
    }

    private function runOcr(string $pdfPath): ?string
    {
        $type = 'auto';

        // Windows Python needs Windows-style paths; run via powershell from WSL
        $pythonExe = 'C:/Users/cairo/AppData/Local/Programs/Python/Python314/python.exe';
        $ocrScript = str_replace('/mnt/c/', 'C:/', base_path('bin/cairo-ocr'));

        // Convert WSL /mnt/c/ storage path to C:/ for Windows Python
        $pdfArg = preg_replace('/^\/mnt\/c\//i', 'C:/', $pdfPath);

        $cmd = 'powershell.exe -Command "'
             . $pythonExe . ' \"' . $ocrScript . '\" \"' . $pdfArg . '\"'
             . ' --type ' . $type . ' --format json 2>&1"';
        return shell_exec($cmd);
    }
}
