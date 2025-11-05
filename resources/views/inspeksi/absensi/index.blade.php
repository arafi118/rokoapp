@extends('inspeksi.layouts.base')

@section('content')
    <div class="card position-relative">
        <div class="card-body">
            <div id="form-container">
                <form action="/inspeksi/absensi-karyawan" method="post" id="formScanQr">
                    @csrf

                    <input type="hidden" name="nik" id="nik">
                    <input type="hidden" name="waktu" id="waktu">
                    <div class="mb-3">
                        <label for="absensi" class="form-label">Jenis Absen</label>
                        <select id="absensi" name="absensi" class="select2 form-select form-select-lg"
                            data-allow-clear="true">
                            <option value="">-- Select Value --</option>
                            <option value="masuk">Masuk</option>
                            <option value="keluar">Keluar</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-info" data-bs-toggle="modal"
                            data-bs-target="#importAbsensi">Import Excel</button>
                        <button type="button" class="btn btn-primary ms-3" id="scanQr">Scan QR</button>
                    </div>
                </form>
            </div>

            <div id="video" style="display: none;" class="w-100">
                <video class="w-100 rounded"></video>

                <div class="d-flex justify-content-end mt-3">
                    <button type="button" class="btn btn-primary" id="stopQr">Stop QR</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="importAbsensi" tabindex="-1" aria-labelledby="importAbsensiLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importAbsensiLabel">
                        Import Absensi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="/inspeksi/absensi-karyawan/import" method="post" id="formImport"
                        enctype="multipart/form-data" target="_blank">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">File Absensi</label>
                            <input type="file" class="form-control" accept=".xlsx, .xls" id="file" name="file">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="import">Import</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="module">
        let NIK = [];

        import QrScanner from '/vendor/qr-scanner/qr-scanner.min.js';
        const qrScanner = new QrScanner(
            document.querySelector('video'),
            result => handleQrResult(result), {
                highlightScanRegion: true,
                highlightCodeOutline: true,
            },
        );

        $(document).on('click', '#scanQr', function() {
            var jenisAbsensi = $('#absensi').val();

            if (!jenisAbsensi) {
                Swal.fire("Error!", "Jenis absensi belum dipilih", "error");
                return;
            }

            $('#form-container').hide();
            $('#video').show();

            NIK = [];
            qrScanner.start();
        })

        $(document).on('click', '#stopQr', function() {
            $('#form-container').show();
            $('#video').hide();

            qrScanner.stop();
        })

        function handleQrResult(result) {
            const nik = result.data;
            if (NIK.includes(nik)) {
                return;
            }
            NIK.push(nik);

            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();

            $('#nik').val(nik);
            $('#waktu').val(`${hours}:${minutes}`);

            var form = $('#formScanQr');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function(result) {
                    if (result.success) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: result.msg,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        })
                    }
                },
                error: function(xhr) {
                    let response = xhr.responseJSON;
                    console.log(response)
                    Swal.fire("Error!", response?.message || "Terjadi kesalahan", "error");

                    NIK.splice(NIK.indexOf(nik), 1);
                }
            })
        }
    </script>

    <script>
        $(document).on('click', '#import', function() {
            var file = $('#file')[0].files[0];
            if (!file) {
                Swal.fire("Error!", "File belum dipilih", "error");
                return;
            }

            var form = $('#formImport');
            form.submit();
        })
    </script>
@endsection
