<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation" aria-label="Main navigation"
    data-accordion="false" id="navigation">
    <li class="nav-item">
        <a href="/inspeksi" class="nav-link active">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-header">Master Data</li>
    <li class="nav-item">
        <a href="/inspeksi/level" class="nav-link">
            <i class="nav-icon bi bi-diagram-3"></i>
            <p>Level</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="/inspeksi/rencana" class="nav-link">
            <i class="nav-icon bi bi-calendar-check"></i>
            <p>Rencana</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon bi bi-person-badge"></i>
            <p>Anggota<i class="nav-arrow bi bi-chevron-right"></i></p>
        </a>
        <ul class="nav nav-treeview">
            <li class="nav-item">
                <a href="/inspeksi/anggota/create" class="nav-link">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Register Anggota</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="/inspeksi/anggota" class="nav-link">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Data Anggota</p>
                </a>
            </li>
        </ul>
    </li>
    <li class="nav-item">
        <a href="/inspeksi/group" class="nav-link">
            <i class="nav-icon bi bi-people"></i>
            <p>Group</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="/inspeksi/karyawan" class="nav-link">
            <i class="nav-icon bi bi-person-lines-fill"></i>
            <p>Karyawan</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="/" class="nav-link">
            <i class="nav-icon bi bi-calendar-event"></i>
            <p>Jadwal</p>
        </a>
    </li>
    <li class="nav-header">Logout</li>
    <li class="nav-item">
        <a href="#" id="btnLogout" class="nav-link">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Logout</p>
        </a>
        <form id="formLogout" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </li>
</ul>
