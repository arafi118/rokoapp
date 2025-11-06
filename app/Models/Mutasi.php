<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;

    protected $table = 'mutasi';

    public function getkaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
