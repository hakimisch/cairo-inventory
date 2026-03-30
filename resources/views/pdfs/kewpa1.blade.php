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
    
    <div class="flex justify-between items-start mb-1">
        <div></div>
        <div class="text-right">
            <div class="font-bold text-[12px]">KEW.PA-1A</div>
            <div class="text-[10px]">No. Rujukan: {{ $receiving->receive_no }}</div>
        </div>
    </div>
    
    <div class="text-center font-bold text-lg mb-6 underline uppercase tracking-widest">BORANG TERIMAAN ASET (BTA)</div>

    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4 bg-gray-50 uppercase">Nama Pembekal</td>
                <td class="border border-black p-2 w-1/4 uppercase font-bold">{{ $receiving->supplier_name }}</td>
                <td class="border border-black p-2 font-bold w-1/4 bg-gray-50 uppercase">No Pesanan Tempatan (PT) / Tarikh</td>
                <td class="border border-black p-2 w-1/4 font-mono uppercase">{{ $receiving->purchase_order_no }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase align-top" rowspan="2">Alamat Pembekal</td>
                <td class="border border-black p-2 uppercase align-top text-[9px]" rowspan="2">{{ $receiving->supplier_address }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">No. Invois</td>
                <td class="border border-black p-2 uppercase">{{ $receiving->invoice_no ?? '-' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">No. Nota Hantaran (DO)</td>
                <td class="border border-black p-2 uppercase">{{ $receiving->delivery_order_no }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase" colspan="2"></td>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">Tarikh Terimaan Aset</td>
                <td class="border border-black p-2">{{ \Carbon\Carbon::parse($receiving->created_at)->format('d/m/Y') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="w-full border-collapse border border-black text-center mb-12">
        <thead class="bg-gray-50 uppercase font-bold text-[9px]">
            <tr>
                <th class="border border-black p-2 w-8" rowspan="2">Bil</th>
                <th class="border border-black p-2 text-left" rowspan="2">Perihal Aset</th>
                <th class="border border-black p-1" colspan="2">Kuantiti</th>
                <th class="border border-black p-1" colspan="2">Harga (RM)</th>
                <th class="border border-black p-2 w-24" rowspan="2">Catatan</th>
            </tr>
            <tr>
                <th class="border border-black p-1 w-16">Dipesan</th>
                <th class="border border-black p-1 w-16">Diterima</th>
                <th class="border border-black p-1 w-20">Seunit</th>
                <th class="border border-black p-1 w-24">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr class="h-20">
                <td class="border border-black p-2">1</td>
                <td class="border border-black p-2 text-left align-top uppercase">{{ $receiving->item_description }}</td>
                <td class="border border-black p-2 text-lg">{{ $receiving->quantity_ordered }}</td>
                <td class="border border-black p-2 text-lg font-bold">{{ $receiving->quantity_received }}</td>
                <td class="border border-black p-2 text-right">{{ number_format($receiving->unit_price ?? 0, 2) }}</td>
                <td class="border border-black p-2 text-right font-bold">{{ number_format($receiving->total_price ?? (($receiving->unit_price ?? 0) * ($receiving->quantity_received ?? 0)), 2) }}</td>
                <td class="border border-black p-2"></td>
            </tr>
        </tbody>
    </table>

    <div class="grid grid-cols-2 gap-20 px-10">
        <div class="text-left space-y-1">
            <p class="font-bold mb-12">(Tandatangan Pegawai Penerima)</p>
            <p>Nama: ....................................................</p>
            <p>Jawatan: ....................................................</p>
            <p>Fakulti/PTJ: CAIRO UTM</p>
        </div>
        <div class="text-left space-y-1">
            <p class="font-bold mb-12">(Tandatangan Pegawai Teknikal)</p>
            <p>Nama: ....................................................</p>
            <p>Jawatan: ....................................................</p>
            <p>Fakulti/PTJ: CAIRO UTM</p>
        </div>
    </div>

    <div class="mt-10 text-[10px] space-y-1">
        <p class="font-bold">Nota :</p>
        <p>Kegunaan di Fakulti/PTJ (3 salinan)</p>
        <p>Salinan 1 - Jabatan/Makmal/Bahagian/Unit</p>
        <p>Salinan 2 - Pejabat/Bahagian/Unit Pentadbiran</p>
        <p>Salinan 3 - Pejabat Bendahari (Pembayaran)</p>
    </div>

</body>
</html>