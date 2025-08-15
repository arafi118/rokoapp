<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';
    protected $fillable = [
        'karyawan_id',
        'tanggal',
        'jumlah_baik',
        'jumlah_buruk',
        'jumlah_buruk2',
        'status_validasi',
    ];

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
