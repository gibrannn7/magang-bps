<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wa_client {

    protected $ci;
    // URL Node.js Server Anda (Pastikan port sama dengan .env Node.js)
    private $api_url = 'http://localhost:3000/send-message'; 
    // API KEY Sama dengan .env Node.js
    private $api_key = 'BPS_SECRET_KEY_123456'; 

    public function __construct()
    {
        $this->ci =& get_instance();
    }

    public function send_message($number, $message)
    {
        $data = [
            'number' => $number,
            'message' => $message
        ];

        $ch = curl_init($this->api_url);
        
        // Setup CURL
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $this->api_key, // Kirim Auth Header
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout 10 detik agar web tidak hang lama

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);

        // Logging (Opsional: Simpan ke tabel whatsapp_logs jika mau)
        if ($http_code == 200) {
            return json_decode($response, true);
        } else {
            log_message('error', 'WA Blast Error: ' . $error . ' | Response: ' . $response);
            return ['status' => false, 'message' => 'Gagal menghubungi server WA'];
        }
    }
}
