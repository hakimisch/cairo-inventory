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
            <div class="font-bold text-[14px]">KEW.PA-11</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">LAPORAN PEMERIKSAAN INVENTORI</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
    </div>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">Jenis Inventori</th>
                <th class="border border-black p-1.5">Daftar KEW.PA-3</th>
                <th class="border border-black p-1.5 text-left">Lokasi</th>
                <th class="border border-black p-1.5 w-16">Kuantiti</th>
                <th class="border border-black p-1.5">Keadaan Inventori</th>
                <th class="border border-black p-1.5 text-left">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inspections as $index => $i)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $i->asset->name ?? '-' }} ({{ $i->asset->category ?? '-' }})</td>
                    <td class="border border-black p-1.5 uppercase">
                        @if($i->is_record_complete)
                            Lengkap
                        @elseif($i->is_record_updated)
                            Kemaskini
                        @else
                            -
                        @endif
                    </td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $i->actual_location ?? '-' }}</td>
                    <td class="border border-black p-1.5">{{ $i->actual_quantity ?? '-' }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $i->status ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-left">{{ $i->notes ?? '-' }}</td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada rekod pemeriksaan inventori.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-8 grid grid-cols-2 gap-8">
        <div>
            {{-- Signatures come from inspection model's signatures JSON --}}
            <div class="mt-16 border-t border-black pt-1 text-center font-bold">Pemeriksa 1</div>
            <div class="mt-2">Nama : ........................................</div>
            <div>Jawatan : ........................................</div>
            <div>Tarikh : ........................................</div>
        </div>
        <div>
            {{-- Signatures come from inspection model's signatures JSON --}}
            <div class="mt-16 border-t border-black pt-1 text-center font-bold">Pemeriksa 2</div>
            <div class="mt-2">Nama : ........................................</div>
            <div>Jawatan : ........................................</div>
            <div>Tarikh : ........................................</div>
        </div>
    </div>

</body>
</html>
