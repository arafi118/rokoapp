@php
    use Carbon\CarbonPeriod;
    use Carbon\Carbon;

    use App\Utils\Tanggal;

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $hariList = [];
    foreach ($period as $d) {
        $hariList[] = [
            'tanggal' => $d->format('Y-m-d'),
            'label' => Tanggal::namaHari($d->format('Y-m-d')) . ' (' . $d->format('d/m') . ')',
        ];
    }
    $weekNumber = Carbon::parse($tanggal_awal)->format('W');

@endphp

<title>{{ $title }}</title>

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 15px;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
    }

    th {
        background-color: #c7f4ff;
        font-weight: bold;
    }

    .header-info td {
        border: none;
        text-align: left;
        padding: 2px 0;
    }

    .group-title {
        background-color: #eee;
        font-weight: bold;
        text-align: left;
        padding: 6px;
        margin-top: 10px;
    }
</style>

<h2 style="text-align:center; font-size:18px; margin-bottom:10px;">LAPORAN PRODUKSI HARIAN</h2>

<table class="header-info">
    <tr>
        <td width="15%">KELOMPOK</td>
        <td>: {{ $kelompok_data ? reset($kelompok_data)['nama'] ?? '-' : '-' }}</td>
    </tr>
    <tr>
        <td>WEEK</td>
        <td>: {{ $weekNumber }}</td>
    </tr>
    <tr>
        <td>PERIODE</td>
        <td>: {{ Carbon::parse($tanggal_awal)->translatedFormat('d F Y') }} -
            {{ Carbon::parse($tanggal_akhir)->translatedFormat('d F Y') }}</td>
    </tr>
</table>


@foreach ($kelompok_data as $group)
    <table>
        <thead>
            {{-- Baris 1: Nama hari --}}
            <tr>
                <th rowspan="3" width="4%">NO</th>
                <th rowspan="3" width="15%">NAMA</th>
                <th rowspan="3" width="7%">NIP</th>
                @foreach ($hariList as $h)
                    @php
                        $hari = Tanggal::namaHari($h['tanggal']);
                        if ($hari === 'Minggu') {
                            continue;
                        }
                    @endphp
                    <th colspan="2">{{ strtoupper($hari) }}</th>
                @endforeach
            </tr>

            {{-- Baris 2: Tanggal (dengan tahun) --}}
            <tr>
                @foreach ($hariList as $h)
                    @php
                        $hari = Tanggal::namaHari($h['tanggal']);
                        if ($hari === 'Minggu') {
                            continue;
                        }
                        $tgl_dengan_tahun = Carbon::parse($h['tanggal'])->format('d/m/Y');
                    @endphp
                    <th colspan="2">{{ $tgl_dengan_tahun }}</th>
                @endforeach
            </tr>

            {{-- Baris 3: Plan & Actual --}}
            <tr>
                @foreach ($hariList as $h)
                    @php
                        $hari = Tanggal::namaHari($h['tanggal']);
                        if ($hari === 'Minggu') {
                            continue;
                        }
                    @endphp
                    <th>PLAN</th>
                    <th>ACTUAL</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @php $no = 1; @endphp
            @foreach ($group['data'] as $karyawan)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td style="text-align:left;">{{ $karyawan['nama'] }}</td>
                    <td>P. {{ $karyawan['nip'] }}</td>
                    @foreach ($hariList as $h)
                        @php
                            $hari = Tanggal::namaHari($h['tanggal']);
                            if ($hari === 'Minggu') {
                                continue;
                            }
                            $t = $h['tanggal'];
                            $plan = $karyawan['tanggal'][$t]['plan'] ?? 0;
                            $actual = $karyawan['tanggal'][$t]['actual'] ?? 0;
                        @endphp
                        <td>{{ $plan > 0 ? number_format($plan) : '' }}</td>
                        <td>{{ $actual > 0 ? number_format($actual) : '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endforeach
