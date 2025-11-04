@extends('inspeksi.layouts.base')

@section('content')
    <div class="row">
        <div class="col-md-12 pb-3">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body">
                    <div class="form-group">
                        <label for="filterKB">Pilih Kelompok</label>
                        <select name="filterKB[]" id="filterKB" class="form-control select2" multiple="multiple">
                            <option value="">- Pilih Kelompok Kanban -</option>
                            <option value="Tsemua">Tampilkan Semua</option>
                            @foreach ($listKelompok as $kelompok)
                                <option value="{{ $kelompok->id }}">{{ $kelompok->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="msg_provinsi"></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="tab-content">
                <div class="tab-pane show active" role="tabpanel">
                    <div class="row">
                        @foreach ($dataKaryawan as $groupId => $group)
                            <div class="col-md-6 mb-3">
                                <div class="card shadow-sm bg-info">
                                    <div class="card-header bg-secondary text-white fw-bold text-center">
                                        {{ $group['group_name'] }}
                                    </div>
                                    <div class="card-body">
                                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                                            @foreach ($group['meja'] as $mejaId => $mejaGroup)
                                                <div class="card border">
                                                    <div class="card-body p-2">
                                                        <div class="kanban" data-group="{{ $groupId }}"
                                                            data-meja="{{ $mejaGroup['meja_id'] }}"
                                                            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                                                            @foreach ($mejaGroup['karyawan'] as $karyawan)
                                                                <div class="card shadow-sm text-center"
                                                                    style="background-color: {{ $karyawan['warna'] }}; color: #fff;"
                                                                    data-meja-saat-ini="{{ $groupId }}-{{ $mejaId }}"
                                                                    data-id-karyawan="{{ $karyawan['id'] }}"
                                                                    data-level="{{ $karyawan['level'] }}"
                                                                    data-inisiallevel="{{ $karyawan['inisial'] }}"
                                                                    data-nik="{{ $karyawan['nik'] }}"
                                                                    data-kode="{{ $karyawan['kode_karyawan'] }}"
                                                                    data-jabatan="{{ $karyawan['jabatan'] }}"
                                                                    data-nama="{{ $karyawan['nama'] }}">
                                                                    <div class="card-body py-2 position-relative">
                                                                        <a href="#"
                                                                            class="text-decoration-none text-white ViewKaryawan dropdown-toggle"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#ViewKaryawan"
                                                                            style="font-size: 12px;">
                                                                            {{ $karyawan['kode_karyawan'] }}
                                                                        </a>
                                                                        <div
                                                                            class="kanban-dropdown dropdown position-absolute">
                                                                            <ul
                                                                                class="dropdown-menu shadow rounded dropdown-container">
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-2 pt-2">
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success" id="SimpanTempatKerja">Simpan Data Tempat Kerja</button>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="ViewKaryawan" tabindex="-1" aria-labelledby="ViewKaryawanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="ViewKaryawanLabel">Detail Karyawan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <img id="fotoKaryawan" src="/assets/img/user.png" class="rounded-circle border shadow-sm mb-2"
                                width="140" height="140">
                            <div><small class="text-muted" id="modaljabatan">-</small></div>
                        </div>
                        <div class="col-md-8">
                            <h3 class="fw-bold mb-2 text-center">Kartu Karyawan Aktif</h3>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td style="width: 130px;"><strong>Nama :</strong></td>
                                    <td id="modalNama">-</td>
                                </tr>
                                <tr>
                                    <td><strong>NIK :</strong></td>
                                    <td id="modalNik">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Kode :</strong></td>
                                    <td id="modalKode">-</td>
                                </tr>
                                <tr>
                                    <td><strong>Level :</strong></td>
                                    <td id="modallevel">-</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $(document).off('contextmenu.bs.dropdown.data-api');
            $(document).off('click.bs.dropdown.data-api');

            $('.select2').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            $('#filterKB').on('change', function() {
                const selectedValues = $(this).val() || [];
                const tampilSemua = selectedValues.includes('Tsemua') || selectedValues.length === 0;

                $('.col-md-6.mb-3').each(function() {
                    const groupId = $(this).find('.kanban').first().data(
                        'group');

                    if (tampilSemua || selectedValues.includes(String(groupId))) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });

        // --- Variabel Global ---
        let PERPINDAHAN_MEJA_KARYAWAN = [];

        // --- Modal Detail ---
        $(document).on("click", ".ViewKaryawan", function() {
            const parentCard = $(this).closest(".card");
            $("#modalNama").text(parentCard.data("nama"));
            $("#modalKode").text(parentCard.data("kode"));
            $("#modallevel").text(parentCard.data("level"));
            $("#modalNik").text(parentCard.data("nik"));
            $("#modaljabatan").text(parentCard.data("jabatan"));
            $("#ViewKaryawan").modal("show");
        });

        // --- Drag & Drop (Dragula) ---
        const drake = dragula($(".kanban").toArray(), {
            mirrorContainer: document.body,
            revertOnSpill: true,
        });

        drake.on("drag", (el) => $(el).addClass("kanban-dragging"));
        drake.on("drop", function(el, container, source) {
            $(el).removeClass("kanban-dragging");
            if (!container) return;

            const id = $(el).data("id-karyawan");
            const mejaSaatIni = $(el).data("meja-saat-ini");
            const groupTujuan = $(container).data("group");
            const mejaTujuan = $(container).data("meja");
            const mejaTujuanLabel = `${groupTujuan}-${mejaTujuan}`;
            const inisialBaru = $(el).data("inisiallevel");
            const countInTarget = $(container).find(".card").length;
            const jumlahGT = $(container).find('.card[data-inisiallevel="GT"]').length;

            if ((inisialBaru === 'GT' || inisialBaru === 'GL') && countInTarget > 4) {
                Swal.fire({
                    icon: "warning",
                    title: "Tidak boleh lebih dari 4 karyawan!",
                    text: "Karyawan dengan level GT atau GL hanya boleh maksimal 4 per meja.",
                    timer: 3000,
                    showConfirmButton: false
                });
                source.appendChild(el);
                return;
            }

            if (inisialBaru === "GT" && jumlahGT > 1) {
                Swal.fire({
                    icon: "warning",
                    title: "Duplikasi GT-(Gunting) tidak diperbolehkan!",
                    text: "Setiap meja hanya boleh memiliki 1 karyawan dengan inisial GT.",
                    timer: 3000,
                    showConfirmButton: false
                });
                if (source) source.appendChild(el);
                return;
            }

            const idx = PERPINDAHAN_MEJA_KARYAWAN.findIndex(x => x.id == id);
            if (idx >= 0) PERPINDAHAN_MEJA_KARYAWAN.splice(idx, 1);
            PERPINDAHAN_MEJA_KARYAWAN.push({
                id,
                meja_saat_ini: mejaSaatIni,
                meja_tujuan: mejaTujuanLabel
            });

            $(el).attr("data-meja-saat-ini", mejaTujuanLabel);
            kirimKeServer(id, mejaSaatIni, mejaTujuanLabel);
        });

        // --- Klik kanan (contextmenu) pindah kanban ---
        $(document).on("contextmenu", ".dropdown-toggle", function(e) {
            e.preventDefault();
            e.stopPropagation();

            const parentCard = $(this).closest(".card");
            const dropdown = parentCard.find(".kanban-dropdown");
            const container = dropdown.find(".dropdown-container");

            $(".kanban-dropdown").removeClass("open");

            const rect = parentCard[0].getBoundingClientRect();
            const isNearRight = rect.right > window.innerWidth - 250;
            dropdown.css(isNearRight ? {
                left: "auto",
                right: "292%"
            } : {
                left: "100%",
                right: "auto"
            });
            dropdown.addClass("open");

            container.html(`
            <li class="text-center fw-bold text-primary py-2 border-bottom bg-light">Pindahkan Kanban</li>
            <li class="text-center text-muted py-1">Memuat data...</li>
        `);

            $.getJSON("/inspeksi/tempat-kerja/group/list", function(groups) {
                let html = `
            <li class="text-center fw-bold text-primary py-2 border-bottom bg-light">Pindahkan Kanban</li>
            ${groups.map(g => `
                                <li class="dropdown-submenu">
                                    <a href="#" class="test d-flex justify-content-between align-items-center" data-group-id="${g.id}">
                                        <span>${g.text}</span>
                                    </a>
                                    <ul class="dropdown-menu submenu" id="submenu-${g.id}"></ul>
                                </li>
                            `).join("")}
            `;
                container.html(html);
            });
        });

        // --- Pilih meja tujuan ---
        $(document).on("click", ".dropdown-submenu a.test", function(e) {
            e.preventDefault();
            e.stopPropagation();

            const groupId = $(this).data("group-id");
            const dropdown = $(this).closest(".kanban-dropdown");
            const submenu = dropdown.find(`#submenu-${groupId}`);

            dropdown.find(".submenu").hide().removeData("loaded");
            submenu.show().html(`<li class="text-center text-muted py-1">Memuat meja...</li>`);

            $.getJSON("/inspeksi/tempat-kerja/meja/list", {
                group_id: groupId
            }, function(list) {
                submenu.html(list.map(m =>
                    `<li><a href="#" class="meja-item" data-group-id="${groupId}" data-meja-id="${m.id}">${m.text}</a></li>`
                ).join(""));
            });
        });

        // --- Klik meja item untuk pindah ---
        $(document).on("click", ".meja-item", function(e) {
            e.preventDefault();

            const groupTujuan = $(this).data("group-id");
            const mejaTujuan = $(this).data("meja-id");
            const mejaTujuanLabel = `${groupTujuan}-${mejaTujuan}`;

            const parentCard = $(".kanban-dropdown.open").closest(".card");
            const el = parentCard[0];
            const id = $(el).data("id-karyawan");
            const mejaSaatIni = $(el).data("meja-saat-ini");
            const inisialBaru = $(el).data("inisiallevel");
            const targetContainer = $(`.kanban[data-group="${groupTujuan}"][data-meja="${mejaTujuan}"]`);
            const countInTarget = targetContainer.find(".card").length;
            const jumlahGT = targetContainer.find('.card[data-inisiallevel="GT"]').length;

            if ((inisialBaru === 'GT' || inisialBaru === 'GL') && countInTarget >= 4) {
                Swal.fire({
                    icon: "warning",
                    title: "Tidak boleh lebih dari 4 karyawan!",
                    text: "Karyawan dengan level GT atau GL hanya boleh maksimal 4 per meja.",
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (inisialBaru === "GT" && jumlahGT >= 1) {
                Swal.fire({
                    icon: "warning",
                    title: "Duplikasi GT-(Gunting) tidak diperbolehkan!",
                    text: "Setiap meja hanya boleh memiliki 1 karyawan dengan inisial GT.",
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }

            if (targetContainer.length) {
                $(el).fadeOut(150, function() {
                    $(this).appendTo(targetContainer).fadeIn(150);
                });
            }

            const idx = PERPINDAHAN_MEJA_KARYAWAN.findIndex(x => x.id == id);
            if (idx >= 0) PERPINDAHAN_MEJA_KARYAWAN.splice(idx, 1);
            PERPINDAHAN_MEJA_KARYAWAN.push({
                id,
                meja_saat_ini: mejaSaatIni,
                meja_tujuan: mejaTujuanLabel
            });

            $(el).attr("data-meja-saat-ini", mejaTujuanLabel);
            $(".kanban-dropdown").removeClass("open");
            kirimKeServer(id, mejaSaatIni, mejaTujuanLabel);
        });

        // --- Tutup dropdown kalau klik di luar ---
        $(document).on("click", () => $(".kanban-dropdown").removeClass("open"));

        // --- Simpan semua perubahan ---
        $(document).on("click", "#SimpanTempatKerja", function(e) {
            e.preventDefault();

            let dataKirim = [];
            $(".kanban .card").each(function() {
                const el = $(this);
                dataKirim.push({
                    id: el.data("id-karyawan"),
                    meja_saat_ini: el.data("meja-saat-ini"),
                    meja_tujuan: el.data("meja-tujuan")
                });
            });

            if (Array.isArray(PERPINDAHAN_MEJA_KARYAWAN) && PERPINDAHAN_MEJA_KARYAWAN.length > 0) {
                PERPINDAHAN_MEJA_KARYAWAN.forEach(ubah => {
                    const index = dataKirim.findIndex(d => d.id === ubah.id);
                    if (index !== -1) dataKirim[index].meja_tujuan = ubah.meja_tujuan;
                });
            }

            const formData = new FormData();
            formData.append("_method", "PUT");
            formData.append("_token", $('meta[name="csrf-token"]').attr("content"));
            formData.append("perpindahan_data", JSON.stringify(dataKirim));

            $.ajax({
                url: "/inspeksi/tempat-kerja/update-banyak",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: res => {
                    Swal.fire({
                        icon: "success",
                        title: res.msg,
                        confirmButtonText: "Kembali ke Dashboard"
                    }).then(r => {
                        if (r.isConfirmed) window.location.href = "/";
                    });
                },
                error: err => {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal menyimpan data",
                        text: err.responseJSON?.msg || "",
                        confirmButtonText: "Tutup"
                    });
                }
            });
        });

        function kirimKeServer(id, mejaSaatIni, mejaTujuanLabel) {
            $.ajax({
                url: "/inspeksi/tempat-kerja/" + id,
                method: "PUT",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    id_karyawan: id,
                    meja_saat_ini: mejaSaatIni,
                    meja_tujuan: mejaTujuanLabel
                }
            });
        }
    </script>
@endsection
