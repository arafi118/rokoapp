@extends('mandor.layouts.base')

@section('content')
    <div class="card position-relative">
        <div class="card-body">
            <video class="w-100"></video>
        </div>
    </div>
@endsection

@section('script')
    <script type="module">
        import QrScanner from '/vendor/qr-scanner/qr-scanner.min.js';
        const qrScanner = new QrScanner(
            document.querySelector('video'),
            result => console.log('decoded qr code:', result), {
                highlightScanRegion: true,
                highlightCodeOutline: true,
            },
        );

        qrScanner.start();
    </script>
@endsection
