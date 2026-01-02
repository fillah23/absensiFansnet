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
            ['nama' => 'Budi Santoso', 'jabatan' => 'Manager'],
            ['nama' => 'Siti Nurhaliza', 'jabatan' => 'Staff IT'],
            ['nama' => 'Ahmad Fauzi', 'jabatan' => 'Staff Admin'],
            ['nama' => 'Dewi Lestari', 'jabatan' => 'Supervisor'],
            ['nama' => 'Rian Pratama', 'jabatan' => 'Staff Marketing'],
        ];

        foreach ($karyawans as $karyawan) {
            Karyawan::create($karyawan);
        }
    }
}
