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
            <div class="font-bold text-[14px]">KEW.PA-5</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">SENARAI DAFTAR INVENTORI</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Tahun :</strong> {{ $year }}</p>
        <p><strong>Tarikh Cetakan :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">No. Siri Pendaftaran</th>
                <th class="border border-black p-1.5 text-left">Nama Inventori</th>
                <th class="border border-black p-1.5">Kategori</th>
                <th class="border border-black p-1.5">Lokasi</th>
                <th class="border border-black p-1.5 w-16">Kuantiti</th>
                <th class="border border-black p-1.5">Unit</th>
                <th class="border border-black p-1.5 w-24">Harga Seunit (RM)</th>
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
                    <td class="border border-black p-1.5">{{ $asset->quantity ?? 1 }}</td>
                    <td class="border border-black p-1.5">{{ $asset->unit_of_measure ?? 'UNIT' }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($asset->purchase_price, 2) }}</td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="8">Tiada inventori dijumpai untuk tahun ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 text-right font-bold text-[11px]">
        Jumlah Item: {{ $assets->count() }} &nbsp;|&nbsp; Jumlah Nilai: RM {{ number_format($assets->sum('purchase_price'), 2) }}
    </div>

    <div class="mt-8 grid grid-cols-2 gap-8">
        <div class="text-center">
            <div class="font-bold uppercase mb-1">Disediakan oleh:</div>
            <div class="mt-10">........................................</div>
            <div>Nama: _________________________</div>
            <div>Jawatan: _________________________</div>
            <div>Tarikh: _________________________</div>
        </div>
        <div class="text-center">
            <div class="font-bold uppercase mb-1">Disahkan oleh:</div>
            <div class="mt-10">........................................</div>
            <div>Nama: _________________________</div>
            <div>Jawatan: _________________________</div>
            <div>Tarikh: _________________________</div>
        </div>
    </div>

    <div class="mt-6 text-[9px] italic border-t border-gray-400 pt-3 text-center">
        * Laporan ini dijana secara automatik daripada sistem CAIRO Inventory. KEW.PA-5 — Senarai Daftar Inventori
    </div>

</body>
</html>
