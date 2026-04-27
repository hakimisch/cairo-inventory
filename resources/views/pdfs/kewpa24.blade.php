<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-24 — Keputusan Tawaran/Sebutharga/Lelongan</title>
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
        <h1>KEW.PA-24</h1>
        <h2>KEPUTUSAN {{ strtoupper($sale->sale_type) }}</h2>
        <p>({{ $sale->sale_type }} Decision)</p>
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
            <td class="label-cell">Tarikh Keputusan</td>
            <td>{{ $sale->decision_date ? $sale->decision_date->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Lokasi</td>
            <td>{{ $sale->sale_location ?? '-' }}</td>
            <td class="label-cell">Pegawai</td>
            <td>{{ $sale->sale_officer ?? '-' }}</td>
        </tr>
    </table>

    <!-- Winning Bids by Item -->
    <table>
        <tr>
            <th colspan="6">B. KEPUTUSAN / DECISION</th>
        </tr>
        <tr>
            <th style="width:6%;">Bil.</th>
            <th style="width:8%;">Lot</th>
            <th style="width:26%;">Perkara</th>
            <th style="width:25%;">Pembida Menang</th>
            <th style="width:20%;">Jumlah Bidaan (RM)</th>
            <th style="width:15%;">Keputusan</th>
        </tr>
        @forelse ($sale->disposalSaleItems as $itemIndex => $item)
            @php
                $winningBid = $item->saleBids->firstWhere('is_winner', true);
            @endphp
            <tr>
                <td style="text-align:center;">{{ $itemIndex + 1 }}</td>
                <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
                <td>{{ $item->item_description }}</td>
                <td>{{ $winningBid->bidder_name ?? 'Tiada / None' }}</td>
                <td style="text-align:right;">{{ $winningBid ? number_format($winningBid->bid_amount, 2) : '-' }}</td>
                <td style="text-align:center;">{{ $winningBid ? 'Diterima' : 'Tiada' }}</td>
            </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center;">Tiada item / No items listed</td>
        </tr>
        @endforelse
    </table>

    <!-- Decision Notes -->
    @if($sale->decision_notes)
    <table>
        <tr>
            <th>C. CATATAN KEPUTUSAN / DECISION NOTES</th>
        </tr>
        <tr>
            <td>{{ $sale->decision_notes }}</td>
        </tr>
    </table>
    @endif

    <div class="footer">
        <p>KEW.PA-24 — Keputusan {{ $sale->sale_type }} | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
