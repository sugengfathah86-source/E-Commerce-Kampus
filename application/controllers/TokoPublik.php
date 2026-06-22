<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class TokoPublik extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['User_model', 'Produk_model', 'Follow_model', 'Notifikasi_model']);
        $this->require_login();
    }

    // Halaman profil toko publik — siapa pun yang login bisa lihat
    public function index($id_penjual) {
        $penjual = $this->User_model->get_by_id($id_penjual);

        if (!$penjual || $penjual->role != 1) {
            $this->session->set_flashdata('error', 'Toko tidak ditemukan.');
            redirect('produk');
        }

        $uid = $this->session->userdata('user_id');

        $data['title']          = $penjual->nama_toko ?: $penjual->nama;
        $data['penjual']        = $penjual;
        $data['produk']         = $this->Produk_model->get_by_seller_public($id_penjual);
        $data['jumlah_follow']  = $this->Follow_model->count_followers($id_penjual);
        $data['is_following']   = $this->Follow_model->is_following($uid, $id_penjual);
        $data['is_pemilik']     = ($uid == $id_penjual);

        $this->render('toko_publik/index', $data);
    }

    public function follow($id_penjual) {
        $uid = $this->session->userdata('user_id');

        if ($uid == $id_penjual) {
            $this->session->set_flashdata('error', 'Kamu tidak bisa follow toko milikmu sendiri.');
            redirect('toko-publik/' . $id_penjual);
        }

        $followed = $this->Follow_model->toggle($uid, $id_penjual);
        $this->session->set_flashdata('success', $followed ? 'Berhasil follow toko ini!' : 'Berhenti follow toko ini.');

        if ($followed) {
            $this->Notifikasi_model->kirim(
                $id_penjual,
                'follow',
                'Pengikut Baru!',
                $this->session->userdata('nama') . ' mulai mengikuti toko kamu.',
                'toko/dashboard'
            );
        }

        redirect('toko-publik/' . $id_penjual);
    }

    // Daftar toko yang diikuti pembeli
    public function following() {
        $uid = $this->session->userdata('user_id');

        $data['title'] = 'Toko yang Saya Ikuti';
        $data['toko']  = $this->Follow_model->get_followed_toko($uid);

        $this->render('toko_publik/following', $data);
    }
}
