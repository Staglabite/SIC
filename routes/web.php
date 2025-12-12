<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\RenminController;
use App\Http\Controllers\Dashboard\PimpinanController;  // pastikan sudah di-import
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

// ==================================================================
// =========================== PIMPINAN =============================
// ==================================================================
Route::middleware('auth:pimpinan')
    ->prefix('dashboard')
    ->name('pimpinan.')
    ->group(function () {

        Route::get('/pimpinan', [PimpinanController::class, 'dashboard'])
            ->name('dashboard');

        Route::get('/pimpinan/approval', [PimpinanController::class, 'validasi'])
            ->name('approval');

        // UBAH INI JADI validasi.update supaya cocok dengan JS
        Route::post('/pimpinan/approval/update', [PimpinanController::class, 'updateStatus'])
            ->name('validasi.update');   // ← nama ini yang dipakai di blade
    });

// ==================================================================
// ============================= RENMIN =============================
// ==================================================================
Route::middleware('auth:renmin')
    ->prefix('dashboard')
    ->name('renmin.')
    ->group(function () {
        Route::get('/renmin', [RenminController::class, 'dashboard'])->name('dashboard');
        Route::get('/renmin/validasi', [RenminController::class, 'validasi'])->name('validasi');
        Route::post('/renmin/validasi/update', [RenminController::class, 'updateStatus'])->name('validasi.update');
    });

// ==================================================================
// ============================ PERSONEL ============================
// ==================================================================
Route::middleware('auth:personel')
    ->prefix('dashboard')
    ->name('personel.')
    ->group(function () {

        Route::get('/personel', [PersonelController::class, 'index'])->name('dashboard');

        Route::get('/personel/pengajuan/create', [PersonelController::class, 'create'])
             ->name('pengajuan.create');

        Route::post('/personel/cuti/store', [PersonelController::class, 'storeCuti'])
             ->name('cuti.store');

        Route::post('/personel/izin/store', [PersonelController::class, 'storeIzin'])
             ->name('izin.store');

        Route::get('/personel/pengajuan/{id}/edit', [PersonelController::class, 'edit'])
             ->name('pengajuan.edit');

        Route::put('/personel/pengajuan/{id}', [PersonelController::class, 'update'])
             ->name('pengajuan.update');

        Route::post('/personel/pengajuan/kirim-ulang', [PersonelController::class, 'kirimUlang'])
             ->name('pengajuan.kirim-ulang');

        Route::get('/personel/surat/download/{id}', [PersonelController::class, 'downloadSurat'])
            ->name('surat.download');

        Route::get('/personel/satker', [PersonelController::class, 'satkerManagement'])
        ->name('satker');


        Route::get('/personel/renmin-management', [PersonelController::class, 'renminManagement'])
        ->name('renmin_management');

        Route::get('/personel/pimpinan-management', [PersonelController::class, 'pimpinanManagement'])
        ->name('pimpinan_management');

        Route::get('/personel/personel-management', [PersonelController::class, 'personelManagement'])
        ->name('personel_management');
    });
