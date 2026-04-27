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
<body class="p-8 text-[10px] leading-tight bg-white font-serif text-black">

    <div class="flex justify-between items-start mb-1">
        <div></div>
        <div class="text-right">
            <div class="font-bold text-[14px]">KEW.PA-12</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">PERAKUAN PEMERIKSAAN TAHUNAN ASET</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Tahun :</strong> {{ $year }}</p>
        <p><strong>Tarikh Laporan :</strong> {{ \Carbon\Carbon::parse($report_date)->format('d/m/Y') }}</p>
    </div>

    <table class="w-full border-collapse border border-black text-center mb-6">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">No. Siri Pendaftaran</th>
                <th class="border border-black p-1.5 text-left">Nama Aset</th>
                <th class="border border-black p-1.5">Kategori</th>
                <th class="border border-black p-1.5">Lokasi</th>
                <th class="border border-black p-1.5 w-24">Harga (RM)</th>
                <th class="border border-black p-1.5">Status Pemeriksaan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($assets as $index => $asset)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 text-left font-mono uppercase">{{ $asset->asset_tag }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $asset->name }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $asset->category }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $asset->location }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($asset->purchase_price, 2) }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $asset->latest_inspection_status ?? 'BELUM DIPERIKSA' }}</td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada aset dijumpai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="font-bold mb-2 uppercase">RINGKASAN</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[40%]">Jumlah Aset Diperiksa</td>
                <td class="border border-black p-2">{{ $summary['inspected'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Jumlah Aset Tidak Diperiksa</td>
                <td class="border border-black p-2">{{ $summary['not_inspected'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Jumlah Aset Rosak / Hilang</td>
                <td class="border border-black p-2">{{ $summary['damaged_lost'] ?? 0 }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Jumlah Keseluruhan</td>
                <td class="border border-black p-2 font-bold">{{ $summary['total'] ?? 0 }}</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-6 grid grid-cols-2 gap-8">
        <div>
            <div class="mt-16 border-t border-black pt-1 text-center font-bold">Pemeriksa</div>
            <div class="mt-2">Nama : ........................................</div>
            <div>Jawatan : ........................................</div>
            <div>Tarikh : ........................................</div>
        </div>
        <div>
            <div class="mt-16 border-t border-black pt-1 text-center font-bold">Ketua PTJ</div>
            <div class="mt-2">Nama : ........................................</div>
            <div>Jawatan : ........................................</div>
            <div>Tarikh : ........................................</div>
        </div>
    </div>

</body>
</html>
