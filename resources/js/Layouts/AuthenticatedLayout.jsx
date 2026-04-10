import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link, usePage } from '@inertiajs/react';
 
// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray500: '#8A8480',
};
 
function NavSection({ label, children }) {
    return (
        <div style={{ marginBottom: 24 }}>
            <p style={{ fontSize: '10px', fontWeight: 700, letterSpacing: '0.1em', textTransform: 'uppercase', color: 'rgba(255,245,171,0.5)', padding: '0 12px', marginBottom: 6 }}>
                {label}
            </p>
            <div style={{ display: 'flex', flexDirection: 'column', gap: 2 }}>{children}</div>
        </div>
    );
}
 
function NavLink({ href, icon, label, active }) {
    return (
        <Link
            href={href}
            style={{
                display: 'flex', alignItems: 'center', gap: 10, padding: '9px 12px', borderRadius: 8, fontSize: '13px',
                fontWeight: active ? 700 : 500,
                color: active ? UTM.gold : 'rgba(255,255,255,0.75)',
                background: active ? 'rgba(248,166,23,0.12)' : 'transparent',
                borderLeft: active ? `3px solid ${UTM.gold}` : '3px solid transparent',
                textDecoration: 'none', transition: 'all 0.12s',
            }}
            onMouseEnter={e => { if (!active) { e.currentTarget.style.background = 'rgba(255,255,255,0.07)'; e.currentTarget.style.color = UTM.white; } }}
            onMouseLeave={e => { if (!active) { e.currentTarget.style.background = 'transparent'; e.currentTarget.style.color = 'rgba(255,255,255,0.75)'; } }}
        >
            <span style={{ fontSize: 15, width: 20, textAlign: 'center', flexShrink: 0 }}>{icon}</span>
            <span>{label}</span>
        </Link>
    );
}
 
export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;
 
    return (
        <div style={{ minHeight: '100vh', background: UTM.gray50, display: 'flex' }}>
 
            <aside style={{ width: 240, background: UTM.maroon, display: 'flex', flexDirection: 'column', position: 'sticky', top: 0, height: '100vh', flexShrink: 0, boxShadow: '2px 0 12px rgba(0,0,0,0.15)' }}>
 
                <div style={{ padding: '20px 16px 16px', borderBottom: '1px solid rgba(255,245,171,0.12)', marginBottom: 20 }}>
                    <div style={{ height: 3, background: `linear-gradient(90deg, ${UTM.gold}, ${UTM.sand})`, borderRadius: 2, marginBottom: 16, marginLeft: -16, marginRight: -16 }} />
                    <div style={{ display: 'flex', alignItems: 'center', gap: 10 }}>
                        <ApplicationLogo style={{ height: 32, width: 'auto' }} className="fill-current" />
                        <div>
                            <p style={{ fontSize: '15px', fontWeight: 800, color: UTM.white, letterSpacing: '0.05em', lineHeight: 1.1 }}>CAIRO INV</p>
                            <p style={{ fontSize: '10px', color: 'rgba(255,245,171,0.6)', letterSpacing: '0.08em', textTransform: 'uppercase' }}>UTM Asset System</p>
                        </div>
                    </div>
                </div>
 
                {/* ── Navigation Logic ── */}
                <nav style={{ flex: 1, padding: '0 12px', overflowY: 'auto' }}>
                    <NavSection label="Utama">
                        {user.role === 'admin' ? (
                            <NavLink href={route('admin.dashboard')} icon="▣" label="Admin Dashboard" active={route().current('admin.dashboard')} />
                        ) : (
                            <NavLink href={route('dashboard')} icon="▣" label="Dashboard" active={route().current('dashboard')} />
                        )}
                    </NavSection>
 
                    {/* ONLY ADMINS SEE LOGISTICS & KEW.PA-1 */}
                    {user.role === 'admin' && (
                        <NavSection label="Logistik (KEW.PA-1)">
                            <NavLink href={route('receivings.create')} icon="＋" label="Daftar Penerimaan" active={route().current('receivings.create')} />
                            <NavLink href={route('receivings.index')} icon="◫" label="Senarai Penerimaan" active={route().current('receivings.index')} />
                        </NavSection>
                    )}
 
                    {/* EVERYONE SEES THE ASSET REGISTRY */}
                    <NavSection label="Daftar Aset (KEW.PA-3)">
                        <NavLink href={route('assets.index')} icon="◈" label="Inventori Aset" active={route().current('assets.*')} />
                    </NavSection>

                    {/* ONLY ADMINS SEE USER MANAGEMENT */}
                    {user.role === 'admin' && (
                        <NavSection label="Sistem">
                            <NavLink href={route('admin.users.index')} icon="👤" label="Pengurusan Pengguna" active={route().current('admin.users.*')} />
                        </NavSection>
                    )}
                </nav>
 
                <div style={{ padding: '14px 16px', borderTop: '1px solid rgba(255,245,171,0.12)', background: 'rgba(0,0,0,0.2)' }}>
                    <p style={{ fontSize: '10px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.08em', color: 'rgba(255,245,171,0.5)', marginBottom: 6 }}>
                        Log masuk sebagai {user.role === 'admin' ? '(Admin)' : ''}
                    </p>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 8, minWidth: 0 }}>
                            <div style={{ width: 28, height: 28, borderRadius: '50%', background: UTM.gold, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '12px', fontWeight: 800, color: UTM.maroon, flexShrink: 0 }}>
                                {user.name.charAt(0).toUpperCase()}
                            </div>
                            <span style={{ fontSize: '13px', fontWeight: 700, color: UTM.white, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                {user.name}
                            </span>
                        </div>
                        <Link href={route('logout')} method="post" as="button" style={{ fontSize: '11px', fontWeight: 700, color: 'rgba(255,100,100,0.8)', background: 'none', border: 'none', cursor: 'pointer', padding: '4px 8px', borderRadius: 4, textDecoration: 'none', flexShrink: 0 }}>
                            Keluar
                        </Link>
                    </div>
                </div>
            </aside>
 
            <div style={{ flex: 1, display: 'flex', flexDirection: 'column', overflow: 'hidden' }}>
                <header style={{ background: UTM.white, borderBottom: `1px solid ${UTM.gray100}`, padding: '0 32px', height: 60, display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexShrink: 0, boxShadow: '0 1px 4px rgba(92,0,31,0.05)' }}>
                    {header}
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 6, padding: '4px 10px', borderRadius: '999px', background: UTM.gray50, border: `1px solid ${UTM.gray100}` }}>
                            <div style={{ width: 8, height: 8, borderRadius: '50%', background: UTM.maroon }} />
                            <span style={{ fontSize: '11px', fontWeight: 600, color: UTM.gray500 }}>CAIRO v1.0</span>
                        </div>
                    </div>
                </header>
                <main style={{ flex: 1, overflowY: 'auto', background: UTM.gray50 }}>
                    {children}
                </main>
            </div>
        </div>
    );
}