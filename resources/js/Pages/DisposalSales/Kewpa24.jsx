import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Kewpa24({ sale }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">KEW.PA-24 — Keputusan Tawaran/Sebutharga/Lelongan</h2>
                    <div className="flex gap-2">
                        <Link href={route('disposal-sales.show', sale.id)} className="text-sm text-indigo-600 hover:text-indigo-900">← Back</Link>
                        <Link href={route('disposal-sales.kewpa24.download', sale.id)} className="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Download PDF</Link>
                    </div>
                </div>
            }
        >
            <Head title="KEW.PA-24 - Keputusan" />
            <div className="py-6">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-center mb-6">KEW.PA-24 — KEPUTUSAN TAWARAN/SEBUTHARGA/LELONGAN</h3>
                        <div className="text-sm space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div><span className="text-gray-500">Rujukan:</span> <span className="font-medium">{sale.sale_reference}</span></div>
                                <div><span className="text-gray-500">Tarikh Keputusan:</span> <span className="font-medium">{sale.decision_date ?? '-'}</span></div>
                                <div><span className="text-gray-500">Jenis:</span> <span className="font-medium">{sale.sale_type}</span></div>
                                <div><span className="text-gray-500">Status:</span> <span className="font-medium">{sale.sale_status}</span></div>
                            </div>
                            {sale.decision_notes && <div><span className="text-gray-500">Catatan Keputusan:</span><p className="mt-1">{sale.decision_notes}</p></div>}

                            <h4 className="font-semibold mt-4 mb-2">Keputusan Bidaan</h4>
                            <table className="w-full border-collapse border border-gray-300 text-xs">
                                <thead><tr className="bg-gray-100">
                                    <th className="border border-gray-300 p-2">Lot</th>
                                    <th className="border border-gray-300 p-2">Pembida</th>
                                    <th className="border border-gray-300 p-2">Jumlah Bidaan (RM)</th>
                                    <th className="border border-gray-300 p-2">Keputusan</th>
                                </tr></thead>
                                <tbody>
                                    {sale.disposal_sale_items?.map(item =>
                                        item.sale_bids?.filter(b => b.is_winner).map(bid => (
                                            <tr key={bid.id}>
                                                <td className="border border-gray-300 p-2">{item.lot_number ?? '-'}</td>
                                                <td className="border border-gray-300 p-2">{bid.bidder_name}</td>
                                                <td className="border border-gray-300 p-2 text-right">{Number(bid.bid_amount).toFixed(2)}</td>
                                                <td className="border border-gray-300 p-2 text-green-600 font-medium">Diterima</td>
                                            </tr>
                                        ))
                                    )}
                                    {(!sale.disposal_sale_items?.some(i => i.sale_bids?.some(b => b.is_winner))) && (
                                        <tr><td colSpan={4} className="border border-gray-300 p-2 text-center text-gray-400">Tiada keputusan bidaan lagi.</td></tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
