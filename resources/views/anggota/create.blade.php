@extends('anggota.layouts.base')

@section('content')
    @php
        $successMessage = session('success');
        $errorMessage = session('error');
    @endphp

    {{-- Hapus alert success karena diganti SweetAlert2 --}}
    @if ($errorMessage)
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errorMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="container mt-4">
        <div class="row">
            <!-- Form -->
            <div class="col-12 col-md-8 d-flex mb-3">
                <form action="{{ url('/anggota') }}" method="POST" class="w-100">
                    @csrf
                    <div class="card h-100 d-flex flex-column">
                        <div class="card-header" style="background-color:#474747; color:#fff;">
                            <h5 class="card-title m-0">Input Produksi Harian</h5>
                        </div>
                        <div class="card-body flex-grow-1">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal Input</label>
                                <input class="form-control" type="date" name="tanggal" id="tanggal"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_baik" class="form-label">Jumlah Produksi Baik</label>
                                <input class="form-control" type="number" name="jumlah_baik" id="jumlah_baik"
                                    placeholder="Masukkan jumlah baik" value="0" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_buruk" class="form-label">Jumlah Produksi Buruk</label>
                                <input class="form-control" type="number" name="jumlah_buruk" id="jumlah_buruk"
                                    placeholder="Masukkan jumlah buruk" value="0" min="0">
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_buruk2" class="form-label">Jumlah Produksi Buruk 2</label>
                                <input class="form-control" type="number" name="jumlah_buruk2" id="jumlah_buruk2"
                                    placeholder="Masukkan jumlah buruk 2" value="0" min="0">
                            </div>
                        </div>
                        <div class="card-footer text-end">
                            <button type="submit" class="btn text-white" style="background-color:#12990d;">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Data Anggota -->
            <div class="col-12 col-md-4 d-flex mb-3">
                <div class="card text-center w-100">
                    <div class="card-body">
                        <!-- QR Code -->
                        <div class="mb-2 p-2 rounded" style="background-color: #dddddd; display: inline-block;">
                            {!! $qrCode !!}
                        </div>

                        <!-- Nama -->
                        <h6 class="fw-bold">{{ $anggota->nama }}</h6>
                        <hr>
                        <div class="mb-2 text-start">
                            <label class="form-label mb-0"><small>Jabatan</small></label>
                            <input type="text" class="form-control form-control-sm"
                                value="{{ $anggota->getjabatan->nama === 'Anggota' ? 'Karyawan' : $anggota->getjabatan->nama }}"
                                disabled>

                        </div>
                        <div class="mb-2 text-start">
                            <label class="form-label mb-0"><small>No Induk</small></label>
                            <input type="text" class="form-control form-control-sm" value="{{ $anggota->nik ?? '0' }}"
                                disabled>
                        </div>
                        <div class="mb-2 text-start">
                            <label class="form-label mb-0"><small>Alamat</small></label>
                            <input type="text" class="form-control form-control-sm" value="{{ $anggota->alamat ?? '0' }}"
                                disabled>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('anggota.cetak', $anggota->id) }}" target="_blank"
                            class="btn flex-fill text-white" style="background-color:#8f2828;">
                            Cetak
                        </a>
                    </div>
                </div>
            </div>
        </div>



    </div>
@endsection
@section('script')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('berhasil'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: @json(session('berhasil')),
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Tambah Data Lagi',
                cancelButtonText: 'Lihat Daftar Produksi',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ url()->current() }}"; // reload form
                } else {
                    window.location.href = "{{ route('anggota.produksi') }}"; // ke daftar
                }
            });
        </script>
    @endif
@endsection
