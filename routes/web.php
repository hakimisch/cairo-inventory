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
use App\Http\Controllers\AssetUpgradeController;
use App\Http\Controllers\VehicleDisposalAssessmentController;
use App\Http\Controllers\DisposalSaleController;
use App\Http\Controllers\DisposalSaleItemController;
use App\Http\Controllers\SaleBidController;

use App\Http\Controllers\KewpaDirectoryController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ─── Admin Group ───
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard: /admin/dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Admin Users
    Route::resource('users', AdminUserController::class)->except(['create', 'show', 'edit']);
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

    // KEW.PA-7 — Laporan Kedudukan Aset
    Route::get('/kewpa7',          [ReportController::class, 'kewpa7'])->name('kewpa7');
    Route::get('/kewpa7/download', [ReportController::class, 'downloadKewpa7'])->name('kewpa7.download');

    // KEW.PA-12 — Perakuan Pemeriksaan Tahunan
    Route::get('/kewpa12',          [ReportController::class, 'kewpa12'])->name('kewpa12');
    Route::get('/kewpa12/download', [ReportController::class, 'downloadKewpa12'])->name('kewpa12.download');

    // KEW.PA-20 — Laporan Pelupusan Aset Tahunan
    Route::get('/kewpa20',          [ReportController::class, 'kewpa20'])->name('kewpa20');
    Route::get('/kewpa20/download', [ReportController::class, 'downloadKewpa20'])->name('kewpa20.download');
});
 

