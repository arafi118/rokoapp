@extends('inspeksi.layouts.base')

@section('content')
    <div class="card">
        <form action="/inspeksi/anggota" method="post" id="FormInputAnggota" autocomplete="off">
            @csrf
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama">
                            <small class="invalid-feedback" id="msg_nama"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="nik">NIK</label>
                            <input type="text" maxlength="16" class="form-control" id="nik" name="nik">
                            <small class="invalid-feedback" id="msg_nik"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="no_kk">No.KK</label>
                            <input type="text" maxlength="16" class="form-control" id="no_kk" name="no_kk">
                            <small class="invalid-feedback" id="msg_no_kk"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="text" class="form-control date" value="{{ date('d/m/Y') }}" id="tanggal_masuk"
                                name="tanggal_masuk">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="jabatan">Jabatan</label>
                                    <select name="jabatan" id="jabatan" class="form-control select2">
                                        <option value="">- Pilih Nama jabatan -</option>
                                        @foreach ($jabatan as $jab)
                                            <option {{ $jab->id == '4' ? 'selected' : '' }} value="{{ $jab->id }}">
                                                {{ ucwords(strtolower($jab->nama)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="invalid-feedback" id="msg_provinsi"></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="level">Operator Produksi</label>
                                    <select name="level" id="level" class="form-control select2">
                                        <option value="">- Pilih Level -</option>
                                        @foreach ($level as $lev)
                                            <option {{ $lev->id == '1' ? 'selected' : '' }} value="{{ $lev->id }}">
                                                {{ ucwords(strtolower($lev->nama)) }} (
                                                {{ ucwords(strtolower($lev->inisial)) }} )
                                            </option>
                                        @endforeach
                                    </select><small class="invalid-feedback" id="msg_level"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" for="nama_ibu_kandung">Nama Ibu Kandung</label>
                            <input type="text" class="form-control" id="nama_ibu_kandung" name="nama_ibu_kandung">
                            <small class="invalid-feedback" id="msg_nama_ibu_kandung"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label" for="tempat_lahir">Tempat & Tanggal Lahir</label>
                            <div class="row g-0">
                                <div class="col-6">
                                    <input type="text" class="form-control rounded-0 rounded-start" id="tempat_lahir"
                                        name="tempat_lahir" placeholder="Tempat">
                                    <small class="invalid-feedback" id="msg_tempat_lahir"></small>
                                </div>
                                <div class="col-6">
                                    <input type="text" class="form-control date rounded-0" id="tanggal_lahir"
                                        name="tanggal_lahir" value="{{ date('d/m/Y') }}" placeholder="Tanggal">
                                    <small class="invalid-feedback" id="msg_tanggal_lahir"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="jenis_kelamin">Jenis Kelamin</label>
                            <div class="btn-group d-flex" role="group">
                                <button type="button" class="btn btn-outline-secondary flex-fill active" data-value="L">
                                    Laki-laki
                                </button>
                                <button type="button" class="btn btn-outline-secondary flex-fill" data-value="P">
                                    Perempuan
                                </button>
                            </div>
                            <input type="hidden" name="jenis_kelamin" id="jenis_kelamin" value="L">
                            <small class="invalid-feedback" id="msg_jenis_kelamin"></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label" for="agama">Agama</label>
                            <select class="form-control select2" id="agama" name="agama">
                                <option value="">-- Pilih Agama --</option>
                                <option value="islam" selected>Islam</option>
                                <option value="kristen_protestan">Kristen Protestan</option>
                                <option value="katolik">Katolik</option>
                                <option value="hindu">Hindu</option>
                                <option value="buddha">Buddha</option>
                                <option value="khonghucu">Khonghucu</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            <small class="invalid-feedback" id="msg_agama"></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label class="form-label" for="status">Status Perkawinan</label>
                            <select class="form-control select2" id="status" name="status">
                                <option value="">-- Pilih Status --</option>
                                <option value="belum menikah" selected>Belum Menikah</option>
                                <option value="menikah">Menikah</option>
                                <option value="cerai hidup">Cerai Hidup</option>
                                <option value="cerai mati">Cerai Mati</option>
                            </select>
                            <small class="invalid-feedback" id="msg_status"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="ijazah">Ijazah</label>
                            <input type="text" class="form-control" id="ijazah" name="ijazah">
                            <small class="invalid-feedback" id="msg_ijazah"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="jurusan">Jurusan</label>
                            <input type="text" class="form-control" id="jurusan" name="jurusan">
                            <small class="invalid-feedback" id="msg_jurusan"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="tahun_lulus">Tahun Lulus</label>
                            <input type="text" class="form-control" id="tahun_lulus" name="tahun_lulus">
                            <small class="invalid-feedback" id="msg_tahun_lulus"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="tinggi_badan">TB</label>
                                    <input type="text" class="form-control" id="tinggi_badan" name="tinggi_badan">
                                    <small class="invalid-feedback" id="msg_tinggi_badan"></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label" for="berat_badan">BB</label>
                                    <input type="text" class="form-control" id="berat_badan" name="berat_badan">
                                    <small class="invalid-feedback" id="msg_berat_badan"></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="norek">No Rekening</label>
                            <input type="text" maxlength="20" class="form-control" id="norek" name="norek">
                            <small class="invalid-feedback" id="msg_norek"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="nama_bank">Nama Bank</label>
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank">
                            <small class="invalid-feedback" id="msg_nama_bank"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="provinsi">Provinsi</label>
                            <select name="provinsi" id="provinsi" class="form-control select2">
                                <option value="">- Pilih Nama Provinsi -</option>
                                @foreach ($provinsi as $prov)
                                    <option value="{{ $prov->kode }}">
                                        {{ ucwords(strtolower($prov->nama)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="invalid-feedback" id="msg_provinsi"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="kota">Kabupaten/Kota</label>
                            <select name="kota" id="kota" class="form-control select2">
                                <option value="">-Pilih Nama Kabupaten-</option>
                            </select>
                            <small class="invalid-feedback" id="msg_kota"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="kecamatan">Kecamatan</label>
                            <select name="kecamatan" id="kecamatan" class="form-control select2">
                                <option value="">-Pilih Nama Kecamatan-</option>
                            </select>
                            <small class="invalid-feedback" id="msg_kecamatan"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label" for="desa">Desa/Kelurahan</label>
                            <select name="desa" id="desa" class="form-control select2">
                                <option value="">- Pilih Nama Desa -</option>
                            </select>
                            <small class="invalid-feedback" id="msg_desa"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label" for="alamat">Alamat Dusun/rw/rt</label>
                            <textarea class="form-control" id="alamat" name="alamat" rows="2" placeholder="Enter ..."></textarea>
                            <small class="invalid-feedback" id="msg_alamat"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username">
                            <small class="invalid-feedback" id="msg_username"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="password">Password</label>
                            <input type="text" class="form-control" id="password" name="password">
                            <small class="invalid-feedback" id="msg_password"></small>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary btn-icon-split" id="SimpanAnggota" type="submit">Simpan
                        Anggota
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        document.querySelectorAll('.btn-group button').forEach(btn => {
            btn.addEventListener('click', function() {
                btn.parentElement.querySelectorAll('button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('jenis_kelamin').value = this.dataset.value;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
            });
        });

        $('.date').datetimepicker({
            i18n: {
                id: {
                    months: [
                        'Januari', 'Februari', 'Maret', 'April',
                        'Mei', 'Juni', 'Juli', 'Agustus',
                        'September', 'Oktober', 'November', 'Desember'
                    ],
                    dayOfWeekShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    dayOfWeek: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
                }
            },
            timepicker: false,
            format: 'd/m/Y'
        });

        $(document).on('change', '#provinsi', function() {
            var kode = $(this).val();
            $.get('/inspeksi/ambil_kab/' + kode, function(result) {
                if (result.success) {
                    setSelectValue('kota', result.data);
                }
            });
        });

        $(document).on('change', '#kota', function() {
            var kode = $(this).val();
            $.get('/inspeksi/ambil_kec/' + kode, function(result) {
                if (result.success) {
                    setSelectValue('kecamatan', result.data);
                }
            });
        });

        $(document).on('change', '#kecamatan', function() {
            var kode = $(this).val();
            $.get('/inspeksi/ambil_desa/' + kode, function(result) {
                if (result.success) {
                    setSelectValue('desa', result.data);
                }
            });
        });

        function setSelectValue(id, data) {
            var label = ucwords(id);
            $('#' + id).empty();
            $('#' + id).append('<option>-- Pilih ' + label + ' --</option>');
            data.forEach((val) => {
                $('#' + id).append('<option value="' + val.kode + '">' + val.nama + '</option>');
            });
        }

        function ucwords(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }

        $(document).on('click', '#SimpanAnggota', function(e) {
            e.preventDefault();
            $('small').html('');
            $('.is-invalid').removeClass('is-invalid');

            var form = $('#FormInputAnggota');
            var actionUrl = form.attr('action');

            $.ajax({
                type: 'POST',
                url: actionUrl,
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            title: result.msg,
                            text: "Tambahkan Register Anggota Baru?",
                            icon: "success",
                            showDenyButton: true,
                            confirmButtonText: "Tambahkan",
                            denyButtonText: `Tidak`
                        }).then((res) => {
                            if (res.isConfirmed) {
                                window.location.reload();
                            } else {
                                window.location.href = '/inspeksi/anggota';
                            }
                        });
                    }
                },
                error: function(result) {
                    const response = result.responseJSON;
                    Swal.fire('Error', 'Cek kembali input yang anda masukkan', 'error');
                    if (response && typeof response === 'object') {
                        $.each(response, function(key, message) {
                            $('#' + key).addClass('is-invalid');
                            $('#msg_' + key).html(message[0]);
                        });
                    }
                }
            });
        });
    </script>
@endsection
