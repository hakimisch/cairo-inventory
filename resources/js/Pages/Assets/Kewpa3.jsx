import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, router } from '@inertiajs/react';

// ── Bahagian A — KEW.PA-3 (Inventori) ────────────────────────────────────────
// PA-3 Bahagian A is simpler and inventory-specific.
// Key differences from PA-2: has Kuantiti + Unit Pengukuran, no engine/chassis/vehicle fields.
function BahagianA({ asset }) {
    return (
        <table className="w-full border-collapse border border-black mb-6">
            <tbody>
                {/* Row 1: Kategori / No. Bar Kod */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50 w-[22%]">Kategori</td>
                    <td className="border border-black p-2 w-[28%] uppercase">{asset.category}</td>
                    <td className="border border-black p-2 font-bold bg-gray-50 w-[22%]">No. Bar Kod</td>
                    <td className="border border-black p-2 w-[28%] font-mono">
                        {asset.national_code || asset.asset_tag}
                    </td>
                </tr>
                {/* Row 2: Jenis / No. Baucer Bayaran */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Jenis</td>
                    <td className="border border-black p-2 uppercase font-bold">INVENTORI</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">No. Baucer Bayaran</td>
                    <td className="border border-black p-2 uppercase font-mono">{asset.voucher_no || '—'}</td>
                </tr>
                {/* Row 3: Sub Jenis / Sub Jenis right column */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Sub Jenis</td>
                    <td className="border border-black p-2 uppercase">{asset.sub_type || '—'}</td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Jenis Vot</td>
                    <td className="border border-black p-2 uppercase font-mono">{asset.budget_vot || '—'}</td>
                </tr>
                {/* Row 4: Kuantiti / Unit Pengukuran — inventory-specific */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Kuantiti</td>
                    <td className="border border-black p-2 font-bold text-center">
                        {asset.quantity ?? 1}
                    </td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Unit Pengukuran</td>
                    <td className="border border-black p-2 uppercase">
                        {asset.unit_of_measure || 'Unit'}
                    </td>
                </tr>
                {/* Row 5: Harga Perolehan Asal / Tarikh Diterima */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Harga Perolehan Asal</td>
                    <td className="border border-black p-2 font-bold">
                        RM {Number(asset.purchase_price).toLocaleString('ms-MY', { minimumFractionDigits: 2 })}
                    </td>
                    <td className="border border-black p-2 font-bold bg-gray-50">Tarikh Diterima</td>
                    <td className="border border-black p-2">
                        {new Date(asset.received_date ?? asset.created_at).toLocaleDateString('ms-MY')}
                    </td>
                </tr>
                {/* Row 6: Tempoh Jaminan / No. Pesanan Tempatan (rowspan 2) */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50">Tempoh Jaminan</td>
                    <td className="border border-black p-2">
                        {asset.warranty_expiry
                            ? new Date(asset.warranty_expiry).toLocaleDateString('ms-MY')
                            : '—'}
                    </td>
                    <td className="border border-black p-2 font-bold bg-gray-50 align-top" rowSpan="2">
                        No. Pesanan Tempatan<br/>Universiti dan Tarikh
                    </td>
                    <td className="border border-black p-2 align-top" rowSpan="2">
                        <div className="font-mono uppercase">{asset.po_reference || '—'}</div>
                        <div className="mt-1">
                            {new Date(asset.received_date ?? asset.created_at).toLocaleDateString('ms-MY')}
                        </div>
                    </td>
                </tr>
                {/* Row 7: Nama Pembekal */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50 align-top">Nama Pembekal dan Alamat</td>
                    <td className="border border-black p-2 align-top uppercase">
                        <div className="font-bold">{asset.supplier_name || '—'}</div>
                        <div className="text-[10px] text-gray-700 whitespace-pre-wrap mt-1">
                            {asset.supplier_address || ''}
                        </div>
                    </td>
                </tr>
                {/* Row 8: Signature block */}
                <tr>
                    <td className="border border-black p-2 font-bold bg-gray-50 align-bottom" colSpan="4">
                        <div className="grid grid-cols-2 gap-8 mt-6">
                            <div>
                                <div className="text-[10px]">.......................................................................</div>
                                <div className="font-bold mt-1">Tandatangan Pegawai Bertanggungjawab</div>
                                <div className="mt-1">Nama &nbsp;&nbsp;&nbsp;: {asset.custodian_name || '...................................'}</div>
                                <div>Jawatan : ....................................</div>
                                <div>Tarikh &nbsp;&nbsp;: {new Date(asset.received_date ?? asset.created_at).toLocaleDateString('ms-MY')}</div>
                                <div>Cop &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    );
}

// ── Komponen / Aksesori ───────────────────────────────────────────────────────
function KomponenTable({ asset }) {
    const components = asset.components || [];
    return (
        <div className="mb-6">
            <div className="font-bold mb-1">KOMPONEN / AKSESORI</div>
            <table className="w-full border-collapse border border-black text-center">
                <thead className="bg-gray-50">
                    <tr>
                        <th className="border border-black p-2 w-12">Bil</th>
                        <th className="border border-black p-2 text-left">Butiran</th>
                        <th className="border border-black p-2 w-20">Kuantiti</th>
                        <th className="border border-black p-2 w-24">Harga (RM)</th>
                        <th className="border border-black p-2 w-28">Siri Komponen</th>
                        <th className="border border-black p-2 w-24">Tahun Tambah</th>
                    </tr>
                </thead>
                <tbody>
                    {components.length === 0 ? (
                        <tr>
                            <td colSpan="6" className="border border-black p-4 text-gray-400 italic">
                                Tiada komponen / aksesori direkodkan.
                            </td>
                        </tr>
                    ) : (
                        components.map((c, i) => (
                            <tr key={i}>
                                <td className="border border-black p-2">{i + 1}</td>
                                <td className="border border-black p-2 text-left uppercase">{c.item || c.description}</td>
                                <td className="border border-black p-2">{c.qty ?? 1}</td>
                                <td className="border border-black p-2">{Number(c.price ?? c.cost ?? 0).toFixed(2)}</td>
                                <td className="border border-black p-2">{c.serial_no || '-'}</td>
                                <td className="border border-black p-2">{c.year_added || '-'}</td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}

// ── Penempatan — PA-3 requires Kuantiti + No. Siri Pendaftaran columns ────────
function PenempatanTable({ asset, data, setData, processing, handleAddPlacement }) {
    const placements = asset.placements || [];
    return (
        <div className="border border-black mt-6">
            <div className="bg-gray-50 font-bold p-2 border-b border-black text-center">PENEMPATAN</div>
            <table className="w-full text-center text-[10px]">
                <thead>
                    <tr className="border-b border-black">
                        <th className="p-2 border-r border-black w-12">Kuantiti</th>
                        <th className="p-2 border-r border-black w-28">No. Siri Pendaftaran</th>
                        <th className="p-2 border-r border-black">Lokasi</th>
                        <th className="p-2 border-r border-black w-20">Tarikh</th>
                        <th className="p-2 border-r border-black">Nama Staf</th>
                        <th className="p-2 border-r border-black w-20">No. Pekerja</th>
                        <th className="p-2 w-20">Tandatangan</th>
                    </tr>
                </thead>
                <tbody>
                    {placements.length === 0 ? (
                        <tr>
                            <td colSpan="7" className="p-4 italic text-gray-500 border-b border-black">Belum ditempatkan</td>
                        </tr>
                    ) : (
                        placements.map(p => (
                            <tr key={p.id} className="border-b border-black">
                                <td className="p-2 border-r border-black">{p.quantity_placed || 1}</td>
                                <td className="p-2 border-r border-black font-mono">{p.specific_serial_no || '-'}</td>
                                <td className="p-2 border-r border-black uppercase">{p.location}</td>
                                <td className="p-2 border-r border-black">{new Date(p.assigned_date).toLocaleDateString('ms-MY')}</td>
                                <td className="p-2 border-r border-black">{p.custodian_name}</td>
                                <td className="p-2 border-r border-black">{p.staff_id || '-'}</td>
                                <td className="p-2"></td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
            {/* Add placement form — hidden on print */}
            <div className="p-3 bg-yellow-50 mt-auto border-t border-black print:hidden">
                <p className="font-bold mb-2 text-yellow-900">Kemaskini Penempatan Baru</p>
                <form onSubmit={handleAddPlacement} className="flex flex-col gap-2">
                    <input
                        type="text" placeholder="Nama Pegawai"
                        value={data.custodian_name} onChange={e => setData('custodian_name', e.target.value)}
                        className="text-[10px] p-1 border rounded" required
                    />
                    <input
                        type="text" placeholder="Lokasi (Bilik/Makmal)"
                        value={data.location} onChange={e => setData('location', e.target.value)}
                        className="text-[10px] p-1 border rounded" required
                    />
                    <div className="flex items-center gap-2">
                        <label className="text-[10px] flex items-center gap-1 cursor-pointer">
                            <input type="checkbox" checked={data.is_lokasi_luar} onChange={e => setData('is_lokasi_luar', e.target.checked)} />
                            Lokasi Pinjaman (Luar)
                        </label>
                        {data.is_lokasi_luar && (
                            <input
                                type="date"
                                value={data.expected_return_date} onChange={e => setData('expected_return_date', e.target.value)}
                                className="text-[10px] p-1 border rounded flex-1" required
                            />
                        )}
                    </div>
                    <button type="submit" disabled={processing} className="bg-yellow-600 text-white text-[10px] font-bold py-1 rounded hover:bg-yellow-700">
                        {processing ? 'Menyimpan...' : 'Simpan Penempatan'}
                    </button>
                </form>
            </div>
        </div>
    );
}

// ── Pemeriksaan — includes Tandatangan column ─────────────────────────────────
function PemeriksaanTable({ asset, data, setData, post, processing }) {
    const inspections = asset.inspections || [];

    const handleAddInspection = (e) => {
        e.preventDefault();
        post(route('assets.inspections.store', asset.id), {
            preserveScroll: true,
            onSuccess: () => setData('notes', ''),
        });
    };

    return (
        <div className="border border-black mt-6">
            <div className="bg-gray-50 font-bold p-2 border-b border-black text-center">PEMERIKSAAN</div>
            <table className="w-full text-center text-[10px]">
                <thead className="bg-gray-50">
                    <tr className="border-b border-black">
                        <th className="p-2 border-r border-black w-[15%]">Tarikh</th>
                        <th className="p-2 border-r border-black w-[22%]">Status Aset</th>
                        <th className="p-2 border-r border-black w-[28%]">Nama Pemeriksa</th>
                        <th className="p-2 border-r border-black">Catatan</th>
                        <th className="p-2 w-[15%]">Tandatangan</th>
                    </tr>
                </thead>
                <tbody>
                    {inspections.length === 0 ? (
                        <tr><td colSpan="5" className="p-4 italic text-gray-500 border-b border-black">Belum ada rekod pemeriksaan.</td></tr>
                    ) : (
                        inspections.map(insp => (
                            <tr key={insp.id} className="border-b border-black">
                                <td className="p-2 border-r border-black">{new Date(insp.inspection_date).toLocaleDateString('ms-MY')}</td>
                                <td className="p-2 border-r border-black uppercase">{insp.status}</td>
                                <td className="p-2 border-r border-black">{insp.inspector_name}</td>
                                <td className="p-2 border-r border-black text-left">{insp.notes || '-'}</td>
                                <td className="p-2"></td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
            <div className="p-3 bg-blue-50 border-t border-black print:hidden">
                <p className="font-bold mb-2 text-blue-900">Tambah Rekod Pemeriksaan Tahunan</p>
                <form onSubmit={handleAddInspection} className="flex gap-2 items-center">
                    <input type="date" required className="text-[10px] p-1 border rounded" value={data.inspection_date} onChange={e => setData('inspection_date', e.target.value)} />
                    <select required className="text-[10px] p-1 border rounded" value={data.status} onChange={e => setData('status', e.target.value)}>
                        <option value="Sedang Digunakan">Sedang Digunakan</option>
                        <option value="Tidak Digunakan">Tidak Digunakan</option>
                        <option value="Rosak">Rosak</option>
                    </select>
                    <input type="text" placeholder="Nama Pemeriksa" className="text-[10px] p-1 border rounded flex-1" value={data.inspector_name} onChange={e => setData('inspector_name', e.target.value)} required />
                    <input type="text" placeholder="Catatan (Pilihan)" className="text-[10px] p-1 border rounded flex-1" value={data.notes} onChange={e => setData('notes', e.target.value)} />
                    <button type="submit" disabled={processing} className="bg-blue-600 text-white text-[10px] font-bold py-1 px-3 rounded hover:bg-blue-700">
                        {processing ? 'Menyimpan...' : 'Simpan'}
                    </button>
                </form>
            </div>
        </div>
    );
}

// ── Pelupusan / Hapus Kira — PA-3 requires Kuantiti + Lokasi columns ──────────
function PelupusanTable({ asset }) {
    const disposals = asset.disposals || [];
    return (
        <div className="border border-black mt-6">
            <div className="bg-gray-50 font-bold p-2 border-b border-black text-center">PELUPUSAN / HAPUS KIRA</div>
            <table className="w-full text-center text-[10px]">
                <thead className="bg-gray-50">
                    <tr className="border-b border-black">
                        <th className="p-2 border-r border-black w-[15%]">Tarikh</th>
                        <th className="p-2 border-r border-black w-[25%]">Rujukan Kelulusan</th>
                        <th className="p-2 border-r border-black w-[18%]">Kaedah Pelupusan</th>
                        <th className="p-2 border-r border-black w-[10%]">Kuantiti</th>
                        <th className="p-2 border-r border-black w-[17%]">Lokasi</th>
                        <th className="p-2 w-[15%]">Tandatangan</th>
                    </tr>
                </thead>
                <tbody>
                    {disposals.length === 0 ? (
                        <tr><td colSpan="6" className="p-4 italic text-gray-500">Aset belum dilupuskan.</td></tr>
                    ) : (
                        disposals.map(d => (
                            <tr key={d.id}>
                                <td className="p-2 border-r border-black">{new Date(d.disposal_date ?? d.created_at).toLocaleDateString('ms-MY')}</td>
                                <td className="p-2 border-r border-black">{d.approval_reference || '-'}</td>
                                <td className="p-2 border-r border-black">{d.disposal_method || 'Dilupuskan'}</td>
                                <td className="p-2 border-r border-black">{d.quantity ?? asset.quantity ?? 1}</td>
                                <td className="p-2 border-r border-black">{d.disposal_location || asset.location || '-'}</td>
                                <td className="p-2"></td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
        </div>
    );
}

// ── Penyelenggaraan — PA-13/14 ─────────────────────────────────────────────────
function MaintenanceTable({ asset }) {
    const [form, setForm] = useState({
        maintenance_date: new Date().toISOString().split('T')[0],
        description: '',
        contract_no: '',
        company_name: '',
        cost: '',
    });
    const [showForm, setShowForm] = useState(false);
    const maintenances = asset.maintenances || [];

    const handleSubmit = (e) => {
        e.preventDefault();
        router.post(route('assets.maintenances.store', asset.id), form, {
            preserveScroll: true,
            onSuccess: () => { setForm({ maintenance_date: new Date().toISOString().split('T')[0], description: '', contract_no: '', company_name: '', cost: '' }); setShowForm(false); }
        });
    };

    return (
        <div className="border border-black mt-6">
            <div className="bg-gray-50 font-bold p-2 border-b border-black text-center">PENYELENGGARAAN (KEW.PA-13/14)</div>
            <table className="w-full text-center text-[10px]">
                <thead className="bg-gray-50">
                    <tr className="border-b border-black">
                        <th className="p-2 border-r border-black w-[13%]">Tarikh</th>
                        <th className="p-2 border-r border-black">Perihal</th>
                        <th className="p-2 border-r border-black w-[16%]">No. Kontrak</th>
                        <th className="p-2 border-r border-black w-[18%]">Syarikat</th>
                        <th className="p-2 border-r border-black w-[12%]">Kos (RM)</th>
                        <th className="p-2 w-[15%]">Tandatangan</th>
                    </tr>
                </thead>
                <tbody>
                    {maintenances.length === 0 ? (
                        <tr><td colSpan="6" className="p-4 italic text-gray-500 border-b border-black">Tiada rekod penyelenggaraan.</td></tr>
                    ) : (
                        maintenances.map(m => (
                            <tr key={m.id} className="border-b border-black">
                                <td className="p-2 border-r border-black">{new Date(m.maintenance_date).toLocaleDateString('ms-MY')}</td>
                                <td className="p-2 border-r border-black text-left">{m.description}</td>
                                <td className="p-2 border-r border-black">{m.contract_no || '-'}</td>
                                <td className="p-2 border-r border-black">{m.company_name || '-'}</td>
                                <td className="p-2 border-r border-black">{Number(m.cost).toLocaleString('ms-MY', { minimumFractionDigits: 2 })}</td>
                                <td className="p-2"></td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
            <div className="p-3 bg-green-50 border-t border-black print:hidden">
                {!showForm ? (
                    <button onClick={() => setShowForm(true)} className="bg-green-600 text-white text-[10px] font-bold py-1 px-3 rounded hover:bg-green-700">
                        + Tambah Rekod Penyelenggaraan
                    </button>
                ) : (
                    <form onSubmit={handleSubmit} className="flex flex-wrap gap-2 items-center">
                        <input type="date" value={form.maintenance_date} onChange={e => setForm(p => ({ ...p, maintenance_date: e.target.value }))} className="text-[10px] p-1 border rounded w-[130px]" required />
                        <input type="text" placeholder="Perihal" value={form.description} onChange={e => setForm(p => ({ ...p, description: e.target.value }))} className="text-[10px] p-1 border rounded flex-1 min-w-[120px]" required />
                        <input type="text" placeholder="No. Kontrak" value={form.contract_no} onChange={e => setForm(p => ({ ...p, contract_no: e.target.value }))} className="text-[10px] p-1 border rounded w-[120px]" />
                        <input type="text" placeholder="Syarikat" value={form.company_name} onChange={e => setForm(p => ({ ...p, company_name: e.target.value }))} className="text-[10px] p-1 border rounded w-[140px]" />
                        <input type="number" step="0.01" placeholder="Kos (RM)" value={form.cost} onChange={e => setForm(p => ({ ...p, cost: e.target.value }))} className="text-[10px] p-1 border rounded w-[100px]" />
                        <button type="submit" className="bg-green-700 text-white text-[10px] font-bold py-1 px-3 rounded hover:bg-green-800">Simpan</button>
                        <button type="button" onClick={() => setShowForm(false)} className="text-[10px] py-1 px-2 text-gray-500">Batal</button>
                    </form>
                )}
            </div>
        </div>
    );
}

// ── Pindahan — PA-6 ────────────────────────────────────────────────────────────
function TransferTable({ asset }) {
    const [form, setForm] = useState({
        transfer_date: new Date().toISOString().split('T')[0],
        to_location: '',
        to_custodian: '',
        reference_no: '',
        reason: '',
    });
    const [showForm, setShowForm] = useState(false);
    const transfers = asset.transfers || [];

    const handleSubmit = (e) => {
        e.preventDefault();
        router.post(route('assets.transfers.store', asset.id), form, {
            preserveScroll: true,
            onSuccess: () => { setForm({ transfer_date: new Date().toISOString().split('T')[0], to_location: '', to_custodian: '', reference_no: '', reason: '' }); setShowForm(false); }
        });
    };

    return (
        <div className="border border-black mt-6">
            <div className="bg-gray-50 font-bold p-2 border-b border-black text-center">PINDAHAN (KEW.PA-6)</div>
            <table className="w-full text-center text-[10px]">
                <thead className="bg-gray-50">
                    <tr className="border-b border-black">
                        <th className="p-2 border-r border-black w-[13%]">Tarikh</th>
                        <th className="p-2 border-r border-black w-[18%]">Lokasi Baru</th>
                        <th className="p-2 border-r border-black w-[18%]">Pegawai Baru</th>
                        <th className="p-2 border-r border-black w-[16%]">No. Rujukan</th>
                        <th className="p-2 border-r border-black">Sebab</th>
                        <th className="p-2 w-[15%]">Tandatangan</th>
                    </tr>
                </thead>
                <tbody>
                    {transfers.length === 0 ? (
                        <tr><td colSpan="6" className="p-4 italic text-gray-500 border-b border-black">Tiada rekod pindahan.</td></tr>
                    ) : (
                        transfers.map(t => (
                            <tr key={t.id} className="border-b border-black">
                                <td className="p-2 border-r border-black">{new Date(t.transfer_date).toLocaleDateString('ms-MY')}</td>
                                <td className="p-2 border-r border-black uppercase">{t.to_location}</td>
                                <td className="p-2 border-r border-black">{t.to_custodian}</td>
                                <td className="p-2 border-r border-black">{t.reference_no || '-'}</td>
                                <td className="p-2 border-r border-black text-left">{t.reason || '-'}</td>
                                <td className="p-2"></td>
                            </tr>
                        ))
                    )}
                </tbody>
            </table>
            <div className="p-3 bg-purple-50 border-t border-black print:hidden">
                {!showForm ? (
                    <button onClick={() => setShowForm(true)} className="bg-purple-600 text-white text-[10px] font-bold py-1 px-3 rounded hover:bg-purple-700">
                        + Tambah Rekod Pindahan
                    </button>
                ) : (
                    <form onSubmit={handleSubmit} className="flex flex-wrap gap-2 items-center">
                        <input type="date" value={form.transfer_date} onChange={e => setForm(p => ({ ...p, transfer_date: e.target.value }))} className="text-[10px] p-1 border rounded w-[130px]" required />
                        <input type="text" placeholder="Lokasi Baru" value={form.to_location} onChange={e => setForm(p => ({ ...p, to_location: e.target.value }))} className="text-[10px] p-1 border rounded w-[140px]" required />
                        <input type="text" placeholder="Pegawai Baru" value={form.to_custodian} onChange={e => setForm(p => ({ ...p, to_custodian: e.target.value }))} className="text-[10px] p-1 border rounded w-[140px]" required />
                        <input type="text" placeholder="No. Rujukan" value={form.reference_no} onChange={e => setForm(p => ({ ...p, reference_no: e.target.value }))} className="text-[10px] p-1 border rounded w-[120px]" />
                        <input type="text" placeholder="Sebab" value={form.reason} onChange={e => setForm(p => ({ ...p, reason: e.target.value }))} className="text-[10px] p-1 border rounded flex-1 min-w-[120px]" />
                        <button type="submit" className="bg-purple-700 text-white text-[10px] font-bold py-1 px-3 rounded hover:bg-purple-800">Simpan</button>
                        <button type="button" onClick={() => setShowForm(false)} className="text-[10px] py-1 px-2 text-gray-500">Batal</button>
                    </form>
                )}
            </div>
        </div>
    );
}

// ── Main Kewpa3 component ─────────────────────────────────────────────────────
export default function Kewpa3({ asset }) {
    const { data, setData, post, processing, reset } = useForm({
        custodian_name: '',
        location: '',
        is_lokasi_luar: false,
        expected_return_date: '',
        inspection_date: new Date().toISOString().split('T')[0],
        status: 'Sedang Digunakan',
        inspector_name: '',
        notes: '',
    });

    const handleAddPlacement = (e) => {
        e.preventDefault();
        post(route('assets.placements.store', asset.id), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const [isDamageModalOpen, setIsDamageModalOpen] = useState(false);
    const {
        data: damageData,
        setData: setDamageData,
        post: postDamage,
        processing: damageProcessing,
        reset: resetDamage
    } = useForm({
        damage_date: new Date().toISOString().split('T')[0],
        last_user: asset.custodian_name || '',
        previous_maintenance_cost: '',
        damage_description: '',
    });

    const submitDamage = (e) => {
        e.preventDefault();
        postDamage(route('assets.damage-reports.store', asset.id), {
            onSuccess: () => { setIsDamageModalOpen(false); resetDamage(); }
        });
    };

    const [isLossModalOpen, setIsLossModalOpen] = useState(false);
    const {
        data: lossData,
        setData: setLossData,
        post: postLoss,
        processing: lossProcessing,
        reset: resetLoss
    } = useForm({
        incident_location: '',
        loss_date: new Date().toISOString().split('T')[0],
        loss_method: '',
        last_officer: asset.custodian_name || '',
        police_report_no: '',
        current_value: '',
        investigation_summary: '',
        approval_reference: '',
    });

    const submitLoss = (e) => {
        e.preventDefault();
        postLoss(route('assets.loss-reports.store', asset.id), {
            onSuccess: () => { setIsLossModalOpen(false); resetLoss(); }
        });
    };

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-3 — Daftar Inventori
            </h2>
        }>
            <Head title={`KEW.PA-3 - ${asset.asset_tag}`} />

            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black relative">

                    {/* ── Header ── */}
                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[14px]">KEW.PA-3</div>
                            <div className="text-[10px]">(No. Siri Pendaftaran : {asset.asset_tag})</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        DAFTAR INVENTORI
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                        <p><strong>Unit/Makmal</strong> &nbsp; {asset.location}</p>
                    </div>

                    <div className="text-[10px] italic mb-4">
                        (Satu (1) daftar bagi satu (1) jenis inventori dalam satu Pesanan Tempatan)
                    </div>

                    <div className="font-bold mb-1 uppercase">BAHAGIAN A</div>
                    <BahagianA asset={asset} />

                    <KomponenTable asset={asset} />

                    <PenempatanTable
                        asset={asset}
                        data={data} setData={setData}
                        processing={processing}
                        handleAddPlacement={handleAddPlacement}
                    />

                    <PemeriksaanTable
                        asset={asset}
                        data={data} setData={setData}
                        post={post} processing={processing}
                    />

                    <PelupusanTable asset={asset} />

                    <MaintenanceTable asset={asset} />

                    <TransferTable asset={asset} />

                    {/* ── 3-copy instruction ── */}
                    <div className="mt-6 text-[10px] italic border-t border-gray-300 pt-3">
                        Nota: Sila sediakan dalam 3 salinan (1-Unit/Makmal; 1-Pejabat Pentadbiran; 1-Pejabat Bendahari)
                    </div>

                    {/* ── Actions ── */}
                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <button
                            onClick={() => setIsDamageModalOpen(true)}
                            className="border border-red-300 text-red-700 px-6 py-2 rounded font-bold hover:bg-red-50"
                        >
                            Lapor Kerosakan (KEW.PA-9)
                        </button>
                        <button
                            onClick={() => setIsLossModalOpen(true)}
                            className="border border-orange-300 text-orange-700 px-6 py-2 rounded font-bold hover:bg-orange-50"
                        >
                            Lapor Kehilangan (KEW.PA-28)
                        </button>
                        <a
                            href={route('assets.kewpa3.download', asset.id)}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-3)
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

            {/* Damage Report Modal */}
            {isDamageModalOpen && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50, padding: 16 }}>
                    <div style={{ background: '#FFF', borderRadius: 12, maxWidth: 500, width: '100%', overflow: 'hidden', boxShadow: '0 10px 40px rgba(0,0,0,0.2)' }}>
                        <div style={{ background: '#5C001F', padding: '16px 20px' }}>
                            <h3 style={{ color: '#FFF', fontWeight: 800, margin: 0 }}>Lapor Kerosakan Aset</h3>
                            <p style={{ color: '#FFF5AB', fontSize: '12px', margin: 0 }}>{asset.name} ({asset.asset_tag})</p>
                        </div>
                        <form onSubmit={submitDamage} style={{ padding: '20px' }}>
                            {[
                                { label: 'Tarikh Kerosakan', type: 'date', key: 'damage_date', required: true },
                                { label: 'Pengguna Terakhir', type: 'text', key: 'last_user', required: true },
                                { label: 'Kos Penyelenggaraan Terdahulu (RM - Jika Ada)', type: 'number', key: 'previous_maintenance_cost', required: false },
                            ].map(f => (
                                <div key={f.key} style={{ marginBottom: 16 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>{f.label}</label>
                                    <input type={f.type} step={f.type === 'number' ? '0.01' : undefined} value={damageData[f.key]} onChange={e => setDamageData(f.key, e.target.value)} required={f.required}
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                            ))}
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Perihal Kerosakan</label>
                                <textarea rows="4" value={damageData.damage_description} onChange={e => setDamageData('damage_description', e.target.value)} required
                                    style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} placeholder="Terangkan kerosakan secara terperinci..." />
                            </div>
                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 8 }}>
                                <button type="button" onClick={() => setIsDamageModalOpen(false)} style={{ padding: '8px 16px', borderRadius: 6, background: '#F9F7F5', color: '#4A4540', fontWeight: 700 }}>Batal</button>
                                <button type="submit" disabled={damageProcessing} style={{ padding: '8px 16px', borderRadius: 6, background: '#5C001F', color: '#FFF', fontWeight: 700 }}>
                                    {damageProcessing ? 'Menghantar...' : 'Hantar Laporan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Loss Report Modal — KEW.PA-28 → 32 */}
            {isLossModalOpen && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50, padding: 16 }}>
                    <div style={{ background: '#FFF', borderRadius: 12, maxWidth: 520, width: '100%', overflow: 'hidden', boxShadow: '0 10px 40px rgba(0,0,0,0.2)' }}>
                        <div style={{ background: '#C2410C', padding: '16px 20px' }}>
                            <h3 style={{ color: '#FFF', fontWeight: 800, margin: 0 }}>Lapor Kehilangan Aset (KEW.PA-28)</h3>
                            <p style={{ color: '#FFEDD5', fontSize: '12px', margin: 0 }}>{asset.name} ({asset.asset_tag})</p>
                        </div>
                        <form onSubmit={submitLoss} style={{ padding: '20px', maxHeight: '70vh', overflowY: 'auto' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                <div style={{ marginBottom: 12 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Tarikh Kehilangan</label>
                                    <input type="date" value={lossData.loss_date} onChange={e => setLossData('loss_date', e.target.value)} required
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                                <div style={{ marginBottom: 12 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Cara Kehilangan</label>
                                    <input type="text" value={lossData.loss_method} onChange={e => setLossData('loss_method', e.target.value)} required placeholder="cth: Hilang / Dicuri"
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                            </div>
                            <div style={{ marginBottom: 12 }}>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Lokasi Insiden</label>
                                <input type="text" value={lossData.incident_location} onChange={e => setLossData('incident_location', e.target.value)} required
                                    style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                <div style={{ marginBottom: 12 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Pegawai Terakhir</label>
                                    <input type="text" value={lossData.last_officer} onChange={e => setLossData('last_officer', e.target.value)} required
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                                <div style={{ marginBottom: 12 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>No. Laporan Polis</label>
                                    <input type="text" value={lossData.police_report_no} onChange={e => setLossData('police_report_no', e.target.value)} required
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                            </div>
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                                <div style={{ marginBottom: 12 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Nilai Semasa (RM)</label>
                                    <input type="number" step="0.01" value={lossData.current_value} onChange={e => setLossData('current_value', e.target.value)} required
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                                <div style={{ marginBottom: 12 }}>
                                    <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Rujukan Kelulusan</label>
                                    <input type="text" value={lossData.approval_reference} onChange={e => setLossData('approval_reference', e.target.value)}
                                        style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} />
                                </div>
                            </div>
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'block', fontSize: '11px', fontWeight: 700, color: '#8A8480', marginBottom: 4 }}>Ringkasan Siasatan</label>
                                <textarea rows="3" value={lossData.investigation_summary} onChange={e => setLossData('investigation_summary', e.target.value)}
                                    style={{ width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #EDE9E4' }} placeholder="Ringkasan siasatan dalaman..." />
                            </div>
                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 8 }}>
                                <button type="button" onClick={() => setIsLossModalOpen(false)} style={{ padding: '8px 16px', borderRadius: 6, background: '#F9F7F5', color: '#4A4540', fontWeight: 700 }}>Batal</button>
                                <button type="submit" disabled={lossProcessing} style={{ padding: '8px 16px', borderRadius: 6, background: '#C2410C', color: '#FFF', fontWeight: 700 }}>
                                    {lossProcessing ? 'Menghantar...' : 'Hantar Laporan'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
