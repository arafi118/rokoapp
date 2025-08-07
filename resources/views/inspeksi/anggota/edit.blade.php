@extends('inspeksi.layouts.base')

@section('content')
    <div class="card">
        <form action="/inspeksi/anggota/{{ $anggotum->id }}" method="post" id="FormUpdateAnggota" autocomplete="off">
            @csrf
            @method('PUT')
            <input type="hidden" name="_method" value="PUT">

            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="text" class="form-control date" id="tanggal_masuk" name="tanggal_masuk"
                                value="{{ \Carbon\Carbon::parse(optional($anggotum->level_aktif)->tanggal_masuk ?? now())->format('d/m/Y') }}">
                            <small class="text-danger" id="msg_tanggal_masuk"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="level">Operator Produksi</label>
                            <select name="level" id="level" class="form-control select2">
                                <option value="">- Pilih Level -</option>
                                @foreach ($level as $lev)
                                    <option value="{{ $lev->id }}"
                                        {{ optional($anggotum->level_aktif)->id == $lev->id ? 'selected' : '' }}>
                                        {{ ucwords(strtolower($lev->nama)) }} (( {{ ucwords(strtolower($lev->inisial)) }} ))
                                    </option>
                                @endforeach

                            </select>
                            <small class="text-danger" id="msg_level"></small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="{{ $anggotum->nama }}">
                            <small class="text-danger" id="msg_nama"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="no_kk">No.KK</label>
                            <input type="text" maxlength="16" class="form-control" id="no_kk" name="no_kk"
                                value="{{ $anggotum->no_kk }}">
                            <small class="text-danger" id="msg_no_kk"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nik">NIK</label>
                            <input type="text" maxlength="16" class="form-control" id="nik" name="nik"
                                value="{{ $anggotum->nik }}">
                            <small class="text-danger" id="msg_nik"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status Perkawinan</label>
                            <select class="form-control select2" id="status" name="status">
                                @php
                                    $status_list = ['belum menikah', 'menikah', 'cerai hidup', 'cerai mati'];
                                @endphp
                                <option value="">-- Pilih Status Perkawinan--</option>
                                @foreach ($status_list as $status)
                                    <option value="{{ $status }}"
                                        {{ $anggotum->status == $status ? 'selected' : '' }}>
                                        {{ ucwords($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_status"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tempat_lahir">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir"
                                value="{{ $anggotum->tempat_lahir }}">
                            <small class="text-danger" id="msg_tempat_lahir"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_lahir">Tanggal Lahir</label>
                            <input type="text" class="form-control date" id="tanggal_lahir" name="tanggal_lahir"
                                value="{{ \Carbon\Carbon::parse($anggotum->tanggal_lahir)->format('d/m/Y') }}">
                            <small class="text-danger" id="msg_tanggal_lahir"></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select class="form-control select2" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">-- Pilih --</option>
                                <option value="L" {{ $anggotum->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki
                                </option>
                                <option value="P" {{ $anggotum->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan
                                </option>
                            </select>
                            <small class="text-danger" id="msg_jenis_kelamin"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="agama">Agama</label>
                            <select class="form-control select2" id="agama" name="agama">
                                <option value="">-- Pilih Agama --</option>
                                @php
                                    $agama_list = [
                                        'islam',
                                        'kristen_protestan',
                                        'katolik',
                                        'hindu',
                                        'buddha',
                                        'khonghucu',
                                        'lainnya',
                                    ];
                                @endphp
                                @foreach ($agama_list as $agama)
                                    <option value="{{ $agama }}"
                                        {{ $anggotum->agama == $agama ? 'selected' : '' }}>
                                        {{ ucwords(str_replace('_', ' ', $agama)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_agama"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <select name="provinsi" id="provinsi" class="form-control select2">
                                <option value="">Pilih Nama Provinsi</option>
                                @foreach ($provinsi as $prov)
                                    <option value="{{ $prov->kode }}"
                                        {{ $anggotum->provinsi == $prov->kode ? 'selected' : '' }}>
                                        {{ ucwords(strtolower($prov->nama)) }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-danger" id="msg_provinsi"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kota">Kabupaten/Kota</label>
                            <select name="kota" id="kota" class="form-control select2">
                                <option value="{{ $anggotum->kota }}">{{ $anggotum->kota_nama ?? '-- Pilih --' }}
                                </option>
                            </select>
                            <small class="text-danger" id="msg_kota"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <select name="kecamatan" id="kecamatan" class="form-control select2">
                                <option value="{{ $anggotum->kecamatan }}">
                                    {{ $anggotum->kecamatan_nama ?? '-- Pilih --' }}
                                </option>
                            </select>
                            <small class="text-danger" id="msg_kecamatan"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="desa">Desa/Kelurahan</label>
                            <select name="desa" id="desa" class="form-control select2">
                                <option value="{{ $anggotum->desa }}">{{ $anggotum->desa_nama ?? '-- Pilih --' }}
                                </option>
                            </select>
                            <small class="text-danger" id="msg_desa"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="alamat">Alamat Dusun/rw/rt</label>
                            <input type="text" class="form-control" id="alamat" name="alamat"
                                value="{{ $anggotum->alamat }}">
                            <small class="text-danger" id="msg_alamat"></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="tinggi_badan">TB</label>
                            <input type="text" class="form-control" id="tinggi_badan" name="tinggi_badan"
                                value="{{ $anggotum->tinggi_badan }}">
                            <small class="text-danger" id="msg_tinggi_badan"></small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="berat_badan">BB</label>
                            <input type="text" class="form-control" id="berat_badan" name="berat_badan"
                                value="{{ $anggotum->berat_badan }}">
                            <small class="text-danger" id="msg_berat_badan"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nama_bank">Nama Bank</label>
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                value="{{ $anggotum->nama_bank }}">
                            <small class="text-danger" id="msg_nama_bank"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ijazah">Ijazah</label>
                            <input type="text" class="form-control" id="ijazah" name="ijazah"
                                value="{{ $anggotum->ijazah }}">
                            <small class="text-danger" id="msg_ijazah"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="jurusan">Jurusan</label>
                            <input type="text" class="form-control" id="jurusan" name="jurusan"
                                value="{{ $anggotum->jurusan }}">
                            <small class="text-danger" id="msg_jurusan"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tahun_lulus">Tahun Lulus</label>
                            <input type="text" class="form-control" id="tahun_lulus" name="tahun_lulus"
                                value="{{ $anggotum->tahun_lulus }}">
                            <small class="text-danger" id="msg_tahun_lulus"></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="norek">No Rekening</label>
                            <input type="text" maxlength="20" class="form-control" id="norek" name="norek"
                                value="{{ $anggotum->norek }}">
                            <small class="text-danger" id="msg_norek"></small>
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="nama_ibu_kandung">Nama Ibu Kandung</label>
                            <input type="text" class="form-control" id="nama_ibu_kandung" name="nama_ibu_kandung"
                                value="{{ $anggotum->nama_ibu_kandung }}">
                            <small class="text-danger" id="msg_nama_ibu_kandung"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="{{ $anggotum->username }}">
                            <small class="text-danger" id="msg_username"></small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password">Password (Isi jika ingin diubah)</label>
                            <input type="text" class="form-control" id="password" name="password">
                            <small class="text-danger" id="msg_password"></small>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary btn-icon-split" id="SimpanEditAnggota" type="submit">Update
                        Anggota</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('script')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
            });
        });
        jQuery.datetimepicker.setLocale('de');
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
        $(document).on('click', '#SimpanEditAnggota', function(e) {
            e.preventDefault();
            $('small').html('');
            $('.is-invalid').removeClass('is-invalid');

            var form = $('#FormUpdateAnggota');
            var actionUrl = form.attr('action');
            var formData = form.serialize();

            $.ajax({
                type: 'POST',
                url: actionUrl,
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(result) {
                    if (result.success) {
                        toastMixin.fire({
                            title: result.msg,
                            icon: 'success'
                        });
                        setTimeout(function() {
                            window.location.href = '/inspeksi/anggota';
                        }, 3000);
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;

                    toastMixin.fire({
                        title: 'Cek kembali input yang Anda masukkan!',
                        icon: 'error'
                    });

                    if (response && response.errors) {
                        $.each(response.errors, function(key, messages) {
                            const input = $('#' + key);
                            input.addClass('is-invalid');
                            $('#msg_' + key).html(messages[0]);
                        });
                    }
                }
            });
        });
    </script>
@endsection
