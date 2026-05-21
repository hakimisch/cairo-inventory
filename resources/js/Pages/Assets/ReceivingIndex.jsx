import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray200: '#D6D0C8',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

function StatusBadge({ status }) {
    const map = {
        pending : { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Menunggu' },
        accepted: { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Diterima' },
        rejected: { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Ditolak'  },
    };
    const s = map[status] || map.pending;
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '3px 10px',
            borderRadius : '999px',
            fontSize     : '11px',
            fontWeight   : 700,
            background   : s.bg,
            color        : s.color,
            border       : `1px solid ${s.border}`,
            whiteSpace   : 'nowrap',
        }}>
            {s.label}
        </span>
    );
}

const inputStyle = {
    width       : '100%',
    padding     : '9px 12px',
    borderRadius: 8,
    border      : `1.5px solid ${UTM.gray100}`,
    fontSize    : '13px',
    color       : UTM.gray900,
    background  : UTM.white,
    outline     : 'none',
    boxSizing   : 'border-box',
};

const labelStyle = {
    display      : 'block',
    fontSize     : '11px',
    fontWeight   : 700,
    textTransform: 'uppercase',
    letterSpacing: '0.07em',
    color        : UTM.gray500,
    marginBottom : 5,
};

// ── Inline Create Form ────────────────────────────────────────────────────────

