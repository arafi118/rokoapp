<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\JenisLaporan;
use Illuminate\Http\Request;

class PelaporanController extends Controller
{
      public function index()
    {
        $title = 'Pelaporan';
        $laporan = JenisLaporan::where([['file', '!=', '0']])->orderBy('urut', 'ASC')->get();
        return view('inspeksi.pelaporan.index', compact('title', 'laporan'));
    }
     public function subLaporan($file)
    {
        if ($file == 'buku_besar') {
            $sub_laporan = [];

        } else {
            $sub_laporan = [
                [
                    'value' => '',
                    'title' => '---'
                ]
            ];

            return view('pelaporan.partials.sub_laporan', [
                'type' => 'select',
                'sub_laporan' => $sub_laporan
            ]);
        }
    }

    public function preview(Request $request)
    {
        $laporan = $request->get('laporan');
        $data = $request->all();

        if ($laporan === 'buku_besar') {
            $data['kode_akun'] = $request->sub_laporan;
            $data['laporan']   = 'buku_besar'; 
            return $this->buku_besar($data);
        }

        if (method_exists($this, $laporan)) {
            return $this->$laporan($data);
        }

        abort(404, 'Laporan tidak ditemukan');
    }
}
