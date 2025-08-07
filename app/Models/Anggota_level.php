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
}
