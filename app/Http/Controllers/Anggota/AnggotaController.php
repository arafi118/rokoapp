<?php

namespace App\Http\Controllers\Anggota;
use App\Models\Anggota_level;
use App\Models\Anggota;
use App\Models\Pendataan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class AnggotaController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $title = 'Data Anggota';
    return view('anggota.index')->with(compact('title'));
  }

  /**
   * Show the form for creating a new resource.
   */
public function create()
{
    $title = 'Input Produksi Harian';
    return view('anggota.create', compact('title'));
}

public function store(Request $request)
{
    $request->validate([
        'tanggal_input' => 'required|date',
        'jumlah' => 'required|numeric|min:0',
    ]);

    // Ambil data level anggota yang login
    $anggotaLevel = Anggota_level::where([
        ['anggota_id', auth()->id()],
        ['status', 'aktif']
    ])->first();

    if (!$anggotaLevel) {
        return redirect()->back()->with('error', 'Gagal menyimpan: anggota tidak punya kelompok aktif.');
    }

    Pendataan::create([
        'anggota_kelompok_id' => $anggotaLevel->id,
        'jumlah' => $request->jumlah,
        'tanggal_input' => $request->tanggal_input,
        'status' => 'DRAFT',
        'keterangan' => $request->keterangan
    ]);

    return redirect()->back()->with('success', 'Data berhasil disimpan!');
}

public function produksi()
{
    $title = 'Daftar Produksi';
    $data = Pendataan::whereHas('anggotaKelompok', function($q) {
        $q->where('anggota_level_id', auth()->user()->id);
    })->orderBy('tanggal_input', 'desc')->get();

    return view('anggota.produksi', compact('title', 'data'));
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
