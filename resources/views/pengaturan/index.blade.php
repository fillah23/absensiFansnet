@extends('layouts.main')

@section('contents')
@include('layouts.sidebar')
<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Pengaturan Sistem</h3>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Konfigurasi Absensi</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pengaturan.update') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">IP WiFi Kantor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ip_kantor" 
                                           value="{{ $pengaturans['ip_kantor']->value ?? '' }}" 
                                           placeholder="172.22.4.1" required>
                                    <small class="text-muted">IP address WiFi kantor yang diizinkan untuk absensi</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Radius Absen (meter) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="radius_absen" 
                                           value="{{ $pengaturans['radius_absen']->value ?? '' }}" 
                                           min="1" required>
                                    <small class="text-muted">Jarak maksimal karyawan dari lokasi kantor (dalam meter)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Latitude Kantor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="latitude_kantor" name="latitude_kantor" 
                                           value="{{ $pengaturans['latitude_kantor']->value ?? '' }}" 
                                           step="any" required >
                                    <small class="text-muted">Klik lokasi di peta untuk memilih koordinat</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Longitude Kantor <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="longitude_kantor" name="longitude_kantor" 
                                           value="{{ $pengaturans['longitude_kantor']->value ?? '' }}" 
                                           step="any" required >
                                    <small class="text-muted">Klik lokasi di peta untuk memilih koordinat</small>
                                </div>
                            </div>
                        </div>

                        <!-- Map Picker -->
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-map"></i> Pilih Lokasi Kantor di Peta</label>
                            <div id="mapPicker" style="height: 400px; width: 100%; border-radius: 10px; border: 2px solid #ddd; cursor: crosshair;"></div>
                            <small class="text-muted"><i class="bi bi-info-circle"></i> Klik pada peta untuk memilih lokasi kantor. Marker akan berpindah ke lokasi yang Anda klik.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Bonus per Kehadiran (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="bonus_per_kehadiran" 
                                           value="{{ $pengaturans['bonus_per_kehadiran']->value ?? '' }}" 
                                           min="0" required>
                                    <small class="text-muted">Nominal bonus yang diberikan per hari kehadiran</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="mb-3"><i class="bi bi-clock"></i> Batas Waktu Absensi</h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jam Mulai Absen Masuk <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam_masuk_mulai" 
                                           value="{{ $pengaturans['jam_masuk_mulai']->value ?? '06:00' }}" required>
                                    <small class="text-muted">Karyawan bisa mulai absen masuk dari jam ini</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jam Batas Absen Masuk <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam_masuk_selesai" 
                                           value="{{ $pengaturans['jam_masuk_selesai']->value ?? '09:00' }}" required>
                                    <small class="text-muted">Batas akhir absen masuk. Lewat jam ini dianggap telat atau tidak bisa absen</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jam Mulai Absen Keluar <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam_keluar_mulai" 
                                           value="{{ $pengaturans['jam_keluar_mulai']->value ?? '16:00' }}" required>
                                    <small class="text-muted">Karyawan bisa mulai absen keluar dari jam ini</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Jam Batas Absen Keluar <span class="text-danger">*</span></label>
                                    <input type="time" class="form-control" name="jam_keluar_selesai" 
                                           value="{{ $pengaturans['jam_keluar_selesai']->value ?? '20:00' }}" required>
                                    <small class="text-muted">Batas akhir absen keluar. Lewat jam ini tidak bisa absen keluar</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="bi bi-info-circle"></i> Informasi Penting:</h6>
                            <ul class="mb-0">
                                <li><strong>IP WiFi Kantor:</strong> Sistem akan validasi 3 segmen pertama saja (contoh: 172.22.4.x, digit terakhir bebas)</li>
                                <li><strong>Pilih Lokasi di Peta:</strong> Klik pada peta di atas untuk memilih lokasi kantor. Koordinat akan terisi otomatis.</li>
                                <li><strong>Batas Waktu:</strong> Karyawan hanya bisa absen dalam rentang waktu yang ditentukan</li>
                                <li><strong>Gunakan GPS:</strong> Klik tombol "📍 Gunakan Lokasi Saya" untuk menggunakan lokasi GPS Anda saat ini</li>
                            </ul>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Card Info Lokasi Saat Ini -->
            <div class="card mt-3">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0 text-white"><i class="bi bi-geo-alt"></i> Lokasi Kantor Saat Ini</h5>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
                    <div class="mt-3">
                        <p class="mb-2"><strong>Koordinat:</strong> {{ $pengaturans['latitude_kantor']->value ?? '-' }}, {{ $pengaturans['longitude_kantor']->value ?? '-' }}</p>
                        <p class="mb-0"><strong>Radius:</strong> {{ $pengaturans['radius_absen']->value ?? '-' }} meter</p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '{{ session("success") }}',
        timer: 2000
    });
