<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JenisLaporan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PelaporanController extends Controller
{
    public function index()
    {
        $title = 'Pelaporan';
        $laporan = JenisLaporan::where('file', '!=', '0')
            ->orderBy('urut', 'ASC')
            ->get();
        
        return view('pelaporan.index', compact('title', 'laporan'));
    }

    public function subLaporan($file)
    {
        if ($file == 'karyawan') {
            $sub_laporan = [
                ['value' => 'terdaftar', 'title' => 'Karyawan Terdaftar'],
                ['value' => 'hadir', 'title' => 'Karyawan Hadir'],
                ['value' => 'tidak_masuk', 'title' => 'Karyawan Tidak Masuk'],
                ['value' => 'direkrut', 'title' => 'Karyawan Direkrut'],
                ['value' => 'keluar', 'title' => 'Karyawan Keluar'],
                ['value' => 'dimutasi', 'title' => 'Karyawan Dimutasi'],
                ['value' => 'kehadiran', 'title' => 'Kehadiran'],
                ['value' => 'komposisi_karyawan', 'title' => 'Komposisi Karyawan'],

            ];
        } 
        elseif ($file == 'jam_kerja') {
            $sub_laporan = [
                ['value' => 'jam_kerja_aktual', 'title' => 'Jam Kerja'],
                ['value' => 'manhours', 'title' => 'Man Hours'],
            ];
        }
        elseif ($file == 'produktifitas') {
            $sub_laporan = [
                ['value' => 'produktifitas_aktual', 'title' => 'Produktifitas'],
                ['value' => 'produktifitas_index', 'title' => 'Index Produktifitas'],
            ];
        }
        elseif ($file == 'kapasitas') {
            $sub_laporan = [
                ['value' => 'stick_hours', 'title' => 'Stick / Hours'],
                ['value' => 'balance_prosses', 'title' => 'Balance Prosses'],
                ['value' => 'index_kapasitas', 'title' => 'Index Kapasitas'],

            ];
        }

        else {
            $sub_laporan = [
                ['value' => '', 'title' => '---']
            ];
        }

        return view('pelaporan.partials.sub_laporan', [
            'type' => 'select',
            'sub_laporan' => $sub_laporan
        ]);
    }


    public function preview(Request $request)
    {
        $laporan = $request->get('laporan');
        $data = $request->all();

        $data['tahun'] = $data['tahun'] ?? date('Y');
        $data['bulan'] = $data['bulan'] ?? date('m');
        $data['hari']  = $data['hari'] ?? null;

        if (method_exists($this, $laporan)) {
            return $this->$laporan($data);
        }

        if (view()->exists("pelaporan.laporan.{$laporan}")) {
        return view("pelaporan.laporan.{$laporan}", [
            'data' => $data,
            'tahun' => $data['tahun'],
            'bulan' => $data['bulan'],
            'hari' => $data['hari'],
        ]);
    }

        abort(404, 'Laporan tidak ditemukan');
    }

   private function karyawan(array $data)
    {
        $sub = $data['sub_laporan'] ?? 'terdaftar';
        $viewPath = "pelaporan.laporan.karyawan_{$sub}";

        if (!view()->exists($viewPath)) {
            abort(404, "View untuk sub laporan {$sub} tidak ditemukan");
        }

        $data['title_karyawan'] = 'Data Karyawan';
        $data['title_hadir'] = 'Karyawan Hadir';
        $data['title_tidak_masuk'] = 'Karyawan Tidak Masuk';
        $data['title_direkrut'] = 'Karyawan Direkrut';
        $data['title_keluar'] = 'Karyawan Keluar';
        $data['title_dimutasi'] = 'Karyawan Dimutasi';
        $data['title_kehadiran'] = 'Kehadiran';
        $data['title_komposisi_karyawan'] = 'Komposisi Karyawan';

        $data['data_karyawan'] = [];
        $data['judul'] = ucfirst(str_replace('_', ' ', $sub));

        $view = view($viewPath, $data)->render();

        if ($sub === 'komposisi_karyawan') {
            $pdf = Pdf::loadHTML($view)
                ->setPaper('a3', 'landscape')
                ->setOptions([
                    'margin-top'    => 10,
                    'margin-bottom' => 10,
                    'margin-left'   => 10,
                    'margin-right'  => 10,
                ]);
        } else {
            $pdf = Pdf::loadHTML($view)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'margin-top'    => 20,
                    'margin-bottom' => 20,
                    'margin-left'   => 15,
                    'margin-right'  => 15,
                ]);
        }

        return $pdf->stream($data['judul'] . '.pdf');
    }


    private function volume(array $data)
    {
        $data['title'] = 'Volume Produksi';

        $view = view('pelaporan.laporan.volume_produksi', $data)->render();

        $pdf = PDF::loadHTML($view)
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'margin-top'    => 30,
            'margin-bottom' => 15,
            'margin-left'   => 25,
            'margin-right'  => 20,
            'enable-local-file-access' => true,
        ]);

        return $pdf->stream();
    }

    private function jam_kerja(array $data)
    {
        $sub = $data['sub_laporan'] ?? 'jam_kerja_aktual';

        if (str_starts_with($sub, 'jam_kerja_')) {
        $viewPath = "pelaporan.laporan.{$sub}";
        } else {
            $viewPath = "pelaporan.laporan.jam_kerja_{$sub}";
        }

        if (!view()->exists($viewPath)) {
            abort(404, "View untuk sub laporan {$sub} tidak ditemukan ($viewPath)");
        }

        $data['title_jamkerja'] = 'Laporan Jam Kerja';
        $data['title_manhours'] = 'Laporan Man Hours';
        $data['judul'] = ucfirst(str_replace('_', ' ', $sub));
        $data['data_jamkerja'] = [];

        $view = view($viewPath, $data)->render();

        $pdf = Pdf::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        return $pdf->stream($data['judul'] . '.pdf');
    }
    private function kapasitas(array $data)
    {
        $sub = $data['sub_laporan'] ?? 'stick_hours';
        $viewPath = "pelaporan.laporan.kapasitas_{$sub}";

        if (!view()->exists($viewPath)) {
            abort(404, "View untuk sub laporan {$sub} tidak ditemukan");
        }

        $data['title_stick_hours'] = 'Stick / Hours';
        $data['title_balance_proses'] = 'Balance Proses';
        $data['title_index_kapasitas'] = 'Index Kapasitas';

        $data['data_kapasitas'] = [];
        $data['judul'] = ucfirst(str_replace('_', ' ', $sub));

        $view = view($viewPath, $data)->render();

        $pdf = Pdf::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        return $pdf->stream($data['judul'] . '.pdf');
    }

    private function produktifitas(array $data)
    {
        $sub = $data['sub_laporan'] ?? 'produktifitas_aktual';
        $viewPath = "pelaporan.laporan.{$sub}";

        if (!view()->exists($viewPath)) {
            abort(404, "View untuk sub laporan {$sub} tidak ditemukan");
        }

        $data['title_produktifitas_aktual'] = 'Produktifitas';
        $data['title_index_produktifitas'] = 'Index Produktifitas';

        $data['data_produktifitas'] = [];
        $data['judul'] = ucfirst(str_replace('_', ' ', $sub));

        $view = view($viewPath, $data)->render();

        $pdf = Pdf::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top' => 20,
                'margin-bottom' => 20,
                'margin-left' => 15,
                'margin-right' => 15,
            ]);

        return $pdf->stream($data['judul'] . '.pdf');
    }

    




}
