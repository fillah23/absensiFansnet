<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Absensi FansNet</title>
    <link rel="shortcut icon" href="{{ asset('logo1.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/compiled/css/app-dark.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .absensi-container {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .camera-container {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            border-radius: 15px;
            overflow: hidden;
            background: #000;
        }
        #video, #canvas {
            width: 100%;
            height: auto;
            display: block;
            min-height: 300px;
        }
        #canvas {
            display: none;
        }
        #video:not([src]) {
            background: #000 url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><text x="50%" y="50%" text-anchor="middle" fill="white" font-size="14">Loading...</text></svg>') center center no-repeat;
        }
        .btn-capture {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid white;
            background: #667eea;
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-capture:hover {
            background: #764ba2;
            transform: translateX(-50%) scale(1.1);
        }
        .status-box {
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
            font-size: 14px;
        }
        .status-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .preview-image {
            width: 100%;
            border-radius: 15px;
            margin-top: 15px;
        }
        @media (max-width: 576px) {
            .absensi-container {
                padding: 20px 15px;
            }
        }
    </style>
</head>
<body>
    <div class="absensi-container">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Absensi FansNet</h2>
            <p class="text-muted mb-2">Sistem Absensi Karyawan</p>
            
            <!-- Jam & Tanggal Simple -->
            <div class="mb-2">
                <div>
                    <span id="currentTime" class="badge bg-primary" style="font-size: 1.2rem;">00:00 WIB</span>
                </div>
                <small id="currentDate" class="text-muted">Loading...</small>
            </div>
            
            <div class="alert alert-info py-2 small mb-3">
                <i class="bi bi-clock"></i> 
                <strong>Tepat Waktu:</strong> {{ $jamMasukMulai }} - {{ $jamMasukSelesai }} (dapat bonus) | 
                <strong>Telat:</strong> Setelah {{ $jamMasukSelesai }} (tidak dapat bonus) | 
                <strong>Absen Keluar:</strong> Kapan saja
            </div>
        </div>

        <!-- Status Box -->
        <div id="statusBox" style="display: none;"></div>

        <!-- Form Absensi -->
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih Nama Karyawan</label>
            <select id="karyawanSelect" class="form-select" style="width: 100%;">
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawans as $karyawan)
                    <option value="{{ $karyawan->id }}">{{ $karyawan->nama }} - {{ $karyawan->jabatan }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tombol Jenis Absensi -->
        <div class="btn-group w-100 mb-3" role="group">
            <input type="radio" class="btn-check" name="jenisAbsensi" id="absenMasuk" value="masuk" checked>
            <label class="btn btn-outline-success btn-lg" for="absenMasuk">
                <i class="bi bi-box-arrow-in-right"></i> Absen Masuk
            </label>

            <input type="radio" class="btn-check" name="jenisAbsensi" id="absenKeluar" value="keluar">
            <label class="btn btn-outline-danger btn-lg" for="absenKeluar">
                <i class="bi bi-box-arrow-right"></i> Absen Keluar
            </label>
        </div>

        <!-- Camera Container -->
        <div class="camera-container">
            <video id="video" autoplay playsinline muted></video>
            <canvas id="canvas"></canvas>
            <button id="btnCapture" class="btn-capture" style="display: none;"></button>
        </div>

        <!-- Preview Foto -->
        <div id="previewContainer" style="display: none;">
            <img id="previewImage" class="preview-image" alt="Preview">
            <div class="d-grid gap-2 mt-3">
                <button id="btnSubmit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Kirim Absensi
                </button>
                <button id="btnRetake" class="btn btn-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Foto Ulang
                </button>
            </div>
        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let video = document.getElementById('video');
        let canvas = document.getElementById('canvas');
        let btnCapture = document.getElementById('btnCapture');
        let previewContainer = document.getElementById('previewContainer');
        let previewImage = document.getElementById('previewImage');
        let currentPosition = null;
        let capturedImage = null;

        // Settings dari backend
        const IP_KANTOR = "{{ $ipKantor }}";
        const LAT_KANTOR = parseFloat("{{ $latKantor }}");
        const LONG_KANTOR = parseFloat("{{ $longKantor }}");
        const RADIUS = parseFloat("{{ $radius }}");
        const JAM_MASUK_MULAI = "{{ $jamMasukMulai }}";
        const JAM_MASUK_SELESAI = "{{ $jamMasukSelesai }}";
        const JAM_KELUAR_MULAI = "{{ $jamKeluarMulai }}";
        const JAM_KELUAR_SELESAI = "{{ $jamKeluarSelesai }}";

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Select2 untuk dropdown karyawan
        $(document).ready(function() {
            $('#karyawanSelect').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Pilih Karyawan --',
                allowClear: true,
                width: '100%'
            });
        });

        // Jam & Tanggal Simple - WIB
        function updateClock() {
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
            
            document.getElementById('currentTime').textContent = timeStr + ' WIB';
            document.getElementById('currentDate').textContent = dateStr;
        }
        
        // Update clock tiap 1 menit
        updateClock();
        setInterval(updateClock, 60000);

        // Fungsi tampilkan status
        function showStatus(message, type) {
            let statusBox = $('#statusBox');
            statusBox.removeClass('status-success status-error status-warning');
            statusBox.addClass('status-' + type);
            statusBox.html('<i class="bi bi-info-circle"></i> ' + message);
            statusBox.show();
        }

        // Validasi IP kantor (3 segmen pertama)
        async function validateIP() {
            try {
                const response = await $.post('/absensi/validate-ip');
                if (!response.valid) {
                    showStatus(`IP tidak valid! Pastikan terhubung ke WiFi kantor. IP Anda: ${response.client_ip}`, 'error');
                    return false;
                }
                return true;
            } catch (error) {
                showStatus('Gagal validasi IP. Periksa koneksi internet.', 'error');
                return false;
            }
        }

        // Validasi batas waktu absensi
        function validateTime(jenisAbsensi) {
            // Tidak ada validasi waktu untuk absen masuk maupun keluar
            // Bisa absen kapan saja, sistem akan otomatis menentukan status (hadir/telat)
            return true;
        }

        // Hitung jarak menggunakan Haversine formula
        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Radius bumi dalam meter
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        // Request akses kamera
        async function startCamera() {
            try {
                // Cek apakah browser mendukung getUserMedia
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    showStatus('Browser Anda tidak mendukung akses kamera. Gunakan browser modern seperti Chrome, Firefox, atau Safari.', 'error');
                    return;
                }

                // Cek apakah menggunakan HTTPS atau localhost
                const isSecure = window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
                if (!isSecure) {
                    showStatus('⚠️ Kamera memerlukan HTTPS atau localhost. Akses via localhost (http://127.0.0.1:8000) atau gunakan HTTPS.', 'error');
                    return;
                }

                showStatus('Meminta izin akses kamera...', 'warning');

                const constraints = {
                    video: {
                        facingMode: 'user', // Kamera depan
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                };

                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                console.log('Stream obtained:', stream);
                console.log('Video tracks:', stream.getVideoTracks());
                
                video.srcObject = stream;
                
                // Tunggu video ready dan playing
                video.onloadedmetadata = function() {
                    console.log('Video metadata loaded');
                    video.play().then(() => {
                        console.log('Video playing successfully');
                        btnCapture.style.display = 'block';
                        showStatus('✓ Kamera siap! Pilih karyawan dan ambil foto.', 'success');
                    }).catch(err => {
                        console.error('Error playing video:', err);
                        showStatus('Error saat memutar video: ' + err.message, 'error');
                    });
                };
                
                // Tambahan error handler untuk video
                video.onerror = function(err) {
                    console.error('Video element error:', err);
                    showStatus('Error pada video element', 'error');
                };
            } catch (error) {
                console.error('Camera error:', error);
                
                let errorMessage = 'Gagal mengakses kamera. ';
                
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    errorMessage += 'Izin kamera ditolak. Klik ikon kamera di address bar dan izinkan akses kamera.';
                } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                    errorMessage += 'Kamera tidak ditemukan. Pastikan perangkat Anda memiliki kamera.';
                } else if (error.name === 'NotReadableError' || error.name === 'TrackStartError') {
                    errorMessage += 'Kamera sedang digunakan aplikasi lain. Tutup aplikasi yang menggunakan kamera.';
                } else if (error.name === 'OverconstrainedError') {
                    errorMessage += 'Kamera tidak mendukung resolusi yang diminta.';
                } else if (error.name === 'SecurityError') {
                    errorMessage += 'Akses kamera diblokir karena alasan keamanan. Gunakan HTTPS atau localhost.';
                } else {
                    errorMessage += error.message || 'Error tidak diketahui.';
                }
                
                showStatus(errorMessage, 'error');
                
                // Tampilkan alert untuk error kritis
                Swal.fire({
                    icon: 'error',
                    title: 'Kamera Tidak Dapat Diakses',
                    html: errorMessage,
                    footer: '<small>Pastikan Anda mengakses via <strong>localhost</strong> atau <strong>HTTPS</strong></small>'
                });
            }
        }

        // Request GPS
        function requestGPS() {
            if (!navigator.geolocation) {
                showStatus('Browser tidak mendukung GPS.', 'error');
                return;
            }

            // Cek apakah menggunakan HTTPS atau localhost
            const isSecure = window.location.protocol === 'https:' || window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
            if (!isSecure) {
                showStatus('⚠️ GPS memerlukan HTTPS atau localhost. Akses via localhost untuk development.', 'error');
                Swal.fire({
                    icon: 'warning',
                    title: 'GPS Memerlukan Koneksi Aman',
                    html: `
                        <p>GPS dan Kamera hanya bisa diakses via <strong>HTTPS</strong> atau <strong>localhost</strong>.</p>
                        <hr>
                        <p class="text-start"><strong>Untuk Testing Development:</strong><br>
                        Akses via: <code>http://localhost:8000</code> atau <code>http://127.0.0.1:8000</code></p>
                        <p class="text-start"><strong>Untuk Production:</strong><br>
                        Deploy aplikasi dengan HTTPS (SSL Certificate)</p>
                        <hr>
                        <small class="text-muted">Browser memblokir akses GPS/Camera dari IP non-secure (${window.location.hostname}) untuk alasan keamanan.</small>
                    `,
                    confirmButtonText: 'Mengerti'
                });
                return;
            }

            showStatus('Mengambil lokasi GPS...', 'warning');
            
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    currentPosition = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };

                    // Cek jarak dari kantor
                    const distance = calculateDistance(
                        LAT_KANTOR, LONG_KANTOR,
                        currentPosition.latitude, currentPosition.longitude
                    );

                    if (distance > RADIUS) {
                        showStatus(`Anda berada di luar radius kantor! Jarak: ${Math.round(distance)} meter (Max: ${RADIUS} meter)`, 'error');
                        currentPosition = null;
                    } else {
                        showStatus(`Lokasi GPS valid. Jarak dari kantor: ${Math.round(distance)} meter`, 'success');
                    }
                },
                (error) => {
                    console.error('GPS error:', error);
                    let errorMsg = 'Gagal mendapatkan lokasi GPS. ';
                    
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMsg += 'Izin lokasi ditolak. Klik ikon lokasi di address bar dan izinkan.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMsg += 'Informasi lokasi tidak tersedia. Pastikan GPS aktif.';
                            break;
                        case error.TIMEOUT:
                            errorMsg += 'Request timeout. Coba lagi.';
                            break;
                        default:
                            errorMsg += error.message || 'Error tidak diketahui.';
                    }
                    
                    showStatus(errorMsg, 'error');
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }

        // Capture foto dengan watermark
        function capturePhoto() {
            const karyawanId = $('#karyawanSelect').val();
            
            if (!karyawanId) {
                Swal.fire('Peringatan', 'Pilih karyawan terlebih dahulu!', 'warning');
                return;
            }

            if (!currentPosition) {
                Swal.fire('Peringatan', 'GPS belum aktif atau lokasi Anda di luar radius kantor!', 'warning');
                requestGPS();
                return;
            }

            // Set canvas size sama dengan video
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw video ke canvas
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Tambahkan watermark
            const datetime = new Date().toLocaleString('id-ID');
            const location = `Lat: ${currentPosition.latitude.toFixed(6)}, Long: ${currentPosition.longitude.toFixed(6)}`;

            ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
            ctx.fillRect(10, canvas.height - 70, canvas.width - 20, 60);

            ctx.fillStyle = 'white';
            ctx.font = 'bold 16px Arial';
            ctx.fillText(datetime, 20, canvas.height - 45);
            ctx.font = '14px Arial';
            ctx.fillText(location, 20, canvas.height - 20);

            // Convert to base64
            capturedImage = canvas.toDataURL('image/png');

            // Show preview
            previewImage.src = capturedImage;
            previewContainer.style.display = 'block';
            video.style.display = 'none';
            btnCapture.style.display = 'none';
        }

        // Retake photo
        function retakePhoto() {
            capturedImage = null;
            previewContainer.style.display = 'none';
            video.style.display = 'block';
            btnCapture.style.display = 'block';
        }

        // Submit absensi
        async function submitAbsensi() {
            const karyawanId = $('#karyawanSelect').val();
            const jenisAbsensi = $('input[name="jenisAbsensi"]:checked').val();

            // Validasi waktu absensi
            if (!validateTime(jenisAbsensi)) {
                return;
            }

            if (!karyawanId) {
                Swal.fire('Error', 'Pilih karyawan terlebih dahulu!', 'error');
                return;
            }

            if (!capturedImage) {
                Swal.fire('Error', 'Ambil foto terlebih dahulu!', 'error');
                return;
            }

            if (!currentPosition) {
                Swal.fire('Error', 'GPS belum aktif!', 'error');
                return;
            }

            // Validasi IP dulu
            const ipValid = await validateIP();
            if (!ipValid) {
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Mengirim Absensi...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const url = jenisAbsensi === 'masuk' ? '/absensi/masuk' : '/absensi/keluar';
            const data = {
                karyawan_id: karyawanId,
                foto: capturedImage,
                latitude: currentPosition.latitude,
                longitude: currentPosition.longitude
            };

            try {
                const response = await $.post(url, data);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000
                });

                // Reset form
                setTimeout(() => {
                    location.reload();
                }, 2000);

            } catch (error) {
                const message = error.responseJSON?.message || 'Terjadi kesalahan saat mengirim absensi';
                Swal.fire('Error', message, 'error');
                retakePhoto();
            }
        }

        // Event listeners
        btnCapture.addEventListener('click', capturePhoto);
        document.getElementById('btnRetake').addEventListener('click', retakePhoto);
        document.getElementById('btnSubmit').addEventListener('click', submitAbsensi);

        // Initialize
        $(document).ready(async function() {
            // Validasi IP
            await validateIP();
            
            // Start camera
            startCamera();
            
            // Request GPS
            requestGPS();

            // Re-request GPS every 30 seconds
            setInterval(requestGPS, 30000);
        });
    </script>
</body>
</html>
