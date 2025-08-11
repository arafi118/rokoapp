<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\kelompok;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class KelompokController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kelompok::with('anggota')->select('kelompok.*');
            return DataTables::eloquent($data)
                ->addColumn('nama_anggota', function ($row) {
                    return $row->anggota ? $row->anggota->nama : '-';
                })
                ->toJson();
        }

        return view('inspeksi.kelompok.index', ['title' => 'Data Kelompok']);
    }

    public function listAnggota(Request $request)
    {
        $search = $request->q;
        $mode = $request->mode;
        $currentId = $request->current_id;

        $query = Anggota::query();
        if ($mode === 'tambah') {
            $query->whereNotIn('id', function ($q) {
                $q->select('anggota_id')->from('kelompok');
            });
        }
        if ($mode === 'edit' && $currentId) {
            $query->where(function ($q) use ($currentId) {
                $q->whereNotIn('id', function ($sub) use ($currentId) {
                    $sub->select('anggota_id')
                        ->from('kelompok')
                        ->where('anggota_id', '<>', $currentId);
                });
            });
        }
        if ($search) {
            $query->where('nama', 'like', "%{$search}%");
        }
        $anggota = $query->limit(10)->get(['id', 'nama']);

        return response()->json($anggota);
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
            "nama",
            "anggota_id",
        ]);

        $rules = [
            'nama'              => 'required',
            'anggota_id'        => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $kelompok = Kelompok::create([
            'nama'              => $request->nama,
            'anggota_id'        => $request->anggota_id,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Kelompok berhasil ditambahkan!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(kelompok $kelompok)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(kelompok $kelompok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, kelompok $kelompok)
    {
        $data = $request->only([
            "nama",
            "anggota_id",
        ]);

        $rules = [
            'nama'              => 'required',
            'anggota_id'        => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $kelompok->update([
            'nama'              => $request->nama,
            'anggota_id'        => $request->anggota_id,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Kelompok berhasil diUpdate!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelompok $kelompok)
    {
        $cek = DB::table('anggota_kelompok')
            ->where('kelompok_id', $kelompok->id)
            ->exists();

        if ($cek) {
            return response()->json([
                'success' => false,
                'message' => 'Kelompok tidak bisa dihapus karena sudah dipakai di data anggota kelompok.'
            ], Response::HTTP_CONFLICT);
        }

        $kelompok->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kelompok berhasil dihapus.'
        ]);
    }
}
