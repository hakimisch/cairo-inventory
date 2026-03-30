import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa1({ receiving }) {
    const totalAmount = (receiving.unit_price ?? 0) * (receiving.quantity_received ?? 0);

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-1A — Borang Terimaan Aset
            </h2>
        }>
            <Head title="KEW.PA-1A Borang Terimaan Aset" />

            {/* Print styles */}
            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black">

                    {/* ── Header ── */}
                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[12px]">KEW.PA-1A</div>
                            <div className="text-[10px]">No. Rujukan BTA : {receiving.receive_no}</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[13px] mb-1 uppercase">
                        BORANG TERIMAAN ASET (BTA)
                    </div>
                    <div className="text-center text-[10px] mb-6">
                        (Disediakan dalam 3 salinan oleh Pegawai Penerima)
                    </div>

                    {/* ── Supplier / reference info ── */}
                    <table className="w-full border-collapse border border-black mb-4">
                        <thead>
                            <tr className="text-center text-[10px] font-bold">
                                <th className="border border-black p-2 text-left w-[18%]">Nama Pembekal :</th>
                                <th className="border border-black p-2 text-left w-[28%]">Alamat Pembekal :</th>
                                <th className="border border-black p-2 w-[18%]">No Pesanan Tempatan<br/>dan Tarikh (PT) :</th>
                                <th className="border border-black p-2 w-[12%]">No Invois :</th>
                                <th className="border border-black p-2 w-[14%]">No Nota Hantaran (DO) :</th>
                                <th className="border border-black p-2 w-[10%]">Tarikh Terimaan Aset :</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr className="align-top">
                                <td className="border border-black p-2 font-bold uppercase">
                                    {receiving.supplier_name}
                                </td>
                                <td className="border border-black p-2 text-[10px]">
                                    {receiving.supplier_address}
                                </td>
                                <td className="border border-black p-2 text-center font-mono">
                                    {receiving.purchase_order_no}
                                </td>
                                <td className="border border-black p-2 text-center font-mono">
                                    {receiving.invoice_no || '—'}
                                </td>
                                <td className="border border-black p-2 text-center font-mono">
                                    {receiving.delivery_order_no}
                                </td>
                                <td className="border border-black p-2 text-center">
                                    {new Date(receiving.created_at).toLocaleDateString('ms-MY')}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Items table ── */}
                    <table className="w-full border-collapse border border-black mb-8 text-center">
                        <thead>
                            <tr className="font-bold text-[10px] bg-gray-50">
                                <th className="border border-black p-2 w-8">Bil</th>
                                <th className="border border-black p-2 text-left">Perihal Aset</th>
                                <th className="border border-black p-2 w-20" colSpan="2">Kuantiti</th>
                                <th className="border border-black p-2 w-28" colSpan="2">Harga</th>
                                <th className="border border-black p-2 w-24">Catatan</th>
                            </tr>
                            <tr className="font-bold text-[10px] bg-gray-50">
                                <th className="border border-black p-1"></th>
                                <th className="border border-black p-1"></th>
                                <th className="border border-black p-1 w-16">Dipesan</th>
                                <th className="border border-black p-1 w-16">Diterima</th>
                                <th className="border border-black p-1 w-24">Seunit</th>
                                <th className="border border-black p-1 w-24">Jumlah</th>
                                <th className="border border-black p-1"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr className="h-16 align-top">
                                <td className="border border-black p-2">1</td>
                                <td className="border border-black p-2 text-left uppercase font-medium">
                                    {receiving.item_description}
                                </td>
                                <td className="border border-black p-2 text-lg font-bold">
                                    {receiving.quantity_ordered}
                                </td>
                                <td className="border border-black p-2 text-lg font-bold">
                                    {receiving.quantity_received}
                                </td>
                                <td className="border border-black p-2 font-bold">
                                    {receiving.unit_price
                                        ? Number(receiving.unit_price).toLocaleString('ms-MY', { minimumFractionDigits: 2 })
                                        : '—'}
                                </td>
                                <td className="border border-black p-2 font-bold">
                                    {receiving.unit_price
                                        ? Number(totalAmount).toLocaleString('ms-MY', { minimumFractionDigits: 2 })
                                        : '—'}
                                </td>
                                <td className="border border-black p-2"></td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Signature blocks ── */}
                    <div className="grid grid-cols-3 gap-6 mt-4">

                        {/* Sig 1 — Pegawai Penerima */}
                        <div className="space-y-1">
                            <p className="font-bold text-[10px]">...........................................................................</p>
                            <p className="font-bold">(Tandatangan Pegawai Penerima)</p>
                            <p>Nama : {receiving.receiver_name || '............................................'}</p>
                            <p>Jawatan : {receiving.receiver_position || '............................................'}</p>
                            <p>Fakulti/PTJ : CAIRO UTM</p>
                            <p>Tarikh : {new Date(receiving.created_at).toLocaleDateString('ms-MY')}</p>
                        </div>

                        {/* Sig 2 — Pegawai Bertanggungjawab */}
                        <div className="space-y-1">
                            <p className="font-bold text-[10px]">...........................................................................</p>
                            <p className="font-bold">(Tandatangan Pegawai Bertanggungjawab)</p>
                            <p>Nama : {receiving.technical_officer_name || '............................................'}</p>
                            <p>Jawatan : {receiving.technical_officer_position || '............................................'}</p>
                            <p>Fakulti/PTJ : CAIRO UTM</p>
                            <p>Tarikh : {new Date(receiving.created_at).toLocaleDateString('ms-MY')}</p>
                        </div>

                        {/* Nota */}
                        <div className="text-[10px] space-y-1">
                            <p className="font-bold">Nota :</p>
                            <p>Kegunaan di Fakulti/PTJ (3 salinan)</p>
                            <p>Salinan 1 - Jabatan/Makmal/Bahagian/Unit</p>
                            <p>Salinan 2 - Pejabat/Bahagian/Unit Pentadbiran</p>
                            <p>Salinan 3 - Pejabat Bendahari (Pembayaran)</p>
                        </div>
                    </div>

                    {/* ── Actions (hidden on print) ── */}
                    <div className="mt-10 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('receivings.kewpa1.download', receiving.id)}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-1A)
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