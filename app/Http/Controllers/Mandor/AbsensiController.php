<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        $title = 'Absensi Karyawan';
        return view('mandor.absensi.index')->with(compact('title'));
    }
}
