<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Anggota_level;
use App\Models\wilayah;
use App\Models\Level;
use Illuminate\Http\Request;
use App\Utils\Tanggal;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Anggota::select([
                'id',
                'nama',
                'nik',
                'desa',
                'nama_bank',
                'norek'
            ]);
            return DataTables::eloquent($data)->toJson();
        }

        return view('inspeksi.anggota.index', ['title' => 'Data Anggota']);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function detail($id)
    {
        $anggota = Anggota::with('level_aktif')->findOrFail($id);

        return view('inspeksi.anggota.detail', compact('anggota'));
    }


    public function create()
    {
        $provinsi   = Wilayah::whereRaw('LENGTH(kode)=2')->get();
        $level      = level::all();

        $title = "Register Anggota";
        return view('inspeksi.anggota.create')->with(compact('title', 'provinsi', 'level'));
    }

    public function ambil_kab($kode)
    {
        $kota = Wilayah::where('kode', 'LIKE', $kode . '%')->whereRaw('length(kode)=5')->get();
        return response()->json([
            'success' => true,
            'data' => $kota
        ]);
    }

    public function ambil_kec($kode)
    {
        $kecamatan = Wilayah::where('kode', 'LIKE', $kode . '%')->whereRaw('length(kode)=8')->get();
        return response()->json([
            'success' => true,
            'data' => $kecamatan
        ]);
    }

    public function ambil_desa($kode)
    {
        $desa = Wilayah::where('kode', 'LIKE', $kode . '%')->whereRaw('length(kode)>8')->get();

        return response()->json([
            'success' => true,
            'data' => $desa
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            "tanggal_masuk",
            "level",
            "nama",
            "no_kk",
            "nik",
            "jenis_kelamin",
            "tempat_lahir",
            "tanggal_lahir",
            "agama",
            "kota",
            "kecamatan",
            "desa",
            "alamat",
            "status",
            "nama_bank",
            "norek",
            "tinggi_badan",
            "berat_badan",
            "ijazah",
            "jurusan",
            "tahun_lulus",
            "nama_ibu_kandung",
            "username",
            "password",
        ]);
        $rules = [
            'tanggal_masuk'     => 'required',
            'level'             => 'required',
            'nama'              => 'required',
            'no_kk'             => 'required',
            'nik'               => 'required',
            'jenis_kelamin'     => 'required',
            'tempat_lahir'      => 'required',
            'tanggal_lahir'     => 'required',
            'agama'             => 'required',
            'kota'              => 'required',
            'kecamatan'         => 'required',
            'desa'              => 'required',
            'alamat'            => 'required',
            'status'            => 'required',
            'nama_bank'         => 'required',
            'norek'             => 'required',
            'tinggi_badan'      => 'required',
            'berat_badan'       => 'required',
            'ijazah'            => 'required',
            'jurusan'           => 'required',
            'tahun_lulus'       => 'required',
            'nama_ibu_kandung'  => 'required',
            'username'          => 'required',
            'password'          => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $kota       = Wilayah::where('kode', $request->kota)->value('nama');
        $kecamatan  = Wilayah::where('kode', $request->kecamatan)->value('nama');
        $desa       = Wilayah::where('kode', $request->desa)->value('nama');

        $Anggota = Anggota::create([
            'nama'              => $request->nama,
            'no_kk'             => $request->no_kk,
            'nik'               => $request->nik,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'tempat_lahir'      => $request->tempat_lahir,
            'tanggal_lahir'     => (string) Tanggal::tglNasional($request->tanggal_lahir),
            'agama'             => $request->agama,
            'kota'              => $kota,
            'kecamatan'         => $kecamatan,
            'desa'              => $desa,
            'alamat'            => $request->alamat,
            'status'            => $request->status,
            'nama_bank'         => $request->nama_bank,
            'norek'             => $request->norek,
            'tinggi_badan'      => $request->tinggi_badan,
            'berat_badan'       => $request->berat_badan,
            'ijazah'            => $request->ijazah,
            'jurusan'           => $request->jurusan,
            'tahun_lulus'       => $request->tahun_lulus,
            'nama_ibu_kandung'  => $request->nama_ibu_kandung,
            'username'          => $request->username,
            'password'          => Hash::make($request->password),
        ]);

        $tanggal = explode('/', $data['tanggal_masuk']);
        $hari = $tanggal[0];
        $bulan = $tanggal[1];
        $tahun = $tanggal[2];

        $anggotaLevel = Anggota_level::where([
            ['tanggal_masuk', 'LIKE', $tahun . '-%'],
            ['level_id', $data['level']]
        ])->orderBy('id_urutan', 'DESC')->first();
        $urutanId = $anggotaLevel ? $anggotaLevel->id_urutan + 1 : 1;

        $anggotaLevel = Anggota_level::create([
            'anggota_id' => $Anggota->id,
            'tanggal_masuk' => (string) Tanggal::tglNasional($request->tanggal_lahir),
            'level_id' => $data['level'],
            'id_urutan' => $urutanId,
            'status' => 'aktif',
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Anggota berhasil ditambahkan!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Anggota $anggotum)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anggota $anggotum)
    {
        $level      = Level::all();
        $provinsi   = Wilayah::whereRaw('LENGTH(kode)=2')->get();

        $title = "Update Anggota";

        return view('inspeksi.anggota.edit')->with(compact('title', 'provinsi', 'anggotum', 'level'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anggota $anggotum)
    {
        $data = $request->only([
            "tanggal_masuk",
            "level",
            "nama",
            "no_kk",
            "nik",
            "jenis_kelamin",
            "tempat_lahir",
            "tanggal_lahir",
            "agama",
            "kota",
            "kecamatan",
            "desa",
            "alamat",
            "status",
            "nama_bank",
            "norek",
            "tinggi_badan",
            "berat_badan",
            "ijazah",
            "jurusan",
            "tahun_lulus",
            "nama_ibu_kandung",
            "username",
            "password",
        ]);

        $rules = [
            'tanggal_masuk'     => 'required',
            'level'             => 'required',
            'nama'              => 'required',
            'no_kk'             => 'required',
            'nik'               => 'required',
            'jenis_kelamin'     => 'required',
            'tempat_lahir'      => 'required',
            'tanggal_lahir'     => 'required',
            'agama'             => 'required',
            'kota'              => 'required',
            'kecamatan'         => 'required',
            'desa'              => 'required',
            'alamat'            => 'required',
            'status'            => 'required',
            'nama_bank'         => 'required',
            'norek'             => 'required',
            'tinggi_badan'      => 'required',
            'berat_badan'       => 'required',
            'ijazah'            => 'required',
            'jurusan'           => 'required',
            'tahun_lulus'       => 'required',
            'nama_ibu_kandung'  => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $kota       = Wilayah::where('kode', $request->kota)->value('nama');
        $kecamatan  = Wilayah::where('kode', $request->kecamatan)->value('nama');
        $desa       = Wilayah::where('kode', $request->desa)->value('nama');

        $anggotum->update([
            'nama'              => $request->nama,
            'no_kk'             => $request->no_kk,
            'nik'               => $request->nik,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'tempat_lahir'      => $request->tempat_lahir,
            'tanggal_lahir'     => (string) Tanggal::tglNasional($request->tanggal_lahir),
            'agama'             => $request->agama,
            'kota'              => $kota,
            'kecamatan'         => $kecamatan,
            'desa'              => $desa,
            'alamat'            => $request->alamat,
            'status'            => $request->status,
            'nama_bank'         => $request->nama_bank,
            'norek'             => $request->norek,
            'tinggi_badan'      => $request->tinggi_badan,
            'berat_badan'       => $request->berat_badan,
            'ijazah'            => $request->ijazah,
            'jurusan'           => $request->jurusan,
            'tahun_lulus'       => $request->tahun_lulus,
            'nama_ibu_kandung'  => $request->nama_ibu_kandung,
            'username'          => $request->username,
            'password'          => $request->password ? Hash::make($request->password) : $anggotum->password,
        ]);

        $tanggal = explode('/', $data['tanggal_masuk']);
        $hari = $tanggal[0];
        $bulan = $tanggal[1];
        $tahun = $tanggal[2];

        $anggotaLevel = Anggota_level::where([
            ['tanggal_masuk', 'LIKE', $tahun . '-%'],
            ['level_id', $data['level']]
        ])->orderBy('id_urutan', 'DESC')->first();
        $urutanId = $anggotaLevel ? $anggotaLevel->id_urutan + 1 : 1;

        if ($request->level != $anggotum->level_aktif->id) {
            $anggotaLevel = Anggota_level::where([
                ['anggota_id', $anggotum->id],
                ['status', 'aktif']
            ])->update([
                'tanggal_masuk' => (string) Tanggal::tglNasional($request->tanggal_lahir),
                'level_id' => $data['level'],
                'id_urutan' => $urutanId,
                'status' => 'aktif',
            ]);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Anggota berhasil diperbarui!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anggota $anggotum)
    {
        Anggota_level::where('anggota_id', $anggotum->id)->delete();

        $anggotum->delete();

        return response()->json([
            'success' => true,
            'msg' => 'Anggota dan level terkait berhasil dihapus.',
        ]);
    }
}
