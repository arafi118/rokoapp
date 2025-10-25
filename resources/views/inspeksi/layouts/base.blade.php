<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} | {{ $title ?? 'Dashboard' }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Berry is trending dashboard template made using Bootstrap 5 design framework.">
    <meta name="keywords" content="Bootstrap admin template, Dashboard UI Kit, Backend Panel, Laravel dashboard">
    <meta name="author" content="ColorlibHQ, codedthemes">
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="supported-color-schemes" content="light dark" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simplebar@6.2.6/dist/simplebar.min.css" />

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <link rel="preload" href="/assets/css/adminlte.css" as="style" />
    <link rel="stylesheet" href="/assets/css/adminlte.min.css?v=3.2.0">

    <style>
        .modal-backdrop.show {
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .kanban-dragging {
            opacity: .4;
        }

        .gu-mirror {
            position: fixed !important;
            margin: 0 !important;
            transform: rotate(2deg) scale(1.02);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .25);
            border-radius: 10px;
            opacity: .95;
            cursor: grabbing;
            z-index: 9999;
            pointer-events: none;
            width: auto !important;
            height: auto !important;
            padding: 0 !important;
        }

        .pc-kanban-cards .card {
            transition: all .25s ease;
        }

        .gu-transit {
            opacity: .3;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            gap: 8px;
        }

        .select2-container--bootstrap4 .select2-results__option {
            background-color: #fff !important;
            color: #333 !important;
        }

        .select2-container--bootstrap4 .select2-results__option--highlighted {
            background-color: #61a7f7 !important;
            /* warna oranye aktif */
            color: #fff !important;
        }

        .select2-dropdown {
            background-color: #fff !important;
            border-color: #ccc !important;
        }

        .select2-search__field {
            background-color: #fff !important;
            color: #333 !important;
        }

        .table-middle tr td {
            vertical-align: middle !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>

    @yield('style')
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#">
                            <i class="fa fa-bars"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="fa fa-expand"></i>
                            <i data-lte-icon="minimize" class="fa fa-compress" style="display:none"></i>
                        </a>
                    </li>
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link">
                            <img src="/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow"
                                alt="User Image" />
                            <span class="d-none d-md-inline">Alexander Pierce</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="/dashboard" class="brand-link">
                    <img src="/assets/img/AdminLTELogo.png" alt="Logo" class="brand-image opacity-75 shadow" />
                    <span class="brand-text fw-light">{{ env('APP_NAME') }}</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">@include('inspeksi.layouts.sidebar')</nav>
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
                                        if ($loop->iteration == 1) {
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
            <div class="float-end d-none d-sm-inline">Magelang Jawa Tengah</div>
            <strong>© 2025 PT. Asta Brata Teknologi - V001.</strong>
        </footer>
    </div>

    @yield('modal')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="/assets/js/adminlte.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.6.6/dragula.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/simplebar@6.2.6/dist/simplebar.min.js"></script>

    <script>
        const toastMixin = Swal.mixin({
            toast: true,
            position: 'top-right',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
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
        });
    </script>

    @yield('script')
</body>

</html>
