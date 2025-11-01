<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JenisLaporan;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Produksi;
use App\Models\Level;
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
                ['value' => 'karyawan_terdaftar', 'title' => 'Karyawan Terdaftar'],
                ['value' => 'karyawan_hadir', 'title' => 'Karyawan Hadir'],
                ['value' => 'karyawan_tidak_masuk', 'title' => 'Karyawan Tidak Masuk'],
                ['value' => 'karyawan_direkrut', 'title' => 'Karyawan Direkrut'],
                ['value' => 'karyawan_keluar', 'title' => 'Karyawan Keluar'],
                ['value' => 'karyawan_dimutasi', 'title' => 'Karyawan Dimutasi'],
                ['value' => 'karyawan_kehadiran', 'title' => 'Kehadiran'],
                ['value' => 'karyawan_komposisi_karyawan', 'title' => 'Komposisi Karyawan'],

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

        if (in_array($laporan, ['karyawan', 'jam_kerja', 'produktifitas']) && $sub) {
            $method = "{$sub}";
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

    public function monitoring_karyawan(array $data)
    {
        $tahun = $data['tahun'];
        $bulan = $data['bulan'];
        $minggu_ke = $data['minggu_ke'] ?? null;

        if ($minggu_ke) {
            $rentang = explode('#', $minggu_ke);
            $tanggal_awal = $rentang[0];
            $tanggal_akhir = $rentang[1];
            $periode_label = 'Mingguan';
        } else {
            $tanggal_awal = "{$tahun}-{$bulan}-01";
            $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));
            $periode_label = 'Bulanan';
        }

        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['getkaryawan.getlevel'])
            ->get();

                
        $kategori_list = ['Giling', 'Gunting', 'Pack', 'OPP', 'Banderol', 'MOP', 'Multi Skill'];

        // Daftar peringkat tetap
        $peringkat_list = ['Resmi', 'Mahir', 'Terampil', 'Pemula'];

        // Bangun struktur kategori => peringkat
        $kategori = [];
        foreach ($kategori_list as $bagian) {
            $kategori[$bagian] = collect();
            foreach ($peringkat_list as $lvl) {
                $kategori[$bagian]->push([
                    'peringkat' => $lvl,
                    'bagian' => $bagian,
                    'nama' => "Operator {$bagian} - {$lvl}",
                ]);
            }
        }
        
        $title = "Data Monitoring Karyawan Periode {$periode_label}";

        $view = view('pelaporan.laporan.monitoring_karyawan', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'absensi'       => $absensi,
            'kategori'      => $kategori, 
            'title'         => $title,
            'bulan'         => $bulan,
            'tahun'         => $tahun,
            'periode_label' => $periode_label,
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

        return $pdf->stream('Monitoring Karyawan.pdf');
    }
public function produktifitas_index(array $data)
{
    $minggu_ke = explode('#', request()->get('minggu_ke'));
    $tanggal_awal = trim($minggu_ke[0]);
    $tanggal_akhir = trim($minggu_ke[1]);

    $produksi = Produksi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
        ->with(['karyawan.getlevel'])
        ->get();

    $total_data = $produksi->count(); // total seluruh baris data dalam rentang tanggal

    $data_per_tanggal = [];

    foreach ($produksi as $p) {
        $tgl = $p->tanggal;
        $level_id = $p->karyawan->getlevel->id ?? null;

        if ($level_id && $total_data > 0) {
            // Jumlah semua nilai (baik + buruk + buruk2)
            $jumlah = (int)$p->jumlah_baik + (int)$p->jumlah_buruk + (int)$p->jumlah_buruk2;

            // Hitung rata-rata per tanggal (dibagi total seluruh data)
            $data_per_tanggal[$tgl][$level_id] = ($data_per_tanggal[$tgl][$level_id] ?? 0) + round($jumlah / $total_data, 2);
        }
    }

    $title = 'Index Produktifitas';

    $view = view('pelaporan.laporan.produktifitas_index', [
        'tanggal_awal'  => $tanggal_awal,
        'tanggal_akhir' => $tanggal_akhir,
        'minggu_ke'     => $minggu_ke,
        'data'          => $data_per_tanggal,
        'title'         => $title,
        'bulan'         => $data['bulan'] ?? null,
        'tahun'         => $data['tahun'] ?? null,
    ])->render();

    $pdf = Pdf::loadHTML($view)
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'margin-top'    => 30,
            'margin-bottom' => 15,
            'margin-left'   => 25,
            'margin-right'  => 20,
            'enable-local-file-access' => true,
        ]);

    return $pdf->stream('Produktifitas Index.pdf');
}




 function produktifitas_aktual(array $data)
{
    $minggu_ke = explode('#', request()->get('minggu_ke'));
    $tanggal_awal = trim($minggu_ke[0]);
    $tanggal_akhir = trim($minggu_ke[1]);

    $produksi = Produksi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
        ->with(['karyawan.getlevel'])
        ->get();

    $total_data = $produksi->count();

    $data_per_tanggal = [];

    if ($total_data > 0) {
        foreach ($produksi as $p) {
            $tgl = $p->tanggal;
            $level_id = $p->karyawan->getlevel->id ?? null;

            if ($level_id) {
                // gunakan float dan jangan round di sini
                $jumlah = (float) $p->jumlah_baik;

                // bagi dengan total_data, simpan hasil presisi
                $nilai = $jumlah / $total_data;

                // akumulasi per tanggal & level
                $data_per_tanggal[$tgl][$level_id] = ($data_per_tanggal[$tgl][$level_id] ?? 0) + $nilai;
            }
        }
    }

    $title = 'Produktifitas (Orang/Jam/Batang)';

    $view = view('pelaporan.laporan.produktifitas_aktual', [
        'tanggal_awal'  => $tanggal_awal,
        'tanggal_akhir' => $tanggal_akhir,
        'minggu_ke'     => $minggu_ke,
        'data'          => $data_per_tanggal,
        'title'         => $title,
        'bulan'         => $data['bulan'] ?? null,
        'tahun'         => $data['tahun'] ?? null,
    ])->render();

    $pdf = Pdf::loadHTML($view)
        ->setPaper('a4', 'landscape')
        ->setOptions([
            'margin-top'    => 30,
            'margin-bottom' => 15,
            'margin-left'   => 25,
            'margin-right'  => 20,
            'enable-local-file-access' => true,
        ]);

    return $pdf->stream('Produktifitas Aktual.pdf');
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

    

    




}
