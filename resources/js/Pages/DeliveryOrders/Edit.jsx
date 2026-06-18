import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Edit({ deliveryOrder, suppliers }) {
    const { data, setData, put, processing, errors } = useForm({
        do_no: deliveryOrder.do_no || '',
        supplier_id: deliveryOrder.supplier_id || '',
        ack_date: deliveryOrder.ack_date || '',
        po_reference: deliveryOrder.po_reference || '',
        sales_rep: deliveryOrder.sales_rep || '',
        terms: deliveryOrder.terms || '',
        total_pages: deliveryOrder.total_pages || 1,
        status: deliveryOrder.status || 'pending',
        notes: deliveryOrder.notes || '',
        document: null,
    });

    function handleSubmit(e) {
        e.preventDefault();
        put(route('delivery-orders.update', deliveryOrder.id));
    }

    return (
        <AuthenticatedLayout>
            <Head title={`Edit DO: ${deliveryOrder.do_no}`} />
            <div style={{ padding: 24, maxWidth: 800, margin: '0 auto' }}>
                {/* ── Header ──────────────────────────────────────────────── */}
                <div style={{ marginBottom: 24 }}>
                    <Link href={route('delivery-orders.show', deliveryOrder.id)} style={{ color: '#5C001F', fontSize: 13, textDecoration: 'none' }}>
                        ← Back to DO Detail
                    </Link>
                    <h1 style={{ fontSize: 22, fontWeight: 700, margin: '8px 0 0' }}>Edit: {deliveryOrder.do_no}</h1>
                </div>

                <form onSubmit={handleSubmit} style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 24 }}>
                    <h2 style={{ fontSize: 15, fontWeight: 600, margin: '0 0 16px', color: '#374151' }}>Document Information</h2>
                    <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                        <div>
                            <label style={labelStyle}>DO No. *</label>
                            <input type="text" value={data.do_no} onChange={e => setData('do_no', e.target.value)} style={inputStyle} />
                            {errors.do_no && <p style={errStyle}>{errors.do_no}</p>}
                        </div>
                        <div>
                            <label style={labelStyle}>Supplier *</label>
                            <select value={data.supplier_id} onChange={e => setData('supplier_id', e.target.value)} style={inputStyle}>
                                <option value="">Select supplier…</option>
                                {suppliers.map(s => (
                                    <option key={s.id} value={s.id}>{s.name}</option>
                                ))}
                            </select>
                            {errors.supplier_id && <p style={errStyle}>{errors.supplier_id}</p>}
                        </div>
                        <div>
                            <label style={labelStyle}>Acknowledgement Date</label>
                            <input type="date" value={data.ack_date} onChange={e => setData('ack_date', e.target.value)} style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>PO Reference</label>
                            <input type="text" value={data.po_reference} onChange={e => setData('po_reference', e.target.value)} style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Sales Rep</label>
                            <input type="text" value={data.sales_rep} onChange={e => setData('sales_rep', e.target.value)} style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Terms</label>
                            <input type="text" value={data.terms} onChange={e => setData('terms', e.target.value)} style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Total Pages</label>
                            <input type="number" min="1" value={data.total_pages} onChange={e => setData('total_pages', e.target.value)} style={inputStyle} />
                        </div>
                        <div>
                            <label style={labelStyle}>Status</label>
                            <select value={data.status} onChange={e => setData('status', e.target.value)} style={inputStyle}>
                                <option value="pending">Pending</option>
                                <option value="partial">Partial</option>
                                <option value="complete">Complete</option>
                                <option value="verified">Verified</option>
                            </select>
                        </div>
                        <div>
                            <label style={labelStyle}>Upload New Document (replaces existing)</label>
                            <input type="file" accept=".pdf,.jpg,.jpeg,.png"
                                onChange={e => setData('document', e.target.files[0])} style={inputStyle} />
                        </div>
                    </div>

                    <div style={{ marginTop: 16 }}>
                        <label style={labelStyle}>Notes</label>
                        <textarea value={data.notes} onChange={e => setData('notes', e.target.value)}
                            rows={3} style={{ ...inputStyle, resize: 'vertical' }} />
                    </div>

                    {/* ── Submit ────────────────────────────────────────────── */}
                    <div style={{ marginTop: 24, display: 'flex', gap: 12 }}>
                        <button
                            type="submit"
                            disabled={processing}
                            style={{
                                padding: '10px 24px',
                                backgroundColor: '#5C001F',
                                color: '#fff',
                                border: 'none',
                                borderRadius: 8,
                                fontSize: 14,
                                fontWeight: 600,
                                cursor: processing ? 'not-allowed' : 'pointer',
                                opacity: processing ? 0.6 : 1,
                            }}
                        >
                            {processing ? 'Saving…' : 'Update Delivery Order'}
                        </button>
                        <Link
                            href={route('delivery-orders.show', deliveryOrder.id)}
                            style={{
                                padding: '10px 24px',
                                backgroundColor: '#fff',
                                color: '#374151',
                                border: '1px solid #D1D5DB',
                                borderRadius: 8,
                                fontSize: 14,
                                textDecoration: 'none',
                            }}
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}

const labelStyle = {
    display: 'block',
    fontSize: 12,
    fontWeight: 600,
    color: '#374151',
    marginBottom: 4,
};
const inputStyle = {
    width: '100%',
    padding: '8px 12px',
    borderRadius: 6,
    border: '1px solid #D1D5DB',
    fontSize: 13,
    boxSizing: 'border-box',
};
const errStyle = {
    color: '#DC2626',
    fontSize: 11,
    margin: '4px 0 0',
};
