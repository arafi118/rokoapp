@php
    use App\Utils\Tanggal;
    use App\Utils\Keuangan;

    $keuangan = new Keuangan();
    $logoBase64 = base64_encode(file_get_contents(public_path('assets/logo.jpg')));
@endphp

<style>
    * {
        font-family: 'Times New Roman', Times, serif;
    }

    html {
        margin: 56px;
        margin-left: 80px;
    }

    .logo {
        position: absolute;
        top: -56px;
        left: -40px;
    }

    ul,
    ol {
        margin-left: -10px;
        page-break-inside: auto !important;
    }

    header {
        position: fixed;
        top: -10px;
        left: 0px;
        right: 0px;
    }

    table tr th,
    table tr td,
    table tr td table.p tr td {
        vertical-align: top !important;
        padding: 2px 4px !important;
    }

    table tr td table tr td {
        padding: 0 !important;
    }

    table.p0 tr th,
    table.p0 tr td {
        padding: 0px !important;
    }

    .break {
        page-break-after: always;
    }

    li {
        text-align: justify;
    }

    .l {
        border-left: 1px solid #000;
    }

    .t {
        border-top: 1px solid #000;
    }

    .r {
        border-right: 1px solid #000;
    }

    .b {
        border-bottom: 1px solid #000;
    }
</style>

