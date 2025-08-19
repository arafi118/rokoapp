<div class="card card-primary" id="formContainer" style="display:none;">
    <div class="card-header">
        <h3 class="card-title text-bold" id="formTitle"></h3>
    </div>
    <form action="" method="post" id="FormGroup" autocomplete="off">
        @csrf
        <input type="hidden" id="id_group" name="id_group">
        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama">Nama Kelompok</label>
                        <input type="text" class="form-control" id="nama" name="nama">
                        <small class="text-danger" id="msg_nama"></small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="mandor">Mandor</label>
                        <select class="form-control select2" id="mandor" name="mandor"></select>
                        <small class="text-danger" id="msg_mandor"></small>
                    </div>
                </div>
            </div>

            <div class="text-end">
                <button type="button" id="btnCancel" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" id="SimpanGroup" class="btn btn-primary btn-sm">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<br>
