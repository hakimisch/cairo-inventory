import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa1({ receiving }) {
    return (
        <AuthenticatedLayout header={<h2 className="print:hidden uppercase text-sm font-bold">Pratonton KEW.PA-1</h2>}>
            <Head title="KEW.PA-1 Borang Penerimaan" />
            
            <div className="py-12 bg-white min-h-screen">
                <div className="max-w-5xl mx-auto border-2 border-black p-8 font-serif text-[11px] leading-tight">
                    <div className="text-right font-bold mb-2">KEW.PA-1</div>
                    <div className="text-center font-bold text-lg mb-6 underline uppercase tracking-tighter">
                        BORANG PENERIMAAN ASET ALIH
                    </div>

                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold w-1/4 bg-gray-50 uppercase">Pembekal</td>
                                <td className="border border-black p-2 w-1/4">
                                    <div className="font-bold">{receiving.supplier_name}</div>
                                    <div className="text-[10px]">{receiving.supplier_address}</div>
                                </td>
                                <td className="border border-black p-2 font-bold w-1/4 bg-gray-50 uppercase">No. Pesanan (PO)</td>
                                <td className="border border-black p-2 w-1/4 font-mono">{receiving.purchase_order_no}</td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 uppercase">No. Nota Hantaran (DO)</td>
                                <td className="border border-black p-2 font-mono">{receiving.delivery_order_no}</td>
                                <td className="border border-black p-2 font-bold bg-gray-50 uppercase">Tarikh Terima</td>
                                <td className="border border-black p-2">{new Date(receiving.created_at).toLocaleDateString('ms-MY')}</td>
                            </tr>
                        </tbody>
                    </table>

                    <table className="w-full border-collapse border border-black text-center">
                        <thead>
                            <tr className="bg-gray-50 uppercase font-bold text-[10px]">
                                <th className="border border-black p-2 w-12">Bil</th>
                                <th className="border border-black p-2 text-left">Keterangan Aset Alih</th>
                                <th className="border border-black p-2 w-32">Kuantiti Dipesan</th>
                                <th className="border border-black p-2 w-32">Kuantiti Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr className="h-20">
                                <td className="border border-black p-2">1</td>
                                <td className="border border-black p-2 text-left align-top uppercase">{receiving.item_description}</td>
                                <td className="border border-black p-2 text-lg">{receiving.quantity_ordered}</td>
                                <td className="border border-black p-2 text-lg font-bold">{receiving.quantity_received}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div className="mt-16 grid grid-cols-2 gap-20">
                        <div className="text-center space-y-12">
                            <p>....................................................</p>
                            <p className="font-bold underline uppercase">Tandatangan Pegawai Penerima</p>
                        </div>
                        <div className="text-center space-y-12">
                            <p>....................................................</p>
                            <p className="font-bold underline uppercase">Tandatangan Pegawai Teknikal</p>
                        </div>
                    </div>

                    <div className="mt-12 flex justify-end gap-3 print:hidden">
                        <a 
                            href={route('receivings.kewpa1.download', receiving.id)} 
                            className="bg-indigo-600 text-white px-6 py-2 rounded font-bold shadow hover:bg-indigo-700"
                        >
                            Download Official PDF (KEW.PA-1)
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