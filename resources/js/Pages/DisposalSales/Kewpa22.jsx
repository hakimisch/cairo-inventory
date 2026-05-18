import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Kewpa22({ sale }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">KEW.PA-22 — Sebutharga Jualan Aset</h2>
                    <div className="flex gap-2">
                        <Link href={route('disposal-sales.index')} className="text-sm text-indigo-600 hover:text-indigo-900">← Senarai Jualan</Link>
                        <Link href={route('disposal-sales.show', sale.id)} className="text-sm text-indigo-600 hover:text-indigo-900">← Butiran</Link>
                        <a href={route('disposal-sales.kewpa22.download', sale.id)} className="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Download PDF</a>
                    </div>
                </div>
            }
        >
            <Head title="KEW.PA-22 - Sebutharga Jualan Aset" />
            <div className="py-6">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-center mb-6">KEW.PA-22 — SEBUTHARGA JUALAN ASET</h3>
                        <div className="text-sm space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div><span className="text-gray-500">Rujukan:</span> <span className="font-medium">{sale.sale_reference}</span></div>
                                <div><span className="text-gray-500">Tarikh:</span> <span className="font-medium">{sale.sale_date ?? '-'}</span></div>
                                <div><span className="text-gray-500">Lokasi:</span> <span className="font-medium">{sale.sale_location ?? '-'}</span></div>
                                <div><span className="text-gray-500">Pegawai:</span> <span className="font-medium">{sale.sale_officer ?? '-'}</span></div>
                            </div>
                            {sale.description && <div><span className="text-gray-500">Penerangan:</span><p className="mt-1">{sale.description}</p></div>}
                            {sale.terms_conditions && <div><span className="text-gray-500">Terma & Syarat:</span><p className="mt-1">{sale.terms_conditions}</p></div>}

                            <h4 className="font-semibold mt-4 mb-2">Item / Lot</h4>
                            <table className="w-full border-collapse border border-gray-300 text-xs">
                                <thead><tr className="bg-gray-100">
                                    <th className="border border-gray-300 p-2">Lot</th>
                                    <th className="border border-gray-300 p-2">Perkara</th>
                                    <th className="border border-gray-300 p-2">Kuantiti</th>
                                    <th className="border border-gray-300 p-2">Anggaran Nilai (RM)</th>
                                </tr></thead>
                                <tbody>
                                    {sale.disposal_sale_items?.map(item => (
                                        <tr key={item.id}>
                                            <td className="border border-gray-300 p-2">{item.lot_number ?? '-'}</td>
                                            <td className="border border-gray-300 p-2">{item.item_description ?? item.asset?.name ?? '-'}</td>
                                            <td className="border border-gray-300 p-2 text-center">{item.quantity}</td>
                                            <td className="border border-gray-300 p-2 text-right">{item.estimated_value ? Number(item.estimated_value).toFixed(2) : '-'}</td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
