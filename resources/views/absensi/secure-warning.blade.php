<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Keamanan - Sistem Absensi FansNet</title>
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/error.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/compiled/svg/favicon.svg') }}" type="image/x-icon">
</head>
<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="error">
        <div class="error-page container">
            <div class="col-md-10 col-12 offset-md-1">
                <div class="text-center">
                    <img class="img-error" src="{{ asset('assets/compiled/svg/error-403.svg') }}" alt="Peringatan" style="max-width: 400px;">
                    <h1 class="error-title text-warning">⚠️ Peringatan Keamanan</h1>
                    <p class="fs-5 text-gray-600">Browser modern memerlukan koneksi aman (HTTPS) atau localhost untuk mengakses GPS dan Kamera.</p>
                    
                    <div class="alert alert-warning mt-4">
                        <h5 class="alert-heading"><i class="bi bi-shield-exclamation"></i> Secure Origin Required</h5>
                        <hr>
                        <div class="text-start">
                            <p class="mb-2">Anda mengakses dari: <code>{{ $current_url }}</code></p>
                            <p class="mb-0">Browser akan <strong>memblokir akses GPS dan Camera</strong> karena tidak menggunakan HTTPS atau localhost.</p>
                        </div>
                    </div>

                    <div class="row mt-4 text-start">
                        <div class="col-md-6">
                            <div class="card border-info h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-info"><i class="bi bi-laptop"></i> Untuk Development/Testing</h5>
                                    <p class="small">Akses aplikasi via localhost agar GPS dan Camera berfungsi:</p>
                                    <div class="bg-light p-3 rounded">
                                        <code class="text-dark">http://localhost:8000</code><br>
                                        <small class="text-muted">atau</small><br>
                                        <code class="text-dark">http://127.0.0.1:8000</code>
                                    </div>
                                    <div class="mt-3">
                                        <a href="http://localhost:8000" class="btn btn-info btn-sm w-100">
                                            <i class="bi bi-arrow-right"></i> Buka via Localhost
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <h5 class="card-title text-success"><i class="bi bi-shield-check"></i> Untuk Production</h5>
                                    <p class="small">Deploy aplikasi dengan HTTPS (SSL Certificate):</p>
                                    <ul class="small">
                                        <li>Install SSL Certificate (Let's Encrypt gratis)</li>
                                        <li>Configure web server (Nginx/Apache) untuk HTTPS</li>
                                        <li>Redirect HTTP ke HTTPS</li>
                                        <li>Akses via <code>https://domain.com</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 border-danger">
                        <div class="card-body text-start">
                            <h6 class="text-danger"><i class="bi bi-exclamation-triangle"></i> Mengapa Ini Diperlukan?</h6>
                            <p class="small mb-2">Browser modern (Chrome, Firefox, Safari, Edge) memberlakukan kebijakan keamanan ketat:</p>
                            <ul class="small mb-0">
                                <li><strong>GPS/Geolocation API</strong> hanya bisa diakses dari secure origin (HTTPS atau localhost)</li>
                                <li><strong>Camera/getUserMedia API</strong> hanya bisa diakses dari secure origin</li>
                                <li>Ini untuk mencegah penyalahgunaan akses lokasi dan camera pengguna</li>
                                <li>IP Address biasa (http://172.22.4.x) tidak dianggap secure origin</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('login') }}" class="btn btn-lg btn-primary">
                            <i class="bi bi-lock"></i> Login Admin
                        </a>
                    </div>

                    <div class="mt-4 text-muted small">
                        <p>Untuk informasi lebih lanjut tentang secure contexts, kunjungi:<br>
                        <a href="https://developer.mozilla.org/en-US/docs/Web/Security/Secure_Contexts" target="_blank">MDN Web Docs - Secure Contexts</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
