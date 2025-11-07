@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);

    // Level yang akan ditampilkan (urutan kolom)
    $levels = [
        'Operator Giling' => 'Giling',
        'Operator Gunting' => 'Gunting',
        'Operator Pack' => 'Packing',
        'Operator Banderol' => 'Banderol',
        'Operator OPP' => 'DPP',
        'Operator MOP' => 'MOP',
    ];

    $weekly_total = array_fill_keys(array_keys($levels), 0);
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
    STICK/HOURS
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
            <th colspan="{{ count($levels) }}">STICK / HOURS (Jumlah Baik)</th>
        </tr>
        <tr>
            @foreach ($levels as $alias)
                <th>{{ $alias }}</th>
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

                $harian = $produksi_stick[$tgl] ?? [];
                $row = [];

                foreach ($levels as $level_nama => $alias) {
                    $row[$alias] = $harian[$level_nama]['per_jam'] ?? 0;
                    $weekly_total[$level_nama] += $row[$alias];
                }
            @endphp

            <tr class="{{ $hari == 'Minggu' ? 'minggu' : '' }}">
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>
                @foreach ($row as $val)
                    <td align="right">{{ number_format($val, 2) }}</td>
                @endforeach
            </tr>

            {{-- Total tiap akhir minggu --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background-color:#f0f0f0;">
                    <td colspan="3">Total</td>
                    @foreach ($levels as $level_nama => $alias)
                        <td align="right">{{ number_format($weekly_total[$level_nama], 2) }}</td>
                    @endforeach
                </tr>
                @php
                    $weekly_total = array_fill_keys(array_keys($levels), 0);
                @endphp
            @endif

            @php $last_week = $week; @endphp
        @endforeach

        {{-- total minggu terakhir jika belum berakhir Minggu --}}
        @if (array_sum($weekly_total) > 0)
            <tr style="font-weight:bold; background-color:#f0f0f0;">
                <td colspan="3">Total</td>
                @foreach ($levels as $level_nama => $alias)
                    <td align="right">{{ number_format($weekly_total[$level_nama], 2) }}</td>
                @endforeach
            </tr>
        @endif
    </tbody>
</table>
