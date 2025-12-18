<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<style>
    #my_camera video { max-width: 100%; height: auto; }
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card card-primary card-outline card-outline-tabs">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-hadir-tab" data-toggle="pill" href="#tab-hadir" role="tab">Absen Hadir</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-izin-tab" data-toggle="pill" href="#tab-izin" role="tab">Form Izin / Sakit</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    
                    <div class="tab-pane fade show active" id="tab-hadir" role="tabpanel">
                        <div class="text-center">
                            <div id="my_camera" class="d-inline-block shadow-lg rounded" style="width:320px; height:240px; background:#ccc;"></div>
                            <br>
                            <button type="button" onClick="take_snapshot()" class="btn btn-primary mt-2">
                                <i class="fas fa-camera"></i> Ambil Foto
                            </button>
                            <input type="hidden" id="foto_data" name="foto">
                        </div>
                        
                        <div id="results" class="text-center mt-3" style="display:none;">
                            <img id="prev_img" src="" class="rounded shadow-sm" width="200">
                            <p class="text-muted text-sm mt-1">Preview Foto</p>
                        </div>

                        <hr>
                        
                        <div class="alert alert-info text-center">
                            <i class="fas fa-map-marker-alt"></i> <span id="geo_info">Mendeteksi Lokasi...</span>
                        </div>

                        <div class="row mt-3">
                            <div class="col-6">
                                <button onclick="kirimAbsen('datang')" id="btn-datang" class="btn btn-success btn-block" disabled>
                                    <i class="fas fa-sign-in-alt"></i> Masuk
                                </button>
                            </div>
                            <div class="col-6">
                                <button onclick="kirimAbsen('pulang')" id="btn-pulang" class="btn btn-danger btn-block" disabled>
                                    <i class="fas fa-sign-out-alt"></i> Pulang
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-izin" role="tabpanel">
						<form id="form-izin" enctype="multipart/form-data">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Tanggal Mulai</label>
										<input type="date" class="form-control" id="tgl_mulai_izin" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Tanggal Selesai</label>
										<input type="date" class="form-control" id="tgl_selesai_izin" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>">
									</div>
								</div>
							</div>
							<div class="form-group">
								<label>Jenis Izin</label>
								<select class="form-control" id="jenis_izin">
									<option value="sakit">Sakit</option>
									<option value="acara_keluarga">Acara Keluarga</option>
									<option value="keperluan_kampus">Keperluan Kampus</option>
									<option value="lainnya">Lainnya</option>
								</select>
							</div>
							<div class="form-group">
								<label>Keterangan Detail</label>
								<textarea class="form-control" id="keterangan_izin" rows="3" placeholder="Jelaskan alasan izin..."></textarea>
							</div>
							<div class="form-group">
								<label>Lampiran Bukti (Gambar: JPG/PNG)</label>
								<input type="file" class="form-control p-1" id="file_bukti" accept="image/*">
								<small class="text-muted">*Wajib lampirkan surat keterangan atau bukti pendukung.</small>
							</div>
							<button type="button" onclick="kirimIzin()" class="btn btn-warning btn-block font-weight-bold">
								<i class="fas fa-paper-plane"></i> Ajukan Izin
							</button>
						</form>
					</div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variable global untuk CSRF (akan diupdate setiap request)
    let csrfName = '<?= $this->security->get_csrf_token_name() ?>';
    let csrfHash = '<?= $this->security->get_csrf_hash() ?>';

    // 1. Setup Webcam
    Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
    Webcam.attach('#my_camera');

    let userLat = null;
    let userLong = null;

    // 2. Geolocation
    if (navigator.geolocation) {
        const options = {
            enableHighAccuracy: true,
            timeout: 10000, 
            maximumAge: 0
        };
        navigator.geolocation.getCurrentPosition(showPosition, showError, options);
    } else {
        document.getElementById("geo_info").innerHTML = "Geolocation tidak didukung browser ini.";
    }

    function showPosition(position) {
        userLat = position.coords.latitude;
        userLong = position.coords.longitude;
        document.getElementById("geo_info").innerHTML = `Lokasi Terkunci: ${userLat.toFixed(5)}, ${userLong.toFixed(5)}`;
        
        document.getElementById('btn-datang').disabled = false;
        document.getElementById('btn-pulang').disabled = false;
    }

    function showError(error) {
        let msg = "";
        switch(error.code) {
            case error.PERMISSION_DENIED: msg = "Izin lokasi ditolak. Aktifkan GPS!"; break;
            case error.POSITION_UNAVAILABLE: msg = "Lokasi tidak ditemukan (Sinyal lemah)."; break;
            case error.TIMEOUT: msg = "Gagal mendeteksi lokasi (Timeout). Refresh halaman."; break;
            default: msg = "Terjadi kesalahan sistem GPS."; break;
        }
        document.getElementById("geo_info").innerHTML = `<span class="text-danger font-weight-bold">${msg}</span>`;
        Swal.fire({ icon: 'error', title: 'GPS Error', text: msg });
    }

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            document.getElementById('results').style.display = 'block';
            document.getElementById('prev_img').src = data_uri;
            document.getElementById('foto_data').value = data_uri;
        });
    }

    // --- FUNGSI KIRIM UMUM (Handling JSON & Error) ---
    async function sendData(url, formData) {
        // Append Token CSRF Terbaru
        formData.append(csrfName, csrfHash);

        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData
            });

            // Baca sebagai text dulu untuk debugging jika server error HTML
            const textResponse = await response.text();
            
            let data;
            try {
                data = JSON.parse(textResponse);
            } catch (err) {
                // Jika gagal parse JSON, berarti Server Error (HTML)
                throw new Error("Server Error: " + textResponse);
            }

            // Update CSRF Token untuk request berikutnya
            if(data.csrf_token) {
                csrfHash = data.csrf_token;
            }

            return data;

        } catch (error) {
            throw error;
        }
    }

    // KIRIM ABSEN HADIR
    function kirimAbsen(tipe) {
        const foto = document.getElementById('foto_data').value;
        if(!foto) { Swal.fire('Foto Wajib', 'Ambil foto terlebih dahulu!', 'warning'); return; }
        if(!userLat) { Swal.fire('Lokasi Error', 'Lokasi belum ditemukan!', 'warning'); return; }

        Swal.fire({
            title: 'Kirim Absensi?',
            text: `Anda akan melakukan absen ${tipe}`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, Kirim!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

                const formData = new FormData();
                formData.append('latitude', userLat);
                formData.append('longitude', userLong);
                formData.append('tipe', tipe);
                formData.append('foto', foto);
                formData.append('is_izin', 'false');

                sendData('<?= base_url('peserta/submit_absen') ?>', formData)
                .then(data => {
                    Swal.close(); // Tutup loading
                    if(data.status) {
                        Swal.fire('Berhasil', data.message, 'success').then(() => window.location.href='<?= base_url('peserta') ?>');
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                })
                .catch(err => {
                    Swal.close();
                    console.error(err);
                    Swal.fire('System Error', err.message, 'error');
                });
            }
        });
    }

    // KIRIM IZIN
    function kirimIzin() {
		const tglMulai = document.getElementById('tgl_mulai_izin').value;
		const tglSelesai = document.getElementById('tgl_selesai_izin').value;
		const jenis = document.getElementById('jenis_izin').value;
		const ket   = document.getElementById('keterangan_izin').value;
		const fileInput = document.getElementById('file_bukti');

		if (!ket || !fileInput.files[0]) {
			Swal.fire('Data Kurang', 'Keterangan dan Lampiran Bukti wajib diisi.', 'warning');
			return;
		}

		Swal.fire({
			title: 'Ajukan Izin?',
			text: `Rentang: ${tglMulai} s/d ${tglSelesai}`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya, Ajukan'
		}).then((result) => {
			if (result.isConfirmed) {
				Swal.fire({ title: 'Mengirim Izin...', allowOutsideClick: false, didOpen: () => { Swal.showLoading() } });

				const formData = new FormData();
				formData.append('is_izin', 'true');
				formData.append('tgl_mulai', tglMulai);
				formData.append('tgl_selesai', tglSelesai);
				formData.append('jenis_izin', jenis);
				formData.append('keterangan', ket);
				formData.append('bukti_file', fileInput.files[0]); // Menambah file

				sendData('<?= base_url('peserta/submit_absen') ?>', formData)
				.then(data => {
					Swal.close();
					if (data.status) {
						Swal.fire('Izin Terdaftar', data.message, 'success').then(() => {
							window.location.href = '<?= base_url('peserta/riwayat_absensi') ?>';
						});
					} else {
						Swal.fire('Gagal', data.message, 'error');
					}
				})
				.catch(err => {
					Swal.close();
					Swal.fire('System Error', 'Terjadi kesalahan saat upload.', 'error');
				});
			}
		});
	}
</script>
