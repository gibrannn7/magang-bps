<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
			redirect('auth/login');
		}
		// Load model yang dibutuhkan
		$this->load->model('M_Admin');
		$this->load->model('M_Master'); // Tambahkan ini
		$this->load->model('M_Absensi');
		$this->load->model('M_Auth');
	}

    private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index()
	{
		$data['title'] = 'Dashboard Statistik';
		$today = date('Y-m-d');

		// Refactor: Menggunakan Model
		$stats = $this->M_Admin->get_stats_pendaftaran();
		$data['total_daftar'] = $stats['total'];
		$data['pending']      = $stats['pending'];
		$data['aktif']        = $stats['aktif'];
		$data['selesai']      = $stats['selesai'];

		$absen_stats = $this->M_Admin->get_stats_absensi_today($today);
		$data['hadir']      = $absen_stats['hadir'];
		$data['telat']      = $absen_stats['telat'];
		$data['absen_izin'] = $absen_stats['izin'];
		$data['belum_absen'] = max(0, $data['aktif'] - ($data['hadir'] + $data['telat'] + $data['absen_izin']));

		$data['pendaftar'] = $this->M_Admin->get_all_pendaftar();

		$this->render_view('admin/dashboard_lte', $data);
	}

    public function berkas($id)
    {
        $data['pendaftar'] = $this->M_Admin->get_pendaftar_by_id($id);
        if (!$data['pendaftar']) show_404();

        $data['dokumen'] = $this->M_Admin->get_dokumen_by_pendaftar($id);
        
        $data['akun'] = null;
        if ($data['pendaftar']->user_id) {
            // Memanggil fungsi yang baru saja ditambahkan di M_Auth
            $data['akun'] = $this->M_Auth->get_user_by_id($data['pendaftar']->user_id);
        }

        $data['title'] = 'Detail Berkas Peserta';
        $this->render_view('admin/detail_berkas', $data);
    }

    public function verifikasi($id, $status) {
    if (!in_array($status, ['diterima', 'ditolak'])) redirect('admin');
    
    // 1. Ambil data pendaftar
    $pendaftar = $this->M_Admin->get_pendaftar_by_id($id);
    if (!$pendaftar) show_404();

    // 2. DIAGNOSA FIX: Update properti status di objek PHP sebelum generate PDF
    // Agar view 'laporan/surat_balasan' membaca status yang benar
    $pendaftar->status = $status; 

    require_once FCPATH . 'vendor/autoload.php'; 

    $update_data = ['status' => $status];

    // --- CASE TERIMA ---
    if ($status === 'diterima') {
        $password_plain = '123456';
        $data_user = [
            'email' => $pendaftar->email,
            'password' => password_hash($password_plain, PASSWORD_DEFAULT),
            'role' => 'peserta',
            'nama_lengkap' => $pendaftar->nama
        ];
        
        // Generate Draft PDF (Sekarang $pendaftar->status sudah 'diterima')
        $filename = 'Draft_Terima_' . time() . '.pdf';
        $html = $this->load->view('laporan/surat_balasan', ['pendaftar' => $pendaftar], TRUE);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        file_put_contents(FCPATH . 'assets/uploads/surat_balasan/' . $filename, $dompdf->output());

        $update_data['file_draft_balasan'] = $filename;
        
        // Update DB & Create User
        $this->M_Admin->proses_verifikasi_diterima($id, $update_data, $data_user);
        $this->session->set_flashdata('success', 'Peserta diterima & Akun telah dibuat.');
    } 
    // --- CASE TOLAK ---
    else {
        // Generate Draft PDF (Sekarang $pendaftar->status sudah 'ditolak')
        $filename = 'Draft_Tolak_' . time() . '.pdf';
        $html = $this->load->view('laporan/surat_balasan', ['pendaftar' => $pendaftar], TRUE);
        
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();
        file_put_contents(FCPATH . 'assets/uploads/surat_balasan/' . $filename, $dompdf->output());

        $update_data['file_draft_balasan'] = $filename;
        
        // Update DB
        $this->db->update('pendaftar', $update_data, ['id' => $id]);
        $this->session->set_flashdata('success', 'Peserta ditolak.');
    }
    
    redirect('admin/berkas/'.$id);
}

