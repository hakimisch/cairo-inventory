<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-32 — Laporan Tindakan Kehilangan Aset Alih Universiti</title>
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
        .section-title { font-size: 11px; font-weight: bold; text-align: center; background-color: #d0d0d0; padding: 5px; margin: 12px 0 6px 0; text-transform: uppercase; }
        .summary-box { border: 1px solid #000; padding: 6px 10px; margin-bottom: 8px; }
        .summary-row { display: flex; justify-content: space-between; padding: 2px 0; }
        .signature-row td { height: 60px; vertical-align: bottom; }
        .footer { text-align: center; font-size: 9px; margin-top: 15px; padding-top: 8px; border-top: 1px solid #000; }
        .page-break { page-break-after: always; }
        .amount-right { text-align: right; }
        .breakdown-item { display: inline-block; margin-right: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-32</h1>
        <h2>LAPORAN TINDAKAN KEHILANGAN ASET ALIH UNIVERSITI</h2>
        <p>(Annual Loss Action Report)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <table>
        <tr>
            <td class="label-cell" style="width:15%;">Tahun Laporan</td>
            <td style="width:35%; font-weight: bold; font-size: 13px;">{{ $year }}</td>
            <td class="label-cell" style="width:15%;">Tarikh Dijana</td>
            <td style="width:35%;">{{ now()->format('d/m/Y') }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SUMMARY SECTION                                                     -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">RUMUSAN LAPORAN / REPORT SUMMARY</div>

    <table>
        <tr>
            <td class="label-cell" style="width:30%;">Jumlah Laporan</td>
            <td style="width:20%; font-weight: bold; font-size: 13px;">{{ $summary['total_reports'] }}</td>
            <td class="label-cell" style="width:30%;">Jumlah Nilai</td>
            <td style="width:20%; font-weight: bold; text-align: right;">RM {{ number_format($summary['total_value'], 2) }}</td>
        </tr>
    </table>

    <!-- Breakdown by action type -->
    <table>
        <tr>
            <th style="width:40%;">Tindakan</th>
            <th style="width:30%;">Bilangan</th>
            <th style="width:30%;">Nilai (RM)</th>
        </tr>
        @php
            $actionLabels = [
                'gantian_setara' => 'Gantian Setara',
                'surcharge'      => 'Surcaj',
                'write_off'      => 'Hapuskira',
                'hapuskira'      => 'Hapuskira',
            ];
        @endphp
        @forelse($summary['by_action'] as $actionType => $data)
            <tr>
                <td>{{ $actionLabels[$actionType] ?? ucfirst(str_replace('_', ' ', $actionType)) }}</td>
                <td style="text-align:center;">{{ $data['count'] }}</td>
                <td class="amount-right">RM {{ number_format($data['value'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="3" style="text-align:center;">Tiada tindakan direkodkan.</td>
            </tr>
        @endforelse
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- DETAIL TABLE                                                         -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BUTIRAN LAPORAN / REPORT DETAILS</div>

    <table>
        <thead>
            <tr>
                <th style="width:4%;">Bil.</th>
                <th style="width:16%;">Ruj. Kelulusan</th>
                <th style="width:14%;">Fakulti/Jabatan</th>
                <th style="width:18%;">Jenis Aset</th>
                <th style="width:12%;">No. Pendaftaran</th>
                <th style="width:12%;">Nilai Perolehan (RM)</th>
                <th style="width:12%;">Nilai Semasa (RM)</th>
                <th style="width:12%;">Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lossReports as $idx => $report)
                <tr>
                    <td style="text-align:center;">{{ $loop->iteration }}.</td>
                    <td>{{ $report->approval_reference ?? '—' }}</td>
                    <td>{{ $report->asset->location ?? '—' }}</td>
                    <td>{{ $report->asset->name ?? '—' }}</td>
                    <td>{{ $report->asset->asset_tag ?? '—' }}</td>
                    <td class="amount-right">{{ $report->asset->purchase_price ? number_format($report->asset->purchase_price, 2) : '—' }}</td>
                    <td class="amount-right">{{ $report->current_value ? number_format($report->current_value, 2) : '—' }}</td>
                    <td>{{ $actionLabels[$report->action_type] ?? ucfirst(str_replace('_', ' ', $report->action_type ?? '—')) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tiada laporan kehilangan untuk tahun {{ $year }}.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SIGNATURE BLOCK                                                     -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">PENGESAHAN</div>

    <table>
        <tr class="signature-row">
            <td style="width:18%;" class="label-cell">Nama</td>
            <td style="width:32%;">_________________________</td>
            <td style="width:18%;" class="label-cell">Jawatan</td>
            <td style="width:32%;">_________________________</td>
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
        <p>KEW.PA-32 — Laporan Tindakan Kehilangan Aset Alih Universiti | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
