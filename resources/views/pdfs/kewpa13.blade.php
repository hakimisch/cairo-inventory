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
            <div class="font-bold text-[14px]">KEW.PA-13</div>
            <div class="text-[10px]">(No. Siri Pendaftaran : {{ $asset->asset_tag }})</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">SENARAI HARTA TETAP YANG MEMERLUKAN PENYELENGGARAAN</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Unit/Makmal :</strong> {{ $asset->location }}</p>
        <p><strong>Nama Aset :</strong> {{ $asset->name }} ({{ $asset->asset_tag }})</p>
    </div>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 w-20">Tarikh</th>
                <th class="border border-black p-1.5 text-left">Perihal Penyelenggaraan</th>
                <th class="border border-black p-1.5 w-24">No. Kontrak</th>
                <th class="border border-black p-1.5">Syarikat</th>
                <th class="border border-black p-1.5 w-20">Kos (RM)</th>
                <th class="border border-black p-1.5 w-24">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($maintenances as $index => $m)
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($m->maintenance_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $m->description }}</td>
                    <td class="border border-black p-1.5">{{ $m->contract_no ?? '-' }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $m->company_name ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($m->cost, 2) }}</td>
                    <td class="border border-black p-1.5">
                        @php $msigs = json_decode($m->signatures ?? '{}', true); @endphp
                        @if(!empty($msigs['officer']))
                            <img src="{{ $msigs['officer'] }}" style="height:28px; vertical-align:middle;">
                        @endif
                    </td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada rekod penyelenggaraan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-8">
        {{-- Signatures come from maintenance model's signatures JSON --}}
        <div class="mt-16 border-t border-black pt-1 text-center font-bold w-64 mx-auto">Tandatangan Pegawai Bertanggungjawab</div>
        <div class="mt-2 text-center">Nama : ........................................</div>
        <div class="text-center">Tarikh : ........................................</div>
    </div>

</body>
</html>
