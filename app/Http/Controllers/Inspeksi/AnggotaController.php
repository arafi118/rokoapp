<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Jabatan;
use App\Models\Karyawan;
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
            $data = Anggota::with('getjabatan');
            return DataTables::eloquent($data)
                ->editColumn('nama', function ($anggota) {
                    return $anggota->nama . ' - ' . $anggota->getjabatan->nama;
                })
                ->toJson();
        }

        return view('inspeksi.anggota.index', ['title' => 'Data Anggota']);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function detail($id)
    {
        $anggota = Anggota::with('karyawan')->findOrFail($id);

        return view('inspeksi.anggota.detail', compact('anggota'));
    }

    public function create()
    {
        $provinsi   = Wilayah::whereRaw('LENGTH(kode)=2')->get();
        $jabatan    = Jabatan::all();
        $level      = level::all();

        $title = "Register Anggota";
        return view('inspeksi.anggota.create')->with(compact('title', 'jabatan', 'provinsi', 'level'));
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
            "jabatan",
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
            'jabatan'           => 'required',
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
            'jabatan'           => $request->jabatan,
            'username'          => $request->username,
            'password'          => Hash::make($request->password),
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
        $jabatan    = Jabatan::all();
        $level      = Level::all();
        $provinsi   = Wilayah::whereRaw('LENGTH(kode)=2')->get();

        $title = "Update Anggota";

        return view('inspeksi.anggota.edit')->with(compact('title', 'provinsi', 'anggotum', 'level', 'jabatan'));
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
            "jabatan",
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
            'jabatan'           => 'required',
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
            'jabatan'           => $request->jabatan,
            'username'          => $request->username,
            'password'          => $request->password ? Hash::make($request->password) : $anggotum->password,
        ]);

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
        if (Karyawan::where('anggota_id', $anggotum->id)->exists()) {
            return response()->json(['success' => false, 'msg' => 'Anggota ini tidak dapat dihapus karena sudah terdaftar sebagai karyawan.']);
        }
        $anggotum->delete();
        return response()->json(['success' => true, 'msg' => 'Anggota berhasil dihapus.']);
    }
}
