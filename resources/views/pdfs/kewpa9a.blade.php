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
            <div class="font-bold text-[14px]">KEW.PA-9A</div>
        </div>
    </div>

    <div class="text-center font-bold text-[14px] mb-1 uppercase">UNIVERSITI TEKNOLOGI MALAYSIA</div>
    <div class="text-center font-bold text-[13px] mb-6 uppercase">BORANG PINJAMAN ASET</div>

    {{-- Asset Info --}}
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[25%]">No. Siri Pendaftaran</td>
                <td class="border border-black p-2 font-mono uppercase">{{ $asset->asset_tag }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[20%]">Kategori</td>
                <td class="border border-black p-2 uppercase">{{ $asset->category }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Nama Aset</td>
                <td class="border border-black p-2 uppercase" colspan="3">{{ $asset->name }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Jenama / Model</td>
                <td class="border border-black p-2 uppercase" colspan="3">{{ $asset->brand }} / {{ $asset->model }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">No. Siri</td>
                <td class="border border-black p-2 font-mono">{{ $asset->serial_number ?? '—' }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50">Lokasi</td>
                <td class="border border-black p-2 uppercase">{{ $asset->location }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Borrower --}}
    <div class="font-bold mb-2 uppercase">MAKLUMAT PEMINJAM</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[30%]">Nama</td>
                <td class="border border-black p-2 uppercase">{{ $placement->custodian_name ?? '........................................' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">No. Staf / Matrik</td>
                <td class="border border-black p-2">{{ $placement->matric_no ?? '........................................' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">No. Tel. Bimbit</td>
                <td class="border border-black p-2">{{ $placement->borrower_phone ?? '........................................' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">PTJ / Unit</td>
                <td class="border border-black p-2 uppercase">CAIRO UTM</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Tujuan Pinjaman</td>
                <td class="border border-black p-2">{{ $placement->purpose ?? '................................................................................' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Loan Period --}}
    <div class="font-bold mb-2 uppercase">TEMPOH PINJAMAN</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[25%]">Tarikh Mula</td>
                <td class="border border-black p-2 w-[25%]">
                    {{ $placement && $placement->assigned_date ? \Carbon\Carbon::parse($placement->assigned_date)->format('d/m/Y') : '................................' }}
                </td>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[25%]">Tarikh Pulang</td>
                <td class="border border-black p-2 w-[25%]">
                    {{ $placement && $placement->expected_return_date ? \Carbon\Carbon::parse($placement->expected_return_date)->format('d/m/Y') : '................................' }}
                </td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Kuantiti</td>
                <td class="border border-black p-2">{{ $placement->quantity_placed ?? 1 }}</td>
                <td class="border border-black p-2 font-bold bg-gray-50">Lokasi Pinjaman</td>
                <td class="border border-black p-2 uppercase">{{ $placement->location ?? $asset->location }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Authorizer --}}
    <div class="font-bold mb-2 uppercase">MAKLUMAT PENYERAH / PEMBERI PINJAM</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[30%]">Nama</td>
                <td class="border border-black p-2 uppercase">{{ $placement->authorizer_name ?? '........................................' }}</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Jawatan</td>
                <td class="border border-black p-2">........................................</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Tandatangan</td>
                <td class="border border-black p-2">........................................</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Tarikh</td>
                <td class="border border-black p-2">........................................</td>
            </tr>
        </tbody>
    </table>

    {{-- Return Confirmation --}}
    <div class="font-bold mb-2 uppercase">PENGESAHAN PEMULANGAN</div>
    <table class="w-full border-collapse border border-black mb-6">
        <tbody>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50 w-[30%]">Tarikh Pulang</td>
                <td class="border border-black p-2">
                    {{ $placement && $placement->returned_date ? \Carbon\Carbon::parse($placement->returned_date)->format('d/m/Y') : '........................................' }}
                </td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Status Aset</td>
                <td class="border border-black p-2 uppercase">........................................</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Tandatangan Penerima</td>
                <td class="border border-black p-2">........................................</td>
            </tr>
            <tr>
                <td class="border border-black p-2 font-bold bg-gray-50">Tarikh</td>
                <td class="border border-black p-2">........................................</td>
            </tr>
        </tbody>
    </table>

    <div class="mt-6 text-[10px] italic border-t border-gray-400 pt-3">
        Nota: Borang ini hendaklah diisi dalam 2 salinan (1-Peminjam; 1-Pejabat PTJ)
    </div>

</body>
</html>
