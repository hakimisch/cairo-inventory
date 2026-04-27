import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa6({ asset }) {
    const placements = asset.placements || [];
    const transfers = asset.transfers || [];

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-6 — Daftar Pergerakan Aset
            </h2>
        }>
            <Head title={`KEW.PA-6 - ${asset.asset_tag}`} />

            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black relative">

                    {/* ── Header ── */}
                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[14px]">KEW.PA-6</div>
                            <div className="text-[10px]">(No. Siri Pendaftaran : {asset.asset_tag})</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        DAFTAR PERGERAKAN ASET
                    </div>

                    <div className="mb-4 leading-relaxed">
                        <p><strong>Fakulti/PTJ</strong> &nbsp; CAIRO UTM</p>
                        <p><strong>Unit/Makmal</strong> &nbsp; {asset.location}</p>
                        <p><strong>Nama Aset</strong> &nbsp; {asset.name} ({asset.asset_tag})</p>
                    </div>

                    {/* ── A. Penempatan / Pinjaman ── */}
                    <div className="font-bold mb-1 uppercase mt-6">A. PENEMPATAN / PINJAMAN</div>
                    <table className="w-full border-collapse border border-black mb-6 text-center">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black">Lokasi</th>
                                <th className="p-2 border-r border-black w-24">Tarikh</th>
                                <th className="p-2 border-r border-black">Nama Pegawai</th>
                                <th className="p-2 border-r border-black w-24">No. Pekerja</th>
                                <th className="p-2 w-24">Tandatangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {placements.length === 0 ? (
                                <tr><td colSpan="6" className="p-4 italic text-gray-500">Tiada rekod penempatan.</td></tr>
                            ) : (
                                placements.map((p, i) => (
                                    <tr key={p.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{i + 1}</td>
                                        <td className="p-2 border-r border-black uppercase">{p.location}</td>
                                        <td className="p-2 border-r border-black">{new Date(p.assigned_date).toLocaleDateString('ms-MY')}</td>
                                        <td className="p-2 border-r border-black uppercase">{p.custodian_name}</td>
                                        <td className="p-2 border-r border-black">{p.staff_id || '-'}</td>
                                        <td className="p-2"></td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    {/* ── B. Pindahan ── */}
                    <div className="font-bold mb-1 uppercase">B. PINDAHAN</div>
                    <table className="w-full border-collapse border border-black mb-6 text-center">
                        <thead className="bg-gray-50 font-bold">
                            <tr className="border-b border-black">
                                <th className="p-2 border-r border-black w-8">Bil</th>
                                <th className="p-2 border-r border-black w-24">Tarikh</th>
                                <th className="p-2 border-r border-black">Lokasi Baru</th>
                                <th className="p-2 border-r border-black">Pegawai Baru</th>
                                <th className="p-2 border-r border-black w-28">No. Rujukan</th>
                                <th className="p-2 border-r border-black">Sebab</th>
                                <th className="p-2 w-24">Tandatangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {transfers.length === 0 ? (
                                <tr><td colSpan="7" className="p-4 italic text-gray-500">Tiada rekod pindahan.</td></tr>
                            ) : (
                                transfers.map((t, i) => (
                                    <tr key={t.id} className="border-b border-black">
                                        <td className="p-2 border-r border-black">{i + 1}</td>
                                        <td className="p-2 border-r border-black">{new Date(t.transfer_date).toLocaleDateString('ms-MY')}</td>
                                        <td className="p-2 border-r border-black uppercase">{t.to_location}</td>
                                        <td className="p-2 border-r border-black uppercase">{t.to_custodian}</td>
                                        <td className="p-2 border-r border-black">{t.reference_no || '-'}</td>
                                        <td className="p-2 border-r border-black text-left">{t.reason || '-'}</td>
                                        <td className="p-2"></td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>

                    {/* ── Signature ── */}
                    <div className="mt-8 grid grid-cols-2 gap-8">
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Tandatangan Pegawai Bertanggungjawab</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                        <div>
                            <div className="mt-16 border-t border-black pt-1 text-center font-bold">Tandatangan Ketua PTJ</div>
                            <div className="mt-2">Nama : ........................................</div>
                            <div>Jawatan : ........................................</div>
                            <div>Tarikh : ........................................</div>
                        </div>
                    </div>

                    <div className="mt-6 text-[10px] italic border-t border-gray-300 pt-3">
                        Nota: Sila sediakan dalam 2 salinan (1-Unit/Makmal; 1-Pejabat Pentadbiran)
                    </div>

                    {/* ── Actions ── */}
                    <div className="mt-6 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('assets.kewpa6.download', asset.id)}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-6)
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
