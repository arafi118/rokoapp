<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota_kelompok extends Model
{
    use HasFactory;
    protected $table = 'anggota_kelompok';
    protected $guarded = [];
    protected $fillable = [
        'kelompok_id',
        'anggota_level_id',
    ];

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

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id', 'id');
    }

    public function anggota()
    {
        return $this->hasOneThrough(
            Anggota::class,
            Anggota_level::class,
            'id',
            'id',
            'anggota_level_id',
            'anggota_id'
        );
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
