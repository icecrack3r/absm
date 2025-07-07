<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ManpowerAuthController;
use App\Http\Controllers\ExportController;

// Export routes
Route::middleware('auth')->group(function () {
    Route::get('/export/projek', [ExportController::class, 'exportProjek'])->name('projek.export');
    Route::get('/export/manpower', [ExportController::class, 'exportManpower'])->name('manpower.export');
    Route::get('/export/jadwal', [ExportController::class, 'exportJadwal'])->name('jadwal.export');
    Route::get('/export/absensi', [ExportController::class, 'exportAbsensi'])->name('absensi.export');
});

Route::get('/', function () {
    return redirect('/manpower/login');
});

// Routes untuk Manpower
Route::prefix('manpower')->group(function () {
    Route::get('login', [ManpowerAuthController::class, 'showLoginForm'])->name('manpower.login');
    Route::post('login', [ManpowerAuthController::class, 'login']);
    
    Route::middleware('auth:manpower')->group(function () {
        Route::get('dashboard', [ManpowerAuthController::class, 'dashboard'])->name('manpower.dashboard');
        Route::post('check-in', [ManpowerAuthController::class, 'checkIn'])->name('manpower.check-in');
        Route::post('check-out', [ManpowerAuthController::class, 'checkOut'])->name('manpower.check-out');
        Route::post('logout', [ManpowerAuthController::class, 'logout'])->name('manpower.logout');
    });
});

