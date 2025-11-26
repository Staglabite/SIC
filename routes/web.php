<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\RenminController;
use App\Http\Controllers\Dashboard\PimpinanController;
use App\Http\Controllers\Dashboard\PersonelController;

// Halaman utama
Route::get('/', fn() => redirect()->route('login'));

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

// Logout semua guard
Route::post('/logout', function (Request $request) {
    if (Auth::guard('personel')->check())   Auth::guard('personel')->logout();
    if (Auth::guard('renmin')->check())     Auth::guard('renmin')->logout();
    if (Auth::guard('pimpinan')->check())   Auth::guard('pimpinan')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login')->with('success', 'Berhasil keluar.');
})->name('logout');

// PIMPINAN
Route::middleware('auth:pimpinan')->group(function () {
    Route::get('/dashboard/pimpinan', [PimpinanController::class, 'index'])->name('dashboard.pimpinan');
});

// RENMIN
Route::middleware('auth:renmin')->prefix('dashboard')->name('renmin.')->group(function () {
    Route::get('/renmin', [RenminController::class, 'dashboard'])->name('dashboard');
    Route::get('/renmin/validasi', [RenminController::class, 'validasi'])->name('validasi');
    Route::post('/renmin/validasi/update', [RenminController::class, 'updateStatus'])->name('validasi.update');
});

Route::middleware('auth:personel')->prefix('dashboard')->name('personel.')->group(function () {

    // Dashboard + Riwayat
    Route::get('/personel', [PersonelController::class, 'index'])
         ->name('dashboard');

    // Form Pengajuan Baru (satu halaman untuk cuti & izin)
    Route::get('/personel/pengajuan/create', [PersonelController::class, 'create'])
         ->name('pengajuan.create');

    // === 2 ROUTE TERPISAH (SESUAI PERMINTAANMU) ===
    Route::post('/personel/cuti/store', [PersonelController::class, 'storeCuti'])
         ->name('cuti.store');

    Route::post('/personel/izin/store', [PersonelController::class, 'storeIzin'])
         ->name('izin.store');
    // ============================================

    // Edit & Update (hanya Tidak Valid)
    Route::get('/personel/pengajuan/{id}/{tipe}/edit', [PersonelController::class, 'edit'])
         ->name('pengajuan.edit');

    Route::put('/personel/pengajuan/{id}/{tipe}', [PersonelController::class, 'update'])
         ->name('pengajuan.update');

    Route::post('/personel/pengajuan/kirim-ulang', [PersonelController::class, 'kirimUlang'])
         ->name('pengajuan.kirim-ulang');
});
