import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Kewpa1({ receiving }) {
    // Support both single receiving record and an array of items on the same DO
    const items = receiving.items && receiving.items.length > 0
        ? receiving.items
        : [{
            delivery_order_no: receiving.delivery_order_no,
            delivery_order_date: receiving.created_at,
            item_description: receiving.item_description,
            quantity_ordered: receiving.quantity_ordered,
            quantity_received: receiving.quantity_received,
            damage_description: receiving.damage_description,
            notes: receiving.notes,
        }];

    const hasDiscrepancyOrDamage = items.some(
        i => (i.quantity_ordered - i.quantity_received) !== 0 || i.damage_description
    );

    return (
        <AuthenticatedLayout header={
            <h2 className="print:hidden text-sm font-bold uppercase" style={{ color: '#5C001F' }}>
                Pratonton KEW.PA-1 — Borang Laporan Penerimaan Aset Alih Universiti
            </h2>
        }>
            <Head title="KEW.PA-1 Borang Laporan Penerimaan Aset Alih Universiti" />

            <style>{`
                @media print {
                    .print\\:hidden { display: none !important; }
                    body { margin: 0; }
                }
                @media (max-width: 767px) {
                    .responsive-table thead { display: none; }
                    .responsive-table tr {
                        display: block;
                        margin-bottom: 10px;
                        border: 1px solid #d1d5db;
                        border-radius: 6px;
                        padding: 10px;
                        background: white;
                    }
                    .responsive-table td {
                        display: block;
                        text-align: right;
                        padding: 5px 0;
                        border: none !important;
                    }
                    .responsive-table td::before {
                        content: attr(data-label);
                        float: left;
                        font-weight: 700;
                        text-transform: uppercase;
                        font-size: 9px;
                        color: #8A8480;
                        letter-spacing: 0.06em;
                    }
                    .responsive-table .empty-cell::before { content: none; }
                }
            `}</style>

            <div className="py-8 bg-gray-100 min-h-screen">
                <div className="max-w-5xl mx-auto bg-white shadow-lg p-10 font-serif text-[11px] leading-tight text-black">

                    {/* ── Header ── */}
                    <div className="flex justify-between items-start mb-1">
                        <div />
                        <div className="text-right">
                            <div className="font-bold text-[12px]">KEW.PA-1</div>
                        </div>
                    </div>

                    <div className="text-center font-bold text-[13px] mb-1 uppercase">
                        UNIVERSITI TEKNOLOGI MALAYSIA
                    </div>
                    <div className="text-center font-bold text-[12px] mb-1 uppercase">
                        BORANG LAPORAN PENERIMAAN ASET ALIH UNIVERSITI
                    </div>
                    <div className="text-center text-[10px] mb-4 italic">
                        (Hendaklah diisi dalam 2 salinan jika terdapat kerosakan/perselisihan)
                    </div>

                    {/* ── Supplier info ── */}
                    <table className="w-full border-collapse border border-black mb-6">
                        <tbody>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[30%]">Nama Pembekal</td>
                                <td className="border border-black p-2 uppercase font-bold" colSpan="3">
                                    {receiving.supplier_name}
                                </td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50 align-top" rowSpan="2">Alamat Pembekal</td>
                                <td className="border border-black p-2 text-[10px] align-top" rowSpan="2" style={{ whiteSpace: 'pre-wrap' }}>
                                    {receiving.supplier_address}
                                </td>
                                <td className="border border-black p-2 font-bold bg-gray-50 w-[20%]">No. Telefon</td>
                                <td className="border border-black p-2 w-[20%]">
                                    {receiving.supplier_phone || '—'}
                                </td>
                            </tr>
                            <tr>
                                <td className="border border-black p-2 font-bold bg-gray-50">No. Faks</td>
                                <td className="border border-black p-2">
                                    {receiving.supplier_fax || '—'}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    {/* ── Items table ── */}
                    <table className="w-full border-collapse border border-black mb-8 text-center text-[10px] responsive-table">
                        <thead>
                            <tr className="font-bold bg-gray-50">
                                <th className="border border-black p-2 w-8" rowSpan="2">Bil</th>
                                <th className="border border-black p-2 w-32" rowSpan="2">Nota Hantaran<br/>(No. &amp; Tarikh)</th>
                                <th className="border border-black p-2 text-left" rowSpan="2">Nama Aset</th>
                                <th className="border border-black p-2" colSpan="3">Kuantiti</th>
                                <th className="border border-black p-2 w-32" rowSpan="2">Perihal Kerosakan</th>
                                <th className="border border-black p-2 w-24" rowSpan="2">Catatan</th>
                            </tr>
                            <tr className="font-bold bg-gray-50">
                                <th className="border border-black p-1 w-12">Dipesan</th>
                                <th className="border border-black p-1 w-12">Diterima</th>
                                <th className="border border-black p-1 w-16">Perselisihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            {items.map((item, idx) => {
                                const diff = (item.quantity_ordered ?? 0) - (item.quantity_received ?? 0);
                                return (
                                    <tr key={idx} className="h-16 align-top">
                                        <td data-label="Bil" className="border border-black p-2">{idx + 1}</td>
                                        <td data-label="Nota Hantaran" className="border border-black p-2 font-mono">
                                            {item.delivery_order_no}<br/>
                                            {new Date(item.delivery_order_date ?? receiving.created_at).toLocaleDateString('ms-MY')}
                                        </td>
                                        <td data-label="Nama Aset" className="border border-black p-2 text-left uppercase font-medium">
                                            {item.item_description}
                                        </td>
                                        <td data-label="Dipesan" className="border border-black p-2 font-bold">{item.quantity_ordered}</td>
                                        <td data-label="Diterima" className="border border-black p-2 font-bold">{item.quantity_received}</td>
                                        <td data-label="Perselisihan" className={`border border-black p-2 font-bold ${diff !== 0 ? 'text-red-600' : ''}`}>
                                            {diff !== 0 ? diff : '-'}
                                        </td>
                                        <td data-label="Kerosakan" className="border border-black p-2 uppercase text-red-600">
                                            {item.damage_description || '-'}
                                        </td>
                                        <td data-label="Catatan" className="border border-black p-2">{item.notes || '-'}</td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>

                    {/* ── Signature blocks ── */}
                    <div className="grid grid-cols-2 gap-10 mt-4">

                        {/* Sig 1 — Penerima */}
                        <div className="space-y-1">
                            <p className="font-bold text-[10px]">...........................................................................</p>
                            <p className="font-bold">Tandatangan Penerima</p>
                            <p>Nama &nbsp;&nbsp;&nbsp;: {receiving.receiver_name || '............................................'}</p>
                            <p>Jawatan : {receiving.receiver_position || '............................................'}</p>
                            <p>Tarikh &nbsp;&nbsp;: {new Date(receiving.created_at).toLocaleDateString('ms-MY')}</p>
                        </div>

                        {/* Sig 2 — Pegawai Bertanggungjawab */}
                        <div className="space-y-1">
                            <p className="font-bold text-[10px]">...........................................................................</p>
                            <p className="font-bold">Tandatangan Pegawai Bertanggungjawab</p>
                            <p>Nama &nbsp;&nbsp;&nbsp;: {receiving.technical_officer_name || '............................................'}</p>
                            <p>Jawatan : {receiving.technical_officer_position || '............................................'}</p>
                            <p>Tarikh &nbsp;&nbsp;: {new Date(receiving.created_at).toLocaleDateString('ms-MY')}</p>
                        </div>
                    </div>

                    {/* ── Copy instruction note ── */}
                    <div className="mt-8 text-[10px] space-y-0.5 border-t border-gray-300 pt-3">
                        <p className="font-bold">Nota :</p>
                        {hasDiscrepancyOrDamage ? (
                            <p>Borang ini hendaklah disediakan dalam <strong>2 salinan</strong> memandangkan terdapat kerosakan atau perselisihan kuantiti.</p>
                        ) : (
                            <p>Borang ini disediakan dalam <strong>1 salinan</strong> jika tiada kerosakan atau perselisihan.</p>
                        )}
                    </div>

                    {/* ── Actions (hidden on print) ── */}
                    <div className="mt-8 flex justify-end gap-3 print:hidden">
                        <a
                            href={route('receivings.kewpa1.download', receiving.id)}
                            className="px-6 py-2 rounded font-bold shadow text-white"
                            style={{ background: '#5C001F' }}
                        >
                            Muat Turun PDF (KEW.PA-1)
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
