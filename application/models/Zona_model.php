<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Zona_model extends CI_Model {

    protected $table = 'shipping_zones';

    // Dipakai saat checkout - zona milik penjual tertentu
    public function get_by_seller($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->order_by('fee', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_by_id_and_seller($id, $id_penjual) {
        return $this->db->where('id', $id)->where('id_penjual', $id_penjual)->get($this->table)->row();
    }

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $id_penjual, $data) {
        return $this->db->where('id', $id)->where('id_penjual', $id_penjual)->update($this->table, $data);
    }

    public function delete($id, $id_penjual) {
        return $this->db->where('id', $id)->where('id_penjual', $id_penjual)->delete($this->table);
    }

    public function count_by_seller($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->count_all_results($this->table);
    }

    // Dipanggil otomatis saat penjual baru buka toko, supaya tidak checkout tanpa pilihan zona
    public function buat_zona_default($id_penjual) {
        $defaults = [
            ['id_penjual' => $id_penjual, 'area_name' => 'Area Kampus', 'fee' => 0],
            ['id_penjual' => $id_penjual, 'area_name' => 'Kost Dekat Kampus', 'fee' => 3000],
            ['id_penjual' => $id_penjual, 'area_name' => 'Luar Kampus', 'fee' => 10000],
        ];
        $this->db->insert_batch($this->table, $defaults);
    }
}
