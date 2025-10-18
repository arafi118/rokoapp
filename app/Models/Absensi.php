<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'absensi';

    protected $guarded = ['id'];

    public function getkaryawan()
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function getmeja()
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    public function getgroup()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
