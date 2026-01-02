<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('pengaturans')->insert([
            [
                'key' => 'ip_kantor',
                'value' => '172.22.4.1',
                'description' => 'IP WiFi Kantor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'latitude_kantor',
                'value' => '-8.19545169665497',
                'description' => 'Latitude Kantor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'longitude_kantor',
                'value' => '113.64377971259721',
                'description' => 'Longitude Kantor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'radius_absen',
                'value' => '100',
                'description' => 'Radius Absen (meter)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'bonus_per_kehadiran',
                'value' => '15000',
                'description' => 'Bonus per Kehadiran',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturans');
    }
};
