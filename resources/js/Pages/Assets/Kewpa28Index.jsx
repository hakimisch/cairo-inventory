import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, useForm } from '@inertiajs/react';

const UTM = {
    maroon : '#5C001F', gold: '#F8A617', goldDark:'#C9840A', sand: '#FFF5AB',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
};

const inputStyle = { width: '100%', padding: '7px 10px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, fontSize: 12, color: UTM.gray700, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const labelStyle = { display: 'block', fontSize: 10, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 3 };

function StatusBadge({ status }) {
    const map = {
        under_investigation: { bg: '#FEF3D6', color: UTM.goldDark, label: 'Under Investigation' },
        committee_review:    { bg: '#DBEAFE', color: '#1E40AF', label: 'Committee Review' },
        approved:            { bg: '#E6F4EC', color: '#1A7A3C', label: 'Approved' },
        closed:              { bg: '#E5E7EB', color: '#4B5563', label: 'Closed' },
    };
    const s = map[status] || { bg: '#F3E0E5', color: UTM.maroon, label: status };
    return <span style={{ display: 'inline-block', padding: '2px 8px', borderRadius: 999, fontSize: 10, fontWeight: 700, background: s.bg, color: s.color, border: `1px solid ${s.bg}` }}>{s.label}</span>;
}

const lossMethodOptions = [
    { value: 'hilang', label: 'Hilang' },
    { value: 'curi',   label: 'Curi' },
    { value: 'musnah', label: 'Musnah' },
    { value: 'other',  label: 'Lain-lain' },
];

// ── Add Loss Report Form ──
function AddLossReportForm({ assets, onDone }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        asset_id: '',
        loss_date: new Date().toISOString().split('T')[0],
        loss_method: 'hilang',
        incident_location: '',
        last_officer: '',
        police_report_no: '',
        current_value: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!data.asset_id) return;
        post(route('assets.loss-reports.store', data.asset_id), {
            preserveScroll: true,
            onSuccess: () => { reset(); onDone(); },
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 16, background: '#FFF7ED', borderRadius: 8, border: `1px solid #FED7AA`, marginBottom: 16 }}>
            <p style={{ fontSize: 12, fontWeight: 700, color: '#C2410C', marginBottom: 12 }}>+ Kehilangan Baru</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Aset</label>
                    <select style={inputStyle} value={data.asset_id} onChange={e => setData('asset_id', e.target.value)} required>
                        <option value="">— Pilih Aset —</option>
                        {assets.map(a => <option key={a.id} value={a.id}>{a.asset_tag} — {a.name}</option>)}
                    </select>
                    {errors.asset_id && <p style={{ fontSize: 10, color: '#DC2626', marginTop: 2 }}>{errors.asset_id}</p>}
                </div>
                <div>
                    <label style={labelStyle}>Tarikh Kehilangan</label>
                    <input type="date" style={inputStyle} value={data.loss_date} onChange={e => setData('loss_date', e.target.value)} required />
                </div>
                <div>
                    <label style={labelStyle}>Kaedah</label>
                    <select style={inputStyle} value={data.loss_method} onChange={e => setData('loss_method', e.target.value)} required>
                        {lossMethodOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Lokasi Kejadian</label>
                    <input type="text" style={inputStyle} value={data.incident_location} onChange={e => setData('incident_location', e.target.value)} required placeholder="Lokasi..."/>
                </div>
                <div>
                    <label style={labelStyle}>Pegawai Terakhir</label>
                    <input type="text" style={inputStyle} value={data.last_officer} onChange={e => setData('last_officer', e.target.value)} placeholder="Nama pegawai..."/>
                </div>
                <div>
                    <label style={labelStyle}>No. Laporan Polis</label>
                    <input type="text" style={inputStyle} value={data.police_report_no} onChange={e => setData('police_report_no', e.target.value)} placeholder="No. laporan..."/>
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 10, marginBottom: 10 }}>
                <div>
                    <label style={labelStyle}>Nilai Semasa (RM)</label>
                    <input type="number" step="0.01" min="0" style={inputStyle} value={data.current_value} onChange={e => setData('current_value', e.target.value)} required placeholder="0.00"/>
                </div>
            </div>
            {errors.current_value && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.current_value}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: '#C2410C', color: UTM.white, fontSize: 12, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}>{processing ? 'Menyimpan...' : 'Tambah Kehilangan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

// ── Edit Loss Report Form ──
function EditLossReportForm({ record, onDone }) {
    const { data, setData, put, processing, errors } = useForm({
        loss_date: record.loss_date ? record.loss_date.split('T')[0] : '',
        loss_method: record.loss_method ?? 'hilang',
        incident_location: record.incident_location ?? '',
        last_officer: record.last_officer ?? '',
        police_report_no: record.police_report_no ?? '',
        current_value: record.current_value ?? '',
        action_type: record.action_type ?? '',
        write_off_value: record.write_off_value ?? '',
        surcharge_amount: record.surcharge_amount ?? '',
        approval_reference: record.approval_reference ?? '',
        investigation_summary: record.investigation_summary ?? '',
        status: record.status ?? 'under_investigation',
        notes: record.notes ?? '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('assets.loss-reports.update', [record.asset_id, record.id]), {
            preserveScroll: true,
            onSuccess: () => onDone(),
        });
    };

    return (
        <form onSubmit={handleSubmit} style={{ padding: 12, background: '#FFF7ED', borderRadius: 6, border: `1px solid #FED7AA`, marginBottom: 8 }}>
            <p style={{ fontSize: 11, fontWeight: 700, color: '#C2410C', marginBottom: 8 }}>Edit Laporan Kehilangan</p>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Tarikh Kehilangan</label><input type="date" style={inputStyle} value={data.loss_date} onChange={e => setData('loss_date', e.target.value)} required /></div>
                <div><label style={labelStyle}>Kaedah</label>
                    <select style={inputStyle} value={data.loss_method} onChange={e => setData('loss_method', e.target.value)} required>
                        {lossMethodOptions.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                </div>
                <div><label style={labelStyle}>Status</label>
                    <select style={inputStyle} value={data.status} onChange={e => setData('status', e.target.value)}>
                        <option value="under_investigation">Under Investigation</option>
                        <option value="committee_review">Committee Review</option>
                        <option value="approved">Approved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Lokasi Kejadian</label><input type="text" style={inputStyle} value={data.incident_location} onChange={e => setData('incident_location', e.target.value)} required /></div>
                <div><label style={labelStyle}>Pegawai Terakhir</label><input type="text" style={inputStyle} value={data.last_officer} onChange={e => setData('last_officer', e.target.value)} /></div>
                <div><label style={labelStyle}>No. Laporan Polis</label><input type="text" style={inputStyle} value={data.police_report_no} onChange={e => setData('police_report_no', e.target.value)} /></div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr 1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Nilai Semasa (RM)</label><input type="number" step="0.01" min="0" style={inputStyle} value={data.current_value} onChange={e => setData('current_value', e.target.value)} required /></div>
                <div><label style={labelStyle}>Rujukan Kelulusan</label><input type="text" style={inputStyle} value={data.approval_reference} onChange={e => setData('approval_reference', e.target.value)} /></div>
                <div><label style={labelStyle}>Tindakan</label>
                    <select style={inputStyle} value={data.action_type} onChange={e => setData('action_type', e.target.value)}>
                        <option value="">— Tiada —</option>
                        <option value="surcharge">Surcharge</option>
                        <option value="write_off">Write Off</option>
                    </select>
                </div>
                <div><label style={labelStyle}>Amaun (RM)</label>
                    <input type="number" step="0.01" min="0" style={inputStyle}
                        value={data.action_type === 'surcharge' ? data.surcharge_amount : data.action_type === 'write_off' ? data.write_off_value : ''}
                        onChange={e => {
                            const val = e.target.value;
                            if (data.action_type === 'surcharge') setData('surcharge_amount', val);
                            else if (data.action_type === 'write_off') setData('write_off_value', val);
                        }}
                        placeholder="0.00" />
                </div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Ringkasan Siasatan (PA-30)</label><textarea style={{ ...inputStyle, resize: 'vertical', minHeight: 40 }} value={data.investigation_summary} onChange={e => setData('investigation_summary', e.target.value)} placeholder="Ringkasan siasatan..." /></div>
            </div>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr', gap: 8, marginBottom: 8 }}>
                <div><label style={labelStyle}>Catatan</label><input type="text" style={inputStyle} value={data.notes} onChange={e => setData('notes', e.target.value)} placeholder="Catatan tambahan (pilihan)..." /></div>
            </div>
            {errors.loss_date && <p style={{ fontSize: 11, color: '#DC2626', marginBottom: 8 }}>{errors.loss_date}</p>}
            <div style={{ display: 'flex', gap: 8 }}>
                <button type="submit" disabled={processing} style={{ padding: '5px 14px', borderRadius: 6, border: 'none', background: '#C2410C', color: UTM.white, fontSize: 11, fontWeight: 700, cursor: processing ? 'not-allowed' : 'pointer' }}>{processing ? 'Menyimpan...' : 'Simpan'}</button>
                <button type="button" onClick={onDone} style={{ padding: '5px 12px', borderRadius: 6, border: `1px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray600, fontSize: 11, fontWeight: 600, cursor: 'pointer' }}>Batal</button>
            </div>
        </form>
    );
}

export default function Kewpa28Index({ records, filters, assets }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [statusFilter, setStatusFilter] = useState(filters?.status || '');
    const [showAdd, setShowAdd] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const handleSearch = (e) => { e.preventDefault(); router.get(route('loss-reports.index'), { search, status: statusFilter }, { preserveState: true, replace: true }); };
    const handleReset = () => { setSearch(''); setStatusFilter(''); router.get(route('loss-reports.index'), {}, { preserveState: true, replace: true }); };

    const handleDelete = (record) => {
        if (window.confirm(`Padam laporan kehilangan aset ${record.asset?.name} (${record.police_report_no || record.incident_location || record.loss_date})?`)) {
            router.delete(route('assets.loss-reports.destroy', [record.asset_id, record.id]), { preserveScroll: true });
        }
    };

    const thStyle = { padding: '10px 14px', fontSize: 11, fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, textAlign: 'left', borderBottom: `1px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const tdStyle = { padding: '12px 14px', fontSize: 13, color: UTM.gray900, borderBottom: `1px solid ${UTM.gray100}` };

    return (
        <AuthenticatedLayout>
            <Head title="KEW.PA-28→32 — Laporan Kehilangan Aset" />
            <style>{`
                @media (max-width: 767px) {
                    .resp-table thead { display: none; }
                    .resp-table tr {
                        display: block;
                        margin-bottom: 12px;
                        border: 1px solid #e5e7eb;
                        border-radius: 8px;
                        padding: 12px;
                        background: white;
                    }
                    .resp-table td {
                        display: block;
                        text-align: right;
                        padding: 6px 0;
                        border: none !important;
                    }
                    .resp-table td::before {
                        content: attr(data-label);
                        float: left;
                        font-weight: 700;
                        text-transform: uppercase;
                        font-size: 10px;
                        color: #8A8480;
                        letter-spacing: 0.07em;
                    }
                    .resp-table .actions-wrap {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 4px;
                        justify-content: flex-end;
                    }
                }
            `}</style>
            <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>
                <div style={{ marginBottom: 28 }}>
                    <h1 style={{ fontSize: 22, fontWeight: 800, color: UTM.maroon, margin: 0 }}>KEW.PA-28→32 — Laporan Kehilangan Aset</h1>
                    <p style={{ fontSize: 13, color: UTM.gray500, marginTop: 4 }}>Senarai laporan kehilangan aset</p>
                </div>

                {/* Toolbar */}
                <div style={{ display: 'flex', gap: 12, alignItems: 'flex-end', marginBottom: 16, flexWrap: 'wrap' }}>
                    <form onSubmit={handleSearch} style={{ display: 'flex', gap: 12, alignItems: 'flex-end', flexWrap: 'wrap', flex: 1 }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={labelStyle}>Carian</label>
                            <input type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="Nama aset, tag, no. laporan polis, lokasi..." style={inputStyle} />
                        </div>
                        <div style={{ minWidth: 160 }}>
                            <label style={labelStyle}>Status</label>
                            <select value={statusFilter} onChange={e => setStatusFilter(e.target.value)} style={inputStyle}>
                                <option value="">Semua</option>
                                <option value="under_investigation">Under Investigation</option>
                                <option value="committee_review">Committee Review</option>
                                <option value="approved">Approved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <button type="submit" style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: UTM.maroon, color: UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer' }}>Cari</button>
                        <button type="button" onClick={handleReset} style={{ padding: '7px 16px', borderRadius: 6, border: `1.5px solid ${UTM.gray100}`, background: UTM.white, color: UTM.gray700, fontSize: 12, fontWeight: 600, cursor: 'pointer' }}>Reset</button>
                    </form>
                    <Link
                        href={route('loss-reports.kewpa31')}
                        style={{ padding: '7px 14px', borderRadius: 6, border: `1.5px solid #1E40AF`, background: UTM.white, color: '#1E40AF', fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap', textDecoration: 'none', display: 'inline-flex', alignItems: 'center' }}
                    >
                        ▤ PA-31 Hapuskira
                    </Link>
                    <Link
                        href={route('loss-reports.kewpa32')}
                        style={{ padding: '7px 14px', borderRadius: 6, border: `1.5px solid #7B1FA2`, background: UTM.white, color: '#7B1FA2', fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap', textDecoration: 'none', display: 'inline-flex', alignItems: 'center' }}
                    >
                        ▤ PA-32 Tindakan
                    </Link>
                    <button onClick={() => setShowAdd(!showAdd)} style={{ padding: '7px 20px', borderRadius: 6, border: 'none', background: showAdd ? UTM.gray100 : '#C2410C', color: showAdd ? UTM.gray700 : UTM.white, fontSize: 12, fontWeight: 700, cursor: 'pointer', whiteSpace: 'nowrap' }}>
                        {showAdd ? 'Batal' : '+ Kehilangan Baru'}
                    </button>
                </div>

                {showAdd && <AddLossReportForm assets={assets} onDone={() => setShowAdd(false)} />}

                {/* Table */}
                <div style={{ background: UTM.white, borderRadius: 12, border: `1px solid ${UTM.gray100}`, overflow: 'hidden' }}>
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', minWidth: '1050px', borderCollapse: 'collapse' }} className="resp-table">
                            <thead><tr style={{ background: UTM.gray50 }}>
                                <th style={thStyle}>Aset</th>
                                <th style={thStyle}>Tag</th>
                                <th style={thStyle}>No. Laporan Polis</th>
                                <th style={thStyle}>Lokasi Kejadian</th>
                                <th style={thStyle}>Rujukan Kelulusan</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Tarikh</th>
                                <th style={thStyle}></th>
                            </tr></thead>
                            <tbody>
                                {records.data.length === 0 ? (
                                    <tr><td colSpan={8} style={{ ...tdStyle, textAlign: 'center', color: UTM.gray500 }}>Tiada rekod kehilangan aset.</td></tr>
                                ) : records.data.map((record, idx) => (
                                    editingId === record.id ? (
                                        <tr key={record.id}>
                                            <td colSpan={8} style={{ padding: 0, background: '#FAFAFA' }}>
                                                <EditLossReportForm record={record} onDone={() => setEditingId(null)} />
                                            </td>
                                        </tr>
                                    ) : (
                                        <tr key={record.id} style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50, transition: 'background 0.12s' }}
                                            onMouseEnter={e => { e.currentTarget.style.background = '#FFF5E8'; }}
                                            onMouseLeave={e => { e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}>
                                            <td style={tdStyle} data-label="Aset">
                                                <Link href={route('assets.kewpa3', record.asset_id)} style={{ color: UTM.maroon, fontWeight: 600, textDecoration: 'none' }}>
                                                    {record.asset?.name || '—'}
                                                </Link>
                                            </td>
                                            <td style={tdStyle} data-label="Tag"><span style={{ fontFamily: 'monospace', fontSize: 12, color: UTM.gray500 }}>{record.asset?.asset_tag || '—'}</span></td>
                                            <td style={tdStyle} data-label="No. Laporan Polis">{record.police_report_no || '—'}</td>
                                            <td style={tdStyle} data-label="Lokasi Kejadian">{record.incident_location || '—'}</td>
                                            <td style={tdStyle} data-label="Rujukan Kelulusan">{record.approval_reference || '—'}</td>
                                            <td style={tdStyle} data-label="Status"><StatusBadge status={record.status} /></td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }} data-label="Tarikh">{record.loss_date ? new Date(record.loss_date).toLocaleDateString('ms-MY') : (record.created_at ? new Date(record.created_at).toLocaleDateString('ms-MY') : '—')}</td>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap' }} data-label="Tindakan">
                                                <div className="actions-wrap" style={{ display: 'flex', gap: 4, flexWrap: 'wrap', justifyContent: 'flex-end' }}>
                                                <Link href={route('assets.kewpa2', record.asset_id)}
                                                    style={{ display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#EDE9E4', color: UTM.gray700, textDecoration: 'none', marginRight: 4 }}>
                                                    Borang
                                                </Link>
                                                <a href={route('assets.loss-reports.kewpa28.download', record.asset_id)}
                                                    style={{ display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: UTM.maroon, color: UTM.white, textDecoration: 'none', marginRight: 4 }}>
                                                    KEW.PA-28
                                                </a>
                                                <a href={route('loss-reports.kewpa29.download', record.id)}
                                                    style={{ display: 'inline-block', padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#1E40AF', color: UTM.white, textDecoration: 'none', marginRight: 4 }}>
                                                    PA-29 JK
                                                </a>
                                                <button onClick={() => setEditingId(record.id)} style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#F5F3FF', color: '#5B21B6', border: 'none', cursor: 'pointer', marginRight: 4 }}>Edit</button>
                                                <button onClick={() => handleDelete(record)} style={{ padding: '4px 10px', borderRadius: 4, fontSize: 10, fontWeight: 600, background: '#FEF2F2', color: '#DC2626', border: 'none', cursor: 'pointer' }}>Padam</button>
                                                </div>
                                            </td>
                                        </tr>
                                    )
                                ))}
                            </tbody>
                        </table>
                    </div>
                    <div style={{ padding: '10px 16px', borderTop: `1px solid ${UTM.gray100}`, background: UTM.gray50, fontSize: 12, color: UTM.gray500 }}>
                        Menunjukkan <strong style={{ color: UTM.maroon }}>{records.data.length}</strong> daripada <strong style={{ color: UTM.maroon }}>{records.total}</strong> rekod
                    </div>
                </div>

                {records.links && (
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 6, marginTop: 20, flexWrap: 'wrap' }}>
                        {records.links.map((link, i) => {
                            if (!link.url) return null;
                            return <Link key={i} href={link.url} preserveState replace
                                style={{ padding: '7px 12px', borderRadius: 6, border: `1px solid ${link.active ? UTM.maroon : UTM.gray100}`, background: link.active ? UTM.maroon : UTM.white, color: link.active ? UTM.white : UTM.gray700, fontSize: 12, fontWeight: 600, textDecoration: 'none' }}
                                dangerouslySetInnerHTML={{ __html: link.label }} />;
                        })}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
