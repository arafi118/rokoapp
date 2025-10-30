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
<style>
    body {
        font-family: Calibri, Arial, sans-serif;
        font-size: 13px;
    }


    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        border: 1px solid #000;
        padding: 4px 6px;
    }

    thead tr:first-child {
        background-color: #c7f4ff;
        color: 000;
    }

    table.no-border,
    table.no-border td,
    table.no-border th {
        border: none !important;
    }
</style>
<title>{{ $title }}</title>

<body>

    <h3 style="text-align:center; margin-bottom:20px; font-size:20px;">
        DATA MONITORING KARYAWAN PT. SDM - PERIODE {{ strtoupper($periode_label) }}
    </h3>

    <table class="no-border" style="margin-bottom:10px;">
        <tr>
            @if ($periode_label == 'Mingguan')
                <td style="text-align:left; width:15%;">MINGGU KE</td>
                <td style="text-align:left; width:25%;">
                    : {{ \Carbon\Carbon::parse($tanggal_awal)->format('W') }}
                </td>
            @else
                <td style="text-align:left; width:15%;">BULAN</td>
                <td style="text-align:left; width:25%;">
                    : {{ Tanggal::namaBulan(date('Y-' . $bulan . '-01')) }}
                </td>
            @endif

            <td style="width:40%;"></td>

            <td style="text-align:left; width:15%;">TAHUN</td>
            <td style="text-align:left; width:20%;">: {{ $tahun }}</td>
        </tr>

        <tr>
            <td style="text-align:left;">TANGGAL</td>
            <td style="text-align:left;">
                :
                @if ($periode_label == 'Bulanan')
                    {{ date('d M', strtotime($tanggal_awal)) }} s/d {{ date('d M', strtotime($tanggal_akhir)) }}
                @else
                    {{ date("d M'y", strtotime($tanggal_awal)) }} s/d {{ date("d M'y", strtotime($tanggal_akhir)) }}
                @endif
            </td>

            <td></td>
            <td style="text-align:left;">BRAND</td>
            <td style="text-align:left;">: ARO-16</td>
        </tr>

        <tr>
            <td style="text-align:left;">LOKASI</td>
            <td style="text-align:left;">: TEMPURAN MAGELANG</td>
            <td></td>
            <td style="text-align:left;">UPDATE TGL</td>
            <td style="text-align:left;">: {{ now()->format('d-m-Y') }}</td>
        </tr>
    </table>



    <table>
        <thead>
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">JABATAN</th>
                <th rowspan="2">PERINGKAT</th>
                <th rowspan="2">TERDAFTAR AWAL</th>
                <th colspan="3">PENAMBAHAN</th>
                <th colspan="3">PENGURANGAN</th>
                <th rowspan="2">TERDAFTAR AKHIR</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr style="background-color:#c7f4ff; color:000;">
                <th>BARU</th>
                <th>NAIK PERINGKAT</th>
                <th>MUTASI</th>
                <th>NAIK PERINGKAT</th>
                <th>MUTASI</th>
                <th>KELUAR</th>
            </tr>
        </thead>

        <tbody>
            <tr style="background-color:#295e77; color:rgb(255, 255, 255);">
                <td colspan="12" class="text-left"><strong>A. KATEGORI BORONG</strong></td>
            </tr>

            @php $no = 1; @endphp
            @foreach ($kategori as $bagian => $levels)
                {{-- Baris per kategori --}}
                @php $first = true; @endphp
                @foreach ($levels as $lvl)
                    <tr>
                        @if ($first)
                            <td>{{ $no++ }}</td>
                            <td class="text-left">{{ $bagian }}</td>
                            @php $first = false; @endphp
                        @else
                            <td></td>
                            <td></td>
                        @endif
                        <td class="text-left">{{ $lvl['peringkat'] }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                        <td></td>
                    </tr>
                @endforeach

                {{-- Total per kategori --}}
                <tr style="background-color:#e0e0e0;">
                    <td colspan="3" class="text-right"><strong>Total {{ $bagian }}</strong></td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td></td>
                </tr>
            @endforeach
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Borongan</strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
        </tbody>
        <tbody>
            {{-- ================= B. KATEGORI BULANAN STRUKTURAL ================= --}}
            <tr style="background-color:#295e77; color:rgb(255, 255, 255);">
                <td colspan="12" class="text-left"><strong>B. KATEGORI BULANAN STRUKTURAL</strong></td>
            </tr>

            @php
                $no = 1;
                $kategori_non_struktual = [
                    'Mandor Inspeksi Giling/Gunting & BOM',
                    'Mandor Kelompok Giling & Gunting',
                    'Mandor Kelompok Pak',
                    'Mandor Kelompok BOM',
                    'Distribusi (Pasok)',
                    'Tobacco Distribusi (Urai Tembakau)',
                    'Baller',
                    'Maintenance',
                    'Kebersihan & Umum',
                    'Petugas Sortir Emboss',
                ];
            @endphp

            @foreach ($kategori_non_struktual as $jabatan)
                <tr>
                    <td>{{ $no++ }}</td>
                    {{-- Gabungkan kolom Jabatan + Peringkat --}}
                    <td colspan="2" class="text-left">{{ $jabatan }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td></td>
                </tr>
            @endforeach

            {{-- Total Bulanan Struktural --}}
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Bulanan Struktural</strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
        </tbody>

        <tbody>
            {{-- ================= B. KATEGORI BULANAN STRUKTURAL ================= --}}
            <tr style="background-color:#295e77; color:rgb(255, 255, 255);">
                <td colspan="12" class="text-left"><strong>C. KATEGORI BULANAN NON STRUKTURAL</strong></td>
            </tr>

            @php
                $no = 1;
                $kategori_non_struktual = [
                    'Kepala Pabrik',
                    'Supervisor Giling & Gunting',
                    'Supervisor Pak & BOM',
                    'Supervisor Logistik & Admin.Produksi',
                    'Supervisor  PA, Umum & Keuangan',
                    'Staff Keuangan ( Payroll )',
                    'Staff Keuangan ( Accounting )',
                    'Staff Personalia & Umum',
                    'Administrasi Produksi',
                    'Satpam Wanita',
                    'Satpam Pria',
                    'Paramedic',
                    'Driver  Forklift',
                    'Driver',
                ];
            @endphp

            @foreach ($kategori_non_struktual as $jabatan)
                <tr>
                    <td>{{ $no++ }}</td>
                    {{-- Gabungkan kolom Jabatan + Peringkat --}}
                    <td colspan="2" class="text-left">{{ $jabatan }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td></td>
                </tr>
            @endforeach

            {{-- Total Bulanan non Struktural --}}
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Bulanan Non Struktural</strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Bulanan</strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Karyawan (Borongan + Bulanan)</strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <table style="border: none; margin-top: 10px; margin-bottom: 0;">
        <tr>
            <td style="border: none" colspan="3" height="5"></td>
        </tr>
        <tr>
            <td style="border: none; font-weight: bold; font-size: 14px;">SUMMARY</td>
        </tr>
    </table>
    <table>
        <thead>
            @php
                $no = 1;
                $summary = [
                    'Kepala Pabrik',
                    'Supervisor Giling & Gunting',
                    'Supervisor Pak & BOM',
                    'Supervisor Logistik & Admin.Produksi',
                    'Supervisor  PA, Umum & Keuangan',
                    'Staff Keuangan ( Payroll )',
                    'Staff Keuangan ( Accounting )',
                    'Staff Personalia & Umum',
                    'Administrasi Produksi',
                ];
            @endphp
            <tr>
                <th rowspan="2">NO</th>
                <th rowspan="2">JABATAN</th>
                <th rowspan="2">TERDAFTAR AWAL</th>
                <th colspan="3">PENAMBAHAN</th>
                <th colspan="3">PENGURANGAN</th>
                <th rowspan="2">TERDAFTAR AKHIR</th>
                <th rowspan="2">KETERANGAN</th>
            </tr>
            <tr style="background-color:#c7f4ff; color:000;">
                <th>BARU</th>
                <th>NAIK</th>
                <th>MUTASI</th>
                <th>NAIK PERINGKAT</th>
                <th>MUTASI</th>
                <th>KELUAR</th>
            </tr>
            @foreach ($summary as $jabatan)
                <tr>
                    <td>{{ $no++ }}</td>
                    {{-- Gabungkan kolom Jabatan + Peringkat --}}
                    <td colspan="2" class="text-left">{{ $jabatan }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td></td>
                </tr>
            @endforeach
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>GRAND TOTAL</strong></td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td></td>
            </tr>
        </thead>
    </table>

</body>
