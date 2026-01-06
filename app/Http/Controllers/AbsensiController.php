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
    // Note: IP validation removed from backend - all validation done in frontend using WebRTC
    
    // Halaman absensi public (tanpa login)
    public function index()
    {
        // Jika bukan localhost dan bukan HTTPS, peringatkan tentang GPS/Camera
        if (!request()->secure() && !in_array(request()->getHost(), ['localhost', '127.0.0.1'])) {
            return view('absensi.secure-warning', [
                'client_ip' => request()->ip(),
                'current_url' => request()->fullUrl()
            ]);
        }
        
        $karyawans = Karyawan::active()->orderBy('nama')->get();
        
        // Ambil data absensi hari ini untuk semua karyawan
        $today = Carbon::today();
        $absensisHariIni = Absensi::whereDate('tanggal', $today)
            ->get()
            ->keyBy('karyawan_id');
        
        $latKantor = Pengaturan::get('latitude_kantor');
        $longKantor = Pengaturan::get('longitude_kantor');
        $radius = Pengaturan::get('radius_absen');
        $jamMasukMulai = Pengaturan::get('jam_masuk_mulai', '06:00');
        $jamMasukSelesai = Pengaturan::get('jam_masuk_selesai', '09:00');
        $jamKeluarMulai = Pengaturan::get('jam_keluar_mulai', '16:00');
        $jamKeluarSelesai = Pengaturan::get('jam_keluar_selesai', '20:00');

        return view('absensi.index', compact('karyawans', 'absensisHariIni', 'latKantor', 'longKantor', 'radius', 
            'jamMasukMulai', 'jamMasukSelesai', 'jamKeluarMulai', 'jamKeluarSelesai'));
    }

    // API untuk mendapatkan pengaturan IP (untuk validasi di frontend)
    public function getIpSettings(Request $request)
    {
        $ipValidationEnabled = Pengaturan::get('ip_validation_enabled', '1');
        $ipKantor = Pengaturan::get('ip_kantor', '0.0.0.0');
        
        return response()->json([
            'ip_validation_enabled' => $ipValidationEnabled == '1',
            'ip_kantor' => $ipKantor,
            'ip_kantor_segments' => explode('.', $ipKantor),
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

        // Tentukan status (telat atau hadir) berdasarkan jam masuk selesai
        $jamMasuk = Carbon::now();
        $jamMasukSelesai = Pengaturan::get('jam_masuk_selesai', '09:00');
        $status = $jamMasuk->format('H:i') > $jamMasukSelesai ? 'telat' : 'hadir';

        // Simpan foto dengan watermark
        try {
            $fotoPath = $this->saveFotoWithWatermark($request->foto, $request->latitude, $request->longitude, 'masuk');
        } catch (\Exception $e) {
            \Log::error('Save photo error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan foto. Coba lagi.'], 500);
        }

        // Simpan atau update absensi
        try {
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
        } catch (\Exception $e) {
            \Log::error('Database error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan absensi. Coba lagi.'], 500);
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
        try {
            $fotoPath = $this->saveFotoWithWatermark($request->foto, $request->latitude, $request->longitude, 'keluar');
        } catch (\Exception $e) {
            \Log::error('Save photo error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan foto. Coba lagi.'], 500);
        }

        // Update absensi
        try {
            $absensi->update([
                'jam_keluar' => Carbon::now(),
                'foto_keluar' => $fotoPath,
                'latitude_keluar' => $request->latitude,
                'longitude_keluar' => $request->longitude,
            ]);
        } catch (\Exception $e) {
            \Log::error('Database error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan absensi. Coba lagi.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Absen keluar berhasil!']);
    }

    // Simpan foto dengan watermark
    private function saveFotoWithWatermark($base64Image, $lat, $long, $type)
    {
        try {
            // Deteksi format gambar (PNG atau JPEG)
            $imageFormat = 'png';
            if (strpos($base64Image, 'data:image/jpeg') !== false) {
                $imageFormat = 'jpeg';
                $image = str_replace('data:image/jpeg;base64,', '', $base64Image);
            } else {
                $image = str_replace('data:image/png;base64,', '', $base64Image);
            }
            
            $image = str_replace(' ', '+', $image);
            $imageData = base64_decode($image);

            // Buat nama file
            $extension = $imageFormat == 'jpeg' ? 'jpg' : 'png';
            $filename = time() . '_' . $type . '_' . uniqid() . '.' . $extension;
            $path = 'absensi/' . date('Y/m');
            
            // Pastikan direktori ada
            if (!Storage::disk('public')->exists($path)) {
                Storage::disk('public')->makeDirectory($path, 0755, true);
            }

            $fullPath = storage_path('app/public/' . $path . '/' . $filename);

            // Simpan gambar sementara
            file_put_contents($fullPath, $imageData);

            // Tambahkan watermark menggunakan GD (dengan error handling)
            try {
                $this->addWatermark($fullPath, $lat, $long, $imageFormat);
            } catch (\Exception $e) {
                // Jika watermark gagal, lanjutkan tanpa watermark
                \Log::error('Watermark failed: ' . $e->getMessage());
            }

            return $path . '/' . $filename;
        } catch (\Exception $e) {
            \Log::error('Save photo failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // Tambahkan watermark ke gambar
    private function addWatermark($imagePath, $lat, $long, $format = 'png')
    {
        // Cek apakah file ada
        if (!file_exists($imagePath)) {
            throw new \Exception('Image file not found');
        }

        // Set memory limit untuk image processing
        $currentMemory = ini_get('memory_limit');
        ini_set('memory_limit', '256M');

        // Load image berdasarkan format
        if ($format == 'jpeg') {
            $image = @imagecreatefromjpeg($imagePath);
        } else {
            $image = @imagecreatefrompng($imagePath);
        }
        
        if (!$image) {
            // Restore memory limit
            ini_set('memory_limit', $currentMemory);
            throw new \Exception('Failed to create image from file');
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Enable alpha blending
        imagealphablending($image, true);
        imagesavealpha($image, true);

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

        // Simpan gambar berdasarkan format
        if ($format == 'jpeg') {
            imagejpeg($image, $imagePath, 85); // Quality 85 untuk JPEG
        } else {
            imagepng($image, $imagePath, 6); // Compression level 6 untuk PNG
        }
        
        imagedestroy($image);

        // Restore memory limit
        ini_set('memory_limit', $currentMemory);
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
