@extends('inspeksi.layouts.base')

@section('content')
    @include('inspeksi.meja.form')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <button id="btnTambah" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Meja
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="meja" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Meja</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="FormHapusMeja" method="post">
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
                formMeja = $('#FormMeja'),
                idMeja = $('#id_meja'),
                formTitle = $('#formTitle');

            const table = $('#meja').DataTable({
                processing: true,
                serverSide: true,
                ajax: '/inspeksi/meja',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: '5%',
                    }, {
                        data: 'nama_meja',
                        name: 'nama_meja',
                        width: '40%',
                        className: 'text-center',
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '20%',
                        className: 'text-center',
                        render: data => `
                            <div class="d-inline-flex gap-1">
                                <button class="btn btn-sm btn-warning btnEdit"
                                    data-id="${data.id}"
                                    data-nama="${data.nama}"
                                    data-kode="${data.kode}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>`
                    }
                ]
            });

            // Tambah
            $('#btnTambah').click(() => {
                formMeja.trigger('reset');
                idMeja.val('');
                formMeja.attr('action', '/inspeksi/meja');
                formMeja.find('input[name="_method"]').remove();
                formTitle.text("Tambah Meja Baru");
                formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
            });

            // Edit
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data();
                idMeja.val(d.id);

                $('#nama').val(d.nama);

                formMeja.attr('action', `/inspeksi/meja/${d.id}`);
                formMeja.find('input[name="_method"]').remove();
                formMeja.append('<input type="hidden" name="_method" value="PUT">');

                formTitle.text("Edit Data Meja");
                formContainer.removeClass("card-primary").addClass("card-warning").slideDown();
            });

            // Batal
            $('#btnCancel').click(() => formContainer.slideUp());

            // Simpan
            $(document).on('click', '#SimpanMeja', function(e) {
                e.preventDefault();
                $('small').empty();
                $('.is-invalid').removeClass('is-invalid');

                let url = formMeja.attr('action');
                $.post(url, formMeja.serialize())
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
                    text: "Data meja akan dihapus permanen!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Hapus",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then(res => {
                    if (res.isConfirmed) {
                        $.ajax({
                            url: `/inspeksi/meja/${id}`,
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
