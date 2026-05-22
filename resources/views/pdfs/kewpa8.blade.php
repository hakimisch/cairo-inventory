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
            <div class="font-bold text-[14px]">KEW.PA-8</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">LAPORAN TAHUNAN HARTA TETAP DAN INVENTORI</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Tahun :</strong> {{ $year }}</p>
        <p><strong>Tarikh Cetakan :</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
    </div>

    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">Lokasi / Unit</th>
                <th class="border border-black p-1.5" colspan="2">Harta Tetap</th>
                <th class="border border-black p-1.5" colspan="2">Inventori</th>
                <th class="border border-black p-1.5 w-20">Jumlah (RM)</th>
            </tr>
            <tr>
                <th class="border border-black p-1"></th>
                <th class="border border-black p-1"></th>
                <th class="border border-black p-1 w-16">Bilangan</th>
                <th class="border border-black p-1 w-24">Nilai (RM)</th>
                <th class="border border-black p-1 w-16">Bilangan</th>
                <th class="border border-black p-1 w-24">Nilai (RM)</th>
                <th class="border border-black p-1"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $index => $row)
                @php
                    $rowTotal = ($row->fixed_value ?? 0) + ($row->inventory_value ?? 0);
                @endphp
                <tr class="h-8">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $row->location ?? 'Tiada Lokasi' }}</td>
                    <td class="border border-black p-1.5">{{ $row->fixed_count ?? 0 }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($row->fixed_value ?? 0, 2) }}</td>
                    <td class="border border-black p-1.5">{{ $row->inventory_count ?? 0 }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($row->inventory_value ?? 0, 2) }}</td>
                    <td class="border border-black p-1.5 text-right font-bold">{{ number_format($rowTotal, 2) }}</td>
                </tr>
            @empty
                <tr class="h-8">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada data untuk tahun ini.</td>
                </tr>
            @endforelse
        </tbody>
        @if(count($rows) > 0)
            @php
                $grandFixedCount = $rows->sum('fixed_count');
                $grandFixedValue = $rows->sum('fixed_value');
                $grandInvCount   = $rows->sum('inventory_count');
                $grandInvValue   = $rows->sum('inventory_value');
                $grandTotal      = $grandFixedValue + $grandInvValue;
            @endphp
            <tfoot class="font-bold bg-gray-50">
                <tr class="h-8">
                    <td class="border border-black p-1.5" colspan="2">JUMLAH BESAR</td>
                    <td class="border border-black p-1.5">{{ $grandFixedCount }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($grandFixedValue, 2) }}</td>
                    <td class="border border-black p-1.5">{{ $grandInvCount }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($grandInvValue, 2) }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

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
        * Laporan ini dijana secara automatik daripada sistem CAIRO Inventory. KEW.PA-8 — Laporan Tahunan Harta Tetap dan Inventori
    </div>

</body>
</html>
