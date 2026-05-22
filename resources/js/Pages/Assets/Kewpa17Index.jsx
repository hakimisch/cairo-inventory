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
        pending  : { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Pending' },
        approved : { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Approved' },
        rejected : { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Rejected' },
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

// ── Add Disposal Form ──
function AddDisposalForm({ assets, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        asset_id: '',
        disposal_date: new Date().toISOString().split('T')[0],
        disposal_method: '',
        request_reason: '',
        approval_reference: '',
        committee_decision: '',
        status: 'pending',
        notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('assets.disposals.store', data.asset_id), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#FEF3F5', borderRadius: 8, border: `1px solid #FBC4CD`, marginBottom: 16 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: UTM.maroon, marginBottom: 12 }}>+ Pelupusan Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Aset</label>
                    <select style={inputStyle} value={data.asset_id} onChange={e => setData('asset_id', e.target.value)} required>
                        <option value="">— Pilih Aset —</option>
                        {assets.map(a => <option key={a.id} value={a.id}>{a.asset_tag} — {a.name}</option>)}
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Tarikh Pelupusan</label>
                    <input type="date" style={inputStyle} value={data.disposal_date} onChange={e => setData('disposal_date', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Kaedah Pelupusan</label>
                    <select style={inputStyle} value={data.disposal_method} onChange={e => setData('disposal_method', e.target.value)} required>
                        <option value="">— Pilih Kaedah —</option>
                        <option value="lelong">Lelong</option>
                        <option value="lupus">Lupus</option>
                        <option value="hibah">Hibah</option>
                        <option value="lain">Lain</option>
                    </select>
                </div>
                <div>
                    <label style={labelStyle}>Rujukan Kelulusan</label>
                    <input type="text" style={inputStyle} value={data.approval_reference} onChange={e => setData('approval_reference', e.target.value)} placeholder="No. rujukan..." />
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Sebab Permohonan</label>
                    <input type="text" style={inputStyle} value={data.request_reason} onChange={e => setData('request_reason', e.target.value)} placeholder="Sebab pelupusan..." />
                </div>
                <div>
                    <label style={labelStyle}>Keputusan Jawatankuasa</label>
                    <input type="text" style={inputStyle} value={data.committee_decision} onChange={e => setData('committee_decision', e.target.value)} placeholder="Keputusan..." />
                </div>
                <div>
                    <label style={labelStyle}>Status</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div style={{ marginBottom: 10 }}>
                <label style={labelStyle}>Catatan</label>
                <input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan tambahan (pilihan)..." />
            </div>
            {errors.asset_id && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.asset_id}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.maroon, color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Pelupusan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Edit Disposal Form ──
function EditDisposalForm({ record, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        disposal_date: record.disposal_date ? record.disposal_date.split('T')[0] : '',
        disposal_method: record.disposal_method ?? '',
        request_reason: record.request_reason ?? '',
        approval_reference: record.approval_reference ?? '',
        committee_decision: record.committee_decision ?? '',
        status: record.status ?? 'pending',
        notes: record.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('assets.disposals.update', [record.asset_id, record.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#FEF3C7', borderRadius: 6, border: `1px solid #FDE68A`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#92400E', marginBottom: 8 }}>Edit Pelupusan</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Tarikh Pelupusan</label><input type="date" style={inputStyle} value={data.disposal_date} onChange={e => setData('disposal_date', e.target.value)} required /></div>
                <div><label style={labelStyle}>Kaedah Pelupusan</label>
                    <select style={inputStyle} value={data.disposal_method} onChange={e => setData('disposal_method', e.target.value)} required>
                        <option value="">— Pilih Kaedah —</option>
                        <option value="lelong">Lelong</option>
                        <option value="lupus">Lupus</option>
                        <option value="hibah">Hibah</option>
                        <option value="lain">Lain</option>
                    </select>
                </div>
                <div><label style={labelStyle}>Rujukan Kelulusan</label><input type="text" style={inputStyle} value={data.approval_reference} onChange={e => setData('approval_reference', e.target.value)} placeholder="No. rujukan..." /></div>
                <div><label style={labelStyle}>Status</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Sebab Permohonan</label><input type="text" style={inputStyle} value={data.request_reason} onChange={e => setData('request_reason', e.target.value)} placeholder="Sebab pelupusan..." /></div>
                <div><label style={labelStyle}>Keputusan Jawatankuasa</label><input type="text" style={inputStyle} value={data.committee_decision} onChange={e => setData('committee_decision', e.target.value)} placeholder="Keputusan..." /></div>
                <div><label style={labelStyle}>Catatan</label><input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan tambahan..." /></div>
            </div>
            {errors.asset_id && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.asset_id}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#92400E', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

export default function Kewpa17Index({ records, filters, assets }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');
    const [showAdd, setShowAdd] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('disposals.index'), { search, status: statusFilter }, { preserveState: true, replace: true });
    };

    const handleReset = () => {
        setSearch('');
        setStatusFilter('');
        router.get(route('disposals.index'), {}, { preserveState: true, replace: true });
    };

    const handleDelete = (record) => {
        if (window.confirm(`Padam pelupusan aset ${record.asset?.name || '—'} (${record.disposal_method || record.approval_reference || 'tiada rujukan'})?`)) {
            router.delete(route('assets.disposals.destroy', [record.asset_id, record.id]), { preserveScroll: true });
        }
    };

    const thStyle = {
        padding      : '10px 16px',
        fontSize     : 11,
        fontWeight   : 700,
        textTransform: 'uppercase',
        letterSpacing: '0.07em',
        color        : UTM.gray500,
        textAlign    : 'left',
        borderBottom : `1px solid ${UTM.gray100}`,
        background   : UTM.gray50,
        whiteSpace   : 'nowrap',
    };

    const tdStyle = {
        padding      : '12px 16px',
        fontSize     : 13,
        color        : UTM.gray900,
        borderBottom : `1px solid ${UTM.gray100}`,
    };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-17/18/19 — Laporan Pelupusan Aset" />

            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                {/* ── Header ─────────────────────────────────────────────── */}
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>
                        KEW.PA-17/18/19 — Laporan Pelupusan Aset
                    </h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                        Senarai pelupusan aset
                    </p>
                </div>

                {/* ── Search / Filter ─────────────────────────────────────── */}
                <form onSubmit={handleSearch} style={{
                    display        : 'flex',
                    gap            : 16,
                    alignItems     : 'flex-end',
                    marginBottom   : showAdd ? 12 : 24,
                    padding        : 20,
                    background     : UTM.white,
                    borderRadius   : 12,
                    border         : `1px solid ${UTM.gray100}`,
                    flexWrap       : 'wrap',
                }}>
                    <div style={{ flex: 1, minWidth: 200 }}>
                        <label style={labelStyle}>Carian</label>
                        <input
                            type="text"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Nama aset, tag, rujukan kelulusan..."
                            style={inputStyle}
                        />
                    </div>
                    <div style={{ minWidth: 150 }}>
                        <label style={labelStyle}>Status</label>
                        <select
                            value={statusFilter}
                            onChange={e => setStatusFilter(e.target.value)}
                            style={inputStyle}
                        >
                            <option value="">Semua</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <button type="submit" style={{
                        padding      : '9px 24px',
                        borderRadius : 8,
                        border       : 'none',
                        background   : UTM.maroon,
                        color        : UTM.white,
                        fontSize     : '13px',
                        fontWeight   : 700,
                        cursor       : 'pointer',
                        whiteSpace   : 'nowrap',
                    }}>
                        Cari
                    </button>
                    <button type="button" onClick={handleReset} style={{
                        padding      : '9px 24px',
                        borderRadius : 8,
                        border       : `1.5px solid ${UTM.gray100}`,
                        background   : UTM.white,
                        color        : UTM.gray700,
                        fontSize     : '13px',
                        fontWeight   : 600,
                        cursor       : 'pointer',
                        whiteSpace   : 'nowrap',
                    }}>
                        Reset
                    </button>
                    <Link
                        href={route('disposals.kewpa18')}
                        style={{ padding: '9px 20px', borderRadius: 8, border: `1.5px solid #7B1FA2`, background: UTM.white, color: '#7B1FA2', fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap', textDecoration: 'none', display: 'inline-flex', alignItems: 'center' }}
                    >
                        ◈ PA-18 Sijil Pemusnahan
                    </Link>
                    <Link
                        href={route('disposals.kewpa19')}
                        style={{ padding: '9px 20px', borderRadius: 8, border: `1.5px solid #C62828`, background: UTM.white, color: '#C62828', fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap', textDecoration: 'none', display: 'inline-flex', alignItems: 'center' }}
                    >
                        ◈ PA-19 Sijil Pelupusan
                    </Link>
                    <button type="button" onClick={() => { setShowAdd(!showAdd); setEditingId(null); }} style={{
                        padding      : '9px 24px',
                        borderRadius : 8,
                        border       : `1.5px solid ${showAdd ? UTM.maroon : UTM.gray100}`,
                        background   : showAdd ? UTM.maroon : UTM.white,
                        color        : showAdd ? UTM.white : UTM.maroon,
                        fontSize     : '13px',
                        fontWeight   : 700,
                        cursor       : 'pointer',
                        whiteSpace   : 'nowrap',
                    }}>
                        {showAdd ? 'Tutup' : '+ Pelupusan Baru'}
                    </button>
                </form>

                {/* ── Add Form ─────────────────────────────────────────────── */}
                {showAdd && (
                    <AddDisposalForm assets={assets} onDone={() => setShowAdd(false)} />
                )}

                {/* ── Table ──────────────────────────────────────────────── */}
                <div style={{
                    background   : UTM.white,
                    borderRadius : 12,
                    border       : `1px solid ${UTM.gray100}`,
                    overflow     : 'hidden',
                }}>
                    <div style={{ overflowX: 'auto', width: '100%' }}>
                        <table style={{ width: '100%', minWidth: '1050px', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr>
                                    <th style={thStyle}>Aset</th>
                                    <th style={thStyle}>Tag</th>
                                    <th style={thStyle}>Kaedah Pelupusan</th>
                                    <th style={thStyle}>Rujukan Kelulusan</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={thStyle}>Tarikh</th>
                                    <th style={thStyle}>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {records.data.length === 0 && !showAdd ? (
                                    <tr>
                                        <td colSpan={7} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>
                                            Tiada rekod pelupusan aset.
                                        </td>
                                    </tr>
                                ) : records.data.map((record, idx) => (
                                    editingId === record.id ? (
                                        <tr key={record.id}>
                                            <td colSpan={7} style={{ padding: 0, background: '#FAFAFA' }}>
                                                <EditDisposalForm record={record} onDone={() => setEditingId(null)} />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={record.id} style={{
                                            background: idx % 2 === 0 ? UTM.white : UTM.gray50,
                                            transition: 'background 0.12s',
                                        }}
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
                                            <td style={tdStyle}>
                                                {record.disposal_method === 'lelong' ? 'Lelong' :
                                                 record.disposal_method === 'lupus' ? 'Lupus' :
                                                 record.disposal_method === 'hibah' ? 'Hibah' :
                                                 record.disposal_method === 'lain' ? 'Lain' :
                                                 record.disposal_method || '—'}
                                            </td>
                                            <td style={tdStyle}>{record.approval_reference || '—'}</td>
                                            <td style={tdStyle}><StatusBadge status={record.status} /></td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                {record.disposal_date
                                                    ? new Date(record.disposal_date).toLocaleDateString('ms-MY')
                                                    : record.created_at
                                                        ? new Date(record.created_at).toLocaleDateString('ms-MY')
                                                        : '—'}
                                            </td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                <Link href={route('assets.kewpa2', record.asset_id)}
                                                    style={{
                                                        display       : 'inline-block',
                                                        padding       : '4px 10px',
                                                        borderRadius  : 4,
                                                        fontSize      : 10,
                                                        fontWeight    : 600,
                                                        background    : '#EDE9E4',
                                                        color         : UTM.gray700,
                                                        textDecoration: 'none',
                                                        marginRight   : 4,
                                                    }}>
                                                    Borang
                                                </Link>
                                                <a href={route('assets.disposals.kewpa17.download', record.asset_id)}
                                                    style={{
                                                        display       : 'inline-block',
                                                        padding       : '4px 10px',
                                                        borderRadius  : 4,
                                                        fontSize      : 10,
                                                        fontWeight    : 600,
                                                        background    : UTM.maroon,
                                                        color         : UTM.white,
                                                        textDecoration: 'none',
                                                        marginRight   : 4,
                                                    }}>
                                                    KEW.PA-17
                                                </a>
                                                <button onClick={() => setEditingId(record.id)}
                                                    style={{
                                                        padding      : '4px 10px',
                                                        borderRadius : 4,
                                                        fontSize     : 10,
                                                        fontWeight   : 600,
                                                        background   : '#FEF3C7',
                                                        color        : '#92400E',
                                                        border       : 'none',
                                                        cursor       : 'pointer',
                                                        marginRight  : 4,
                                                    }}>
                                                    Edit
                                                </button>
                                                <button onClick={() => handleDelete(record)}
                                                    style={{
                                                        padding    : '4px 10px',
                                                        borderRadius: 4,
                                                        fontSize   : 10,
                                                        fontWeight : 600,
                                                        background : '#FEF2F2',
                                                        color      : '#DC2626',
                                                        border     : 'none',
                                                        cursor     : 'pointer',
                                                    }}>
                                                    Padam
                                                </button>
                                            </td>
                                        </tr>
                                    )
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {records.data.length > 0 && (
                        <div style={{ padding: '10px 16px', borderTop: `1px solid ${UTM.gray100}`, background: UTM.gray50, fontSize: 12, color: UTM.gray500 }}>
                            Menunjukkan <strong style={{ color: UTM.maroon }}>{records.data.length}</strong> daripada <strong style={{ color: UTM.maroon }}>{records.total}</strong> rekod
                        </div>
                    )}
                </div>

                {/* ── Pagination ─────────────────────────────────────────── */}
                {records.links && (
                    <div style={{
                        display        : 'flex',
                        justifyContent : 'center',
                        gap            : 6,
                        marginTop      : 24,
                        flexWrap       : 'wrap',
                    }}>
                        {records.links.map((link, i) => {
                            if (!link.url) return null;
                            const active = link.active;
                            return (
                                <Link
                                    key={i}
                                    href={link.url}
                                    preserveState
                                    replace
                                    style={{
                                        padding      : '8px 14px',
                                        borderRadius : 8,
                                        border       : `1px solid ${active ? UTM.maroon : UTM.gray100}`,
                                        background   : active ? UTM.maroon : UTM.white,
                                        color        : active ? UTM.white : UTM.gray700,
                                        fontSize     : '12px',
                                        fontWeight   : 600,
                                        textDecoration: 'none',
                                        cursor       : 'pointer',
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
