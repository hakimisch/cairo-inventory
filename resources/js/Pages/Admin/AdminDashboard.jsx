import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import {
    Chart as ChartJS, ArcElement, Tooltip, Legend,
    CategoryScale, LinearScale, BarElement,
} from 'chart.js';
import { Pie, Bar } from 'react-chartjs-2';

ChartJS.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, BarElement);

const UTM = {
    maroon: '#5C001F', maroon2: '#7A0029', gold: '#F8A617', goldDark: '#C9840A',
    sand: '#FFF5AB', white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4',
    gray300: '#C5BFB8', gray500: '#8A8480', gray700: '#4A4540', gray900: '#1E1B18',
};

const CAMPUS_COLORS = { utm_kl: UTM.maroon, utm_jb: UTM.gold, other: UTM.gray300 };

function Card({ children, style = {} }) {
    return (
        <div style={{ background: UTM.white, borderRadius: 12, boxShadow: '0 1px 4px rgba(92,0,31,0.07)', padding: '20px 22px', ...style }}>
            {children}
        </div>
    );
}

function SectionTitle({ children, color }) {
    return (
        <p style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', color: color || UTM.maroon, marginBottom: 14, display: 'flex', alignItems: 'center', gap: 8 }}>
            <span style={{ display: 'inline-block', width: 3, height: 13, background: UTM.gold, borderRadius: 2 }} />
            {children}
        </p>
    );
}

function StatCard({ label, value, sub, accent, dark }) {
    return (
        <div style={{ background: dark ? UTM.maroon : UTM.white, borderLeft: `4px solid ${accent || UTM.gold}`, borderRadius: 10, padding: '16px 20px', boxShadow: '0 1px 4px rgba(92,0,31,0.08)' }}>
            <p style={{ fontSize: '10px', fontWeight: 700, letterSpacing: '0.08em', textTransform: 'uppercase', color: dark ? UTM.sand : UTM.gray500, marginBottom: 4 }}>
                {label}
            </p>
            <p style={{ fontSize: '22px', fontWeight: 900, lineHeight: 1.1, color: dark ? UTM.white : UTM.maroon, marginBottom: sub ? 3 : 0 }}>
                {value}
            </p>
            {sub && <p style={{ fontSize: '12px', color: dark ? UTM.sand : UTM.gray500 }}>{sub}</p>}
        </div>
    );
}

function AlertCard({ label, count, color, bg, border, href }) {
    const inner = (
        <div style={{ background: bg, border: `1.5px solid ${border}`, borderRadius: 10, padding: '14px 18px', display: 'flex', justifyContent: 'space-between', alignItems: 'center', textDecoration: 'none' }}>
            <p style={{ fontSize: '13px', fontWeight: 600, color }}>{label}</p>
            <span style={{ fontSize: '22px', fontWeight: 900, color }}>{count}</span>
        </div>
    );
    return href ? <Link href={href} style={{ textDecoration: 'none' }}>{inner}</Link> : inner;
}

