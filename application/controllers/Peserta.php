<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peserta extends CI_Controller {

    // Koordinat Kantor BPS Banten (Titik Pusat)
    // Sesuai request: -6.171144960493601, 106.1609483232592
    const LAT_KANTOR = -6.171144960493601;
    const LONG_KANTOR = 106.1609483232592;
    const MAX_RADIUS_METER = 100; // Radius toleransi

    public function __construct() {
        parent::__construct();
        // Cek Login & Role
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'peserta') {
            redirect('auth/login');
        }
        date_default_timezone_set('Asia/Jakarta'); // Wajib WIB
    }

	private function render_view($view, $data = []) {
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layout/admin_template', $data);
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');
        $today = date('Y-m-d');

        // Data Absensi
        $data['absensi'] = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row();
        $data['riwayat'] = $this->db->order_by('tanggal', 'DESC')->limit(5)->get_where('absensi', ['user_id' => $user_id])->result();
        
        // Cek Status Magang
        $data['pendaftar'] = $this->db->get_where('pendaftar', ['user_id' => $user_id])->row();

        $data['title'] = 'Dashboard Peserta';
        $this->render_view('peserta/dashboard', $data);
    }

    // Halaman Absen (Form Kamera & Map)
    public function absen_area()
    {
        $data['title'] = 'Lakukan Absensi';
        $this->render_view('peserta/absen_form', $data);
    }

    // PROSES INTI: POST ABSEN
    public function submit_absen()
    {
        // 1. Cek Hari (Sabtu=6, Minggu=7 Libur)
        $hari_ini = date('N'); 
        if ($hari_ini >= 6) { 
            echo json_encode(['status' => false, 'message' => 'Hari Libur: Absensi tidak dapat dilakukan.']);
            return;
        }

        // 2. Cek Jam (Validasi Waktu Server)
        $now = date('H:i:s');
        // Contoh: Absen buka jam 06:00 pagi
        if ($now < '06:00:00') {
            echo json_encode(['status' => false, 'message' => 'Absensi belum dibuka. Dimulai pukul 06:00 WIB.']);
            return;
        }

        // Ambil Input
        $is_izin = $this->input->post('is_izin'); // String 'true'/'false'
        $user_id = $this->session->userdata('user_id');
        $today = date('Y-m-d');

        // --- LOGIC IZIN / SAKIT ---
        if ($is_izin === 'true') {
            $jenis_izin = $this->input->post('jenis_izin');
            $keterangan = $this->input->post('keterangan');

            // Validasi Input Kosong
            if (empty($jenis_izin) || empty($keterangan)) {
                echo json_encode(['status' => false, 'message' => 'Jenis izin dan keterangan wajib diisi!']);
                return;
            }

            $cek = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row();
            if($cek) {
                echo json_encode(['status' => false, 'message' => 'Anda sudah input absen/izin hari ini.']);
                return;
            }

            $this->db->insert('absensi', [
                'user_id' => $user_id,
                'tanggal' => $today,
                'jam_datang' => $now, // Tetap catat jam input
                'status' => 'izin', // status di db jadi 'izin'
                'keterangan' => "[$jenis_izin] $keterangan"
            ]);

            echo json_encode(['status' => true, 'message' => 'Pengajuan Izin Berhasil Disimpan.']);
            return;
        }

        // --- LOGIC ABSEN HADIR (FOTO & LOKASI) ---
        $lat_user = $this->input->post('latitude');
        $long_user = $this->input->post('longitude');
        $tipe = $this->input->post('tipe');
        $foto_base64 = $this->input->post('foto');

        // Validasi Data Lokasi & Foto
        if (empty($lat_user) || empty($long_user) || empty($foto_base64)) {
            echo json_encode(['status' => false, 'message' => 'Data Lokasi atau Foto tidak dikirim sempurna. Refresh halaman.']);
            return;
        }

        // Validasi Jarak
        $jarak_meter = $this->haversineGreatCircleDistance(self::LAT_KANTOR, self::LONG_KANTOR, $lat_user, $long_user);
        if ($jarak_meter > self::MAX_RADIUS_METER) {
            echo json_encode(['status' => false, 'message' => 'Jarak terlalu jauh (' . round($jarak_meter) . 'm). Wajib di area kantor.']);
            return;
        }

        $cek = $this->db->get_where('absensi', ['user_id' => $user_id, 'tanggal' => $today])->row();

        if ($tipe === 'datang') {
            if ($cek) {
                echo json_encode(['status' => false, 'message' => 'Sudah absen datang hari ini.']);
                return;
            }

            $foto = $this->_save_base64_image($foto_base64, 'datang');
            $status = ($now > '08:00:00') ? 'telat' : 'hadir';

            $this->db->insert('absensi', [
                'user_id' => $user_id,
                'tanggal' => $today,
                'jam_datang' => $now,
                'lat_datang' => $lat_user,
                'long_datang' => $long_user,
                'foto_datang' => $foto,
                'status' => $status
            ]);

        } elseif ($tipe === 'pulang') {
            if (!$cek) {
                echo json_encode(['status' => false, 'message' => 'Anda belum absen datang.']);
                return;
            }
            if ($cek->jam_pulang != NULL) {
                echo json_encode(['status' => false, 'message' => 'Sudah absen pulang.']);
                return;
            }

            // Validasi Jam Pulang
            $jam_pulang_min = ($hari_ini == 5) ? '16:30:00' : '16:00:00'; // Jumat beda jam
            if ($now < $jam_pulang_min) {
                echo json_encode(['status' => false, 'message' => 'Belum jam pulang (' . $jam_pulang_min . ').']);
                return;
            }

            $foto = $this->_save_base64_image($foto_base64, 'pulang');
            $this->db->update('absensi', [
                'jam_pulang' => $now,
                'lat_pulang' => $lat_user,
                'long_pulang' => $long_user,
                'foto_pulang' => $foto
            ], ['id' => $cek->id]);
        }

        echo json_encode(['status' => true, 'message' => 'Absensi Berhasil! Jarak: ' . round($jarak_meter) . 'm']);
    }


    // --- HELPER FUNCTION: Haversine Formula ---
    private function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius; // Return distance in meters
    }

    private function _save_base64_image($base64_string, $prefix) {
        // Hapus header base64 (data:image/png;base64,)
        $image_parts = explode(";base64,", $base64_string);
        $image_base64 = base64_decode($image_parts[1]);
        
        $filename = $prefix . '_' . uniqid() . '.png';
        $path = './assets/uploads/absensi/' . $filename;
        
        // Pastikan folder ada
        if (!is_dir('./assets/uploads/absensi')) {
            mkdir('./assets/uploads/absensi', 0777, true);
        }

        file_put_contents($path, $image_base64);
        return $filename;
    }

	public function download_sertifikat()
    {
        require_once FCPATH . 'vendor/autoload.php';

        $user_id = $this->session->userdata('user_id');
        $pendaftar = $this->db->get_where('pendaftar', ['user_id' => $user_id])->row();

        // Validasi Status
        if (!$pendaftar || $pendaftar->status !== 'selesai') {
            $this->session->set_flashdata('error', 'Program magang belum selesai!');
            redirect('peserta');
        }

        // Format Tanggal Indo
        $bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $tm = strtotime($pendaftar->tgl_mulai);
        $ts = strtotime($pendaftar->tgl_selesai);
        
        $tgl_mulai = date('j', $tm) . ' ' . $bulan[(int)date('n', $tm)] . ' ' . date('Y', $tm);
        $tgl_selesai = date('j', $ts) . ' ' . $bulan[(int)date('n', $ts)] . ' ' . date('Y', $ts);
        
        $data = [
            'nama' => strtoupper($pendaftar->nama),
            'periode' => $tgl_mulai . ' - ' . $tgl_selesai,
            'tanggal_sertifikat' => 'Serang, ' . date('j') . ' ' . $bulan[(int)date('n')] . ' ' . date('Y'),
            // Pastikan file gambar background ada di assets/templates/sertifikat.jpg
            'background_path' => base_url('assets/templates/sertifikat.jpg') 
        ];

        // Load View HTML
        // Pastikan Anda sudah membuat file view di: application/views/laporan/pdf_sertifikat.php
        $html = $this->load->view('laporan/pdf_sertifikat', $data, TRUE);

        // --- BAGIAN FIX ERROR TYPE ERROR ---
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true); // Agar bisa load gambar via URL/Path
        $options->set('defaultFont', 'Times-Roman');

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        $dompdf->stream("Sertifikat_Magang_" . str_replace(' ', '_', $pendaftar->nama) . ".pdf", ["Attachment" => 1]);
    }
}
