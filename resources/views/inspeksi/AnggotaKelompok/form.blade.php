<div class="card card-primary" id="formContainer" style="display:none;">
    <div class="card-header">
        <h3 class="card-title text-bold" id="formTitle"></h3>
    </div>
    <form action="" method="post" id="formAnggotaKelompok" autocomplete="off">
        @csrf

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="anggota_level_id">Nama Anggota</label>
                        <select class="form-control select2" id="anggota_level_id" name="anggota_level_id">
                            <option value="">-- Pilih Anggota --</option>
                        </select>
                        <small class="text-danger" id="msg_anggota_level_id"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kelompok_id">Nama Kelompok</label>
                        <select class="form-control select2" id="kelompok_id" name="kelompok_id">
                            <option value="">-- Pilih Kelompok --</option>
                        </select>
                        <small class="text-danger" id="msg_kelompok_id"></small>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="button" id="btnCancel" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" id="SimpanKelompok" class="btn btn-primary btn-sm">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<br>