public function upload_file_final() {
    $id = $this->input->post('id');
    $notif_method = $this->input->post('notif_method');
    $pendaftar = $this->M_Admin->get_pendaftar_by_id($id);

    $config['upload_path']   = './assets/uploads/surat_balasan/';
    $config['allowed_types'] = 'pdf';
    $config['file_name']     = 'Final_' . $id . '_' . time();

    $this->load->library('upload', $config);

    if ($this->upload->do_upload('file_upload')) {
        $file_name = $this->upload->data('file_name');
        $file_url  = base_url('assets/uploads/surat_balasan/' . $file_name);
        $file_path = FCPATH . 'assets/uploads/surat_balasan/' . $file_name;

        $this->db->update('pendaftar', ['file_surat_balasan' => $file_name], ['id' => $id]);

        // PESAN DINAMIS
        if ($pendaftar->status == 'diterima') {
            $msg_wa = "Halo *{$pendaftar->nama}*,\n\nSelamat! Anda *DINYATAKAN DITERIMA* magang di BPS Banten.\n\nLogin Ke Sistem:\nEmail: *{$pendaftar->email}*\nPassword: *123456*\n\nSurat resmi terlampir.";
            $msg_email = "<h3>Selamat, {$pendaftar->nama}!</h3><p>Anda diterima magang. Akun login:<br>Email: {$pendaftar->email}<br>Password: 123456</p>";
        } else {
            $msg_wa = "Halo *{$pendaftar->nama}*,\n\nMohon maaf, Anda *BELUM DAPAT DITERIMA* magang di BPS Banten periode ini.\n\nTetap semangat! Surat resmi terlampir.";
            $msg_email = "<h3>Halo, {$pendaftar->nama}</h3><p>Mohon maaf, pengajuan magang Anda <strong>BELUM DAPAT DITERIMA</strong>.</p>";
        }

        // TRIGGER NOTIFIKASI
        if ($notif_method == 'wa') {
            $this->wa_client->send_message($pendaftar->no_hp, $msg_wa, $file_url);
        } else {
            $this->load->library('email');
            $this->email->from('fatihmaulana8@gmail.com', 'BPS Banten');
            $this->email->to($pendaftar->email);
            $this->email->subject('Informasi Status Magang BPS Banten');
            $this->email->message($msg_email);
            $this->email->attach($file_path);
            $this->email->send();
        }
        $this->session->set_flashdata('success', 'File Berhasil di Upload & Notifikasi Terkirim!');
    }
    redirect('admin/berkas/'.$id);
}

public function cetak_sertifikat($pendaftar_id) {
    require_once FCPATH . 'vendor/autoload.php';
    $pendaftar = $this->M_Admin->get_pendaftar_by_id($pendaftar_id);
    if (!$pendaftar) show_404();

    $data['pendaftar'] = $pendaftar;
    $html = $this->load->view('laporan/pdf_sertifikat', $data, TRUE);

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape'); // Sertifikat biasanya landscape
    $dompdf->render();
    $dompdf->stream("Draft_Sertifikat_{$pendaftar->nama}.pdf", ["Attachment" => 0]);
}

// // --- Method Baru: Handler Upload dari Admin (Surat Balasan & Sertifikat) ---
// public function upload_file_final() {
//     $id = $this->input->post('id');
//     $tipe = $this->input->post('tipe'); // 'surat' atau 'sertifikat'

//     if (empty($_FILES['file_upload']['name'])) {
//         $this->session->set_flashdata('error', 'File belum dipilih.');
//         redirect($_SERVER['HTTP_REFERER']);
//     }

//     // Config Upload
//     $folder = ($tipe == 'surat') ? 'surat_balasan' : 'sertifikat';
//     $config['upload_path']   = './assets/uploads/' . $folder . '/';
//     $config['allowed_types'] = 'pdf';
//     $config['max_size']      = 2048;
//     $config['file_name']     = $tipe . '_final_' . $id . '_' . time();

