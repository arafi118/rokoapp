<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Anggota;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Group::with('getmandor')->get();
            return DataTables::of($data)->make(true);
        }

        return view('inspeksi.group.index', ['title' => 'Data Group']);
    }

    public function list(Request $request)
    {
        $search = $request->get('q');
        $query = Anggota::select('id', 'nama')
            ->where('jabatan', 3);

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
            "mandor",
            "nama",
        ]);

        $rules = [
            'mandor'            => 'required',
            'nama'              => 'required',
        ];

        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $Group = Group::create([
            'mandor' => $request->mandor,
            'nama' => $request->nama,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Group berhasil ditambahkan!',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $data = $request->only([
            "mandor",
            "nama",
        ]);
        $rules = [
            'mandor' => 'required',
            'nama' => 'required',
        ];
        $validate = Validator::make($data, $rules);
        if ($validate->fails()) {
            return response()->json($validate->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $group->update([
            'mandor' => $request->mandor,
            'nama' => $request->nama,
        ]);
        return response()->json([
            'success' => true,
            'msg' => 'Group berhasil diupdate!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        // Cek apakah Level dipakai di tabel anggota
        $terpakai = DB::table('karyawan')->where('group_id', $group->id)->exists();

        if ($terpakai) {
            return response()->json([
                'success' => false,
                'msg'     => 'Group tidak bisa dihapus karena sudah dipakai anggota aktif.',
            ], Response::HTTP_CONFLICT);
        }

        // Jika tidak terpakai, hapus
        $group->delete();

        return response()->json([
            'success' => true,
            'msg'     => 'Group berhasil dihapus.',
        ]);
    }
}
