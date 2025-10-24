<div class="card card-primary" id="formContainer" style="display:none;">
    <div class="card-header">
        <h3 class="card-title text-bold" id="formTitle"></h3>
    </div>
    <form action="" method="post" id="FormKaryawan" autocomplete="off">
        @csrf
        <input type="hidden" id="id_karyawan" name="id_karyawan">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nama">Nama Karyawan</label>
                        <select class="form-control select2" id="anggota_id" name="anggota_id"></select>
                        <small class="text-danger" id="msg_nama"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="group_id">Group</label>
                        <select class="form-control select2" id="group_id" name="group_id"></select>
                        <small class="text-danger" id="msg_group_id"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="posisi">Posisi</label>
                        <select class="form-control select2" id="meja_id" name="meja_id"></select>
                        <small class="text-danger" id="msg_posisi"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_masuk">Tanggal Masuk</label>
                        <input type="text" class="form-control date" id="tanggal_masuk" name="tanggal_masuk"
                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <small class="text-danger" id="msg_tanggal_masuk"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="tanggal_keluar">Tanggal Keluar</label>
                        <input type="text" class="form-control date" id="tanggal_keluar" name="tanggal_keluar"
                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <small class="text-danger" id="msg_tanggal_keluar"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="posisi">Level</label>
                        <select class="form-control select2" id="level_id" name="level_id"></select>
                        <small class="text-danger" id="msg_posisi"></small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">Status</label><select id="status" name="status"
                            class="form-control select2">
                            <option value="">-- Pilih Status --</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                        <small class="text-danger" id="msg_status"></small>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="button" id="btnCancel" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" id="SimpanKaryawan" class="btn btn-primary btn-sm">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<br>