//     if (!is_dir($config['upload_path'])) mkdir($config['upload_path'], 0777, true);

//     $this->load->library('upload', $config);

//     if ($this->upload->do_upload('file_upload')) {
//         $file_data = $this->upload->data();
//         $file_name = $file_data['file_name'];

//         if ($tipe == 'surat') {
//             $this->db->update('pendaftar', ['file_surat_balasan' => $file_name], ['id' => $id]);
//             $msg = "Surat Balasan E-Sign berhasil diupload.";
//         } else {
//             $this->db->update('pendaftar', ['file_sertifikat' => $file_name], ['id' => $id]);
//             $msg = "Sertifikat Final berhasil diupload.";
//         }

//         $this->session->set_flashdata('success', $msg);
//     } else {
//         $this->session->set_flashdata('error', $this->upload->display_errors());
//     }

//     redirect($_SERVER['HTTP_REFERER']);
// }

    public function set_selesai($id)
    {
        // REFACTOR: Menggunakan M_Admin
        $this->M_Admin->update_status_pendaftar($id, 'selesai');
        $this->session->set_flashdata('success', 'Status magang selesai');
        redirect('admin');
    }

    public function broadcast()
    {
        $data['title'] = 'Broadcast WhatsApp';
        $this->render_view('admin/broadcast', $data);
    }

    public function send_broadcast()
    {
        $no = $this->input->post('no_tujuan');
        $pesan = $this->input->post('pesan');

        if ($no && $pesan) {
            $result = $this->wa_client->send_message($no, $pesan);
            $this->session->set_flashdata(
                $result['status'] ? 'success' : 'error',
                $result['status'] ? 'Pesan terkirim' : $result['message']
            );
        }
        redirect('admin/broadcast');
    }

    public function rekap_absensi($user_id)
    {
        require_once FCPATH . 'vendor/autoload.php';

        // Memanggil fungsi yang baru saja ditambahkan di M_Auth
        $data['peserta'] = $this->M_Auth->get_user_by_id($user_id);
        if (!$data['peserta']) show_404();

        $data['detail'] = $this->M_Admin->get_pendaftar_by_user($user_id);
        $data['absensi'] = $this->M_Absensi->get_absensi_by_user($user_id);

        $dompdf = new \Dompdf\Dompdf();
        $html = $this->load->view('laporan/pdf_absensi', $data, TRUE);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Rekap_Absensi_{$data['peserta']->nama_lengkap}.pdf", ["Attachment" => 0]);
    }

	public function master_kampus() {
        $data['title'] = 'Master Data Kampus';
        // Refactor: Mengambil data dari model M_Master
        // Pastikan nama tabel atau fungsi di model sesuai (biasanya get_all_kampus atau get_all_institusi)
        $data['list'] = $this->M_Master->get_all_kampus(); 
        $this->render_view('admin/master_kampus', $data);
    }

public function master_kampus_add() {
    $data = [
        'nama_institusi' => $this->input->post('nama_institusi'),
        'kategori' => $this->input->post('kategori')
    ];
    
    if($data['nama_institusi'] && $data['kategori']) {
        // Refactor: Gunakan model M_Master
        $this->M_Master->insert_kampus($data);
        $this->session->set_flashdata('success', 'Data berhasil ditambahkan');
    }
    redirect('admin/master_kampus');
}

public function master_kampus_delete($id) {
    // Refactor: Gunakan model M_Master
    $this->M_Master->delete_kampus($id);
    $this->session->set_flashdata('success', 'Data berhasil dihapus');
    redirect('admin/master_kampus');
}
    
    // --- MASTER DATA JURUSAN ---
    public function master_jurusan() {
    $data['title'] = 'Master Data Jurusan';
    // Refactor: Gunakan model M_Master
    $data['list'] = $this->M_Master->get_all_jurusan();
    $this->render_view('admin/master_jurusan', $data);
}

