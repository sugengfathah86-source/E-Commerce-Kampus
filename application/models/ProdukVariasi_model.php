<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class ProdukVariasi_model extends CI_Model {

    protected $table = 'produk_variasi';

    public function get_by_produk($id_produk) {
        return $this->db->where('id_produk', $id_produk)->order_by('id', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function delete($id, $id_produk) {
        return $this->db->where('id', $id)->where('id_produk', $id_produk)->delete($this->table);
    }

    public function kurangi_stok($id, $qty) {
        $this->db->set('stok', 'stok - ' . (int) $qty, FALSE)->where('id', $id)->update($this->table);
    }

    public function has_variasi($id_produk) {
        return $this->db->where('id_produk', $id_produk)->count_all_results($this->table) > 0;
    }
}
