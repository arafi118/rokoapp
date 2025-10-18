@php
    use App\Utils\Tanggal;

    $tanggal = new Datetime($tanggal_awal);
    $minggu_ke = $tanggal->format('W');

    $start = new DateTime($tanggal_awal);
    $end = new DateTime($tanggal_akhir);

    $period = new DatePeriod($start, new DateInterval('P1D'), $end);

    $last = date('Ymd', strtotime('-1 day', strtotime($tanggal_akhir)));
@endphp

<style>
    * {
        font-family: Arial, Helvetica, sans-serif;
    }

    html {
        margin: 75.59px;
        margin-left: 94.48px;
    }

    ul,
    ol {
        margin-left: -10px;
        page-break-inside: auto !important;
    }

    header {
        position: fixed;
        top: -10px;
        left: 0px;
        right: 0px;
    }

    table tr th,
    table tr td,
    table tr td table.p tr td {
        padding: 2px 4px !important;
    }

    table tr td table tr td {
        padding: 0 !important;
    }

    table.p0 tr th,
    table.p0 tr td {
        padding: 0px !important;
    }

    .break {
        page-break-after: always;
    }

    li {
        text-align: justify;
    }

    .l {
        border-left: 1px solid #000;
    }

    .t {
        border-top: 1px solid #000;
    }

    .r {
        border-right: 1px solid #000;
    }

    .b {
        border-bottom: 1px solid #000;
    }
</style>

<table border="0" cellspacing="0" cellpadding="0" width="100%" style="font-size: 11px;">
    <tr>
        <td class="t l b" colspan="4" rowspan="2">Absensi {{ $kelompok->nama }}</td>
        <td class="t l b r" colspan="18" height="20"></td>
    </tr>
    <tr>
        <td class="t l b" colspan="3">Week: {{ $minggu_ke }}</td>
        <td class="t l b" colspan="3">Bulan: {{ date('m', strtotime($tanggal_akhir)) }}</td>
        <td class="t l b r" colspan="12">&nbsp;</td>
    </tr>
    <tr>
        <td class="t l b" colspan="3">Hari</td>
        <td class="t l b" rowspan="4" align="center" width="2%">G R A D E</td>
        @foreach ($period as $date)
            <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" colspan="3" align="center">
                {{ Tanggal::namaHari($date->format('Y-m-d')) }}
            </td>
        @endforeach
    </tr>

    <tr>
        <td class="t l b" colspan="3">Tanggal</td>
        @foreach ($period as $date)
            <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" colspan="3" align="center">
                {{ $date->format('m/d/Y') }}
            </td>
        @endforeach
    </tr>
    <tr>
        <td class="t l b" colspan="3">Jam Kerja</td>
        @foreach ($period as $date)
            <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" colspan="3">
                &nbsp;
            </td>
        @endforeach
    </tr>
    <tr>
        <td class="t l b" width="4%" align="center">No.</td>
        <td class="t l b" width="10%">ID</td>
        <td class="t l b" width="24%">Nama</td>
        @foreach ($period as $date)
            <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" width="2%" align="center">Plan</td>
            <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" width="5%" align="center">Actual</td>
            <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" width="3%" align="center">JK</td>
        @endforeach
    </tr>

    @foreach ($absenMingguan as $karyawan)
        <tr>
            <td class="t l b" align="center">{{ $loop->iteration }}</td>
            <td class="t l b">{{ $karyawan['kode_karyawan'] }}</td>
            <td class="t l b">{{ $karyawan['nama'] }}</td>
            <td class="t l b"></td>
            @foreach ($period as $date)
                @php
                    if (isset($karyawan['absensi'][$date->format('Y-m-d')])) {
                        $absen = $karyawan['absensi'][$date->format('Y-m-d')];

                        $jam_masuk = date_create($absen['jam_masuk']);
                        $jam_keluar = date_create($absen['jam_keluar']);
                        $diff = date_diff($jam_keluar, $jam_masuk);

                        $status = $absen['status'];
                        $jam_kerja = $diff->h;
                    } else {
                        $status = 'A';
                        $jam_kerja = 0;
                    }
                @endphp
                <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" align="center">{{ $status }}
                </td>
                <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" align="center">0</td>
                <td class="t l b {{ $last == $date->format('Ymd') ? 'r' : '' }}" align="center">{{ $jam_kerja }}
                </td>
            @endforeach
        </tr>
    @endforeach
</table>
