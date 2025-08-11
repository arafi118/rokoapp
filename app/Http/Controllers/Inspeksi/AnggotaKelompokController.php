<?php

namespace App\Http\Controllers\inspeksi;

use App\Http\Controllers\Controller;
use App\Models\anggota_kelompok;
use App\Models\kelompok;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class AnggotaKelompokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = anggota_kelompok::with(['kelompok', 'anggota']);

            return DataTables::eloquent($data)
                ->addColumn('nama_anggota', fn ($row) => $row->anggota->nama ?? '-')
                ->addColumn('nama', fn ($row) => $row->kelompok->nama ?? '-')
                ->addColumn('anggota_id', fn ($row) => $row->anggota->id ?? null)
                ->addColumn('kelompok_id', fn ($row) => $row->kelompok->id ?? null)
                ->toJson();
        }

        return view('inspeksi.Anggotakelompok.index', [
            'title' => 'Data Anggota Kelompok'
        ]);
    }
    public function listAnggota(Request $request)
    {
        $search = $request->q;

        $query = Anggota::select('id', 'nama');
        if (!empty($search)) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        $anggota = $query->orderBy('nama')->limit(20)->get();

        return response()->json($anggota);
    }

    public function listKelompok(Request $request)
    {
        $search = $request->q;

        $query = Kelompok::select('id', 'nama');
        if (!empty($search)) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        return response()->json($query->orderBy('nama')->get());
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(anggota_kelompok $anggota_kelompok)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(anggota_kelompok $anggota_kelompok)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, anggota_kelompok $anggota_kelompok)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(anggota_kelompok $anggota_kelompok)
    {
        //
    }
}
