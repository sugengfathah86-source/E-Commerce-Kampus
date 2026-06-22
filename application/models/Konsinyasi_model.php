<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Konsinyasi_model extends CI_Model {

    protected $table = 'konsinyasi';

    public function insert($data) {
        return $this->db->insert($this->table, $data);
    }

    public function get_by_penitip($id_penitip) {
        return $this->db->select('konsinyasi.*, users.nama as nama_penjual, users.nama_toko')
                        ->from($this->table)
                        ->join('users', 'users.id = konsinyasi.id_penjual')
                        ->where('konsinyasi.id_penitip', $id_penitip)
                        ->order_by('konsinyasi.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_by_penjual($id_penjual, $status = '') {
        $this->db->select('konsinyasi.*, users.nama as nama_penitip')
                 ->from($this->table)
                 ->join('users', 'users.id = konsinyasi.id_penitip')
                 ->where('konsinyasi.id_penjual', $id_penjual)
                 ->order_by('konsinyasi.created_at', 'DESC');
        if ($status !== '') {
            $this->db->where('konsinyasi.status', $status);
        }
        return $this->db->get()->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_detail_for_seller($id, $id_penjual) {
        return $this->db->select('konsinyasi.*, users.nama as nama_penitip, users.no_wa as wa_penitip')
                        ->from($this->table)
                        ->join('users', 'users.id = konsinyasi.id_penitip')
                        ->where('konsinyasi.id', $id)
                        ->where('konsinyasi.id_penjual', $id_penjual)
                        ->get()
                        ->row();
    }

    public function update_status($id, $id_penjual, $status) {
        return $this->db->where('id', $id)->where('id_penjual', $id_penjual)->update($this->table, ['status' => $status]);
    }

    public function tautkan_produk($id, $id_produk) {
        return $this->db->where('id', $id)->update($this->table, ['id_produk' => $id_produk, 'status' => 'diterima']);
    }

    public function count_pending($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->where('status', 'menunggu')->count_all_results($this->table);
    }
}
