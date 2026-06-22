<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Kategori_model extends CI_Model {

    protected $table = 'kategori';

    public function get_all() {
        return $this->db->order_by('nama_kategori', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }
}
