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
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="minggu_ke" class="form-label">Pilih Minggu</label>
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
                <table class="table table-striped" id="table-absen-harian" style="width: 100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Absen Hari Ini</th>
                            <th></th>
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

                    <div class="card mb-4 shadow-sm">
                        <div class="card-body">
                            <form id="formEditAbsenKaryawan">
                                @csrf

                                <input type="hidden" id="id_karyawan" name="id_karyawan">
                                <table class="table table-bordered" style="width: 100%;" id="absen-harian">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Senin</th>
                                            <th class="text-center">Selasa</th>
                                            <th class="text-center">Rabu</th>
                                            <th class="text-center">Kamis</th>
                                            <th class="text-center">Jumat</th>
                                            <th class="text-center">Sabtu</th>
                                            <th class="text-center">Minggu</th>
                                        </tr>
                                        <tr id="daftarTanggal"></tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </form>
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

        $(document).on('change', '#minggu_ke, #kelompok', function() {
            var minggu_ke = $('#minggu_ke').val().replace('#', '_')
            var kelompok = $('#kelompok').val()

            if (minggu_ke) {
                dateStart = minggu_ke.split('_')[0]
                dateEnd = minggu_ke.split('_')[1]
            }

            if (table) {
                table.destroy()
            }

            table = $('#table-absen-harian').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/inspeksi/absensi-karyawan/absensi_mingguan?minggu_ke=' + minggu_ke + '&kelompok=' +
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
                    data: 'id',
                    name: 'id',
                    render: (data, type, row) => {
                        const absen = row.getabsensi?.find(a => a.tanggal === TODAY);
                        return absen ? absen.status : '-';
                    },
                    orderable: false,
                    searchable: false
                }, {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                }]
            });
        })

        $(document).on('click', '.btn-detail', function(e) {
            e.preventDefault()

            var data = table.row($(this).parents('tr')).data();

            const startDate = new Date(dateStart);
            const endDate = new Date(dateEnd);
            let currentDate = new Date(startDate);

            const dates = [];
            while (currentDate <= endDate) {
                const formatted = currentDate.toISOString().split('T')[0];
                dates.push(formatted);

                currentDate.setDate(currentDate.getDate() + 1);
            }

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

            var tanggalTR = $('#daftarTanggal').html('')
            dates.forEach(date => {
                tanggalTR.append(`<th class="text-center">${date}</th>`)
            })

            var tbody = $('#absen-harian tbody').html('')
            var tr = $('<tr>')
            dates.forEach(date => {
                var status = 'A';
                const absen = data.getabsensi.find(a => a.tanggal === date);
                if (absen) {
                    status = absen.status;
                }

                var select = '<select class="form-select" name="absen[' + date + ']">'
                select += `<option value="H" ${status == 'H' ? 'selected' : ''}>H</option>`
                select += `<option value="S" ${status == 'S' ? 'selected' : ''}>S</option>`
                select += `<option value="I" ${status == 'I' ? 'selected' : ''}>I</option>`
                select += `<option value="A" ${status == 'A' ? 'selected' : ''}>A</option>`
                select += `<option value="T" ${status == 'T' ? 'selected' : ''}>T</option>`
                select += '</select>';

                tr.append(`<td>${select}</td>`)
            })
            tbody.append(tr)

            $('#id_karyawan').val(data.id)
            $('#modalAbsenKaryawan').modal('show')
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
