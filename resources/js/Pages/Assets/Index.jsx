import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { Link } from '@inertiajs/react';

export default function Index({ assets, totalValue }) {
    const [search, setSearch] = useState('');

    // Filter assets locally for the prototype demo
    const filteredAssets = assets.filter(asset => 
        asset.name.toLowerCase().includes(search.toLowerCase()) ||
        asset.asset_tag.toLowerCase().includes(search.toLowerCase())
    );

    return (
        <AuthenticatedLayout
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">CAIRO Inventory Management</h2>}
        >
            <Head title="Inventory" />

            <div className="py-12 bg-gray-50">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    
                    {/* Top Bar: Stats & Search */}
                    <div className="flex flex-col md:flex-row justify-between items-end mb-6 gap-4">
                        <div className="bg-white p-6 rounded-lg shadow-sm border-l-4 border-indigo-600 flex-1">
                            <p className="text-xs font-bold text-gray-500 uppercase">Total Portfolio Value</p>
                            <p className="text-3xl font-black text-gray-900">RM {Number(totalValue).toLocaleString()}</p>
                        </div>
                        
                        <div className="w-full md:w-1/3">
                            <input 
                                type="text"
                                placeholder="Search by name or tag..."
                                className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                onChange={(e) => setSearch(e.target.value)}
                            />
                        </div>
                    </div>

                    {/* Table */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table className="min-w-full divide-y divide-gray-200">
                            {/* ... (Your previous table head) ... */}
                            
                            <tbody className="bg-white divide-y divide-gray-200">
                                {filteredAssets.map((asset) => (
                                    <tr key={asset.id} className="hover:bg-gray-50 transition">
                                        <td className="px-6 py-4 font-mono font-bold text-indigo-600 uppercase">
                                            {asset.asset_tag}
                                        </td>
                                        <td className="px-6 py-4 text-gray-900 font-medium">
                                            {asset.name}
                                        </td>
                                        <td className="px-6 py-4 text-gray-600">
                                            RM {Number(asset.purchase_price).toLocaleString()}
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className={`px-2 py-1 text-xs font-bold rounded-full ${
                                                asset.status === 'active' ? 'bg-green-100 text-green-700' : 
                                                asset.status === 'repair' ? 'bg-yellow-100 text-yellow-700' : 
                                                'bg-red-100 text-red-700'
                                            }`}>
                                                {asset.status}
                                            </span>
                                        </td>
                                        {/*ACTIONS COLUMN */}
                                        <td className="px-6 py-4 text-right text-sm font-medium space-x-2 whitespace-nowrap">
                                            <Link
                                                href={route('assets.kewpa3', asset.id)}
                                                className="text-emerald-600 hover:text-emerald-900 bg-emerald-50 px-3 py-1 rounded-md transition"
                                            >
                                                View
                                            </Link>

                                            <a 
                                                href={route('assets.kewpa3.download', asset.id)} 
                                                className="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded-md transition shadow-sm inline-flex items-center"
                                            >
                                                <span>PDF</span>
                                            </a>

                                            <Link
                                                href={route('assets.kewpa2', asset.id)}
                                                className="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1 rounded-md transition"
                                            >
                                                Reject
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}