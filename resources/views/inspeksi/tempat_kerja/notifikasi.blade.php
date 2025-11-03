@extends('inspeksi.layouts.base')
@section('content')
    @if ($anggota && optional($anggota->getjabatan)->id == 1)
        <div class="container text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/595/595067.png" alt="Not Found" class="img-fluid mb-4"
                style="max-width: 150px;">
            <h1 class="display-4 fw-bold">Oops!</h1>
            <p class="lead mb-4">Absensi Karyawan Tidak Ditemukan/Sudah Selesai</p>
            <button id="aktifkan-absensi" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i> Aktifkan Kembali Absensi
            </button>
        </div>
    @else
        <div class="container text-center py-5">
            <img src="https://cdn-icons-png.flaticon.com/512/595/595067.png" alt="Not Found" class="img-fluid mb-4"
                style="max-width: 150px;">
            <h1 class="display-4 fw-bold">Oops!</h1>
            <p class="lead mb-4">Absensi Karyawan Tidak Ditemukan/Sudah Selesai</p>
            <a href="/" class="btn btn-primary btn-lg me-2">Kembali ke Beranda</a>
            <a href="javascript:location.reload()" class="btn btn-outline-secondary btn-lg">Muat Ulang</a>
            <div class="mt-4 text-muted">
                <p>Jika Ingin Mengaktifkan Absensi Karyawan, Silahkan Hubungi Admin/Kepala Divisi.</p>
            </div>
        </div>
    @endif
@endsection
@section('script')
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    <script>
        $(document).on('click', '#aktifkan-absensi', function() {
            Swal.fire({
                title: "Aktifkan Absensi?",
                text: "Tindakan ini akan mengaktifkan absensi untuk hari ini.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Aktifkan",
                cancelButtonText: "Batal",
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33"
            }).then((result) => {
                if (result.isConfirmed) {
                    const today = new Date().toISOString().split('T')[0];

                    $.ajax({
                        url: '/inspeksi/tempat-kerja/aktif-kembali',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            tanggal: today
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: "Memproses...",
                                text: "Mohon tunggu sebentar",
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: response.success ? "success" : "info",
                                title: response.success ? "Berhasil!" : "Informasi",
                                text: response.msg ??
                                    "Absensi telah diaktifkan untuk hari ini.",
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal!",
                                text: "Terjadi kesalahan saat mengaktifkan absensi."
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
