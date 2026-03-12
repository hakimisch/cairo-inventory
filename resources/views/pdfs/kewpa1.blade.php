<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-10 text-[10px] leading-tight bg-white font-serif">
    <div class="text-right font-bold mb-1">KEW.PA-1</div>
    <div class="text-center font-bold text-lg mb-6 underline uppercase tracking-tighter">BORANG PENERIMAAN ASET ALIH</div>

    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold w-1/4 bg-gray-50 uppercase">Pembekal</td>
                <td class="border border-black p-2 w-1/4">
                    <div class="font-bold">{{ $receiving->supplier_name }}</div>
                    <div class="text-[9px]">{{ $receiving->supplier_address }}</div>
                </td>
                <td class="border border-black p-2 font-bold w-1/4 bg-gray-50 uppercase">No. Pesanan (PO)</td>
                <td class="border border-black p-2 w-1/4">{{ $receiving->purchase_order_no }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">No. Nota Hantaran (DO)</td>
                <td class="border border-black p-2">{{ $receiving->delivery_order_no }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">Tarikh Terima</td>
                <td class="border border-black p-2">{{ \Carbon\Carbon::parse($receiving->created_at)->format('d/m/Y') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 uppercase font-bold text-[9px]">
            <tr>
                <th class="border border-black p-2 w-12">Bil</th>
                <th class="border border-black p-2 text-left">Keterangan Aset Alih</th>
                <th class="border border-black p-2 w-24">Kuantiti Dipesan</th>
                <th class="border border-black p-2 w-24">Kuantiti Diterima</th>
            </tr>
        </thead>
        <tbody>
            <tr class="h-24">
                <td class="border border-black p-2">1</td>
                <td class="border border-black p-2 text-left align-top uppercase">{{ $receiving->item_description }}</td>
                <td class="border border-black p-2 text-lg">{{ $receiving->quantity_ordered }}</td>
                <td class="border border-black p-2 text-lg font-bold">{{ $receiving->quantity_received }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-20 flex justify-between px-10">
        <div class="text-center">
            <p class="mb-16">....................................................</p>
            <p class="font-bold underline uppercase">Pegawai Penerima</p>
        </div>
        <div class="text-center">
            <p class="mb-16">....................................................</p>
            <p class="font-bold underline uppercase">Pegawai Teknikal</p>
        </div>
    </div>
</body>
</html>