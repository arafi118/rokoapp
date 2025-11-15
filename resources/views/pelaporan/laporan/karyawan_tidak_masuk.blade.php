@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    //     BERHENTI 1 HARI SEBELEUM TANGGAL AKHIR
    // $start = new DateTime($tanggal_awal);
    // $end = new DateTime($tanggal_akhir);
    // $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $weekly_total = array_fill(1, 8, 0);
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
    LAPORAN KARYAWAN TIDAK MASUK
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
            <th colspan="8">JUMLAH KARYAWAN TIDAK MASUK (satuan orang)</th>
        </tr>
        <tr>
            <th>Giling</th>
            <th>Gunting</th>
            <th>Packing</th>
            <th>Banderol</th>
            <th>DPP</th>
            <th>MOP</th>
            <th>Bulanan Struktural</th>
            <th>Bulanan Non Struktural</th>
        </tr>
    </thead>
    <tbody>

        {{-- ====== DATA TIDAK MASUK BULAN LALU ====== --}}
        @php
            $tgl_tampil_lalu = \Carbon\Carbon::parse($tanggal_bulan_lalu)->format('d-M-y');
            $hari_lalu = Tanggal::namaHari($tanggal_bulan_lalu);

            $weekly_total = array_fill(1, 8, 0);
            $last_week = null;
        @endphp

        <tr>
            <td colspan="3"><strong>Tidak Masuk Bulan Lalu</strong></td>
            @foreach ($tidak_masuk_lalu as $val)
                <td>{{ $val }}</td>
            @endforeach
        </tr>

        <tr>
            <td></td>
            <td>{{ $tgl_tampil_lalu }}</td>
            <td>{{ $hari_lalu }}</td>
            @foreach ($tidak_masuk_lalu as $val)
                <td>{{ $val }}</td>
            @endforeach
        </tr>

        <tr style="font-weight:bold;">
            <td colspan="3">Total</td>
            @foreach ($tidak_masuk_lalu as $val)
                <td>{{ $val }}</td>
            @endforeach
        </tr>


        {{-- ====== DATA MINGGU BERJALAN ====== --}}
        @foreach ($period as $date)
            @php
                $week = $date->format('W');
                $tgl = $date->format('Y-m-d');
                $tgl_tampil = $date->format('d-M-y');
                $hari = Tanggal::namaHari($tgl);

                $harian = $karyawan[$tgl] ?? [];

                $row = [];
                for ($i = 1; $i <= 8; $i++) {
                    $row[$i] = $harian[$i] ?? 0;
                    $weekly_total[$i] += $row[$i];
                }
            @endphp

            <tr class="{{ $hari == 'Minggu' ? 'minggu' : '' }}">
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>
                @foreach ($row as $val)
                    <td>{{ $val }}</td>
                @endforeach
            </tr>

            {{-- total akhir minggu --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background:#f0f0f0;">
                    <td colspan="3">Total</td>
                    @foreach ($weekly_total as $val)
                        <td>{{ $val }}</td>
                    @endforeach
                </tr>
                @php
                    $weekly_total = array_fill(1, 8, 0);
                @endphp
            @endif

            @php $last_week = $week; @endphp
        @endforeach

        {{-- total minggu terakhir --}}
        @if (array_sum($weekly_total) > 0)
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td colspan="3">Total</td>
                @foreach ($weekly_total as $val)
                    <td>{{ $val }}</td>
                @endforeach
            </tr>
        @endif

    </tbody>

</table>
