import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

const UTM = {
    maroon : '#7B1818',
    gold   : '#D4A843',
    white  : '#FFFFFF',
    gray50 : '#F8F6F3',
    gray100: '#EDE9E4',
    gray200: '#D6D0C8',
    gray500: '#8A8078',
    gray600: '#6B6360',
    gray700: '#4A4440',
};

const cardStyle = {
    background   : UTM.white,
    borderRadius : 12,
    border       : `1px solid ${UTM.gray100}`,
    padding      : 20,
};

const sectionTitleStyle = {
    fontSize      : '11px',
    fontWeight    : 700,
    color         : UTM.gray500,
    textTransform : 'uppercase',
    letterSpacing : '0.5px',
    marginBottom  : 12,
};

const labelStyle = {
    fontSize : '12px',
    color    : UTM.gray500,
};

const valueStyle = {
    fontSize   : '13px',
    fontWeight : 600,
    color      : UTM.gray700,
};

function SaleTypeBadge({ type }) {
    const map = {
        Tawaran   : { bg: '#DBEAFE', color: '#1E40AF' },
        Sebutharga: { bg: '#EDE9FE', color: '#5B21B6' },
        Lelongan  : { bg: '#FFEDD5', color: '#9A3412' },
    };
    const s = map[type] || { bg: UTM.gray100, color: UTM.gray600 };
    return (
        <span style={{
            display       : 'inline-block',
            padding       : '2px 10px',
            borderRadius  : 999,
            fontSize      : '11px',
            fontWeight    : 700,
            background    : s.bg,
            color         : s.color,
        }}>
            {type}
        </span>
    );
}

