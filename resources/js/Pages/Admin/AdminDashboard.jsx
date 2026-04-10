import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import {
    Chart as ChartJS,
    ArcElement, Tooltip, Legend,
    CategoryScale, LinearScale, BarElement,
} from 'chart.js';
import { Pie, Bar } from 'react-chartjs-2';
 
ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement);
 
// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon  : '#5C001F',
    maroon2 : '#7A0029',
    gold    : '#F8A617',
    goldDark: '#C9840A',
    sand    : '#FFF5AB',
    sandMid : '#FAE88A',
    white   : '#FFFFFF',
    gray50  : '#F9F7F5',
    gray100 : '#EDE9E4',
    gray300 : '#C5BFB8',
    gray500 : '#8A8480',
    gray700 : '#4A4540',
    gray900 : '#1E1B18',
};
 
const CAMPUS_COLORS = { utm_kl: UTM.maroon, utm_jb: UTM.gold, other: UTM.gray300 };
const CAMPUS_LABELS = { utm_kl: 'UTM Kuala Lumpur', utm_jb: 'UTM Johor Bahru', other: 'Lain-lain' };
 
// ─── Tiny reusables ───────────────────────────────────────────────────────────
function SectionTitle({ children }) {
    return (
        <p style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase',
                    letterSpacing: '0.08em', color: UTM.maroon, marginBottom: 14,
                    display: 'flex', alignItems: 'center', gap: 8 }}>
            <span style={{ display: 'inline-block', width: 3, height: 14,
                           background: UTM.gold, borderRadius: 2 }} />
            {children}
        </p>
    );
}
 
function Card({ children, style = {} }) {
    return (
        <div style={{ background: UTM.white, borderRadius: 12,
                      boxShadow: '0 1px 4px rgba(92,0,31,0.07)',
                      padding: '22px 24px', ...style }}>
            {children}
        </div>
    );
}
 
function StatCard({ label, value, sub, accent, dark }) {
    return (
        <div style={{ background: dark ? UTM.maroon : UTM.white,
                      borderLeft: `4px solid ${accent || UTM.gold}`,
                      borderRadius: 10, padding: '18px 22px',
                      boxShadow: '0 1px 4px rgba(92,0,31,0.08)' }}>
            <p style={{ fontSize: '10px', fontWeight: 700, letterSpacing: '0.08em',
                        textTransform: 'uppercase', color: dark ? UTM.sand : UTM.gray500,
                        marginBottom: 4 }}>{label}</p>
            <p style={{ fontSize: '24px', fontWeight: 900, lineHeight: 1.1,
                        color: dark ? UTM.white : UTM.maroon,
                        marginBottom: sub ? 3 : 0 }}>{value}</p>
            {sub && <p style={{ fontSize: '12px', color: dark ? UTM.sand : UTM.gray500 }}>{sub}</p>}
        </div>
    );
}
 
function TypePill({ type }) {
    const f = type === 'fixed_asset';
    return (
        <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: '999px',
                       fontSize: '10px', fontWeight: 700,
                       background: f ? '#F3E0E5' : '#FEF3D6',
                       color: f ? UTM.maroon : UTM.goldDark,
                       border: `1px solid ${f ? '#E8C0CB' : '#F5D890'}` }}>
            {f ? 'Tetap' : 'Inv'}
        </span>
    );
}
 
function StatusDot({ status }) {
    const c = { active: '#1A7A3C', repair: UTM.goldDark, disposed: UTM.maroon };
    return (
        <span style={{ display: 'inline-block', width: 7, height: 7, borderRadius: '50%',
                       background: c[status] || UTM.gray300, marginRight: 4, flexShrink: 0 }} />
    );
}
 
