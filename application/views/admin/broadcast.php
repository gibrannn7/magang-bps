<div class="row justify-content-center">
    <div class="col-md-8">

        <!-- ===============================
             BROADCAST MANUAL
        ================================ -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fab fa-whatsapp"></i> Kirim Pesan Manual
                </h3>
            </div>

            <form action="<?= base_url('admin/send_broadcast') ?>" method="POST">
                <input type="hidden"
                       name="<?= $this->security->get_csrf_token_name(); ?>"
                       value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="card-body">
                    <div class="form-group">
                        <label>Nomor Tujuan</label>
                        <input type="text"
                               name="no_tujuan"
                               class="form-control"
                               placeholder="Contoh: 081234567890"
                               required>
                        <small class="text-muted">
                            Gunakan awalan <strong>08</strong> atau <strong>62</strong>
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Isi Pesan</label>
                        <textarea name="pesan"
                                  class="form-control"
                                  rows="5"
                                  placeholder="Tulis pesan Anda di sini..."
                                  required></textarea>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right">
                        <i class="fas fa-paper-plane"></i> Kirim Sekarang
                    </button>
                </div>
            </form>
        </div>

        <!-- ===============================
             BROADCAST VIA EXCEL / CSV
        ================================ -->
        <div class="card card-warning mt-4">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-excel"></i> Broadcast via CSV / Excel
                </h3>
            </div>

            <form action="<?= base_url('admin/broadcast_excel') ?>"
                  method="POST"
                  enctype="multipart/form-data">

                <input type="hidden"
                       name="<?= $this->security->get_csrf_token_name(); ?>"
                       value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="card-body">
                    <div class="form-group">
                        <label>Upload File (Format: CSV)</label>
                        <input type="file"
                               name="file_excel"
                               class="form-control p-1"
                               required
                               accept=".csv">

                        <small class="text-muted">
                            Format Kolom:
                            <br>1. No HP
                            <br>2. Nama
                            <br>3. Pesan Khusus (Opsional)
                            <br>
                            <a href="<?= base_url('assets/templates/template_broadcast.csv') ?>"
                               class="text-primary">
                                Download Template CSV
                            </a>
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Pesan Default</label>
                        <textarea name="pesan_default"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Pesan ini akan dipakai jika kolom pesan di CSV kosong"></textarea>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-upload"></i> Upload & Kirim
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
