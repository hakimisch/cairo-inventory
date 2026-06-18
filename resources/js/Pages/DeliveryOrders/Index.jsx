import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const STATUS_MAP = {
    pending:  { label: 'Pending',  color: '#92400E', bg: '#FFFBEB' },
    partial: { label: 'Partial',  color: '#92400E', bg: '#FEF3C7' },
    complete:{ label: 'Complete', color: '#166534', bg: '#F0FDF4' },
    verified:{ label: 'Verified', color: '#1E40AF', bg: '#EFF6FF' },
};

export default function Index({ deliveryOrders, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');

    function handleSearch(e) {
        const val = e.target.value;
        setSearch(val);
        router.get(route('delivery-orders.index'), { search: val, status: statusFilter }, { preserveState: true, replace: true });
    }

    function handleStatusFilter(s) {
        setStatusFilter(s);
        router.get(route('delivery-orders.index'), { search, status: s }, { preserveState: true, replace: true });
    }

    return (
        <AuthenticatedLayout>
            <Head title="Delivery Orders" />
            <div style={{ padding: 24, maxWidth: 1200, margin: '0 auto' }}>
                {/* ── Header ──────────────────────────────────────────────── */}
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20, flexWrap: 'wrap', gap: 12 }}>
                    <div>
                        <h1 style={{ fontSize: 22, fontWeight: 700, margin: 0 }}>Delivery Orders</h1>
                        <p style={{ color: '#6B7280', fontSize: 13, margin: '4px 0 0' }}>DO-based item verification system</p>
                    </div>
                    <Link
                        href={route('delivery-orders.create')}
                        style={{
                            padding: '10px 20px',
                            backgroundColor: '#5C001F',
                            color: '#fff',
                            borderRadius: 8,
                            textDecoration: 'none',
                            fontSize: 14,
                            fontWeight: 600,
                        }}
                    >
                        ＋ New Delivery Order
                    </Link>
                </div>

                {/* ── Filters ──────────────────────────────────────────────── */}
                <div style={{ display: 'flex', gap: 12, marginBottom: 20, flexWrap: 'wrap' }}>
                    <input
                        type="text"
                        placeholder="Search DO No. or PO Reference…"
                        value={search}
                        onChange={handleSearch}
                        style={{
                            padding: '8px 14px',
                            borderRadius: 8,
                            border: '1px solid #D1D5DB',
                            fontSize: 13,
                            flex: 1,
                            minWidth: 250,
                        }}
                    />
                    <select
                        value={statusFilter}
                        onChange={(e) => handleStatusFilter(e.target.value)}
                        style={{
                            padding: '8px 14px',
                            borderRadius: 8,
                            border: '1px solid #D1D5DB',
                            fontSize: 13,
                        }}
                    >
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="partial">Partial</option>
                        <option value="complete">Complete</option>
                        <option value="verified">Verified</option>
                    </select>
                </div>

                {/* ── DO Table ────────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                        <thead>
                            <tr style={{ backgroundColor: '#F9FAFB', borderBottom: '1px solid #E5E7EB' }}>
                                <th style={thStyle}>DO No.</th>
                                <th style={thStyle}>Supplier</th>
                                <th style={thStyle}>PO Reference</th>
                                <th style={thStyle}>Date</th>
                                <th style={thStyle}>Items</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Created By</th>
                                <th style={thStyle}></th>
                            </tr>
                        </thead>
                        <tbody>
                            {deliveryOrders.data.length === 0 && (
                                <tr>
                                    <td colSpan={8} style={{ textAlign: 'center', padding: 40, color: '#9CA3AF' }}>
                                        No Delivery Orders yet. Click "New Delivery Order" to create one.
                                    </td>
                                </tr>
                            )}
                            {deliveryOrders.data.map((doItem) => {
                                const st = STATUS_MAP[doItem.status] || STATUS_MAP.pending;
                                return (
                                    <tr key={doItem.id} style={{ borderBottom: '1px solid #F3F4F6' }}>
                                        <td style={tdStyle}>
                                            <Link href={route('delivery-orders.show', doItem.id)} style={{ color: '#5C001F', fontWeight: 600, textDecoration: 'none' }}>
                                                {doItem.do_no}
                                            </Link>
                                        </td>
                                        <td style={tdStyle}>{doItem.supplier_name}</td>
                                        <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 12 }}>{doItem.po_reference || '—'}</td>
                                        <td style={tdStyle}>{doItem.ack_date || '—'}</td>
                                        <td style={tdStyle}>
                                            {doItem.received_count}/{doItem.line_items_count}
                                        </td>
                                        <td style={tdStyle}>
                                            <span style={{
                                                display: 'inline-block',
                                                padding: '2px 10px',
                                                borderRadius: 20,
                                                fontSize: 11,
                                                fontWeight: 600,
                                                color: st.color,
                                                backgroundColor: st.bg,
                                            }}>
                                                {st.label}
                                            </span>
                                        </td>
                                        <td style={{ ...tdStyle, color: '#6B7280' }}>{doItem.created_by_name}</td>
                                        <td style={tdStyle}>
                                            <Link
                                                href={route('delivery-orders.show', doItem.id)}
                                                style={{ color: '#5C001F', fontSize: 12, textDecoration: 'none' }}
                                            >
                                                View →
                                            </Link>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>

                {/* ── Pagination ──────────────────────────────────────────── */}
                {deliveryOrders.links && (
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                        {deliveryOrders.links.map((link, i) => (
                            <Link
                                key={i}
                                href={link.url || '#'}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                style={{
                                    padding: '6px 12px',
                                    borderRadius: 6,
                                    fontSize: 13,
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
