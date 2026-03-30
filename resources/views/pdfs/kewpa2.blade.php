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
    <div class="text-right font-bold mb-1">KEW.PA-2</div>
    <div class="mb-4">
        <p>No. Rujukan Permohonan: <span class="font-bold">{{ $asset->asset_tag }}</span></p>
    </div>

    <div class="text-center font-bold text-lg mb-6 leading-tight">
        UNIVERSITI TEKNOLOGI MALAYSIA<br/>
        DAFTAR HARTA TETAP
    </div>

    <div class="mb-4 space-y-1">
        <p><strong>Fakulti/PTJ:</strong> CAIRO UTM</p>
        <p><strong>Unit/Makmal:</strong> {{ $asset->location }}</p>
    </div>

    <div class="font-bold mb-1 uppercase">BAHAGIAN A</div>
    
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50 w-[22%]">Kategori</td>
                <td class="border border-black p-1.5 w-[28%] uppercase">{{ $asset->category }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 w-[22%]">No Bar Kod</td>
                <td class="border border-black p-1.5 w-[28%] font-mono">{{ $asset->national_code ?? $asset->asset_tag }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Nama Alat</td>
                <td class="border border-black p-1.5 uppercase font-bold">{{ $asset->name }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Saga</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->saga_id ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Jenis</td>
                <td class="border border-black p-1.5 font-bold uppercase">HARTA TETAP</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">No Baucer Bayaran</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->voucher_no ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Sub Jenis/Jenama/Model</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->brand }} / {{ $asset->model }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Bajet</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->budget_vot ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Buatan</td>
                <td class="border border-black p-1.5 uppercase">{{ $asset->brand ?? '—' }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Harga Perolehan Asal</td>
                <td class="border border-black p-1.5 font-bold">RM {{ number_format($asset->purchase_price, 2) }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Jenis dan No Enjin</td>
                <td class="border border-black p-1.5">—</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Tarikh Diterima</td>
                <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Casis</td>
                <td class="border border-black p-1.5">—</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-top" rowspan="3">No. Pesanan Tempatan<br/>Universiti dan Tarikh</td>
                <td class="border border-black p-1.5 align-top" rowspan="3">
                    {{ $asset->po_reference }}<br/>
                    {{ \Carbon\Carbon::parse($asset->created_at)->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Siri Buatan</td>
                <td class="border border-black p-1.5 font-mono">{{ $asset->serial_number ?? '—' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Pendaftaran Kenderaan</td>
                <td class="border border-black p-1.5">—</td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Peralatan Diterima</td>
                <td class="border border-black p-1.5">SET</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Tempoh Jaminan</td>
                <td class="border border-black p-1.5">
                    {{ $asset->warranty_expiry ? \Carbon\Carbon::parse($asset->warranty_expiry)->format('d/m/Y') : '—' }}
                </td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Penyelenggaraan</td>
                <td class="border border-black p-1.5 font-bold">{{ $asset->requires_maintenance ? 'YA' : 'TIDAK' }}</td>
                <td class="border border-black p-1.5 font-bold bg-gray-50 align-top" rowspan="2">Pembekal</td>
                <td class="border border-black p-1.5 align-top uppercase" rowspan="2">
                    <strong>{{ $asset->supplier_name }}</strong><br/>
                    <span class="text-[8px] leading-tight">{{ $asset->supplier_address }}</span>
                </td>
            </tr>
            <tr>
                <td class="border border-black p-1.5 font-bold bg-gray-50">Status Alat</td>
                <td class="border border-black p-1.5 font-bold">{{ $asset->status === 'active' ? 'ASET BARU' : 'LAMA' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="font-bold mb-1 uppercase">KOMPONEN / AKSESORI</div>
    <table class="w-full border-collapse border border-black mb-8 text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-8">Bil</th>
                <th class="border border-black p-1.5 text-left">Keterangan</th>
                <th class="border border-black p-1.5 w-32">Harga (RM)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->components ?? [] as $index => $comp)
                <tr>
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $comp['description'] ?? $comp['brand'] }}</td>
                    <td class="border border-black p-1.5 text-right">{{ number_format($comp['cost'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td class="border border-black p-3">1</td>
                    <td class="border border-black p-3 italic text-gray-500 uppercase">TIADA DATA KOMPONEN DIREKODKAN</td>
                    <td class="border border-black p-3">0.00</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="w-1/2 mb-8">
        <p class="font-bold mb-10">Tandatangan Pegawai Bertanggungjawab</p>
        <p>Nama: {{ $asset->custodian_name ?? 'BELUM DITETAPKAN' }}</p>
        <p>Jawatan: PEGAWAI PENYELIDIK CAIRO</p>
        <p>Tarikh: {{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</p>
        <p>Cop:</p>
    </div>

    <div class="font-bold mb-1 uppercase">PENEMPATAN</div>
    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1.5 w-12">BIL</th>
                <th class="border border-black p-1.5 w-24">TARIKH</th>
                <th class="border border-black p-1.5 text-left">LOKASI</th>
                <th class="border border-black p-1.5 text-left">NAMA PEGAWAI</th>
                <th class="border border-black p-1.5 w-32">T/TANGAN</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->placements as $index => $placement)
                <tr class="h-10">
                    <td class="border border-black p-1.5">{{ $index + 1 }}</td>
                    <td class="border border-black p-1.5">{{ \Carbon\Carbon::parse($placement->assigned_date)->format('d/m/Y') }}</td>
                    <td class="border border-black p-1.5 text-left uppercase">
                        {{ $placement->location }} 
                        @if($placement->is_lokasi_luar) <span class="text-[8px] italic">(Luar)</span> @endif
                    </td>
                    <td class="border border-black p-1.5 text-left uppercase">{{ $placement->custodian_name }}</td>
                    <td class="border border-black p-1.5"></td> </tr>
            @empty
                <tr class="h-10">
                    <td class="border border-black p-1.5" colspan="5">Tiada rekod penempatan</td>
                </tr>
            @endforelse
            
            @if($asset->placements->where('is_lokasi_luar', true)->isEmpty())
            <tr class="bg-gray-50">
                <td class="border border-black p-1.5 font-bold text-right" colspan="2">LOKASI LUAR</td>
                <td class="border border-black p-1.5 text-left italic text-gray-500" colspan="3">Tiada rekod penempatan luar</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>