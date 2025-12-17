<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Magang BPS Provinsi Banten</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Warna BPS Custom */
        .text-bps-blue { color: #003366; }
        .bg-bps-blue { background-color: #003366; }
        .hover\:bg-bps-blue-dark:hover { background-color: #002244; }
        .text-bps-orange { color: #FF9900; }
        
        /* Styling tambahan untuk input */
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            border-color: #003366;
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
    </style>
</head>
<body class="h-screen w-full overflow-hidden flex">

    <div class="hidden lg:flex w-1/2 relative bg-gray-900 items-center justify-center overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="<?= base_url('assets/img/background.jpeg') ?>" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-br from-[#003366] to-black opacity-80"></div>
        </div>

        <div class="relative z-10 text-center px-12">
            <div class="mb-12">
				<img src="<?= base_url('assets/img/logo.png') ?>" 
					class="h-44 w-auto mx-auto">
			</div>

            <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">Sistem Informasi Magang</h1>
            <h2 class="text-xl text-gray-300 font-light uppercase tracking-widest mb-8">Badan Pusat Statistik Provinsi Banten</h2>
            
            <p class="text-gray-400 text-sm max-w-md mx-auto leading-relaxed">
                Platform terintegrasi untuk pendaftaran, seleksi, dan pengelolaan kegiatan magang secara digital, transparan, dan efisien.
            </p>

            <!-- <div class="mt-12 flex justify-center space-x-2">
                <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
            </div> -->
        </div>
    </div>

    <div class="w-full lg:w-1/2 bg-white flex flex-col justify-center items-center px-8 sm:px-12 lg:px-24 relative">
        
        <div class="lg:hidden absolute top-8 text-center">
            <img src="<?= base_url('assets/img/logo.png') ?>" class="h-12 mx-auto mb-2">
            <h3 class="font-bold text-bps-blue">BPS PROVINSI BANTEN</h3>
        </div>

        <div class="w-full max-w-md">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Selamat Datang</h2>
                <p class="text-gray-500 text-sm">Silakan masuk menggunakan akun yang telah terdaftar.</p>
            </div>

            <form action="<?= base_url('auth/process_login') ?>" method="POST" autocomplete="off">
    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

    <div class="mb-5">
        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Email Address</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                <i class="fas fa-envelope"></i>
            </span>
            <input type="email" name="email" 
                   class="form-input w-full pl-11 pr-4 py-3 border border-gray-300 rounded-lg text-gray-800 bg-gray-50 focus:bg-white outline-none" 
                   placeholder="Masukkan email anda" required autofocus>
        </div>
    </div>

    <div class="mb-5">
        <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Password</label>
        <div class="relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                <i class="fas fa-lock"></i>
            </span>
            <input type="password" name="password" id="password"
                   class="form-input w-full pl-11 pr-12 py-3 border border-gray-300 rounded-lg text-gray-800 bg-gray-50 focus:bg-white outline-none" 
                   placeholder="Masukkan password" required>
            
            <button type="button" onclick="togglePassword()" 
                    class="absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-bps-blue transition cursor-pointer focus:outline-none">
                <i class="fas fa-eye" id="eye-icon"></i>
            </button>
        </div>
    </div>

                <div class="mb-8">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">Keamanan</label>
                    <div class="flex items-center gap-3">
                        <div class="bg-bps-blue text-white px-4 py-3 rounded-lg font-mono font-bold text-lg tracking-widest select-none shadow-md w-1/3 text-center">
                            <?= $captcha_text ?>
                        </div>
                        <input type="number" name="captcha" 
                               class="form-input w-2/3 px-4 py-3 border border-gray-300 rounded-lg text-gray-800 bg-gray-50 focus:bg-white outline-none" 
                               placeholder="Hasil..." required>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-bps-blue text-white font-bold py-3.5 rounded-lg shadow-lg hover:bg-opacity-90 hover:shadow-xl transition duration-300 transform active:scale-95 flex justify-center items-center gap-2">
                    MASUK SEKARANG <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </form>
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">&copy; <?= date('Y') ?> BPS Provinsi Banten. All rights reserved.</p>
            </div>
        </div>
    </div>

    <script>
        // 1. Toggle Password Visibility
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // 2. SweetAlert for Errors (Flashdata)
        <?php if ($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Masuk',
                text: '<?= $this->session->flashdata('error') ?>',
                confirmButtonColor: '#003366',
                confirmButtonText: 'Coba Lagi',
				heightAuto: false,
				buttonsStyling: true
            });
        <?php endif; ?>

        // 3. SweetAlert for Logout/Success
        <?php if ($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= $this->session->flashdata('success') ?>',
                timer: 2000,
                showConfirmButton: false,
				heightAuto: false
            });
        <?php endif; ?>
    </script>
</body>
</html>
