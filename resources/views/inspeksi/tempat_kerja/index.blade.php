@extends('inspeksi.layouts.base')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="tab-content">
                <div class="tab-pane show active" role="tabpanel">
                    <div class="row">
                        @for ($k = 1; $k <= 8; $k++)
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                                            @for ($m = 1; $m <= 12; $m++)
                                                @php
                                                    $warna = rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255);
                                                @endphp
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="kanban"
                                                            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;"
                                                            data-meja="{{ $k }}-{{ $m }}">
                                                            @for ($a = 1; $a <= 4; $a++)
                                                                <div class="card shadow-sm text-center"
                                                                    style="background-color: rgb({{ $warna }});"
                                                                    data-meja-saat-ini="{{ $k }}-{{ $m }}"
                                                                    data-id-karyawan="{{ $a * $m }}">
                                                                    <div class="card-body py-2">
                                                                        <a href="#" data-bs-toggle="modal"
                                                                            data-bs-target="#ViewKaryawan"
                                                                            class="text-decoration-none text-dark"
                                                                            style="font-size: 10px;">P.
                                                                            {{ $a * $m }}</a>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <img src="/assets/img/user2-160x160.jpg" alt="Foto Karyawan"
                                class="rounded-circle border shadow-sm mb-2" width="140" height="140">
                            <div><small class="text-muted">Supervisor Produksi</small></div>
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-2 text-center">Kartu Karyawan Aktif</h3>
                            <table>
                                <tr>
                                    <td class="pe-3" style="width: 120px;"><strong>Nama</strong></td>
                                    <td>:</td>
                                    <td class="ps-2">Ahmad Choirul Muna</td>
                                </tr>
                                <tr>
                                    <td class="pe-3"><strong>ID</strong></td>
                                    <td>:</td>
                                    <td class="ps-2">P.25080014</td>
                                </tr>
                                <tr>
                                    <td class="pe-3"><strong>Jabatan</strong></td>
                                    <td>:</td>
                                    <td class="ps-2">Supervisor Produksi</td>
                                </tr>
                                <tr>
                                    <td class="pe-3"><strong>Departemen</strong></td>
                                    <td>:</td>
                                    <td class="ps-2">Operasional</td>
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
        var PERPINDAHAN_MEJA_KARYAWAN = [];
        $(document).ready(function() {
            ['touchstart', 'touchmove', 'wheel'].forEach(evt => {
                document.addEventListener(evt, () => {}, {
                    passive: false
                });
            });

            const drake = dragula($('.kanban').toArray(), {
                mirrorContainer: document.body,
                revertOnSpill: true,
                moves: (el, src, handle) => {
                    return !($(handle).closest('.dropdown').length || $(el).hasClass(
                        'pc-kanban-wrapper'))
                }
            });

            drake.on('drag', (el, src, handle) => {
                if (!$(el).hasClass('pc-kanban-wrapper')) {
                    return $(el).addClass('kanban-dragging')
                }
            });

            drake.on('drop', (el, container) => {
                if (!$(el).hasClass('pc-kanban-wrapper')) {
                    const item = el;
                    const tujuan = container;

                    var id = $(item).attr('data-id-karyawan');
                    var meja_saat_ini = $(item).attr('data-meja-saat-ini');
                    var meja_tujuan = $(tujuan).attr('data-meja');

                    const data = PERPINDAHAN_MEJA_KARYAWAN.find(x => parseInt(x.id) == parseInt(id));
                    const dataIndex = PERPINDAHAN_MEJA_KARYAWAN.findIndex(x => x.id == id);
                    if (dataIndex >= 0) {
                        PERPINDAHAN_MEJA_KARYAWAN.splice(dataIndex, 1);
                    }

                    PERPINDAHAN_MEJA_KARYAWAN.push({
                        id: id,
                        meja_saat_ini: meja_saat_ini,
                        meja_tujuan: meja_tujuan
                    })

                    $("[data-id-karyawan='" + id + "']").attr("data-meja-saat-ini", meja_tujuan);
                    return $(el).removeClass('kanban-dragging')
                }
            });
        });
    </script>
@endsection
