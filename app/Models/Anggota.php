<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable; // ini penting
use Illuminate\Notifications\Notifiable;

class Anggota extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "anggota";

    protected $fillable = [
        'nama',
        'nik',
        'no_kk',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'desa',
        'kecamatan',
        'kabupaten',
        'ijazah',
        'jabatan_id',
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'anggota_id');
    }

    public function semua_karyawan()
    {
        return $this->hasMany(Karyawan::class, 'anggota_id');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id');
    }
}
