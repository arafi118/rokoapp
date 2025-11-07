<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    public function index()
    {
        $anggota = Anggota::with('karyawan', 'getjabatan')->find(auth()->user()->id);
        $title = 'Profil Saya';
        return view('anggota.profile.index', compact('anggota', 'title'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
            'inputFoto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $anggota = Anggota::findOrFail($id);
        $namaFile = $anggota->foto;

        if ($request->hasFile('inputFoto')) {
            if ($anggota->foto && Storage::disk('public')->exists("profil/{$anggota->foto}")) {
                Storage::disk('public')->delete("profil/{$anggota->foto}");
            }
            $file = $request->file('inputFoto');
            $namaFile = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('profil', $namaFile, 'public');
        }

        $anggota->update([
            'username'  => $request->username,
            'password'  => Hash::make($request->password),
            'foto'      => $anggota->foto = $namaFile,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Profil berhasil diperbarui!',
            'username' => $anggota->username,
            'foto' => $anggota->foto,
        ]);
    }
}
