import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const UTM = {
    maroon : '#5C001F', gold: '#F8A617', goldDark:'#C9840A',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
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

const methodBadge = (method) => {
    const colors = {
        Tanam     : { bg: '#E6F4EC', color: '#1A7A3C' },
        Bakar     : { bg: '#FEF3D6', color: '#C9840A' },
        Tenggelam : { bg: '#E0F2FE', color: '#0369A1' },
        Jualan    : { bg: '#F3E8FF', color: '#7C3AED' },
        Pindahan  : { bg: '#FCE7F3', color: '#BE185D' },
    };
    const c = colors[method] || { bg: UTM.gray100, color: UTM.gray700 };
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '2px 10px',
            borderRadius : 4,
            fontSize     : 11,
            fontWeight   : 600,
            background   : c.bg,
            color        : c.color,
            whiteSpace   : 'nowrap',
        }}>
            {method}
        </span>
    );
};

export default function Kewpa19({ disposals }) {
    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-19 — Sijil Pelupusan" />

            <div style={{ maxWidth: 1100, margin: '0 auto', padding: '32px 24px' }}>
                {/* ── Header ─────────────────────────────────────────────── */}
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>
                        KEW.PA-19 — Sijil Pelupusan
                    </h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                        Fixed Asset Disposal Certificate — Semua rekod pelupusan aset tetap
                    </p>
                </div>

                {/* ── Back Link ───────────────────────────────────────────── */}
                <Link
                    href={route('disposals.index')}
                    style={{
                        display       : 'inline-flex',
                        alignItems    : 'center',
                        gap           : 6,
                        marginBottom  : 20,
                        padding       : '8px 16px',
                        borderRadius  : 8,
                        border        : `1px solid ${UTM.gray100}`,
                        background    : UTM.white,
                        color         : UTM.gray700,
                        fontSize      : 12,
                        fontWeight    : 600,
                        textDecoration: 'none',
                    }}
                >
                    &larr; Kembali ke Senarai Pelupusan
                </Link>

                {/* ── Table ──────────────────────────────────────────────── */}
                <div style={{
                    background   : UTM.white,
                    borderRadius : 12,
                    border       : `1px solid ${UTM.gray100}`,
                    overflow     : 'hidden',
                }}>
                    <div style={{ overflowX: 'auto', width: '100%' }}>
                        <table style={{ width: '100%', minWidth: 900, borderCollapse: 'collapse' }}>
                            <thead>
                                <tr>
                                    <th style={thStyle}>Bil</th>
                                    <th style={thStyle}>Aset</th>
                                    <th style={thStyle}>Tag</th>
                                    <th style={thStyle}>No. Siri</th>
                                    <th style={thStyle}>Kaedah Pelupusan</th>
                                    <th style={thStyle}>Rujukan Kelulusan</th>
                                    <th style={thStyle}>Tarikh</th>
                                    <th style={thStyle}>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {disposals.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>
                                            Tiada rekod pelupusan aset.
                                        </td>
                                    </tr>
                                ) : disposals.map((disposal, idx) => (
                                    <tr key={disposal.id} style={{
                                        background: idx % 2 === 0 ? UTM.white : UTM.gray50,
                                    }}>
                                        <td style={tdStyle}>{idx + 1}</td>
                                        <td style={tdStyle}>
                                            <span style={{ fontWeight: 600, color: UTM.maroon }}>
                                                {disposal.asset?.name || '—'}
                                            </span>
                                        </td>
                                        <td style={tdStyle}>
                                            <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>
                                                {disposal.asset?.asset_tag || '—'}
                                            </span>
                                        </td>
                                        <td style={tdStyle}>
                                            {disposal.asset?.serial_number || '—'}
                                        </td>
                                        <td style={tdStyle}>
                                            {methodBadge(disposal.disposal_method)}
                                        </td>
                                        <td style={tdStyle}>
                                            {disposal.approval_reference || '—'}
                                        </td>
                                        <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                            {disposal.disposal_date
                                                ? new Date(disposal.disposal_date).toLocaleDateString('ms-MY')
                                                : '—'}
                                        </td>
                                        <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                            <a
                                                href={route('disposals.kewpa19.download', disposal.id)}
                                                style={{
                                                    display       : 'inline-block',
                                                    padding       : '4px 12px',
                                                    borderRadius  : 4,
                                                    fontSize      : 10,
                                                    fontWeight    : 700,
                                                    background    : UTM.maroon,
                                                    color         : UTM.white,
                                                    textDecoration: 'none',
                                                    whiteSpace    : 'nowrap',
                                                    marginRight   : 4,
                                                }}
                                            >
                                                PDF KEW.PA-19
                                            </a>
                                            <a
                                                href={route('disposals.kewpa15.download', disposal.id)}
                                                style={{
                                                    display       : 'inline-block',
                                                    padding       : '4px 12px',
                                                    borderRadius  : 4,
                                                    fontSize      : 10,
                                                    fontWeight    : 700,
                                                    background    : '#1E40AF',
                                                    color         : UTM.white,
                                                    textDecoration: 'none',
                                                    whiteSpace    : 'nowrap',
                                                }}
                                            >
                                                PA-15 JK
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div style={{
                        padding   : '10px 16px',
                        borderTop : `1px solid ${UTM.gray100}`,
                        background: UTM.gray50,
                        fontSize  : 12,
                        color     : UTM.gray500,
                    }}>
                        Menunjukkan <strong style={{ color: UTM.maroon }}>{disposals.length}</strong> rekod pelupusan
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
