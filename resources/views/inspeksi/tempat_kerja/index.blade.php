@extends('inspeksi.layouts.base')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tab-content">
                <div class="tab-pane show active" role="tabpanel">
                    <div class="row">
                        @foreach ($dataKaryawan as $groupId => $group)
                            <div class="col-md-12 mb-3">
                                <div class="card shadow-sm">
                                    {{-- <div class="card-header bg-primary text-white fw-bold">
                                        {{ $group['group_name'] }}
                                    </div> --}}
                                    <div class="card-body">
                                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                                            @foreach ($group['meja'] as $mejaId => $mejaGroup)
                                                @php
                                                    $warna =
                                                        rand(100, 255) . ',' . rand(100, 255) . ',' . rand(100, 255);
                                                @endphp
                                                <div class="card border">
                                                    {{-- <div class="card-header bg-light fw-semibold">
                                                        Meja {{ $mejaGroup['meja_id'] }}
                                                    </div> --}}
                                                    <div class="card-body">
                                                        <div class="kanban" data-group="{{ $groupId }}"
                                                            data-meja="{{ $mejaGroup['meja_id'] }}"
                                                            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;">
                                                            @foreach ($mejaGroup['karyawan'] as $karyawan)
                                                                <div class="card shadow-sm text-center"
                                                                    style="background-color: rgb({{ $warna }}); color: #fff;"
                                                                    data-meja-saat-ini="{{ $groupId }}-{{ $mejaId }}"
                                                                    data-id-karyawan="{{ $karyawan['id'] }}"
                                                                    data-level="{{ $karyawan['level'] }}"
                                                                    data-nik="{{ $karyawan['nik'] }}"
                                                                    data-kode="{{ $karyawan['kode_karyawan'] }}"
                                                                    data-jabatan="{{ $karyawan['jabatan'] }}"
                                                                    data-nama="{{ $karyawan['nama'] }}">
                                                                    <div class="card-body py-2">
                                                                        <a href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#ViewKaryawan"
                                                                            class="text-decoration-none text-white"
                                                                            style="font-size: 12px;">
                                                                            {{ $karyawan['kode_karyawan'] }}
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card p-2 pt-2">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success" id="SimpanTempatKerja">Simpan Data Tempat Kerja</button>
        </div>
    </div>
@endsection

@section('modal')
    <!-- MODAL VIEW KARYAWAN -->
    <div class="modal fade" id="ViewKaryawan" tabindex="-1" aria-labelledby="ViewKaryawanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ViewKaryawanLabel">Detail Karyawan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <img id="fotoKaryawan" src="/assets/img/user.png" class="rounded-circle border shadow-sm mb-2"
                                width="140" height="140">
                            <div><small class="text-muted" id="modaljabatan">-</small></div>
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-2 text-center">Kartu Karyawan Aktif</h3>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td style="width: 130px;"><strong>Nama&nbsp;:</strong></td>
                                    <td id="modalNama">-</td>
                                </tr>
                                <tr>
                                    <td><strong>NIK&nbsp;&nbsp;&nbsp;&nbsp;:</strong></td>
                                    <td id="modalNik">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Kode&nbsp;&nbsp;:</strong></td>
                                    <td id="modalKode">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Level&nbsp;&nbsp;:</strong></td>
                                    <td id="modallevel">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        $(document).on('click', '[data-bs-target="#ViewKaryawan"]', function() {
            const parentCard = $(this).closest('.card');
            $('#modalNama').text(parentCard.data('nama'));
            $('#modalKode').text(parentCard.data('kode'));
            $('#modallevel').text(parentCard.data('level'));
            $('#modalNik').text(parentCard.data('nik'));
            $('#modaljabatan').text(parentCard.data('jabatan'));
        });

        var PERPINDAHAN_MEJA_KARYAWAN = [];

        $(document).ready(function() {
            const drake = dragula($('.kanban').toArray(), {
                mirrorContainer: document.body,
                revertOnSpill: true,
            });

            drake.on('drag', (el) => $(el).addClass('kanban-dragging'));

            drake.on('drop', (el, container) => {
                $(el).removeClass('kanban-dragging');

                const id = $(el).data('id-karyawan');
                const mejaSaatIni = $(el).data('meja-saat-ini');
                const groupTujuan = $(container).data('group');
                const mejaTujuan = $(container).data('meja');
                const mejaTujuanLabel = `${groupTujuan}-${mejaTujuan}`;
                const idx = PERPINDAHAN_MEJA_KARYAWAN.findIndex(x => x.id == id);
                if (idx >= 0) PERPINDAHAN_MEJA_KARYAWAN.splice(idx, 1);
                PERPINDAHAN_MEJA_KARYAWAN.push({
                    id: id,
                    meja_saat_ini: mejaSaatIni,
                    meja_tujuan: mejaTujuanLabel
                });
                $(el).attr('data-meja-saat-ini', mejaTujuanLabel);

                $.ajax({
                    url: '/inspeksi/tempat-kerja/' + id,
                    method: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id_karyawan: id,
                        meja_saat_ini: mejaSaatIni,
                        meja_tujuan: mejaTujuanLabel
                    }
                });
            });
            $(document).on('click', '#SimpanTempatKerja', function(e) {
                e.preventDefault();

                let dataKirim = PERPINDAHAN_MEJA_KARYAWAN;

                if (dataKirim.length === 0) {
                    dataKirim = [];
                    $('.kanban .card').each(function() {
                        const el = $(this);
                        const id = el.data('id-karyawan');
                        const mejaSaatIni = el.data('meja-saat-ini');
                        const mejaTujuan = el.data(
                            'meja-saat-ini');

                        dataKirim.push({
                            id: id,
                            meja_saat_ini: mejaSaatIni,
                            meja_tujuan: mejaTujuan
                        });
                    });
                }

                console.log('Data yang dikirim:', dataKirim);

                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('perpindahan_data', JSON.stringify(dataKirim));

                $.ajax({
                    url: '/inspeksi/tempat-kerja/update-banyak',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: res.msg,
                            confirmButtonText: 'Kembali ke Dashboard'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href =
                                    '/';
                            }
                        });
                    },
                    error: function(err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal menyimpan data',
                            text: err.responseJSON?.msg || '',
                            confirmButtonText: 'Tutup'
                        });
                    }
                });

            });

        });
    </script>
@endsection
