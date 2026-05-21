import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    gold   : '#F8A617',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray300: '#C5BFB8',
    gray500: '#8A8480',
    gray700: '#4A4540',
    gray900: '#1E1B18',
};

const inputStyle = { width: '100%', padding: '9px 12px', borderRadius: 8, border: `1.5px solid ${UTM.gray100}`, fontSize: '13px', color: UTM.gray900, background: UTM.white, outline: 'none', boxSizing: 'border-box' };
const inputSmallStyle = { ...inputStyle, padding: '7px 10px', fontSize: '12px' };
const labelStyle = { display: 'block', fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 4 };

function RoleBadge({ role }) {
    const isAdmin = role === 'admin';
    return (
        <span style={{
            display: 'inline-block', padding: '3px 10px', borderRadius: '999px', fontSize: '11px', fontWeight: 700,
            background: isAdmin ? '#F3E0E5' : '#E6F4EC',
            color: isAdmin ? UTM.maroon : '#1A7A3C',
            border: `1px solid ${isAdmin ? '#E8C0CB' : '#B2DFC2'}`,
            whiteSpace: 'nowrap',
        }}>
            {isAdmin ? 'Pentadbir Utama' : 'Staf Biasa'}
        </span>
    );
}

function SelectRole({ value, onChange, small }) {
    const style = small ? inputSmallStyle : inputStyle;
    return (
        <select style={style} value={value} onChange={onChange}>
            <option value="user">Staf Biasa (User)</option>
            <option value="admin">Pentadbir (Admin)</option>
        </select>
    );
}

