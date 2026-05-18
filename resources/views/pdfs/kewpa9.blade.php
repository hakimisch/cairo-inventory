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
            <div class="font-bold text-[14px]">KEW.PA-9</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">LAPORAN KEROSAKAN ASET</div>

    {{-- Asset Info --}}
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[25%]">No. Siri Pendaftaran</td>
                <td class="border border-black p-2 font-mono uppercase">{{ $report->asset->asset_tag }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[20%]">Kategori</td>
                <td class="border border-black p-2 uppercase">{{ $report->asset->category }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Nama Aset</td>
                <td class="border border-black p-2 uppercase" colspan="3">{{ $report->asset->name }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Jenama / Model</td>
                <td class="border border-black p-2 uppercase" colspan="3">{{ $report->asset->brand }} / {{ $report->asset->model }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">No. Siri</td>
                <td class="border border-black p-2 font-mono">{{ $report->asset->serial_number ?? '—' }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50">Lokasi</td>
                <td class="border border-black p-2 uppercase">{{ $report->asset->location }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Damage Report Details --}}
    <div class="font-bold mb-2 uppercase">BUTIRAN KEROSAKAN</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[30%]">Dilaporkan Oleh</td>
                <td class="border border-black p-2 uppercase">{{ $report->reported_by }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Tarikh Kerosakan</td>
                <td class="border border-black p-2">{{ $report->damage_date ? \Carbon\Carbon::parse($report->damage_date)->format('d/m/Y') : '................................' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Pengguna Terakhir</td>
                <td class="border border-black p-2 uppercase">{{ $report->last_user ?? '................................' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Kos Penyelenggaraan Lepas</td>
                <td class="border border-black p-2">RM {{ number_format($report->previous_maintenance_cost ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Huraian Kerosakan</td>
                <td class="border border-black p-2">{{ $report->damage_description }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Status</td>
                <td class="border border-black p-2 uppercase">
                    @switch($report->status)
                        @case('pending') <span>Menunggu Siasatan</span> @break
                        @case('investigating') <span>Dalam Siasatan</span> @break
                        @case('resolved') <span>Selesai</span> @break
                        @default <span>{{ $report->status }}</span>
                    @endswitch
                </td>
            </tr>
        </tbody>
    </table>

    {{-- Technical Notes --}}
    @if($report->technical_notes)
    <div class="font-bold mb-2 uppercase">CATATAN TEKNIKAL</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2" colspan="2">{{ $report->technical_notes }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Recommendation --}}
    @if($report->recommendation)
    <div class="font-bold mb-2 uppercase">SYOR / CADANGAN</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2" colspan="2">{{ $report->recommendation }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- HOD Decision --}}
    @if($report->hod_decision)
    <div class="font-bold mb-2 uppercase">KEPUTUSAN KETUA PTJ</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[30%]">Keputusan</td>
                <td class="border border-black p-2 uppercase">{{ $report->hod_decision }}</td>
            </tr>
        </tbody>
    </table>
    @endif

    {{-- Signatures --}}
    <table class="w-full border-collapse border border-black mt-8">
        <tbody>
            <tr>
                <td class="border border-black p-3 w-1/2 align-top">
                    <div class="font-bold mb-8 uppercase">Disediakan Oleh:</div>
                    <div class="mt-12 border-t border-black pt-1 text-center font-bold">Tandatangan</div>
                    <div class="mt-1">Nama: ........................................</div>
                    <div>Jawatan: ........................................</div>
                    <div>Tarikh: ........................................</div>
                </td>
                <td class="border border-black p-3 w-1/2 align-top">
                    <div class="font-bold mb-8 uppercase">Disahkan Oleh (Ketua PTJ):</div>
                    <div class="mt-12 border-t border-black pt-1 text-center font-bold">Tandatangan</div>
                    <div class="mt-1">Nama: ........................................</div>
                    <div>Jawatan: ........................................</div>
                    <div>Tarikh: ........................................</div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="mt-6 text-[10px] italic border-t border-gray-400 pt-3">
        Nota: Laporan ini hendaklah diisi dalam 2 salinan (1-PTJ; 1-Pejabat Harta Benda)
    </div>

</body>
</html>
