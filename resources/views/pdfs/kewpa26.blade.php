<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-26 — Jadual Buka Sebutharga Pelupusan Aset Alih Universiti</title>
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
        <h1>KEW.PA-26</h1>
        <h2>JADUAL BUKA SEBUTHARGA PELUPUSAN ASET ALIH UNIVERSITI</h2>
        <p>(Quotation Opening Schedule for Disposal of Movable Assets — University)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Opening Information -->
    <table>
        <tr>
            <th colspan="4">A. MAKLUMAT PEMBUKAAN / OPENING INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Rujukan Sebutharga</td>
            <td>{{ $sale->sale_reference }}</td>
            <td class="label-cell">No. Sebutharga</td>
            <td>{{ $sale->sealed_envelope_ref ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Buka</td>
            <td>{{ now()->format('d/m/Y') }}</td>
            <td class="label-cell">Masa Buka</td>
            <td>{{ $sale->closing_datetime ? $sale->closing_datetime->format('h:i A') : '12:00 PM' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tempat</td>
            <td colspan="3">{{ $sale->sale_location ?? 'Pejabat Pendaftar, UTM' }}</td>
        </tr>
    </table>

    <!-- Bids by Item -->
    <table>
        <tr>
            <th colspan="6">B. BUKAAN BIDAAN / BID OPENING RECORD</th>
        </tr>
        <tr>
            <th style="width:5%;">Bil.</th>
            <th style="width:8%;">Lot</th>
            <th style="width:22%;">Perkara</th>
            <th style="width:22%;">Kod Pembida / Bidder Code</th>
            <th style="width:18%;">Harga Bidaan (RM)</th>
            <th style="width:25%;">Catatan</th>
        </tr>
        @forelse ($sale->disposalSaleItems as $itemIndex => $item)
            @forelse ($item->saleBids as $bidIndex => $bid)
            <tr>
                <td style="text-align:center;">{{ $itemIndex + 1 }}.{{ $bidIndex + 1 }}</td>
                <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
                <td>{{ $item->item_description }}</td>
                <td>{{ $bid->bidder_name ?? 'Bida-' . ($bidIndex + 1) }}</td>
                <td style="text-align:right;">{{ isset($bid->bid_amount) ? number_format($bid->bid_amount, 2) : '-' }}</td>
                <td>{{ $bid->status ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td style="text-align:center;">{{ $itemIndex + 1 }}</td>
                <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
                <td>{{ $item->item_description }}</td>
                <td colspan="3" style="text-align:center;">Tiada bidaan / No bids received</td>
            </tr>
            @endforelse
        @empty
        <tr>
            <td colspan="6" style="text-align:center;">Tiada item / No items listed</td>
        </tr>
        @endforelse
    </table>

    <!-- Disclaimer -->
    <table>
        <tr>
            <th>C. PERAKUAN PEMBUKAAN / OPENING CERTIFICATION</th>
        </tr>
        <tr>
            <td style="padding: 10px;">
                <p><strong>Nota Penting / Important Note:</strong></p>
                <p>Universiti tidak terikat untuk menerima bidaan yang terendah atau mana-mana bidaan yang dikemukakan. 
                Keputusan universiti adalah muktamad.</p>
                <p><em>The university is not bound to accept the lowest or any bid submitted. 
                The university's decision is final.</em></p>
                <p>Bidaan ini sah untuk tempoh <strong>{{ $sale->bid_validity_days ?? 60 }} hari</strong> dari tarikh tutup sebutharga.</p>
            </td>
        </tr>
    </table>

    <!-- Signatures -->
    <table>
        <tr>
            <th colspan="4">D. JAWATANKUASA PEMBUKAAN / OPENING COMMITTEE</th>
        </tr>
        <tr class="signature-row">
            <td style="width:25%;">
                <strong>Pengerusi:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:25%;">
                <strong>Ahli 1:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:25%;">
                <strong>Ahli 2:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
            <td style="width:25%;">
                <strong>Setiausaha:</strong><br>
                Nama: _________________________<br>
                Jawatan: ______________________<br>
                Tarikh: _______________________<br>
                Tandatangan: __________________
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>KEW.PA-26 — Jadual Buka Sebutharga Pelupusan Aset Alih | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
