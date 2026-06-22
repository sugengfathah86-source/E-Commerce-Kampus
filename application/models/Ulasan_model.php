<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Ulasan_model extends CI_Model {

    protected $table = 'ulasan';

    public function insert($data) {
        $this->db->insert($this->table, $data);
        $this->_update_rating_produk($data['id_produk']);
        return $this->db->insert_id();
    }

    public function get_by_produk($id_produk) {
        return $this->db->select('ulasan.*, users.nama as nama_pembeli, users.foto_profil')
                        ->from($this->table)
                        ->join('users', 'users.id = ulasan.id_pembeli')
                        ->where('ulasan.id_produk', $id_produk)
                        ->order_by('ulasan.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function sudah_diulas($id_order, $id_produk) {
        return $this->db->where('id_order', $id_order)->where('id_produk', $id_produk)->get($this->table)->row() !== NULL;
    }

    public function get_items_belum_diulas($id_order) {
        // Item dalam order yang statusnya selesai tapi belum ada ulasan
        return $this->db->select('order_items.id_produk, order_items.nama_barang')
                        ->from('order_items')
                        ->where('order_items.id_order', $id_order)
                        ->where('order_items.id_produk IS NOT NULL')
                        ->get()
                        ->result();
    }

    private function _update_rating_produk($id_produk) {
        $result = $this->db->select_avg('rating')->select_sum('rating', 'total')
                           ->where('id_produk', $id_produk)
                           ->get($this->table)
                           ->row();
        $count = $this->db->where('id_produk', $id_produk)->count_all_results($this->table);
        $avg = $count > 0 ? round($result->rating, 1) : 0;

        $this->db->where('id', $id_produk)->update('produk', [
            'rating_avg'   => $avg,
            'rating_count' => $count,
        ]);
    }
}
