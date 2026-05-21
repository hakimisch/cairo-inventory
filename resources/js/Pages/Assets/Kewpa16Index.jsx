import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';

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

// ─── Shared inline style objects ──────────────────────────────────────────────
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

const thStyle = {
    padding      : '12px 20px',
    fontSize     : '11px',
    fontWeight   : 700,
    textTransform: 'uppercase',
    letterSpacing: '0.07em',
    color        : UTM.gray500,
    textAlign    : 'left',
    borderBottom : `1px solid ${UTM.gray100}`,
    whiteSpace   : 'nowrap',
};

const tdStyle = {
    padding      : '14px 20px',
    fontSize     : '13px',
    color        : UTM.gray900,
    borderBottom : `1px solid ${UTM.gray100}`,
};

// ─── Status Badge ─────────────────────────────────────────────────────────────
function StatusBadge({ status }) {
    const map = {
        draft    : { bg: '#FEF3D6', color: UTM.goldDark, border: '#F5D890', label: 'Draft' },
        submitted: { bg: '#E6F4EC', color: '#1A7A3C',    border: '#B2DFC2', label: 'Submitted' },
        approved : { bg: '#DCE8F5', color: '#1A4A7A',    border: '#B2C8DF', label: 'Approved' },
        rejected : { bg: '#F3E0E5', color: UTM.maroon,   border: '#E8C0CB', label: 'Rejected' },
    };
    const s = map[status] || map.draft;
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

// ─── Form field definitions ───────────────────────────────────────────────────
const FORM_FIELDS = [
    { name: 'plate_no',          label: 'No. Plat',              type: 'text',     col: 6 },
    { name: 'chassis_no',        label: 'No. Casis',            type: 'text',     col: 6 },
    { name: 'engine_no',         label: 'No. Enjin',             type: 'text',     col: 6 },
    { name: 'vehicle_brand',     label: 'Jenama',               type: 'text',     col: 6 },
    { name: 'vehicle_model',     label: 'Model',                type: 'text',     col: 6 },
    { name: 'vehicle_year',      label: 'Tahun',                type: 'number',   col: 6 },
    { name: 'engine_capacity',   label: 'Kapasiti Enjin',       type: 'text',     col: 6 },
    { name: 'fuel_type',         label: 'Jenis Bahan Api',      type: 'text',     col: 6 },
    { name: 'vehicle_color',     label: 'Warna Kenderaan',      type: 'text',     col: 6 },
    { name: 'road_tax_expiry',   label: 'Cukai Jalan Tamat',    type: 'date',     col: 6 },
    { name: 'assessment_date',   label: 'Tarikh Penilaian',     type: 'date',     col: 6 },
    { name: 'estimated_value',   label: 'Nilai Anggaran (RM)',  type: 'number',   col: 6 },
    { name: 'assessor_name',     label: 'Nama Penilai',         type: 'text',     col: 6 },
    { name: 'assessor_position', label: 'Jawatan Penilai',      type: 'text',     col: 6 },
    { name: 'status',            label: 'Status',               type: 'select',   col: 12,
      options: [
          { value: 'draft',     label: 'Draft' },
          { value: 'submitted', label: 'Submitted' },
          { value: 'approved',  label: 'Approved' },
          { value: 'rejected',  label: 'Rejected' },
      ]},
    { name: 'condition_report',  label: 'Laporan Kondisi',      type: 'textarea', col: 12 },
    { name: 'recommendation',    label: 'Cadangan',             type: 'textarea', col: 12 },
    { name: 'notes',             label: 'Nota',                 type: 'textarea', col: 12 },
];

// ─── Empty form state ─────────────────────────────────────────────────────────
const emptyForm = () => ({
    asset_id:         '',
    plate_no:         '',
    chassis_no:       '',
    engine_no:        '',
    vehicle_brand:    '',
    vehicle_model:    '',
    vehicle_year:     '',
    road_tax_expiry:  '',
    engine_capacity:  '',
    fuel_type:        '',
    vehicle_color:    '',
    condition_report: '',
    estimated_value:  '',
    assessment_date:  '',
    assessor_name:    '',
    assessor_position:'',
    recommendation:   '',
    status:           'draft',
    notes:            '',
});

// ─── Inline CRUD Form Modal ──────────────────────────────────────────────────
function CrudFormModal({ visible, editing, form, setForm, assets, onSave, onClose }) {
    if (!visible) return null;

    const handleChange = (e) => {
        const { name, value } = e.target;
        setForm(f => ({ ...f, [name]: value }));
    };

    return (
        <div style={{
            position        : 'fixed',
            top             : 0,
            left            : 0,
            right           : 0,
            bottom          : 0,
            background      : 'rgba(0,0,0,0.4)',
            zIndex          : 999,
            display         : 'flex',
            alignItems      : 'center',
            justifyContent  : 'center',
            padding         : 20,
        }} onClick={onClose}>
            <div style={{
                background   : UTM.white,
                borderRadius : 14,
                maxWidth     : 780,
                width        : '100%',
                maxHeight    : '90vh',
                overflowY    : 'auto',
                padding      : '28px 32px',
                boxShadow    : '0 12px 40px rgba(0,0,0,0.15)',
            }} onClick={e => e.stopPropagation()}>
                <h2 style={{ fontSize: 18, fontWeight: 800, color: UTM.maroon, margin: '0 0 4px' }}>
                    {editing ? 'Kemaskini Penilaian Pelupusan' : 'Tambah Penilaian Pelupusan'}
                </h2>
                <p style={{ fontSize: 12, color: UTM.gray500, margin: '0 0 20px' }}>
                    {editing ? `Untuk aset: ${editing.asset?.name || ''}` : 'Isi maklumat penilaian kenderaan'}
                </p>

                <div style={{ display: 'flex', flexWrap: 'wrap', gap: 16 }}>
                    {/* Asset selector — only show when creating */}
                    {!editing && (
                        <div style={{ flex: '0 0 100%' }}>
                            <label style={labelStyle}>Aset</label>
                            <select
                                name="asset_id"
                                value={form.asset_id}
                                onChange={handleChange}
                                style={inputStyle}
                            >
                                <option value="">— Pilih Aset —</option>
                                {assets.map(a => (
                                    <option key={a.id} value={a.id}>{a.name} ({a.asset_tag})</option>
                                ))}
                            </select>
                        </div>
                    )}

                    {FORM_FIELDS.map(field => {
                        const width = field.col === 12 ? '100%' : `calc(${field.col}/12 * 100% - 8px)`;
                        if (field.type === 'select') {
                            return (
                                <div key={field.name} style={{ flex: `0 0 ${width}`, minWidth: 0 }}>
                                    <label style={labelStyle}>{field.label}</label>
                                    <select
                                        name={field.name}
                                        value={form[field.name] || ''}
                                        onChange={handleChange}
                                        style={inputStyle}
                                    >
                                        {field.options.map(opt => (
                                            <option key={opt.value} value={opt.value}>{opt.label}</option>
                                        ))}
                                    </select>
                                </div>
                            );
                        }
                        if (field.type === 'textarea') {
                            return (
                                <div key={field.name} style={{ flex: `0 0 ${width}`, minWidth: 0 }}>
                                    <label style={labelStyle}>{field.label}</label>
                                    <textarea
                                        name={field.name}
                                        value={form[field.name] || ''}
                                        onChange={handleChange}
                                        rows={3}
                                        style={{ ...inputStyle, resize: 'vertical' }}
                                    />
                                </div>
                            );
                        }
                        return (
                            <div key={field.name} style={{ flex: `0 0 ${width}`, minWidth: 0 }}>
                                <label style={labelStyle}>{field.label}</label>
                                <input
                                    type={field.type}
                                    name={field.name}
                                    value={form[field.name] || ''}
                                    onChange={handleChange}
                                    style={inputStyle}
                                />
                            </div>
                        );
                    })}
                </div>

                {/* Action buttons */}
                <div style={{ display: 'flex', gap: 10, justifyContent: 'flex-end', marginTop: 24 }}>
                    <button
                        type="button"
                        onClick={onClose}
                        style={{
                            padding      : '9px 24px',
                            borderRadius : 8,
                            border       : `1.5px solid ${UTM.gray100}`,
                            background   : UTM.white,
                            color        : UTM.gray700,
                            fontSize     : '13px',
                            fontWeight   : 600,
                            cursor       : 'pointer',
                        }}
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        onClick={onSave}
                        disabled={!editing && !form.asset_id}
                        style={{
                            padding      : '9px 24px',
                            borderRadius : 8,
                            border       : 'none',
                            background   : (!editing && !form.asset_id) ? UTM.gray100 : UTM.maroon,
                            color        : (!editing && !form.asset_id) ? UTM.gray500 : UTM.white,
                            fontSize     : '13px',
                            fontWeight   : 700,
                            cursor       : (!editing && !form.asset_id) ? 'not-allowed' : 'pointer',
                        }}
                    >
                        {editing ? 'Simpan Perubahan' : 'Tambah'}
                    </button>
                </div>
            </div>
        </div>
    );
}

// ─── Main Page Component ──────────────────────────────────────────────────────
export default function Kewpa16Index({ records, filters, assets }) {
    const { flash } = usePage().props;

    // ── Search state ──────────────────────────────────────────────────────────
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');

    // ── CRUD modal state ──────────────────────────────────────────────────────
    const [showModal, setShowModal] = useState(false);
    const [editingRecord, setEditingRecord] = useState(null);
    const [form, setForm] = useState(emptyForm());
    const [saving, setSaving] = useState(false);
    const [deletingId, setDeletingId] = useState(null);

    // ── Flash message visibility ──────────────────────────────────────────────
    const [flashMsg, setFlashMsg] = useState(flash?.success || flash?.error || '');
    const [flashType, setFlashType] = useState(flash?.success ? 'success' : flash?.error ? 'error' : '');
    React.useEffect(() => {
        if (flash?.success || flash?.error) {
            setFlashMsg(flash.success || flash.error);
            setFlashType(flash.success ? 'success' : 'error');
            const t = setTimeout(() => { setFlashMsg(''); setFlashType(''); }, 4000);
            return () => clearTimeout(t);
        }
    }, [flash]);

    // ── Open modal for creating ────────────────────────────────────────────────
    const openCreate = () => {
        setEditingRecord(null);
        setForm(emptyForm());
        setShowModal(true);
    };

    // ── Open modal for editing ─────────────────────────────────────────────────
    const openEdit = (record) => {
        setEditingRecord(record);
        setForm({
            asset_id:         record.asset_id,
            plate_no:         record.plate_no || '',
            chassis_no:       record.chassis_no || '',
            engine_no:        record.engine_no || '',
            vehicle_brand:    record.vehicle_brand || '',
            vehicle_model:    record.vehicle_model || '',
            vehicle_year:     record.vehicle_year || '',
            road_tax_expiry:  record.road_tax_expiry || '',
            engine_capacity:  record.engine_capacity || '',
            fuel_type:        record.fuel_type || '',
            vehicle_color:    record.vehicle_color || '',
            condition_report: record.condition_report || '',
            estimated_value:  record.estimated_value || '',
            assessment_date:  record.assessment_date || '',
            assessor_name:    record.assessor_name || '',
            assessor_position:record.assessor_position || '',
            recommendation:   record.recommendation || '',
            status:           record.status || 'draft',
            notes:            record.notes || '',
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingRecord(null);
    };

    // ── Save (create or update) ────────────────────────────────────────────────
    const handleSave = () => {
        const assetId = editingRecord ? editingRecord.asset_id : form.asset_id;
        if (!assetId) return;

        setSaving(true);
        router.post(route('assets.vehicle-disposal.store', assetId), form, {
            preserveScroll: true,
            preserveState:  true,
            onSuccess: () => {
                closeModal();
                setSaving(false);
            },
            onError: () => {
                setSaving(false);
            },
        });
    };

    // ── Delete with confirmation ───────────────────────────────────────────────
    const handleDelete = (record) => {
        if (!window.confirm(`Padam penilaian pelupusan untuk "${record.asset?.name || record.plate_no}"? Tindakan ini tidak boleh dibatalkan.`)) return;

        setDeletingId(record.id);
        router.delete(route('assets.vehicle-disposal.destroy', record.asset_id), {
            preserveScroll: true,
            preserveState:  true,
            onFinish: () => setDeletingId(null),
        });
    };

    // ── Search / Filter ────────────────────────────────────────────────────────
    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('vehicle-disposals.index'), { search, status: statusFilter }, { preserveState: true, replace: true });
    };

    const handleReset = () => {
        setSearch('');
        setStatusFilter('');
        router.get(route('vehicle-disposals.index'), {}, { preserveState: true, replace: true });
    };

    // ── Render ─────────────────────────────────────────────────────────────────
    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-16 — Perakuan Pelupusan Kenderaan" />

            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>

                {/* ── Flash message ─────────────────────────────────────────── */}
                {flashMsg && (
                    <div style={{
                        padding      : '12px 18px',
                        borderRadius : 10,
                        marginBottom : 20,
                        fontSize     : '13px',
                        fontWeight   : 600,
                        background   : flashType === 'success' ? '#E6F4EC' : '#F3E0E5',
                        color        : flashType === 'success' ? '#1A7A3C' : UTM.maroon,
                        border       : `1px solid ${flashType === 'success' ? '#B2DFC2' : '#E8C0CB'}`,
                    }}>
                        {flashMsg}
                    </div>
                )}

                {/* ── Header ────────────────────────────────────────────────── */}
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: 28 }}>
                    <div>
                        <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>
                            KEW.PA-16 — Perakuan Pelupusan Kenderaan
                        </h1>
                        <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>
                            Senarai penilaian pelupusan kenderaan
                        </p>
                    </div>
                    <button
                        type="button"
                        onClick={openCreate}
                        style={{
                            padding      : '10px 22px',
                            borderRadius : 8,
                            border       : 'none',
                            background   : UTM.maroon,
                            color        : UTM.white,
                            fontSize     : '13px',
                            fontWeight   : 700,
                            cursor       : 'pointer',
                            whiteSpace   : 'nowrap',
                            display      : 'flex',
                            alignItems   : 'center',
                            gap          : 6,
                        }}
                    >
                        + Tambah Penilaian
                    </button>
                </div>

                {/* ── Search / Filter ────────────────────────────────────────── */}
                <form onSubmit={handleSearch} style={{
                    display      : 'flex',
                    gap          : 16,
                    alignItems   : 'flex-end',
                    marginBottom : 24,
                    padding      : 20,
                    background   : UTM.white,
                    borderRadius : 12,
                    border       : `1px solid ${UTM.gray100}`,
                    flexWrap     : 'wrap',
                }}>
                    <div style={{ flex: 1, minWidth: 200 }}>
                        <label style={labelStyle}>Carian</label>
                        <input
                            type="text"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Nama aset, tag, no. plat, penilai..."
                            style={inputStyle}
                        />
                    </div>
                    <div style={{ minWidth: 150 }}>
                        <label style={labelStyle}>Status</label>
                        <select
                            value={statusFilter}
                            onChange={e => setStatusFilter(e.target.value)}
                            style={inputStyle}
                        >
                            <option value="">Semua</option>
                            <option value="draft">Draft</option>
                            <option value="submitted">Submitted</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
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

                {/* ── Table ─────────────────────────────────────────────────── */}
                <div style={{
                    background   : UTM.white,
                    borderRadius : 12,
                    border       : `1px solid ${UTM.gray100}`,
                    overflow     : 'hidden',
                }}>
                    <div style={{ overflowX: 'auto', width: '100%' }}>
                        <table style={{ width: '100%', minWidth: '1150px', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ background: UTM.gray50 }}>
                                    <th style={thStyle}>Aset</th>
                                    <th style={thStyle}>Tag</th>
                                    <th style={thStyle}>No. Plat</th>
                                    <th style={thStyle}>Penilai</th>
                                    <th style={thStyle}>Nilai Anggaran</th>
                                    <th style={thStyle}>Status</th>
                                    <th style={thStyle}>Tarikh</th>
                                    <th style={{ ...thStyle, textAlign: 'center' }}>Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {records.data.length === 0 ? (
                                    <tr>
                                        <td colSpan={8} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500, padding: 40 }}>
                                            Tiada rekod penilaian pelupusan kenderaan.
                                            <br />
                                            <button
                                                type="button"
                                                onClick={openCreate}
                                                style={{
                                                    marginTop      : 12,
                                                    padding        : '8px 18px',
                                                    borderRadius   : 6,
                                                    border         : `1px solid ${UTM.maroon}`,
                                                    background     : UTM.white,
                                                    color          : UTM.maroon,
                                                    fontSize       : '12px',
                                                    fontWeight     : 600,
                                                    cursor         : 'pointer',
                                                }}
                                            >
                                                + Tambah Penilaian Baru
                                            </button>
                                        </td>
                                    </tr>
                                ) : records.data.map((record, idx) => (
                                    <tr key={record.id} style={{
                                        background: idx % 2 === 0 ? UTM.white : UTM.gray50,
                                        transition: 'background 0.12s',
                                    }}
                                        onMouseEnter={e => { e.currentTarget.style.background = '#FFF5E8'; }}
                                        onMouseLeave={e => { e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}
                                    >
                                        <td style={tdStyle}>
                                            <Link href={route('assets.vehicle-disposal.index', record.asset_id)}
                                                style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                {record.asset?.name || '—'}
                                            </Link>
                                        </td>
                                        <td style={tdStyle}>
                                            <span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>
                                                {record.asset?.asset_tag || '—'}
                                            </span>
                                        </td>
                                        <td style={tdStyle}>{record.plate_no || '—'}</td>
                                        <td style={tdStyle}>{record.assessor_name || '—'}</td>
                                        <td style={tdStyle}>
                                            {record.estimated_value
                                                ? `RM ${Number(record.estimated_value).toLocaleString('ms-MY')}`
                                                : '—'}
                                        </td>
                                        <td style={tdStyle}><StatusBadge status={record.status} /></td>
                                        <td style={{ ...tdStyle, whiteSpace: 'nowrap' }}>
                                            {record.created_at ? new Date(record.created_at).toLocaleDateString('ms-MY') : '—'}
                                        </td>
                                        <td style={{ ...tdStyle, whiteSpace: 'nowrap', textAlign: 'center' }}>
                                            {/* Borang link */}
                                            <Link
                                                href={route('assets.vehicle-disposal.kewpa16', record.asset_id)}
                                                style={{
                                                    display       : 'inline-block',
                                                    padding       : '5px 10px',
                                                    borderRadius  : 6,
                                                    fontSize      : '11px',
                                                    fontWeight    : 600,
                                                    background    : '#EDE9E4',
                                                    color         : UTM.gray700,
                                                    textDecoration: 'none',
                                                    marginRight   : 4,
                                                }}
                                                title="Borang Penilaian"
                                            >
                                                Borang
                                            </Link>
                                            {/* KEW.PA-16 PDF link */}
                                            <a
                                                href={route('assets.vehicle-disposal.kewpa16.download', record.asset_id)}
                                                style={{
                                                    display       : 'inline-block',
                                                    padding       : '5px 10px',
                                                    borderRadius  : 6,
                                                    fontSize      : '11px',
                                                    fontWeight    : 600,
                                                    background    : UTM.maroon,
                                                    color         : UTM.white,
                                                    textDecoration: 'none',
                                                    marginRight   : 4,
                                                }}
                                                title="Muat turun KEW.PA-16"
                                            >
                                                PDF
                                            </a>
                                            {/* Edit button */}
                                            <button
                                                type="button"
                                                onClick={() => openEdit(record)}
                                                style={{
                                                    display       : 'inline-block',
                                                    padding       : '5px 10px',
                                                    borderRadius  : 6,
                                                    fontSize      : '11px',
                                                    fontWeight    : 600,
                                                    border        : `1px solid ${UTM.gray100}`,
                                                    background    : UTM.white,
                                                    color         : UTM.gray700,
                                                    cursor        : 'pointer',
                                                    marginRight   : 4,
                                                    lineHeight    : '14px',
                                                }}
                                                title="Kemaskini"
                                            >
                                                Edit
                                            </button>
                                            {/* Delete button */}
                                            <button
                                                type="button"
                                                onClick={() => handleDelete(record)}
                                                disabled={deletingId === record.id}
                                                style={{
                                                    display       : 'inline-block',
                                                    padding       : '5px 10px',
                                                    borderRadius  : 6,
                                                    fontSize      : '11px',
                                                    fontWeight    : 600,
                                                    border        : 'none',
                                                    background    : '#F3E0E5',
                                                    color         : UTM.maroon,
                                                    cursor        : deletingId === record.id ? 'not-allowed' : 'pointer',
                                                    lineHeight    : '14px',
                                                    opacity       : deletingId === record.id ? 0.6 : 1,
                                                }}
                                                title="Padam"
                                            >
                                                {deletingId === record.id ? '...' : 'Padam'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                {/* ── Pagination ──────────────────────────────────────────────── */}
                {records.links && (
                    <div style={{
                        display       : 'flex',
                        justifyContent: 'center',
                        gap           : 6,
                        marginTop     : 24,
                        flexWrap      : 'wrap',
                    }}>
                        {records.links.map((link, i) => {
                            if (!link.url) return null;
                            const active = link.active;
                            return (
                                <Link
                                    key={i}
                                    href={link.url}
                                    preserveState
                                    replace
                                    style={{
                                        padding       : '8px 14px',
                                        borderRadius  : 8,
                                        border        : `1px solid ${active ? UTM.maroon : UTM.gray100}`,
                                        background    : active ? UTM.maroon : UTM.white,
                                        color         : active ? UTM.white : UTM.gray700,
                                        fontSize      : '12px',
                                        fontWeight    : 600,
                                        textDecoration: 'none',
                                        cursor        : 'pointer',
                                    }}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                />
                            );
                        })}
                    </div>
                )}
            </div>

            {/* ── CRUD Modal ──────────────────────────────────────────────────── */}
            <CrudFormModal
                visible={showModal}
                editing={editingRecord}
                form={form}
                setForm={setForm}
                assets={assets}
                onSave={handleSave}
                onClose={closeModal}
            />
        </AuthenticatedLayout>
    );
}
