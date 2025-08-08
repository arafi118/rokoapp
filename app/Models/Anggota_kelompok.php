<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota_kelompok extends Model
{
    use HasFactory;
    protected $table = 'anggota_kelompok';
    protected $guarded = [];
    
     public function level_aktif()
    {
        return $this->hasOne(Anggota_level::class, 'anggota_id')->where('status', 'aktif');
    }

    public function anggotaLevel()
    {
        return $this->belongsTo(Anggota_level::class, 'anggota_level_id');
    }

    public function absensiAnggota()
    {
        return $this->hasMany(Absensi_anggota::class, 'anggota_kelompok_id');
    }

    // Optional helper method
    public function sudahAbsenMasukHariIni()
    {
        return $this->absensiAnggota()
            ->whereDate('jam', now()->toDateString())
            ->where('status', 'masuk')
            ->exists();
    }
}
