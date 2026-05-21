import React, { useState } from 'react';
import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const UTM = {
    maroon : '#7B1818',
    gold   : '#D4A843',
    white  : '#FFFFFF',
    gray50 : '#F8F6F3',
    gray100: '#EDE9E4',
    gray200: '#D6D0C8',
    gray500: '#8A8078',
    gray600: '#6B6360',
    gray700: '#4A4440',
};

const cardStyle = { background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, padding: 20 };
const sectionTitleStyle = { fontSize: 11, fontWeight: 700, color: UTM.gray500, textTransform: 'uppercase', letterSpacing: '0.5px', marginBottom: 12 };
const labelStyle = { fontSize: 12, color: UTM.gray500 };
const valueStyle = { fontSize: 13, fontWeight: 600, color: UTM.gray700 };
const inputStyle = { width: '100%', padding: '7px 10px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, fontSize: 12, color: UTM.gray700, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const selectStyle = { ...inputStyle, cursor: 'pointer' };

function SaleTypeBadge({ type }) {
    const map = { Tawaran: { bg: '#DBEAFE', color: '#1E40AF' }, Sebutharga: { bg: '#EDE9FE', color: '#5B21B6' }, Lelongan: { bg: '#FFEDD5', color: '#9A3412' } };
    const s = map[type] || { bg: UTM.gray100, color: UTM.gray600 };
    return <span style={{ display: 'inline-block', padding: '2px 10px', borderRadius: 999, fontSize: 11, fontWeight: 700, background: s.bg, color: s.color }}>{type}</span>;
}

// ── Inline editable field ────────────────────────────────────────────────────
function InlineField({ label, value, editValue, onChange, type = 'text', options, step }) {
    return (
        <div>
            <label style={{ fontSize: 10, fontWeight: 700, color: UTM.gray500, textTransform: 'uppercase', marginBottom: 2, display: 'block' }}>{label}</label>
            {options ? (
                <select style={selectStyle} value={editValue} onChange={e => onChange(e.target.value)}>
                    <option value="">—</option>
                    {options.map(o => <option key={o.value ?? o} value={o.value ?? o}>{o.label ?? o}</option>)}
                </select>
            ) : (
                <input type={type} step={step} style={inputStyle} value={editValue} onChange={e => onChange(e.target.value)} />
            )}
        </div>
    );
}

// ── Add Item Form ────────────────────────────────────────────────────────────
function AddItemForm({ sale, onDone, assets }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        asset_id: '', item_description: '', quantity: '1', reserve_price: '', estimated_value: '', lot_number: '', status: 'available', notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('disposal-sales.items.store', sale.id), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#FFFBEB', borderRadius: 8, border: `1px solid #FDE68A`, marginBottom: 12 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: '#92400E', marginBottom: 12 }}>+ Tambah Item Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <InlineField label="Aset (Pilihan)" editValue={data.asset_id} onChange={v => setData('asset_id', v)} options={[{ value: '', label: '— Tiada Aset —' }, ...assets.map(a => ({ value: String(a.id), label: `${a.asset_tag} — ${a.name}` }))]} />
                <InlineField label="Butiran Item" editValue={data.item_description} onChange={v => setData('item_description', v)} />
                <InlineField label="Lot No." editValue={data.lot_number} onChange={v => setData('lot_number', v)} />
                <InlineField label="Kuantiti" type="number" editValue={data.quantity} onChange={v => setData('quantity', v)} />
                <InlineField label="Harga Rizab (RM)" type="number" step="0.01" editValue={data.reserve_price} onChange={v => setData('reserve_price', v)} />
                <InlineField label="Anggaran Nilai (RM)" type="number" step="0.01" editValue={data.estimated_value} onChange={v => setData('estimated_value', v)} />
            </div>
            {errors.asset_id && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.asset_id}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: '#92400E', color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Item'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Add Bid Form ─────────────────────────────────────────────────────────────
function AddBidForm({ sale, item, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        bidder_name: '', bidder_ic: '', bidder_phone: '', bidder_address: '',
        bid_amount: '', bid_date: new Date().toISOString().split('T')[0],
        deposit_paid: false, deposit_amount: '', status: 'pending', notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('disposal-sales.items.bids.store', [sale.id, item.id]), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#F0FDF4', borderRadius: 6, border: `1px solid #BBF7D0`, marginTop: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#166534', marginBottom: 8 }}>+ Tambah Bidaan Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <InlineField label="Nama Bidaan" editValue={data.bidder_name} onChange={v => setData('bidder_name', v)} />
                <InlineField label="No. IC" editValue={data.bidder_ic} onChange={v => setData('bidder_ic', v)} />
                <InlineField label="No. Telefon" editValue={data.bidder_phone} onChange={v => setData('bidder_phone', v)} />
                <InlineField label="Jumlah Bidaan (RM)" type="number" step="0.01" editValue={data.bid_amount} onChange={v => setData('bid_amount', v)} />
                <InlineField label="Tarikh Bidaan" type="date" editValue={data.bid_date} onChange={v => setData('bid_date', v)} />
                <InlineField label="Deposit (RM)" type="number" step="0.01" editValue={data.deposit_amount} onChange={v => setData('deposit_amount', v)} />
            </div>
            {errors.bidder_name && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.bidder_name}</p>}
            <div style={{ display: 'flex', alignItems: 'center', gap: 12, marginBottom: 8 }}>
                <label style={{ display: 'flex', alignItems: 'center', gap: 6, fontSize: 12, cursor: 'pointer' }}>
                    <input type="checkbox" checked={data.deposit_paid} onChange={e => setData('deposit_paid', e.target.checked)} />
                    Deposit Dibayar
                </label>
            </div>
            <InlineField label="Catatan" editValue={data.notes} onChange={v => setData('notes', v)} />
            <div style={{ display: 'flex', gap: 8, marginTop: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '6px 16px', borderRadius: 6, border: 'none', background: '#166534', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Bidaan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '6px 14px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Inline Edit Item ─────────────────────────────────────────────────────────
function EditItemForm({ sale, item, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        item_description: item.item_description ?? '',
        quantity: item.quantity ?? '1',
        reserve_price: item.reserve_price ?? '',
        estimated_value: item.estimated_value ?? '',
        lot_number: item.lot_number ?? '',
        status: item.status ?? 'available',
        notes: item.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('disposal-sales.items.update', [sale.id, item.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#EFF6FF', borderRadius: 6, border: `1px solid #BFDBFE`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#1E40AF', marginBottom: 8 }}>Edit Item</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <InlineField label="Butiran Item" editValue={data.item_description} onChange={v => setData('item_description', v)} />
                <InlineField label="Lot No." editValue={data.lot_number} onChange={v => setData('lot_number', v)} />
                <InlineField label="Kuantiti" type="number" editValue={data.quantity} onChange={v => setData('quantity', v)} />
                <InlineField label="Harga Rizab (RM)" type="number" step="0.01" editValue={data.reserve_price} onChange={v => setData('reserve_price', v)} />
                <InlineField label="Anggaran Nilai (RM)" type="number" step="0.01" editValue={data.estimated_value} onChange={v => setData('estimated_value', v)} />
                <InlineField label="Status" editValue={data.status} onChange={v => setData('status', v)} options={['available', 'sold', 'unsold', 'withdrawn']} />
            </div>
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '6px 16px', borderRadius: 6, border: 'none', background: '#1E40AF', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '6px 14px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Inline Edit Bid ──────────────────────────────────────────────────────────
function EditBidForm({ sale, item, bid, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        bidder_name: bid.bidder_name ?? '',
        bidder_ic: bid.bidder_ic ?? '',
        bidder_phone: bid.bidder_phone ?? '',
        bidder_address: bid.bidder_address ?? '',
        bid_amount: bid.bid_amount ?? '',
        bid_date: bid.bid_date ? bid.bid_date.split('T')[0] : new Date().toISOString().split('T')[0],
        deposit_paid: bid.deposit_paid ?? false,
        deposit_amount: bid.deposit_amount ?? '',
        is_winner: bid.is_winner ?? false,
        award_date: bid.award_date ? bid.award_date.split('T')[0] : '',
        payment_date: bid.payment_date ? bid.payment_date.split('T')[0] : '',
        payment_received: bid.payment_received ?? false,
        status: bid.status ?? 'pending',
        notes: bid.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('disposal-sales.items.bids.update', [sale.id, item.id, bid.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 10, background: '#F5F3FF', borderRadius: 6, border: `1px solid #DDD6FE`, marginTop: 6 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#5B21B6', marginBottom: 8 }}>Edit Bidaan — {bid.bidder_name}</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <InlineField label="Nama" editValue={data.bidder_name} onChange={v => setData('bidder_name', v)} />
                <InlineField label="No. IC" editValue={data.bidder_ic} onChange={v => setData('bidder_ic', v)} />
                <InlineField label="No. Telefon" editValue={data.bidder_phone} onChange={v => setData('bidder_phone', v)} />
                <InlineField label="Jumlah Bidaan (RM)" type="number" step="0.01" editValue={data.bid_amount} onChange={v => setData('bid_amount', v)} />
                <InlineField label="Tarikh Bidaan" type="date" editValue={data.bid_date} onChange={v => setData('bid_date', v)} />
                <InlineField label="Deposit (RM)" type="number" step="0.01" editValue={data.deposit_amount} onChange={v => setData('deposit_amount', v)} />
                <InlineField label="Tarikh Anugerah" type="date" editValue={data.award_date} onChange={v => setData('award_date', v)} />
                <InlineField label="Tarikh Bayaran" type="date" editValue={data.payment_date} onChange={v => setData('payment_date', v)} />
                <InlineField label="Status" editValue={data.status} onChange={v => setData('status', v)} options={['pending', 'accepted', 'rejected', 'paid', 'completed']} />
            </div>
            <div style={{ display: 'flex', gap: 16, marginBottom: 8, flexWrap: 'wrap' }}>
                <label style={{ display: 'flex', alignItems: 'center', gap: 5, fontSize: 11, cursor: 'pointer' }}><input type="checkbox" checked={data.deposit_paid} onChange={e => setData('deposit_paid', e.target.checked)} /> Deposit Dibayar</label>
                <label style={{ display: 'flex', alignItems: 'center', gap: 5, fontSize: 11, cursor: 'pointer' }}><input type="checkbox" checked={data.is_winner} onChange={e => setData('is_winner', e.target.checked)} /> Pemenang</label>
                <label style={{ display: 'flex', alignItems: 'center', gap: 5, fontSize: 11, cursor: 'pointer' }}><input type="checkbox" checked={data.payment_received} onChange={e => setData('payment_received', e.target.checked)} /> Bayaran Diterima</label>
            </div>
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#5B21B6', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Main Page ────────────────────────────────────────────────────────────────
export default function Show({ sale, assets }) {
    const [showAddItem, setShowAddItem] = useState(false);
    const [editingItem, setEditingItem] = useState(null);
    const [showAddBid, setShowAddBid] = useState({});
    const [editingBid, setEditingBid] = useState(null);

    const handleDeleteItem = (item) => {
        if (window.confirm(`Padam item "${item.item_description || item.asset?.name}" (Lot ${item.lot_number})?`)) {
            router.delete(route('disposal-sales.items.destroy', [sale.id, item.id]), { preserveScroll: true });
        }
    };

    const handleDeleteBid = (bid) => {
        if (window.confirm(`Padam bidaan daripada ${bid.bidder_name} (RM ${Number(bid.bid_amount).toLocaleString()})?`)) {
            router.delete(route('disposal-sales.items.bids.destroy', [sale.id, bid.disposal_sale_item_id, bid.id]), { preserveScroll: true });
        }
    };

    const DetailItem = ({ label, children }) => (
        <div><div style={labelStyle}>{label}</div><div style={valueStyle}>{children ?? '—'}</div></div>
    );

    const btnStyle = (bg, color) => ({
        display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600,
        background: bg, color, border: 'none', cursor: 'pointer', textDecoration: 'none',
    });

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', width: '100%' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                        <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                            Disposal Sale: {sale.sale_reference}
                        </h2>
                    </div>
                    <Link href={route('disposal-sales.index')}
                        style={{ fontSize: 13, color: UTM.gray500, textDecoration: 'none', fontWeight: 600 }}>
                        ← Back to List
                    </Link>
                </div>
            }
        >
            <Head title={`Sale: ${sale.sale_reference}`} />

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 960, margin: '0 auto', display: 'flex', flexDirection: 'column', gap: 20 }}>

                    {/* ── Sale Details ── */}
                    <div style={cardStyle}>
                        <h3 style={sectionTitleStyle}>Sale Details</h3>
                        <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))', gap: 16 }}>
                            <DetailItem label="Reference">{sale.sale_reference}</DetailItem>
                            <div><div style={labelStyle}>Type</div><SaleTypeBadge type={sale.sale_type} /></div>
                            <DetailItem label="Date">{sale.sale_date ?? '-'}</DetailItem>
                            <DetailItem label="Location">{sale.sale_location ?? '-'}</DetailItem>
                            <DetailItem label="Officer">{sale.sale_officer ?? '-'}</DetailItem>
                            <DetailItem label="Deposit Required">{sale.deposit_required ? `RM ${Number(sale.deposit_required).toLocaleString()}` : '-'}</DetailItem>
                            <DetailItem label="Status">{sale.status}</DetailItem>
                            <DetailItem label="Sale Status">{sale.sale_status}</DetailItem>
                        </div>
                        {sale.description && (
                            <div style={{ marginTop: 16 }}>
                                <div style={labelStyle}>Description</div>
                                <p style={{ ...valueStyle, margin: '4px 0 0', fontWeight: 400 }}>{sale.description}</p>
                            </div>
                        )}
                    </div>

                    {/* ── Asset Disposal Info ── */}
                    {sale.asset_disposal && (
                        <div style={cardStyle}>
                            <h3 style={sectionTitleStyle}>Asset Disposal</h3>
                            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))', gap: 16 }}>
                                <DetailItem label="Asset">{sale.asset_disposal.asset?.name} ({sale.asset_disposal.asset?.asset_tag})</DetailItem>
                                <DetailItem label="Method">{sale.asset_disposal.disposal_method}</DetailItem>
                                <DetailItem label="Date">{sale.asset_disposal.disposal_date ?? '-'}</DetailItem>
                                <DetailItem label="Status">{sale.asset_disposal.status}</DetailItem>
                            </div>
                        </div>
                    )}

                    {/* ── Sale Items ── */}
                    <div style={{ ...cardStyle, padding: 0, overflow: 'hidden' }}>
                        <div style={{ padding: '16px 20px', borderBottom: `1px solid ${UTM.gray100}`, display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <h3 style={{ ...sectionTitleStyle, marginBottom: 0 }}>
                                Sale Items ({sale.disposal_sale_items?.length ?? 0})
                            </h3>
                            <button onClick={() => setShowAddItem(!showAddItem)} style={{
                                padding: '6px 14px', borderRadius: 6, border: 'none',
                                background: showAddItem ? UTM.gray100 : '#92400E', color: showAddItem ? UTM.gray700 : UTM.white,
                                fontSize: 11, fontWeight: 700, cursor: 'pointer',
                            }}>
                                {showAddItem ? 'Batal' : '+ Tambah Item'}
                            </button>
                        </div>

                        <div style={{ padding: '12px 20px' }}>
                            {showAddItem && <AddItemForm sale={sale} onDone={() => setShowAddItem(false)} assets={assets} />}

                            {(!sale.disposal_sale_items || sale.disposal_sale_items.length === 0) && !showAddItem && (
                                <p style={{ textAlign: 'center', color: UTM.gray500, fontSize: 12, padding: 24 }}>
                                    Tiada item jualan. Klik "+ Tambah Item" untuk menambah.
                                </p>
                            )}

                            {sale.disposal_sale_items?.map((item) => (
                                <div key={item.id} style={{ borderBottom: `1px solid ${UTM.gray100}`, padding: '12px 0' }}>
                                    {editingItem === item.id ? (
                                        <EditItemForm sale={sale} item={item} onDone={() => setEditingItem(null)} />
                                    ) : (
                                        <>
                                            {/* Item row */}
                                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                                                <div style={{ flex: 1 }}>
                                                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(140px, 1fr))', gap: 8 }}>
                                                        <DetailItem label="Lot">{item.lot_number ?? '-'}</DetailItem>
                                                        <DetailItem label="Description">{item.item_description ?? item.asset?.name ?? '-'}</DetailItem>
                                                        <DetailItem label="Qty">{item.quantity}</DetailItem>
                                                        <DetailItem label="Reserve">{item.reserve_price ? `RM ${Number(item.reserve_price).toLocaleString()}` : '-'}</DetailItem>
                                                        <DetailItem label="Est. Value">{item.estimated_value ? `RM ${Number(item.estimated_value).toLocaleString()}` : '-'}</DetailItem>
                                                        <DetailItem label="Status">
                                                            <span style={{
                                                                display: 'inline-block', padding: '1px 8px', borderRadius: 999, fontSize: 10, fontWeight: 600,
                                                                background: item.status === 'available' ? '#DBEAFE' : item.status === 'sold' ? '#DCFCE7' : '#FEE2E2',
                                                                color: item.status === 'available' ? '#1E40AF' : item.status === 'sold' ? '#166534' : '#991B1B',
                                                            }}>{item.status}</span>
                                                        </DetailItem>
                                                    </div>
                                                </div>
                                                <div style={{ display: 'flex', gap: 4, flexShrink: 0, marginLeft: 12 }}>
                                                    <button onClick={() => setEditingItem(item.id)} style={btnStyle('#EFF6FF', '#1E40AF')}>Edit</button>
                                                    <button onClick={() => handleDeleteItem(item)} style={btnStyle('#FEF2F2', '#DC2626')}>Padam</button>
                                                </div>
                                            </div>

                                            {/* Bids section */}
                                            <div style={{ marginTop: 8, marginLeft: 16, paddingLeft: 12, borderLeft: `2px solid ${UTM.gold}` }}>
                                                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 }}>
                                                    <p style={{ fontSize: 11, color: UTM.gray500, fontWeight: 600 }}>
                                                        Bidaan ({item.sale_bids?.length ?? 0})
                                                    </p>
                                                    <button onClick={() => setShowAddBid({ ...showAddBid, [item.id]: !showAddBid[item.id] })} style={{
                                                        padding: '3px 10px', borderRadius: 4, border: 'none',
                                                        background: showAddBid[item.id] ? UTM.gray100 : '#166534', color: showAddBid[item.id] ? UTM.gray700 : UTM.white,
                                                        fontSize: 10, fontWeight: 700, cursor: 'pointer',
                                                    }}>
                                                        {showAddBid[item.id] ? 'Batal' : '+ Bidaan'}
                                                    </button>
                                                </div>

                                                {showAddBid[item.id] && <AddBidForm sale={sale} item={item} onDone={() => setShowAddBid({ ...showAddBid, [item.id]: false })} />}

                                                {(!item.sale_bids || item.sale_bids.length === 0) && !showAddBid[item.id] && (
                                                    <p style={{ fontSize: 11, color: UTM.gray200, fontStyle: 'italic' }}>Tiada bidaan lagi.</p>
                                                )}

                                                {item.sale_bids?.map((bid) => (
                                                    <div key={bid.id}>
                                                        {editingBid === bid.id ? (
                                                            <EditBidForm sale={sale} item={item} bid={bid} onDone={() => setEditingBid(null)} />
                                                        ) : (
                                                            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', padding: '4px 0', gap: 8 }}>
                                                                <div style={{ display: 'flex', gap: 16, fontSize: 11, color: UTM.gray600, flex: 1, flexWrap: 'wrap' }}>
                                                                    <span style={{ fontWeight: 600 }}>{bid.bidder_name}</span>
                                                                    <span style={{ fontWeight: 700, color: UTM.gray700 }}>RM {Number(bid.bid_amount).toLocaleString()}</span>
                                                                    {bid.bid_date && <span style={{ color: UTM.gray500 }}>{new Date(bid.bid_date).toLocaleDateString('ms-MY')}</span>}
                                                                    {bid.deposit_paid && <span style={{ color: '#166534', fontWeight: 600 }}>✓ Deposit</span>}
                                                                    {bid.is_winner && <span style={{ color: '#92400E', fontWeight: 700 }}>★ Pemenang</span>}
                                                                    <span style={{
                                                                        display: 'inline-block', padding: '1px 6px', borderRadius: 999, fontSize: 9, fontWeight: 600,
                                                                        background: bid.status === 'accepted' || bid.status === 'paid' || bid.status === 'completed' ? '#DCFCE7' : bid.status === 'rejected' ? '#FEE2E2' : '#FEF3C7',
                                                                        color: bid.status === 'accepted' || bid.status === 'paid' || bid.status === 'completed' ? '#166534' : bid.status === 'rejected' ? '#991B1B' : '#92400E',
                                                                    }}>{bid.status}</span>
                                                                </div>
                                                                <div style={{ display: 'flex', gap: 4, flexShrink: 0 }}>
                                                                    <button onClick={() => setEditingBid(bid.id)} style={btnStyle('#F5F3FF', '#5B21B6')}>Edit</button>
                                                                    <button onClick={() => handleDeleteBid(bid)} style={btnStyle('#FEF2F2', '#DC2626')}>Padam</button>
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                ))}
                                            </div>
                                        </>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* ── KEW.PA Form Links ── */}
                    <div style={cardStyle}>
                        <h3 style={sectionTitleStyle}>KEW.PA Forms</h3>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                            {[
                                { label: 'PA-21 Tawaran Jualan', route: 'disposal-sales.kewpa21' },
                                { label: 'PA-22 Sebutharga Jualan', route: 'disposal-sales.kewpa22' },
                                { label: 'PA-23 Lelongan Jualan', route: 'disposal-sales.kewpa23' },
                                { label: 'PA-24 Keputusan', route: 'disposal-sales.kewpa24' },
                                { label: 'PA-25 Laporan', route: 'disposal-sales.kewpa25' },
                                { label: 'PA-26 Perakuan (T/S/L)', route: 'disposal-sales.kewpa26' },
                                { label: 'PA-27 Perakuan (Pelupusan)', route: 'disposal-sales.kewpa27' },
                                { label: 'PA-27A Perakuan (Lupus)', route: 'disposal-sales.kewpa27a' },
                            ].map((form) => (
                                <Link key={form.route} href={route(form.route, sale.id)} style={{
                                    display: 'inline-block', padding: '7px 16px', borderRadius: 6, fontSize: 12, fontWeight: 600,
                                    background: UTM.maroon, color: UTM.white, textDecoration: 'none',
                                }}>{form.label}</Link>
                            ))}
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