export default function UserIndex({ users }) {
    const [showAddForm, setShowAddForm] = useState(false);
    const [editingUserId, setEditingUserId] = useState(null);
    const [deleteConfirmId, setDeleteConfirmId] = useState(null);

    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
        name: '',
        email: '',
        password: '',
        role: 'user',
    });

    // ── Handlers ───────────────────────────────────────────────────────────────

    const openAddForm = () => {
        clearErrors();
        reset();
        setEditingUserId(null);
        setShowAddForm(true);
    };

    const closeAddForm = () => {
        setShowAddForm(false);
        clearErrors();
        reset();
    };

    const openEditForm = (user) => {
        clearErrors();
        setShowAddForm(false);
        setEditingUserId(user.id);
        setData({ name: user.name, email: user.email, password: '', role: user.role || 'user' });
    };

    const closeEditForm = () => {
        setEditingUserId(null);
        clearErrors();
        reset();
    };

    const handleCreate = (e) => {
        e.preventDefault();
        post(route('admin.users.store'), {
            onSuccess: () => closeAddForm(),
        });
    };

    const handleUpdate = (e, id) => {
        e.preventDefault();
        put(route('admin.users.update', id), {
            onSuccess: () => closeEditForm(),
        });
    };

    const requestDelete = (id) => {
        setDeleteConfirmId(id);
    };

    const confirmDelete = (id) => {
        destroy(route('admin.users.destroy', id), {
            onSuccess: () => setDeleteConfirmId(null),
        });
    };

    const cancelDelete = () => {
        setDeleteConfirmId(null);
    };

    // ── Styles ─────────────────────────────────────────────────────────────────

    const thStyle = { padding: '12px 20px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.07em', textTransform: 'uppercase', color: UTM.gray500, textAlign: 'left', borderBottom: `2px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };
    const btnMaroon = { padding: '7px 14px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: UTM.maroon, color: UTM.white, border: 'none', cursor: 'pointer', whiteSpace: 'nowrap' };
    const btnGray = { padding: '7px 14px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: UTM.gray100, color: UTM.gray700, border: 'none', cursor: 'pointer', whiteSpace: 'nowrap' };
    const btnDanger = { padding: '7px 14px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: '#F3E0E5', color: UTM.maroon, border: 'none', cursor: 'pointer', whiteSpace: 'nowrap' };
    const btnSmallMaroon = { padding: '5px 12px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: UTM.maroon, color: UTM.white, border: 'none', cursor: 'pointer' };

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '16px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                        <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                            Pengurusan Pengguna
                        </h2>
                    </div>
                    {!showAddForm && (
                        <button onClick={openAddForm} style={{ background: UTM.maroon, color: UTM.white, padding: '9px 18px', borderRadius: 8, fontSize: '13px', fontWeight: 700, border: 'none', cursor: 'pointer', boxShadow: '0 2px 6px rgba(92,0,31,0.2)', whiteSpace: 'nowrap' }}>
                            + Pengguna Baru
                        </button>
                    )}
                </div>
            }
        >
            <Head title="Pengurusan Pengguna" />

            {/* Error banner for self-deletion attempt */}
            {errors.error && (
                <div style={{ background: '#F3E0E5', color: UTM.maroon, padding: '12px 24px', textAlign: 'center', fontWeight: 700, fontSize: '13px' }}>
                    {errors.error}
                </div>
            )}

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 1000, margin: '0 auto' }}>

                    <div style={{ background: UTM.white, borderRadius: 12, boxShadow: '0 1px 4px rgba(92,0,31,0.07)', overflow: 'hidden' }}>
                        <div style={{ overflowX: 'auto', width: '100%' }}>
                            <table style={{ width: '100%', minWidth: '700px', borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr>
                                        <th style={thStyle}>Nama Penuh</th>
                                        <th style={thStyle}>E-mel</th>
                                        <th style={thStyle}>Peranan (Role)</th>
                                        <th style={thStyle}>Tarikh Daftar</th>
                                        <th style={{ ...thStyle, textAlign: 'right' }}>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    {/* ── Inline Add Form ── */}
                                    {showAddForm && (
                                        <tr>
                                            <td colSpan={5} style={{ padding: '16px 20px', background: '#FFF8E8', borderBottom: `2px solid ${UTM.gold}` }}>
                                                <form onSubmit={handleCreate} style={{ display: 'flex', flexWrap: 'wrap', gap: '12px', alignItems: 'flex-end' }}>
                                                    <div style={{ flex: '1 1 160px', minWidth: 0 }}>
                                                        <label style={labelStyle}>Nama Penuh</label>
                                                        <input type="text" required style={inputSmallStyle} value={data.name} onChange={e => setData('name', e.target.value)} placeholder="Nama penuh" />
                                                        {errors.name && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.name}</p>}
                                                    </div>
                                                    <div style={{ flex: '1 1 180px', minWidth: 0 }}>
                                                        <label style={labelStyle}>E-mel</label>
                                                        <input type="email" required style={inputSmallStyle} value={data.email} onChange={e => setData('email', e.target.value)} placeholder="email@utm.my" />
                                                        {errors.email && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.email}</p>}
                                                    </div>
                                                    <div style={{ flex: '0 1 140px', minWidth: 0 }}>
                                                        <label style={labelStyle}>Peranan</label>
                                                        <SelectRole small value={data.role} onChange={e => setData('role', e.target.value)} />
                                                        {errors.role && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.role}</p>}
                                                    </div>
                                                    <div style={{ flex: '0 1 150px', minWidth: 0 }}>
                                                        <label style={labelStyle}>Kata Laluan</label>
                                                        <input type="password" required style={inputSmallStyle} value={data.password} onChange={e => setData('password', e.target.value)} placeholder="Minimum 8 aksara" />
                                                        {errors.password && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.password}</p>}
                                                    </div>
                                                    <div style={{ display: 'flex', gap: 8, paddingBottom: 1, flex: '0 0 auto' }}>
                                                        <button type="submit" disabled={processing} style={processing ? { ...btnSmallMaroon, opacity: 0.6, cursor: 'not-allowed' } : btnSmallMaroon}>
                                                            {processing ? 'Menyimpan...' : 'Simpan'}
                                                        </button>
                                                        <button type="button" onClick={closeAddForm} style={btnGray}>
                                                            Batal
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                    )}

                                    {/* ── User Rows ── */}
                                    {users.map((user, idx) => {
                                        const isEditing = editingUserId === user.id;
                                        return (
                                            <tr key={user.id} style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50, transition: 'background 0.12s' }} onMouseEnter={e => { if (!isEditing) e.currentTarget.style.background = '#FFF5E8'; }} onMouseLeave={e => { if (!isEditing) e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50; }}>

                                                {isEditing ? (
                                                    /* ── Inline Edit Form (full row) ── */
                                                    <td colSpan={5} style={{ padding: '16px 20px', background: '#FFF8E8', borderBottom: `2px solid ${UTM.gold}` }}>
                                                        <form onSubmit={(e) => handleUpdate(e, user.id)} style={{ display: 'flex', flexWrap: 'wrap', gap: '12px', alignItems: 'flex-end' }}>
                                                            <div style={{ flex: '1 1 160px', minWidth: 0 }}>
                                                                <label style={labelStyle}>Nama Penuh</label>
                                                                <input type="text" required style={inputSmallStyle} value={data.name} onChange={e => setData('name', e.target.value)} />
                                                                {errors.name && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.name}</p>}
                                                            </div>
                                                            <div style={{ flex: '1 1 180px', minWidth: 0 }}>
                                                                <label style={labelStyle}>E-mel</label>
                                                                <input type="email" required style={inputSmallStyle} value={data.email} onChange={e => setData('email', e.target.value)} />
                                                                {errors.email && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.email}</p>}
                                                            </div>
                                                            <div style={{ flex: '0 1 140px', minWidth: 0 }}>
                                                                <label style={labelStyle}>Peranan</label>
                                                                <SelectRole small value={data.role} onChange={e => setData('role', e.target.value)} />
                                                                {errors.role && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.role}</p>}
                                                            </div>
                                                            <div style={{ flex: '0 1 150px', minWidth: 0 }}>
                                                                <label style={labelStyle}>Kata Laluan <span style={{ textTransform: 'none', fontWeight: 400, fontSize: '10px' }}>(kosongkan jika kekal)</span></label>
                                                                <input type="password" style={inputSmallStyle} value={data.password} onChange={e => setData('password', e.target.value)} placeholder="Biarkan kosong" />
                                                                {errors.password && <p style={{ color: 'red', fontSize: '11px', marginTop: 2 }}>{errors.password}</p>}
                                                            </div>
                                                            <div style={{ display: 'flex', gap: 8, paddingBottom: 1, flex: '0 0 auto' }}>
                                                                <button type="submit" disabled={processing} style={processing ? { ...btnSmallMaroon, opacity: 0.6, cursor: 'not-allowed' } : btnSmallMaroon}>
                                                                    {processing ? 'Menyimpan...' : 'Kemas Kini'}
                                                                </button>
                                                                <button type="button" onClick={closeEditForm} style={btnGray}>
                                                                    Batal
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                ) : (
                                                    /* ── Normal Display Row ── */
                                                    <>
                                                        <td style={{ padding: '14px 20px', fontSize: '13px', fontWeight: 700, color: UTM.gray900 }}>{user.name}</td>
                                                        <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>{user.email}</td>
                                                        <td style={{ padding: '14px 20px' }}>
                                                            <RoleBadge role={user.role} />
                                                        </td>
                                                        <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>{new Date(user.created_at).toLocaleDateString('ms-MY')}</td>
                                                        <td style={{ padding: '14px 20px', textAlign: 'right', whiteSpace: 'nowrap' }}>
                                                            {deleteConfirmId === user.id ? (
                                                                <span style={{ display: 'inline-flex', alignItems: 'center', gap: 6 }}>
                                                                    <span style={{ fontSize: '12px', color: UTM.maroon, fontWeight: 600 }}>Padamkan?</span>
                                                                    <button onClick={() => confirmDelete(user.id)} disabled={processing} style={{ ...btnDanger, padding: '5px 10px', fontSize: '11px' }}>
                                                                        Ya
                                                                    </button>
                                                                    <button onClick={cancelDelete} style={{ ...btnGray, padding: '5px 10px', fontSize: '11px' }}>
                                                                        Tidak
                                                                    </button>
                                                                </span>
                                                            ) : (
                                                                <>
                                                                    <button onClick={() => openEditForm(user)} style={{ display: 'inline-block', padding: '5px 12px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: '#EDE9E4', color: UTM.gray700, border: 'none', cursor: 'pointer', marginRight: 8 }}>
                                                                        Edit
                                                                    </button>
                                                                    <button onClick={() => requestDelete(user.id)} style={{ display: 'inline-block', padding: '5px 12px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: '#F3E0E5', color: UTM.maroon, border: 'none', cursor: 'pointer' }}>
                                                                        Padam
                                                                    </button>
                                                                </>
                                                            )}
                                                        </td>
                                                    </>
                                                )}
                                            </tr>
                                        );
                                    })}

                                    {users.length === 0 && !showAddForm && (
                                        <tr><td colSpan={5} style={{ padding: '48px', textAlign: 'center', color: UTM.gray500, fontSize: '14px' }}>Tiada pengguna didaftarkan. Klik "+ Pengguna Baru" untuk mendaftar.</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        <div style={{ padding: '12px 20px', borderTop: `1px solid ${UTM.gray100}`, background: UTM.gray50, fontSize: '12px', color: UTM.gray500 }}>
                            Jumlah Pengguna: <strong style={{ color: UTM.maroon }}>{users.length}</strong>
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
