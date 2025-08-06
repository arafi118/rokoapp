@extends('anggota.layouts.base')

@section('content')
    <div class="container">
        <div class="card card-warning">
            <div class="card-header">
                <h3 class="card-title">Input Produksi</h3>
            </div>
            <form action="{{ url('/anggota') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tanggal_input" class="form-label">Tanggal Input</label>
                        <input class="form-control" type="date" name="tanggal_input" id="tanggal_input"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah Produksi</label>
                        <input class="form-control" type="number" name="jumlah" id="jumlah"
                            placeholder="Masukkan jumlah produksi" required>
                    </div>


                </div>

                <div class="card-footer text-end">
                    <div class="card-footer text-end">
                        <button type="submit" class="btn" style="background-color:#800000; color:white;">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
