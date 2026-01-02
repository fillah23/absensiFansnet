<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $karyawans = Karyawan::active()->get();
        $rekap = [];
        $bonusPerKehadiran = floatval(Pengaturan::get('bonus_per_kehadiran', 50000));

        foreach ($karyawans as $karyawan) {
            $absensis = Absensi::byKaryawan($karyawan->id)
                              ->byMonth($bulan, $tahun)
                              ->get();

            $hadir = $absensis->where('status', 'hadir')->count();
            $telat = $absensis->where('status', 'telat')->count();
            $totalKehadiran = $hadir + $telat;
            $bonus = $totalKehadiran * $bonusPerKehadiran;
            
            // Ambil foto masuk terakhir
            $fotoTerakhir = $absensis->where('foto_masuk', '!=', null)
                                     ->sortByDesc('tanggal')
                                     ->first();

            $rekap[] = [
                'karyawan' => $karyawan,
                'hadir' => $hadir,
                'telat' => $telat,
                'total_kehadiran' => $totalKehadiran,
                'bonus' => $bonus,
                'foto_terakhir' => $fotoTerakhir ? $fotoTerakhir->foto_masuk : null,
            ];
        }

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('rekap.index', compact('rekap', 'bulan', 'tahun', 'bulanList', 'bonusPerKehadiran'));
    }

    public function detail(Request $request, $karyawanId)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        $karyawan = Karyawan::findOrFail($karyawanId);
        $absensis = Absensi::byKaryawan($karyawanId)
                          ->byMonth($bulan, $tahun)
                          ->orderBy('tanggal')
                          ->get();

        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('rekap.detail', compact('karyawan', 'absensis', 'bulan', 'tahun', 'bulanList'));
    }
}
