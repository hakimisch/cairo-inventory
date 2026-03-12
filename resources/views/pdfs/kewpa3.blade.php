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
<body class="p-10 text-[10px] leading-tight bg-white">
    <div class="text-right font-bold mb-1">KEW.PA-3</div>
    <div class="text-center font-bold text-lg mb-6 underline uppercase tracking-tighter">DAFTAR HARTA MODAL</div>
    
    <div class="flex justify-between mb-4 border-b border-black pb-2">
        <div>
            <p><strong>Kementerian/Jabatan:</strong> CAIRO UTM</p>
            <p><strong>Bahagian:</strong> Research & Robotics</p>
        </div>
        <div class="text-right">
            <p><strong>No. Siri Pendaftaran:</strong> {{ $asset->asset_tag }}</p>
        </div>
    </div>

    <div class="font-bold mb-1">BAHAGIAN A</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 w-1/4 font-bold bg-gray-50 uppercase">Keterangan Aset</td>
                <td colspan="3" class="border border-black p-2 uppercase font-medium">{{ $asset->name }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">Kategori</td>
                <td class="border border-black p-2 w-1/4">{{ $asset->category }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 w-1/4 uppercase">Tarikh Perolehan</td>
                <td class="border border-black p-2">{{ \Carbon\Carbon::parse($asset->received_date ?? $asset->created_at)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase text-indigo-700">Harga Perolehan</td>
                <td class="border border-black p-2 font-bold text-indigo-700 uppercase">RM {{ number_format($asset->purchase_price, 2) }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 uppercase">Lokasi</td>
                <td class="border border-black p-2">{{ $asset->location }}</td>
            </tr>
        </tbody>
    </table>

    <div class="font-bold mb-1 uppercase italic text-gray-600">BAHAGIAN B (Aksesori/Komponen)</div>
    <table class="w-full border-collapse border border-black text-center">
        <thead class="bg-gray-50 font-bold">
            <tr>
                <th class="border border-black p-1 w-8">Bil</th>
                <th class="border border-black p-1">No. Siri Pendaftaran Komponen</th>
                <th class="border border-black p-1">Jenis/Jenama</th>
                <th class="border border-black p-1">Kos (RM)</th>
                <th class="border border-black p-1">Tarikh Pasang</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asset->components ?? [] as $index => $comp)
                <tr>
                    <td class="border border-black p-1">{{ $index + 1 }}</td>
                    <td class="border border-black p-1">{{ $comp['serial_no'] }}</td>
                    <td class="border border-black p-1">{{ $comp['brand'] }}</td>
                    <td class="border border-black p-1 text-right">{{ number_format($comp['cost'], 2) }}</td>
                    <td class="border border-black p-1">{{ $comp['installed_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="border border-black p-4 italic text-gray-400 uppercase">Tiada data komponen didaftarkan</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>