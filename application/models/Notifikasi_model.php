<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Notifikasi_model extends CI_Model {

    protected $table = 'notifikasi';

    public function kirim($id_user, $tipe, $judul, $pesan = '', $link = null) {
        return $this->db->insert($this->table, [
            'id_user' => $id_user,
            'tipe'    => $tipe,
            'judul'   => $judul,
            'pesan'   => $pesan,
            'link'    => $link,
        ]);
    }

    public function get_by_user($id_user, $limit = 15) {
        return $this->db->where('id_user', $id_user)->order_by('created_at', 'DESC')->limit($limit)->get($this->table)->result();
    }

    public function count_unread($id_user) {
        return $this->db->where('id_user', $id_user)->where('is_read', 0)->count_all_results($this->table);
    }

    public function tandai_dibaca($id, $id_user) {
        return $this->db->where('id', $id)->where('id_user', $id_user)->update($this->table, ['is_read' => 1]);
    }

    public function tandai_semua_dibaca($id_user) {
        return $this->db->where('id_user', $id_user)->update($this->table, ['is_read' => 1]);
    }
}
