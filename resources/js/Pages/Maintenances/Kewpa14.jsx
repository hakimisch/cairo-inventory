import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Kewpa14({ maintenances }) {
    const records = maintenances || [];

    const formatDate = (dateStr) => {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('ms-MY', { day: '2-digit', month: '2-digit', year: 'numeric' });
    };

    const formatCost = (cost) => {
        if (cost == null) return '-';
        return Number(cost).toLocaleString('ms-MY', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-14 — Daftar Penyelenggaraan Harta Tetap
            </h2>
        }>
            <Head title="KEW.PA-14 - Daftar Penyelenggaraan Harta Tetap" />

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
                            <div className="font-bold text-[14px]">KEW.PA-14</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        DAFTAR PENYELENGGARAAN HARTA TETAP
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                    </div>

                    <table className="w-full border-collapse border border-black text-center mb-4">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black w-24">Tarikh</th>
                                <th className="p-2 border-r border-black text-left">Butir-Butir Kerja</th>
                                <th className="p-2 border-r border-black">No. Kontrak/PT & Tarikh</th>
                                <th className="p-2 border-r border-black">Nama Syarikat</th>
                                <th className="p-2 border-r border-black w-24">Kos RM</th>
                                <th className="p-2 w-28">Nama & Tandatangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {records.length === 0 ? (
                                <tr><td colSpan="7" className="p-4 italic text-gray-500">Tiada rekod penyelenggaraan.</td></tr>
                            ) : (
                                records.map((m, idx) => (
                                    <tr key={m.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{idx + 1}</td>
                                        <td className="p-2 border-r border-black">{formatDate(m.maintenance_date)}</td>
                                        <td className="p-2 border-r border-black text-left uppercase">{m.description || '-'}</td>
                                        <td className="p-2 border-r border-black">{m.contract_no || '-'}</td>
                                        <td className="p-2 border-r border-black uppercase">{m.company_name || '-'}</td>
                                        <td className="p-2 border-r border-black text-right">{formatCost(m.cost)}</td>
                                        <td className="p-2"></td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    <div className="text-[10px] mb-6 leading-relaxed">
                        <p><strong>Nota:</strong> (a) tarikh, (b) butir-butir kerja termasuk alat ganti, (c) no. kontrak/PT, (d) nama syarikat, (e) kos, (f) tandatangan pegawai bertanggungjawab</p>
                    </div>

                    <div className="mt-4">
                        <div className="mt-12 border-t border-black pt-1 text-center font-bold w-80 mx-auto">Pegawai Bertanggungjawab</div>
                        <div className="mt-2 text-center">Nama : ........................................</div>
                        <div className="text-center">Jawatan : ........................................</div>
                        <div className="text-center">Tarikh : ........................................</div>
                    </div>

                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <Link
                            href={route('maintenances.kewpa14.download')}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-14)
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
