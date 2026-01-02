<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - Sistem Absensi FansNet</title>
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/error.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
</head>
<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-8 col-12 offset-md-2">
                <div class="text-center">
                    <img class="img-error" src="{{ asset('assets/compiled/svg/error-403.svg') }}" alt="Akses Ditolak" style="max-width: 400px;">
                    <h1 class="error-title">Akses Ditolak</h1>
                    <p class="fs-5 text-gray-600">Anda harus terhubung ke WiFi kantor untuk mengakses halaman absensi.</p>
                    
                    <div class="alert alert-danger mt-4">
                        <h5 class="alert-heading"><i class="bi bi-wifi-off"></i> Koneksi Tidak Valid</h5>
                        <hr>
                        <p class="mb-0">Sistem mendeteksi bahwa Anda tidak terhubung ke jaringan WiFi kantor. Silakan pastikan perangkat Anda terhubung ke WiFi kantor yang telah ditentukan.</p>
                    </div>

                    <div class="card mt-4 border-primary">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><i class="bi bi-info-circle"></i> Cara Mengakses Halaman Absensi</h5>
                            <ol class="text-start mt-3">
                                <li class="mb-2">Pastikan perangkat Anda terhubung ke <strong>WiFi kantor</strong></li>
                                <li class="mb-2">Periksa pengaturan WiFi di perangkat Anda</li>
                                <li class="mb-2">Pastikan Anda menggunakan jaringan yang benar</li>
                                <li class="mb-2">Setelah terhubung ke WiFi kantor, <strong>refresh halaman ini</strong></li>
                            </ol>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button onclick="window.location.reload()" class="btn btn-lg btn-primary">
                            <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-lg btn-outline-secondary ms-2">
                            <i class="bi bi-lock"></i> Login Admin
                        </a>
                    </div>

                    <div class="mt-4 text-muted small">
                        <p>Jika Anda sudah terhubung ke WiFi kantor dan masih melihat halaman ini,<br>
                        silakan hubungi administrator sistem.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
