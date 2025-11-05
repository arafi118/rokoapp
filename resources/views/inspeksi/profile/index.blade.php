@extends('inspeksi.layouts.base')
@section('content')
    <form action="{{ url('inspeksi/update/' . $anggota->id) }}" method="POST" enctype="multipart/form-data"
        id="FormUpdateProfil">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body text-center">
                        <label for="inputFoto" style="cursor:pointer; display:inline-block; position:relative;">
                            @if ($anggota->foto && file_exists(storage_path('app/public/profil/' . $anggota->foto)))
                                <img src="{{ asset('storage/profil/' . $anggota->foto) }}" alt="Foto Profil"
                                    id="previewFoto" class="rounded-circle shadow-sm mb-3 border border-3 border-light"
                                    width="120" height="120" style="object-fit: cover;">
                            @else
                                <div id="previewFoto"
                                    class="d-flex align-items-center justify-content-center rounded-circle shadow-sm mb-3 border border-3 border-light bg-light text-muted"
                                    style="width:120px; height:120px; font-size:28px;">
                                    <i class="bi bi-camera"></i>
                                </div>
                            @endif
                        </label>
                        <input type="file" id="inputFoto" name="inputFoto" accept="image/*" style="display:none;">
                        <h4 class="fw-bold mb-0">{{ $anggota->username }}</h4>
                        <p class="text-muted mb-4">{{ $anggota->getjabatan->nama }}</p>
                        <div class="form-group text-start">
                            <label for="username" class="form-label fw-semibold">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ $anggota->username }}">
                            <small class="text-danger" id="msg_username"></small>
                        </div>
                        <div class="form-group text-start">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="password1" name="password">
                            <small class="text-danger" id="msg_password"></small>
                        </div>
                        <hr>
                        <div class="form-group text-start">
                            <label for="password_confirmation" class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password2">
                            <small class="text-danger" id="msg_password_confirmation"></small>
                        </div>
                        <div class="mt-4 d-flex justify-content-end">
                            <button class="btn btn-primary w-50" id="Simpan"><i class="bi bi-save me-1"></i>
                                Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="timeline">
                    <div class="time-label"> <span class="bg-green">Detail Profile</span> </div>
                    <div> <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item"> <span class="time"><i class="fas fa-clock"></i>🟢 🔴 ⚫</span>
                            <h3 class="timeline-header">&nbsp;</h3>
                            <div class="timeline-body" style="text-align: justify;"> Saya,
                                <strong>{{ $anggota->nama }}</strong>, dengan nomor induk kependudukan
                                <strong>{{ $anggota->nik }}</strong>, lahir di <strong>{{ $anggota->tempat_lahir }}</strong>
                                pada tanggal
                                <strong>{{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->translatedFormat('d F Y') }}</strong>.
                                Saat ini saya berdomisili di Desa <strong>{{ $anggota->desa }}</strong>, Kecamatan
                                <strong>{{ $anggota->kecamatan }}</strong>, Kabupaten/Kota
                                <strong>{{ $anggota->kota }}</strong>, dan beralamat lengkap di
                                <strong>{{ $anggota->alamat }}</strong>.
                            </div>
                        </div>
                    </div>
                    <div> <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item"> <span class="time"><i class="fas fa-clock"></i>🟢 🔴 🟡 </span>
                            <h3 class="timeline-header">&nbsp;</h3>
                            <div class="timeline-body" style="text-align: justify;"> Saya beragama
                                <strong>{{ $anggota->agama }}</strong>, berstatus
                                <strong>{{ ucfirst($anggota->status) }}</strong>, dan merupakan lulusan
                                <strong>{{ $anggota->jurusan }}</strong> pada tahun
                                <strong>{{ $anggota->tahun_lulus }}</strong>. Saat ini saya bekerja sebagai
                                <strong>{{ $anggota->getjabatan->nama ?? '-' }}</strong> dengan tinggi badan
                                <strong>{{ $anggota->tinggi_badan }}</strong> cm dan berat badan
                                <strong>{{ $anggota->berat_badan }}</strong> kg.
                            </div>
                        </div>
                    </div>
                    <div> <i class="fas fa-envelope bg-blue"></i>
                        <div class="timeline-item"> <span class="time"><i class="fas fa-clock"></i>🔴 🟡 ⚫</span>
                            <h3 class="timeline-header">&nbsp;</h3>
                            <div class="timeline-body" style="text-align: justify;"> Untuk keperluan administrasi, saya
                                memiliki rekening bank atas nama sendiri di <strong>{{ $anggota->nama_bank }}</strong>
                                dengan nomor rekening <strong>{{ $anggota->norek }}</strong>. Nama ibu kandung saya adalah
                                <strong>{{ $anggota->nama_ibu_kandung }}</strong>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('script')
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
        });

        $(document).on('change', '#inputFoto', function(e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Format tidak valid! Hanya JPG, JPEG, atau PNG.'
                    });
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(evt) {
                    if ($('#previewFoto').is('div')) {
                        $('#previewFoto').replaceWith(`
                            <img src="${evt.target.result}" 
                                id="previewFoto" 
                                class="rounded-circle shadow-sm mb-3 border border-3 border-light" 
                                width="120" height="120" style="object-fit:cover;">
                        `);
                    } else {
                        $('#previewFoto').attr('src', evt.target.result);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
        $(document).on('click', '#Simpan', function(e) {
            e.preventDefault();

            const pass1 = $('#password1').val();
            const pass2 = $('#password2').val();

            if (pass1 !== pass2) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Pastikan password harus sama!'
                });
                return;
            }

            let form = $('#FormUpdateProfil')[0];
            let formData = new FormData(form);

            $.ajax({
                type: 'POST',
                url: $('#FormUpdateProfil').attr('action'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(r) {
                    if (r.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Profil berhasil diperbarui!'
                        });

                        // --- Update foto profil navbar ---
                        if (r.foto) {
                            $('.user-image').attr('src', '/storage/profil/' + r.foto + '?v=' +
                            new Date().getTime());
                            $('#previewFoto').attr('src', '/storage/profil/' + r.foto + '?v=' +
                                new Date().getTime());
                        }

                        // --- Update nama di navbar ---
                        if (r.username) {
                            $('.d-none.d-md-inline').text(r.username);
                        }
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: r.msg || 'Gagal memperbarui profil.'
                        });
                    }
                },
                error: function(x) {
                    Toast.fire({
                        icon: 'error',
                        title: x.responseJSON?.message || 'Terjadi kesalahan!'
                    });
                }
            });
        });
    </script>
@endsection
