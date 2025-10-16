@php
    use App\Utils\Tanggal;

    $tahun = date('Y');

    $daftarBulan = [
        1 => '01. JANUARI',
        2 => '02. FEBRUARI',
        3 => '03. MARET',
        4 => '04. APRIL',
        5 => '05. MEI',
        6 => '06. JUNI',
        7 => '07. JULI',
        8 => '08. AGUSTUS',
        9 => '09. SEPTEMBER',
        10 => '10. OKTOBER',
        11 => '11. NOVEMBER',
        12 => '12. DESEMBER',
    ];

    $namaBulan = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];
@endphp

@extends('inspeksi.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="/inspeksi/laporan-absensi" method="post" id="formCetakLaporanAbsensi" target="_blank">
                @csrf
                <div class="row">
                    {{-- Bulan --}}
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="bulan" class="form-label">Pilih Bulan</label>
                            <select id="bulan" name="bulan" class="form-select select2 form-select-lg">
                                <option value="">-- Pilih Bulan --</option>
                                @foreach ($daftarBulan as $key => $nama)
                                    <option value="{{ $key }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Kelompok --}}
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Pilih Kelompok</label>
                            <select id="kelompok" name="kelompok" class="form-select select2 form-select-lg">
                                <option value="">-- Pilih Kelompok --</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Minggu --}}
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="minggu_ke" class="form-label">Pilih Minggu</label>
                            <select id="minggu_ke" name="minggu_ke" class="form-select select2 form-select-lg">
                                <option value="">-- Pilih Minggu --</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>

            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary" id="cetakLaporan">Cetak Laporan</button>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            const namaBulan = [
                '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ]

            $('#bulan').on('change', function() {
                const bulan = $(this).val()
                const tahun = new Date().getFullYear()
                const mingguSelect = $('#minggu_ke')
                mingguSelect.empty().append('<option value="">-- Pilih Minggu --</option>')

                if (!bulan) return

                const tanggalAwal = new Date(tahun, bulan - 1, 1)
                const tanggalAkhir = new Date(tahun, bulan, 0)

                let mingguKe = 1
                let current = new Date(tanggalAwal)

                while (current <= tanggalAkhir) {
                    const start = new Date(current)
                    const end = new Date(current)
                    end.setDate(end.getDate() + 6)
                    if (end > tanggalAkhir) end.setDate(tanggalAkhir.getDate())

                    const opsiLabel =
                        `Minggu ke-${mingguKe}: ${start.getDate()} ${namaBulan[start.getMonth()+1]} ${tahun} s/d ${end.getDate()} ${namaBulan[end.getMonth()+1]} ${tahun}`
                    const opsiValue =
                        `${start.toISOString().split('T')[0]}#${end.toISOString().split('T')[0]}`
                    mingguSelect.append(`<option value="${opsiValue}">${opsiLabel}</option>`)

                    current.setDate(current.getDate() + 7)
                    mingguKe++
                }
            })

            $('#cetakLaporan').on('click', function(e) {
                e.preventDefault()
                const bulan = $('#bulan').val()
                const kelompok = $('#kelompok').val()
                const minggu = $('#minggu_ke').val()

                if (!bulan || !kelompok || !minggu) {
                    alert('Silakan pilih bulan, kelompok, dan minggu terlebih dahulu.')
                    return
                }

                $('#formCetakLaporanAbsensi').submit()
            })
        })
    </script>
@endsection
