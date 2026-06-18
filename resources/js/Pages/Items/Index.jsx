import React, { useState, useMemo } from 'react';
import { Head, Link, router, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

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
    gray200: '#D5CFC9',
    gray300: '#B0A89F',
    gray500: '#8A8480',
    gray600: '#625E5B',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

// ─── Source colors ────────────────────────────────────────────────────────────
const SOURCE_STYLES = {
    PO:    { bg: '#E3F2FD', color: '#1565C0', border: '#90CAF9', label: 'PO' },
    DO:    { bg: '#FFF3E0', color: '#E65100', border: '#FFCC80', label: 'DO' },
    Asset: { bg: '#E8F5E9', color: '#2E7D32', border: '#A5D6A7', label: 'Aset' },
};

// ─── DO Line Item status colors ──────────────────────────────────────────────
const STATUS_STYLES = {
    pending:  { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Pending' },
    scanned:  { bg: '#E3F2FD', color: '#1565C0',    border: '#90CAF9', label: 'Scanned' },
    verified: { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Verified' },
    received: { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Received' },
    no_serial:{ bg: '#F3E5F5', color: '#7B1FA2',    border: '#CE93D8', label: 'No S/N' },
    damaged:  { bg: '#FFEBEE', color: '#C62828',    border: '#EF9A9A', label: 'Damaged' },
    shortage: { bg: '#FFEBEE', color: '#B71C1C',    border: '#EF9A9A', label: 'Shortage' },
    active:   { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Aktif' },
    repair:   { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Selenggara' },
    disposed: { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Dilupus' },
    complete: { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Selesai' },
};

function StatusBadge({ status }) {
    const s = STATUS_STYLES[status] || { bg: UTM.gray100, color: UTM.gray500, border: UTM.gray200, label: status };
    return (
        <span style={{
            display: 'inline-block', padding: '2px 8px', borderRadius: 999,
            fontSize: 11, fontWeight: 700, whiteSpace: 'nowrap',
            background: s.bg, color: s.color, border: `1px solid ${s.border}`,
        }}>
            {s.label}
        </span>
    );
}

function SourceBadge({ source }) {
    const s = SOURCE_STYLES[source] || SOURCE_STYLES.PO;
    return (
        <span style={{
            display: 'inline-block', padding: '2px 8px', borderRadius: 4,
            fontSize: 11, fontWeight: 700, whiteSpace: 'nowrap',
            background: s.bg, color: s.color, border: `1px solid ${s.border}`,
        }}>
            {s.label}
        </span>
    );
}

function SerialIndicator({ hasSerial, serialNo }) {
    if (serialNo) {
        return <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray700 }}>{serialNo}</span>;
    }
    if (hasSerial === false) {
        return <span style={{ fontSize: 11, color: UTM.gray500, fontStyle: 'italic' }}>Tiada S/N</span>;
    }
    return <span style={{ fontSize: 11, color: UTM.gray300 }}>—</span>;
}

function StatCard({ label, count, color }) {
    return (
        <div style={{
            background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`,
            padding: '14px 20px', textAlign: 'center', minWidth: 100,
        }}>
            <div style={{ fontSize: 28, fontWeight: 800, color: color ?? UTM.maroon }}>{count}</div>
            <div style={{ fontSize: 11, fontWeight: 600, color: UTM.gray500, marginTop: 2 }}>{label}</div>
        </div>
    );
}

export default function ItemsIndex({ items, counts, pagination, filters }) {
    const { auth } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [srcFilter, setSrcFilter] = useState(filters.source ?? '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/items', { ...filters, search, source: srcFilter, page: 1 }, { preserveState: true, replace: true });
    };

    const handlePage = (page) => {
        router.get('/items', { ...filters, search, source: srcFilter, page }, { preserveState: true, replace: true });
    };

    const handleFilter = (source) => {
        const val = source === srcFilter ? '' : source;
        setSrcFilter(val);
        router.get('/items', { ...filters, search, source: val, page: 1 }, { preserveState: true, replace: true });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Items Overview" />

            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '24px 20px' }}>
                {/* Header */}
                <div style={{ marginBottom: 24 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.gray900, margin: 0 }}>
                        Items Overview
                    </h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                        Unified view of all items across the procurement pipeline — from PO orders through DO deliveries to registered assets.
                    </p>
                </div>

                {/* Stats row */}
                <div style={{ display: 'flex', gap: 12, marginBottom: 20, flexWrap: 'wrap' }}>
                    <StatCard label="All Items"   count={counts.total} color={UTM.maroon} />
                    <StatCard label="PO Orders"   count={counts.po}    color="#1565C0" />
                    <StatCard label="DO Deliveries" count={counts.do}  color="#E65100" />
                    <StatCard label="Registered Assets" count={counts.assets} color="#2E7D32" />
                </div>

                {/* Filters */}
                <div style={{ display: 'flex', gap: 10, marginBottom: 16, alignItems: 'center', flexWrap: 'wrap' }}>
                    <form onSubmit={handleSearch} style={{ display: 'flex', gap: 8, flex: 1, minWidth: 200 }}>
                        <input
                            type="text"
                            placeholder="Search items, brand, model, serial..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            style={{
                                flex: 1, padding: '9px 14px', borderRadius: 8,
                                border: `1.5px solid ${UTM.gray100}`, fontSize: 13, outline: 'none',
                            }}
                        />
                        <button type="submit" style={{
                            padding: '9px 18px', borderRadius: 8, border: 'none',
                            background: UTM.maroon, color: UTM.white, fontWeight: 700, fontSize: 13, cursor: 'pointer',
                        }}>
                            Search
                        </button>
                    </form>

                    <div style={{ display: 'flex', gap: 6 }}>
                        {['PO', 'DO', 'Asset'].map(src => (
                            <button
                                key={src}
                                onClick={() => handleFilter(src)}
                                style={{
                                    padding: '6px 14px', borderRadius: 6, border: `1.5px solid ${srcFilter === src ? UTM.maroon : UTM.gray100}`,
                                    background: srcFilter === src ? '#FDF0F3' : UTM.white,
                                    color: srcFilter === src ? UTM.maroon : UTM.gray500,
                                    fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                }}
                            >
                                {SOURCE_STYLES[src]?.label ?? src}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Items table */}
                {items.length === 0 ? (
                    <div style={{
                        background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`,
                        padding: 48, textAlign: 'center', color: UTM.gray500, fontSize: 14,
                    }}>
                        No items found.{counts.total === 0 ? ' Import documents via the Procurement Import section to populate items.' : ' Try a different filter or search term.'}
                    </div>
                ) : (
                    <div style={{ background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                            <thead>
                                <tr style={{ background: UTM.gray50, borderBottom: `1.5px solid ${UTM.gray100}` }}>
                                    <th style={thStyle}>Source</th>
                                    <th style={thStyle}>Doc No.</th>
                                    <th style={thStyle}>Description</th>
                                    <th style={thStyle}>Brand</th>
                                    <th style={thStyle}>Model</th>
                                    <th style={thStyle}>Serial No.</th>
                                    <th style={thStyle}>Qty</th>
                                    <th style={thStyle}>Supplier</th>
                                    <th style={thStyle}>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map((item) => (
                                    <tr key={item.id} style={{ borderBottom: `1px solid ${UTM.gray50}`, transition: 'background 0.1s' }}
                                        onMouseEnter={e => e.currentTarget.style.background = UTM.gray50}
                                        onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
                                    >
                                        <td style={tdStyle}>
                                            <SourceBadge source={item.source} />
                                        </td>
                                        <td style={tdStyle}>
                                            {item.url ? (
                                                <Link href={item.url} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                    {item.source_no}
                                                </Link>
                                            ) : (
                                                <span style={{ color: UTM.gray700, fontWeight: 600 }}>{item.source_no}</span>
                                            )}
                                        </td>
                                        <td style={{ ...tdStyle, maxWidth: 220, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                            <span title={item.description}>{item.description}</span>
                                        </td>
                                        <td style={tdStyle}>{item.brand ?? '—'}</td>
                                        <td style={tdStyle}>{item.model ?? '—'}</td>
                                        <td style={tdStyle}>
                                            <SerialIndicator hasSerial={item.has_serial} serialNo={item.serial_no} />
                                        </td>
                                        <td style={{ ...tdStyle, textAlign: 'center' }}>{item.quantity}</td>
                                        <td style={{ ...tdStyle, maxWidth: 160, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}
                                            title={item.supplier}>
                                            {item.supplier}
                                        </td>
                                        <td style={tdStyle}>
                                            <StatusBadge status={item.status} />
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        {/* Pagination */}
                        {pagination.last_page > 1 && (
                            <div style={{ display: 'flex', justifyContent: 'center', gap: 6, padding: '14px 16px', borderTop: `1px solid ${UTM.gray100}` }}>
                                {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map(p => (
                                    <button
                                        key={p}
                                        onClick={() => handlePage(p)}
                                        style={{
                                            padding: '6px 12px', borderRadius: 6, border: `1.5px solid ${p === pagination.current_page ? UTM.maroon : UTM.gray100}`,
                                            background: p === pagination.current_page ? '#FDF0F3' : UTM.white,
                                            color: p === pagination.current_page ? UTM.maroon : UTM.gray500,
                                            fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                        }}
                                    >
                                        {p}
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>
                )}

                {/* Legend */}
                <div style={{ marginTop: 16, display: 'flex', gap: 16, flexWrap: 'wrap', fontSize: 12, color: UTM.gray500 }}>
                    <span>▤ <strong>PO</strong> = Purchase Order item (ordered)</span>
                    <span>▤ <strong>DO</strong> = Delivery Order item (in verification pipeline)</span>
                    <span>▤ <strong>Aset</strong> = Registered asset (PA-2/PA-3)</span>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

const thStyle = {
    padding: '10px 12px', textAlign: 'left', fontSize: 11, fontWeight: 700,
    textTransform: 'uppercase', letterSpacing: '0.05em', color: UTM.gray500,
    whiteSpace: 'nowrap',
};

const tdStyle = {
    padding: '10px 12px', color: UTM.gray700, verticalAlign: 'middle',
};
