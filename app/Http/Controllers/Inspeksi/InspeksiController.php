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
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class InspeksiController extends Controller
{   
    public function index(Request $request)
    {
        $kategori = $request->get('kategori', 'mingguan'); 
        $periode  = $request->get('periode', 'periode_ini'); 
        $today    = Carbon::today();
        if ($kategori == 'mingguan') {

            if ($periode == 'periode_ini') {
                $start = $today->copy()->startOfWeek(Carbon::MONDAY);
                $end   = $today->copy()->endOfWeek(Carbon::SUNDAY);

            } else { 
                $lastMonthEnd = $today->copy()->subMonthNoOverflow()->endOfMonth();
                $start = $lastMonthEnd->copy()->startOfWeek(Carbon::MONDAY);
                $end   = $lastMonthEnd;
            }
        } else {
            if ($periode == 'periode_ini') {
                $start = $today->copy()->startOfMonth();
                $end   = $today->copy()->endOfMonth();
            } else {
                $lastMonth = $today->copy()->subMonthNoOverflow();
                $start     = $lastMonth->copy()->startOfMonth();
                $end       = $lastMonth->copy()->endOfMonth();
            }
        }

        $levels = [
            'guntinggiling' => 1,
            'pack'          => 3,
            'banderol'      => 4,
            'opp'           => 5,
            'mop'           => 6,
        ];

        $karyawan_ids = [];
        foreach ($levels as $key => $level) {
            $karyawan_ids[$key] = Karyawan::where('level', $level)->pluck('id')->toArray();
        }

        // Hitung total aktual & target
        $totals = [];
        foreach ($levels as $key => $level) {
            $aktual = Produksi::whereIn('karyawan_id', $karyawan_ids[$key])
                ->whereDate('tanggal', '>=', $start)
                ->whereDate('tanggal', '<=', $end)
                ->sum('jumlah_baik');
            $target = Absensi::whereIn('karyawan_id', $karyawan_ids[$key])
                ->whereDate('tanggal', '>=', $start)
                ->whereDate('tanggal', '<=', $end)
                ->sum('target_harian');

            $totals['aktual_'.$key] = $aktual;
            $totals['target_'.$key] = $target;
        }

        // Jika request AJAX untuk DataTables
        if ($request->ajax()) {

            $query = Produksi::query()
                ->join('karyawan', 'karyawan.id', '=', 'produksi.karyawan_id')
                ->selectRaw("
                    DATE(produksi.tanggal) as tanggal,
                    SUM(CASE WHEN karyawan.level = 1 THEN produksi.jumlah_baik ELSE 0 END) as guntinggiling,
                    SUM(CASE WHEN karyawan.level = 3 THEN produksi.jumlah_baik ELSE 0 END) as pack,
                    SUM(CASE WHEN karyawan.level = 4 THEN produksi.jumlah_baik ELSE 0 END) as banderol,
                    SUM(CASE WHEN karyawan.level = 5 THEN produksi.jumlah_baik ELSE 0 END) as opp,
                    SUM(CASE WHEN karyawan.level = 6 THEN produksi.jumlah_baik ELSE 0 END) as mop
                ")
                ->whereDate('produksi.tanggal', '>=', $start)
                ->whereDate('produksi.tanggal', '<=', $end)
                ->groupBy('tanggal')
                ->orderBy('tanggal', 'asc');

            return DataTables::of($query)
                ->filter(function ($instance) {
                    $search = request('search.value');
                    if (!empty($search)) {
                        $search = strtolower($search);
                        $instance->havingRaw('
                            LOWER(DATE_FORMAT(tanggal, "%Y-%m-%d")) LIKE ?
                            OR LOWER(DATE_FORMAT(tanggal, "%d-%m-%Y")) LIKE ?
                            OR LOWER(DATE_FORMAT(tanggal, "%d/%m/%Y")) LIKE ?
                            OR LOWER(DAYNAME(tanggal)) LIKE ?
                        ', [
                            "%{$search}%",
                            "%{$search}%",
                            "%{$search}%",
                            "%{$search}%",
                        ]);
                    }
                })
                ->editColumn('tanggal', fn($row) => Carbon::parse($row->tanggal)->format('Y-m-d'))
                ->with('totals', $totals)
                ->toJson();
        }
        return view('inspeksi.index', [
            'title'    => 'Dashboard Inspeksi',
            'start'    => $start,
            'end'      => $end,
            'kategori' => $kategori,
            'periode'  => $periode,
            'totals'   => $totals,
            'aktual_guntinggiling' => $totals['aktual_guntinggiling'] ?? 0,
            'target_guntinggiling' => $totals['target_guntinggiling'] ?? 0,
            'aktual_pack'          => $totals['aktual_pack'] ?? 0,
            'target_pack'          => $totals['target_pack'] ?? 0,
            'aktual_banderol'      => $totals['aktual_banderol'] ?? 0,
            'target_banderol'      => $totals['target_banderol'] ?? 0,
            'aktual_opp'           => $totals['aktual_opp'] ?? 0,
            'target_opp'           => $totals['target_opp'] ?? 0,
            'aktual_mop'           => $totals['aktual_mop'] ?? 0,
            'target_mop'           => $totals['target_mop'] ?? 0,
            'selisih_guntinggiling'=> $totals['selisih_guntinggiling'] ?? 0,
        ]);
    }

    public function chart(Request $request)
    {
        $kategori = $request->input('kategori', 'mingguan');
        $periode  = $request->input('periode', 'periode_ini');

        $today  = now()->startOfDay();
        $dates  = [];
        $labels = [];

        if ($kategori === 'mingguan') {
            if ($periode === 'periode_lalu') {
                $lastDayPrevMonth = $today->copy()->subMonthNoOverflow()->endOfMonth();
                $startOfWeek      = $lastDayPrevMonth->copy()->startOfWeek();
                $endOfWeek        = $lastDayPrevMonth->copy()->endOfWeek();
            } else {
                $startOfWeek = $today->copy()->startOfWeek();
                $endOfWeek   = $today->copy()->endOfWeek();
            }

            for ($date = $startOfWeek->copy(); $date->lte($endOfWeek); $date->addDay()) {
                $dates[]  = $date->format('Y-m-d');
                $labels[] = $date->isoFormat('ddd');
            }
        } else {
            $baseDate = $periode === 'periode_lalu'
                ? $today->copy()->subMonthNoOverflow()
                : $today->copy();

            $startOfMonth = $baseDate->copy()->startOfMonth();
            $endOfMonth   = $baseDate->copy()->endOfMonth();

            $weeks     = [];
            $weekStart = $startOfMonth->copy()->startOfWeek();

            while ($weekStart->lte($endOfMonth)) {
                $weekEnd    = $weekStart->copy()->endOfWeek();
                $rangeStart = $weekStart->lt($startOfMonth) ? $startOfMonth->copy() : $weekStart->copy();
                $rangeEnd   = $weekEnd->gt($endOfMonth) ? $endOfMonth->copy() : $weekEnd->copy();

                $weeks[] = [
                    'label' => $rangeStart->format('d') . '-' . $rangeEnd->format('d'),
                    'start' => $rangeStart->copy(),
                    'end'   => $rangeEnd->copy(),
                ];

                $weekStart->addWeek();
            }

            $weeks = array_slice($weeks, -4);

            foreach ($weeks as $week) {
                $labels[] = $week['label'];
                $dates[]  = [
                    $week['start']->format('Y-m-d'),
                    $week['end']->format('Y-m-d'),
                ];
            }
        }

        $levels = [
            'gtgl'     => 1,
            'pack'     => 3,
            'banderol' => 4,
            'opp'      => 5,
            'mop'      => 6,
        ];

        $datasets = [];

        foreach ($levels as $key => $level) {
            $data = [];

            if ($kategori === 'mingguan') {
                foreach ($dates as $date) {
                    $jumlah = Produksi::whereDate('tanggal', $date)
                        ->whereIn('karyawan_id', Karyawan::where('level', $level)->pluck('id'))
                        ->sum('jumlah_baik');

                    $data[] = $jumlah;
                }
            } else {
                foreach ($dates as $range) {
                    [$startDate, $endDate] = $range;

                    $jumlah = Produksi::whereBetween('tanggal', [$startDate, $endDate])
                        ->whereIn('karyawan_id', Karyawan::where('level', $level)->pluck('id'))
                        ->sum('jumlah_baik');

                    $data[] = $jumlah;
                }
            }

            $datasets[$key] = $data;
        }

        return response()->json([
            'labels'   => $labels,
            'datasets' => $datasets,
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
