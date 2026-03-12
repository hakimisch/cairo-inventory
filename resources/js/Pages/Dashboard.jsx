import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';
import { Pie } from 'react-chartjs-2';

ChartJS.register(ArcElement, Tooltip, Legend);

export default function Dashboard({ auth, stats, chartData, highValueAssets }) {
    // Prepare data for Chart.js
    const data = {
        labels: chartData.map(item => item.category),
        datasets: [
            {
                data: chartData.map(item => item.total),
                backgroundColor: [
                    '#4f46e5', // Indigo
                    '#10b981', // Emerald
                    '#f59e0b', // Amber
                    '#ef4444', // Red
                    '#8b5cf6', // Violet
                ],
                borderWidth: 1,
            },
        ],
    };

    return (
        <AuthenticatedLayout
            header={<h2 className="font-semibold text-xl text-gray-800">System Overview</h2>}
        >
            <Head title="Dashboard" />

            {/* Stat Cards (Your existing code) */}
            <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <p className="text-sm text-gray-500 font-bold uppercase">Total Investment</p>
                    <p className="text-2xl font-black text-indigo-600">RM {Number(stats.total_value).toLocaleString()}</p>
                </div>
                {/* ... other cards ... */}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {/* LEFT COLUMN: Chart */}
            <div className="md:col-span-1 bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-fit">
                <h3 className="font-bold text-gray-700 mb-4 text-lg">Value by Category</h3>
                <div className="px-4">
                    <Pie data={data} options={{ plugins: { legend: { position: 'bottom' } } }} />
                </div>
            </div>

            {/* RIGHT COLUMN: Welcome + High Value List */}
            <div className="md:col-span-2 space-y-6">
                {/* Welcome Section */}
                <div className="bg-indigo-900 text-white rounded-xl p-8 shadow-lg">
                    <h3 className="text-2xl font-bold">Welcome back, {auth.user.name}</h3>
                    <p className="mt-2 text-indigo-200">
                        CAIRO UTM currently holds <strong>{stats.total_count}</strong> tracked assets.
                    </p>
                </div>

                {/* High-Value Assets List Section */}
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 className="font-bold text-gray-700 mb-4 text-lg text-indigo-900">Top High-Value Assets</h3>
                    <div className="space-y-4">
                        {highValueAssets.map(asset => (
                            <div key={asset.id} className="flex justify-between items-center border-b border-gray-50 pb-3 last:border-0">
                                <div>
                                    <p className="font-bold text-gray-900">{asset.name}</p>
                                    <p className="text-xs text-gray-500 font-mono uppercase">{asset.asset_tag}</p>
                                </div>
                                <p className="text-indigo-600 font-black">RM {Number(asset.purchase_price).toLocaleString()}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
        </AuthenticatedLayout>
    );
}