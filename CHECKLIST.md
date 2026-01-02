# Setup Checklist - Sistem Absensi FansNet

Gunakan checklist ini untuk memastikan sistem telah di-setup dengan benar.

## ✅ Initial Setup

- [ ] Clone/copy project ke folder
- [ ] Copy `.env.example` menjadi `.env`
- [ ] Edit `.env` sesuai konfigurasi database
- [ ] Jalankan `composer install`
- [ ] Jalankan `php artisan key:generate`
- [ ] Buat database `absensi_fansnet` di MySQL
- [ ] Jalankan `php artisan migrate`
- [ ] Jalankan `php artisan storage:link`

## ✅ Create Admin User

- [ ] Jalankan `php artisan tinker`
- [ ] Create user admin dengan command:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@fansnet.com',
    'password' => bcrypt('password123')
]);
```
- [ ] Exit dari tinker

## ✅ (Optional) Seed Sample Data

- [ ] Jalankan `php artisan db:seed --class=KaryawanSeeder`
- [ ] Verifikasi data karyawan sample sudah masuk

## ✅ Test Server

- [ ] Jalankan `php artisan serve`
- [ ] Buka browser ke `http://localhost:8000`
- [ ] Verifikasi halaman absensi muncul

## ✅ Login Admin

- [ ] Buka `http://localhost:8000/login`
- [ ] Login dengan:
  - Email: `admin@fansnet.com`
  - Password: `password123`
- [ ] Verifikasi berhasil masuk ke dashboard

## ✅ Setup Pengaturan Sistem

- [ ] Masuk ke menu **Pengaturan Sistem**
- [ ] Atur **IP WiFi Kantor** (contoh: `172.22.4.1`)
  - Cek IP lokal dengan `ipconfig` (Windows) atau `ifconfig` (Linux/Mac)
- [ ] Atur **Latitude Kantor** (contoh: `-6.200000`)
  - Dapatkan dari Google Maps
- [ ] Atur **Longitude Kantor** (contoh: `106.816666`)
  - Dapatkan dari Google Maps
- [ ] Atur **Radius Absen** (contoh: `100` meter)
- [ ] Atur **Bonus per Kehadiran** (contoh: `50000`)
- [ ] Klik **Simpan Pengaturan**
- [ ] Verifikasi map menampilkan lokasi yang benar

## ✅ Tambah Karyawan

- [ ] Masuk ke menu **Data Karyawan**
- [ ] Klik **Tambah Karyawan**
- [ ] Tambahkan minimal 1 karyawan untuk testing
- [ ] Verifikasi karyawan muncul di tabel

## ✅ Test Halaman Absensi (Public)

- [ ] Logout dari admin
- [ ] Buka `http://localhost:8000`
- [ ] Verifikasi checklist berikut:

### Validasi IP
- [ ] Status box menampilkan informasi IP
- [ ] IP sesuai dengan yang di-set (atau bypass untuk testing)

### Validasi Kamera
- [ ] Browser meminta izin akses kamera
- [ ] Berikan izin kamera
- [ ] Video preview muncul
- [ ] Tombol capture muncul

### Validasi GPS
- [ ] Browser meminta izin akses lokasi
- [ ] Berikan izin lokasi
- [ ] Status box menampilkan GPS berhasil (atau jarak)

### Test Absen Masuk
- [ ] Pilih nama karyawan dari dropdown
- [ ] Pilih "Absen Masuk"
- [ ] Klik tombol capture untuk ambil foto
- [ ] Foto preview muncul dengan watermark
- [ ] Klik "Kirim Absensi"
- [ ] Alert sukses muncul
- [ ] Halaman refresh

### Test Absen Keluar
- [ ] Pilih karyawan yang sama
- [ ] Pilih "Absen Keluar"
- [ ] Ambil foto dan kirim
- [ ] Verifikasi berhasil

## ✅ Verifikasi Data Absensi (Admin)

- [ ] Login kembali sebagai admin
- [ ] Masuk ke **Daftar Absensi**
- [ ] Verifikasi absensi tadi muncul
- [ ] Klik tombol **Lihat** pada foto
- [ ] Verifikasi foto muncul dengan watermark
- [ ] Verifikasi data lokasi GPS tersimpan

## ✅ Test Rekap Absensi

- [ ] Masuk ke menu **Rekap Absensi**
- [ ] Pilih bulan dan tahun saat ini
- [ ] Klik **Tampilkan**
- [ ] Verifikasi data kehadiran muncul
- [ ] Verifikasi perhitungan bonus benar
- [ ] Klik **Detail** pada salah satu karyawan
- [ ] Verifikasi detail absensi per hari muncul

