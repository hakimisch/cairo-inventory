import { useState, useMemo } from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

// ─── UTM brand palette ────────────────────────────────────────────────────────
const UTM = {
    maroon : '#5C001F',
    maroon2: '#7A0029',
    gold   : '#F8A617',
    goldDark:'#C9840A',
    sand   : '#FFF5AB',
    white  : '#FFFFFF',
    gray50 : '#F9F7F5',
    gray100: '#EDE9E4',
    gray200: '#D5CFC9',
    gray300: '#B0A89F',
    gray500: '#8A8480',
    gray600: '#625E5B',
};

// ─── Stage Colors ──────────────────────────────────────────────────────────────
const STAGE_COLORS = {
    'Logistik & Penerimaan':    { bg: '#E8F5E9', text: '#2E7D32', border: '#A5D6A7' },
    'Pendaftaran Aset':         { bg: '#E3F2FD', text: '#1565C0', border: '#90CAF9' },
    'Pergerakan & Pemeriksaan': { bg: '#FFF3E0', text: '#E65100', border: '#FFCC80' },
    'Penyelenggaraan':          { bg: '#F3E5F5', text: '#7B1FA2', border: '#CE93D8' },
    'Pelupusan & Jualan':       { bg: '#FCE4EC', text: '#C62828', border: '#EF9A9A' },
    'Kehilangan':               { bg: '#FFEBEE', text: '#B71C1C', border: '#EF9A9A' },
    'Laporan Tahunan':          { bg: '#E8EAF6', text: '#283593', border: '#9FA8DA' },
};

const STAGE_ORDER = [
    'Logistik & Penerimaan',
    'Pendaftaran Aset',
    'Pergerakan & Pemeriksaan',
    'Penyelenggaraan',
    'Pelupusan & Jualan',
    'Kehilangan',
    'Laporan Tahunan',
];

