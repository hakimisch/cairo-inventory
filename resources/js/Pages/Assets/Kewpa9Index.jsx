import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F', gold: '#F8A617', goldDark:'#C9840A', sand: '#FFF5AB',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
};

const inputStyle = { width: '100%', padding: '7px 10px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, fontSize: 12, color: UTM.gray700, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const labelStyle = { display: 'block', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 3 };

function StatusBadge({ status }) {
    const map = {
        pending    : { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Pending' },
        approved   : { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Approved' },
        rejected   : { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Rejected' },
        in_progress: { bg: '#DBEAFE', color: '#1E40AF',    border: '#B3D4F7', label: 'In Progress' },
        resolved   : { bg: '#E5E7EB', color: '#4B5563',    border: '#D1D5DB', label: 'Resolved' },
    };
    const s = map[status] || { bg: '#F3E0E5', color: UTM.maroon, label: status };
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '2px 8px',
            borderRadius : 999,
            fontSize     : 10,
            fontWeight   : 700,
            background   : s.bg,
            color        : s.color,
            border       : `1px solid ${s.border || s.bg}`,
            whiteSpace   : 'nowrap',
        }}>
            {s.label}
        </span>
    );
}

const statusOptions = [
    { value: 'pending',     label: 'Pending' },
    { value: 'approved',    label: 'Approved' },
    { value: 'rejected',    label: 'Rejected' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'resolved',    label: 'Resolved' },
];

// ── Add Damage Report Form ──
function AddDamageReportForm({ assets, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        asset_id: '',
        damage_date: new Date().toISOString().split('T')[0],
        last_user: '',
        previous_maintenance_cost: '',
        damage_description: '',
        technical_notes: '',
        recommendation: '',
        hod_decision: '',
        status: 'pending',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!data.asset_id) return;
        post(route('assets.damage-reports.store', data.asset_id), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#FEF3D6', borderRadius: 8, border: `1px solid #F5D890`, marginBottom: 16 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: UTM.goldDark, marginBottom: 12 }}>+ Aduan Kerosakan Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Aset</label>
                    <select style={inputStyle} value={data.asset_id} onChange={e => setData('asset_id', e.target.value)} required>
                        <option value="">— Pilih Aset —</option>
                        {assets.map(a => <option key={a.id} value={a.id}>{a.asset_tag} — {a.name}</option>)}
                    </select>
                    {errors.asset_id && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.asset_id}</p>}
                </div>
                <div>
                    <label style={labelStyle}>Tarikh Kerosakan</label>
                    <input type="date" style={inputStyle} value={data.damage_date} onChange={e => setData('damage_date', e.target.value)} required />
                    {errors.damage_date && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.damage_date}</p>}
                </div>
                <div>
                    <label style={labelStyle}>Pengguna Terakhir</label>
                    <input type="text" style={inputStyle} value={data.last_user} onChange={e => setData('last_user', e.target.value)} placeholder="Nama pengguna..." />
                    {errors.last_user && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.last_user}</p>}
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Kos Penyelenggaraan Sebelum (RM)</label>
                    <input type="number" step="0.01" min="0" style={inputStyle} value={data.previous_maintenance_cost} onChange={e => setData('previous_maintenance_cost', e.target.value)} placeholder="0.00" />
                    {errors.previous_maintenance_cost && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.previous_maintenance_cost}</p>}
                </div>
                <div>
                    <label style={labelStyle}>Status</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        {statusOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Keputusan HOD</label>
                    <input type="text" style={inputStyle} value={data.hod_decision} onChange={e => setData('hod_decision', e.target.value)} placeholder="Keputusan HOD..." />
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Keterangan Kerosakan</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.damage_description} onChange={e => setData('damage_description', e.target.value)} required placeholder="Terangkan kerosakan..." />
                    {errors.damage_description && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.damage_description}</p>}
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Nota Teknikal</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.technical_notes} onChange={e => setData('technical_notes', e.target.value)} placeholder="Nota teknikal (pilihan)..." />
                </div>
                <div>
                    <label style={labelStyle}>Rekomendasi</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.recommendation} onChange={e => setData('recommendation', e.target.value)} placeholder="Rekomendasi (pilihan)..." />
                </div>
            </div>
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.goldDark, color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Kerosakan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
            {errors.asset_id && <p style={{ fontSize: 11, color: '#DC2626', marginTop: 8 }}>{errors.asset_id}</p>}
        </form>
    );
}

