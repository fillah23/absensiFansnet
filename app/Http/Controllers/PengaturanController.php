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
        $request->validate([
            'ip_kantor' => 'required|ip',
            'latitude_kantor' => 'required|numeric',
            'longitude_kantor' => 'required|numeric',
            'radius_absen' => 'required|numeric|min:1',
            'bonus_per_kehadiran' => 'required|numeric|min:0',
            'jam_masuk_mulai' => 'required|date_format:H:i',
            'jam_masuk_selesai' => 'required|date_format:H:i',
            'jam_keluar_mulai' => 'required|date_format:H:i',
            'jam_keluar_selesai' => 'required|date_format:H:i',
        ]);

        foreach ($request->except('_token') as $key => $value) {
            Pengaturan::set($key, $value);
        }

        return redirect()->back()->with('success', 'Pengaturan berhasil diupdate');
    }
}
