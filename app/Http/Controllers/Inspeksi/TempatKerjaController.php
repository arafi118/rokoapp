<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Group;
use App\Models\Karyawan;
use App\Models\Anggota;
use App\Models\Meja;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $anggota = Anggota::find(auth()->user()->id);
            $title = "Tempat Karyawan Bekerja";
            return view('inspeksi.tempat_kerja.notifikasi', compact('title', 'anggota'));
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
                    'inisial'       => $karyawan->getlevel->inisial ?? '-',
                    'nik'           => $karyawan->getanggota->nik ?? '-',
                    'jabatan'       => $karyawan->getanggota->getjabatan->nama ?? '-',
                    'warna'         => $karyawan->getlevel->warna ?? '#5b5a5cff',
                ];
            }

            $dataKaryawan[$group->id]['meja'] = $dataMeja;
        }

        $title = "Tempat Karyawan Bekerja";

        return view('inspeksi.tempat_kerja.index', compact('dataKaryawan', 'title'));
    }

    public function listgroup(Request $request)
    {
        $search = $request->get('q');
        $query = Group::select('id', 'nama');

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $anggota = $query->get();

        $results = $anggota->map(function ($a) {
            return [
                'id' => $a->id,
                'text' => $a->nama
            ];
        });

        return response()->json($results);
    }

    public function listmeja(Request $request)
    {
        $search = $request->get('q');
        $query = Meja::select('id', 'nama_meja');

        if ($search) {
            $query->where('nama_meja', 'like', "%{$search}%");
        }

        $anggota = $query->get();

        $results = $anggota->map(function ($a) {
            return [
                'id' => $a->id,
                'text' => $a->nama_meja
            ];
        });

        return response()->json($results);
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
            $mejaFinal = $item['meja_tujuan'] ?? $item['meja_saat_ini'];

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

    public function updateBanyakKaryawan()
    {
        DB::beginTransaction();

        try {
            $latestDate = Absensi::max('tanggal');

            if (!$latestDate) {
                Log::info('Tidak ada data absensi untuk diperbarui.');
                DB::rollBack();
                return;
            }

            $absensi = Absensi::where('tanggal', $latestDate)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($absensi as $absen) {
                DB::table('karyawan')
                    ->where('id', $absen->karyawan_id)
                    ->update([
                        'group_id'   => $absen->group_id,
                        'meja_id'    => $absen->meja_id,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            Log::info("Update banyak karyawan berhasil pada tanggal: {$latestDate}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update banyak karyawan: ' . $e->getMessage());
        }
    }

    public function aktifKembali(Request $request)
    {
        $tanggal = $request->tanggal;

        // Validasi tanggal
        if (!$tanggal) {
            return response()->json([
                'success' => false,
                'msg' => 'Tanggal tidak ditemukan dalam permintaan.'
            ], 400);
        }

        // Update semua absensi yang tanggalnya sama persis
        $updated = \App\Models\Absensi::whereDate('tanggal', $tanggal)
            ->update([
                'status_absen' => 'open',
                'updated_at' => now(),
            ]);

        if ($updated > 0) {
            return response()->json([
                'success' => true,
                'msg' => "Absensi tanggal {$tanggal} berhasil diaktifkan kembali."
            ]);
        } else {
            return response()->json([
                'success' => false,
                'msg' => "Tidak ada data absensi pada tanggal {$tanggal}."
            ]);
        }
    }
}
