<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>KEW.PA-22 — Borang Tender Pelupusan Aset Alih Universiti</title>
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
        .declaration { font-size: 11px; margin-bottom: 15px; padding: 10px; border: 1px solid #000; background-color: #fafafa; text-align: justify; }
    </style>
</head>
<body>

    <div class="header">
        <h1>KEW.PA-22</h1>
        <h2>BORANG TENDER PELUPUSAN ASET ALIH UNIVERSITI</h2>
        <p>(University Movable Asset Disposal Tender Form)</p>
        <p>Universiti Teknologi Malaysia</p>
    </div>

    <!-- Tender Form Information -->
    <table>
        <tr>
            <th colspan="4">A. MAKLUMAT BORANG TENDER / TENDER FORM INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Rujukan Sebutharga</td>
            <td>{{ $sale->sale_reference }}</td>
            <td class="label-cell">Jenis</td>
            <td>{{ $sale->sale_type }}</td>
        </tr>
        <tr>
            <td class="label-cell">Tarikh Sebutharga</td>
            <td>{{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '-' }}</td>
            <td class="label-cell">Lokasi</td>
            <td>{{ $sale->sale_location ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Pegawai Bertanggungjawab</td>
            <td>{{ $sale->sale_officer ?? '-' }}</td>
            <td class="label-cell">Status</td>
            <td>{{ $sale->status ?? '-' }}</td>
        </tr>
    </table>

    <!-- Bidder Information -->
    <table>
        <tr>
            <th colspan="4">B. MAKLUMAT BIDDER / BIDDER INFORMATION</th>
        </tr>
        <tr>
            <td class="label-cell">Nama Penuh / Full Name</td>
            <td colspan="3" style="height: 25px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">No. IC / No. Syarikat</td>
            <td colspan="3" style="height: 25px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">Alamat / Address</td>
            <td colspan="3" style="height: 50px;">&nbsp;</td>
        </tr>
        <tr>
            <td class="label-cell">No. Telefon / Phone</td>
            <td style="height: 25px;">&nbsp;</td>
            <td class="label-cell">Emel / Email</td>
            <td style="height: 25px;">&nbsp;</td>
        </tr>
    </table>

    <!-- Asset Disposal Information -->
    <table>
        <tr>
            <th colspan="4">C. MAKLUMAT PELUPUSAN / DISPOSAL INFORMATION</th>
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
            <td class="label-cell">No. Siri</td>
            <td>{{ $sale->assetDisposal->asset->serial_number ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label-cell">Kaedah Pelupusan</td>
            <td>{{ $sale->assetDisposal->disposal_method ?? '-' }}</td>
            <td class="label-cell">Sebab Pelupusan</td>
            <td>{{ $sale->assetDisposal->disposal_reason ?? '-' }}</td>
        </tr>
    </table>

    <!-- Item Pricing Table -->
    <table>
        <tr>
            <th colspan="6">D. HARGA ITEM / ITEM PRICING</th>
        </tr>
        <tr>
            <th style="width:6%;">Bil.</th>
            <th style="width:8%;">Lot</th>
            <th style="width:32%;">Perkara / Description</th>
            <th style="width:8%;">Kuantiti</th>
            <th style="width:23%;">Harga Rizab (RM)</th>
            <th style="width:23%;">Tawaran Bidaan (RM)</th>
        </tr>
        @forelse ($sale->disposalSaleItems as $index => $item)
        <tr>
            <td style="text-align:center;">{{ $index + 1 }}</td>
            <td style="text-align:center;">{{ $item->lot_number ?? '-' }}</td>
            <td>{{ $item->item_description }}@if($item->asset) ({{ $item->asset->name }}) @endif</td>
            <td style="text-align:center;">{{ $item->quantity }}</td>
            <td style="text-align:right;">{{ $item->estimated_value ? number_format($item->estimated_value, 2) : '-' }}</td>
            <td style="text-align:right;">&nbsp;</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center;">Tiada item / No items listed</td>
        </tr>
        @endforelse
    </table>

    <!-- Bid Validity -->
    <table>
        <tr>
            <th>E. TEMPOH SAH LAKU BIDAAN / BID VALIDITY PERIOD</th>
        </tr>
        <tr>
            <td>Tempoh sah laku bidaan ini ialah {{ $sale->bid_validity_days ?? '60' }} hari dari tarikh tutup tender.</td>
        </tr>
    </table>

    <!-- Declaration -->
    <table>
        <tr>
            <th>F. PENGAKUAN / DECLARATION</th>
        </tr>
        <tr>
            <td>
                <div class="declaration">
                    Saya dengan ini mengaku memahami dan bersetuju dengan semua syarat-syarat yang ditetapkan.
                    <br><br>
                    <em>I hereby acknowledge that I understand and agree to all the terms and conditions set forth.</em>
                </div>
            </td>
        </tr>
    </table>

    <!-- Signature -->
    {{-- Signatures stored in $sale->signatures JSON --}}
    <table>
        <tr>
            <th colspan="2">TANDATANGAN / SIGNATURE</th>
        </tr>
        <tr class="signature-row">
            <td style="width:50%;">
                <strong>Nama / Name:</strong><br>
                <strong>Tandatangan / Signature:</strong>
                @php $sigs = json_decode($sale->signatures ?? '{}', true); @endphp
                @if(!empty($sigs['bidder1']))
                    <br><img src="{{ $sigs['bidder1'] }}" style="height:40px; vertical-align:middle; display:block;">
                @else
                    <br>______________________________
                @endif
                <br>
                <strong>Tarikh / Date:</strong>
            </td>
            <td style="width:50%;">
                <strong>Nama / Name:</strong><br>
                <strong>Tandatangan / Signature:</strong>
                @php $sigs = json_decode($sale->signatures ?? '{}', true); @endphp
                @if(!empty($sigs['bidder2']))
                    <br><img src="{{ $sigs['bidder2'] }}" style="height:40px; vertical-align:middle; display:block;">
                @else
                    <br>______________________________
                @endif
                <br>
                <strong>Tarikh / Date:</strong>
            </td>
        </tr>
    </table>

    <!-- Terms & Conditions -->
    @if($sale->terms_conditions)
    <table>
        <tr>
            <th>G. SYARAT & KETENTUAN / TERMS & CONDITIONS</th>
        </tr>
        <tr>
            <td>{{ $sale->terms_conditions }}</td>
        </tr>
    </table>
    @endif

    <!-- Deposit -->
    @if($sale->deposit_required)
    <table>
        <tr>
            <th>H. DEPOSIT</th>
        </tr>
        <tr>
            <td>Deposit diperlukan: RM {{ number_format($sale->deposit_required, 2) }}</td>
        </tr>
    </table>
    @endif

    <div class="footer">
        <p>KEW.PA-22 — Borang Tender Pelupusan Aset Alih Universiti | Universiti Teknologi Malaysia</p>
        <p>Dokumen ini sah dan lengkap. Document is valid and complete.</p>
    </div>

</body>
</html>
