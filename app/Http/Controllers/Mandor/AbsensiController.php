<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\Jadwal;
use App\Models\Karyawan;
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
            throw new \Exception("Anggota tidak ditemukan");
        }

        $karyawan = Karyawan::where('anggota_id', $anggota->id)->first();
        if (!$karyawan) {
            throw new \Exception("Karyawan tidak ditemukan");
        }

        $hari = $this->namaHari($tanggal);
        $jadwal = Jadwal::where('hari', $hari)->first();

        $presensi = Absensi::where('karyawan_id', $karyawan->id)->where('tanggal', $tanggal)->first();
        if ($data['absensi'] == 'masuk') {
            $presensi = Absensi::create([
                'karyawan_id' => $karyawan->id,
                'group_id' => $karyawan->group_id,
                'jadwal' => $jadwal->id,
                'tanggal' => $tanggal,
                'jam_masuk' => $data['waktu'],
                'status' => (strtotime($data['waktu']) > strtotime($jadwal->jam_masuk)) ? "T" : "H",
            ]);
        } else {
            $presensi = Absensi::where('karyawan_id', $karyawan->id)->where('tanggal', $tanggal)->first();
            if (!$presensi) {
                throw new \Exception("Belum absens masuk");
            }

            $presensi->jam_keluar = $data['waktu'];
            $presensi->save();
        }

        return response()->json([
            'success'  => true,
            'msg' => $anggota->nama . " berhasil absen " . $data['absensi'] . " pada " . $data['waktu'],
        ]);
    }

    private function namaHari($tanggal)
    {
        $hari = date('D', strtotime($tanggal));

        switch ($hari) {
            case 'Sun':
                $nama = "Minggu";
                break;
            case 'Mon':
                $nama = "Senin";
                break;
            case 'Tue':
                $nama = "Selasa";
                break;
            case 'Wed':
                $nama = "Rabu";
                break;
            case 'Thu':
                $nama = "Kamis";
                break;
            case 'Fri':
                $nama = "Jumat";
                break;
            case 'Sat':
                $nama = "Sabtu";
                break;
            default:
                $nama = "Tidak diketahui";
                break;
        }

        return $nama;
    }
}