function CampusCard({ campusKey, data }) {
    const color = CAMPUS_COLORS[campusKey];
    const pct = data.count === 0 ? 0 : Math.round((data.fixed_count / data.count) * 100);
    return (
        <Card style={{ borderTop: `3px solid ${color}`, padding: '18px 20px' }}>
            <div style={{ display: 'flex', justifyContent: 'space-between',
                          alignItems: 'flex-start', marginBottom: 12 }}>
                <div>
                    <p style={{ fontSize: '13px', fontWeight: 800, color: UTM.gray900,
                                marginBottom: 2 }}>{data.label}</p>
                    <p style={{ fontSize: '11px', color: UTM.gray500 }}>
                        {data.count} aset · {data.active_count} aktif
                    </p>
                </div>
                <p style={{ fontSize: '14px', fontWeight: 900, color, whiteSpace: 'nowrap' }}>
                    RM {Number(data.value).toLocaleString()}
                </p>
            </div>
 
            {/* Fixed vs Inventory bar */}
            <div style={{ marginBottom: 12 }}>
                <div style={{ display: 'flex', justifyContent: 'space-between',
                              fontSize: '11px', color: UTM.gray500, marginBottom: 4 }}>
                    <span>Tetap: {data.fixed_count}</span>
                    <span>Inventori: {data.inventory_count}</span>
                </div>
                <div style={{ height: 5, borderRadius: 3, background: UTM.gray100, overflow: 'hidden' }}>
                    <div style={{ height: '100%', width: `${pct}%`, background: UTM.maroon,
                                  borderRadius: 3, transition: 'width 0.5s ease' }} />
                </div>
            </div>
 
            {/* Top assets */}
            {data.top_assets.length > 0 ? (
                <div style={{ borderTop: `1px solid ${UTM.gray100}`, paddingTop: 10 }}>
                    <p style={{ fontSize: '10px', fontWeight: 700, color: UTM.gray500,
                                textTransform: 'uppercase', letterSpacing: '0.06em',
                                marginBottom: 8 }}>Aset Utama</p>
                    {data.top_assets.map(a => (
                        <div key={a.id} style={{ display: 'flex', justifyContent: 'space-between',
                                                  alignItems: 'center', marginBottom: 6 }}>
                            <div style={{ display: 'flex', alignItems: 'center', gap: 5, minWidth: 0 }}>
                                <StatusDot status={a.status} />
                                <span style={{ fontSize: '12px', color: UTM.gray700, fontWeight: 600,
                                               overflow: 'hidden', textOverflow: 'ellipsis',
                                               whiteSpace: 'nowrap', maxWidth: 140 }}>{a.name}</span>
                                <TypePill type={a.asset_type} />
                            </div>
                            <span style={{ fontSize: '11px', fontWeight: 700, color,
                                           whiteSpace: 'nowrap', marginLeft: 6 }}>
                                RM {Number(a.purchase_price).toLocaleString()}
                            </span>
                        </div>
                    ))}
                </div>
            ) : (
                <p style={{ fontSize: '12px', color: UTM.gray300, fontStyle: 'italic',
                             textAlign: 'center', padding: '8px 0' }}>
                    Tiada aset direkodkan
                </p>
            )}
        </Card>
    );
}
 
