<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnggotController extends Controller
{
    public function index()
    {
        $title = 'Dashboard';
        return view('anggota.index')->with(compact('title'));
    }
}
