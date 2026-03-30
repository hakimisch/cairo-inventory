import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
 
// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
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
 
export default function ReceivingIndex({ receivings }) {
    const [selectedItem, setSelectedItem] = useState(null);
 
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
    };
 
    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                        <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                            Pengurusan Penerimaan (KEW.PA-1)
                        </h2>
                    </div>
                    <Link
                        href={route('receivings.create')}
                        style={{
                            background    : UTM.maroon,
                            color         : UTM.white,
                            padding       : '9px 18px',
                            borderRadius  : 8,
                            fontSize      : '13px',
                            fontWeight    : 700,
                            textDecoration: 'none',
                            boxShadow     : '0 2px 6px rgba(92,0,31,0.2)',
                        }}
                    >
                        + Daftar Penerimaan Baru
                    </Link>
                </div>
            }
        >
            <Head title="Senarai Penerimaan" />
 
            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 1280, margin: '0 auto' }}>
 
                    <div style={{ background: UTM.white, borderRadius: 12,
                                  boxShadow: '0 1px 4px rgba(92,0,31,0.07)', overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr>
                                    <th style={thStyle}>No. Penerimaan</th>
                                    <th style={thStyle}>Pembekal</th>
                                    <th style={thStyle}>Item</th>
                                    <th style={thStyle}>Qty Dipesan</th>
                                    <th style={thStyle}>Qty Diterima</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {receivings.map((item, idx) => (
                                    <tr key={item.id}
                                        style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50 }}
                                        onMouseEnter={e => e.currentTarget.style.background = '#FFF5E8'}
                                        onMouseLeave={e => e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50}
                                    >
                                        <td style={{ padding: '14px 20px', fontFamily: 'monospace',
                                                     fontSize: '12px', fontWeight: 700, color: UTM.maroon }}>
                                            {item.receive_no}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>
                                            {item.supplier_name}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px',
                                                     fontWeight: 600, color: UTM.gray900 }}>
                                            {item.item_description}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px',
                                                     color: UTM.gray700, textAlign: 'center' }}>
                                            {item.quantity_ordered}
                                        </td>
                                        <td style={{ padding: '14px 20px', fontSize: '13px',
                                                     color: UTM.gray700, textAlign: 'center' }}>
                                            {item.quantity_received}
                                        </td>
                                        <td style={{ padding: '14px 20px' }}>
                                            <StatusBadge status={item.status} />
                                        </td>
                                        <td style={{ padding: '14px 20px', textAlign: 'right',
                                                     whiteSpace: 'nowrap' }}>
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
                                                        href={route('receivings.reject', item.id)} /* Make sure this route exists in web.php */
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
                                                        }}
                                                   >
                                                        Tolak
                                                    </Link>
                                                </>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                                {receivings.length === 0 && (
                                    <tr>
                                        <td colSpan={7} style={{ padding: '48px', textAlign: 'center',
                                                                  color: UTM.gray500, fontSize: '14px' }}>
                                            Tiada rekod penerimaan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
 
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
                        <form onSubmit={handleAcceptSubmit} style={{ padding: '24px' }}>
                            <p style={{ fontSize: '12px', color: UTM.gray500, marginBottom: 20 }}>
                                Lengkapkan butiran untuk menjana{' '}
                                <strong style={{ color: UTM.maroon }}>KEW.PA-3</strong> dan mendaftarkan aset.
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
                                        maxLength="255" // ROBUSTNESS: Prevent database overflow
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
                                        // ROBUSTNESS: Restrict to today or future dates only
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

                            {/* NEW: Asset Photo Upload */}
                            <div style={{ marginBottom: 20 }}>
                                <label style={labelStyle}>Gambar Aset (Pilihan)</label>
                                <div style={{
                                    border: `1.5px dashed ${UTM.gray300}`,
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
                                    *Gambar akan dipaparkan pada borang KEW.PA-3
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

                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10 }}>
                                <button
                                    type="button"
                                    onClick={() => {
                                        setSelectedItem(null);
                                        reset(); // ROBUSTNESS: Clear form state on cancel
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
                                        background  : processing ? UTM.gray300 : UTM.maroon,
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
 