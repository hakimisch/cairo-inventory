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
            <div class="font-bold text-[14px]">KEW.PA-28</div>
            <div class="text-[10px]">(No. Siri Pendaftaran : {{ $asset->asset_tag }})</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">LAPORAN KEHILANGAN ASET</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Unit/Makmal :</strong> {{ $asset->location }}</p>
        <p><strong>Nama Aset :</strong> {{ $asset->name }} ({{ $asset->asset_tag }})</p>
    </div>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">Lokasi Kejadian</th>
                <th class="border border-black p-1.5 w-20">Tarikh</th>
                <th class="border border-black p-1.5">Cara Hilang</th>
                <th class="border border-black p-1.5">Pegawai Terakhir</th>
                <th class="border border-black p-1.5 w-24">No. Laporan Polis</th>
                <th class="border border-black p-1.5 w-20">Nilai (RM)</th>
                <th class="border border-black p-1.5">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->lossReports as $index => $lr)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $lr->incident_location }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($lr->loss_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $lr->loss_method }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $lr->last_officer ?? '-' }}</td>
                    <td class="border border-black p-1.5">{{ $lr->police_report_no ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($lr->current_value, 2) }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $lr->status }}</td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="8">Tiada rekod kehilangan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-8">
        <div class="mt-16 border-t border-black pt-1 text-center font-bold w-64 mx-auto">Tandatangan Pegawai Bertanggungjawab</div>
        <div class="mt-2 text-center">Nama : ........................................</div>
        <div class="text-center">Jawatan : ........................................</div>
        <div class="text-center">Tarikh : ........................................</div>
    </div>

</body>
</html>
