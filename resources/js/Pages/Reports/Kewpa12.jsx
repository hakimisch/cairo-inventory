import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa12({ assets, year, reportDate, summary }) {
    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-12 — Perakuan Pemeriksaan Tahunan Aset
            </h2>
        }>
            <Head title="KEW.PA-12 - Perakuan Pemeriksaan Tahunan" />

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
                            <div className="font-bold text-[14px]">KEW.PA-12</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        PERAKUAN PEMERIKSAAN TAHUNAN ASET
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                        <p><strong>Tahun</strong> &nbsp; {year}</p>
                        <p><strong>Tarikh Laporan</strong> &nbsp; {reportDate}</p>
                    </div>

                    <table className="w-full border-collapse border border-black text-center mb-6">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black text-left">No. Siri Pendaftaran</th>
                                <th className="p-2 border-r border-black text-left">Nama Aset</th>
                                <th className="p-2 border-r border-black">Kategori</th>
                                <th className="p-2 border-r border-black">Lokasi</th>
                                <th className="p-2 border-r border-black w-28">Harga (RM)</th>
                                <th className="p-2">Status Pemeriksaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {assets.length === 0 ? (
                                <tr><td colSpan="7" className="p-4 italic text-gray-500">Tiada aset dijumpai.</td></tr>
                            ) : (
                                assets.map((asset, i) => (
                                    <tr key={asset.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{i + 1}</td>
                                        <td className="p-2 border-r border-black text-left font-mono uppercase">{asset.asset_tag}</td>
                                        <td className="p-2 border-r border-black text-left uppercase">{asset.name}</td>
                                        <td className="p-2 border-r border-black uppercase">{asset.category}</td>
                                        <td className="p-2 border-r border-black uppercase">{asset.location}</td>
                                        <td className="p-2 border-r border-black text-right">{Number(asset.purchase_price).toFixed(2)}</td>
                                        <td className="p-2 uppercase">{asset.latest_inspection_status || 'BELUM DIPERIKSA'}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    <div className="font-bold mb-2 uppercase">RINGKASAN</div>
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[40%]">Jumlah Aset Diperiksa</td>
                                <td className="border border-black p-2">{summary?.inspected || 0}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Jumlah Aset Tidak Diperiksa</td>
                                <td className="border border-black p-2">{summary?.not_inspected || 0}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Jumlah Aset Rosak / Hilang</td>
                                <td className="border border-black p-2">{summary?.damaged_lost || 0}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Jumlah Keseluruhan</td>
                                <td className="border border-black p-2 font-bold">{summary?.total || 0}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div className="mt-6 grid grid-cols-2 gap-8">
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Pemeriksa</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Ketua PTJ</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('reports.kewpa12.download', { year, report_date: reportDate })}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-12)
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
