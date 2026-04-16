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