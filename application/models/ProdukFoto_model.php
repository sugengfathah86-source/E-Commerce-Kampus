<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class ProdukFoto_model extends CI_Model {

    protected $table = 'produk_foto';

    public function get_by_produk($id_produk) {
        return $this->db->where('id_produk', $id_produk)->order_by('urutan', 'ASC')->get($this->table)->result();
    }

    public function insert($id_produk, $foto, $urutan = 0) {
        return $this->db->insert($this->table, [
            'id_produk' => $id_produk,
            'foto'      => $foto,
            'urutan'    => $urutan,
        ]);
    }

    public function delete($id, $id_produk) {
        // Validasi kepemilikan dilakukan di controller (cek id_produk milik penjual)
        return $this->db->where('id', $id)->where('id_produk', $id_produk)->delete($this->table);
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function count_by_produk($id_produk) {
        return $this->db->where('id_produk', $id_produk)->count_all_results($this->table);
    }
}
