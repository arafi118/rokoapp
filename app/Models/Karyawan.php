<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;
    protected $table = 'karyawan';

    public function getlevel()
    {
        return $this->belongsTo(Level::class, 'level');
    }

    public function getanggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id');
    }
}
