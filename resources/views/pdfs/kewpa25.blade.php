<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-25 — Borang Sebutharga Pelupusan Aset Alih Universiti</title>
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
        .blank-field { display: inline-block; min-width: 200px; border-bottom: 1px solid #000; margin: 0 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-25</h1>
        <h2>BORANG SEBUTHARGA PELUPUSAN ASET ALIH UNIVERSITI</h2>
        <p>(Quotation Form for Disposal of Movable Assets — University)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Quotation Reference -->
    <table>
        <tr>
            <th colspan="4">MAKLUMAT SEBUTHARGA / QUOTATION REFERENCE</th>
        </tr>
        <tr>
            <td class="label-cell">Rujukan Sebutharga</td>
            <td>{{ $sale->sale_reference }}</td>
            <td class="label-cell">No. Sebutharga</td>
            <td>{{ $sale->sealed_envelope_ref ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Tutup</td>
            <td>{{ $sale->closing_datetime ? $sale->closing_datetime->format('d/m/Y h:i A') : '-' }}</td>
            <td class="label-cell">Tempoh Sah Laku</td>
            <td>{{ $sale->bid_validity_days ?? 60 }} hari</td>
        </tr>
    </table>

    <!-- Bidder Information -->
    <table>
        <tr>
            <th colspan="2">MAKLUMAT PEMBIDA / BIDDER INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell" style="width:25%;">Nama Penuh / Nama Syarikat</td>
            <td style="border-bottom: 1px solid #000; height: 28px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">No. Kad Pengenalan / No. Syarikat</td>
            <td style="border-bottom: 1px solid #000; height: 28px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">Alamat</td>
            <td style="border-bottom: 1px solid #000; height: 28px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">No. Telefon</td>
            <td style="border-bottom: 1px solid #000; height: 28px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">Alamat E-mel</td>
            <td style="border-bottom: 1px solid #000; height: 28px;">&nbsp;</td>
        </tr>
    </table>

    <!-- Item Pricing Table -->
    <table>
        <tr>
            <th colspan="5">HARGA BIDAAN / BID PRICING</th>
        </tr>
        <tr>
            <th style="width:6%;">Bil.</th>
            <th style="width:10%;">Lot</th>
            <th style="width:34%;">Perkara / Description</th>
            <th style="width:25%;">Harga Simpanan (RM)</th>
            <th style="width:25%;">Harga Bidaan (RM)</th>
        </tr>
        @forelse ($sale->disposalSaleItems as $itemIndex => $item)
        <tr>
            <td style="text-align:center;">{{ $itemIndex + 1 }}</td>
            <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
            <td>{{ $item->item_description }}</td>
            <td style="text-align:right;">{{ isset($item->reserve_price) ? number_format($item->reserve_price, 2) : '________________' }}</td>
            <td style="text-align:right;">________________</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align:center;">Tiada item / No items listed</td>
        </tr>
        @endforelse
    </table>

    <!-- Declaration -->
    <table>
        <tr>
            <th>PERAKUAN DAN PERJANJIAN / DECLARATION AND AGREEMENT</th>
        </tr>
        <tr>
            <td style="padding: 10px;">
                <p>Saya dengan ini mengaku bahawa maklumat yang diberikan adalah benar dan sahih. 
                Saya bersetuju bahawa bidaan ini adalah sah dan mengikat untuk tempoh 
                <strong>{{ $sale->bid_validity_days ?? 60 }} hari</strong> dari tarikh tutup sebutharga.</p>

                <p>Saya faham dan bersetuju bahawa:</p>
                <ol style="margin-top: 5px; padding-left: 20px;">
                    <li>Bidaan yang tidak lengkap atau tidak mematuhi syarat akan ditolak.</li>
                    <li>Universiti berhak untuk tidak menerima mana-mana bidaan yang dikemukakan.</li>
                    <li>Keputusan universiti adalah muktamad.</li>
                </ol>

                <br>
                <table>
                    <tr>
                        <td style="border: none; width: 50%;">
                            <strong>Tandatangan:</strong> _________________________<br><br>
                            <strong>Nama:</strong> _________________________<br><br>
                            <strong>Tarikh:</strong> _________________________<br>
                        </td>
                        <td style="border: none; width: 50%;">
                            <strong>Cop Rasmi / Cap Syarikat:</strong><br><br>
                            <div style="border: 2px dashed #000; width: 150px; height: 80px; text-align: center; line-height: 80px; color: #999; font-size: 10px;">
                                Cop Syarikat
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        <p>KEW.PA-25 — Borang Sebutharga Pelupusan Aset Alih | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