## ✅ Test Export/Print

- [ ] Di halaman Rekap Absensi
- [ ] Klik tombol **Export Excel**
- [ ] Verifikasi file Excel terdownload
- [ ] Klik tombol **Export PDF**
- [ ] Verifikasi PDF muncul
- [ ] Klik tombol **Print**
- [ ] Verifikasi print preview muncul

## ✅ Test CRUD Karyawan

### Create
- [ ] Tambah karyawan baru
- [ ] Verifikasi berhasil ditambahkan

### Update
- [ ] Edit data karyawan
- [ ] Verifikasi data terupdate

### Toggle Status
- [ ] Klik tombol status (Aktif/Nonaktif)
- [ ] Verifikasi status berubah

### Delete
- [ ] Hapus karyawan (gunakan karyawan test)
- [ ] Konfirmasi penghapusan
- [ ] Verifikasi karyawan terhapus

## ✅ Test Update Pengaturan

- [ ] Masuk ke **Pengaturan Sistem**
- [ ] Ubah salah satu nilai (misal: radius)
- [ ] Simpan
- [ ] Verifikasi perubahan tersimpan
- [ ] Verifikasi map terupdate (jika ubah koordinat)

## ✅ Test Mobile Responsive

- [ ] Buka halaman absensi di mobile browser
- [ ] Verifikasi tampilan responsive
- [ ] Test absensi dari mobile
- [ ] Verifikasi kamera berfungsi di mobile
- [ ] Verifikasi GPS berfungsi di mobile

## ✅ Security Test

### IP Validation
- [ ] Coba akses dari IP berbeda (atau ubah IP di pengaturan)
- [ ] Verifikasi absensi ditolak jika IP tidak sesuai

### GPS Validation
- [ ] Coba fake location di luar radius
- [ ] Verifikasi absensi ditolak jika di luar radius

### Double Absensi
- [ ] Coba absen masuk 2x di hari yang sama
- [ ] Verifikasi ditolak dengan pesan sudah absen

### Admin Access
- [ ] Logout dari admin
- [ ] Coba akses `http://localhost:8000/dashboard` tanpa login
- [ ] Verifikasi redirect ke login

## ✅ File Storage Check

- [ ] Masuk ke folder `storage/app/public/absensi`
- [ ] Verifikasi foto absensi tersimpan
- [ ] Verifikasi folder struktur: `YYYY/MM/`
- [ ] Buka salah satu foto
- [ ] Verifikasi watermark muncul

## ✅ Database Check

```sql
-- Jalankan query ini di MySQL
SELECT * FROM karyawans;
SELECT * FROM pengaturans;
SELECT * FROM absensis;
SELECT * FROM users;
```

- [ ] Verifikasi semua tabel ada dan terisi

## ✅ Performance Check

- [ ] Test load halaman < 2 detik
- [ ] Test upload foto < 3 detik
- [ ] Verifikasi tidak ada error di console browser
- [ ] Verifikasi tidak ada error di `storage/logs/laravel.log`

## ✅ Production Ready Checklist

Jika akan deploy ke production:

- [ ] Set `APP_ENV=production` di `.env`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Setup HTTPS/SSL Certificate
- [ ] Jalankan `php artisan config:cache`
- [ ] Jalankan `php artisan route:cache`
- [ ] Jalankan `php artisan view:cache`
- [ ] Set permission: `chmod -R 775 storage bootstrap/cache`
- [ ] Ganti password admin default
- [ ] Update IP WiFi kantor dengan IP production
- [ ] Update koordinat lokasi kantor dengan lokasi sebenarnya
- [ ] Setup database backup automation
- [ ] Test dari berbagai device
- [ ] Setup monitoring & logs

## ✅ Documentation Check

- [ ] Baca `README.md`
- [ ] Baca `QUICK_START.md`
- [ ] Baca `NOTES.md`
- [ ] Baca `COMMANDS.md`
- [ ] Simpan dokumentasi untuk referensi

---

## 🎉 All Done!

Jika semua checklist di atas sudah ✅, maka sistem siap digunakan!

### Next Steps:
1. Train user/karyawan cara menggunakan sistem
2. Setup backup database rutin
3. Monitor logs secara berkala
4. Update sistem secara berkala

### Support:
Jika ada masalah, cek:
- `storage/logs/laravel.log`
- Browser console (F12)
- `NOTES.md` untuk troubleshooting

---

**Setup Checklist** - Sistem Absensi FansNet
Happy Coding! 🚀
