<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Rencana;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use App\Utils\Tanggal;

class RencanaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $idTerbaru = Rencana::max('id');
            $data = Rencana::select([
                'id',
                'tanggal',
                'rencana_produksi',
                'rencana_kehadiran',
                'rencana_karyawan',
            ]);
            return DataTables::eloquent($data)
                ->addColumn('action', function ($row) use ($idTerbaru) {
                    if ($row->id == $idTerbaru) {
                        return '
                        <button class="btn btn-sm btn-warning btnEdit"
                            data-id="' . $row->id . '"
                            data-tanggal="' . $row->tanggal . '"
                            data-rencana_produksi="' . $row->rencana_produksi . '"
                            data-rencana_kehadiran="' . $row->rencana_kehadiran . '"
                            data-rencana_karyawan="' . $row->rencana_karyawan . '">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                    }
                    return '
                        <button class="btn btn-sm btn-secondary" disabled title="Hanya data terbaru yang bisa diubah">
                            <i class="bi bi-lock"></i> Terkunci
                        </button>';
                })
                ->rawColumns(['action'])
                ->toJson();
        }

        return view('inspeksi.rencana.index', ['title' => 'Data Rencana']);
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
            "tanggal",
            "rencana_produksi",
            "rencana_kehadiran",
            "rencana_karyawan",
        ]);
        $rules = [
            'tanggal'              => 'required',
            'rencana_produksi'     => 'required',
            'rencana_kehadiran'    => 'required',
            'rencana_karyawan'     => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $rencana = Rencana::create([
            'tanggal'                    => (string) Tanggal::tglNasional($request->tanggal),
            'rencana_produksi'           => $request->rencana_produksi,
            'rencana_kehadiran'          => $request->rencana_kehadiran,
            'rencana_karyawan'           => $request->rencana_karyawan,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Rencana berhasil ditambahkan!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rencana $rencana)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rencana $rencana)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rencana $rencana)
    {
        $data = $request->only([
            "tanggal",
            "rencana_produksi",
            "rencana_kehadiran",
            "rencana_karyawan",
        ]);

        $rules = [
            'tanggal'              => 'required|date_format:d/m/Y',
            'rencana_produksi'     => 'required|numeric',
            'rencana_kehadiran'    => 'required|numeric',
            'rencana_karyawan'     => 'required|numeric',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json([
                'success' => false,
                'msg' => $validate->errors()->first()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $rencana->update([
            'tanggal'            => (string) Tanggal::tglNasional($request->tanggal),
            'rencana_produksi'   => $request->rencana_produksi,
            'rencana_kehadiran'  => $request->rencana_kehadiran,
            'rencana_karyawan'   => $request->rencana_karyawan,
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Rencana berhasil diperbarui!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rencana $rencana)
    {
        $idTerbaru = Rencana::max('id');
        if ($rencana->id != $idTerbaru) {
            return response()->json([
                'success' => false,
                'msg' => 'Hanya data terbaru yang bisa dihapus.'
            ], 403);
        }
        try {
            $rencana->delete();
            return response()->json([
                'success' => true,
                'msg' => 'Data berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Gagal menghapus data. ' . $e->getMessage()
            ], 500);
        }
    }
}
