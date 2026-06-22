<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Keranjang extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Keranjang_model', 'Produk_model', 'ProdukVariasi_model']);
        $this->require_login();
    }

    public function index() {
        $uid = $this->session->userdata('user_id');
        $items = $this->Keranjang_model->get_items($uid);

        $grouped = [];
        foreach ($items as $item) {
            $pid = $item->id_penjual;
            if (!isset($grouped[$pid])) {
                $grouped[$pid] = [
                    'nama_penjual' => $item->nama_penjual,
                    'nama_toko'    => $item->nama_toko,
                    'items'        => [],
                ];
            }
            $grouped[$pid]['items'][] = $item;
        }

        $data['title']   = 'Keranjang Belanja';
        $data['grouped'] = $grouped;

        $this->render('keranjang/index', $data);
    }

    public function tambah() {
        $id_produk = (int) $this->input->post('id_produk');
        $id_variasi = (int) $this->input->post('id_variasi') ?: NULL;
        $qty = max(1, (int) $this->input->post('qty'));
        $uid = $this->session->userdata('user_id');

        $produk = $this->Produk_model->get_by_id($id_produk);

        if (!$produk || $produk->status !== 'aktif') {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('produk');
        }

        if ($produk->id_penjual == $uid) {
            $this->session->set_flashdata('error', 'Kamu tidak bisa membeli produk milikmu sendiri.');
            redirect('produk/detail/' . $id_produk);
        }

        // Tentukan batas stok: dari variasi jika ada, atau dari produk utama.
        // Produk pre-order tidak terbatas stok (bisa terus dipesan).
        $stok_tersedia = $produk->stok;
        if ($id_variasi) {
            $variasi = $this->ProdukVariasi_model->get_by_id($id_variasi);
            if (!$variasi || $variasi->id_produk != $id_produk) {
                $this->session->set_flashdata('error', 'Variasi produk tidak valid.');
                redirect('produk/detail/' . $id_produk);
            }
            $stok_tersedia = $variasi->stok;
        }

        if (!$produk->is_preorder && $stok_tersedia <= 0) {
            $this->session->set_flashdata('error', 'Stok produk habis.');
            redirect('produk/detail/' . $id_produk);
        }

        $existing = $this->Keranjang_model->find_existing($uid, $id_produk, $id_variasi);

        if ($existing) {
            $new_qty = $produk->is_preorder ? $existing->qty + $qty : min($stok_tersedia, $existing->qty + $qty);
            $this->Keranjang_model->update_qty($existing->id, $new_qty);
        } else {
            $qty = $produk->is_preorder ? $qty : min($qty, $stok_tersedia);
            $this->Keranjang_model->insert($uid, $id_produk, $qty, $id_variasi);
        }

        $this->session->set_flashdata('success', 'Produk ditambahkan ke keranjang!');
        redirect('keranjang');
    }

    public function update() {
        $cart_id = (int) $this->input->post('cart_id');
        $qty = max(1, (int) $this->input->post('qty'));
        $uid = $this->session->userdata('user_id');

        $cart = $this->Keranjang_model->get_by_id($cart_id, $uid);
        if ($cart) {
            $produk = $this->Produk_model->get_by_id($cart->id_produk);
            if (!$produk->is_preorder) {
                $batas = $cart->id_variasi ? $this->ProdukVariasi_model->get_by_id($cart->id_variasi)->stok : $produk->stok;
                $qty = min($qty, $batas);
            }
            $this->Keranjang_model->update_qty($cart_id, $qty);
        }

        redirect('keranjang');
    }

    public function hapus($cart_id) {
        $uid = $this->session->userdata('user_id');
        $this->Keranjang_model->delete($cart_id, $uid);

        $this->session->set_flashdata('success', 'Produk dihapus dari keranjang.');
        redirect('keranjang');
    }
}
