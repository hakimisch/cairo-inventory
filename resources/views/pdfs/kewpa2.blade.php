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
            <div class="font-bold text-[14px]">KEW.PA-2</div>
            <div class="text-[10px]">(No. Siri Pendaftaran : {{ $asset->asset_tag }})</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">DAFTAR HARTA TETAP</div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ :</strong> CAIRO UTM</p>
        <p><strong>Unit/Makmal :</strong> {{ $asset->location }}</p>
    </div>

    <div class="font-bold mb-1 uppercase">BAHAGIAN A</div>

    {{-- ── Bahagian A table — aligned to KEW.PA-2 standard ── --}}
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            {{-- Row 1: Kategori / No. Bar Kod --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50 w-[22%]">Kategori</td>
                <td class="border border-black p-1.5 w-[28%] uppercase">{{ $asset->category }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 w-[22%]">No. Bar Kod</td>
                <td class="border border-black p-1.5 w-[28%] font-mono">{{ $asset->national_code ?? $asset->asset_tag }}</td>
            </tr>
            {{-- Row 2: Jenis Vot / No. Baucer Bayaran + Jumlah --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Jenis Vot</td>
                <td class="border border-black p-1.5 uppercase font-mono">{{ $asset->budget_vot ?? '—' }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">
                    No. Baucer Bayaran :<br>
                    <span class="font-normal">Jumlah :</span>
                </td>
                <td class="border border-black p-1.5">
                    <div class="uppercase font-mono">{{ $asset->voucher_no ?? '—' }}</div>
                    <div class="font-bold mt-1">RM {{ number_format($asset->purchase_price, 2) }}</div>
                </td>
            </tr>
            {{-- Row 3: Sub Jenis/Jenama/Model / Harga Perolehan Asal --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Sub Jenis / Jenama / Model</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->brand }} / {{ $asset->model }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Harga Perolehan Asal</td>
                <td class="border border-black p-1.5 font-bold">RM {{ number_format($asset->purchase_price, 2) }}</td>
            </tr>
            {{-- Row 4: Buatan / Tarikh Diterima --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Buatan</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->brand ?? '—' }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Tarikh Diterima</td>
                <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</td>
            </tr>
            {{-- Row 5: Jenis dan No. Enjin / No. Pesanan Tempatan (rowspan 3) --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Jenis dan No. Enjin</td>
                <td class="border border-black p-1.5">{{ $asset->engine_type_no ?? '—' }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-top" rowspan="3">No. Pesanan Tempatan<br/>Universiti dan Tarikh</td>
                <td class="border border-black p-1.5 align-top" rowspan="3">
                    <div class="font-mono uppercase">{{ $asset->po_reference ?? '—' }}</div>
                    <div class="mt-1">{{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</div>
                </td>
            </tr>
            {{-- Row 6: No. Casis/Siri Pembuat --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">No. Casis / Siri Pembuat</td>
                <td class="border border-black p-1.5 font-mono uppercase">{{ $asset->serial_number ?? '—' }}</td>
            </tr>
            {{-- Row 7: No. Pendaftaran Kenderaan --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">No. Pendaftaran (Bagi Kenderaan)</td>
                <td class="border border-black p-1.5">{{ $asset->vehicle_reg_no ?? '—' }}</td>
            </tr>
            {{-- Row 8: Tempoh Jaminan --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Tempoh Jaminan</td>
                <td class="border border-black p-1.5" colspan="3">
                    {{ $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('d/m/Y') : '—' }}
                </td>
            </tr>
            {{-- Row 9: Kompenon/Aksesori & Nama Pembekal (rowspan 2) --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Kompenon / Aksesori</td>
                <td class="border border-black p-1.5 uppercase">
                    @php
                        $compList = collect($asset->components ?? [])
                            ->map(fn($c) => is_array($c) ? ($c['description'] ?? $c['item'] ?? '') : ($c->description ?? $c->item ?? ''))
                            ->filter()
                            ->implode(', ');
                    @endphp
                    {{ $compList ?: '—' }}
                </td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-top" rowspan="2">Nama Pembekal dan Alamat</td>
                <td class="border border-black p-1.5 align-top uppercase" rowspan="2">
                    <strong>{{ $asset->supplier_name ?? '—' }}</strong><br/>
                    <span class="text-[8px] leading-tight">{{ $asset->supplier_address ?? '' }}</span>
                </td>
            </tr>
            {{-- Row 10: Signature --}}
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-bottom" colspan="2">
                    <div class="mt-10">...................................................................</div>
                    <div class="font-bold mt-1">Tandatangan Pegawai Bertanggungjawab</div>
                    <div class="mt-1">Nama &nbsp;&nbsp;&nbsp;: {{ $asset->custodian_name ?? '.................................' }}</div>
                    <div>Jawatan : .................................</div>
                    <div>Tarikh &nbsp;&nbsp;: {{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</div>
                    <div>Cop &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>
                </td>
            </tr>
        </tbody>
    </table>

    {{-- ── Komponen / Aksesori detail table ── --}}
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
                    <td class="border border-black p-3" colspan="4">
                        <span class="italic text-gray-500 uppercase">Tiada data komponen direkodkan</span>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── PENEMPATAN ── --}}
    <div class="font-bold mb-1 uppercase">PENEMPATAN</div>
    <table class="w-full border-collapse border border-black mb-6 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5">Lokasi</th>
                <th class="border border-black p-1.5 w-24">Tarikh</th>
                <th class="border border-black p-1.5 text-left">Nama Staf</th>
                <th class="border border-black p-1.5 w-24">No. Pekerja</th>
                <th class="border border-black p-1.5 w-24">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->placements as $placement)
                <tr class="h-10">
                    <td class="border border-black p-1.5 uppercase">{{ $placement->location }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($placement->assigned_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $placement->custodian_name }}</td>
                    <td class="border border-black p-1.5">{{ $placement->staff_id ?? '-' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-10">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="5">Tiada rekod penempatan</td>
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

    {{-- ── PELUPUSAN / HAPUS KIRA — standard columns: Rujukan Kelulusan / Tarikh / Kaedah / Tandatangan ── --}}
    <div class="font-bold mb-1 uppercase">PELUPUSAN / HAPUS KIRA</div>
    <table class="w-full border-collapse border border-black mb-8 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-[28%]">Rujukan Kelulusan</th>
                <th class="border border-black p-1.5 w-[15%]">Tarikh</th>
                <th class="border border-black p-1.5 w-[27%]">Kaedah Pelupusan</th>
                <th class="border border-black p-1.5 w-[30%]">Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @if($asset->status === 'disposed')
                <tr class="h-10">
                    <td class="border border-black p-1.5">{{ $asset->disposal_reference ?? '-' }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($asset->updated_at)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5">{{ $asset->disposal_method ?? 'Dilupuskan' }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @else
                <tr class="h-10">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="4">Aset belum dilupuskan.</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- ── Page break before Bahagian B ── --}}
    <div style="page-break-before: always;"></div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">DAFTAR HARTA TETAP</div>

    {{-- ── BAHAGIAN B ── --}}
    <div class="font-bold mb-1 uppercase">BAHAGIAN B : BUTIR-BUTIR PENAMBAHAN, PENGGANTIAN DAN NAIKTARAF</div>
    <table class="w-full border-collapse border border-black text-center text-[10px]">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 w-20">Tarikh</th>
                <th class="border border-black p-1.5 text-left">Butiran</th>
                <th class="border border-black p-1.5 w-20">Tempoh Jaminan</th>
                <th class="border border-black p-1.5 w-20">Kos (RM)</th>
                <th class="border border-black p-1.5 w-32">Nama Peg. B/t/jawab &amp; Tandatangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->upgrades ?? [] as $index => $upgrade)
                <tr class="h-10">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($upgrade->date ?? $upgrade['date'])->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $upgrade->description ?? $upgrade['description'] }}</td>
                    <td class="border border-black p-1.5">{{ $upgrade->warranty_period ?? $upgrade['warranty_period'] ?? '-' }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($upgrade->cost ?? $upgrade['cost'] ?? 0, 2) }}</td>
                    <td class="border border-black p-1.5"></td>
                </tr>
            @empty
                <tr class="h-10">
                    <td class="border border-black p-1.5 italic text-gray-500" colspan="6">Tiada rekod penambahan atau naiktaraf</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ── Copy instruction note ── --}}
    <div class="mt-6 text-[10px] italic border-t border-gray-400 pt-3">
        Nota: Sila sediakan dalam 3 salinan (1-Unit Makmal; 1-Pejabat Pentadbiran; 1-Pejabat Bendahari)
    </div>

</body>
</html>
