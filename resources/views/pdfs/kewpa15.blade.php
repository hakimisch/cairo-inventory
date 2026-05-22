<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page { margin: 20mm 25mm; }
        @font-face { font-family: 'Serif'; src: local('Times New Roman'); }
        body {
            font-family: 'Serif', 'Times New Roman', serif;
            font-size: 11px;
            line-height: 1.6;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 12px; }
        .header h1 { font-size: 13px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
        .header .coa { font-size: 28px; margin-bottom: 4px; }
        .header .university { font-size: 14px; font-weight: bold; margin: 0 0 2px 0; text-transform: uppercase; }
        .header .sub { font-size: 10px; margin: 0; }
        .ref-line { font-size: 10px; margin-bottom: 20px; }
        .ref-line strong { font-weight: bold; }
        .salutation { font-size: 11px; margin-bottom: 12px; }
        .title-line { font-size: 12px; font-weight: bold; text-align: center; margin: 16px 0; text-transform: uppercase; text-decoration: underline; }
        .body-text { font-size: 11px; margin-bottom: 12px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; font-size: 10px; }
        table th, table td { border: 1px solid #000; padding: 6px 8px; text-align: center; vertical-align: middle; }
        table th { background: #f0f0f0; font-weight: bold; }
        table td.left { text-align: left; }
        .signature-block { margin-top: 32px; }
        .signature-row { display: flex; justify-content: flex-end; margin-top: 24px; }
        .signature-box { text-align: center; width: 260px; }
        .signature-box .sig-line { margin-top: 40px; border-top: 1px solid #000; padding-top: 4px; font-weight: bold; }
        .signature-box .sig-role { font-size: 10px; margin-top: 2px; }
        .signature-box .sig-date { font-size: 10px; margin-top: 2px; }
        .footer { margin-top: 30px; font-size: 9px; text-align: center; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
        .cc-section { margin-top: 20px; font-size: 10px; }
        .cc-section strong { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <div class="coa">◈</div>
        <h1>UNIVERSITI TEKNOLOGI MALAYSIA</h1>
        <div class="sub">Pejabat Pendaftar, 81310 UTM Johor Bahru, Johor Darul Ta'zim</div>
        <div class="sub">Tel: 07-555 7000 &nbsp; Faks: 07-555 7001 &nbsp; Laman Web: www.utm.my</div>
    </div>

    <div class="ref-line">
        <strong>Ruj. Kami :</strong> UTM.CAIRO/PA-15/{{ $disposal->id }}/{{ date('Y') }}<br>
        <strong>Tarikh &nbsp;&nbsp;&nbsp;:</strong> {{ \Carbon\Carbon::now()->format('d F Y') }}
    </div>

    <div class="title-line">SURAT LANTIKAN JAWATANKUASA PEMERIKSA PELUPUSAN ASET ALIH UNIVERSITI</div>
    <div class="title-line" style="font-size: 10px; text-decoration: none;">(KEW.PA-15)</div>

    <div class="salutation">
        Kepada:
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:8%">Bil</th>
                <th style="width:28%">Nama</th>
                <th style="width:22%">Jawatan dalam JK</th>
                <th style="width:22%">Jawatan Rasmi</th>
                <th style="width:20%">Tempoh Lantikan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $committee = $disposal->committeeAppointments ?? collect();
            @endphp
            @forelse($committee as $index => $member)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="left">{{ $member->user->name ?? '____________________' }}</td>
                    <td>
                        @switch($member->role)
                            @case('chairman') Pengerusi @break
                            @case('secretary') Setiausaha @break
                            @case('member') Ahli @break
                            @default {{ $member->role }}
                        @endswitch
                    </td>
                    <td>{{ $member->user?->email ?? '____________________' }}</td>
                    <td>{{ $member->valid_from ? \Carbon\Carbon::parse($member->valid_from)->format('d/m/Y') : '—' }}
                        hingga {{ $member->valid_until ? \Carbon\Carbon::parse($member->valid_until)->format('d/m/Y') : '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="font-style: italic; color: #888;">Tiada ahli jawatankuasa dilantik lagi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="body-text">
        Dengan segala hormatnya, perkara di atas adalah dirujuk.
    </div>

    <div class="body-text">
        Sukacita dimaklumkan bahawa tuan/puan telah dilantik sebagai
        <strong>Jawatankuasa Pemeriksa Pelupusan Aset Alih Universiti</strong>
        bagi tujuan memeriksa dan mengesahkan pelupusan aset yang berikut:
    </div>

    <table>
        <tr>
            <th style="width:25%">No. Siri Pendaftaran</th>
            <td class="left">{{ $disposal->asset->asset_tag ?? '—' }}</td>
        </tr>
        <tr>
            <th>Nama Aset</th>
            <td class="left">{{ $disposal->asset->name ?? '—' }}</td>
        </tr>
        <tr>
            <th>Kaedah Pelupusan</th>
            <td class="left">{{ $disposal->disposal_method ?? '—' }}</td>
        </tr>
        <tr>
            <th>Rujukan Kelulusan</th>
            <td class="left">{{ $disposal->approval_reference ?? '—' }}</td>
        </tr>
    </table>

    <div class="body-text">
        Tuan/puan adalah diminta menjalankan tugas dengan penuh tanggungjawab dan melaporkan hasil pemeriksaan kepada pihak pengurusan Universiti dalam tempoh yang ditetapkan.
    </div>

    <div class="body-text">
        Sekian, terima kasih.
    </div>

    <div class="signature-block">
        <div class="signature-row">
            <div class="signature-box">
                <div class="sig-line">{{ $disposal->committeeAppointments->firstWhere('role', 'chairman')?->user?->name ?? '____________________' }}</div>
                <div class="sig-role">Pengerusi</div>
                <div class="sig-role">Jawatankuasa Pemeriksa Pelupusan</div>
                <div class="sig-date">Tarikh: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <div class="cc-section">
        <strong>s.k.:</strong><br>
        1. Fail Individu<br>
        2. Pejabat Bendahari<br>
        3. Pejabat Pendaftar
    </div>

    <div class="footer">
        KEW.PA-15 — Lantikan Jawatankuasa Pemeriksa Pelupusan Aset Alih Universiti
    </div>

</body>
</html>
