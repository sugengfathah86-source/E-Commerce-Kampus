<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login() {
        if ($this->session->userdata('logged_in')) {
            redirect('produk');
        }
        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

    // Login manual dengan email + password
    public function login_process() {
        $email    = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->User_model->get_by_email($email);

        if (!$user || !$user->password || !password_verify($password, $user->password)) {
            $this->session->set_flashdata('error', 'Email atau password salah.');
            redirect('login');
        }

        if ($user->status_akun === 'suspend') {
            $this->session->set_flashdata('error', 'Akun kamu disuspend. Hubungi admin.');
            redirect('login');
        }

        $this->session->set_userdata([
            'logged_in' => TRUE,
            'user_id'   => $user->id,
            'nama'      => $user->nama,
            'email'     => $user->email,
            'role'      => $user->role,
            'foto'      => $user->foto_profil,
        ]);

        redirect('produk');
    }

    // Halaman registrasi
    public function register() {
        if ($this->session->userdata('logged_in')) {
            redirect('produk');
        }
        $this->load->view('auth/register');
    }

    // Proses registrasi
    public function register_process() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim|min_length[3]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules('konfirmasi_password', 'Konfirmasi Password', 'required|matches[password]');

        if ($this->form_validation->run() === FALSE) {
            $data['errors'] = $this->form_validation->error_array();
            $this->load->view('auth/register', $data);
            return;
        }

        $email = $this->input->post('email');

        // Validasi domain email kampus
        $domain = substr(strrchr($email, "@"), 1);
        $allowed = $this->config->item('allowed_domain');
        if ($allowed && strtolower($domain) !== strtolower($allowed)) {
            $data['errors'] = ["Gunakan email kampus (@$allowed). Email @$domain tidak diizinkan."];
            $this->load->view('auth/register', $data);
            return;
        }
        if ($this->User_model->get_by_email($email)) {
            $data['errors'] = ['Email sudah terdaftar. Silakan login.'];
            $this->load->view('auth/register', $data);
            return;
        }

        $this->User_model->insert([
            'nama'     => $this->input->post('nama'),
            'email'    => $email,
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            'role'     => 0,
        ]);

        $this->session->set_flashdata('success', 'Akun berhasil dibuat! Silakan login.');
        redirect('login');
    }

    // Step 1: arahkan user ke halaman consent Google
    public function google_login() {
        $state = bin2hex(random_bytes(16));
        $this->session->set_userdata('oauth_state', $state);

        $params = [
            'client_id'     => $this->config->item('google_client_id'),
            'redirect_uri'  => $this->config->item('google_redirect_uri'),
            'response_type' => 'code',
            'scope'         => 'email profile',
            'state'         => $state,
            'prompt'        => 'select_account',
        ];

        $auth_url = $this->config->item('google_auth_url') . '?' . http_build_query($params);
        redirect($auth_url, 'location', 302);
    }

    // Step 2: callback dari Google
    public function google_callback() {
        $state = $this->input->get('state');
        $code  = $this->input->get('code');

        // Validasi state (anti CSRF)
        if (!$state || $state !== $this->session->userdata('oauth_state')) {
            $this->session->set_flashdata('error', 'Validasi keamanan gagal. Silakan login ulang.');
            redirect('login');
        }
        $this->session->unset_userdata('oauth_state');

        if (!$code) {
            $this->session->set_flashdata('error', 'Login dengan Google dibatalkan.');
            redirect('login');
        }

        // Tukar code menjadi access token
        $token_data = $this->_exchange_code_for_token($code);
        if (!$token_data || !isset($token_data['access_token'])) {
            $this->session->set_flashdata('error', 'Gagal mendapatkan akses dari Google.');
            redirect('login');
        }

        // Ambil profil user dari Google
        $google_user = $this->_get_google_userinfo($token_data['access_token']);
        if (!$google_user || !isset($google_user['email'])) {
            $this->session->set_flashdata('error', 'Gagal mengambil data profil Google.');
            redirect('login');
        }

        $email     = $google_user['email'];
        $nama      = $google_user['name'] ?? 'Mahasiswa';
        $google_id = $google_user['id'] ?? '';
        $foto      = $google_user['picture'] ?? null;

        // VALIDASI DOMAIN — hanya email kampus yang diizinkan
        $domain = substr(strrchr($email, "@"), 1);
        $allowed = $this->config->item('allowed_domain');

        if (strtolower($domain) !== strtolower($allowed)) {
            $this->session->set_flashdata('error', "Gunakan email kampus (@$allowed)! Email kamu (@$domain) tidak diizinkan.");
            redirect('login');
        }

        // Cek user sudah ada atau daftar otomatis
        $user = $this->User_model->get_by_email($email);

        if (!$user) {
            $user_id = $this->User_model->insert([
                'google_id'   => $google_id,
                'email'       => $email,
                'nama'        => $nama,
                'foto_profil' => $foto,
                'role'        => 0,
            ]);
            $user = $this->User_model->get_by_id($user_id);
        }

        // Simpan ke session
        $this->session->set_userdata([
            'logged_in' => TRUE,
            'user_id'   => $user->id,
            'nama'      => $user->nama,
            'email'     => $user->email,
            'role'      => $user->role,
            'foto'      => $user->foto_profil,
        ]);

        redirect('produk');
    }

    private function _exchange_code_for_token($code) {
        $params = [
            'code'          => $code,
            'client_id'     => $this->config->item('google_client_id'),
            'client_secret' => $this->config->item('google_client_secret'),
            'redirect_uri'  => $this->config->item('google_redirect_uri'),
            'grant_type'    => 'authorization_code',
        ];

        $ch = curl_init($this->config->item('google_token_url'));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error || !$response) {
            return null;
        }
        return json_decode($response, true);
    }

    private function _get_google_userinfo($access_token) {
        $url = $this->config->item('google_userinfo_url') . '?access_token=' . urlencode($access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }
        return json_decode($response, true);
    }
}