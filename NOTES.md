# CATATAN PENTING - Sistem Absensi FansNet

## ⚠️ PENTING UNTUK PRODUCTION

### 1. HTTPS Wajib untuk Kamera & GPS
Fitur kamera dan GPS browser **MEMERLUKAN HTTPS** untuk security. 
- Development (localhost): HTTP bisa digunakan
- Production: **WAJIB HTTPS**

Setup SSL/HTTPS:
```bash
# Menggunakan Let's Encrypt (Gratis)
sudo certbot --nginx -d domain.com
```

### 2. Pengaturan IP WiFi Kantor
IP yang diset di pengaturan harus **IP LOKAL** WiFi kantor, bukan IP public.

Cara cek IP lokal:
- Windows: `ipconfig` → lihat IPv4 Address
- Linux/Mac: `ifconfig` atau `ip addr`

Contoh IP lokal: `192.168.1.1`, `172.22.4.1`, `10.0.0.1`

### 3. Permission Storage Folder
Pastikan folder storage bisa ditulis:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 4. Koordinat Lokasi Kantor
Gunakan Google Maps untuk mendapatkan koordinat yang akurat:
1. Buka maps.google.com
2. Klik kanan pada lokasi kantor
3. Copy koordinat yang muncul
4. Format: Latitude, Longitude (contoh: -6.200000, 106.816666)

### 5. Browser Support
Pastikan menggunakan browser modern:
- ✅ Chrome/Edge (Recommended)
- ✅ Firefox
- ✅ Safari (iOS/Mac)
- ❌ IE (Not Supported)

### 6. Mobile Device Requirements
Untuk absensi di mobile:
- GPS harus aktif
- Izin kamera diberikan
- Izin lokasi diberikan
- Koneksi WiFi kantor aktif

## 🔧 Testing & Development

### Testing IP Validation (Bypass untuk Development)
Jika ingin bypass IP validation untuk testing, edit `AbsensiController.php`:

```php
// Comment baris ini untuk bypass IP check
// if ($request->ip() !== $ipKantor) {
//     return response()->json(['success' => false, ...]);
// }
```

### Testing GPS (Fake Location)
Untuk testing GPS di Chrome DevTools:
1. Buka DevTools (F12)
2. Menu ⋮ → More tools → Sensors
3. Set custom location

## 📝 Checklist Deployment Production

- [ ] Setup HTTPS (SSL Certificate)
- [ ] Update `.env` dengan konfigurasi production
- [ ] Set `APP_ENV=production` dan `APP_DEBUG=false`
- [ ] Jalankan `php artisan config:cache`
- [ ] Jalankan `php artisan route:cache`
- [ ] Jalankan `php artisan view:cache`
- [ ] Set permission storage folder
- [ ] Backup database secara berkala
- [ ] Update IP WiFi kantor di pengaturan
- [ ] Update koordinat lokasi kantor
- [ ] Test absensi dari device mobile
- [ ] Setup cron job untuk backup otomatis

## 🛠 Maintenance

### Backup Database
```bash
# Manual backup
mysqldump -u root -p absensi_fansnet > backup.sql

# Restore
mysql -u root -p absensi_fansnet < backup.sql
```

### Clear All Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Update Composer Dependencies
```bash
composer update
```

## 🐛 Common Issues

### Issue: Foto tidak tersimpan
**Solusi**: 
```bash
php artisan storage:link
chmod -R 775 storage
```

### Issue: GPS tidak akurat
**Solusi**: 
- Pastikan device di outdoor atau dekat jendela
- Tunggu beberapa detik hingga GPS lock
- Refresh halaman

### Issue: IP tidak valid (padahal sudah di WiFi kantor)
**Solusi**:
- Cek IP dengan `ipconfig` atau `ifconfig`
- Update IP di menu Pengaturan Sistem
- Pastikan tidak menggunakan VPN
- Cek apakah menggunakan IP dynamic (DHCP)

### Issue: Kamera tidak muncul
**Solusi**:
- Berikan izin kamera di browser
- Tutup aplikasi lain yang menggunakan kamera
- Gunakan HTTPS di production
- Coba browser lain

## 📊 Database Schema

```sql
-- Tabel karyawans
id, nama, jabatan, is_active, created_at, updated_at

-- Tabel pengaturans
id, key, value, description, created_at, updated_at

-- Tabel absensis
id, karyawan_id, tanggal, jam_masuk, jam_keluar,
foto_masuk, foto_keluar, latitude_masuk, longitude_masuk,
latitude_keluar, longitude_keluar, ip_address, status,
keterangan, created_at, updated_at

-- Index penting
UNIQUE(karyawan_id, tanggal) pada tabel absensis
```

## 🔐 Security Best Practices

1. **Ganti password admin default** setelah instalasi
2. **Backup database** secara berkala (minimal 1x per hari)
3. **Update Laravel** ke versi terbaru secara berkala
4. **Gunakan HTTPS** di production
5. **Set strong password** untuk database
6. **Disable directory listing** di web server
7. **Monitor logs** secara berkala

## 📱 Testing Mobile

### Android
1. Sambungkan ke WiFi kantor
2. Buka Chrome
3. Akses URL aplikasi
4. Izinkan kamera & lokasi

### iOS
1. Sambungkan ke WiFi kantor
2. Buka Safari
3. Akses URL aplikasi
4. Izinkan kamera & lokasi

## 🎯 Features Roadmap (Future Enhancement)

- [ ] Push notification untuk reminder absensi
- [ ] Dashboard analytics & grafik kehadiran
- [ ] Export laporan ke berbagai format
- [ ] Multi-lokasi kantor (cabang)
- [ ] Shift kerja kustomisasi per karyawan
- [ ] Integrasi dengan sistem payroll
- [ ] Face recognition untuk validasi tambahan
- [ ] QR Code untuk quick check-in

---

**Sistem Absensi FansNet** - Built with ❤️ using Laravel 11
© 2025 - All Rights Reserved
