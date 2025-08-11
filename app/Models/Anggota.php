<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Anggota extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = "anggota";

    protected $fillable = [
        'nama',
        'no_kk',
        'nik',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'kota',
        'kecamatan',
        'desa',
        'alamat',
        'status',
        'nama_bank',
        'norek',
        'tinggi_badan',
        'berat_badan',
        'ijazah',
        'jurusan',
        'tahun_lulus',
        'nama_ibu_kandung',
        'username',
        'password',
    ];

    public function level_aktif()
    {
        return $this->hasOne(Anggota_level::class, 'anggota_id')->where('status', 'aktif');
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'id', 'anggota_id');
    }
}
