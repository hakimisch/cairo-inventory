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

// ─── Source / status styles ────────────────────────────────────────────────────
const STATUS_STYLES = {
    // Master item statuses
    diterima:       { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Diterima' },
    belum_diterima: { bg: '#FFF3E0', color: '#E65100',    border: '#FFCC80', label: 'Belum Diterima' },
    // DO line item statuses
    pending:        { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Pending' },
    scanned:        { bg: '#E3F2FD', color: '#1565C0',    border: '#90CAF9', label: 'Scanned' },
    verified:       { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Verified' },
    received:       { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Received' },
    no_serial:      { bg: '#F3E5F5', color: '#7B1FA2',    border: '#CE93D8', label: 'No S/N' },
    damaged:        { bg: '#FFEBEE', color: '#C62828',    border: '#EF9A9A', label: 'Damaged' },
    shortage:       { bg: '#FFEBEE', color: '#B71C1C',    border: '#EF9A9A', label: 'Shortage' },
    // Asset statuses
    active:         { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Aktif' },
    repair:         { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Selenggara' },
    disposed:       { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Dilupus' },
    complete:       { bg: '#E8F5E9', color: '#2E7D32',    border: '#A5D6A7', label: 'Selesai' },
};

const SOURCE_STYLES = {
    Master: { bg: '#F3E5F5', color: '#7B1FA2', border: '#CE93D8', label: 'Master' },
    DO:     { bg: '#FFF3E0', color: '#E65100', border: '#FFCC80', label: 'DO' },
    Asset:  { bg: '#E8F5E9', color: '#2E7D32', border: '#A5D6A7', label: 'Aset' },
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
    const s = SOURCE_STYLES[source] || SOURCE_STYLES.Master;
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

// ─── Helpers ───────────────────────────────────────────────────────────────────
function PriceTag({ val }) {
    if (val === null || val === undefined) return <span style={{ color: UTM.gray300 }}>—</span>;
    const num = typeof val === 'string' ? parseFloat(val) : val;
    return <span>RM {num.toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>;
}

function SpecTooltip({ spec }) {
    if (!spec) return <span style={{ color: UTM.gray300 }}>—</span>;
    const preview = spec.length > 80 ? spec.slice(0, 80) + '...' : spec;
    return (
        <span title={spec} style={{ cursor: 'help', color: UTM.gray500, fontSize: 12 }}>
            {preview}
        </span>
    );
}

// ─── Main Component ────────────────────────────────────────────────────────────
export default function ItemsIndex({ items, counts, categories, pagination, filters }) {
    const { auth } = usePage().props;
    const [search, setSearch] = useState(filters.search ?? '');
    const [catFilter, setCatFilter] = useState(filters.category ?? '');
    const [srcFilter, setSrcFilter] = useState(filters.source ?? '');
    const [dateFrom, setDateFrom] = useState(filters.date_from ?? '');
    const [dateTo, setDateTo] = useState(filters.date_to ?? '');
    const [expandedId, setExpandedId] = useState(null);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/items', { ...filters, search, category: catFilter, source: srcFilter, date_from: dateFrom, date_to: dateTo, page: 1 },
            { preserveState: true, replace: true });
    };

    const handlePage = (page) => {
        router.get('/items', { ...filters, search, category: catFilter, source: srcFilter, date_from: dateFrom, date_to: dateTo, page },
            { preserveState: true, replace: true });
    };

    const handleFilter = (key, val) => {
        const newVal = val === (key === 'source' ? srcFilter : catFilter) ? '' : val;
        if (key === 'source') setSrcFilter(newVal);
        else setCatFilter(newVal);
        router.get('/items', { ...filters, search, category: key === 'category' ? newVal : catFilter, source: key === 'source' ? newVal : srcFilter, date_from: dateFrom, date_to: dateTo, page: 1 },
            { preserveState: true, replace: true });
    };

    const fmtDT = (iso) => {
        if (!iso) return '—';
        const d = new Date(iso);
        return d.toLocaleString('ms-MY', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Items Overview" />

            <div style={{ maxWidth: 1400, margin: '0 auto', padding: '24px 20px' }}>
                {/* Header */}
                <div style={{ marginBottom: 24 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.gray900, margin: 0 }}>
                        Master Equipment List
                    </h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                        Complete equipment register — all 156 items across 5 labs, sourced from the master inventory list.
                        Items without delivery records show as Belum Diterima.
                    </p>
                </div>

                {/* Stats row */}
                <div style={{ display: 'flex', gap: 12, marginBottom: 20, flexWrap: 'wrap' }}>
                    <StatCard label="All Items"        count={counts.total}          color={UTM.maroon} />
                    <StatCard label="Diterima"         count={counts.diterima}       color="#2E7D32" />
                    <StatCard label="Belum Diterima"   count={counts.belum_diterima} color="#E65100" />
                    <StatCard label="DO Records"       count={counts.do}             color="#E65100" />
                    <StatCard label="Registered Assets" count={counts.assets}        color="#2E7D32" />
                </div>

                {/* Filters */}
                <div style={{ display: 'flex', gap: 10, marginBottom: 16, alignItems: 'center', flexWrap: 'wrap' }}>
                    <form onSubmit={handleSearch} style={{ display: 'flex', gap: 8, flex: 1, minWidth: 200 }}>
                        <input
                            type="text"
                            placeholder="Search name, item no., brand, spec, serial..."
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

                    {/* Status filter */}
                    <div style={{ display: 'flex', gap: 6 }}>
                        {[{key: 'diterima', label: 'Diterima'}, {key: 'belum_diterima', label: 'Belum Diterima'}].map(s => (
                            <button
                                key={s.key}
                                onClick={() => handleFilter('source', s.key)}
                                style={{
                                    padding: '6px 14px', borderRadius: 6,
                                    border: `1.5px solid ${srcFilter === s.key ? UTM.maroon : UTM.gray100}`,
                                    background: srcFilter === s.key ? '#FDF0F3' : UTM.white,
                                    color: srcFilter === s.key ? UTM.maroon : UTM.gray500,
                                    fontWeight: 600, fontSize: 12, cursor: 'pointer',
                                }}
                            >
                                {s.label}
                            </button>
                        ))}
                    </div>

                    {/* Category filter */}
                    {categories?.length > 0 && (
                        <select
                            value={catFilter}
                            onChange={e => handleFilter('category', e.target.value)}
                            style={{
                                padding: '6px 14px', borderRadius: 6, fontSize: 12,
                                border: `1.5px solid ${UTM.gray100}`, background: UTM.white,
                                color: UTM.gray700, fontWeight: 600, cursor: 'pointer',
                            }}
                        >
                            <option value="">All Labs</option>
                            {categories.map(c => (
                                <option key={c} value={c}>{c}</option>
                            ))}
                        </select>
                    )}

                    {/* Date range filter */}
                    <div style={{ display: 'flex', gap: 6, alignItems: 'center' }}>
                        <input type="date" value={dateFrom}
                            onChange={e => {
                                setDateFrom(e.target.value);
                                router.get('/items', { ...filters, search, category: catFilter, source: srcFilter, date_from: e.target.value, date_to: dateTo, page: 1 },
                                    { preserveState: true, replace: true });
                            }}
                            style={{
                                padding: '6px 10px', borderRadius: 6, fontSize: 12,
                                border: `1.5px solid ${UTM.gray100}`, background: UTM.white,
                                color: UTM.gray700, fontWeight: 600, cursor: 'pointer',
                            }}
                        />
                        <span style={{ color: UTM.gray300, fontSize: 11 }}>to</span>
                        <input type="date" value={dateTo}
                            onChange={e => {
                                setDateTo(e.target.value);
                                router.get('/items', { ...filters, search, category: catFilter, source: srcFilter, date_from: dateFrom, date_to: e.target.value, page: 1 },
                                    { preserveState: true, replace: true });
                            }}
                            style={{
                                padding: '6px 10px', borderRadius: 6, fontSize: 12,
                                border: `1.5px solid ${UTM.gray100}`, background: UTM.white,
                                color: UTM.gray700, fontWeight: 600, cursor: 'pointer',
                            }}
                        />
                    </div>
                </div>

                {/* Items table */}
                {items.length === 0 ? (
                    <div style={{
                        background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`,
                        padding: 48, textAlign: 'center', color: UTM.gray500, fontSize: 14,
                    }}>
                        No items found.{counts.total === 0 ? ' Run the MasterItemSeeder to populate the equipment list.' : ' Try a different filter or search term.'}
                    </div>
                ) : (
                    <div style={{ background: UTM.white, borderRadius: 10, border: `1.5px solid ${UTM.gray100}`, overflow: 'auto' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13, minWidth: 1200 }}>
                            <thead>
                                <tr style={{ background: UTM.gray50, borderBottom: `1.5px solid ${UTM.gray100}` }}>
                                    <th style={thStyle}>Source</th>
                                    <th style={thStyle}>Item No.</th>
                                    <th style={thStyle}>Category</th>
                                    <th style={thStyle}>Name</th>
                                    <th style={thStyle}>Brand</th>
                                    <th style={thStyle}>Spec</th>
                                    <th style={thStyle}>Serial No.</th>
                                    <th style={thStyle}>Qty</th>
                                    <th style={{ ...thStyle, textAlign: 'right' }}>Unit Price</th>
                                    <th style={{ ...thStyle, textAlign: 'right' }}>Total</th>
                                    <th style={thStyle}>SODO</th>
                                    <th style={thStyle}>DO No.</th>
                                    <th style={thStyle}>Supplier</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={thStyle}></th>
                                </tr>
                            </thead>
                            <tbody>
                                {items.map((item) => (
                                    <React.Fragment key={item.id}>
                                    <tr style={{ borderBottom: `1px solid ${UTM.gray50}`, transition: 'background 0.1s' }}
                                        onMouseEnter={e => e.currentTarget.style.background = UTM.gray50}
                                        onMouseLeave={e => e.currentTarget.style.background = 'transparent'}
                                    >
                                        <td style={tdStyle}>
                                            <SourceBadge source={item.source} />
                                        </td>
                                        <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 12 }}>
                                            {item.item_no ?? '—'}
                                        </td>
                                        <td style={{ ...tdStyle, maxWidth: 130 }}>
                                            <span style={{ fontSize: 12, color: UTM.gray600 }}>{item.category ?? '—'}</span>
                                        </td>
                                        <td style={{ ...tdStyle, maxWidth: 200, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                            {item.url ? (
                                                <Link href={item.url} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}
                                                    title={item.name}>
                                                    {item.name}
                                                </Link>
                                            ) : (
                                                <span title={item.name}>{item.name}</span>
                                            )}
                                        </td>
                                        <td style={tdStyle}>{item.brand ?? '—'}</td>
                                        <td style={{ ...tdStyle, maxWidth: 180 }}>
                                            <SpecTooltip spec={item.specification} />
                                        </td>
                                        <td style={{ ...tdStyle, maxWidth: 150 }}>
                                            <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray700, wordBreak: 'break-all' }}>
                                                {item.serial_no ?? '—'}
                                            </span>
                                        </td>
                                        <td style={{ ...tdStyle, textAlign: 'center' }}>{item.quantity}</td>
                                        <td style={{ ...tdStyle, textAlign: 'right', whiteSpace: 'nowrap' }}>
                                            <PriceTag val={item.unit_price} />
                                        </td>
                                        <td style={{ ...tdStyle, textAlign: 'right', whiteSpace: 'nowrap' }}>
                                            <PriceTag val={item.total_price} />
                                        </td>
                                        <td style={{ ...tdStyle, fontSize: 12 }}>{item.sodo_bayaran ?? '—'}</td>
                                        <td style={{ ...tdStyle, fontSize: 12, fontFamily: 'monospace' }}>{item.do_no ?? '—'}</td>
                                        <td style={{ ...tdStyle, maxWidth: 140, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}
                                            title={item.supplier}>
                                            {item.supplier}
                                        </td>
                                        <td style={tdStyle}>
                                            <StatusBadge status={item.status} />
                                        </td>
                                        <td style={tdStyle}>
                                            <button
                                                onClick={() => setExpandedId(expandedId === (item.id) ? null : item.id)}
                                                style={{
                                                    padding: '4px 10px', borderRadius: 6, fontSize: 11, fontWeight: 700,
                                                    background: expandedId === (item.id) ? UTM.gold : '#EDE9E4',
                                                    color: expandedId === (item.id) ? UTM.maroon : UTM.gray700,
                                                    border: 'none', cursor: 'pointer', whiteSpace: 'nowrap',
                                                }}
                                            >
                                                {expandedId === (item.id) ? 'Tutup' : 'Butiran'}
                                            </button>
                                        </td>
                                    </tr>

                                    {/* ── Expandable detail row ── */}
                                    {expandedId === (item.id) && (
                                        <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                            <td colSpan={15} style={{ padding: 0 }}>
                                                <div style={{
                                                    padding: '20px 24px', display: 'flex', flexWrap: 'wrap', gap: 24,
                                                    borderLeft: `4px solid ${UTM.gold}`,
                                                }}>
                                                    {/* Left column: timestamps */}
                                                    <div style={{ minWidth: 200 }}>
                                                        <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>
                                                            Time & Date
                                                        </h4>
                                                        <table style={{ width: '100%', fontSize: 12, lineHeight: 1.8 }}>
                                                            <tbody>
                                                                <tr><td style={{ color: UTM.gray500, width: '50%' }}>Added</td>
                                                                    <td style={{ fontWeight: 600, color: UTM.gray900 }}>{fmtDT(item.created_at)}</td></tr>
                                                                <tr><td style={{ color: UTM.gray500 }}>Verified</td>
                                                                    <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.verified_at ? fmtDT(item.verified_at) : '—'}</td></tr>
                                                                <tr><td style={{ color: UTM.gray500 }}>Received</td>
                                                                    <td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.received_date && item.received_date !== '—' ? item.received_date : '—'}</td></tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                    {/* Right column: full spec + serials */}
                                                    <div style={{ flex: 1, minWidth: 300 }}>
                                                        <h4 style={{ fontSize: 11, fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: 6, marginBottom: 10 }}>
                                                            Full Specification
                                                        </h4>
                                                        <pre style={{
                                                            margin: 0, fontSize: 12, lineHeight: 1.6, whiteSpace: 'pre-wrap',
                                                            wordBreak: 'break-word', fontFamily: 'inherit', color: UTM.gray700,
                                                            maxHeight: 200, overflow: 'auto',
                                                        }}>
                                                            {item.specification || 'No specification available.'}
                                                        </pre>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                    </React.Fragment>
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
                    <span>▤ <strong>Master</strong> = Equipment register item (canonical source)</span>
                    <span>▤ <strong>DO</strong> = Delivery Order item (in verification pipeline)</span>
                    <span>▤ <strong>Aset</strong> = Registered asset (PA-2/PA-3)</span>
                    <span>▤ <strong>RM —</strong> = Price info in DO/Asset detail pages</span>
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
