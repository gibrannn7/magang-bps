<div class="row">
    <div class="col-md-5">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Tambah Data</h3></div>
            <form action="<?= base_url('admin/master_kampus_add') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="card-body">
                    <div class="form-group">
                        <label>Nama Instansi / Sekolah</label>
                        <input type="text" name="nama_institusi" class="form-control" required placeholder="Contoh: Univ. Banten">
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori" class="form-control">
                            <option value="universitas">Universitas</option>
                            <option value="sekolah">Sekolah (SMK/SMA)</option>
                        </select>
                    </div>
                </div>
                <div class="card-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered datatable-init">
                    <thead><tr><th>No</th><th>Nama</th><th>Kategori</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php $no=1; foreach($list as $l): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $l->nama_institusi ?></td>
                            <td><span class="badge badge-info"><?= $l->kategori ?></span></td>
                            <td>
                                <a href="<?= base_url('admin/master_kampus_delete/'.$l->id) ?>" onclick="return confirm('Hapus?')" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
