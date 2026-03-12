import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa2({ asset }) {
    return (
        <AuthenticatedLayout header={<h2 className="print:hidden uppercase text-sm font-bold">Pratonton KEW.PA-2</h2>}>
            <Head title="KEW.PA-2 Borang Penolakan" />
            
            <div className="py-12 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight">
                    
                    {/* Top Header */}
                    <div className="flex justify-between items-start mb-2">
                        <div className="font-bold underline italic">WAJIB CETAK</div>
                        <div className="text-right font-bold text-sm">KEW.PA-2</div>
                    </div>

                    <div className="text-right mb-4">
                        <p>No. Rujukan: 0001/{new Date().getFullYear()}</p>
                    </div>

                    <div className="text-center mb-8">
                        <h1 className="text-lg font-bold underline uppercase tracking-widest">BORANG PENOLAKAN ASET ALIH</h1>
                    </div>

                    {/* Supplier & Reference Info Table */}
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td rowSpan="2" className="border border-black p-2 w-1/3 align-top">
                                    <span className="font-bold">Nama dan Alamat Pembekal:</span><br />
                                    {asset.supplier_name || 'N/A'}<br />
                                    {asset.supplier_address || 'N/A'}
                                </td>
                                <td colSpan="2" className="border border-black p-1 text-center font-bold">Pesanan Kerajaan (PK) / Kontrak</td>
                                <td colSpan="2" className="border border-black p-1 text-center font-bold">Nota Hantaran (DO)</td>
                            </tr>
                            <tr className="text-center">
                                <td className="border border-black p-1 font-bold w-1/6">No. Rujukan</td>
                                <td className="border border-black p-1 font-bold w-1/6">Tarikh</td>
                                <td className="border border-black p-1 font-bold w-1/6">No. Rujukan</td>
                                <td className="border border-black p-1 font-bold w-1/6">Tarikh</td>
                            </tr>
                            <tr className="text-center h-8">
                                <td className="border border-black p-1"></td>
                                <td className="border border-black p-1">{asset.po_reference || '-'}</td>
                                <td className="border border-black p-1">{new Date(asset.created_at).toLocaleDateString('ms-MY')}</td>
                                <td className="border border-black p-1">{asset.do_reference || '-'}</td>
                                <td className="border border-black p-1">{new Date(asset.created_at).toLocaleDateString('ms-MY')}</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* Rejection Details Table */}
                    <table className="w-full border-collapse border border-black mb-8 text-center">
                        <thead>
                            <tr className="font-bold">
                                <td rowSpan="2" className="border border-black p-2 w-16">No. Kod</td>
                                <td rowSpan="2" className="border border-black p-2">Keterangan Aset Alih</td>
                                <td colSpan="2" className="border border-black p-1">Kuantiti</td>
                                <td rowSpan="2" className="border border-black p-2 w-32">Sebab-Sebab Penolakan</td>
                                <td rowSpan="2" className="border border-black p-2 w-32">Catatan</td>
                            </tr>
                            <tr className="font-bold">
                                <td className="border border-black p-1 w-16">Dipesan</td>
                                <td className="border border-black p-1 w-16">Ditolak</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr className="h-16">
                                <td className="border border-black p-2 font-mono uppercase">{asset.asset_tag}</td>
                                <td className="border border-black p-2 uppercase text-left">{asset.name}</td>
                                <td className="border border-black p-2">1</td>
                                <td className="border border-black p-2">1</td>
                                <td className="border border-black p-2 italic">{asset.rejection_reason || 'Kerosakan fizikal / tidak mengikut spesifikasi'}</td>
                                <td className="border border-black p-2">Sila hubungi pembekal untuk penggantian.</td>
                            </tr>
                        </tbody>
                    </table>

                    {/* Signature Section */}
                    <div className="grid grid-cols-2 gap-10 mt-12">
                        <div className="space-y-1">
                            <p className="font-bold">Pegawai Penerima</p>
                            <div className="h-12"></div>
                            <p>....................................................</p>
                            <p>Nama: {asset.receiver_name || 'Hafiz Hakimi'}</p>
                            <p>Jawatan: Asset Officer (CAIRO)</p>
                            <p>Tarikh: {new Date().toLocaleDateString('ms-MY')}</p>
                        </div>
                        <div className="space-y-1">
                            <p className="font-bold">Akuan Terima Pembekal</p>
                            <div className="h-12"></div>
                            <p>....................................................</p>
                            <p>Nama:</p>
                            <p>Tarikh:</p>
                            <p>Cap Syarikat:</p>
                        </div>
                    </div>

                    {/* Print Action */}
                    <div className="mt-10 flex justify-end gap-3 print:hidden">
                        <a 
                            href={route('assets.kewpa2.download', asset.id)} 
                            className="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow-lg transition"
                        >
                            Download Official PDF (KEW.PA-2)
                        </a>
                        <button onClick={() => window.print()} className="border border-gray-300 px-6 py-2 rounded font-bold hover:bg-gray-50">
                            Cetak Skrin
                        </button>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}