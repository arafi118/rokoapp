@extends('inspeksi.layouts.base')
@section('content')
    @include('inspeksi.group.form')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <button id="btnTambah" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Group
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="group" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Mandor</th>
                                    <th>Nama Kelompok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="FormHapusGroup" method="post">
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
                formGroup = $('#FormGroup'),
                idGroup = $('#id_group'),
                formTitle = $('#formTitle');

            const table = $('#group').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/inspeksi/group',
                columns: [{
                        data: 'getmandor.nama',
                        name: 'getmandor.nama'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: data => `
                            <div class="d-inline-flex gap-1">
                                <button class="btn btn-sm btn-warning btnEdit"
                                    data-id="${data.id}"
                                    data-nama="${data.nama}"
                                    data-mandor="${data.getmandor ? data.getmandor.id : ''}"
                                    data-mandor_nama="${data.getmandor ? data.getmandor.nama : ''}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>`
                    }
                ]
            });

            // Inisialisasi Select2 untuk Mandor
            $('#mandor').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Mandor --',
                allowClear: true,
                ajax: {
                    url: '/inspeksi/group/list',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true
                }
            });

            // Tambah
            $('#btnTambah').click(() => {
                formGroup.trigger('reset');
                idGroup.val('');
                formGroup.attr('action', '/inspeksi/group');
                formGroup.find('input[name="_method"]').remove();
                formTitle.text("Tambah Group Baru");
                formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
            });

            // Edit
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data();

                $('#id_group').val(d.id);
                $('#nama').val(d.nama);

                $('#mandor').find('option').remove();
                if (d.mandor) {
                    let option = new Option(d.mandor_nama, d.mandor, true, true);
                    $('#mandor').append(option).trigger('change');
                } else {
                    $('#mandor').val(null).trigger('change');
                }

                $('#FormGroup').attr('action', `/inspeksi/group/${d.id}`);
                $('#FormGroup').find('input[name="_method"]').remove();
                $('#FormGroup').append('<input type="hidden" name="_method" value="PUT">');

                $('#formTitle').text("Edit Group");
                $('#formContainer').removeClass("card-primary").addClass("card-warning").slideDown();
            });

            // Batal
            $('#btnCancel').click(() => formContainer.slideUp());

            // Simpan
            $(document).on('click', '#SimpanGroup', function(e) {
                e.preventDefault();
                $('small').empty();
                $('.is-invalid').removeClass('is-invalid');

                let url = formGroup.attr('action');
                $.post(url, formGroup.serialize())
                    .done(res => {
                        toast.fire({
                            icon: 'success',
                            title: res.msg
                        });
                        formContainer.slideUp();
                        table.ajax.reload();
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

            // Hapus
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
                            url: `/inspeksi/group/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(r) {
                                Swal.fire(r.success ? "Berhasil!" : "Gagal", r.msg, r
                                        .success ? "success" : "info")
                                    .then(() => {
                                        if (r.success) table.ajax.reload();
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
