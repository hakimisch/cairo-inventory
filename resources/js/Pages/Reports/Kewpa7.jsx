import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa7({ assets, location, reportDate }) {
    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-7 — Laporan Kedudukan Aset
            </h2>
        }>
            <Head title="KEW.PA-7 - Laporan Kedudukan Aset" />

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
                            <div className="font-bold text-[14px]">KEW.PA-7</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        LAPORAN KEDUDUKAN ASET
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                        <p><strong>Tarikh Laporan</strong> &nbsp; {reportDate}</p>
                        <p><strong>Lokasi</strong> &nbsp; {location || 'SEMUA LOKASI'}</p>
                    </div>

                    <table className="w-full border-collapse border border-black text-center">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black text-left">No. Siri Pendaftaran</th>
                                <th className="p-2 border-r border-black text-left">Nama Aset</th>
                                <th className="p-2 border-r border-black">Kategori</th>
                                <th className="p-2 border-r border-black">Lokasi Semasa</th>
                                <th className="p-2 border-r border-black">Nama Pegawai</th>
                                <th className="p-2 border-r border-black w-28">Harga (RM)</th>
                                <th className="p-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            {assets.length === 0 ? (
                                <tr><td colSpan="8" className="p-4 italic text-gray-500">Tiada aset dijumpai untuk lokasi ini.</td></tr>
                            ) : (
                                assets.map((asset, i) => (
                                    <tr key={asset.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{i + 1}</td>
                                        <td className="p-2 border-r border-black text-left font-mono uppercase">{asset.asset_tag}</td>
                                        <td className="p-2 border-r border-black text-left uppercase">{asset.name}</td>
                                        <td className="p-2 border-r border-black uppercase">{asset.category}</td>
                                        <td className="p-2 border-r border-black uppercase">{asset.location}</td>
                                        <td className="p-2 border-r border-black uppercase">{asset.custodian_name || '-'}</td>
                                        <td className="p-2 border-r border-black text-right">{Number(asset.purchase_price).toFixed(2)}</td>
                                        <td className="p-2 uppercase">{asset.status}</td>
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
                            href={route('reports.kewpa7.download', { location: location || '', report_date: reportDate })}
                            onClick={(e) => {
                                e.preventDefault();
                                const copies = window.prompt('Bilangan salinan?', '3');
                                if (copies && !isNaN(copies) && parseInt(copies) > 0) {
                                    window.location.href = route('reports.kewpa7.download', { location: location || '', report_date: reportDate }) + `?copies=${parseInt(copies)}`;
                                }
                            }}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-7)
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
