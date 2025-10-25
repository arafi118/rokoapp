@extends('inspeksi.layouts.base')
@section('content')
    @include('inspeksi.rencana.form')
    <div class="row mb-2">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <button id="btnTambah" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Tambah Rencana
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table id="rencana" class="table table-hover table-striped table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Rencana Produksi</th>
                                    <th>Rencana Kehadiran</th>
                                    <th>Rencana Karyawan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="FormHapusRencana" method="post">
        @method('DELETE')
        @csrf
    </form>
@endsection
@section('script')
    <script>
        $(function() {
            // Fungsi set tanggal (default hari ini atau dari parameter)
            function setTanggalRencana(tgl = null) {
                let tanggal = tgl || new Date().toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
                $('#tanggal').val(tanggal).datetimepicker('destroy').datetimepicker({
                    i18n: {
                        id: {
                            months: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
                                'Agustus', 'September', 'Oktober', 'November', 'Desember'
                            ],
                            dayOfWeekShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                            dayOfWeek: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                        }
                    },
                    timepicker: false,
                    format: 'd/m/Y',
                    scrollMonth: false,
                    scrollTime: false,
                    scrollInput: false
                });
            }

            // Toast notifikasi
            const toast = Swal.mixin({
                toast: true,
                position: 'top-right',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            // Variabel DOM
            const formContainer = $('#formContainer'),
                formRencana = $('#FormRencana'),
                idRencana = $('#id_rencana'),
                formTitle = $('#formTitle');

            // Inisialisasi DataTable
            const table = $('#rencana').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('/inspeksi/rencana') }}',
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal',
                        width: '20%'
                    },
                    {
                        data: 'rencana_produksi',
                        name: 'rencana_produksi',
                        width: '20%'
                    },
                    {
                        data: 'rencana_kehadiran',
                        name: 'rencana_kehadiran',
                        width: '20%'
                    },
                    {
                        data: 'rencana_karyawan',
                        name: 'rencana_karyawan',
                        width: '20%'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: '20%'
                    }
                ]
            });

            // Tambah
            $('#btnTambah').click(() => {
                formRencana.trigger('reset');
                idRencana.val('');
                formRencana.attr('action', '{{ route('rencana.store') }}')
                    .find('input[name="_method"]').remove();
                formTitle.text("Tambah Rencana Baru");
                formContainer.removeClass("card-warning").addClass("card-primary").slideDown();
                setTanggalRencana();
            });

            // Edit
            $(document).on('click', '.btnEdit', function() {
                let d = $(this).data();
                idRencana.val(d.id);
                $('#rencana_produksi').val(d.rencana_produksi);
                $('#rencana_kehadiran').val(d.rencana_kehadiran);
                $('#rencana_karyawan').val(d.rencana_karyawan);
                formRencana.attr('action', `/inspeksi/rencana/${d.id}`)
                    .find('input[name="_method"]').remove()
                    .end().append('<input type="hidden" name="_method" value="PUT">');
                formTitle.text("Edit Rencana");
                formContainer.removeClass("card-primary").addClass("card-warning").slideDown();
                setTanggalRencana(d.tanggal);
            });

            // Batal
            $('#btnCancel').click(() => formContainer.slideUp());

            // Simpan
            $(document).on('click', '#SimpanRencana', function(e) {
                e.preventDefault();
                $('small').empty();
                $('.is-invalid').removeClass('is-invalid');
                $.post(formRencana.attr('action'), formRencana.serialize())
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

            // Hapus data
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
                            url: `/inspeksi/rencana/${id}`,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: r => {
                                Swal.fire(
                                    r.success ? "Berhasil!" : "Gagal",
                                    r.msg,
                                    r.success ? "success" : "info"
                                ).then(() => {
                                    if (r.success) table.ajax.reload();
                                });
                            },
                            error: xhr => {
                                Swal.fire(
                                    "Error",
                                    xhr.responseJSON?.msg ||
                                    "Terjadi kesalahan pada server.",
                                    "error"
                                );
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
