@extends('inspeksi.layouts.base')

@section('content')
    @include('inspeksi.karyawan.form')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <button id="btnCetakSP" class="btn btn-info btn-sm">
                            <i class="bi bi-plus-circle"></i> Cetak SP
                        </button>
                        <button id="btnTambah" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Karyawan
                        </button>
                    </div>
                    <div class="table-responsive pt-3">
                        <table id="karyawan" class="table table-hover table-striped table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>
                                        <div class="icheck-primary d-inline">
                                            <input type="checkbox" id="checkAll">
                                            <label for="checkAll">
                                            </label>
                                        </div>
                                    </th>
                                    <th>Nama Karyawan</th>
                                    <th>Kode Karyawan</th>
                                    <th>Group Karyawan</th>
                                    <th>Tanggal Masuk</th>
                                    <th>Level</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="FormHapusKaryawan" method="post">
        @method('DELETE')
        @csrf
    </form>
@endsection

@section('script')
    <script>
        $(function() {
            const toast = Swal.mixin({
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            const formContainer = $('#formContainer'),
                formKaryawan = $('#FormKaryawan'),
                idKaryawan = $('#id_karyawan'),
                formTitle = $('#formTitle');

            const table = $('#karyawan').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/inspeksi/karyawan/data?level_karyawan=1&status=aktif',
                columns: [{
                        data: 'id',
                        name: 'id',
                        orderable: false,
                        searchable: false,
                        render: (data, type, row) => {
                            return `
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" name="id[]" id="id-${data}" class="id" value="${data}">
                                    <label for="id-${data}">
                                    </label>
                                </div>`
                        }
                    }, {
                        data: 'getanggota.nama',
                        name: 'getanggota.nama',
                        render: function(data, type, row) {
                            let badge = '';
                            let status = (row.status_karyawan || '').toLowerCase();

                            if (status === 'aktif') {
                                badge = '<span class="badge bg-success ms-2">Aktif</span>';
                            } else if (status === 'nonaktif') {
                                badge = '<span class="badge bg-danger ms-2">Nonaktif</span>';
                            } else {
                                badge =
                                    '<span class="badge bg-secondary ms-2">Tidak Diketahui</span>';
                            }

                            return `${data} ${badge}`;
                        }
                    },
                    {
                        data: 'kode_karyawan',
                        name: 'kode_karyawan'
                    },
                    {
                        data: 'getgroup.nama',
                        name: 'getgroup.nama',
                        defaultContent: '-'
                    },
                    {
                        data: 'tanggal_masuk',
                        name: 'tanggal_masuk'
                    },
                    {
                        data: 'getlevel.nama',
                        name: 'getlevel.nama'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: data => `
                            <div class="d-inline-flex gap-1">
                                <button class="btn btn-sm btn-warning btnEdit"
                                    data-id="${data.id}"
                                    data-nama="${data.getanggota?.nama || ''}"
                                    data-nama_karyawan="${data.getanggota?.nama || ''}"
                                    data-group="${data.getgroup ? data.getgroup.id : ''}"
                                    data-group_nama="${data.getgroup ? data.getgroup.nama : ''}"
                                    data-level="${data.getlevel ? data.getlevel.id : ''}"
                                    data-level_nama="${data.getlevel ? data.getlevel.nama : ''}"
                                    data-meja="${data.getmeja ? data.getmeja.id : ''}"
                                    data-meja_nama="${data.getmeja ? data.getmeja.nama_meja : ''}"
                                    data-tanggal="${data.tanggal_masuk || ''}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>`
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });

            const filterWrapper = $('#karyawan_filter');
            filterWrapper.empty();

            filterWrapper.append(`
                <div class="input-group">
                <label class="input-group-text" for="search-karyawan">Cari</label>
                <input type="text" id="search-karyawan" class="form-control" placeholder="Cari...">
                <select class="form-select" id="status-karyawan">
                    <option value="all">Semua</option>
                    <option value="aktif" selected>Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>
                </div>
            `);

            $('#search-karyawan').on('keyup', function() {
                table.search(this.value).draw();
            });

            $('#status-karyawan').on('change', function() {
                const val = this.value;
                table.ajax.url(`/inspeksi/karyawan/data?level_karyawan=1&status=${val}`).load();
            });

            $(document).on('change', '#checkAll', function() {
                if ($(this).is(':checked')) {
                    $('.id').prop('checked', true);
                } else {
                    $('.id').prop('checked', false);
                }
            })

            $('#group_id').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Group --',
                allowClear: true,
                ajax: {
                    url: '/inspeksi/karyawan/list-group',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            $('#meja_id').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Posisi Meja --',
                allowClear: true,
                ajax: {
                    url: '/inspeksi/karyawan/list-meja',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            $('#anggota_id').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Anggota --',
                allowClear: true,
                ajax: {
                    url: '/inspeksi/karyawan/list-anggota',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            $('#level_id').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Level --',
                allowClear: true,
                ajax: {
                    url: '/inspeksi/karyawan/list-level',
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data
                    }),
                    cache: true
                }
            });

            $(document).on('click', '#btnCetakSP', function(e) {
                e.preventDefault();

                var checkbox = $('.id:checked');
                var id = [];
                checkbox.each(function() {
                    id.push($(this).val());
                });

                paramsId = id.join(',');
                window.open(`/inspeksi/karyawan/cetak-sp?id_karyawan=${paramsId}`, '_blank');
            })

            // Tombol Tambah
            $('#btnTambah').click(() => {
                formKaryawan.trigger('reset');
                idKaryawan.val('');
                $('#group_id, #meja_id, #anggota_id, #level_id').val(null).trigger('change');
                formKaryawan.attr('action', '/inspeksi/karyawan');
                formKaryawan.find('input[name="_method"]').remove();
                formTitle.text("Tambah Karyawan Baru");
                formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
                $('.tanggal-keluar-wrapper').hide();

                $('#tanggal_masuk').prop('disabled', false);
                $('#anggota_id').prop('disabled', false);
            });

            // Tombol Edit
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data();
                idKaryawan.val(d.id);
                $('#tanggal_masuk').val(d.tanggal).prop('disabled', true);

                formContainer.slideDown();
                $('.tanggal-keluar-wrapper').show();

                $('#group_id').empty().append(new Option(d.group_nama, d.group, true, true)).trigger(
                    'change');
                $('#anggota_id').empty().append(new Option(d.nama_karyawan, d.anggota, true, true))
                    .trigger('change').prop('disabled', true);
                $('#meja_id').empty().append(new Option(d.meja_nama, d.meja, true, true)).trigger('change');
                $('#level_id').empty().append(new Option(d.level_nama, d.level, true, true)).trigger(
                    'change');

                formKaryawan.attr('action', `/inspeksi/karyawan/${d.id}`);
                formKaryawan.find('input[name="_method"]').remove();
                formKaryawan.append('<input type="hidden" name="_method" value="PUT">');

                formTitle.text("Edit Karyawan");
                formContainer.removeClass("card-primary").addClass("card-warning");
            });

            jQuery.datetimepicker.setLocale('de');
            $('.date').datetimepicker({
                i18n: {
                    id: {
                        months: [
                            'Januari', 'Februari', 'Maret', 'April',
                            'Mei', 'Juni', 'Juli', 'Agustus',
                            'September', 'Oktober', 'November', 'Desember'
                        ],
                        dayOfWeekShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                        dayOfWeek: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                    }
                },
                timepicker: false,
                format: 'd/m/Y'
            });

            $('#btnCancel').click(() => formContainer.slideUp());

            $(document).on('click', '#SimpanKaryawan', function(e) {
                e.preventDefault();
                $('small').empty();
                $('.is-invalid').removeClass('is-invalid');
                let url = formKaryawan.attr('action');
                $.post(url, formKaryawan.serialize())
                    .done(res => {
                        toast.fire({
                            icon: 'success',
                            title: res.msg
                        });
                        formContainer.slideUp();
                        table.ajax.reload(null, false);
                    })
                    .fail(err => {
                        let r = err.responseJSON || {};
                        toast.fire({
                            icon: 'error',
                            title: r.msg || 'Terjadi kesalahan'
                        });
                        $.each(r, (k, m) => {
                            $('#' + k).addClass('is-invalid');
                            $('#msg_' + k).html(m[0]);
                        });
                    });
            });

            $(document).on('click', '.btn-delete', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                Swal.fire({
                    title: "Apakah Anda yakin?",
                    text: "Data akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Hapus",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then(res => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: `/inspeksi/karyawan/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(r) {
                                Swal.fire(r.success ? "Berhasil!" : "Gagal", r.msg, r
                                        .success ? "success" : "info")
                                    .then(() => {
                                        if (r.success) table.ajax.reload(null,
                                            false);
                                    });
                            },
                            error: function(xhr) {
                                let msg = xhr.responseJSON?.msg ||
                                    "Terjadi kesalahan pada server.";
                                Swal.fire("Error", msg, "error");
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
