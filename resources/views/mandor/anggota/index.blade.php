@extends('mandor.layouts.base')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="daftar-anggota">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-left">NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Tempat, Tanggal Lahir</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailAnggota" tabindex="-1" aria-labelledby="modalDetailAnggotaLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalDetailAnggotaLabel">Detail Anggota</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="col-12 d-flex align-items-stretch flex-column">
                        <div class="card bg-light d-flex flex-fill">
                            <div class="card-header text-muted border-bottom-0 text-capitalize">
                                <span id="namaLevel"></span>
                            </div>
                            <div class="card-body pt-0">
                                <div class="row">
                                    <div class="col-7">
                                        <h2 class="lead">
                                            <b id="namaAnggota"></b>
                                        </h2>
                                        <p class="text-muted text-sm">
                                            <b>NIK: </b> <span id="nikAnggota"></span>
                                        </p>
                                        <ul class="ml-4 mb-0 fa-ul text-muted">
                                            <li class="small">
                                                <span class="fa-li">
                                                    <i class="fas fa-lg fa-building"></i>
                                                </span>
                                                Alamat : <span id="alamatAnggota"></span>
                                            </li>
                                            <li class="small">
                                                <span class="fa-li">
                                                    <i class="fas fa-lg fa-phone"></i>
                                                </span>
                                                Tempat, Tanggal Lahir : <span id="ttlAnggota"></span>
                                            </li>
                                            <li class="small">
                                                <span class="fa-li">
                                                    <i class="fas fa-lg fa-phone"></i>
                                                </span>
                                                Masa Kerja : <span id="masaKerja"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-5 text-center">
                                        <img src="" alt="user-avatar" id="fotoAnggota"
                                            class="rounded-circle img-fluid">
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        var table = $('#daftar-anggota').DataTable({
            processing: true,
            serverSide: true,
            ajax: "/mandor/anggota",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'nik',
                    name: 'nik'
                },
                {
                    data: 'nama',
                    name: 'nama'
                },
                {
                    data: 'tempat_lahir',
                    name: 'tempat_lahir'
                },
                {
                    data: 'aksi',
                    name: 'aksi',
                    orderable: false,
                    searchable: false
                },
            ]
        })

        $('#daftar-anggota').on('click', '.btn-detail', function() {
            var data = table.row($(this).parents('tr')).data();

            $('#namaLevel').text(data.level);
            $('#namaAnggota').text(data.nama);
            $('#nikAnggota').text(data.nik);
            $('#alamatAnggota').text(data.alamat);
            $('#ttlAnggota').text(data.tempat_lahir);
            $('#fotoAnggota').attr('src', (data.foto.length > 3) ? '/storage/profil/' + data.foto :
                '/assets/img/default-150x150.png');
            $('#masaKerja').text(data.masa_kerja);

            $('#modalDetailAnggota').modal('show');
        })
    </script>
@endsection
