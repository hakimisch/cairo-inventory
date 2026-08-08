<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Supplier;
use App\Models\MasterItem;
use App\Services\OcrService;

class ImportPdfData extends Command
{
    protected $signature = 'cairo:import-pdf
        {path : Path to the PDF file}
        {--supplier= : Supplier ID or name to associate}
        {--ref= : Reference number for the document}
        {--dry-run : Preview without importing}';

    protected $description = 'OCR a PDF and import its items into the master equipment list';

    public function handle(): int
    {
        $pdfPath = $this->argument('path');

        if (!file_exists($pdfPath)) {
            $this->error("File not found: {$pdfPath}");
            return self::FAILURE;
        }

        // Step 1: Run OCR pipeline
        $this->line("Running OCR pipeline on: {$pdfPath}");

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
                ['Items Found', count($data['items'] ?? [])],
                ['Method', $data['method'] ?? 'unknown'],
            ]
        );

        $items = $data['items'] ?? [];
        if (empty($items)) {
            $this->warn('No items were extracted from this document.');
            return self::SUCCESS;
        }

        // Show preview
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
        if (!$this->confirm('Import these ' . count($items) . ' items into the master equipment list?', true)) {
            $this->info('Import cancelled.');
            return self::SUCCESS;
        }

        // Step 4: Import into master_items
        $this->import($data);

        return self::SUCCESS;
    }

    private function runOcr(string $pdfPath): ?string
    {
        return app(OcrService::class)->runOcr($pdfPath, 'auto');
    }

    private function import(array $data): void
    {
        $items = $data['items'] ?? [];

        // Find or create supplier
        $supplierName = $this->option('supplier') ?? $data['supplier'] ?? null;
        $supplier = null;

        if ($supplierName) {
            if (is_numeric($supplierName)) {
                $supplier = Supplier::find((int) $supplierName);
            } else {
                $supplier = Supplier::firstOrCreate(
                    ['name' => $supplierName],
                    ['is_active' => true]
                );
            }
        }

        $this->newLine();
        $this->line('Importing into master equipment list...');

        $service = app(\App\Services\MasterItemImportService::class);
        $result = $service->importAsMasterItems($items, $supplier);

        $this->info("Imported {$result['imported']} items into master equipment list.");
        if ($result['skipped'] > 0) {
            $this->warn("{$result['skipped']} duplicate items skipped.");
        }
        $this->line('Visit /verification to verify and create assets.');
    }
}