public function master_jurusan_add() {
    $nama = $this->input->post('nama_jurusan');
    if($nama) {
        // Refactor: Gunakan model M_Master
        $this->M_Master->insert_jurusan(['nama_jurusan' => $nama]);
        $this->session->set_flashdata('success', 'Data berhasil ditambahkan');
    }
    redirect('admin/master_jurusan');
}

public function master_jurusan_delete($id) {
    // Refactor: Gunakan model M_Master
    $this->M_Master->delete_jurusan($id);
    $this->session->set_flashdata('success', 'Data berhasil dihapus');
    redirect('admin/master_jurusan');
}

	// TASK 4: Monitoring Harian
    public function monitoring_harian()
	{
		$data['title'] = 'Monitoring Absensi Hari Ini';
		$today = date('Y-m-d');
		
		// Refactor: Mengambil data via model
		$data['absensi'] = $this->M_Absensi->get_monitoring_harian($today);
			
		$this->render_view('admin/monitoring_harian', $data);
	}

    // TASK 2: Broadcast Excel (CSV Simple Parse)
    public function broadcast_excel()
    {
        if(empty($_FILES['file_excel']['name'])){
            $this->session->set_flashdata('error', 'File belum dipilih');
            redirect('admin/broadcast');
        }

        // Upload Config
        $config['upload_path'] = './assets/uploads/';
        $config['allowed_types'] = 'csv|xls|xlsx'; 
        $config['max_size'] = 2048;
        
        $this->load->library('upload', $config);
        
        if ($this->upload->do_upload('file_excel')) {
            $file_data = $this->upload->data();
            $file_path = './assets/uploads/' . $file_data['file_name'];
            
            // Parsing Sederhana CSV (Agar tidak perlu library berat jika belum ada)
            // Jika file XLSX, saran saya konversi ke CSV dulu atau gunakan PhpSpreadsheet
            // Disini saya contohkan CSV handling agar universal tanpa composer error
            $file = fopen($file_path, 'r');
            $success_count = 0;
            
            while (($row = fgetcsv($file, 1000, ",")) !== FALSE) {
                // Asumsi: Kolom 0 = No HP, Kolom 1 = Nama, Kolom 2 = Pesan
                $no_hp = $row[0]; 
                $pesan = isset($row[2]) ? $row[2] : $this->input->post('pesan_default');
                
                // Skip header jika ada (validasi numerik simpel)
                if(!is_numeric(str_replace(['+','-',' '], '', $no_hp))) continue;

                $this->wa_client->send_message($no_hp, $pesan);
                $success_count++;
            }
            fclose($file);
            unlink($file_path); // Hapus file

            $this->session->set_flashdata('success', "Broadcast terkirim ke $success_count nomor.");
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        }
        redirect('admin/broadcast');
    }

	public function data_peserta()
    {
        $data['title'] = 'Data Semua Peserta';
        // REFACTOR: Gunakan M_Admin
        $data['list'] = $this->M_Admin->get_all_peserta_detailed();
        $this->render_view('admin/data_peserta', $data);
    }

    // --- TASK 2: MASTER FAKULTAS ---
    public function master_fakultas() {
			$data['title'] = 'Master Data Fakultas';
			// Refactor: Gunakan model M_Master
			$data['list'] = $this->M_Master->get_all_fakultas();
			$this->render_view('admin/master_fakultas', $data);
		}

		public function master_fakultas_add() {
			$nama = $this->input->post('nama_fakultas');
			if($nama) {
				// Refactor: Gunakan model M_Master
				$this->M_Master->insert_fakultas(['nama_fakultas' => $nama]);
				$this->session->set_flashdata('success', 'Fakultas berhasil ditambahkan');
			}
			redirect('admin/master_fakultas');
		}

		public function master_fakultas_delete($id) {
			// Refactor: Gunakan model M_Master
			$this->M_Master->delete_fakultas($id);
			$this->session->set_flashdata('success', 'Fakultas berhasil dihapus');
			redirect('admin/master_fakultas');
		}

	public function reset_password($user_id)
    {
        if (empty($user_id)) show_404();
        $new_password = password_hash('123456', PASSWORD_DEFAULT);
        // REFACTOR: Gunakan M_Admin
        $this->M_Admin->update_password($user_id, $new_password);
        $this->session->set_flashdata('success', 'Password berhasil direset menjadi: 123456');
        redirect($_SERVER['HTTP_REFERER']);
    }

	public function monitoring_absensi()
	{
		$tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
		$filter_status = $this->input->get('status');

		// Refactor: Mengambil semua peserta diterima dan status absensi via model
		$query_results = $this->M_Absensi->get_monitoring_filter($tanggal);

		foreach ($query_results as $row) {
			// Logika penentuan status tampilan (Logic tetap, hanya sumber data berubah)
			if (!$row->absensi_status) {
				$row->display_status = 'Belum Absen';
				$row->label_class = 'badge-secondary';
			} elseif ($row->absensi_status == 'izin') {
				$row->display_status = 'Izin (' . strtoupper($row->jenis_izin) . ')';
				$row->label_class = 'badge-warning';
			} elseif ($row->absensi_status == 'telat' || ($row->jam_datang > '08:00:00' && $row->absensi_status == 'hadir')) {
				$row->display_status = 'Terlambat';
				$row->label_class = 'badge-danger';
			} elseif ($row->absensi_status == 'hadir') {
				$row->display_status = 'Hadir';
				$row->label_class = 'badge-success';
			} else {
				$row->display_status = ucfirst($row->absensi_status);
				$row->label_class = 'badge-info';
			}
		}

		// Filter status berdasarkan display_status
		if ($filter_status) {
			$query_results = array_filter($query_results, function($item) use ($filter_status) {
				if ($filter_status == 'masuk') return ($item->absensi_status == 'hadir' || $item->absensi_status == 'telat');
				if ($filter_status == 'izin') return ($item->absensi_status == 'izin');
				if ($filter_status == 'belum') return (empty($item->absensi_status));
				return true;
			});
		}

		$data['absensi'] = $query_results;
		$data['tanggal'] = $tanggal;
		$data['filter_status'] = $filter_status;
		$data['title'] = "Monitoring Absensi Harian";

		$this->render_view('admin/monitoring_absensi', $data);
	}

