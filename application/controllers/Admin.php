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

    public function verifikasi($id, $status)
    {
        if (!in_array($status, ['diterima', 'ditolak'])) redirect('admin');

        $pendaftar = $this->db->get_where('pendaftar', ['id' => $id])->row();
        if (!$pendaftar) show_404();

        $this->db->trans_start();

        $this->db->update('pendaftar', ['status' => $status], ['id' => $id]);

        $pesan_wa = '';

        if ($status === 'diterima' && $pendaftar->user_id === NULL) {

            $username = 'MAGANG' . rand(1000, 9999);
            $password_plain = '123456';

            $this->db->insert('users', [
                'username' => $username,
                'password' => password_hash($password_plain, PASSWORD_DEFAULT),
                'role' => 'peserta',
                'nama_lengkap' => $pendaftar->nama
            ]);

            $user_id = $this->db->insert_id();

            $this->db->update('pendaftar', ['user_id' => $user_id], ['id' => $id]);

            $pesan_wa =
                "Halo *{$pendaftar->nama}*,\n\n".
                "Selamat! Anda DINYATAKAN DITERIMA sebagai peserta magang di BPS Banten.\n\n".
                "Periode: {$pendaftar->tgl_mulai} s/d {$pendaftar->tgl_selesai}\n\n".
                "Login:\nUsername: {$username}\nPassword: {$password_plain}\n\n".
                "Alamat: KP3B Serang, Banten.";

        } elseif ($status === 'ditolak') {

            $pesan_wa =
                "Halo *{$pendaftar->nama}*,\n\n".
                "Terima kasih telah mendaftar magang di BPS Banten.\n".
                "Mohon maaf, Anda BELUM DAPAT kami terima.\n\n".
                "Tetap semangat dan sukses selalu.";
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === TRUE && $pesan_wa) {
            $this->wa_client->send_message($pendaftar->no_hp, $pesan_wa);
            $this->session->set_flashdata('success', 'Status diperbarui & WA terkirim');
        } else {
            $this->session->set_flashdata('error', 'Gagal memproses data');
        }

        redirect('admin');
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
}
