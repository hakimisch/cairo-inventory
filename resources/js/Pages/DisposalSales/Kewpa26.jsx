import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Kewpa26({ sale }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">KEW.PA-26 — Perakuan Pelupusan (Tawaran/Sebutharga/Lelongan)</h2>
                    <div className="flex gap-2">
                        <Link href={route('disposal-sales.index')} className="text-sm text-indigo-600 hover:text-indigo-900">← Senarai Jualan</Link>
                        <Link href={route('disposal-sales.show', sale.id)} className="text-sm text-indigo-600 hover:text-indigo-900">← Butiran</Link>
                        <a href={route('disposal-sales.kewpa26.download', sale.id)} className="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Download PDF</a>
                    </div>
                </div>
            }
        >
            <Head title="KEW.PA-26 - Perakuan (T/S/L)" />
            <div className="py-6">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-center mb-6">KEW.PA-26 — PERAKUAN PELUPUSAN (TAWARAN/SEBUTHARGA/LELONGAN)</h3>
                        <div className="text-sm space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div><span className="text-gray-500">Rujukan:</span> <span className="font-medium">{sale.sale_reference}</span></div>
                                <div><span className="text-gray-500">Tarikh Sijil:</span> <span className="font-medium">{sale.certificate_date ?? '-'}</span></div>
                                <div><span className="text-gray-500">No. Sijil:</span> <span className="font-medium">{sale.certificate_reference ?? '-'}</span></div>
                                <div><span className="text-gray-500">Jenis:</span> <span className="font-medium">{sale.sale_type}</span></div>
                            </div>

                            <h4 className="font-semibold mt-4 mb-2">Item Dilupuskan</h4>
                            <table className="w-full border-collapse border border-gray-300 text-xs">
                                <thead><tr className="bg-gray-100">
                                    <th className="border border-gray-300 p-2">Lot</th>
                                    <th className="border border-gray-300 p-2">Perkara</th>
                                    <th className="border border-gray-300 p-2">Pembida Berjaya</th>
                                    <th className="border border-gray-300 p-2">Jumlah (RM)</th>
                                </tr></thead>
                                <tbody>
                                    {sale.disposal_sale_items?.map(item => {
                                        const winner = item.sale_bids?.find(b => b.is_winner);
                                        return (
                                            <tr key={item.id}>
                                                <td className="border border-gray-300 p-2">{item.lot_number ?? '-'}</td>
                                                <td className="border border-gray-300 p-2">{item.item_description ?? item.asset?.name ?? '-'}</td>
                                                <td className="border border-gray-300 p-2">{winner?.bidder_name ?? '-'}</td>
                                                <td className="border border-gray-300 p-2 text-right">{winner ? Number(winner.bid_amount).toFixed(2) : '-'}</td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>

                            <div className="mt-6 grid grid-cols-3 gap-4 text-center">
                                <div><p className="font-semibold">Pegawai Pelupusan</p><p className="text-gray-500 mt-8">_________________________</p></div>
                                <div><p className="font-semibold">Pengerusi Jawatankuasa</p><p className="text-gray-500 mt-8">_________________________</p></div>
                                <div><p className="font-semibold">Pendaftar</p><p className="text-gray-500 mt-8">_________________________</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
