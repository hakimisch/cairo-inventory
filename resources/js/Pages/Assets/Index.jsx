import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Link } from '@inertiajs/react';
 
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
    gray300: '#C5BFB8',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};
 
function StatusBadge({ status }) {
    const styles = {
        active  : { bg: '#E6F4EC', color: '#1A7A3C', border: '#B2DFC2' },
        repair  : { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890' },
        disposed: { bg: '#F3E0E5', color: UTM.maroon, border: '#E8C0CB' },
    };
    const s = styles[status] || styles.disposed;
    const labels = { active: 'Aktif', repair: 'Selenggara', disposed: 'Dilupus' };
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '3px 10px',
            borderRadius : '999px',
            fontSize     : '11px',
            fontWeight   : 700,
            letterSpacing: '0.04em',
            background   : s.bg,
            color        : s.color,
            border       : `1px solid ${s.border}`,
        }}>
            {labels[status] ?? status}
        </span>
    );
}
 
function TypeBadge({ type }) {
    const isFixed = type === 'fixed_asset';
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '3px 10px',
            borderRadius  : '999px',
            fontSize     : '11px',
            fontWeight   : 700,
            background   : isFixed ? '#F3E0E5' : '#FEF3D6',
            color        : isFixed ? UTM.maroon : UTM.goldDark,
            border       : `1px solid ${isFixed ? '#E8C0CB' : '#F5D890'}`,
        }}>
            {isFixed ? 'Aset Tetap' : 'Inventori'}
        </span>
    );
}

