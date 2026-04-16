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
            <div class="font-bold text-[14px]">KEW.PA-3</div>
            <div class="text-[10px]">(No. Siri Pendaftaran : {{ $asset->asset_tag }})</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-2 uppercase">DAFTAR INVENTORI</div>
    <div class="text-center italic text-[10px] mb-4">(Satu (1) daftar bagi satu (1) jenis inventori dalam satu Pesanan Tempatan)</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Unit/Makmal :</strong> {{ $asset->location }}</p>
    </div>

    <div class="font-bold mb-1 uppercase">BAHAGIAN A</div>

    {{-- ── Bahagian A — PA-3 specific (inventory register, no engine/chassis/vehicle fields) ── --}}
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            {{-- Row 1: Kategori / No. Bar Kod --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50 w-[22%]">Kategori</td>
                <td class="border border-black p-1.5 w-[28%] uppercase">{{ $asset->category }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 w-[22%]">No. Bar Kod</td>
                <td class="border border-black p-1.5 w-[28%] font-mono">{{ $asset->national_code ?? $asset->asset_tag }}</td>
            </tr>
            {{-- Row 2: Jenis / No. Baucer Bayaran --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Jenis</td>
                <td class="border border-black p-1.5 uppercase font-bold">INVENTORI</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">No. Baucer Bayaran</td>
                <td class="border border-black p-1.5 uppercase font-mono">{{ $asset->voucher_no ?? '—' }}</td>
            </tr>
            {{-- Row 3: Sub Jenis / Jenis Vot --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Sub Jenis</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->sub_type ?? '—' }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Jenis Vot</td>
                <td class="border border-black p-1.5 uppercase font-mono">{{ $asset->budget_vot ?? '—' }}</td>
            </tr>
            {{-- Row 4: Kuantiti / Unit Pengukuran — inventory-specific --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Kuantiti</td>
                <td class="border border-black p-1.5 font-bold text-center">{{ $asset->quantity ?? 1 }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Unit Pengukuran</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->unit_of_measure ?? 'Unit' }}</td>
            </tr>
            {{-- Row 5: Harga Perolehan Asal / Tarikh Diterima --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Harga Perolehan Asal</td>
                <td class="border border-black p-1.5 font-bold">RM {{ number_format($asset->purchase_price, 2) }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Tarikh Diterima</td>
                <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</td>
            </tr>
            {{-- Row 6: Tempoh Jaminan / No. Pesanan Tempatan (rowspan 2) --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Tempoh Jaminan</td>
                <td class="border border-black p-1.5">
                    {{ $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('d/m/Y') : '—' }}
                </td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-top" rowspan="2">No. Pesanan Tempatan<br/>Universiti dan Tarikh</td>
                <td class="border border-black p-1.5 align-top" rowspan="2">
                    <div class="font-mono uppercase">{{ $asset->po_reference ?? '—' }}</div>
                    <div class="mt-1">{{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</div>
                </td>
            </tr>
            {{-- Row 7: Nama Pembekal dan Alamat --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-top">Nama Pembekal dan Alamat</td>
                <td class="border border-black p-1.5 align-top uppercase">
                    <strong>{{ $asset->supplier_name ?? '—' }}</strong><br/>
                    <span class="text-[8px] leading-tight">{{ $asset->supplier_address ?? '' }}</span>
                </td>
            </tr>
            {{-- Row 8: Signature block spanning full width --}}
            <tr>
                <td class="border border-black p-1.5 align-bottom" colspan="4">
                    <div class="grid grid-cols-2 gap-8 mt-6">
                        <div>
                            <div>...................................................................</div>
                            <div class="font-bold mt-1">Tandatangan Pegawai Bertanggungjawab</div>
                            <div class="mt-1">Nama &nbsp;&nbsp;&nbsp;: {{ $asset->custodian_name ?? '.................................' }}</div>
                            <div>Jawatan : .................................</div>
                            <div>Tarikh &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</div>
                            <div>Cop &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ── Komponen / Aksesori ── --}}
    <div class="font-bold mb-1 uppercase">KOMPONEN / AKSESORI</div>
    <table class="w-full border-collapse border border-black mb-8 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">Keterangan</th>
                <th class="border border-black p-1.5 w-16">Kuantiti</th>
                <th class="border border-black p-1.5 w-28">Harga (RM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->components ?? [] as $index => $comp)
                <tr>
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">
                        {{ is_array($comp) ? ($comp['description'] ?? $comp['item'] ?? '—') : ($comp->description ?? $comp->item ?? '—') }}
                    </td>
                    <td class="border border-black p-1.5">
                        {{ is_array($comp) ? ($comp['qty'] ?? 1) : ($comp->qty ?? 1) }}
                    </td>
                    <td class="border border-black p-1.5 text-right">
                        {{ number_format(is_array($comp) ? ($comp['cost'] ?? $comp['price'] ?? 0) : ($comp->cost ?? $comp->price ?? 0), 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="border border-black p-3 italic text-gray-500" colspan="4">Tiada data komponen direkodkan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── PENEMPATAN — PA-3 requires Kuantiti + No. Siri Pendaftaran ── --}}
    <div class="font-bold mb-1 uppercase">PENEMPATAN</div>
    <table class="w-full border-collapse border border-black mb-6 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-12">Kuantiti</th>
                <th class="border border-black p-1.5 w-28">No. Siri Pendaftaran</th>
                <th class="border border-black p-1.5 text-left">Lokasi</th>
                <th class="border border-black p-1.5 w-24">Tarikh</th>
                <th class="border border-black p-1.5 text-left">Nama Staf</th>
                <th class="border border-black p-1.5 w-20">No. Pekerja</th>
                <th class="border border-black p-1.5 w-24">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->placements as $placement)
                <tr class="h-10">
                    <td class="border border-black p-1.5">{{ $placement->quantity_placed ?? 1 }}</td>
                    <td class="border border-black p-1.5 font-mono">{{ $placement->specific_serial_no ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $placement->location }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($placement->assigned_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $placement->custodian_name }}</td>
                    <td class="border border-black p-1.5">{{ $placement->staff_id ?? '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-10">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="7">Tiada rekod penempatan</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── PEMERIKSAAN — with Tandatangan column ── --}}
    <div class="font-bold mb-1 uppercase">PEMERIKSAAN</div>
    <table class="w-full border-collapse border border-black mb-6 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-[15%]">Tarikh</th>
                <th class="border border-black p-1.5 w-[22%]">Status Aset</th>
                <th class="border border-black p-1.5 w-[28%]">Nama Pemeriksa</th>
                <th class="border border-black p-1.5">Catatan</th>
                <th class="border border-black p-1.5 w-[15%]">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->inspections as $insp)
                <tr class="h-10">
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($insp->inspection_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $insp->status }}</td>
                    <td class="border border-black p-1.5">{{ $insp->inspector_name }}</td>
                    <td class="border border-black p-1.5 text-left">{{ $insp->notes ?: '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-10">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="5">Belum ada rekod pemeriksaan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── PELUPUSAN / HAPUS KIRA — PA-3 standard: Tarikh / Rujukan / Kaedah / Kuantiti / Lokasi / Tandatangan ── --}}
    <div class="font-bold mb-1 uppercase">PELUPUSAN / HAPUS KIRA</div>
    <table class="w-full border-collapse border border-black mb-6 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-[15%]">Tarikh</th>
                <th class="border border-black p-1.5 w-[22%]">Rujukan Kelulusan</th>
                <th class="border border-black p-1.5 w-[18%]">Kaedah Pelupusan</th>
                <th class="border border-black p-1.5 w-[10%]">Kuantiti</th>
                <th class="border border-black p-1.5 w-[18%]">Lokasi</th>
                <th class="border border-black p-1.5 w-[17%]">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @if($asset->status === 'disposed')
                <tr class="h-10">
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($asset->updated_at)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5">{{ $asset->disposal_reference ?? '-' }}</td>
                    <td class="border border-black p-1.5">{{ $asset->disposal_method ?? 'Dilupuskan' }}</td>
                    <td class="border border-black p-1.5">{{ $asset->disposal_quantity ?? $asset->quantity ?? 1 }}</td>
                    <td class="border border-black p-1.5 uppercase">{{ $asset->disposal_location ?? $asset->location ?? '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @else
                <tr class="h-10">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="6">Aset belum dilupuskan.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ── 3-copy instruction ── --}}
    <div class="mt-4 text-[10px] italic border-t border-gray-400 pt-3">
        Nota: Sila sediakan dalam 3 salinan (1-Unit/Makmal; 1-Pejabat Pentadbiran; 1-Pejabat Bendahari)
    </div>

</body>
</html>
