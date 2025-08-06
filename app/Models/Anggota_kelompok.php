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

}
