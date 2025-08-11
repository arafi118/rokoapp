@extends('inspeksi.layouts.base')
@section('content')
    @include('inspeksi.kelompok.form')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <button id="btnTambah" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Kelompok
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="kelompok" class="table table-hover table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Anggota</th>
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

    <form id="FormHapusKelompok" method="post">
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
                formKelompok = $('#FormKelompok'),
                idKelompok = $('#id_kelompok'),
                formTitle = $('#formTitle');
            const table = $('#kelompok').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('/inspeksi/kelompok') }}',
                columns: [{
                        data: 'nama_anggota',
                        name: 'nama_anggota'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
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
                                data-anggota_id="${data.anggota_id}"
                                data-nama_anggota="${data.nama_anggota}"
                                data-nama="${data.nama}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${data.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>`
                    }
                ]
            });

            // select Anggota
            $('#anggota_id').select2({
                theme: 'bootstrap4',
                placeholder: '-- Pilih Anggota --',
                ajax: {
                    url: '/inspeksi/Kelompok/listAnggota',
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term,
                        mode: $('#id_kelompok').val() ? 'edit' : 'tambah', // cek mode
                        current_id: $('#anggota_id').val() // kirim anggota_id saat edit
                    }),
                    processResults: data => ({
                        results: data.map(item => ({
                            id: item.id,
                            text: item.nama
                        }))
                    }),
                    cache: true
                }
            });

            // Tambah
            $('#btnTambah').click(() => {
                formKelompok.trigger('reset');
                idKelompok.val('');
                $('#anggota_id').val(null).trigger('change');
                formKelompok.attr('action', '{{ route('kelompok.store') }}');
                formKelompok.find('input[name="_method"]').remove();
                formTitle.text("Tambah Kelompok Baru");
                formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
            });

            // Edit
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data();
                idKelompok.val(d.id);
                $('#nama').val(d.nama);
                if (d.anggota_id && d.nama_anggota) {
                    let option = new Option(d.nama_anggota, d.anggota_id, true, true);
                    $('#anggota_id').append(option).trigger('change');
                }
                formKelompok.attr('action', `/inspeksi/kelompok/${d.id}`);
                formKelompok.find('input[name="_method"]').remove();
                formKelompok.append('<input type="hidden" name="_method" value="PUT">');
                formTitle.text("Edit Kelompok");
                formContainer.removeClass("card-primary").addClass("card-warning").slideDown();
            });

            // Batal
            $('#btnCancel').click(() => formContainer.slideUp());

            // Simpan
            $(document).on('click', '#SimpanKelompok', function(e) {
                e.preventDefault();
                $('small').empty();
                $('.is-invalid').removeClass('is-invalid');
                let url = formKelompok.attr('action');
                $.post(url, formKelompok.serialize())
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
                        $.each(r.errors || {}, (k, m) => {
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
                            url: `/inspeksi/kelompok/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(r) {
                                Swal.fire(
                                    r.success ? "Berhasil!" : "Gagal",
                                    r.message,
                                    r.success ? "success" : "info"
                                ).then(() => {
                                    if (r.success) {
                                        table.ajax.reload(null, false);
                                    }
                                });
                            },
                            error: function(xhr) {
                                let msg = xhr.responseJSON?.message ||
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
