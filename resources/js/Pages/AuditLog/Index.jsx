import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

const UTM = {
    maroon : '#5C001F', gold: '#F8A617', goldDark:'#C9840A',
    white: '#FFFFFF', gray50: '#F9F7F5', gray100: '#EDE9E4', gray500: '#8A8480',
    gray700: '#4A4540', gray900: '#1E1B18',
};

const inputStyle = {
    width: '100%', padding: '7px 10px', borderRadius: 6,
    border: `1.5px solid ${UTM.gray100}`, fontSize: 12,
    color: UTM.gray700, background: UTM.white, outline: 'none',
    boxSizing: 'border-box',
};

const EventBadge = ({ event }) => {
    const map = {
        created: { bg: '#D1FAE5', color: '#065F46', label: 'Cipta' },
        updated: { bg: '#DBEAFE', color: '#1E40AF', label: 'Kemas Kini' },
        deleted: { bg: '#FEE2E2', color: '#991B1B', label: 'Padam' },
        restored: { bg: '#FEF3C7', color: '#92400E', label: 'Pulih' },
    };
    const s = map[event] || { bg: '#F3E8FF', color: '#6B21A8', label: event };
    return (
        <span style={{
            display: 'inline-block', padding: '2px 10px', borderRadius: 999,
            fontSize: 10, fontWeight: 700, background: s.bg, color: s.color,
            whiteSpace: 'nowrap',
        }}>
            {s.label}
        </span>
    );
};

