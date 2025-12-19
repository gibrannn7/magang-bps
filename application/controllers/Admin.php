<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'admin') {
            redirect('auth/login');
        }
    }

    private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index()
	{
		$data['title'] = 'Dashboard Statistik';
		$today = date('Y-m-d');

		// --- ROW 1: STATUS PENDAFTARAN ---
		$data['total_daftar'] = $this->db->count_all('pendaftar');
		$data['pending'] = $this->db->where('status', 'pending')->count_all_results('pendaftar');
		$data['aktif'] = $this->db->where('status', 'diterima')->count_all_results('pendaftar');
		$data['selesai'] = $this->db->where('status', 'selesai')->count_all_results('pendaftar'); // Data Alumni

		// --- ROW 2: REKAP ABSEN HARI INI ---
		// Tepat Waktu
		$data['hadir'] = $this->db->where(['tanggal' => $today, 'status' => 'hadir'])->count_all_results('absensi');
		// Terlambat
		$data['telat'] = $this->db->where(['tanggal' => $today, 'status' => 'telat'])->count_all_results('absensi');
		// Izin/Sakit
		$data['absen_izin'] = $this->db->where(['tanggal' => $today, 'status' => 'izin'])->count_all_results('absensi');
		// Belum Absen (Total Aktif - Semua yang sudah input data hari ini)
		$data['belum_absen'] = max(0, $data['aktif'] - ($data['hadir'] + $data['telat'] + $data['absen_izin']));

		$data['pendaftar'] = $this->db->select('pendaftar.*, users.email as akun_user') // Pastikan 'pendaftar.*' dan tidak ada '*' di akhir alias
			->join('users', 'users.id = pendaftar.user_id', 'left')
			->order_by('pendaftar.id', 'DESC')
			->get('pendaftar')
			->result();

		$this->render_view('admin/dashboard_lte', $data);
	}

    public function berkas($id)
    {
        $data['pendaftar'] = $this->db->get_where('pendaftar', ['id' => $id])->row();
        if (!$data['pendaftar']) show_404();

        $data['dokumen'] = $this->db->get_where('dokumen', ['pendaftar_id' => $id])->result();
        $data['akun'] = $this->db->get_where('users', ['id' => $data['pendaftar']->user_id])->row();

        $data['title'] = 'Detail Berkas Peserta';
        $this->render_view('admin/detail_berkas', $data);
    }

    public function verifikasi($id, $status, $notif_type = 'wa')
{
    if (!in_array($status, ['diterima', 'ditolak'])) redirect('admin');

    $pendaftar = $this->db->get_where('pendaftar', ['id' => $id])->row();
    if (!$pendaftar) show_404();

    require_once FCPATH . 'vendor/autoload.php'; 

    $this->db->trans_start();

    $update_data = ['status' => $status];
    $pesan_teks = '';
    $password_plain = '123456'; // Password default
    
    if ($status === 'diterima') {
        // 1. Generate Surat Balasan PDF
        $filename_surat = 'Surat_Balasan_' . str_replace(' ', '_', $pendaftar->nama) . '_' . date('YmdHis') . '.pdf';
        $save_path = FCPATH . 'assets/uploads/surat_balasan/';
        if (!is_dir($save_path)) mkdir($save_path, 0777, true);

        $html = $this->load->view('laporan/surat_balasan', ['pendaftar' => $pendaftar], TRUE);
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($save_path . $filename_surat, $dompdf->output());

        $update_data['file_surat_balasan'] = $filename_surat;

        // 2. Buat User Login jika belum ada
        if ($pendaftar->user_id === NULL) {
            $this->db->insert('users', [
                'email'    => $pendaftar->email,
                'password' => password_hash($password_plain, PASSWORD_DEFAULT),
                'role' => 'peserta',
                'nama_lengkap' => $pendaftar->nama
            ]);
            $update_data['user_id'] = $this->db->insert_id();
        }
        
        $pesan_teks = "Selamat! Anda DINYATAKAN DITERIMA magang di BPS Banten.\n\nLogin Akun:\nEmail: {$pendaftar->email}\nPassword: {$password_plain}";
    } 

    $this->db->update('pendaftar', $update_data, ['id' => $id]);
    $this->db->trans_complete();

    if ($this->db->trans_status() === TRUE) {
        // --- LOGIKA NOTIFIKASI ---
        if ($notif_type === 'wa') {
            // Kirim via WhatsApp
            $this->wa_client->send_message($pendaftar->no_hp, "Halo *{$pendaftar->nama}*,\n\n" . $pesan_teks);
            $this->session->set_flashdata('success', 'Status Diterima & Notifikasi WA Terkirim.');
        } 
        else if ($notif_type === 'email') {
            // Kirim via Brevo Email menggunakan Library Native CI3
            $this->load->library('email');
            
            $this->email->from('fatihmaulana8@gmail.com', 'Admin Magang BPS Banten');
            $this->email->to($pendaftar->email); // Mengambil kolom email dari DB
            $this->email->subject('Pemberitahuan Status Magang BPS Banten');
            
            // Format HTML untuk Email
            $email_content = "
                <h3>Halo, {$pendaftar->nama}</h3>
                <p>Selamat, pengajuan magang Anda telah <strong>DITERIMA</strong>.</p>
                <p>Berikut adalah detail akun login Anda:</p>
                <ul>
                    <li><b>Email:</b> {$pendaftar->email}</li>
                    <li><b>Password:</b> {$password_plain}</li>
                </ul>
                <p>Silakan login ke dashboard untuk mengunduh Surat Balasan resmi.</p>
                <br>
                <p>Terima Kasih,<br>BPS Provinsi Banten</p>
            ";
            
            $this->email->message($email_content);
            
            if($this->email->send()) {
                $this->session->set_flashdata('success', 'Status Diterima & Email Brevo Terkirim.');
            } else {
                $this->session->set_flashdata('error', 'Data diperbarui, tapi Email gagal kirim: ' . $this->email->print_debugger());
            }
        }
    } else {
        $this->session->set_flashdata('error', 'Gagal memproses data.');
    }

    redirect($_SERVER['HTTP_REFERER'] ?? 'admin');
}

    public function set_selesai($id)
    {
        $this->db->update('pendaftar', ['status' => 'selesai'], ['id' => $id]);
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

        $data['peserta'] = $this->db->get_where('users', ['id' => $user_id])->row();
        if (!$data['peserta']) show_404();

        $data['detail'] = $this->db->get_where('pendaftar', ['user_id' => $user_id])->row();
        $data['absensi'] = $this->db->order_by('tanggal', 'ASC')
            ->get_where('absensi', ['user_id' => $user_id])->result();

        $dompdf = new \Dompdf\Dompdf();
        $html = $this->load->view('laporan/pdf_absensi', $data, TRUE);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Rekap_Absensi_{$data['peserta']->nama_lengkap}.pdf", ["Attachment" => 0]);
    }

	public function master_kampus() {
        $data['title'] = 'Master Data Kampus/Sekolah';
        // Ambil data real dari DB (Pastikan tabel master_institusi sudah dibuat sesuai planning sebelumnya)
        $data['list'] = $this->db->get('master_institusi')->result();
        $this->render_view('admin/master_kampus', $data);
    }

	public function master_kampus_add() {
        $nama = $this->input->post('nama_institusi');
        $kategori = $this->input->post('kategori');
        
        if($nama && $kategori) {
            $this->db->insert('master_institusi', ['nama_institusi' => $nama, 'kategori' => $kategori]);
            $this->session->set_flashdata('success', 'Data berhasil ditambahkan');
        }
        redirect('admin/master_kampus');
    }

    public function master_kampus_delete($id) {
        $this->db->delete('master_institusi', ['id' => $id]);
        $this->session->set_flashdata('success', 'Data berhasil dihapus');
        redirect('admin/master_kampus');
    }
    
    // --- MASTER DATA JURUSAN ---
    public function master_jurusan() {
        $data['title'] = 'Master Data Jurusan';
        $data['list'] = $this->db->get('master_jurusan')->result();
        $this->render_view('admin/master_jurusan', $data);
    }

    public function master_jurusan_add() {
        $nama = $this->input->post('nama_jurusan');
        if($nama) {
            $this->db->insert('master_jurusan', ['nama_jurusan' => $nama]);
            $this->session->set_flashdata('success', 'Data berhasil ditambahkan');
        }
        redirect('admin/master_jurusan');
    }

    public function master_jurusan_delete($id) {
        $this->db->delete('master_jurusan', ['id' => $id]);
        $this->session->set_flashdata('success', 'Data berhasil dihapus');
        redirect('admin/master_jurusan');
    }

	// TASK 4: Monitoring Harian
    public function monitoring_harian()
    {
        $data['title'] = 'Monitoring Absensi Hari Ini';
        $today = date('Y-m-d');
        
        // Join tabel absensi dengan user dan pendaftar
        $data['absensi'] = $this->db->select('absensi.*, users.nama_lengkap, pendaftar.institusi')
            ->from('absensi')
            ->join('users', 'users.id = absensi.user_id')
            ->join('pendaftar', 'pendaftar.user_id = users.id')
            ->where('absensi.tanggal', $today)
            ->get()
            ->result();
            
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

	// --- TASK 1: DATA SEMUA PESERTA (Monitoring Full) ---
    public function data_peserta()
    {
        $data['title'] = 'Data Semua Peserta';
        
        // Mengambil semua data tanpa filter status (kecuali user melakukan filter nanti di view)
        $data['list'] = $this->db->select('pendaftar.*, users.email')
            ->join('users', 'users.id = pendaftar.user_id', 'left')
            ->order_by('pendaftar.id', 'DESC')
            ->get('pendaftar')
            ->result();

        $this->render_view('admin/data_peserta', $data);
    }

    // --- TASK 2: MASTER FAKULTAS ---
    public function master_fakultas() {
        $data['title'] = 'Master Data Fakultas';
        $data['list'] = $this->db->get('master_fakultas')->result();
        $this->render_view('admin/master_fakultas', $data);
    }

    public function master_fakultas_add() {
        $nama = $this->input->post('nama_fakultas');
        if($nama) {
            $this->db->insert('master_fakultas', ['nama_fakultas' => $nama]);
            $this->session->set_flashdata('success', 'Fakultas berhasil ditambahkan');
        }
        redirect('admin/master_fakultas');
    }

    public function master_fakultas_delete($id) {
        $this->db->delete('master_fakultas', ['id' => $id]);
        $this->session->set_flashdata('success', 'Fakultas berhasil dihapus');
        redirect('admin/master_fakultas');
    }

	public function reset_password($user_id)
    {
        if (empty($user_id)) show_404();

        // Default password: 123456
        $new_password = password_hash('123456', PASSWORD_DEFAULT);

        $this->db->update('users', ['password' => $new_password], ['id' => $user_id]);

        $this->session->set_flashdata('success', 'Password berhasil direset menjadi: 123456');
        
        // Redirect kembali ke halaman asal (misal data peserta)
        redirect($_SERVER['HTTP_REFERER']);
    }
public function monitoring_absensi()
{
    $tanggal = $this->input->get('tanggal') ?: date('Y-m-d');
    $filter_status = $this->input->get('status');

    // Mengambil semua peserta dengan status 'diterima' dan join ke tabel absensi pada tanggal tersebut
    $this->db->select('u.id as user_id, p.nama, p.institusi, a.id as absensi_id, a.jam_datang, a.jam_pulang, a.status as absensi_status, a.bukti_izin, a.keterangan, a.jenis_izin');
    $this->db->from('users u');
    $this->db->join('pendaftar p', 'u.id = p.user_id'); 
    $this->db->join('absensi a', "u.id = a.user_id AND a.tanggal = '$tanggal'", 'left');
    $this->db->where('u.role', 'peserta');
    $this->db->where('p.status', 'diterima'); 

    $query_results = $this->db->get()->result();

    foreach ($query_results as $row) {
        // Logika penentuan status tampilan dan class badge
        if (!$row->absensi_status) {
            $row->display_status = 'Belum Absen';
            $row->label_class = 'badge-secondary';
        } elseif ($row->absensi_status == 'izin') {
            $row->display_status = 'Izin (' . strtoupper($row->jenis_izin) . ')';
            $row->label_class = 'badge-warning';
        } elseif ($row->absensi_status == 'telat' || ($row->jam_datang > '08:00:00' && $row->absensi_status == 'hadir')) {
            // Jika status di DB 'telat' ATAU jam datang > 08:00 meskipun status 'hadir'
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

    // Cek apakah sudah ada record di tabel absensi untuk user dan tanggal ini
    $existing = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $tanggal])->row();

    $data = [
        'user_id'    => $user_id,
        'tanggal'    => $tanggal,
        'jam_datang' => $jam_datang ?: NULL,
        'jam_pulang' => $jam_pulang ?: NULL,
        'status'     => $status,
        'keterangan' => 'Diinput manual oleh Admin'
    ];

    if ($existing) {
        $this->db->where('id', $existing->id);
        $this->db->update('absensi', $data);
    } else {
        $this->db->insert('absensi', $data);
    }

    $this->session->set_flashdata('success', 'Berhasil memperbarui absensi manual.');
    redirect('admin/monitoring_absensi?tanggal=' . $tanggal);
}
public function master_admin() {
    $data['title'] = 'Manajemen Akun Admin';
    $data['admins'] = $this->db->get_where('users', ['role' => 'admin'])->result();
    $this->render_view('admin/master_admin', $data);
}

public function admin_add() {
    $data = [
        'nama_lengkap' => $this->input->post('nama'),
        'email' => $this->input->post('email'),
        'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
        'role' => 'admin'
    ];
    $this->db->insert('users', $data);
    $this->session->set_flashdata('success', 'Admin baru berhasil ditambahkan');
    redirect('admin/master_admin');
}

public function admin_delete($id) {
    // Proteksi agar admin tidak menghapus dirinya sendiri
    if ($id == $this->session->userdata('user_id')) {
        $this->session->set_flashdata('error', 'Tidak bisa menghapus akun sendiri!');
    } else {
        $this->db->delete('users', ['id' => $id]);
        $this->session->set_flashdata('success', 'Akun admin telah dihapus');
    }
    redirect('admin/master_admin');
}
}
