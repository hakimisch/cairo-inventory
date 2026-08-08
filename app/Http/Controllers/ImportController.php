<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Models\MasterItem;
use App\Services\OcrService;

class ImportController extends Controller
{
    /**
     * Show the unified import page.
     */
    public function index()
    {
        return inertia('Import/Index', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'preview'   => session('import_preview'),
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

        // GET request — just redirect back to import form
        if ($request->isMethod('get')) {
            return redirect()->route('import.index');
        }

        // Case 1: File upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'pdf') {
                // PDF OCR can take 60-180s — allow unlimited execution
                set_time_limit(0);
                $result = $this->parsePdf($file, $request->input('type_hint', 'auto'));
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

        $previewData = [
            'items'      => $items,
            'metadata'   => $metadata ?? [],
            'raw_text'   => ($sourceType === 'pdf' && isset($result)) ? ($result['raw_text'] ?? null) : null,
            'source'     => $sourceType,
            'file_name'  => $request->hasFile('file') ? $file->getClientOriginalName() : null,
            'item_count' => count($items),
        ];

        // Confidence score is only meaningful for OCR-processed PDFs
        if ($sourceType === 'pdf' && isset($result)) {
            $previewData['confidence'] = $result['confidence'] ?? null;
        }

        session()->flash('import_preview', $previewData);

        return inertia('Import/Index', [
            'suppliers' => Supplier::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'preview'   => $previewData,
        ]);
    }

    /**
     * Confirm import: save items into master_items register.
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
            // ── Extended fields ─────────────────────────────────────────
            'items.*.specification'    => 'nullable|string',
            'items.*.country_of_origin' => 'nullable|string',
            'items.*.total_price'      => 'nullable|numeric|min:0',
            'items.*.sodo_bayaran'     => 'nullable|string',
            'items.*.supplier_name'    => 'nullable|string',
            'items.*.do_no'            => 'nullable|string',
            'items.*.invoice_no'       => 'nullable|string',
            'items.*.received_date'    => 'nullable|string',
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

        $result = $this->importAsMasterItems($items, $supplier);

        $message = "{$result['imported']} items imported into master equipment list.";
        if ($result['skipped'] > 0) {
            $message .= " {$result['skipped']} duplicates skipped.";
        }

        return redirect()->route('import.index')
            ->with('success', $message);
    }

    // ─── Import into master_items ─────────────────────────────────────────

    private function importAsMasterItems(array $items, ?Supplier $supplier): array
    {
        return app(\App\Services\MasterItemImportService::class)->importAsMasterItems($items, $supplier);
    }

    // ─── Parsers ───────────────────────────────────────────────────────────

    private function parsePdf($file, string $typeHint = 'auto'): array
    {
        $path = $file->store('import-temp', 'local');
        $fullPath = Storage::disk('local')->path($path);

        try {
            $json = $this->runOcr($fullPath, $typeHint);
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
        // Use fgetcsv for proper quoting support (handles multi-line quoted fields)
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) return [];

        $header = array_map('strtolower', fgetcsv($handle));
        if (empty($header)) {
            fclose($handle);
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle)) !== false) {
            $rows[] = $row;
        }
        fclose($handle);

        $data = $rows;

        $fieldMap = [
            'description'     => ['description', 'nama item', 'item description', 'nama', 'name', 'perkara', 'butiran', 'product'],
            'item_code'       => ['item_code', 'no item', 'no.', 'item code', 'code', 'part_no', 'part number', 'bil', 'number'],
            'brand'           => ['brand', 'jenama', 'manufacturer', 'make', 'merk'],
            'model'           => ['model', 'type', 'tipe', 'part'],
            'serial'          => ['serial', 'serial_number', 'serial number', 'no siri', 'sn', 's/n', 'no.siri'],
            'category'        => ['category', 'kategori', 'jenis', 'section'],
            'qty'             => ['quantity', 'qty', 'kuantiti', 'kuantiti', 'quantity_ordered', 'ordered'],
            'unit'            => ['unit_of_measure', 'uom', 'unit'],
            'price'           => ['price', 'harga seunit', 'unit_price', 'harga', 'cost', 'amount'],
            // ── Master list extended fields ─────────────────────────────────
            'specification'   => ['specification', 'spesifikasi', 'spek', 'spec', 'technical specification'],
            'country_of_origin' => ['negara asal alat', 'country_of_origin', 'negara asal', 'country of origin', 'origin'],
            'total_price'     => ['jumlah harga', 'total_price', 'total price', 'jumlah', 'total'],
            'sodo_bayaran'    => ['sodo bayaran', 'sodo_bayaran', 'sodo', 'so/do'],
            'supplier_name'   => ['pembekal', 'supplier_name', 'supplier', 'vendor', 'pembekal'],
            'do_no'           => ['no do', 'do_no', 'do no', 'delivery order no', 'do number'],
            'invoice_no'      => ['invoice_no', 'invoice no', 'no invois', 'invoice number', 'inv', 'invois'],
            'received_date'   => ['date received', 'received_date', 'tarikh terima', 'tarikh diterima', 'received date', 'date_received', 'tarikh'],
            'status'          => ['status', 'item status'],
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
                // Brand-model split: If the brand value looks like "BRAND ModelXYZ"
                // and no separate model column was found, split on first space.
                $brand = $item['brand'] ?? null;
                $modelRaw = $item['model'] ?? null;
                if ($brand !== null && $modelRaw === null && str_contains($brand, ' ')) {
                    $parts = explode(' ', $brand, 2);
                    $brand = $parts[0];
                    $modelRaw = $parts[1];
                }

                $items[] = [
                    'description'      => $item['description'] ?? $item['item_code'] ?? '',
                    'item_code'        => $item['item_code'] ?? null,
                    'brand'            => $brand,
                    'model'            => $modelRaw,
                    'serial_number'    => $item['serial'] ?? null,
                    'category'         => $item['category'] ?? null,
                    'quantity_ordered' => (int) ($item['qty'] ?? 1),
                    'unit'             => $item['unit'] ?? 'unit',
                    'unit_price'       => $this->parsePrice($item['price'] ?? null),
                    // ── Extended fields ──────────────────────────────────
                    'specification'    => $item['specification'] ?? null,
                    'country_of_origin' => $item['country_of_origin'] ?? null,
                    'total_price'      => $this->parsePrice($item['total_price'] ?? null),
                    'sodo_bayaran'     => $item['sodo_bayaran'] ?? null,
                    'supplier_name'    => $item['supplier_name'] ?? null,
                    'do_no'            => $item['do_no'] ?? null,
                    'invoice_no'       => $item['invoice_no'] ?? null,
                    'received_date'    => $item['received_date'] ?? null,
                    'status_import'    => $item['status'] ?? null,
                ];
            }
        }

        return $items;
    }

    private function parseExcel($file): array
    {
        try {
            if (class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
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
        }

        return $this->parseCsv($file);
    }

    private function parsePastedText(string $text): array
    {
        $lines = explode("\n", trim($text));
        if (count($lines) >= 2) {
            $csvPath = tempnam(sys_get_temp_dir(), 'csv') . '.csv';
            file_put_contents($csvPath, $text);
            $items = $this->parseCsv(new \Illuminate\Http\UploadedFile($csvPath, 'paste.csv', 'text/csv', null, true));
            @unlink($csvPath);
            if (!empty($items)) return $items;
        }

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

    /**
     * Find the best-matching column index for a set of aliases.
     *
     * Scans ALL header columns and returns the one with the LONGEST matching
     * alias (longer = more specific). Prevents short aliases like 'no' or 'item'
     * or 'unit' from accidentally matching unrelated columns ('no item' vs 'no',
     * 'nama item' vs 'item', 'harga seunit' vs 'unit').
     *
     * Exact matches always win immediately. Prefix/suffix and substring matches
     * use word-boundary detection for short aliases (< 5 chars) to avoid false
     * positives like 'unit' matching inside 'seunit'.
     */
    private function findColumnIndex(array $header, array $aliases): ?int
    {
        // Sort aliases longest-first so specific matches are scored first
        usort($aliases, fn($a, $b) => strlen($b) - strlen($a));

        $bestIdx = null;
        $bestAliasLen = 0;

        foreach ($header as $idx => $col) {
            $normalized = trim(strtolower($col));

            foreach ($aliases as $alias) {
                $aliasLow = strtolower(trim($alias));

                // Exact match = highest priority, return immediately
                if ($normalized === $aliasLow) {
                    return $idx;
                }

                // Prefix/suffix match — with word-boundary guard for short aliases
                $prefixOk = str_starts_with($normalized, $aliasLow)
                    && (strlen($aliasLow) >= 5
                        || strlen($normalized) === strlen($aliasLow)
                        || !ctype_alnum($normalized[strlen($aliasLow)] ?? ' '));
                $suffixOk = str_ends_with($normalized, $aliasLow)
                    && (strlen($aliasLow) >= 5
                        || strlen($normalized) === strlen($aliasLow)
                        || ($strlen = strlen($normalized)) > strlen($aliasLow)
                           && !ctype_alnum($normalized[$strlen - strlen($aliasLow) - 1]));

                if ($prefixOk || $suffixOk) {
                    if (strlen($aliasLow) > $bestAliasLen) {
                        $bestAliasLen = strlen($aliasLow);
                        $bestIdx = $idx;
                    }
                    continue; // prefix/suffix > plain substring
                }

                // Plain substring match — only for longer aliases (>= 5 chars)
                if (strlen($aliasLow) >= 5 && str_contains($normalized, $aliasLow)) {
                    if (strlen($aliasLow) > $bestAliasLen) {
                        $bestAliasLen = strlen($aliasLow);
                        $bestIdx = $idx;
                    }
                }
            }
        }

        return $bestIdx;
    }

    private function parsePrice(?string $value): ?float
    {
        if (!$value) return null;
        $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '', $value));
        return $cleaned !== '' ? (float) $cleaned : null;
    }

    private function runOcr(string $pdfPath, string $type = 'auto'): ?string
    {
        return app(OcrService::class)->runOcr($pdfPath, $type);
    }
}