export default function Index({ activities, filters, events }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [eventFilter, setEventFilter] = useState(filters?.event || '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('audit-log.index'),
            { search, event: eventFilter },
            { preserveState: true, replace: true }
        );
    };

    const handleReset = () => {
        setSearch('');
        setEventFilter('');
        router.get(route('audit-log.index'), {}, { preserveState: true, replace: true });
    };

    const thStyle = {
        padding: '10px 14px', fontSize: 10, fontWeight: 700,
        letterSpacing: '0.07em', textTransform: 'uppercase',
        color: UTM.gray500, textAlign: 'left',
        borderBottom: `2px solid ${UTM.gray100}`, background: UTM.gray50,
        whiteSpace: 'nowrap',
    };

    const tdStyle = {
        padding: '10px 14px', fontSize: 11, color: UTM.gray700,
        borderBottom: `1px solid ${UTM.gray100}`,
    };

    const attemptParse = (props) => {
        if (!props) return null;
        if (typeof props === 'string') {
            try { return JSON.parse(props); } catch { return null; }
        }
        return props;
    };

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                    <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                        Log Audit — Rekod Aktiviti Sistem
                    </h2>
                </div>
            }
        >
            <Head title="Log Audit" />
            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '24px' }}>
                <div style={{ maxWidth: 1200, margin: '0 auto' }}>

                    {/* Search & Filter */}
                    <form onSubmit={handleSearch} style={{
                        display: 'flex', gap: 12, alignItems: 'flex-end',
                        flexWrap: 'wrap', marginBottom: 20, padding: 16,
                        background: UTM.white, borderRadius: 10,
                        border: `1px solid ${UTM.gray100}`,
                    }}>
                        <div style={{ flex: 1, minWidth: 200 }}>
                            <label style={{
                                display: 'block', fontSize: 10, fontWeight: 700,
                                textTransform: 'uppercase', letterSpacing: '0.07em',
                                color: UTM.gray500, marginBottom: 3,
                            }}>Carian</label>
                            <input type="text" value={search}
                                onChange={e => setSearch(e.target.value)}
                                placeholder="Cari perihal, subjek, pengguna..."
                                style={inputStyle} />
                        </div>
                        <div>
                            <label style={{
                                display: 'block', fontSize: 10, fontWeight: 700,
                                textTransform: 'uppercase', letterSpacing: '0.07em',
                                color: UTM.gray500, marginBottom: 3,
                            }}>Jenis Acara</label>
                            <select value={eventFilter}
                                onChange={e => setEventFilter(e.target.value)}
                                style={{ ...inputStyle, cursor: 'pointer', minWidth: 130 }}>
                                <option value="">Semua</option>
                                {events.map(e => (
                                    <option key={e} value={e}>{e}</option>
                                ))}
                            </select>
                        </div>
                        <button type="submit" style={{
                            padding: '7px 20px', borderRadius: 6, border: 'none',
                            background: UTM.maroon, color: UTM.white,
                            fontSize: 12, fontWeight: 700, cursor: 'pointer',
                        }}>Cari</button>
                        <button type="button" onClick={handleReset} style={{
                            padding: '7px 16px', borderRadius: 6,
                            border: `1.5px solid ${UTM.gray100}`, background: UTM.white,
                            color: UTM.gray700, fontSize: 12, fontWeight: 600,
                            cursor: 'pointer',
                        }}>Reset</button>
                    </form>

                    {/* Table */}
                    <div style={{
                        background: UTM.white, borderRadius: 12,
                        border: `1px solid ${UTM.gray100}`, overflow: 'hidden',
                    }}>
                        <div style={{ overflowX: 'auto' }}>
                            <table style={{ width: '100%', minWidth: 900, borderCollapse: 'collapse' }}>
                                <thead>
                                    <tr>
                                        <th style={thStyle}>Masa</th>
                                        <th style={thStyle}>Acara</th>
                                        <th style={thStyle}>Perihal</th>
                                        <th style={thStyle}>Subjek</th>
                                        <th style={thStyle}>ID Subjek</th>
                                        <th style={thStyle}>Pengguna</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {activities.data?.length === 0 ? (
                                        <tr>
                                            <td colSpan={6} style={{
                                                ...tdStyle, textAlign: 'center',
                                                color: UTM.gray500, padding: '40px 16px',
                                            }}>
                                                Tiada aktiviti direkodkan.
                                            </td>
                                        </tr>
                                    ) : activities.data?.map((a, idx) => (
                                        <tr key={a.id} style={{
                                            background: idx % 2 === 0 ? UTM.white : UTM.gray50,
                                        }}>
                                            <td style={{ ...tdStyle, whiteSpace: 'nowrap', fontSize: 10 }}>
                                                {a.created_at ? new Date(a.created_at).toLocaleString('ms-MY') : '—'}
                                            </td>
                                            <td style={tdStyle}>
                                                <EventBadge event={a.event} />
                                            </td>
                                            <td style={{ ...tdStyle, maxWidth: 300, overflow: 'hidden', textOverflow: 'ellipsis' }}>
                                                {a.description}
                                            </td>
                                            <td style={tdStyle}>
                                                <span style={{
                                                    fontFamily: 'monospace', fontSize: 10,
                                                    background: UTM.gray50, padding: '2px 6px',
                                                    borderRadius: 4,
                                                }}>
                                                    {a.subject_type}
                                                </span>
                                            </td>
                                            <td style={{ ...tdStyle, fontFamily: 'monospace', fontSize: 10 }}>
                                                #{a.subject_id ?? '—'}
                                            </td>
                                            <td style={{ ...tdStyle, fontSize: 10 }}>
                                                {a.causer_name}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        {/* Pagination */}
                        {activities.links && (
                            <div style={{
                                display: 'flex', justifyContent: 'center', gap: 4,
                                padding: '12px 16px', borderTop: `1px solid ${UTM.gray100}`,
                            }}>
                                {activities.links.map((link, i) => (
                                    link.url ? (
                                        <Link key={i} href={link.url}
                                            dangerouslySetInnerHTML={{ __html: link.label }}
                                            style={{
                                                padding: '4px 10px', borderRadius: 4,
                                                fontSize: 11, fontWeight: 600,
                                                background: link.active ? UTM.maroon : UTM.gray50,
                                                color: link.active ? UTM.white : UTM.gray700,
                                                textDecoration: 'none',
                                            }}
                                        />
                                    ) : (
                                        <span key={i} style={{
                                            padding: '4px 10px', borderRadius: 4, fontSize: 11,
                                            color: UTM.gray500,
                                        }} dangerouslySetInnerHTML={{ __html: link.label }} />
                                    )
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