// ── Edit Damage Report Form ──
function EditDamageReportForm({ record, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        damage_date: record.damage_date ? record.damage_date.split('T')[0] : '',
        last_user: record.last_user ?? '',
        previous_maintenance_cost: record.previous_maintenance_cost ?? '',
        damage_description: record.damage_description ?? '',
        technical_notes: record.technical_notes ?? '',
        recommendation: record.recommendation ?? '',
        hod_decision: record.hod_decision ?? '',
        status: record.status ?? 'pending',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('assets.damage-reports.update', [record.asset_id, record.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#FFF7ED', borderRadius: 6, border: `1px solid #FED7AA`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#C2410C', marginBottom: 8 }}>Edit Aduan Kerosakan</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div>
                    <label style={labelStyle}>Tarikh Kerosakan</label>
                    <input type="date" style={inputStyle} value={data.damage_date} onChange={e => setData('damage_date', e.target.value)} required />
                    {errors.damage_date && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.damage_date}</p>}
                </div>
                <div>
                    <label style={labelStyle}>Pengguna Terakhir</label>
                    <input type="text" style={inputStyle} value={data.last_user} onChange={e => setData('last_user', e.target.value)} placeholder="Nama pengguna..." />
                </div>
                <div>
                    <label style={labelStyle}>Kos Penyelenggaraan Sebelum (RM)</label>
                    <input type="number" step="0.01" min="0" style={inputStyle} value={data.previous_maintenance_cost} onChange={e => setData('previous_maintenance_cost', e.target.value)} placeholder="0.00" />
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div>
                    <label style={labelStyle}>Status</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        {statusOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Keputusan HOD</label>
                    <input type="text" style={inputStyle} value={data.hod_decision} onChange={e => setData('hod_decision', e.target.value)} placeholder="Keputusan HOD..." />
                </div>
                <div></div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 8, marginBottom: 8 }}>
                <div>
                    <label style={labelStyle}>Keterangan Kerosakan</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.damage_description} onChange={e => setData('damage_description', e.target.value)} required placeholder="Terangkan kerosakan..." />
                    {errors.damage_description && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.damage_description}</p>}
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div>
                    <label style={labelStyle}>Nota Teknikal</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.technical_notes} onChange={e => setData('technical_notes', e.target.value)} placeholder="Nota teknikal (pilihan)..." />
                </div>
                <div>
                    <label style={labelStyle}>Rekomendasi</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.recommendation} onChange={e => setData('recommendation', e.target.value)} placeholder="Rekomendasi (pilihan)..." />
                </div>
            </div>
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#C2410C', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

