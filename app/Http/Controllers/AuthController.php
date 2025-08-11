<?php

namespace App\Http\Controllers;

use App\Models\Absensi_anggota;
use App\Models\Anggota_kelompok;
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
    $data = $request->only(['username', 'password']);

    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    if (Auth::attempt($data)) {
        $user = Auth::user();

        // Cari anggota_level aktif
        $anggotaLevel = Anggota_level::where('anggota_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        if (!$anggotaLevel) {
            Auth::logout();
            return redirect()->back()->with('error', 'Status anggota tidak aktif');
        }

        // Ambil data level
        $level = Level::find($anggotaLevel->level_id);

        // Kalau level anggota, wajib punya kelompok dan absen
        if ($level && strtolower($level->nama) === 'anggota') {
            // Cari anggota_kelompok terkait
            $anggotaKelompok = Anggota_kelompok::where('anggota_level_id', $anggotaLevel->id)->first();
            if (!$anggotaKelompok) {
                Auth::logout();
                return redirect()->back()->with('error', 'Anggota kelompok tidak ditemukan');
            }

            // Cek apakah sudah absen masuk hari ini
            $sudahAbsenMasuk = Absensi_anggota::where('anggota_kelompok_id', $anggotaKelompok->id)
                ->whereDate('jam', now()->toDateString())
                ->where('status', 'masuk')
                ->exists();

            if (!$sudahAbsenMasuk) {
                Auth::logout();
                return redirect()->back()->with('error', 'Anda belum absen masuk hari ini');
            }
        }

        // Tentukan redirect sesuai level
        $redirect = '/anggota';
        if ($level && in_array(strtolower($level->nama), ['inspeksi', 'mandor'])) {
            $redirect = '/' . strtolower($level->nama);
        }

        return redirect($redirect)->with('success', 'Login Berhasil');
    }

    return redirect()->back()->with('error', 'Login Gagal');
}


}
