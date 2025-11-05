<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Imports\ImportAbsensi;
use App\Models\Absensi;
use App\Models\Anggota;
use App\Models\Group;
use App\Models\Jadwal;
use App\Models\Karyawan;
use App\Models\Produksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AbsensiController extends Controller
{
    public function index()
    {
        $title = 'Absensi Karyawan';
        return view('inspeksi.absensi.index')->with(compact('title'));
    }

    public function store(Request $request)
    {
        $data = $request->only([
            "nik",
            "waktu",
            "absensi",
        ]);

        $tanggal = date('Y-m-d');
        $anggota = Anggota::where('nik', $data['nik'])->first();
        if (!$anggota) {
            throw new \Exception("Anggota tidak ditemukan");
        }

        $karyawan = Karyawan::where('anggota_id', $anggota->id)->first();
        if (!$karyawan) {
            throw new \Exception("Karyawan tidak ditemukan");
        }

        $hari = $this->namaHari($tanggal);
        $jadwal = Jadwal::where('hari', $hari)->first();

        $presensi = Absensi::where('karyawan_id', $karyawan->id)->where('tanggal', $tanggal)->first();
        if ($data['absensi'] == 'masuk') {
            $presensi = Absensi::create([
                'karyawan_id' => $karyawan->id,
                'group_id' => $karyawan->group_id,
                'jadwal' => $jadwal->id,
                'tanggal' => $tanggal,
                'jam_masuk' => $data['waktu'],
                'status' => (strtotime($data['waktu']) > strtotime($jadwal->jam_masuk)) ? "T" : "H",
            ]);
        } else {
            $presensi = Absensi::where('karyawan_id', $karyawan->id)->where('tanggal', $tanggal)->first();
            if (!$presensi) {
                throw new \Exception("Belum absens masuk");
            }

            $presensi->jam_keluar = $data['waktu'];
            $presensi->save();
        }

        return response()->json([
            'success'  => true,
            'msg' => $anggota->nama . " berhasil absen " . $data['absensi'] . " pada " . $data['waktu'],
        ]);
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $path = $file->store('import');

        $level = [
            'giling' => 1,
            'gunting' => 2,
            'packing' => 3,
            'bandrol' => 4,
            'opp' => 5,
            'mop' => 6,
        ];

        $dataTanggal = [];
        $dataKodeKaryawan = [];
        $dataAbsensi = [];
        $jenisAbsen = '';

        $sheets = Excel::toArray(new ImportAbsensi(), $path);
        foreach ($sheets as $index => $sheet) {
            $jumlahData = count($sheet);
            if ($index == 0) {
                $jenisAbsen = strtolower(explode(' ', $sheet[1][1])[1]);
            }
            for ($i = 5; $i < $jumlahData; $i++) {
                $data = $sheet[$i];
                if ($i == 5) {
                    $dataTanggal = [
                        date('Y-m-d', ($data[4] - 25569) * 86400),
                        date('Y-m-d', ($data[7] - 25569) * 86400),
                        date('Y-m-d', ($data[10] - 25569) * 86400),
                        date('Y-m-d', ($data[13] - 25569) * 86400),
                        date('Y-m-d', ($data[16] - 25569) * 86400),
                        date('Y-m-d', ($data[19] - 25569) * 86400),
                    ];
                } else {
                    if (is_numeric($data[0])) {
                        $dataKodeKaryawan[] = $data[1];
                        $dataAbsensi[] = [
                            'kode' => $data[1],
                            'nama' => $data[2],
                            'kelompok' => $index + 1,
                            'absensi' => [
                                $dataTanggal[0] => [
                                    'status' => $data[4],
                                    'plan' => $data[5],
                                    'jk' => $data[6],
                                ],
                                $dataTanggal[1] => [
                                    'status' => $data[7],
                                    'plan' => $data[8],
                                    'jk' => $data[9],
                                ],
                                $dataTanggal[2] => [
                                    'status' => $data[10],
                                    'plan' => $data[11],
                                    'jk' => $data[12],
                                ],
                                $dataTanggal[3] => [
                                    'status' => $data[13],
                                    'plan' => $data[14],
                                    'jk' => $data[15],
                                ],
                                $dataTanggal[4] => [
                                    'status' => $data[16],
                                    'plan' => $data[17],
                                    'jk' => $data[18],
                                ],
                                $dataTanggal[5] => [
                                    'status' => $data[19],
                                    'plan' => $data[20],
                                    'jk' => $data[21],
                                ],
                            ]
                        ];
                    }
                }
            }
        }

        $dataIdKaryawan = Karyawan::whereIn('kode_karyawan', $dataKodeKaryawan)->get()->pluck('id', 'kode_karyawan')->toArray();

        $insertAbsensi = [];
        foreach ($dataAbsensi as $index => $absensi) {
            $idKaryawan = $dataIdKaryawan[$absensi['kode']] ?? null;
            if (!$idKaryawan) {
                $anggota = Anggota::create([
                    'nama' => $absensi['nama']
                ]);

                $kodeKaryawan = $absensi['kode'];
                $tahun = substr($kodeKaryawan, 3, 2);
                $bulan = substr($kodeKaryawan, 5, 2);

                $tanggalMasuk = $tahun . '-' . $bulan . '-01';
                $tanggalMasuk = date('Y-m-d', strtotime($tanggalMasuk));

                $karyawan = Karyawan::create([
                    'group_id' => $absensi['kelompok'],
                    'meja_id' => '0',
                    'anggota_id' => $anggota->id,
                    'kode_karyawan' => $absensi['kode'],
                    'tanggal_masuk' => $tanggalMasuk,
                    'tanggal_keluar' => null,
                    'status' => 'aktif',
                    'level' => $level[$jenisAbsen],
                ]);

                $idKaryawan = $karyawan->id;
            }

            $nomor = 1;
            $allowInsert = true;
            foreach ($absensi['absensi'] as $tanggal => $data) {
                if (trim($data['status']) == 'K') {
                    $allowInsert = false;
                    Karyawan::where('id', $idKaryawan)->update([
                        'status' => 'nonaktif',
                        'tanggal_keluar' => $tanggal,
                    ]);

                    continue;
                }

                $jamMasuk = '07:00:00';
                $jamKeluar = '15:00:00';
                if (date('D', strtotime($tanggal)) == 'Sat') {
                    $jamKeluar = '12:30:00';
                }

                if ($allowInsert) {
                    $insertAbsensi[] = [
                        'karyawan_id' => $idKaryawan,
                        'group_id' => $absensi['kelompok'],
                        'meja_id' => '0',
                        'jadwal' => $nomor,
                        'tanggal' => $tanggal,
                        'jam_masuk' => $jamMasuk,
                        'jam_keluar' => $jamKeluar,
                        'status' => trim($data['status']),
                        'status_absen' => 'close',
                        'target_harian' => $data['plan'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $nomor++;
            }
        }

        $tanggalAbsen = array_keys($dataTanggal);
        Absensi::whereIn('tanggal', $tanggalAbsen)->delete();

        $chunkSize = 100;
        foreach (array_chunk($insertAbsensi, $chunkSize) as $chunk) {
            Absensi::insert($chunk);
        }

        echo "<script>window.close();</script>";
        exit;
    }

    public function laporan()
    {
        $groups = Group::all();

        $title = 'Laporan Kehadiran';
        return view('inspeksi.absensi.laporan')->with(compact('title', 'groups'));
    }

    public function absenHarian()
    {
        if (request()->ajax()) {
            $data = [
                "tanggal" => request()->get('tanggal'),
                "kelompok" => request()->get('kelompok'),
            ];

            $tanggal = $data['tanggal'];

            $karyawan = Karyawan::select(
                'karyawan.*',
                'absensi.status as absen',
                'absensi.tanggal as tgl_absen',
                'absensi.group_id as kelompok',
                'absensi.target_harian as plan'
            )->with([
                'getlevel',
                'getanggota',
                'getproduksi' => function ($query) use ($tanggal) {
                    $query->where('tanggal', $tanggal);
                }
            ])->leftJoin('absensi', 'karyawan.id', '=', 'absensi.karyawan_id')
                ->where('absensi.tanggal', $tanggal);

            if ($data['kelompok'] != '') {
                $karyawan = $karyawan->where('absensi.group_id', $data['kelompok']);
            }

            $karyawan = $karyawan->get();

            return datatables()->of($karyawan)
                ->addIndexColumn()
                ->rawColumns(['aksi'])
                ->make(true);
        }
    }

    public function update(Request $request)
    {
        $data = $request->only([
            'id_karyawan',
            'tanggal',
            'absen'
        ]);


        $absensi = Absensi::where('karyawan_id', $data['id_karyawan'])
            ->where('tanggal', $data['tanggal'])->first();

        if ($absensi) {
            Absensi::where('id', $absensi->id)->update([
                'status' => $data['absen']
            ]);
        } else {
            Absensi::create([
                'karyawan_id' => $data['id_karyawan'],
                'group_id' => Karyawan::where('id', $data['id_karyawan'])->first()->group_id,
                'tanggal' => $data['tanggal'],
                'status' => $data['absen'],
            ]);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Berhasil update absensi'
        ]);
    }

    public function inputPlan(Request $request)
    {
        $data = $request->only([
            'id',
            'tanggal',
            'plan'
        ]);

        $absensi = Absensi::where('karyawan_id', $data['id'])
            ->where('tanggal', $data['tanggal'])->first();

        if ($absensi) {
            Absensi::where('id', $absensi->id)->update([
                'target_harian' => $data['plan']
            ]);
        } else {
            Absensi::create([
                'karyawan_id' => $data['id'],
                'group_id' => Karyawan::where('id', $data['id'])->first()->group_id,
                'tanggal' => $data['tanggal'],
                'target_harian' => $data['plan'],
            ]);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Target harian berhasil diinput'
        ]);
    }

    public function inputActual(Request $request)
    {
        $data = $request->only([
            'id',
            'tanggal',
            'actual'
        ]);

        $produksi = Produksi::where('karyawan_id', $data['id'])
            ->where('tanggal', $data['tanggal'])->first();

        if ($produksi) {
            Produksi::where('id', $produksi->id)->update([
                'jumlah_baik' => $data['actual']
            ]);
        } else {
            Produksi::create([
                'karyawan_id' => $data['id'],
                'tanggal' => $data['tanggal'],
                'jumlah_baik' => $data['actual'],
                'jumlah_buruk' => 0,
                'jumlah_buruk2' => 0,
                'status_validasi' => 'DRAFT',
            ]);
        }

        return response()->json([
            'success' => true,
            'msg' => 'Actual berhasil diinput'
        ]);
    }

    public function cetak(Request $request)
    {
        $data = $request->only([
            "minggu_ke",
            "kelompok",
        ]);

        $minggu_ke = explode('#', $data['minggu_ke']);
        $tanggal_awal = $minggu_ke[0];
        $tanggal_akhir = $minggu_ke[1];

        $kelompok = Group::where('id', $data['kelompok'])->first();
        $absensi = Absensi::whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir])->where('group_id', $data['kelompok'])->with([
            'getkaryawan.getanggota',
            'getkaryawan.getproduksi' => function ($query) use ($tanggal_awal, $tanggal_akhir) {
                $query->whereBetween('tanggal', [$tanggal_awal, $tanggal_akhir]);
            },
        ])->get();

        $absenMingguan = [];
        foreach ($absensi as $a) {
            if (!isset($absenMingguan[$a->getkaryawan->id])) {
                $absenMingguan[$a->getkaryawan->id] = [
                    'kode_karyawan' => $a->getkaryawan->kode_karyawan,
                    "nama" => $a->getkaryawan->getanggota->nama,
                    "absensi" => [
                        $a->tanggal => [
                            'tanggal' => $a->tanggal,
                            'status' => $a->status,
                            'produksi' => $a->getkaryawan->getproduksi,
                            'jam_masuk' => $a->jam_masuk,
                            'jam_keluar' => $a->jam_keluar
                        ]
                    ]
                ];
            } else {
                $absenMingguan[$a->getkaryawan->id]['absensi'][$a->tanggal] = [
                    'tanggal' => $a->tanggal,
                    'status' => $a->status,
                    'produksi' => $a->getkaryawan->getproduksi,
                    'jam_masuk' => $a->jam_masuk,
                    'jam_keluar' => $a->jam_keluar
                ];
            }
        }

        return Pdf::loadView('inspeksi.absensi.cetak', compact('kelompok', 'absenMingguan', 'tanggal_awal', 'tanggal_akhir'))->setPaper('a4', 'landscape')->stream();
    }

    private function namaHari($tanggal)
    {
        $hari = date('D', strtotime($tanggal));

        switch ($hari) {
            case 'Sun':
                $nama = "Minggu";
                break;
            case 'Mon':
                $nama = "Senin";
                break;
            case 'Tue':
                $nama = "Selasa";
                break;
            case 'Wed':
                $nama = "Rabu";
                break;
            case 'Thu':
                $nama = "Kamis";
                break;
            case 'Fri':
                $nama = "Jumat";
                break;
            case 'Sat':
                $nama = "Sabtu";
                break;
            default:
                $nama = "Tidak diketahui";
                break;
        }

        return $nama;
    }

    private function namaBulan($tanggal)
    {
        $bulanList = [
            1 => '01. JANUARI',
            2 => '02. FEBRUARI',
            3 => '03. MARET',
            4 => '04. APRIL',
            5 => '05. MEI',
            6 => '06. JUNI',
            7 => '07. JULI',
            8 => '08. AGUSTUS',
            9 => '09. SEPTEMBER',
            10 => '10. OKTOBER',
            11 => '11. NOVEMBER',
            12 => '12. DESEMBER',
        ];

        $bulan = date('n', strtotime($tanggal));
        return $bulanList[$bulan] ?? 'Tidak diketahui';
    }
}
