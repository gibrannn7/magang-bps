<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Formulir Pendaftaran
        </h2>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <form action="<?= base_url('daftar/submit') ?>" method="POST" enctype="multipart/form-data" class="space-y-6" onsubmit="showLoading()">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                <div class="bg-blue-50 p-4 rounded-md mb-4">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">Data Diri</h3>
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Peserta</label>
                            <select name="jenis_peserta" id="jenis_peserta" onchange="toggleForm()" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="mahasiswa">Mahasiswa</option>
                                <option value="siswa">Siswa PKL</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700" id="label_id">NIM</label>
                            <input type="number" name="nim_nis" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700" id="label_inst">Nama Universitas</label>
                            <input type="text" name="institusi" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jurusan</label>
                            <input type="text" name="jurusan" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>

                         <div>
                            <label class="block text-sm font-medium text-gray-700">Nomor WhatsApp (Aktif)</label>
                            <input type="number" name="no_hp" placeholder="08..." required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 p-4 rounded-md mb-4">
                    <h3 class="text-lg font-medium text-green-900 mb-4">Rencana Magang</h3>
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Magang</label>
                            <select name="jenis_magang" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="wajib">Magang Wajib / KKP</option>
                                <option value="mandiri">Magang Mandiri</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2 flex gap-4">
                            <div class="w-1/2">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                <input type="date" name="tgl_mulai" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                            </div>
                            <div class="w-1/2">
                                <label class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-100 p-4 rounded-md mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Berkas</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">CV (PDF/DOCX) - Max 5MB</label>
                            <input type="file" name="file_cv" accept=".pdf,.doc,.docx" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pas Foto (JPG/PNG) - Formal</label>
                            <input type="file" name="file_foto" accept=".jpg,.jpeg,.png" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Surat Pengantar (PDF/JPG)</label>
                            <input type="file" name="file_surat" accept=".pdf,.jpg,.jpeg,.png" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Kirim Pendaftaran
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleForm() {
        const jenis = document.getElementById('jenis_peserta').value;
        const labelId = document.getElementById('label_id');
        const labelInst = document.getElementById('label_inst');

        if(jenis === 'siswa') {
            labelId.innerText = 'NIS / NISN';
            labelInst.innerText = 'Nama Sekolah (SMK/SMA)';
        } else {
            labelId.innerText = 'NIM';
            labelInst.innerText = 'Nama Universitas';
        }
    }
</script>
