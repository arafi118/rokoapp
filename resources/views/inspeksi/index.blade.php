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
                                            <button type="button"
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
                                            <button type="button"
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
                                            <button type="button"
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
                                            <button type="button"
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
                                        <span class="bg-red" id="tgl_data"></span>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header no-border"><a href="#">ARS-16</a>
                                                <span id="total_keseluruhan"></span> btg
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
                                                🟢 Giling : <span id="hadir_gl"></span>
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="hadir_gt"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header"><a href="#">Pekerja </a> tidak Hadir :</h3>
                                            <div class="timeline-body">
                                                🟢 Giling : <span id="tidak_hadir_gl"></span>
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="tidak_hadir_gt"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fas fa-clock"></i> GT/GL</span>
                                            <h3 class="timeline-header"><a href="#">Total </a> Pekerja :</h3>
                                            <div class="timeline-body">
                                                🟢 Giling : <span id="total_gl"></span>
                                            </div>
                                            <div class="timeline-body">
                                                🟢 Gunting : <span id="total_gt"></span>
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
                    <h5 class="modal-title" id="modalPACKLabel">Detail PACK</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Isi detail data PACK di sini...</p>
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
                    <h5 class="modal-title" id="modalBanderolLabel">Detail BANDEROL</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Isi detail data Banderol di sini...</p>
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
                    <h5 class="modal-title" id="modalOPPLabel">Detail OPP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Isi detail data OPP di sini...</p>
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
                    <h5 class="modal-title" id="modalMOPLabel">Detail MOP</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Isi detail data MOP di sini...</p>
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
        $(document).on('click', '#ModalGTGL', function(e) {
            e.preventDefault();

            $.ajax({
                url: "/inspeksi/modalGLGT",
                method: "GET",
                beforeSend: function() {
                    $('#modalGLGT').modal('show');
                    $('#tgl_data, #jam_kerja, #gl_jumlah_baik, #gt_jumlah_baik, #total_keseluruhan, #hadir_gl, #hadir_gt, #tidak_hadir_gl, #tidak_hadir_gt, #total_gl, #total_gt')
                        .text('...');
                },
                success: function(res) {
                    const r = res.rekap;
                    $('#tgl_data').text(res.tanggal);
                    $('#gl_jumlah_baik').text(res.rataGL);
                    $('#gt_jumlah_baik').text(res.rataGL);
                    $('#total_keseluruhan').text(res.totalKeseluruhan);
                    $('#hadir_gl').text(r.GL.hadir);
                    $('#tidak_hadir_gl').text(r.GL.tidak_hadir);
                    $('#total_gl').text(r.GL.total);
                    $('#hadir_gt').text(r.GT.hadir);
                    $('#tidak_hadir_gt').text(r.GT.tidak_hadir);
                    $('#total_gt').text(r.GT.total);
                },
                error: function() {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat memuat data.', 'error');
                }
            });
        });
    </script>
@endsection
