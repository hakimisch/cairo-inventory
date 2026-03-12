import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link, usePage } from '@inertiajs/react';

export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;

    return (
        <div className="min-h-screen bg-gray-100 flex">
            {/* Sidebar */}
            <aside className="w-64 bg-indigo-900 text-white hidden md:flex flex-col sticky top-0 h-screen">
                <div className="p-6 flex items-center gap-3">
                    <ApplicationLogo className="block h-9 w-auto fill-current text-white" />
                    <span className="font-bold text-lg tracking-wider">CAIRO INV</span>
                </div>

                <nav className="flex-1 px-4 mt-4 space-y-6">
                    {/* General Section */}
                    <div>
                        <p className="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Main</p>
                        <Link href={route('dashboard')} className={`block p-3 rounded-lg hover:bg-indigo-800 transition ${route().current('dashboard') ? 'bg-indigo-800' : ''}`}>
                            📊 Dashboard
                        </Link>
                    </div>

                    {/* Operational Section (KEW.PA-1 Workflow) */}
                    <div>
                        <p className="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Logistics (KEW.PA-1)</p>
                        <div className="space-y-1">
                            <Link href={route('receivings.create')} className={`block p-3 rounded-lg hover:bg-indigo-800 transition ${route().current('receivings.create') ? 'bg-indigo-800' : ''}`}>
                                ➕ New Delivery
                            </Link>
                            <Link href={route('receivings.index')} className={`block p-3 rounded-lg hover:bg-indigo-800 transition ${route().current('receivings.index') ? 'bg-indigo-800' : ''}`}>
                                🚚 Receiving List
                            </Link>
                        </div>
                    </div>

                    {/* Registry Section (KEW.PA-3 Inventory) */}
                    <div>
                        <p className="px-3 text-xs font-semibold text-indigo-300 uppercase tracking-wider mb-2">Registry (KEW.PA-3)</p>
                        <Link href={route('assets.index')} className={`block p-3 rounded-lg hover:bg-indigo-800 transition ${route().current('assets.*') ? 'bg-indigo-800' : ''}`}>
                            📦 Assets Inventory
                        </Link>
                    </div>
                </nav>

                {/* User Footer */}
                <div className="p-4 border-t border-indigo-800 bg-indigo-950">
                    <p className="text-xs text-indigo-300 italic mb-1 uppercase">Logged in as</p>
                    <div className="flex justify-between items-center">
                        <span className="text-sm font-bold truncate">{user.name}</span>
                        <Link href={route('logout')} method="post" as="button" className="text-xs text-red-400 hover:text-red-300 font-bold">
                            Logout
                        </Link>
                    </div>
                </div>
            </aside>

            {/* Main Content Area */}
            <div className="flex-1 flex flex-col overflow-hidden">
                <header className="bg-white shadow-sm py-4 px-8 border-b border-gray-200">
                    <div className="flex justify-between items-center">
                        {header}
                        <div className="text-xs text-gray-400 font-medium">
                            CAIRO Asset Management System v1.0
                        </div>
                    </div>
                </header>
                <main className="flex-1 overflow-y-auto bg-gray-50 p-8">
                    {children}
                </main>
            </div>
        </div>
    );
}