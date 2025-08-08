<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Kelompok;
use App\Utils\Tanggal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $kelompok = Kelompok::where('anggota_id', auth()->user()->id)->first();

            $anggota = Anggota::join('anggota_level', 'anggota.id', '=', 'anggota_level.anggota_id')
                ->join('level', 'anggota_level.level_id', '=', 'level.id')
                ->join('anggota_kelompok', 'anggota_level.id', '=', 'anggota_kelompok.anggota_level_id')
                ->where('anggota_kelompok.kelompok_id', $kelompok->id)
                ->where('anggota_level.status', 'aktif')
                ->select('anggota.*', 'level.nama as level', 'anggota_level.tanggal_masuk as tgl_masuk', 'anggota_level.tanggal_keluar as tgl_keluar');

            return DataTables::eloquent($anggota)
                ->addIndexColumn()
                ->addColumn('aksi', function ($anggota) {
                    return
                        '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary text-light btn-detail" data-id="' . $anggota->id . '"><i class="bi bi-eye"></i></button>
                            <button type="button" class="btn btn-sm btn-warning btn-edit" data-id="' . $anggota->id . '"><i class="bi bi-pencil-square"></i></button>
                        </div>';
                })
                ->addColumn('masa_kerja', function ($anggota) {
                    $tgl_keluar = 'Sekarang';
                    if ($anggota->tgl_keluar) {
                        $tgl_keluar = Tanggal::tglLatin($anggota->tgl_keluar);
                    }

                    return Tanggal::tglLatin($anggota->tgl_masuk) . ' - ' . $tgl_keluar;
                })
                ->editColumn('nama', function ($anggota) {
                    return ucwords($anggota->nama);
                })
                ->editColumn('tempat_lahir', function ($anggota) {
                    return ucwords($anggota->tempat_lahir) . ', ' . Tanggal::tglLatin($anggota->tanggal_lahir);
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $title = 'Daftar Anggota';
        return view('mandor.anggota.index')->with(compact('title'));
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
    public function show(Anggota $anggota)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anggota $anggota)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anggota $anggota)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Anggota $anggota)
    {
        //
    }
}
