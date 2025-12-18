<div class="card card-outline card-info">
    <div class="card-header">
        <h3 class="card-title">Log Kehadiran Selama Magang</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped datatable-init">
            <thead>
                <tr>
                    <th width="10">No</th>
                    <th>Tanggal</th>
                    <th>Jam Datang</th>
                    <th>Jam Pulang</th>
                    <th>Status</th>
                    <th>Keterangan/Lokasi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach($absensi as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d F Y', strtotime($row->tanggal)) ?></td>
                    <td class="text-center"><?= $row->jam_datang ?></td>
                    <td class="text-center"><?= $row->jam_pulang ?></td>
                    <td class="text-center">
                        <span class="badge badge-<?= ($row->status=='hadir')?'success':(($row->status=='telat')?'warning':'danger') ?>">
                            <?= strtoupper($row->status) ?>
                        </span>
                    </td>
                    <td>
						<?php if($row->status == 'izin'): ?>
							<div class="mb-1"><strong><?= strtoupper($row->jenis_izin ?? 'IZIN') ?>:</strong> <?= $row->keterangan ?></div>
							<?php if(!empty($row->bukti_izin)): ?>
								<a href="<?= base_url('assets/uploads/absensi/'.$row->bukti_izin) ?>" target="_blank" class="btn btn-xs btn-outline-primary">
									<i class="fas fa-image"></i> Lihat Bukti
								</a>
							<?php endif; ?>
						<?php else: ?>
							<small class="text-muted"><i class="fas fa-map-marker-alt"></i> <?= $row->lat_datang ?>, <?= $row->long_datang ?></small>
						<?php endif; ?>
					</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
