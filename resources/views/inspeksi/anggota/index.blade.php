@extends('inspeksi.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="anggota" class="table table-hover table-striped table-bordered" style="width: 100%;">
                    <thead align="center">
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Desa</th>
                            <th>Nama Bank</th>
                            <th>Nomor Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <form action="" method="post" id="FormHapusAnggota">
        @method('DELETE')
        @csrf
    </form>
@endsection
@section('modal')
    <div class="modal fade" id="modalDetailAnggota" tabindex="-1" aria-labelledby="modalDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Anggota</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="kontenDetailAnggota">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: "btn btn-danger fw-bold shadow-sm",
                    cancelButton: "btn btn-secondary me-2"
                },
                buttonsStyling: false
            });

            let tableAnggota = $('#anggota').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ url('/inspeksi/anggota') }}',
                columns: [{
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'nik',
                        name: 'nik'
                    },
                    {
                        data: 'desa',
                        name: 'desa'
                    },
                    {
                        data: 'nama_bank',
                        name: 'nama_bank'
                    },
                    {
                        data: 'norek',
                        name: 'norek'
                    },
                    {
                        data: 'id',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data) {
                            return `
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Aksi
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#" class="dropdown-item text-info d-flex align-items-center gap-2 btn-detail" data-id="${data}">
                                            <i class="bi bi-info-circle text-info"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-warning d-flex align-items-center gap-2" href="/inspeksi/anggota/${data}/edit">
                                            <i class="bi bi-pencil-square text-warning"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item text-danger d-flex align-items-center gap-2 btn-delete" data-id="${data}">
                                            <i class="bi bi-trash text-danger"></i> Hapus
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        `;
                        }
                    }
                ]
            });

            $(document).on('click', '.btn-detail', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                $('#kontenDetailAnggota').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                $('#modalDetailAnggota').modal('show');

                $.ajax({
                    url: `/inspeksi/anggota/${id}/detail`,
                    type: 'GET',
                    success: function(res) {
                        $('#kontenDetailAnggota').html(res);
                    },
                    error: function() {
                        $('#kontenDetailAnggota').html(
                            '<p class="text-danger">Gagal memuat data.</p>');
                    }
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
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.post(`/inspeksi/anggota/${id}`, $('#FormHapusAnggota').serialize(),
                            function(res) {
                                Swal.fire({
                                        title: res.success ? "Berhasil!" : "Gagal",
                                        text: res.msg,
                                        icon: res.success ? "success" : "warning"
                                    })
                                    .then(() => {
                                        if (res.success) tableAnggota.ajax.reload();
                                    });
                            }).fail(() => Swal.fire({
                            title: "Error",
                            text: "Terjadi kesalahan server.",
                            icon: "error"
                        }));
                    }
                });
            });

        });
    </script>
@endsection
