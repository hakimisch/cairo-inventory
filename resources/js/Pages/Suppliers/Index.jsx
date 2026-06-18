import { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ suppliers, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '', registration_no: '', address: '', phone: '', email: '', contact_person: '', is_active: true,
    });

    function handleSearch(e) {
        const val = e.target.value;
        setSearch(val);
        router.get(route('suppliers.index'), { search: val }, { preserveState: true, replace: true });
    }

    function openCreate() {
        reset();
        setEditingId(null);
        setShowForm(true);
    }

    function openEdit(s) {
        setData({
            name: s.name, registration_no: s.registration_no || '', address: s.address || '',
            phone: s.phone || '', email: s.email || '', contact_person: s.contact_person || '',
            is_active: s.is_active,
        });
        setEditingId(s.id);
        setShowForm(true);
    }

    function handleSubmit(e) {
        e.preventDefault();
        if (editingId) {
            put(route('suppliers.update', editingId), {
                preserveScroll: true,
                onSuccess: () => { setShowForm(false); setEditingId(null); reset(); },
            });
        } else {
            post(route('suppliers.store'), {
                preserveScroll: true,
                onSuccess: () => { setShowForm(false); reset(); },
            });
        }
    }

    function handleDelete(id) {
        if (!confirm('Delete this supplier?')) return;
        router.delete(route('suppliers.destroy', id), { preserveScroll: true });
    }

    return (
        <AuthenticatedLayout>
            <Head title="Suppliers" />
            <div style={{ padding: 24, maxWidth: 1000, margin: '0 auto' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 20, flexWrap: 'wrap', gap: 12 }}>
                    <div>
                        <h1 style={{ fontSize: 22, fontWeight: 700, margin: 0 }}>Suppliers</h1>
                        <p style={{ color: '#6B7280', fontSize: 13, margin: '4px 0 0' }}>Manage your supplier records</p>
                    </div>
                    <button onClick={openCreate} style={{
                        padding: '10px 20px', backgroundColor: '#5C001F', color: '#fff',
                        border: 'none', borderRadius: 8, fontSize: 14, fontWeight: 600, cursor: 'pointer',
                    }}>
                        ＋ New Supplier
                    </button>
                </div>

                {/* ── Inline Form ──────────────────────────────────────────── */}
                {showForm && (
                    <form onSubmit={handleSubmit} style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', padding: 20, marginBottom: 20 }}>
                        <h2 style={{ fontSize: 14, fontWeight: 600, margin: '0 0 12px', color: '#374151' }}>
                            {editingId ? 'Edit Supplier' : 'New Supplier'}
                        </h2>
                        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: 12 }}>
                            <div>
                                <label style={labelStyle}>Name *</label>
                                <input value={data.name} onChange={e => setData('name', e.target.value)} style={inputStyle} />
                                {errors.name && <p style={errStyle}>{errors.name}</p>}
                            </div>
                            <div>
                                <label style={labelStyle}>Registration No.</label>
                                <input value={data.registration_no} onChange={e => setData('registration_no', e.target.value)}
                                    style={inputStyle} placeholder="e.g. 199901011819 (486719-K)" />
                            </div>
                            <div style={{ gridColumn: '1 / -1' }}>
                                <label style={labelStyle}>Address</label>
                                <textarea value={data.address} onChange={e => setData('address', e.target.value)}
                                    rows={2} style={{ ...inputStyle, resize: 'vertical' }} />
                            </div>
                            <div>
                                <label style={labelStyle}>Phone</label>
                                <input value={data.phone} onChange={e => setData('phone', e.target.value)} style={inputStyle} />
                            </div>
                            <div>
                                <label style={labelStyle}>Email</label>
                                <input type="email" value={data.email} onChange={e => setData('email', e.target.value)} style={inputStyle} />
                            </div>
                            <div>
                                <label style={labelStyle}>Contact Person</label>
                                <input value={data.contact_person} onChange={e => setData('contact_person', e.target.value)} style={inputStyle} />
                            </div>
                            <div>
                                <label style={labelStyle}>
                                    <input type="checkbox" checked={data.is_active} onChange={e => setData('is_active', e.target.checked)}
                                        style={{ marginRight: 6 }} />
                                    Active
                                </label>
                            </div>
                        </div>
                        <div style={{ marginTop: 12, display: 'flex', gap: 8 }}>
                            <button type="submit" disabled={processing} style={{
                                padding: '8px 20px', backgroundColor: '#5C001F', color: '#fff',
                                border: 'none', borderRadius: 6, fontSize: 13, fontWeight: 600, cursor: 'pointer',
                            }}>
                                {processing ? 'Saving…' : editingId ? 'Update' : 'Create'}
                            </button>
                            <button type="button" onClick={() => { setShowForm(false); setEditingId(null); reset(); }}
                                style={{
                                    padding: '8px 20px', backgroundColor: '#fff', color: '#374151',
                                    border: '1px solid #D1D5DB', borderRadius: 6, fontSize: 13, cursor: 'pointer',
                                }}>
                                Cancel
                            </button>
                        </div>
                    </form>
                )}

                {/* ── Search ───────────────────────────────────────────────── */}
                <input type="text" placeholder="Search suppliers…" value={search} onChange={handleSearch}
                    style={{
                        width: '100%', padding: '8px 14px', borderRadius: 8, border: '1px solid #D1D5DB',
                        fontSize: 13, marginBottom: 16, boxSizing: 'border-box',
                    }} />

                {/* ── Table ────────────────────────────────────────────────── */}
                <div style={{ backgroundColor: '#fff', borderRadius: 12, border: '1px solid #E5E7EB', overflow: 'hidden' }}>
                    <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: 13 }}>
                        <thead>
                            <tr style={{ backgroundColor: '#F9FAFB', borderBottom: '1px solid #E5E7EB' }}>
                                <th style={thStyle}>Name</th>
                                <th style={thStyle}>Registration No.</th>
                                <th style={thStyle}>Contact</th>
                                <th style={thStyle}>DOs</th>
                                <th style={thStyle}>Status</th>
                                <th style={thStyle}>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {suppliers.data.length === 0 && (
                                <tr><td colSpan={6} style={{ textAlign: 'center', padding: 40, color: '#9CA3AF' }}>No suppliers yet.</td></tr>
                            )}
                            {suppliers.data.map(s => (
                                <tr key={s.id} style={{ borderBottom: '1px solid #F3F4F6' }}>
                                    <td style={tdStyle}><strong>{s.name}</strong></td>
                                    <td style={{ ...tdStyle, fontSize: 12, fontFamily: 'monospace' }}>{s.registration_no || '—'}</td>
                                    <td style={tdStyle}>
                                        {s.phone && <div style={{ fontSize: 12 }}>{s.phone}</div>}
                                        {s.email && <div style={{ fontSize: 12, color: '#6B7280' }}>{s.email}</div>}
                                        {s.contact_person && <div style={{ fontSize: 12, color: '#6B7280' }}>{s.contact_person}</div>}
                                    </td>
                                    <td style={tdStyle}>{s.delivery_orders_count}</td>
                                    <td style={tdStyle}>
                                        <span style={{
                                            display: 'inline-block', padding: '2px 10px', borderRadius: 20,
                                            fontSize: 11, fontWeight: 600,
                                            color: s.is_active ? '#166534' : '#DC2626',
                                            backgroundColor: s.is_active ? '#F0FDF4' : '#FEF2F2',
                                        }}>
                                            {s.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td style={tdStyle}>
                                        <div style={{ display: 'flex', gap: 4 }}>
                                            <button onClick={() => openEdit(s)} style={actionBtnStyle}>Edit</button>
                                            <button onClick={() => handleDelete(s.id)} style={{ ...actionBtnStyle, color: '#DC2626' }}>Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                {/* ── Pagination ──────────────────────────────────────────── */}
                {suppliers.links && (
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 8, marginTop: 20 }}>
                        {suppliers.links.map((link, i) => (
                            <Link key={i} href={link.url || '#'}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                                style={{
                                    padding: '6px 12px', borderRadius: 6, fontSize: 13, textDecoration: 'none',
                                    backgroundColor: link.active ? '#5C001F' : '#fff',
                                    color: link.active ? '#fff' : '#374151',
                                    border: '1px solid #D1D5DB',
                                    pointerEvents: link.url ? 'auto' : 'none',
                                    opacity: link.url ? 1 : 0.4,
                                }} />
                        ))}
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

const thStyle = {
    padding: '10px 14px', textAlign: 'left', fontWeight: 600,
    fontSize: 11, color: '#6B7280', textTransform: 'uppercase', letterSpacing: '0.05em',
};
const tdStyle = { padding: '10px 14px', verticalAlign: 'middle' };
const labelStyle = { display: 'block', fontSize: 12, fontWeight: 600, color: '#374151', marginBottom: 4 };
const inputStyle = { width: '100%', padding: '8px 12px', borderRadius: 6, border: '1px solid #D1D5DB', fontSize: 13, boxSizing: 'border-box' };
const errStyle = { color: '#DC2626', fontSize: 11, margin: '4px 0 0' };
const actionBtnStyle = {
    padding: '4px 10px', borderRadius: 4, fontSize: 11, fontWeight: 600,
    border: 'none', cursor: 'pointer', backgroundColor: '#F3F4F6', color: '#374151',
};
