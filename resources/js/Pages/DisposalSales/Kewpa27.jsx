import React from 'react';
import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Kewpa27({ sale }) {
    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">KEW.PA-27 — Perakuan Pelupusan (Pelupusan)</h2>
                    <div className="flex gap-2">
                        <Link href={route('disposal-sales.index')} className="text-sm text-indigo-600 hover:text-indigo-900">← Senarai Jualan</Link>
                        <Link href={route('disposal-sales.show', sale.id)} className="text-sm text-indigo-600 hover:text-indigo-900">← Butiran</Link>
                        <a href={route('disposal-sales.kewpa27.download', sale.id)} className="px-3 py-1.5 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">Download PDF</a>
                    </div>
                </div>
            }
        >
            <Head title="KEW.PA-27 - Perakuan (Pelupusan)" />
            <div className="py-6">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 className="text-lg font-semibold text-center mb-6">KEW.PA-27 — PERAKUAN PELUPUSAN (PELUPUSAN)</h3>
                        <div className="text-sm space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div><span className="text-gray-500">Rujukan:</span> <span className="font-medium">{sale.sale_reference}</span></div>
                                <div><span className="text-gray-500">Tarikh Sijil:</span> <span className="font-medium">{sale.certificate_date ?? '-'}</span></div>
                                <div><span className="text-gray-500">No. Sijil:</span> <span className="font-medium">{sale.certificate_reference ?? '-'}</span></div>
                            </div>

                            <p>Dengan ini diperakui bahawa pelupusan aset melalui <strong>{sale.sale_type}</strong> telah selesai dilaksanakan mengikut prosedur yang ditetapkan.</p>

                            {sale.asset_disposal && (
                                <div className="bg-gray-50 p-3 rounded">
                                    <p><span className="text-gray-500">Aset:</span> {sale.asset_disposal.asset?.name} ({sale.asset_disposal.asset?.asset_tag})</p>
                                    <p><span className="text-gray-500">Kaedah Pelupusan:</span> {sale.asset_disposal.disposal_method}</p>
                                    <p><span className="text-gray-500">Tarikh Pelupusan:</span> {sale.asset_disposal.disposal_date ?? '-'}</p>
                                </div>
                            )}

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
