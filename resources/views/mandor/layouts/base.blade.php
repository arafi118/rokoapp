@php
    $anggota = Auth::user();
@endphp
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} | {{ $title }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="title" content="{{ env('APP_NAME') }} | {{ $title }}" />
    <link rel="preload" href="/assets/css/adminlte.css" as="style" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" media="print" onload="this.media='all'" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="/assets/css/adminlte.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#"><i class="bi bi-list"></i></a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display:none"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#"
                            class="nav-link dropdown-toggle d-flex align-items-center text-decoration-none"
                            data-bs-toggle="dropdown" style="width:auto;transition:all 0.2s ease;">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('storage/profil/' . ($anggota->foto ?? 'default.jpg')) }}"
                                    onerror="this.onerror=null;this.src='{{ asset('storage/profil/default.jpg') }}';"
                                    class="rounded-circle me-2 user-image" alt="User Image"
                                    style="object-fit:cover;width:40px;height:40px;border:2px solid rgba(108,108,108,0.7);transition:transform 0.3s ease;"
                                    onmouseover="this.style.transform='scale(1.05)'"
                                    onmouseout="this.style.transform='scale(1)'">
                                <span
                                    class="fw-semibold text-dark d-none d-md-inline">{{ $anggota->nama ?? 'Guest User' }}</span>
                            </div>
                            <i class="bi bi-chevron-down ms-2 text-muted"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 overflow-hidden">
                            <li class="dropdown-header text-center pb-2">
                                <strong class="fw-semibold">{{ $anggota->nama }}</strong><br>
                                <small class="text-muted">{{ $anggota->getjabatan->nama }}</small>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-2">
                            </li>
                            <li>
                                <a href="/mandor/profile" class="dropdown-item rounded-3 py-2">
                                    <i class="bi bi-person-circle me-2 ms-2"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <a href="#" id="btnLogout" class="dropdown-item text-danger rounded-3 py-2">
                                    <i class="bi bi-box-arrow-right me-2 ms-2"></i> Logout
                                </a>
                                <form id="formLogout" action="/mandor/logout" method="POST" style="display:none;">
                                    @csrf</form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="/assets/index.html" class="brand-link">
                    <img src="/assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">AdminLTE 4</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">@include('anggota.layouts.sidebar')</nav>
            </div>
        </aside>

        <main class="app-main">
            <div class="app-content-header">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <h3 class="mb-0">{{ $title }}</h3>
                        </div>
                        <div class="col-sm-6">
                            @php
                                $path = explode('/', request()->path());
                                $basePath = '';
                            @endphp
                            <ol class="breadcrumb float-sm-end">
                                @foreach ($path as $p)
                                    @php
                                        $pathName = str_replace('-', ' ', $p);
                                        if ($loop->iteration == '1') {
                                            $pathName = 'Home';
                                        }
                                    @endphp
                                    <li class="breadcrumb-item {{ $p == end($path) ? 'active' : '' }}">
                                        @if ($p == end($path))
                                            {{ ucwords($pathName) }}
                                        @else
                                            <a
                                                href="/{{ $basePath }}{{ $p }}">{{ ucwords($pathName) }}</a>
                                        @endif
                                    </li>
                                    @php $basePath .= $p . '/'; @endphp
                                @endforeach
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">@yield('content')</div>
            </div>
        </main>

        <footer class="app-footer">
            <div class="float-end d-none d-sm-inline">Anything you want</div>
            <strong>Copyright &copy; 2014-2025
                <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
            </strong> All rights reserved.
        </footer>
    </div>

    <!-- SCRIPT ZONE -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1"></script>
    <script src="/assets/js/adminlte.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const toastMixin = Swal.mixin({
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        @if (session('success'))
            toastMixin.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif
        @if (session('error'))
            toastMixin.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif

        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4'
            });
            $('.datatable').DataTable();
            const sidebarWrapper = document.querySelector('.sidebar-wrapper');
            if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars)
                OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                    scrollbars: {
                        autoHide: 'leave'
                    }
                });
        });

        $(document).on('click', '#btnLogout', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Yakin mau logout?',
                text: "Kamu akan keluar dari sistem.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) $('#formLogout').submit();
            });
        });
    </script>
    @yield('script')
</body>

</html>
