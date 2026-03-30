import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

// ── Shared Bahagian A table used by both PA-2 (Harta Tetap) and PA-3 (Inventori) ──
function BahagianA({ asset, jenisLabel }) {
    return (
        <table className="w-full border-collapse border border-black mb-6">
            <tbody>
                {/* Row 1 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50 w-[22%]">Kategori</td>
                    <td className="border border-black p-2 w-[28%] uppercase">{asset.category}</td>
                    <td className="border border-black p-2 font-bold bg-gray-50 w-[22%]">No Bar Kod</td>
                    <td className="border border-black p-2 w-[28%] font-mono">
                        {asset.national_code || asset.asset_tag}
                    </td>
                </tr>
                {/* Row 2 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Nama Alat</td>
                    <td className="border border-black p-2 uppercase font-bold">{asset.name}</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Saga</td>
                    <td className="border border-black p-2 font-mono uppercase">{asset.saga_id || '—'}</td>
                </tr>
                {/* Row 3 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Jenis</td>
                    <td className="border border-black p-2 font-bold uppercase">{jenisLabel}</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">No Baucer Bayaran</td>
                    <td className="border border-black p-2 uppercase">{asset.voucher_no || '—'}</td>
                </tr>
                {/* Row 4 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Sub Jenis/Jenama/Model</td>
                    <td className="border border-black p-2 uppercase">
                        {[asset.brand, asset.model].filter(Boolean).join(' / ') || '—'}
                    </td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Bajet</td>
                    <td className="border border-black p-2 uppercase">{asset.budget_vot || '—'}</td>
                </tr>
                {/* Row 5 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Buatan</td>
                    <td className="border border-black p-2 uppercase">{asset.brand || '—'}</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Harga Perolehan Asal</td>
                    <td className="border border-black p-2 font-bold">
                        RM {Number(asset.purchase_price).toLocaleString('ms-MY', { minimumFractionDigits: 2 })}
                    </td>
                </tr>
                {/* Row 6 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Jenis dan No Enjin</td>
                    <td className="border border-black p-2">—</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Tarikh Diterima</td>
                    <td className="border border-black p-2">
                        {new Date(asset.received_date || asset.created_at).toLocaleDateString('ms-MY')}
                    </td>
                </tr>
                {/* Row 7 — No Casis + PO (rowspan 3) */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">No Casis</td>
                    <td className="border border-black p-2">—</td>
                    <td className="border border-black p-2 font-bold bg-gray-50 align-top" rowSpan="3">
                        No. Pesanan Tempatan<br />Universiti dan Tarikh
                    </td>
                    <td className="border border-black p-2 align-top font-mono" rowSpan="3">
                        {asset.po_reference || '—'}
                    </td>
                </tr>
                {/* Row 8 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Siri Buatan</td>
                    <td className="border border-black p-2 font-mono">{asset.serial_number || '—'}</td>
                </tr>
                {/* Row 9 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">No Pendaftaran Kenderaan</td>
                    <td className="border border-black p-2">—</td>
                </tr>
                {/* Row 10 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Peralatan Diterima</td>
                    <td className="border border-black p-2">SET</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Tempoh Jaminan</td>
                    <td className="border border-black p-2">
                        {asset.warranty_period ? `${asset.warranty_period} tahun` : '—'}
                    </td>
                </tr>
                {/* Row 11 — Penyelenggaraan + Pembekal (rowspan 2) */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Penyelenggaraan</td>
                    <td className="border border-black p-2 font-bold">
                        {asset.requires_maintenance ? 'YA' : 'TIDAK'}
                    </td>
                    <td className="border border-black p-2 font-bold bg-gray-50 align-top" rowSpan="2">Pembekal</td>
                    <td className="border border-black p-2 align-top uppercase" rowSpan="2">
                        <strong>{asset.supplier_name || '—'}</strong><br />
                        <span className="text-[9px]">{asset.supplier_address || ''}</span>
                    </td>
                </tr>
                {/* Row 12 */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Status Alat</td>
                    <td className="border border-black p-2 font-bold">
                        {asset.status === 'active' ? 'ASET BARU' : 'LAMA'}
                    </td>
                </tr>
            </tbody>
        </table>
    );
}

// ── Komponen / Aksesori table ──────────────────────────────────────────────────
function KomponenTable({ asset }) {
    return (
        <>
            <div className="font-bold mb-1 uppercase">KOMPONEN / AKSESORI</div>
            <table className="w-full border-collapse border border-black mb-0">
                <thead>
                    <tr className="bg-gray-50 font-bold text-[10px]">
                        <th className="border border-black p-2 w-8 text-center">Bil</th>
                        <th className="border border-black p-2 text-left w-1/2">Keterangan</th>
                        <th className="border border-black p-2 w-24 text-right">Harga (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    {asset.components && asset.components.length > 0 ? (
                        asset.components.map((comp, i) => (
                            <tr key={i}>
                                <td className="border border-black p-2 text-center">{i + 1}</td>
                                <td className="border border-black p-2 uppercase">{comp.description || comp.brand || '—'}</td>
                                <td className="border border-black p-2 text-right">
                                    {comp.cost ? Number(comp.cost).toLocaleString('ms-MY', { minimumFractionDigits: 2 }) : '0.00'}
                                </td>
                            </tr>
                        ))
                    ) : (
                        <tr className="h-12">
                            <td className="border border-black p-2 text-center">1</td>
                            <td className="border border-black p-2 italic text-gray-400 uppercase">—</td>
                            <td className="border border-black p-2 text-right">0</td>
                        </tr>
                    )}
                </tbody>
            </table>
        </>
    );
}

// ── Signature + Penempatan ─────────────────────────────────────────────────────
function SignatureAndPenempatan({ asset, data, setData, processing, handleAddPlacement }) {
    return (
        <>
            {/* 2-column: Komponen description + Signature */}
            <table className="w-full border-collapse border-t-0 border border-black mb-6">
                <tbody>
                    <tr>
                        <td className="border border-black p-3 w-1/2 align-top text-[10px]">
                            {/* empty — komponen keterangan cell */}
                        </td>
                        <td className="border border-black p-3 w-1/2 align-top">
                            <p className="mb-8">...........................................................................</p>
                            <p className="font-bold">Tandatangan Pegawai Bertanggungjawab</p>
                            <p className="mt-2">Nama : <span className="font-bold uppercase">{asset.custodian_name || '—'}</span></p>
                            <p>Jawatan : PEGAWAI PENYELIDIK CAIRO</p>
                            <p>Tarikh : {new Date(asset.received_date || asset.created_at).toLocaleDateString('ms-MY')}</p>
                            <p>Cop :</p>
                        </td>
                    </tr>
                </tbody>
            </table>

            {/* PENEMPATAN */}
            <div className="font-bold mb-1 text-center uppercase tracking-widest">PENEMPATAN</div>
            <table className="w-full border-collapse border border-black text-center text-[10px]">
                <thead className="bg-gray-50 font-bold">
                    <tr>
                        <th className="border border-black p-2 w-8">BIL</th>
                        <th className="border border-black p-2 w-20">TARIKH</th>
                        <th className="border border-black p-2 text-left">LOKASI</th>
                        <th className="border border-black p-2 text-left">NAMA PEGAWAI</th>
                        <th className="border border-black p-2 w-28">T/TANGAN</th>
                    </tr>
                </thead>
                <tbody>
                    {asset.placements && asset.placements.length > 0 ? (
                        <>
                            {asset.placements.map((p, i) => (
                                <tr key={p.id} className="h-10">
                                    <td className="border border-black p-1.5">{i + 1}</td>
                                    <td className="border border-black p-1.5">
                                        {new Date(p.assigned_date).toLocaleDateString('ms-MY')}
                                    </td>
                                    <td className="border border-black p-1.5 text-left uppercase">{p.location}</td>
                                    <td className="border border-black p-1.5 text-left uppercase">{p.custodian_name}</td>
                                    <td className="border border-black p-1.5"></td>
                                </tr>
                            ))}
                            {/* Lokasi Luar row — shows if any placement is marked luar */}
                            {asset.placements.some(p => p.is_lokasi_luar) && (
                                <tr>
                                    <td className="border border-black p-1.5 font-bold" colSpan="2">LOKASI LUAR</td>
                                    <td className="border border-black p-1.5 uppercase text-left" colSpan="3">
                                        {asset.placements.find(p => p.is_lokasi_luar)?.location || '—'}
                                    </td>
                                </tr>
                            )}
                        </>
                    ) : (
                        <tr className="h-10">
                            <td className="border border-black p-1.5" colSpan="5">
                                Tiada rekod penempatan
                            </td>
                        </tr>
                    )}
                </tbody>
            </table>

            {/* Add Placement form — hidden on print */}
            <div className="mt-5 p-4 bg-gray-50 border border-gray-200 rounded print:hidden">
                <p className="font-bold text-xs uppercase mb-3" style={{ color: '#5C001F' }}>
                    Tambah Rekod Penempatan / Pinjaman
                </p>
                <form onSubmit={handleAddPlacement} className="flex flex-wrap gap-3 items-end">
                    <div className="flex-1 min-w-40">
                        <label className="block text-[10px] font-bold text-gray-600 mb-1">
                            Nama Pegawai Bertanggungjawab
                        </label>
                        <input
                            type="text" required
                            value={data.custodian_name}
                            onChange={e => setData('custodian_name', e.target.value)}
                            className="w-full text-xs p-2 border border-gray-300 rounded"
                            placeholder="Nama Pegawai"
                        />
                    </div>
                    <div className="flex-1 min-w-40">
                        <label className="block text-[10px] font-bold text-gray-600 mb-1">Lokasi Baru</label>
                        <input
                            type="text" required
                            value={data.location}
                            onChange={e => setData('location', e.target.value)}
                            className="w-full text-xs p-2 border border-gray-300 rounded"
                            placeholder="Bilik / Makmal"
                        />
                    </div>
                    <div>
                        <label className="block text-[10px] font-bold text-gray-600 mb-1">Tarikh</label>
                        <input
                            type="date" required
                            value={data.assigned_date}
                            onChange={e => setData('assigned_date', e.target.value)}
                            className="text-xs p-2 border border-gray-300 rounded"
                        />
                    </div>
                    <div className="flex items-center gap-2 mb-1">
                        <input
                            type="checkbox" id="luar"
                            checked={data.is_lokasi_luar}
                            onChange={e => setData('is_lokasi_luar', e.target.checked)}
                        />
                        <label htmlFor="luar" className="text-[10px] font-bold text-gray-600">Lokasi Luar</label>
                    </div>
                    <button
                        type="submit" disabled={processing}
                        className="px-4 py-2 rounded text-xs font-bold text-white disabled:opacity-50"
                        style={{ background: '#5C001F' }}
                    >
                        {processing ? 'Menyimpan...' : 'Tambah Rekod'}
                    </button>
                </form>
            </div>
        </>
    );
}

// ── KEW.PA-2 — Daftar Harta Tetap ─────────────────────────────────────────────
export default function Kewpa2({ asset }) {
    const { data, setData, post, processing, reset } = useForm({
        custodian_name : '',
        location       : '',
        is_lokasi_luar : false,
        assigned_date  : new Date().toISOString().split('T')[0],
    });

    const handleAddPlacement = (e) => {
        e.preventDefault();
        post(route('assets.placements.store', asset.id), { onSuccess: () => reset() });
    };

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-2 — Daftar Harta Tetap
            </h2>
        }>
            <Head title={`KEW.PA-2 — ${asset.asset_tag}`} />

            <style>{`
                @media print { .print\\:hidden { display: none !important; } body { margin: 0; } }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black">

                    {/* Header */}
                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[12px]">KEW.PA-2 DERAF</div>
                            <div className="text-[10px]">No. Rujukan Permohonan : {asset.asset_tag}</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[13px] mb-4">
                        UNIVERSITI TEKNOLOGI MALAYSIA<br />
                        DAFTAR HARTA TETAP
                    </div>

                    <div className="mb-4 space-y-0.5">
                        <p><strong>Fakulti/PTJ</strong>&nbsp;&nbsp; CAIRO UTM</p>
                        <p><strong>Unit/Makmal</strong>&nbsp; {asset.location}</p>
                    </div>

                    <div className="font-bold mb-1">BAHAGIAN A</div>
                    <BahagianA asset={asset} jenisLabel="HARTA TETAP" />

                    <KomponenTable asset={asset} />
                    <SignatureAndPenempatan
                        asset={asset}
                        data={data} setData={setData}
                        processing={processing}
                        handleAddPlacement={handleAddPlacement}
                    />

                    {/* Actions */}
                    <div className="mt-8 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('assets.kewpa2.download', asset.id)}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-2)
                        </a>
                        <button
                            onClick={() => window.print()}
                            className="border border-gray-300 px-6 py-2 rounded font-bold hover:bg-gray-50"
                        >
                            Cetak Skrin
                        </button>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}