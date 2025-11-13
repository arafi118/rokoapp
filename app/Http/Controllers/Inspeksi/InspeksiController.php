<?php

namespace App\Http\Controllers\Inspeksi;

use App\Http\Controllers\Controller;
use App\Models\Anggota;
use App\Models\Karyawan;
use App\Models\Absensi;
use App\Models\Produksi;
use App\Models\Group;
use App\Models\Level;
use Illuminate\Http\Request;

class InspeksiController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');

        $data['aktual_guntinggiling'] = Produksi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [1])->pluck('id'))
            ->sum('jumlah_baik');

        $data['target_guntinggiling'] = Absensi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [1])->pluck('id'))
            ->sum('target_harian');

        $data['aktual_pack'] = Produksi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [3])->pluck('id'))
            ->sum('jumlah_baik');

        $data['target_pack'] = Absensi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [3])->pluck('id'))
            ->sum('target_harian');

        $data['aktual_banderol'] = Produksi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [4])->pluck('id'))
            ->sum('jumlah_baik');

        $data['target_banderol'] = Absensi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [4])->pluck('id'))
            ->sum('target_harian');

        $data['aktual_opp'] = Produksi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [5])->pluck('id'))
            ->sum('jumlah_baik');

        $data['target_opp'] = Absensi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [5])->pluck('id'))
            ->sum('target_harian');

        $data['aktual_mop'] = Produksi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [6])->pluck('id'))
            ->sum('jumlah_baik');

        $data['target_mop'] = Absensi::whereDate('tanggal', $today)
            ->whereIn('karyawan_id', Karyawan::whereIn('level', [6])->pluck('id'))
            ->sum('target_harian');

        $data['title'] = "Dashboard";
        return view('inspeksi.index')->with($data);
    }
    public function chart(Request $request)
    {
        $periode = $request->input('periode', 'mingguan');

        $today = now();

        if ($periode === 'mingguan') {
            $startOfWeek = $today->copy()->startOfWeek();
            $endOfWeek = $today->copy()->endOfWeek();

            $dates  = [];
            $labels = [];
            for ($date = $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
                $labels[] = $date->format('D');
            }
        } else {
            $startOfMonth   = $today->copy()->startOfMonth();
            $endOfMonth     = $today->copy()->endOfMonth();

            $dates  = [];
            $labels = [];

            $weekStart = $startOfMonth->copy()->startOfWeek();
            while ($weekStart->lte($endOfMonth)) {
                $weekEnd    = $weekStart->copy()->endOfWeek();
                $labels[]   = 'Minggu ' . $weekStart->weekOfMonth;
                $dates[]    = [$weekStart->copy()->format('Y-m-d'), $weekEnd->copy()->format('Y-m-d')];
                $weekStart->addWeek();
            }
        }

        $levels = [
            'gtgl'      => 1,
            'pack'      => 3,
            'banderol'  => 4,
            'opp'       => 5,
            'mop'       => 6
        ];

        $datasets = [];
        foreach ($levels as $key => $level) {
            $data = [];
            if ($periode === 'mingguan') {
                foreach ($dates as $date) {
                    $jumlah = Produksi::whereDate('tanggal', $date)
                        ->whereIn('karyawan_id', Karyawan::where('level', $level)->pluck('id'))
                        ->sum('jumlah_baik');
                    $data[] = $jumlah;
                }
            } else {
                foreach ($dates as $range) {
                    $jumlah = Produksi::whereBetween('tanggal', $range)
                        ->whereIn('karyawan_id', Karyawan::where('level', $level)->pluck('id'))
                        ->sum('jumlah_baik');
                    $data[] = $jumlah;
                }
            }
            $datasets[$key] = $data;
        }

        return response()->json([
            'labels'    => $labels,
            'datasets'  => $datasets
        ]);
    }

    public function modalGLGT()
    {
        $today = now()->toDateString();

        $absensi    = Absensi::with('getkaryawan')->whereDate('tanggal', $today)->get();
        $produksi   = Produksi::whereDate('tanggal', $today)->get();
        $karyawan   = Karyawan::select('id', 'level')->get();

        $data = $absensi->map(function ($absen) use ($produksi) {
            $produksiKaryawan   = $produksi->where('karyawan_id', $absen->karyawan_id);
            $jamMasuk           = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
            $jamKeluar          = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar) : null;

            if (!$jamMasuk || !$jamKeluar) {
                $totalJam = 1;
            } else {
                if ($jamKeluar->lessThan($jamMasuk)) $jamKeluar->addDay();
                $totalJam = $jamKeluar->diffInMinutes($jamMasuk) / 60;
                if ($totalJam <= 0) $totalJam = 1;
            }

            $totalBaik      = $produksiKaryawan->sum('jumlah_baik');
            $totalRusak     = $produksiKaryawan->sum('jumlah_rusak') ?? 0;
            $produktivitas  = $totalBaik / $totalJam;

            return [
                'karyawan_id'   => $absen->karyawan_id,
                'level'         => optional($absen->getkaryawan)->level,
                'jam_kerja'     => round($totalJam, 2),
                'jumlah_baik'   => $totalBaik,
                'jumlah_rusak'  => $totalRusak,
                'produktivitas' => round($produktivitas, 2),
            ];
        });

        $rataGL = round($data->where('level', 1)->avg('produktivitas') ?? 0, 2);
        $rataGT = round($data->where('level', 1)->avg('produktivitas') ?? 0, 2);

        $dataGLGT = $data->whereIn('level', 1);
        $totalKeseluruhan = $dataGLGT->isEmpty()
            ? 0
            : $dataGLGT->groupBy('level')->map->sum('jumlah_baik')->values()->sum();

        $karyawanHadirIds = $absensi->pluck('karyawan_id')->toArray();
        $totalGL  = $karyawan->where('level', 1)->count();
        $totalGT  = $karyawan->where('level', 2)->count();
        $hadirGL  = $karyawan->where('level', 1)->whereIn('id', $karyawanHadirIds)->count();
        $hadirGT  = $karyawan->where('level', 2)->whereIn('id', $karyawanHadirIds)->count();
        $tidakHadirGL = $totalGL - $hadirGL;
        $tidakHadirGT = $totalGT - $hadirGT;

        return response()->json([
            'tanggal_GTGL'          => $today,
            'rata_GL'               => number_format($rataGL, 0, ',', '.'),
            'rata_GT'               => number_format($rataGT, 0, ',', '.'),
            'totalKeseluruhan_GTGL' => $totalKeseluruhan,
            'rekap' => [
                'GL' => [
                    'hadir_GL'      => $hadirGL,
                    'tidak_hadirGL' => $tidakHadirGL,
                    'total_GL'      => $totalGL,
                ],
                'GT' => [
                    'hadir_GT'      => $hadirGT,
                    'tidak_hadirGT' => $tidakHadirGT,
                    'total_GT'      => $totalGT,
                ],
            ],
        ]);
    }


    public function modalPACK()
    {
        $today = now()->toDateString();

        $absensi = Absensi::with('getkaryawan:id,level')
            ->whereDate('tanggal', $today)
            ->get();

        $produksi = Produksi::whereDate('tanggal', $today)->get()
            ->groupBy('karyawan_id');

        $karyawan = Karyawan::select('id', 'level')->get();

        $data = $absensi->map(function ($absen) use ($produksi) {
            $jamMasuk  = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
            $jamKeluar = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar) : null;

            $totalJam = $jamMasuk && $jamKeluar
                ? max($jamKeluar->lessThan($jamMasuk) ? $jamKeluar->addDay()->diffInMinutes($jamMasuk) / 60 : $jamKeluar->diffInMinutes($jamMasuk) / 60, 1)
                : 1;

            $produksiKaryawan = $produksi[$absen->karyawan_id] ?? collect();
            $totalBaik = $produksiKaryawan->sum('jumlah_baik');

            return [
                'level'         => $absen->getkaryawan->level ?? null,
                'jam_kerja'     => round($totalJam, 2),
                'jumlah_baik'   => $totalBaik,
                'produktivitas' => round($totalBaik / $totalJam, 2),
            ];
        });

        $packData = $data->where('level', 3);

        $totalBtgs   = $packData->sum('jumlah_baik');
        $totalPACK   = round($totalBtgs / 16, 2);
        $rataBtgs    = round($packData->avg('produktivitas') ?? 0, 2);
        $rataPACK    = round($rataBtgs / 16, 2);

        $karyawanHadir = $absensi->pluck('karyawan_id')->toArray();
        $totalpk = $karyawan->where('level', 3)->count();
        $hadirpk = $karyawan->where('level', 3)->whereIn('id', $karyawanHadir)->count();
        $tidakHadirpk = $totalpk - $hadirpk;

        return response()->json([
            'tanggal_PK' => $today,
            'PACK' => [
                'ARS' => [
                    'total_btg_PK'  => number_format($totalBtgs, 0, ',', '.'),
                    'total_pack_PK' => number_format($totalPACK, 0, ',', '.'),
                ],
                'rata_prod' => [
                    'btg_PK'  => number_format($rataBtgs, 0, ',', '.'),
                    'pack_PK' => number_format($rataPACK, 0, ',', '.'),
                ],
                'hadir_PK'       => $hadirpk,
                'tidak_hadir_PK' => $tidakHadirpk,
                'total_PK'       => $totalpk,
            ],
        ]);
    }


    public function modalBANDEROL()
    {
        $today = now()->toDateString();

        $absensi = Absensi::with('getkaryawan:id,level')
            ->whereDate('tanggal', $today)
            ->get();

        $produksi = Produksi::whereDate('tanggal', $today)
            ->get()
            ->groupBy('karyawan_id');

        $karyawan = Karyawan::select('id', 'level')->get();

        $data = $absensi->map(function ($absen) use ($produksi) {
            $jamMasuk  = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
            $jamKeluar = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar) : null;

            $totalJam = $jamMasuk && $jamKeluar
                ? max($jamKeluar->lessThan($jamMasuk)
                    ? $jamKeluar->addDay()->diffInMinutes($jamMasuk) / 60
                    : $jamKeluar->diffInMinutes($jamMasuk) / 60, 1)
                : 1;

            $produksiKaryawan = $produksi[$absen->karyawan_id] ?? collect();
            $totalBaik = $produksiKaryawan->sum('jumlah_baik');

            return [
                'level'         => $absen->getkaryawan->level ?? null,
                'jumlah_baik'   => $totalBaik,
                'produktivitas' => round($totalBaik / $totalJam, 2),
            ];
        });

        $banderolData = $data->where('level', 4);

        $totalBtgs   = $banderolData->sum('jumlah_baik');
        $totalPack   = round($totalBtgs / 16, 2);
        $rataBtgs    = round($banderolData->avg('produktivitas') ?? 0, 2);
        $rataPack    = round($rataBtgs / 16, 2);

        $karyawanHadir = $absensi->pluck('karyawan_id')->toArray();
        $totalBDL = $karyawan->where('level', 4)->count();
        $hadirBDL = $karyawan->where('level', 4)->whereIn('id', $karyawanHadir)->count();
        $tidakHadirBDL = $totalBDL - $hadirBDL;

        return response()->json([
            'tanggal_BDL' => $today,
            'BANDEROL' => [
                'ARS' => [
                    'total_btg_BDL'  => number_format($totalBtgs, 0, ',', '.'),
                    'total_pack_BDL' => number_format($totalPack, 0, ',', '.'),
                ],
                'rata_prod' => [
                    'btg_BDL'  => number_format($rataBtgs, 0, ',', '.'),
                    'pack_BDL' => number_format($rataPack, 0, ',', '.'),
                ],
                'hadir_BDL'       => $hadirBDL,
                'tidak_hadir_BDL' => $tidakHadirBDL,
                'total_BDL'       => $totalBDL,
            ],
        ]);
    }

    public function modalOPP()
    {
        $today = now()->toDateString();

        $absensi  = Absensi::with('getkaryawan')->whereDate('tanggal', $today)->get();
        $produksi = Produksi::whereDate('tanggal', $today)->get();
        $karyawan = Karyawan::select('id', 'level')->get();

        $data = $absensi->map(function ($absen) use ($produksi) {
            $produksiKaryawan = $produksi->where('karyawan_id', $absen->karyawan_id);

            $jamMasuk  = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
            $jamKeluar = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar) : null;

            if (!$jamMasuk || !$jamKeluar) {
                $totalJam = 1;
            } else {
                if ($jamKeluar->lessThan($jamMasuk)) $jamKeluar->addDay();
                $totalJam = max($jamKeluar->diffInMinutes($jamMasuk) / 60, 1);
            }

            $totalBaik = $produksiKaryawan->sum('jumlah_baik');
            $produktivitas = $totalBaik / $totalJam;

            return [
                'karyawan_id'   => $absen->karyawan_id,
                'level'         => optional($absen->getkaryawan)->level,
                'jam_kerja'     => round($totalJam, 2),
                'jumlah_baik'   => $totalBaik,
                'produktivitas' => round($produktivitas, 2),
            ];
        });

        $dataOPP = $data->where('level', 5);

        $totalBtg_OPP = $dataOPP->sum('jumlah_baik');
        $totalPack_OPP = round($totalBtg_OPP / 16, 2);

        $rataBtg_OPP = round($dataOPP->avg('produktivitas') ?? 0, 2);
        $rataPack_OPP = round($rataBtg_OPP / 16, 2);

        $karyawan_OPP = $absensi->pluck('karyawan_id')->toArray();
        $total_OPP = $karyawan->where('level', 5)->count();
        $hadir_OPP = $karyawan->where('level', 5)->whereIn('id', $karyawan_OPP)->count();
        $tidakHadir_OPP = $total_OPP - $hadir_OPP;

        return response()->json([
            'tanggal_OPP' => $today,
            'OPP' => [
                'ARS' => [
                    'total_btg_OPP'  => number_format($totalBtg_OPP, 0, ',', '.'),
                    'total_pack_OPP' => number_format($totalPack_OPP, 0, ',', '.'),
                ],
                'rata_prod' => [
                    'btg_OPP'  => number_format($rataBtg_OPP, 0, ',', '.'),
                    'pack_OPP' => number_format($rataPack_OPP, 0, ',', '.'),
                ],
                'hadir_OPP'       => $hadir_OPP,
                'tidak_hadir_OPP' => $tidakHadir_OPP,
                'total_OPP'       => $total_OPP,
            ],
        ]);
    }

    public function modalMOP()
    {
        $today = now()->toDateString();

        $absensi  = Absensi::with('getkaryawan')->whereDate('tanggal', $today)->get();
        $produksi = Produksi::whereDate('tanggal', $today)->get();
        $karyawan = Karyawan::select('id', 'level')->get();

        $data = $absensi->map(function ($absen) use ($produksi) {
            $produksiKaryawan = $produksi->where('karyawan_id', $absen->karyawan_id);

            $jamMasuk  = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
            $jamKeluar = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar) : null;

            if (!$jamMasuk || !$jamKeluar) {
                $totalJam = 1;
            } else {
                if ($jamKeluar->lessThan($jamMasuk)) $jamKeluar->addDay();
                $totalJam = max($jamKeluar->diffInMinutes($jamMasuk) / 60, 1);
            }

            $totalBaik = $produksiKaryawan->sum('jumlah_baik');
            $produktivitas = $totalBaik / $totalJam;

            return [
                'karyawan_id'   => $absen->karyawan_id,
                'level'         => optional($absen->getkaryawan)->level,
                'jam_kerja'     => round($totalJam, 2),
                'jumlah_baik'   => $totalBaik,
                'produktivitas' => round($produktivitas, 2),
            ];
        });

        $dataMOP = $data->where('level', 6);

        $totalBtg_MOP = $dataMOP->sum('jumlah_baik');
        $totalPack_MOP = round($totalBtg_MOP / 16, 2);

        $rataBtg_MOP = round($dataMOP->avg('produktivitas') ?? 0, 2);
        $rataPack_MOP = round($rataBtg_MOP / 16, 2);

        $karyawan_MOP = $absensi->pluck('karyawan_id')->toArray();
        $total_MOP = $karyawan->where('level', 6)->count();
        $hadir_MOP = $karyawan->where('level', 6)->whereIn('id', $karyawan_MOP)->count();
        $tidakHadir_MOP = $total_MOP - $hadir_MOP;

        return response()->json([
            'tanggal_MOP' => $today,
            'MOP' => [
                'ARS' => [
                    'total_btg_MOP'  => number_format($totalBtg_MOP, 0, ',', '.'),
                    'total_pack_MOP' => number_format($totalPack_MOP, 0, ',', '.'),
                ],
                'rata_prod' => [
                    'btg_MOP'  => number_format($rataBtg_MOP, 0, ',', '.'),
                    'pack_MOP' => number_format($rataPack_MOP, 0, ',', '.'),
                ],
                'hadir_MOP'       => $hadir_MOP,
                'tidak_hadir_MOP' => $tidakHadir_MOP,
                'total_MOP'       => $total_MOP,
            ],
        ]);
    }
}
