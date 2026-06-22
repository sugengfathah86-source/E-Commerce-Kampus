<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Wishlist extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Wishlist_model');
        $this->require_login();
    }

    public function index() {
        $uid = $this->session->userdata('user_id');

        $data['title']    = 'Wishlist Saya';
        $data['wishlist'] = $this->Wishlist_model->get_by_user($uid);

        $this->render('wishlist/index', $data);
    }

    public function toggle($id_produk) {
        $uid = $this->session->userdata('user_id');
        $added = $this->Wishlist_model->toggle($uid, $id_produk);

        if ($added) {
            $this->session->set_flashdata('success', 'Ditambahkan ke wishlist!');
        } else {
            $this->session->set_flashdata('success', 'Dihapus dari wishlist.');
        }

        // Kembali ke halaman sebelumnya (detail produk atau daftar wishlist)
        redirect($this->input->post('redirect_to') ?: 'produk/detail/' . $id_produk);
    }
}
