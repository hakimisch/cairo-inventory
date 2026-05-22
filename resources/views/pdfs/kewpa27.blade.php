<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-27 — Kenyataan Jualan Lelongan Aset Alih Universiti</title>
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
        .rule-list { margin: 4px 0 4px 25px; padding: 0; }
        .rule-list li { margin-bottom: 4px; line-height: 1.5; }
        .rule-letter { font-weight: bold; margin-right: 4px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-27</h1>
        <h2>KENYATAAN JUALAN LELONGAN ASET ALIH UNIVERSITI</h2>
        <p>(Auction Sale Notice)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Auction Information -->
    <table>
        <tr>
            <th colspan="4">A. MAKLUMAT LELONGAN / AUCTION INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Rujukan Lelongan</td>
            <td>{{ $sale->sale_reference }}</td>
            <td class="label-cell">Jenis</td>
            <td>Lelongan</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Lelongan</td>
            <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}</td>
            <td class="label-cell">Lokasi</td>
            <td>{{ $sale->sale_location ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Pegawai Lelong</td>
            <td colspan="3">{{ $sale->sale_officer ?? '-' }}</td>
        </tr>
        @if($sale->viewing_date_start || $sale->viewing_date_end)
        <tr>
            <td class="label-cell">Tarikh Lawatan Aset</td>
            <td colspan="3">
                {{ $sale->viewing_date_start ? $sale->viewing_date_start->format('d/m/Y') : '-' }}
                @if($sale->viewing_date_end) hingga {{ $sale->viewing_date_end->format('d/m/Y') }} @endif
                (jam 9.00 pagi — 4.00 petang)
            </td>
        </tr>
        @endif
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
            <td class="label-cell">Kaedah Pelupusan</td>
            <td>{{ $sale->assetDisposal->disposal_method ?? '-' }}</td>
        </tr>
    </table>

    <!-- Auction Items -->
    <table>
        <tr>
            <th colspan="5">C. SENARAI ASET YANG DILELONG / LIST OF AUCTION ASSETS</th>
        </tr>
        <tr>
            <th style="width:8%;">Bil.</th>
            <th style="width:10%;">Lot</th>
            <th style="width:42%;">Perkara / Description</th>
            <th style="width:10%;">Kuantiti</th>
            <th style="width:30%;">Harga Rizab (RM)</th>
        </tr>
        @forelse ($sale->disposalSaleItems as $index => $item)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
            <td>{{ $item->item_description }}@if($item->asset) ({{ $item->asset->name }}) @endif</td>
            <td style="text-align:center;">{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ $item->reserve_price ? number_format($item->reserve_price, 2) : '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;">Tiada item / No items listed</td>
        </tr>
        @endforelse
    </table>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- 11 AUCTION RULES (from KEW.PA-27 PDF ground truth)           -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <div class="section-title">D. SYARAT-SYARAT LELONGAN / AUCTION RULES</div>

    <table>
        <tr>
            <td style="padding: 8px 12px;">
                <p style="font-weight:bold; margin-bottom:6px;">Syarat-Syarat Lelongan / Terms & Conditions of Auction</p>
                <ol class="rule-list" style="list-style-type: lower-alpha;">
                    <li><span class="rule-letter">(a)</span> Aset akan dijual tertakluk kepada harga rezab. / Assets are sold subject to reserve price.</li>
                    <li><span class="rule-letter">(b)</span> Pembida perlu mendaftar dengan pegawai pelelong dengan memberikan nama penuh, nombor K/P dan alamat. / Bidders must register with the auctioneer providing full name, IC number, and address.</li>
                    <li><span class="rule-letter">(c)</span> Penawar harga tertinggi adalah pembida berjaya; jika perselisihan, akan dilelong semula. / The highest bidder shall be the successful bidder; in the event of a dispute, the item shall be re-auctioned.</li>
                    <li><span class="rule-letter">(d)</span> Universiti berhak mengubah susunan jualan senarai KEW.PA-27(A). / The University reserves the right to change the order of sale of the list in KEW.PA-27(A).</li>
                    <li><span class="rule-letter">(e)</span> Universiti berhak menarik balik mana-mana aset daripada senarai. / The University reserves the right to withdraw any asset from the list.</li>
                    <li><span class="rule-letter">(f)</span> Semua aset dilelong secara &quot;as-is-where-is basis&quot;. / All assets are sold on an &quot;as-is-where-is basis&quot;.</li>
                    <li><span class="rule-letter">(g)</span> Universiti tidak bertanggungjawab ke atas aset yang telah dijual. / The University shall not be held responsible for assets that have been sold.</li>
                    <li><span class="rule-letter">(h)</span> Perbelanjaan mengangkut aset ditanggung oleh pembida. / Transport costs for the assets shall be borne by the bidder.</li>
                    <li><span class="rule-letter">(i)</span> Pembayaran penuh hendaklah dibuat dalam tempoh 7 hari dari tarikh lelongan. / Full payment must be made within 7 days from the date of auction.</li>
                    <li><span class="rule-letter">(j)</span> Pembida perlu mengambil aset dalam tempoh 14 hari dari tarikh lelongan; jika tidak, Universiti berhak melupuskannya. / The bidder must collect the assets within 14 days from the auction date; failing which, the University reserves the right to dispose of them.</li>
                    <li><span class="rule-letter">(k)</span> Segala bayaran dijelaskan sepenuhnya sebelum aset boleh dikeluarkan dari premis jabatan. / All payments must be settled in full before assets may be removed from the department's premises.</li>
                </ol>
            </td>
        </tr>
    </table>

    <!-- Terms & Conditions -->
    @if($sale->terms_conditions)
    <table>
        <tr>
            <th>E. SYARAT & KETENTUAN TAMBAHAN / ADDITIONAL TERMS & CONDITIONS</th>
        </tr>
        <tr>
            <td>{{ $sale->terms_conditions }}</td>
        </tr>
    </table>
    @endif

    <!-- Signatures -->
    <table>
        <tr>
            <th colspan="3">F. PENGESAHAN / ENDORSEMENT</th>
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
        <p>KEW.PA-27 — Kenyataan Jualan Lelongan Aset Alih Universiti | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