export default function Kewpa9Index({ records, filters, assets }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');
    const [showAdd, setShowAdd] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('damage-reports.index'), { search, status: statusFilter }, { preserveState: true, replace: true });
    };

    const handleReset = () => {
        setSearch('');
        setStatusFilter('');
        router.get(route('damage-reports.index'), {}, { preserveState: true, replace: true });
    };

    const handleDelete = (record) => {
        if (window.confirm(`Padam aduan kerosakan aset ${record.asset?.name} (${record.damage_description || record.damage_date})?`)) {
            router.delete(route('assets.damage-reports.destroy', [record.asset_id, record.id]), { preserveScroll: true });
        }
    };

    const thStyle = { padding: '10px 14px', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, textAlign: 'left', borderBottom: `1px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const tdStyle = { padding: '12px 14px', fontSize: 13, color: UTM.gray900, borderBottom: `1px solid ${UTM.gray100}` };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-9 — Aduan Kerosakan Aset" />

            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                {/* ── Header ─────────────────────────────────────────────── */}
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>
                        KEW.PA-9 — Aduan Kerosakan Aset
                    </h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                        Senarai laporan kerosakan aset
                    </p>
                </div>

                {/* ── Toolbar ──────────────────────────────────────────────── */}
                <div style={{ display: 'flex', gap: 12, alignItems: 'flex-end', marginBottom: 16, flexWrap: 'wrap' }}>
                    <form onSubmit={handleSearch} style={{ display: 'flex', gap: 12, alignItems: 'flex-end', flexWrap: 'wrap', flex: 1 }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={labelStyle}>Carian</label>
                            <input
                                type="text"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                placeholder="Nama aset, tag, pelapor..."
                                style={inputStyle}
                            />
                        </div>
                        <div style={{ minWidth: 150 }}>
                            <label style={labelStyle}>Status</label>
                            <select value={statusFilter} onChange={e => setStatusFilter(e.target.value)} style={inputStyle}>
                                <option value="">Semua</option>
                                {statusOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                            </select>
                        </div>
                        <button type="submit" style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.maroon, color: UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer' }}>
                            Cari
                        </button>
                        <button type="button" onClick={handleReset} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>
                            Reset
                        </button>
                    </form>
                    <button onClick={() => setShowAdd(!showAdd)} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: showAdd ? UTM.gray100 : UTM.goldDark, color: showAdd ? UTM.gray700 : UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap' }}>
                        {showAdd ? 'Batal' : '+ Aduan Baru'}
                    </button>
                </div>

                {showAdd && <AddDamageReportForm assets={assets} onDone={() => setShowAdd(false)} />}

                {/* ── Table ──────────────────────────────────────────────── */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', minWidth: '1050px', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr>
                                    <th style={thStyle}>Aset</th>
                                    <th style={thStyle}>Tag</th>
                                    <th style={thStyle}>Pengguna Terakhir</th>
                                    <th style={thStyle}>Kerosakan</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={thStyle}>Tarikh</th>
                                    <th style={thStyle}></th>
                                </tr>
                            </thead>
                            <tbody>
                                {records.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>
                                            Tiada rekod kerosakan aset.
                                        </td>
                                    </tr>
                                ) : records.data.map((record, idx) => (
                                    editingId === record.id ? (
                                        <tr key={record.id}>
                                            <td colSpan={7} style={{ padding: 0, background: '#FAFAFA' }}>
                                                <EditDamageReportForm record={record} onDone={() => setEditingId(null)} />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={record.id}
                                            style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50, transition: 'background 0.12s' }}
                                            onMouseEnter={e => { e.currentTarget.style.background = '#FFF5E8'; }}
                                            onMouseLeave={e => { e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}
                                        >
                                            <td style={tdStyle}>
                                                <Link href={route('assets.kewpa3', record.asset_id)}
                                                    style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                    {record.asset?.name || '—'}
                                                </Link>
                                            </td>
                                            <td style={tdStyle}>
                                                <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>
                                                    {record.asset?.asset_tag || '—'}
                                                </span>
                                            </td>
                                            <td style={tdStyle}>{record.last_user || '—'}</td>
                                            <td style={tdStyle}>
                                                <span style={{
                                                    maxWidth    : 200,
                                                    display     : 'inline-block',
                                                    overflow    : 'hidden',
                                                    textOverflow: 'ellipsis',
                                                    whiteSpace  : 'nowrap',
                                                }}>
                                                    {record.damage_description || '—'}
                                                </span>
                                            </td>
                                            <td style={tdStyle}><StatusBadge status={record.status} /></td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                {record.damage_date ? new Date(record.damage_date).toLocaleDateString('ms-MY') : (record.created_at ? new Date(record.created_at).toLocaleDateString('ms-MY') : '—')}
                                            </td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                <Link href={route('assets.kewpa2', record.asset_id)}
                                                    style={{ display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#EDE9E4', color: UTM.gray700, textDecoration: 'none', marginRight: 4 }}>
                                                    Borang
                                                </Link>
                                                <a href={route('damage-reports.kewpa9.download', record.id)}
                                                    style={{ display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: UTM.maroon, color: UTM.white, textDecoration: 'none', marginRight: 4 }}>
                                                    KEW.PA-9
                                                </a>
                                                <button onClick={() => setEditingId(record.id)}
                                                    style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#F5F3FF', color: '#5B21B6', border: 'none', cursor: 'pointer', marginRight: 4 }}>
                                                    Edit
                                                </button>
                                                <button onClick={() => handleDelete(record)}
                                                    style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#FEF2F2', color: '#DC2626', border: 'none', cursor: 'pointer' }}>
                                                    Padam
                                                </button>
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

                {/* ── Pagination ─────────────────────────────────────────── */}
                {records.links && (
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 6, marginTop: 20, flexWrap: 'wrap' }}>
                        {records.links.map((link, i) => {
                            if (!link.url) return null;
                            return (
                                <Link key={i} href={link.url} preserveState replace
                                    style={{
                                        padding      : '7px 12px',
                                        borderRadius : 6,
                                        border       : `1px solid ${link.active ? UTM.maroon : UTM.gray100}`,
                                        background   : link.active ? UTM.maroon : UTM.white,
                                        color        : link.active ? UTM.white : UTM.gray700,
                                        fontSize     : 12,
                                        fontWeight   : 600,
                                        textDecoration: 'none',
                                    }}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            );
                        })}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
