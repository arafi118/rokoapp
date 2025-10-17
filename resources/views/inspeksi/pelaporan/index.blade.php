@extends('inspeksi.layouts.base')

@section('content')
    <style>
        /* Perapian tampilan select2 agar sejajar dengan form-select */
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

        /* Biar card lebih pendek (ngepres) */
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
    </style>

    <div class="card">
        <div class="card-body">
            <form action="/pelaporan/preview" method="GET" target="_blank">
                <div id="laporanRow" class="row g-3 align-items-end mt-2">

                    {{-- Nama Laporan --}}
                    <div id="colLaporan" class="col-md-6">
                        <label class="form-label">Nama Laporan</label>
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
                    <div id="subLaporan" class="col-md-6">
                        <label class="form-label">Nama Sub Laporan</label>
                        @if (request('laporan') == 'calk')
                            <input type="hidden" name="sub_laporan" value="calk_default">
                            <div class="form-control-plaintext">Catatan atas Laporan Keuangan</div>
                        @else
                            <select name="sub_laporan" id="sub_laporan" class="form-select select2">
                                <option value="">---</option>
                                @if (request('sub_laporan'))
                                    <option value="{{ request('sub_laporan') }}" selected>{{ request('sub_laporan') }}
                                    </option>
                                @endif
                            </select>
                        @endif
                    </div>
                </div>

                {{-- Tombol --}}
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
                    $.get(`/app/pelaporan/sub_laporan/${file}`, res => {
                        $('#subLaporan').html(res);
                        $('#subLaporan').find('select').select2({
                            width: '100%'
                        });
                    });
                } else {
                    $('#subLaporan').html('');
                }
            });
        });
    </script>
@endsection
