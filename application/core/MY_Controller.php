<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    protected function require_login() {
        if (!$this->session->userdata('logged_in')) {
            redirect('login');
        }
    }

    protected function require_seller() {
        $this->require_login();
        if ($this->session->userdata('role') != 1) {
            redirect('toko/buka');
        }
    }

    protected function render($view, $data = []) {
        $data['cart_count'] = 0;
        $data['notif_unread'] = 0;
        $data['notif_terbaru'] = [];
        if ($this->session->userdata('logged_in')) {
            $uid = $this->session->userdata('user_id');
            $this->load->model(['Keranjang_model', 'Notifikasi_model']);
            $data['cart_count'] = $this->Keranjang_model->get_cart_count($uid);
            $data['notif_unread'] = $this->Notifikasi_model->count_unread($uid);
            $data['notif_terbaru'] = $this->Notifikasi_model->get_by_user($uid, 5);
        }
        $data['content'] = $this->load->view($view, $data, TRUE);
        $this->load->view('layouts/main', $data);
    }

    protected function rupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}