</script>
@endif

<!-- Leaflet CSS & JS untuk OpenStreetMap -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
// Tunggu DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize map untuk PICKER (form atas)
    const initialLat = parseFloat("{{ $pengaturans['latitude_kantor']->value ?? '-6.200000' }}");
    const initialLng = parseFloat("{{ $pengaturans['longitude_kantor']->value ?? '106.816666' }}");
    const initialRadius = parseFloat("{{ $pengaturans['radius_absen']->value ?? '100' }}");

    // Map Picker dengan OpenStreetMap
    const mapPicker = L.map('mapPicker').setView([initialLat, initialLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(mapPicker);

    // Marker yang bisa dipindah
    const pickerMarker = L.marker([initialLat, initialLng], {
        draggable: true
    }).addTo(mapPicker);

    // Circle radius
    const pickerCircle = L.circle([initialLat, initialLng], {
        color: 'red',
        fillColor: '#f03',
        fillOpacity: 0.3,
        radius: initialRadius
    }).addTo(mapPicker);

    // Update input fields ketika marker dipindah
    function updateCoordinates(lat, lng) {
        document.getElementById('latitude_kantor').value = lat.toFixed(8);
        document.getElementById('longitude_kantor').value = lng.toFixed(8);
        
        pickerMarker.setLatLng([lat, lng]);
        pickerCircle.setLatLng([lat, lng]);
        
        pickerMarker.bindPopup('<b>Lokasi Terpilih</b><br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6)).openPopup();
    }

    // Klik di map untuk pilih lokasi
    mapPicker.on('click', function(e) {
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });

    // Drag marker untuk pilih lokasi
    pickerMarker.on('dragend', function(e) {
        const position = pickerMarker.getLatLng();
        updateCoordinates(position.lat, position.lng);
    });

    // Update radius circle ketika input radius berubah
    const radiusInput = document.querySelector('input[name="radius_absen"]');
    if (radiusInput) {
        radiusInput.addEventListener('input', function(e) {
            const newRadius = parseFloat(e.target.value) || 100;
            pickerCircle.setRadius(newRadius);
        });
    }

    // Buat custom control button untuk GPS di dalam peta
    L.Control.LocateButton = L.Control.extend({
        onAdd: function(map) {
            const btn = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');
            btn.innerHTML = '📍';
            btn.title = 'Gunakan Lokasi Saya';
            btn.style.backgroundColor = 'white';
            btn.style.width = '34px';
            btn.style.height = '34px';
            btn.style.border = '2px solid rgba(0,0,0,0.2)';
            btn.style.borderRadius = '4px';
            btn.style.cursor = 'pointer';
            btn.style.fontSize = '18px';
            btn.style.display = 'flex';
            btn.style.alignItems = 'center';
            btn.style.justifyContent = 'center';

            btn.onmouseover = function() {
                this.style.backgroundColor = '#f4f4f4';
            };
            btn.onmouseout = function() {
                this.style.backgroundColor = 'white';
            };

            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!navigator.geolocation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'GPS Tidak Didukung',
                        text: 'Browser Anda tidak mendukung Geolocation API.'
                    });
                    return;
                }

                // Tampilkan loading
                btn.innerHTML = '⏳';
                btn.disabled = true;
                btn.style.cursor = 'wait';

                // Options untuk GPS dengan timeout lebih panjang
                const gpsOptions = {
                    enableHighAccuracy: false, // false = lebih cepat
                    timeout: 20000, // Timeout 20 detik (lebih lama untuk sinyal lemah)
                    maximumAge: 0 // Selalu ambil lokasi fresh
                };

                // Coba ambil lokasi dengan GPS
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        const accuracy = position.coords.accuracy;
                        
                        updateCoordinates(lat, lng);
                        mapPicker.setView([lat, lng], 18);
                        
                        // Reset button
                        btn.innerHTML = '✓';
                        btn.style.backgroundColor = '#4CAF50';
                        btn.style.color = 'white';
                        setTimeout(() => {
                            btn.innerHTML = '📍';
                            btn.style.backgroundColor = 'white';
                            btn.style.color = 'black';
                            btn.disabled = false;
                            btn.style.cursor = 'pointer';
                        }, 2000);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Lokasi Berhasil Diambil!',
                            html: 'Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6) + '<br><small>Akurasi: ±' + Math.round(accuracy) + ' meter</small>',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    },
                    function(error) {
                        // Reset button
                        btn.innerHTML = '📍';
                        btn.style.backgroundColor = 'white';
                        btn.style.color = 'black';
                        btn.disabled = false;
                        btn.style.cursor = 'pointer';

                        // Jika timeout atau error, tawarkan gunakan IP geolocation
                        if (error.code === error.TIMEOUT || error.code === error.POSITION_UNAVAILABLE) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'GPS Tidak Dapat Diakses',
                                html: 'Sinyal GPS lemah atau tidak tersedia.<br><br><strong>Solusi:</strong><br>1. <strong>Klik langsung di peta</strong> untuk pilih lokasi manual<br>2. Gunakan pencarian lokasi dengan IP (kurang akurat)<br>3. Pastikan Anda di tempat terbuka dan coba lagi',
                                showCancelButton: true,
                                confirmButtonText: 'Gunakan IP Location (Cepat)',
                                cancelButtonText: 'Pilih Manual di Peta',
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#6c757d'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Gunakan IP-based geolocation sebagai fallback
                                    fetch('https://ipapi.co/json/')
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.latitude && data.longitude) {
                                                updateCoordinates(data.latitude, data.longitude);
                                                mapPicker.setView([data.latitude, data.longitude], 16);
                                                
                                                Swal.fire({
                                                    icon: 'info',
                                                    title: 'Lokasi Berdasarkan IP',
                                                    html: 'Lat: ' + data.latitude.toFixed(6) + '<br>Lng: ' + data.longitude.toFixed(6) + '<br><small>Lokasi: ' + data.city + ', ' + data.region + '<br><strong>⚠️ Akurasi rendah (±2-5km), sesuaikan di peta!</strong></small>',
                                                    timer: 5000
                                                });
                                            } else {
                                                Swal.fire('Error', 'Gagal mendapatkan lokasi dari IP. Silakan pilih manual di peta.', 'error');
                                            }
                                        })
                                        .catch(() => {
                                            Swal.fire('Error', 'Gagal mengakses layanan lokasi IP. Silakan pilih manual di peta.', 'error');
                                        });
                                } else {
                                    // User pilih cancel, beri highlight di peta
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Pilih Lokasi di Peta',
                                        text: 'Klik pada peta untuk memilih lokasi kantor Anda',
                                        toast: true,
                                        position: 'top',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                    // Blink effect pada peta
                                    mapPicker.getContainer().style.border = '3px solid #ff9800';
                                    setTimeout(() => {
                                        mapPicker.getContainer().style.border = '2px solid #ddd';
                                    }, 2000);
                                }
                            });
                        } else {
                            // Permission denied atau error lainnya
                            let errorMsg = 'Pastikan GPS aktif dan izin lokasi diberikan.';
                            let errorTips = '';
                            
                            if (error.code === error.PERMISSION_DENIED) {
                                errorMsg = 'Izin lokasi ditolak.';
                                errorTips = '<small>Klik ikon 🔒 atau lokasi di address bar browser, lalu izinkan akses lokasi.<br><br><strong>Atau klik langsung di peta untuk pilih manual.</strong></small>';
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Mengambil Lokasi',
                                html: errorMsg + '<br><br>' + errorTips
                            });
                        }
                    },
                    gpsOptions
                );
            };

            return btn;
        },
        onRemove: function(map) {
            // Nothing to do here
        }
    });

    L.control.locateButton = function(opts) {
        return new L.Control.LocateButton(opts);
    }

    // Tambahkan button ke map
    L.control.locateButton({ position: 'topleft' }).addTo(mapPicker);

    // Initialize map untuk PREVIEW (card bawah)
    const previewLat = parseFloat("{{ $pengaturans['latitude_kantor']->value ?? '-6.200000' }}");
    const previewLng = parseFloat("{{ $pengaturans['longitude_kantor']->value ?? '106.816666' }}");
    const previewRadius = parseFloat("{{ $pengaturans['radius_absen']->value ?? '100' }}");

    const previewMap = L.map('map').setView([previewLat, previewLng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(previewMap);

    // Marker preview
    const previewMarker = L.marker([previewLat, previewLng]).addTo(previewMap)
        .bindPopup('<b>Lokasi Kantor</b><br>Latitude: ' + previewLat + '<br>Longitude: ' + previewLng)
        .openPopup();

    // Circle preview
    const previewCircle = L.circle([previewLat, previewLng], {
        color: 'blue',
        fillColor: '#30f',
        fillOpacity: 0.2,
        radius: previewRadius
    }).addTo(previewMap);
});
</script>
@endsection
