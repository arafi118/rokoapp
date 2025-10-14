<div class="card card-primary" id="formContainer" style="display:none;">
    <div class="card-header">
        <h3 class="card-title text-bold" id="formTitle"></h3>
    </div>
    <form action="" method="post" id="FormMeja" autocomplete="off">
        @csrf
        <input type="hidden" id="id_meja" name="id_meja">

        <div class="card-body">
            <div class="row mb-2">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="nama_meja">Nama Meja</label>
                        <input type="text" class="form-control" id="nama_meja" name="nama_meja">
                        <small class="text-danger" id="msg_nama_meja"></small>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <button type="button" id="btnCancel" class="btn btn-secondary btn-sm">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <button type="submit" id="SimpanMeja" class="btn btn-primary btn-sm">
                    <i class="bi bi-save"></i> Simpan
                </button>
            </div>
        </div>
    </form>
</div>
<br>
