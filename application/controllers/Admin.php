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

        $data['total_daftar'] = $this->db->count_all('pendaftar');
        $data['pending'] = $this->db->where('status', 'pending')->count_all_results('pendaftar');
        $data['aktif'] = $this->db->where('status', 'diterima')->count_all_results('pendaftar');

        $data['pendaftar'] = $this->db->select('pendaftar.*, users.username as akun_user')
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

    public function verifikasi($id, $status, $kirim_wa = 1)
{
    if (!in_array($status, ['diterima', 'ditolak'])) redirect('admin');

    $pendaftar = $this->db->get_where('pendaftar', ['id' => $id])->row();
    if (!$pendaftar) show_404();

    require_once FCPATH . 'vendor/autoload.php'; // Load Composer Autoload (untuk DomPDF)

    $this->db->trans_start();

    // Data update default
    $update_data = ['status' => $status];
    $pesan_wa = '';
    
    // --- LOGIKA JIKA DITERIMA: GENERATE PDF & BUAT USER ---
    if ($status === 'diterima') {
        
        // 1. Generate Surat Balasan PDF
        $filename_surat = 'Surat_Balasan_' . str_replace(' ', '_', $pendaftar->nama) . '_' . date('YmdHis') . '.pdf';
        $save_path = FCPATH . 'assets/uploads/surat_balasan/';
        
        // Buat folder jika belum ada
        if (!is_dir($save_path)) {
            mkdir($save_path, 0777, true);
        }

        // Load View ke String HTML
        $html = $this->load->view('laporan/surat_balasan', ['pendaftar' => $pendaftar], TRUE);

        // Render PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Simpan ke File
        file_put_contents($save_path . $filename_surat, $dompdf->output());

        // Masukkan nama file ke array update
        $update_data['file_surat_balasan'] = $filename_surat;

        // 2. Buat User Login (Seperti sebelumnya)
        if ($pendaftar->user_id === NULL) {
            $password_plain = '123456';
            $this->db->insert('users', [
                'username' => 'MAGANG' . rand(1000, 9999),
                'email'    => $pendaftar->email,
                'password' => password_hash($password_plain, PASSWORD_DEFAULT),
                'role' => 'peserta',
                'nama_lengkap' => $pendaftar->nama
            ]);
            $update_data['user_id'] = $this->db->insert_id();

            // Template WA Diterima
            $pesan_wa = "Halo *{$pendaftar->nama}*,\n\n".
                "Selamat! Anda DINYATAKAN DITERIMA magang di BPS Banten.\n".
                "Surat Balasan resmi telah diterbitkan dan dapat diunduh di dashboard.\n\n".
                "Login Akun:\nEmail: {$pendaftar->email}\nPass: {$password_plain}\n\n".
                "Terima kasih.";
        }
    } 
    // --- LOGIKA JIKA DITOLAK ---
    elseif ($status === 'ditolak') {
        $pesan_wa = "Halo *{$pendaftar->nama}*,\n\nMohon maaf, pengajuan magang Anda belum dapat kami terima saat ini.\nTetap semangat!";
    }

    // 3. Eksekusi Update Database
    $this->db->update('pendaftar', $update_data, ['id' => $id]);

    $this->db->trans_complete();

    // 4. Kirim WA
    if ($this->db->trans_status() === TRUE) {
        if ($kirim_wa == 1 && !empty($pesan_wa)) {
            $this->wa_client->send_message($pendaftar->no_hp, $pesan_wa);
            $this->session->set_flashdata('success', 'Status Diterima, Surat Terbit & WA Terkirim.');
        } else {
            $this->session->set_flashdata('success', 'Status diperbarui (Tanpa WA).');
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
        $data['list'] = $this->db->select('pendaftar.*, users.username')
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

    // Query Mengambil Peserta Aktif dan Join Absensi
    $this->db->select('u.id as user_id, p.nama, p.institusi, a.jam_datang, a.jam_pulang, a.status as absensi_status, a.bukti_izin, a.keterangan, a.jenis_izin');
    $this->db->from('users u');
    $this->db->join('pendaftar p', 'u.id = p.user_id'); 
    $this->db->join('absensi a', "u.id = a.user_id AND a.tanggal = '$tanggal'", 'left');
    $this->db->where('u.role', 'peserta');
    $this->db->where('p.status', 'diterima'); 

    $query_results = $this->db->get()->result();

    foreach ($query_results as $row) {
        if (!$row->absensi_status) {
            $row->display_status = 'Belum Absen';
            $row->label_class = 'badge-secondary';
        } elseif ($row->absensi_status == 'izin') {
            $row->display_status = 'Izin (' . strtoupper($row->jenis_izin) . ')';
            $row->label_class = 'badge-warning';
        } else {
            $row->display_status = 'Masuk';
            $row->label_class = 'badge-success';
        }
    }

    if ($filter_status) {
        $query_results = array_filter($query_results, function($item) use ($filter_status) {
            if ($filter_status == 'masuk') return ($item->display_status == 'Masuk');
            if ($filter_status == 'izin') return (strpos($item->display_status, 'Izin') !== false);
            if ($filter_status == 'belum') return ($item->display_status == 'Belum Absen');
            return true;
        });
    }

    $data['absensi'] = $query_results;
    $data['tanggal'] = $tanggal;
    $data['filter_status'] = $filter_status;
    $data['title'] = "Monitoring Absensi Harian";

    // JANGAN memanggil header, navbar, footer secara manual di sini
    // Gunakan fungsi render_view yang sudah ada di baris 12 Admin.php
    $this->render_view('admin/monitoring_absensi', $data);
}
}
