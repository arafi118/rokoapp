<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Group;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Utils\Tanggal;

class TempatKerjaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statusAbsen = Absensi::where('tanggal', date('Y-m-d'))
            ->where('status_absen', 'close')
            ->first();

        if ($statusAbsen) {

            $title = "Tempat Karyawan Bekerja";
            return view('inspeksi.tempat_kerja.notifikasi', compact('title'));
        }

        $grouped = Group::with([
            'getkaryawan' => function ($query) {
                $query->select('karyawan.*', 'absensi.tanggal', 'absensi.jam_masuk', 'absensi.jam_keluar')
                    ->join('absensi', 'karyawan.id', '=', 'absensi.karyawan_id')
                    ->where('absensi.tanggal', date('Y-m-d'))
                    ->where('absensi.status_absen', 'open');
            },
            'getkaryawan.getlevel',
            'getkaryawan.getanggota.getjabatan'
        ])->get();

        $dataKaryawan = [];
        foreach ($grouped as $group) {
            $dataKaryawan[$group->id] = [
                "id" => $group->id,
                "group_name" => $group->nama,
            ];

            $dataMeja = [];
            foreach ($group->getkaryawan as $karyawan) {
                if (!isset($dataMeja[$karyawan->meja_id])) {
                    $dataMeja[$karyawan->meja_id] = [
                        'meja_id'   => $karyawan->meja_id,
                        'karyawan'  => []
                    ];
                }

                $dataMeja[$karyawan->meja_id]['karyawan'][] = [
                    'id'            => $karyawan->id,
                    'kode_karyawan' => $karyawan->kode_karyawan,
                    'nama'          => $karyawan->getanggota->nama,
                    'level'         => $karyawan->getlevel->nama ?? '-',
                    'nik'           => $karyawan->getanggota->nik ?? '-',
                    'jabatan'       => $karyawan->getanggota->getjabatan->nama ?? '-',
                ];
            }

            $dataKaryawan[$group->id]['meja'] = $dataMeja;
        }

        $title = "Tempat Karyawan Bekerja";

        return view('inspeksi.tempat_kerja.index', compact('dataKaryawan', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'meja_tujuan',
            'meja_saat_ini',
            'id_karyawan',
        ]);

        $validate = Validator::make($data, [
            'meja_tujuan'   => 'required|string',
            'meja_saat_ini' => 'required|string',
            'id_karyawan'   => 'required|integer|exists:karyawan,id',
        ]);

        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $groupTujuan = explode('-', $request->meja_tujuan)[0] ?? null;
        $mejaTujuan  = explode('-', $request->meja_tujuan)[1] ?? null;

        if (!$groupTujuan || !$mejaTujuan) {
            return response()->json(['success' => false, 'msg' => 'Format meja_tujuan tidak valid!'], 400);
        }

        $absensi = Absensi::where('karyawan_id', $request->id_karyawan)
            ->whereDate('tanggal', date('Y-m-d'))
            ->first();

        if (!$absensi) {
            return response()->json(['success' => false, 'msg' => 'Data absensi hari ini tidak ditemukan!'], 404);
        }

        $absensi->update([
            'group_id' => $groupTujuan,
            'meja_id'  => $mejaTujuan,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Karyawan berhasil dipindah!',
        ]);
    }

    public function updateBanyak(Request $request)
    {
        $data = json_decode($request->input('perpindahan_data', '[]'), true);

        if (empty($data)) {
            return response()->json([
                'success' => false,
                'msg' => 'Tidak ada data yang dikirim.'
            ], 422);
        }

        DB::beginTransaction();

        foreach ($data as $item) {
            // Tentukan meja yang akan digunakan
            $mejaFinal = $item['meja_tujuan'] ?? $item['meja_saat_ini'];

            // Jika meja_tujuan sama dengan meja_saat_ini, tetap pakai meja_saat_ini
            if (isset($item['meja_tujuan']) && $item['meja_tujuan'] === $item['meja_saat_ini']) {
                $mejaFinal = $item['meja_saat_ini'];
            }

            $groupId = null;
            $mejaId = null;
            if ($mejaFinal) {
                $parts = explode('-', $mejaFinal);
                if (count($parts) >= 2) {
                    [$groupId, $mejaId] = $parts;
                }
            }

            Absensi::where('karyawan_id', $item['id'])
                ->whereDate('tanggal', now()->format('Y-m-d'))
                ->update([
                    'group_id'     => $groupId,
                    'meja_id'      => $mejaId,
                    'status_absen' => 'close',
                ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => 'Semua karyawan berhasil disimpan dan status diubah menjadi close!'
        ]);
    }
}
