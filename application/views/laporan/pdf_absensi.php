<!DOCTYPE html>
<html>
<head>
    <title>Rekap Absensi</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12px; }
        .kop-table { width: 100%; border-bottom: 3px double #000; margin-bottom: 20px; }
        .kop-table td { border: none; padding: 5px; }
        .logo-bps { width: 90px; }
        .kop-text { text-align: left; }
        .kop-title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .kop-sub { font-size: 14px; font-weight: bold; text-transform: uppercase; }
        .kop-address { font-size: 10px; font-style: italic; }
        
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #000; padding: 5px; }
        table.data th { background-color: #eee; text-align: center; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td width="100" align="center">
                <?php
$logoPath = FCPATH . 'assets/img/logo.png';
$logoBase64 = base64_encode(file_get_contents($logoPath));
?>

<img src="data:image/png;base64,<?= $logoBase64 ?>" class="logo-bps">
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

    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="text-decoration: underline; margin: 0;">REKAPITULASI KEHADIRAN</h3>
        <p style="margin: 5px;">Periode: <?= $detail->tgl_mulai ?> s/d <?= $detail->tgl_selesai ?></p>
    </div>

    <table>
        <tr><td width="100">Nama</td><td>: <b><?= $peserta->nama_lengkap ?></b></td></tr>
        <tr><td>Instansi</td><td>: <?= $detail->institusi ?></td></tr>
        <tr><td>Unit Kerja</td><td>: BPS Provinsi Banten</td></tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Datang</th>
                <th>Pulang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach($absensi as $row): ?>
            <tr>
                <td align="center"><?= $no++ ?></td>
                <td align="center"><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                <td align="center"><?= $row->jam_datang ?></td>
                <td align="center"><?= $row->jam_pulang ?></td>
                <td align="center"><?= strtoupper($row->status) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="float: right; width: 200px; margin-top: 40px; text-align: center;">
        Serang, <?= date('d F Y') ?><br>
        Kepala Bagian Umum,<br><br><br><br>
        <b>Ridwan Hidayat</b><br>
        NIP. 1980xxxx
    </div>
</body>
</html>
