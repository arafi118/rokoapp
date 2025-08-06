@extends('inspeksi.layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Form Input</h3>
                </div>

                <form action="/anggota/create" method="post" id="FormInputAnggota">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="no_kk">No.KK</label>
                                    <input type="text" maxlength="16" class="form-control" id="no_kk" name="no_kk">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nik">NIK</label>
                                    <input type="text" maxlength="16" class="form-control" id="nik" name="nik">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select class="form-control select2" type="text" id="jenis_kelamin"
                                        name="jenis_kelamin">
                                        <option value="">-- Pilih --</option>
                                        <option value="L">laki-laki</option>
                                        <option value="P">Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="tempat_lahir">Temppat Lahir</label>
                                    <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir</label>
                                    <input type="text" class="form-control date" value="{{ date('d/m/Y') }}"
                                        id="tanggal_lahir" name="tanggal_lahir">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="agama">Agama</label>
                                    <select class="form-control select2" id="agama" name="agama">
                                        <option value="">-- Pilih Agama --</option>
                                        <option value="islam">Islam</option>
                                        <option value="kristen_protestan">Kristen Protestan</option>
                                        <option value="katolik">Katolik</option>
                                        <option value="hindu">Hindu</option>
                                        <option value="buddha">Buddha</option>
                                        <option value="khonghucu">Khonghucu</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kota">Provinsi</label>
                                    <input type="text" class="form-control" id="kota" name="kota">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kota">Kabupaten/Kota</label>
                                    <input type="text" class="form-control" id="kota" name="kota">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="kecamatan">Kecamatan</label>
                                    <input type="text" class="form-control" id="kecamatan" name="kecamatan">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="desa">Desa</label>
                                    <input type="text" class="form-control" id="desa" name="desa">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label for="alamat">Alamat Dusun/rw/rt</label>
                                    <input type="text" class="form-control" id="alamat" name="alamat">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status Pernikahan</label>
                                    <select class="form-control select2" id="status" name="status">
                                        <option value="">-- Pilih --</option>
                                        <option value="belum menikah">Belum Menikah</option>
                                        <option value="menikah">Menikah</option>
                                        <option value="cerai hidup">Cerai Hidup</option>
                                        <option value="cerai mati">Cerai Mati</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nama_bank">Nama Bank</label>
                                    <input type="text" class="form-control" id="nama_bank" name="nama_bank">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="norek">No Rekening</label>
                                    <input type="text" class="form-control" id="norek" name="norek">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tinggi_badan">TB</label>
                                    <input type="number" class="form-control" id="tinggi_badan" name="tinggi_badan">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="berat_badan">BB</label>
                                    <input type="number" class="form-control" id="berat_badan" name="berat_badan">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ijazah">Ijazah</label>
                                    <input type="text" class="form-control" id="ijazah" name="ijazah">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="jurusan">Jurusan</label>
                                    <input type="text" class="form-control" id="jurusan" name="jurusan">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tahun_lulus">Tahun Lulus</label>
                                    <input type="text" class="form-control" id="tahun_lulus" name="tahun_lulus">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="nama_ibu_kandung">Nama Ibu Kandung</label>
                                    <input type="text" class="form-control" id="nama_ibu_kandung"
                                        name="nama_ibu_kandung">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" class="form-control" id="password" name="password">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-secondary btn-icon-split" id="SimpanAnggota" type="submit">Simpan
                                Anggota
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                de: {
                    months: [
                        'Januari', 'Februari', 'Maret', 'April',
                        'Mei', 'Juni', 'Juli', 'Agustus',
                        'September', 'Oktober', 'November', 'Desember',
                    ],
                    days: [
                        'Minggu', 'Senin', 'Selasa', 'Rabu',
                        'Kamis', 'Jumat', 'Sabtu',
                    ],
                    dayOfWeekShort: [
                        "Min", "Sen", "Sel", "Rab",
                        "Kam", "Jum", "Sab",
                    ],
                    dayOfWeek: [
                        "Minggu", "Senin", "Selasa", "Rabu",
                        "Kamis", "Jumat", "Sabtu",
                    ],
                    monthsShort: [
                        "Jan", "Feb", "Mar", "Apr",
                        "Mei", "Jun", "Jul", "Agu",
                        "Sep", "Okt", "Nov", "Des",
                    ],
                    daysShort: [
                        "Ming.", "Sen", "Sel", "Rab",
                        "Kam", "Jum", "Sab",
                    ],
                    daysMin: [
                        "Ming.", "Sen", "Sel", "Rab",
                        "Kam", "Jum", "Sab",
                    ]
                }
            },
            timepicker: false,
            format: 'd/m/Y'
        });
    </script>
@endsection
