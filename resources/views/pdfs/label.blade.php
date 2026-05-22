<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Label Aset — {{ $asset->asset_tag }}</title>
    <style>
        @page {
            margin: 0;
            size: A4;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 10px;
            color: #000;
        }

        /* ── Two labels per A4 sheet ── */
        .page {
            width: 210mm;
            height: 297mm;
            display: flex;
            flex-direction: column;
        }

        .label {
            width: 210mm;
            height: 148.5mm; /* half of A4 */
            box-sizing: border-box;
            padding: 12mm 15mm;
            border-bottom: 1px dashed #999;
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
        }
        .label:last-child {
            border-bottom: none;
        }

        .header {
            text-align: center;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 0.05em;
            color: #5C001F;
            border-bottom: 2px solid #F8A617;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .barcode-container {
            text-align: center;
            margin: 8px 0;
        }
        .barcode-container img {
            max-width: 80%;
            height: 50px;
        }

        .barcode-text {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.15em;
            margin-top: 2px;
        }

        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 12px;
            margin-top: 6px;
            border: 1px solid #ccc;
            padding: 8px 10px;
            border-radius: 4px;
            flex: 1;
            align-content: flex-start;
        }

        .info-item {
            flex: 1 1 45%;
            font-size: 10px;
            line-height: 1.5;
        }

        .info-item .label-text {
            color: #666;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.04em;
        }

        .info-item .value {
            font-weight: 700;
            font-size: 11px;
            color: #1E1B18;
        }

        .footer {
            text-align: center;
            font-size: 7px;
            color: #999;
            margin-top: auto;
            padding-top: 4px;
            border-top: 1px solid #eee;
        }

        @media print {
            .label:last-child {
                page-break-after: avoid;
            }
            .label:nth-child(odd) {
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- ── Label 1 ── --}}
        <div class="label">
            <div class="header">UNIVERSITI TEKNOLOGI MALAYSIA &mdash; CAIRO</div>

            <div class="barcode-container">
                @php
                    $barcodeVal = $asset->national_code ?? $asset->asset_tag;
                    $barcodePng = (new \Picqer\Barcode\BarcodeGeneratorPNG())->getBarcode($barcodeVal, \Picqer\Barcode\BarcodeGeneratorPNG::TYPE_CODE_128, 2, 55);
                    $barcodeB64 = base64_encode($barcodePng);
                @endphp
                <img src="data:image/png;base64,{{ $barcodeB64 }}" alt="{{ $barcodeVal }}">
            </div>
            <div class="barcode-text">{{ $barcodeVal }}</div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="label-text">Tag Aset</div>
                    <div class="value">{{ $asset->asset_tag }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Nama Aset</div>
                    <div class="value">{{ $asset->name }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Kategori</div>
                    <div class="value">{{ $asset->category ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Lokasi</div>
                    <div class="value">{{ $asset->location ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Jenis</div>
                    <div class="value">{{ $asset->asset_type === 'fixed_asset' ? 'Aset Tetap' : 'Inventori' }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Harga (RM)</div>
                    <div class="value">{{ number_format($asset->purchase_price, 2) }}</div>
                </div>
            </div>

            <div class="footer">Label Aset — CAIRO Asset Management System</div>
        </div>

        {{-- ── Label 2 (duplicate, same asset) ── --}}
        <div class="label">
            <div class="header">UNIVERSITI TEKNOLOGI MALAYSIA &mdash; CAIRO</div>

            <div class="barcode-container">
                <img src="data:image/png;base64,{{ $barcodeB64 }}" alt="{{ $barcodeVal }}">
            </div>
            <div class="barcode-text">{{ $barcodeVal }}</div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="label-text">Tag Aset</div>
                    <div class="value">{{ $asset->asset_tag }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Nama Aset</div>
                    <div class="value">{{ $asset->name }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Kategori</div>
                    <div class="value">{{ $asset->category ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Lokasi</div>
                    <div class="value">{{ $asset->location ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Jenis</div>
                    <div class="value">{{ $asset->asset_type === 'fixed_asset' ? 'Aset Tetap' : 'Inventori' }}</div>
                </div>
                <div class="info-item">
                    <div class="label-text">Harga (RM)</div>
                    <div class="value">{{ number_format($asset->purchase_price, 2) }}</div>
                </div>
            </div>

            <div class="footer">Label Aset — CAIRO Asset Management System</div>
        </div>
    </div>
</body>
</html>
