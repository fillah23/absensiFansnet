<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::latest()->get();
        return view('karyawan.index', compact('karyawans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
        ]);

        Karyawan::create($request->all());

        return redirect()->back()->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
        ]);

        $karyawan->update($request->all());

        return redirect()->back()->with('success', 'Karyawan berhasil diupdate');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->back()->with('success', 'Karyawan berhasil dihapus');
    }

    public function toggleStatus(Karyawan $karyawan)
    {
        $karyawan->update(['is_active' => !$karyawan->is_active]);

        return redirect()->back()->with('success', 'Status karyawan berhasil diubah');
    }
}
