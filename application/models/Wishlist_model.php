<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Wishlist_model extends CI_Model {

    protected $table = 'wishlist';

    public function is_wishlisted($id_pembeli, $id_produk) {
        return $this->db->where('id_pembeli', $id_pembeli)->where('id_produk', $id_produk)->get($this->table)->row() !== NULL;
    }

    public function toggle($id_pembeli, $id_produk) {
        $existing = $this->db->where('id_pembeli', $id_pembeli)->where('id_produk', $id_produk)->get($this->table)->row();
        if ($existing) {
            $this->db->where('id', $existing->id)->delete($this->table);
            return FALSE; // dihapus
        }
        $this->db->insert($this->table, ['id_pembeli' => $id_pembeli, 'id_produk' => $id_produk]);
        return TRUE; // ditambahkan
    }

    public function get_by_user($id_pembeli) {
        return $this->db->select('produk.*, users.nama as nama_penjual, users.nama_toko, kategori.nama_kategori, wishlist.created_at as wishlisted_at')
                        ->from($this->table)
                        ->join('produk', 'produk.id = wishlist.id_produk')
                        ->join('users', 'users.id = produk.id_penjual')
                        ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                        ->where('wishlist.id_pembeli', $id_pembeli)
                        ->order_by('wishlist.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function count_by_user($id_pembeli) {
        return $this->db->where('id_pembeli', $id_pembeli)->count_all_results($this->table);
    }

    // Untuk badge jumlah wishlist di kartu produk (opsional, "X orang menyimpan ini")
    public function count_by_produk($id_produk) {
        return $this->db->where('id_produk', $id_produk)->count_all_results($this->table);
    }
}
