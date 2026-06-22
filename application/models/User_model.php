<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class User_model extends CI_Model {

    protected $table = 'users';

    public function get_by_email($email) {
        return $this->db->where('email', $email)->get($this->table)->row();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function insert($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function jadikan_penjual($id, $nama_toko, $no_wa) {
        return $this->db->where('id', $id)->update($this->table, [
            'role' => 1,
            'nama_toko' => $nama_toko,
            'no_wa' => $no_wa,
        ]);
    }

    public function update_profile($id, $data) {
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    // ============ SISTEM POIN ============
    public function tambah_poin($id, $jumlah) {
        $this->db->set('poin', 'poin + ' . (int) $jumlah, FALSE)->where('id', $id)->update($this->table);
    }

    public function kurangi_poin($id, $jumlah) {
        $this->db->set('poin', 'GREATEST(poin - ' . (int) $jumlah . ', 0)', FALSE)->where('id', $id)->update($this->table);
    }

    // ============ ADMIN: KELOLA USER ============
    public function get_all_users($filter_role = null) {
        $this->db->order_by('created_at', 'DESC');
        if ($filter_role !== null) {
            $this->db->where('role', $filter_role);
        }
        return $this->db->get($this->table)->result();
    }

    public function suspend($id) {
        return $this->db->where('id', $id)->update($this->table, ['status_akun' => 'suspend']);
    }

    public function aktifkan($id) {
        return $this->db->where('id', $id)->update($this->table, ['status_akun' => 'aktif']);
    }

    public function set_verified($id, $verified) {
        return $this->db->where('id', $id)->update($this->table, ['toko_verified' => $verified ? 1 : 0]);
    }

    public function count_total() {
        return $this->db->count_all_results($this->table);
    }

    public function count_penjual() {
        return $this->db->where('role', 1)->count_all_results($this->table);
    }

    public function get_daftar_fakultas() {
        return $this->db->select('DISTINCT(fakultas) as fakultas', FALSE)->where('fakultas IS NOT NULL')->where('fakultas !=', '')
                        ->order_by('fakultas', 'ASC')->get($this->table)->result();
    }
}
