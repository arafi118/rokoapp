<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InspeksiController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        return view('inspeksi.index')->with(compact('title'));
    }
}
