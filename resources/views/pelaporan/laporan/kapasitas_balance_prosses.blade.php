@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    // Buat rentang tanggal harian
    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);

    // Inisialisasi total mingguan
    $proses = ['Giling', 'Gunting', 'Packing', 'Banderol', 'DPP', 'MOP'];
    $weekly_total = array_fill_keys($proses, 0);
    $last_week = null;

    // Pemetaan level_id ke nama proses (disesuaikan dengan struktur di DB)
    $level_map = [
        1 => 'Giling',
        2 => 'Gunting',
        3 => 'Packing',
        4 => 'Banderol',
        5 => 'DPP',
        6 => 'MOP',
    ];
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

    th,
    td {
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

<h2 style="text-align:center; font-size:20px; font-weight:bold; margin-bottom:15px;">
    BALANCE PROSES
</h2>

<table class="header-info" style="margin-bottom:10px;">
    <tr>
        <td width="15%">BULAN</td>
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

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
        <tr>
            <th rowspan="2" width="5%">WEEK</th>
            <th rowspan="2" width="12%">TANGGAL</th>
            <th rowspan="2" width="10%">HARI</th>
            <th colspan="6">BALANCE PROSES</th>
        </tr>
        <tr>
            @foreach ($proses as $nama)
                <th>{{ $nama }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($period as $date)
            @php
                $week = $date->format('W');
                $tgl = $date->format('Y-m-d');
                $tgl_tampil = $date->format('d-M-y');
                $hari = Tanggal::namaHari($tgl);

                // Ambil data persentase harian dari controller
                $harian = $persentase[$tgl] ?? [];

                // Siapkan baris data untuk tiap proses
                $row = [];
                foreach ($proses as $nama) {
                    // Samakan format key seperti di data (misalnya 'Operator Giling')
                    $key = 'Operator ' . $nama;
                    $row[$nama] = $harian[$key] ?? 0;
                    $weekly_total[$nama] += $row[$nama];
                }

            @endphp

            <tr class="{{ $hari == 'Minggu' ? 'minggu' : '' }}">
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>
                @foreach ($row as $val)
                    <td>{{ number_format($val, 2) }}</td>
                @endforeach
            </tr>

            {{-- total tiap akhir minggu --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background-color:#f0f0f0;">
                    <td colspan="3">Total</td>
                    @foreach ($weekly_total as $val)
                        <td>{{ number_format($val, 2) }}</td>
                    @endforeach
                </tr>
                @php
                    $weekly_total = array_fill_keys($proses, 0);
                @endphp
            @endif

            @php $last_week = $week; @endphp
        @endforeach

        {{-- total minggu terakhir jika tidak berakhir di Minggu --}}
        @if (array_sum($weekly_total) > 0)
            <tr style="font-weight:bold; background-color:#f0f0f0;">
                <td colspan="3">Total</td>
                @foreach ($weekly_total as $val)
                    <td>{{ number_format($val, 2) }}</td>
                @endforeach
            </tr>
        @endif
    </tbody>
</table>
