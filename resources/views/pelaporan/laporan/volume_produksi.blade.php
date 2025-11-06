@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;
    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $weekly_total = [];
    foreach ($levels as $lvl) {
        $weekly_total[$lvl->nama] = 0;
    }
    $last_week = null;
@endphp
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
        vertical-align: middle;
    }

    thead th {
        background-color: #c7f4ff;
        font-weight: bold;
        text-align: center;
    }

    .header-info td {
        border: none;
        text-align: left;
        padding: 2px 0;
    }

    .total-row td {
        font-weight: bold;
        text-align: center;
    }

    h2 {
        text-align: center;
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 15px;
    }
</style>
<title>{{ $title }}</title>
<h2>VOLUME PRODUKSI</h2>

<table class="header-info" style="margin-bottom:10px;">
    <tr>
        <td width="15%">BULAN</td>
        <td>: {{ Tanggal::namaBulan(date('Y-' . $bulan . '-01')) }} {{ $tahun }}</td>
    </tr>
    <tr>
        <td>LOKASI</td>
        <td>: SKT MAGELANG - PT. ATI</td>
    </tr>
</table>

<table border="1" cellspacing="0" cellpadding="4" width="100%">
    <thead>
        <tr>
            <th rowspan="3" width="5%">WEEK</th>
            <th rowspan="3" width="10%">TANGGAL</th>
            <th rowspan="3" width="8%">HARI</th>
            <th rowspan="3" width="10%">Rencana Produksi Terbanderol</th>
            <th colspan="{{ count($levels) - 1 }}">BRAND : ARS-16</th>
            <th rowspan="3" width="10%">Pengeluaran Finished Goods<br>(batang)</th>
            <th rowspan="3" width="6%">Afkir<br>batang</th>
            <th colspan="3">STOCK AKHIR<br>(batang)</th>
        </tr>
        <tr>
            <th colspan="{{ count($levels) - 1 }}">PRODUKSI AKTUAL<br>(batang)</th>
            <th rowspan="2" width="8%">ZB<br>(Polosan)</th>
            <th rowspan="2" width="8%">Pack Tercukai<br>(Wanspot)</th>
            <th rowspan="2" width="8%">Pak Terbanderol</th>
        </tr>
        <tr>
            @foreach ($levels as $lvl)
                @if (strtolower($lvl->nama) != 'multi level')
                    <th width="7%">{{ str_replace('Operator ', '', $lvl->nama) }}</th>
                @endif
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
                $harian = $rekap[$tgl] ?? [];
                $row = [];
                foreach ($levels as $lvl) {
                    if (strtolower($lvl->nama) != 'multi level') {
                        $row[$lvl->nama] = $harian[$lvl->nama] ?? 0;
                        $weekly_total[$lvl->nama] += $row[$lvl->nama];
                    }
                }
            @endphp
            <tr class="{{ $hari == 'Minggu' ? 'minggu' : '' }}">
                <td>{{ $last_week !== $week ? $week : '' }}</td>
                <td>{{ $tgl_tampil }}</td>
                <td>{{ $hari }}</td>
                <td></td>
                @foreach ($levels as $lvl)
                    @if (strtolower($lvl->nama) != 'multi level')
                        <td style="text-align:right;">{{ number_format($row[$lvl->nama]) }}</td>
                    @endif
                @endforeach
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

            {{-- Total tiap akhir minggu --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background-color:#f0f0f0;">
                    <td colspan="4">Total</td>
                    @foreach ($levels as $lvl)
                        @if (strtolower($lvl->nama) != 'multi level')
                            <td style="text-align:right;">{{ number_format($weekly_total[$lvl->nama]) }}</td>
                        @endif
                    @endforeach
                    <td colspan="5"></td>
                </tr>
                @php
                    foreach ($levels as $lvl) {
                        if (strtolower($lvl->nama) != 'multi level') {
                            $weekly_total[$lvl->nama] = 0;
                        }
                    }
                @endphp
            @endif

            @php $last_week = $week; @endphp
        @endforeach

        {{-- Total minggu terakhir --}}
        @if (collect($weekly_total)->sum() > 0)
            <tr style="font-weight:bold; background-color:#f0f0f0;">
                <td colspan="4">Total Akhir</td>
                @foreach ($levels as $lvl)
                    @if (strtolower($lvl->nama) != 'multi level')
                        <td style="text-align:right;">{{ number_format($weekly_total[$lvl->nama]) }}</td>
                    @endif
                @endforeach
                <td colspan="5"></td>
            </tr>
        @endif
    </tbody>
</table>
