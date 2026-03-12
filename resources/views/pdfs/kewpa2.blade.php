{{-- resources/views/pdfs/kewpa2.blade.php --}}
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
<body class="p-10 text-[10px] leading-tight bg-white font-serif">
    
    <div class="flex justify-between items-start mb-2">
        <div class="font-bold underline italic uppercase text-[8px]">Wajib Cetak</div>
        <div class="text-right font-bold text-sm">KEW.PA-2</div>
    </div>

    <div class="text-right mb-4">
        <p>No. Rujukan: 0001/{{ date('Y') }}</p>
    </div>

    <div class="text-center mb-8">
        <h1 class="text-lg font-bold underline uppercase tracking-widest">BORANG PENOLAKAN ASET ALIH</h1>
    </div>

    {{-- Supplier & Reference Info Table --}}
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td rowspan="2" class="border border-black p-2 w-1/3 align-top">
                    <span class="font-bold">Nama dan Alamat Pembekal:</span><br />
                    <div class="mt-1 uppercase leading-normal">
                        {{ $asset->supplier_name ?? 'N/A' }}<br />
                        {{ $asset->supplier_address ?? 'N/A' }}
                    </div>
                </td>
                <td colspan="2" class="border border-black p-1 text-center font-bold bg-gray-50 uppercase text-[8px]">Pesanan Kerajaan (PK) / Kontrak</td>
                <td colspan="2" class="border border-black p-1 text-center font-bold bg-gray-50 uppercase text-[8px]">Nota Hantaran (DO)</td>
            </tr>
            <tr class="text-center text-[9px]">
                <td class="border border-black p-1 font-bold w-1/6">No. Rujukan</td>
                <td class="border border-black p-1 font-bold w-1/6">Tarikh</td>
                <td class="border border-black p-1 font-bold w-1/6">No. Rujukan</td>
                <td class="border border-black p-1 font-bold w-1/6">Tarikh</td>
            </tr>
            <tr class="text-center h-8 font-mono">
                <td class="border border-black p-1"></td>
                <td class="border border-black p-1">{{ $asset->po_reference ?? '-' }}</td>
                <td class="border border-black p-1 font-serif">{{ \Carbon\Carbon::parse($asset->created_at)->format('d/m/Y') }}</td>
                <td class="border border-black p-1">{{ $asset->do_reference ?? '-' }}</td>
                <td class="border border-black p-1 font-serif">{{ \Carbon\Carbon::parse($asset->created_at)->format('d/m/Y') }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Rejection Details Table --}}
    <table class="w-full border-collapse border border-black mb-8 text-center uppercase">
        <thead>
            <tr class="font-bold bg-gray-50">
                <td rowspan="2" class="border border-black p-2 w-16">No. Kod</td>
                <td rowspan="2" class="border border-black p-2">Keterangan Aset Alih</td>
                <td colspan="2" class="border border-black p-1">Kuantiti</td>
                <td rowspan="2" class="border border-black p-2 w-32">Sebab-Sebab Penolakan</td>
                <td rowspan="2" class="border border-black p-2 w-32">Catatan</td>
            </tr>
            <tr class="font-bold bg-gray-50">
                <td class="border border-black p-1 w-12">Dipesan</td>
                <td class="border border-black p-1 w-12">Ditolak</td>
            </tr>
        </thead>
        <tbody>
            <tr class="h-20">
                <td class="border border-black p-2 font-mono text-[9px]">{{ $asset->asset_tag }}</td>
                <td class="border border-black p-2 text-left align-top font-bold">{{ $asset->name }}</td>
                <td class="border border-black p-2 text-lg">1</td>
                <td class="border border-black p-2 text-lg">1</td>
                <td class="border border-black p-2 italic text-[9px] normal-case">{{ $asset->rejection_reason ?? 'Kerosakan fizikal / tidak mengikut spesifikasi' }}</td>
                <td class="border border-black p-2 normal-case">Sila hubungi pembekal untuk penggantian.</td>
            </tr>
        </tbody>
    </table>

    {{-- Signature Section --}}
    <div class="grid grid-cols-2 gap-20 mt-20 text-left px-10">
        <div class="space-y-1">
            <p class="font-bold uppercase text-[9px]">Pegawai Penerima</p>
            <div class="h-16"></div>
            <p>....................................................</p>
            <p>Nama: {{ $asset->receiver_name ?? 'Hafiz Hakimi' }}</p>
            <p>Jawatan: Asset Officer (CAIRO)</p>
            <p>Tarikh: {{ date('d/m/Y') }}</p>
        </div>
        <div class="space-y-1 text-center">
            <p class="font-bold uppercase text-[9px] text-left">Akuan Terima Pembekal</p>
            <div class="h-16"></div>
            <p class="text-left">....................................................</p>
            <p class="text-left">Nama:</p>
            <p class="text-left">Tarikh:</p>
            <p class="text-left">Cap Syarikat:</p>
        </div>
    </div>

</body>
</html>