<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-31 — Sijil Hapuskira Aset Alih Universiti</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; line-height: 1.4; color: #000; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h1 { font-size: 16px; margin: 0; }
        .header h2 { font-size: 13px; margin: 4px 0; text-transform: uppercase; }
        .header p { font-size: 10px; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #e8e8e8; font-size: 11px; text-align: center; font-weight: bold; }
        td { font-size: 11px; }
        .label-cell { width: 28%; font-weight: bold; background-color: #f5f5f5; }
        .value-cell { font-weight: bold; }
        .section-title { font-size: 11px; font-weight: bold; text-align: center; background-color: #d0d0d0; padding: 5px; margin: 12px 0 6px 0; text-transform: uppercase; }
        .signature-row td { height: 60px; vertical-align: bottom; }
        .footer { text-align: center; font-size: 9px; margin-top: 15px; padding-top: 8px; border-top: 1px solid #000; }
        .page-break { page-break-after: always; }
        .declaration-text { font-size: 11px; text-align: justify; margin: 10px 0; padding: 8px; border: 1px solid #000; }
        .amount-box { text-align: right; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-31</h1>
        <h2>SIJIL HAPUSKIRA ASET ALIH UNIVERSITI</h2>
        <p>(Write-off Certificate)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Reference -->
    <table>
        <tr>
            <td class="label-cell">No. Rujukan</td>
            <td style="width:22%;">{{ $lossReport->approval_reference ?? '________________________' }}</td>
            <td class="label-cell">Tarikh</td>
            <td style="width:22%;">{{ $lossReport->applied_date ? $lossReport->applied_date->format('d/m/Y') : ($lossReport->created_at ? $lossReport->created_at->format('d/m/Y') : '________________') }}</td>
        </tr>
    </table>

    <!-- Declaration -->
    <div class="section-title">PERAKUAN HAPUSKIRA / WRITE-OFF CERTIFICATION</div>

    <table>
        <tr>
            <th style="width:5%;">Bil.</th>
            <th style="width:25%;">Jenis Aset</th>
            <th style="width:20%;">No. Pendaftaran</th>
            <th style="width:25%;">Nilai Perolehan (RM)</th>
            <th style="width:25%;">Nilai Semasa (RM)</th>
        </tr>
        <tr>
            <td style="text-align:center;">1.</td>
            <td>{{ $lossReport->asset->name ?? '________________________' }}</td>
            <td>{{ $lossReport->asset->asset_tag ?? '________________' }}</td>
            <td class="amount-box">{{ $lossReport->asset->purchase_price ? number_format($lossReport->asset->purchase_price, 2) : '________________' }}</td>
            <td class="amount-box">{{ $lossReport->current_value ? number_format($lossReport->current_value, 2) : '________________' }}</td>
        </tr>
    </table>

    <!-- Write-off value -->
    <table>
        <tr>
            <td class="label-cell">Nilai Hapuskira (RM)</td>
            <td class="value-cell amount-box">{{ $lossReport->write_off_value ? number_format($lossReport->write_off_value, 2) : '________________________' }}</td>
        </tr>
    </table>

    <div class="declaration-text">
        <p><strong>Dengan ini diakui bahawa aset tersebut di atas telah diluluskan untuk hapuskira berdasarkan keputusan Jawatankuasa Kehilangan Harta / Jawatankuasa Kewangan Universiti.</strong></p>
        <p style="margin-top:8px;">
            Rujukan JK: {{ $lossReport->approval_reference ?? '________________________' }}<br>
            Tarikh Keputusan: {{ $lossReport->applied_date ? $lossReport->applied_date->format('d/m/Y') : ($lossReport->created_at ? $lossReport->created_at->format('d/m/Y') : '________________') }}
        </p>
    </div>

    <!-- Single signature: Pegawai Aset -->
    {{-- Signatures could be stored on loss report model's signatures JSON --}}
    <div class="section-title">PENGESAHAN PEGAWAI ASET</div>

    <table>
        <tr class="signature-row">
            <td style="width:20%;" class="label-cell">Nama</td>
            <td style="width:30%;">_________________________</td>
            <td style="width:20%;" class="label-cell">Jawatan</td>
            <td style="width:30%;">_________________________</td>
        </tr>
        <tr class="signature-row">
            <td class="label-cell">Tarikh</td>
            <td>_________________________</td>
            <td class="label-cell">Tandatangan</td>
            <td>_________________________</td>
        </tr>
        <tr>
            <td class="label-cell">Cop Jabatan</td>
            <td colspan="3">_________________________</td>
        </tr>
    </table>

    <div class="footer">
        <p>KEW.PA-31 — Sijil Hapuskira Aset Alih Universiti | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
