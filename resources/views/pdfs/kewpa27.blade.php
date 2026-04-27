<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-27 — Perakuan Pelupusan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; line-height: 1.5; color: #000; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0; text-transform: uppercase; }
        .header h2 { font-size: 14px; margin: 5px 0; }
        .header p { font-size: 11px; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #000; padding: 5px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f0f0f0; font-size: 11px; }
        td { font-size: 11px; }
        .label-cell { width: 30%; font-weight: bold; background-color: #f9f9f9; }
        .section-title { font-size: 12px; font-weight: bold; text-align: center; background-color: #e0e0e0; padding: 6px; margin-bottom: 10px; }
        .signature-row td { height: 60px; vertical-align: bottom; }
        .footer { text-align: center; font-size: 10px; margin-top: 20px; padding-top: 10px; border-top: 1px solid #000; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-27</h1>
        <h2>PERAKUAN PELUPUSAN</h2>
        <p>(Disposal Certificate)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Sale Information -->
    <table>
        <tr>
            <th colspan="4">A. MAKLUMAT JUALAN / SALE INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Rujukan</td>
            <td>{{ $sale->sale_reference }}</td>
            <td class="label-cell">Jenis</td>
            <td>{{ $sale->sale_type }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Jualan</td>
            <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}</td>
            <td class="label-cell">Lokasi</td>
            <td>{{ $sale->sale_location ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Pegawai Bertanggungjawab</td>
            <td>{{ $sale->sale_officer ?? '-' }}</td>
            <td class="label-cell">Status</td>
            <td>{{ $sale->status ?? '-' }}</td>
        </tr>
    </table>

    <!-- Asset Disposal Information -->
    <table>
        <tr>
            <th colspan="4">B. MAKLUMAT PELUPUSAN / DISPOSAL INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Tag Aset</td>
            <td>{{ $sale->assetDisposal->asset->asset_tag ?? '-' }}</td>
            <td class="label-cell">Nama Aset</td>
            <td>{{ $sale->assetDisposal->asset->name ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Kategori</td>
            <td>{{ $sale->assetDisposal->asset->category ?? '-' }}</td>
            <td class="label-cell">No. Siri</td>
            <td>{{ $sale->assetDisposal->asset->serial_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Kaedah Pelupusan</td>
            <td>{{ $sale->assetDisposal->disposal_method ?? '-' }}</td>
            <td class="label-cell">Sebab Pelupusan</td>
            <td>{{ $sale->assetDisposal->disposal_reason ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Harga Belian (RM)</td>
            <td>{{ $sale->assetDisposal->asset ? number_format($sale->assetDisposal->asset->purchase_price, 2) : '-' }}</td>
            <td class="label-cell">Tarikh Pelupusan</td>
            <td>{{ $sale->assetDisposal->disposal_date ? $sale->assetDisposal->disposal_date->format('d/m/Y') : '-' }}</td>
        </tr>
    </table>

    <!-- Certificate Details -->
    <table>
        <tr>
            <th colspan="4">C. BUTIRAN PERAKUAN / CERTIFICATE DETAILS</th>
        </tr>
        <tr>
            <td class="label-cell">Jenis Perakuan</td>
            <td>{{ $sale->certificate_type ?? 'Pelupusan' }}</td>
            <td class="label-cell">Rujukan Perakuan</td>
            <td>{{ $sale->certificate_reference ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Perakuan</td>
            <td colspan="3">{{ $sale->certificate_date ? $sale->certificate_date->format('d/m/Y') : '-' }}</td>
        </tr>
    </table>

    <!-- Signatures -->
    <table>
        <tr>
            <th colspan="3">D. PENGESAHAN / ENDORSEMENT</th>
        </tr>
        <tr class="signature-row">
            <td style="width:33%;">
                <strong>Pegawai Pelupusan:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:33%;">
                <strong>Pengerusi Jawatankuasa:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:33%;">
                <strong>Pendaftar:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>KEW.PA-27 — Perakuan Pelupusan | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
