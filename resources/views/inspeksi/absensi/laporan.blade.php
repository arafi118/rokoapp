@php
    use App\Utils\Tanggal;

    $tahun = date('Y');

    $date = new DateTime();
    $date->setISODate($tahun, 53);
    $jumlahMinggu = $date->format('W') === '53' ? 53 : 52;
@endphp

@extends('inspeksi.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="/inspeksi/laporan-absensi" method="post" id="formCetakLaporanAbsensi" target="_blank">
                @csrf

                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="minggu_ke" class="form-label">Pilih Minggu</label>
                            <select id="minggu_ke" name="minggu_ke" class="select2 form-select form-select-lg"
                                data-allow-clear="true">
                                <option value="">-- Select Value --</option>
                                @for ($i = 1; $i <= $jumlahMinggu; $i++)
                                    @php
                                        $monday = new DateTime();
                                        $monday->setISODate($tahun, $i, 1);
                                        $sunday = clone $monday;
                                        $sunday->modify('+6 days');

                                        $tanggal = $monday->format('Y-m-d') . '#' . $sunday->format('Y-m-d');
                                        $minggu_ke = str_pad($i, 2, '0', STR_PAD_LEFT);
                                    @endphp

                                    <option value="{{ $tanggal }}">
                                        {{ "Minggu ke-$minggu_ke: " . Tanggal::tglLatin($monday->format('Y-m-d')) . ' s/d ' . Tanggal::tglLatin($sunday->format('Y-m-d')) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <select id="kelompok" name="kelompok" class="select2 form-select form-select-lg"
                                data-allow-clear="true">
                                <option value="">-- Select Value --</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">
                                        {{ $group->nama }}
                                    </option>
                                @endforeach
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
        $(document).on('click', '#cetakLaporan', function(e) {
            e.preventDefault()

            var minggu_ke = $('#minggu_ke').val()
            var kelompok = $('#kelompok').val()
            var form = $('#formCetakLaporanAbsensi')

            if (minggu_ke == '' || kelompok == '') {
                return false
            }

            form.submit()
        })
    </script>
@endsection
