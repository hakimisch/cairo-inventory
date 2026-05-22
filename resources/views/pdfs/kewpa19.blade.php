<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-19 — Sijil Pelupusan</title>
    <style>
        @page { margin: 20mm 20mm; }
        @font-face { font-family: 'Serif'; src: local('Times New Roman'); }
        body {
            font-family: 'Serif', 'Times New Roman', serif;
            font-size: 10px;
            line-height: 1.5;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { font-size: 18px; font-weight: bold; margin: 0 0 4px 0; }
        .header h2 { font-size: 14px; font-weight: bold; margin: 0 0 2px 0; }
        .header .subtitle { font-size: 11px; font-style: italic; margin: 0 0 2px 0; }
        .header .university { font-size: 12px; font-weight: bold; margin: 0; }
        .ref-row { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 10px; }
        .ref-row strong { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 9px; }
        table th, table td { border: 1px solid #000; padding: 4px 5px; text-align: center; vertical-align: middle; }
        table th { background: #f0f0f0; font-weight: bold; }
        table td.left { text-align: left; }
        .signature-row { display: flex; justify-content: space-between; margin-top: 28px; gap: 30px; }
        .signature-box { flex: 1; text-align: center; }
        .signature-box .sig-label { font-size: 10px; font-weight: bold; margin-bottom: 4px; }
        .signature-box .sig-name { font-size: 11px; font-weight: bold; margin-top: 40px; border-top: 1px solid #000; padding-top: 4px; }
        .signature-box .sig-role { font-size: 10px; margin-top: 2px; }
        .signature-box .sig-line { font-size: 10px; margin-top: 2px; }
        .footer { margin-top: 20px; font-size: 9px; text-align: center; color: #888; border-top: 1px solid #ccc; padding-top: 6px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-19</h1>
        <h2>SIJIL PELUPUSAN HARTA TETAP</h2>
        <p class="subtitle">(Fixed Asset Disposal Certificate)</p>
        <p class="university">UNIVERSITI TEKNOLOGI MALAYSIA</p>
    </div>

    <div class="ref-row">
        <div>
            <strong>No. Rujukan:</strong>
            {{ $disposal->approval_reference ?? '________________________' }}
        </div>
        <div>
            <strong>Tarikh:</strong>
            {{ $disposal->disposal_date ? \Carbon\Carbon::parse($disposal->disposal_date)->format('d/m/Y') : '________________________' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:4%">Bil</th>
                <th style="width:18%">Jenis Barang</th>
                <th style="width:12%">No. Siri</th>
                <th style="width:6%">Kuantiti</th>
                <th style="width:8%">Tahun Dibeli</th>
                <th style="width:10%">Harga Seunit (RM)</th>
                <th style="width:10%">Jumlah (RM)</th>
                <th style="width:14%">Ulasan Pemeriksa</th>
                <th style="width:10%">Cara Pelupusan</th>
                <th style="width:8%">Tarikh Dilupuskan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td class="left">{{ $disposal->asset->name ?? '—' }}</td>
                <td>{{ $disposal->asset->serial_number ?? '—' }}</td>
                <td>1</td>
                <td>{{ $disposal->asset->purchase_date ? \Carbon\Carbon::parse($disposal->asset->purchase_date)->format('Y') : '—' }}</td>
                <td class="right">{{ $disposal->asset->purchase_price ? number_format($disposal->asset->purchase_price, 2) : '—' }}</td>
                <td class="right">{{ $disposal->asset->purchase_price ? number_format($disposal->asset->purchase_price, 2) : '—' }}</td>
                <td></td>
                <td>{{ $disposal->disposal_method ?? '—' }}</td>
                <td>{{ $disposal->disposal_date ? \Carbon\Carbon::parse($disposal->disposal_date)->format('d/m/Y') : '—' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- 3 AJK Signature Blocks -->
    @php
        $committee = $disposal->committeeAppointments ?? collect();
        $pengerusi = $committee->firstWhere('role', 'Pengerusi');
        $ahli1     = $committee->firstWhere('role', 'Ahli');
        $ahli2     = $committee->filter(fn($c) => $c->role === 'Ahli')->slice(1)->first();
    @endphp

    <div style="margin-top: 8px;">
        <p style="font-size: 10px; font-weight: bold; margin-bottom: 4px;">JAWATANKUASA PELUPUSAN:</p>
    </div>

    <div class="signature-row">
        <div class="signature-box">
            @php $sigs = json_decode($disposal->signatures ?? '{}', true); @endphp
            <div class="sig-label">Pengerusi</div>
            <div class="sig-name">{{ $pengerusi?->user?->name ?? '________________________' }}</div>
            <div class="sig-line">Tandatangan:
                @if(!empty($sigs['pengerusi']))
                    <br><img src="{{ $sigs['pengerusi'] }}" style="height:40px; vertical-align:middle; display:block; margin:4px auto;">
                @else
                    ........................................
                @endif
            </div>
            <div class="sig-line">Tarikh: ........................................</div>
        </div>
        <div class="signature-box">
            @php $sigs = json_decode($disposal->signatures ?? '{}', true); @endphp
            <div class="sig-label">Ahli 1</div>
            <div class="sig-name">{{ $ahli1?->user?->name ?? '________________________' }}</div>
            <div class="sig-line">Tandatangan:
                @if(!empty($sigs['ahli1']))
                    <br><img src="{{ $sigs['ahli1'] }}" style="height:40px; vertical-align:middle; display:block; margin:4px auto;">
                @else
                    ........................................
                @endif
            </div>
            <div class="sig-line">Tarikh: ........................................</div>
        </div>
        <div class="signature-box">
            @php $sigs = json_decode($disposal->signatures ?? '{}', true); @endphp
            <div class="sig-label">Ahli 2</div>
            <div class="sig-name">{{ $ahli2?->user?->name ?? '________________________' }}</div>
            <div class="sig-line">Tandatangan:
                @if(!empty($sigs['ahli2']))
                    <br><img src="{{ $sigs['ahli2'] }}" style="height:40px; vertical-align:middle; display:block; margin:4px auto;">
                @else
                    ........................................
                @endif
            </div>
            <div class="sig-line">Tarikh: ........................................</div>
        </div>
    </div>

    <div class="footer">
        KEW.PA-19 — Sijil Pelupusan Harta Tetap
    </div>

</body>
</html>
