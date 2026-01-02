<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RekapController;

// Halaman absensi public (tanpa login) - HALAMAN UTAMA
Route::get('/', [AbsensiController::class, 'index'])->name('absensi.index');
Route::post('/absensi/validate-ip', [AbsensiController::class, 'validateIp'])->name('absensi.validateIp');
Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
Route::post('/absensi/keluar', [AbsensiController::class, 'absenKeluar'])->name('absensi.keluar');

// Routes yang memerlukan login
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('absensi.daftar');
    })->name('dashboard');

    // Daftar Absensi Hari Ini
    Route::get('/absensi/daftar', [AbsensiController::class, 'daftarAbsensi'])->name('absensi.daftar');

    // CRUD Karyawan
    Route::prefix('karyawan')->name('karyawan.')->group(function () {
        Route::get('/', [KaryawanController::class, 'index'])->name('index');
        Route::post('/', [KaryawanController::class, 'store'])->name('store');
        Route::put('/{karyawan}', [KaryawanController::class, 'update'])->name('update');
        Route::delete('/{karyawan}', [KaryawanController::class, 'destroy'])->name('destroy');
        Route::post('/{karyawan}/toggle', [KaryawanController::class, 'toggleStatus'])->name('toggle');
    });

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', [PengaturanController::class, 'index'])->name('index');
        Route::post('/', [PengaturanController::class, 'update'])->name('update');
    });

    // Rekap Absensi
    Route::prefix('rekap')->name('rekap.')->group(function () {
        Route::get('/', [RekapController::class, 'index'])->name('index');
        Route::get('/{karyawan}', [RekapController::class, 'detail'])->name('detail');
    });
});

// Login/Logout
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
