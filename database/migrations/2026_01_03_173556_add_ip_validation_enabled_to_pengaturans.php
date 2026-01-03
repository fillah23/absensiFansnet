<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default value untuk ip_validation_enabled (aktif by default)
        DB::table('pengaturans')->insert([
            'key' => 'ip_validation_enabled',
            'value' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus setting ip_validation_enabled
        DB::table('pengaturans')->where('key', 'ip_validation_enabled')->delete();
    }
};
