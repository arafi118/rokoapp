@php
    use App\Utils\Tanggal;

    $tahun = date('Y');

    $date = new DateTime();
    $date->setISODate($tahun, 53);
    $jumlahMinggu = $date->format('W') === '53' ? 53 : 52;
@endphp
@extends(Request::segment(1) . '.layouts.base')

@section('content')
    <style>
        .select2-container .select2-selection--single {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            display: flex;
            align-items: center;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding-left: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }

        .card-body {
            padding: 0.75rem 1rem !important;
        }

        .row.g-3 {
            --bs-gutter-y: 0.5rem;
        }

        .form-label {
            margin-bottom: 0.2rem !important;
            font-size: 0.9rem;
        }

        .btn {
            padding: 4px 12px;
            font-size: 0.875rem;
        }

        .select2-container--bootstrap4 .select2-results__option {
            background-color: #fff !important;
            color: #333 !important;
        }

        .select2-container--bootstrap4 .select2-results__option--highlighted {
            background-color: #ff8800 !important;
            /* warna oranye aktif */
            color: #fff !important;
        }

        .select2-dropdown {
            background-color: #fff !important;
            border-color: #ccc !important;
        }

        .select2-search__field {
            background-color: #fff !important;
            color: #333 !important;
        }
    </style>

    <div class="card">
        <div class="card-body">
            <form action="/{{ Request::segment(1) }}/pelaporan/preview" method="GET" target="_blank">
                <div id="laporanRow" class="row g-3 align-items-end mt-2">
                    <div class="row g-3">
                        {{-- Tahun --}}
                        <div class="col-md-4">
                            <label class="form-label">Pilih Tahun</label>
                            <select name="tahun" class="form-select select2">
                                @for ($i = 2020; $i <= date('Y'); $i++)
                                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                        {{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Bulan --}}
                        <div class="col-md-4">
                            <label class="form-label"> Pilih Bulan</label>
                            <select name="bulan" class="form-select select2">
                                @foreach ([
            '01' => 'JANUARI',
            '02' => 'FEBRUARI',
            '03' => 'MARET',
            '04' => 'APRIL',
            '05' => 'MEI',
            '06' => 'JUNI',
            '07' => 'JULI',
            '08' => 'AGUSTUS',
            '09' => 'SEPTEMBER',
            '10' => 'OKTOBER',
            '11' => 'NOVEMBER',
            '12' => 'DESEMBER',
        ] as $num => $name)
                                    <option value="{{ $num }}" {{ $num == date('m') ? 'selected' : '' }}>
                                        {{ $num }}.
                                        {{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3" style="padding-right: 10px;">
                            <label for="minggu_ke" class="form-label">Pilih Minggu</label>
                            <select id="minggu_ke" name="minggu_ke" class="form-select select2">
                                <option value="">---</option>
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

                        <div class="col-md-4 mb-3" style="padding-right: 10px;">
                            <label class="form-label">Pilih Hari</label>
                            <select name="hari" class="form-select select2">
                                <option value="">---</option>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                @endfor
                            </select>
                        </div>

                        <div id="colLaporan" class="col-md-4 mb-3" style="padding-left: 10px;">
                            <label class="form-label"> Pilih Nama Laporan</label>
                            <select id="laporan" name="laporan" class="form-select select2">
                                <option value="">---</option>
                                @foreach ($laporan as $item)
                                    <option value="{{ $item->file }}"
                                        {{ request('laporan') == $item->file ? 'selected' : '' }}>
                                        {{ $item->nama_laporan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sub Laporan --}}
                        <div id="subLaporan" class="col-md-4">
                            <label class="form-label"> Pilih Nama Sub Laporan</label>
                            <select name="sub_laporan" id="sub_laporan" class="form-select select2">
                                <option value="">---</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="submit" name="action" value="excel" class="btn btn-success">Excel</button>
                            <button type="submit" name="action" value="preview" class="btn btn-primary">Preview</button>
                        </div>
                    </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('.select2').select2({
                width: '100%'
            });

            $('#laporan').on('change', function() {
                const file = $(this).val();
                if (file) {
                    $.get(`/{{ Request::segment(1) }}/pelaporan/sub_laporan/${file}`, res => {
                        $('#subLaporan').html(res);
                        $('#subLaporan').find('select').select2({
                            width: '100%'
                        });
                    });
                } else {
                    $('#subLaporan').html(`
                    <label class="form-label">Nama Sub Laporan</label>
                    <select name="sub_laporan" id="sub_laporan" class="form-select select2">
                        <option value="">---</option>
                    </select>
                `);
                }
            });
        });
        // === FILTER MINGGU BERDASARKAN BULAN ===
        $(document).ready(function() {
            const $bulan = $('select[name="bulan"]');
            const $minggu = $('#minggu_ke');

            // Simpan semua opsi minggu awal (karena nanti mau difilter)
            const semuaOpsiMinggu = $minggu.find('option').clone();

            function filterMinggu() {
                const bulanDipilih = $bulan.val();
                $minggu.empty().append('<option value="">---</option>');

                semuaOpsiMinggu.each(function() {
                    const val = $(this).val();
                    if (!val.includes('#')) return; // skip opsi kosong
                    const [start, end] = val.split('#');
                    const startDate = new Date(start);
                    const endDate = new Date(end);

                    let adaDiBulan = false;
                    const temp = new Date(startDate);
                    while (temp <= endDate) {
                        if ((temp.getMonth() + 1).toString().padStart(2, '0') === bulanDipilih) {
                            adaDiBulan = true;
                            break;
                        }
                        temp.setDate(temp.getDate() + 1);
                    }

                    if (adaDiBulan) {
                        $minggu.append($(this).clone());
                    }
                });

                // refresh select2 setelah diubah
                $minggu.select2({
                    width: '100%'
                });
            }

            // panggil awal sesuai bulan berjalan
            filterMinggu();

            // jalankan setiap kali bulan diganti
            $bulan.on('change', function() {
                filterMinggu();
            });
        });
    </script>
@endsection
