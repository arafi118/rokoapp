<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation"
    data-accordion="false" id="navigation">
    <li class="nav-item">
        <a href="/anggota" class="nav-link active">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
        </a>
    </li>

    <li class="nav-header">Master Data</li>
    <li class="nav-item">
        <a href="/anggota/produksi" class="nav-link">
            <i class="nav-icon bi bi-card-list"></i>
            <p>Daftar Produksi</p>
        </a>
    </li>
    <li class="nav-header">Pelaporan</li>
    <li class="nav-item">
        <a href="/anggota/laporan" class="nav-link">
            <i class="nav-icon bi bi-calendar-check"></i>
            <p>Laporan</p>
        </a>
    </li>

    <li class="nav-header">Logout</li>
    <li class="nav-item">
        <a href="#" id="btnLogout" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Logout</p>
        </a>
        <form id="formLogout" action="/anggota/logout" method="POST" style="display:none;">
            @csrf
        </form>
    </li>
</ul>
