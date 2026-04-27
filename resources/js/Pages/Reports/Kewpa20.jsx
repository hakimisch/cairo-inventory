import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa20({ disposals, year, reportDate }) {
    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-20 — Laporan Pelupusan Aset Tahunan
            </h2>
        }>
            <Head title="KEW.PA-20 - Laporan Pelupusan Aset Tahunan" />

            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-6xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black relative">

                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[14px]">KEW.PA-20</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        LAPORAN PELUPUSAN ASET TAHUNAN
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                        <p><strong>Tahun</strong> &nbsp; {year}</p>
                        <p><strong>Tarikh Laporan</strong> &nbsp; {reportDate}</p>
                    </div>

                    <table className="w-full border-collapse border border-black text-center">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black text-left">No. Siri Pendaftaran</th>
                                <th className="p-2 border-r border-black text-left">Nama Aset</th>
                                <th className="p-2 border-r border-black">Kaedah Pelupusan</th>
                                <th className="p-2 border-r border-black w-24">Tarikh</th>
                                <th className="p-2 border-r border-black w-28">Rujukan Kelulusan</th>
                                <th className="p-2 w-24">Harga (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            {disposals.length === 0 ? (
                                <tr><td colSpan="7" className="p-4 italic text-gray-500">Tiada pelupusan direkodkan untuk tahun ini.</td></tr>
                            ) : (
                                disposals.map((d, i) => (
                                    <tr key={d.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{i + 1}</td>
                                        <td className="p-2 border-r border-black text-left font-mono uppercase">{d.asset?.asset_tag}</td>
                                        <td className="p-2 border-r border-black text-left uppercase">{d.asset?.name}</td>
                                        <td className="p-2 border-r border-black uppercase">{d.disposal_method || '-'}</td>
                                        <td className="p-2 border-r border-black">{d.disposal_date ? new Date(d.disposal_date).toLocaleDateString('ms-MY') : '-'}</td>
                                        <td className="p-2 border-r border-black">{d.approval_reference || '-'}</td>
                                        <td className="p-2 text-right">{d.asset ? Number(d.asset.purchase_price).toFixed(2) : '0.00'}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    <div className="mt-6 grid grid-cols-2 gap-8">
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Disediakan Oleh</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Disahkan Oleh (Ketua PTJ)</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('reports.kewpa20.download', { year, report_date: reportDate })}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-20)
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
