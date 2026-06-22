<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Follow_model extends CI_Model {

    protected $table = 'follow_toko';

    public function is_following($id_pembeli, $id_penjual) {
        return $this->db->where('id_pembeli', $id_pembeli)->where('id_penjual', $id_penjual)->get($this->table)->row() !== NULL;
    }

    public function toggle($id_pembeli, $id_penjual) {
        $existing = $this->db->where('id_pembeli', $id_pembeli)->where('id_penjual', $id_penjual)->get($this->table)->row();
        if ($existing) {
            $this->db->where('id', $existing->id)->delete($this->table);
            return FALSE;
        }
        $this->db->insert($this->table, ['id_pembeli' => $id_pembeli, 'id_penjual' => $id_penjual]);
        return TRUE;
    }

    public function count_followers($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->count_all_results($this->table);
    }

    public function get_followed_toko($id_pembeli) {
        return $this->db->select('users.id, users.nama, users.nama_toko, users.foto_profil, users.toko_verified')
                        ->from($this->table)
                        ->join('users', 'users.id = follow_toko.id_penjual')
                        ->where('follow_toko.id_pembeli', $id_pembeli)
                        ->order_by('follow_toko.created_at', 'DESC')
                        ->get()
                        ->result();
    }
}
