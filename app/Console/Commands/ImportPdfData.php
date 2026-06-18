<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\DeliveryOrder;
use App\Models\DoLineItem;

class ImportPdfData extends Command
{
    protected $signature = 'cairo:import-pdf
        {path : Path to the PDF file}
        {--type=auto : Document type (auto|po|do|quotation)}
        {--dry-run : Preview without importing}
        {--supplier= : Supplier ID or name to associate}
        {--ref= : Override document reference number}';

    protected $description = 'OCR and import procurement document data from PDF';

    private string $pythonCmd;
    private string $ocrScript;

    public function __construct()
    {
        parent::__construct();

        // Detect Windows Python — use WSL-compatible /mnt/c/ path
        $this->pythonCmd = 'python3';
        $winPython = '/mnt/c/Users/cairo/AppData/Local/Programs/Python/Python314/python.exe';

        // Prefer Windows Python since it has PyMuPDF + Tesseract installed
        if (file_exists($winPython)) {
            $this->pythonCmd = $winPython;
        }

        $this->ocrScript = base_path('bin/cairo-ocr');
    }

    public function handle(): int
    {
        $pdfPath = $this->argument('path');

        if (!file_exists($pdfPath)) {
            $this->error("File not found: {$pdfPath}");
            return self::FAILURE;
        }

        // Step 1: Run OCR pipeline
        $this->line("Running OCR pipeline on: {$pdfPath}");
        $this->line("  Type: {$this->option('type')}");
        if ($this->option('dry-run')) {
            $this->line("  Mode: DRY RUN (no data will be saved)");
        }

        $json = $this->runOcr($pdfPath);

        if (!$json) {
            $this->error('OCR extraction failed. Is Tesseract + PyMuPDF installed?');
            return self::FAILURE;
        }

        $data = json_decode($json, true);
        if (!$data) {
            $this->error('Failed to parse OCR output.');
            return self::FAILURE;
        }

        // Step 2: Display summary
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['Document Type', strtoupper($data['document_type'] ?? 'unknown')],
                ['Reference', $data['reference'] ?? 'N/A'],
                ['Supplier', $data['supplier'] ?? 'N/A'],
                ['Date', $data['date'] ?? 'N/A'],
                ['Total Amount', $data['total_amount'] ? 'RM ' . number_format($data['total_amount'], 2) : 'N/A'],
                ['Items Found', count($data['items'] ?? [])],
                ['Method', $data['method'] ?? 'unknown'],
            ]
        );

        $items = $data['items'] ?? [];
        if (empty($items)) {
            $this->warn('No items were extracted from this document.');
            $this->line('Raw text preview:');
            $this->line(substr($data['raw_text'] ?? '', 0, 500));
            return self::SUCCESS;
        }

        // Show first 10 items
        $this->newLine();
        $this->line('Preview of extracted items:');
        $this->table(
            ['#', 'Code', 'Description', 'Qty', 'Unit Price'],
            collect($items)->take(10)->map(fn ($item, $i) => [
                $i + 1,
                $item['item_code'] ?? '-',
                mb_substr($item['description'] ?? '', 50),
                $item['quantity_ordered'] ?? 1,
                $item['unit_price'] ? 'RM ' . number_format($item['unit_price'], 2) : '-',
            ])->toArray()
        );

        if (count($items) > 10) {
            $this->line('  ... and ' . (count($items) - 10) . ' more items');
        }

        if ($this->option('dry-run')) {
            $this->info('Dry run complete. No data was saved.');
            return self::SUCCESS;
        }

        // Step 3: Ask for confirmation
        if (!$this->confirm('Import these ' . count($items) . ' items into the system?', true)) {
            $this->info('Import cancelled.');
            return self::SUCCESS;
        }

        // Step 4: Import
        $this->import($data, $pdfPath);

        return self::SUCCESS;
    }

    private function runOcr(string $pdfPath): ?string
    {
        $type = $this->option('type');

        // Windows Python needs Windows-style paths; run via powershell from WSL
        // Convert WSL /mnt/c/ paths to C:/ paths for Windows Python
        $winPdfPath = preg_replace('/^\/mnt\/([a-z])\//', '$1:/', $pdfPath);
        $winPdfPath = preg_replace('/^([A-Za-z]):\//', '$1:/', $winPdfPath);

        // Run via powershell.exe to give Windows Python proper Windows paths
        $pythonExe = 'C:/Users/cairo/AppData/Local/Programs/Python/Python314/python.exe';
        $ocrScript = str_replace('/mnt/c/', 'C:/', $this->ocrScript);
        $pdfArg = $winPdfPath;

        $cmd = 'powershell.exe -Command "'
             . $pythonExe . ' \"' . $ocrScript . '\" \"' . $pdfArg . '\"'
             . ' --type ' . $type . ' --format json 2>&1"';

        $output = shell_exec($cmd);
        return $output;
    }

    private function import(array $data, string $pdfPath): void
    {
        $docType = $data['document_type'] ?? 'unknown';
        $items = $data['items'] ?? [];

        // Find or create supplier
        $supplierName = $this->option('supplier') ?? $data['supplier'] ?? null;
        $supplier = null;

        if ($supplierName) {
            // Check if it's an ID or name
            if (is_numeric($supplierName)) {
                $supplier = Supplier::find((int) $supplierName);
            } else {
                $supplier = Supplier::firstOrCreate(
                    ['name' => $supplierName],
                    ['is_active' => true]
                );
            }
        }

        $reference = $this->option('ref') ?? $data['reference'] ?? null;
        $userId = auth()->id() ?? 1;

        $this->newLine();
        $this->line('Importing...');

        if ($docType === 'po' || $docType === 'quotation') {
            $this->importAsPurchaseOrder($data, $items, $supplier, $reference, $userId, $pdfPath);
        } elseif ($docType === 'do') {
            $this->importAsDeliveryOrder($data, $items, $supplier, $reference, $userId, $pdfPath);
        } else {
            // Ask user what to create
            $choice = $this->choice(
                'Unknown document type. Import as?',
                ['purchase_order', 'delivery_order', 'skip'],
                'purchase_order'
            );

            match ($choice) {
                'purchase_order' => $this->importAsPurchaseOrder($data, $items, $supplier, $reference, $userId, $pdfPath),
                'delivery_order' => $this->importAsDeliveryOrder($data, $items, $supplier, $reference, $userId, $pdfPath),
                'skip' => $this->info('Skipped.'),
            };
        }

        $this->info('Import complete!');
    }

    private function importAsPurchaseOrder(
        array $data, array $items, ?Supplier $supplier,
        ?string $reference, int $userId, string $pdfPath
    ): void {
        $po = PurchaseOrder::create([
            'po_no'         => $reference ?? 'MANUAL-' . now()->format('YmdHis'),
            'supplier_id'   => $supplier?->id,
            'order_date'    => $this->parseDate($data['date'] ?? null),
            'grand_total'   => $data['total_amount'],
            'currency'      => 'MYR',
            'status'        => 'pending',
            'created_by'    => $userId,
        ]);

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        foreach ($items as $item) {
            $po->items()->create([
                'item_code'        => $item['item_code'] ?? null,
                'description'      => $item['description'] ?? '(no description)',
                'brand'            => $item['brand'] ?? null,
                'model'            => $item['model'] ?? null,
                'category'         => $item['category'] ?? null,
                'quantity_ordered' => $item['quantity_ordered'] ?? 1,
                'unit'             => $item['unit'] ?? 'unit',
                'unit_price'       => $item['unit_price'],
                'total_price'      => $item['total_price'],
                'status'           => 'pending',
            ]);
            $bar->advance();
        }
        $bar->finish();

        $this->newLine(2);
        $this->info("Created Purchase Order #{$po->po_no} with {$po->items()->count()} items.");

        // Attach the PDF as media
        if (file_exists($pdfPath)) {
            $po->addMedia($pdfPath)
               ->toMediaCollection('po_documents');
            $this->line('PDF attached to PO record.');
        }
    }

    private function importAsDeliveryOrder(
        array $data, array $items, ?Supplier $supplier,
        ?string $reference, int $userId, string $pdfPath
    ): void {
        $do = DeliveryOrder::create([
            'do_no'       => $reference ?? 'MANUAL-' . now()->format('YmdHis'),
            'supplier_id' => $supplier?->id,
            'po_reference'=> $data['reference'] ?? null,
            'status'      => 'pending',
            'total_pages' => $data['page_count'] ?? 1,
            'created_by'  => $userId,
        ]);

        $bar = $this->output->createProgressBar(count($items));
        $bar->start();

        foreach ($items as $item) {
            $do->lineItems()->create([
                'item_code'        => $item['item_code'] ?? null,
                'description'      => $item['description'] ?? '(no description)',
                'brand'            => $item['brand'] ?? null,
                'model'            => $item['model'] ?? null,
                'serial_number'    => $item['serial_number'] ?? null,
                'category'         => $item['category'] ?? null,
                'quantity_ordered' => $item['quantity_ordered'] ?? 1,
                'unit'             => $item['unit'] ?? 'unit',
                'status'           => 'pending',
            ]);
            $bar->advance();
        }
        $bar->finish();

        $this->newLine(2);
        $this->info("Created Delivery Order #{$do->do_no} with {$do->lineItems()->count()} items.");

        if (file_exists($pdfPath)) {
            $do->addMedia($pdfPath)
               ->toMediaCollection('do_documents');
            $this->line('PDF attached to DO record.');
        }
    }

    private function parseDate(?string $dateStr): ?string
    {
        if (!$dateStr) return null;

        // Try common formats
        $formats = [
            'd/m/Y', 'd/m/y', 'Y-m-d', 'd M Y', 'd F Y',
            'j/n/Y', 'j/n/y', 'j M Y', 'd M y',
        ];

        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $dateStr);
            if ($dt) {
                return $dt->format('Y-m-d');
            }
        }

        // Try strtotime as fallback
        $ts = strtotime($dateStr);
        if ($ts !== false) {
            return date('Y-m-d', $ts);
        }

        return null;
    }
}
