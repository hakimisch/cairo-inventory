import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray200: '#D6D0C8',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

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

const selectStyle = { ...inputStyle, cursor: 'pointer' };

const labelStyle = {
    display      : 'block',
    fontSize     : '11px',
    fontWeight   : 700,
    textTransform: 'uppercase',
    letterSpacing: '0.07em',
    color        : UTM.gray500,
    marginBottom : 5,
};

// ── Helpers ───────────────────────────────────────────────────────────────────

const ROLE_OPTIONS = [
    { value: 'chairman',  label: 'Pengerusi' },
    { value: 'member',    label: 'Ahli' },
    { value: 'secretary', label: 'Setiausaha' },
];

const TYPE_OPTIONS = [
    { value: 'App\\Models\\AssetDisposal',     label: 'Pelupusan Aset (KEW.PA-15)' },
    { value: 'App\\Models\\AssetLossReport',   label: 'Laporan Kehilangan (KEW.PA-29)' },
];

function TypeBadge({ type }) {
    const typeLabel = TYPE_OPTIONS.find(t => t.value === type)?.label?.split(' (')[0] ?? type?.split('\\').pop() ?? '—';
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '2px 10px',
            borderRadius : 999,
            fontSize     : '11px',
            fontWeight   : 700,
            background   : '#EDE9FE',
            color        : '#5B21B6',
        }}>
            {typeLabel}
        </span>
    );
}

function StatusBadge({ validFrom, validUntil }) {
    const today = new Date();
    const from = validFrom ? new Date(validFrom) : null;
    const until = validUntil ? new Date(validUntil) : null;

    let active = true;
    if (from && from > today) active = false;
    if (until && until < today) active = false;

    if (active) {
        return (
            <span style={{
                display      : 'inline-block',
                padding      : '2px 10px',
                borderRadius : 999,
                fontSize     : '11px',
                fontWeight   : 700,
                background   : '#D1FAE5',
                color        : '#065F46',
            }}>
                Aktif
            </span>
        );
    }
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '2px 10px',
            borderRadius : 999,
            fontSize     : '11px',
            fontWeight   : 700,
            background   : '#FEE2E2',
            color        : '#991B1B',
        }}>
            Tamat/Lupus
        </span>
    );
}

function RoleBadge({ role }) {
    const map = {
        chairman  : { bg: '#DBEAFE', color: '#1E40AF', label: 'Pengerusi' },
        secretary : { bg: '#FEF3C7', color: '#92400E', label: 'Setiausaha' },
        member    : { bg: '#E6F4EC', color: '#065F46', label: 'Ahli' },
    };
    const s = map[role] || { bg: UTM.gray100, color: UTM.gray700, label: role };
    return (
        <span style={{
            display      : 'inline-block',
            padding      : '2px 10px',
            borderRadius : 999,
            fontSize     : '11px',
            fontWeight   : 700,
            background   : s.bg,
            color        : s.color,
        }}>
            {s.label}
        </span>
    );
}

// ── Inline Field Helper ───────────────────────────────────────────────────────

function InlineField({ label, editValue, onChange, type = 'text', options, step }) {
    return (
        <div>
            <label style={{ fontSize: 10, fontWeight: 700, color: UTM.gray500, textTransform: 'uppercase', marginBottom: 2, display: 'block' }}>{label}</label>
            {options ? (
                <select style={selectStyle} value={editValue} onChange={e => onChange(e.target.value)}>
                    {options.map(o => <option key={o.value ?? o} value={o.value ?? o}>{o.label ?? o}</option>)}
                </select>
            ) : (
                <input type={type} step={step} style={inputStyle} value={editValue} onChange={e => onChange(e.target.value)} />
            )}
        </div>
    );
}

// ── Add Appointment Form ──────────────────────────────────────────────────────

