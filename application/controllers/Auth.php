<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect($this->session->userdata('role') == 'admin' ? 'admin' : 'peserta');
        }
        $this->load->view('auth/login');
    }

    public function process_login()
    {
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password');

        $user = $this->db->get_where('users', ['username' => $username])->row();

        if ($user) {
            // Verifikasi Hash Password
            if (password_verify($password, $user->password)) {
                $sess_data = [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role,
                    'nama_lengkap' => $user->nama_lengkap,
                    'logged_in' => TRUE
                ];
                $this->session->set_userdata($sess_data);

                // Update Last Login
                $this->db->update('users', ['last_login' => date('Y-m-d H:i:s')], ['id' => $user->id]);

                redirect($user->role == 'admin' ? 'admin' : 'peserta');
            } else {
                $this->session->set_flashdata('error', 'Password salah!');
                redirect('auth/login');
            }
        } else {
            $this->session->set_flashdata('error', 'Username tidak ditemukan!');
            redirect('auth/login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

	public function fix_password()
    {
        $username = 'admin'; // Username admin Anda
        $password_baru = 'admin123'; // Password yang diinginkan
        
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);
        
        $this->db->where('username', $username);
        $this->db->update('users', ['password' => $hash]);
        
        echo "<h1>Sukses!</h1>";
        echo "Password untuk user <b>$username</b> sudah direset menjadi: <b>$password_baru</b><br>";
        echo "Hash baru: " . $hash;
        echo "<br><br><a href='".base_url('auth/login')."'>Login Sekarang</a>";
    }
}
