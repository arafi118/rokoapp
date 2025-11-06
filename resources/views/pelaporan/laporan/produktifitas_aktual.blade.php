@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $weekly_total = [];
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
        background-color: #d6eef8;
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
    PRODUKTIFITAS (ORANG/JAM/BATANG)
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
            <th colspan="6">JUMLAH PRODUKSI (BATANG)</th>
        </tr>
        <tr>
            <th>Giling</th>
            <th>Gunting</th>
            <th>Packing</th>
            <th>Banderol</th>
            <th>DPP</th>
            <th>MOP</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($period as $date)
            @php
                $week = $date->format('W');
                $tgl = $date->format('Y-m-d');
                $tgl_tampil = $date->format('d-M-y');
                $hari = Tanggal::namaHari($tgl);

                $harian = $data[$tgl] ?? [];
                $row = [];

                // Level: 1=Giling, 2=Gunting, 3=Packing, 4=Banderol, 5=DPP, 6=MOP
                for ($i = 1; $i <= 6; $i++) {
                    $row[$i] = $harian[$i] ?? 0;
                    $weekly_total[$i] = ($weekly_total[$i] ?? 0) + $row[$i];
                }

                $total_harian = array_sum($row);
            @endphp

            <tr class="{{ $hari == 'Minggu' ? 'minggu' : '' }}">
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>

                {{-- tampilkan tanpa format ribuan dan tanpa trailing nol --}}
                @foreach ($row as $val)
                    <td align="right">
                        {{ fmod($val, 1) == 0 ? (int) $val : rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.') }}
                    </td>
                @endforeach
            </tr>

            {{-- total tiap akhir minggu --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background-color:#f0f0f0;">
                    <td colspan="3">Total</td>
                    @foreach ($weekly_total as $val)
                        <td align="right">
                            {{ fmod($val, 1) == 0 ? (int) $val : rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.') }}
                        </td>
                    @endforeach

                </tr>
                @php $weekly_total = []; @endphp
            @endif

            @php $last_week = $week; @endphp
        @endforeach

        {{-- total terakhir jika tidak berakhir di minggu --}}
        @if (array_sum($weekly_total ?? []) > 0)
            <tr style="font-weight:bold; background-color:#f0f0f0;">
                <td colspan="3">Total </td>
                @foreach ($weekly_total as $val)
                    <td align="right">
                        {{ fmod($val, 1) == 0 ? (int) $val : rtrim(rtrim(number_format($val, 2, '.', ''), '0'), '.') }}
                    </td>
                @endforeach
                <td align="right">
                    <strong>{{ fmod(array_sum($weekly_total), 1) == 0 ? (int) array_sum($weekly_total) : rtrim(rtrim(number_format(array_sum($weekly_total), 2, '.', ''), '0'), '.') }}</strong>
                </td>
            </tr>
        @endif
    </tbody>
</table>
