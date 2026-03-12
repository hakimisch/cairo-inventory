// resources/js/Pages/Assets/CreateReceiving.jsx

import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, Link } from '@inertiajs/react';

export default function CreateReceiving() {
    const { data, setData, post, processing, errors } = useForm({
        supplier_name: '',
        supplier_address: '',
        purchase_order_no: '',
        delivery_order_no: '',
        item_description: '',
        quantity_ordered: 1,
        quantity_received: 1,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('receivings.store'));
    };

    return (
        <AuthenticatedLayout header={<h2 className="font-semibold text-xl text-gray-800">Daftar Penerimaan Baru (KEW.PA-1)</h2>}>
            <Head title="Penerimaan Baru" />

            <div className="py-12">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8">
                    <form onSubmit={submit} className="bg-white p-8 rounded-xl shadow-sm space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="md:col-span-2">
                                <label className="block text-sm font-bold text-gray-700">Nama Pembekal</label>
                                <input type="text" className="mt-1 block w-full rounded-md border-gray-300" value={data.supplier_name} onChange={e => setData('supplier_name', e.target.value)} required />
                                {errors.supplier_name && <div className="text-red-500 text-xs mt-1">{errors.supplier_name}</div>}
                            </div>
                            
                            <div className="md:col-span-2">
                                <label className="block text-sm font-bold text-gray-700">Alamat Pembekal</label>
                                <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="2" value={data.supplier_address} onChange={e => setData('supplier_address', e.target.value)} required />
                            </div>

                            <div>
                                <label className="block text-sm font-bold text-gray-700">No. Pesanan Kerajaan (PO)</label>
                                <input type="text" className="mt-1 block w-full rounded-md border-gray-300" value={data.purchase_order_no} onChange={e => setData('purchase_order_no', e.target.value)} required />
                            </div>

                            <div>
                                <label className="block text-sm font-bold text-gray-700">No. Nota Hantaran (DO)</label>
                                <input type="text" className="mt-1 block w-full rounded-md border-gray-300" value={data.delivery_order_no} onChange={e => setData('delivery_order_no', e.target.value)} required />
                            </div>

                            <div className="md:col-span-2">
                                <label className="block text-sm font-bold text-gray-700">Keterangan Aset</label>
                                <textarea className="mt-1 block w-full rounded-md border-gray-300" rows="3" value={data.item_description} onChange={e => setData('item_description', e.target.value)} required />
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-4 border-t pt-6">
                            <Link href={route('receivings.index')} className="text-sm font-bold text-gray-500 hover:text-gray-700">Batal</Link>
                            <button type="submit" disabled={processing} className="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold shadow-md hover:bg-indigo-700 disabled:opacity-50 transition">
                                {processing ? 'Menyimpan...' : 'Daftar Penerimaan'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}