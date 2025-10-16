<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} | {{ $title ?? 'Dashboard' }}</title>

    <!-- Meta Tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Berry is trending dashboard template made using Bootstrap 5 design framework. Berry is available in Bootstrap, React, CodeIgniter, Angular, and .NET.">
    <meta name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Backend Panel, Laravel dashboard, AdminLTE, Berry template">
    <meta name="author" content="ColorlibHQ, codedthemes">
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <meta name="supported-color-schemes" content="light dark" />

    <!-- Favicon -->
    <link rel="icon" href="https://berrydashboard.io/codeignitor/default/public/assets/images/favicon.svg"
        type="image/x-icon">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Icons -->
    <link rel="stylesheet"
        href="https://berrydashboard.io/codeignitor/default/public/assets/fonts/phosphor/duotone/style.css">
    <link rel="stylesheet"
        href="https://berrydashboard.io/codeignitor/default/public/assets/fonts/tabler-icons.min.css">
    <link rel="stylesheet" href="https://berrydashboard.io/codeignitor/default/public/assets/fonts/feather.css">
    <link rel="stylesheet" href="https://berrydashboard.io/codeignitor/default/public/assets/fonts/fontawesome.css">
    <link rel="stylesheet" href="https://berrydashboard.io/codeignitor/default/public/assets/fonts/material.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />

    <!-- Plugin CSS -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css"
        integrity="sha512-f0tzWhCwVFS3WeYaofoLWkTP62ObhewQ1EZn65oSYDZUg1+CyywGKkWzm8BxaJj5HGKI72PnMH9jYyIFz+GH7g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">

    <!-- Template CSS -->
    <link rel="preload" href="/assets/css/adminlte.css" as="style" />
    <link rel="stylesheet" href="/assets/css/adminlte.min.css?v=3.2.0">
    <link rel="stylesheet" href="https://berrydashboard.io/codeignitor/default/public/assets/css/style.css"
        id="main-style-link">
    <link rel="stylesheet" href="https://berrydashboard.io/codeignitor/default/public/assets/css/style-preset.css">

    <!-- Inline custom -->
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
    </style>

    @yield('style')
</head>


<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#"><i
                                class="bi bi-list"></i></a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#" data-lte-toggle="fullscreen"><i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i><i data-lte-icon="minimize"
                                class="bi bi-fullscreen-exit" style="display:none"></i></a></li>
                    <li class="nav-item dropdown user-menu"><a href="#" class="nav-link"><img
                                src="/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow"
                                alt="User Image" /><span class="d-none d-md-inline">Alexander Pierce</span></a></li>
                </ul>
            </div>
        </nav>
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="/assets/index.html" class="brand-link"><img src="/assets/img/AdminLTELogo.png"
                        alt="AdminLTE Logo" class="brand-image opacity-75 shadow" /><span
                        class="brand-text fw-light">AdminLTE 4</span></a>
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
            <strong>© 2025 PT. Asta Brata Teknologi - V001.</strong>.
        </footer>
    </div>
    @yield('modal')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.js"
        integrity="sha512-+UiyfI4KyV1uypmEqz9cOIJNwye+u+S58/hSwKEAeUMViTTqM9/L4lqu8UxJzhmzGpms8PzFJDzEqXL9niHyjA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <script src="/assets/js/adminlte.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/plugins/popper.min.js"></script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/plugins/simplebar.min.js"></script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/icon/custom-font.js"></script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/script.js"></script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/theme.js"></script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/plugins/feather.min.js"></script>
    <script>
        layout_change('light');
    </script>
    <script src="https://berrydashboard.io/codeignitor/default/public/assets/js/plugins/dragula.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                theme: 'bootstrap4',
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
                if (result.isConfirmed) {
                    $('#formLogout').submit();
                }
            });
        });
    </script>

    @yield('script')
</body>

</html>
