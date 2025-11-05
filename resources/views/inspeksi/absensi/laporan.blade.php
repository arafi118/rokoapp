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
                            <th>Plan</th>
                            <th>Actual</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalAbsenKaryawan" tabindex="-1" aria-labelledby="modalAbsenKaryawanLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalAbsenKaryawanLabel">
                        Edit Absen Karyawan
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body row align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <img src="/assets/img/user.png" alt="Foto Profil" class="rounded-circle img-fluid"
                                    style="max-width: 180px;">
                                <h5 class="mt-3 fw-bold mb-0">
                                    <span id="nama-anggota"></span>
                                </h5>
                                <h6 class="text-muted">(<span id="nik-anggota"></span>)</h6>
                            </div>

                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-birthday-cake text-danger me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="tempat-lahir"></span>,
                                                        <span id="tanggal-lahir"></span>
                                                    </div>
                                                    <small class="text-muted">Tempat & Tanggal Lahir</small>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-graduation-cap text-primary me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="ijazah"></span> - <span
                                                            id="jurusan"></span>
                                                        (<span id="tahun-lulus"></span>)</div>
                                                    <small class="text-muted">Pendidikan Terakhir</small>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-university text-success me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="nama-bank"></span> - <span
                                                            id="norek"></span>
                                                    </div>
                                                    <small class="text-muted">Informasi Bank</small>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-venus-mars text-secondary me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold">
                                                        <span id="jenis-kelamin"></span>
                                                    </div>
                                                    <small class="text-muted">Jenis Kelamin</small>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-praying-hands text-warning me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="agama"></span></div>
                                                    <small class="text-muted">Agama</small>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-heart text-danger me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="status"></span></div>
                                                    <small class="text-muted">Status</small>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-female text-pink me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="nama-ibu"></span></div>
                                                    <small class="text-muted">Nama Ibu Kandung</small>
                                                </div>
                                            </li>
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-ruler-vertical text-dark me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="tinggi-badan"></span> cm /
                                                        <span id="berat-badan"></span> kg
                                                    </div>
                                                    <small class="text-muted">Tinggi & Berat Badan</small>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>

                                    <div class="col-md-12">
                                        <ul class="list-unstyled">
                                            <li class="d-flex align-items-start mb-2">
                                                <i class="fas fa-map-marker-alt text-info me-2 mt-1 fs-5"></i>
                                                <div>
                                                    <div class="fw-bold"><span id="alamat"></span>, <span
                                                            id="desa"></span>,
                                                        <span id="kecamatan"></span>, <span id="kota"></span>
                                                    </div>
                                                    <small class="text-muted">Alamat Lengkap</small>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="SimpanAbsenKaryawan">Simpan</button>
                </div>
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
            $('#tanggal').empty().trigger("change");
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
                        name: 'kode_karyawan',
                        render: (data, type, row) => {
                            return `<span class="detail cursor-pointer">${data}</span>`;
                        }
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
                    }, {
                        data: 'id',
                        name: 'id',
                        render: (data, type, row) => {
                            return `<input type="number" data-id="${data}" data-tanggal="${row.tgl_absen}" class="form-control input-plan" value="${row.plan}">`
                        },
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'id',
                        name: 'id',
                        render: (data, type, row) => {
                            var jumlahProduksi = 0;
                            row.getproduksi.forEach(produksi => {
                                jumlahProduksi += parseInt(produksi.jumlah_baik);
                            })

                            return `<input type="number" data-id="${data}" data-tanggal="${row.tgl_absen}" class="form-control input-actual" value="${jumlahProduksi}">`
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

        $(document).on('change', '.input-plan', function(e) {
            e.preventDefault()

            var data = table.row($(this).parents('tr')).data();
            var id = $(this).data('id')
            var tanggal = $(this).data('tanggal')
            var plan = $(this).val()

            $.ajax({
                url: '/inspeksi/absensi-karyawan/input-plan',
                type: 'POST',
                data: {
                    id: id,
                    tanggal: tanggal,
                    plan: plan,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.msg,
                        icon: 'success',
                    }).then((res) => {

                    })
                }
            })
        })

        $(document).on('change', '.input-actual', function(e) {
            e.preventDefault()

            var data = table.row($(this).parents('tr')).data();
            var id = $(this).data('id')
            var tanggal = $(this).data('tanggal')
            var actual = $(this).val()

            $.ajax({
                url: '/inspeksi/absensi-karyawan/input-actual',
                type: 'POST',
                data: {
                    id: id,
                    tanggal: tanggal,
                    actual: actual,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.msg,
                        icon: 'success',
                    }).then((res) => {

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

                    })
                }
            })
        })

        $(document).on('click', '.detail', function(e) {
            e.preventDefault()

            var data = table.row($(this).parents('tr')).data();

            $('#nama-anggota').text(data.getanggota.nama)
            $('#nik-anggota').text(data.getanggota.nik)

            $('#tempat-lahir').text(data.getanggota.tempat_lahir)
            $('#tanggal-lahir').text(data.getanggota.tanggal_lahir)
            $('#ijazah').text(data.getanggota.ijazah)
            $('#jurusan').text(data.getanggota.jurusan)
            $('#tahun-lulus').text(data.getanggota.tahun_lulus)
            $('#nama-bank').text(data.getanggota.nama_bank)
            $('#norek').text(data.getanggota.norek)
            $('#jenis-kelamin').text(data.getanggota.jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan')

            $('#agama').text(data.getanggota.agama)
            $('#status').text(data.getanggota.status)
            $('#nama-ibu').text(data.getanggota.nama_ibu)
            $('#tinggi-badan').text(data.getanggota.tinggi_badan)
            $('#berat-badan').text(data.getanggota.berat_badan)

            $('#alamat').text(data.getanggota.alamat)
            $('#desa').text(data.getanggota.desa)
            $('#kecamatan').text(data.getanggota.kecamatan)
            $('#kota').text(data.getanggota.kota)

            $('#modalAbsenKaryawan').modal('show')
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
