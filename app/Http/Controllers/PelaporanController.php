<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\JenisLaporan;
use App\Models\Absensi;
use App\Models\Karyawan;
use App\Models\Produksi;
use App\Models\Level;
use App\Models\Group;
use App\Models\Mutasi;
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
                ['value' => 'karyawan_komposisi_karyawan', 'title' => 'Komposisi Karyawan'],
                ['value' => 'volume_produksi', 'title' => 'Volume Produksi'],


            ];
        } elseif ($file == 'jam_kerja') {
            $sub_laporan = [
                ['value' => 'jam_kerja_aktual', 'title' => 'Jam Kerja'],
                ['value' => 'jam_kerja_manhours', 'title' => 'Man Hours'],
            ];
        } elseif ($file == 'produktifitas') {
            $sub_laporan = [
                ['value' => 'produktifitas_aktual', 'title' => 'Produktivitas'],
                ['value' => 'produktifitas_index', 'title' => 'Index Produktivitas'],
                ['value' => 'produktifitas_harian', 'title' => 'Produksi Harian'],

            ];
        } elseif ($file == 'kapasitas') {
            $sub_laporan = [
                ['value' => 'stick_hours', 'title' => 'Stick / Hours'],
                ['value' => 'balance_prosses', 'title' => 'Balance Prosses'],
                ['value' => 'index_kapasitas', 'title' => 'Index Kapasitas'],

            ];
        } elseif ($file == 'produksi') {
            // 🔹 ambil semua kelompok dari tabel group
            $groups = Group::select('id', 'nama')->orderBy('nama')->get();

            // ubah jadi array sesuai format sub_laporan
            $sub_laporan = $groups->map(function ($g) {
                return [
                    'value' => 'laporan_produksi_' . $g->id, // nanti method dinamis
                    'title' => $g->nama
                ];
            })->toArray();
        } else {
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

        if (in_array($laporan, ['karyawan', 'jam_kerja', 'produktifitas', 'kapasitas', 'produksi']) && $sub) {
            $method = "{$sub}";
            if (method_exists($this, $method)) {
                return $this->$method($data);
            }
        }
        if (str_starts_with($sub, 'laporan_produksi_')) {
            $group_id = str_replace('laporan_produksi_', '', $sub);
            return $this->laporan_produksi($data, $group_id);
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
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // --- Ambil data absensi minggu terpilih
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['getkaryawan.getlevel'])
            ->get();

        // --- Hitung per tanggal & level
        $karyawan = [];
        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($karyawan[$tanggal])) {
                    $karyawan[$tanggal] = [];
                }

                $karyawan[$tanggal][$level_id] = ($karyawan[$tanggal][$level_id] ?? 0) + 1;
            }
        }


        // ========= "TERDAFTAR BULAN LALU" =========
        $tanggal_bulan_lalu = \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d');

        $absen_bulan_lalu = Absensi::whereDate('tanggal', $tanggal_bulan_lalu)
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Hitung per level (1-8)
        $terdaftar_lalu = [];
        for ($i = 1; $i <= 8; $i++) {
            $terdaftar_lalu[$i] = 0;
        }

        foreach ($absen_bulan_lalu as $a) {
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id && isset($terdaftar_lalu[$level_id])) {
                $terdaftar_lalu[$level_id] += 1;
            }
        }

        $title = 'Data Karyawan';

        $view = view('pelaporan.laporan.karyawan_terdaftar', [
            'tanggal_awal'        => $tanggal_awal,
            'tanggal_akhir'       => $tanggal_akhir,
            'minggu_ke'           => $minggu_ke,
            'absensi'             => $absensi,
            'karyawan'            => $karyawan,
            'title'               => $title,
            'bulan'               => $data['bulan'],
            'tahun'               => $data['tahun'],

            // kirim variabel baru
            'terdaftar_lalu'      => $terdaftar_lalu,
            'tanggal_bulan_lalu'  => $tanggal_bulan_lalu,
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

        // Ambil absensi hadir dalam periode
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->where('status', 'H')
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Hitung jumlah hadir per tanggal per level
        $karyawan = [];
        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                if (!isset($karyawan[$tanggal])) {
                    $karyawan[$tanggal] = [];
                }

                $karyawan[$tanggal][$level_id] = ($karyawan[$tanggal][$level_id] ?? 0) + 1;
            }
        }

        // Hitung Data Hadir Bulan Lalu
        $tanggal_bulan_lalu = \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d');

        $absensi_lalu = Absensi::where('tanggal', $tanggal_bulan_lalu)
            ->where('status', 'H')
            ->with(['getkaryawan.getlevel'])
            ->get();

        $terdaftar_lalu = array_fill(1, 8, 0); // Default 0

        foreach ($absensi_lalu as $a) {
            $level_id = $a->getkaryawan->getlevel->id ?? null;
            if ($level_id && isset($terdaftar_lalu[$level_id])) {
                $terdaftar_lalu[$level_id] += 1;
            }
        }

        $title = 'Karyawan Hadir';

        $view = view('pelaporan.laporan.karyawan_hadir', [
            'tanggal_awal'      => $tanggal_awal,
            'tanggal_akhir'     => $tanggal_akhir,
            'minggu_ke'         => $minggu_ke,
            'absensi'           => $absensi,
            'karyawan'          => $karyawan,
            'title'             => $title,
            'bulan'             => $data['bulan'],
            'tahun'             => $data['tahun'],
            'terdaftar_lalu'    => $terdaftar_lalu,
            'tanggal_bulan_lalu' => $tanggal_bulan_lalu,
        ])->render();

        return PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->stream('Karyawan Hadir.pdf');
    }


    private function karyawan_tidak_masuk(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        // Ambil absensi tidak masuk dalam periode
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->where('status', 'T')
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Hitung jumlah per tanggal & per level
        $karyawan = [];
        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                $karyawan[$tanggal][$level_id] = ($karyawan[$tanggal][$level_id] ?? 0) + 1;
            }
        }

        // ==============================
        // Hitung Tidak Masuk Bulan Lalu
        // ==============================
        $tanggal_bulan_lalu = \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d');

        $absensi_lalu = Absensi::where('tanggal', $tanggal_bulan_lalu)
            ->where('status', 'T')
            ->with(['getkaryawan.getlevel'])
            ->get();

        $tidak_masuk_lalu = array_fill(1, 8, 0);

        foreach ($absensi_lalu as $a) {
            $level_id = $a->getkaryawan->getlevel->id ?? null;
            if ($level_id && isset($tidak_masuk_lalu[$level_id])) {
                $tidak_masuk_lalu[$level_id] += 1;
            }
        }

        $title = 'Karyawan Tidak Masuk';

        $view = view('pelaporan.laporan.karyawan_tidak_masuk', [
            'tanggal_awal'       => $tanggal_awal,
            'tanggal_akhir'      => $tanggal_akhir,
            'minggu_ke'          => $minggu_ke,
            'absensi'            => $absensi,
            'karyawan'           => $karyawan,
            'title'              => $title,
            'bulan'              => $data['bulan'],
            'tahun'              => $data['tahun'],
            'tidak_masuk_lalu'   => $tidak_masuk_lalu,
            'tanggal_bulan_lalu' => $tanggal_bulan_lalu,
        ])->render();

        return PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->stream('Karyawan Tidak Masuk.pdf');
    }


    public function karyawan_direkrut(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        // DATA DIREKRUT PERIODE INI

        $karyawan_baru = Karyawan::whereBetween('tanggal_masuk', [$tanggal_awal, $tanggal_akhir])
            ->with('getlevel')
            ->get();

        $rekruit = [];
        foreach ($karyawan_baru as $k) {
            $tanggal = $k->tanggal_masuk;
            $level_id = $k->getlevel->id ?? null;

            if ($level_id) {
                $rekruit[$tanggal][$level_id] = ($rekruit[$tanggal][$level_id] ?? 0) + 1;
            }
        }

        // DATA DIREKRUT BULAN LALU

        $tanggal_bulan_lalu = \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d');

        $rekruit_lalu_raw = Karyawan::where('tanggal_masuk', $tanggal_bulan_lalu)
            ->with('getlevel')
            ->get();

        $rekruit_bulan_lalu = array_fill(1, 8, 0);

        foreach ($rekruit_lalu_raw as $k) {
            $level_id = $k->getlevel->id ?? null;

            if ($level_id && isset($rekruit_bulan_lalu[$level_id])) {
                $rekruit_bulan_lalu[$level_id]++;
            }
        }

        $title = 'Karyawan Direkrut';

        // kirim ke view
        $view = view('pelaporan.laporan.karyawan_direkrut', [
            'tanggal_awal'        => $tanggal_awal,
            'tanggal_akhir'       => $tanggal_akhir,
            'minggu_ke'           => $minggu_ke,
            'karyawan'            => $rekruit,
            'title'               => $title,
            'bulan'               => $data['bulan'],
            'tahun'               => $data['tahun'],
            'rekruit_bulan_lalu'  => $rekruit_bulan_lalu,
            'tanggal_bulan_lalu'  => $tanggal_bulan_lalu,
        ])->render();

        return PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->stream('Karyawan Direkrut.pdf');
    }


    public function karyawan_keluar(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        //  DATA KELUAR BULAN LALU
        $tanggal_bulan_lalu = \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d');

        $keluar_lalu_raw = Karyawan::where('tanggal_keluar', $tanggal_bulan_lalu)
            ->with('getlevel')
            ->get();

        $keluar_bulan_lalu = array_fill(1, 8, 0);

        foreach ($keluar_lalu_raw as $k) {
            $level_id = $k->getlevel->id ?? null;
            if ($level_id && isset($keluar_bulan_lalu[$level_id])) {
                $keluar_bulan_lalu[$level_id]++;
            }
        }

        // DATA KELUAR MINGGU INI
        $karyawan_keluar = Karyawan::whereBetween('tanggal_keluar', [$tanggal_awal, $tanggal_akhir])
            ->with('getlevel')
            ->get();

        $keluar = [];
        foreach ($karyawan_keluar as $k) {
            $tanggal = $k->tanggal_keluar;
            $level_id = $k->getlevel->id ?? null;

            if ($level_id) {
                $keluar[$tanggal][$level_id] = ($keluar[$tanggal][$level_id] ?? 0) + 1;
            }
        }

        return PDF::loadHTML(view('pelaporan.laporan.karyawan_keluar', [
            'tanggal_awal'        => $tanggal_awal,
            'tanggal_akhir'       => $tanggal_akhir,
            'minggu_ke'           => $minggu_ke,
            'karyawan'            => $keluar,
            'title'               => 'Karyawan Keluar',
            'bulan'               => $data['bulan'],
            'tahun'               => $data['tahun'],
            'keluar_bulan_lalu'   => $keluar_bulan_lalu,
            'tanggal_bulan_lalu'  => $tanggal_bulan_lalu,
        ]))
            ->setPaper('a4', 'landscape')
            ->stream('Karyawan Keluar.pdf');
    }


    public function karyawan_dimutasi(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        //  DATA MUTASI BULAN LALU
        $tanggal_bulan_lalu = \Carbon\Carbon::parse($tanggal_awal)->subDay()->format('Y-m-d');

        $mutasi_lalu_raw = Mutasi::where('tanggal', $tanggal_bulan_lalu)
            ->with(['getkaryawan.getlevel'])
            ->get();

        $mutasi_bulan_lalu = array_fill(1, 8, 0);

        foreach ($mutasi_lalu_raw as $row) {
            $level_id = $row->getkaryawan->getlevel->id ?? null;
            if ($level_id && isset($mutasi_bulan_lalu[$level_id])) {
                $mutasi_bulan_lalu[$level_id]++;
            }
        }

        // DATA MUTASI MINGGU BERJALAN
        $mutasi = Mutasi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['getkaryawan.getlevel'])
            ->get();

        $kelompok_per_tanggal = [];

        foreach ($mutasi as $row) {
            $tanggal = $row->tanggal;
            $level_id = $row->getkaryawan->getlevel->id ?? null;

            if ($level_id) {
                $kelompok_per_tanggal[$tanggal][$level_id] =
                    ($kelompok_per_tanggal[$tanggal][$level_id] ?? 0) + 1;
            }
        }

        return PDF::loadHTML(view('pelaporan.laporan.karyawan_dimutasi', [
            'tanggal_awal'        => $tanggal_awal,
            'tanggal_akhir'       => $tanggal_akhir,
            'minggu_ke'           => $minggu_ke,
            'karyawan'            => $kelompok_per_tanggal,
            'title'               => 'Karyawan Dimutasi',
            'bulan'               => $data['bulan'],
            'tahun'               => $data['tahun'],
            'mutasi_bulan_lalu'   => $mutasi_bulan_lalu,
            'tanggal_bulan_lalu'  => $tanggal_bulan_lalu,
        ]))
            ->setPaper('a4', 'landscape')
            ->stream('Karyawan Dimutasi.pdf');
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

        $tanggal_awal = "{$tahun}-{$bulan}-01";
        $tanggal_akhir = date("Y-m-t", strtotime($tanggal_awal));
        $periode_label = 'Bulanan';
        if ($minggu_ke) {
            $rentang = explode('#', $minggu_ke);
            $tanggal_awal = $rentang[0];
            $tanggal_akhir = $rentang[1];
            $periode_label = 'Mingguan';
        }

        $levels = Level::get()->pluck([], 'id')->toArray();
        $peringkat = [
            'P' => 'Pemula',
            'T' => 'Terampil',
            'M' => 'Mahir',
            'R' => 'Resmi',
        ];

        $daftarKaryawan = Karyawan::with([
            'getmutasi' => function ($q) use ($tanggal_akhir) {
                $q->where('tanggal', '<=', $tanggal_akhir)->orderBy('tanggal', 'desc');
            }
        ])->orderBy('id', 'asc')->get();

        $listMutasiKaryawan = [];
        foreach ($daftarKaryawan as $karyawan) {
            $dataKaryawan = [];

            $dataKaryawan['id'] = $karyawan->id;
            $dataKaryawan['peringkat'] = substr($karyawan->kode_karyawan, 0, 1);
            $dataKaryawan['masuk'] = $karyawan->tanggal_masuk;
            $dataKaryawan['keluar'] = $karyawan->tanggal_keluar;
            $dataKaryawan['level'] = $karyawan->level;
            $dataKaryawan['status'] = $karyawan->status;
            $dataKaryawan['mutasi'] = [];

            if (count($karyawan->getmutasi) > 0) {
                $mutasi = $karyawan->getmutasi[0];
                $mutasiKaryawan = json_decode($mutasi->karyawan, true);
                $riwayatKaryawan = json_decode($mutasi->riwayat, true);

                if ($mutasi->tanggal < $tanggal_awal) {
                    $dataKaryawan = [
                        'id' => $mutasiKaryawan['id'],
                        'peringkat' => substr($mutasiKaryawan['kode_karyawan'], 0, 1),
                        'masuk' => $mutasiKaryawan['tanggal_masuk'],
                        'keluar' => $mutasiKaryawan['tanggal_keluar'],
                        'level' => $mutasiKaryawan['level'],
                        'status' => $mutasiKaryawan['status'],
                    ];
                } else {
                    $dataKaryawan = [
                        'id' => $riwayatKaryawan['id'],
                        'peringkat' => substr($riwayatKaryawan['kode_karyawan'], 0, 1),
                        'masuk' => $riwayatKaryawan['tanggal_masuk'],
                        'keluar' => $riwayatKaryawan['tanggal_keluar'],
                        'level' => $riwayatKaryawan['level'],
                        'status' => $riwayatKaryawan['status'],
                        'mutasi' => [
                            'jenis_mutasi' => $mutasi->jenis_mutasi,
                            'peringkat' => substr($mutasiKaryawan['kode_karyawan'], 0, 1),
                            'masuk' => $mutasi->tanggal,
                            'keluar' => $mutasiKaryawan['tanggal_keluar'],
                            'level' => $mutasiKaryawan['level'],
                            'status' => $mutasiKaryawan['status'],
                        ],
                    ];
                }
            }

            $listMutasiKaryawan[] = $dataKaryawan;
        }

        foreach ($listMutasiKaryawan as $listMutasi) {
            if ($listMutasi['keluar'] < $tanggal_awal && $listMutasi['status'] === 'nonaktif') {
                continue;
            }

            $isAwal = $listMutasi['masuk'] < $tanggal_awal;
            $levelRef = &$levels[$listMutasi['level']]['data'][$listMutasi['peringkat']];
            $key = $isAwal ? 'terdaftar_awal' : 'baru';
            $levelRef[$key] = ($levelRef[$key] ?? 0) + 1;

            if (!empty($listMutasi['mutasi'])) {
                $mutasi = $listMutasi['mutasi'];
                $mutasiRef = &$levels[$mutasi['level']]['data'][$mutasi['peringkat']];
                $jenis = $mutasi['jenis_mutasi'];

                $levelRef['pengurangan'][$jenis] = ($levelRef['pengurangan'][$jenis] ?? 0) + 1;
                $mutasiRef['penambahan'][$jenis] = ($mutasiRef['penambahan'][$jenis] ?? 0) + 1;
            }
        }

        $grouped = collect($levels)->groupBy('level_karyawan');
        $levels = $grouped->toArray();

        $title = "Data Monitoring Karyawan Periode {$periode_label}";
        $view = view('pelaporan.laporan.monitoring_karyawan', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'levels'        => $levels,
            'peringkat'     => $peringkat,
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

        return $pdf->stream($title . '.pdf');
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

        $title = 'Index Produktivitas';

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

        $title = 'Produktivitas (Orang/Jam/Batang)';

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

    private function laporan_produksi(array $data, $group_id)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // Ambil data absensi & produksi sesuai group_id + periode
        $absensi = Absensi::with(['getkaryawan.getanggota', 'getgroup'])
            ->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->where('group_id', $group_id)
            ->get();

        $produksi = Produksi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->whereIn('karyawan_id', $absensi->pluck('karyawan_id')->unique())
            ->get();

        // Susun data per group -> karyawan -> tanggal
        $kelompok_data = [];

        foreach ($absensi as $a) {
            // skip jika relasi tidak lengkap
            if (!$a->getgroup || !$a->getkaryawan) {
                continue;
            }

            $group_nama    = $a->getgroup->nama ?? '-';
            $karyawan      = $a->getkaryawan;
            $anggota       = $karyawan->getanggota ?? null;
            $karyawan_id   = $a->karyawan_id;
            $tgl_absen     = $a->tanggal;
            $karyawan_nama = $anggota->nama ?? '-';
            $nip           = $karyawan->kode_karyawan ?? '-';
            $plan          = $a->target_harian ?? 0;

            // hitung actual
            $actual = $produksi
                ->where('karyawan_id', $karyawan_id)
                ->where('tanggal', $tgl_absen)
                ->sum('jumlah_baik');

            $kelompok_data[$group_id]['nama'] = $group_nama;
            $kelompok_data[$group_id]['data'][$karyawan_id]['nama'] = $karyawan_nama;
            $kelompok_data[$group_id]['data'][$karyawan_id]['nip'] = $nip;
            $kelompok_data[$group_id]['data'][$karyawan_id]['tanggal'][$tgl_absen] = [
                'plan'   => $plan,
                'actual' => $actual,
            ];
        }

        // Jika tidak ada data sama sekali, buat struktur kosong agar tabel tetap muncul
        if (empty($kelompok_data)) {
            $kelompok_data[$group_id] = [
                'nama' => '-',
                'data' => [
                    [
                        'nama' => '-',
                        'nip' => '-',
                        'tanggal' => [],
                    ],
                ],
            ];
        }

        $title = 'Laporan Produksi Harian';

        $view = view('pelaporan.laporan.produksi_harian', [
            'title'         => $title,
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'kelompok_data' => $kelompok_data,
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

        return $pdf->stream('laporan_produksi_' . $group_id . '.pdf');
    }

    private function volume_produksi(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // Ambil level operator (bukan mandor / kepala)
        $levels = Level::where('level_karyawan', 1)
            ->orderBy('id', 'asc')
            ->get();

        // --- PRODUKSI AKTUAL ---
        $produksi = Produksi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['karyawan.getlevel'])
            ->get();

        $rekap = [];

        foreach ($produksi as $p) {
            $tanggal = $p->tanggal;
            $level_nama = $p->karyawan->getlevel->nama ?? null;

            if ($level_nama) {
                if (!isset($rekap[$tanggal][$level_nama])) {
                    $rekap[$tanggal][$level_nama] = 0;
                }

                // Jumlah batang baik per bagian (level)
                $rekap[$tanggal][$level_nama] += (int) $p->jumlah_baik;
            }
        }

        // --- RENCANA PRODUKSI (TARGET HARIAN) ---
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['getkaryawan.getlevel'])
            ->get();

        $rencana = [];

        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_nama = $a->getkaryawan->getlevel->nama ?? null;

            if ($level_nama && $a->target_harian) {
                if (!isset($rencana[$tanggal][$level_nama])) {
                    $rencana[$tanggal][$level_nama] = 0;
                }

                // Jumlahkan target_harian per level per tanggal
                $rencana[$tanggal][$level_nama] += (int) $a->target_harian;
            }
        }

        $title = 'Volume Produksi';

        $view = view('pelaporan.laporan.volume_produksi', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'produksi'      => $produksi,
            'rekap'         => $rekap,
            'rencana'       => $rencana, // ← ditambahkan ke view
            'levels'        => $levels,
            'title'         => $title,
            'bulan'         => $data['bulan'],
            'tahun'         => $data['tahun'],
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

        return $pdf->stream('Volume_Produksi.pdf');
    }


    private function jam_kerja_aktual(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // Ambil data absensi sesuai periode
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->whereIn('status', ['H', 'T'])
            ->with(['getkaryawan.getlevel'])
            ->get();

        // Inisialisasi variabel
        $jam_kerja = [];
        $rata_rata_jam_kerja = [];

        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            // --- Hanya proses jika karyawan dan tanggal valid
            if (!$level_id) continue;

            // --- Jika jam_masuk dan jam_keluar terisi
            if ($a->jam_masuk && $a->jam_keluar) {
                $jam_masuk = strtotime($a->tanggal . ' ' . $a->jam_masuk);
                $jam_keluar = strtotime($a->tanggal . ' ' . $a->jam_keluar);

                // Jika shift malam (jam keluar < jam masuk)
                if ($jam_keluar < $jam_masuk) {
                    $jam_keluar = strtotime('+1 day', $jam_keluar);
                }

                // Hitung selisih dalam jam
                $selisih_jam = round(($jam_keluar - $jam_masuk) / 3600, 2);
            } else {
                $selisih_jam = 0;
            }

            // Simpan total per tanggal & level
            $jam_kerja[$tanggal][$level_id] = ($jam_kerja[$tanggal][$level_id] ?? 0) + $selisih_jam;

            // Simpan untuk rata-rata
            $rata_rata_jam_kerja[$tanggal][] = $selisih_jam;
        }

        // Hitung rata-rata jam kerja per tanggal (abaikan nilai 0)
        foreach ($rata_rata_jam_kerja as $tgl => $values) {
            $valid = array_filter($values, fn($v) => $v > 0);
            $rata_rata_jam_kerja[$tgl] = count($valid) > 0
                ? round(array_sum($valid) / count($valid), 2)
                : 0;
        }

        $title = 'Laporan Jam Kerja';

        // Render ke tampilan Blade
        $view = view('pelaporan.laporan.jam_kerja_aktual', [
            'tanggal_awal'         => $tanggal_awal,
            'tanggal_akhir'        => $tanggal_akhir,
            'jam_kerja'            => $jam_kerja,
            'rata_rata_jam_kerja'  => $rata_rata_jam_kerja,
            'absensi'              => $absensi,
            'title'                => $title,
            'bulan'                => $data['bulan'],
            'tahun'                => $data['tahun'],
        ])->render();

        // Buat PDF
        $pdf = PDF::loadHTML($view)
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'margin-top'    => 30,
                'margin-bottom' => 15,
                'margin-left'   => 25,
                'margin-right'  => 20,
                'enable-local-file-access' => true,
            ]);

        return $pdf->stream('Jam Kerja.pdf');
    }

    private function jam_kerja_manhours(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // Ambil data absensi selama periode
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->whereIn('status', ['H', 'T'])
            ->with(['getkaryawan.getlevel'])
            ->get();

        $jam_kerja = [];
        $jumlah_karyawan = [];
        $manhours = [];

        foreach ($absensi as $a) {
            $tanggal = $a->tanggal;
            $level_id = $a->getkaryawan->getlevel->id ?? null;

            if (!$level_id || !$a->jam_masuk || !$a->jam_keluar) continue;

            $jam_masuk = strtotime($a->tanggal . ' ' . $a->jam_masuk);
            $jam_keluar = strtotime($a->tanggal . ' ' . $a->jam_keluar);

            // Jika shift malam (jam keluar < jam masuk)
            if ($jam_keluar < $jam_masuk) {
                $jam_keluar = strtotime('+1 day', $jam_keluar);
            }

            $selisih_jam = round(($jam_keluar - $jam_masuk) / 3600, 2);

            // Simpan jam kerja total per tanggal per level
            $jam_kerja[$tanggal][$level_id] = ($jam_kerja[$tanggal][$level_id] ?? 0) + $selisih_jam;

            // Tambah jumlah orang per level
            $jumlah_karyawan[$tanggal][$level_id] = ($jumlah_karyawan[$tanggal][$level_id] ?? 0) + 1;

            // Hitung manhours (jam kerja × jumlah orang)
            $manhours[$tanggal][$level_id] = ($manhours[$tanggal][$level_id] ?? 0) + $selisih_jam;
        }

        // Hitung total per tanggal untuk ditampilkan di bagian bawah
        $total_per_tanggal = [];
        foreach ($manhours as $tanggal => $levels) {
            $total_per_tanggal[$tanggal] = array_sum($levels);
        }

        $title = 'Laporan Jam Kerja (Manhours)';

        $view = view('pelaporan.laporan.jam_kerja_manhours', [
            'tanggal_awal'        => $tanggal_awal,
            'tanggal_akhir'       => $tanggal_akhir,
            'jam_kerja'           => $jam_kerja,
            'jumlah_karyawan'     => $jumlah_karyawan,
            'manhours'            => $manhours,
            'total_per_tanggal'   => $total_per_tanggal,
            'absensi'             => $absensi,
            'title'               => $title,
            'bulan'               => $data['bulan'],
            'tahun'               => $data['tahun'],
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

        return $pdf->stream('Jam Kerja (Manhours).pdf');
    }

    private function stick_hours(array $data, $return_raw = false)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        $produksi = Produksi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])
            ->with(['karyawan.getlevel', 'karyawan.getabsensi'])
            ->get();

        $produksi_stick = [];

        foreach ($produksi as $p) {
            $tanggal = $p->tanggal;
            $karyawan = $p->karyawan;
            $level = $karyawan->getlevel->nama ?? null;

            if ($level) {
                $absen = $karyawan->getabsensi
                    ->where('tanggal', $tanggal)
                    ->first();

                $jam_kerja = 8;
                if ($absen && $absen->jam_masuk && $absen->jam_keluar) {
                    $jam_masuk = \Carbon\Carbon::parse($absen->jam_masuk);
                    $jam_keluar = \Carbon\Carbon::parse($absen->jam_keluar);
                    $jam_kerja = $jam_keluar->diffInHours($jam_masuk);
                }

                if (!isset($produksi_stick[$tanggal])) {
                    $produksi_stick[$tanggal] = [];
                }

                $jumlah_baik = $p->jumlah_baik ?? 0;

                if (isset($produksi_stick[$tanggal][$level])) {
                    $produksi_stick[$tanggal][$level]['total'] += $jumlah_baik;
                    $produksi_stick[$tanggal][$level]['jam_kerja'] += $jam_kerja;
                } else {
                    $produksi_stick[$tanggal][$level] = [
                        'total' => $jumlah_baik,
                        'jam_kerja' => $jam_kerja,
                    ];
                }
            }
        }

        // Hitung produksi per jam (stick/hours)
        foreach ($produksi_stick as $tanggal => &$levels) {
            foreach ($levels as $level => &$data_level) {
                $jam_kerja = $data_level['jam_kerja'] ?: 1;
                $data_level['per_jam'] = $data_level['total'] / $jam_kerja;
            }
        }

        // Jika hanya ingin data mentah
        if ($return_raw) {
            // Kembalikan hanya nilai per jam per level
            $hasil = [];
            foreach ($produksi_stick as $tanggal => $levels) {
                foreach ($levels as $level => $data_level) {
                    $hasil[$tanggal][$level] = $data_level['per_jam'];
                }
            }
            return $hasil;
        }

        $title = 'Stick / Hours';

        $view = view('pelaporan.laporan.kapasitas_stick_hours', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'produksi_stick' => $produksi_stick,
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

        return $pdf->stream('Produksi_per_Jam.pdf');
    }


    public function balance_prosses(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // Ambil hasil stick/hours dalam bentuk array
        $stick_hours_data = $this->stick_hours($data, true);

        $hasil = [];
        $persentase = [];

        foreach ($stick_hours_data as $tanggal => $levels) {
            // Cari nilai tertinggi dari semua level di tanggal tsb
            $max = max($levels);

            foreach ($levels as $level => $value) {
                // Bandingkan dengan nilai tertinggi
                $hasil[$tanggal][$level] = round($value, 3);
                $persentase[$tanggal][$level] = $max > 0
                    ? round($value / $max, 2)
                    : 0;
            }
        }

        $title = 'Balance Proses';

        $view = view('pelaporan.laporan.kapasitas_balance_prosses', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'hasil'         => $hasil,
            'persentase'    => $persentase,
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

        return $pdf->stream('Balance_Proses.pdf');
    }

    public function index_kapasitas(array $data)
    {
        $minggu_ke = explode('#', request()->get('minggu_ke'));
        $tanggal_awal = trim($minggu_ke[0]);
        $tanggal_akhir = trim($minggu_ke[1]);

        // Ambil hasil stick/hours per hari
        $stick_hours_data = $this->stick_hours($data, true);

        $hasil = [];
        $index = [];

        foreach ($stick_hours_data as $tanggal => $levels) {
            $total_harian = array_sum($levels);

            foreach ($levels as $level => $value) {
                $hasil[$tanggal][$level] = round($value, 3);
                $index[$tanggal][$level] = $total_harian > 0
                    ? round($value / $total_harian, 2)
                    : 0;
            }

            // Total = 1, hanya untuk pengecekan
            $index[$tanggal]['Total'] = 1;
        }

        $title = 'Index Kapasitas';

        $view = view('pelaporan.laporan.kapasitas_index_kapasitas', [
            'tanggal_awal'  => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
            'minggu_ke'     => $minggu_ke,
            'hasil'         => $hasil,
            'index'         => $index,
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

        return $pdf->stream('Index_Kapasitas.pdf');
    }
}
