<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendataan extends Model
{
    use HasFactory;
    protected $table = 'pendataan';
    protected $guarded = [];

    public function anggotakelompok()
    {
        return $this->belongsTo(Anggota_kelompok::class,'anggota_kelompok_id');
    }


}
