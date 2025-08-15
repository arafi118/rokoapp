<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Anggota;
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
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $anggota = Anggota::where('username', $request->username)->first();
        if ($anggota) {
            if (password_verify($request->password, $anggota->password)) {
                $karyawan = Karyawan::where('anggota_id', $anggota->id)
                    ->where('status', 'aktif')
                    ->first();

                if ($karyawan) {
                    $absen = false;
                    if (strtolower($karyawan->status) == 'aktif') {
                        $sudahAbsenMasuk = Absensi::where('karyawan_id', $karyawan->id)
                            ->whereDate('tanggal', date('Y-m-d'))
                            ->whereIn('status', ['H', 'T'])
                            ->first();

                        if ($sudahAbsenMasuk) {
                            $absen = true;
                        }
                    }

                    if ($absen == false) {
                        return back()->with('error', 'Karyawan Belum Absen');
                    }
                }

                $redirect = '/' . strtolower($anggota->getjabatan->nama);
                if (Auth::attempt($request->only('username', 'password'))) {
                    return redirect($redirect)->with('success', 'Login Berhasil');
                }
            }
        }

        return back()->with('error', 'Login Gagal. Username atau Password Salah');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Terima Kasih, Anda Berhasil Logout');
    }
}
