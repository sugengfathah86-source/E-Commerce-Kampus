<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Produk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Produk_model', 'Kategori_model', 'Wishlist_model', 'ProdukFoto_model', 'ProdukVariasi_model', 'Ulasan_model', 'User_model']);
        $this->require_login();
    }

    public function index() {
        $id_kategori = (int) $this->input->get('kategori');
        $keyword = trim($this->input->get('q') ?? '');
        $harga_min = ($this->input->get('harga_min') !== '' && $this->input->get('harga_min') !== null) ? (int) $this->input->get('harga_min') : null;
        $harga_max = ($this->input->get('harga_max') !== '' && $this->input->get('harga_max') !== null) ? (int) $this->input->get('harga_max') : null;
        $fakultas = trim($this->input->get('fakultas') ?? '');

        $data['title']        = 'Belanja';
        $data['produk']       = $this->Produk_model->get_all_aktif($id_kategori, $keyword, $harga_min, $harga_max, $fakultas ?: null);
        $data['kategori']     = $this->Kategori_model->get_all();
        $data['id_kategori']  = $id_kategori;
        $data['keyword']      = $keyword;
        $data['harga_min']    = $harga_min;
        $data['harga_max']    = $harga_max;
        $data['fakultas']     = $fakultas;
        $data['daftar_fakultas'] = $this->User_model->get_daftar_fakultas();

        $uid = $this->session->userdata('user_id');
        $data['rekomendasi']  = $this->Produk_model->get_rekomendasi($uid, 8);

        $this->render('produk/index', $data);
    }

    public function detail($id) {
        $produk = $this->Produk_model->get_detail($id);

        if (!$produk) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('produk');
        }

        $uid = $this->session->userdata('user_id');

        $data['title']         = $produk->nama_barang;
        $data['produk']        = $produk;
        $data['is_wishlisted'] = $this->Wishlist_model->is_wishlisted($uid, $id);
        $data['galeri']        = $this->ProdukFoto_model->get_by_produk($id);
        $data['variasi']       = $this->ProdukVariasi_model->get_by_produk($id);
        $data['ulasan']        = $this->Ulasan_model->get_by_produk($id);

        $this->render('produk/detail', $data);
    }
}
