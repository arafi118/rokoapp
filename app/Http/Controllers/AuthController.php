<?php

namespace App\Http\Controllers;

use App\Models\Anggota_level;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function auth(Request $request)
    {
        $data = $request->only([
            'username',
            'password'
        ]);

        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt($data)) {
            $user = Auth::user();
            $anggotaLevel = Anggota_level::where([
                ['anggota_id', $user->id],
                ['status', 'aktif']
            ])->first();
            $level = Level::where('id', $anggotaLevel->level_id)->first();

            $redirect = '/anggota';
            if ($level->name == 'inspeksi' || $level->name == 'mandor') {
                $redirect = '/' . $level->name;
            }

            return redirect($redirect)->with('success', 'Login Berhasil');
        }

        return redirect()->back()->with('error', 'Login Gagal');
    }
}
