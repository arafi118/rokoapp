@extends('inspeksi.layouts.base')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane show active" role="tabpanel">
                            <div class="pc-kanban-wrapper d-flex flex-wrap gap-3">
                                <div class="pc-kanban-column col-12" style="min-width: 100%;">
                                    <div class="pc-kanban-header bg-primary text-white py-3 rounded text-center">
                                        <span class="fw-bold">KELOMPOK 1</span>
                                    </div>
                                    <div class="pc-kanban-body mt-2 mb-0 min-vh-80">
                                        <div class="pc-kanban-cards row g-0">
                                            @for ($i = 0; $i < 48; $i++)
                                                <div class="col-2 p-1">
                                                    <div class="card shadow-sm text-center draggable mb-0">
                                                        <div class="card-body py-2">
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#ViewKaryawan"
                                                                class="text-decoration-none text-dark">P. 29880132</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="pc-kanban-column col-12" style="min-width: 100%;">
                                    <div class="pc-kanban-header bg-primary text-white py-3 rounded text-center">
                                        <span class="fw-bold">KELOMPOK 1</span>
                                    </div>
                                    <div class="pc-kanban-body mt-2 mb-0 min-vh-80">
                                        <div class="pc-kanban-cards row g-0">
                                            @for ($i = 0; $i < 48; $i++)
                                                <div class="col-2 p-1">
                                                    <div class="card shadow-sm text-center draggable mb-0">
                                                        <div class="card-body py-2">
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#ViewKaryawan"
                                                                class="text-decoration-none text-dark">P. 29880132</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="pc-kanban-column col-12" style="min-width: 100%;">
                                    <div class="pc-kanban-header bg-primary text-white py-3 rounded text-center">
                                        <span class="fw-bold">KELOMPOK 1</span>
                                    </div>
                                    <div class="pc-kanban-body mt-2 mb-0 min-vh-80">
                                        <div class="pc-kanban-cards row g-0">
                                            @for ($i = 0; $i < 48; $i++)
                                                <div class="col-2 p-1">
                                                    <div class="card shadow-sm text-center draggable mb-0">
                                                        <div class="card-body py-2">
                                                            <a href="#" data-bs-toggle="modal"
                                                                data-bs-target="#ViewKaryawan"
                                                                class="text-decoration-none text-dark">P. 29880132</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
        $(document).ready(function() {
            ['touchstart', 'touchmove', 'wheel'].forEach(evt => {
                document.addEventListener(evt, () => {}, {
                    passive: false
                });
            });

            const drake = dragula($('.pc-kanban-cards').toArray(), {
                mirrorContainer: document.body,
                revertOnSpill: true,
                moves: (el, src, handle) => !$(handle).closest('.dropdown').length
            });

            drake.on('drag', el => $(el).addClass('kanban-dragging'));
            drake.on('dragend', el => $(el).removeClass('kanban-dragging'));

            $('.pc-kanban-body').each(function() {
                new SimpleBar(this);
            });
        });
    </script>
@endsection
