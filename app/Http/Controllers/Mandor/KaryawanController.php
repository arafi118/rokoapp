<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Group;
use App\Models\Karyawan;
use App\Models\Kelompok;
use App\Utils\Tanggal;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $group = Group::where('mandor', auth()->user()->id)->first();
            $karyawan = Karyawan::where('group_id', $group->id)->with('getanggota', 'getlevel');

            return DataTables::eloquent($karyawan)
                ->addIndexColumn()
                ->addColumn('aksi', function ($karyawan) {
                    return
                        '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-primary text-light btn-detail" data-id="' . $karyawan->id . '"><i class="bi bi-eye"></i></button>
                        </div>';
                })
                ->addColumn('masa_kerja', function ($karyawan) {
                    $tanggal_keluar = 'Sekarang';
                    if ($karyawan->tanggal_keluar != null) {
                        $tanggal_keluar = Tanggal::tglLatin($karyawan->tanggal_keluar);
                    }

                    return Tanggal::tglLatin($karyawan->tanggal_masuk) . ' - ' . $tanggal_keluar;
                })
                ->editColumn('nama', function ($karyawan) {
                    return ucwords($karyawan->getanggota->nama);
                })
                ->editColumn('tempat_lahir', function ($karyawan) {
                    return ucwords($karyawan->getanggota->tempat_lahir) . ', ' . Tanggal::tglLatin($karyawan->getanggota->tanggal_lahir);
                })
                ->rawColumns(['aksi'])
                ->make(true);
        }

        $title = 'Daftar Karyawan';
        return view('mandor.karyawan.index')->with(compact('title'));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Karyawan $karyawan)
    {
        //
    }
}
