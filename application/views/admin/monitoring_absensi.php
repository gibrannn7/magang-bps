<div class="card card-outline card-primary">
    <div class="card-body">
        <form method="GET" action="<?= base_url('admin/monitoring_absensi') ?>" class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Pilih Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $tanggal ?>">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="masuk" <?= $filter_status == 'masuk' ? 'selected' : '' ?>>Masuk</option>
                        <option value="izin" <?= $filter_status == 'izin' ? 'selected' : '' ?>>Izin/Sakit</option>
                        <option value="belum" <?= $filter_status == 'belum' ? 'selected' : '' ?>>Belum Absen</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header bg-navy">
        <h3 class="card-title">Daftar Absensi Peserta (<?= date('d M Y', strtotime($tanggal)) ?>)</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Peserta</th>
                        <th>Instansi</th>
                        <th>Status</th>
                        <th>Jam Datang</th>
                        <th>Jam Pulang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($absensi)): ?>
                        <tr><td colspan="7" class="text-center py-4">Tidak ada data peserta aktif.</td></tr>
                    <?php endif; ?>
                    <?php $no=1; foreach($absensi as $row): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $row->nama ?></strong></td>
                        <td><?= $row->institusi ?></td>
                        <td><span class="badge <?= $row->label_class ?> p-2"><?= $row->display_status ?></span></td>
                        <td><?= ($row->jam_datang && $row->jam_datang != '00:00:00') ? $row->jam_datang : '-' ?></td>
                        <td><?= ($row->jam_pulang && $row->jam_pulang != '00:00:00') ? $row->jam_pulang : '-' ?></td>
                        <td>
                            <?php if($row->absensi_status == 'izin'): ?>
                                <button type="button" class="btn btn-xs btn-info" 
                                        onclick="showIzinDetail('<?= $row->nama ?>', '<?= strtoupper($row->jenis_izin) ?>', '<?= $row->keterangan ?>', '<?= base_url('assets/uploads/absensi/'.$row->bukti_izin) ?>')">
                                    <i class="fas fa-eye"></i> Lihat Bukti
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalIzin" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title font-weight-bold">Detail Izin Peserta</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body text-center">
                <p id="izin_info" class="text-left mb-3"></p>
                <img id="izin_img" src="" class="img-fluid border shadow-sm" style="max-height: 500px;" alt="Bukti Izin">
            </div>
        </div>
    </div>
</div>

<script>
function showIzinDetail(nama, jenis, ket, imgPath) {
    $('#izin_info').html(`<strong>Peserta:</strong> ${nama}<br><strong>Jenis:</strong> ${jenis}<br><strong>Alasan:</strong> ${ket}`);
    $('#izin_img').attr('src', imgPath);
    $('#modalIzin').modal('show');
}
</script>