// ─── Regular User Dashboard ───
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── KEW.PA Form Directory ───
Route::get('/kewpa', [KewpaDirectoryController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('kewpa.directory');

// Asset Management Group (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');

    // ── List / Index Pages for KEW.PA Forms (Phase B) ─────────────────────────
    Route::get('/transfers',          [AssetTransferController::class, 'index'])->name('transfers.index');
    Route::get('/damage-reports',     [DamageReportController::class, 'index'])->name('damage-reports.index');
    Route::get('/placements',         [AssetController::class, 'kewpa9aIndex'])->name('placements.index');
    Route::get('/inspections',        [AssetInspectionController::class, 'index'])->name('inspections.index');
    Route::get('/maintenances',       [AssetMaintenanceController::class, 'index'])->name('maintenances.index');
    Route::get('/vehicle-disposals',  [VehicleDisposalAssessmentController::class, 'indexAll'])->name('vehicle-disposals.index');
    Route::get('/disposals',          [AssetDisposalController::class, 'index'])->name('disposals.index');
    Route::get('/loss-reports',       [AssetLossReportController::class, 'index'])->name('loss-reports.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store'); // For the Add Asset form
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
    Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::post('/assets/{asset}/placements', [AssetController::class, 'storePlacement'])->name('assets.placements.store');
    Route::get('/assets/{asset}/kewpa2', [AssetController::class, 'kewpa2'])->name('assets.kewpa2');
    Route::get('/assets/{asset}/kewpa3', [AssetController::class, 'kewpa3'])->name('assets.kewpa3');
    // Aduan Kerosakan (KEW.PA-9)
    Route::post('/assets/{asset}/damage-reports', [DamageReportController::class, 'store'])->name('assets.damage-reports.store');
    Route::put('/assets/{asset}/damage-reports/{damageReport}', [DamageReportController::class, 'update'])->name('assets.damage-reports.update');
    Route::delete('/assets/{asset}/damage-reports/{damageReport}', [DamageReportController::class, 'destroy'])->name('assets.damage-reports.destroy');
    Route::get('/damage-reports/{damageReport}/kewpa9/download', [DamageReportController::class, 'downloadKewpa9'])->name('damage-reports.kewpa9.download');
    Route::post('/assets/{asset}/inspections', [AssetInspectionController::class, 'store'])->name('assets.inspections.store');
    Route::put('/assets/{asset}/inspections/{inspection}', [AssetInspectionController::class, 'update'])->name('assets.inspections.update');
    Route::delete('/assets/{asset}/inspections/{inspection}', [AssetInspectionController::class, 'destroy'])->name('assets.inspections.destroy');

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

    // Naiktaraf (Upgrades) — Bahagian B on KEW.PA-2/PA-3
    Route::post('/assets/{asset}/upgrades', [AssetUpgradeController::class, 'store'])->name('assets.upgrades.store');
    Route::put('/assets/{asset}/upgrades/{upgrade}', [AssetUpgradeController::class, 'update'])->name('assets.upgrades.update');
    Route::delete('/assets/{asset}/upgrades/{upgrade}', [AssetUpgradeController::class, 'destroy'])->name('assets.upgrades.destroy');

    // ── Phase 4: New View & Download Routes ──────────────────────────────────

    // KEW.PA-6 — Daftar Pergerakan Aset
    Route::get('/assets/{asset}/kewpa6',          [AssetController::class, 'kewpa6'])->name('assets.kewpa6');
    Route::get('/assets/{asset}/kewpa6/download', [AssetController::class, 'downloadKewpa6'])->name('assets.kewpa6.download');

    // KEW.PA-9A — Borang Pinjaman Aset
    Route::get('/assets/{asset}/kewpa9a',                [AssetController::class, 'kewpa9a'])->name('assets.kewpa9a');
    Route::get('/assets/{asset}/kewpa9a/download',       [AssetController::class, 'downloadKewpa9a'])->name('assets.kewpa9a.download');
    Route::get('/assets/{asset}/placements/{placement}/kewpa9a',       [AssetController::class, 'kewpa9a'])->name('assets.placements.kewpa9a');
    Route::get('/assets/{asset}/placements/{placement}/kewpa9a/download', [AssetController::class, 'downloadKewpa9a'])->name('assets.placements.kewpa9a.download');

    // KEW.PA-10 — Laporan Pemeriksaan Aset
    Route::get('/assets/{asset}/kewpa10',          [AssetController::class, 'kewpa10'])->name('assets.kewpa10');
    Route::get('/assets/{asset}/kewpa10/download', [AssetController::class, 'downloadKewpa10'])->name('assets.kewpa10.download');

    // KEW.PA-13/14 — Rekod Penyelenggaraan (PDF download)
    Route::get('/assets/{asset}/maintenances/kewpa13/download', [AssetMaintenanceController::class, 'downloadKewpa13'])->name('assets.maintenances.kewpa13.download');

    // KEW.PA-17/18/19 — Laporan Pelupusan (PDF download)
    Route::get('/assets/{asset}/disposals/kewpa17/download', [AssetDisposalController::class, 'downloadKewpa17'])->name('assets.disposals.kewpa17.download');

    // KEW.PA-28→32 — Laporan Kehilangan (PDF download)
    Route::get('/assets/{asset}/loss-reports/kewpa28/download', [AssetLossReportController::class, 'downloadKewpa28'])->name('assets.loss-reports.kewpa28.download');

    // ── Phase 5: PA-16 Vehicle Disposal Assessment ────────────────────────────

    // KEW.PA-16 — Perakuan Pelupusan Kenderaan
    Route::get('/assets/{asset}/vehicle-disposal', [VehicleDisposalAssessmentController::class, 'index'])->name('assets.vehicle-disposal.index');
    Route::post('/assets/{asset}/vehicle-disposal', [VehicleDisposalAssessmentController::class, 'store'])->name('assets.vehicle-disposal.store');
    Route::delete('/assets/{asset}/vehicle-disposal', [VehicleDisposalAssessmentController::class, 'destroy'])->name('assets.vehicle-disposal.destroy');
    Route::get('/assets/{asset}/vehicle-disposal/kewpa16', [VehicleDisposalAssessmentController::class, 'kewpa16'])->name('assets.vehicle-disposal.kewpa16');
    Route::get('/assets/{asset}/vehicle-disposal/kewpa16/download', [VehicleDisposalAssessmentController::class, 'downloadKewpa16'])->name('assets.vehicle-disposal.kewpa16.download');
});

// ── Phase 5: PA-21→27A Tender/Sebutharga/Lelongan ──────────────────────────

