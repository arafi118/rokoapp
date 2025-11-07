@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $proses = ['Giling', 'Gunting', 'Packing', 'Banderol', 'DPP', 'MOP'];
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

    .header-info td {
        border: none;
        text-align: left;
        padding: 2px 0;
    }
</style>

<h2 style="text-align:center; font-size:20px; font-weight:bold; margin-bottom:15px;">
    INDEX KAPASITAS
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
            <th colspan="6">INDEX KAPASITAS</th>
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

                $harian = $index[$tgl] ?? [];

                $row = [];
                foreach ($proses as $nama) {
                    $key = collect(array_keys($harian))->first(
                        fn($k) => str_contains(strtolower($k), strtolower($nama)),
                    );
                    $val = $key ? $harian[$key] ?? 0 : $harian[$nama] ?? 0;
                    $row[$nama] = $val;
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

            @php $last_week = $week; @endphp
        @endforeach
    </tbody>
</table>
