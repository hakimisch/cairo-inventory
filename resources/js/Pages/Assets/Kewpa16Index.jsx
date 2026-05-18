import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

function StatusBadge({ status }) {
    const map = {
        draft    : { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Draft' },
        submitted: { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Submitted' },
        approved : { bg: '#DCE8F5', color: '#1A4A7A',    border: '#B2C8DF', label: 'Approved' },
        rejected : { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Rejected' },
    };
    const s = map[status] || map.draft;
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '3px 10px',
            borderRadius : '999px',
            fontSize     : '11px',
            fontWeight   : 700,
            background   : s.bg,
            color        : s.color,
            border       : `1px solid ${s.border}`,
            whiteSpace   : 'nowrap',
        }}>
            {s.label}
        </span>
    );
}

const inputStyle = {
    width       : '100%',
    padding     : '9px 12px',
    borderRadius: 8,
    border      : `1.5px solid ${UTM.gray100}`,
    fontSize    : '13px',
    color       : UTM.gray900,
    background  : UTM.white,
    outline     : 'none',
    boxSizing   : 'border-box',
};

const labelStyle = {
    display      : 'block',
    fontSize     : '11px',
    fontWeight   : 700,
    textTransform: 'uppercase',
    letterSpacing: '0.07em',
    color        : UTM.gray500,
    marginBottom : 5,
};

export default function Kewpa16Index({ records, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('vehicle-disposals.index'), { search, status: statusFilter }, { preserveState: true, replace: true });
    };

    const handleReset = () => {
        setSearch('');
        setStatusFilter('');
        router.get(route('vehicle-disposals.index'), {}, { preserveState: true, replace: true });
    };

    const thStyle = {
        padding      : '12px 20px',
        fontSize     : '11px',
        fontWeight   : 700,
        textTransform: 'uppercase',
        letterSpacing: '0.07em',
        color        : UTM.gray500,
        textAlign    : 'left',
        borderBottom : `1px solid ${UTM.gray100}`,
        whiteSpace   : 'nowrap',
    };

    const tdStyle = {
        padding      : '14px 20px',
        fontSize     : '13px',
        color        : UTM.gray900,
        borderBottom : `1px solid ${UTM.gray100}`,
    };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-16 — Perakuan Pelupusan Kenderaan" />

            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                {/* ── Header ─────────────────────────────────────────────── */}
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>
                        KEW.PA-16 — Perakuan Pelupusan Kenderaan
                    </h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                        Senarai penilaian pelupusan kenderaan
                    </p>
                </div>

                {/* ── Search / Filter ─────────────────────────────────────── */}
                <form onSubmit={handleSearch} style={{
                    display        : 'flex',
                    gap            : 16,
                    alignItems     : 'flex-end',
                    marginBottom   : 24,
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
                            placeholder="Nama aset, tag, no. plat, penilai..."
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
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
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
                </form>

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
                            <tr style={{ background: UTM.gray50 }}>
                                <th style={thStyle}>Aset</th>
                                <th style={thStyle}>Tag</th>
                                <th style={thStyle}>No. Plat</th>
                                <th style={thStyle}>Penilai</th>
                                <th style={thStyle}>Nilai Anggaran</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Tarikh</th>
                                <th style={thStyle}></th>
                            </tr>
                        </thead>
                        <tbody>
                            {records.data.length === 0 ? (
                                <tr>
                                    <td colSpan={8} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>
                                        Tiada rekod penilaian pelupusan kenderaan.
                                    </td>
                                </tr>
) : records.data.map((record, idx) => (
                                                <tr key={record.id} style={{
                                                    background: idx % 2 === 0 ? UTM.white : UTM.gray50,
                                                    transition: 'background 0.12s',
                                                }}
                                                    onMouseEnter={e => { e.currentTarget.style.background = '#FFF5E8'; }}
                                                    onMouseLeave={e => { e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}
                                >
                                    <td style={tdStyle}>
                                        <Link href={route('assets.vehicle-disposal.index', record.asset_id)}
                                            style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                            {record.asset?.name || '—'}
                                        </Link>
                                    </td>
                                    <td style={tdStyle}>
                                        <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>
                                            {record.asset?.asset_tag || '—'}
                                        </span>
                                    </td>
                                    <td style={tdStyle}>{record.plate_no || '—'}</td>
                                    <td style={tdStyle}>{record.assessor_name || '—'}</td>
                                    <td style={tdStyle}>
                                        {record.estimated_value
                                            ? `RM ${Number(record.estimated_value).toLocaleString('ms-MY')}`
                                            : '—'}
                                    </td>
                                    <td style={tdStyle}><StatusBadge status={record.status} /></td>
                                    <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                        {record.created_at ? new Date(record.created_at).toLocaleDateString('ms-MY') : '—'}
                                    </td>
                                    <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                        <Link href={route('assets.vehicle-disposal.kewpa16', record.asset_id)}
                                            style={{
                                                display       : 'inline-block',
                                                padding       : '5px 12px',
                                                borderRadius  : 6,
                                                fontSize      : '11px',
                                                fontWeight    : 600,
                                                background    : '#EDE9E4',
                                                color         : UTM.gray700,
                                                textDecoration: 'none',
                                                marginRight   : 6,
                                            }}>
                                            Borang
                                        </Link>
                                        <a href={route('assets.vehicle-disposal.kewpa16.download', record.asset_id)}
                                            style={{
                                                display       : 'inline-block',
                                                padding       : '5px 12px',
                                                borderRadius  : 6,
                                                fontSize      : '11px',
                                                fontWeight    : 600,
                                                background    : UTM.maroon,
                                                color         : UTM.white,
                                                textDecoration: 'none',
                                            }}>
                                            KEW.PA-16
                                        </a>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
</div>
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
