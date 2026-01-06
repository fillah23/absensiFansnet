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
        #video {
            transform: scaleX(-1);
        }
        #canvas {
            display: none;
        }
        #video.streaming {
            transform: scaleX(-1);
        }
        #video:not(.streaming) {
            background: #000 url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><text x="50%" y="50%" text-anchor="middle" fill="white" font-size="14">Loading...</text></svg>') center center no-repeat;
            transform: none;
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

        <!-- Info Status Karyawan -->
        <div id="karyawanStatusInfo" class="alert alert-warning py-2 small mb-3" style="display: none;">
            <i class="bi bi-info-circle"></i> <span id="karyawanStatusText"></span>
        </div>

        <!-- Form Absensi -->
        <div class="mb-3">
            <label class="form-label fw-bold">Pilih Nama Karyawan</label>
            <select id="karyawanSelect" class="form-select" style="width: 100%;">
                <option value="">-- Pilih Karyawan --</option>
                @foreach($karyawans as $karyawan)
                    @php
                        $absensi = $absensisHariIni->get($karyawan->id);
                        $statusIcon = '';
                        $jamMasukStr = '';
                        $jamKeluarStr = '';
                        
                        if ($absensi) {
                            // Convert to string format jika ada
                            if ($absensi->jam_masuk) {
                                $jamMasukStr = $absensi->jam_masuk instanceof \Carbon\Carbon 
                                    ? $absensi->jam_masuk->format('H:i') 
                                    : date('H:i', strtotime($absensi->jam_masuk));
                            }
                            
                            if ($absensi->jam_keluar) {
                                $jamKeluarStr = $absensi->jam_keluar instanceof \Carbon\Carbon 
                                    ? $absensi->jam_keluar->format('H:i') 
                                    : date('H:i', strtotime($absensi->jam_keluar));
                            }
                            
                            // Set status icon
                            if ($absensi->jam_masuk && $absensi->jam_keluar) {
                                $statusIcon = '✅ ';
                            } elseif ($absensi->jam_masuk) {
                                $statusIcon = '🟢 ';
                            }
                        }
                    @endphp
                    <option value="{{ $karyawan->id }}" 
                            data-absen-masuk="{{ $jamMasukStr }}"
                            data-absen-keluar="{{ $jamKeluarStr }}"
                            data-status="{{ $absensi && $absensi->status ? $absensi->status : '' }}">
                        {{ $statusIcon }}{{ $karyawan->nama }} - {{ $karyawan->jabatan }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted">🟢 = Sudah absen masuk | ✅ = Sudah absen masuk & keluar</small>
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

            // Event listener ketika karyawan dipilih
            $('#karyawanSelect').on('change', function() {
                const selectedOption = $(this).find('option:selected');
                const karyawanId = $(this).val();
                const infoBox = $('#karyawanStatusInfo');
                const infoText = $('#karyawanStatusText');
                
                if (!karyawanId) {
                    infoBox.hide();
                    return;
                }
                
                const absenMasuk = selectedOption.data('absen-masuk');
                const absenKeluar = selectedOption.data('absen-keluar');
                const status = selectedOption.data('status');
                const namaKaryawan = selectedOption.text().replace(/[🟢✅]/g, '').trim();
                
                if (absenMasuk && absenKeluar) {
                    // Sudah absen masuk dan keluar
                    infoBox.removeClass('alert-warning alert-success').addClass('alert-info');
                    infoText.html(`<strong>${namaKaryawan}</strong> sudah absen <strong>MASUK</strong> (${absenMasuk}) dan <strong>KELUAR</strong> (${absenKeluar}) hari ini.`);
                    infoBox.show();
                } else if (absenMasuk) {
                    // Sudah absen masuk, belum keluar
                    let statusText = status === 'telat' ? '(Telat)' : '(Tepat Waktu)';
                    infoBox.removeClass('alert-info alert-warning').addClass('alert-success');
                    infoText.html(`<strong>${namaKaryawan}</strong> sudah absen <strong>MASUK</strong> pada ${absenMasuk} ${statusText}. Belum absen keluar.`);
                    infoBox.show();
                } else {
                    // Belum absen sama sekali
                    infoBox.removeClass('alert-info alert-success').addClass('alert-warning');
                    infoText.html(`<strong>${namaKaryawan}</strong> belum melakukan absensi hari ini.`);
                    infoBox.show();
                }
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

        // Global variable for local IP
        let detectedLocalIP = null;
        let ipSettings = null;

        // Detect local IP using WebRTC
        function detectLocalIP(callback) {
            const ips = [];
            const RTCPeerConnection = window.RTCPeerConnection || 
                                     window.mozRTCPeerConnection || 
                                     window.webkitRTCPeerConnection;
            
            if (!RTCPeerConnection) {
                callback(null);
                return;
            }

            const pc = new RTCPeerConnection({iceServers: [{urls: 'stun:stun.l.google.com:19302'}]});
            pc.createDataChannel('');
            pc.createOffer().then(offer => pc.setLocalDescription(offer)).catch(err => console.error(err));

            pc.onicecandidate = (ice) => {
                if (!ice || !ice.candidate || !ice.candidate.candidate) return;
                const parts = ice.candidate.candidate.split(' ');
                const ip = parts[4];
                if (ip && ips.indexOf(ip) === -1 && 
                    (ip.startsWith('192.168.') || ip.startsWith('172.') || ip.startsWith('10.'))) {
                    ips.push(ip);
                }
            };

            setTimeout(() => {
                pc.close();
                // Prioritas: IP yang match range kantor atau IP lokal pertama
                const wifiIP = ips[0]; // Ambil IP lokal pertama
                callback(wifiIP);
            }, 2000);
        }

        // Validasi IP kantor (3 segmen pertama) - dilakukan di frontend
        async function validateIP() {
            try {
                // Dapatkan pengaturan IP dari server
                const settingsResponse = await $.get('/absensi/ip-settings');
                ipSettings = settingsResponse;
                
                console.log('IP Settings:', ipSettings);
                
                // Jika validasi IP dinonaktifkan, skip
                if (!ipSettings.ip_validation_enabled) {
                    console.log('IP validation disabled');
                    showStatus('Validasi IP dinonaktifkan', 'info');
                    return true;
                }
                
                // Pastikan IP lokal sudah terdeteksi
                if (!detectedLocalIP) {
                    showStatus('Menunggu verifikasi koneksi...', 'warning');
                    return false;
                }
                
                console.log('Validating Local IP:', detectedLocalIP);
                console.log('Against Office IP:', ipSettings.ip_kantor);
                
                // Bypass untuk localhost
                if (detectedLocalIP === '127.0.0.1' || detectedLocalIP === '::1') {
                    console.log('Localhost detected, validation passed');
                    showStatus('Akses dari localhost (development mode)', 'success');
                    return true;
                }
                
                // Bandingkan 3 segmen pertama
                const officeSegments = ipSettings.ip_kantor_segments;
                const localSegments = detectedLocalIP.split('.');
                
                const valid = officeSegments.length >= 3 && localSegments.length >= 3 &&
                             officeSegments[0] == localSegments[0] &&
                             officeSegments[1] == localSegments[1] &&
                             officeSegments[2] == localSegments[2];
                
                console.log('IP Validation Result:', valid);
                console.log('Office segments:', officeSegments.slice(0, 3));
                console.log('Local segments:', localSegments.slice(0, 3));
                
                if (!valid) {
                    const requiredRange = officeSegments[0] + '.' + officeSegments[1] + '.' + officeSegments[2] + '.x';
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Akses Ditolak',
                        text: 'Gunakan WiFi kantor untuk absensi.',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then(() => {
                        // Redirect ke halaman blocked
                        window.location.href = '/blocked';
                    });
                    
                    return false;
                }
                
                // IP valid
                console.log('✓ IP validation passed');
                showStatus('✓ Koneksi WiFi kantor terverifikasi', 'success');
                return true;
                
            } catch (error) {
                console.error('Validation error:', error);
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
                        video.classList.add('streaming');
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

            // Set canvas size - resize untuk mengurangi ukuran file
            const maxWidth = 1024; // Max width untuk gambar
            const maxHeight = 768; // Max height untuk gambar
            
            let width = video.videoWidth;
            let height = video.videoHeight;
            
            // Resize jika lebih besar dari max
            if (width > maxWidth || height > maxHeight) {
                const ratio = Math.min(maxWidth / width, maxHeight / height);
                width = width * ratio;
                height = height * ratio;
            }
            
            canvas.width = width;
            canvas.height = height;

            // Draw video ke canvas
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, width, height);

            // Tambahkan watermark
            const datetime = new Date().toLocaleString('id-ID');
            const location = `Lat: ${currentPosition.latitude.toFixed(6)}, Long: ${currentPosition.longitude.toFixed(6)}`;

            ctx.fillStyle = 'rgba(0, 0, 0, 0.6)';
            ctx.fillRect(10, height - 70, width - 20, 60);

            ctx.fillStyle = 'white';
            ctx.font = 'bold 16px Arial';
            ctx.fillText(datetime, 20, height - 45);
            ctx.font = '14px Arial';
            ctx.fillText(location, 20, height - 20);

            // Convert to base64 dengan kualitas JPEG lebih rendah untuk ukuran lebih kecil
            // Gunakan JPEG dengan quality 0.7 untuk balance antara kualitas dan ukuran
            capturedImage = canvas.toDataURL('image/jpeg', 0.7);

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
            
            // Tambahkan local IP jika terdeteksi
            if (detectedLocalIP) {
                data.local_ip = detectedLocalIP;
            }

            try {
                const response = await $.post(url, data);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000
                });

                // Reset form - redirect ke halaman utama menggunakan GET
                setTimeout(() => {
                    window.location.href = '/';
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
            // Show loading message
            showStatus('Memverifikasi koneksi WiFi...', 'info');
            
            // Detect local IP first and wait for it
            await new Promise((resolve) => {
                detectLocalIP((localIP) => {
                    if (localIP) {
                        detectedLocalIP = localIP;
                        console.log('✓ Local IP detected:', localIP);
                        showStatus('✓ WiFi lokal terdeteksi', 'success');
                    } else {
                        console.log('✗ Local IP not detected, will use server IP');
                        showStatus('Koneksi terdeteksi', 'warning');
                    }
                    resolve();
                });
            });
            
            // Wait a bit for IP detection to complete
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Then validate IP
            console.log('Starting IP validation with local IP:', detectedLocalIP);
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
