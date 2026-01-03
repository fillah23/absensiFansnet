<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    // Halaman absensi public (tanpa login)
    public function index()
    {
        // Cek apakah validasi IP diaktifkan
        $ipValidationEnabled = Pengaturan::get('ip_validation_enabled', '1');
        
        // Jika validasi IP aktif, lakukan pengecekan
        if ($ipValidationEnabled == '1') {
            // Validasi IP dulu sebelum menampilkan halaman
            $ipKantor = Pengaturan::get('ip_kantor');
            $clientIp = request()->ip();
            
            // Bypass validation untuk localhost dalam development mode
            // CATATAN: Untuk production, hapus bagian ini agar IP validation ketat
            $isLocalhost = in_array($clientIp, ['127.0.0.1', '::1', 'localhost']);
            
            if (!$isLocalhost) {
                // Ambil 3 segmen pertama dari IP
                $ipKantorSegments = explode('.', $ipKantor);
                $clientIpSegments = explode('.', $clientIp);
                
                // Bandingkan 3 segmen pertama saja
                $validIp = count($ipKantorSegments) >= 3 && count($clientIpSegments) >= 3 &&
                           $ipKantorSegments[0] == $clientIpSegments[0] &&
                           $ipKantorSegments[1] == $clientIpSegments[1] &&
                           $ipKantorSegments[2] == $clientIpSegments[2];
                
                // Jika IP tidak valid, tampilkan halaman error
                if (!$validIp) {
                    $requiredIpRange = $ipKantorSegments[0] . '.' . $ipKantorSegments[1] . '.' . $ipKantorSegments[2] . '.x';
                    return view('absensi.blocked', [
                        'client_ip' => $clientIp,
                        'required_ip_range' => $requiredIpRange,
                        'show_localhost_warning' => true
                    ]);
                }
            }
        }
        
        // Jika bukan localhost dan bukan HTTPS, peringatkan tentang GPS/Camera
        if (!request()->secure() && !in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            return view('absensi.secure-warning', [
                'client_ip' => request()->ip(),
                'current_url' => request()->fullUrl()
            ]);
        }
        
        $karyawans = Karyawan::active()->orderBy('nama')->get();
        $latKantor = Pengaturan::get('latitude_kantor');
        $longKantor = Pengaturan::get('longitude_kantor');
        $radius = Pengaturan::get('radius_absen');
        $jamMasukMulai = Pengaturan::get('jam_masuk_mulai', '06:00');
        $jamMasukSelesai = Pengaturan::get('jam_masuk_selesai', '09:00');
        $jamKeluarMulai = Pengaturan::get('jam_keluar_mulai', '16:00');
        $jamKeluarSelesai = Pengaturan::get('jam_keluar_selesai', '20:00');

        return view('absensi.index', compact('karyawans', 'ipKantor', 'latKantor', 'longKantor', 'radius', 
            'jamMasukMulai', 'jamMasukSelesai', 'jamKeluarMulai', 'jamKeluarSelesai'));
    }

    // Validasi IP (3 segmen pertama saja, digit terakhir bebas)
    public function validateIp(Request $request)
    {
        // Cek apakah validasi IP diaktifkan
        $ipValidationEnabled = Pengaturan::get('ip_validation_enabled', '1');
        
        // Jika validasi IP dinonaktifkan, langsung return valid
        if ($ipValidationEnabled != '1') {
            return response()->json([
                'valid' => true,
                'client_ip' => $request->ip(),
                'required_ip_range' => 'Validasi IP dinonaktifkan'
            ]);
        }
        
        $ipKantor = Pengaturan::get('ip_kantor');
        $clientIp = $request->ip();

        // Bypass validation untuk localhost dalam development mode
        $isLocalhost = in_array($clientIp, ['127.0.0.1', '::1', 'localhost']);
        
        if ($isLocalhost) {
            return response()->json([
                'valid' => true,
                'client_ip' => $clientIp,
                'required_ip_range' => 'localhost (development mode)'
            ]);
        }

        // Ambil 3 segmen pertama dari IP (contoh: 172.22.4)
        $ipKantorSegments = explode('.', $ipKantor);
        $clientIpSegments = explode('.', $clientIp);
        
        // Bandingkan 3 segmen pertama saja
        $valid = count($ipKantorSegments) >= 3 && count($clientIpSegments) >= 3 &&
                 $ipKantorSegments[0] == $clientIpSegments[0] &&
                 $ipKantorSegments[1] == $clientIpSegments[1] &&
                 $ipKantorSegments[2] == $clientIpSegments[2];

        return response()->json([
            'valid' => $valid,
            'client_ip' => $clientIp,
            'required_ip_range' => $ipKantorSegments[0] . '.' . $ipKantorSegments[1] . '.' . $ipKantorSegments[2] . '.x'
        ]);
    }

    // Proses absen masuk
    public function absenMasuk(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'foto' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Tidak ada validasi waktu - bisa absen masuk kapan saja
        // Jika lewat jam selesai akan masuk sebagai "telat"

        // Cek apakah validasi IP diaktifkan
        $ipValidationEnabled = Pengaturan::get('ip_validation_enabled', '1');
        
        if ($ipValidationEnabled == '1') {
            // Cek IP (3 segmen pertama saja)
            $ipKantor = Pengaturan::get('ip_kantor');
            $clientIp = $request->ip();
            
            // Bypass validation untuk localhost dalam development mode
            $isLocalhost = in_array($clientIp, ['127.0.0.1', '::1', 'localhost']);
            
            if (!$isLocalhost) {
                $ipKantorSegments = explode('.', $ipKantor);
                $clientIpSegments = explode('.', $clientIp);
                
                $ipValid = count($ipKantorSegments) >= 3 && count($clientIpSegments) >= 3 &&
                           $ipKantorSegments[0] == $clientIpSegments[0] &&
                           $ipKantorSegments[1] == $clientIpSegments[1] &&
                           $ipKantorSegments[2] == $clientIpSegments[2];
                
                if (!$ipValid) {
                    return response()->json(['success' => false, 'message' => 'IP tidak valid. Pastikan Anda terhubung ke WiFi kantor.'], 403);
                }
            }
        }

        // Cek jarak
        $latKantor = floatval(Pengaturan::get('latitude_kantor'));
        $longKantor = floatval(Pengaturan::get('longitude_kantor'));
        $radius = floatval(Pengaturan::get('radius_absen'));

        $distance = $this->calculateDistance($latKantor, $longKantor, $request->latitude, $request->longitude);

        if ($distance > $radius) {
            return response()->json(['success' => false, 'message' => "Anda berada di luar radius kantor. Jarak: " . round($distance) . " meter"], 403);
        }

        // Cek apakah sudah absen hari ini
        $today = Carbon::today();
        $absensi = Absensi::where('karyawan_id', $request->karyawan_id)
                         ->where('tanggal', $today)
                         ->first();

        if ($absensi && $absensi->jam_masuk) {
            return response()->json(['success' => false, 'message' => 'Anda sudah absen masuk hari ini'], 400);
        }

        // Simpan foto dengan watermark
        $fotoPath = $this->saveFotoWithWatermark($request->foto, $request->latitude, $request->longitude, 'masuk');

        // Tentukan status (telat atau hadir) berdasarkan jam masuk selesai
        $jamMasuk = Carbon::now();
        $jamMasukSelesai = Pengaturan::get('jam_masuk_selesai', '09:00');
        $status = $jamMasuk->format('H:i') > $jamMasukSelesai ? 'telat' : 'hadir';

        // Simpan atau update absensi
        if ($absensi) {
            $absensi->update([
                'jam_masuk' => $jamMasuk,
                'foto_masuk' => $fotoPath,
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'ip_address' => $request->ip(),
                'status' => $status,
            ]);
        } else {
            Absensi::create([
                'karyawan_id' => $request->karyawan_id,
                'tanggal' => $today,
                'jam_masuk' => $jamMasuk,
                'foto_masuk' => $fotoPath,
                'latitude_masuk' => $request->latitude,
                'longitude_masuk' => $request->longitude,
                'ip_address' => $request->ip(),
                'status' => $status,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Absen masuk berhasil!', 'status' => $status]);
    }

    // Proses absen keluar
    public function absenKeluar(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'foto' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Tidak ada validasi waktu untuk absen keluar - bisa kapan saja setelah absen masuk
        // Karyawan bisa pulang lebih awal atau lembur

        // Cek apakah validasi IP diaktifkan
        $ipValidationEnabled = Pengaturan::get('ip_validation_enabled', '1');
        
        if ($ipValidationEnabled == '1') {
            // Cek IP (3 segmen pertama saja)
            $ipKantor = Pengaturan::get('ip_kantor');
            $clientIp = $request->ip();
            
            // Bypass validation untuk localhost dalam development mode
            $isLocalhost = in_array($clientIp, ['127.0.0.1', '::1', 'localhost']);
            
            if (!$isLocalhost) {
                $ipKantorSegments = explode('.', $ipKantor);
                $clientIpSegments = explode('.', $clientIp);
                
                $ipValid = count($ipKantorSegments) >= 3 && count($clientIpSegments) >= 3 &&
                           $ipKantorSegments[0] == $clientIpSegments[0] &&
                           $ipKantorSegments[1] == $clientIpSegments[1] &&
                           $ipKantorSegments[2] == $clientIpSegments[2];
                
                if (!$ipValid) {
                    return response()->json(['success' => false, 'message' => 'IP tidak valid. Pastikan Anda terhubung ke WiFi kantor.'], 403);
                }
            }
        }

        // Cek jarak
        $latKantor = floatval(Pengaturan::get('latitude_kantor'));
        $longKantor = floatval(Pengaturan::get('longitude_kantor'));
        $radius = floatval(Pengaturan::get('radius_absen'));

        $distance = $this->calculateDistance($latKantor, $longKantor, $request->latitude, $request->longitude);

        if ($distance > $radius) {
            return response()->json(['success' => false, 'message' => "Anda berada di luar radius kantor. Jarak: " . round($distance) . " meter"], 403);
        }

        // Cek apakah sudah absen masuk
        $today = Carbon::today();
        $absensi = Absensi::where('karyawan_id', $request->karyawan_id)
                         ->where('tanggal', $today)
                         ->first();

        if (!$absensi || !$absensi->jam_masuk) {
            return response()->json(['success' => false, 'message' => 'Anda belum absen masuk hari ini'], 400);
        }

        if ($absensi->jam_keluar) {
            return response()->json(['success' => false, 'message' => 'Anda sudah absen keluar hari ini'], 400);
        }

        // Simpan foto dengan watermark
        $fotoPath = $this->saveFotoWithWatermark($request->foto, $request->latitude, $request->longitude, 'keluar');

        // Update absensi
        $absensi->update([
            'jam_keluar' => Carbon::now(),
            'foto_keluar' => $fotoPath,
            'latitude_keluar' => $request->latitude,
            'longitude_keluar' => $request->longitude,
        ]);

        return response()->json(['success' => true, 'message' => 'Absen keluar berhasil!']);
    }

    // Simpan foto dengan watermark
    private function saveFotoWithWatermark($base64Image, $lat, $long, $type)
    {
        // Decode base64
        $image = str_replace('data:image/png;base64,', '', $base64Image);
        $image = str_replace(' ', '+', $image);
        $imageData = base64_decode($image);

        // Buat nama file
        $filename = time() . '_' . $type . '_' . uniqid() . '.png';
        $path = 'absensi/' . date('Y/m');
        
        // Pastikan direktori ada
        if (!Storage::disk('public')->exists($path)) {
            Storage::disk('public')->makeDirectory($path);
        }

        $fullPath = storage_path('app/public/' . $path . '/' . $filename);

        // Simpan gambar sementara
        file_put_contents($fullPath, $imageData);

        // Tambahkan watermark menggunakan GD
        $this->addWatermark($fullPath, $lat, $long);

        return $path . '/' . $filename;
    }

    // Tambahkan watermark ke gambar
    private function addWatermark($imagePath, $lat, $long)
    {
        $image = imagecreatefrompng($imagePath);
        $width = imagesx($image);
        $height = imagesy($image);

        // Warna untuk teks (putih dengan background hitam semi-transparan)
        $white = imagecolorallocate($image, 255, 255, 255);
        $black = imagecolorallocatealpha($image, 0, 0, 0, 50);

        // Font size
        $fontSize = 3;

        // Teks watermark
        $datetime = date('d-m-Y H:i:s');
        $location = "Lat: $lat, Long: $long";

        // Posisi teks (kiri bawah)
        $x = 10;
        $y = $height - 40;

        // Background untuk teks
        imagefilledrectangle($image, $x - 5, $y - 5, $x + strlen($datetime) * 8, $y + 30, $black);

        // Tambahkan teks
        imagestring($image, $fontSize, $x, $y, $datetime, $white);
        imagestring($image, $fontSize, $x, $y + 15, $location, $white);

        // Simpan gambar
        imagepng($image, $imagePath);
        imagedestroy($image);
    }

    // Hitung jarak antara dua koordinat (Haversine formula)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    // Daftar absensi (untuk admin)
    public function daftarAbsensi()
    {
        $absensis = Absensi::with('karyawan')
                          ->whereDate('tanggal', Carbon::today())
                          ->latest()
                          ->get();

        return view('absensi.daftar', compact('absensis'));
    }
}
