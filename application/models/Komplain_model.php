<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Komplain_model extends CI_Model {

    protected $table = 'komplain';

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function get_all() {
        return $this->db->select('komplain.*, orders.kode_order, users.nama as nama_pembeli')
                        ->from($this->table)
                        ->join('orders', 'orders.id = komplain.id_order')
                        ->join('users', 'users.id = komplain.id_pembeli')
                        ->order_by('komplain.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_detail($id) {
        return $this->db->select('komplain.*, orders.kode_order, orders.total, users.nama as nama_pembeli, users.email')
                        ->from($this->table)
                        ->join('orders', 'orders.id = komplain.id_order')
                        ->join('users', 'users.id = komplain.id_pembeli')
                        ->where('komplain.id', $id)
                        ->get()
                        ->row();
    }

    public function get_by_pembeli($id_pembeli) {
        return $this->db->select('komplain.*, orders.kode_order')
                        ->from($this->table)
                        ->join('orders', 'orders.id = komplain.id_order')
                        ->where('komplain.id_pembeli', $id_pembeli)
                        ->order_by('komplain.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function sudah_komplain($id_order) {
        return $this->db->where('id_order', $id_order)->get($this->table)->row() !== NULL;
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }
}
