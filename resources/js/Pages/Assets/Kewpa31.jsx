import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const UTM = {
    maroon:  '#5C001F', gold: '#F8A617', goldDark: '#C9840A', sand: '#FFF5AB',
    white:   '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
};

function ActionBadge({ actionType }) {
    const map = {
        write_off: { bg: '#FEF3D6', color: UTM.goldDark, label: 'Hapuskira' },
        surcharge: { bg: '#DBEAFE', color: '#1E40AF', label: 'Surcaj' },
    };
    const s = map[actionType] || { bg: '#E5E7EB', color: '#4B5563', label: actionType ?? '—' };
    return <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: 999, fontSize: 10, fontWeight: 700, background: s.bg, color: s.color, border: `1px solid ${s.bg}` }}>{s.label}</span>;
}

export default function Kewpa31({ lossReports }) {
    const thStyle = { padding: '10px 14px', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, textAlign: 'left', borderBottom: `1px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const tdStyle = { padding: '12px 14px', fontSize: 13, color: UTM.gray900, borderBottom: `1px solid ${UTM.gray100}` };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-31 - Sijil Hapuskira" />
            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>KEW.PA-31 — Sijil Hapuskira</h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>Senarai laporan hapuskira aset yang telah diluluskan</p>
                </div>

                {/* Summary card */}
                <div style={{ display: 'flex', gap: 12, marginBottom: 20, flexWrap: 'wrap' }}>
                    <div style={{ flex: '1 1 200px', background: UTM.white, borderRadius: 8, border: `1px solid ${UTM.gray100}`, padding: '14px 16px' }}>
                        <p style={{ fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, margin: 0 }}>Jumlah Hapuskira</p>
                        <p style={{ fontSize: 24, fontWeight: 800, color: UTM.maroon, margin: '4px 0 0 0' }}>{lossReports.length}</p>
                    </div>
                    <div style={{ flex: '1 1 200px', background: UTM.white, borderRadius: 8, border: `1px solid ${UTM.gray100}`, padding: '14px 16px' }}>
                        <p style={{ fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, margin: 0 }}>Jumlah Nilai Hapuskira</p>
                        <p style={{ fontSize: 24, fontWeight: 800, color: UTM.maroon, margin: '4px 0 0 0' }}>
                            RM {lossReports.reduce((sum, r) => sum + parseFloat(r.write_off_value || 0), 0).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                        </p>
                    </div>
                </div>

                {/* Table */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', minWidth: '900px', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ background: UTM.gray50 }}>
                                    <th style={thStyle}>Aset</th>
                                    <th style={thStyle}>No. Tag</th>
                                    <th style={thStyle}>Nilai Semasa</th>
                                    <th style={thStyle}>Nilai Hapuskira</th>
                                    <th style={thStyle}>Rujukan Kelulusan</th>
                                    <th style={thStyle}>Tindakan</th>
                                    <th style={thStyle}></th>
                                </tr>
                            </thead>
                            <tbody>
                                {lossReports.length === 0 ? (
                                    <tr>
                                        <td colSpan={7} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>Tiada rekod hapuskira.</td>
                                    </tr>
                                ) : lossReports.map((record, idx) => (
                                    <tr key={record.id}
                                        style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50, transition: 'background 0.12s' }}
                                        onMouseEnter={e => { e.currentTarget.style.background = '#FFF5E8'; }}
                                        onMouseLeave={e => { e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}>
                                        <td style={tdStyle}>
                                            <Link href={route('assets.kewpa3', record.asset_id)} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                {record.asset?.name || '—'}
                                            </Link>
                                        </td>
                                        <td style={tdStyle}><span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>{record.asset?.asset_tag || '—'}</span></td>
                                        <td style={{ ...tdStyle, textAlign: 'right' }}>RM {parseFloat(record.current_value || 0).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                        <td style={{ ...tdStyle, textAlign: 'right' }}>RM {parseFloat(record.write_off_value || 0).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                        <td style={tdStyle}>{record.approval_reference || '—'}</td>
                                        <td style={tdStyle}><ActionBadge actionType={record.action_type} /></td>
                                        <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                            <a href={route('loss-reports.kewpa31.download', record.id)}
                                                style={{ display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: UTM.maroon, color: UTM.white, textDecoration: 'none' }}>
                                                PDF
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div style={{ padding: '10px 16px', borderTop: `1px solid ${UTM.gray100}`, background: UTM.gray50, fontSize: 12, color: UTM.gray500 }}>
                        Menunjukkan <strong style={{ color: UTM.maroon }}>{lossReports.length}</strong> rekod hapuskira
                    </div>
                </div>

                <div style={{ marginTop: 16 }}>
                    <Link href={route('loss-reports.index')}
                        style={{ display: 'inline-block', padding: '7px 20px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                        ← Kembali ke Senarai Kehilangan
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
