import { useState } from 'react';
import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link, usePage } from '@inertiajs/react';
import { ToastProvider } from '@/Components/Toast';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    maroon2: '#7A0029',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray500: '#8A8480',
};

function NavSection({ label, children }) {
    return (
        <div style={{ marginBottom: 20 }}>
            <p style={{
                fontSize     : '10px',
                fontWeight   : 700,
                letterSpacing: '0.1em',
                textTransform: 'uppercase',
                color        : 'rgba(255,245,171,0.5)',
                padding      : '0 12px',
                marginBottom : 6,
            }}>
                {label}
            </p>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 2 }}>
                {children}
            </div>
        </div>
    );
}

function NavLink({ href, icon, label, active }) {
    return (
        <Link
            href={href}
            style={{
                display        : 'flex',
                alignItems     : 'center',
                gap            : 10,
                padding        : '9px 12px',
                borderRadius   : 8,
                fontSize       : '13px',
                fontWeight     : active ? 700 : 500,
                color          : active ? UTM.gold : 'rgba(255,255,255,0.75)',
                background     : active ? 'rgba(248,166,23,0.12)' : 'transparent',
                borderLeft     : active ? `3px solid ${UTM.gold}` : '3px solid transparent',
                textDecoration : 'none',
                transition     : 'all 0.12s',
            }}
            onMouseEnter={e => {
                if (!active) {
                    e.currentTarget.style.background = 'rgba(255,255,255,0.07)';
                    e.currentTarget.style.color = UTM.white;
                }
            }}
            onMouseLeave={e => {
                if (!active) {
                    e.currentTarget.style.background = 'transparent';
                    e.currentTarget.style.color = 'rgba(255,255,255,0.75)';
                }
            }}
        >
            <span style={{ fontSize: 15, width: 20, textAlign: 'center', flexShrink: 0 }}>{icon}</span>
            <span>{label}</span>
        </Link>
    );
}

// ─── Collapsible sub-menu group (for PA-21→27A) ──────────────────────────────
function NavGroup({ label, icon, children, defaultOpen = false }) {
    const [open, setOpen] = useState(defaultOpen);

    return (
        <div style={{ marginBottom: 2 }}>
            <button
                onClick={() => setOpen(!open)}
                style={{
                    display        : 'flex',
                    alignItems     : 'center',
                    gap            : 10,
                    padding        : '9px 12px',
                    borderRadius   : 8,
                    fontSize       : '13px',
                    fontWeight     : 600,
                    color          : 'rgba(255,255,255,0.75)',
                    background     : 'transparent',
                    border         : 'none',
                    cursor         : 'pointer',
                    width          : '100%',
                    textAlign      : 'left',
                    textDecoration : 'none',
                    transition     : 'all 0.12s',
                }}
                onMouseEnter={e => {
                    e.currentTarget.style.background = 'rgba(255,255,255,0.07)';
                    e.currentTarget.style.color = UTM.white;
                }}
                onMouseLeave={e => {
                    e.currentTarget.style.background = 'transparent';
                    e.currentTarget.style.color = 'rgba(255,255,255,0.75)';
                }}
            >
                <span style={{ fontSize: 15, width: 20, textAlign: 'center', flexShrink: 0 }}>{icon}</span>
                <span style={{ flex: 1 }}>{label}</span>
                <span style={{
                    fontSize   : '10px',
                    transition : 'transform 0.2s',
                    transform  : open ? 'rotate(90deg)' : 'rotate(0deg)',
                    color      : 'rgba(255,245,171,0.5)',
                }}>
                    ▸
                </span>
            </button>
            {open && (
                <div style={{
                    marginLeft : 12,
                    borderLeft : '1px solid rgba(255,245,171,0.15)',
                    paddingLeft: 8,
                    display    : 'flex',
                    flexDirection: 'column',
                    gap        : 1,
                }}>
                    {children}
                </div>
            )}
        </div>
    );
}

function SubNavLink({ href, label, active }) {
    return (
        <Link
            href={href}
            style={{
                display        : 'flex',
                alignItems     : 'center',
                gap            : 8,
                padding        : '6px 10px',
                borderRadius   : 6,
                fontSize       : '12px',
                fontWeight     : active ? 700 : 400,
                color          : active ? UTM.gold : 'rgba(255,255,255,0.65)',
                background     : active ? 'rgba(248,166,23,0.10)' : 'transparent',
                textDecoration : 'none',
                transition     : 'all 0.12s',
            }}
            onMouseEnter={e => {
                if (!active) {
                    e.currentTarget.style.background = 'rgba(255,255,255,0.05)';
                    e.currentTarget.style.color = UTM.white;
                }
            }}
            onMouseLeave={e => {
                if (!active) {
                    e.currentTarget.style.background = 'transparent';
                    e.currentTarget.style.color = 'rgba(255,255,255,0.65)';
                }
            }}
        >
            <span style={{ fontSize: '10px', color: active ? UTM.gold : 'rgba(255,245,171,0.3)' }}>▪</span>
            {label}
        </Link>
    );
}

