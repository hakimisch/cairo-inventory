import { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const STATUS_MAP = {
    pending:  { label: 'Pending',  color: '#92400E', bg: '#FFFBEB', icon: '⏳' },
    received: { label: 'Received', color: '#166534', bg: '#F0FDF4', icon: '✅' },
    shortage: { label: 'Shortage', color: '#B45309', bg: '#FFFBEB', icon: '⚠' },
    damaged:  { label: 'Damaged',  color: '#DC2626', bg: '#FEF2F2', icon: '! ' },
    verified: { label: 'Verified', color: '#1E40AF', bg: '#EFF6FF', icon: '◉' },
    no_serial:{ label: 'No S/N',   color: '#6B7280', bg: '#F3F4F6', icon: '⊘' },
};

const DO_STATUS_MAP = {
    pending:  { label: 'Pending',  color: '#92400E', bg: '#FFFBEB' },
    partial: { label: 'Partial',  color: '#92400E', bg: '#FEF3C7' },
    complete:{ label: 'Complete', color: '#166534', bg: '#F0FDF4' },
    verified:{ label: 'Verified', color: '#1E40AF', bg: '#EFF6FF' },
};

export default function Show({ deliveryOrder, suppliers }) {
    const doItem = deliveryOrder;
    const doStatus = DO_STATUS_MAP[doItem.status] || DO_STATUS_MAP.pending;

    // ── Inline add line item ──
    const [showAddItem, setShowAddItem] = useState(false);
    const [newItem, setNewItem] = useState({
        description: '', category: '', item_code: '', brand: '', model: '',
        serial_number: '', quantity_ordered: 1, unit: 'unit', notes: '',
    });

    // ── Inline edit serial number ──
    const [editingSerial, setEditingSerial] = useState(null); // line item id
    const [editSerialVal, setEditSerialVal] = useState('');

    function startEditSerial(item) {
        setEditingSerial(item.id);
        setEditSerialVal(item.serial_number || '');
    }

    function saveSerial(itemId) {
        router.put(
            route('delivery-orders.line-items.update', [doItem.id, itemId]),
            { serial_number: editSerialVal },
            { preserveScroll: true, preserveState: true, onSuccess: () => setEditingSerial(null) }
        );
    }

    function cancelEditSerial() {
        setEditingSerial(null);
    }

    function markNoSerial(itemId) {
        if (!confirm('Mark this item as having no serial number? It will be verified by inspection instead of scan.')) return;
        router.put(
            route('delivery-orders.line-items.update', [doItem.id, itemId]),
            { has_serial: false, status: 'no_serial', notes: 'Item verified by inspection (no serial number)' },
            { preserveScroll: true, preserveState: true }
        );
    }

    function addLineItem() {
        if (!newItem.description.trim()) return;
        router.post(route('delivery-orders.line-items.store', doItem.id), newItem, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                setNewItem({ description: '', category: '', item_code: '', brand: '', model: '', serial_number: '', quantity_ordered: 1, unit: 'unit', notes: '' });
                setShowAddItem(false);
            },
        });
    }

    function verifyItem(lineItemId, createAsset = false) {
        router.post(
            route('delivery-orders.verify', [doItem.id, lineItemId]),
            { create_asset: createAsset },
            { preserveScroll: true, preserveState: true }
        );
    }

    function deleteLineItem(lineItemId) {
        if (!confirm('Delete this line item?')) return;
        router.delete(route('delivery-orders.line-items.destroy', [doItem.id, lineItemId]), {
            preserveScroll: true,
            preserveState: true,
        });
    }

    function deleteDO() {
        if (!confirm('Delete this entire Delivery Order? This cannot be undone.')) return;
        router.delete(route('delivery-orders.destroy', doItem.id));
    }

    return (
        <AuthenticatedLayout>
            <Head title={`DO: ${doItem.do_no}`} />
            <div style={{ padding: 24, maxWidth: 1000, margin: '0 auto' }}>
                {/* ── Header ──────────────────────────────────────────────── */}
                <div style={{ marginBottom: 20 }}>
                    <Link href={route('delivery-orders.index')} style={{ color: '#5C001F', fontSize: 13, textDecoration: 'none' }}>
                        ← Back to Delivery Orders
                    </Link>
                </div>

                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', flexWrap: 'wrap', gap: 12, marginBottom: 24 }}>
                    <div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                            <h1 style={{ fontSize: 22, fontWeight: 700, margin: 0 }}>{doItem.do_no}</h1>
                            <span style={{
                                display: 'inline-block', padding: '3px 12px', borderRadius: 20,
                                fontSize: 11, fontWeight: 700, color: doStatus.color, backgroundColor: doStatus.bg,
                            }}>
                                {doStatus.label}
                            </span>
                        </div>
                        <p style={{ color: '#6B7280', fontSize: 13, margin: '6px 0 0' }}>
                            {doItem.supplier_name} · {doItem.po_reference || 'No PO ref'} · Created {doItem.created_at?.split('T')[0] || '—'}
                        </p>
                    </div>
                    <div style={{ display: 'flex', gap: 8 }}>
                        <Link
                            href={route('delivery-orders.edit', doItem.id)}
                            style={secBtnStyle}
                        >
                            Edit
                        </Link>
                        <Link
                            href={route('delivery-orders.batch-import.form', doItem.id)}
                            style={{ ...secBtnStyle, backgroundColor: '#5C001F', color: '#fff' }}
                        >
                            Batch Import
                        </Link>
                        <button onClick={deleteDO} style={{ ...secBtnStyle, color: '#DC2626' }}>
                            Delete
                        </button>
                    </div>
                </div>

                {/* ── Progress Bar ────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 24 }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 8 }}>
                        <span style={{ fontSize: 13, fontWeight: 600 }}>Verification Progress</span>
                        <span style={{ fontSize: 13, color: '#6B7280' }}>
                            {doItem.total_received} / {doItem.total_ordered} items received ({doItem.progress_pct}%)
                        </span>
                    </div>
                    <div style={{ width: '100%', height: 8, backgroundColor: '#E5E7EB', borderRadius: 4, overflow: 'hidden' }}>
                        <div style={{
                            width: `${doItem.progress_pct}%`,
                            height: '100%',
                            backgroundColor: doItem.progress_pct === 100 ? '#166534' : '#5C001F',
                            borderRadius: 4,
                            transition: 'width 0.3s ease',
                        }} />
                    </div>
                </div>

                {/* ── DO Details ──────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 24 }}>
                    <h2 style={{ fontSize: 14, fontWeight: 600, margin: '0 0 12px', color: '#374151' }}>Document Details</h2>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 12 }}>
                        <InfoRow label="Supplier" value={doItem.supplier_name} />
                        <InfoRow label="PO Reference" value={doItem.po_reference || '—'} />
                        <InfoRow label="Acknowledgement Date" value={doItem.ack_date || '—'} />
                        <InfoRow label="Sales Rep" value={doItem.sales_rep || '—'} />
                        <InfoRow label="Terms" value={doItem.terms || '—'} />
                        <InfoRow label="Total Pages" value={doItem.total_pages} />
                        <InfoRow label="Created By" value={doItem.created_by_name} />
                        <InfoRow label="Last Updated" value={doItem.updated_at?.split('T')[0] || '—'} />
                    </div>
                    {doItem.notes && (
                        <div style={{ marginTop: 12, padding: 12, backgroundColor: '#F9FAFB', borderRadius: 8, fontSize: 13, color: '#6B7280' }}>
                            {doItem.notes}
                        </div>
                    )}
                </div>

                {/* ── Uploaded Documents ──────────────────────────────────── */}
                {doItem.documents?.length > 0 && (
                    <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 24 }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: '0 0 12px', color: '#374151' }}>Documents</h2>
                        <div style={{ display: 'flex', gap: 12 }}>
                            {doItem.documents.map(doc => (
                                <a key={doc.id} href={doc.url} target="_blank" rel="noreferrer"
                                    style={{
                                        display: 'flex', alignItems: 'center', gap: 8,
                                        padding: '10px 16px', backgroundColor: '#F9FAFB',
                                        borderRadius: 8, textDecoration: 'none', fontSize: 13, color: '#5C001F',
                                        border: '1px solid #E5E7EB',
                                    }}
                                >
                                    ◰ {doc.name}
                                </a>
                            ))}
                        </div>
                    </div>
                )}

                {/* ── Line Items ──────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden', marginBottom: 24 }}>
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '16px 20px', borderBottom: '1px solid #E5E7EB' }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: 0, color: '#374151' }}>
                            Line Items ({doItem.line_items?.length || 0})
                        </h2>
                        <button
                            onClick={() => setShowAddItem(!showAddItem)}
                            style={{
                                padding: '6px 14px',
                                backgroundColor: '#5C001F',
                                color: '#fff',
                                border: 'none',
                                borderRadius: 6,
                                fontSize: 12,
                                fontWeight: 600,
                                cursor: 'pointer',
                            }}
                        >
                            {showAddItem ? 'Cancel' : '+ Add Item'}
                        </button>
                    </div>

                    {/* ── Inline Add Form ─────────────────────────────────────── */}
                    {showAddItem && (
                        <div style={{ padding: 16, backgroundColor: '#F9FAFB', borderBottom: '1px solid #E5E7EB' }}>
                            <div style={{ display: 'grid', gridTemplateColumns: '2fr 1fr 1fr 1fr 1fr', gap: 8 }}>
                                <div>
                                    <label style={miniLabel}>Description *</label>
                                    <input type="text" value={newItem.description}
                                        onChange={e => setNewItem(p => ({ ...p, description: e.target.value }))}
                                        style={miniInput} placeholder="Item name" />
                                </div>
                                <div>
                                    <label style={miniLabel}>Category</label>
                                    <input type="text" value={newItem.category}
                                        onChange={e => setNewItem(p => ({ ...p, category: e.target.value }))}
                                        style={miniInput} placeholder="e.g. Office Equipment" />
                                </div>
                                <div>
                                    <label style={miniLabel}>Brand</label>
                                    <input type="text" value={newItem.brand}
                                        onChange={e => setNewItem(p => ({ ...p, brand: e.target.value }))}
                                        style={miniInput} placeholder="e.g. DELL" />
                                </div>
                                <div>
                                    <label style={miniLabel}>Model</label>
                                    <input type="text" value={newItem.model}
                                        onChange={e => setNewItem(p => ({ ...p, model: e.target.value }))}
                                        style={miniInput} placeholder="e.g. QCM1250" />
                                </div>
                                <div>
                                    <label style={miniLabel}>S/N</label>
                                    <input type="text" value={newItem.serial_number}
                                        onChange={e => setNewItem(p => ({ ...p, serial_number: e.target.value }))}
                                        style={miniInput} placeholder="Serial number" />
                                </div>
                            </div>
                            <div style={{ display: 'flex', gap: 8, marginTop: 8 }}>
                                <input type="number" min="1" value={newItem.quantity_ordered}
                                    onChange={e => setNewItem(p => ({ ...p, quantity_ordered: parseInt(e.target.value) || 1 }))}
                                    style={{ ...miniInput, width: 80 }} placeholder="Qty" />
                                <input type="text" value={newItem.unit}
                                    onChange={e => setNewItem(p => ({ ...p, unit: e.target.value }))}
                                    style={miniInput} placeholder="Unit" />
                                <button onClick={addLineItem}
                                    style={{
                                        padding: '8px 16px', backgroundColor: '#5C001F', color: '#fff',
                                        border: 'none', borderRadius: 6, fontSize: 12, fontWeight: 600, cursor: 'pointer',
                                    }}>
                                    Add
                                </button>
                            </div>
                        </div>
                    )}

                    {/* ── Items Table ─────────────────────────────────────────── */}
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 12 }}>
                        <thead>
                            <tr style={{ backgroundColor: '#F9FAFB' }}>
                                <th style={thStyle}>#</th>
                                <th style={thStyle}>Description</th>
                                <th style={thStyle}>Brand / Model</th>
                                <th style={thStyle}>Serial No.</th>
                                <th style={thStyle}>Qty</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {doItem.line_items?.length === 0 && (
                                <tr>
                                    <td colSpan={7} style={{ textAlign: 'center', padding: 40, color: '#9CA3AF' }}>
                                        No line items yet. Click "+ Add Item" to add items from the DO.
                                    </td>
                                </tr>
                            )}
                            {doItem.line_items?.map((item, idx) => {
                                const st = STATUS_MAP[item.status] || STATUS_MAP.pending;
                                return (
                                    <tr key={item.id} style={{ borderBottom: '1px solid #F3F4F6' }}>
                                        <td style={tdStyle}>{idx + 1}</td>
                                        <td style={tdStyle}>
                                            <div style={{ fontWeight: 600 }}>{item.description}</div>
                                            {item.category && <div style={{ fontSize: 11, color: '#9CA3AF' }}>{item.category}</div>}
                                            {item.item_code && <div style={{ fontSize: 11, color: '#9CA3AF' }}>Code: {item.item_code}</div>}
                                        </td>
                                        <td style={tdStyle}>
                                            {item.brand && <div>{item.brand}</div>}
                                            {item.model && <div style={{ fontSize: 11, color: '#6B7280' }}>{item.model}</div>}
                                        </td>
                                        <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 11 }}>
                                            {editingSerial === item.id ? (
                                                <div style={{ display: 'flex', gap: 4, alignItems: 'center' }}>
                                                    <input
                                                        type="text"
                                                        value={editSerialVal}
                                                        onChange={e => setEditSerialVal(e.target.value)}
                                                        style={{ padding: '4px 6px', border: '1px solid #D1D5DB', borderRadius: 4, fontSize: 11, width: 120 }}
                                                        autoFocus
                                                        onKeyDown={e => { if (e.key === 'Enter') saveSerial(item.id); if (e.key === 'Escape') cancelEditSerial(); }}
                                                    />
                                                    <button onClick={() => saveSerial(item.id)}
                                                        style={{ padding: '3px 8px', background: '#166534', color: '#fff', border: 'none', borderRadius: 3, fontSize: 11, cursor: 'pointer' }}>
                                                        Save
                                                    </button>
                                                    <button onClick={cancelEditSerial}
                                                        style={{ padding: '3px 8px', background: '#F3F4F6', border: '1px solid #D1D5DB', borderRadius: 3, fontSize: 11, cursor: 'pointer' }}>
                                                        ×
                                                    </button>
                                                </div>
                                            ) : (
                                                <div style={{ display: 'flex', alignItems: 'center', gap: 4 }}>
                                                    <span>{item.serial_number || (item.has_serial === false ? '⊘ No S/N' : '—')}</span>
                                                    {item.status === 'pending' && item.has_serial !== false && (
                                                        <button onClick={() => startEditSerial(item)}
                                                            style={{ padding: '1px 6px', background: 'none', border: '1px solid #D1D5DB', borderRadius: 3, fontSize: 10, cursor: 'pointer', color: '#6B7280' }}>
                                                            ✎
                                                        </button>
                                                    )}
                                                </div>
                                            )}
                                        </td>
                                        <td style={tdStyle}>
                                            {item.quantity_received}/{item.quantity_ordered} {item.unit}
                                        </td>
                                        <td style={tdStyle}>
                                            <span style={{
                                                display: 'inline-flex', alignItems: 'center', gap: 3,
                                                padding: '2px 8px', borderRadius: 20, fontSize: 11, fontWeight: 600,
                                                color: st.color, backgroundColor: st.bg,
                                            }}>
                                                {st.icon} {st.label}
                                            </span>
                                        </td>
                                        <td style={tdStyle}>
                                            <div style={{ display: 'flex', gap: 4 }}>
                                                {item.status === 'pending' && (
                                                    <>
                                                        <button onClick={() => verifyItem(item.id, false)}
                                                            style={actionBtnStyle}>
                                                            Receive
                                                        </button>
                                                        <button onClick={() => verifyItem(item.id, true)}
                                                            style={{ ...actionBtnStyle, backgroundColor: '#1E40AF', color: '#fff' }}>
                                                            +Asset
                                                        </button>
                                                        {item.has_serial !== false && item.status === 'pending' && (
                                                            <button onClick={() => markNoSerial(item.id)}
                                                                style={{ ...actionBtnStyle, backgroundColor: '#F3F4F6', color: '#6B7280' }}>
                                                                No S/N
                                                            </button>
                                                        )}
                                                    </>
                                                )}
                                                {item.status === 'no_serial' && (
                                                    <span style={{ fontSize: 11, color: '#6B7280' }}>Inspected</span>
                                                )}
                                                <button onClick={() => deleteLineItem(item.id)}
                                                    style={{ ...actionBtnStyle, color: '#DC2626', backgroundColor: '#FEF2F2' }}>
                                                    ×
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function InfoRow({ label, value }) {
    return (
        <div>
            <p style={{ fontSize: 11, color: '#9CA3AF', margin: '0 0 2px', textTransform: 'uppercase', letterSpacing: '0.05em' }}>{label}</p>
            <p style={{ fontSize: 13, color: '#374151', margin: 0 }}>{value}</p>
        </div>
    );
}

const thStyle = {
    padding: '8px 12px',
    textAlign: 'left',
    fontWeight: 600,
    fontSize: 11,
    color: '#6B7280',
    textTransform: 'uppercase',
    letterSpacing: '0.05em',
};
const tdStyle = {
    padding: '8px 12px',
    verticalAlign: 'middle',
};
const secBtnStyle = {
    padding: '8px 16px',
    backgroundColor: '#fff',
    color: '#374151',
    border: '1px solid #D1D5DB',
    borderRadius: 6,
    fontSize: 13,
    cursor: 'pointer',
    textDecoration: 'none',
    display: 'inline-block',
};
const actionBtnStyle = {
    padding: '4px 10px',
    borderRadius: 4,
    fontSize: 11,
    fontWeight: 600,
    border: 'none',
    cursor: 'pointer',
    backgroundColor: '#F0FDF4',
    color: '#166534',
};
const miniLabel = {
    display: 'block', fontSize: 11, fontWeight: 600, color: '#6B7280', marginBottom: 2,
};
const miniInput = {
    width: '100%',
    padding: '6px 10px',
    borderRadius: 4,
    border: '1px solid #D1D5DB',
    fontSize: 12,
    boxSizing: 'border-box',
};
