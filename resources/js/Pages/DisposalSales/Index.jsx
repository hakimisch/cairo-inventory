import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

const labelStyle = {
    display   : 'block',
    fontSize  : '12px',
    fontWeight: 700,
    color     : UTM.gray700,
    marginBottom: 4,
    textTransform: 'uppercase',
    letterSpacing: '0.5px',
};

const inputStyle = {
    width        : '100%',
    padding      : '8px 12px',
    borderRadius : 6,
    border       : `1.5px solid ${UTM.gray200}`,
    fontSize     : '13px',
    color        : UTM.gray700,
    background   : UTM.white,
    outline      : 'none',
    transition   : 'border-color 0.15s',
    boxSizing    : 'border-box',
};

function SaleTypeBadge({ type }) {
    const map = {
        Tawaran   : { bg: '#DBEAFE', color: '#1E40AF' },
        Sebutharga: { bg: '#EDE9FE', color: '#5B21B6' },
        Lelongan  : { bg: '#FFEDD5', color: '#9A3412' },
    };
    const s = map[type] || { bg: UTM.gray100, color: UTM.gray600 };
    return (
        <span style={{
            display       : 'inline-block',
            padding       : '2px 10px',
            borderRadius  : 999,
            fontSize      : '11px',
            fontWeight    : 700,
            background    : s.bg,
            color         : s.color,
        }}>
            {type}
        </span>
    );
}

function StatusBadge({ status }) {
    const map = {
        draft    : { label: 'Draf',       bg: '#F3F4F6', color: '#374151' },
        active   : { label: 'Aktif',      bg: '#D1FAE5', color: '#065F46' },
        completed: { label: 'Selesai',    bg: '#DBEAFE', color: '#1E40AF' },
        cancelled: { label: 'Batal',      bg: '#FEE2E2', color: '#991B1B' },
    };
    const s = map[status] || { label: status, bg: UTM.gray100, color: UTM.gray600 };
    return (
        <span style={{
            display       : 'inline-block',
            padding       : '2px 10px',
            borderRadius  : 999,
            fontSize      : '11px',
            fontWeight    : 700,
            background    : s.bg,
            color         : s.color,
        }}>
            {s.label}
        </span>
    );
}

const KEWPA_FORMS = [
    { label: 'PA-21 Tawaran Jualan',       route: 'disposal-sales.kewpa21' },
    { label: 'PA-22 Sebutharga Jualan',     route: 'disposal-sales.kewpa22' },
    { label: 'PA-23 Lelongan Jualan',       route: 'disposal-sales.kewpa23' },
    { label: 'PA-24 Keputusan',             route: 'disposal-sales.kewpa24' },
    { label: 'PA-25 Laporan',               route: 'disposal-sales.kewpa25' },
    { label: 'PA-26 Perakuan (T/S/L)',      route: 'disposal-sales.kewpa26' },
    { label: 'PA-27 Perakuan (Pelupusan)',  route: 'disposal-sales.kewpa27' },
    { label: 'PA-27A Perakuan (Lupus)',     route: 'disposal-sales.kewpa27a' },
];

