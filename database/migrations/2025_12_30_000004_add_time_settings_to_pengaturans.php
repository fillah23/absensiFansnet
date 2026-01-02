<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            // Jika belum ada, tambahkan field batas waktu absensi
        });

        // Insert default time settings
        DB::table('pengaturans')->insert([
            [
                'key' => 'jam_masuk_mulai',
                'value' => '06:00',
                'description' => 'Jam mulai bisa absen masuk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'jam_masuk_selesai',
                'value' => '08:00',
                'description' => 'Jam batas absen masuk',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'jam_keluar_mulai',
                'value' => '16:00',
                'description' => 'Jam mulai bisa absen keluar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'jam_keluar_selesai',
                'value' => '18:00',
                'description' => 'Jam batas absen keluar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Insert default admin user untuk login
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('pengaturans')->whereIn('key', [
            'jam_masuk_mulai',
            'jam_masuk_selesai',
            'jam_keluar_mulai',
            'jam_keluar_selesai'
        ])->delete();
    }
};
