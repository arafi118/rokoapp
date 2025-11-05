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
    protected $guarded = ['id'];

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'anggota_id');
    }

    public function semua_karyawan()
    {
        return $this->hasMany(Karyawan::class, 'anggota_id');
    }

    public function getjabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan');
    }
    public function group()
    {
        return $this->hasMany(Group::class, 'mandor', 'id');
    }
}
