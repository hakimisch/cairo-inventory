import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

const UTM = {
    maroon : '#5C001F',
    maroon2: '#7A0029',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray300: '#C5BFB8',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

const inputStyle = {
    width        : '100%',
    padding      : '9px 12px',
    borderRadius : 8,
    border       : `1.5px solid ${UTM.gray100}`,
    fontSize     : '13px',
    color        : UTM.gray900,
    background   : UTM.white,
    outline      : 'none',
    boxSizing    : 'border-box',
    transition   : 'border-color 0.12s',
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

const selectStyle = { ...inputStyle, cursor: 'pointer' };
const textareaStyle = { ...inputStyle, resize: 'vertical', minHeight: 70 };

function Field({ label, error, children }) {
    return (
        <div>
            <label style={labelStyle}>{label}</label>
            {children}
            {error && <p style={{ fontSize: '11px', color: '#DC2626', marginTop: 3 }}>{error}</p>}
        </div>
    );
}

function SectionCard({ title, icon, children, defaultOpen = true }) {
    return (
        <div style={{
            background   : UTM.white,
            borderRadius : 12,
            border       : `1px solid ${UTM.gray100}`,
            overflow     : 'hidden',
            marginBottom : 20,
        }}>
            <div style={{
                padding      : '16px 20px',
                borderBottom : `1px solid ${UTM.gray100}`,
                background   : UTM.gray50,
                display      : 'flex',
                alignItems   : 'center',
                gap          : 10,
            }}>
                <span style={{ fontSize: 16 }}>{icon}</span>
                <h3 style={{ fontSize: '14px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>{title}</h3>
            </div>
            <div style={{ padding: '20px' }}>
                {children}
            </div>
        </div>
    );
}

export default function Edit({ asset }) {
    const { data, setData, put, processing, errors } = useForm({
        asset_tag:            asset.asset_tag ?? '',
        name:                 asset.name ?? '',
        category:             asset.category ?? '',
        sub_category:         asset.sub_category ?? '',
        asset_type:           asset.asset_type ?? 'fixed_asset',
        campus:               asset.campus ?? 'utm_kl',
        purchase_price:       asset.purchase_price ?? '',
        location:             asset.location ?? '',
        status:               asset.status ?? 'active',
        quantity:             asset.quantity ?? 1,
        unit_of_measure:      asset.unit_of_measure ?? 'Unit',
        national_code:        asset.national_code ?? '',
        supplier_name:        asset.supplier_name ?? '',
        supplier_address:     asset.supplier_address ?? '',
        po_reference:         asset.po_reference ?? '',
        do_reference:         asset.do_reference ?? '',
        warranty_period:      asset.warranty_period ?? '',
        warranty_expiry:      asset.warranty_expiry ? asset.warranty_expiry.split('T')[0] : '',
        received_date:        asset.received_date ? asset.received_date.split('T')[0] : '',
        rejection_reason:     asset.rejection_reason ?? '',
        receiver_name:        asset.receiver_name ?? '',
        custodian_name:       asset.custodian_name ?? '',
        model:                asset.model ?? '',
        brand:                asset.brand ?? '',
        serial_number:        asset.serial_number ?? '',
        requires_maintenance: asset.requires_maintenance ?? false,
        saga_id:              asset.saga_id ?? '',
        voucher_no:           asset.voucher_no ?? '',
        budget_vot:           asset.budget_vot ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('assets.update', asset.id), {
            preserveScroll: true,
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                    <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                        Edit Aset — {asset.asset_tag}
                    </h2>
                </div>
            }
        >
            <Head title={`Edit Aset - ${asset.asset_tag}`} />

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 960, margin: '0 auto' }}>

                    {/* ── Back link ── */}
                    <Link
                        href={route('assets.index')}
                        style={{
                            display       : 'inline-flex',
                            alignItems    : 'center',
                            gap           : 6,
                            fontSize      : '13px',
                            fontWeight    : 600,
                            color         : UTM.gray500,
                            textDecoration: 'none',
                            marginBottom  : 20,
                        }}
                    >
                        ← Kembali ke Senarai Aset
                    </Link>

                    <form onSubmit={handleSubmit}>

                        {/* ── Maklumat Asas ── */}
                        <SectionCard title="Maklumat Asas" icon="📦">
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16 }}>
                                <Field label="Tag Aset" error={errors.asset_tag}>
                                    <input type="text" style={inputStyle} value={data.asset_tag}
                                        onChange={e => setData('asset_tag', e.target.value)} required />
                                </Field>
                                <Field label="Nama Aset" error={errors.name}>
                                    <input type="text" style={inputStyle} value={data.name}
                                        onChange={e => setData('name', e.target.value)} required />
                                </Field>
                                <Field label="Jenis Aset" error={errors.asset_type}>
                                    <select style={selectStyle} value={data.asset_type}
                                        onChange={e => setData('asset_type', e.target.value)}>
                                        <option value="fixed_asset">Aset Tetap</option>
                                        <option value="inventory">Inventori</option>
                                    </select>
                                </Field>
                                <Field label="Kategori" error={errors.category}>
                                    <input type="text" style={inputStyle} value={data.category}
                                        onChange={e => setData('category', e.target.value)} required />
                                </Field>
                                <Field label="Sub Kategori" error={errors.sub_category}>
                                    <input type="text" style={inputStyle} value={data.sub_category}
                                        onChange={e => setData('sub_category', e.target.value)} />
                                </Field>
                                <Field label="Kampus" error={errors.campus}>
                                    <select style={selectStyle} value={data.campus}
                                        onChange={e => setData('campus', e.target.value)}>
                                        <option value="utm_kl">UTM KL</option>
                                        <option value="utm_jb">UTM JB</option>
                                        <option value="other">Lain-lain</option>
                                    </select>
                                </Field>
                                <Field label="Lokasi" error={errors.location}>
                                    <input type="text" style={inputStyle} value={data.location}
                                        onChange={e => setData('location', e.target.value)} required />
                                </Field>
                                <Field label="Status" error={errors.status}>
                                    <select style={selectStyle} value={data.status}
                                        onChange={e => setData('status', e.target.value)}>
                                        <option value="active">Aktif</option>
                                        <option value="repair">Selenggara</option>
                                        <option value="disposed">Dilupus</option>
                                    </select>
                                </Field>
                                <Field label="Pegawai Bertanggungjawab" error={errors.custodian_name}>
                                    <input type="text" style={inputStyle} value={data.custodian_name}
                                        onChange={e => setData('custodian_name', e.target.value)} />
                                </Field>
                            </div>
                        </SectionCard>

                        {/* ── Kewangan & Pembelian ── */}
                        <SectionCard title="Kewangan & Pembelian" icon="💰">
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16 }}>
                                <Field label="Harga Perolehan (RM)" error={errors.purchase_price}>
                                    <input type="number" step="0.01" min="0" style={inputStyle}
                                        value={data.purchase_price}
                                        onChange={e => setData('purchase_price', e.target.value)} required />
                                </Field>
                                <Field label="SAGA ID" error={errors.saga_id}>
                                    <input type="text" style={inputStyle} value={data.saga_id}
                                        onChange={e => setData('saga_id', e.target.value)} />
                                </Field>
                                <Field label="Vot Bajet" error={errors.budget_vot}>
                                    <input type="text" style={inputStyle} value={data.budget_vot}
                                        onChange={e => setData('budget_vot', e.target.value)} />
                                </Field>
                                <Field label="No. Baucer Bayaran" error={errors.voucher_no}>
                                    <input type="text" style={inputStyle} value={data.voucher_no}
                                        onChange={e => setData('voucher_no', e.target.value)} />
                                </Field>
                                <Field label="No. Pesanan (PO)" error={errors.po_reference}>
                                    <input type="text" style={inputStyle} value={data.po_reference}
                                        onChange={e => setData('po_reference', e.target.value)} />
                                </Field>
                                <Field label="No. D/O" error={errors.do_reference}>
                                    <input type="text" style={inputStyle} value={data.do_reference}
                                        onChange={e => setData('do_reference', e.target.value)} />
                                </Field>
                                <Field label="Tarikh Diterima" error={errors.received_date}>
                                    <input type="date" style={inputStyle} value={data.received_date}
                                        onChange={e => setData('received_date', e.target.value)} />
                                </Field>
                                <Field label="No. Kod Kebangsaan" error={errors.national_code}>
                                    <input type="text" style={inputStyle} value={data.national_code}
                                        onChange={e => setData('national_code', e.target.value)} />
                                </Field>
                                <Field label="Kuantiti" error={errors.quantity}>
                                    <input type="number" min="1" style={inputStyle} value={data.quantity}
                                        onChange={e => setData('quantity', e.target.value)} />
                                </Field>
                                <Field label="Unit Pengukuran" error={errors.unit_of_measure}>
                                    <input type="text" style={inputStyle} value={data.unit_of_measure}
                                        onChange={e => setData('unit_of_measure', e.target.value)} />
                                </Field>
                            </div>
                        </SectionCard>

                        {/* ── Spesifikasi Teknikal ── */}
                        <SectionCard title="Spesifikasi Teknikal" icon="⚙️">
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 16 }}>
                                <Field label="Jenama" error={errors.brand}>
                                    <input type="text" style={inputStyle} value={data.brand}
                                        onChange={e => setData('brand', e.target.value)} />
                                </Field>
                                <Field label="Model" error={errors.model}>
                                    <input type="text" style={inputStyle} value={data.model}
                                        onChange={e => setData('model', e.target.value)} />
                                </Field>
                                <Field label="No. Siri" error={errors.serial_number}>
                                    <input type="text" style={inputStyle} value={data.serial_number}
                                        onChange={e => setData('serial_number', e.target.value)} />
                                </Field>
                            </div>
                        </SectionCard>

                        {/* ── Pembekal & Waranti ── */}
                        <SectionCard title="Pembekal & Waranti" icon="🛡️">
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="Nama Pembekal" error={errors.supplier_name}>
                                    <input type="text" style={inputStyle} value={data.supplier_name}
                                        onChange={e => setData('supplier_name', e.target.value)} />
                                </Field>
                                <Field label="Tempoh Waranti" error={errors.warranty_period}>
                                    <input type="text" style={inputStyle} value={data.warranty_period}
                                        onChange={e => setData('warranty_period', e.target.value)} />
                                </Field>
                                <Field label="Alamat Pembekal" error={errors.supplier_address}>
                                    <textarea style={textareaStyle} value={data.supplier_address}
                                        onChange={e => setData('supplier_address', e.target.value)} />
                                </Field>
                                <Field label="Tarikh Tamat Waranti" error={errors.warranty_expiry}>
                                    <input type="date" style={inputStyle} value={data.warranty_expiry}
                                        onChange={e => setData('warranty_expiry', e.target.value)} />
                                </Field>
                            </div>
                            <div style={{ marginTop: 16 }}>
                                <Field label="Penyelenggaraan Berkala" error={errors.requires_maintenance}>
                                    <label style={{ display: 'flex', alignItems: 'center', gap: 8, cursor: 'pointer' }}>
                                        <input type="checkbox"
                                            checked={data.requires_maintenance}
                                            onChange={e => setData('requires_maintenance', e.target.checked)}
                                            style={{ width: 16, height: 16, cursor: 'pointer' }} />
                                        <span style={{ fontSize: '13px', color: UTM.gray700 }}>
                                            Aset ini memerlukan penyelenggaraan berkala
                                        </span>
                                    </label>
                                </Field>
                            </div>
                        </SectionCard>

                        {/* ── Penerima & Catatan ── */}
                        <SectionCard title="Penerima & Catatan" icon="📝">
                            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 16 }}>
                                <Field label="Nama Penerima" error={errors.receiver_name}>
                                    <input type="text" style={inputStyle} value={data.receiver_name}
                                        onChange={e => setData('receiver_name', e.target.value)} />
                                </Field>
                                <Field label="Sebab Penolakan (Jika Ada)" error={errors.rejection_reason}>
                                    <textarea style={textareaStyle} value={data.rejection_reason}
                                        onChange={e => setData('rejection_reason', e.target.value)} />
                                </Field>
                            </div>
                        </SectionCard>

                        {/* ── Action buttons ── */}
                        <div style={{
                            display        : 'flex',
                            justifyContent : 'flex-end',
                            gap            : 12,
                            paddingTop     : 8,
                            paddingBottom  : 40,
                        }}>
                            <Link
                                href={route('assets.index')}
                                style={{
                                    padding      : '10px 24px',
                                    borderRadius : 8,
                                    border       : `1.5px solid ${UTM.gray100}`,
                                    background   : UTM.white,
                                    color        : UTM.gray700,
                                    fontSize     : '13px',
                                    fontWeight   : 600,
                                    textDecoration: 'none',
                                }}
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                style={{
                                    padding    : '10px 32px',
                                    borderRadius: 8,
                                    border     : 'none',
                                    background : UTM.maroon,
                                    color      : UTM.white,
                                    fontSize   : '13px',
                                    fontWeight : 700,
                                    cursor     : processing ? 'not-allowed' : 'pointer',
                                    opacity    : processing ? 0.7 : 1,
                                }}
                            >
                                {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
