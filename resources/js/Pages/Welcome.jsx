import { Head, Link } from '@inertiajs/react';

export default function Welcome({ auth, canLogin, canRegister, laravelVersion, phpVersion }) {
    const kewpaForms = [
        { id: 'KEW.PA-1', name: 'Penerimaan Aset', status: '✅' },
        { id: 'KEW.PA-1A', name: 'Penerimaan Aset (Lanjutan)', status: '✅' },
        { id: 'KEW.PA-2', name: 'Daftar Aset', status: '✅' },
        { id: 'KEW.PA-3', name: 'Kad Daftar Aset', status: '✅' },
        { id: 'KEW.PA-4', name: 'Laporan Aset', status: '✅' },
        { id: 'KEW.PA-5', name: 'Laporan Aset (Kategori)', status: '✅' },
        { id: 'KEW.PA-6', name: 'Pelupusan Aset', status: '✅' },
        { id: 'KEW.PA-7', name: 'Laporan Aset (Lokasi)', status: '✅' },
        { id: 'KEW.PA-8', name: 'Laporan Aset (Campus)', status: '✅' },
        { id: 'KEW.PA-9', name: 'Laporan Kerosakan', status: '✅' },
        { id: 'KEW.PA-9A', name: 'Sijil Pemeriksaan', status: '✅' },
        { id: 'KEW.PA-10', name: 'Pindah Aset', status: '✅' },
        { id: 'KEW.PA-12', name: 'Laporan Aset (Nilai)', status: '✅' },
        { id: 'KEW.PA-13', name: 'Penyelenggaraan', status: '✅' },
        { id: 'KEW.PA-16', name: 'Pelupusan Kenderaan', status: '✅' },
        { id: 'KEW.PA-17', name: 'Perakuan Pelupusan', status: '✅' },
        { id: 'KEW.PA-20', name: 'Laporan Aset (Ringkasan)', status: '✅' },
        { id: 'KEW.PA-21', name: 'Tawaran Jualan Aset', status: '✅' },
        { id: 'KEW.PA-22', name: 'Sebutharga Jualan Aset', status: '✅' },
        { id: 'KEW.PA-23', name: 'Lelongan Jualan Aset', status: '✅' },
        { id: 'KEW.PA-24', name: 'Keputusan Jualan', status: '✅' },
        { id: 'KEW.PA-25', name: 'Laporan Jualan', status: '✅' },
        { id: 'KEW.PA-26', name: 'Perakuan (T/S/L)', status: '✅' },
        { id: 'KEW.PA-27', name: 'Perakuan Pelupusan', status: '✅' },
        { id: 'KEW.PA-27A', name: 'Perakuan Lupus', status: '✅' },
        { id: 'KEW.PA-28', name: 'Laporan Kehilangan', status: '✅' },
    ];

    const features = [
        {
            icon: (
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            ),
            title: 'Asset Lifecycle Management',
            description: 'Complete end-to-end tracking from receiving and registration to placement, maintenance, transfer, and disposal.',
        },
        {
            icon: (
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            ),
            title: '26 KEW.PA Forms',
            description: 'Full coverage of all KEW.PA standard forms with digital generation, PDF export, and print-ready formatting.',
        },
        {
            icon: (
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            ),
            title: 'Comprehensive Reporting',
            description: 'Multi-dimensional reports by category, location, campus, value, and custom date ranges with real-time data.',
        },
        {
            icon: (
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            ),
            title: 'Role-Based Access',
            description: 'Secure multi-tier access with Admin, Staff, and User roles. Granular permissions for sensitive operations.',
        },
        {
            icon: (
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            ),
            title: 'Advanced Filtering',
            description: 'Powerful search and filter capabilities across all modules — by status, category, campus, date range, and more.',
        },
        {
            icon: (
                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
            ),
            title: 'PDF Export & Printing',
            description: 'One-click PDF generation for all KEW.PA forms using Browsershot, with proper formatting and signature blocks.',
        },
    ];

    const stats = [
        { label: 'KEW.PA Forms', value: '26' },
        { label: 'Asset Lifecycle Stages', value: '10+' },
        { label: 'User Roles', value: '3' },
        { label: 'Campuses Supported', value: 'Multi' },
    ];

    return (
        <>
            <Head title="Welcome — CAIRO Inventory System" />

            <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 text-white">
                {/* Navigation */}
                <nav className="relative z-10 flex items-center justify-between px-6 py-4 sm:px-12">
                    <div className="flex items-center gap-3">
                        <div className="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white font-bold text-sm">
                            CI
                        </div>
                        <span className="text-lg font-semibold tracking-tight">
                            CAIRO <span className="text-blue-400">Inventory</span>
                        </span>
                    </div>
                    <div className="flex items-center gap-4">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <>
                                {canLogin && (
                                    <Link
                                        href={route('login')}
                                        className="text-sm font-medium text-slate-300 transition hover:text-white"
                                    >
                                        Log in
                                    </Link>
                                )}
                                {canRegister && (
                                    <Link
                                        href={route('register')}
                                        className="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                    >
                                        Register
                                    </Link>
                                )}
                            </>
                        )}
                    </div>
                </nav>

                {/* Hero Section */}
                <section className="relative px-6 pt-20 pb-24 sm:px-12 lg:pt-28 lg:pb-32">
                    <div className="mx-auto max-w-5xl text-center">
                        <div className="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-500/10 px-4 py-1.5 text-sm text-blue-300 ring-1 ring-blue-500/20">
                            <span className="h-2 w-2 rounded-full bg-blue-400 animate-pulse" />
                            Universiti Teknologi Malaysia
                        </div>
                        <h1 className="text-4xl font-bold tracking-tight sm:text-5xl lg:text-6xl">
                            CAIRO{' '}
                            <span className="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                                Asset Inventory
                            </span>{' '}
                            System
                        </h1>
                        <p className="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-slate-300">
                            Comprehensive Asset Inventory and Reporting Online system for managing
                            university assets across the entire lifecycle — from procurement and
                            registration to maintenance, disposal, and reporting.
                        </p>
                        <div className="mt-10 flex items-center justify-center gap-4">
                            {!auth.user && canRegister && (
                                <Link
                                    href={route('register')}
                                    className="rounded-lg bg-blue-600 px-8 py-3 text-base font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                >
                                    Get Started
                                </Link>
                            )}
                            {auth.user ? (
                                <Link
                                    href={route('dashboard')}
                                    className="rounded-lg bg-blue-600 px-8 py-3 text-base font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                >
                                    Go to Dashboard
                                </Link>
                            ) : null}
                            <a
                                href="#features"
                                className="rounded-lg border border-slate-600 px-8 py-3 text-base font-medium text-slate-300 transition hover:border-slate-500 hover:text-white"
                            >
                                Learn More
                            </a>
                        </div>
                    </div>

                    {/* Quick Stats */}
                    <div className="mx-auto mt-16 grid max-w-4xl grid-cols-2 gap-4 sm:grid-cols-4">
                        {stats.map((stat) => (
                            <div
                                key={stat.label}
                                className="rounded-xl border border-slate-700/50 bg-slate-800/40 p-4 text-center backdrop-blur-sm"
                            >
                                <div className="text-2xl font-bold text-blue-400">{stat.value}</div>
                                <div className="mt-1 text-xs font-medium text-slate-400 uppercase tracking-wider">{stat.label}</div>
                            </div>
                        ))}
                    </div>
                </section>

                {/* Features Section */}
                <section id="features" className="px-6 pb-24 sm:px-12">
                    <div className="mx-auto max-w-6xl">
                        <div className="mb-12 text-center">
                            <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
                                Comprehensive{' '}
                                <span className="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                                    Asset Management
                                </span>
                            </h2>
                            <p className="mx-auto mt-4 max-w-2xl text-slate-400">
                                Everything you need to manage university assets efficiently, from
                                registration to disposal, all in one integrated platform.
                            </p>
                        </div>

                        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            {features.map((feature) => (
                                <div
                                    key={feature.title}
                                    className="group rounded-xl border border-slate-700/50 bg-slate-800/30 p-6 backdrop-blur-sm transition hover:border-blue-500/30 hover:bg-slate-800/50"
                                >
                                    <div className="mb-4 inline-flex rounded-lg bg-blue-500/10 p-3 text-blue-400 ring-1 ring-blue-500/20">
                                        {feature.icon}
                                    </div>
                                    <h3 className="text-lg font-semibold text-white">{feature.title}</h3>
                                    <p className="mt-2 text-sm leading-relaxed text-slate-400">
                                        {feature.description}
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* KEW.PA Coverage Section */}
                <section className="px-6 pb-24 sm:px-12">
                    <div className="mx-auto max-w-6xl">
                        <div className="mb-12 text-center">
                            <h2 className="text-3xl font-bold tracking-tight sm:text-4xl">
                                KEW.PA{' '}
                                <span className="bg-gradient-to-r from-blue-400 to-cyan-300 bg-clip-text text-transparent">
                                    Form Coverage
                                </span>
                            </h2>
                            <p className="mx-auto mt-4 max-w-2xl text-slate-400">
                                All 26 KEW.PA standard forms are fully implemented with digital
                                generation, PDF export, and print-ready formatting.
                            </p>
                        </div>

                        <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            {kewpaForms.map((form) => (
                                <div
                                    key={form.id}
                                    className="flex items-center gap-3 rounded-lg border border-slate-700/30 bg-slate-800/20 px-4 py-3 transition hover:border-green-500/30 hover:bg-slate-800/40"
                                >
                                    <span className="text-sm">{form.status}</span>
                                    <div>
                                        <div className="text-sm font-medium text-white">{form.id}</div>
                                        <div className="text-xs text-slate-500">{form.name}</div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* CTA Section */}
                <section className="px-6 pb-24 sm:px-12">
                    <div className="mx-auto max-w-4xl">
                        <div className="rounded-2xl border border-blue-500/20 bg-gradient-to-br from-blue-600/10 to-cyan-600/10 p-8 text-center sm:p-12">
                            <h2 className="text-2xl font-bold tracking-tight sm:text-3xl">
                                Ready to streamline your asset management?
                            </h2>
                            <p className="mx-auto mt-4 max-w-xl text-slate-400">
                                Join UTM's comprehensive asset inventory system and experience
                                efficient, paperless asset lifecycle management.
                            </p>
                            <div className="mt-8 flex items-center justify-center gap-4">
                                {!auth.user && canRegister && (
                                    <Link
                                        href={route('register')}
                                        className="rounded-lg bg-blue-600 px-8 py-3 text-base font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                    >
                                        Create Account
                                    </Link>
                                )}
                                {auth.user && (
                                    <Link
                                        href={route('dashboard')}
                                        className="rounded-lg bg-blue-600 px-8 py-3 text-base font-medium text-white transition hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                                    >
                                        Go to Dashboard
                                    </Link>
                                )}
                                <Link
                                    href={route('login')}
                                    className="rounded-lg border border-slate-600 px-8 py-3 text-base font-medium text-slate-300 transition hover:border-slate-500 hover:text-white"
                                >
                                    {auth.user ? 'Dashboard' : 'Sign In'}
                                </Link>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Footer */}
                <footer className="border-t border-slate-800 px-6 py-8 sm:px-12">
                    <div className="mx-auto max-w-6xl">
                        <div className="flex flex-col items-center justify-between gap-4 sm:flex-row">
                            <div className="flex items-center gap-2 text-sm text-slate-500">
                                <div className="flex h-7 w-7 items-center justify-center rounded bg-blue-600 text-white font-bold text-xs">
                                    CI
                                </div>
                                <span>CAIRO Inventory System</span>
                            </div>
                            <div className="flex flex-col items-center gap-2 text-xs text-slate-600 sm:flex-row sm:gap-4">
                                <span>&copy; {new Date().getFullYear()} Universiti Teknologi Malaysia</span>
                                <span className="hidden sm:inline">·</span>
                                <span>CAIRO v{laravelVersion}</span>
                                <span className="hidden sm:inline">·</span>
                                <span>PHP {phpVersion}</span>
                                <span className="hidden sm:inline">·</span>
                                <span>Laravel {laravelVersion}</span>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}
