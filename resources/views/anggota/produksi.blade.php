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
                        <th class="text-center">Jumlah Baik</th>
                        <th class="text-center">Jumlah Buruk</th>
                        <th class="text-center">Jumlah Buruk 2</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $row->jumlah_baik }}</td>
                            <td class="text-center">{{ $row->jumlah_buruk }}</td>
                            <td class="text-center">{{ $row->jumlah_buruk2 }}</td>
                            <td class="text-center">
                                {{ $row->jumlah_baik + $row->jumlah_buruk + $row->jumlah_buruk2 }}
                            </td>
                            <td class="text-center">
                                {{ \Carbon\Carbon::parse($row->tanggal)->locale('id')->translatedFormat('d F Y') }}
                            </td>
                        </tr>
                    @endforeach
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
                pageLength: 10,
                language: {
                    emptyTable: "Tidak ada data tersedia"
                },
                columnDefs: [{
                    targets: '_all',
                    defaultContent: ''
                }]
            });
        });
    </script>
@endsection
