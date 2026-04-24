<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\AdminDashboardController as AdminDashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DamageReportController;
use App\Http\Controllers\AssetInspectionController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\AssetDisposalController;
use App\Http\Controllers\CommitteeAppointmentController;
use App\Http\Controllers\AssetLossReportController;
use App\Http\Controllers\AssetTransferController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ─── Admin Group ───
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard: /admin/dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin Users
    Route::resource('users', AdminUserController::class)->except(['create', 'show', 'edit']);
});

// ── Admin dashboard ───────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
});
 
// ── Annual report forms (admin-only) ─────────────────────────────────────────
Route::middleware(['auth', 'verified', 'admin'])->prefix('reports')->name('reports.')->group(function () {
 
    // KEW.PA-4 — Senarai Harta Tetap
    Route::get('/kewpa4',          [ReportController::class, 'kewpa4'])->name('kewpa4');
    Route::get('/kewpa4/download', [ReportController::class, 'downloadKewpa4'])->name('kewpa4.download');
 
    // KEW.PA-5 — Senarai Inventori
    Route::get('/kewpa5',          [ReportController::class, 'kewpa5'])->name('kewpa5');
    Route::get('/kewpa5/download', [ReportController::class, 'downloadKewpa5'])->name('kewpa5.download');
 
    // KEW.PA-8 — Laporan Tahunan
    Route::get('/kewpa8',          [ReportController::class, 'kewpa8'])->name('kewpa8');
    Route::get('/kewpa8/download', [ReportController::class, 'downloadKewpa8'])->name('kewpa8.download');
});
 

// ─── Regular User Dashboard ───
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Asset Management Group (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store'); // For the Add Asset form
    Route::post('/assets/{asset}/placements', [AssetController::class, 'storePlacement'])->name('assets.placements.store');
    Route::get('/assets/{asset}/kewpa2', [AssetController::class, 'kewpa2'])->name('assets.kewpa2');
    Route::get('/assets/{asset}/kewpa3', [AssetController::class, 'kewpa3'])->name('assets.kewpa3');
    // Aduan Kerosakan (KEW.PA-9)
    Route::post('/assets/{asset}/damage-reports', [DamageReportController::class, 'store'])->name('assets.damage-reports.store');
    Route::get('/damage-reports/{damageReport}/kewpa9/download', [DamageReportController::class, 'downloadKewpa9'])->name('damage-reports.kewpa9.download');
    Route::post('/assets/{asset}/inspections', [AssetInspectionController::class, 'store'])->name('assets.inspections.store');

    // ── Phase 2: New Routes ──────────────────────────────────────────────────

    // KEW.PA-13/14 — Penyelenggaraan (Maintenance)
    Route::post('/assets/{asset}/maintenances', [AssetMaintenanceController::class, 'store'])->name('assets.maintenances.store');
    Route::put('/assets/{asset}/maintenances/{maintenance}', [AssetMaintenanceController::class, 'update'])->name('assets.maintenances.update');
    Route::delete('/assets/{asset}/maintenances/{maintenance}', [AssetMaintenanceController::class, 'destroy'])->name('assets.maintenances.destroy');

    // KEW.PA-17/18/19 — Pelupusan (Disposal)
    Route::post('/assets/{asset}/disposals', [AssetDisposalController::class, 'store'])->name('assets.disposals.store');
    Route::put('/assets/{asset}/disposals/{disposal}', [AssetDisposalController::class, 'update'])->name('assets.disposals.update');
    Route::delete('/assets/{asset}/disposals/{disposal}', [AssetDisposalController::class, 'destroy'])->name('assets.disposals.destroy');

    // KEW.PA-28→32 — Kehilangan (Loss Report)
    Route::post('/assets/{asset}/loss-reports', [AssetLossReportController::class, 'store'])->name('assets.loss-reports.store');
    Route::put('/assets/{asset}/loss-reports/{lossReport}', [AssetLossReportController::class, 'update'])->name('assets.loss-reports.update');
    Route::delete('/assets/{asset}/loss-reports/{lossReport}', [AssetLossReportController::class, 'destroy'])->name('assets.loss-reports.destroy');

    // KEW.PA-6 — Daftar Pergerakan (Transfer/Movement)
    Route::post('/assets/{asset}/transfers', [AssetTransferController::class, 'store'])->name('assets.transfers.store');
    Route::put('/assets/{asset}/transfers/{transfer}', [AssetTransferController::class, 'update'])->name('assets.transfers.update');
    Route::delete('/assets/{asset}/transfers/{transfer}', [AssetTransferController::class, 'destroy'])->name('assets.transfers.destroy');
});

// KEW.PA-15/29 — Committee Appointments (standalone, polymorphic)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/committee-appointments', [CommitteeAppointmentController::class, 'store'])->name('committee-appointments.store');
    Route::put('/committee-appointments/{committeeAppointment}', [CommitteeAppointmentController::class, 'update'])->name('committee-appointments.update');
    Route::delete('/committee-appointments/{committeeAppointment}', [CommitteeAppointmentController::class, 'destroy'])->name('committee-appointments.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


Route::get('/receivings', [AssetController::class, 'receivingIndex'])->name('receivings.index');
Route::get('/receivings/{receiving}/kewpa1', [AssetController::class, 'kewpa1'])->name('receivings.kewpa1');
Route::post('/receivings/{receiving}/accept', [AssetController::class, 'acceptReceiving'])->name('receivings.accept');
Route::post('/receivings/{receiving}/reject', [AssetController::class, 'rejectReceiving'])->name('receivings.reject');

Route::get('/receivings/create', [AssetController::class, 'createReceiving'])->name('receivings.create');
Route::post('/receivings', [AssetController::class, 'storeReceiving'])->name('receivings.store');

Route::get('/receivings/{receiving}/kewpa1/download', [AssetController::class, 'downloadKewpa1'])->name('receivings.kewpa1.download');
Route::get('/assets/{asset}/kewpa2/download', [AssetController::class, 'downloadKewpa2'])->name('assets.kewpa2.download');
Route::get('/assets/{asset}/kewpa3/download', [AssetController::class, 'downloadKewpa3'])->name('assets.kewpa3.download');

require __DIR__.'/auth.php';