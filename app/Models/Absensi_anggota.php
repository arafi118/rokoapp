<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi_anggota extends Model
{
    use HasFactory;
    protected $table = 'absensi_anggota';
    protected $guarded = [];

    public function anggotaKelompok()
    {
        return $this->belongsTo(Anggota_kelompok::class, 'anggota_kelompok_id');
    }

    public function absensi()
    {
        return $this->belongsTo(Absensi::class, 'absensi_id');
    }
}
