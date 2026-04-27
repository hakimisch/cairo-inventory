import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa9a({ asset, placement }) {
    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-9A — Borang Pinjaman Aset
            </h2>
        }>
            <Head title={`KEW.PA-9A - ${asset.asset_tag}`} />

            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-4xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black relative">

                    {/* ── Header ── */}
                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[14px]">KEW.PA-9A</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[14px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[13px] mb-6 uppercase">
                        BORANG PINJAMAN ASET
                    </div>

                    {/* ── Asset Info ── */}
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[25%]">No. Siri Pendaftaran</td>
                                <td className="border border-black p-2 font-mono uppercase">{asset.asset_tag}</td>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[20%]">Kategori</td>
                                <td className="border border-black p-2 uppercase">{asset.category}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Nama Aset</td>
                                <td className="border border-black p-2 uppercase" colSpan="3">{asset.name}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Jenama / Model</td>
                                <td className="border border-black p-2 uppercase" colSpan="3">{asset.brand} / {asset.model}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">No. Siri</td>
                                <td className="border border-black p-2 font-mono">{asset.serial_number || '—'}</td>
                                <td className="border border-black p-2 font-bold bg-gray-50">Lokasi</td>
                                <td className="border border-black p-2 uppercase">{asset.location}</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Peminjam (Borrower) Section ── */}
                    <div className="font-bold mb-2 uppercase">MAKLUMAT PEMINJAM</div>
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[30%]">Nama</td>
                                <td className="border border-black p-2 uppercase">{placement?.custodian_name || '........................................'}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">No. Staf / Matrik</td>
                                <td className="border border-black p-2">{placement?.matric_no || '........................................'}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">No. Tel. Bimbit</td>
                                <td className="border border-black p-2">{placement?.borrower_phone || '........................................'}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">PTJ / Unit</td>
                                <td className="border border-black p-2 uppercase">CAIRO UTM</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Tujuan Pinjaman</td>
                                <td className="border border-black p-2">{placement?.purpose || '................................................................................'}</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Loan Details ── */}
                    <div className="font-bold mb-2 uppercase">TEMPOH PINJAMAN</div>
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[25%]">Tarikh Mula</td>
                                <td className="border border-black p-2 w-[25%]">
                                    {placement?.assigned_date
                                        ? new Date(placement.assigned_date).toLocaleDateString('ms-MY')
                                        : '................................'}
                                </td>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[25%]">Tarikh Pulang</td>
                                <td className="border border-black p-2 w-[25%]">
                                    {placement?.expected_return_date
                                        ? new Date(placement.expected_return_date).toLocaleDateString('ms-MY')
                                        : '................................'}
                                </td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Kuantiti</td>
                                <td className="border border-black p-2">{placement?.quantity_placed || 1}</td>
                                <td className="border border-black p-2 font-bold bg-gray-50">Lokasi Pinjaman</td>
                                <td className="border border-black p-2 uppercase">{placement?.location || asset.location}</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Authorizer / Pemberi Pinjam ── */}
                    <div className="font-bold mb-2 uppercase">MAKLUMAT PENYERAH / PEMBERI PINJAM</div>
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[30%]">Nama</td>
                                <td className="border border-black p-2 uppercase">{placement?.authorizer_name || '........................................'}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Jawatan</td>
                                <td className="border border-black p-2">........................................</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Tandatangan</td>
                                <td className="border border-black p-2">........................................</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Tarikh</td>
                                <td className="border border-black p-2">........................................</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Return Confirmation ── */}
                    <div className="font-bold mb-2 uppercase">PENGESAHAN PEMULANGAN</div>
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[30%]">Tarikh Pulang</td>
                                <td className="border border-black p-2">
                                    {placement?.returned_date
                                        ? new Date(placement.returned_date).toLocaleDateString('ms-MY')
                                        : '........................................'}
                                </td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Status Aset</td>
                                <td className="border border-black p-2 uppercase">........................................</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Tandatangan Penerima</td>
                                <td className="border border-black p-2">........................................</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">Tarikh</td>
                                <td className="border border-black p-2">........................................</td>
                            </tr>
                        </tbody>
                    </table>

                    <div className="mt-6 text-[10px] italic border-t border-gray-300 pt-3">
                        Nota: Borang ini hendaklah diisi dalam 2 salinan (1-Peminjam; 1-Pejabat PTJ)
                    </div>

                    {/* ── Actions ── */}
                    <div className="mt-6 flex justify-end gap-3 print:hidden">
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
