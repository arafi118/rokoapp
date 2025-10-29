<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JenisLaporan;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Produksi;
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
                ['value' => 'aktual', 'title' => 'Jam Kerja'],
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
        $sub = $request->get('sub_laporan');
        $data = $request->all();

        $data['tahun'] = $data['tahun'] ?? date('Y');
        $data['bulan'] = $data['bulan'] ?? date('m');
        $data['hari']  = $data['hari'] ?? null;

        if (in_array($laporan, ['karyawan', 'jam_kerja']) && $sub) {
            $method = "{$laporan}_{$sub}";
            if (method_exists($this, $method)) {
                return $this->$method($data);
            }
        }
        if (method_exists($this, $laporan)) {
            return $this->$laporan($data);
        }

        if (view()->exists("pelaporan.laporan.{$laporan}")) {
            return view("pelaporan.laporan.{$laporan}", $data);
        }

        abort(404, 'Laporan tidak ditemukan');
    }

    public function karyawan_terdaftar(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Hitung jumlah karyawan per tanggal dan per level
        $karyawan = [];
        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($karyawan[$tanggal])) {
                    $karyawan[$tanggal] = [];
                }

                if (isset($karyawan[$tanggal][$level_id])) {
                    $karyawan[$tanggal][$level_id] += 1;
                } else {
                    $karyawan[$tanggal][$level_id] = 1;
                }
            }
        }
        $title = 'Data Karyawan';

        $view = view('pelaporan.laporan.karyawan_terdaftar', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'absensi'       => $absensi,
            'karyawan'      => $karyawan,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
        ])->render();

         $pdf = PDF::loadHTML($view)
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'margin-top'    => 30,
            'margin-bottom' => 15,
            'margin-left'   => 25,
            'margin-right'  => 20,
            'enable-local-file-access' => true,
        ]);

        return $pdf->stream('Karyawan Terdaftar.pdf');
    }

    private function karyawan_hadir(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->where('status','H')
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Hitung jumlah karyawan per tanggal dan per level
        $karyawan = [];
        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($karyawan[$tanggal])) {
                    $karyawan[$tanggal] = [];
                }

                if (isset($karyawan[$tanggal][$level_id])) {
                    $karyawan[$tanggal][$level_id] += 1;
                } else {
                    $karyawan[$tanggal][$level_id] = 1;
                }
            }
        }
        $title = 'Karyawan Hadir';

        $view = view('pelaporan.laporan.karyawan_hadir', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'absensi'       => $absensi,
            'karyawan'      => $karyawan,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
        ])->render();
        $pdf = PDF::loadHTML($view)
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'margin-top'    => 30,
            'margin-bottom' => 15,
            'margin-left'   => 25,
            'margin-right'  => 20,
            'enable-local-file-access' => true,
        ]);


        return $pdf->stream('Karyawan Hadir.pdf');
    }

    private function karyawan_tidak_masuk(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->where('status','T')
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Hitung jumlah karyawan per tanggal dan per level
        $karyawan = [];
        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($karyawan[$tanggal])) {
                    $karyawan[$tanggal] = [];
                }

                if (isset($karyawan[$tanggal][$level_id])) {
                    $karyawan[$tanggal][$level_id] += 1;
                } else {
                    $karyawan[$tanggal][$level_id] = 1;
                }
            }
        }
        // dd($absensi->groupBy('tanggal')->keys());

        $title = 'Karyawan Tidak Masuk';

        $view = view('pelaporan.laporan.karyawan_tidak_masuk', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'absensi'       => $absensi,
            'karyawan'      => $karyawan,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
        ])->render();
        $pdf = PDF::loadHTML($view)
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'margin-top'    => 30,
            'margin-bottom' => 15,
            'margin-left'   => 25,
            'margin-right'  => 20,
            'enable-local-file-access' => true,
        ]);


        return $pdf->stream('Karyawan Tidak Masuk.pdf');
    }

    public function karyawan_direkrut(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        // Ambil data karyawan yang direkrut berdasarkan tanggal_masuk
        $karyawan_baru = Karyawan::whereBetween('tanggal_masuk', [$tanggal_awal, $tanggal_akhir])
            ->with('getlevel')
            ->get();

        // Hitung jumlah karyawan per tanggal & per level
        $rekruit = [];
        foreach ($karyawan_baru as $k) {
            $tanggal = $k->tanggal_masuk;
            $level_id = $k->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($rekruit[$tanggal])) {
                    $rekruit[$tanggal] = [];
                }

                $rekruit[$tanggal][$level_id] = ($rekruit[$tanggal][$level_id] ?? 0) + 1;
            }
        }

        $title = 'Karyawan Direkrut';

        $view = view('pelaporan.laporan.karyawan_direkrut', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'karyawan'      => $rekruit,
            'karyawan_baru' => $karyawan_baru,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
        ])->render();

        $pdf = PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top'    => 30,
                'margin-bottom' => 15,
                'margin-left'   => 25,
                'margin-right'  => 20,
                'enable-local-file-access' => true,
            ]);

        return $pdf->stream('Karyawan Direkrut.pdf');
    }

    public function karyawan_keluar(array $data) 
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        // Ambil data karyawan yang KELUAR berdasarkan tanggal_keluar
        $karyawan_keluar = Karyawan::whereBetween('tanggal_keluar', [$tanggal_awal, $tanggal_akhir])
            ->with('getlevel')
            ->get();

        // Hitung jumlah karyawan keluar per tanggal & per level
        $keluar = [];
        foreach ($karyawan_keluar as $k) {
            $tanggal = $k->tanggal_keluar;
            $level_id = $k->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($keluar[$tanggal])) {
                    $keluar[$tanggal] = [];
                }

                $keluar[$tanggal][$level_id] = ($keluar[$tanggal][$level_id] ?? 0) + 1;
            }
        }
        $title = 'Karyawan Keluar';

        $view = view('pelaporan.laporan.karyawan_keluar', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'karyawan'      => $keluar,
            'karyawan_keluar' => $karyawan_keluar,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
        ])->render();

        $pdf = PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top'    => 30,
                'margin-bottom' => 15,
                'margin-left'   => 25,
                'margin-right'  => 20,
                'enable-local-file-access' => true,
            ]);

        return $pdf->stream('Karyawan Keluar.pdf');
    }

    public function karyawan_dimutasi(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        // Ambil data absensi dalam rentang minggu
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Deteksi karyawan yang punya group/meja berbeda di minggu itu
        $mutasi = [];
        $kelompok_per_tanggal = [];

        $grouping = $absensi->groupBy('karyawan_id');
        foreach ($grouping as $karyawan_id => $records) {
            $uniqueGroup = $records->pluck('group_id')->unique();
            $uniqueMeja = $records->pluck('meja_id')->unique();

            if ($uniqueGroup->count() > 1 || $uniqueMeja->count() > 1) {
                // Ambil tanggal terakhir mutasi
                $tanggal_mutasi = $records->sortBy('tanggal')->last()->tanggal;
                $karyawan = $records->first()->getkaryawan;
                $level_id = $karyawan->getlevel->id ?? null;

                if ($level_id) {
                    if (!isset($kelompok_per_tanggal[$tanggal_mutasi])) {
                        $kelompok_per_tanggal[$tanggal_mutasi] = [];
                    }
                    $kelompok_per_tanggal[$tanggal_mutasi][$level_id] =
                        ($kelompok_per_tanggal[$tanggal_mutasi][$level_id] ?? 0) + 1;
                }

                $mutasi[] = $karyawan;
            }
        }

        $title = 'Karyawan Dimutasi';

        $view = view('pelaporan.laporan.karyawan_dimutasi', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'karyawan'      => $kelompok_per_tanggal,
            'karyawan_mutasi' => $mutasi,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
        ])->render();
        
        
        $pdf = PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top'    => 30,
                'margin-bottom' => 15,
                'margin-left'   => 25,
                'margin-right'  => 20,
                'enable-local-file-access' => true,
            ]);

        return $pdf->stream('Karyawan Dimutasi.pdf');
    }

