import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa10({ asset }) {
    const inspections = asset.inspections || [];

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-10 — Laporan Pemeriksaan Aset
            </h2>
        }>
            <Head title={`KEW.PA-10 - ${asset.asset_tag}`} />

            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black relative">

                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[14px]">KEW.PA-10</div>
                            <div className="text-[10px]">(No. Siri Pendaftaran : {asset.asset_tag})</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        LAPORAN PEMERIKSAAN ASET
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                        <p><strong>Unit/Makmal</strong> &nbsp; {asset.location}</p>
                        <p><strong>Nama Aset</strong> &nbsp; {asset.name} ({asset.asset_tag})</p>
                    </div>

                    <table className="w-full border-collapse border border-black text-center">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black w-24">Tarikh</th>
                                <th className="p-2 border-r border-black">Nama Pemeriksa</th>
                                <th className="p-2 border-r border-black">Status Aset</th>
                                <th className="p-2 border-r border-black text-left">Catatan</th>
                                <th className="p-2 w-28">Tandatangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {inspections.length === 0 ? (
                                <tr><td colSpan="6" className="p-4 italic text-gray-500">Tiada rekod pemeriksaan.</td></tr>
                            ) : (
                                inspections.map((insp, i) => (
                                    <tr key={insp.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{i + 1}</td>
                                        <td className="p-2 border-r border-black">{new Date(insp.inspection_date).toLocaleDateString('ms-MY')}</td>
                                        <td className="p-2 border-r border-black uppercase">{insp.inspector_name}</td>
                                        <td className="p-2 border-r border-black uppercase">{insp.status}</td>
                                        <td className="p-2 border-r border-black text-left">{insp.notes || '-'}</td>
                                        <td className="p-2"></td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    <div className="mt-8">
                        <div className="mt-16 border-t border-black pt-1 text-center font-bold w-64 mx-auto">Tandatangan Pemeriksa</div>
                        <div className="mt-2 text-center">Nama : ........................................</div>
                        <div className="text-center">Jawatan : ........................................</div>
                        <div className="text-center">Tarikh : ........................................</div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('assets.kewpa10.download', asset.id)}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-10)
                        </a>
                        <button
                            onClick={() => window.print()}
                            className="border border-gray-300 px-6 py-2 rounded font-bold hover:bg-gray-50"
                        >
                            Cetak Skrin
                        </button>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
