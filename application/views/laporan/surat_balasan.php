<!DOCTYPE html>
<html>
<head>
    <title>Surat Balasan Magang</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; }
        .kop-table td { border: none; padding: 5px; }
        .logo-bps { width: 90px; }
        .kop-text { text-align: left; }
        .kop-title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .kop-sub { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .kop-address { font-size: 10px; font-style: italic; }
        
        table.content-table { width: 100%; margin-top: 10px; border-collapse: collapse; }
        table.content-table td { vertical-align: top; padding: 2px; }
        
        .ttd-box { float: right; width: 250px; margin-top: 40px; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td width="100" align="center">
                <?php
                // Convert gambar ke base64 agar tampil di PDF
                $path = FCPATH . 'assets/img/logo.png';
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                ?>
                <img src="<?= $base64 ?>" class="logo-bps">
            </td>
            <td class="kop-text">
                <span class="kop-title">BADAN PUSAT STATISTIK</span><br>
                <span class="kop-sub">PROVINSI BANTEN</span><br>
                <span class="kop-address">
                    Jl. Syeh Nawawi Al Bantani, Kawasan Pusat Pemerintahan Provinsi Banten (KP3B), Kav. H1-2 Serang Banten.<br>
                    Telp: (0254) 267027. Email: bps3600@bps.go.id, Website: http://banten.bps.go.id
                </span>
            </td>
        </tr>
    </table>

    <table class="content-table" style="margin-bottom: 20px;">
        <tr>
            <td width="80">Nomor</td>
            <td width="10">:</td>
            <td>B-<?= rand(1000,9999) ?>/36000/HM.340/<?= date('Y') ?></td>
        </tr>
        <tr>
            <td>Sifat</td>
            <td>:</td>
            <td>Biasa</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>:</td>
            <td>-</td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>:</td>
            <td>Penerimaan KKP Mahasiswa</td>
        </tr>
    </table>

    <p>
        Yth. Dekan/Ketua/Kepala Sekolah <?= $pendaftar->institusi ?><br>
        di<br>
        Tempat
    </p>

    <p align="right">Serang, <?= date('d F Y') ?></p>

    <p style="text-align: justify;">
        Menindaklanjuti surat Nomor: <?= $pendaftar->no_surat ?> tanggal <?= date('d F Y', strtotime($pendaftar->tgl_surat)) ?>
        tentang Permohonan Izin KKP (Kuliah Kerja Praktik) mahasiswa sebagai berikut :
    </p>

    <table class="content-table" style="margin-left: 20px; width: 90%;">
        <tr>
            <td width="150">Nama</td>
            <td width="10">:</td>
            <td><b><?= $pendaftar->nama ?></b></td>
        </tr>
        <tr>
            <td>NIM / NIS</td>
            <td>:</td>
            <td><?= $pendaftar->nim_nis ?></td>
        </tr>
        <tr>
            <td>Jurusan / Prodi</td>
            <td>:</td>
            <td><?= $pendaftar->jurusan ?></td>
        </tr>
        <tr>
            <td>Fakultas</td>
            <td>:</td>
            <td><?= $pendaftar->fakultas ?></td>
        </tr>
    </table>

    <p style="text-align: justify;">
        Bersama ini disampaikan pada prinsipnya kami <b>tidak berkeberatan dan menerima</b>
        mahasiswa tersebut untuk melaksanakan Kuliah Kerja Praktik di Badan Pusat Statistik
        Provinsi Banten mulai tanggal <?= date('d F Y', strtotime($pendaftar->tgl_mulai)) ?> s.d <?= date('d F Y', strtotime($pendaftar->tgl_selesai)) ?>.
    </p>

    <p>
        Demikian disampaikan, atas perhatian dan kerjasamanya diucapkan terimakasih.
    </p>

    <div class="ttd-box">
        Plt. Kepala BPS Provinsi Banten,<br><br><br><br><br>
        <b>Ridwan Hidayat</b><br>
        <small style="font-style: italic; color: gray;">Dokumen ini telah ditandatangani secara elektronik</small>
    </div>

</body>
</html>