private function karyawan_komposisi_karyawan(array $data)
{
    $minggu_ke = explode('#', request()->get('minggu_ke'));
    $tanggal_awal = trim($minggu_ke[0]);
    $tanggal_akhir = trim($minggu_ke[1]);

    // Ambil semua data produksi di rentang tanggal
    $produksi = Produksi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
        ->with(['karyawan.getlevel', 'karyawan.getgroup']) // ✅ gunakan relasi yang benar
        ->get();

    // Debug dulu untuk memastikan ada data
    if ($produksi->isEmpty()) {
        dd('Data produksi kosong untuk rentang tanggal tersebut', $tanggal_awal, $tanggal_akhir);
    }

    $komposisi = []; // [tanggal][bagian][kategori] = jumlah

    foreach ($produksi as $p) {
        $tanggal = $p->tanggal;

        // Ambil nama group dari relasi karyawan
        $group_nama = strtolower($p->karyawan->getgroup->nama ?? '-');
        $bagian = 'Lainnya';

        if (str_contains($group_nama, 'giling')) {
            $bagian = 'Giling';
        } elseif (str_contains($group_nama, 'gunting')) {
            $bagian = 'Gunting';
        } elseif (str_contains($group_nama, 'packing')) {
            $bagian = 'Packing';
        } elseif (str_contains($group_nama, 'banderol')) {
            $bagian = 'Banderol';
        } elseif (str_contains($group_nama, 'opp')) {
            $bagian = 'OPP';
        } elseif (str_contains($group_nama, 'mop')) {
            $bagian = 'MOP';
        }

        // Ambil kode level (E/D/C/B/A)
        $level_kode = strtoupper($p->karyawan->getlevel->kode ?? 'D');

        // Hitung jumlah per tanggal, bagian, dan level
        $komposisi[$tanggal][$bagian][$level_kode] =
            ($komposisi[$tanggal][$bagian][$level_kode] ?? 0) + 1;
    }
    // Siapkan data untuk Blade
    $data['komposisi'] = $komposisi;
    $data['tanggal_awal'] = $tanggal_awal;
    $data['tanggal_akhir'] = $tanggal_akhir;
    $data['title'] = 'Komposisi Karyawan';

    $view = view('pelaporan.laporan.karyawan_komposisi_karyawan', $data)->render();

    $pdf = Pdf::loadHTML($view)
        ->setPaper('a3', 'landscape')
        ->setOptions(['margin' => 10]);

    return $pdf->stream('Komposisi Karyawan.pdf');
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

    private function jam_kerja_aktual(array $data)
    {
        $data['title_jamkerja'] = 'Laporan Jam Kerja';
        $view = view('pelaporan.laporan.jam_kerja_aktual', $data)->render();

        $pdf = Pdf::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top'    => 20,
                'margin-bottom' => 20,
                'margin-left'   => 15,
                'margin-right'  => 15,
            ]);

        return $pdf->stream('Jam Kerja.pdf');
    }

    private function jam_kerja_manhours(array $data)
    {
        $data['title_manhours'] = 'Laporan Man Hours';
        $view = view('pelaporan.laporan.jam_kerja_manhours', $data)->render();

        $pdf = Pdf::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top'    => 20,
                'margin-bottom' => 20,
                'margin-left'   => 15,
                'margin-right'  => 15,
            ]);

        return $pdf->stream('Man Hours.pdf');
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