function WarrantyBadge({ expiryDate }) {
    if (!expiryDate) {
        return <span style={{ color: UTM.gray300, fontSize: '12px' }}>—</span>;
    }

    // Set times to midnight to ensure accurate day calculation
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    const expiry = new Date(expiryDate);
    expiry.setHours(0, 0, 0, 0);

    const diffTime = expiry - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    let status = 'valid';
    let label = 'Aktif';

    if (diffDays < 0) {
        status = 'expired';
        label = 'Tamat';
    } else if (diffDays <= 90) {
        status = 'expiring';
        label = `${diffDays} hari`;
    }

    const styles = {
        valid:    { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2' }, // Green
        expiring: { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890' }, // Amber
        expired:  { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB' }, // Red
    };

    const s = styles[status];

    return (
        <span style={{
            display      : 'inline-block',
            padding      : '3px 10px',
            borderRadius : '999px',
            fontSize     : '11px',
            fontWeight   : 700,
            letterSpacing: '0.04em',
            background   : s.bg,
            color        : s.color,
            border       : `1px solid ${s.border}`,
        }}>
            {label}
        </span>
    );
}
 
// ─── Filter tab button ────────────────────────────────────────────────────────
function FilterTab({ label, count, active, onClick }) {
    return (
        <button
            onClick={onClick}
            style={{
                padding      : '8px 18px',
                borderRadius : '8px',
                fontSize     : '13px',
                fontWeight   : 700,
                cursor       : 'pointer',
                border       : 'none',
                transition   : 'all 0.15s',
                background   : active ? UTM.maroon : UTM.white,
                color        : active ? UTM.white : UTM.gray700,
                boxShadow    : active ? '0 2px 8px rgba(92,0,31,0.18)' : '0 1px 2px rgba(0,0,0,0.06)',
            }}
        >
            {label}
            <span style={{
                marginLeft   : 7,
                display      : 'inline-block',
                padding      : '1px 7px',
                borderRadius : '999px',
                fontSize     : '11px',
                background   : active ? 'rgba(248,166,23,0.25)' : UTM.gray100,
                color        : active ? UTM.gold : UTM.gray500,
                fontWeight   : 800,
            }}>
                {count}
            </span>
        </button>
    );
}
 
export default function Index({ assets, totalValue }) {
    const [search, setSearch]       = useState('');
    const [typeFilter, setTypeFilter] = useState('all'); // 'all' | 'fixed_asset' | 'inventory'
 
    const filtered = assets.filter(a => {
        const matchSearch =
            a.name.toLowerCase().includes(search.toLowerCase()) ||
            a.asset_tag.toLowerCase().includes(search.toLowerCase()) ||
            (a.location ?? '').toLowerCase().includes(search.toLowerCase()) ||
            (a.custodian_name ?? '').toLowerCase().includes(search.toLowerCase())
            ;
        const matchType =
            typeFilter === 'all' || a.asset_type === typeFilter;
        return matchSearch && matchType;
    });
 
    const fixedCount     = assets.filter(a => a.asset_type === 'fixed_asset').length;
    const inventoryCount = assets.filter(a => a.asset_type === 'inventory').length;
 
    const thStyle = {
        padding      : '12px 20px',
        fontSize     : '11px',
        fontWeight   : 700,
        letterSpacing: '0.07em',
        textTransform: 'uppercase',
        color        : UTM.gray500,
        textAlign    : 'left',
        borderBottom : `2px solid ${UTM.gray100}`,
        background   : UTM.gray50,
        whiteSpace   : 'nowrap',
    };
 
    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                    <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                        Inventori Aset CAIRO
                    </h2>
                </div>
            }
        >
            <Head title="Inventori Aset" />
 
            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 1280, margin: '0 auto' }}>
 
                    {/* ── Top bar ── */}
                    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 16,
                                  alignItems: 'flex-end', marginBottom: 20 }}>
 
                        {/* Total value card */}
                        <div style={{
                            background   : UTM.maroon,
                            borderRadius : 10,
                            padding      : '18px 24px',
                            borderLeft   : `4px solid ${UTM.gold}`,
                            boxShadow    : '0 2px 8px rgba(92,0,31,0.15)',
                            minWidth     : 220,
                        }}>
                            <p style={{ fontSize: '11px', fontWeight: 700, textTransform: 'uppercase',
                                        letterSpacing: '0.08em', color: UTM.sand, marginBottom: 4 }}>
                                Jumlah Nilai Portfolio
                            </p>
                            <p style={{ fontSize: '28px', fontWeight: 900, color: UTM.white, lineHeight: 1 }}>
                                RM {Number(totalValue).toLocaleString()}
                            </p>
                        </div>
 
                        {/* Spacer */}
                        <div style={{ flex: 1 }} />
 
                        {/* Search */}
                        <div style={{ position: 'relative', width: 280 }}>
                            <span style={{ position: 'absolute', left: 12, top: '50%',
                                           transform: 'translateY(-50%)', color: UTM.gray300,
                                           fontSize: 16, pointerEvents: 'none' }}>🔍</span>
                            <input
                                type="text"
                                placeholder="Cari nama, tag, lokasi..."
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                style={{
                                    width        : '100%',
                                    padding      : '10px 14px 10px 36px',
                                    borderRadius : 8,
                                    border       : `1.5px solid ${UTM.gray100}`,
                                    fontSize     : '13px',
                                    color        : UTM.gray900,
                                    background   : UTM.white,
                                    outline      : 'none',
                                    boxSizing    : 'border-box',
                                }}
                            />
                        </div>
                    </div>
 
                    {/* ── Filter tabs ── */}
                    <div style={{ display: 'flex', gap: 8, marginBottom: 16 }}>
                        <FilterTab
                            label="Semua Aset"
                            count={assets.length}
                            active={typeFilter === 'all'}
                            onClick={() => setTypeFilter('all')}
                        />
                        <FilterTab
                            label="Aset Tetap"
                            count={fixedCount}
                            active={typeFilter === 'fixed_asset'}
                            onClick={() => setTypeFilter('fixed_asset')}
                        />
                        <FilterTab
                            label="Inventori"
                            count={inventoryCount}
                            active={typeFilter === 'inventory'}
                            onClick={() => setTypeFilter('inventory')}
                        />
                    </div>
 
                    {/* ── Table ── */}
                    <div style={{ background: UTM.white, borderRadius: 12,
                                  boxShadow: '0 1px 4px rgba(92,0,31,0.07)', overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr>
                                    <th style={thStyle}>Tag Aset</th>
                                    <th style={thStyle}>Nama Aset</th>
                                    <th style={thStyle}>Jenis</th>
                                    <th style={thStyle}>Kategori</th>
                                    <th style={thStyle}>Harga (RM)</th>
                                    <th style={thStyle}>Pegawai</th>
                                    <th style={thStyle}>Lokasi</th>
                                    <th style={thStyle}>Waranti</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.length === 0 && (
                                    <tr>
                                        <td colSpan={8} style={{ padding: '48px', textAlign: 'center',
                                                                  color: UTM.gray300, fontSize: '14px' }}>
                                            Tiada aset dijumpai.
                                        </td>
                                    </tr>
                                )}
                                {filtered.map((asset, idx) => (
                                    <tr key={asset.id} style={{
                                        background  : idx % 2 === 0 ? UTM.white : UTM.gray50,
                                        transition  : 'background 0.12s',
                                    }}
                                    onMouseEnter={e => e.currentTarget.style.background = '#FFF5E8'}
                                    onMouseLeave={e => e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50}
                                    >
                                        <td style={{ padding: '14px 20px', fontFamily: 'monospace',
                                                     fontWeight: 700, color: UTM.maroon,
                                                     fontSize: '12px', textTransform: 'uppercase',
                                                     whiteSpace: 'nowrap' }}>
                                            {asset.asset_tag}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontWeight: 600,
                                                     color: UTM.gray900, fontSize: '13px' }}>
                                            {asset.name}
                                        </td>
                                        <td style={{ padding: '14px 20px' }}>
                                            {asset.asset_type
                                                ? <TypeBadge type={asset.asset_type} />
                                                : <span style={{ color: UTM.gray300, fontSize: '12px' }}>—</span>
                                            }
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px',
                                                     color: UTM.gray700 }}>
                                            {asset.category || '—'}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px',
                                                     fontWeight: 700, color: UTM.gray900 }}>
                                            {Number(asset.purchase_price).toLocaleString()}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>
                                            {asset.custodian_name || <span style={{color: UTM.gray300, fontStyle: 'italic'}}>Belum ditetapkan</span>}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px',
                                                     color: UTM.gray700, maxWidth: 180,
                                                     overflow: 'hidden', textOverflow: 'ellipsis',
                                                     whiteSpace: 'nowrap' }}>
                                            {asset.location || '—'}
                                        </td>
                                        <td style={{ padding: '14px 20px' }}>
                                            <WarrantyBadge expiryDate={asset.warranty_expiry} />
                                        </td>

                                        <td style={{ padding: '14px 20px' }}>
                                            <StatusBadge status={asset.status} />
                                        </td>
                                        <td style={{ padding: '14px 20px', textAlign: 'right',
                                                     whiteSpace: 'nowrap' }}>
                                            <Link
                                                href={route('assets.kewpa3', asset.id)}
                                                style={{
                                                    display     : 'inline-block',
                                                    padding     : '5px 12px',
                                                    borderRadius: 6,
                                                    fontSize    : '12px',
                                                    fontWeight  : 700,
                                                    background  : '#EDE9E4',
                                                    color       : UTM.gray700,
                                                    textDecoration: 'none',
                                                    marginRight : 6,
                                                    transition  : 'background 0.12s',
                                                }}
                                            >
                                                Lihat
                                            </Link>
 
                                            <a
                                                // FORK LOGIC: Fixed Assets get KEW.PA-2, Inventory gets KEW.PA-3
                                                href={asset.asset_type === 'fixed_asset' ? route('assets.kewpa2.download', asset.id) : route('assets.kewpa3.download', asset.id)}
                                                style={{
                                                    display     : 'inline-block',
                                                    padding     : '5px 12px',
                                                    borderRadius: 6,
                                                    fontSize    : '12px',
                                                    fontWeight  : 700,
                                                    background  : UTM.maroon,
                                                    color       : UTM.white,
                                                    textDecoration: 'none',
                                                    marginRight : 6,
                                                }}
                                            >
                                                {asset.asset_type === 'fixed_asset' ? 'KEW.PA-2' : 'KEW.PA-3'}
                                            </a>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
 
                        {/* Table footer */}
                        <div style={{ padding: '12px 20px', borderTop: `1px solid ${UTM.gray100}`,
                                      background: UTM.gray50, fontSize: '12px', color: UTM.gray500 }}>
                            Menunjukkan <strong style={{ color: UTM.maroon }}>{filtered.length}</strong> daripada{' '}
                            <strong style={{ color: UTM.maroon }}>{assets.length}</strong> aset
                        </div>
                    </div>
 
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
 