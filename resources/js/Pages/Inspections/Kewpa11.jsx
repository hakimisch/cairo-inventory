import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Kewpa11({ inspections }) {
    const records = inspections || [];
    const statusLabel = (i) => {
        if (i.is_record_complete) return 'Lengkap';
        if (i.is_record_updated) return 'Kemaskini';
        return '-';
    };

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-11 — Laporan Pemeriksaan Inventori
            </h2>
        }>
            <Head title="KEW.PA-11 - Laporan Pemeriksaan Inventori" />

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
                            <div className="font-bold text-[14px]">KEW.PA-11</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        LAPORAN PEMERIKSAAN INVENTORI
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                    </div>

                    <table className="w-full border-collapse border border-black text-center mb-6">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black text-left">Jenis Inventori</th>
                                <th className="p-2 border-r border-black">Daftar KEW.PA-3</th>
                                <th className="p-2 border-r border-black text-left">Lokasi</th>
                                <th className="p-2 border-r border-black w-20">Kuantiti</th>
                                <th className="p-2 border-r border-black">Keadaan Inventori</th>
                                <th className="p-2 text-left">Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {records.length === 0 ? (
                                <tr><td colSpan="7" className="p-4 italic text-gray-500">Tiada rekod pemeriksaan inventori.</td></tr>
                            ) : (
                                records.map((i, idx) => (
                                    <tr key={i.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{idx + 1}</td>
                                        <td className="p-2 border-r border-black text-left uppercase">
                                            {i.asset?.name ?? '-'} ({i.asset?.category ?? '-'})
                                        </td>
                                        <td className="p-2 border-r border-black uppercase">{statusLabel(i)}</td>
                                        <td className="p-2 border-r border-black text-left uppercase">{i.actual_location || '-'}</td>
                                        <td className="p-2 border-r border-black">{i.actual_quantity ?? '-'}</td>
                                        <td className="p-2 border-r border-black uppercase">{i.status || '-'}</td>
                                        <td className="p-2 text-left">{i.notes || '-'}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    <div className="mt-6 grid grid-cols-2 gap-8">
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Pemeriksa 1</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Pemeriksa 2</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <Link
                            href={route('inspections.kewpa11.download')}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-11)
                        </Link>
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
