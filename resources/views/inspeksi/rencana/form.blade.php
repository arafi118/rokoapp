<div class="card card-primary" id="formContainer" style="display:none;">
    <div class="card-header">
        <h3 class="card-title text-bold" id="formTitle"></h3>
    </div>
    <form action="" method="post" id="FormRencana" autocomplete="off">
        @csrf
        <input type="hidden" id="id_rencana" name="id_rencana">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal">Tanggal</label>
                        <input type="text" class="form-control date" id="tanggal" name="tanggal">
                        <small class="text-danger" id="msg_tanggal"></small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rencana_produksi">Rencana Produksi</label>
                        <input type="number" class="form-control" id="rencana_produksi" name="rencana_produksi">
                        <small class="text-danger" id="msg_rencana_produksi"></small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rencana_kehadiran">Rencana Kehadiran</label>
                        <input type="number" class="form-control" id="rencana_kehadiran" name="rencana_kehadiran">
                        <small class="text-danger" id="msg_rencana_kehadiran"></small>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rencana_karyawan">Rencana Karyawan</label>
                        <input type="number" class="form-control" id="rencana_karyawan" name="rencana_karyawan">
                        <small class="text-danger" id="msg_rencana_karyawan"></small>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="button" id="btnCancel" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" id="SimpanRencana" class="btn btn-primary btn-sm">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<br>
