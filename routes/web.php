<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Vue grand écran — publique (pas besoin de login pour l'écran TV)
Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/latest', [DisplayController::class, 'latest'])->name('display.latest');

// Accès kiosque Raspberry Pi — protégé par token, sans authentification
Route::get('/tv/{token}', [DisplayController::class, 'kiosk'])->name('display.kiosk');
Route::get('/tv/{token}/latest', [DisplayController::class, 'kioskLatest'])->name('display.kiosk.latest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::patch('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Administration — réservé aux admins
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', Admin\UserController::class);
    Route::get('appointments/export', [Admin\AppointmentController::class, 'export'])->name('appointments.export');
    Route::resource('appointments', Admin\AppointmentController::class)->only(['index', 'edit', 'update', 'destroy']);
    Route::get('resets', [Admin\ResetController::class, 'index'])->name('resets.index');
    Route::post('resets', [Admin\ResetController::class, 'store'])->name('resets.store');
    Route::get('badges', [Admin\BadgeController::class, 'index'])->name('badges.index');
    Route::post('badges/award', [Admin\BadgeController::class, 'award'])->name('badges.award');
    Route::post('badges/revoke', [Admin\BadgeController::class, 'revoke'])->name('badges.revoke');
});

require __DIR__.'/auth.php';
