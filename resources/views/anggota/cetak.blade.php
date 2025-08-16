<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Kartu Anggota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .card {
            width: 300px;
            margin: 50px auto;
            /* posisi tengah */
        }

        .form-label {
            font-weight: normal;
            /* label tidak tebal */
            margin-top: 5px;
        }

        input.form-control {
            background-color: #f0f3f5;
        }
    </style>
</head>

<body onload="window.print()">

    <div class="card">
        <div class="card-body text-center">

            {{-- QR Code dengan background abu-abu --}}
            <div style="display: inline-block; background-color: #e9ecef; padding: 6px; border-radius: 4px;">
                {!! $qrCode !!}
            </div>

            {{-- Nama (tebal) --}}
            <div class="fw-bold mt-2" style="font-size: 1.1rem;">
                {{ $anggota->nama }}
            </div>

            {{-- Kode (kecil, warna abu-abu) --}}
            <div style="color: #6c757d; font-size: 0.9rem;">
                {{ $anggota->kode }}
            </div>

            <hr>

            {{-- Form Info Anggota --}}
            <div class="text-start">
                <label class="form-label">Jabatan</label>
                <input type="text" class="form-control form-control-sm"
                    value="{{ $anggota->getjabatan->nama === 'Anggota' ? 'Karyawan' : $anggota->getjabatan->nama }}"
                    disabled>
                <label class="form-label">No Induk</label>
                <input type="text" class="form-control mb-2" value="{{ $anggota->nik ?? '-' }}" disabled>
                <label class="form-label">Tempat Lahir</label>
                <input type="text" class="form-control mb-2" value="{{ $anggota->alamat ?? '-' }}" disabled>


            </div>
        </div>

    </div>

</body>

</html>
