import { useState } from 'react'; 
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function ReceivingIndex({ receivings }) {
    const [selectedItem, setSelectedItem] = useState(null);
    
    const { data, setData, post, processing, reset } = useForm({
        unit_price: '',
        category: '',
        location: '',
    });

    const handleAcceptClick = (item) => {
        setSelectedItem(item);
        reset();
    };

    const handleAcceptSubmit = (e) => {
        e.preventDefault();
        post(route('receivings.accept', selectedItem.id), {
            onSuccess: () => setSelectedItem(null),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">Pengurusan Penerimaan (KEW.PA-1)</h2>
                    <Link 
                        href={route('receivings.create')} 
                        className="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-indigo-700"
                    >
                        + Daftar Penerimaan Baru
                    </Link>
                </div>
            }
        >
            <Head title="Receiving Inventory" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr className="text-xs font-bold text-gray-500 uppercase">
                                    <th className="px-6 py-3 text-left">No. Penerimaan</th>
                                    <th className="px-6 py-3 text-left">Pembekal</th>
                                    <th className="px-6 py-3 text-left">Item</th>
                                    <th className="px-6 py-3 text-left">Status</th>
                                    <th className="px-6 py-3 text-right">Tindakan</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200">
                                {receivings.map((item) => (
                                    <tr key={item.id} className="hover:bg-gray-50">
                                        <td className="px-6 py-4 font-mono text-sm">{item.receive_no}</td>
                                        <td className="px-6 py-4 text-sm">{item.supplier_name}</td>
                                        <td className="px-6 py-4 text-sm font-medium">{item.item_description}</td>
                                        <td className="px-6 py-4">
                                            <span className={`px-2 py-1 text-xs font-bold rounded-full ${
                                                item.status === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                            }`}>
                                                {item.status}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right space-x-3">
                                            <Link 
                                                href={route('receivings.kewpa1', item.id)}
                                                className="text-indigo-600 hover:underline text-sm font-medium"
                                            >
                                                Lihat KEW.PA-1
                                            </Link>
                                            
                                            {item.status === 'pending' && (
                                                <button 
                                                    onClick={() => handleAcceptClick(item)}
                                                    className="bg-green-600 text-white px-3 py-1.5 rounded-md text-sm font-bold hover:bg-green-700 transition"
                                                >
                                                    Terima & Daftar Aset
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Acceptance Modal (KEW.PA-3 Precursor) */}
            {selectedItem && (
                <div className="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-xl shadow-xl max-w-md w-full p-8">
                        <h3 className="text-lg font-bold text-gray-900 mb-2">Sahkan Penerimaan</h3>
                        <p className="text-sm text-gray-500 mb-6">Sila lengkapkan butiran perolehan untuk menjana <span className="font-bold text-indigo-600">KEW.PA-3</span>.</p>
                        
                        <form onSubmit={handleAcceptSubmit} className="space-y-4">
                            <div>
                                <label className="block text-xs font-black uppercase text-gray-400 mb-1">Harga Seunit (RM)</label>
                                <input 
                                    type="number" 
                                    required
                                    className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                                    value={data.unit_price}
                                    onChange={e => setData('unit_price', e.target.value)}
                                    placeholder="e.g. 50000"
                                />
                            </div>

                            <div>
                                <label className="block text-xs font-black uppercase text-gray-400 mb-1">Kategori Aset</label>
                                <select 
                                    required
                                    className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                                    value={data.category}
                                    onChange={e => setData('category', e.target.value)}
                                >
                                    <option value="">Pilih Kategori...</option>
                                    <option value="Server">Server</option>
                                    <option value="Workstation">Workstation</option>
                                    <option value="GPU Node">GPU Node</option>
                                    <option value="Sensor">Sensor</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-xs font-black uppercase text-gray-400 mb-1">Lokasi Penempatan</label>
                                <input 
                                    type="text" 
                                    required
                                    className="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500"
                                    value={data.location}
                                    onChange={e => setData('location', e.target.value)}
                                    placeholder="e.g. Makmal AI, Aras 2"
                                />
                            </div>

                            <div className="flex justify-end gap-3 mt-8">
                                <button 
                                    type="button"
                                    onClick={() => setSelectedItem(null)}
                                    className="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700"
                                >
                                    Batal
                                </button>
                                <button 
                                    type="submit"
                                    disabled={processing}
                                    className="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold shadow-md hover:bg-indigo-700 disabled:opacity-50"
                                >
                                    {processing ? 'Menjana...' : 'Sahkan & Daftar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}