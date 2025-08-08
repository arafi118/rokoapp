@extends('anggota.layouts.base')
@section('content')
    @php
        use Carbon\Carbon;
    @endphp

    <style>
        #tabel-produksi th,
        #tabel-produksi td {
            border: 1px solid #dee2e6 !important;
        }
    </style>

    <div class="card">
        <div class="card-body">
            <table id="tabel-produksi" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th class="text-center" style="width: 50px;">No</th>
                        <th class="text-center" style="width: 100px;">Jumlah</th>
                        <th class="text-center" style="width: 150px;">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $row)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $row->jumlah }}</td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($row->tanggal_input)->locale('id')->translatedFormat('d F Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td class="text-center">Tidak Ada Data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            $('#tabel-produksi').DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: false,
                pageLength: 10
                // opsi yang bisa bikin data hilang dihapus
            });
        });
    </script>
@endsection
