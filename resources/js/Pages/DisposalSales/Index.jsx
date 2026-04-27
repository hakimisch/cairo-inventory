import React from 'react';
import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ sales, filters }) {
    const { url } = usePage();

    const saleTypeBadge = (type) => {
        const colors = {
            Tawaran: 'bg-blue-100 text-blue-800',
            Sebutharga: 'bg-purple-100 text-purple-800',
            Lelongan: 'bg-orange-100 text-orange-800',
        };
        return <span className={`px-2 py-0.5 text-xs font-medium rounded-full ${colors[type] || 'bg-gray-100 text-gray-800'}`}>{type}</span>;
    };

    const statusBadge = (status) => {
        const colors = {
            draft: 'bg-gray-100 text-gray-800',
            active: 'bg-green-100 text-green-800',
            completed: 'bg-blue-100 text-blue-800',
            cancelled: 'bg-red-100 text-red-800',
        };
        return <span className={`px-2 py-0.5 text-xs font-medium rounded-full ${colors[status] || 'bg-gray-100 text-gray-800'}`}>{status}</span>;
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Disposal Sales — PA-21 to PA-27A
                </h2>
            }
        >
            <Head title="Disposal Sales" />

            <div className="py-6">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div className="p-4 border-b border-gray-200 flex items-center justify-between">
                            <div className="flex gap-2">
                                {['', 'Tawaran', 'Sebutharga', 'Lelongan'].map((type) => (
                                    <Link
                                        key={type}
                                        href={type ? `${url.split('?')[0]}?sale_type=${type}` : url.split('?')[0]}
                                        className={`px-3 py-1.5 text-xs font-medium rounded-md ${
                                            (filters?.sale_type || '') === type
                                                ? 'bg-indigo-100 text-indigo-700'
                                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                                        }`}
                                    >
                                        {type || 'All'}
                                    </Link>
                                ))}
                            </div>
                        </div>

                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200">
                                {sales.data?.length === 0 && (
                                    <tr><td colSpan={6} className="px-4 py-8 text-center text-gray-500">No disposal sales found.</td></tr>
                                )}
                                {sales.data?.map((sale) => (
                                    <tr key={sale.id} className="hover:bg-gray-50">
                                        <td className="px-4 py-3 text-sm font-medium text-gray-900">{sale.sale_reference}</td>
                                        <td className="px-4 py-3">{saleTypeBadge(sale.sale_type)}</td>
                                        <td className="px-4 py-3 text-sm text-gray-600">
                                            {sale.asset_disposal?.asset?.name ?? '-'}
                                            <span className="text-xs text-gray-400 ml-1">({sale.asset_disposal?.asset?.asset_tag ?? '-'})</span>
                                        </td>
                                        <td className="px-4 py-3 text-sm text-gray-600">{sale.sale_date ?? '-'}</td>
                                        <td className="px-4 py-3">{statusBadge(sale.status)}</td>
                                        <td className="px-4 py-3">
                                            <div className="flex gap-1.5 flex-wrap">
                                                <Link href={route('disposal-sales.show', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">View</Link>
                                                <Link href={route('disposal-sales.kewpa21', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-21</Link>
                                                <Link href={route('disposal-sales.kewpa22', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-22</Link>
                                                <Link href={route('disposal-sales.kewpa23', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-23</Link>
                                                <Link href={route('disposal-sales.kewpa24', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-24</Link>
                                                <Link href={route('disposal-sales.kewpa25', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-25</Link>
                                                <Link href={route('disposal-sales.kewpa26', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-26</Link>
                                                <Link href={route('disposal-sales.kewpa27', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-27</Link>
                                                <Link href={route('disposal-sales.kewpa27a', sale.id)} className="text-xs text-indigo-600 hover:text-indigo-900">PA-27A</Link>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>

                        {sales.links && (
                            <div className="p-4 border-t border-gray-200">
                                <div className="flex gap-1 justify-center" dangerouslySetInnerHTML={{ __html: sales.links }} />
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
