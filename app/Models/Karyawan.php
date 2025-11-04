<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';
    protected $guarded = ['id'];

    public function getlevel()
    {
        return $this->belongsTo(Level::class, 'level');
    }

    public function getanggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }

    public function getabsensi()
    {
        return $this->hasMany(Absensi::class, 'karyawan_id');
    }

    public function getproduksi()
    {
        return $this->hasMany(Produksi::class, 'karyawan_id');
    }

    public function getgroup()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function getmeja()
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    public function getmutasi()
    {
        return $this->hasMany(Mutasi::class, 'karyawan_id');
    }
}
