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
const labelStyle = { display: 'block', fontSize: '11px', fontWeight: 700, textTransform: 'uppercase', letterSpacing: '0.07em', color: UTM.gray500, marginBottom: 5 };

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

export default function UserIndex({ users }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingUser, setEditingUser] = useState(null);

    const { data, setData, post, put, delete: destroy, processing, errors, reset, clearErrors } = useForm({
        name: '',
        email: '',
        password: '',
        role: 'user',
    });

    const openModal = (user = null) => {
        clearErrors();
        if (user) {
            setEditingUser(user);
            setData({ name: user.name, email: user.email, password: '', role: user.role || 'user' });
        } else {
            setEditingUser(null);
            reset();
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingUser(null);
        reset();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingUser) {
            put(route('admin.users.update', editingUser.id), { onSuccess: () => closeModal() });
        } else {
            post(route('admin.users.store'), { onSuccess: () => closeModal() });
        }
    };

    const handleDelete = (id) => {
        if (confirm('Adakah anda pasti mahu memadam pengguna ini?')) {
            destroy(route('admin.users.destroy', id));
        }
    };

    const thStyle = { padding: '12px 20px', fontSize: '11px', fontWeight: 700, letterSpacing: '0.07em', textTransform: 'uppercase', color: UTM.gray500, textAlign: 'left', borderBottom: `2px solid ${UTM.gray100}`, background: UTM.gray50, whiteSpace: 'nowrap' };

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
                    <button
                        onClick={() => openModal()}
                        style={{ background: UTM.maroon, color: UTM.white, padding: '9px 18px', borderRadius: 8, fontSize: '13px', fontWeight: 700, border: 'none', cursor: 'pointer', boxShadow: '0 2px 6px rgba(92,0,31,0.2)', whiteSpace: 'nowrap' }}
                    >
                        + Tambah Pengguna
                    </button>
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
                                    {users.map((user, idx) => (
                                        <tr key={user.id} style={{ background: idx % 2 === 0 ? UTM.white : UTM.gray50, transition: 'background 0.12s' }} onMouseEnter={e => e.currentTarget.style.background = '#FFF5E8'} onMouseLeave={e => e.currentTarget.style.background = idx % 2 === 0 ? UTM.white : UTM.gray50}>
                                            <td style={{ padding: '14px 20px', fontSize: '13px', fontWeight: 700, color: UTM.gray900 }}>{user.name}</td>
                                            <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>{user.email}</td>
                                            <td style={{ padding: '14px 20px' }}>
                                                <RoleBadge role={user.role} />
                                            </td>
                                            <td style={{ padding: '14px 20px', fontSize: '13px', color: UTM.gray700 }}>{new Date(user.created_at).toLocaleDateString('ms-MY')}</td>
                                            <td style={{ padding: '14px 20px', textAlign: 'right', whiteSpace: 'nowrap' }}>
                                                <button onClick={() => openModal(user)} style={{ display: 'inline-block', padding: '5px 12px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: '#EDE9E4', color: UTM.gray700, border: 'none', cursor: 'pointer', marginRight: 8 }}>
                                                    Edit
                                                </button>
                                                <button onClick={() => handleDelete(user.id)} style={{ display: 'inline-block', padding: '5px 12px', borderRadius: 6, fontSize: '12px', fontWeight: 700, background: '#F3E0E5', color: UTM.maroon, border: 'none', cursor: 'pointer' }}>
                                                    Padam
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                    {users.length === 0 && (
                                        <tr><td colSpan={5} style={{ padding: '48px', textAlign: 'center', color: UTM.gray500, fontSize: '14px' }}>Tiada pengguna didaftarkan.</td></tr>
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

            {/* ── Modal Tambah/Edit Pengguna ── */}
            {isModalOpen && (
                <div style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.45)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 50, padding: 16 }}>
                    <div style={{ background: UTM.white, borderRadius: 16, boxShadow: '0 8px 40px rgba(92,0,31,0.2)', maxWidth: 400, width: '100%', overflow: 'hidden' }}>
                        
                        <div style={{ background: UTM.maroon, padding: '20px 24px' }}>
                            <p style={{ fontSize: '16px', fontWeight: 800, color: UTM.white, margin: 0 }}>
                                {editingUser ? 'Kemaskini Pengguna' : 'Tambah Pengguna Baru'}
                            </p>
                        </div>

                        <form onSubmit={handleSubmit} style={{ padding: '24px' }}>
                            <div style={{ marginBottom: 16 }}>
                                <label style={labelStyle}>Nama Penuh</label>
                                <input type="text" required style={inputStyle} value={data.name} onChange={e => setData('name', e.target.value)} />
                                {errors.name && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.name}</p>}
                            </div>

                            <div style={{ marginBottom: 16 }}>
                                <label style={labelStyle}>E-mel</label>
                                <input type="email" required style={inputStyle} value={data.email} onChange={e => setData('email', e.target.value)} />
                                {errors.email && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.email}</p>}
                            </div>

                            <div style={{ marginBottom: 16 }}>
                                <label style={labelStyle}>Peranan (Role)</label>
                                <select style={inputStyle} value={data.role} onChange={e => setData('role', e.target.value)}>
                                    <option value="user">Staf Biasa (User)</option>
                                    <option value="admin">Pentadbir (Admin)</option>
                                </select>
                                {errors.role && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.role}</p>}
                            </div>

                            <div style={{ marginBottom: 24 }}>
                                <label style={labelStyle}>Kata Laluan {editingUser && <span style={{ textTransform: 'none', fontWeight: 400 }}>(Biarkan kosong jika tidak mahu tukar)</span>}</label>
                                <input type="password" required={!editingUser} style={inputStyle} value={data.password} onChange={e => setData('password', e.target.value)} />
                                {errors.password && <p style={{ color: 'red', fontSize: '11px', marginTop: 4 }}>{errors.password}</p>}
                            </div>

                            <div style={{ display: 'flex', justifyContent: 'flex-end', gap: 10 }}>
                                <button type="button" onClick={closeModal} style={{ padding: '10px 20px', borderRadius: 8, fontSize: '13px', fontWeight: 700, background: UTM.gray100, color: UTM.gray700, border: 'none', cursor: 'pointer' }}>
                                    Batal
                                </button>
                                <button type="submit" disabled={processing} style={{ padding: '10px 24px', borderRadius: 8, fontSize: '13px', fontWeight: 700, background: processing ? UTM.gray300 : UTM.maroon, color: UTM.white, border: 'none', cursor: processing ? 'not-allowed' : 'pointer' }}>
                                    {processing ? 'Menyimpan...' : 'Simpan Pengguna'}
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}