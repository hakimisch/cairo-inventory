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
            <div class="font-bold text-[14px]">KEW.PA-14</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">DAFTAR PENYELENGGARAAN HARTA TETAP</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
    </div>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 w-20">Tarikh</th>
                <th class="border border-black p-1.5 text-left">Butir-Butir Kerja</th>
                <th class="border border-black p-1.5">No. Kontrak/PT & Tarikh</th>
                <th class="border border-black p-1.5">Nama Syarikat</th>
                <th class="border border-black p-1.5 w-20">Kos RM</th>
                <th class="border border-black p-1.5 w-28">Nama & Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $index => $m)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5">{{ $m->maintenance_date ? \Carbon\Carbon::parse($m->maintenance_date)->format('d/m/Y') : '-' }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $m->description ?? '-' }}</td>
                    <td class="border border-black p-1.5">{{ $m->contract_no ?? '-' }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $m->company_name ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-right">{{ $m->cost ? number_format($m->cost, 2) : '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada rekod penyelenggaraan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 text-[9px] leading-relaxed">
        <p><strong>Nota:</strong> (a) tarikh, (b) butir-butir kerja termasuk alat ganti, (c) no. kontrak/PT, (d) nama syarikat, (e) kos, (f) tandatangan pegawai bertanggungjawab</p>
    </div>

    <div class="mt-8">
        <div class="mt-16 border-t border-black pt-1 text-center font-bold w-80 mx-auto">Pegawai Bertanggungjawab</div>
        <div class="mt-2 text-center">Nama : ........................................</div>
        <div class="text-center">Jawatan : ........................................</div>
        <div class="text-center">Tarikh : ........................................</div>
    </div>

</body>
</html>