function AddAppointmentForm({ users, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        user_id: '',
        role: '',
        appointable_type: '',
        appointable_id: '',
        valid_from: new Date().toISOString().split('T')[0],
        valid_until: '',
        notes: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('committee-appointments.store'), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#FFFBEB', borderRadius: 8, border: `1px solid #FDE68A`, marginBottom: 16 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: '#92400E', marginBottom: 12 }}>+ Pelantikan Jawatankuasa Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <InlineField label="Ahli Jawatankuasa" editValue={data.user_id} onChange={v => setData('user_id', v)}
                    options={[{ value: '', label: '— Pilih Ahli —' }, ...users.map(u => ({ value: String(u.id), label: `${u.name} (${u.email})` }))]} />
                <InlineField label="Peranan" editValue={data.role} onChange={v => setData('role', v)}
                    options={[{ value: '', label: '— Pilih Peranan —' }, ...ROLE_OPTIONS]} />
                <InlineField label="Jenis Rujukan" editValue={data.appointable_type} onChange={v => setData('appointable_type', v)}
                    options={[{ value: '', label: '— Pilih Jenis —' }, ...TYPE_OPTIONS]} />
                <InlineField label="ID Rujukan" type="number" editValue={data.appointable_id} onChange={v => setData('appointable_id', v)} />
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <InlineField label="Tarikh Mula" type="date" editValue={data.valid_from} onChange={v => setData('valid_from', v)} />
                <InlineField label="Tarikh Tamat" type="date" editValue={data.valid_until} onChange={v => setData('valid_until', v)} />
                <div>
                    <label style={{ fontSize: 10, fontWeight: 700, color: UTM.gray500, textTransform: 'uppercase', marginBottom: 2, display: 'block' }}>Catatan</label>
                    <input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan (pilihan)..." />
                </div>
            </div>
            {errors.user_id && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.user_id}</p>}
            {errors.valid_until && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.valid_until}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: '#92400E', color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Pelantikan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Edit Appointment Form ─────────────────────────────────────────────────────

function EditAppointmentForm({ record, users, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        user_id: String(record.user_id ?? ''),
        role: record.role ?? '',
        valid_from: record.valid_from ? record.valid_from.split('T')[0] : '',
        valid_until: record.valid_until ? record.valid_until.split('T')[0] : '',
        notes: record.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('committee-appointments.update', record.id), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#FEF3C7', borderRadius: 6, border: `1px solid #FDE68A`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#92400E', marginBottom: 8 }}>Edit Pelantikan Jawatankuasa</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <InlineField label="Ahli Jawatankuasa" editValue={data.user_id} onChange={v => setData('user_id', v)}
                    options={users.map(u => ({ value: String(u.id), label: `${u.name} (${u.email})` }))} />
                <InlineField label="Peranan" editValue={data.role} onChange={v => setData('role', v)}
                    options={ROLE_OPTIONS} />
                <InlineField label="Tarikh Mula" type="date" editValue={data.valid_from} onChange={v => setData('valid_from', v)} />
                <InlineField label="Tarikh Tamat" type="date" editValue={data.valid_until} onChange={v => setData('valid_until', v)} />
            </div>
            <div style={{ marginBottom: 8 }}>
                <label style={{ fontSize: 10, fontWeight: 700, color: UTM.gray500, textTransform: 'uppercase', marginBottom: 2, display: 'block' }}>Catatan</label>
                <input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan (pilihan)..." />
            </div>
            {errors.valid_until && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.valid_until}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#92400E', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Main Page ─────────────────────────────────────────────────────────────────

export default function Index({ appointments, filters, users }) {
    const [search, setSearch]       = useState(filters?.search || '');
    const [expandedId, setExpandedId] = useState(null);
    const [showAdd, setShowAdd]     = useState(false);
    const [editingId, setEditingId] = useState(null);

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('committee-appointments.index'),
            { search },
            { preserveState: true, replace: true }
        );
    };

    const handleReset = () => {
        setSearch('');
        router.get(route('committee-appointments.index'), {}, { preserveState: true, replace: true });
    };

    const handleDelete = (record) => {
        if (window.confirm(`Padam pelantikan jawatankuasa untuk ${record.user?.name || '—'}?`)) {
            router.delete(route('committee-appointments.destroy', record.id), { preserveScroll: true });
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

    const tdStyle = {
        padding    : '14px 20px',
        fontSize   : '13px',
        color      : UTM.gray700,
        borderBottom: `1px solid ${UTM.gray100}`,
    };

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '16px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                        <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                            Jawatankuasa Pelantikan — KEW.PA-15 / PA-29
                        </h2>
                    </div>
                    <button
                        onClick={() => { setShowAdd(!showAdd); setEditingId(null); }}
                        style={{
                            padding      : '9px 18px',
                            borderRadius : 8,
                            border       : 'none',
                            background   : UTM.maroon,
                            color        : UTM.white,
                            fontSize     : '13px',
                            fontWeight   : 700,
                            cursor       : 'pointer',
                            boxShadow    : '0 2px 6px rgba(92,0,31,0.2)',
                            whiteSpace   : 'nowrap',
                        }}
                    >
                        {showAdd ? '✕ Batal' : '+ Pelantikan Baru'}
                    </button>
                </div>
            }
        >
            <Head title="Jawatankuasa Pelantikan" />

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 1280, margin: '0 auto' }}>

                    {/* ── Search Bar ──────────────────────────────────────── */}
                    <form onSubmit={handleSearch} style={{
                        display       : 'flex',
                        gap           : 16,
                        alignItems    : 'flex-end',
                        marginBottom  : 20,
                        padding       : 20,
                        background    : UTM.white,
                        borderRadius  : 12,
                        border        : `1px solid ${UTM.gray100}`,
                        flexWrap      : 'wrap',
                    }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={labelStyle}>Carian</label>
                            <input
                                type="text"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                                placeholder="Cari ahli jawatankuasa atau peranan..."
                                style={inputStyle}
                            />
                        </div>
                        <button type="submit" style={{
                            padding      : '9px 24px',
                            borderRadius : 8,
                            border       : 'none',
                            background   : UTM.maroon,
                            color        : UTM.white,
                            fontSize     : '13px',
                            fontWeight   : 700,
                            cursor       : 'pointer',
                            whiteSpace   : 'nowrap',
                        }}>
                            Cari
                        </button>
                        <button type="button" onClick={handleReset} style={{
                            padding      : '9px 24px',
                            borderRadius : 8,
                            border       : `1.5px solid ${UTM.gray100}`,
                            background   : UTM.white,
                            color        : UTM.gray700,
                            fontSize     : '13px',
                            fontWeight   : 600,
                            cursor       : 'pointer',
                            whiteSpace   : 'nowrap',
                        }}>
                            Reset
                        </button>
                    </form>

                    {/* ── Inline Add Form ───────────────────────────────────── */}
                    {showAdd && (
                        <AddAppointmentForm
                            users={users ?? []}
                            onDone={() => setShowAdd(false)}
                        />
                    )}

                    {/* ── Table Card ───────────────────────────────────────── */}
                    <div style={{
                        background   : UTM.white,
                        borderRadius : 12,
                        boxShadow    : '0 1px 8px rgba(0,0,0,0.06)',
                        border       : `1px solid ${UTM.gray100}`,
                        overflow     : 'hidden',
                    }}>
                        <div style={{ overflowX: 'auto', width: '100%' }}>
                            <table style={{ width: '100%', minWidth: '800px', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr>
                                        <th style={thStyle}>Ahli Jawatankuasa</th>
                                        <th style={thStyle}>Peranan</th>
                                        <th style={thStyle}>Jenis Rujukan</th>
                                        <th style={thStyle}>Tempoh</th>
                                        <th style={thStyle}>Status</th>
                                        <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {appointments.data?.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500, padding: '40px 16px' }}>
                                                Tiada rekod pelantikan jawatankuasa.
                                            </td>
                                        </tr>
                                    ) : (
                                        appointments.data?.map((record, index) => (
                                            <React.Fragment key={record.id}>
                                                <tr style={{
                                                        background  : expandedId === record.id ? '#FFF5E8' : (index % 2 === 0 ? UTM.white : UTM.gray50),
                                                        transition  : 'background 0.12s',
                                                    }}
                                                    onMouseEnter={e => { if(expandedId !== record.id) e.currentTarget.style.background = '#FFF5E8' }}
                                                    onMouseLeave={e => { if(expandedId !== record.id) e.currentTarget.style.background = index % 2 === 0 ? UTM.white : UTM.gray50 }}
                                                >
                                                    <td style={{ padding: '14px 20px', fontSize: '13px', fontWeight: 600, color: UTM.gray900 }}>
                                                        <span>{record.user?.name ?? '—'}</span>
                                                        <span style={{ fontSize: 11, color: UTM.gray500, display: 'block', fontWeight: 400 }}>
                                                            {record.user?.email ?? ''}
                                                        </span>
                                                    </td>
                                                    <td style={tdStyle}><RoleBadge role={record.role} /></td>
                                                    <td style={tdStyle}>
                                                        <TypeBadge type={record.appointable_type} />
                                                        <span style={{ fontFamily: 'monospace', fontSize: 11, color: UTM.gray500, marginLeft: 6 }}>
                                                            #{record.appointable_id}
                                                        </span>
                                                    </td>
                                                    <td style={{ ...tdStyle, fontSize: '12px' }}>
                                                        {record.valid_from ?? '—'} → {record.valid_until ?? '—'}
                                                    </td>
                                                    <td style={tdStyle}>
                                                        <StatusBadge validFrom={record.valid_from} validUntil={record.valid_until} />
                                                    </td>
                                                    <td style={{ ...tdStyle, whiteSpace: 'nowrap', textAlign: 'right' }}>
                                                        <button
                                                            onClick={() => { setExpandedId(expandedId === record.id ? null : record.id); if (editingId === record.id) setEditingId(null); }}
                                                            style={{
                                                                display     : 'inline-block',
                                                                padding     : '5px 12px',
                                                                borderRadius: 6,
                                                                fontSize    : '11px',
                                                                fontWeight  : 600,
                                                                background  : expandedId === record.id ? UTM.gold : '#EDE9E4',
                                                                color       : expandedId === record.id ? UTM.maroon : UTM.gray700,
                                                                border      : 'none',
                                                                cursor      : 'pointer',
                                                                marginRight : 4,
                                                                transition  : 'all 0.12s',
                                                            }}
                                                        >
                                                            {expandedId === record.id ? 'Tutup' : 'Butiran'}
                                                        </button>
                                                        <button
                                                            onClick={() => { setEditingId(editingId === record.id ? null : record.id); setExpandedId(null); }}
                                                            style={{
                                                                display     : 'inline-block',
                                                                padding     : '5px 12px',
                                                                borderRadius: 6,
                                                                fontSize    : '11px',
                                                                fontWeight  : 600,
                                                                background  : editingId === record.id ? '#FDE68A' : '#E6F4EC',
                                                                color       : editingId === record.id ? '#92400E' : '#065F46',
                                                                border      : 'none',
                                                                cursor      : 'pointer',
                                                                marginRight : 4,
                                                                transition  : 'all 0.12s',
                                                            }}
                                                        >
                                                            {editingId === record.id ? 'Batal' : 'Edit'}
                                                        </button>
                                                        <button
                                                            onClick={() => handleDelete(record)}
                                                            style={{
                                                                display     : 'inline-block',
                                                                padding     : '5px 12px',
                                                                borderRadius: 6,
                                                                fontSize    : '11px',
                                                                fontWeight  : 600,
                                                                background  : '#FEE2E2',
                                                                color       : '#991B1B',
                                                                border      : 'none',
                                                                cursor      : 'pointer',
                                                                transition  : 'all 0.12s',
                                                            }}
                                                        >
                                                            Hapus
                                                        </button>
                                                    </td>
                                                </tr>

                                                {/* ── Edit Form Row ── */}
                                                {editingId === record.id && (
                                                    <tr style={{ background: '#FFFBEB' }}>
                                                        <td colSpan={6} style={{ padding: 0 }}>
                                                            <EditAppointmentForm
                                                                record={record}
                                                                users={users ?? []}
                                                                onDone={() => setEditingId(null)}
                                                            />
                                                        </td>
                                                    </tr>
                                                )}

                                                {/* ── Expandable Details Row ── */}
                                                {expandedId === record.id && editingId !== record.id && (
                                                    <tr style={{ background: '#FAFAFA', borderBottom: `2px solid ${UTM.gray100}` }}>
                                                        <td colSpan={6} style={{ padding: 0 }}>
                                                            <div style={{
                                                                padding: '20px 24px',
                                                                display: 'flex',
                                                                flexWrap: 'wrap',
                                                                gap: '24px',
                                                                borderLeft: `4px solid ${UTM.gold}`,
                                                            }}>
                                                                <div style={{ flex: '1 1 400px', display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(200px, 1fr))', gap: '16px' }}>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Ahli Jawatankuasa</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{record.user?.name ?? '—'}</div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500 }}>{record.user?.email ?? ''}</div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Peranan</div>
                                                                        <RoleBadge role={record.role} />
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Jenis Rujukan</div>
                                                                        <TypeBadge type={record.appointable_type} />
                                                                        <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray700, marginLeft: 6 }}>#{record.appointable_id}</span>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Status</div>
                                                                        <StatusBadge validFrom={record.valid_from} validUntil={record.valid_until} />
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Tempoh Sah</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>
                                                                            {record.valid_from ?? '—'} hingga {record.valid_until ?? '—'}
                                                                        </div>
                                                                    </div>
                                                                    <div>
                                                                        <div style={{ fontSize: '11px', color: UTM.gray500, marginBottom: 2 }}>Catatan</div>
                                                                        <div style={{ fontSize: '13px', fontWeight: 600, color: UTM.gray700 }}>{record.notes || '—'}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </React.Fragment>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* ── Pagination ───────────────────────────────────── */}
                        {appointments.links && (
                            <div style={{
                                display        : 'flex',
                                justifyContent : 'center',
                                gap            : 6,
                                padding        : '16px 24px',
                                borderTop      : `1px solid ${UTM.gray100}`,
                                flexWrap       : 'wrap',
                            }}>
                                {appointments.links.map((link, i) => {
                                    if (!link.url) return null;
                                    const isActive = link.active;
                                    return (
                                        <Link
                                            key={i}
                                            href={link.url}
                                            style={{
                                                padding      : '6px 12px',
                                                borderRadius : 6,
                                                fontSize     : '12px',
                                                fontWeight   : isActive ? 700 : 500,
                                                background   : isActive ? UTM.maroon : UTM.gray100,
                                                color        : isActive ? UTM.white : UTM.gray700,
                                                textDecoration: 'none',
                                                transition   : 'all 0.12s',
                                            }}
                                            preserveState
                                            preserveScroll
                                        >
                                            <span dangerouslySetInnerHTML={{ __html: link.label }} />
                                        </Link>
                                    );
                                })}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
