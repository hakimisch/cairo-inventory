import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

const UTM = {
    maroon : '#5C001F', gold: '#F8A617', goldDark:'#C9840A', sand: '#FFF5AB',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
};

const inputStyle = { width: '100%', padding: '7px 10px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, fontSize: 12, color: UTM.gray700, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const labelStyle = { display: 'block', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 3 };

function StatusBadge({ status }) {
    const map = { pending: { bg: '#FEF3D6', color: UTM.goldDark, label: 'Pending' }, completed: { bg: '#E6F4EC', color: '#1A7A3C', label: 'Completed' } };
    const s = map[status] || { bg: '#F3E0E5', color: UTM.maroon, label: status };
    return <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: 999, fontSize: 10, fontWeight: 700, background: s.bg, color: s.color, border: `1px solid ${s.bg}` }}>{s.label}</span>;
}

// ── Add Inspection Form ──
function AddInspectionForm({ assets, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        asset_id: '', inspection_date: new Date().toISOString().split('T')[0],
        status: 'Sedang Digunakan', inspector_name: '', notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('assets.inspections.store', data.asset_id), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#EFF6FF', borderRadius: 8, border: `1px solid #BFDBFE`, marginBottom: 16 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: '#1E40AF', marginBottom: 12 }}>+ Tambah Pemeriksaan Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Aset</label>
                    <select style={inputStyle} value={data.asset_id} onChange={e => setData('asset_id', e.target.value)} required>
                        <option value="">— Pilih Aset —</option>
                        {assets.map(a => <option key={a.id} value={a.id}>{a.asset_tag} — {a.name}</option>)}
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Tarikh</label>
                    <input type="date" style={inputStyle} value={data.inspection_date} onChange={e => setData('inspection_date', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Status Aset</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        <option value="Sedang Digunakan">Sedang Digunakan</option>
                        <option value="Tidak Digunakan">Tidak Digunakan</option>
                        <option value="Rosak">Rosak</option>
                        <option value="Baik">Baik</option>
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Nama Pemeriksa</label>
                    <input type="text" style={inputStyle} value={data.inspector_name} onChange={e => setData('inspector_name', e.target.value)} placeholder="Nama pemeriksa..." />
                </div>
            </div>
            <div style={{ marginBottom: 10 }}>
                <label style={labelStyle}>Catatan</label>
                <input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan pemeriksaan (pilihan)..." />
            </div>
            {errors.asset_id && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.asset_id}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: '#1E40AF', color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Pemeriksaan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Edit Inspection Form ──
function EditInspectionForm({ record, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        inspection_date: record.inspection_date ? record.inspection_date.split('T')[0] : '',
        status: record.status ?? '',
        inspector_name: record.inspector_name ?? '',
        notes: record.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('assets.inspections.update', [record.asset_id, record.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#F5F3FF', borderRadius: 6, border: `1px solid #DDD6FE`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#5B21B6', marginBottom: 8 }}>Edit Pemeriksaan</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Tarikh</label><input type="date" style={inputStyle} value={data.inspection_date} onChange={e => setData('inspection_date', e.target.value)} required /></div>
                <div><label style={labelStyle}>Status</label><select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}><option value="Sedang Digunakan">Sedang Digunakan</option><option value="Tidak Digunakan">Tidak Digunakan</option><option value="Rosak">Rosak</option><option value="Baik">Baik</option></select></div>
                <div><label style={labelStyle}>Pemeriksa</label><input type="text" style={inputStyle} value={data.inspector_name} onChange={e => setData('inspector_name', e.target.value)} /></div>
                <div><label style={labelStyle}>Catatan</label><input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} /></div>
            </div>
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#5B21B6', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

export default function Kewpa10Index({ records, filters, assets }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');
    const [showAdd, setShowAdd] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const handleSearch = (e) => { e.preventDefault(); router.get(route('inspections.index'), { search, status: statusFilter }, { preserveState: true, replace: true }); };
    const handleReset = () => { setSearch(''); setStatusFilter(''); router.get(route('inspections.index'), {}, { preserveState: true, replace: true }); };

    const handleDelete = (record) => {
        if (window.confirm(`Padam pemeriksaan aset ${record.asset?.name} (${record.inspection_date})?`)) {
            router.delete(route('assets.inspections.destroy', [record.asset_id, record.id]), { preserveScroll: true });
        }
    };

    const thStyle = { padding: '10px 16px', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, textAlign: 'left', borderBottom: `1px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const tdStyle = { padding: '12px 16px', fontSize: 13, color: UTM.gray900, borderBottom: `1px solid ${UTM.gray100}` };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-10 — Pemeriksaan Aset" />
            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>KEW.PA-10 — Pemeriksaan Aset</h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>Senarai pemeriksaan aset / inventory</p>
                </div>

                {/* Toolbar */}
                <div style={{ display: 'flex', gap: 12, alignItems: 'flex-end', marginBottom: 16, flexWrap: 'wrap' }}>
                    <form onSubmit={handleSearch} style={{ display: 'flex', gap: 12, alignItems: 'flex-end', flexWrap: 'wrap', flex: 1 }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={labelStyle}>Carian</label>
                            <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Nama aset, pemeriksa..." style={inputStyle} />
                        </div>
                        <div style={{ minWidth: 140 }}>
                            <label style={labelStyle}>Status</label>
                            <select value={statusFilter} onChange={e => setStatusFilter(e.target.value)} style={inputStyle}>
                                <option value="">Semua</option>
                                <option value="Sedang Digunakan">Sedang Digunakan</option>
                                <option value="Tidak Digunakan">Tidak Digunakan</option>
                                <option value="Rosak">Rosak</option>
                                <option value="Baik">Baik</option>
                            </select>
                        </div>
                        <button type="submit" style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.maroon, color: UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer' }}>Cari</button>
                        <button type="button" onClick={handleReset} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Reset</button>
                    </form>
                    <Link
                        href={route('inspections.kewpa11')}
                        style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid #7B1FA2`, background: UTM.white, color: '#7B1FA2', fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap', textDecoration: 'none', display: 'inline-flex', alignItems: 'center' }}
                    >
                        ◈ PA-11 Inventori
                    </Link>
                    <button onClick={() => setShowAdd(!showAdd)} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: showAdd ? UTM.gray100 : '#1E40AF', color: showAdd ? UTM.gray700 : UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap' }}>
                        {showAdd ? 'Batal' : '+ Pemeriksaan Baru'}
                    </button>
                </div>

                {showAdd && <AddInspectionForm assets={assets} onDone={() => setShowAdd(false)} />}

                {/* Table */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', minWidth: '900px', borderCollapse: 'collapse' }}>
                            <thead><tr style={{ background: UTM.gray50 }}>
                                <th style={thStyle}>Aset</th>
                                <th style={thStyle}>Tag</th>
                                <th style={thStyle}>Tarikh</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Pemeriksa</th>
                                <th style={thStyle}>Catatan</th>
                                <th style={thStyle}></th>
                            </tr></thead>
                            <tbody>
                                {records.data.length === 0 ? (
                                    <tr><td colSpan={7} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>Tiada rekod pemeriksaan.</td></tr>
                                ) : records.data.map((record, idx) => (
                                    editingId === record.id ? (
                                        <tr key={record.id}>
                                            <td colSpan={7} style={{ padding: 0, background: '#FAFAFA' }}>
                                                <EditInspectionForm record={record} onDone={() => setEditingId(null)} />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={record.id} style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50 }}>
                                            <td style={tdStyle}>
                                                <Link href={route('assets.kewpa2', record.asset_id)} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                    {record.asset?.name || '—'}
                                                </Link>
                                            </td>
                                            <td style={tdStyle}><span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>{record.asset?.asset_tag || '—'}</span></td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>{new Date(record.inspection_date).toLocaleDateString('ms-MY')}</td>
                                            <td style={tdStyle}><StatusBadge status={record.status === 'Sedang Digunakan' || record.status === 'Baik' ? 'completed' : 'pending'} /></td>
                                            <td style={tdStyle}>{record.inspector_name || '—'}</td>
                                            <td style={{ ...tdStyle, maxWidth: 200, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{record.notes || '—'}</td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                <button onClick={() => setEditingId(record.id)} style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#F5F3FF', color: '#5B21B6', border: 'none', cursor: 'pointer', marginRight: 4 }}>Edit</button>
                                                <button onClick={() => handleDelete(record)} style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#FEF2F2', color: '#DC2626', border: 'none', cursor: 'pointer' }}>Padam</button>
                                            </td>
                                        </tr>
                                    )
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div style={{ padding: '10px 16px', borderTop: `1px solid ${UTM.gray100}`, background: UTM.gray50, fontSize: 12, color: UTM.gray500 }}>
                        Menunjukkan <strong style={{ color: UTM.maroon }}>{records.data.length}</strong> daripada <strong style={{ color: UTM.maroon }}>{records.total}</strong> rekod
                    </div>
                </div>

                {records.links && (
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 6, marginTop: 20, flexWrap: 'wrap' }}>
                        {records.links.map((link, i) => {
                            if (!link.url) return null;
                            return <Link key={i} href={link.url} preserveState replace
                                style={{ padding: '7px 12px', borderRadius: 6, border: `1px solid ${link.active ? UTM.maroon : UTM.gray100}`, background: link.active ? UTM.maroon : UTM.white, color: link.active ? UTM.white : UTM.gray700, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}
                                dangerouslySetInnerHTML={{ __html: link.label }} />;
                        })}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
