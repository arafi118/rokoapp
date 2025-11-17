@php
    use App\Utils\Tanggal;
    use Carbon\CarbonPeriod;

    $period = CarbonPeriod::create($tanggal_awal, '1 day', $tanggal_akhir);
    $weekly_total = array_fill(1, 8, 0);
    $last_week = null;

@endphp
<style>
    body {
        font-family: Calibri, Arial, sans-serif;
        font-size: 12px;
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

            @php
                $nomor = 1;

                $totalTerdaftarAwal1 = 0;
                $totalTambahBaru1 = 0;
                $totalTambahNaikPeringkat1 = 0;
                $totalTambahMutasi1 = 0;
                $totalKurangNaikPeringkat1 = 0;
                $totalKurangMutasi1 = 0;
                $totalKurangKeluar1 = 0;
                $totalTerdaftarAkhir1 = 0;
            @endphp
            @foreach ($levels[1] as $level)
                @foreach ($peringkat as $key => $value)
                    @php
                        $terdaftarAwal = 0;
                        $tambahBaru = 0;
                        $tambahNaikPeringkat = 0;
                        $tambahMutasi = 0;
                        $kurangNaikPeringkat = 0;
                        $kurangMutasi = 0;
                        $kurangKeluar = 0;

                        if (isset($level['data'][$key])) {
                            $data = $level['data'][$key];
                            $terdaftarAwal = $data['terdaftar_awal'] ?? 0;
                            $tambahBaru = $data['baru'] ?? 0;
                            $tambahNaikPeringkat = $data['penambahan']['naik peringkat'] ?? 0;
                            $tambahMutasi = $data['penambahan']['mutasi'] ?? 0;
                            $kurangNaikPeringkat = $data['pengurangan']['naik peringkat'] ?? 0;
                            $kurangMutasi = $data['pengurangan']['mutasi'] ?? 0;
                            $kurangKeluar = $data['pengurangan']['keluar'] ?? 0;
                        }

                        $jumlahPenambahan = $tambahBaru + $tambahNaikPeringkat + $tambahMutasi;
                        $jumlahPengurangan = $kurangNaikPeringkat + $kurangMutasi + $kurangKeluar;

                        $terdaftarAkhir = $terdaftarAwal + $jumlahPenambahan - $jumlahPengurangan;

                        $totalTerdaftarAwal1 += $terdaftarAwal;
                        $totalTambahBaru1 += $tambahBaru;
                        $totalTambahNaikPeringkat1 += $tambahNaikPeringkat;
                        $totalTambahMutasi1 += $tambahMutasi;
                        $totalKurangNaikPeringkat1 += $kurangNaikPeringkat;
                        $totalKurangMutasi1 += $kurangMutasi;
                        $totalKurangKeluar1 += $kurangKeluar;
                        $totalTerdaftarAkhir1 += $terdaftarAkhir;
                    @endphp
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ count($peringkat) }}">{{ $nomor++ }}</td>
                            <td rowspan="{{ count($peringkat) }}">{{ $level['nama'] }}</td>
                        @endif
                        <td>{{ $value }}</td>
                        <td align="center">{{ $terdaftarAwal }}</td>
                        <td align="center">{{ $tambahBaru }}</td>
                        <td align="center">{{ $tambahNaikPeringkat }}</td>
                        <td align="center">{{ $tambahMutasi }}</td>
                        <td align="center">{{ $kurangNaikPeringkat }}</td>
                        <td align="center">{{ $kurangMutasi }}</td>
                        <td align="center">{{ $kurangKeluar }}</td>
                        <td align="center">{{ $terdaftarAkhir }}</td>
                        <td align="center"></td>
                    </tr>
                @endforeach

                <tr style="background-color:#e0e0e0;">
                    <td colspan="3">
                        <b>Total {{ $level['nama'] }}</b>
                    </td>
                    <td colspan="9">&nbsp;</td>
                </tr>
            @endforeach

            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Borongan</strong></td>
                <td align="center">{{ $totalTerdaftarAwal1 }}</td>
                <td align="center">{{ $totalTambahBaru1 }}</td>
                <td align="center">{{ $totalTambahNaikPeringkat1 }}</td>
                <td align="center">{{ $totalTambahMutasi1 }}</td>
                <td align="center">{{ $totalKurangNaikPeringkat1 }}</td>
                <td align="center">{{ $totalKurangMutasi1 }}</td>
                <td align="center">{{ $totalKurangKeluar1 }}</td>
                <td align="center">{{ $totalTerdaftarAkhir1 }}</td>
                <td align="center"></td>
            </tr>
        </tbody>
        <tbody>
            {{-- ================= B. KATEGORI BULANAN STRUKTURAL ================= --}}
            <tr style="background-color:#295e77; color:rgb(255, 255, 255);">
                <td colspan="12" class="text-left"><strong>B. KATEGORI BULANAN STRUKTURAL</strong></td>
            </tr>

            @php
                $no = 1;

                $totalTerdaftarAwal2 = 0;
                $totalTambahBaru2 = 0;
                $totalTambahNaikPeringkat2 = 0;
                $totalTambahMutasi2 = 0;
                $totalKurangNaikPeringkat2 = 0;
                $totalKurangMutasi2 = 0;
                $totalKurangKeluar2 = 0;
                $totalTerdaftarAkhir2 = 0;
            @endphp

            @foreach ($levels[2] as $level)
                @php
                    $terdaftarAwal = 0;
                    $tambahBaru = 0;
                    $tambahNaikPeringkat = 0;
                    $tambahMutasi = 0;
                    $kurangNaikPeringkat = 0;
                    $kurangMutasi = 0;
                    $kurangKeluar = 0;

                    if (isset($level['data']['K'])) {
                        $data = $level['data']['K'];
                        $terdaftarAwal = $data['terdaftar_awal'] ?? 0;
                        $tambahBaru = $data['baru'] ?? 0;
                        $tambahNaikPeringkat = $data['penambahan']['naik peringkat'] ?? 0;
                        $tambahMutasi = $data['penambahan']['mutasi'] ?? 0;
                        $kurangNaikPeringkat = $data['pengurangan']['naik peringkat'] ?? 0;
                        $kurangMutasi = $data['pengurangan']['mutasi'] ?? 0;
                        $kurangKeluar = $data['pengurangan']['keluar'] ?? 0;
                    }

                    $jumlahPenambahan = $tambahBaru + $tambahNaikPeringkat + $tambahMutasi;
                    $jumlahPengurangan = $kurangNaikPeringkat + $kurangMutasi + $kurangKeluar;

                    $terdaftarAkhir = $terdaftarAwal + $jumlahPenambahan - $jumlahPengurangan;

                    $totalTerdaftarAwal2 += $terdaftarAwal;
                    $totalTambahBaru2 += $tambahBaru;
                    $totalTambahNaikPeringkat2 += $tambahNaikPeringkat;
                    $totalTambahMutasi2 += $tambahMutasi;
                    $totalKurangNaikPeringkat2 += $kurangNaikPeringkat;
                    $totalKurangMutasi2 += $kurangMutasi;
                    $totalKurangKeluar2 += $kurangKeluar;
                    $totalTerdaftarAkhir2 += $terdaftarAkhir;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td colspan="2" class="text-left">{{ $level['nama'] }}</td>
                    <td align="center">{{ $terdaftarAwal }}</td>
                    <td align="center">{{ $tambahBaru }}</td>
                    <td align="center">{{ $tambahNaikPeringkat }}</td>
                    <td align="center">{{ $tambahMutasi }}</td>
                    <td align="center">{{ $kurangNaikPeringkat }}</td>
                    <td align="center">{{ $kurangMutasi }}</td>
                    <td align="center">{{ $kurangKeluar }}</td>
                    <td align="center">{{ $terdaftarAkhir }}</td>
                    <td align="center"></td>
                </tr>
            @endforeach

            {{-- Total Bulanan Struktural --}}
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Bulanan Struktural</strong></td>
                <td align="center">{{ $totalTerdaftarAwal2 }}</td>
                <td align="center">{{ $totalTambahBaru2 }}</td>
                <td align="center">{{ $totalTambahNaikPeringkat2 }}</td>
                <td align="center">{{ $totalTambahMutasi2 }}</td>
                <td align="center">{{ $totalKurangNaikPeringkat2 }}</td>
                <td align="center">{{ $totalKurangMutasi2 }}</td>
                <td align="center">{{ $totalKurangKeluar2 }}</td>
                <td align="center">{{ $totalTerdaftarAkhir2 }}</td>
                <td align="center"></td>
            </tr>
        </tbody>

        <tbody>
            {{-- ================= B. KATEGORI BULANAN STRUKTURAL ================= --}}
            <tr style="background-color:#295e77; color:rgb(255, 255, 255);">
                <td colspan="12" class="text-left"><strong>C. KATEGORI BULANAN NON STRUKTURAL</strong></td>
            </tr>

            @php
                $no = 1;

                $totalTerdaftarAwal3 = 0;
                $totalTambahBaru3 = 0;
                $totalTambahNaikPeringkat3 = 0;
                $totalTambahMutasi3 = 0;
                $totalKurangNaikPeringkat3 = 0;
                $totalKurangMutasi3 = 0;
                $totalKurangKeluar3 = 0;
                $totalTerdaftarAkhir3 = 0;
            @endphp

            @foreach ($levels[3] as $level)
                @php
                    $terdaftarAwal = 0;
                    $tambahBaru = 0;
                    $tambahNaikPeringkat = 0;
                    $tambahMutasi = 0;
                    $kurangNaikPeringkat = 0;
                    $kurangMutasi = 0;
                    $kurangKeluar = 0;

                    if (isset($level['data']['K'])) {
                        $data = $level['data']['K'];
                        $terdaftarAwal = $data['terdaftar_awal'] ?? 0;
                        $tambahBaru = $data['baru'] ?? 0;
                        $tambahNaikPeringkat = $data['penambahan']['naik peringkat'] ?? 0;
                        $tambahMutasi = $data['penambahan']['mutasi'] ?? 0;
                        $kurangNaikPeringkat = $data['pengurangan']['naik peringkat'] ?? 0;
                        $kurangMutasi = $data['pengurangan']['mutasi'] ?? 0;
                        $kurangKeluar = $data['pengurangan']['keluar'] ?? 0;
                    }

                    $jumlahPenambahan = $tambahBaru + $tambahNaikPeringkat + $tambahMutasi;
                    $jumlahPengurangan = $kurangNaikPeringkat + $kurangMutasi + $kurangKeluar;

                    $terdaftarAkhir = $terdaftarAwal + $jumlahPenambahan - $jumlahPengurangan;

                    $totalTerdaftarAwal3 += $terdaftarAwal;
                    $totalTambahBaru3 += $tambahBaru;
                    $totalTambahNaikPeringkat3 += $tambahNaikPeringkat;
                    $totalTambahMutasi3 += $tambahMutasi;
                    $totalKurangNaikPeringkat3 += $kurangNaikPeringkat;
                    $totalKurangMutasi3 += $kurangMutasi;
                    $totalKurangKeluar3 += $kurangKeluar;
                    $totalTerdaftarAkhir3 += $terdaftarAkhir;
                @endphp
                <tr>
                    <td>{{ $no++ }}</td>
                    <td colspan="2" class="text-left">{{ $level['nama'] }}</td>
                    <td align="center">{{ $terdaftarAwal }}</td>
                    <td align="center">{{ $tambahBaru }}</td>
                    <td align="center">{{ $tambahNaikPeringkat }}</td>
                    <td align="center">{{ $tambahMutasi }}</td>
                    <td align="center">{{ $kurangNaikPeringkat }}</td>
                    <td align="center">{{ $kurangMutasi }}</td>
                    <td align="center">{{ $kurangKeluar }}</td>
                    <td align="center">{{ $terdaftarAkhir }}</td>
                    <td align="center"></td>
                </tr>
            @endforeach

            {{-- Total Bulanan non Struktural --}}
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Bulanan Non Struktural</strong></td>
                <td align="center">{{ $totalTerdaftarAwal3 }}</td>
                <td align="center">{{ $totalTambahBaru3 }}</td>
                <td align="center">{{ $totalTambahNaikPeringkat3 }}</td>
                <td align="center">{{ $totalTambahMutasi3 }}</td>
                <td align="center">{{ $totalKurangNaikPeringkat3 }}</td>
                <td align="center">{{ $totalKurangMutasi3 }}</td>
                <td align="center">{{ $totalKurangKeluar3 }}</td>
                <td align="center">{{ $totalTerdaftarAkhir3 }}</td>
                <td align="center"></td>
            </tr>
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Bulanan</strong></td>
                <td align="center">{{ $totalTerdaftarAwal3 + $totalTerdaftarAwal2 }}</td>
                <td align="center">{{ $totalTambahBaru3 + $totalTambahBaru2 }}</td>
                <td align="center">{{ $totalTambahNaikPeringkat3 + $totalTambahNaikPeringkat2 }}</td>
                <td align="center">{{ $totalTambahMutasi3 + $totalTambahMutasi2 }}</td>
                <td align="center">{{ $totalKurangNaikPeringkat3 + $totalKurangNaikPeringkat2 }}</td>
                <td align="center">{{ $totalKurangMutasi3 + $totalKurangMutasi2 }}</td>
                <td align="center">{{ $totalKurangKeluar3 + $totalKurangKeluar2 }}</td>
                <td align="center">{{ $totalTerdaftarAkhir3 + $totalTerdaftarAkhir2 }}</td>
                <td align="center"></td>
            </tr>
            <tr style="background-color:#e0e0e0;">
                <td colspan="3" class="text-right"><strong>Total Karyawan (Borongan + Bulanan)</strong></td>
                <td align="center">{{ $totalTerdaftarAwal3 + $totalTerdaftarAwal2 + $totalTerdaftarAwal1 }}</td>
                <td align="center">{{ $totalTambahBaru3 + $totalTambahBaru2 + $totalTambahBaru1 }}</td>
                <td align="center">
                    {{ $totalTambahNaikPeringkat3 + $totalTambahNaikPeringkat2 + $totalTambahNaikPeringkat1 }}</td>
                <td align="center">{{ $totalTambahMutasi3 + $totalTambahMutasi2 + $totalTambahMutasi1 }}</td>
                <td align="center">
                    {{ $totalKurangNaikPeringkat3 + $totalKurangNaikPeringkat2 + $totalKurangNaikPeringkat1 }}</td>
                <td align="center">{{ $totalKurangMutasi3 + $totalKurangMutasi2 + $totalKurangMutasi1 }}</td>
                <td align="center">{{ $totalKurangKeluar3 + $totalKurangKeluar2 + $totalKurangKeluar1 }}</td>
                <td align="center">
                    {{ $totalTerdaftarAkhir3 + $totalTerdaftarAkhir2 + $totalTerdaftarAkhir1 }}</td>
                <td align="center"></td>
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
