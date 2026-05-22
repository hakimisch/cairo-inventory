<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-30 — Laporan Akhir Kehilangan Aset Alih Universiti</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 11px; line-height: 1.4; color: #000; }
        .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header h1 { font-size: 18px; margin: 0; }
        .header h2 { font-size: 14px; margin: 4px 0; text-transform: uppercase; }
        .header p { font-size: 10px; margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #000; padding: 4px 6px; text-align: left; vertical-align: top; }
        th { background-color: #e8e8e8; font-size: 11px; text-align: center; }
        td { font-size: 11px; }
        .label-cell { width: 28%; font-weight: bold; background-color: #f5f5f5; }
        .section-title { font-size: 11px; font-weight: bold; text-align: center; background-color: #d0d0d0; padding: 5px; margin: 12px 0 6px 0; text-transform: uppercase; }
        .subsection-title { font-size: 11px; font-weight: bold; margin: 6px 0 3px 0; }
        .signature-row td { height: 50px; vertical-align: bottom; }
        .footer { text-align: center; font-size: 9px; margin-top: 15px; padding-top: 8px; border-top: 1px solid #000; }
        .page-break { page-break-after: always; }
        .field-value { min-height: 18px; }
        .checkbox-inline { display: inline-block; margin-right: 12px; }
        .rule-list { margin: 4px 0 4px 20px; padding: 0; }
        .rule-list li { margin-bottom: 3px; }
    </style>
</head>
<body>
    @php $report = $lossReport->finalLossReport; @endphp

    <div class="header">
        <h1>KEW.PA-30</h1>
        <h2>LAPORAN AKHIR KEHILANGAN ASET ALIH UNIVERSITI</h2>
        <p>(Final Loss Investigation Report)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Report Reference -->
    <table>
        <tr>
            <td class="label-cell">No. Rujukan Laporan</td>
            <td style="width:22%;">{{ $lossReport->approval_reference ?? '________________________' }}</td>
            <td class="label-cell">Tarikh Laporan</td>
            <td style="width:22%;">{{ $lossReport->created_at ? $lossReport->created_at->format('d/m/Y') : '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Rujukan PA-28</td>
            <td>{{ $lossReport->police_report_no ?? '________________________' }}</td>
            <td class="label-cell">Status Siasatan</td>
            <td>{{ strtoupper(str_replace('_', ' ', $lossReport->status)) }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 1: BUTIRAN ASET / ASSET DETAILS (a-f)                -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 1: BUTIRAN ASET / ASSET DETAILS</div>

    <table>
        <tr>
            <td class="label-cell" style="width:15%;">(a) No. Tag Aset</td>
            <td style="width:35%;">{{ $report->asset_tag_no ?? $lossReport->asset->asset_tag ?? '________________' }}</td>
            <td class="label-cell" style="width:15%;">(b) Perihal Aset</td>
            <td style="width:35%;">{{ $report->asset_description ?? $lossReport->asset->name ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">(c) Kategori</td>
            <td>{{ $report->asset_category ?? $lossReport->asset->category ?? '________________' }}</td>
            <td class="label-cell">(d) No. Siri</td>
            <td>{{ $report->asset_serial_no ?? $lossReport->asset->serial_number ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">(e) Lokasi Berdaftar</td>
            <td>{{ $report->asset_location_registered ?? $lossReport->asset->location ?? '________________' }}</td>
            <td class="label-cell">(f) Pegawai Terakhir</td>
            <td>{{ $report->last_custodian ?? $lossReport->last_officer ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 2: PERIHAL KEHILANGAN / LOSS DESCRIPTION (a-e)       -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 2: PERIHAL KEHILANGAN / LOSS DESCRIPTION</div>

    <table>
        <tr>
            <td class="label-cell" style="width:15%;">(a) Perihal Kehilangan</td>
            <td colspan="3">{{ $report->incident_description ?? $lossReport->notes ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">(b) Tarikh Kejadian</td>
            <td style="width:35%;">{{ $report->incident_date ? $report->incident_date->format('d/m/Y') : ($lossReport->loss_date ? $lossReport->loss_date->format('d/m/Y') : '_______________') }}</td>
            <td class="label-cell" style="width:15%;">(c) Masa</td>
            <td style="width:35%;">{{ $report->incident_time ?? '_______________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">(d) Tempat Kejadian</td>
            <td colspan="3">{{ $report->incident_location_details ?? $lossReport->incident_location ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">(e) Cara Kehilangan</td>
            <td colspan="3">{{ $report->incident_circumstances ?? $lossReport->loss_method ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 3: DAPATAN POLIS / POLICE FINDINGS                    -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 3: DAPATAN POLIS / POLICE FINDINGS</div>

    <table>
        <tr>
            <td class="label-cell" style="width:20%;">No. Laporan Polis</td>
            <td style="width:30%;">{{ $report->police_report_ref ?? $lossReport->police_report_no ?? '________________' }}</td>
            <td class="label-cell" style="width:20%;">Balai Polis</td>
            <td style="width:30%;">{{ $report->police_station ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Pegawai Penyiasat</td>
            <td colspan="3">{{ $report->police_officer_name ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Dapatan Siasatan Polis</td>
            <td colspan="3">{{ $report->police_investigation_findings ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 4: KETERANGAN SAKSI / WITNESS STATEMENTS (a-b)        -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 4: KETERANGAN SAKSI / WITNESS STATEMENTS</div>

    <table>
        <tr>
            <th style="width:10%;">Saksi</th>
            <th style="width:25%;">Nama</th>
            <th style="width:65%;">Keterangan</th>
        </tr>
        <tr>
            <td style="text-align:center;">(a)</td>
            <td>{{ $report->witness_1_name ?? '____________________________' }}</td>
            <td>{{ $report->witness_1_statement ?? '________________' }}</td>
        </tr>
        <tr>
            <td style="text-align:center;">(b)</td>
            <td>{{ $report->witness_2_name ?? '____________________________' }}</td>
            <td>{{ $report->witness_2_statement ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 5: PEMATUHAN PROSEDUR / PROCEDURAL COMPLIANCE         -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 5: PEMATUHAN PROSEDUR / PROCEDURAL COMPLIANCE</div>

    <table>
        <tr>
            <td class="label-cell" style="width:25%;">Prosedur Dipatuhi?</td>
            <td>
                @if($report && $report->complied_with_procedures === true)
                    &#9745; Ya / Yes
                @elseif($report && $report->complied_with_procedures === false)
                    &#9745; Tidak / No
                @else
                    &#9744; Ya / Yes &nbsp;&nbsp;&nbsp; &#9744; Tidak / No
                @endif
            </td>
        </tr>
        <tr>
            <td class="label-cell">Catatan Pematuhan</td>
            <td>{{ $report->procedural_compliance_notes ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Kekurangan / Gaps</td>
            <td>{{ $report->procedural_gaps ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 6: LANGKAH PENCEGAHAN / PREVENTION STEPS              -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 6: LANGKAH PENCEGAHAN / PREVENTION STEPS</div>

    <table>
        <tr>
            <td class="label-cell" style="width:25%;">Tindakan Diambil</td>
            <td>{{ $report->prevention_actions_taken ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Saranan Pencegahan</td>
            <td>{{ $report->prevention_recommendations ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 7: RUMUSAN SIASATAN / INVESTIGATION SUMMARY           -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 7: RUMUSAN SIASATAN / INVESTIGATION SUMMARY</div>

    <table>
        <tr>
            <td colspan="2">{{ $report->investigation_conclusion ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- SECTION 8: SYOR / RECOMMENDATION                              -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">BAHAGIAN 8: SYOR / RECOMMENDATION</div>

    <table>
        <tr>
            <td class="label-cell" style="width:25%;">Tindakan Disyorkan</td>
            <td>
                @php
                    $action = $report->recommended_action ?? '';
                    $options = [
                        'gantian_setara' => 'Gantian Setara',
                        'surcaj'         => 'Surcaj',
                        'tatatertib'     => 'Tindakan Tatatertib',
                        'hapuskira'      => 'Hapuskira',
                    ];
                @endphp
                @foreach($options as $val => $label)
                    <span class="checkbox-inline">
                        @if($action === $val) &#9745; @else &#9744; @endif {{ $label }}
                    </span>
                @endforeach
            </td>
        </tr>
        <tr>
            <td class="label-cell">Rasional / Justifikasi</td>
            <td>{{ $report->recommendation_rationale ?? '________________' }}</td>
        </tr>
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- PAGE 2: SIGNATURES & ENDORSEMENT                              -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="page-break"></div>

    <div class="header">
        <h1>KEW.PA-30</h1>
        <h2>LAPORAN AKHIR KEHILANGAN ASET ALIH UNIVERSITI</h2>
        <p>(Halaman 2 — Pengesahan & Perakuan)</p>
    </div>

    <div class="section-title">PENGESAHAN JAWATANKUASA PENYIASAT</div>
    {{-- Signatures could be stored on loss report model's signatures JSON --}}

    <table>
        <tr>
            <th style="width:10%;">Bil.</th>
            <th style="width:30%;">Nama</th>
            <th style="width:25%;">Jawatan</th>
            <th style="width:20%;">Tarikh</th>
            <th style="width:15%;">Tandatangan</th>
        </tr>
        <tr class="signature-row">
            <td style="text-align:center;">1.</td>
            <td>_________________________</td>
            <td>Pengerusi</td>
            <td>_________________</td>
            <td>_________________</td>
        </tr>
        <tr class="signature-row">
            <td style="text-align:center;">2.</td>
            <td>_________________________</td>
            <td>Ahli</td>
            <td>_________________</td>
            <td>_________________</td>
        </tr>
        <tr class="signature-row">
            <td style="text-align:center;">3.</td>
            <td>_________________________</td>
            <td>Ahli</td>
            <td>_________________</td>
            <td>_________________</td>
        </tr>
    </table>

    <div class="section-title">PENGESAHAN PEGAWAI PENGAWAL</div>

    <table>
        <tr>
            <td style="width:15%;" class="label-cell">Nama</td>
            <td style="width:35%;">_________________________</td>
            <td style="width:15%;" class="label-cell">Jawatan</td>
            <td style="width:35%;">_________________________</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh</td>
            <td>_________________________</td>
            <td class="label-cell">Tandatangan</td>
            <td>_________________________</td>
        </tr>
        <tr>
            <td colspan="4" style="padding:8px; font-style:italic;">
                <strong>Keputusan:</strong>
                &#9744; Diterima / Accepted &nbsp;&nbsp;&nbsp;
                &#9744; Ditolak / Rejected &nbsp;&nbsp;&nbsp;
                &#9744; Rujuk Semula / Refer Back
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>KEW.PA-30 — Laporan Akhir Kehilangan Aset Alih Universiti | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
