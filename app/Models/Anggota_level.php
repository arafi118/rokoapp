<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota_level extends Model
{
    use HasFactory;
    protected $table = 'anggota_level';

    protected $fillable = [
        'anggota_id',
        'tanggal_masuk',
        'tanggal_keluar',
        'status',
        'level_id',
        'id_urutan',
    ];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
        public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
    public function anggotaKelompok()
    {
        return $this->hasOne(Anggota_kelompok::class, 'anggota_level_id');
    }
}
