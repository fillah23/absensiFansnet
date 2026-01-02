# API Documentation - Sistem Absensi FansNet

## Base URL
```
http://localhost:8000/api
```

## Authentication
Untuk endpoint yang memerlukan autentikasi, gunakan Laravel Sanctum token di header:
```
Authorization: Bearer {token}
```

---

## Public Endpoints (No Auth Required)

### 1. Validate IP
Validasi apakah IP client sesuai dengan IP WiFi kantor.

**Endpoint:** `POST /absensi/validate-ip`

**Request:**
```json
{}
```

**Response Success (200):**
```json
{
    "valid": true,
    "client_ip": "172.22.4.1",
    "required_ip": "172.22.4.1"
}
```

**Response Error (200):**
```json
{
    "valid": false,
    "client_ip": "192.168.1.100",
    "required_ip": "172.22.4.1"
}
```

---

### 2. Absen Masuk
Kirim absensi masuk dengan foto dan lokasi GPS.

**Endpoint:** `POST /absensi/masuk`

**Request:**
```json
{
    "karyawan_id": 1,
    "foto": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgA...",
    "latitude": -6.200000,
    "longitude": 106.816666
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Absen masuk berhasil!",
    "status": "hadir"
}
```

**Response Error (403):**
```json
{
    "success": false,
    "message": "IP tidak valid. Pastikan Anda terhubung ke WiFi kantor."
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "Anda sudah absen masuk hari ini"
}
```

---

### 3. Absen Keluar
Kirim absensi keluar dengan foto dan lokasi GPS.

**Endpoint:** `POST /absensi/keluar`

**Request:**
```json
{
    "karyawan_id": 1,
    "foto": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgA...",
    "latitude": -6.200000,
    "longitude": 106.816666
}
```

**Response Success (200):**
```json
{
    "success": true,
    "message": "Absen keluar berhasil!"
}
```

**Response Error (400):**
```json
{
    "success": false,
    "message": "Anda belum absen masuk hari ini"
}
```

---

## Protected Endpoints (Requires Authentication)

### 4. Get Karyawan List
Mendapatkan daftar semua karyawan.

**Endpoint:** `GET /karyawan`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama": "Budi Santoso",
            "jabatan": "Manager",
            "is_active": true,
            "created_at": "2025-12-30T10:00:00.000000Z"
        },
        {
            "id": 2,
            "nama": "Siti Nurhaliza",
            "jabatan": "Staff IT",
            "is_active": true,
            "created_at": "2025-12-30T10:00:00.000000Z"
        }
    ]
}
```

---

### 5. Create Karyawan
Menambahkan karyawan baru.

**Endpoint:** `POST /karyawan`

**Request:**
```json
{
    "nama": "Ahmad Fauzi",
    "jabatan": "Staff Admin"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "Karyawan berhasil ditambahkan",
    "data": {
        "id": 3,
        "nama": "Ahmad Fauzi",
        "jabatan": "Staff Admin",
        "is_active": true
    }
}
```

---

### 6. Update Karyawan
Mengupdate data karyawan.

**Endpoint:** `PUT /karyawan/{id}`

**Request:**
```json
{
    "nama": "Ahmad Fauzi Updated",
    "jabatan": "Senior Staff Admin"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Karyawan berhasil diupdate",
    "data": {
        "id": 3,
        "nama": "Ahmad Fauzi Updated",
        "jabatan": "Senior Staff Admin",
        "is_active": true
    }
}
```

---

### 7. Delete Karyawan
Menghapus karyawan.

**Endpoint:** `DELETE /karyawan/{id}`

**Response (200):**
```json
{
    "success": true,
    "message": "Karyawan berhasil dihapus"
}
```

---

### 8. Toggle Karyawan Status
Mengubah status aktif/nonaktif karyawan.

**Endpoint:** `POST /karyawan/{id}/toggle`

**Response (200):**
```json
{
    "success": true,
    "message": "Status karyawan berhasil diubah",
    "data": {
        "id": 3,
        "nama": "Ahmad Fauzi",
        "jabatan": "Staff Admin",
        "is_active": false
    }
}
```

---

### 9. Get Absensi Today
Mendapatkan daftar absensi hari ini.

**Endpoint:** `GET /absensi/daftar`

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "karyawan": {
                "id": 1,
                "nama": "Budi Santoso",
                "jabatan": "Manager"
            },
            "tanggal": "2025-12-30",
            "jam_masuk": "08:30:00",
            "jam_keluar": "17:00:00",
            "status": "hadir",
            "foto_masuk": "absensi/2025/12/1234567890_masuk_abc123.png",
            "latitude_masuk": "-6.200000",
            "longitude_masuk": "106.816666"
        }
    ]
}
```

