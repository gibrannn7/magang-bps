<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Dashboard Admin</h1>

    <!-- ===============================
         SUMMARY
    ================================ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm">Total Pendaftar</h3>
            <p class="text-3xl font-bold"><?= $total_daftar ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <h3 class="text-gray-500 text-sm">Diterima</h3>
            <p class="text-3xl font-bold"><?= $total_terima ?></p>
        </div>
    </div>

    <!-- ===============================
         TABLE
    ================================ -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Data Pendaftar Terbaru
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instansi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dokumen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach($pendaftar as $p): ?>
                    <tr>
                        <!-- NAMA -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= $p->nama ?></div>
                            <div class="text-sm text-gray-500"><?= $p->jenis_peserta ?></div>
                        </td>

                        <!-- INSTANSI -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= $p->institusi ?></div>
                            <div class="text-sm text-gray-500"><?= $p->jurusan ?></div>
                        </td>

                        <!-- DOKUMEN -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                            <a href="<?= base_url('admin/berkas/'.$p->id) ?>" class="hover:underline">
                                Lihat Berkas
                            </a>
                        </td>

                        <!-- STATUS -->
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php 
                                $bg = 'bg-gray-100 text-gray-800';
                                if($p->status == 'diterima') $bg = 'bg-green-100 text-green-800';
                                if($p->status == 'ditolak')  $bg = 'bg-red-100 text-red-800';
                                if($p->status == 'selesai')  $bg = 'bg-purple-100 text-purple-800';
                            ?>
                            <span class="px-2 inline-flex text-xs font-semibold rounded-full <?= $bg ?>">
                                <?= ucfirst($p->status) ?>
                            </span>
                        </td>

                        <!-- AKSI -->
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">

                            <?php if($p->status == 'pending'): ?>

                                <a href="javascript:void(0)"
                                   onclick="konfirmasiVerifikasi(
                                       '<?= base_url('admin/verifikasi/'.$p->id.'/diterima') ?>',
                                       'terima'
                                   )"
                                   class="text-green-600 hover:text-green-900 mr-3 font-semibold">
                                    <i class="fas fa-check"></i> Terima
                                </a>

                                <a href="javascript:void(0)"
                                   onclick="konfirmasiVerifikasi(
                                       '<?= base_url('admin/verifikasi/'.$p->id.'/ditolak') ?>',
                                       'tolak'
                                   )"
                                   class="text-red-600 hover:text-red-900 font-semibold">
                                    <i class="fas fa-times"></i> Tolak
                                </a>

                            <?php elseif($p->status == 'diterima'): ?>

                                <a href="<?= base_url('admin/rekap_absensi/'.$p->user_id) ?>"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    Rekap Absen
                                </a>

                                <a href="<?= base_url('admin/set_selesai/'.$p->id) ?>"
                                   onclick="return confirm('Yakin set status SELESAI? Sertifikat akan terbuka untuk peserta.')"
                                   class="text-purple-600 hover:text-purple-900 font-bold">
                                    Tamatkan Magang
                                </a>

                            <?php elseif($p->status == 'selesai'): ?>

                                <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Selesai
                                </span>

                                <a href="<?= base_url('admin/rekap_absensi/'.$p->user_id) ?>"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-900 ml-2">
                                    Rekap Absen
                                </a>

                            <?php endif; ?>

                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ===============================
     SWEETALERT VERIFIKASI
=============================== -->
<script>
function konfirmasiVerifikasi(url, aksi) {
    let title = aksi === 'terima' ? 'Terima Peserta?' : 'Tolak Peserta?';
    let text  = aksi === 'terima'
        ? 'Akun login akan dibuat & notifikasi WhatsApp akan dikirim.'
        : 'Peserta akan menerima notifikasi penolakan via WhatsApp.';
    let color = aksi === 'terima' ? '#16a34a' : '#dc2626';

    Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: color,
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}
</script>
