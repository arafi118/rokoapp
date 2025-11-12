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

            $dates = [];
            $labels = [];
            for ($date = $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
                $dates[] = $date->format('Y-m-d');
                $labels[] = $date->format('D');
            }
        } else {
            $startOfMonth = $today->copy()->startOfMonth();
            $endOfMonth = $today->copy()->endOfMonth();

            $dates = [];
            $labels = [];

            $weekStart = $startOfMonth->copy()->startOfWeek();
            while ($weekStart->lte($endOfMonth)) {
                $weekEnd = $weekStart->copy()->endOfWeek();
                $labels[] = 'Minggu ' . $weekStart->weekOfMonth;
                $dates[] = [$weekStart->copy()->format('Y-m-d'), $weekEnd->copy()->format('Y-m-d')];
                $weekStart->addWeek();
            }
        }

        $levels = [
            'gtgl' => 1,
            'pack' => 3,
            'banderol' => 4,
            'opp' => 5,
            'mop' => 6
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
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }

    public function modalGLGT()
    {
        $today = now()->toDateString();

        $absensi = Absensi::with('getkaryawan')->whereDate('tanggal', $today)->get();
        $produksi = Produksi::whereDate('tanggal', $today)->get();
        $karyawan = Karyawan::select('id', 'level')->get();

        $data = $absensi->map(function ($absen) use ($produksi) {
            $produksiKaryawan = $produksi->where('karyawan_id', $absen->karyawan_id);
            $jamMasuk = $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk) : null;
            $jamKeluar = $absen->jam_keluar ? \Carbon\Carbon::parse($absen->jam_keluar) : null;

            if (!$jamMasuk || !$jamKeluar) {
                $totalJam = 1;
            } else {
                if ($jamKeluar->lessThan($jamMasuk)) $jamKeluar->addDay();
                $totalJam = $jamKeluar->diffInMinutes($jamMasuk) / 60;
                if ($totalJam <= 0) $totalJam = 1;
            }

            $totalBaik = $produksiKaryawan->sum('jumlah_baik');
            $totalRusak = $produksiKaryawan->sum('jumlah_rusak') ?? 0;
            $produktivitas = $totalBaik / $totalJam;

            return [
                'karyawan_id' => $absen->karyawan_id,
                'level' => optional($absen->getkaryawan)->level,
                'jam_kerja' => round($totalJam, 2),
                'jumlah_baik' => $totalBaik,
                'jumlah_rusak' => $totalRusak,
                'produktivitas' => round($produktivitas, 2),
            ];
        });

        $rataGL = round($data->where('level', 1)->avg('produktivitas') ?? 0, 2);
        $rataGT = round($data->where('level', 2)->avg('produktivitas') ?? 0, 2);
        $totalKeseluruhan = $data->sum('jumlah_baik');

        $karyawanHadirIds = $absensi->pluck('karyawan_id')->toArray();
        $totalGL = $karyawan->where('level', 1)->count();
        $totalGT = $karyawan->where('level', 2)->count();
        $hadirGL = $karyawan->where('level', 1)->whereIn('id', $karyawanHadirIds)->count();
        $hadirGT = $karyawan->where('level', 2)->whereIn('id', $karyawanHadirIds)->count();
        $tidakHadirGL = $totalGL - $hadirGL;
        $tidakHadirGT = $totalGT - $hadirGT;

        return response()->json([
            'tanggal' => $today,
            'rataGL' => $rataGL,
            'rataGT' => $rataGT,
            'totalKeseluruhan' => $totalKeseluruhan,
            'rekap' => [
                'GL' => ['hadir' => $hadirGL, 'tidak_hadir' => $tidakHadirGL, 'total' => $totalGL],
                'GT' => ['hadir' => $hadirGT, 'tidak_hadir' => $tidakHadirGT, 'total' => $totalGT],
            ],
        ]);
    }
}
