<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    protected $table = 'kelompok';
    protected $guarded = ['id'];

    protected $fillable = [
        'anggota_id',
        'nama',
    ];

    public function anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id', 'id');
    }

    public function anggota_kelompok()
    {
        return $this->hasMany(Anggota_kelompok::class, 'kelompok_id', 'id');
    }
}
