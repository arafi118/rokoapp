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
    {{-- <div class="card w-100 mt-3">
        <div class="card-body d-flex flex-wrap gap-3">



            <button type="" class="btn flex-fill text-white" style="background-color:#818181; min-width: 120px;">
                INPUT PRODUKSI HARIAN
            </button>
        </div>
    </div> --}}
    <div class="container mt-4">
        <div class="row">
            <!-- KIRI: Info Anggota -->
            <div class="col-md-4 mb-3" style="padding-left: 2px;">
                <div class="card h-100">
                    <div class="card-body text-center">

                        {{-- QR Code dengan background abu-abu --}}
                        <div style="display: inline-block; background-color: #e9ecef; padding: 6px; border-radius: 4px;">
                            {!! $qrCode !!}
                        </div>

                        {{-- Nama --}}
                        <div class="fw-bold mt-2" style="font-size: 1.1rem;">
                            {{ $anggota->nama }}
                        </div>

                        {{-- ID/Kode --}}
                        <div style="color: #6c757d; font-size: 0.9rem;">
                            {{ $anggota->kode }}
                        </div>

                        <hr>

                        {{-- Form info anggota dibuat rata kiri --}}
                        <div class="text-start px-2">
                            <label class="form-label" style="font-weight: normal;">Nik</label>
                            <input type="text" class="form-control mb-2" value="{{ $anggota->nik ?? '-' }}" disabled>

                            <label class="form-label" style="font-weight: normal;">Alamat</label>
                            <input type="text" class="form-control mb-2" value="{{ $anggota->alamat ?? '-' }}" disabled>

                            <label class="form-label" style="font-weight: normal;">Tempat Lahir</label>
                            <input type="text" class="form-control mb-2" value="{{ $anggota->tempat_lahir ?? '-' }}"
                                disabled>
                        </div>
                        <div class="card-body d-flex flex-wrap gap-3">
                            <a href="{{ route('anggota.cetak', $anggota->id) }}" target="_blank"
                                class="btn flex-fill text-white" style="background-color:#911111; min-width: 120px;">
                                Cetak
                            </a>
                        </div>
                    </div>
                </div>
            </div>


            <!-- KANAN: Form Input Produksi Harian -->
            <div class="col-md-8">
                <!-- Form menyelimuti seluruh card -->
                <form action="{{ url('/anggota') }}" method="POST">
                    @csrf

                    <!-- Card Input Produksi Harian -->
                    <div class="card">
                        <div class="card-header" style="background-color:#e4a908; color:#fff;">
                            <h5 class="card-title m-0">Input Produksi Harian</h5>
                        </div>

                        <div class="card-body">
                            <div class="mb-3">
                                <label for="tanggal_input" class="form-label">Tanggal Input</label>
                                <input class="form-control" type="date" name="tanggal_input" id="tanggal_input"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah" class="form-label">Jumlah Produksi</label>
                                <input class="form-control" type="number" name="jumlah" id="jumlah"
                                    placeholder="Masukkan jumlah produksi" required>
                            </div>

                            <!-- Tombol Simpan langsung di dalam card-body -->
                            <div class="text-end">
                                <button type="submit" class="btn text-white"
                                    style="background-color:#474747; min-width: 120px;">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection
@section('script')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session('success_create'))
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: @json(session('success_create')),
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
