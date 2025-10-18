<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $table = 'group';
    protected $guarded = ['id'];

    public function getmandor()
    {
        return $this->belongsTo(Anggota::class, 'mandor');
    }

    public function getkaryawan()
    {
        return $this->hasMany(Karyawan::class, 'group_id');
    }
}
