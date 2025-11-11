<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\Anggota;
use App\Models\Meja;
use App\Models\Level;
use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('inspeksi.karyawan.index', ['title' => 'Karyawan Borong']);
    }

    public function nonBorong()
    {
        return view('inspeksi.karyawan.non-borong', ['title' => 'Karyawan Non Borong']);
    }

    public function getData()
    {
        if (request()->ajax()) {
            $level_karyawan = request()->get('level_karyawan') ?? 1;
            $data = Karyawan::with(
                'getanggota',
                'getgroup',
                'getmeja',
                'getlevel'
            )->select('karyawan.*', 'level.nama as level_nama')
                ->join('level', 'karyawan.level', '=', 'level.id')
                ->where('level.level_karyawan', $level_karyawan);

            if (request()->get('status') && request()->get('status') !== 'all') {
                $data = $data->where('karyawan.status', request()->get('status'));
            }

            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('status_karyawan', function ($row) {
                    return $row->status;
                })
                ->addColumn('status_anggota', function ($row) {
                    return $row->getanggota->status ?? '-';
                })
                ->toJson();
        }
    }

    public function getgroup(Request $request)
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

    public function getanggota(Request $request)
    {
        $search = $request->get('q');
        $query = Anggota::select('id', 'nama');

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

    public function getlevel(Request $request)
    {
        $search = $request->get('q');
        $query = Level::select('id', 'nama', 'inisial');

        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }

        $anggota = $query->get();
        $results = $anggota->map(function ($a) {
            return [
                'id' => $a->id,
                'text' => "{$a->nama} ({$a->inisial})"
            ];
        });

        return response()->json($results);
    }


    public function getmeja(Request $request)
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only([
            'anggota_id',
            'meja_id',
            'tanggal_masuk',
            'group_id',
            'level_id',
            'tanggal_masuk',
            'status',
        ]);

        $rules = [
            'anggota_id'    => 'required',
            'meja_id'       => 'required',
            'group_id'      => 'required',
            'level_id'      => 'required',
            'tanggal_masuk' => 'required',
            'status'        => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $tahun = $request->tanggal_masuk ? date('y', strtotime($request->tanggal_masuk)) : date('y');
        $bulan = $request->tanggal_masuk ? date('m', strtotime($request->tanggal_masuk)) : date('m');
        $lastKode = Karyawan::orderBy('kode_karyawan', 'desc')->value('kode_karyawan');
        $nextNumber = $lastKode ? ((int) substr($lastKode, -4)) + 1 : 1;
        $kodekaryawan = 'P. ' . $tahun . $bulan . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $karyawan = Karyawan::create([
            'anggota_id'        => $data['anggota_id'],
            'meja_id'           => $data['meja_id'],
            'group_id'          => $data['group_id'],
            'kode_karyawan'     => $kodekaryawan,
            'tanggal_masuk'     => $data['tanggal_masuk'],
            'tanggal_keluar'    => null,
            'level'             => $data['level_id'],
            'status'            => $data['status'],
        ]);

        return response()->json([
            'success'   => true,
            'msg'       => 'Karyawan berhasil ditambahkan',
            'data'      => $karyawan
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Karyawan $karyawan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Karyawan $karyawan)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Karyawan $karyawan)
    {
        $data = $request->only([
            'meja_id',
            'group_id',
            'tanggal_keluar',
            'level_id',
            'status',
        ]);
        $rules = [
            'meja_id'       => 'required',
            'group_id'      => 'required',
            'level_id'      => 'required',
            'status'        => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $karyawan->update($data);
        return response()->json([
            'success'   => true,
            'msg'       => 'Karyawan berhasil diupdate',
            'data'      => $karyawan
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan)
    {
        $absensni = $karyawan->getabsensi()->count();
        if ($absensni > 0) {
            return response()->json([
                'success'   => false,
                'msg'       => 'Karyawan tidak dapat dihapus karena sudah terdaftar di data absensi'
            ], Response::HTTP_BAD_REQUEST);
        }

        $karyawan->delete();
        return response()->json([
            'success'   => true,
            'msg'       => 'Karyawan berhasil dihapus'
        ], Response::HTTP_OK);
    }

    public function cetak()
    {
        $paramIdKaryawan = request()->get('id_karyawan');
        $idKaryawan = explode(',', $paramIdKaryawan);

        $karyawan = Karyawan::with([
            'getanggota'
        ])->whereIn('id', $idKaryawan)->get();

        return Pdf::loadView('inspeksi.karyawan.cetak', compact('karyawan'))->setPaper('a4', 'portrait')->stream();
    }
}
