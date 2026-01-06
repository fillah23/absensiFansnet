<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\RekapController;

// Halaman absensi public (tanpa login) - HALAMAN UTAMA
Route::get('/', [AbsensiController::class, 'index'])->name('absensi.index');
Route::get('/blocked', function() {
    return view('absensi.blocked', [
        'client_ip' => request('client_ip'),
        'required_ip_range' => request('required_ip_range')
    ]);
})->name('absensi.blocked');
Route::get('/absensi/ip-settings', [AbsensiController::class, 'getIpSettings'])->name('absensi.getIpSettings');
Route::post('/absensi/masuk', [AbsensiController::class, 'absenMasuk'])->name('absensi.masuk');
Route::post('/absensi/keluar', [AbsensiController::class, 'absenKeluar'])->name('absensi.keluar');

// Check IP Address (untuk debugging)
Route::get('/check-ip', function () {
    $ip = request()->ip();
    
    // Helper function untuk get real IP (sama seperti di controller)
    $getRealClientIp = function($request) {
        if (in_array($request->ip(), ['127.0.0.1', '::1'])) {
            $hostname = gethostname();
            return gethostbyname($hostname);
        }
        
        $ip = $request->header('CF-Connecting-IP') 
            ?? $request->header('X-Forwarded-For')
            ?? $request->header('X-Real-IP')
            ?? $request->ip();
            
        if (strpos($ip, ',') !== false) {
            $ips = explode(',', $ip);
            $ip = trim($ips[0]);
        }
        
        return $ip;
    };
    
    $realClientIp = $getRealClientIp(request());
    $hostname = gethostname();
    $serverIp = gethostbyname($hostname);
    
    return response()->json([
        'PROBLEM_IDENTIFIED' => 'IP publik ISP terdeteksi, bukan IP WiFi lokal',
        'PUBLIC_IP' => $realClientIp,
        'NEEDED_IP' => '172.22.4.x (IP WiFi lokal)',
        '---' => '---',
        'detection_method' => [
            'is_localhost' => in_array(request()->ip(), ['127.0.0.1', '::1']),
            'method_used' => in_array(request()->ip(), ['127.0.0.1', '::1']) ? 'gethostbyname()' : 'CF-Connecting-IP / X-Forwarded-For',
        ],
        'raw_data' => [
            'request_ip' => request()->ip(),
            'server_hostname' => $hostname,
            'server_ip' => $serverIp,
            'CF_Connecting_IP' => request()->header('CF-Connecting-IP'),
            'X_Forwarded_For' => request()->header('X-Forwarded-For'),
            'X_Real_IP' => request()->header('X-Real-IP'),
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
            'X_Client_Local_IP' => request()->header('X-Client-Local-IP'), // Custom header dari JavaScript
        ],
        'explanation' => [
            'localhost_access' => 'Windows localhost → gethostbyname() → 172.22.4.9 ✓ BENAR',
            'cloudflare_problem' => 'Via Cloudflare → hanya dapat IP publik (165.99.225.73), BUKAN IP WiFi lokal',
            'network_flow' => 'WiFi (172.22.4.9) → Router NAT → Internet (165.99.225.73) → Cloudflare → Server',
            'cloudflare_limitation' => 'Cloudflare TIDAK BISA melihat IP lokal WiFi, hanya IP publik ISP',
        ],
        'SOLUTIONS' => [
            '1_use_local_ip_header' => 'Kirim IP lokal via JavaScript WebRTC (lihat halaman /test-local-ip)',
            '2_whitelist_public_ip' => 'Whitelist IP publik ISP: 165.99.225.73 (tapi bisa berubah)',
            '3_direct_access' => 'Akses langsung ke server tanpa Cloudflare untuk user internal',
            '4_vpn_solution' => 'Gunakan VPN atau direct connection untuk staff kantor',
        ],
        'RECOMMENDATION' => 'Gunakan solusi #1: Kirim IP lokal dari JavaScript WebRTC',
    ], 200, [], JSON_PRETTY_PRINT);
})->name('check.ip');

// Route untuk test WebRTC Local IP detection
Route::get('/test-local-ip', function () {
    return view('test-local-ip');
})->name('test.local.ip');

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