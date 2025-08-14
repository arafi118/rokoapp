<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class LevelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Level::select([
                'id',
                'nama',
                'inisial',
            ]);
            return DataTables::eloquent($data)->toJson();
        }

        return view('inspeksi.level.index', ['title' => 'Data Level']);
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
            "inisial",
        ]);
        $rules = [
            'nama'              => 'required',
            'inisial'           => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $Level = Level::create([
            'nama'              => $request->nama,
            'inisial'           => $request->inisial,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Level berhasil ditambahkan!',
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Level $level)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Level $level)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Level $level)
    {
        $data = $request->only([
            "nama",
            "inisial",
        ]);
        $rules = [
            'nama'              => 'required',
            'inisial'           => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $level->update($validate->validated());

        return response()->json([
            'success' => true,
            'msg' => 'Level berhasil diUpdate!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Level $level)
    {
        // Cek apakah Level dipakai di tabel anggota
        $terpakai = DB::table('karyawan')->where('level', $level->id)->exists();

        if ($terpakai) {
            return response()->json([
                'success' => false,
                'msg'     => 'Level tidak bisa dihapus karena sudah dipakai anggota aktif.',
            ], Response::HTTP_CONFLICT);
        }

        // Jika tidak terpakai, hapus
        $level->delete();

        return response()->json([
            'success' => true,
            'msg'     => 'Level berhasil dihapus.',
        ]);
    }
}
