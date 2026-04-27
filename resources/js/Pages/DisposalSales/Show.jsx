import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Show({ sale }) {
    const saleTypeBadge = (type) => {
        const colors = { Tawaran: 'bg-blue-100 text-blue-800', Sebutharga: 'bg-purple-100 text-purple-800', Lelongan: 'bg-orange-100 text-orange-800' };
        return <span className={`px-2 py-0.5 text-xs font-medium rounded-full ${colors[type] || 'bg-gray-100 text-gray-800'}`}>{type}</span>;
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Disposal Sale: {sale.sale_reference}
                    </h2>
                    <Link href={route('disposal-sales.index')} className="text-sm text-indigo-600 hover:text-indigo-900">← Back to List</Link>
                </div>
            }
        >
            <Head title={`Sale: ${sale.sale_reference}`} />

            <div className="py-6">
                <div className="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {/* Sale Details */}
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Sale Details</h3>
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div><span className="text-gray-500">Reference:</span> <span className="font-medium">{sale.sale_reference}</span></div>
                            <div><span className="text-gray-500">Type:</span> {saleTypeBadge(sale.sale_type)}</div>
                            <div><span className="text-gray-500">Date:</span> <span className="font-medium">{sale.sale_date ?? '-'}</span></div>
                            <div><span className="text-gray-500">Location:</span> <span className="font-medium">{sale.sale_location ?? '-'}</span></div>
                            <div><span className="text-gray-500">Officer:</span> <span className="font-medium">{sale.sale_officer ?? '-'}</span></div>
                            <div><span className="text-gray-500">Deposit Required:</span> <span className="font-medium">{sale.deposit_required ? `RM ${Number(sale.deposit_required).toFixed(2)}` : '-'}</span></div>
                            <div><span className="text-gray-500">Status:</span> <span className="font-medium">{sale.status}</span></div>
                            <div><span className="text-gray-500">Sale Status:</span> <span className="font-medium">{sale.sale_status}</span></div>
                        </div>
                        {sale.description && <div className="mt-3 text-sm"><span className="text-gray-500">Description:</span> <p className="mt-1">{sale.description}</p></div>}
                    </div>

                    {/* Asset Disposal Info */}
                    {sale.asset_disposal && (
                        <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                            <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Asset Disposal</h3>
                            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div><span className="text-gray-500">Asset:</span> <span className="font-medium">{sale.asset_disposal.asset?.name} ({sale.asset_disposal.asset?.asset_tag})</span></div>
                                <div><span className="text-gray-500">Method:</span> <span className="font-medium">{sale.asset_disposal.disposal_method}</span></div>
                                <div><span className="text-gray-500">Date:</span> <span className="font-medium">{sale.asset_disposal.disposal_date ?? '-'}</span></div>
                                <div><span className="text-gray-500">Status:</span> <span className="font-medium">{sale.asset_disposal.status}</span></div>
                            </div>
                        </div>
                    )}

                    {/* Sale Items */}
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div className="p-4 border-b border-gray-200">
                            <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wider">Sale Items ({sale.disposal_sale_items?.length ?? 0})</h3>
                        </div>
                        {sale.disposal_sale_items?.map((item) => (
                            <div key={item.id} className="p-4 border-b border-gray-100 last:border-b-0">
                                <div className="grid grid-cols-2 md:grid-cols-5 gap-3 text-sm">
                                    <div><span className="text-gray-500">Lot:</span> <span className="font-medium">{item.lot_number ?? '-'}</span></div>
                                    <div><span className="text-gray-500">Description:</span> <span className="font-medium">{item.item_description ?? item.asset?.name ?? '-'}</span></div>
                                    <div><span className="text-gray-500">Qty:</span> <span className="font-medium">{item.quantity}</span></div>
                                    <div><span className="text-gray-500">Reserve:</span> <span className="font-medium">{item.reserve_price ? `RM ${Number(item.reserve_price).toFixed(2)}` : '-'}</span></div>
                                    <div><span className="text-gray-500">Status:</span> <span className="font-medium">{item.status}</span></div>
                                </div>
                                {/* Bids for this item */}
                                {item.sale_bids?.length > 0 && (
                                    <div className="mt-2 ml-4 pl-3 border-l-2 border-indigo-200">
                                        <p className="text-xs text-gray-500 mb-1">Bids ({item.sale_bids.length}):</p>
                                        {item.sale_bids.map((bid) => (
                                            <div key={bid.id} className="text-xs text-gray-600 flex gap-3">
                                                <span>{bid.bidder_name}</span>
                                                <span className="font-medium">RM {Number(bid.bid_amount).toFixed(2)}</span>
                                                <span className={bid.is_winner ? 'text-green-600 font-medium' : ''}>{bid.is_winner ? '★ Winner' : ''}</span>
                                            </div>
                                        ))}
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>

                    {/* KEW.PA Form Links */}
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <h3 className="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">KEW.PA Forms</h3>
                        <div className="flex flex-wrap gap-2">
                            {[
                                { label: 'PA-21 Tawaran Jualan', route: 'disposal-sales.kewpa21' },
                                { label: 'PA-22 Sebutharga Jualan', route: 'disposal-sales.kewpa22' },
                                { label: 'PA-23 Lelongan Jualan', route: 'disposal-sales.kewpa23' },
                                { label: 'PA-24 Keputusan', route: 'disposal-sales.kewpa24' },
                                { label: 'PA-25 Laporan', route: 'disposal-sales.kewpa25' },
                                { label: 'PA-26 Perakuan (T/S/L)', route: 'disposal-sales.kewpa26' },
                                { label: 'PA-27 Perakuan (Pelupusan)', route: 'disposal-sales.kewpa27' },
                                { label: 'PA-27A Perakuan (Lupus)', route: 'disposal-sales.kewpa27a' },
                            ].map((form) => (
                                <Link
                                    key={form.route}
                                    href={route(form.route, sale.id)}
                                    className="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 rounded-md hover:bg-indigo-100"
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
