# Sistem Absensi FansNet

Sistem absensi karyawan berbasis web dengan fitur GPS tracking, kamera real-time, dan validasi IP WiFi kantor.

## Fitur Utama

### 1. **Absensi Tanpa Login (Halaman Utama)**
- Akses kamera real-time (tidak bisa impor dari galeri)
- Validasi GPS wajib aktif
- Validasi IP WiFi kantor
- Validasi radius lokasi kantor
- Watermark otomatis (tanggal, waktu, lokasi) pada foto
- Mobile-friendly responsive design
- Absen masuk dan absen keluar

### 2. **Dashboard Admin (Perlu Login)**
- **Daftar Absensi Hari Ini**: Monitoring real-time absensi karyawan
- **CRUD Karyawan**: Kelola data karyawan (nama, jabatan, status aktif/nonaktif)
- **Pengaturan Sistem**:
  - IP WiFi kantor
  - Koordinat lokasi kantor (latitude, longitude)
  - Radius absensi (dalam meter)
  - Bonus per kehadiran
  - Map visual lokasi kantor
- **Rekap Absensi**:
  - Filter per bulan dan tahun
  - Perhitungan bonus gaji otomatis
  - Detail absensi per karyawan
  - Export ke Excel, PDF, Print

## Teknologi

- **Backend**: Laravel 11
- **Frontend**: Bootstrap 5, Blade Templates
- **Database**: MySQL
- **JavaScript**: jQuery, Leaflet (maps), WebRTC (camera)
- **API**: Geolocation API, MediaDevices API

## Instalasi

### 1. Clone Repository
```bash
cd absensiFansNet
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_fansnet
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Migrasi Database
```bash
php artisan migrate
```

### 5. Create Storage Link
```bash
php artisan storage:link
```

### 6. Buat User Admin
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@fansnet.com',
    'password' => bcrypt('password123')
]);
```

### 7. Jalankan Server
```bash
php artisan serve
```

Akses aplikasi di: `http://localhost:8000`

## Konfigurasi Awal

### 1. Login sebagai Admin
- URL: `/login`
- Email: `admin@fansnet.com`
- Password: `password123`

### 2. Setup Pengaturan Sistem
Masuk ke menu **Pengaturan Sistem** dan atur:

- **IP WiFi Kantor**: Contoh `172.22.4.1` (sesuaikan dengan IP WiFi kantor Anda)
- **Latitude Kantor**: Contoh `-6.200000` (dapat dari Google Maps)
- **Longitude Kantor**: Contoh `106.816666` (dapat dari Google Maps)
- **Radius Absen**: Contoh `100` (dalam meter)
- **Bonus per Kehadiran**: Contoh `50000` (Rp 50.000)

**Cara mendapatkan koordinat:**
1. Buka [Google Maps](https://www.google.com/maps)
2. Cari lokasi kantor Anda
3. Klik kanan pada lokasi
4. Klik koordinat yang muncul
5. Copy Latitude dan Longitude

### 3. Tambah Data Karyawan
Masuk ke menu **Data Karyawan** dan tambahkan karyawan dengan:
- Nama
- Jabatan

## Cara Menggunakan

### Untuk Karyawan (Absensi):
1. Buka halaman utama: `http://localhost:8000` atau IP server
2. **Pastikan**:
   - Terhubung ke WiFi kantor
   - GPS/Lokasi aktif
   - Izin kamera diberikan
   - Berada dalam radius kantor
3. Pilih nama karyawan dari dropdown
4. Pilih jenis absensi (Masuk/Keluar)
5. Ambil foto selfie
6. Klik "Kirim Absensi"

### Untuk Admin:
1. Login di `/login`
2. **Daftar Absensi**: Lihat siapa saja yang sudah absen hari ini
3. **Data Karyawan**: Kelola data karyawan
4. **Rekap Absensi**: 
   - Pilih bulan dan tahun
   - Lihat total kehadiran dan bonus per karyawan
   - Export laporan
   - Klik "Detail" untuk melihat absensi harian per karyawan
5. **Pengaturan Sistem**: Update konfigurasi sistem

## Struktur Database

### Tabel `karyawans`
- id
- nama
- jabatan
- is_active (status aktif/nonaktif)

### Tabel `pengaturans`
- id
- key (ip_kantor, latitude_kantor, longitude_kantor, radius_absen, bonus_per_kehadiran)
- value
- description

### Tabel `absensis`
- id
- karyawan_id
- tanggal
- jam_masuk
- jam_keluar
- foto_masuk (path)
- foto_keluar (path)
- latitude_masuk
- longitude_masuk
- latitude_keluar
- longitude_keluar
- ip_address
- status (hadir/telat/tidak_hadir)
- keterangan

## Validasi Sistem

### 1. Validasi IP WiFi Kantor
Sistem hanya menerima absensi dari IP WiFi kantor yang sudah diset. Jika IP tidak sesuai, absensi ditolak.

### 2. Validasi Lokasi GPS
Sistem menghitung jarak karyawan dari kantor menggunakan rumus Haversine. Jika di luar radius, absensi ditolak.

### 3. Validasi Waktu
- Absen masuk sebelum jam 09:00 = Status "Hadir"
- Absen masuk setelah jam 09:00 = Status "Telat"
- Satu karyawan hanya bisa absen masuk dan keluar 1x per hari

### 4. Validasi Kamera
- Hanya bisa menggunakan kamera real-time (tidak bisa upload dari galeri)
- Foto otomatis diberi watermark tanggal, waktu, dan lokasi GPS

## Perhitungan Bonus

```
Total Bonus = (Jumlah Hadir + Jumlah Telat) × Bonus per Kehadiran
```

Contoh:
- Hadir: 20 hari
- Telat: 2 hari
- Bonus per Kehadiran: Rp 50.000
- **Total Bonus = 22 × 50.000 = Rp 1.100.000**

## Troubleshooting

### GPS Tidak Berfungsi
- Pastikan browser memiliki izin akses lokasi
- Aktifkan GPS di perangkat
- Gunakan HTTPS (untuk production)

### Kamera Tidak Muncul
- Berikan izin kamera di browser
- Pastikan tidak ada aplikasi lain yang menggunakan kamera
- Gunakan HTTPS (untuk production)

### IP Tidak Valid
- Cek IP WiFi kantor dengan `ipconfig` (Windows) atau `ifconfig` (Linux/Mac)
- Update IP di menu Pengaturan Sistem
- Pastikan terhubung ke WiFi kantor yang sama

### Lokasi Di Luar Radius
- Cek koordinat kantor sudah benar
- Tingkatkan radius absensi jika perlu
- Pastikan GPS mendapat sinyal yang baik

## Production Deployment

### 1. Gunakan HTTPS
Kamera dan GPS memerlukan HTTPS untuk keamanan:
```bash
# Install SSL Certificate (Let's Encrypt)
sudo certbot --nginx -d domain.com
```

### 2. Optimize Performance
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Set Permission Storage
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## Lisensi

Proprietary - All Rights Reserved

---

**Sistem Absensi FansNet** - © 2025

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
