<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $pengaturans = Pengaturan::all()->keyBy('key');
        return view('pengaturan.index', compact('pengaturans'));
    }

    public function update(Request $request)
    {
        // Buat validation rules dinamis berdasarkan toggle IP validation
        $rules = [
            'latitude_kantor' => 'required|numeric',
            'longitude_kantor' => 'required|numeric',
            'radius_absen' => 'required|numeric|min:1',
            'bonus_per_kehadiran' => 'required|numeric|min:0',
            'jam_masuk_mulai' => 'required|date_format:H:i',
            'jam_masuk_selesai' => 'required|date_format:H:i',
            'jam_keluar_mulai' => 'required|date_format:H:i',
            'jam_keluar_selesai' => 'required|date_format:H:i',
        ];
        
        // Jika IP validation enabled, tambahkan validasi untuk ip_kantor
        if ($request->has('ip_validation_enabled') && $request->ip_validation_enabled == '1') {
            $rules['ip_kantor'] = 'required|ip';
        }
        
        $request->validate($rules);

        // Simpan semua pengaturan kecuali _token
        foreach ($request->except('_token') as $key => $value) {
            Pengaturan::set($key, $value);
        }
        
        // Jika toggle tidak di-check, set value menjadi 0
        if (!$request->has('ip_validation_enabled')) {
            Pengaturan::set('ip_validation_enabled', '0');
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diupdate');
    }
}
