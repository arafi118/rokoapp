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


            <div class="col-8">
                <form action="{{ url('/anggota') }}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header" style="background-color:#535353; color:#fff;">
                            <h5 class="card-title m-0">Input Produksi Harian</h5>
                        </div>
                        <div class="card-body">
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

                            <div class="text-end">
                                <a href="{{ route('anggota.cetak', $anggota->id) }}" target="_blank"
                                    class="btn flex-fill text-white" style="background-color:#f1ad1a; min-width: 15px;">
                                    Cetak
                                </a>
                                <button type="submit" class="btn text-white"
                                    style="background-color:#474747; min-width: 10px;">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-4 mb-3">
                <div class="card border-0">
                    <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start p-2"
                        style="background-color: #8a1919; color: white; border-radius: 6px;">

                        <!-- QR Code -->
                        <div class="p-2 mx-auto mx-md-0 d-flex align-items-center justify-content-center"
                            style="width: 80px; height: 80px; background-color: white; border-radius: 4px; overflow: hidden; margin-top: 4px;">
                            {!! $qrCode !!}
                        </div>

                        <!-- Garis Pembatas (hanya di layar kecil) -->
                        <div class="d-md-none w-100" style="border-bottom: 1px solid rgba(255,255,255,0.4); margin: 8px 0;">
                        </div>

                        <!-- Data Anggota -->
                        <div class="flex-grow-1 mt-2 mt-md-0 ms-md-3">
                            <div class="row mb-1">
                                <div class="col-12 col-md-4"><strong>Nama</strong> : {{ $anggota->nama }}</div>
                                <div class="col-12 col-md-4"><strong>Desa</strong> : </div>
                                <div class="col-12 col-md-4"><strong>Dusun</strong> : </div>
                            </div>
                            <div class="row mb-1">
                                <div class="col-12 col-md-4"><strong>No Induk</strong> : </div>
                                <div class="col-12 col-md-4"><strong>alamat</strong></div>
                                <div class="col-12 col-md-4"><strong>Rt</strong> :</div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4"><strong>Rw</strong> : </div>
                                <div class="col-12 col-md-4"><strong>No HP</strong> : </div>
                                <div class="col-12 col-md-4"><strong>Email</strong> : </div>
                            </div>
                        </div>
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
