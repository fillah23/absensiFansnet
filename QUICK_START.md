# Quick Start Guide - Sistem Absensi FansNet

## Langkah Cepat Setup

### 1. Siapkan Database
```bash
# Buat database MySQL bernama: absensi_fansnet
```

### 2. Jalankan Migrasi
```bash
php artisan migrate
```

### 3. Buat User Admin
```bash
php artisan tinker
```
Kemudian jalankan:
```php
\App\Models\User::create(['name' => 'Admin', 'email' => 'admin@fansnet.com', 'password' => bcrypt('password123')]);
exit
```

### 4. (Opsional) Seed Data Karyawan Test
```bash
php artisan db:seed --class=KaryawanSeeder
```

### 5. Buat Storage Link
```bash
php artisan storage:link
```

### 6. Jalankan Server
```bash
php artisan serve
```

### 7. Akses Aplikasi
- **Halaman Absensi (Public)**: http://localhost:8000
- **Login Admin**: http://localhost:8000/login
  - Email: admin@fansnet.com
  - Password: password123

## Setup Pengaturan Pertama Kali

1. Login sebagai admin
2. Masuk ke menu **Pengaturan Sistem**
3. Atur:
   - IP WiFi Kantor (default: 172.22.4.1)
   - Latitude Kantor (contoh: -6.200000)
   - Longitude Kantor (contoh: 106.816666)
   - Radius Absen (contoh: 100 meter)
   - Bonus per Kehadiran (contoh: 50000)
4. Simpan

## Menambah Karyawan

1. Login sebagai admin
2. Masuk ke menu **Data Karyawan**
3. Klik **Tambah Karyawan**
4. Isi Nama dan Jabatan
5. Simpan

## Test Absensi

1. Buka http://localhost:8000
2. Pilih nama karyawan
3. Pilih "Absen Masuk"
4. Izinkan akses kamera dan lokasi
5. Ambil foto
6. Kirim absensi

**CATATAN**: Untuk testing lokal:
- IP validation mungkin perlu disesuaikan
- GPS validation bisa sementara di-bypass untuk testing
- Pastikan HTTPS untuk production (kamera dan GPS memerlukan HTTPS)

## Struktur Menu Admin

1. **Daftar Absensi** - Lihat absensi hari ini
2. **Data Karyawan** - CRUD karyawan
3. **Rekap Absensi** - Laporan dan bonus gaji
4. **Pengaturan Sistem** - Konfigurasi IP, lokasi, radius, bonus

## Troubleshooting Cepat

### Error: Storage link
```bash
php artisan storage:link
```

### Error: Migration
```bash
php artisan migrate:fresh
```

### Lupa Password Admin
```bash
php artisan tinker
$user = \App\Models\User::where('email', 'admin@fansnet.com')->first();
$user->password = bcrypt('password_baru');
$user->save();
exit
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Fitur Utama

✅ Absensi tanpa login dengan kamera real-time
✅ Validasi IP WiFi kantor
✅ Validasi GPS dan radius lokasi
✅ Watermark otomatis pada foto (datetime + lokasi)
✅ CRUD Karyawan
✅ Rekap absensi dan perhitungan bonus
✅ Export Excel, PDF, Print
✅ Mobile-friendly responsive

## Security Features

🔒 IP WiFi Validation
🔒 GPS Location Validation
🔒 Radius Check
🔒 Camera-only (no gallery upload)
🔒 Watermarked photos
🔒 Admin authentication required

---
**Sistem Absensi FansNet** © 2025