export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;

    // ── Helper: check if current route matches any of the given patterns ──
    const isActive = (...patterns) => patterns.some(p => route().current(p));

    return (
        <div style={{ minHeight: '100vh', background: UTM.gray50, display: 'flex' }}>

            {/* ── Sidebar ── */}
            <aside style={{
                width      : 240,
                background : UTM.maroon,
                display    : 'flex',
                flexDirection: 'column',
                position   : 'sticky',
                top        : 0,
                height     : '100vh',
                flexShrink : 0,
                boxShadow  : '2px 0 12px rgba(0,0,0,0.15)',
            }}>
                {/* Logo / brand */}
                <div style={{
                    padding        : '20px 16px 16px',
                    borderBottom   : '1px solid rgba(255,245,171,0.12)',
                    marginBottom   : 16,
                }}>
                    <div style={{
                        height      : 3,
                        background  : `linear-gradient(90deg, ${UTM.gold}, ${UTM.sand})`,
                        borderRadius: 2,
                        marginBottom: 16,
                        marginLeft  : -16,
                        marginRight : -16,
                    }} />

                    <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <ApplicationLogo style={{ height: 32, width: 'auto' }} className="fill-current" />
                        <div>
                            <p style={{ fontSize: '15px', fontWeight: 800, color: UTM.white,
                                        letterSpacing: '0.05em', lineHeight: 1.1 }}>
                                CAIRO INV
                            </p>
                            <p style={{ fontSize: '10px', color: 'rgba(255,245,171,0.6)',
                                        letterSpacing: '0.08em', textTransform: 'uppercase' }}>
                                UTM Asset System
                            </p>
                        </div>
                    </div>
                </div>

                {/* Nav */}
                <nav style={{ flex: 1, padding: '0 12px', overflowY: 'auto' }}>

                    {/* ═══════════════════════════════════════════════════════
                       UTAMA
                       ═══════════════════════════════════════════════════════ */}
                    <NavSection label="Utama">
                        {user.role === 'admin' ? (
                            <NavLink
                                href={route('admin.dashboard')}
                                icon="▣"
                                label="Admin Dashboard"
                                active={route().current('admin.dashboard')}
                            />
                        ) : (
                            <NavLink
                                href={route('dashboard')}
                                icon="▣"
                                label="Dashboard"
                                active={route().current('dashboard')}
                            />
                        )}
                    </NavSection>

                    {/* ═══════════════════════════════════════════════════════
                       LOGISTIK & PENERIMAAN
                       ═══════════════════════════════════════════════════════ */}
                    <NavSection label="Logistik & Penerimaan">
                        {user.role === 'admin' && (
                            <NavLink
                                href={route('receivings.create')}
                                icon="＋"
                                label="PA-1 Daftar Penerimaan"
                                active={route().current('receivings.create')}
                            />
                        )}
                        <NavLink
                            href={route('receivings.index')}
                            icon="◫"
                            label="PA-1 Senarai Penerimaan"
                            active={isActive('receivings.index', 'receivings.kewpa1')}
                        />
                        <NavLink
                            href={route('assets.index')}
                            icon="◈"
                            label="PA-2/3 Pendaftaran Aset"
                            active={isActive('assets.*')}
                        />
                    </NavSection>

                    {/* ═══════════════════════════════════════════════════════
                       PERGERAKAN & PEMERIKSAAN
                       ═══════════════════════════════════════════════════════ */}
                    <NavSection label="Pergerakan & Pemeriksaan">
                        <NavLink
                            href={route('transfers.index')}
                            icon="↗"
                            label="PA-6 Daftar Pergerakan"
                            active={isActive('transfers.index', 'assets.kewpa6', 'assets.kewpa6.download')}
                        />
                        <NavLink
                            href={route('damage-reports.index')}
                            icon="⚠️"
                            label="PA-9 Aduan Kerosakan"
                            active={isActive('damage-reports.index', 'damage-reports.kewpa9.download')}
                        />
                        <NavLink
                            href={route('placements.index')}
                            icon="📋"
                            label="PA-9A Pinjaman Aset"
                            active={isActive('placements.index', 'assets.kewpa9a', 'assets.placements.kewpa9a', 'assets.kewpa9a.download', 'assets.placements.kewpa9a.download')}
                        />
                        <NavLink
                            href={route('inspections.index')}
                            icon="🔍"
                            label="PA-10 Pemeriksaan Aset"
                            active={isActive('inspections.index', 'assets.kewpa10', 'assets.kewpa10.download')}
                        />
                        <NavLink
                            href={route('reports.kewpa12')}
                            icon="📄"
                            label="PA-12 Perakuan Tahunan"
                            active={isActive('reports.kewpa12', 'reports.kewpa12.download')}
                        />
                    </NavSection>

                    {/* ═══════════════════════════════════════════════════════
                       PENYELENGGARAAN
                       ═══════════════════════════════════════════════════════ */}
                    <NavSection label="Penyelenggaraan">
                        <NavLink
                            href={route('maintenances.index')}
                            icon="🔧"
                            label="PA-13/14 Penyelenggaraan"
                            active={isActive('maintenances.index', 'assets.maintenances.*')}
                        />
                    </NavSection>

                    {/* ═══════════════════════════════════════════════════════
                       PELUPUSAN & JUALAN
                       ═══════════════════════════════════════════════════════ */}
                    <NavSection label="Pelupusan & Jualan">
                        <NavLink
                            href={route('vehicle-disposals.index')}
                            icon="🚗"
                            label="PA-16 Pelupusan Kenderaan"
                            active={isActive('vehicle-disposals.index', 'assets.vehicle-disposal.*')}
                        />
                        <NavLink
                            href={route('disposals.index')}
                            icon="🗑️"
                            label="PA-17/18/19 Pelupusan"
                            active={isActive('disposals.index', 'assets.disposals.*')}
                        />
                        <NavLink
                            href={route('reports.kewpa20')}
                            icon="📊"
                            label="PA-20 Laporan Pelupusan"
                            active={isActive('reports.kewpa20', 'reports.kewpa20.download')}
                        />

                        {/* ── Collapsible: PA-21→27A Jualan Aset ── */}
                        <NavGroup
                            icon="💰"
                            label="PA-21→27A Jualan Aset"
                            defaultOpen={isActive(
                                'disposal-sales.*',
                                'disposal-sales.kewpa21', 'disposal-sales.kewpa21.download',
                                'disposal-sales.kewpa22', 'disposal-sales.kewpa22.download',
                                'disposal-sales.kewpa23', 'disposal-sales.kewpa23.download',
                                'disposal-sales.kewpa24', 'disposal-sales.kewpa24.download',
                                'disposal-sales.kewpa25', 'disposal-sales.kewpa25.download',
                                'disposal-sales.kewpa26', 'disposal-sales.kewpa26.download',
                                'disposal-sales.kewpa27', 'disposal-sales.kewpa27.download',
                                'disposal-sales.kewpa27a', 'disposal-sales.kewpa27a.download',
                            )}
                        >
                            <SubNavLink
                                href={route('disposal-sales.index')}
                                label="Senarai Jualan"
                                active={isActive('disposal-sales.index', 'disposal-sales.show')}
                            />
                        </NavGroup>
                    </NavSection>

                    {/* ═══════════════════════════════════════════════════════
                       KEHILANGAN
                       ═══════════════════════════════════════════════════════ */}
                    <NavSection label="Kehilangan">
                        <NavLink
                            href={route('loss-reports.index')}
                            icon="⚠️"
                            label="PA-28→32 Kehilangan"
                            active={isActive('loss-reports.index', 'assets.loss-reports.*')}
                        />
                    </NavSection>

                    {/* ═══════════════════════════════════════════════════════
                       LAPORAN (Admin)
                       ═══════════════════════════════════════════════════════ */}
                    {user.role === 'admin' && (
                        <NavSection label="Laporan Tahunan">
                            <NavLink
                                href={route('reports.kewpa4')}
                                icon="▤"
                                label="PA-4 Harta Tetap"
                                active={isActive('reports.kewpa4', 'reports.kewpa4.download')}
                            />
                            <NavLink
                                href={route('reports.kewpa5')}
                                icon="▤"
                                label="PA-5 Inventori"
                                active={isActive('reports.kewpa5', 'reports.kewpa5.download')}
                            />
                            <NavLink
                                href={route('reports.kewpa7')}
                                icon="▤"
                                label="PA-7 Kedudukan Aset"
                                active={isActive('reports.kewpa7', 'reports.kewpa7.download')}
                            />
                            <NavLink
                                href={route('reports.kewpa8')}
                                icon="▤"
                                label="PA-8 Lap. Tahunan"
                                active={isActive('reports.kewpa8', 'reports.kewpa8.download')}
                            />
                        </NavSection>
                    )}

                    {/* ═══════════════════════════════════════════════════════
                       SISTEM (Admin)
                       ═══════════════════════════════════════════════════════ */}
                    {user.role === 'admin' && (
                        <NavSection label="Sistem">
                            <NavLink
                                href={route('admin.users.index')}
                                icon="👤"
                                label="Pengurusan Pengguna"
                                active={isActive('admin.users.*')}
                            />
                        </NavSection>
                    )}
                </nav>

                {/* User footer */}
                <div style={{
                    padding      : '14px 16px',
                    borderTop    : '1px solid rgba(255,245,171,0.12)',
                    background   : 'rgba(0,0,0,0.2)',
                }}>
                    <p style={{ fontSize: '10px', fontWeight: 700, textTransform: 'uppercase',
                                letterSpacing: '0.08em', color: 'rgba(255,245,171,0.5)',
                                marginBottom: 6 }}>
                        Log masuk sebagai
                    </p>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, minWidth: 0 }}>
                            {/* Avatar circle */}
                            <div style={{
                                width: 28, height: 28, borderRadius: '50%',
                                background: user.role === 'admin' ? UTM.gold : 'rgba(248,166,23,0.3)',
                                display: 'flex', alignItems: 'center', justifyContent: 'center',
                                fontSize: '12px', fontWeight: 800,
                                color: user.role === 'admin' ? UTM.maroon : UTM.white,
                                flexShrink: 0,
                            }}>
                                {user.name.charAt(0).toUpperCase()}
                            </div>
                            <div style={{ minWidth: 0 }}>
                                <span style={{ fontSize: '13px', fontWeight: 700, color: UTM.white,
                                               overflow: 'hidden', textOverflow: 'ellipsis',
                                               whiteSpace: 'nowrap', display: 'block' }}>
                                    {user.name}
                                </span>
                                {user.role === 'admin' && (
                                    <span style={{ fontSize: '10px', fontWeight: 700, color: UTM.gold,
                                                   textTransform: 'uppercase', letterSpacing: '0.06em' }}>
                                        Admin
                                    </span>
                                )}
                            </div>
                        </div>
                        <Link
                            href={route('logout')}
                            method="post"
                            as="button"
                            style={{
                                fontSize      : '11px',
                                fontWeight    : 700,
                                color         : 'rgba(255,100,100,0.8)',
                                background    : 'none',
                                border        : 'none',
                                cursor        : 'pointer',
                                padding       : '4px 8px',
                                borderRadius  : 4,
                                textDecoration: 'none',
                                flexShrink    : 0,
                            }}
                        >
                            Keluar
                        </Link>
                    </div>
                </div>
            </aside>

            {/* ── Main content area ── */}
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', overflow: 'hidden' }}>

                {/* Top header bar */}
                <header style={{
                    background  : UTM.white,
                    borderBottom: `1px solid ${UTM.gray100}`,
                    padding     : '0 32px',
                    height      : 60,
                    display     : 'flex',
                    alignItems  : 'center',
                    justifyContent: 'space-between',
                    flexShrink  : 0,
                    boxShadow   : '0 1px 4px rgba(92,0,31,0.05)',
                }}>
                    {header}

                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        {/* UTM color pill */}
                        <div style={{
                            display     : 'flex',
                            alignItems  : 'center',
                            gap         : 6,
                            padding     : '4px 10px',
                            borderRadius: '999px',
                            background  : UTM.gray50,
                            border      : `1px solid ${UTM.gray100}`,
                        }}>
                            <div style={{ width: 8, height: 8, borderRadius: '50%',
                                          background: UTM.maroon }} />
                            <span style={{ fontSize: '11px', fontWeight: 600, color: UTM.gray500 }}>
                                CAIRO v1.0
                            </span>
                        </div>
                    </div>
                </header>

                <main style={{ flex: 1, overflowY: 'auto', background: UTM.gray50 }}>
                    <ToastProvider>
                        {children}
                    </ToastProvider>
                </main>
            </div>
        </div>
    );
}
