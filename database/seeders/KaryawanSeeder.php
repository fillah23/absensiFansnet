<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Karyawan;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $karyawans = [
            ['nama' => 'Andik', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Emprit', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Filla', 'jabatan' => 'NOC', 'is_active' => true],
            ['nama' => 'Lutfi', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Martono', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Mulyo Atmojo', 'jabatan' => 'HUMAS', 'is_active' => true],
            ['nama' => 'Rahma', 'jabatan' => 'ADMIN', 'is_active' => true],
            ['nama' => 'Roni', 'jabatan' => 'NOC', 'is_active' => true],
            ['nama' => 'Rudi', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Samo', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Santo', 'jabatan' => 'Teknisi', 'is_active' => true],
            ['nama' => 'Silvi', 'jabatan' => 'ADMIN', 'is_active' => true],
            ['nama' => 'Ulil', 'jabatan' => 'KOLEKTOR', 'is_active' => true],
            ['nama' => 'Yanto', 'jabatan' => 'Kepala Teknisi', 'is_active' => true],
            ['nama' => 'Zidan', 'jabatan' => 'NOC', 'is_active' => true],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::create($karyawan);
        }
    }
}
