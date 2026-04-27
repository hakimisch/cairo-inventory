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

    {{-- ── Header ── --}}
    <div class="flex justify-between items-start mb-1">
        <div></div>
        <div class="text-right">
            <div class="font-bold text-[14px]">KEW.PA-6</div>
            <div class="text-[10px]">(No. Siri Pendaftaran : {{ $asset->asset_tag }})</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">DAFTAR PERGERAKAN ASET</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Unit/Makmal :</strong> {{ $asset->location }}</p>
        <p><strong>Nama Aset :</strong> {{ $asset->name }} ({{ $asset->asset_tag }})</p>
    </div>

    {{-- ── Penempatan (Placements) ── --}}
    <div class="font-bold mb-1 uppercase mt-6">A. PENEMPATAN / PINJAMAN</div>
    <table class="w-full border-collapse border border-black mb-6 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5">Lokasi</th>
                <th class="border border-black p-1.5 w-20">Tarikh</th>
                <th class="border border-black p-1.5">Nama Pegawai</th>
                <th class="border border-black p-1.5 w-20">No. Pekerja</th>
                <th class="border border-black p-1.5 w-20">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->placements as $index => $p)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $p->location }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($p->assigned_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $p->custodian_name }}</td>
                    <td class="border border-black p-1.5">{{ $p->staff_id ?? '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="6">Tiada rekod penempatan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Pindahan (Transfers) ── --}}
    <div class="font-bold mb-1 uppercase">B. PINDAHAN</div>
    <table class="w-full border-collapse border border-black mb-6 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 w-20">Tarikh</th>
                <th class="border border-black p-1.5">Lokasi Baru</th>
                <th class="border border-black p-1.5">Pegawai Baru</th>
                <th class="border border-black p-1.5 w-24">No. Rujukan</th>
                <th class="border border-black p-1.5">Sebab</th>
                <th class="border border-black p-1.5 w-20">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->transfers as $index => $t)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($t->transfer_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $t->to_location }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $t->to_custodian }}</td>
                    <td class="border border-black p-1.5">{{ $t->reference_no ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-left">{{ $t->reason ?? '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada rekod pindahan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Signature ── --}}
    <div class="mt-8 grid grid-cols-2 gap-8">
        <div>
            <div class="mt-16 border-t border-black pt-1 text-center font-bold">Tandatangan Pegawai Bertanggungjawab</div>
            <div class="mt-2">Nama : ........................................</div>
            <div>Jawatan : ........................................</div>
            <div>Tarikh : ........................................</div>
        </div>
        <div>
            <div class="mt-16 border-t border-black pt-1 text-center font-bold">Tandatangan Ketua PTJ</div>
            <div class="mt-2">Nama : ........................................</div>
            <div>Jawatan : ........................................</div>
            <div>Tarikh : ........................................</div>
        </div>
    </div>

    <div class="mt-6 text-[10px] italic border-t border-gray-400 pt-3">
        Nota: Sila sediakan dalam 2 salinan (1-Unit/Makmal; 1-Pejabat Pentadbiran)
    </div>

</body>
</html>