Route::middleware(['auth', 'verified'])->prefix('disposal-sales')->name('disposal-sales.')->group(function () {

    // Disposal Sale Management
    Route::get('/',             [DisposalSaleController::class, 'index'])->name('index');
    Route::post('/',            [DisposalSaleController::class, 'store'])->name('store');
    Route::get('/{sale}',       [DisposalSaleController::class, 'show'])->name('show');
    Route::put('/{sale}',       [DisposalSaleController::class, 'update'])->name('update');
    Route::delete('/{sale}',    [DisposalSaleController::class, 'destroy'])->name('destroy');

    // Sale Items (nested)
    Route::get('/{sale}/items',             [DisposalSaleItemController::class, 'index'])->name('items.index');
    Route::post('/{sale}/items',            [DisposalSaleItemController::class, 'store'])->name('items.store');
    Route::put('/{sale}/items/{item}',      [DisposalSaleItemController::class, 'update'])->name('items.update');
    Route::delete('/{sale}/items/{item}',   [DisposalSaleItemController::class, 'destroy'])->name('items.destroy');

    // Bids (nested under items)
    Route::get('/{sale}/items/{item}/bids',            [SaleBidController::class, 'index'])->name('items.bids.index');
    Route::post('/{sale}/items/{item}/bids',           [SaleBidController::class, 'store'])->name('items.bids.store');
    Route::put('/{sale}/items/{item}/bids/{bid}',      [SaleBidController::class, 'update'])->name('items.bids.update');
    Route::delete('/{sale}/items/{item}/bids/{bid}',   [SaleBidController::class, 'destroy'])->name('items.bids.destroy');

    // KEW.PA Form Views
    Route::get('/{sale}/kewpa21',   [DisposalSaleController::class, 'kewpa21'])->name('kewpa21');
    Route::get('/{sale}/kewpa22',   [DisposalSaleController::class, 'kewpa22'])->name('kewpa22');
    Route::get('/{sale}/kewpa23',   [DisposalSaleController::class, 'kewpa23'])->name('kewpa23');
    Route::get('/{sale}/kewpa24',   [DisposalSaleController::class, 'kewpa24'])->name('kewpa24');
    Route::get('/{sale}/kewpa25',   [DisposalSaleController::class, 'kewpa25'])->name('kewpa25');
    Route::get('/{sale}/kewpa26',   [DisposalSaleController::class, 'kewpa26'])->name('kewpa26');
    Route::get('/{sale}/kewpa27',   [DisposalSaleController::class, 'kewpa27'])->name('kewpa27');
    Route::get('/{sale}/kewpa27a',  [DisposalSaleController::class, 'kewpa27a'])->name('kewpa27a');

    // PDF Downloads
    Route::get('/{sale}/kewpa21/download',  [DisposalSaleController::class, 'downloadKewpa21'])->name('kewpa21.download');
    Route::get('/{sale}/kewpa22/download',  [DisposalSaleController::class, 'downloadKewpa22'])->name('kewpa22.download');
    Route::get('/{sale}/kewpa23/download',  [DisposalSaleController::class, 'downloadKewpa23'])->name('kewpa23.download');
    Route::get('/{sale}/kewpa24/download',  [DisposalSaleController::class, 'downloadKewpa24'])->name('kewpa24.download');
    Route::get('/{sale}/kewpa25/download',  [DisposalSaleController::class, 'downloadKewpa25'])->name('kewpa25.download');
    Route::get('/{sale}/kewpa26/download',  [DisposalSaleController::class, 'downloadKewpa26'])->name('kewpa26.download');
    Route::get('/{sale}/kewpa27/download',  [DisposalSaleController::class, 'downloadKewpa27'])->name('kewpa27.download');
    Route::get('/{sale}/kewpa27a/download', [DisposalSaleController::class, 'downloadKewpa27a'])->name('kewpa27a.download');
});

// KEW.PA-15/29 — Committee Appointments (standalone, polymorphic)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/committee-appointments', [CommitteeAppointmentController::class, 'index'])->name('committee-appointments.index');
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
Route::put('/receivings/{receiving}', [AssetController::class, 'updateReceiving'])->name('receivings.update');
Route::delete('/receivings/{receiving}', [AssetController::class, 'destroyReceiving'])->name('receivings.destroy');
Route::get('/assets/{asset}/kewpa2/download', [AssetController::class, 'downloadKewpa2'])->name('assets.kewpa2.download');
Route::get('/assets/{asset}/kewpa3/download', [AssetController::class, 'downloadKewpa3'])->name('assets.kewpa3.download');

require __DIR__.'/auth.php';