export default function Show({ sale }) {
    const DetailItem = ({ label, children }) => (
        <div>
            <div style={labelStyle}>{label}</div>
            <div style={valueStyle}>{children ?? '—'}</div>
        </div>
    );

    return (
        <AuthenticatedLayout
            header={
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', width: '100%' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                        <div style={{ width: 4, height: 24, background: UTM.gold, borderRadius: 2 }} />
                        <h2 style={{ fontSize: '18px', fontWeight: 700, color: UTM.maroon, margin: 0 }}>
                            Disposal Sale: {sale.sale_reference}
                        </h2>
                    </div>
                    <Link
                        href={route('disposal-sales.index')}
                        style={{
                            fontSize      : '13px',
                            color         : UTM.gray500,
                            textDecoration: 'none',
                            fontWeight    : 600,
                            transition    : 'color 0.12s',
                        }}
                    >
                        ← Back to List
                    </Link>
                </div>
            }
        >
            <Head title={`Sale: ${sale.sale_reference}`} />

            <div style={{ background: UTM.gray50, minHeight: '100vh', padding: '28px 24px' }}>
                <div style={{ maxWidth: 900, margin: '0 auto', display: 'flex', flexDirection: 'column', gap: 20 }}>

                    {/* ── Sale Details ───────────────────────────────────────── */}
                    <div style={cardStyle}>
                        <h3 style={sectionTitleStyle}>Sale Details</h3>
                        <div style={{
                            display  : 'grid',
                            gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))',
                            gap      : 16,
                        }}>
                            <DetailItem label="Reference">{sale.sale_reference}</DetailItem>
                            <div>
                                <div style={labelStyle}>Type</div>
                                <SaleTypeBadge type={sale.sale_type} />
                            </div>
                            <DetailItem label="Date">{sale.sale_date ?? '-'}</DetailItem>
                            <DetailItem label="Location">{sale.sale_location ?? '-'}</DetailItem>
                            <DetailItem label="Officer">{sale.sale_officer ?? '-'}</DetailItem>
                            <DetailItem label="Deposit Required">
                                {sale.deposit_required ? `RM ${Number(sale.deposit_required).toLocaleString()}` : '-'}
                            </DetailItem>
                            <DetailItem label="Status">{sale.status}</DetailItem>
                            <DetailItem label="Sale Status">{sale.sale_status}</DetailItem>
                        </div>
                        {sale.description && (
                            <div style={{ marginTop: 16 }}>
                                <div style={labelStyle}>Description</div>
                                <p style={{ ...valueStyle, margin: '4px 0 0', fontWeight: 400 }}>
                                    {sale.description}
                                </p>
                            </div>
                        )}
                    </div>

                    {/* ── Asset Disposal Info ─────────────────────────────────── */}
                    {sale.asset_disposal && (
                        <div style={cardStyle}>
                            <h3 style={sectionTitleStyle}>Asset Disposal</h3>
                            <div style={{
                                display  : 'grid',
                                gridTemplateColumns: 'repeat(auto-fill, minmax(180px, 1fr))',
                                gap      : 16,
                            }}>
                                <DetailItem label="Asset">
                                    {sale.asset_disposal.asset?.name} ({sale.asset_disposal.asset?.asset_tag})
                                </DetailItem>
                                <DetailItem label="Method">{sale.asset_disposal.disposal_method}</DetailItem>
                                <DetailItem label="Date">{sale.asset_disposal.disposal_date ?? '-'}</DetailItem>
                                <DetailItem label="Status">{sale.asset_disposal.status}</DetailItem>
                            </div>
                        </div>
                    )}

                    {/* ── Sale Items ─────────────────────────────────────────── */}
                    <div style={{ ...cardStyle, padding: 0, overflow: 'hidden' }}>
                        <div style={{ padding: 20, borderBottom: `1px solid ${UTM.gray100}` }}>
                            <h3 style={{ ...sectionTitleStyle, marginBottom: 0 }}>
                                Sale Items ({sale.disposal_sale_items?.length ?? 0})
                            </h3>
                        </div>
                        {sale.disposal_sale_items?.map((item) => (
                            <div
                                key={item.id}
                                style={{
                                    padding    : '16px 20px',
                                    borderBottom: `1px solid ${UTM.gray100}`,
                                }}
                            >
                                <div style={{
                                    display  : 'grid',
                                    gridTemplateColumns: 'repeat(auto-fill, minmax(150px, 1fr))',
                                    gap      : 12,
                                }}>
                                    <DetailItem label="Lot">{item.lot_number ?? '-'}</DetailItem>
                                    <DetailItem label="Description">
                                        {item.item_description ?? item.asset?.name ?? '-'}
                                    </DetailItem>
                                    <DetailItem label="Qty">{item.quantity}</DetailItem>
                                    <DetailItem label="Reserve">
                                        {item.reserve_price ? `RM ${Number(item.reserve_price).toLocaleString()}` : '-'}
                                    </DetailItem>
                                    <DetailItem label="Status">{item.status}</DetailItem>
                                </div>

                                {/* Bids for this item */}
                                {item.sale_bids?.length > 0 && (
                                    <div style={{
                                        marginTop : 8,
                                        marginLeft: 16,
                                        paddingLeft: 12,
                                        borderLeft: `2px solid ${UTM.gold}`,
                                    }}>
                                        <p style={{ fontSize: 12, color: UTM.gray500, marginBottom: 4 }}>
                                            Bids ({item.sale_bids.length}):
                                        </p>
                                        {item.sale_bids.map((bid) => (
                                            <div key={bid.id} style={{
                                                display: 'flex',
                                                gap: 12,
                                                fontSize: 12,
                                                color: UTM.gray600,
                                            }}>
                                                <span>{bid.bidder_name}</span>
                                                <span style={{ fontWeight: 600 }}>
                                                    RM {Number(bid.bid_amount).toLocaleString()}
                                                </span>
                                                {bid.is_winner && (
                                                    <span style={{ color: '#065F46', fontWeight: 600 }}>★ Winner</span>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>

                    {/* ── KEW.PA Form Links ──────────────────────────────────── */}
                    <div style={cardStyle}>
                        <h3 style={sectionTitleStyle}>KEW.PA Forms</h3>
                        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
                            {[
                                { label: 'PA-21 Tawaran Jualan',       route: 'disposal-sales.kewpa21' },
                                { label: 'PA-22 Sebutharga Jualan',     route: 'disposal-sales.kewpa22' },
                                { label: 'PA-23 Lelongan Jualan',       route: 'disposal-sales.kewpa23' },
                                { label: 'PA-24 Keputusan',             route: 'disposal-sales.kewpa24' },
                                { label: 'PA-25 Laporan',               route: 'disposal-sales.kewpa25' },
                                { label: 'PA-26 Perakuan (T/S/L)',      route: 'disposal-sales.kewpa26' },
                                { label: 'PA-27 Perakuan (Pelupusan)',  route: 'disposal-sales.kewpa27' },
                                { label: 'PA-27A Perakuan (Lupus)',     route: 'disposal-sales.kewpa27a' },
                            ].map((form) => (
                                <Link
                                    key={form.route}
                                    href={route(form.route, sale.id)}
                                    style={{
                                        display       : 'inline-block',
                                        padding       : '7px 16px',
                                        borderRadius  : 6,
                                        fontSize      : '12px',
                                        fontWeight    : 600,
                                        background    : UTM.maroon,
                                        color         : UTM.white,
                                        textDecoration: 'none',
                                        transition    : 'all 0.12s',
                                    }}
                                >
                                    {form.label}
                                </Link>
                            ))}
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}
