<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-24 — Kenyataan Tawaran Sebutharga Pelupusan Aset Alih Universiti</title>
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
        <h2>KENYATAAN TAWARAN SEBUTHARGA PELUPUSAN ASET ALIH UNIVERSITI</h2>
        <p>(Quotation Notice for Disposal of Movable Assets — University)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Quotation Information -->
    <table>
        <tr>
            <th colspan="4">A. MAKLUMAT SEBUTHARGA / QUOTATION INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Rujukan Sebutharga</td>
            <td>{{ $sale->sale_reference }}</td>
            <td class="label-cell">No. Sebutharga</td>
            <td>{{ $sale->sealed_envelope_ref ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Mula Lawatan</td>
            <td>{{ $sale->viewing_date_start ? $sale->viewing_date_start->format('d/m/Y') : '-' }}</td>
            <td class="label-cell">Tarikh Tamat Lawatan</td>
            <td>{{ $sale->viewing_date_end ? $sale->viewing_date_end->format('d/m/Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh & Masa Tutup</td>
            <td>{{ $sale->closing_datetime ? $sale->closing_datetime->format('d/m/Y h:i A') : '-' }}</td>
            <td class="label-cell">Tempoh Sah Laku Bidaan</td>
            <td>{{ $sale->bid_validity_days ?? 60 }} hari</td>
        </tr>
        <tr>
            <td class="label-cell">Alamat Peti Tender</td>
            <td colspan="3">{{ $sale->tender_box_address ?? 'Pejabat Pendaftar, UTM' }}</td>
        </tr>
    </table>

    <p style="font-size: 11px; font-style: italic; margin-bottom: 15px;">
        <strong>Nota Penting:</strong> Sampul bertutup bertanda '{{ $sale->sealed_envelope_ref ?? 'No. Sebutharga' }}' hendaklah dikemukakan 
        sebelum jam 12.00 tengahari pada tarikh tutup. Serahan lewat tidak akan dipertimbangkan.
    </p>

    <!-- Item List -->
    <table>
        <tr>
            <th colspan="4">B. SENARAI ITEM / LIST OF ITEMS</th>
        </tr>
        <tr>
            <th style="width:8%;">Bil.</th>
            <th style="width:10%;">Lot</th>
            <th style="width:42%;">Perkara / Description</th>
            <th style="width:40%;">Catatan / Remarks</th>
        </tr>
        @forelse ($sale->disposalSaleItems as $itemIndex => $item)
        <tr>
            <td style="text-align:center;">{{ $itemIndex + 1 }}</td>
            <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
            <td>{{ $item->item_description }}</td>
            <td>{{ $item->remarks ?? '-' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" style="text-align:center;">Tiada item / No items listed</td>
        </tr>
        @endforelse
    </table>

    <!-- Decision Notes -->
    @if($sale->decision_notes)
    <table>
        <tr>
            <th>C. CATATAN / NOTES</th>
        </tr>
        <tr>
            <td>{{ $sale->decision_notes }}</td>
        </tr>
    </table>
    @endif

{{-- Signatures stored in $sale->signatures JSON --}}
    <div class="footer">
        <p>KEW.PA-24 — Kenyataan Tawaran Sebutharga Pelupusan Aset Alih | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
