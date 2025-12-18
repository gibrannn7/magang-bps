<?php
$status_magang = $this->db
    ->get_where('pendaftar', ['user_id' => $this->session->userdata('user_id')])
    ->row()
    ->status;
?>

<div class="row">

    <div class="col-md-12">
        <h3 class="mb-3">
            Halo, <?= $this->session->userdata('nama_lengkap') ?>
        </h3>

        <?php if($status_magang == 'selesai'): ?>
        <div class="alert alert-success alert-dismissible">
            <h5><i class="icon fas fa-check"></i> Selamat!</h5>
            Magang Anda telah selesai. Sertifikat sudah terbit.
            <a href="<?= base_url('peserta/download_sertifikat') ?>"
               class="btn btn-light btn-sm ml-2 text-success font-weight-bold">
               Download Sertifikat
            </a>
        </div>
        <?php endif; ?>
    </div>
	<?php 
// Ambil data pendaftar terbaru untuk user ini
$pendaftar = $this->db->get_where('pendaftar', ['user_id' => $this->session->userdata('user_id')])->row();

if($pendaftar && !empty($pendaftar->file_surat_balasan)): 
?>
    <div class="col-md-12 mb-3">
        <div class="alert alert-info">
            <h5><i class="icon fas fa-envelope-open-text"></i> Surat Balasan Magang</h5>
            Surat balasan resmi dari BPS Provinsi Banten telah terbit. Silakan unduh sebagai bukti penerimaan.
            <br>
            <a href="<?= base_url('assets/uploads/surat_balasan/' . $pendaftar->file_surat_balasan) ?>" 
               target="_blank" 
               class="btn btn-light text-info font-weight-bold mt-2">
               <i class="fas fa-download"></i> Download Surat Balasan
            </a>
        </div>
    </div>
<?php endif; ?>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title">Kehadiran Hari Ini (<?= date('d M Y') ?>)</h3>
            </div>
            <div class="card-body text-center">
                <div class="row">

                    <div class="col-md-6 border-right">
                        <h5>ABSEN DATANG</h5>
                        <h1 class="font-weight-bold text-primary">
                            <?= isset($absensi->jam_datang) ? date('H:i', strtotime($absensi->jam_datang)) : '--:--' ?>
                        </h1>

                        <?php if(!isset($absensi->jam_datang)): ?>
                            <a href="<?= base_url('peserta/absen_area') ?>"
                               class="btn btn-primary btn-block mt-3">
                                Absen Sekarang
                            </a>
                        <?php else: ?>
                            <span class="badge badge-success mt-2">
                                <?= strtoupper($absensi->status) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-6">
                        <h5>ABSEN PULANG</h5>
                        <h1 class="font-weight-bold text-danger">
                            <?= isset($absensi->jam_pulang) ? date('H:i', strtotime($absensi->jam_pulang)) : '--:--' ?>
                        </h1>

                        <?php if(isset($absensi->jam_datang) && !isset($absensi->jam_pulang)): ?>
                            <a href="<?= base_url('peserta/absen_area') ?>"
                               class="btn btn-danger btn-block mt-3">
                                Absen Pulang
                            </a>
                        <?php elseif(!isset($absensi->jam_datang)): ?>
                            <span class="text-muted">Absen datang terlebih dahulu</span>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">5 Riwayat Absensi Terakhir</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Datang</th>
                            <th>Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($riwayat)): foreach($riwayat as $r): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($r->tanggal)) ?></td>
                            <td><?= $r->jam_datang ?></td>
                            <td><?= $r->jam_pulang ?? '-' ?></td>
                            <td>
                                <span class="badge badge-success">
                                    <?= strtoupper($r->status) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Belum ada riwayat absensi
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