function CreateReceivingForm({ onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        supplier_name:      '',
        supplier_address:   '',
        purchase_order_no:  '',
        delivery_order_no:  '',
        invoice_no:         '',
        item_description:   '',
        quantity_ordered:   '1',
        quantity_received:  '1',
        unit_price:         '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('receivings.store'), {
            preserveScroll: true,
            onSuccess: () => {
                reset();
                onDone();
            },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{
            background: '#FFF5E8', borderRadius: 12,
            border: `1.5px solid ${UTM.gold}`,
            padding: 20, marginBottom: 20,
        }}>
            <p style={{ fontSize: 14, fontWeight: 800, color: UTM.maroon, marginBottom: 16 }}>
                📝 Daftar Penerimaan Baru
            </p>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 12 }}>
                <div>
                    <label style={labelStyle}>Nama Pembekal *</label>
                    <input type="text" style={inputStyle} value={data.supplier_name}
                        onChange={e => setData('supplier_name', e.target.value)} required />
                    {errors.supplier_name && <p style={{ color: '#DC2626', fontSize: 11, marginTop: 3 }}>{errors.supplier_name}</p>}
                </div>
                <div>
                    <label style={labelStyle}>No. Pesanan (PO) *</label>
                    <input type="text" style={inputStyle} value={data.purchase_order_no}
                        onChange={e => setData('purchase_order_no', e.target.value)} required />
                    {errors.purchase_order_no && <p style={{ color: '#DC2626', fontSize: 11, marginTop: 3 }}>{errors.purchase_order_no}</p>}
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 12 }}>
                <div>
                    <label style={labelStyle}>Alamat Pembekal *</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 60 }} value={data.supplier_address}
                        onChange={e => setData('supplier_address', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Butiran Item *</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 60 }} value={data.item_description}
                        onChange={e => setData('item_description', e.target.value)} required />
                    {errors.item_description && <p style={{ color: '#DC2626', fontSize: 11, marginTop: 3 }}>{errors.item_description}</p>}
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 12, marginBottom: 12 }}>
                <div>
                    <label style={labelStyle}>No. DO *</label>
                    <input type="text" style={inputStyle} value={data.delivery_order_no}
                        onChange={e => setData('delivery_order_no', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>No. Invois *</label>
                    <input type="text" style={inputStyle} value={data.invoice_no}
                        onChange={e => setData('invoice_no', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Kuantiti Dipesan *</label>
                    <input type="number" min="1" style={inputStyle} value={data.quantity_ordered}
                        onChange={e => setData('quantity_ordered', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Kuantiti Diterima *</label>
                    <input type="number" min="1" style={inputStyle} value={data.quantity_received}
                        onChange={e => setData('quantity_received', e.target.value)} required />
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12, marginBottom: 16 }}>
                <div>
                    <label style={labelStyle}>Harga Seunit (RM) *</label>
                    <input type="number" step="0.01" min="0" style={inputStyle} value={data.unit_price}
                        onChange={e => setData('unit_price', e.target.value)} required />
                    {errors.unit_price && <p style={{ color: '#DC2626', fontSize: 11, marginTop: 3 }}>{errors.unit_price}</p>}
                </div>
            </div>

            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing}
                    style={{
                        padding: '8px 22px', borderRadius: 6, border: 'none',
                        background: processing ? UTM.gray200 : UTM.maroon,
                        color: UTM.white, fontSize: 12, fontWeight: 700,
                        cursor: processing ? 'not-allowed' : 'pointer',
                        opacity: processing ? 0.7 : 1,
                        boxShadow: processing ? 'none' : '0 2px 6px rgba(92,0,31,0.2)',
                    }}>
                    {processing ? 'Menyimpan...' : 'Simpan'}
                </button>
                <button type="button" onClick={() => { reset(); onDone(); }}
                    style={{
                        padding: '8px 16px', borderRadius: 6,
                        border: `1.5px solid ${UTM.gray100}`,
                        background: UTM.white, color: UTM.gray600,
                        fontSize: 12, fontWeight: 600, cursor: 'pointer',
                    }}>
                    Batal
                </button>
            </div>
        </form>
    );
}

// ── Inline Edit Form ─────────────────────────────────────────────────────────

function EditReceivingForm({ record, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        supplier_name:      record.supplier_name ?? '',
        supplier_address:   record.supplier_address ?? '',
        purchase_order_no:  record.purchase_order_no ?? '',
        delivery_order_no:  record.delivery_order_no ?? '',
        invoice_no:         record.invoice_no ?? '',
        item_description:   record.item_description ?? '',
        quantity_ordered:   String(record.quantity_ordered ?? ''),
        quantity_received:  String(record.quantity_received ?? ''),
        unit_price:         String(record.unit_price ?? ''),
        notes:              record.notes ?? '',
        damage_description: record.damage_description ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('receivings.update', record.id), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#FEF3C7', borderRadius: 8, border: `1px solid #FDE68A`, marginBottom: 8 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: '#92400E', marginBottom: 12 }}>Edit Penerimaan — {record.receive_no}</p>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Nama Pembekal *</label>
                    <input type="text" style={inputStyle} value={data.supplier_name} onChange={e => setData('supplier_name', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>No. PO *</label>
                    <input type="text" style={inputStyle} value={data.purchase_order_no} onChange={e => setData('purchase_order_no', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>No. DO *</label>
                    <input type="text" style={inputStyle} value={data.delivery_order_no} onChange={e => setData('delivery_order_no', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>No. Invois *</label>
                    <input type="text" style={inputStyle} value={data.invoice_no} onChange={e => setData('invoice_no', e.target.value)} required />
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Alamat Pembekal *</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 60 }} value={data.supplier_address} onChange={e => setData('supplier_address', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Butiran Item *</label>
                    <textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 60 }} value={data.item_description} onChange={e => setData('item_description', e.target.value)} required />
                </div>
            </div>

            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Kuantiti Dipesan *</label>
                    <input type="number" min="1" style={inputStyle} value={data.quantity_ordered} onChange={e => setData('quantity_ordered', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Kuantiti Diterima *</label>
                    <input type="number" min="1" style={inputStyle} value={data.quantity_received} onChange={e => setData('quantity_received', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Harga Seunit (RM) *</label>
                    <input type="number" step="0.01" min="0" style={inputStyle} value={data.unit_price} onChange={e => setData('unit_price', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Catatan</label>
                    <input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan (pilihan)..." />
                </div>
            </div>

            <div style={{ marginBottom: 10 }}>
                <label style={labelStyle}>Kerosakan (jika ada)</label>
                <input type="text" style={inputStyle} value={data.damage_description} onChange={e => setData('damage_description', e.target.value)} placeholder="Huraian kerosakan (pilihan)..." />
            </div>

            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: '#92400E', color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Main Page ────────────────────────────────────────────────────────────────

export default function ReceivingIndex({ receivings }) {
    const [selectedItem, setSelectedItem] = useState(null);
    const [expandedId, setExpandedId]     = useState(null);
    const [editingId, setEditingId]       = useState(null);
    const [showCreate, setShowCreate]     = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        unit_price : '',
        category   : '',
        location   : '',
        custodian_name  : '',
        asset_type : 'fixed_asset',
        campus     : 'utm_jb',
        warranty_expiry : '',
        photo           : null,
        brand           : '',
        model           : '',
        serial_number   : '',
        saga_id         : '',
        budget_vot      : '',
    });

    const handleAcceptClick = (item) => {
        setSelectedItem(item);
        reset();
    };

    const handleAcceptSubmit = (e) => {
        e.preventDefault();
        post(route('receivings.accept', selectedItem.id), {
            onSuccess: () => setSelectedItem(null),
        });
    };

    const handleDelete = (item) => {
        if (window.confirm(`Padam penerimaan ${item.receive_no} (${item.supplier_name})? Tindakan ini tidak boleh dibatalkan.`)) {
            router.delete(route('receivings.destroy', item.id), { preserveScroll: true });
        }
    };

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
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '16px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                        <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                            Pengurusan Penerimaan (KEW.PA-1)
                        </h2>
                    </div>
                    <button
                        onClick={() => setShowCreate(!showCreate)}
                        style={{
                            background    : showCreate ? UTM.gray100 : UTM.maroon,
                            color         : showCreate ? UTM.gray700 : UTM.white,
                            padding       : '9px 18px',
                            borderRadius  : 8,
                            fontSize      : '13px',
                            fontWeight    : 700,
                            border        : showCreate ? `1.5px solid ${UTM.gray200}` : 'none',
                            cursor        : 'pointer',
                            boxShadow     : showCreate ? 'none' : '0 2px 6px rgba(92,0,31,0.2)',
                            whiteSpace    : 'nowrap',
                            transition    : 'all 0.12s',
                        }}
                    >
                        {showCreate ? '✕ Batal' : '+ Daftar Penerimaan Baru'}
                    </button>
                </div>
            }
        >
            <Head title="Senarai Penerimaan" />

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 1280, margin: '0 auto' }}>

                    {/* ── Inline Create Form ── */}
                    {showCreate && (
                        <CreateReceivingForm onDone={() => setShowCreate(false)} />
                    )}

                    {/* ── Table Container (Scrollable) ── */}
                    <div style={{ background: UTM.white, borderRadius: 12,
                                  boxShadow: '0 1px 4px rgba(92,0,31,0.07)', overflow: 'hidden' }}>
                        <div style={{ overflowX: 'auto', width: '100%' }}>
                            <table style={{ width: '100%', minWidth: '1050px', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr>
                                        <th style={thStyle}>No. Penerimaan</th>
                                        <th style={thStyle}>Pembekal</th>
                                        <th style={thStyle}>Item</th>
                                        <th style={{ ...thStyle, textAlign: 'center' }}>Qty Dipesan</th>
                                        <th style={{ ...thStyle, textAlign: 'center' }}>Qty Diterima</th>
                                        <th style={thStyle}>Status</th>
                                        <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {receivings.length === 0 && (
                                        <tr>
                                            <td colSpan={7} style={{ padding: '48px', textAlign: 'center', color: UTM.gray500, fontSize: '14px' }}>
                                                Tiada rekod penerimaan.
                                            </td>
                                        </tr>
                                    )}
                                    {receivings.map((item, idx) => (
                                        <React.Fragment key={item.id}>
                                            <tr style={{
                                                    background  : expandedId === item.id ? '#FFF5E8' : (idx % 2 === 0 ? UTM.white : UTM.gray50),
                                                    transition  : 'background 0.12s',
                                                }}
                                                onMouseEnter={e => { if(expandedId !== item.id) e.currentTarget.style.background = '#FFF5E8' }}
                                                onMouseLeave={e => { if(expandedId !== item.id) e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50 }}
                                            >
                                                <td style={{ padding: '14px 20px', fontFamily: 'monospace', fontSize: '12px', fontWeight: 700, color: UTM.maroon, whiteSpace: 'nowrap' }}>
                                                    {item.receive_no}
                                                </td>
                                                <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>
                                                    {item.supplier_name}
                                                </td>
                                                <td style={{ padding: '14px 20px', fontSize: '13px', fontWeight: 600, color: UTM.gray900 }}>
                                                    {item.item_description}
                                                </td>
                                                <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700, textAlign: 'center' }}>
                                                    {item.quantity_ordered}
                                                </td>
                                                <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700, textAlign: 'center' }}>
                                                    {item.quantity_received}
                                                </td>
                                                <td style={{ padding: '14px 20px' }}>
                                                    <StatusBadge status={item.status} />
                                                </td>
                                                <td style={{ padding: '14px 20px', textAlign: 'right', whiteSpace: 'nowrap' }}>
                                                    {/* Expand Details Button */}
                                                    <button
                                                        onClick={() => { setExpandedId(expandedId === item.id ? null : item.id); if (editingId === item.id) setEditingId(null); }}
                                                        style={{
                                                            display     : 'inline-block',
                                                            padding     : '5px 12px',
                                                            borderRadius: 6,
                                                            fontSize    : '12px',
                                                            fontWeight  : 700,
                                                            background  : expandedId === item.id ? UTM.gold : '#EDE9E4',
                                                            color       : expandedId === item.id ? UTM.maroon : UTM.gray700,
                                                            border      : 'none',
                                                            cursor      : 'pointer',
                                                            marginRight : 8,
                                                            transition  : 'all 0.12s',
                                                        }}
                                                    >
                                                        {expandedId === item.id ? 'Tutup' : 'Butiran'}
                                                    </button>

                                                    <Link
                                                        href={route('receivings.kewpa1', item.id)}
                                                        style={{
                                                            display       : 'inline-block',
                                                            padding       : '5px 12px',
                                                            borderRadius  : 6,
                                                            fontSize      : '12px',
                                                            fontWeight    : 700,
                                                            color         : UTM.maroon,
                                                            background    : '#F3E0E5',
                                                            textDecoration: 'none',
                                                            marginRight   : 8,
                                                        }}
                                                    >
                                                        KEW.PA-1
                                                    </Link>

                                                    {item.status === 'pending' && (
                                                        <>
                                                            <button
                                                                onClick={() => handleAcceptClick(item)}
                                                                style={{
                                                                    padding     : '5px 12px',
                                                                    borderRadius: 6,
                                                                    fontSize    : '12px',
                                                                    fontWeight  : 700,
                                                                    background  : '#1A7A3C',
                                                                    color       : UTM.white,
                                                                    border      : 'none',
                                                                    cursor      : 'pointer',
                                                                    marginRight : 8,
                                                                }}
                                                            >
                                                                Terima & Daftar
                                                            </button>
                                                            <Link
                                                                href={route('receivings.reject', item.id)}
                                                                method="post"
                                                                as="button"
                                                                style={{
                                                                    padding     : '5px 12px',
                                                                    borderRadius: 6,
                                                                    fontSize    : '12px',
                                                                    fontWeight  : 700,
                                                                    background  : '#F3E0E5',
                                                                    color       : UTM.maroon,
                                                                    border      : 'none',
                                                                    cursor      : 'pointer',
                                                                    marginRight : 8,
                                                                }}
                                                           >
                                                                Tolak
                                                            </Link>
                                                            <button
                                                                onClick={() => { setEditingId(editingId === item.id ? null : item.id); if (expandedId === item.id) setExpandedId(null); }}
                                                                style={{
                                                                    padding     : '5px 12px',
                                                                    borderRadius: 6,
                                                                    fontSize    : '12px',
                                                                    fontWeight  : 700,
                                                                    background  : editingId === item.id ? '#FDE68A' : '#E6F4EC',
                                                                    color       : editingId === item.id ? '#92400E' : '#065F46',
                                                                    border      : 'none',
                                                                    cursor      : 'pointer',
                                                                    marginRight : 8,
                                                                }}
                                                            >
                                                                {editingId === item.id ? 'Batal' : 'Edit'}
                                                            </button>
                                                            <button
                                                                onClick={() => handleDelete(item)}
                                                                style={{
                                                                    padding     : '5px 12px',
                                                                    borderRadius: 6,
                                                                    fontSize    : '12px',
                                                                    fontWeight  : 700,
                                                                    background  : '#FEE2E2',
                                                                    color       : '#991B1B',
                                                                    border      : 'none',
                                                                    cursor      : 'pointer',
                                                                }}
                                                            >
                                                                Hapus
                                                            </button>
                                                        </>
                                                    )}
                                                </td>
                                            </tr>

                                            {/* ── Edit Form Row ── */}
                                            {editingId === item.id && (
                                                <tr style={{ background: '#FFFBEB' }}>
                                                    <td colSpan={7} style={{ padding: 0 }}>
                                                        <EditReceivingForm
                                                            record={item}
                                                            onDone={() => setEditingId(null)}
                                                        />
                                                    </td>
                                                </tr>
                                            )}

                                            {/* ── Collapsible Expandable Row ── */}
                                            {expandedId === item.id && editingId !== item.id && (
                                                <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                                    <td colSpan={7} style={{ padding: 0 }}>
                                                        <div style={{ 
                                                            padding: '24px 32px', 
                                                            display: 'flex', 
                                                            flexWrap: 'wrap', 
                                                            gap: '32px', 
                                                            borderLeft: `4px solid ${UTM.gold}` 
                                                        }}>
                                                            
                                                            {/* Info Columns Grid */}
                                                            <div style={{ 
                                                                flex: '1 1 100%', 
                                                                display: 'grid', 
                                                                gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))', 
                                                                gap: '24px' 
                                                            }}>
                                                                
                                                                {/* Maklumat Pembekal */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Maklumat Pembekal</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>Nama Pembekal</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.supplier_name || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500, verticalAlign: 'top' }}>Alamat</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.supplier_address || '—'}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                {/* Rujukan Dokumen */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Rujukan Dokumen</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>No. Pesanan (PO)</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.purchase_order_no || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>No. Hantaran (DO)</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.delivery_order_no || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>No. Invois</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.invoice_no || '—'}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Tarikh Daftar</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{new Date(item.created_at).toLocaleDateString('ms-MY')}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                {/* Butiran Harga */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Kuantiti & Kewangan</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>Dipesan / Diterima</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.quantity_ordered} / {item.quantity_received} unit</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Harga Seunit</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>RM {Number(item.unit_price || 0).toLocaleString('ms-MY', { minimumFractionDigits: 2 })}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Jumlah Harga</td><td style={{ fontWeight: 800, color: UTM.maroon }}>RM {Number(item.total_price || (item.unit_price * item.quantity_received)).toLocaleString('ms-MY', { minimumFractionDigits: 2 })}</td></tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                                {/* Pegawai Terlibat */}
                                                                <div>
                                                                    <h4 style={{ fontSize: '12px', fontWeight: 800, color: UTM.maroon, textTransform: 'uppercase', borderBottom: `2px solid ${UTM.gray100}`, paddingBottom: '6px', marginBottom: '12px' }}>Pegawai Pengesah</h4>
                                                                    <table style={{ width: '100%', fontSize: '12px', lineHeight: '1.8' }}>
                                                                        <tbody>
                                                                            <tr><td style={{ color: UTM.gray500, width: '40%' }}>Pegawai Penerima</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.receiver_name || <span style={{ color: UTM.gray300, fontStyle: 'italic' }}>Belum diisi</span>}</td></tr>
                                                                            <tr><td style={{ color: UTM.gray500 }}>Pegawai Pengesah</td><td style={{ fontWeight: 600, color: UTM.gray900 }}>{item.technical_officer_name || <span style={{ color: UTM.gray300, fontStyle: 'italic' }}>Belum diisi</span>}</td></tr>
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

                        <div style={{ padding: '12px 20px', borderTop: `1px solid ${UTM.gray100}`,
                                      background: UTM.gray50, fontSize: '12px', color: UTM.gray500 }}>
                            <strong style={{ color: UTM.maroon }}>{receivings.length}</strong> rekod penerimaan
                        </div>
                    </div>

                </div>
            </div>

            {/* ── Acceptance modal ── */}
            {selectedItem && (
                <div style={{
                    position      : 'fixed',
                    inset         : 0,
                    background    : 'rgba(0,0,0,0.45)',
                    display       : 'flex',
                    alignItems    : 'center',
                    justifyContent: 'center',
                    zIndex        : 50,
                    padding       : 16,
                }}>
                    <div style={{
                        background  : UTM.white,
                        borderRadius: 16,
                        boxShadow   : '0 8px 40px rgba(92,0,31,0.2)',
                        maxWidth    : 480,
                        width       : '100%',
                        overflow    : 'hidden',
                    }}>
                        {/* Modal header */}
                        <div style={{ background: UTM.maroon, padding: '20px 24px' }}>
                            <p style={{ fontSize: '16px', fontWeight: 800, color: UTM.white, marginBottom: 4 }}>
                                Sahkan Penerimaan Aset
                            </p>
                            <p style={{ fontSize: '12px', color: UTM.sand }}>
                                {selectedItem.item_description}
                            </p>
                        </div>

                        {/* Modal body */}
                        <form onSubmit={handleAcceptSubmit} style={{ padding: '24px', maxHeight: '80vh', overflowY: 'auto' }}>
                            <p style={{ fontSize: '12px', color: UTM.gray500, marginBottom: 20 }}>
                                Lengkapkan butiran untuk menjana{' '}
                                <strong style={{ color: UTM.maroon }}>KEW.PA-3 / KEW.PA-2</strong> dan mendaftarkan aset.
                            </p>

                            {/* ── UTM Specs & Financials ── */}
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14, marginBottom: 20 }}>
                                <div>
                                    <label style={labelStyle}>Buatan (Brand) *</label>
                                    <input 
                                        type="text" 
                                        required 
                                        style={inputStyle} 
                                        value={data.brand} 
                                        onChange={e => setData('brand', e.target.value)} 
                                        placeholder="cth: Lenovo" 
                                    />
                                    {errors?.brand && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.brand}</p>}
                                </div>
        
                                <div>
                                    <label style={labelStyle}>Model *</label>
                                    <input 
                                        type="text" 
                                        required 
                                        style={inputStyle} 
                                        value={data.model} 
                                        onChange={e => setData('model', e.target.value)} 
                                        placeholder="cth: Legion Pro 5" 
                                    />
                                    {errors?.model && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.model}</p>}
                                </div>

                                <div>
                                    <label style={labelStyle}>No. Siri / Casis *</label>
                                    <input 
                                        type="text" 
                                        required 
                                        style={inputStyle} 
                                        value={data.serial_number} 
                                        onChange={e => setData('serial_number', e.target.value)} 
                                        placeholder="cth: PF57G36K" 
                                    />
                                    {errors?.serial_number && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.serial_number}</p>}
                                </div>

                                <div>
                                    <label style={labelStyle}>ID SAGA</label>
                                    <input 
                                        type="text" 
                                        style={inputStyle} 
                                        value={data.saga_id} 
                                        onChange={e => setData('saga_id', e.target.value)} 
                                        placeholder="cth: B35201" 
                                    />
                                </div>

                                <div style={{ gridColumn: '1 / -1' }}>
                                    <label style={labelStyle}>Vot Bajet</label>
                                    <input 
                                        type="text" 
                                        style={inputStyle} 
                                        value={data.budget_vot} 
                                        onChange={e => setData('budget_vot', e.target.value)} 
                                        placeholder="cth: A.K090302.5500.07204" 
                                    />
                                </div>
                            </div>

                            <div style={{ marginBottom: 14 }}>
                                <label style={labelStyle}>Kategori Aset *</label>
                                <select
                                    required
                                    style={inputStyle}
                                    value={data.category}
                                    onChange={e => setData('category', e.target.value)}
                                >
                                    <option value="">Pilih kategori...</option>
                                    <option value="Server">Server</option>
                                    <option value="Workstation">Workstation</option>
                                    <option value="GPU Node">GPU Node</option>
                                    <option value="Sensor">Sensor</option>
                                    <option value="Perabot">Perabot</option>
                                    <option value="Lain-lain">Lain-lain</option>
                                </select>
                                {errors?.category && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.category}</p>}
                            </div>

                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 14, marginBottom: 20 }}>
                                <div>
                                    <label style={labelStyle}>Lokasi Penempatan *</label>
                                    <input
                                        type="text"
                                        required
                                        maxLength="255"
                                        style={inputStyle}
                                        value={data.location}
                                        onChange={e => setData('location', e.target.value)}
                                        placeholder="cth: Makmal AI"
                                    />
                                    {errors?.location && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.location}</p>}
                                </div>
        
                                <div>
                                    <label style={labelStyle}>Tamat Waranti</label>
                                    <input
                                        type="date"
                                        min={new Date().toISOString().split('T')[0]} 
                                        style={inputStyle}
                                        value={data.warranty_expiry}
                                        onChange={e => setData('warranty_expiry', e.target.value)}
                                    />
                                    {errors?.warranty_expiry && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.warranty_expiry}</p>}
                                </div>
                            </div>

                            <div style={{ marginBottom: 20 }}>
                                <label style={labelStyle}>Pegawai Bertanggungjawab (Pemegang Aset)</label>
                                <input
                                    type="text"
                                    maxLength="255"
                                    style={inputStyle}
                                    value={data.custodian_name}
                                    onChange={e => setData('custodian_name', e.target.value)}
                                    placeholder="cth: Ts. Dr. Mohd Ibrahim"
                                />
                                {errors?.custodian_name && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.custodian_name}</p>}
                            </div>

                            <div style={{ marginBottom: 20 }}>
                                <label style={labelStyle}>Gambar Aset (Pilihan)</label>
                                <div style={{
                                    border: `1.5px dashed ${UTM.gray200}`,
                                    borderRadius: 8,
                                    padding: '12px',
                                    background: UTM.white,
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: 10
                                }}>
                                    <input
                                        type="file"
                                        accept="image/*"
                                        onChange={e => setData('photo', e.target.files[0])}
                                        style={{
                                            fontSize: '12px',
                                            color: UTM.gray700,
                                            width: '100%'
                                        }}
                                    />
                                </div>
                                <p style={{ fontSize: '10px', color: UTM.gray500, marginTop: 4, fontStyle: 'italic' }}>
                                    *Gambar akan dipaparkan pada borang pendaftaran akhir
                                </p>
                                {errors?.photo && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.photo}</p>}
                            </div>

                            <div style={{ marginBottom: 20 }}>
                                <label style={labelStyle}>Kampus *</label>
                                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 10 }}>
                                    {[
                                        { value: 'utm_jb', label: 'UTM Johor Bahru' },
                                        { value: 'utm_kl', label: 'UTM Kuala Lumpur' },
                                    ].map(opt => (
                                        <button
                                            key={opt.value}
                                            type="button"
                                            onClick={() => setData('campus', opt.value)}
                                            style={{
                                                padding    : '10px 14px',
                                                borderRadius: 8,
                                                border     : `2px solid ${data.campus === opt.value
                                                    ? (opt.value === 'utm_kl' ? UTM.maroon : UTM.goldDark)
                                                    : UTM.gray100}`,
                                                background : data.campus === opt.value
                                                    ? (opt.value === 'utm_kl' ? '#F3E0E5' : '#FEF3D6')
                                                    : UTM.white,
                                                cursor     : 'pointer',
                                                fontWeight : 700,
                                                fontSize   : '13px',
                                                color      : data.campus === opt.value
                                                    ? (opt.value === 'utm_kl' ? UTM.maroon : UTM.goldDark)
                                                    : UTM.gray700,
                                                transition : 'all 0.12s',
                                            }}
                                        >
                                            {opt.label}
                                        </button>
                                    ))}
                                </div>
                                {errors?.campus && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.campus}</p>}
                            </div>

                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10, marginTop: 10 }}>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setSelectedItem(null);
                                        reset(); 
                                    }}
                                    style={{
                                        padding     : '10px 20px',
                                        borderRadius: 8,
                                        fontSize    : '13px',
                                        fontWeight  : 700,
                                        background  : UTM.gray100,
                                        color       : UTM.gray700,
                                        border      : 'none',
                                        cursor      : 'pointer',
                                    }}
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    style={{
                                        padding     : '10px 24px',
                                        borderRadius: 8,
                                        fontSize    : '13px',
                                        fontWeight  : 700,
                                        background  : processing ? UTM.gray200 : UTM.maroon,
                                        color       : UTM.white,
                                        border      : 'none',
                                        cursor      : processing ? 'not-allowed' : 'pointer',
                                        boxShadow   : processing ? 'none' : '0 2px 8px rgba(92,0,31,0.2)',
                                    }}
                                >
                                    {processing ? 'Menjana...' : 'Sahkan & Daftar Aset'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