public function save_absen_manual()
{
    $user_id = $this->input->post('user_id');
    $tanggal = $this->input->post('tanggal');
    $jam_datang = $this->input->post('jam_datang');
    $jam_pulang = $this->input->post('jam_pulang');
    $status = $this->input->post('status');

    // Refactor: Cek via model
    $existing = $this->M_Absensi->check_existing_absen($user_id, $tanggal);

    $data = [
        'user_id'    => $user_id,
        'tanggal'    => $tanggal,
        'jam_datang' => $jam_datang ?: NULL,
        'jam_pulang' => $jam_pulang ?: NULL,
        'status'     => $status,
        'keterangan' => 'Diinput manual oleh Admin'
    ];

    // Simpan via model
    $existing_id = $existing ? $existing->id : NULL;
    $this->M_Absensi->save_absen_manual($data, $existing_id);

    $this->session->set_flashdata('success', 'Berhasil memperbarui absensi manual.');
    redirect('admin/monitoring_absensi?tanggal=' . $tanggal);
}

	public function master_admin() {
			$data['title'] = 'Manajemen Akun Admin';
			// REFACTOR: Gunakan M_Admin
			$data['admins'] = $this->M_Admin->get_admins();
			$this->render_view('admin/master_admin', $data);
		}

public function admin_add() {
        $data = [
            'nama_lengkap' => $this->input->post('nama'),
            'email' => $this->input->post('email'),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role' => 'admin'
        ];
        // REFACTOR: Gunakan M_Admin
        $this->M_Admin->insert_admin($data);
        $this->session->set_flashdata('success', 'Admin baru berhasil ditambahkan');
        redirect('admin/master_admin');
    }

public function admin_delete($id) {
        if ($id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Tidak bisa menghapus akun sendiri!');
        } else {
            // REFACTOR: Gunakan M_Admin
            $this->M_Admin->delete_admin($id);
            $this->session->set_flashdata('success', 'Akun admin telah dihapus');
        }
        redirect('admin/master_admin');
    }
}
