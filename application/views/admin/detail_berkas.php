<div class="row">

    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">

                <h3 class="profile-username text-center"><?= $pendaftar->nama ?></h3>
                <p class="text-muted text-center"><?= $pendaftar->jenis_peserta ?></p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>No WA</b> <span class="float-right"><?= $pendaftar->no_hp ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Instansi</b> <span class="float-right"><?= $pendaftar->institusi ?></span>
                    </li>
                    <li class="list-group-item">
                        <b>Jurusan</b> <span class="float-right"><?= $pendaftar->jurusan ?></span>
                    </li>
                </ul>

                <?php if(!empty($akun)): ?>
                <div class="alert alert-info">
                    <strong>Info Akun Login</strong><br>
                    Username: <?= $akun->username ?><br>
                    Password Default: 123456
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dokumen Upload</h3>
            </div>

            <div class="card-body p-0">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="20%">Jenis</th>
                            <th>Preview</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php foreach($dokumen as $d): 
                            $folder = ($d->jenis_dokumen == 'foto') ? 'foto' : (($d->jenis_dokumen == 'cv') ? 'cv' : 'surat');
                            $filepath = base_url('assets/uploads/'.$folder.'/'.$d->file_path);
                            $ext = strtolower(pathinfo($d->file_path, PATHINFO_EXTENSION));
                        ?>
                        <tr>
                            <td><?= strtoupper($d->jenis_dokumen) ?></td>
                            <td>
                                <?php if(in_array($ext, ['jpg','jpeg','png'])): ?>
                                    <img src="<?= $filepath ?>" class="img-fluid" style="max-height:120px;border:1px solid #ddd;padding:2px">
                                <?php elseif($ext == 'pdf'): ?>
                                    <a href="#" onclick="window.open('<?= $filepath ?>','_blank','width=900,height=600');return false;">
                                        <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                        <span class="ml-2">Preview PDF</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">
                                        <i class="fas fa-file-word fa-2x"></i>
                                        <span class="ml-2">DOCX (Download)</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= $filepath ?>" target="_blank" download class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>

                        <?php if(empty($dokumen)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted">Belum ada dokumen</td>
                        </tr>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
