<!-- </main>

<footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h3 class="text-lg font-bold">Badan Pusat Statistik Provinsi Banten</h3>
                <p class="text-gray-400 text-sm mt-1">Jl. Syeh Nawawi Al Bantani, Serang, Banten.</p>
            </div>
            <div class="text-sm text-gray-500">
                &copy; <?= date('Y') ?> BPS Banten. All rights reserved.
            </div>
        </div>
    </div>
</footer>

<script>
    // Global Helper: Show Loading
    function showLoading() {
        document.getElementById('loading-screen').classList.remove('hidden');
    }
    
    function hideLoading() {
        document.getElementById('loading-screen').classList.add('hidden');
    }

    // Flashdata SweetAlert Logic (Akan dipanggil jika ada flashdata dari controller)
    <?php if ($this->session->flashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= $this->session->flashdata('success') ?>',
            confirmButtonColor: '#2563EB'
        });
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= $this->session->flashdata('error') ?>',
            confirmButtonColor: '#DC2626'
        });
    <?php endif; ?>
</script>

</body>
</html> -->
