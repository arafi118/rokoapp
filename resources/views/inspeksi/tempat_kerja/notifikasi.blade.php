@extends('inspeksi.layouts.base')
@section('content')
    <div class="container text-center py-5">
        <img src="https://cdn-icons-png.flaticon.com/512/595/595067.png" alt="Not Found" class="img-fluid mb-4"
            style="max-width: 150px;">
        <h1 class="display-4 fw-bold">Oops!</h1>
        <p class="lead mb-4">Absensi Karyawan Tidak Ditemukan/Sudah Selesai</p>
        <a href="/" class="btn btn-primary btn-lg me-2">Kembali ke Beranda</a>
        <a href="javascript:location.reload()" class="btn btn-outline-secondary btn-lg">Muat Ulang</a>
        <div class="mt-4 text-muted">
            <p>Cek kembali URL yang kamu masukkan, atau gunakan menu navigasi untuk menemukan halaman yang tepat.</p>
        </div>
    </div>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
@endsection
