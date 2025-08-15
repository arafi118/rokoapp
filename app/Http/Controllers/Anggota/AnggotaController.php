<?php

namespace App\Http\Controllers\Anggota;

use App\Models\Anggota;
use App\Models\Karyawan;
use App\Models\Produksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AnggotaController extends Controller
{
    /**
     * Halaman utama (dashboard) anggota
     */
    public function index()
    {
        $title = 'Data Anggota';
        return view('anggota.index', compact('title'));
    }

    /**
     * Form input produksi (langsung tampil setelah login anggota)
     */
    public function create()
    {
        $user = auth()->user();
        $title = 'Input Produksi';

        // Ambil data anggota
        $anggota = Anggota::find($user->id);

        // Ambil data karyawan aktif dari anggota
        $karyawan = Karyawan::where('anggota_id', $anggota->id)
            ->where('status', 'aktif')
            ->first();

        if (!$karyawan) {
            return redirect()->route('anggota.dashboard')
                ->with('error', 'Anda tidak terdaftar sebagai karyawan aktif.');
        }

        $qrCode = QrCode::size(100)->generate($anggota->id);

        return view('anggota.create', compact('anggota', 'karyawan', 'qrCode', 'title'));
    }

    /**
     * Simpan data produksi
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jumlah_baik' => 'nullable|numeric|min:0',
            'jumlah_buruk' => 'nullable|numeric|min:0',
            'jumlah_buruk2' => 'nullable|numeric|min:0',
        ]);

        $karyawan = Karyawan::where('anggota_id', auth()->id())
            ->where('status', 'aktif')
            ->first();

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Gagal menyimpan: karyawan tidak ditemukan atau tidak aktif.');
        }

        $produksi = Produksi::where('karyawan_id', $karyawan->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($produksi) {
            // Update jumlah jika sudah ada data di tanggal yang sama
            $produksi->jumlah_baik += $request->jumlah_baik ?? 0;
            $produksi->jumlah_buruk += $request->jumlah_buruk ?? 0;
            $produksi->jumlah_buruk2 += $request->jumlah_buruk2 ?? 0;
            $produksi->save();

            return redirect()->route('anggota.dashboard')->with('berhasil', 'Data produksi berhasil diperbarui!');
        } else {
            // Simpan data baru
            Produksi::create([
                'karyawan_id' => $karyawan->id,
                'tanggal' => $request->tanggal,
                'jumlah_baik' => $request->jumlah_baik ?? 0,
                'jumlah_buruk' => $request->jumlah_buruk ?? 0,
                'jumlah_buruk2' => $request->jumlah_buruk2 ?? 0,
                'status_validasi' => 'DRAFT'
            ]);

            return redirect()->route('anggota.dashboard')->with('berhasil', 'Data produksi berhasil disimpan!');
        }
    }

    /**
     * Daftar produksi anggota
     */
    public function produksi()
    {
        $title = 'Daftar Produksi';

        $karyawan = Karyawan::where('anggota_id', auth()->id())
            ->where('status', 'aktif')
            ->first();

        if (!$karyawan) {
            $data = collect(); // data kosong
        } else {
            $data = Produksi::where('karyawan_id', $karyawan->id)
                ->orderBy('tanggal', 'desc')
                ->get();
        }

        return view('anggota.produksi', compact('title', 'data'));
    }

    /**
     * Cetak data produksi anggota
     */
    public function cetak($id)
    {
        $title = '';
        $anggota = Anggota::findOrFail($id);
        $karyawan = Karyawan::where('anggota_id', $anggota->id)->first();

        if (!$karyawan) {
            abort(404, 'Karyawan tidak ditemukan');
        }

        $produksi = Produksi::where('karyawan_id', $karyawan->id)->get();
        $qrCode = QrCode::size(70)->generate($anggota->id);

        return view('anggota.cetak', compact('anggota', 'produksi', 'qrCode','title'));
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
