<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MandorController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        return view('mandor.index')->with(compact('title'));
    }
}
