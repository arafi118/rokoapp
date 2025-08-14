<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rencana extends Model
{
    use HasFactory;
    protected $table = 'rencana';
    protected $fillable = [
        'tanggal',
        'rencana_produksi',
        'rencana_kehadiran',
        'rencana_karyawan',
    ];
}
