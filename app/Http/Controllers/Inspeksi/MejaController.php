<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Meja;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class MejaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Meja::select('*');
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->toJson();
        }

        return view('inspeksi.meja.index', ['title' => 'Data Posisi Meja']);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            "nama_meja",
        ]);
        $rules = [
            'nama_meja'              => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $posisi = Meja::create([
            'nama_meja'              => $request->nama_meja,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Posisi Meja berhasil ditambahkan!',
        ]);
    }

    public function update(Request $request, Meja $meja)
    {
        $data = $request->only([
            "nama_meja",
        ]);
        $rules = [
            'nama_meja'              => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $meja->update($validate->validated());

        return response()->json([
            'success' => true,
            'msg' => 'Posisi Meja berhasil diUpdate!',
        ]);
    }

    public function destroy(Meja $meja)
    {
        // Cek apakah Level dipakai di tabel anggota
        $terpakai = DB::table('karyawan')->where('meja_id', $meja->id)->exists();

        if ($terpakai) {
            return response()->json([
                'success' => false,
                'msg'     => 'Posisi Meja tidak bisa dihapus karena sudah dipakai anggota aktif.',
            ], Response::HTTP_CONFLICT);
        }

        // Jika tidak terpakai, hapus
        $meja->delete();

        return response()->json([
            'success' => true,
            'msg'     => 'Posisi Meja berhasil dihapus.',
        ]);
    }
}
