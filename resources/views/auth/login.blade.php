<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi FansNet</title>
    <link rel="shortcut icon" href="{{ asset('logo1.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/auth.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script src="{{ asset('assets/static/js/initTheme.js') }}"></script>
    <div id="auth">
        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <!-- Jam & Tanggal Simple -->
                    <div class="text-center mb-3">
                        <div>
                            <span id="loginTime" class="badge bg-primary" style="font-size: 1rem;">00:00 WIB</span>
                        </div>
                        <small id="loginDate" class="text-muted">Loading...</small>
                    </div>
                    
                    <h1 class="auth-title">Log in.</h1>
                    <p class="auth-subtitle mb-5">Masuk dengan akun Anda untuk melanjutkan.</p>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="email" class="form-control form-control-xl @error('email') is-invalid @enderror" 
                                name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
                            <div class="form-control-icon">
                                <i class="bi bi-person"></i>
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group position-relative has-icon-left mb-4">
                            <input type="password" class="form-control form-control-xl @error('password') is-invalid @enderror" 
                                name="password" placeholder="Password" required>
                            <div class="form-control-icon">
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-check form-check-lg d-flex align-items-end mb-4">
                            <input class="form-check-input me-2" type="checkbox" name="remember" id="flexCheckDefault">
                            <label class="form-check-label text-gray-600" for="flexCheckDefault">
                                Ingat Saya
                            </label>
                        </div>
                        <button class="btn btn-primary btn-block btn-lg shadow-lg mt-2">
                            <i class="bi bi-box-arrow-in-right"></i> Log in
                        </button>
                    </form>
                    
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    <div class="d-flex justify-content-center align-items-center h-100">
                        <div class="text-center text-white">
                            <h1 class="display-1">📋</h1>
                            <h2 class="fw-bold">Sistem Absensi FansNet</h2>
                            <p class="lead">Kelola absensi karyawan dengan mudah dan efisien</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Jam & Tanggal Simple - WIB
        function updateLoginClock() {
            const now = new Date();
            const options = { timeZone: 'Asia/Jakarta' };
            
            const timeStr = now.toLocaleTimeString('id-ID', { 
                ...options, 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false
            });
            
            const dateStr = now.toLocaleDateString('id-ID', { 
                ...options,
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            document.getElementById('loginTime').textContent = timeStr + ' WIB';
            document.getElementById('loginDate').textContent = dateStr;
        }
        
        updateLoginClock();
        setInterval(updateLoginClock, 60000); // Update tiap 1 menit
        
        @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK',
            timer: 3000
        });
        @endif

        @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: "{{ session('error') }}",
            icon: 'error',
            confirmButtonText: 'OK'
        });
        @endif
    </script>
</body>

</html>
