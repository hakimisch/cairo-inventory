<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-16 — Perakuan Pelupusan Kenderaan</title>
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
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .info-item { font-size: 11px; }
        .info-item strong { display: inline-block; width: 120px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-16</h1>
        <h2>PERAKUAN PELUPUSAN KENDERAAN</h2>
        <p>(Vehicle Disposal Certificate)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Asset Information -->
    <table>
        <tr>
            <th colspan="4">A. MAKLUMAT ASET / ASSET INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Tag Aset</td>
            <td>{{ $asset->asset_tag }}</td>
            <td class="label-cell">Kategori</td>
            <td>{{ $asset->category }}</td>
        </tr>
        <tr>
            <td class="label-cell">Nama Aset</td>
            <td>{{ $asset->name }}</td>
            <td class="label-cell">Sub Kategori</td>
            <td>{{ $asset->sub_category ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jenama</td>
            <td>{{ $asset->brand ?? '-' }}</td>
            <td class="label-cell">Model</td>
            <td>{{ $asset->model ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">No. Siri</td>
            <td>{{ $asset->serial_number ?? '-' }}</td>
            <td class="label-cell">Lokasi</td>
            <td>{{ $asset->location }}</td>
        </tr>
        <tr>
            <td class="label-cell">Harga Belian (RM)</td>
            <td>{{ number_format($asset->purchase_price, 2) }}</td>
            <td class="label-cell">Status</td>
            <td>{{ $asset->status }}</td>
        </tr>
    </table>

    <!-- Vehicle Registration Details -->
    <table>
        <tr>
            <th colspan="4">B. BUTIRAN PENDAFTARAN KENDERAAN / VEHICLE REGISTRATION DETAILS</th>
        </tr>
        <tr>
            <td class="label-cell">No. Plat</td>
            <td>{{ $assessment->plate_no }}</td>
            <td class="label-cell">No. Casis (VIN)</td>
            <td>{{ $assessment->chassis_no ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">No. Enjin</td>
            <td>{{ $assessment->engine_no ?? '-' }}</td>
            <td class="label-cell">No. Cukai Jalan</td>
            <td>{{ $assessment->road_tax_expiry ? $assessment->road_tax_expiry->format('d/m/Y') : '-' }}</td>
        </tr>
    </table>

    <!-- Vehicle Specifications -->
    <table>
        <tr>
            <th colspan="4">C. SPESIFIKASI KENDERAAN / VEHICLE SPECIFICATIONS</th>
        </tr>
        <tr>
            <td class="label-cell">Jenama</td>
            <td>{{ $assessment->vehicle_brand ?? '-' }}</td>
            <td class="label-cell">Model</td>
            <td>{{ $assessment->vehicle_model ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tahun</td>
            <td>{{ $assessment->vehicle_year ?? '-' }}</td>
            <td class="label-cell">Kapasiti Enjin</td>
            <td>{{ $assessment->engine_capacity ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Jenis Bahan Api</td>
            <td>{{ $assessment->fuel_type ?? '-' }}</td>
            <td class="label-cell">Warna</td>
            <td>{{ $assessment->vehicle_color ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Anggaran Nilai (RM)</td>
            <td colspan="3">{{ $assessment->estimated_value ? number_format($assessment->estimated_value, 2) : '-' }}</td>
        </tr>
    </table>

    <!-- Condition & Assessment -->
    <table>
        <tr>
            <th colspan="2">D. LAPORAN KEADAAN & PENILAIAN / CONDITION & ASSESSMENT REPORT</th>
        </tr>
        <tr>
            <td class="label-cell" style="width:20%;">Laporan Keadaan</td>
            <td>{{ $assessment->condition_report ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Cadangan</td>
            <td>{{ $assessment->recommendation ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Penilaian</td>
            <td>{{ $assessment->assessment_date ? $assessment->assessment_date->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Penilai</td>
            <td>{{ $assessment->assessor_name ?? '-' }} {{ $assessment->assessor_position ? '(' . $assessment->assessor_position . ')' : '' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Catatan</td>
            <td>{{ $assessment->notes ?? '-' }}</td>
        </tr>
    </table>

    <!-- Signatures -->
    {{-- Signatures could be stored on assessment model's signatures JSON --}}
    <table>
        <tr>
            <th colspan="3">E. PENGESAHAN / ENDORSEMENT</th>
        </tr>
        <tr class="signature-row">
            <td style="width:33%;">
                <strong>Penilai:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:33%;">
                <strong>Ketua Jabatan:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:33%;">
                <strong>Jawatankuasa Pelupusan:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>KEW.PA-16 — Perakuan Pelupusan Kenderaan | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
