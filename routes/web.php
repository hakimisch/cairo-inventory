<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Asset Management Group (Protected)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::post('/assets', [AssetController::class, 'store'])->name('assets.store'); // For the Add Asset form
    Route::post('/assets/{asset}/placements', [AssetController::class, 'storePlacement'])->name('assets.placements.store');
    Route::get('/assets/{asset}/kewpa2', [AssetController::class, 'kewpa2'])->name('assets.kewpa2');
    Route::get('/assets/{asset}/kewpa3', [AssetController::class, 'kewpa3'])->name('assets.kewpa3');
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