---

### 10. Get Rekap Absensi
Mendapatkan rekap absensi per bulan.

**Endpoint:** `GET /rekap?bulan={bulan}&tahun={tahun}`

**Query Parameters:**
- `bulan` (optional): 1-12, default: bulan sekarang
- `tahun` (optional): tahun, default: tahun sekarang

**Example:** `GET /rekap?bulan=12&tahun=2025`

**Response (200):**
```json
{
    "success": true,
    "bulan": 12,
    "tahun": 2025,
    "bonus_per_kehadiran": 50000,
    "data": [
        {
            "karyawan": {
                "id": 1,
                "nama": "Budi Santoso",
                "jabatan": "Manager"
            },
            "hadir": 20,
            "telat": 2,
            "total_kehadiran": 22,
            "bonus": 1100000
        }
    ]
}
```

---

### 11. Get Detail Absensi Karyawan
Mendapatkan detail absensi karyawan per bulan.

**Endpoint:** `GET /rekap/{karyawan_id}?bulan={bulan}&tahun={tahun}`

**Response (200):**
```json
{
    "success": true,
    "karyawan": {
        "id": 1,
        "nama": "Budi Santoso",
        "jabatan": "Manager"
    },
    "bulan": 12,
    "tahun": 2025,
    "data": [
        {
            "id": 1,
            "tanggal": "2025-12-01",
            "jam_masuk": "08:30:00",
            "jam_keluar": "17:00:00",
            "status": "hadir",
            "foto_masuk": "absensi/2025/12/...",
            "latitude_masuk": "-6.200000",
            "longitude_masuk": "106.816666"
        }
    ]
}
```

---

### 12. Get Pengaturan
Mendapatkan semua pengaturan sistem.

**Endpoint:** `GET /pengaturan`

**Response (200):**
```json
{
    "success": true,
    "data": {
        "ip_kantor": "172.22.4.1",
        "latitude_kantor": "-6.200000",
        "longitude_kantor": "106.816666",
        "radius_absen": "100",
        "bonus_per_kehadiran": "50000"
    }
}
```

---

### 13. Update Pengaturan
Mengupdate pengaturan sistem.

**Endpoint:** `POST /pengaturan`

**Request:**
```json
{
    "ip_kantor": "172.22.4.1",
    "latitude_kantor": "-6.200000",
    "longitude_kantor": "106.816666",
    "radius_absen": "100",
    "bonus_per_kehadiran": "50000"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Pengaturan berhasil diupdate"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "nama": [
            "The nama field is required."
        ]
    }
}
```

### Unauthorized (401)
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "IP tidak valid. Pastikan Anda terhubung ke WiFi kantor."
}
```

### Not Found (404)
```json
{
    "message": "Resource not found."
}
```

### Server Error (500)
```json
{
    "message": "Server error. Please try again later."
}
```

---

## Notes

### Foto Format
Foto harus dalam format base64 dengan prefix:
```
data:image/png;base64,{base64_string}
```

### GPS Coordinates
- Latitude: -90 hingga 90
- Longitude: -180 hingga 180
- Gunakan 6 digit desimal untuk akurasi

### Status Absensi
- `hadir`: Masuk sebelum jam 09:00
- `telat`: Masuk setelah jam 09:00
- `tidak_hadir`: Tidak melakukan absensi

### IP Validation
Sistem akan memvalidasi IP client dengan IP WiFi kantor yang telah diset.
Client harus terhubung ke WiFi kantor untuk dapat melakukan absensi.

### GPS Validation
Sistem akan menghitung jarak antara lokasi client dengan lokasi kantor menggunakan rumus Haversine.
Jarak tidak boleh melebihi radius yang telah ditentukan.

---

## Future API Endpoints (Roadmap)

```
POST   /api/auth/login           - Login admin
POST   /api/auth/logout          - Logout admin
GET    /api/dashboard/stats      - Dashboard statistics
GET    /api/reports/export       - Export reports
POST   /api/notifications/send   - Send notifications
```

---

**API Documentation** - Sistem Absensi FansNet
Version 1.0 - Last Updated: December 30, 2025
