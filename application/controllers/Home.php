<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function index()
    {
        $data['title'] = 'Beranda';

		$data['kampus_list'] = $this->db->get('master_institusi')->result();
		$data['fakultas_list'] = $this->db->get('master_fakultas')->result();
        $data['jurusan_list'] = $this->db->get('master_jurusan')->result();

		$this->load->view('home/index', $data);
    }

	public function submit()
	{
		// 1. Validasi Input Dasar
		$this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim|xss_clean');
		
		// UPDATE: Validasi Email
		// Pastikan email belum terdaftar di tabel users maupun pendaftar
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]|is_unique[pendaftar.email]', [
			'is_unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau login.'
		]);
		
		$this->form_validation->set_rules('jenis_peserta', 'Jenis Peserta', 'required|in_list[mahasiswa,siswa]');
		$this->form_validation->set_rules('nim_nis', 'NIM/NIS', 'required|trim');
		$this->form_validation->set_rules('no_surat', 'Nomor Surat', 'required|trim');
		$this->form_validation->set_rules('tgl_surat', 'Tanggal Surat', 'required');
		$this->form_validation->set_rules('fakultas', 'Fakultas', 'required|trim');
		$this->form_validation->set_rules('no_hp', 'Nomor WhatsApp', 'required|numeric|min_length[10]|max_length[15]');
		$this->form_validation->set_rules('tgl_mulai', 'Tanggal Mulai', 'required');
		$this->form_validation->set_rules('tgl_selesai', 'Tanggal Selesai', 'required');

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect('home#daftar'); 
			return;
		}

		// 2. Hitung Durasi (Bulan) - CODE LAMA TETAP SAMA
		$d1 = new DateTime($this->input->post('tgl_mulai'));
		$d2 = new DateTime($this->input->post('tgl_selesai'));
		$interval = $d1->diff($d2);
		$durasi_bulan = $interval->m + ($interval->y * 12) + ($interval->d > 15 ? 1 : 0); 
		if($durasi_bulan < 1) $durasi_bulan = 1;

		// 3. Konfigurasi Upload - CODE LAMA TETAP SAMA
		$upload_cv = $this->_upload_file('file_cv', 'cv', 'pdf|doc|docx');
		$upload_foto = $this->_upload_file('file_foto', 'foto', 'jpg|jpeg|png');
		$upload_surat = $this->_upload_file('file_surat', 'surat', 'pdf|jpg|jpeg|png');

		if(isset($upload_cv['error']) || isset($upload_foto['error']) || isset($upload_surat['error'])) {
			$error_msg = '';
			$error_msg .= isset($upload_cv['error']) ? 'CV: '.$upload_cv['error'].' ' : '';
			$error_msg .= isset($upload_foto['error']) ? 'Foto: '.$upload_foto['error'].' ' : '';
			$error_msg .= isset($upload_surat['error']) ? 'Surat: '.$upload_surat['error'] : '';
			
			$this->session->set_flashdata('error', $error_msg);
			redirect('home#daftar');
			return;
		}

		// 4. Database Transaction
		$this->db->trans_start();

		$data_pendaftar = [
			'nama' => $this->input->post('nama', TRUE),
			// UPDATE: Simpan Email ke tabel pendaftar
			'email' => $this->input->post('email', TRUE),
			'jenis_peserta' => $this->input->post('jenis_peserta'),
			'nim_nis' => $this->input->post('nim_nis'),
			'no_surat' => $this->input->post('no_surat', TRUE),
			'tgl_surat' => $this->input->post('tgl_surat'),
			'institusi' => $this->input->post('institusi', TRUE),
			'fakultas' => $this->input->post('fakultas', TRUE),
			'jurusan' => $this->input->post('jurusan', TRUE),
			'no_hp' => $this->input->post('no_hp'),
			'jenis_magang' => $this->input->post('jenis_magang'),
			'tgl_mulai' => $this->input->post('tgl_mulai'),
			'tgl_selesai' => $this->input->post('tgl_selesai'),
			'durasi_bulan' => $durasi_bulan,
			'status' => 'pending'
		];

		$this->db->insert('pendaftar', $data_pendaftar);
		$pendaftar_id = $this->db->insert_id();

		// Insert Batch Dokumen (CODE LAMA TETAP SAMA)
		$dokumen = [
			[
				'pendaftar_id' => $pendaftar_id,
				'jenis_dokumen' => 'cv',
				'file_path' => $upload_cv['file_name'],
				'file_name_original' => $upload_cv['client_name']
			],
			[
				'pendaftar_id' => $pendaftar_id,
            'jenis_dokumen' => 'foto',
            'file_path' => $upload_foto['file_name'],
            'file_name_original' => $upload_foto['client_name']
        ],
        [
            'pendaftar_id' => $pendaftar_id,
            'jenis_dokumen' => 'surat_permohonan',
            'file_path' => $upload_surat['file_name'],
            'file_name_original' => $upload_surat['client_name']
        ]
    ];
    $this->db->insert_batch('dokumen', $dokumen);

    $this->db->trans_complete();

    if ($this->db->trans_status() === FALSE) {
        $this->session->set_flashdata('error', 'Terjadi kesalahan sistem database.');
        redirect('home#daftar');
    } else {
        // KIRIM NOTIFIKASI WA (CODE LAMA TETAP SAMA)
        $pesan_wa = "Halo *{$data_pendaftar['nama']}*,\n\nPendaftaran magang Anda di BPS Banten telah diterima.\nNo. Surat: {$data_pendaftar['no_surat']}\n\nMohon tunggu proses SELEKSI dan verifikasi admin.";
        $this->wa_client->send_message($data_pendaftar['no_hp'], $pesan_wa);

        $this->session->set_flashdata('success', 'Pendaftaran Berhasil! Silakan tunggu konfirmasi via WhatsApp.');
        redirect('home');
    }
    }

	private function _upload_file($field_name, $folder, $types)
    {
        $config['upload_path']   = './assets/uploads/' . $folder;
        $config['allowed_types'] = $types;
        $config['max_size']      = 5120; // 5MB
        $config['encrypt_name']  = TRUE; // Security: Rename to Random UUID

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($field_name)) {
            return ['error' => $this->upload->display_errors()];
        }
        return $this->upload->data();
    }
}
