<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\RenminController;
use App\Http\Controllers\Dashboard\PimpinanController;
use App\Http\Controllers\Dashboard\PersonelController;

Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth.renmin')->group(function () {
    Route::get('/dashboard/renmin', [RenminController::class, 'index'])->name('dashboard.renmin');
});

Route::middleware('auth.pimpinan')->group(function () {
    Route::get('/dashboard/pimpinan', [PimpinanController::class, 'index'])->name('dashboard.pimpinan');
});

Route::middleware('auth.personel')->group(function () {
    Route::get('/dashboard/personel', [PersonelController::class, 'index'])->name('dashboard.personel');
});