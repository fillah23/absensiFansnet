<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jabatan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
