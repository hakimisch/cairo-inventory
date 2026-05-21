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
    const map = { pending: { bg: '#FEF3D6', color: UTM.goldDark, label: 'Pending' }, approved: { bg: '#E6F4EC', color: '#1A7A3C', label: 'Approved' }, completed: { bg: '#DBEAFE', color: '#1E40AF', label: 'Completed' }, rejected: { bg: '#F3E0E5', color: UTM.maroon, label: 'Rejected' } };
    const s = map[status] || { bg: '#F3E0E5', color: UTM.maroon, label: status };
    return <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: 999, fontSize: 10, fontWeight: 700, background: s.bg, color: s.color, border: `1px solid ${s.bg}` }}>{s.label}</span>;
}

// ── Add Transfer Form ──
function AddTransferForm({ assets, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        asset_id: '', to_location: '', to_custodian: '', transfer_date: new Date().toISOString().split('T')[0],
        reference_no: '', reason: '', status: 'pending',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('assets.transfers.store', data.asset_id), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#F5F3FF', borderRadius: 8, border: `1px solid #DDD6FE`, marginBottom: 16 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: '#5B21B6', marginBottom: 12 }}>+ Rekod Pergerakan Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Aset</label>
                    <select style={inputStyle} value={data.asset_id} onChange={e => setData('asset_id', e.target.value)} required>
                        <option value="">— Pilih Aset —</option>
                        {assets.map(a => <option key={a.id} value={a.id}>{a.asset_tag} — {a.name}</option>)}
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Lokasi Baru</label>
                    <input type="text" style={inputStyle} value={data.to_location} onChange={e => setData('to_location', e.target.value)} required placeholder="Makmal/Bilik..." />
                </div>
                <div>
                    <label style={labelStyle}>Penjaga Baru</label>
                    <input type="text" style={inputStyle} value={data.to_custodian} onChange={e => setData('to_custodian', e.target.value)} required placeholder="Nama pegawai..." />
                </div>
                <div>
                    <label style={labelStyle}>Tarikh</label>
                    <input type="date" style={inputStyle} value={data.transfer_date} onChange={e => setData('transfer_date', e.target.value)} required />
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>No. Rujukan</label>
                    <input type="text" style={inputStyle} value={data.reference_no} onChange={e => setData('reference_no', e.target.value)} placeholder="No. rujukan (pilihan)..." />
                </div>
                <div>
                    <label style={labelStyle}>Sebab</label>
                    <input type="text" style={inputStyle} value={data.reason} onChange={e => setData('reason', e.target.value)} placeholder="Sebab pergerakan (pilihan)..." />
                </div>
            </div>
            {errors.asset_id && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.asset_id}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: '#5B21B6', color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Pergerakan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Edit Transfer Form ──
function EditTransferForm({ record, onDone }) {
    const { data, setData, put, processing } = useForm({
        to_location: record.to_location ?? '',
        to_custodian: record.to_custodian ?? '',
        from_location: record.from_location ?? '',
        from_custodian: record.from_custodian ?? '',
        transfer_date: record.transfer_date ? record.transfer_date.split('T')[0] : '',
        reference_no: record.reference_no ?? '',
        reason: record.reason ?? '',
        status: record.status ?? 'pending',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('assets.transfers.update', [record.asset_id, record.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#FEF3C7', borderRadius: 6, border: `1px solid #FDE68A`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#92400E', marginBottom: 8 }}>Edit Pergerakan</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Dari</label><input type="text" style={inputStyle} value={data.from_location} onChange={e => setData('from_location', e.target.value)} placeholder="Lokasi asal..." /></div>
                <div><label style={labelStyle}>Ke</label><input type="text" style={inputStyle} value={data.to_location} onChange={e => setData('to_location', e.target.value)} required /></div>
                <div><label style={labelStyle}>Penjaga Baru</label><input type="text" style={inputStyle} value={data.to_custodian} onChange={e => setData('to_custodian', e.target.value)} required /></div>
                <div><label style={labelStyle}>Tarikh</label><input type="date" style={inputStyle} value={data.transfer_date} onChange={e => setData('transfer_date', e.target.value)} required /></div>
                <div><label style={labelStyle}>No. Rujukan</label><input type="text" style={inputStyle} value={data.reference_no} onChange={e => setData('reference_no', e.target.value)} /></div>
                <div><label style={labelStyle}>Status</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        <option value="pending">Pending</option><option value="approved">Approved</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div style={{ gridColumn: 'span 2' }}><label style={labelStyle}>Sebab</label><input type="text" style={inputStyle} value={data.reason} onChange={e => setData('reason', e.target.value)} /></div>
            </div>
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#92400E', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

export default function Kewpa6Index({ records, filters, assets }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');
    const [showAdd, setShowAdd] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const handleSearch = (e) => { e.preventDefault(); router.get(route('transfers.index'), { search, status: statusFilter }, { preserveState: true, replace: true }); };
    const handleReset = () => { setSearch(''); setStatusFilter(''); router.get(route('transfers.index'), {}, { preserveState: true, replace: true }); };

    const handleDelete = (record) => {
        if (window.confirm(`Padam pergerakan aset ${record.asset?.name} (${record.reference_no || record.to_location})?`)) {
            router.delete(route('assets.transfers.destroy', [record.asset_id, record.id]), { preserveScroll: true });
        }
    };

    const thStyle = { padding: '10px 16px', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, textAlign: 'left', borderBottom: `1px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const tdStyle = { padding: '12px 16px', fontSize: 13, color: UTM.gray900, borderBottom: `1px solid ${UTM.gray100}` };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-6 — Daftar Pergerakan Aset" />
            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>KEW.PA-6 — Daftar Pergerakan Aset</h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>Senarai pergerakan / pindah milik aset</p>
                </div>

                {/* Toolbar */}
                <div style={{ display: 'flex', gap: 12, alignItems: 'flex-end', marginBottom: 16, flexWrap: 'wrap' }}>
                    <form onSubmit={handleSearch} style={{ display: 'flex', gap: 12, alignItems: 'flex-end', flexWrap: 'wrap', flex: 1 }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={labelStyle}>Carian</label>
                            <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Nama aset, tag, rujukan, lokasi..." style={inputStyle} />
                        </div>
                        <div style={{ minWidth: 140 }}>
                            <label style={labelStyle}>Status</label>
                            <select value={statusFilter} onChange={e => setStatusFilter(e.target.value)} style={inputStyle}>
                                <option value="">Semua</option>
                                <option value="pending">Pending</option><option value="approved">Approved</option>
                                <option value="completed">Completed</option><option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <button type="submit" style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.maroon, color: UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer' }}>Cari</button>
                        <button type="button" onClick={handleReset} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Reset</button>
                    </form>
                    <button onClick={() => setShowAdd(!showAdd)} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: showAdd ? UTM.gray100 : '#5B21B6', color: showAdd ? UTM.gray700 : UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap' }}>
                        {showAdd ? 'Batal' : '+ Pergerakan Baru'}
                    </button>
                </div>

                {showAdd && <AddTransferForm assets={assets} onDone={() => setShowAdd(false)} />}

                {/* Table */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', minWidth: '1000px', borderCollapse: 'collapse' }}>
                            <thead><tr style={{ background: UTM.gray50 }}>
                                <th style={thStyle}>Aset</th>
                                <th style={thStyle}>Tag</th>
                                <th style={thStyle}>Rujukan</th>
                                <th style={thStyle}>Dari</th>
                                <th style={thStyle}>Ke</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Tarikh</th>
                                <th style={thStyle}></th>
                            </tr></thead>
                            <tbody>
                                {records.data.length === 0 ? (
                                    <tr><td colSpan={8} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>Tiada rekod pergerakan aset.</td></tr>
                                ) : records.data.map((record, idx) => (
                                    editingId === record.id ? (
                                        <tr key={record.id}>
                                            <td colSpan={8} style={{ padding: 0, background: '#FAFAFA' }}>
                                                <EditTransferForm record={record} onDone={() => setEditingId(null)} />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={record.id} style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50 }}>
                                            <td style={tdStyle}>
                                                <Link href={route('assets.kewpa3', record.asset_id)} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                    {record.asset?.name || '—'}
                                                </Link>
                                            </td>
                                            <td style={tdStyle}><span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>{record.asset?.asset_tag || '—'}</span></td>
                                            <td style={tdStyle}>{record.reference_no || '—'}</td>
                                            <td style={tdStyle}>{record.from_location || record.asset?.location || '—'}</td>
                                            <td style={tdStyle}>{record.to_location}</td>
                                            <td style={tdStyle}><StatusBadge status={record.status} /></td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>{new Date(record.transfer_date || record.created_at).toLocaleDateString('ms-MY')}</td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                <Link href={route('assets.kewpa6', record.asset_id)} style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#EDE9E4', color: UTM.gray700, textDecoration: 'none', marginRight: 4 }}>Borang</Link>
                                                <button onClick={() => setEditingId(record.id)} style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#FEF3C7', color: '#92400E', border: 'none', cursor: 'pointer', marginRight: 4 }}>Edit</button>
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
