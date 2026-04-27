import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

const UTM = { maroon: '#5C001F', gold: '#F8A617', white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480', gray700: '#4A4540', gray900: '#1E1B18' };

export default function Dashboard({ auth, myAssets, myStats }) {
    return (
        <AuthenticatedLayout header={
            <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>Aset Saya</h2>
            </div>
        }>
            <Head title="Dashboard" />

            <div style={{ padding: '28px', maxWidth: 1000, margin: '0 auto' }}>
                
                {/* Welcome Banner */}
                <div style={{ background: UTM.maroon, borderRadius: 12, padding: '24px', marginBottom: 24, boxShadow: '0 4px 12px rgba(92,0,31,0.15)' }}>
                    <p style={{ fontSize: '20px', fontWeight: 800, color: UTM.white, marginBottom: 4 }}>
                        Selamat datang, {auth.user.name}
                    </p>
                    <p style={{ fontSize: '13px', color: '#FFF5AB' }}>
                        Terdapat {myStats.total} aset berdaftar di bawah tanggungjawab anda.
                    </p>
                </div>

                {/* Stat Summary */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: 16, marginBottom: 24 }}>
                    <div style={{ background: UTM.white, padding: '20px', borderRadius: 12, border: `1px solid ${UTM.gray100}` }}>
                        <p style={{ fontSize: '11px', color: UTM.gray500, fontWeight: 700, textTransform: 'uppercase' }}>Jumlah Aset</p>
                        <p style={{ fontSize: '28px', color: UTM.maroon, fontWeight: 900 }}>{myStats.total}</p>
                    </div>
                    <div style={{ background: UTM.white, padding: '20px', borderRadius: 12, border: `1px solid ${UTM.gray100}` }}>
                        <p style={{ fontSize: '11px', color: UTM.gray500, fontWeight: 700, textTransform: 'uppercase' }}>Aset Tetap</p>
                        <p style={{ fontSize: '28px', color: UTM.gray900, fontWeight: 900 }}>{myStats.fixed}</p>
                    </div>
                    <div style={{ background: UTM.white, padding: '20px', borderRadius: 12, border: `1px solid ${UTM.gray100}` }}>
                        <p style={{ fontSize: '11px', color: UTM.gray500, fontWeight: 700, textTransform: 'uppercase' }}>Inventori</p>
                        <p style={{ fontSize: '28px', color: UTM.gray900, fontWeight: 900 }}>{myStats.inventory}</p>
                    </div>
                </div>

                {/* ── KEW.PA Form Quick-Access (User) ── */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, padding: '20px 22px', marginBottom: 24 }}>
                    <p style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', color: UTM.maroon, marginBottom: 14, display: 'flex', alignItems: 'center', gap: 8 }}>
                        <span style={{ display: 'inline-block', width: 3, height: 13, background: UTM.gold, borderRadius: 2 }} />
                        Borang KEW.PA — Akses Pantas
                    </p>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))', gap: 10 }}>
                        {[
                            { label: 'PA-1 Penerimaan',  href: route('receivings.index'),     color: UTM.maroon,    bg: '#F3E0E5' },
                            { label: 'PA-2/3 Aset',      href: route('assets.index'),          color: '#0C447C',     bg: '#E6F1FB' },
                            { label: 'PA-6 Pergerakan',  href: route('transfers.index'),       color: UTM.goldDark,  bg: '#FEF3D6' },
                            { label: 'PA-9 Kerosakan',   href: route('damage-reports.index'),  color: '#1A7A3C',     bg: '#E6F4EC' },
                            { label: 'PA-9A Pinjaman',   href: route('placements.index'),      color: UTM.maroon,    bg: '#F3E0E5' },
                            { label: 'PA-10 Periksa',    href: route('inspections.index'),     color: '#0C447C',     bg: '#E6F1FB' },
                            { label: 'PA-13/14 Selenggara', href: route('maintenances.index'), color: UTM.goldDark,  bg: '#FEF3D6' },
                        ].map(item => (
                            <Link key={item.label} href={item.href} style={{ textDecoration: 'none' }}>
                                <div style={{
                                    background    : item.bg,
                                    borderRadius  : 9,
                                    padding       : '12px 14px',
                                    border        : `1.5px solid transparent`,
                                    cursor        : 'pointer',
                                    transition    : 'border-color .12s',
                                    textAlign     : 'center',
                                }}
                                    onMouseEnter={e => e.currentTarget.style.borderColor = item.color}
                                    onMouseLeave={e => e.currentTarget.style.borderColor = 'transparent'}
                                >
                                    <p style={{ fontSize: '12px', fontWeight: 700, color: item.color, margin: 0 }}>{item.label}</p>
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>

                {/* My Assets List */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ padding: '16px 20px', background: UTM.gray50, borderBottom: `1px solid ${UTM.gray100}` }}>
                        <p style={{ fontSize: '13px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase' }}>Senarai Aset Di Bawah Jagaan Anda</p>
                    </div>
                    
                    {myAssets.length > 0 ? (
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <tbody>
                                {myAssets.map(asset => (
                                    <tr key={asset.id} style={{ borderBottom: `1px solid ${UTM.gray100}` }}>
                                        <td style={{ padding: '16px 20px' }}>
                                            <p style={{ fontSize: '13px', fontWeight: 700, color: UTM.gray900 }}>{asset.name}</p>
                                            <p style={{ fontSize: '11px', color: UTM.gray500, fontFamily: 'monospace' }}>{asset.asset_tag}</p>
                                        </td>
                                        <td style={{ padding: '16px 20px', fontSize: '12px', color: UTM.gray700 }}>
                                            {asset.location}
                                        </td>
                                        <td style={{ padding: '16px 20px', textAlign: 'right' }}>
                                            <Link
                                                href={route('assets.index')}
                                                style={{ fontSize: '12px', fontWeight: 700, color: UTM.maroon, textDecoration: 'none', background: '#F3E0E5', padding: '6px 12px', borderRadius: 6 }}
                                            >
                                                Lihat Butiran
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    ) : (
                        <div style={{ padding: '40px', textAlign: 'center', color: UTM.gray500, fontSize: '13px' }}>
                            Tiada aset yang didaftarkan di bawah nama anda buat masa ini.
                        </div>
                    )}
                </div>

            </div>
        </AuthenticatedLayout>
    );
}