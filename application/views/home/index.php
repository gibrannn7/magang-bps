<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magang BPS Provinsi Banten</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bps: {
                            dark: '#003366', // Biru BPS Tua
                            light: '#0099CC', // Biru BPS Muda
                            accent: '#FF9900', // Oranye Aksen
                            soft: '#E6F7FF',   // Biru background halus
                            success: '#10B981', // Hijau Sukses
                            error: '#EF4444'    // Merah Error
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Hero Background dengan Overlay Gradient */
        .hero-bg {
            background: linear-gradient(to bottom, rgba(0, 51, 102, 0.9), rgba(0, 51, 102, 0.7)), url('<?= base_url("assets/img/background.jpeg") ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Pattern Footer */
        .footer-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        /* Custom File Input Styles */
        .file-upload-active {
            border-color: #0099CC !important;
            background-color: #E6F7FF !important;
        }
        .file-upload-active i {
            color: #0099CC !important;
        }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <nav class="absolute w-full z-50 transition-all duration-300 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <img src="<?= base_url('assets/img/logo.png') ?>" class="h-10 w-auto bg-white rounded-full p-1 shadow-md">
                    <div class="text-white font-bold leading-tight hidden md:block">
                        <span class="block text-sm text-gray-200">BADAN PUSAT STATISTIK</span>
                        <span class="block text-lg tracking-wide font-extrabold text-white">PROVINSI BANTEN</span>
                    </div>
                </div>
                <div class="hidden md:flex space-x-6 items-center">
                    <a href="#home" class="text-gray-200 hover:text-white font-medium transition hover:underline underline-offset-4 decoration-bps-accent">Beranda</a>
                    <a href="#about" class="text-gray-200 hover:text-white font-medium transition hover:underline underline-offset-4 decoration-bps-accent">Tentang</a>
                    <a href="#daftar" class="text-gray-200 hover:text-white font-medium transition hover:underline underline-offset-4 decoration-bps-accent">Pendaftaran</a>
                    <a href="<?= base_url('auth/login') ?>" class="bg-white text-bps-dark px-6 py-2 rounded-full font-bold hover:bg-bps-accent hover:text-white transition shadow-lg transform hover:-translate-y-0.5">Login</a>
                </div>
            </div>
        </div>
    </nav>

    <section id="home" class="hero-bg h-screen flex items-center justify-center text-center px-4 relative">
        <div class="max-w-5xl" data-aos="fade-up" data-aos-duration="1000">
            <div class="inline-block px-4 py-1 mb-4 border border-white/30 rounded-full bg-white/10 backdrop-blur-sm">
                <span class="text-bps-accent font-semibold text-sm tracking-wide uppercase">
                    <i class="fas fa-chart-pie mr-2"></i> Penerimaan Magang & PKL BPS
                </span>
            </div>
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight drop-shadow-xl">
                Mencetak Talenta Data, <br>
                Hadirkan <span class="text-transparent bg-clip-text bg-gradient-to-r from-bps-accent to-yellow-200">Insight untuk Negeri</span>
            </h1>
            <p class="text-gray-100 text-lg md:text-xl mb-10 max-w-3xl mx-auto font-light leading-relaxed">
                Bergabunglah dengan Badan Pusat Statistik Provinsi Banten. 
                Tempa keahlian statistik Anda dalam lingkungan profesional dan berkontribusi nyata bagi pembangunan Indonesia.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#daftar" class="bg-bps-accent text-bps-dark px-8 py-4 rounded-full text-lg font-bold shadow-xl hover:bg-white hover:text-bps-dark transform hover:scale-105 transition duration-300">
                    Daftar Sekarang <i class="fas fa-arrow-right ml-2"></i>
                </a>
                <a href="#about" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/10 transition duration-300">
                    Pelajari Program
                </a>
            </div>
        </div>
        
        <div class="absolute bottom-10 animate-bounce text-white/50">
            <i class="fas fa-chevron-down text-2xl"></i>
        </div>
    </section>

    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-bps-light font-bold tracking-wider uppercase text-sm">Profil Singkat</span>
                <h2 class="text-bps-dark font-extrabold text-3xl md:text-4xl mt-2 mb-4">Kenapa BPS Banten?</h2>
                <div class="w-20 h-1.5 bg-bps-accent mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div data-aos="fade-right" class="relative">
                    <div class="absolute -top-4 -left-4 w-24 h-24 bg-bps-light/10 rounded-full -z-10"></div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-bps-accent/10 rounded-full -z-10"></div>
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80" class="rounded-2xl shadow-2xl z-10 relative border-4 border-white">
                </div>
                <div data-aos="fade-left">
                    <h3 class="text-2xl font-bold text-bps-dark mb-4">Pusat Rujukan Data Statistik</h3>
                    <p class="text-gray-600 mb-6 leading-relaxed text-lg">
                        Badan Pusat Statistik (BPS) adalah lembaga pemerintah non-kementerian yang bertanggung jawab langsung kepada Presiden. Kami menyediakan data berkualitas untuk Indonesia Maju.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start p-4 bg-gray-50 rounded-xl hover:bg-bps-soft transition duration-300">
                            <div class="bg-white p-3 rounded-lg shadow-sm text-bps-light mr-4">
                                <i class="fas fa-database text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Big Data & Statistik</h4>
                                <p class="text-sm text-gray-600 mt-1">Pelajari metodologi pengumpulan dan pengolahan data modern.</p>
                            </div>
                        </div>
                        <div class="flex items-start p-4 bg-gray-50 rounded-xl hover:bg-bps-soft transition duration-300">
                            <div class="bg-white p-3 rounded-lg shadow-sm text-bps-light mr-4">
                                <i class="fas fa-laptop-code text-xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Implementasi Teknologi</h4>
                                <p class="text-sm text-gray-600 mt-1">Pengembangan sistem informasi dan visualisasi data statistik.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="daftar" class="py-24 bg-gray-50 relative">
        <div class="absolute top-0 left-0 w-full h-64 bg-bps-dark rounded-b-[3rem] -z-0"></div>

        <div class="max-w-5xl mx-auto px-4 relative z-10">
            <div class="text-center mb-12" data-aos="fade-up">
                <h2 class="text-white font-bold text-3xl md:text-4xl mb-2">Formulir Pendaftaran</h2>
                <p class="text-blue-100 text-lg">Lengkapi data diri Anda untuk memulai seleksi</p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100" data-aos="zoom-in">
                <div class="bg-gray-50 p-8 border-b border-gray-200">
                    <div class="flex items-center justify-between relative max-w-2xl mx-auto">
                        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-300 -z-10 rounded-full"></div>
                        <div id="line-progress" class="absolute left-0 top-1/2 transform -translate-y-1/2 w-0 h-1.5 bg-bps-light transition-all duration-500 -z-0 rounded-full"></div>

                        <div class="flex flex-col items-center z-10">
                            <div id="step-icon-1" class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg transition-all duration-300 bg-bps-light text-white shadow-lg ring-4 ring-white">1</div>
                            <span class="text-xs font-bold mt-3 text-bps-dark uppercase tracking-wider">Biodata</span>
                        </div>
                        <div class="flex flex-col items-center z-10">
                            <div id="step-icon-2" class="w-12 h-12 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center font-bold text-lg transition-all duration-300 ring-4 ring-white">2</div>
                            <span class="text-xs font-bold mt-3 text-gray-400 uppercase tracking-wider">Akademik</span>
                        </div>
                        <div class="flex flex-col items-center z-10">
                            <div id="step-icon-3" class="w-12 h-12 rounded-full bg-white border-2 border-gray-300 text-gray-400 flex items-center justify-center font-bold text-lg transition-all duration-300 ring-4 ring-white">3</div>
                            <span class="text-xs font-bold mt-3 text-gray-400 uppercase tracking-wider">Berkas</span>
                        </div>
                    </div>
                </div>

                <form id="form-daftar" action="<?= base_url('home/submit') ?>" method="POST" enctype="multipart/form-data" class="p-8 md:p-12">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <div id="form-step-1" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
								<input type="text" name="nama" required class="w-full rounded-xl border-gray-300 focus:border-bps-light focus:ring-bps-light px-5 py-3 border transition shadow-sm bg-gray-50 focus:bg-white" placeholder="Sesuai KTP/KTM">
							</div>

							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email (Aktif)</label>
								<input type="email" name="email" required class="w-full rounded-xl border-gray-300 focus:border-bps-light focus:ring-bps-light px-5 py-3 border transition shadow-sm bg-gray-50 focus:bg-white" placeholder="contoh@email.com">
								<small class="text-gray-500">Email ini akan digunakan untuk <b>Login</b>.</small>
							</div>
							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Nomor WhatsApp</label>
								<input type="number" name="no_hp" placeholder="Contoh: 08123456789" required class="w-full rounded-xl border-gray-300 focus:border-bps-light focus:ring-bps-light px-5 py-3 border transition shadow-sm bg-gray-50 focus:bg-white">
							</div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Status Peserta</label>
                                <div class="relative">
                                    <select name="jenis_peserta" id="jenis_peserta" class="w-full rounded-xl border-gray-300 focus:border-bps-light focus:ring-bps-light px-5 py-3 border appearance-none bg-gray-50 focus:bg-white transition shadow-sm">
                                        <option value="mahasiswa">Mahasiswa</option>
                                        <option value="siswa">Siswa SMK/SMA</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                             <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">NIM / NISN</label>
                                <input type="number" name="nim_nis" required class="w-full rounded-xl border-gray-300 focus:border-bps-light focus:ring-bps-light px-5 py-3 border transition shadow-sm bg-gray-50 focus:bg-white">
                            </div>
                        </div>
                        <div class="flex justify-end pt-4">
                            <button type="button" onclick="nextStep(2)" class="bg-bps-light text-white px-8 py-3 rounded-xl font-bold hover:bg-bps-dark transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center">
                                Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <div id="form-step-2" class="space-y-8 hidden">
						<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
							
							<div class="md:col-span-2">
								<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
									<div class="flex">
										<div class="ml-3">
											<p class="text-sm text-yellow-700">
												Masukan Nomor Surat Permohonan sesuai surat resmi dari Kampus/Sekolah. <br>
												Contoh: <b>202/FILKOM/I.I/XI/2025</b>
											</p>
										</div>
									</div>
								</div>
							</div>

							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Nomor Surat Permohonan</label>
								<input type="text" name="no_surat" required class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white transition" placeholder="Nomor Surat Kampus/Sekolah">
							</div>

							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Surat Dibuat</label>
								<input type="date" name="tgl_surat" required class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white transition">
							</div>

							<div class="md:col-span-2 border-t pt-4"></div>

							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Instansi / Universitas / Sekolah</label>
								<input list="list-kampus" name="institusi" required class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white transition" placeholder="Ketik nama kampus/sekolah...">
								<datalist id="list-kampus">
									<?php foreach($kampus_list as $k): ?>
										<option value="<?= $k->nama_institusi ?>">
									<?php endforeach; ?>
								</datalist>
							</div>

							<div id="container-fakultas">
								<label class="block text-sm font-semibold text-gray-700 mb-2">Fakultas</label>
								<input list="list-fakultas" name="fakultas" id="input-fakultas" class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white transition" placeholder="Pilih atau ketik fakultas...">
								
								<datalist id="list-fakultas">
									<?php foreach($fakultas_list as $f): ?>
										<option value="<?= $f->nama_fakultas ?>">
									<?php endforeach; ?>
								</datalist>
								<small class="text-gray-500 text-xs mt-1">*Ketik manual jika tidak ada di list</small>
							</div>

							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan / Program Studi</label>
								<input list="list-jurusan" name="jurusan" required class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white transition" placeholder="Cari jurusan...">
								<datalist id="list-jurusan">
									<?php foreach($jurusan_list as $j): ?>
										<option value="<?= $j->nama_jurusan ?>">
									<?php endforeach; ?>
								</datalist>
							</div>

							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Rencana Tanggal Mulai</label>
								<input type="date" name="tgl_mulai" required class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white focus:border-bps-light focus:ring-bps-light transition">
							</div>
							<div>
								<label class="block text-sm font-semibold text-gray-700 mb-2">Rencana Tanggal Selesai</label>
								<input type="date" name="tgl_selesai" required class="w-full rounded-xl border-gray-300 px-5 py-3 border bg-gray-50 focus:bg-white focus:border-bps-light focus:ring-bps-light transition">
							</div>

							<div class="md:col-span-2">
								<label class="block text-sm font-semibold text-gray-700 mb-3">Jenis Permohonan Magang</label>
								<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
									<label class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-bps-soft hover:border-bps-light transition group">
										<input type="radio" name="jenis_magang" value="wajib" checked class="w-5 h-5 text-bps-light focus:ring-bps-light border-gray-300">
										<div class="ml-3">
											<span class="block font-bold text-gray-800 group-hover:text-bps-dark">Magang Wajib / KKP</span>
											<span class="block text-xs text-gray-500">Program wajib dari kampus/sekolah</span>
										</div>
									</label>
									<label class="flex items-center p-4 border-2 rounded-xl cursor-pointer hover:bg-bps-soft hover:border-bps-light transition group">
										<input type="radio" name="jenis_magang" value="mandiri" class="w-5 h-5 text-bps-light focus:ring-bps-light border-gray-300">
										<div class="ml-3">
											<span class="block font-bold text-gray-800 group-hover:text-bps-dark">Magang Mandiri</span>
											<span class="block text-xs text-gray-500">Inisiatif pengembangan diri sendiri</span>
										</div>
									</label>
								</div>
							</div>

						</div>
						<div class="flex justify-between pt-4">
							<button type="button" onclick="nextStep(1)" class="text-gray-500 font-semibold hover:text-bps-dark px-6 py-3 transition">Kembali</button>
							<button type="button" onclick="nextStep(3)" class="bg-bps-light text-white px-8 py-3 rounded-xl font-bold hover:bg-bps-dark transition shadow-lg hover:shadow-xl transform hover:-translate-y-1 flex items-center">
								Selanjutnya <i class="fas fa-arrow-right ml-2"></i>
							</button>
						</div>
					</div>
                    <div id="form-step-3" class="space-y-8 hidden">
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 mb-6 flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-1 mr-3 text-lg"></i>
                            <div>
                                <h5 class="font-bold text-blue-800 text-sm">Persyaratan Berkas</h5>
                                <p class="text-sm text-blue-700">Pastikan file dalam format PDF/DOCX (untuk CV) atau JPG/PNG (untuk Foto/Surat) dengan ukuran maksimal 5MB.</p>
                            </div>
                        </div>
                        
                        <div class="space-y-6">
                            
                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Curriculum Vitae (CV) <span class="text-red-500">*</span></label>
                                <div id="container-cv" class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-bps-light hover:bg-bps-soft transition cursor-pointer">
                                    <input type="file" name="file_cv" id="file_cv" accept=".pdf,.doc,.docx" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="updateFileLabel(this, 'label-cv', 'icon-cv', 'container-cv')">
                                    
                                    <div class="relative z-10 pointer-events-none">
                                        <i id="icon-cv" class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 group-hover:text-bps-light transition"></i>
                                        <p id="label-cv" class="text-sm text-gray-500 group-hover:text-bps-dark font-medium truncate px-4">Klik untuk upload atau drag file ke sini</p>
                                    </div>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pas Foto Resmi <span class="text-red-500">*</span></label>
                                <div id="container-foto" class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-bps-light hover:bg-bps-soft transition cursor-pointer">
                                    <input type="file" name="file_foto" id="file_foto" accept=".jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="updateFileLabel(this, 'label-foto', 'icon-foto', 'container-foto')">
                                    
                                    <div class="relative z-10 pointer-events-none">
                                        <i id="icon-foto" class="fas fa-image text-3xl text-gray-400 mb-2 group-hover:text-bps-light transition"></i>
                                        <p id="label-foto" class="text-sm text-gray-500 group-hover:text-bps-dark font-medium truncate px-4">Klik untuk upload foto</p>
                                    </div>
                                </div>
                            </div>

                            <div class="group">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Surat Pengantar Kampus/Sekolah <span class="text-red-500">*</span></label>
                                <div id="container-surat" class="relative border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-bps-light hover:bg-bps-soft transition cursor-pointer">
                                    <input type="file" name="file_surat" id="file_surat" accept=".pdf,.jpg,.jpeg,.png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" onchange="updateFileLabel(this, 'label-surat', 'icon-surat', 'container-surat')">
                                    
                                    <div class="relative z-10 pointer-events-none">
                                        <i id="icon-surat" class="fas fa-file-alt text-3xl text-gray-400 mb-2 group-hover:text-bps-light transition"></i>
                                        <p id="label-surat" class="text-sm text-gray-500 group-hover:text-bps-dark font-medium truncate px-4">Klik untuk upload surat pengantar</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between pt-8">
                            <button type="button" onclick="nextStep(2)" class="text-gray-500 font-semibold hover:text-bps-dark px-6 py-3 transition">Kembali</button>
                            <button type="button" onclick="validateAndSubmit()" class="bg-bps-dark text-white px-10 py-4 rounded-xl font-bold shadow-xl hover:bg-gray-800 transition transform hover:-translate-y-1 w-full md:w-auto flex justify-center items-center">
                                <i class="fas fa-paper-plane mr-2"></i> Kirim Pendaftaran
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16">
                <div data-aos="fade-right">
                    <div class="mb-6">
                        <h2 class="text-3xl font-extrabold text-bps-dark">Lokasi Kantor</h2>
                        <p class="text-gray-500 mt-2">Kunjungi kantor kami di pusat pemerintahan Provinsi Banten.</p>
                    </div>
                    <div class="p-2 bg-gray-100 rounded-3xl shadow-inner">
                        <div id="map" class="h-96 rounded-2xl shadow-lg z-0 border border-gray-300"></div>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <div class="mb-8">
                        <h2 class="text-3xl font-extrabold text-bps-dark">Hubungi Kami</h2>
                        <p class="text-gray-500 mt-2">Tim kami siap membantu pertanyaan Anda seputar program magang.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-6">
                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                            <div class="flex items-start">
                                <div class="bg-bps-soft p-4 rounded-xl text-bps-light group-hover:bg-bps-light group-hover:text-white transition duration-300">
                                    <i class="fas fa-map-marker-alt text-2xl"></i>
                                </div>
                                <div class="ml-5">
                                    <h4 class="font-bold text-gray-900 text-lg">Alamat Kantor</h4>
                                    <p class="text-gray-600 mt-1 leading-relaxed text-sm">Jl. Syeh Nawawi Al Bantani, Kawasan Pusat Pemerintahan Provinsi Banten (KP3B), Kav. H1-2, Serang Banten.</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                            <div class="flex items-center">
                                <div class="bg-bps-soft p-4 rounded-xl text-bps-light group-hover:bg-bps-light group-hover:text-white transition duration-300">
                                    <i class="fas fa-phone-alt text-2xl"></i>
                                </div>
                                <div class="ml-5">
                                    <h4 class="font-bold text-gray-900 text-lg">Layanan Telepon</h4>
                                    <p class="text-gray-600 mt-1 font-mono text-lg font-medium">(0254) 267027</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                            <div class="flex items-center">
                                <div class="bg-bps-soft p-4 rounded-xl text-bps-light group-hover:bg-bps-light group-hover:text-white transition duration-300">
                                    <i class="fas fa-envelope-open-text text-2xl"></i>
                                </div>
                                <div class="ml-5">
                                    <h4 class="font-bold text-gray-900 text-lg">Email Resmi</h4>
                                    <a href="mailto:bps3600@bps.go.id" class="text-bps-light hover:text-bps-dark font-medium transition mt-1 block">bps3600@bps.go.id</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="w-full text-bps-dark leading-none bg-white">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto">
            <path fill="currentColor" fill-opacity="1" d="M0,192L48,197.3C96,203,192,213,288,229.3C384,245,480,267,576,250.7C672,235,768,181,864,160C960,139,1056,149,1152,160C1248,171,1344,181,1392,186.7L1440,192L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>

    <footer class="bg-bps-dark text-white pt-4 pb-12 footer-pattern border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
                
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="<?= base_url('assets/img/logo.png') ?>" class="h-12 w-auto bg-white rounded p-1 shadow">
                        <div>
                            <h3 class="font-bold text-xl leading-none">Badan Pusat Statistik</h3>
                            <span class="text-bps-accent text-2xl font-black tracking-widest">Provinsi BANTEN</span>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed max-w-sm">
                        Berkomitmen menyediakan data statistik berkualitas yang terpercaya untuk mewujudkan Indonesia Maju. Bergabunglah bersama kami membangun literasi data bangsa.
                    </p>
                    <div class="flex space-x-4 pt-4">
                        <a href="https://www.instagram.com/bps_banten/" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bps-accent hover:text-bps-dark transition duration-300 border border-white/10">
                            <i class="fab fa-instagram text-lg"></i>
                        </a>
                        <a href="https://x.com/bps_banten" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bps-accent hover:text-bps-dark transition duration-300 border border-white/10">
                            <i class="fab fa-twitter text-lg"></i>
                        </a>
                        <a href="https://www.youtube.com/channel/UCpXgebASNuZ0q_-f6t9RnPA" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bps-accent hover:text-bps-dark transition duration-300 border border-white/10">
                            <i class="fab fa-youtube text-lg"></i>
                        </a>
                        <a href="https://banten.bps.go.id/id" target="_blank" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bps-accent hover:text-bps-dark transition duration-300 border border-white/10">
                            <i class="fas fa-globe text-lg"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6 text-white border-b-2 border-bps-accent inline-block pb-1">Tautan Cepat</h4>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li><a href="#home" class="hover:text-bps-accent transition flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-bps-light"></i> Beranda</a></li>
                        <li><a href="#about" class="hover:text-bps-accent transition flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-bps-light"></i> Tentang Program</a></li>
                        <li><a href="#daftar" class="hover:text-bps-accent transition flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-bps-light"></i> Formulir Pendaftaran</a></li>
                        <li><a href="<?= base_url('auth/login') ?>" class="hover:text-bps-accent transition flex items-center"><i class="fas fa-chevron-right text-xs mr-2 text-bps-light"></i> Login Admin</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-bold mb-6 text-white border-b-2 border-bps-accent inline-block pb-1">Jam Pelayanan</h4>
                    <div class="space-y-3 text-gray-300 text-sm">
                        <div class="flex justify-between border-b border-gray-700 pb-2">
                            <span>Senin - Kamis</span>
                            <span class="font-bold text-white">08.00 - 16.00</span>
                        </div>
                        <div class="flex justify-between border-b border-gray-700 pb-2">
                            <span>Jumat</span>
                            <span class="font-bold text-white">08.00 - 16.30</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Sabtu - Minggu</span>
                            <span>Tutup</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <p>&copy; 2025 BPS Provinsi Banten. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    
    <script>
	document.addEventListener("DOMContentLoaded", function() {
    const inputMulai = document.querySelector('input[name="tgl_mulai"]');
    const inputSelesai = document.querySelector('input[name="tgl_selesai"]');

    if(inputMulai && inputSelesai) {
        inputMulai.addEventListener('change', function() {
            if(this.value) {
                // Ambil tanggal mulai
                const tgl = new Date(this.value);
                
                // Tambah 1 Minggu (7 hari)
                tgl.setDate(tgl.getDate() + 7);
                
                // Format ke YYYY-MM-DD
                const yyyy = tgl.getFullYear();
                const mm = String(tgl.getMonth() + 1).padStart(2, '0');
                const dd = String(tgl.getDate()).padStart(2, '0');
                
                const minDate = `${yyyy}-${mm}-${dd}`;
                
                // Set Value otomatis
                inputSelesai.value = minDate;
                
                // Set atribut MIN agar tidak bisa pilih tanggal sebelumnya (Validasi Frontend)
                inputSelesai.setAttribute('min', minDate);
                
                // Opsional: Buat readonly agar user TIDAK BISA ubah sama sekali jika peraturan ketat
                // inputSelesai.setAttribute('readonly', true); 
            }
        });
    }
});
	
        // Init AOS Animation
        AOS.init({
            once: true,
            offset: 100,
            duration: 800,
        });

		const jenisPesertaSelect = document.getElementById('jenis_peserta');
        const containerFakultas = document.getElementById('container-fakultas');
        const inputFakultas = document.getElementById('input-fakultas');

        function toggleFakultas() {
            const jenis = jenisPesertaSelect.value;
            
            if (jenis === 'siswa') {
                // Jika Siswa: Sembunyikan field & isi otomatis dengan "-"
                containerFakultas.classList.add('hidden');
                inputFakultas.value = '-';
                inputFakultas.removeAttribute('required');
            } else {
                // Jika Mahasiswa: Tampilkan field & Reset jika isinya "-"
                containerFakultas.classList.remove('hidden');
                if(inputFakultas.value === '-') {
                    inputFakultas.value = '';
                }
                inputFakultas.setAttribute('required', 'required');
            }
        }

        // Panggil fungsi saat halaman dimuat (untuk handle jika ada error validation redirect)
        if(jenisPesertaSelect) {
            toggleFakultas();
            // Panggil fungsi saat dropdown berubah
            jenisPesertaSelect.addEventListener('change', toggleFakultas);
        }

        // 1. UPDATE FILE NAME LABEL (Agar tidak terlihat kosong)
        function updateFileLabel(input, labelId, iconId, containerId) {
            const label = document.getElementById(labelId);
            const icon = document.getElementById(iconId);
            const container = document.getElementById(containerId);
            
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                
                // Ganti teks label dengan nama file
                label.innerText = fileName;
                label.classList.add('text-bps-dark', 'font-bold');
                label.classList.remove('text-gray-500');

                // Ubah icon jadi centang
                icon.className = "fas fa-check-circle text-3xl text-green-500 mb-2 transition";
                
                // Ubah style container
                container.classList.remove('border-dashed', 'border-gray-300');
                container.classList.add('border-solid', 'file-upload-active');
            } else {
                // Reset jika batal
                label.innerText = "Klik untuk upload...";
                label.classList.remove('text-bps-dark', 'font-bold');
                label.classList.add('text-gray-500');
                
                icon.className = "fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2 transition";
                
                container.classList.add('border-dashed', 'border-gray-300');
                container.classList.remove('border-solid', 'file-upload-active');
            }
        }

        function validateAndSubmit() {
            // Cek apakah data kosong (Validasi sederhana)
            const nama = document.querySelector('input[name="nama"]').value;
            const hp = document.querySelector('input[name="no_hp"]').value;
            const cv = document.getElementById('file_cv').files.length;
            const foto = document.getElementById('file_foto').files.length;
            const surat = document.getElementById('file_surat').files.length;

            if(!nama || !hp || cv === 0 || foto === 0 || surat === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Mohon lengkapi semua isian dan berkas sebelum mengirim.',
                    confirmButtonColor: '#003366'
                });
                return;
            }

            // ALERT KONFIRMASI (Cek Lagi)
            Swal.fire({
                title: 'Sudah yakin?',
                text: "Pastikan data Anda sudah benar sebelum dikirim.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#003366',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Kirim Pendaftaran',
                cancelButtonText: 'Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan Loading
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    
                    // Submit Form
                    document.getElementById('form-daftar').submit();
                }
            });
        }

        // 2. ALERT SUKSES (Muncul otomatis setelah halaman reload/selesai submit)
        // CodeIgniter akan mengirim flashdata 'success', dan JS ini akan menangkapnya
        <?php if($this->session->flashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil!',
                text: <?= json_encode($this->session->flashdata('success')) ?>, // Gunakan json_encode
                confirmButtonColor: '#003366'
            });
        <?php endif; ?>

        // Handle jika ada error dari controller (Opsional, biar tau kalau gagal)
        <?php if($this->session->flashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                // PERBAIKAN DISINI: Gunakan json_encode agar enter/kutip aman
                text: <?= json_encode(strip_tags($this->session->flashdata('error'))) ?>, 
                confirmButtonColor: '#EF4444'
            });
        <?php endif; ?>

        // Wizard Logic
        function nextStep(step) {
            document.getElementById('form-step-1').classList.add('hidden');
            document.getElementById('form-step-2').classList.add('hidden');
            document.getElementById('form-step-3').classList.add('hidden');
            
            document.getElementById('form-step-' + step).classList.remove('hidden');
            document.getElementById('daftar').scrollIntoView({ behavior: 'smooth' });

            // Update Progress Bar UI
            const line = document.getElementById('line-progress');
            const icon1 = document.getElementById('step-icon-1');
            const icon2 = document.getElementById('step-icon-2');
            const icon3 = document.getElementById('step-icon-3');
            
            const activeClass = ['bg-bps-light', 'text-white', 'ring-bps-light'];
            const inactiveClass = ['bg-white', 'text-gray-400', 'ring-white'];

            if(step === 1) {
                line.style.width = '0%';
                icon2.classList.remove(...activeClass); icon2.classList.add(...inactiveClass);
                icon3.classList.remove(...activeClass); icon3.classList.add(...inactiveClass);
            } else if(step === 2) {
                line.style.width = '50%';
                icon1.classList.add(...activeClass);
                icon2.classList.remove(...inactiveClass); icon2.classList.add(...activeClass);
                icon3.classList.remove(...activeClass); icon3.classList.add(...inactiveClass);
            } else if(step === 3) {
                line.style.width = '100%';
                icon2.classList.add(...activeClass);
                icon3.classList.remove(...inactiveClass); icon3.classList.add(...activeClass);
            }
        }

        // Map Logic
        var map = L.map('map').setView([-6.171144960493601, 106.1609483232592], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; OpenStreetMap contributors'
        }).addTo(map);
        
        var customIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        L.marker([-6.171144960493601, 106.1609483232592], {icon: customIcon}).addTo(map)
            .bindPopup('<b>BPS Provinsi Banten</b><br>KP3B Serang').openPopup();
    </script>
</body>
</html>
