<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Keranjang_model extends CI_Model {

    protected $table = 'cart';

    public function get_cart_count($id_pembeli) {
        $result = $this->db->select_sum('qty')->where('id_pembeli', $id_pembeli)->get($this->table)->row();
        return $result->qty ?? 0;
    }

    public function get_items($id_pembeli) {
        return $this->db->select('cart.id as cart_id, cart.qty, cart.id_variasi, produk.id as produk_id, produk.nama_barang,
                                   produk.harga as harga_dasar, produk.foto, produk.stok as stok_produk, produk.id_penjual,
                                   produk.is_preorder,
                                   produk_variasi.nama_variasi, produk_variasi.stok as stok_variasi, produk_variasi.harga_tambahan,
                                   users.nama as nama_penjual, users.nama_toko')
                        ->from($this->table)
                        ->join('produk', 'produk.id = cart.id_produk')
                        ->join('produk_variasi', 'produk_variasi.id = cart.id_variasi', 'left')
                        ->join('users', 'users.id = produk.id_penjual')
                        ->where('cart.id_pembeli', $id_pembeli)
                        ->order_by('produk.id_penjual', 'ASC')
                        ->order_by('cart.id', 'ASC')
                        ->get()
                        ->result();
    }

    public function get_items_by_seller($id_pembeli, $id_penjual) {
        return $this->db->select('cart.id as cart_id, cart.qty, cart.id_variasi, produk.id as produk_id, produk.nama_barang,
                                   produk.harga as harga_dasar, produk.foto, produk.stok as stok_produk, produk.is_preorder,
                                   produk_variasi.nama_variasi, produk_variasi.stok as stok_variasi, produk_variasi.harga_tambahan')
                        ->from($this->table)
                        ->join('produk', 'produk.id = cart.id_produk')
                        ->join('produk_variasi', 'produk_variasi.id = cart.id_variasi', 'left')
                        ->where('cart.id_pembeli', $id_pembeli)
                        ->where('produk.id_penjual', $id_penjual)
                        ->get()
                        ->result();
    }

    public function find_existing($id_pembeli, $id_produk, $id_variasi = null) {
        $this->db->where('id_pembeli', $id_pembeli)->where('id_produk', $id_produk);
        if ($id_variasi) {
            $this->db->where('id_variasi', $id_variasi);
        } else {
            $this->db->where('id_variasi IS NULL');
        }
        return $this->db->get($this->table)->row();
    }

    public function insert($id_pembeli, $id_produk, $qty, $id_variasi = null) {
        return $this->db->insert($this->table, [
            'id_pembeli' => $id_pembeli,
            'id_produk'  => $id_produk,
            'id_variasi' => $id_variasi,
            'qty'        => $qty,
        ]);
    }

    public function update_qty($cart_id, $qty) {
        return $this->db->where('id', $cart_id)->update($this->table, ['qty' => $qty]);
    }

    public function get_by_id($cart_id, $id_pembeli) {
        return $this->db->where('id', $cart_id)->where('id_pembeli', $id_pembeli)->get($this->table)->row();
    }

    public function delete($cart_id, $id_pembeli) {
        return $this->db->where('id', $cart_id)->where('id_pembeli', $id_pembeli)->delete($this->table);
    }

    public function delete_items($cart_ids) {
        return $this->db->where_in('id', $cart_ids)->delete($this->table);
    }
}