function FormDropdown({ saleId, open, onToggle }) {
    return (
        <div style={{ position: 'relative', display: 'inline-block' }}>
            <button
                onClick={onToggle}
                style={{
                    display       : 'inline-block',
                    padding       : '5px 12px',
                    borderRadius  : 6,
                    fontSize      : '11px',
                    fontWeight    : 600,
                    border        : 'none',
                    cursor        : 'pointer',
                    background    : UTM.maroon,
                    color         : UTM.white,
                    transition    : 'all 0.12s',
                }}
            >
                Forms ▾
            </button>
            {open && (
                <>
                    <div
                        onClick={onToggle}
                        style={{
                            position: 'fixed', top: 0, left: 0, right: 0, bottom: 0,
                            zIndex: 49, background: 'transparent',
                        }}
                    />
                    <div style={{
                        position  : 'absolute',
                        top       : '100%',
                        right     : 0,
                        zIndex    : 50,
                        background: UTM.white,
                        borderRadius : 8,
                        boxShadow    : '0 4px 16px rgba(0,0,0,0.14)',
                        border       : `1px solid ${UTM.gray100}`,
                        padding      : 6,
                        minWidth     : 220,
                        marginTop    : 4,
                    }}>
                        {KEWPA_FORMS.map((f) => (
                            <a
                                key={f.route}
                                href={route(f.route, saleId)}
                                style={{
                                    display      : 'block',
                                    padding      : '7px 12px',
                                    borderRadius : 4,
                                    fontSize     : '12px',
                                    color        : UTM.gray700,
                                    textDecoration: 'none',
                                    whiteSpace   : 'nowrap',
                                    transition   : 'background 0.1s',
                                }}
                                onMouseEnter={e => { e.target.style.background = UTM.gray50; }}
                                onMouseLeave={e => { e.target.style.background = 'transparent'; }}
                            >
                                {f.label}
                            </a>
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}

export default function Index({ sales, filters, typeCounts }) {
    const [search, setSearch]           = useState(filters?.search || '');
    const [saleTypeFilter, setSaleTypeFilter] = useState(filters?.sale_type || '');
    const [openFormId, setOpenFormId]   = useState(null);
    const [expandedId, setExpandedId]   = useState(null);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('disposal-sales.index'),
            { search, sale_type: saleTypeFilter },
            { preserveState: true, replace: true }
        );
    };

    const handleReset = () => {
        setSearch('');
        setSaleTypeFilter('');
        router.get(route('disposal-sales.index'), {}, { preserveState: true, replace: true });
    };

    const handleFilterClick = (type) => {
        const newType = saleTypeFilter === type ? '' : type;
        setSaleTypeFilter(newType);
        router.get(route('disposal-sales.index'),
            newType ? { sale_type: newType, search } : { search },
            { preserveState: true, replace: true }
        );
    };

    const thStyle = {
        padding     : '12px 16px',
        fontSize    : '11px',
        fontWeight  : 700,
        color       : UTM.gray500,
        textTransform: 'uppercase',
        letterSpacing: '0.5px',
        textAlign   : 'left',
        borderBottom: `2px solid ${UTM.gray200}`,
        background  : UTM.gray50,
    };

    const tdStyle = {
        padding    : '14px 16px',
        fontSize   : '13px',
        color      : UTM.gray700,
        borderBottom: `1px solid ${UTM.gray100}`,
    };

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                    <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                        Disposal Sales — KEW.PA-21 to PA-27A
                    </h2>
                </div>
            }
        >
            <Head title="Disposal Sales" />

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 1280, margin: '0 auto' }}>

                    {/* ── Search & Filter Bar ─────────────────────────────── */}
                    <form onSubmit={handleSearch} style={{
                        display       : 'flex',
                        gap           : 16,
                        alignItems    : 'flex-end',
                        marginBottom  : 20,
                        padding       : 20,
                        background    : UTM.white,
                        borderRadius  : 12,
                        border        : `1px solid ${UTM.gray100}`,
                        flexWrap      : 'wrap',
                    }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={labelStyle}>Carian</label>
                            <input
                                type="text"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                placeholder="Cari rujukan, pegawai, lokasi..."
                                style={inputStyle}
                            />
                        </div>
                        <button type="submit" style={{
                            padding      : '9px 24px',
                            borderRadius : 8,
                            border       : 'none',
                            background   : UTM.maroon,
                            color        : UTM.white,
                            fontSize     : '13px',
                            fontWeight   : 700,
                            cursor       : 'pointer',
                            whiteSpace   : 'nowrap',
                        }}>
                            Cari
                        </button>
                        <button type="button" onClick={handleReset} style={{
                            padding      : '9px 24px',
                            borderRadius : 8,
                            border       : `1.5px solid ${UTM.gray100}`,
                            background   : UTM.white,
                            color        : UTM.gray700,
                            fontSize     : '13px',
                            fontWeight   : 600,
                            cursor       : 'pointer',
                            whiteSpace   : 'nowrap',
                        }}>
                            Reset
                        </button>
                    </form>

                    {/* ── Table Card ───────────────────────────────────────── */}
                    <div style={{
                        background   : UTM.white,
                        borderRadius : 12,
                        boxShadow    : '0 1px 8px rgba(0,0,0,0.06)',
                        border       : `1px solid ${UTM.gray100}`,
                        overflow     : 'hidden',
                    }}>
                        {/* ── Filter Buttons ───────────────────────────────── */}
                        <div style={{
                            padding      : '16px 20px',
                            borderBottom : `1px solid ${UTM.gray100}`,
                            display      : 'flex',
                            alignItems   : 'center',
                            flexWrap     : 'wrap',
                            gap          : 8,
                        }}>
                            {[
                                { label: 'Semua', value: '', count: typeCounts?.total ?? 0 },
                                { label: 'Tawaran', value: 'Tawaran', count: typeCounts?.Tawaran ?? 0 },
                                { label: 'Sebutharga', value: 'Sebutharga', count: typeCounts?.Sebutharga ?? 0 },
                                { label: 'Lelongan', value: 'Lelongan', count: typeCounts?.Lelongan ?? 0 },
                            ].map((t) => (
                                <button
                                    key={t.value}
                                    onClick={() => handleFilterClick(t.value)}
                                    style={{
                                        padding      : '7px 16px',
                                        borderRadius : 8,
                                        fontSize     : '12px',
                                        fontWeight   : 700,
                                        border       : 'none',
                                        cursor       : 'pointer',
                                        transition   : 'all 0.12s',
                                        background   : saleTypeFilter === t.value ? UTM.maroon : UTM.white,
                                        color        : saleTypeFilter === t.value ? UTM.white : UTM.gray600,
                                        boxShadow    : saleTypeFilter === t.value ? '0 2px 8px rgba(92,0,31,0.18)' : '0 1px 2px rgba(0,0,0,0.06)',
                                    }}
                                >
                                    {t.label}
                                    <span style={{
                                        marginLeft   : 7,
                                        display      : 'inline-block',
                                        padding      : '1px 7px',
                                        borderRadius : 999,
                                        fontSize     : '11px',
                                        background   : saleTypeFilter === t.value ? 'rgba(248,166,23,0.25)' : UTM.gray100,
                                        color        : saleTypeFilter === t.value ? UTM.gold : UTM.gray500,
                                        fontWeight   : 800,
                                    }}>
                                        {t.count}
                                    </span>
                                </button>
                            ))}
                        </div>

                        {/* ── Table ────────────────────────────────────────── */}
                        <div style={{ overflowX: 'auto', width: '100%' }}>
                            <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: 800 }}>
                                <thead>
                                    <tr>
                                        <th style={thStyle}>Rujukan</th>
                                        <th style={thStyle}>Jenis</th>
                                        <th style={thStyle}>Aset</th>
                                        <th style={thStyle}>Tarikh</th>
                                        <th style={thStyle}>Status</th>
                                        <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {sales.data?.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500, padding: '40px 16px' }}>
                                                Tiada rekod jualan pelupusan.
                                            </td>
                                        </tr>
                                    ) : (
sales.data?.map((sale, index) => (
                                            <React.Fragment key={sale.id}>
                                                <tr
                                                    style={{
                                                        background  : expandedId === sale.id ? '#FFF5E8' : (index % 2 === 0 ? UTM.white : UTM.gray50),
                                                        transition  : 'background 0.12s',
                                                    }}
                                                    onMouseEnter={e => { if(expandedId !== sale.id) e.currentTarget.style.background = '#FFF5E8' }}
                                                    onMouseLeave={e => { if(expandedId !== sale.id) e.currentTarget.style.background = index % 2 === 0 ? UTM.white : UTM.gray50 }}
                                                >
                                                    <td style={tdStyle}>
                                                        <span style={{ fontWeight: 600, color: UTM.maroon }}>{sale.sale_reference}</span>
                                                    </td>
                                                    <td style={tdStyle}><SaleTypeBadge type={sale.sale_type} /></td>
                                                    <td style={tdStyle}>
                                                        {sale.asset_disposal?.asset?.name ?? '—'}
                                                        <span style={{ fontFamily: 'monospace', fontSize: 11, color: UTM.gray500, marginLeft: 6 }}>
                                                            ({sale.asset_disposal?.asset?.asset_tag ?? '—'})
                                                        </span>
                                                    </td>
                                                    <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                                        {sale.sale_date ?? '—'}
                                                    </td>
                                                    <td style={tdStyle}><StatusBadge status={sale.status} /></td>
                                                    <td style={{ ...tdStyle, whiteSpace: 'nowrap', textAlign: 'right' }}>
                                                        <button
                                                            onClick={() => setExpandedId(expandedId === sale.id ? null : sale.id)}
                                                            style={{
                                                                display     : 'inline-block',
                                                                padding     : '5px 12px',
                                                                borderRadius: 6,
                                                                fontSize    : '11px',
                                                                fontWeight  : 600,
                                                                background  : expandedId === sale.id ? UTM.gold : '#EDE9E4',
                                                                color       : expandedId === sale.id ? UTM.maroon : UTM.gray700,
                                                                border      : 'none',
                                                                cursor      : 'pointer',
                                                                transition  : 'all 0.12s',
                                                            }}
                                                        >
                                                            {expandedId === sale.id ? 'Tutup' : 'Butiran'}
                                                        </button>
                                                    </td>
                                                </tr>

                                                {/* ── Collapsible Expandable Row ── */}
                                                {expandedId === sale.id && (
                                                    <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                                        <td colSpan={6} style={{ padding: 0 }}>
                                                            <div style={{ 
                                                                padding: '20px 24px', 
                                                                display: 'flex', 
                                                                flexWrap: 'wrap',
                                                                gap: '24px', 
                                                                borderLeft: `4px solid ${UTM.gold}` 
                                                            }}>
                                                                {/* Sale Details Grid */}
                                                                <div style={{ flex: '1 1 400px', display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Rujukan</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.sale_reference}</div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Jenis</div>
                                                                        <SaleTypeBadge type={sale.sale_type} />
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Tarikh</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.sale_date ?? '—'}</div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Lokasi</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.sale_location ?? '—'}</div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Pegawai</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.sale_officer ?? '—'}</div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Deposit</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>
                                                                            {sale.deposit_required ? `RM ${Number(sale.deposit_required).toLocaleString()}` : '—'}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Status</div>
                                                                        <StatusBadge status={sale.status} />
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Status Jualan</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.sale_status ?? '—'}</div>
                                                                    </div>
                                                                </div>

                                                                {/* Description */}
                                                                {sale.description && (
                                                                    <div style={{ width: '100%' }}>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Penerangan</div>
                                                                        <p style={{ fontSize: '13px', color: UTM.gray700, margin: 0 }}>{sale.description}</p>
                                                                    </div>
                                                                )}

                                                                {/* Asset Disposal Info */}
                                                                {sale.asset_disposal && (
                                                                    <div style={{ width: '100%', borderTop: `1px solid ${UTM.gray100}`, paddingTop: 16 }}>
                                                                        <h4 style={{ fontSize: '11px', fontWeight: 700, color: UTM.maroon, textTransform: 'uppercase', margin: '0 0 8px' }}>
                                                                            Pelupusan Aset
                                                                        </h4>
                                                                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))', gap: 12 }}>
                                                                            <div>
                                                                                <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Aset</div>
                                                                                <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>
                                                                                    {sale.asset_disposal.asset?.name} ({sale.asset_disposal.asset?.asset_tag})
                                                                                </div>
                                                                            </div>
                                                                            <div>
                                                                                <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Kaedah</div>
                                                                                <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.asset_disposal.disposal_method}</div>
                                                                            </div>
                                                                            <div>
                                                                                <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Tarikh</div>
                                                                                <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.asset_disposal.disposal_date ?? '—'}</div>
                                                                            </div>
                                                                            <div>
                                                                                <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Status</div>
                                                                                <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{sale.asset_disposal.status}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                )}

                                                                {/* Sale Items */}
                                                                {sale.disposal_sale_items?.length > 0 && (
                                                                    <div style={{ width: '100%', borderTop: `1px solid ${UTM.gray100}`, paddingTop: 16 }}>
                                                                        <h4 style={{ fontSize: '11px', fontWeight: 700, color: UTM.maroon, textTransform: 'uppercase', margin: '0 0 8px' }}>
                                                                            Item Jualan ({sale.disposal_sale_items.length})
                                                                        </h4>
                                                                        {sale.disposal_sale_items.map((item) => (
                                                                            <div key={item.id} style={{ padding: '8px 0', borderBottom: `1px solid ${UTM.gray50}`, display: 'flex', flexWrap: 'wrap', gap: 16, alignItems: 'center' }}>
                                                                                <span style={{ fontSize: 13, fontWeight: 600, color: UTM.gray700, minWidth: 100 }}>{item.item_description ?? item.asset?.name ?? '—'}</span>
                                                                                <span style={{ fontSize: 12, color: UTM.gray500 }}>Lot: {item.lot_number ?? '—'}</span>
                                                                                <span style={{ fontSize: 12, color: UTM.gray500 }}>Qty: {item.quantity}</span>
                                                                                <span style={{ fontSize: 12, fontWeight: 700, color: UTM.gray900 }}>{item.reserve_price ? `RM ${Number(item.reserve_price).toLocaleString()}` : '—'}</span>
                                                                                <span style={{ fontSize: 12, color: UTM.gray500 }}>{item.status}</span>
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                )}

                                                                {/* KEW.PA Forms */}
                                                                <div style={{ width: '100%', borderTop: `1px solid ${UTM.gray100}`, paddingTop: 16 }}>
                                                                    <h4 style={{ fontSize: '11px', fontWeight: 700, color: UTM.maroon, textTransform: 'uppercase', margin: '0 0 8px' }}>
                                                                        KEW.PA Forms
                                                                    </h4>
                                                                    <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6 }}>
                                                                        {KEWPA_FORMS.map((f) => (
                                                                            <a
                                                                                key={f.route}
                                                                                href={route(f.route, sale.id)}
                                                                                style={{
                                                                                    display       : 'inline-block',
                                                                                    padding       : '5px 12px',
                                                                                    borderRadius  : 6,
                                                                                    fontSize      : '11px',
                                                                                    fontWeight    : 600,
                                                                                    background    : UTM.maroon,
                                                                                    color         : UTM.white,
                                                                                    textDecoration: 'none',
                                                                                    transition    : 'all 0.12s',
                                                                                }}
                                                                            >
                                                                                {f.label}
                                                                            </a>
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </React.Fragment>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* ── Pagination ───────────────────────────────────── */}
                        {sales.links && (
                            <div style={{
                                display        : 'flex',
                                justifyContent : 'center',
                                gap            : 6,
                                padding        : '16px 24px',
                                borderTop      : `1px solid ${UTM.gray100}`,
                                flexWrap       : 'wrap',
                            }}>
                                {sales.links.map((link, i) => {
                                    if (!link.url) return null;
                                    const isActive = link.active;
                                    return (
                                        <Link
                                            key={i}
                                            href={link.url}
                                            style={{
                                                padding      : '6px 12px',
                                                borderRadius : 6,
                                                fontSize     : '12px',
                                                fontWeight   : isActive ? 700 : 500,
                                                background   : isActive ? UTM.maroon : UTM.gray100,
                                                color        : isActive ? UTM.white : UTM.gray700,
                                                textDecoration: 'none',
                                                transition   : 'all 0.12s',
                                            }}
                                            preserveState
                                            preserveScroll
                                        >
                                            <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                        </Link>
                                    );
                                })}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
