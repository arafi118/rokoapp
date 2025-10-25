@php
    use App\Utils\Tanggal;

    $tahun = date('Y');

    $date = new DateTime();
    $date->setISODate($tahun, 53);
    $jumlahMinggu = $date->format('W') === '53' ? 53 : 52;
@endphp

@extends('inspeksi.layouts.base')

@section('content')
    <div class="card mb-3">
        <div class="card-body">
            <form action="/inspeksi/laporan-absensi" method="post" id="formCetakLaporanAbsensi" target="_blank">
                @csrf

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="minggu_ke" class="form-label">Pilih Minggu Ke</label>
                            <select id="minggu_ke" name="minggu_ke" class="select2 form-select form-select-lg"
                                style="width: 100%" data-allow-clear="true">
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
                            <label for="tanggal" class="form-label">Pilih Tanggal</label>
                            <select id="tanggal" name="tanggal" class="select2 form-select form-select-lg"
                                style="width: 100%" data-allow-clear="true">
                                <option value="">-- Select Value --</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="kelompok" class="form-label">Kelompok</label>
                            <select id="kelompok" name="kelompok" class="select2 form-select form-select-lg"
                                style="width: 100%" data-allow-clear="true">
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

    <div id="tableAbsen">
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-middle" id="table-absen-harian" style="width: 100%; !important">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Absen Hari Ini</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        const TODAY = '{{ date('Y-m-d') }}'

        var dateStart = ''
        var dateEnd = ''

        let table;

        $(document).on('change', '#minggu_ke', function() {
            $('#minggu').empty().trigger("change");
            var minggu_ke = $('#minggu_ke').val().replace('#', '_')

            if (minggu_ke) {
                dateStart = minggu_ke.split('_')[0]
                dateEnd = minggu_ke.split('_')[1]
            }

            const startDate = new Date(dateStart);
            const endDate = new Date(dateEnd);
            let currentDate = new Date(startDate);

            const dates = [];
            var newOption = new Option("Pilih Tanggal", "", false, false);
            $('#tanggal').append(newOption);
            while (currentDate <= endDate) {
                const formatted = currentDate.toISOString().split('T')[0];

                var newOption = new Option(formatted, formatted, false, false);
                $('#tanggal').append(newOption);

                currentDate.setDate(currentDate.getDate() + 1);
            }
        })

        $(document).on('change', '#tanggal,#kelompok', function() {
            var value = $('#tanggal').val()
            var kelompok = $('#kelompok').val()

            if (value) {
                if (table) {
                    table.destroy()
                }

                table = $('#table-absen-harian').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '/inspeksi/absensi-karyawan/absensi_harian?tanggal=' + value +
                        '&kelompok=' +
                        kelompok,
                    columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'kode_karyawan',
                        name: 'kode_karyawan'
                    }, {
                        data: 'getanggota.nama',
                        name: 'getanggota.nama'
                    }, {
                        data: 'absen',
                        name: 'absen',
                        render: (data, type, row) => {
                            var select = '<select class="form-select input-select" data-id="' +
                                row.id + '" data-tanggal="' + row.tgl_absen +
                                '" name="absen[]">'
                            select +=
                                `<option value="H" ${data == 'H' ? 'selected' : ''}>H</option>`
                            select +=
                                `<option value="S" ${data == 'S' ? 'selected' : ''}>S</option>`
                            select +=
                                `<option value="I" ${data == 'I' ? 'selected' : ''}>I</option>`
                            select +=
                                `<option value="A" ${data == 'A' ? 'selected' : ''}>A</option>`
                            select +=
                                `<option value="T" ${data == 'T' ? 'selected' : ''}>T</option>`
                            select += '</select>';

                            return select;
                        },
                        orderable: false,
                        searchable: false
                    }],
                    "createdRow": function(row, data, dataIndex) {
                        if (data.group_id != data.kelompok) {
                            $(row).find('td').addClass('bg-info');
                        }
                    }
                });
            }
        })

        $(document).on('click', '#SimpanAbsenKaryawan', function(e) {
            e.preventDefault()

            var form = $('#formEditAbsenKaryawan')
            $.ajax({
                url: '/inspeksi/absensi-karyawan/edit',
                type: 'POST',
                data: form.serialize(),
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.msg,
                        icon: 'success',
                    }).then((res) => {
                        $('#modalAbsenKaryawan').modal('hide')
                        table.ajax.reload()
                    })
                }
            })
        })

        $(document).on('change', '.input-select', function(e) {
            e.preventDefault()

            var data = table.row($(this).parents('tr')).data();

            var id_karyawan = data.id
            var tanggal = $(this).data('tanggal')
            var absen = $(this).val()

            var form = $('#formEditAbsenKaryawan')
            $.ajax({
                url: '/inspeksi/absensi-karyawan/edit',
                type: 'POST',
                data: {
                    id_karyawan: id_karyawan,
                    tanggal: tanggal,
                    absen: absen,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.msg,
                        icon: 'success',
                    }).then((res) => {
                        $('#modalAbsenKaryawan').modal('hide')
                        table.ajax.reload()
                    })
                }
            })
        })

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
