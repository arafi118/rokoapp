@php
use App\Utils\Tanggal;
use Carbon\CarbonPeriod;

$period = iterator_to_array(CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir));
$bagian_list = ['Giling', 'Gunting', 'Packing', 'Banderol', 'OPP', 'MOP'];
$levels = ['E', 'D', 'C', 'B', 'A'];

// Inisialisasi total mingguan
$weekly_total = [];
foreach ($bagian_list as $b) {
    foreach ($levels as $l) {
        $weekly_total[$b][$l] = 0;
    }
    $weekly_total[$b]['TOTAL'] = 0;
}

$last_week = null;
@endphp
<title>{{ $title }}</title>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid #000;
        padding: 3px 5px;
        text-align: center;
    }

    thead th {
        background-color: #c7f4ff;
        font-weight: bold;
    }

    .title {
        text-align: left;
        font-weight: bold;
    }

    .header-info td {
        border: none;
        text-align: left;
        padding: 2px 0;
    }
</style>

<h2 style="text-align:center;">KOMPOSISI KARYAWAN</h2>

<table class="header-info" style="margin-bottom:8px;">
    <tr>
        <td width="10%">BULAN</td>
        <td>: {{ Tanggal::namaBulan(date('Y-' . $bulan . '-01')) }} {{ $tahun }}</td>
    </tr>
    <tr>
        <td>LOKASI</td>
        <td>: SKT MAGELANG - PT. ATI</td>
    </tr>
    <tr>
        <td>BRAND</td>
        <td>: ARS-16</td>
    </tr>
</table>

<table border="1" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th rowspan="2">WEEK</th>
            <th rowspan="2">TANGGAL</th>
            <th rowspan="2">HARI</th>
            @foreach ($bagian_list as $b)
                <th colspan="{{ count($levels) + 1 }}">{{ strtoupper($b) }}</th>
            @endforeach
            <th colspan="{{ count($levels) + 1 }}">TOTAL KARYAWAN</th>
            <th colspan="{{ count($levels) + 1 }}">KOMPOSISI (%)</th>
        </tr>
        <tr>
            @foreach (array_merge($bagian_list, ['TOTAL KARYAWAN', 'KOMPOSISI']) as $b)
                @foreach ($levels as $l)
                    <th>{{ $l }}</th>
                @endforeach
                <th>TOTAL</th>
            @endforeach
        </tr>
    </thead>

    <tbody>
        @foreach ($period as $i => $date)
            @php
                $week = $date->format('W');
                $tgl = $date->format('Y-m-d');
                $tgl_tampil = $date->format('d-M-y');
                $hari = Tanggal::namaHari($tgl);

                // Data harian
                $data_hari = $komposisi[$tgl] ?? [];

                // Siapkan baris harian
                $row = [];
                foreach ($bagian_list as $b) {
                    foreach ($levels as $l) {
                        $row[$b][$l] = 0;
                    }
                    $row[$b]['TOTAL'] = 0;
                }

                // Isi data harian
                foreach ($data_hari as $bagian => $level_data) {
                    foreach ($levels as $l) {
                        $row[$bagian][$l] = (int) ($level_data[$l] ?? 0);
                    }
                    $row[$bagian]['TOTAL'] = array_sum(array_intersect_key($level_data, array_flip($levels)));
                }

                // Total harian
                $total_karyawan = [];
                foreach ($levels as $l) {
                    $total_karyawan[$l] = array_sum(array_column($row, $l));
                }
                $total_karyawan['TOTAL'] = array_sum(array_values($total_karyawan));

                // Komposisi %
                $persen = [];
                foreach ($levels as $l) {
                    $persen[$l] = $total_karyawan['TOTAL'] > 0
                        ? round(($total_karyawan[$l] / $total_karyawan['TOTAL']) * 100, 2)
                        : 0;
                }
                $persen['TOTAL'] = $total_karyawan['TOTAL'] > 0 ? array_sum($persen) : 0;

                // Tambahkan ke total mingguan
                foreach ($bagian_list as $b) {
                    foreach ($levels as $l) {
                        $weekly_total[$b][$l] += $row[$b][$l];
                    }
                    $weekly_total[$b]['TOTAL'] += $row[$b]['TOTAL'];
                }
            @endphp

            {{-- Baris harian --}}
            <tr>
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>

                @foreach ($bagian_list as $b)
                    @foreach ($levels as $l)
                        <td>{{ $row[$b][$l] }}</td>
                    @endforeach
                    <td>{{ $row[$b]['TOTAL'] }}</td>
                @endforeach

                @foreach ($levels as $l)
                    <td>{{ $total_karyawan[$l] }}</td>
                @endforeach
                <td>{{ $total_karyawan['TOTAL'] }}</td>

                @foreach ($levels as $l)
                    <td>{{ $persen[$l] }}</td>
                @endforeach
                <td>{{ $persen['TOTAL'] }}</td>
            </tr>

            {{-- Jika minggu berganti atau akhir periode --}}
            @php
                $next_date = $period[$i + 1] ?? null;
                $next_week = $next_date ? $next_date->format('W') : null;
                $is_last = $i === count($period) - 1;

                if ($is_last || $next_week !== $week) {
                    $weekly_total_karyawan = [];
                    foreach ($levels as $l) {
                        $weekly_total_karyawan[$l] = array_sum(array_map(fn($b) => $weekly_total[$b][$l], $bagian_list));
                    }
                    $weekly_total_karyawan['TOTAL'] = array_sum($weekly_total_karyawan);

                    $weekly_persen = [];
                    foreach ($levels as $l) {
                        $weekly_persen[$l] = $weekly_total_karyawan['TOTAL'] > 0
                            ? round(($weekly_total_karyawan[$l] / $weekly_total_karyawan['TOTAL']) * 100, 2)
                            : 0;
                    }
                    $weekly_persen['TOTAL'] = $weekly_total_karyawan['TOTAL'] > 0 ? array_sum($weekly_persen) : 0;
            @endphp

            <tr class="total-mingguan" style="font-weight:bold;background:#ebeaea;">
                <td colspan="3">Total</td>
                @foreach ($bagian_list as $b)
                    @foreach ($levels as $l)
                        <td>{{ $weekly_total[$b][$l] }}</td>
                    @endforeach
                    <td>{{ $weekly_total[$b]['TOTAL'] }}</td>
                @endforeach

                @foreach ($levels as $l)
                    <td>{{ $weekly_total_karyawan[$l] }}</td>
                @endforeach
                <td>{{ $weekly_total_karyawan['TOTAL'] }}</td>

                @foreach ($levels as $l)
                    <td>{{ $weekly_persen[$l] }}</td>
                @endforeach
                <td>{{ $weekly_persen['TOTAL'] }}</td>
            </tr>

            @php
                // Reset total mingguan
                foreach ($bagian_list as $b) {
                    foreach ($levels as $l) {
                        $weekly_total[$b][$l] = 0;
                    }
                    $weekly_total[$b]['TOTAL'] = 0;
                }
                }
                $last_week = $week;
            @endphp
        @endforeach
    </tbody>
</table>
