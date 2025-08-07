@extends('inspeksi.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="anggota" class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIK</th>
                            <th>Desa</th>
                            <th>Nama Bank</th>
                            <th>Nomor Rekening</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
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
                                            <i class="fas fa-eye text-info"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-warning d-flex align-items-center gap-2" href="/inspeksi/anggota/${data}/edit">
                                            <i class="fas fa-edit text-warning"></i> Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item text-danger d-flex align-items-center gap-2 btn-delete" data-id="${data}">
                                            <i class="fas fa-trash-alt text-danger"></i> Hapus
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        `;
                        }
                    }
                ]
            });
        });
    </script>
@endsection