export default function KewpaDirectory({ forms }) {
    const { auth } = usePage().props;
    const isAdmin = auth.user?.role === 'admin';
    const [search, setSearch] = useState('');
    const [filterStage, setFilterStage] = useState('all');

    // Filter forms
    const filtered = useMemo(() => {
        let list = forms;

        // Hide admin-only from non-admin
        if (!isAdmin) {
            list = list.filter(f => !f.admin_only);
        }

        // Stage filter
        if (filterStage !== 'all') {
            list = list.filter(f => f.stage === filterStage);
        }

        // Search filter
        if (search.trim()) {
            const q = search.toLowerCase().trim();
            list = list.filter(f =>
                f.name.toLowerCase().includes(q) ||
                f.name_en.toLowerCase().includes(q) ||
                f.number.toLowerCase().includes(q) ||
                f.stage.toLowerCase().includes(q) ||
                f.description.toLowerCase().includes(q)
            );
        }

        return list;
    }, [forms, search, filterStage, isAdmin]);

    // Group by stage
    const grouped = useMemo(() => {
        const map = {};
        for (const f of filtered) {
            if (!map[f.stage]) map[f.stage] = [];
            map[f.stage].push(f);
        }
        return map;
    }, [filtered]);

    return (
        <AuthenticatedLayout
            header={
                <h2 style={{ fontSize: 20, fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                    📋 Direktori KEW.PA
                </h2>
            }
        >
            <Head title="KEW.PA Directory" />

            <div style={{ maxWidth: 1100, margin: '0 auto', padding: '32px 24px' }}>

                {/* ── Intro ── */}
                <div style={{
                    background: UTM.white,
                    borderRadius: 10,
                    padding: '20px 24px',
                    marginBottom: 24,
                    border: `1px solid ${UTM.gray100}`,
                }}>
                    <p style={{ fontSize: 14, color: UTM.gray600, margin: 0, lineHeight: 1.6 }}>
                        Pilih borang KEW.PA di bawah untuk melihat senarai, paparan borang, atau muat turun PDF.
                        Direktori ini merangkumi kesemua 32 borang KEW.PA merentasi kitaran hayat pengurusan aset.
                    </p>
                </div>

                {/* ── Search & Filter ── */}
                <div style={{
                    display: 'flex', gap: 12, marginBottom: 24,
                    flexWrap: 'wrap', alignItems: 'center',
                }}>
                    <div style={{ flex: 1, minWidth: 200, position: 'relative' }}>
                        <span style={{
                            position: 'absolute', left: 12, top: '50%',
                            transform: 'translateY(-50%)',
                            fontSize: 14, color: UTM.gray500, pointerEvents: 'none',
                        }}>
                            🔍
                        </span>
                        <input
                            type="text"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari borang KEW.PA... (nombor, nama, keterangan)"
                            style={{
                                width: '100%',
                                padding: '10px 12px 10px 36px',
                                border: `1px solid ${UTM.gray200}`,
                                borderRadius: 8,
                                fontSize: 13,
                                color: UTM.gray600,
                                background: UTM.white,
                                outline: 'none',
                                boxSizing: 'border-box',
                            }}
                        />
                    </div>

                    <select
                        value={filterStage}
                        onChange={e => setFilterStage(e.target.value)}
                        style={{
                            padding: '10px 14px',
                            border: `1px solid ${UTM.gray200}`,
                            borderRadius: 8,
                            fontSize: 13,
                            color: UTM.gray600,
                            background: UTM.white,
                            cursor: 'pointer',
                            outline: 'none',
                        }}
                    >
                        <option value="all">Semua Peringkat</option>
                        {STAGE_ORDER.map(s => {
                            const c = STAGE_COLORS[s] || {};
                            return (
                                <option key={s} value={s}>{s}</option>
                            );
                        })}
                    </select>

                    <div style={{
                        fontSize: 12, color: UTM.gray500,
                        padding: '10px 0', whiteSpace: 'nowrap',
                    }}>
                        {filtered.length} borang
                    </div>
                </div>

                {/* ── Form Cards Grouped by Stage ── */}
                {STAGE_ORDER.map(stage => {
                    const stageForms = grouped[stage];
                    if (!stageForms || stageForms.length === 0) return null;

                    const colors = STAGE_COLORS[stage] || { bg: UTM.gray50, text: UTM.gray600, border: UTM.gray200 };

                    return (
                        <div key={stage} style={{ marginBottom: 32 }}>
                            {/* Stage header */}
                            <div style={{
                                display: 'flex', alignItems: 'center', gap: 10,
                                marginBottom: 12,
                            }}>
                                <div style={{
                                    padding: '4px 12px',
                                    borderRadius: 6,
                                    fontSize: 11,
                                    fontWeight: 700,
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.06em',
                                    background: colors.bg,
                                    color: colors.text,
                                    border: `1px solid ${colors.border}`,
                                }}>
                                    {stage}
                                </div>
                                <div style={{
                                    flex: 1, height: 1, background: colors.border,
                                }} />
                            </div>

                            {/* Cards grid */}
                            <div style={{
                                display: 'grid',
                                gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))',
                                gap: 12,
                            }}>
                                {stageForms.map((form, idx) => (
                                    <KewpaCard key={idx} form={form} colors={colors} />
                                ))}
                            </div>
                        </div>
                    );
                })}

                {filtered.length === 0 && (
                    <div style={{
                        textAlign: 'center', padding: 48,
                        color: UTM.gray500, fontSize: 14,
                    }}>
                        Tiada borang KEW.PA yang sepadan dengan carian anda.
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

// ─── Individual Card ──────────────────────────────────────────────────────────
function KewpaCard({ form, colors }) {
    const statusColors = {
        implemented:    { label: '✅ Sedia',    color: '#2E7D32', bg: '#E8F5E9' },
        partial:        { label: '⚠️ Sebahagian', color: '#E65100', bg: '#FFF3E0' },
        planned:        { label: '📋 Dirancang', color: '#1565C0', bg: '#E3F2FD' },
    };
    const sc = statusColors[form.status] || statusColors.planned;

    return (
        <div style={{
            background: UTM.white,
            borderRadius: 10,
            border: `1px solid ${UTM.gray100}`,
            padding: '14px 16px',
            display: 'flex',
            flexDirection: 'column',
            gap: 8,
            transition: 'box-shadow 0.15s, transform 0.15s',
        }}
            onMouseEnter={e => {
                e.currentTarget.style.boxShadow = '0 4px 12px rgba(92,0,31,0.08)';
                e.currentTarget.style.transform = 'translateY(-1px)';
            }}
            onMouseLeave={e => {
                e.currentTarget.style.boxShadow = 'none';
                e.currentTarget.style.transform = 'none';
            }}
        >
            {/* Top row: number + status */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start' }}>
                <span style={{
                    fontSize: 11, fontWeight: 800,
                    color: colors.text, letterSpacing: '0.04em',
                }}>
                    KEW.{form.number}
                </span>
                <span style={{
                    fontSize: 10, fontWeight: 700,
                    padding: '2px 8px', borderRadius: 4,
                    background: sc.bg, color: sc.color,
                }}>
                    {sc.label}
                </span>
            </div>

            {/* Title */}
            <div>
                <p style={{
                    fontSize: 14, fontWeight: 700,
                    color: UTM.maroon, margin: 0, lineHeight: 1.3,
                }}>
                    {form.name}
                </p>
                <p style={{
                    fontSize: 11, color: UTM.gray500, margin: '2px 0 0',
                    fontStyle: 'italic',
                }}>
                    {form.name_en}
                </p>
            </div>

            {/* Description */}
            <p style={{
                fontSize: 12, color: UTM.gray600, margin: 0,
                lineHeight: 1.5, flex: 1,
            }}>
                {form.description}
            </p>

            {/* Action links */}
            <div style={{
                display: 'flex', gap: 6, flexWrap: 'wrap',
                marginTop: 4, paddingTop: 8,
                borderTop: `1px solid ${UTM.gray100}`,
            }}>
                <ActionLink
                    href={route(form.route_list, form.route_view_params || {})}
                    label="Senarai"
                    icon="📋"
                />
                {form.route_view && (
                    form.route_view.includes('.download') ? (
                        <ActionLink
                            href={route(form.route_view, form.route_view_params || {})}
                            label="PDF"
                            icon="📄"
                        />
                    ) : (
                        <ActionLink
                            href={route(form.route_view, form.route_view_params || {})}
                            label="Lihat"
                            icon="👁️"
                        />
                    )
                )}
                {form.route_download && (
                    <ActionLink
                        href={route(form.route_download, form.route_download_params || {})}
                        label="Muat Turun PDF"
                        icon="⬇️"
                    />
                )}
            </div>
        </div>
    );
}

function ActionLink({ href, label, icon }) {
    return (
        <Link
            href={href || '#'}
            style={{
                display: 'inline-flex', alignItems: 'center', gap: 3,
                fontSize: 11, fontWeight: 600,
                padding: '3px 8px',
                borderRadius: 5,
                color: UTM.maroon,
                background: '#FFF5F0',
                textDecoration: 'none',
                transition: 'background 0.1s',
            }}
            onMouseEnter={e => { e.currentTarget.style.background = '#FFE8E0'; }}
            onMouseLeave={e => { e.currentTarget.style.background = '#FFF5F0'; }}
        >
            {icon} {label}
        </Link>
    );
}
