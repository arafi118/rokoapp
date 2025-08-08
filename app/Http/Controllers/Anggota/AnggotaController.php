<?php

namespace App\Http\Controllers\Anggota;
use App\Models\Anggota_level;
use App\Models\Anggota;
use App\Models\Pendataan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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
    $user = auth()->user();
    $title = 'Dashboard';

    // Jika user adalah anggota, ambil data terkait
    $anggota = Anggota::find($user->id);
    $qrCode = QrCode::size(100)->generate($anggota->id);

    return view('anggota.create', compact('anggota', 'qrCode','title'));
}

public function store(Request $request)
{
    $request->validate([
        'tanggal_input' => 'required|date',
        'jumlah' => 'required|numeric|min:0',
    ]);

    $anggotaLevel = Anggota_level::where([
        ['anggota_id', auth()->id()],
        ['status', 'aktif']
    ])->first();

    if (!$anggotaLevel) {
        return redirect()->back()->with('error', 'Gagal menyimpan: anggota tidak punya kelompok aktif.');
    }

    $produksi = Pendataan::where('anggota_kelompok_id', $anggotaLevel->id)
        ->where('tanggal_input', $request->tanggal_input)
        ->first();

    if ($produksi) {
        $produksi->jumlah += $request->jumlah;
        $produksi->save();

        return redirect()->route('anggota.dashboard')->with('success_create', 'Data berhasil ditambahkan!');
    } else {
        Pendataan::create([
            'anggota_kelompok_id' => $anggotaLevel->id,
            'jumlah' => $request->jumlah,
            'tanggal_input' => $request->tanggal_input,
            'status' => 'DRAFT'
        ]);

        return redirect()->route('anggota.dashboard')->with('success', 'Data berhasil disimpan!');
    }
}




public function produksi()
{
    $title = 'Daftar Produksi';

    // cari anggota_level yang aktif untuk user login
    $anggotaLevel = Anggota_level::where([
        ['anggota_id', auth()->id()],
        ['status', 'aktif']
    ])->first();

    if (!$anggotaLevel) {
        $data = collect(); // kosong
    } else {
        $data = Pendataan::where('anggota_kelompok_id', $anggotaLevel->id)
            ->orderBy('tanggal_input', 'desc')
            ->get();
    }

    return view('anggota.produksi', compact('title', 'data'));
}

public function cetak($id)
{
    $anggota = Anggota::findOrFail($id);
    $produksi = Pendataan::where('anggota_kelompok_id', $id)->get();

     $qrCode = QrCode::size(70)->generate($anggota->id);

    return view('anggota.cetak', compact('anggota', 'produksi', 'qrCode'));
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
