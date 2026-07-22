<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Centralized OCR service for CAIRO Inventory.
 *
 * Runs the cairo-ocr Python pipeline against a PDF and returns structured JSON.
 * Calls the Windows Python installation directly — no WSL bridge needed.
 */
class OcrService
{
    /**
     * Run OCR on a PDF file and return the raw JSON output.
     *
     * @param  string  $pdfPath  Absolute path to the PDF
     * @param  string  $type     Document type hint: auto|po|do|quotation
     * @return string|null       Raw JSON string from the OCR script, or null on failure
     */
    public function runOcr(string $pdfPath, string $type = 'auto'): ?string
    {
        $winPython = $this->findPython();
        $ocrScript = base_path('bin/cairo-ocr');
        $tessdataDir = $this->getTessdataDir();

        // Set TESSDATA_PREFIX so Python/Tesseract find msa+eng language data
        putenv("TESSDATA_PREFIX={$tessdataDir}");

        // Build command — shell_exec on Windows runs via cmd.exe.
        // No bash -c wrapper, so escapeshellarg's double-quoting is safe.
        $cmd = sprintf(
            '%s %s %s --type %s --format json 2>&1',
            escapeshellarg($winPython),
            escapeshellarg($ocrScript),
            escapeshellarg($pdfPath),
            $type
        );

        $result = shell_exec($cmd);

        if ($result === null || trim($result) === '') {
            Log::warning('cairo-ocr returned empty output', [
                'python' => $winPython,
                'script' => $ocrScript,
                'pdf'    => $pdfPath,
                'tessdata' => $tessdataDir,
            ]);
            return null;
        }

        return $result;
    }

    /**
     * Locate the Windows Python executable.
     */
    private function findPython(): string
    {
        $candidates = [
            'C:/Users/cairo/AppData/Local/Programs/Python/Python314/python.exe',
            'C:/Users/cairo/AppData/Local/Programs/Python/Python313/python.exe',
            'python',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return 'python';
    }

    /**
     * Get the tessdata directory containing msa+eng traineddata.
     * Uses C:\Windows\Temp\tessdata_cairo which is writable and
     * consistent regardless of PHP's sys_get_temp_dir() value.
     */
    private function getTessdataDir(): string
    {
        $dir = 'C:/Windows/Temp/tessdata_cairo';
        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }
        return $dir;
    }
}