<title>Surat Perjanjian Pemagang</title>
@foreach ($karyawan as $k)
    @php
        $kode_karyawan = $k->kode_karyawan;
        $tahun = date('Y', strtotime($k->tanggal_masuk));
        $bulan = date('m', strtotime($k->tanggal_masuk));
        $urutan = substr($k->kode_karyawan, -4);
    @endphp

    @if (!$loop->first)
        <div class="break"></div>
    @endif

    <div style="font-size: 14px;">
        <div>
            <img src="data:image/jpeg;base64,{{ $logoBase64 }}" alt="Logo" width="140" class="logo">
        </div>
        <div>
            <div style="text-align: center; font-size: 20px">
                <div> <b>PERJANJIAN PEMAGANGAN</b> </div>
                <div> <b>ANTARA</b> </div>
                <div> <b>PT. SATU DESA MANDIRI DENGAN PESERTA MAGANG</b> </div>
            </div>
            <div style="text-align: center; font-size:16px;">
                Nomor : P {{ $urutan }}/SDM-MGL/PMG/{{ $keuangan->romawi($bulan) }}/{{ $tahun }}
            </div>
        </div>

        <p style="text-align: justify;">
            Pada hari ini {{ Tanggal::namaHari($k->tanggal_masuk) }} tanggal
            {{ $keuangan->terbilang(Tanggal::hari($k->tanggal_masuk)) }} bulan
            {{ Tanggal::namaBulan($k->tanggal_masuk) }}
            tahun {{ $keuangan->terbilang($tahun) }} ({{ Tanggal::tglIndo($k->tanggal_masuk) }}) yang bertanda
            tangan di bawah ini :
        </p>

        <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td width="28%">Nama</td>
                <td width="2%">:</td>
                <td>Agus Sudrikamto, SH</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>Malang, 8 Agustus 1965</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>RT. 05/01 Dusun Kebonagung Desa Tamanharjo Kec. Singosari Kab. Malang</td>
            </tr>

            <tr>
                <td colspan="3" style="text-align: justify;">
                    Selanjutnya disebut sebagai PIHAK KESATU
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
                <td>Nama</td>
                <td>:</td>
                <td>{{ $k->getanggota->nama }}</td>
            </tr>
            <tr>
                <td>NIP</td>
                <td>:</td>
                <td>{{ $k->kode_karyawan }}</td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $k->getanggota->tempat_lahir }}, {{ Tanggal::tglLatin($k->getanggota->tanggal_lahir) }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $k->getanggota->alamat }}</td>
            </tr>
            <tr>
                <td>NIK</td>
                <td>:</td>
                <td>{{ $k->getanggota->nik }}</td>
            </tr>
            <tr>
                <td colspan="3" style="text-align: justify;">
                    Selanjutnya disebut sebagai PIHAK KEDUA
                </td>
            </tr>
        </table>
        <p style="text-align: justify;">
            PIHAK KESATU dan PIHAK KEDUA yang selanjutnya secara bersama-sama disebut PARA PIHAK sepakat untuk
            mengikatkan diri dalam suatu Perjanjian Pemagangan dengan ketentuan sebagai berikut:
        </p>

        <div style="text-align: center;">
            <div><b>Pasal 1</b></div>
            <div><b>KESEPAKATAN</b></div>
            <p style="text-align: justify;">
                PIHAK KESATU bersedia menerima PIHAK KEDUA sebagai peserta Program Pemagangan, dan PIHAK KEDUA
                menyatakan kesediaannya untuk mengikuti program Pemagangan yang dilaksanakan oleh PIHAK KESATU di
                Perusahaan PT. Satu Desa Mandiri yang berlokasi di Jl. Raya Magelang Purworejo Km. 11 Dusun Balong Desa
                Tanggulrejo, Kecamatan Tempuran Kabupaten Magelang, Propinsi Jawa Tengah.
            </p>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 2</b></div>
            <div><b>JANGKA WAKTU PEMAGANGAN</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    Jangka waktu pelaksanaan pemagangan adalah selama 10 (sepuluh) bulan terhitung sejak tanggal
                    {{ Tanggal::tglLatin($k->tanggal_masuk) }} sampai dengan
                    {{ Tanggal::tglLatin(date('Y-m-d', strtotime('+10 month', strtotime($k->tanggal_masuk)))) }}
                </li>
                <li>
                    Pemagangan dilaksanakan setiap hari kerja mulai pukul 07.00 sampai dengan pukul 15.00 WIB.
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 3</b></div>
            <div><b>JENIS KEJURUAN DAN PROGRAM PEMAGANGAN</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    Pemagangan yang dilaksanakan oleh PIHAK KESATU adalah Program Pemagangan Ketrampilan Proses Produksi
                    dan Pengemasan Sigaret Kretek Tangan (SKT)
                </li>
                <li>
                    Program Pemagangan untuk mencapai kualifikasi secara bertahap mulai dari pemula, terampil sampai
                    dengan mahir sesuai dengan kurikulum dan silabus yang telah disusun
                </li>
                <li>
                    Program pemagangan sebagaimana dimaksudkan pada ayat (1) dan ayat (2) tercantum dalam Lampiran
                    Perijinan Pemagangan ini.
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 4</b></div>
            <div><b>HAK DAN KEWAJIBAN PIHAK KESATU</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    <div>PIHAK KESATU berhak untuk;</div>
                    <ol style="list-style: lower-alpha; text-align: justify;">
                        <li>
                            Memberhentikan PIHAK KEDUA yang menyimpang dari ketentuan yang telah disepakati dalam
                            Perjanjian Pemagangan tanpa kompensasi dan atau Pesangon;
                        </li>
                        <li>Memanfaatkan hasil kerja peserta pemagangan; dan</li>
                        <li>Memberlakukan tata tertib dan Perjanjian Pemagangan</li>
                    </ol>
                </li>
                <li>
                    <div>Penyimpangan sebagaimana dimaksud pada ayat (1) huruf a, meliputi:</div>
                    <ol style="list-style: lower-alpha; text-align: justify;">
                        <li>
                            Melakukan kelalaian dan tindakan yang tidak bertanggung jawab, walaupun telah mendapat
                            peringatan;
                        </li>
                        <li>
                            Dengan sengaja merusak, merugikan, atau membiarkan dalam keadaan bahaya barang milik PIHAK
                            KESATU;
                        </li>
                        <li>
                            Melakukan tindakan kejahatan diantaranya berkelahi, mencuri, menggelapkan, menipu dan
                            membawa serta memperdagangkan barang-barang terlarang baik di dalam maupun di luar
                            perusahaan;
                        </li>
                        <li>
                            Membolos atau tidak masuk magang tanpa alasan yang sah sesuai dengan peraturan yang
                            berlaku di Perusahaan; dan
                        </li>
                        <li>
                            PIHAK KEDUA melanggar dari ketentuan yang telah disepakati dalam Perjanjian Pemagangan ini.
                        </li>
                    </ol>
                </li>
                <li>
                    <div>PIHAK KESATU berkewajiban untuk:</div>
                    <ol style="list-style: lower-alpha; text-align: justify;">
                        <li>
                            Membimbing peserta pemagangan sesuai dengan program pemagangan;
                        </li>
                        <li>
                            Memenuhi hak peserta pemagangan sesuai dengan Perjanjian Pemagangan;
                        </li>
                        <li>
                            Menyediakan alat pelindung diri sesuai dengan persyaratan keselamatan dan kesehatan kerja;
                        </li>
                        <li>
                            Mengikutsertakan peserta Pemagangan dalam program jaminan;
                        </li>
                        <li>
                            Memberikan uang saku kepada peserta pemagangan;
                        </li>
                        <li>Mengevaluasi peserta pemagangan;</li>
                        <li>Memberikan sertifikat Pemagangan atau Surat Keterangan Telah Mengikuti Pemagangan.</li>
                    </ol>
                </li>
                <li>
                    PIHAK KESATU dapat merekrut dan atau menyalurkan PIHAK KEDUA menjadi karyawan bagi karyawan yang
                    belum bekerja sesuai peraturan yang berlaku di Perusahaan, setelah program pemagangan selesai
                    dilaksanakan
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 5</b></div>
            <div><b>HAK DAN KEWAJIBAN PIHAK KEDUA</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    <div>PIHAK KEDUA berhak untuk</div>
                    <ol style="list-style: lower-alpha; text-align: justify;">
                        <li>
                            Memperoleh bimbingan dari Pembimbing Pemagangan;
                        </li>
                        <li>Memperoleh fasilitas keselamatan dan kesehatan kerja;</li>
                        <li>Memperoleh uang saku;</li>
                        <li>Diikutsertakan dalam program jaminan sosial; dan</li>
                        <li>Memperoleh sertifikat pemagangan atau surat keterangan telah mengikuti Pemagangan.</li>
                    </ol>
                </li>
                <li>
                    <div>PIHAK KEDUA berkewajiban untuk:</div>
                    <ol style="list-style: lower-alpha; text-align: justify;">
                        <li>
                            Mematuhi ketentuan yang telah disepakati dalam Perjanjian Pemagangan;
                        </li>
                        <li>Mengikuti program pemagangan sampai selesai; </li>
                        <li>Mentaati tata tertib yang berlaku di Perusahaan sebagai Penyelenggara Pemagangan;</li>
                        <li>Mentaati segala instruksi dari tenaga pelatih atau pembimbing pemagangan;</li>
                        <li>
                            Tidak menuntut untuk dijadikan karyawan di Perusahaan setelah selesai pemagangan sesuai
                            dengan perjanjian;
                        </li>
                        <li>Menjaga informasi dan kerahasiaan dari PIHAK KESATU dan</li>
                        <li>Menjaga nama baik PIHAK KESATU.</li>
                    </ol>
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 6</b></div>
            <div><b>UANG SAKU</b></div>
            <p style="text-align: justify;">
                PIHAK KEDUA berhak memperoleh uang saku sebesar Rp. {{ number_format(59220, 2) }} (lima puluh sembilan
                ribu dua ratus dua
                puluh rupiah) dari PIHAK KESATU.
            </p>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 7</b></div>
            <div><b>SANKSI</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    Dalam hal PIHAK KEDUA melanggar ketentuan yang sudah disepakati dalam Perjanjian Pemagangan ini dan
                    mengakibatkan kerugian pada Perusahaan, PIHAK KESATU dapat mengeluarkan PIHAK KEDUA dari program
                    pemagangan yang sedang berjalan.
                </li>
                <li>
                    Dalam hal PIHAK KEDUA melanggar ketentuan yang sudah disepakati dalam Perjanjian Pemagangan ini dan
                    mengakibatkan kerugian pada Perusahaan, PIHAK KESATU dapat mengeluarkan PIHAK KEDUA dari program
                    pemagangan yang sedang berjalan.
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 8</b></div>
            <div><b>PERSELISIHAN</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    Jika terjadi perselisihan antara PARA PIHAK maka akan diselesaikan secara musyawarah untuk mencapai
                    mufakat.
                </li>
                <li>
                    Jika musyawarah untuk mencapai mufakat sebagaimana dimaksud pada ayat (1) tidak tercapai, maka PARA
                    PIHAK dapat meminta bantuan fasilitasi dari dinas yang menyelenggarakan urusan pemerintahan daerah
                    provinsi atau kabupaten/ kota di bidang ketenagakerjaan sesuai dengan ketentuan peraturan
                    perundang-undangan.
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 9</b></div>
            <div><b>LAIN-LAIN</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    Jika isi ketentuan dalam perjanjian ini ada yang bertentangan dengan ketentuan peraturan
                    perundang-undangan maka akan dilakukan revisi atau perubahan oleh PARA PIHAK.
                </li>
                <li>
                    Hal lain yang belum diatur dalam perjanjian ini akan diatur sesuai dengan kebutuhan dan kesepakatan
                    para pihak dan tidak bertentangan dengan ketentuan peraturan perundang-undangan.
                </li>
            </ul>
        </div>

        <div style="text-align: center;">
            <div><b>Pasal 10</b></div>
            <div><b>PENUTUP</b></div>
            <ul style="margin-left: -20px; list-style: decimal; text-align: justify">
                <li>
                    Perjanjian Pemagangan ini dibuat dan ditandatangani oleh PARA PIHAK dalam keadaan sadar dan tanpa
                    paksaan dari pihak manapun juga.
                </li>
                <li>
                    Perjanjian Pemagangan ini berlaku sejak tanggal ditandatangani oleh PARA PIHAK.
                </li>
                <li>
                    Perjanjian pemagangan ini berakhir sesuai dengan jangka waktu sebagaimana dimaksud dalam Pasal 2
                    ayat (1).
                </li>
            </ul>
        </div>

        <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
                <td align="center">PIHAK KEDUA</td>
                <td align="center"></td>
                <td align="center">PIHAK KESATU,</td>
            </tr>
            <tr>
                <td align="center">PESERTA PEMAGANGAN</td>
                <td align="center"></td>
                <td align="center">PT. SATU DESA MANDIRI</td>
            </tr>
            <tr>
                <td colspan="3" height="40">&nbsp;</td>
            </tr>
            <tr>
                <td align="center">
                    ({{ strtoupper($k->getanggota->nama) }})
                </td>
                <td align="center"></td>
                <td align="center">(AGUS SUDRIKAMTO, SH)</td>
            </tr>

            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
                <td align="center"></td>
                <td align="center">Mengetahui dan Mengesahkan,</td>
                <td align="center"></td>
            </tr>
            <tr>
                <td align="center"></td>
                <td align="center">Kepala Dinas Perindustrian dan Tenaga Kerja</td>
                <td align="center"></td>
            </tr>
            <tr>
                <td colspan="3" height="40">&nbsp;</td>
            </tr>
            <tr>
                <td align="center"></td>
                <td align="center">Dra. SITI ZUMAROH., M.M.</td>
                <td align="center"></td>
            </tr>
            <tr>
                <td align="center"></td>
                <td align="center">Pembina Utama Muda</td>
                <td align="center"></td>
            </tr>
            <tr>
                <td align="center"></td>
                <td align="center">NIP. 197006161990012002</td>
                <td align="center"></td>
            </tr>
        </table>
    </div>
@endforeach
