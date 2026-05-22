import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
 
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
            whiteSpace   : 'nowrap',
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
            borderRadius : '999px',
            fontSize     : '11px',
            fontWeight   : 700,
            background   : isFixed ? '#F3E0E5' : '#FEF3D6',
            color        : isFixed ? UTM.maroon : UTM.goldDark,
            border       : `1px solid ${isFixed ? '#E8C0CB' : '#F5D890'}`,
            whiteSpace   : 'nowrap',
        }}>
            {isFixed ? 'Aset Tetap' : 'Inventori'}
        </span>
    );
}

function WarrantyBadge({ expiryDate }) {
    if (!expiryDate) {
        return <span style={{ color: UTM.gray300, fontSize: '12px' }}>—</span>;
    }

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
        valid:    { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2' },
        expiring: { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890' },
        expired:  { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB' },
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
            whiteSpace   : 'nowrap',
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
    const [search, setSearch]         = useState('');
    const [typeFilter, setTypeFilter] = useState('all'); 
    const [expandedId, setExpandedId] = useState(null); 
 
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
 
                        <div style={{ flex: 1 }} />
 
                        <div style={{ position: 'relative', width: 280, maxWidth: '100%' }}>
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
                    <div style={{ display: 'flex', gap: 8, marginBottom: 16, flexWrap: 'wrap' }}>
                        <FilterTab
                            label="Semua Aset" count={assets.length}
                            active={typeFilter === 'all'} onClick={() => setTypeFilter('all')}
                        />
                        <FilterTab
                            label="Aset Tetap" count={fixedCount}
                            active={typeFilter === 'fixed_asset'} onClick={() => setTypeFilter('fixed_asset')}
                        />
                        <FilterTab
                            label="Inventori" count={inventoryCount}
                            active={typeFilter === 'inventory'} onClick={() => setTypeFilter('inventory')}
                        />
                    </div>
 
                    {/* ── Table Container (Scrollable) ── */}
                    <div style={{ background: UTM.white, borderRadius: 12,
                                  boxShadow: '0 1px 4px rgba(92,0,31,0.07)', overflow: 'hidden' }}>
                        <div style={{ overflowX: 'auto', width: '100%' }}>
                            <table style={{ width: '100%', minWidth: '1050px', borderCollapse: 'collapse' }}>
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
                                            <td colSpan={10} style={{ padding: '48px', textAlign: 'center',
                                                                    color: UTM.gray300, fontSize: '14px' }}>
                                                Tiada aset dijumpai.
                                            </td>
                                        </tr>
                                    )}
                                    {filtered.map((asset, idx) => (
                                        <React.Fragment key={asset.id}>
                                            <tr style={{
                                                background  : expandedId === asset.id ? '#FFF5E8' : (idx % 2 === 0 ? UTM.white : UTM.gray50),
                                                transition  : 'background 0.12s',
                                            }}
                                            onMouseEnter={e => { if(expandedId !== asset.id) e.currentTarget.style.background = '#FFF5E8' }}
                                            onMouseLeave={e => { if(expandedId !== asset.id) e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50 }}
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
                                                <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>
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
                                                <td style={{ padding: '14px 20px', textAlign: 'right', whiteSpace: 'nowrap' }}>
                                                    {/* Expand Details Button */}
                                                    <button
                                                        onClick={() => setExpandedId(expandedId === asset.id ? null : asset.id)}
                                                        style={{
                                                            display     : 'inline-block',
                                                            padding     : '5px 12px',
                                                            borderRadius: 6,
                                                            fontSize    : '12px',
                                                            fontWeight  : 700,
                                                            background  : expandedId === asset.id ? UTM.gold : '#EDE9E4',
                                                            color       : expandedId === asset.id ? UTM.maroon : UTM.gray700,
                                                            border      : 'none',
                                                            cursor      : 'pointer',
                                                            marginRight : 6,
                                                            transition  : 'all 0.12s',
                                                        }}
                                                    >
                                                        {expandedId === asset.id ? 'Tutup' : 'Butiran'}
                                                    </button>
                                                    
                                                    {/* Placement / Borang Link */}
                                                    <Link
                                                        href={asset.asset_type === 'fixed_asset' ? route('assets.kewpa2', asset.id) : route('assets.kewpa3', asset.id)}
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
                                                        Borang
                                                    </Link>
        
                                                    {/* PDF Download Button */}
                                                    <a
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

                                                    {/* Label Download Button */}
                                                    <a
                                                        href={route('assets.label', asset.id)}
                                                        style={{
                                                            display     : 'inline-block',
                                                            padding     : '5px 12px',
                                                            borderRadius: 6,
                                                            fontSize    : '12px',
                                                            fontWeight  : 700,
                                                            background  : '#7B1FA2',
                                                            color       : UTM.white,
                                                            textDecoration: 'none',
                                                            marginRight : 6,
                                                        }}
                                                    >
                                                        Label
                                                    </a>

                                                    {/* Edit Button */}
                                                    <Link
                                                        href={route('assets.edit', asset.id)}
                                                        style={{
                                                            display     : 'inline-block',
                                                            padding     : '5px 12px',
                                                            borderRadius: 6,
                                                            fontSize    : '12px',
                                                            fontWeight  : 700,
                                                            background  : '#EEF2FF',
                                                            color       : '#4338CA',
                                                            textDecoration: 'none',
                                                            marginRight : 6,
                                                            border      : '1px solid #C7D2FE',
                                                        }}
                                                    >
                                                        Edit
                                                    </Link>

                                                    {/* Delete Button */}
                                                    <button
                                                        onClick={() => {
                                                            if (window.confirm(`Padam aset "${asset.name}" (${asset.asset_tag})?\nSemua rekod berkaitan akan turut dipadam.`)) {
                                                                router.delete(route('assets.destroy', asset.id));
                                                            }
                                                        }}
                                                        style={{
                                                            display     : 'inline-block',
                                                            padding     : '5px 12px',
                                                            borderRadius: 6,
                                                            fontSize    : '12px',
                                                            fontWeight  : 700,
                                                            background  : '#FEF2F2',
                                                            color       : '#DC2626',
                                                            border      : '1px solid #FECACA',
                                                            cursor      : 'pointer',
                                                        }}
                                                    >
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>

                                            {/* ── Collapsible Expandable Row ── */}
                                            {expandedId === asset.id && (
                                                <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                                    <td colSpan={10} style={{ padding: 0 }}>
                                                        <div style={{ 
                                                            padding: '24px 32px', 
                                                            display: 'flex', 
                                                            flexWrap: 'wrap', // Allows sections to drop below if squished
                                                            gap: '32px', 
                                                            borderLeft: `4px solid ${UTM.gold}` 
                                                        }}>
                                                            
                                                            {/* Image Section */}
                                                            <div style={{ width: '220px', flexShrink: 0 }}>
                                                                {asset.image_url ? (
                                                                    <img 
                                                                        src={asset.image_url} 
                                                                        alt={asset.name} 
                                                                        style={{ width: '100%', height: '220px', objectFit: 'cover', borderRadius: '8px', border: `1px solid ${UTM.gray300}`, boxShadow: '0 2px 8px rgba(0,0,0,0.1)' }} 
                                                                    />
                                                                ) : (
                                                                    <div style={{ width: '100%', height: '220px', background: UTM.gray100, borderRadius: '8px', display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'center', color: UTM.gray500, border: `1px dashed ${UTM.gray300}` }}>
                                                                        <span style={{ fontSize: '24px', marginBottom: '8px' }}>📷</span>
                                                                        <span style={{ fontSize: '12px', fontWeight: 600 }}>Tiada Gambar</span>
                                                                    </div>
                                                                )}
                                                            </div>

                                                            {/* Info Columns Grid */}
                                                            <div style={{ 
                                                                flex: '1 1 600px', // Tells flexbox it wants to take remaining space, but wraps if less than 600px
                                                                display: 'grid', 
                                                                gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', 
                                                                gap: '24px' 
                                                            }}>
                                                                
                                                                {/* Spesifikasi */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Spesifikasi Teknikal</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>Jenama</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.brand || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Model</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.model || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>No. Siri / Casis</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.serial_number || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>No. Bar Kod</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.national_code || asset.asset_tag}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                {/* Kewangan */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Kewangan & Pembelian</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>SAGA ID</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.saga_id || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Vot Bajet</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.budget_vot || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>No. Pesanan (PO)</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.po_reference || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Tarikh Diterima</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.received_date ? new Date(asset.received_date).toLocaleDateString('ms-MY') : new Date(asset.created_at).toLocaleDateString('ms-MY')}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                {/* Maklumat Pembekal */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Maklumat Pembekal</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>Nama</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.supplier_name || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500, verticalAlign: 'top' }}>Alamat</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.supplier_address || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Penyelenggaraan</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.requires_maintenance ? 'Perlu (Berkala)' : 'Tidak Perlu'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Tamat Waranti</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{asset.warranty_expiry ? new Date(asset.warranty_expiry).toLocaleDateString('ms-MY') : '—'}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            )}
                                        </React.Fragment>
                                    ))}
                                </tbody>
                            </table>
                        </div>
 
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