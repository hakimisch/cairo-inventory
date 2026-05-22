<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-18 — Sijil Pemusnahan</title>
    <style>
        @page { margin: 25mm 20mm; }
        @font-face { font-family: 'Serif'; src: local('Times New Roman'); }
        body {
            font-family: 'Serif', 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.6;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 20px; font-weight: bold; margin: 0 0 4px 0; }
        .header h2 { font-size: 16px; font-weight: bold; margin: 0 0 2px 0; }
        .header .subtitle { font-size: 12px; font-style: italic; margin: 0 0 2px 0; }
        .header .university { font-size: 13px; font-weight: bold; margin: 0; }
        .ref-row { display: flex; justify-content: space-between; margin-bottom: 16px; font-size: 12px; }
        .ref-row strong { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 12px; }
        table th, table td { border: 1px solid #000; padding: 6px 8px; text-align: left; vertical-align: top; }
        table th { background: #f0f0f0; font-weight: bold; text-align: center; }
        table td.label { font-weight: bold; width: 30%; background: #f9f9f9; }
        .checkbox-list { list-style: none; padding: 0; margin: 0; }
        .checkbox-list li { margin-bottom: 4px; }
        .checkbox-list .box { display: inline-block; width: 14px; height: 14px; border: 1px solid #000; margin-right: 6px; vertical-align: middle; text-align: center; font-size: 11px; line-height: 14px; }
        .checkbox-list .checked { background: #000; color: #fff; }
        .note { font-size: 11px; font-style: italic; margin-bottom: 16px; color: #555; }
        .signature-row { display: flex; justify-content: space-between; margin-top: 32px; gap: 40px; }
        .signature-box { flex: 1; }
        .signature-box .sig-line { margin-top: 48px; border-top: 1px solid #000; padding-top: 4px; font-size: 11px; text-align: center; font-weight: bold; }
        .signature-box .sig-label { font-size: 10px; text-align: center; margin-top: 2px; }
        .blank-field { display: inline-block; min-width: 200px; border-bottom: 1px solid #000; margin-left: 4px; }
        .footer { margin-top: 24px; font-size: 10px; text-align: center; color: #888; border-top: 1px solid #ccc; padding-top: 8px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-18</h1>
        <h2>SIJIL PENGESAHAN PEMUSNAHAN ASET ALIH UNIVERSITI</h2>
        <p class="subtitle">(Destruction Certificate)</p>
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
        <tr>
            <td class="label">Jenis Aset</td>
            <td>{{ $disposal->asset->name ?? '—' }} ({{ $disposal->asset->asset_tag ?? '—' }})</td>
        </tr>
        <tr>
            <td class="label">Kuantiti</td>
            <td>1</td>
        </tr>
        <tr>
            <td class="label">Cara Pemusnahan</td>
            <td>
                <ul class="checkbox-list">
                    <li>
                        <span class="box {{ $disposal->disposal_method === 'Tanam' ? 'checked' : '' }}">
                            {{ $disposal->disposal_method === 'Tanam' ? '/' : '' }}
                        </span>
                        Tanam (Bury)
                    </li>
                    <li>
                        <span class="box {{ $disposal->disposal_method === 'Bakar' ? 'checked' : '' }}">
                            {{ $disposal->disposal_method === 'Bakar' ? '/' : '' }}
                        </span>
                        Bakar (Burn)
                    </li>
                    <li>
                        <span class="box {{ $disposal->disposal_method === 'Tenggelam' ? 'checked' : '' }}">
                            {{ $disposal->disposal_method === 'Tenggelam' ? '/' : '' }}
                        </span>
                        Tenggelam (Drown)
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td class="label">Tarikh Pemusnahan</td>
            <td>{{ $disposal->disposal_date ? \Carbon\Carbon::parse($disposal->disposal_date)->format('d/m/Y') : '________________________' }}</td>
        </tr>
        <tr>
            <td class="label">Tempat Pemusnahan</td>
            <td><span class="blank-field">&nbsp;</span></td>
        </tr>
    </table>

    <p class="note">* Pilih mana yang berkenaan</p>

    <div class="signature-row">
        <div class="signature-box">
            <div class="sig-line">Tandatangan</div>
            <div class="sig-label">
                Nama: ........................................<br>
                Jawatan: ........................................<br>
                Tarikh: ........................................
            </div>
        </div>
        <div class="signature-box">
            <div class="sig-line">Tandatangan</div>
            <div class="sig-label">
                Nama: ........................................<br>
                Jawatan: ........................................<br>
                Tarikh: ........................................
            </div>
        </div>
    </div>

    <div class="footer">
        KEW.PA-18 — Sijil Pengesahan Pemusnahan Aset Alih Universiti
    </div>

</body>
</html>
