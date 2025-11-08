@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $weekly_total = [];

    // Inisialisasi stok awal
    $stok_batangan_sebelumnya = 0;
    $stok_zb_sebelumnya = 0;
    $stok_pack_tercukai_sebelumnya = 0;
    $stok_pack_terbanderol_sebelumnya = 0;

    $last_week = null;

    foreach ($levels as $lvl) {
        $weekly_total[$lvl->nama] = 0;
    }
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
            <th rowspan="3" width="10%">Rencana Produksi Terbanderol<br>(batang)</th>
            <th colspan="{{ count($levels) - 1 }}">BRAND : ARS-16</th>
            <th rowspan="3" width="10%">Pengeluaran Finished Goods<br>(batang)</th>
            <th rowspan="3" width="8%">Afkir<br>(batang)</th>
            <th colspan="4">STOCK AKHIR<br>(batang)</th>
        </tr>
        <tr>
            <th colspan="{{ count($levels) - 1 }}">PRODUKSI AKTUAL<br>(batang)</th>
            <th rowspan="2" width="8%">Batangan</th>
            <th rowspan="2" width="8%">ZB<br>(Polosan)</th>
            <th rowspan="2" width="8%">Pack Tercukai<br>(Wanspot)</th>
            <th rowspan="2" width="8%">Pack Terbanderol</th>
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
                $rencana_harian = $rencana[$tgl] ?? [];

                $total_rencana = collect($rencana_harian)
                    ->filter(fn($val, $lvl) => strtolower($lvl) != 'multi level')
                    ->sum();

                $giling = $harian['Giling'] ?? 0;
                $gunting = $harian['Gunting'] ?? 0;
                $packing = $harian['Packing'] ?? 0;
                $banderol = $harian['Banderol'] ?? 0;
                $opp = $harian['OPP'] ?? 0;
                $mop = $harian['MOP'] ?? 0;

                $pengeluaran = 0;
                $afkir = 0;

                $stok_batangan = $stok_batangan_sebelumnya + $total_rencana - $pengeluaran - $afkir;
                $stok_zb = $stok_zb_sebelumnya + $gunting - $afkir;
                $stok_pack_tercukai = $stok_pack_tercukai_sebelumnya + $mop - $opp;
                $stok_pack_terbanderol = $stok_pack_terbanderol_sebelumnya + $opp - $banderol;

                $stok_batangan_sebelumnya = $stok_batangan;
                $stok_zb_sebelumnya = $stok_zb;
                $stok_pack_tercukai_sebelumnya = $stok_pack_tercukai;
                $stok_pack_terbanderol_sebelumnya = $stok_pack_terbanderol;

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

                <td style="text-align:right;">{{ number_format($total_rencana) }}</td>

                @foreach ($levels as $lvl)
                    @if (strtolower($lvl->nama) != 'multi level')
                        <td style="text-align:right;">{{ number_format($row[$lvl->nama]) }}</td>
                    @endif
                @endforeach

                <td style="text-align:right;">{{ number_format($pengeluaran) }}</td>
                <td style="text-align:right;">{{ number_format($afkir) }}</td>

                <td style="text-align:right;">{{ number_format($stok_batangan) }}</td>
                <td style="text-align:right;">{{ number_format($stok_zb) }}</td>
                <td style="text-align:right;">{{ number_format($stok_pack_tercukai) }}</td>
                <td style="text-align:right;">{{ number_format($stok_pack_terbanderol) }}</td>
            </tr>

            {{-- === TOTAL MINGGUAN === --}}
            @if ($hari == 'Minggu')
                <tr style="font-weight:bold; background-color:#f0f0f0;">
                    <td colspan="4">Total</td>
                    @foreach ($levels as $lvl)
                        @if (strtolower($lvl->nama) != 'multi level')
                            <td style="text-align:right;">{{ number_format($weekly_total[$lvl->nama]) }}</td>
                        @endif
                    @endforeach
                    {{-- Sisa kolom: Pengeluaran, Afkir, 4 stok = 6 --}}
                    <td colspan="6"></td>
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
    </tbody>
</table>
