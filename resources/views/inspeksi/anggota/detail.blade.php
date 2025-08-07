<div class="card mb-4 shadow-sm">
    <div class="card-body row align-items-center">
        <div class="col-md-4 text-center mb-3 mb-md-0">
            <img src="/assets/img/user.png" alt="Foto Profil" class="rounded-circle img-fluid" style="max-width: 180px;">
            <h5 class="mt-3 fw-bold mb-0">{{ $anggota->nama }}</h5>
            <h6 class="text-muted">({{ $anggota->nik }})</h6>
        </div>

        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-birthday-cake text-danger me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->tempat_lahir }}, {{ $anggota->tanggal_lahir }}</div>
                                <small class="text-muted">Tempat & Tanggal Lahir</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-graduation-cap text-primary me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->ijazah }} - {{ $anggota->jurusan }}
                                    ({{ $anggota->tahun_lulus }})</div>
                                <small class="text-muted">Pendidikan Terakhir</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-university text-success me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->nama_bank }} - {{ $anggota->norek }}</div>
                                <small class="text-muted">Informasi Bank</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-venus-mars text-secondary me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </div>
                                <small class="text-muted">Jenis Kelamin</small>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-praying-hands text-warning me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->agama }}</div>
                                <small class="text-muted">Agama</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-heart text-danger me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->status }}</div>
                                <small class="text-muted">Status</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-female text-pink me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->nama_ibu_kandung }}</div>
                                <small class="text-muted">Nama Ibu Kandung</small>
                            </div>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-ruler-vertical text-dark me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->tinggi_badan }} cm / {{ $anggota->berat_badan }} kg
                                </div>
                                <small class="text-muted">Tinggi & Berat Badan</small>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="col-md-12">
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-start mb-2">
                            <i class="fas fa-map-marker-alt text-info me-2 mt-1 fs-5"></i>
                            <div>
                                <div class="fw-bold">{{ $anggota->alamat }}, {{ $anggota->desa }},
                                    {{ $anggota->kecamatan }}, {{ $anggota->kota }}</div>
                                <small class="text-muted">Alamat Lengkap</small>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
