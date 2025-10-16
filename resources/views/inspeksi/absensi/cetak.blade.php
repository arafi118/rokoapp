@php
    use App\Utils\Tanggal;

    $tanggal = new Datetime($tanggal_awal);
    $minggu_ke = $tanggal->format('W');

    $start = new DateTime($tanggal_awal);
    $end = new DateTime($tanggal_akhir);

    $period = new DatePeriod($start, new DateInterval('P1D'), $end);
@endphp

<table border="1" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td colspan="4" rowspan="3">Absensi {{ $kelompok->nama }}</td>
        <td colspan="18"></td>
    </tr>
    <tr>
        <td colspan="18">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">Week: {{ $minggu_ke }}</td>
        <td colspan="3">Bulan: {{ date('m', strtotime($tanggal_akhir)) }}</td>
        <td colspan="12">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">Hari</td>
        <td rowspan="4">G R A D E</td>
        @foreach ($period as $date)
            <td colspan="3">{{ Tanggal::namaHari($date->format('Y-m-d')) }}</td>
        @endforeach
    </tr>

    <tr>
        <td colspan="3">Tanggal</td>
        @foreach ($period as $date)
            <td colspan="3">{{ $date->format('m/d/Y') }}</td>
        @endforeach
    </tr>
    <tr>
        <td colspan="3">Jam Kerja</td>
        @foreach ($period as $date)
            <td colspan="3">&nbsp;</td>
        @endforeach
    </tr>
    <tr>
        <td>No.</td>
        <td>ID</td>
        <td>Nama</td>
        @foreach ($period as $date)
            <td>Plan</td>
            <td>Actual</td>
            <td>JK</td>
        @endforeach
    </tr>
</table>
