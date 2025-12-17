<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner"><h3><?= $total_daftar ?></h3><p>Total Pendaftar</p></div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner"><h3><?= $pending ?></h3><p>Perlu Verifikasi</p></div>
            <div class="icon"><i class="fas fa-clock"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner"><h3><?= $aktif ?></h3><p>Sedang Magang</p></div>
            <div class="icon"><i class="fas fa-check"></i></div>
        </div>
    </div>
</div>

<div class="card card-primary card-outline card-outline-tabs">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-toggle="pill" href="#tab-pending">Verifikasi (<?= $pending ?>)</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-aktif">Aktif Magang (<?= $aktif ?>)</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="pill" href="#tab-selesai">Arsip/Selesai</a></li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content">
            
            <div class="tab-pane fade show active" id="tab-pending">
                <table class="table table-bordered table-striped datatable-init">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendaftar as $p): if($p->status != 'pending') continue; ?>
                        <tr>
                            <td>
                                <?= $p->nama ?><br>
                                <small class="text-muted"><?= $p->jurusan ?></small>
                            </td>
                            <td><?= $p->institusi ?></td>
                            <td>
                                <a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="btn btn-xs btn-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                                <button type="button"
                                        onclick="confirmAction('<?= base_url('admin/verifikasi/'.$p->id.'/diterima') ?>', 'terima')"
                                        class="btn btn-xs btn-success"
                                        title="Terima">
                                    <i class="fas fa-check"></i> Terima
                                </button>
                                <button type="button"
                                        onclick="confirmAction('<?= base_url('admin/verifikasi/'.$p->id.'/ditolak') ?>', 'tolak')"
                                        class="btn btn-xs btn-danger"
                                        title="Tolak">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="tab-aktif">
                <table class="table table-bordered table-striped datatable-init">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Periode</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendaftar as $p): if($p->status != 'diterima') continue; ?>
                        <tr>
                            <td>
                                <strong><?= $p->nama ?></strong>
                                <?php if($p->akun_user): ?>
                                    <br><small class="text-success"><i class="fas fa-user-check"></i> Akun Aktif</small>
                                <?php else: ?>
                                    <br><small class="text-danger"><i class="fas fa-exclamation-circle"></i> Error Akun</small>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/y', strtotime($p->tgl_mulai)) ?> - <?= date('d/m/y', strtotime($p->tgl_selesai)) ?></td>
                            <td>
                                <a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="btn btn-xs btn-info" title="Lihat Detail & Akun"><i class="fas fa-eye"></i> Detail</a>

                                <a href="<?= base_url('admin/rekap_absensi/'.$p->user_id) ?>" target="_blank" class="btn btn-xs btn-default border" title="Rekap Absensi">
                                    <i class="fas fa-file-pdf text-danger"></i> Rekap
                                </a>

                                <?php if($p->user_id): ?>
                                <button 
                                    type="button"
                                    class="btn btn-xs btn-warning btn-reset-pass"
                                    data-url="<?= base_url('admin/reset_password/'.$p->user_id) ?>"
                                    data-nama="<?= $p->nama ?>"
                                    title="Reset Password ke 123456">
                                    <i class="fas fa-key"></i> Reset
                                </button>
                                <?php endif; ?>
                                <button type="button"
                                    onclick="confirmTamatkan('<?= base_url('admin/set_selesai/'.$p->id) ?>')"
                                    class="btn btn-xs btn-primary font-weight-bold"
                                    title="Tamatkan">
                                    <i class="fas fa-flag-checkered"></i> Tamat
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="tab-selesai">
                 <table class="table table-bordered datatable-init">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Instansi</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendaftar as $p): if($p->status != 'selesai' && $p->status != 'ditolak') continue; ?>
                        <tr>
                            <td><?= $p->nama ?></td>
                            <td><?= $p->institusi ?></td>
                            <td>
                                <span class="badge badge-<?= $p->status == 'selesai' ? 'success' : 'danger' ?>"><?= strtoupper($p->status) ?></span>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="btn btn-xs btn-info" title="Lihat Detail"><i class="fas fa-eye"></i> Detail</a>

                                <?php if($p->status == 'selesai'): ?>
                                    <a href="<?= base_url('admin/rekap_absensi/'.$p->user_id) ?>" target="_blank" class="btn btn-xs btn-default border" title="Rekap Akhir">
                                        <i class="fas fa-file-pdf text-danger"></i> Rekap
                                    </a>
                                    
                                    <?php if($p->user_id): ?>
                                    <button 
                                        type="button"
                                        class="btn btn-xs btn-warning btn-reset-pass"
                                        data-url="<?= base_url('admin/reset_password/'.$p->user_id) ?>"
                                        data-nama="<?= $p->nama ?>"
                                        title="Reset Password">
                                        <i class="fas fa-key"></i> Reset
                                    </button>
                                    <?php endif; ?>
                                    <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Logic Konfirmasi Penerimaan/Penolakan
function confirmAction(baseUrl, action) {
    let title = action === 'terima' ? 'Terima Peserta Ini?' : 'Tolak Peserta Ini?';
    let text  = action === 'terima'
        ? 'Akun login akan dibuat otomatis.'
        : 'Status peserta akan diubah menjadi Ditolak.';
    let confirmBtnText = action === 'terima' ? 'Ya, Terima & Kirim WA' : 'Ya, Tolak & Kirim WA';
    let denyBtnText    = action === 'terima' ? 'Terima Tanpa WA' : 'Tolak Tanpa WA';
    let iconColor      = action === 'terima' ? '#28a745' : '#dc3545';

    Swal.fire({
        title: title,
        text: text + ' Apakah ingin mengirim notifikasi WhatsApp?',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true, // Tombol Tambahan
        confirmButtonColor: iconColor,
        denyButtonColor: '#6c757d',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fab fa-whatsapp"></i> ' + confirmBtnText,
        denyButtonText: '<i class="fas fa-eye-slash"></i> ' + denyBtnText,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Pilihan YA (Kirim WA) -> Param ke-3 = 1
            window.location.href = baseUrl + '/1'; 
        } else if (result.isDenied) {
            // Pilihan DENY (Tanpa WA) -> Param ke-3 = 0
            window.location.href = baseUrl + '/0';
        }
    });
}

// Logic Tamatkan Magang
function confirmTamatkan(url) {
    Swal.fire({
        title: 'Tamatkan Magang?',
        text: 'Status peserta akan dipindahkan ke arsip / selesai.',
        icon: 'warning',
		iconColor: '#1261f3',
        showCancelButton: true,
        confirmButtonText: 'Ya, Tamatkan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#1261f3ff'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

// UPDATE: LOGIC RESET PASSWORD (Sama persis dengan data_peserta.php)
document.addEventListener('click', function(e) {
    // Menggunakan event delegation agar bekerja pada elemen dinamis/tab
    if (e.target.closest('.btn-reset-pass')) {
        const btn = e.target.closest('.btn-reset-pass');
        const url = btn.getAttribute('data-url');
        const nama = btn.getAttribute('data-nama');

        Swal.fire({
            title: 'Reset Password?',
            html: `Password user <b>${nama}</b> akan direset menjadi:<br><b style="font-size:1.2em">123456</b>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
});
</script>
