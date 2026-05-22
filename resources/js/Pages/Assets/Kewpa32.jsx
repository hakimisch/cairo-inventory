import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

const UTM = {
    maroon:  '#5C001F', gold: '#F8A617', goldDark: '#C9840A', sand: '#FFF5AB',
    white:   '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
};

const actionLabels = {
    gantian_setara: 'Gantian Setara',
    surcharge:      'Surcaj',
    write_off:      'Hapuskira',
    hapuskira:      'Hapuskira',
};

const actionColors = {
    gantian_setara: { bg: '#E6F4EC', color: '#1A7A3C' },
    surcharge:      { bg: '#DBEAFE', color: '#1E40AF' },
    write_off:      { bg: '#FEF3D6', color: '#C9840A' },
    hapuskira:      { bg: '#FEF3D6', color: '#C9840A' },
};

function ActionBadge({ actionType }) {
    const s = actionColors[actionType] || { bg: '#E5E7EB', color: '#4B5563' };
    const label = actionLabels[actionType] || (actionType ? actionType.replace(/_/g, ' ') : '—');
    return <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: 999, fontSize: 10, fontWeight: 700, background: s.bg, color: s.color, border: `1px solid ${s.bg}` }}>{label}</span>;
}

function SummaryCard({ label, value, bg, color }) {
    return (
        <div style={{ flex: '1 1 180px', background: bg || UTM.white, borderRadius: 8, border: `1px solid ${UTM.gray100}`, padding: '14px 16px' }}>
            <p style={{ fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, margin: 0 }}>{label}</p>
            <p style={{ fontSize: 24, fontWeight: 800, color: color || UTM.maroon, margin: '4px 0 0 0' }}>{value}</p>
        </div>
    );
}

export default function Kewpa32({ lossReports, summary, year, years }) {
    const handleYearChange = (e) => {
        router.get(route('loss-reports.kewpa32'), { year: e.target.value }, { preserveState: true, replace: true });
    };

    const thStyle = { padding: '10px 14px', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, textAlign: 'left', borderBottom: `1px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const tdStyle = { padding: '12px 14px', fontSize: 13, color: UTM.gray900, borderBottom: `1px solid ${UTM.gray100}` };

    const byAction = summary?.by_action || {};
    const actionEntries = Object.entries(byAction);
    const totalValue = parseFloat(summary?.total_value || 0);
    const totalReports = summary?.total_reports || 0;

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-32 - Laporan Tindakan Kehilangan" />
            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                {/* Header */}
                <div style={{ marginBottom: 28, display: 'flex', alignItems: 'flex-start', justifyContent: 'space-between', flexWrap: 'wrap', gap: 12 }}>
                    <div>
                        <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>KEW.PA-32 — Laporan Tindakan Kehilangan</h1>
                        <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>Laporan tindakan kehilangan aset alih universiti tahunan</p>
                    </div>
                    <div style={{ display: 'flex', gap: 8, alignItems: 'flex-end' }}>
                        <div>
                            <label style={{ display: 'block', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 3 }}>Tahun</label>
                            <select value={year} onChange={handleYearChange}
                                style={{ padding: '7px 10px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, fontSize: 12, color: UTM.gray700, background: UTM.white, outline: 'none', minWidth: 100 }}>
                                {years.map(y => <option key={y} value={y}>{y}</option>)}
                            </select>
                        </div>
                        <a href={route('loss-reports.kewpa32.download', { year })}
                            style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.maroon, color: UTM.white, fontSize: 12, fontWeight: 700, textDecoration: 'none', cursor: 'pointer', whiteSpace: 'nowrap' }}>
                            Download PDF
                        </a>
                    </div>
                </div>

                {/* Summary Cards */}
                <div style={{ display: 'flex', gap: 12, marginBottom: 24, flexWrap: 'wrap' }}>
                    <SummaryCard label="Jumlah Laporan" value={totalReports} />
                    <SummaryCard label="Jumlah Nilai" value={`RM ${totalValue.toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`} />
                    {actionEntries.map(([actionType, data]) => {
                        const c = actionColors[actionType] || { bg: '#E5E7EB', color: '#4B5563' };
                        return (
                            <SummaryCard
                                key={actionType}
                                label={actionLabels[actionType] || actionType.replace(/_/g, ' ')}
                                value={data.count}
                                bg={c.bg}
                                color={c.color}
                            />
                        );
                    })}
                </div>

                {/* Detail Table */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', minWidth: '1050px', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ background: UTM.gray50 }}>
                                    <th style={thStyle}>Bil.</th>
                                    <th style={thStyle}>Ruj. Kelulusan</th>
                                    <th style={thStyle}>Fakulti/Jabatan</th>
                                    <th style={thStyle}>Jenis Aset</th>
                                    <th style={thStyle}>No. Pendaftaran</th>
                                    <th style={{ ...thStyle, textAlign: 'right' }}>Nilai Perolehan (RM)</th>
                                    <th style={{ ...thStyle, textAlign: 'right' }}>Nilai Semasa (RM)</th>
                                    <th style={thStyle}>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {lossReports.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>
                                            Tiada laporan kehilangan untuk tahun {year}.
                                        </td>
                                    </tr>
                                ) : lossReports.map((record, idx) => (
                                    <tr key={record.id}
                                        style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50, transition: 'background 0.12s' }}
                                        onMouseEnter={e => { e.currentTarget.style.background = '#FFF5E8'; }}
                                        onMouseLeave={e => { e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}>
                                        <td style={{ ...tdStyle, textAlign: 'center' }}>{idx + 1}.</td>
                                        <td style={tdStyle}>{record.approval_reference || '—'}</td>
                                        <td style={tdStyle}>{record.asset?.location || '—'}</td>
                                        <td style={tdStyle}>
                                            <Link href={route('assets.kewpa3', record.asset_id)} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                {record.asset?.name || '—'}
                                            </Link>
                                        </td>
                                        <td style={tdStyle}><span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>{record.asset?.asset_tag || '—'}</span></td>
                                        <td style={{ ...tdStyle, textAlign: 'right' }}>
                                            {record.asset?.purchase_price
                                                ? `RM ${parseFloat(record.asset.purchase_price).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                                : '—'}
                                        </td>
                                        <td style={{ ...tdStyle, textAlign: 'right' }}>
                                            {record.current_value
                                                ? `RM ${parseFloat(record.current_value).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                                : '—'}
                                        </td>
                                        <td style={tdStyle}><ActionBadge actionType={record.action_type} /></td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div style={{ padding: '10px 16px', borderTop: `1px solid ${UTM.gray100}`, background: UTM.gray50, fontSize: 12, color: UTM.gray500 }}>
                        Menunjukkan <strong style={{ color: UTM.maroon }}>{lossReports.length}</strong> rekod untuk tahun <strong style={{ color: UTM.maroon }}>{year}</strong>
                    </div>
                </div>

                <div style={{ marginTop: 16, display: 'flex', gap: 8 }}>
                    <Link href={route('loss-reports.index')}
                        style={{ display: 'inline-block', padding: '7px 20px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}>
                        ← Kembali ke Senarai Kehilangan
                    </Link>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
