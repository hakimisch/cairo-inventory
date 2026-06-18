import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const RESULT_MAP = {
    match:     { label: 'Match',     color: '#166534', bg: '#F0FDF4', icon: '✅' },
    mismatch:  { label: 'Mismatch',  color: '#DC2626', bg: '#FEF2F2', icon: '❌' },
    duplicate: { label: 'Duplicate', color: '#92400E', bg: '#FFFBEB', icon: '⚠' },
    new:       { label: 'New',       color: '#1E40AF', bg: '#EFF6FF', icon: '🔵' },
};

export default function History({ scans, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [resultFilter, setResultFilter] = useState(filters?.result || '');

    function handleSearch(e) {
        const val = e.target.value;
        setSearch(val);
        router.get(route('scanner.history'), { search: val, result: resultFilter }, { preserveState: true, replace: true });
    }

    function handleResultFilter(r) {
        setResultFilter(r);
        router.get(route('scanner.history'), { search, result: r }, { preserveState: true, replace: true });
    }

    return (
        <AuthenticatedLayout>
            <Head title="Scan History" />
            <div style={{ padding: 24, maxWidth: 1000, margin: '0 auto' }}>
                {/* ── Header ──────────────────────────────────────────────── */}
                <div style={{ marginBottom: 20 }}>
                    <Link href={route('scanner.index')} style={{ color: '#5C001F', fontSize: 13, textDecoration: 'none' }}>
                        ← Back to Scanner
                    </Link>
                    <h1 style={{ fontSize: 22, fontWeight: 700, margin: '8px 0 0' }}>Scan History</h1>
                </div>

                {/* ── Filters ──────────────────────────────────────────────── */}
                <div style={{ display: 'flex', gap: 12, marginBottom: 20 }}>
                    <input type="text" placeholder="Search serial number…" value={search}
                        onChange={handleSearch}
                        style={{
                            padding: '8px 14px', borderRadius: 8, border: '1px solid #D1D5DB',
                            fontSize: 13, flex: 1, minWidth: 200,
                        }} />
                    <select value={resultFilter} onChange={e => handleResultFilter(e.target.value)}
                        style={{ padding: '8px 14px', borderRadius: 8, border: '1px solid #D1D5DB', fontSize: 13 }}>
                        <option value="">All Results</option>
                        <option value="match">Match</option>
                        <option value="mismatch">Mismatch</option>
                        <option value="duplicate">Duplicate</option>
                        <option value="new">New</option>
                    </select>
                </div>

                {/* ── Scans Table ─────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                        <thead>
                            <tr style={{ backgroundColor: '#F9FAFB', borderBottom: '1px solid #E5E7EB' }}>
                                <th style={thStyle}>Serial No.</th>
                                <th style={thStyle}>Result</th>
                                <th style={thStyle}>DO / Item</th>
                                <th style={thStyle}>Asset</th>
                                <th style={thStyle}>Scanned By</th>
                                <th style={thStyle}>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            {scans.data.length === 0 && (
                                <tr>
                                    <td colSpan={6} style={{ textAlign: 'center', padding: 40, color: '#9CA3AF' }}>
                                        No scan records found.
                                    </td>
                                </tr>
                            )}
                            {scans.data.map(s => {
                                const st = RESULT_MAP[s.result] || RESULT_MAP.mismatch;
                                return (
                                    <tr key={s.id} style={{ borderBottom: '1px solid #F3F4F6' }}>
                                        <td style={{ ...tdStyle, fontFamily: 'monospace', fontWeight: 600 }}>
                                            {s.serial_number}
                                        </td>
                                        <td style={tdStyle}>
                                            <span style={{
                                                display: 'inline-flex', alignItems: 'center', gap: 3,
                                                padding: '2px 10px', borderRadius: 20, fontSize: 11, fontWeight: 600,
                                                color: st.color, backgroundColor: st.bg,
                                            }}>
                                                {st.icon} {st.label}
                                            </span>
                                        </td>
                                        <td style={tdStyle}>
                                            {s.do_no ? (
                                                <>
                                                    <Link href={route('delivery-orders.show', s.do_no.split('/')[0] || s.do_no)}
                                                          style={{ color: '#5C001F', textDecoration: 'none', fontWeight: 600 }}>
                                                        {s.do_no}
                                                    </Link>
                                                    {s.item_description && (
                                                        <div style={{ fontSize: 12, color: '#6B7280', marginTop: 2 }}>{s.item_description}</div>
                                                    )}
                                                </>
                                            ) : (
                                                <span style={{ color: '#9CA3AF' }}>—</span>
                                            )}
                                        </td>
                                        <td style={tdStyle}>
                                            {s.asset_tag ? (
                                                <span style={{ fontFamily: 'monospace', color: '#5C001F' }}>#{s.asset_tag}</span>
                                            ) : (
                                                <span style={{ color: '#9CA3AF' }}>—</span>
                                            )}
                                        </td>
                                        <td style={{ ...tdStyle, color: '#6B7280' }}>{s.scanner_name}</td>
                                        <td style={{ ...tdStyle, color: '#6B7280', fontSize: 12 }}>
                                            {s.scanned_at ? new Date(s.scanned_at).toLocaleString() : '—'}
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>

                {/* ── Pagination ──────────────────────────────────────────── */}
                {scans.links && (
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                        {scans.links.map((link, i) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                style={{
                                    padding: '6px 12px', borderRadius: 6, fontSize: 13,
                                    textDecoration: 'none',
                                    backgroundColor: link.active ? '#5C001F' : '#fff',
                                    color: link.active ? '#fff' : '#374151',
                                    border: '1px solid #D1D5DB',
                                    pointerEvents: link.url ? 'auto' : 'none',
                                    opacity: link.url ? 1 : 0.4,
                                }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

const thStyle = {
    padding: '10px 14px',
    textAlign: 'left',
    fontWeight: 600,
    fontSize: 11,
    color: '#6B7280',
    textTransform: 'uppercase',
    letterSpacing: '0.05em',
};
const tdStyle = {
    padding: '10px 14px',
    verticalAlign: 'middle',
};
