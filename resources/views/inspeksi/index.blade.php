@extends('inspeksi.layouts.base')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 order-first">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center fs-7 text-secondary">
                                        <div class="card-title fw-bold">Gunting/Giling</div>
                                        <div>{{ $target_guntinggiling }}</div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-end">
                                        <div>
                                            <h2 class="h2 text-primary d-inline">{{ $aktual_guntinggiling }}</h2>
                                            <span>btg</span>
                                        </div>
                                        <div>
                                            <button type="button" id="ModalGTGL"
                                                class="btn btn-primary btn-icon rounded-circle -rotate-45"
                                                data-bs-toggle="modal" data-bs-target="#modalGLGT">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center fs-7 text-secondary">
                                        <div class="card-title fw-bold">PACK</div>
                                        <div>{{ $target_pack }}</div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-end">
                                        <div>
                                            <h2 class="h2 text-primary d-inline">{{ $aktual_pack }}</h2> <span>btg</span>
                                        </div>
                                        <div>
                                            <button type="button" id="ModalPACK"
                                                class="btn btn-primary btn-icon rounded-circle -rotate-45"
                                                data-bs-toggle="modal" data-bs-target="#modalPACK">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 mb-4">
                    <div class="card h-100 w-100">
                        <div class="card-body" style="height:220px;">
                            <canvas id="chart" style="width:100%; height:100%;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center fs-7 text-secondary">
                                        <div class="card-title fw-bold">BANDEROL</div>
                                        <div>{{ $target_banderol }}</div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-end">
                                        <div>
                                            <h2 class="h2 text-primary d-inline">{{ $aktual_banderol }}</h2>
                                            <span>btg</span>
                                        </div>
                                        <div>
                                            <button type="button" id="ModalBDL"
                                                class="btn btn-primary btn-icon rounded-circle -rotate-45"
                                                data-bs-toggle="modal" data-bs-target="#modalBanderol">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center fs-7 text-secondary">
                                        <div class="card-title fw-bold">OPP</div>
                                        <div>{{ $target_opp }}</div>
                                    </div>
                                    <div class="mt-4 d-flex justify-content-between align-items-end">
                                        <div>
                                            <h2 class="h2 text-primary d-inline">{{ $aktual_opp }}</h2> <span>btg</span>
                                        </div>
                                        <div>
                                            <button type="button" id="ModalOPP"
                                                class="btn btn-primary btn-icon rounded-circle -rotate-45"
                                                data-bs-toggle="modal" data-bs-target="#modalOPP">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center fs-7 text-secondary">
                                        <div class="card-title fw-bold">MOP</div>
                                        <div>{{ $target_mop }}</div>
                                    </div>

                                    <div class="mt-4 d-flex justify-content-between align-items-end">
                                        <div>
                                            <h2 class="h2 text-primary d-inline">{{ $aktual_mop }}</h2> <span>btg</span>
                                        </div>
                                        <div>
                                            <button type="button" id="ModalMOP"
                                                class="btn btn-primary btn-icon rounded-circle -rotate-45"
                                                data-bs-toggle="modal" data-bs-target="#modalMOP">
                                                <i class="bi bi-arrow-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('modal')
    {{-- Modal GL/GT --}}
    <div class="modal fade" id="modalGLGT" tabindex="-1" aria-labelledby="modalGLGTLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalGLGTLabel">Detail Gunting / Giling</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="timeline">
                                    <div class="time-label">
                                        <span class="bg-red" id="tgl_dataGLGT"></span>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header no-border"><a href="#">ARS-16</a>
                                                <span id="total_keseluruhanGLGT"></span> btg
                                            </h3>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header"><a href="#">Rata-rata </a> Prod :</h3>
                                            <div class="timeline-body">
                                                🟢 Giling : <span id="gl_jumlah_baik"></span> btg/jam/org
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="gt_jumlah_baik"></span> btg/jam/org
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header"><a href="#">Pekerja </a> Hadir :</h3>
                                            <div class="timeline-body">
                                                🟢 Giling : <span id="gl_hadir"> orang</span>
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="gt_hadir"> orang</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header"><a href="#">Pekerja </a> tidak Hadir :</h3>
                                            <div class="timeline-body">
                                                🟢 Giling : <span id="gl_tidak_hadir"> orang</span>
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="gt_tidak_hadir"> orang</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header"><a href="#">Total </a> Pekerja :</h3>
                                            <div class="timeline-body">
                                                🟢 Giling : <span id="gl_total"> orang</span>
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="gt_total"> orang</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal PACK --}}
    <div class="modal fade" id="modalPACK" tabindex="-1" aria-labelledby="modalPACKLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="modalPACKLabel">
                        Detail PACK ( <span id="tgl_dataPK"></span> )
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_hadirPK"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja tidak hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_tidak_hadirPK"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Total Pekerja</p>
                                    <h4 class="fw-bold mb-0 text-dark"> <span id="total_pekerjaPK"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-md-6 border-end border-2 border-secondary">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> ARS-16</h4>
                            <p class="text-muted mb-1 mt-3">
                                <span id="total_btgPK"></span> btg
                            </p>
                            <h6 class="fw-bold">( <span id="total_packPK"></span> pack )</h6>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> Rata-rata Productivity</h4>
                            <p class="text-muted mb-1 mt-3"><span id="rata_btgPK"></span> btg</p>
                            <h6 class="fw-bold">( <span id="rata_packPK"></span> pack )</h6>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal BANDEROL --}}
    <div class="modal fade" id="modalBanderol" tabindex="-1" aria-labelledby="modalBanderolLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalBanderolLabel">
                        Detail BANDEROL ( <span id="tgl_dataBDL"></span> )
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_hadirBDL"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja tidak hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_tidak_hadirBDL"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Total Pekerja</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="total_pekerjaBDL"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-md-6 border-end border-2 border-secondary">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> ARS-16</h4>
                            <p class="text-muted mb-1 mt-3"><span id="total_btgBDL"></span> btg</p>
                            <h6 class="fw-bold">( <span id="total_packBDL"></span> pack )</h6>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> Rata-rata Productivity</h4>
                            <p class="text-muted mb-1 mt-3"><span id="rata_btgBDL"></span> btg</p>
                            <h6 class="fw-bold">( <span id="rata_packBDL"></span> pack )</h6>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    {{-- Modal OPP --}}
    <div class="modal fade" id="modalOPP" tabindex="-1" aria-labelledby="modalOPPLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="modalOPPLabel">Detail OPP <span id="tgl_dataOPP"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_hadirOPP"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja tidak hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_tidak_hadirOPP"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Total Pekerja</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="total_pekerjaOPP"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-md-6 border-end border-2 border-secondary">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> ARS-16</h4>
                            <p class="text-muted mb-1 mt-3"><span id="total_btgOPP"></span> btg</p>
                            <h6 class="fw-bold">( <span id="total_packOPP"></span> pack )</h6>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> Rata-rata Productivity</h4>
                            <p class="text-muted mb-1 mt-3"><span id="rata_btgOPP"></span> btg</p>
                            <h6 class="fw-bold">( <span id="rata_packOPP"></span> pack )</h6>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal MOP --}}
    <div class="modal fade" id="modalMOP" tabindex="-1" aria-labelledby="modalMOPLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="modalMOPLabel">Detail MOP <span id="tgl_dataMOP"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_hadirMOP"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Pekerja tidak hadir</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="pekerja_tidak_hadirMOP"></span></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <p class="text-muted mb-1">Total Pekerja</p>
                                    <h4 class="fw-bold mb-0 text-dark"><span id="total_pekerjaMOP"></span></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row align-items-center">
                        <div class="col-md-6 border-end border-2 border-secondary">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> ARS-16</h4>
                            <p class="text-muted mb-1 mt-3"><span id="total_btgMOP"></span> btg</p>
                            <h6 class="fw-bold">( <span id="total_packMOP"></span> pack )</h6>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold text-primary"><i class="bi bi-brush-fill"></i> Rata-rata Productivity</h4>
                            <p class="text-muted mb-1 mt-3"><span id="rata_btgMOP"></span> btg</p>
                            <h6 class="fw-bold">( <span id="rata_packMOP"></span> pack )</h6>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script>
        // Chart.js Initialization
        const ctx = document.getElementById('chart');
        let chartInstance = null;
        const progressContainer = $(`
                <div class="progress mt-3" style="height:8px; display:none;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                        role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            `);
        const cardBody = $('#chart').closest('.card-body');
        $('#chart').css('height', '215px');
        $('#chart').after(progressContainer);

        function buatChart(labels, d1, d2, d3, d4, d5, judul) {
            const data = {
                labels,
                datasets: [{
                        label: 'GT/GL',
                        data: d1,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.3)',
                        tension: .3,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'PACK',
                        data: d2,
                        borderColor: 'rgba(255, 206, 86, 1)',
                        backgroundColor: 'rgba(255, 206, 86, 0.3)',
                        tension: .3,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'BANDEROL',
                        data: d3,
                        borderColor: 'rgba(75, 192, 75, 1)',
                        backgroundColor: 'rgba(75, 192, 75, 0.3)',
                        tension: .3,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'OPP',
                        data: d4,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.3)',
                        tension: .3,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    },
                    {
                        label: 'MOP',
                        data: d5,
                        borderColor: 'rgba(108, 117, 125, 1)',
                        backgroundColor: 'rgba(108, 117, 125, 0.3)',
                        tension: .3,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }
                ]
            };

            const config = {
                type: 'line',
                data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                usePointStyle: true,
                                pointStyle: 'circle',
                                boxWidth: 8,
                                padding: 10,
                                font: {
                                    size: 11
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: judul,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            };

            if (chartInstance) chartInstance.destroy();
            chartInstance = new Chart(ctx, config);
        }

        function gantiChart(mode) {
            let progress = 0;
            const bar = progressContainer.find('.progress-bar');
            progressContainer.show();
            bar.css('width', '0%').attr('aria-valuenow', 0);
            const interval = setInterval(() => {
                progress = Math.min(progress + 10, 90);
                bar.css('width', progress + '%').attr('aria-valuenow', progress);
            }, 200);

            $.ajax({
                url: '/inspeksi/chart',
                method: 'GET',
                data: {
                    periode: mode
                },
                beforeSend: function() {
                    if (chartInstance) chartInstance.destroy();
                    const tempCtx = ctx.getContext('2d');
                    tempCtx.clearRect(0, 0, ctx.width, ctx.height);
                },
                success: function(res) {
                    clearInterval(interval);
                    bar.css('width', '100%').attr('aria-valuenow', 100);

                    const labels = res.labels;
                    const d1 = res.datasets.gtgl || [];
                    const d2 = res.datasets.pack || [];
                    const d3 = res.datasets.banderol || [];
                    const d4 = res.datasets.opp || [];
                    const d5 = res.datasets.mop || [];

                    const judul = mode === 'mingguan' ?
                        'Data Aktual Mingguan' :
                        'Data Aktual Bulanan';
                    setTimeout(() => {
                        buatChart(labels, d1, d2, d3, d4, d5, judul);
                        progressContainer.fadeOut(400);
                    }, 300);
                },
                error: function(err) {
                    clearInterval(interval);
                    bar.addClass('bg-danger').css('width', '100%');
                    console.error(err);
                    alert('Gagal memuat data chart.');
                    setTimeout(() => progressContainer.fadeOut(400), 800);
                }
            });
        }
        $(document).on('change', 'input[name="periode"]', function() {
            $('input[name="periode"]').each(function() {
                $(`label[for="${this.id}"]`)
                    .removeClass('btn-warning')
                    .addClass('btn-outline-warning');
            });
            $(`label[for="${this.id}"]`)
                .removeClass('btn-outline-warning')
                .addClass('btn-warning');
            gantiChart($(this).attr('id'));
        });
        $('input[name="periode"]:checked').trigger('change');
    </script>
    <script>
        // Modal GL/GT
        $(document).on('click', '#ModalGTGL', function(e) {
            e.preventDefault();

            $.ajax({
                url: "/inspeksi/modalGLGT",
                method: "GET",
                beforeSend: function() {
                    const modal = $('#modalGLGT');
                    modal.modal('show');
                    modal.find('span').each(function() {
                        $(this).text('...');
                    });
                },
                success: function(res) {
                    const r = res.rekap;
                    const modal = $('#modalGLGT');

                    modal.find('#tgl_dataGLGT').text(res.tanggal_GTGL);
                    modal.find('#total_keseluruhanGLGT').text(res.totalKeseluruhan_GTGL);
                    modal.find('#gl_jumlah_baik').text(res.rata_GL);
                    modal.find('#gt_jumlah_baik').text(res.rata_GT);
                    modal.find('#gl_hadir').text(r.GL.hadir_GL);
                    modal.find('#gt_hadir').text(r.GT.hadir_GT);
                    modal.find('#gl_tidak_hadir').text(r.GL.tidak_hadirGL);
                    modal.find('#gt_tidak_hadir').text(r.GT.tidak_hadirGT);
                    modal.find('#gl_total').text(r.GL.total_GL);
                    modal.find('#gt_total').text(r.GT.total_GT);
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memuat data GL/GT.', 'error');
                }
            });
        });

        // Modal PACK
        $(document).on('click', '#ModalPACK', function(e) {
            e.preventDefault();

            $.ajax({
                url: "/inspeksi/modalPACK",
                method: "GET",
                beforeSend: function() {
                    const modal = $('#modalPACK');
                    modal.modal('show');
                    modal.find('span').each(function() {
                        $(this).text('...');
                    });
                },
                success: function(res) {
                    const p = res.PACK;
                    const modal = $('#modalPACK');

                    modal.find('#tgl_dataPK').text(res.tanggal_PK);
                    modal.find('#total_btgPK').text(p.ARS.total_btg_PK);
                    modal.find('#total_packPK').text(p.ARS.total_pack_PK);
                    modal.find('#rata_btgPK').text(p.rata_prod.btg_PK);
                    modal.find('#rata_packPK').text(p.rata_prod.pack_PK);
                    modal.find('#pekerja_hadirPK').text(p.hadir_PK);
                    modal.find('#pekerja_tidak_hadirPK').text(p.tidak_hadir_PK);
                    modal.find('#total_pekerjaPK').text(p.total_PK);
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memuat data PACK.', 'error');
                }
            });
        });

        // Modal BANDEROL
        $(document).on('click', '#ModalBDL', function(e) {
            e.preventDefault();

            $.ajax({
                url: "/inspeksi/modalBANDEROL",
                method: "GET",
                beforeSend: function() {
                    const modal = $('#modalBanderol');
                    modal.modal('show');
                    modal.find('span').each(function() {
                        $(this).text('...');
                    });
                },
                success: function(res) {
                    const BD = res.BANDEROL;
                    const modal = $('#modalBanderol');

                    modal.find('#tgl_dataBDL').text(res.tanggal_BDL);
                    modal.find('#total_btgBDL').text(BD.ARS.total_btg_BDL);
                    modal.find('#total_packBDL').text(BD.ARS.total_pack_BDL);
                    modal.find('#rata_btgBDL').text(BD.rata_prod.btg_BDL);
                    modal.find('#rata_packBDL').text(BD.rata_prod.pack_BDL);
                    modal.find('#pekerja_hadirBDL').text(BD.hadir_BDL);
                    modal.find('#pekerja_tidak_hadirBDL').text(BD.tidak_hadir_BDL);
                    modal.find('#total_pekerjaBDL').text(BD.total_BDL);
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memuat data PACK.', 'error');
                }
            });
        });

        // Modal OPP
        $(document).on('click', '#ModalOPP', function(e) {
            e.preventDefault();

            $.ajax({
                url: "/inspeksi/modalOPP",
                method: "GET",
                beforeSend: function() {
                    const modal = $('#modalOPP');
                    modal.modal('show');
                    modal.find('span').each(function() {
                        $(this).text('...');
                    });
                },
                success: function(res) {
                    const o = res.OPP;
                    const modal = $('#modalOPP');

                    modal.find('#tgl_dataOPP').text(res.tanggal_OPP);
                    modal.find('#total_btgOPP').text(o.ARS.total_btg_OPP);
                    modal.find('#total_packOPP').text(o.ARS.total_pack_OPP);
                    modal.find('#rata_btgOPP').text(o.rata_prod.btg_OPP);
                    modal.find('#rata_packOPP').text(o.rata_prod.pack_OPP);
                    modal.find('#pekerja_hadirOPP').text(o.hadir_OPP);
                    modal.find('#pekerja_tidak_hadirOPP').text(o.tidak_hadir_OPP);
                    modal.find('#total_pekerjaOPP').text(o.total_OPP);
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memuat data PACK.', 'error');
                }
            });
        });

        // Modal MOP
        $(document).on('click', '#ModalMOP', function(e) {
            e.preventDefault();

            $.ajax({
                url: "/inspeksi/modalMOP",
                method: "GET",
                beforeSend: function() {
                    const modal = $('#modalMOP');
                    modal.modal('show');
                    modal.find('span').each(function() {
                        $(this).text('...');
                    });
                },
                success: function(res) {
                    const mp = res.MOP;
                    const modal = $('#modalMOP');

                    modal.find('#tgl_dataMOP').text(res.tanggal_MOP);
                    modal.find('#total_btgMOP').text(mp.ARS.total_btg_MOP);
                    modal.find('#total_packMOP').text(mp.ARS.total_pack_MOP);
                    modal.find('#rata_btgMOP').text(mp.rata_prod.btg_MOP);
                    modal.find('#rata_packMOP').text(mp.rata_prod.pack_MOP);
                    modal.find('#pekerja_hadirMOP').text(mp.hadir_MOP);
                    modal.find('#pekerja_tidak_hadirMOP').text(mp.tidak_hadir_MOP);
                    modal.find('#total_pekerjaMOP').text(mp.total_MOP);
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memuat data PACK.', 'error');
                }
            });
        });
    </script>
@endsection
