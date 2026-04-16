<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face { font-family: 'Serif'; src: local('Times New Roman'); }
        body { font-family: 'Serif', serif; }
    </style>
</head>
<body class="p-10 text-[10px] leading-tight bg-white font-serif text-black">

    {{-- ── Header ── --}}
    <div class="flex justify-between items-start mb-1">
        <div></div>
        <div class="text-right">
            <div class="font-bold text-[12px]">KEW.PA-1</div>
        </div>
    </div>

    <div class="text-center font-bold text-[13px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[12px] mb-1 uppercase">BORANG LAPORAN PENERIMAAN ASET ALIH UNIVERSITI</div>
    <div class="text-center italic text-[10px] mb-6">(Hendaklah diisi dalam 2 salinan jika terdapat kerosakan/perselisihan)</div>

    {{-- ── Supplier info ── --}}
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[30%]">Nama Pembekal</td>
                <td class="border border-black p-2 uppercase font-bold" colspan="3">{{ $receiving->supplier_name }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 align-top" rowspan="2">Alamat Pembekal</td>
                <td class="border border-black p-2 text-[9px] align-top" rowspan="2" style="white-space: pre-wrap;">{{ $receiving->supplier_address }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[20%]">No. Telefon</td>
                <td class="border border-black p-2 w-[20%]">{{ $receiving->supplier_phone ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">No. Faks</td>
                <td class="border border-black p-2">{{ $receiving->supplier_fax ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- ── Items table ── --}}
    {{-- Support both a single receiving record and a collection of items --}}
    @php
        $items = isset($receiving->items) && $receiving->items->count() > 0
            ? $receiving->items
            : collect([[
                'delivery_order_no'  => $receiving->delivery_order_no,
                'delivery_order_date'=> $receiving->created_at,
                'item_description'   => $receiving->item_description,
                'quantity_ordered'   => $receiving->quantity_ordered,
                'quantity_received'  => $receiving->quantity_received,
                'damage_description' => $receiving->damage_description ?? null,
                'notes'              => $receiving->notes ?? null,
            ]]);

        $hasIssue = $items->contains(fn($i) =>
            is_array($i)
                ? (($i['quantity_ordered'] - $i['quantity_received']) != 0 || !empty($i['damage_description']))
                : (($i->quantity_ordered - $i->quantity_received) != 0 || !empty($i->damage_description))
        );
    @endphp

    <table class="w-full border-collapse border border-black text-center mb-12 text-[9px]">
        <thead class="bg-gray-50 font-bold uppercase">
            <tr>
                <th class="border border-black p-2 w-8" rowspan="2">Bil</th>
                <th class="border border-black p-2 w-32" rowspan="2">Nota Hantaran<br/>(No. &amp; Tarikh)</th>
                <th class="border border-black p-2 text-left" rowspan="2">Nama Aset</th>
                <th class="border border-black p-1" colspan="3">Kuantiti</th>
                <th class="border border-black p-2 w-32" rowspan="2">Perihal Kerosakan</th>
                <th class="border border-black p-2 w-24" rowspan="2">Catatan</th>
            </tr>
            <tr>
                <th class="border border-black p-1 w-12">Dipesan</th>
                <th class="border border-black p-1 w-12">Diterima</th>
                <th class="border border-black p-1 w-16">Perselisihan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                @php
                    $doNo   = is_array($item) ? $item['delivery_order_no']   : $item->delivery_order_no;
                    $doDate = is_array($item) ? $item['delivery_order_date']  : ($item->delivery_order_date ?? $receiving->created_at);
                    $desc   = is_array($item) ? $item['item_description']     : $item->item_description;
                    $qOrd   = is_array($item) ? $item['quantity_ordered']     : $item->quantity_ordered;
                    $qRec   = is_array($item) ? $item['quantity_received']    : $item->quantity_received;
                    $dmg    = is_array($item) ? ($item['damage_description'] ?? null) : ($item->damage_description ?? null);
                    $notes  = is_array($item) ? ($item['notes'] ?? null)      : ($item->notes ?? null);
                    $diff   = $qOrd - $qRec;
                @endphp
                <tr class="h-16 align-top">
                    <td class="border border-black p-2">{{ $index + 1 }}</td>
                    <td class="border border-black p-2 font-mono">
                        {{ $doNo }}<br/>
                        {{ \Carbon\Carbon::parse($doDate)->format('d/m/Y') }}
                    </td>
                    <td class="border border-black p-2 text-left uppercase">{{ $desc }}</td>
                    <td class="border border-black p-2">{{ $qOrd }}</td>
                    <td class="border border-black p-2">{{ $qRec }}</td>
                    <td class="border border-black p-2 font-bold {{ $diff != 0 ? 'text-red-600' : '' }}">
                        {{ $diff != 0 ? $diff : '-' }}
                    </td>
                    <td class="border border-black p-2 uppercase {{ $dmg ? 'text-red-600' : '' }}">{{ $dmg ?? '-' }}</td>
                    <td class="border border-black p-2">{{ $notes ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- ── Signature blocks ── --}}
    <div class="grid grid-cols-2 gap-20 px-10">
        <div class="text-left space-y-1">
            <p class="font-bold mb-12">Tandatangan Penerima</p>
            <p>Nama &nbsp;&nbsp;&nbsp;: ....................................................</p>
            <p>Jawatan : ....................................................</p>
            <p>Tarikh &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($receiving->created_at)->format('d/m/Y') }}</p>
        </div>
        <div class="text-left space-y-1">
            <p class="font-bold mb-12">Tandatangan Pegawai Bertanggungjawab</p>
            <p>Nama &nbsp;&nbsp;&nbsp;: ....................................................</p>
            <p>Jawatan : ....................................................</p>
            <p>Tarikh &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($receiving->created_at)->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- ── Copy instruction note ── --}}
    <div class="mt-8 text-[10px] space-y-1 border-t border-gray-400 pt-3">
        <p class="font-bold">Nota :</p>
        @if($hasIssue)
            <p>Borang ini hendaklah disediakan dalam <strong>2 salinan</strong> memandangkan terdapat kerosakan atau perselisihan kuantiti.</p>
        @else
            <p>Borang ini disediakan dalam <strong>1 salinan</strong> jika tiada kerosakan atau perselisihan.</p>
        @endif
    </div>

</body>
</html>
