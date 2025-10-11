<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Anggota;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function index()
    {
        $title = 'Absensi Karyawan';
        return view('mandor.absensi.index')->with(compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->only([
            "nik",
            "waktu",
            "absensi",
        ]);

        $tanggal = date('Y-m-d');
        $anggota = Anggota::where('nik', $data['nik'])->first();

        if (!$anggota) {
            throw new \Exception("Karyawan tidak ditemukan");
        }

        if ($data['absensi'] == 'masuk') {
            $presensi = Absensi::create([
                'user_id' => $anggota->id,
                'tanggal' => $tanggal,
                'waktu' => $data['waktu'],
                'jam_masuk' => $data['waktu'],
                'jam_pulang' => null,
                'status' => 'masuk',
            ]);
        } else {
            $presensi = Absensi::where('user_id', $anggota->id)->where('tanggal', $tanggal)->first();

            if (!$presensi) {
                throw new \Exception("Belum absens masuk");
            }

            $presensi->jam_pulang = $data['waktu'];
            $presensi->save();
        }

        return response()->json([
            'success'  => true,
            'msg' => $anggota->nama . " berhasil absen " . $data['absensi'] . " pada " . $data['waktu'],
        ]);
    }
}
