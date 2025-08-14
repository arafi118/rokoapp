@extends('inspeksi.layouts.base')
@section('content')
    @include('inspeksi.level.form')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <button id="btnTambah" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Level
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="level" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Inisial</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="FormHapusLevel" method="post">
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
                formLevel = $('#FormLevel'),
                idLevel = $('#id_level'),
                formTitle = $('#formTitle');
            const table = $('#level').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('/inspeksi/level') }}',
                columns: [{
                        data: 'nama',
                        name: 'nama',
                        width: '30%'
                    },
                    {
                        data: 'inisial',
                        name: 'inisial',
                        width: '15%'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        width: '20%',
                        render: data => `
                    <div class="d-inline-flex gap-1">
                        <button class="btn btn-sm btn-warning btnEdit"
                            data-id="${data.id}"
                            data-nama="${data.nama}"
                            data-inisial="${data.inisial}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>`
                    }
                ]
            });
            // Tambah
            $('#btnTambah').click(() => {
                formLevel.trigger('reset');
                idLevel.val('');
                formLevel.attr('action', '{{ route('level.store') }}');
                formLevel.find('input[name="_method"]').remove();
                formTitle.text("Tambah Level Baru");
                formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
            });
            // Edit
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data();
                idLevel.val(d.id);
                $('#nama').val(d.nama);
                $('#inisial').val(d.inisial);
                formLevel.attr('action', `/inspeksi/level/${d.id}`);
                formLevel.find('input[name="_method"]').remove();
                formLevel.append('<input type="hidden" name="_method" value="PUT">');
                formTitle.text("Edit Level");
                formContainer.removeClass("card-primary").addClass("card-warning").slideDown();
            });
            // Batal
            $('#btnCancel').click(() => formContainer.slideUp());
            // Simpan
            $(document).on('click', '#SimpanLevel', function(e) {
                e.preventDefault();
                $('small').empty();
                $('.is-invalid').removeClass('is-invalid');
                let url = formLevel.attr('action');
                $.post(url, formLevel.serialize())
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
                            url: `/inspeksi/level/${id}`,
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