export default function AdminDashboard({ auth, stats, adminAlerts, kewpaCounts, chartData, assetTypeChart, campusChart, campusStats, highValueAssets }) {
    const PIE_OPTS = { plugins: { legend: { position: 'bottom', labels: { font: { size: 11, weight: '600' }, color: UTM.gray700, padding: 12 } } } };
    const CHART_COLORS = [UTM.maroon, UTM.gold, UTM.maroon2, UTM.goldDark, '#A0001A', '#E8C85A'];

    const categoryPie = {
        labels: chartData.map(i => i.category),
        datasets: [{ data: chartData.map(i => i.total), backgroundColor: CHART_COLORS, borderColor: UTM.white, borderWidth: 2 }],
    };
    
    const typePie = {
        labels: assetTypeChart.map(i => i.asset_type === 'fixed_asset' ? 'Aset Tetap' : 'Inventori'),
        datasets: [{ data: assetTypeChart.map(i => i.total), backgroundColor: [UTM.maroon, UTM.gold], borderColor: UTM.white, borderWidth: 2 }],
    };

    const campusBar = {
        labels: campusChart.map(i => i.campus === 'utm_kl' ? 'UTM KL' : i.campus === 'utm_jb' ? 'UTM JB' : 'Lain'),
        datasets: [{
            label: 'Nilai (RM)', data: campusChart.map(i => i.total),
            backgroundColor: campusChart.map(i => CAMPUS_COLORS[i.campus] ?? UTM.gray300),
            borderRadius: 6, borderSkipped: false,
        }],
    };

    const campusBarOpts = {
        responsive: true, plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: UTM.gray500, font: { size: 12 } } },
            y: { grid: { color: UTM.gray100 }, ticks: { color: UTM.gray500, callback: v => 'RM ' + Number(v).toLocaleString() } },
        },
    };

    return (
        <AuthenticatedLayout header={
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                    Papan Pemuka Pentadbir
                </h2>
                <span style={{ display: 'inline-block', padding: '3px 10px', borderRadius: '999px', fontSize: '11px', fontWeight: 700, background: '#F3E0E5', color: UTM.maroon, border: '1px solid #E8C0CB' }}>
                    Admin
                </span>
            </div>
        }>
            <Head title="Admin Dashboard" />

            <div style={{ padding: '24px 28px', maxWidth: 1400, margin: '0 auto' }}>

                {/* ── Alert row ── */}
                <div style={{ marginBottom: 20 }}>
                    <p style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', color: UTM.gray500, marginBottom: 10 }}>
                        Tindakan Diperlukan
                    </p>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 12 }}>
                        <AlertCard label="Penerimaan Menunggu Kelulusan" count={adminAlerts.pending_receivings} color="#0C447C" bg="#E6F1FB" border="#85B7EB" href={route('receivings.index')} />
                        <AlertCard label="Aset Perlu Penyelenggaraan" count={adminAlerts.maintenance_needed} color={UTM.goldDark} bg="#FEF3D6" border="#F5D890" href={route('assets.index')} />
                        <AlertCard label="Waranti Tamat ≤ 90 Hari" count={adminAlerts.expiring_warranties} color={UTM.maroon} bg="#F3E0E5" border="#E8C0CB" href={route('assets.index')} />
                    </div>
                </div>

                {/* ── Stats ── */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(180px,1fr))', gap: 12, marginBottom: 20 }}>
                    <StatCard label="Jumlah Nilai Aset" value={`RM ${Number(stats.total_value).toLocaleString()}`} accent={UTM.gold} dark />
                    <StatCard label="Jumlah Aset" value={stats.total_count} sub={`${stats.active_count} aktif · ${stats.repair_count} selenggara`} />
                    <StatCard label="Aset Tetap" value={stats.fixed_asset_count} sub={`RM ${Number(stats.fixed_asset_value).toLocaleString()}`} accent={UTM.maroon} />
                    <StatCard label="Inventori" value={stats.inventory_count} sub={`RM ${Number(stats.inventory_value).toLocaleString()}`} accent={UTM.gold} />
                </div>

                {/* ── Charts + high value ── */}
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1.6fr', gap: 16, marginBottom: 20 }}>
                    <Card>
                        <SectionTitle>Mengikut Kategori</SectionTitle>
                        <Pie data={categoryPie} options={PIE_OPTS} />
                    </Card>
                    <Card>
                        <SectionTitle>Jenis Aset</SectionTitle>
                        <Pie data={typePie} options={PIE_OPTS} />
                    </Card>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 14 }}>
                        <div style={{ background: UTM.maroon, borderRadius: 12, padding: '18px 22px', boxShadow: '0 2px 10px rgba(92,0,31,0.18)', position: 'relative', overflow: 'hidden' }}>
                            <div style={{ position: 'absolute', top: 0, left: 0, right: 0, height: 3, background: `linear-gradient(90deg, ${UTM.gold}, ${UTM.sand})` }} />
                            <p style={{ fontSize: '18px', fontWeight: 800, color: UTM.white, marginBottom: 5 }}>
                                Selamat kembali, {auth.user.name}
                            </p>
                            <p style={{ fontSize: '12px', color: UTM.sand }}>
                                CAIRO UTM — <strong style={{ color: UTM.gold }}>{stats.total_count}</strong> aset
                            </p>
                        </div>
                        <Card style={{ flex: 1 }}>
                            <SectionTitle>Aset Bernilai Tinggi</SectionTitle>
                            {highValueAssets.map((a, i) => (
                                <div key={a.id} style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '8px 0', borderBottom: i < highValueAssets.length - 1 ? `1px solid ${UTM.gray100}` : 'none' }}>
                                    <div style={{ display: 'flex', alignItems: 'center', gap: 8, minWidth: 0 }}>
                                        <span style={{ width: 20, height: 20, borderRadius: '50%', background: UTM.maroon, color: UTM.white, fontSize: '10px', fontWeight: 800, flexShrink: 0, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>{i + 1}</span>
                                        <div style={{ minWidth: 0 }}>
                                            <p style={{ fontSize: '12px', fontWeight: 700, color: UTM.gray900, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{a.name}</p>
                                            <p style={{ fontSize: '10px', color: UTM.gray500, fontFamily: 'monospace', textTransform: 'uppercase' }}>{a.asset_tag}</p>
                                        </div>
                                    </div>
                                    <p style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, whiteSpace: 'nowrap', marginLeft: 10 }}>
                                        RM {Number(a.purchase_price).toLocaleString()}
                                    </p>
                                </div>
                            ))}
                        </Card>
                    </div>
                </div>

                {/* ── Campus bar ── */}
                <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr 1fr', gap: 16, marginBottom: 20 }}>
                    <Card>
                        <SectionTitle>Nilai Aset Mengikut Kampus</SectionTitle>
                        {campusChart.length > 0
                            ? <Bar data={campusBar} options={campusBarOpts} />
                            : <p style={{ fontSize: '13px', color: UTM.gray300, fontStyle: 'italic', textAlign: 'center', padding: '20px 0' }}>
                                  Tiada data kampus. Pastikan medan campus diisi.
                              </p>}
                    </Card>
                    {Object.entries(campusStats).map(([key, cs]) => (
                        <Card key={key} style={{ borderTop: `3px solid ${CAMPUS_COLORS[key] ?? UTM.gray300}` }}>
                            <p style={{ fontSize: '13px', fontWeight: 800, color: UTM.gray900, marginBottom: 4 }}>
                                {cs.label}
                            </p>
                            <p style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 12 }}>
                                {cs.count} aset · {cs.active_count} aktif
                            </p>
                            <p style={{ fontSize: '16px', fontWeight: 900, color: CAMPUS_COLORS[key] ?? UTM.gray500 }}>
                                RM {Number(cs.value).toLocaleString()}
                            </p>
                            <div style={{ marginTop: 10, fontSize: '12px', color: UTM.gray500 }}>
                                <span>Tetap: {cs.fixed_count}</span>
                                <span style={{ margin: '0 8px', color: UTM.gray300 }}>·</span>
                                <span>Inventori: {cs.inventory_count}</span>
                            </div>
                        </Card>
                    ))}
                </div>

                {/* ── KEW.PA Form Quick-Access ── */}
                <Card style={{ marginBottom: 20 }}>
                    <SectionTitle>Borang KEW.PA — Akses Pantas</SectionTitle>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))', gap: 10 }}>
                        {[
                            { label: 'PA-1 Penerimaan',  count: kewpaCounts.pending_receivings,    href: route('receivings.index'),         color: UTM.maroon,    bg: '#F3E0E5' },
                            { label: 'PA-6 Pergerakan',  count: kewpaCounts.total_transfers,       href: route('transfers.index'),          color: '#0C447C',     bg: '#E6F1FB' },
                            { label: 'PA-9 Kerosakan',   count: kewpaCounts.total_damage_reports,  href: route('damage-reports.index'),     color: UTM.goldDark,  bg: '#FEF3D6' },
                            { label: 'PA-9A Pinjaman',   count: kewpaCounts.total_placements,      href: route('placements.index'),         color: '#1A7A3C',     bg: '#E6F4EC' },
                            { label: 'PA-10 Pemeriksaan',count: kewpaCounts.total_inspections,     href: route('inspections.index'),        color: UTM.maroon,    bg: '#F3E0E5' },
                            { label: 'PA-13/14 Selenggara',count: kewpaCounts.total_maintenances,  href: route('maintenances.index'),       color: '#0C447C',     bg: '#E6F1FB' },
                            { label: 'PA-16 Kenderaan',  count: kewpaCounts.total_vehicle_disposals, href: route('vehicle-disposals.index'), color: UTM.goldDark,  bg: '#FEF3D6' },
                            { label: 'PA-17/18/19 Lupus',count: kewpaCounts.total_disposals,       href: route('disposals.index'),          color: '#1A7A3C',     bg: '#E6F4EC' },
                            { label: 'PA-28→32 Hilang',  count: kewpaCounts.total_loss_reports,    href: route('loss-reports.index'),       color: UTM.maroon,    bg: '#F3E0E5' },
                            { label: 'PA-21→27A Jualan', count: kewpaCounts.active_disposal_sales, href: route('disposal-sales.index'),     color: '#0C447C',     bg: '#E6F1FB' },
                        ].map(item => (
                            <Link key={item.label} href={item.href} style={{ textDecoration: 'none' }}>
                                <div style={{
                                    background    : item.bg,
                                    borderRadius  : 9,
                                    padding       : '12px 14px',
                                    border        : `1.5px solid transparent`,
                                    cursor        : 'pointer',
                                    transition    : 'border-color .12s',
                                    display       : 'flex',
                                    justifyContent: 'space-between',
                                    alignItems    : 'center',
                                }}
                                    onMouseEnter={e => e.currentTarget.style.borderColor = item.color}
                                    onMouseLeave={e => e.currentTarget.style.borderColor = 'transparent'}
                                >
                                    <p style={{ fontSize: '12px', fontWeight: 700, color: item.color, margin: 0 }}>{item.label}</p>
                                    <span style={{
                                        display       : 'inline-flex',
                                        alignItems    : 'center',
                                        justifyContent: 'center',
                                        minWidth      : 24,
                                        height        : 24,
                                        borderRadius  : '999px',
                                        background    : item.color,
                                        color         : '#FFFFFF',
                                        fontSize      : '11px',
                                        fontWeight    : 800,
                                        padding       : '0 6px',
                                    }}>
                                        {item.count}
                                    </span>
                                </div>
                            </Link>
                        ))}
                    </div>
                </Card>

                {/* ── Reports quick-access ── */}
                <Card>
                    <SectionTitle>Laporan Tahunan</SectionTitle>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(220px, 1fr))', gap: 12 }}>
                        {[
                            { title: 'KEW.PA-4', sub: 'Senarai Harta Tetap', href: route('reports.kewpa4'), color: UTM.maroon },
                            { title: 'KEW.PA-5', sub: 'Senarai Inventori', href: route('reports.kewpa5'), color: UTM.goldDark },
                            { title: 'KEW.PA-7', sub: 'Kedudukan Aset',    href: route('reports.kewpa7'), color: '#0C447C' },
                            { title: 'KEW.PA-8', sub: 'Laporan Tahunan',   href: route('reports.kewpa8'), color: UTM.maroon },
                            { title: 'KEW.PA-12',sub: 'Perakuan Tahunan',  href: route('reports.kewpa12'),color: UTM.goldDark },
                            { title: 'KEW.PA-20',sub: 'Laporan Pelupusan', href: route('reports.kewpa20'),color: '#1A7A3C' },
                        ].map(r => (
                            <Link key={r.title} href={r.href} style={{ textDecoration: 'none' }}>
                                <div style={{ background: UTM.gray50, borderRadius: 9, padding: '14px 18px', border: `1.5px solid ${UTM.gray100}`, cursor: 'pointer', transition: 'border-color .12s', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                                    <div>
                                        <p style={{ fontSize: '14px', fontWeight: 800, color: r.color, marginBottom: 2 }}>{r.title}</p>
                                        <p style={{ fontSize: '12px', color: UTM.gray500 }}>{r.sub}</p>
                                    </div>
                                    <span style={{ fontSize: 18, color: UTM.gray300 }}>›</span>
                                </div>
                            </Link>
                        ))}
                    </div>
                </Card>

            </div>
        </AuthenticatedLayout>
    );
}