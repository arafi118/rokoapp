@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

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
    LAPORAN JAM KERJA AKTUAL
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
        <tr style="background-color:#d9e1f2; font-weight:bold; text-align:center;">
            <th rowspan="2" width="5%">WEEK</th>
            <th rowspan="2" width="12%">TANGGAL</th>
            <th rowspan="2" width="10%">HARI</th>
            <th rowspan="2" width="12%">Rencana Jam Kerja (jam/orang)</th>
            <th colspan="8">JAM KERJA AKTUAL (satuan jam/orang)</th>
        </tr>
        <tr style="background-color:#d9e1f2; text-align:center;">
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
        @foreach ($period as $date)
            @php
                $week = $date->format('W');
                $tgl = $date->format('Y-m-d');
                $tgl_tampil = $date->format('d-M-y');
                $hari = Tanggal::namaHari($tgl);

                // Data harian dari controller
                $harian = $jam_kerja[$tgl] ?? [];

                // Buat baris level 1–8
                $row = [];
                for ($i = 1; $i <= 8; $i++) {
                    $nilai = $harian[$i] ?? 0;
                    $row[$i] = number_format($nilai, 2, ',', '.');
                    $weekly_total[$i] += $nilai;
                }

                // Rata-rata jam kerja per tanggal
                $jam_rata = $rata_rata_jam_kerja[$tgl] ?? 0;
            @endphp

            <tr class="{{ $hari == 'Minggu' ? 'minggu' : '' }}">
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>
                <td style="text-align:center;">
                    {{ $hari == 'Minggu' ? '' : number_format($jam_rata, 2, ',', '.') }}
                </td>

                @for ($i = 1; $i <= 8; $i++)
                    <td style="text-align:right;">{{ $row[$i] }}</td>
                @endfor
            </tr>

            {{-- Jika Minggu, tampilkan total mingguan --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background-color:#f2f2f2;">
                    <td colspan="4">Total Mingguan</td>
                    @for ($i = 1; $i <= 8; $i++)
                        <td style="text-align:right;">{{ number_format($weekly_total[$i], 2, ',', '.') }}</td>
                    @endfor
                </tr>
                @php
                    $weekly_total = array_fill(1, 8, 0);
                @endphp
            @endif

            @php $last_week = $week; @endphp
        @endforeach

        {{-- Total akhir (jika tidak berhenti di hari Minggu) --}}
        @if (array_sum($weekly_total) > 0)
            <tr style="font-weight:bold; background-color:#f2f2f2;">
                <td colspan="4">Total Akhir</td>
                @for ($i = 1; $i <= 8; $i++)
                    <td style="text-align:right;">{{ number_format($weekly_total[$i], 2, ',', '.') }}</td>
                @endfor
            </tr>
        @endif
    </tbody>
</table>