// ─── Main Dashboard ───────────────────────────────────────────────────────────
export default function Dashboard({ auth, stats, chartData, assetTypeChart,
                                    campusChart, campusStats, highValueAssets }) {
    const PIE_OPTS = {
        plugins: {
            legend: {
                position: 'bottom',
                labels: { font: { size: 11, weight: '600' }, color: UTM.gray700, padding: 12 },
            },
        },
    };
 
    const CHART_COLORS = [UTM.maroon, UTM.gold, UTM.maroon2, UTM.goldDark, '#A0001A', '#E8C85A'];
 
    const categoryPieData = {
        labels: chartData.map(i => i.category),
        datasets: [{ data: chartData.map(i => i.total),
                     backgroundColor: CHART_COLORS, borderColor: UTM.white, borderWidth: 2 }],
    };
 
    const typeLabels = { fixed_asset: 'Aset Tetap', inventory: 'Inventori' };
    const typePieData = {
        labels: assetTypeChart.map(i => typeLabels[i.asset_type] ?? i.asset_type),
        datasets: [{ data: assetTypeChart.map(i => i.total),
                     backgroundColor: [UTM.maroon, UTM.gold], borderColor: UTM.white, borderWidth: 2 }],
    };
 
    const campusBarData = {
        labels: campusChart.map(i => i.label),
        datasets: [{
            label: 'Nilai (RM)',
            data: campusChart.map(i => i.total),
            backgroundColor: campusChart.map(i => CAMPUS_COLORS[i.campus] ?? UTM.gray300),
            borderRadius: 6,
            borderSkipped: false,
        }],
    };
    const campusBarOpts = {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false },
                 ticks: { color: UTM.gray500, font: { size: 12, weight: '600' } } },
            y: { grid: { color: UTM.gray100 },
                 ticks: { color: UTM.gray500, font: { size: 11 },
                          callback: v => 'RM ' + Number(v).toLocaleString() } },
        },
    };
 
    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                    <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                        System Overview
                    </h2>
                </div>
            }
        >
            <Head title="Dashboard" />
 
            <div style={{ padding: '28px', maxWidth: 1400, margin: '0 auto' }}>
 
                {/* ── Stat cards ── */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(190px, 1fr))',
                              gap: 14, marginBottom: 22 }}>
                    <StatCard label="Jumlah Nilai Aset"
                        value={`RM ${Number(stats.total_value).toLocaleString()}`}
                        accent={UTM.gold} dark />
                    <StatCard label="Jumlah Aset" value={stats.total_count}
                        sub={`${stats.active_count} aktif · ${stats.repair_count} selenggara`}
                        accent={UTM.gold} />
                    <StatCard label="Aset Tetap" value={stats.fixed_asset_count}
                        sub={`RM ${Number(stats.fixed_asset_value).toLocaleString()}`}
                        accent={UTM.maroon} />
                    <StatCard label="Inventori" value={stats.inventory_count}
                        sub={`RM ${Number(stats.inventory_value).toLocaleString()}`}
                        accent={UTM.gold} />
                </div>
 
                {/* ── Row 1: Charts + welcome ── */}
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1.6fr',
                              gap: 16, marginBottom: 16 }}>
 
                    <Card>
                        <SectionTitle>Nilai Mengikut Kategori</SectionTitle>
                        <Pie data={categoryPieData} options={PIE_OPTS} />
                    </Card>
 
                    <Card>
                        <SectionTitle>Jenis Aset</SectionTitle>
                        <Pie data={typePieData} options={PIE_OPTS} />
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr',
                                      gap: 8, marginTop: 14 }}>
                            <div style={{ background: '#F3E0E5', borderRadius: 8, padding: '10px 12px' }}>
                                <p style={{ fontSize: '10px', fontWeight: 700, color: UTM.maroon,
                                            textTransform: 'uppercase', marginBottom: 2 }}>Aset Tetap</p>
                                <p style={{ fontSize: '22px', fontWeight: 900, color: UTM.maroon }}>
                                    {stats.fixed_asset_count}
                                </p>
                            </div>
                            <div style={{ background: '#FEF3D6', borderRadius: 8, padding: '10px 12px' }}>
                                <p style={{ fontSize: '10px', fontWeight: 700, color: UTM.goldDark,
                                            textTransform: 'uppercase', marginBottom: 2 }}>Inventori</p>
                                <p style={{ fontSize: '22px', fontWeight: 900, color: UTM.goldDark }}>
                                    {stats.inventory_count}
                                </p>
                            </div>
                        </div>
                    </Card>
 
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                        {/* Welcome banner */}
                        <div style={{ background: UTM.maroon, borderRadius: 12, padding: '22px 24px',
                                      position: 'relative', overflow: 'hidden',
                                      boxShadow: '0 2px 10px rgba(92,0,31,0.18)' }}>
                            <div style={{ position: 'absolute', top: 0, left: 0, right: 0, height: 3,
                                          background: `linear-gradient(90deg, ${UTM.gold}, ${UTM.sand})` }} />
                            <p style={{ fontSize: '20px', fontWeight: 800, color: UTM.white, marginBottom: 6 }}>
                                Selamat kembali, {auth.user.name} 👋
                            </p>
                            <p style={{ fontSize: '13px', color: UTM.sand, lineHeight: 1.6 }}>
                                CAIRO UTM — <strong style={{ color: UTM.gold }}>{stats.total_count}</strong> aset ·{' '}
                                <strong style={{ color: UTM.gold }}>{stats.fixed_asset_count}</strong> Aset Tetap ·{' '}
                                <strong style={{ color: UTM.gold }}>{stats.inventory_count}</strong> Inventori
                            </p>
                        </div>
 
                        {/* High-value assets */}
                        <Card style={{ flex: 1 }}>
                            <SectionTitle>Aset Bernilai Tinggi</SectionTitle>
                            {highValueAssets.map((asset, idx) => (
                                <div key={asset.id} style={{
                                    display: 'flex', justifyContent: 'space-between',
                                    alignItems: 'center', padding: '10px 0',
                                    borderBottom: idx < highValueAssets.length - 1
                                        ? `1px solid ${UTM.gray100}` : 'none',
                                }}>
                                    <div style={{ display: 'flex', alignItems: 'center',
                                                  gap: 10, minWidth: 0 }}>
                                        <span style={{
                                            width: 22, height: 22, borderRadius: '50%',
                                            background: UTM.maroon, color: UTM.white,
                                            fontSize: '10px', fontWeight: 800, flexShrink: 0,
                                            display: 'flex', alignItems: 'center',
                                            justifyContent: 'center',
                                        }}>{idx + 1}</span>
                                        <div style={{ minWidth: 0 }}>
                                            <p style={{ fontSize: '13px', fontWeight: 700,
                                                        color: UTM.gray900, marginBottom: 2,
                                                        overflow: 'hidden', textOverflow: 'ellipsis',
                                                        whiteSpace: 'nowrap' }}>
                                                {asset.name}
                                            </p>
                                            <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                                                <span style={{ fontSize: '10px', color: UTM.gray500,
                                                               fontFamily: 'monospace',
                                                               textTransform: 'uppercase' }}>
                                                    {asset.asset_tag}
                                                </span>
                                                {asset.asset_type && <TypePill type={asset.asset_type} />}
                                                {asset.campus && (
                                                    <span style={{ fontSize: '10px', fontWeight: 700,
                                                                   color: CAMPUS_COLORS[asset.campus] ?? UTM.gray500 }}>
                                                        {asset.campus === 'utm_kl' ? 'KL' : asset.campus === 'utm_jb' ? 'JB' : '—'}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                    <p style={{ fontSize: '13px', fontWeight: 800, color: UTM.maroon,
                                                whiteSpace: 'nowrap', marginLeft: 10 }}>
                                        RM {Number(asset.purchase_price).toLocaleString()}
                                    </p>
                                </div>
                            ))}
                        </Card>
                    </div>
                </div>
 
                {/* ── Row 2: Campus / location breakdown ── */}
                <div>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 14 }}>
                        <div style={{ width: 3, height: 18, background: UTM.gold, borderRadius: 2 }} />
                        <p style={{ fontSize: '14px', fontWeight: 800, color: UTM.maroon }}>
                            Pecahan Mengikut Kampus
                        </p>
                        <span style={{ fontSize: '12px', color: UTM.gray500 }}>
                            — nilai &amp; pengagihan aset UTM KL vs UTM JB
                        </span>
                    </div>
 
                    <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr 1fr 1fr',
                                  gap: 14, alignItems: 'start' }}>
                        {/* Bar chart */}
                        <Card>
                            <SectionTitle>Nilai Aset Mengikut Kampus</SectionTitle>
                            {campusChart.length > 0
                                ? <Bar data={campusBarData} options={campusBarOpts} />
                                : <p style={{ fontSize: '13px', color: UTM.gray300,
                                               fontStyle: 'italic', textAlign: 'center',
                                               padding: '24px 0' }}>
                                      Tiada data kampus. Pastikan medan 'campus' diisi pada aset.
                                  </p>
                            }
                        </Card>
 
                        {/* One card per campus */}
                        {Object.entries(campusStats).map(([key, data]) => (
                            <CampusCard key={key} campusKey={key} data={data} />
                        ))}
                    </div>
                </div>
 
            </div>
        </AuthenticatedLayout>
    );
}
 