<?php

namespace App\Services;

/**
 * Centralized OCR service for CAIRO Inventory.
 *
 * Runs the cairo-ocr Python pipeline against a PDF and returns structured JSON.
 * All three import paths (ImportController, PdfImportController, ImportPdfData)
 * now delegate here instead of duplicating the shell command logic.
 */
class OcrService
{
    /**
     * Run OCR on a PDF file and return the raw JSON output.
     *
     * Standardizes on the Windows Python + PowerShell approach so that
     * Tesseract (installed on Windows) is always accessible. The Python
     * script's WSL self-re-exec detection becomes a no-op when called
     * this way, avoiding duplicate process spawns.
     *
     * @param  string  $pdfPath  Absolute path to the PDF (WSL or Windows format)
     * @param  string  $type     Document type hint: auto|po|do|quotation
     * @return string|null       Raw JSON string from the OCR script, or null on failure
     */
    public function runOcr(string $pdfPath, string $type = 'auto'): ?string
    {
        $pythonExe = $this->findPython();
        $ocrScript = base_path('bin/cairo-ocr');

        // Convert paths to Windows format for PowerShell
        $winScript = $this->toWindowsPath($ocrScript);
        $winPdf = $this->toWindowsPath($pdfPath);

        $cmd = sprintf(
            'powershell.exe -Command "%s \\"%s\\" \\"%s\\" --type %s --format json 2>&1"',
            $pythonExe,
            $winScript,
            $winPdf,
            $type
        );

        return shell_exec($cmd);
    }

    /**
     * Locate the Windows Python executable.
     */
    private function findPython(): string
    {
        $default = 'C:/Users/cairo/AppData/Local/Programs/Python/Python314/python.exe';
        $alt = 'C:/Users/cairo/AppData/Local/Programs/Python/Python313/python.exe';

        if (file_exists($default)) {
            return $default;
        }
        if (file_exists($alt)) {
            return $alt;
        }

        // Fallback — hope it's on PATH inside PowerShell
        return 'python';
    }

    /**
     * Convert a WSL /mnt/c/... path to Windows C:/... format.
     * Also handles paths already in Windows format (pass-through).
     */
    private function toWindowsPath(string $path): string
    {
        // Already a Windows path
        if (preg_match('/^[A-Za-z]:\\//', $path)) {
            return $path;
        }

        // WSL /mnt/c/... → C:/...
        return preg_replace('/^\\/mnt\\/([a-zA-Z])\\//', '$1:/', $path);
    }
}
