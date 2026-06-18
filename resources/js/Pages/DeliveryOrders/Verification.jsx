import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const DO_STATUS_MAP = {
    pending:  { label: 'Pending',  color: '#92400E', bg: '#FFFBEB' },
    partial: { label: 'Partial',  color: '#92400E', bg: '#FEF3C7' },
    complete:{ label: 'Complete', color: '#166534', bg: '#F0FDF4' },
    verified:{ label: 'Verified', color: '#1E40AF', bg: '#EFF6FF' },
};

export default function Verification({ deliveryOrders, totals }) {
    const [statusFilter, setStatusFilter] = useState('');

    const filtered = statusFilter
        ? deliveryOrders.filter(d => d.status === statusFilter)
        : deliveryOrders;

    return (
        <AuthenticatedLayout>
            <Head title="Verification Dashboard" />
            <div style={{ padding: 24, maxWidth: 1000, margin: '0 auto' }}>
                <h1 style={{ fontSize: 22, fontWeight: 700, margin: '0 0 4px' }}>◈ Verification Dashboard</h1>
                <p style={{ color: '#6B7280', fontSize: 13, margin: '0 0 24px' }}>
                    Track delivery order progress across all suppliers
                </p>

                {/* ── Summary Cards ────────────────────────────────────────── */}
                <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: 12, marginBottom: 24 }}>
                    <SummaryCard label="Total DOs" value={totals.total_dos} color="#5C001F" />
                    <SummaryCard label="Total Items Ordered" value={totals.total_ordered} color="#374151" />
                    <SummaryCard label="Total Items Received" value={totals.total_received} color="#166534" />
                    <SummaryCard label="Pending" value={totals.pending_count} color="#92400E" />
                    <SummaryCard label="Complete" value={totals.complete_count} color="#166534" />
                    <SummaryCard label="Verified" value={totals.verified_count} color="#1E40AF" />
                </div>

                {/* ── Overall Progress Bar ──────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 24 }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                        <span style={{ fontSize: 14, fontWeight: 600, color: '#374151' }}>Overall Verification Progress</span>
                        <span style={{ fontSize: 13, fontWeight: 600, color: totals.overall_progress === 100 ? '#166534' : '#5C001F' }}>
                            {totals.overall_progress}%
                        </span>
                    </div>
                    <div style={{ width: '100%', height: 12, backgroundColor: '#E5E7EB', borderRadius: 6, overflow: 'hidden' }}>
                        <div style={{
                            width: `${totals.overall_progress}%`,
                            height: '100%',
                            background: totals.overall_progress === 100
                                ? 'linear-gradient(90deg, #166534, #22C55E)'
                                : 'linear-gradient(90deg, #5C001F, #DC2626)',
                            borderRadius: 6,
                            transition: 'width 0.5s ease',
                        }} />
                    </div>
                    <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 8, fontSize: 12, color: '#6B7280' }}>
                        <span>{totals.total_received} items received</span>
                        <span>{totals.total_ordered - totals.total_received} items remaining</span>
                    </div>
                </div>

                {/* ── Status Filter ────────────────────────────────────────── */}
                <div style={{ display: 'flex', gap: 8, marginBottom: 16 }}>
                    <button onClick={() => setStatusFilter('')}
                        style={filterBtnStyle(!statusFilter)}>
                        All ({deliveryOrders.length})
                    </button>
                    <button onClick={() => setStatusFilter('pending')}
                        style={filterBtnStyle(statusFilter === 'pending')}>
                        Pending
                    </button>
                    <button onClick={() => setStatusFilter('partial')}
                        style={filterBtnStyle(statusFilter === 'partial')}>
                        Partial
                    </button>
                    <button onClick={() => setStatusFilter('complete')}
                        style={filterBtnStyle(statusFilter === 'complete')}>
                        Complete
                    </button>
                    <button onClick={() => setStatusFilter('verified')}
                        style={filterBtnStyle(statusFilter === 'verified')}>
                        Verified
                    </button>
                </div>

                {/* ── DO List with Progress ────────────────────────────────── */}
                <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                    {filtered.length === 0 && (
                        <div style={{ textAlign: 'center', padding: 40, color: '#9CA3AF', backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB' }}>
                            No Delivery Orders match the selected filter.
                        </div>
                    )}
                    {filtered.map(doItem => {
                        const st = DO_STATUS_MAP[doItem.status] || DO_STATUS_MAP.pending;
                        return (
                            <Link
                                key={doItem.id}
                                href={route('delivery-orders.show', doItem.id)}
                                style={{
                                    backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB',
                                    padding: 16, textDecoration: 'none', display: 'block',
                                    transition: 'box-shadow 0.15s',
                                }}
                                onMouseEnter={e => e.currentTarget.style.boxShadow = '0 2px 8px rgba(0,0,0,0.06)'}
                                onMouseLeave={e => e.currentTarget.style.boxShadow = 'none'}
                            >
                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 10, flexWrap: 'wrap', gap: 8 }}>
                                    <div>
                                        <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                                            <span style={{ fontSize: 15, fontWeight: 700, color: '#5C001F' }}>{doItem.do_no}</span>
                                            <span style={{
                                                display: 'inline-block', padding: '2px 10px', borderRadius: 20,
                                                fontSize: 11, fontWeight: 600, color: st.color, backgroundColor: st.bg,
                                            }}>
                                                {st.label}
                                            </span>
                                        </div>
                                        <p style={{ fontSize: 12, color: '#6B7280', margin: '4px 0 0' }}>
                                            {doItem.supplier_name} · {doItem.po_reference || 'No PO'} · {doItem.line_items_count} line items
                                        </p>
                                    </div>
                                    <div style={{ textAlign: 'right' }}>
                                        <span style={{ fontSize: 13, fontWeight: 700, color: doItem.progress_pct === 100 ? '#166534' : '#5C001F' }}>
                                            {doItem.total_received}/{doItem.total_ordered}
                                        </span>
                                        <span style={{ fontSize: 12, color: '#6B7280', marginLeft: 4 }}>items</span>
                                    </div>
                                </div>

                                {/* Progress bar */}
                                <div style={{ width: '100%', height: 6, backgroundColor: '#E5E7EB', borderRadius: 3, overflow: 'hidden' }}>
                                    <div style={{
                                        width: `${doItem.progress_pct}%`,
                                        height: '100%',
                                        backgroundColor: doItem.progress_pct === 100 ? '#166534' : '#5C001F',
                                        borderRadius: 3,
                                        transition: 'width 0.3s ease',
                                    }} />
                                </div>

                                <div style={{ display: 'flex', justifyContent: 'space-between', marginTop: 6, fontSize: 11, color: '#9CA3AF' }}>
                                    <span>{doItem.ack_date || 'No date'}</span>
                                    <span>{doItem.progress_pct}% complete</span>
                                </div>
                            </Link>
                        );
                    })}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function SummaryCard({ label, value, color }) {
    return (
        <div style={{
            backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB',
            padding: '14px 16px', textAlign: 'center',
        }}>
            <p style={{ fontSize: 22, fontWeight: 700, margin: 0, color }}>{value}</p>
            <p style={{ fontSize: 12, color: '#6B7280', margin: '4px 0 0' }}>{label}</p>
        </div>
    );
}

function filterBtnStyle(active) {
    return {
        padding: '6px 14px', borderRadius: 6, fontSize: 12, fontWeight: 600,
        border: active ? '2px solid #5C001F' : '1px solid #D1D5DB',
        backgroundColor: active ? '#FDF2F4' : '#fff',
        color: active ? '#5C001F' : '#374151',
        cursor: 'pointer',
    };
}
