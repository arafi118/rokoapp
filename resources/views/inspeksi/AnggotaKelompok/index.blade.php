    @extends('inspeksi.layouts.base')
    @section('content')
        @include('inspeksi.AnggotaKelompok.form')
        <div class="row mb-2">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <button id="btnTambah" class="btn btn-success btn-sm">
                                <i class="bi bi-plus-circle"></i> Tambah Anggota Kelompok
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table id="AnggotaKelompok" class="table table-hover table-striped table-bordered">
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

        <form id="FormHapusAnggotaKelompok" method="post">
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

                // Ambil elemen-elemen form
                const formContainer = $('#formContainer');
                const formAnggotaKelompok = $('#formAnggotaKelompok');
                const idKelompok = $('<input type="hidden" id="id_kelompok" name="id_kelompok">');
                formAnggotaKelompok.append(idKelompok); // kalau belum ada di form
                const formTitle = $('#formTitle');

                const table = $('#AnggotaKelompok').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ url('/inspeksi/AnggotaKelompok') }}',
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
                                data-anggota_level_id="${data.anggota_level_id}"
                                data-kelompok_id="${data.kelompok_id}">
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
                $('#anggota_level_id').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Pilih Anggota --',
                    ajax: {
                        url: '/inspeksi/AnggotaKelompok/listAnggota',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term
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
                // select Kelompok
                $('#kelompok_id').select2({
                    theme: 'bootstrap4',
                    placeholder: '-- Pilih Kelompok --',
                    ajax: {
                        url: '/inspeksi/AnggotaKelompok/listKelompok',
                        dataType: 'json',
                        delay: 250,
                        data: params => ({
                            q: params.term
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
                    formAnggotaKelompok.trigger('reset');
                    idKelompok.val('');
                    formAnggotaKelompok.attr('action', '{{ route('AnggotaKelompok.store') }}');
                    formAnggotaKelompok.find('input[name="_method"]').remove();
                    formTitle.text("Tambah Anggota Kelompok Baru");
                    formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
                });

                // Edit
                $(document).on('click', '.btnEdit', function() {
                    let d = $(this).data();
                    idKelompok.val(d.id);
                    $('#anggota_level_id').val(d.anggota_level_id);
                    $('#kelompok_id').val(d.kelompk_id);
                    formAnggotaKelompok.attr('action', `/inspeksi/AnggotaKelompok/${d.id}`);
                    formAnggotaKelompok.find('input[name="_method"]').remove();
                    formAnggotaKelompok.append('<input type="hidden" name="_method" value="PUT">');
                    formTitle.text("Edit Anggota Kelompok");
                    formContainer.removeClass("card-primary").addClass("card-warning").slideDown();
                });

                // Batal
                $('#btnCancel').click(() => formContainer.slideUp());

                // Simpan
                $(document).on('click', '#SimpanKelompok', function(e) {
                    e.preventDefault();
                    $('small').empty();
                    $('.is-invalid').removeClass('is-invalid');
                    let url = formAnggotaKelompok.attr('action');
                    $.post(url, formAnggotaKelompok.serialize())
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
                                url: `/inspeksi/AnggotaKelompok/${id}`,
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
