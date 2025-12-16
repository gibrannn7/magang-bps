<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

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
                        <form id="form-izin">
                            <div class="form-group">
                                <label>Jenis Izin</label>
                                <select class="form-control" id="jenis_izin">
                                    <option value="sakit">Sakit</option>
                                    <option value="acara_keluarga">Acara Keluarga</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Keterangan Detail</label>
                                <textarea class="form-control" id="keterangan_izin" rows="3" placeholder="Jelaskan alasan izin..."></textarea>
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
    // 1. Setup Webcam
    Webcam.set({ width: 320, height: 240, image_format: 'jpeg', jpeg_quality: 90 });
    Webcam.attach('#my_camera');

    let userLat = null;
    let userLong = null;

    // 2. Geolocation
    if (navigator.geolocation) {
        // Tambahkan options timeout (5 detik)
        const options = {
            enableHighAccuracy: true,
            timeout: 5000, 
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
        
        // Enable tombol
        document.getElementById('btn-datang').disabled = false;
        document.getElementById('btn-pulang').disabled = false;
    }

    // UPDATE FUNGSI SHOW ERROR
    function showError(error) {
        let msg = "";
        switch(error.code) {
            case error.PERMISSION_DENIED: msg = "Izin lokasi ditolak. Aktifkan GPS!"; break;
            case error.POSITION_UNAVAILABLE: msg = "Lokasi tidak ditemukan (Sinyal lemah)."; break;
            case error.TIMEOUT: msg = "Gagal mendeteksi lokasi (Timeout). Coba lagi."; break;
            default: msg = "Terjadi kesalahan sistem GPS."; break;
        }
        document.getElementById("geo_info").innerHTML = `<span class="text-danger font-weight-bold">${msg}</span>`;
        
        // Matikan loading jika ada
        Swal.fire({ icon: 'error', title: 'GPS Error', text: msg });
    }

    function take_snapshot() {
        Webcam.snap(function(data_uri) {
            document.getElementById('results').style.display = 'block';
            document.getElementById('prev_img').src = data_uri;
            document.getElementById('foto_data').value = data_uri;
        });
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
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Kirim!'
        }).then((result) => {
            if (result.isConfirmed) {
                showLoading(); // Function global loading (jika ada) or manual Swal loading

                const formData = new FormData();
                formData.append('latitude', userLat);
                formData.append('longitude', userLong);
                formData.append('tipe', tipe);
                formData.append('foto', foto);
                formData.append('is_izin', 'false');
                formData.append(CSRF_NAME, CSRF_HASH);

                fetch('<?= base_url('peserta/submit_absen') ?>', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.status) {
                        Swal.fire('Berhasil', data.message, 'success').then(() => window.location.href='<?= base_url('peserta') ?>');
                    } else {
                        Swal.fire('Gagal', data.message, 'error');
                    }
                });
            }
        });
    }

    // KIRIM IZIN
    function kirimIzin() {
		const jenis = document.getElementById('jenis_izin').value;
		const ket   = document.getElementById('keterangan_izin').value;

		// Validasi keterangan
		if (!ket) {
			Swal.fire(
				'Isi Keterangan',
				'Keterangan izin wajib diisi secara detail.',
				'warning'
			);
			return;
		}

		// Konfirmasi pengajuan izin
		Swal.fire({
			title: 'Ajukan Izin?',
			text: 'Anda menyatakan tidak dapat hadir hari ini.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Ya, Ajukan',
			cancelButtonText: 'Batal'
		}).then((result) => {
			if (result.isConfirmed) {

				// Loading
				Swal.fire({
					title: 'Mengirim Izin...',
					text: 'Mohon tunggu sebentar',
					allowOutsideClick: false,
					didOpen: () => {
						Swal.showLoading();
					}
				});

				const formData = new FormData();
				formData.append('is_izin', 'true');
				formData.append('jenis_izin', jenis);
				formData.append('keterangan', ket);
				formData.append(CSRF_NAME, CSRF_HASH);

				fetch('<?= base_url('peserta/submit_absen') ?>', {
					method: 'POST',
					body: formData
				})
				.then(response => response.json())
				.then(data => {
					Swal.close();

					if (data.status) {
						Swal.fire(
							'Izin Terkirim',
							data.message,
							'success'
						).then(() => {
							window.location.href = '<?= base_url('peserta') ?>';
						});
					} else {
						Swal.fire(
							'Gagal',
							data.message,
							'error'
						);
					}
				})
				.catch(() => {
					Swal.close();
					Swal.fire(
						'Error',
						'Terjadi kesalahan saat mengirim data.',
						'error'
					);
				});
			}
		});
	}


    function showLoading() {
        Swal.fire({ title: 'Memproses...', didOpen: () => { Swal.showLoading() } });
    }
</script>
