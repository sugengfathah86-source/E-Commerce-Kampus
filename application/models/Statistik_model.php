<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Statistik_model extends CI_Model {

    public function total_user() {
        return $this->db->count_all_results('users');
    }

    public function total_penjual() {
        return $this->db->where('role', 1)->count_all_results('users');
    }

    public function total_produk() {
        return $this->db->count_all_results('produk');
    }

    public function total_produk_aktif() {
        return $this->db->where('status', 'aktif')->count_all_results('produk');
    }

    public function total_transaksi() {
        return $this->db->count_all_results('orders');
    }

    public function total_gmv() {
        // Gross Merchandise Value — total nilai transaksi yang sudah selesai
        $result = $this->db->select_sum('total')->where('status', 'selesai')->get('orders')->row();
        return $result->total ?? 0;
    }

    public function omzet_per_bulan($bulan = 6) {
        return $this->db->select("DATE_FORMAT(created_at, '%Y-%m') as bulan, SUM(total) as total, COUNT(*) as jumlah")
                        ->from('orders')
                        ->where('status', 'selesai')
                        ->where('created_at >=', date('Y-m-d', strtotime("-$bulan months")))
                        ->group_by('bulan')
                        ->order_by('bulan', 'ASC')
                        ->get()
                        ->result();
    }

    public function kategori_terpopuler($limit = 5) {
        return $this->db->select('kategori.nama_kategori, COUNT(produk.id) as jumlah_produk')
                        ->from('produk')
                        ->join('kategori', 'kategori.id = produk.id_kategori')
                        ->where('produk.status', 'aktif')
                        ->group_by('kategori.id')
                        ->order_by('jumlah_produk', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result();
    }

    public function user_terbaru($limit = 5) {
        return $this->db->order_by('created_at', 'DESC')->limit($limit)->get('users')->result();
    }

    public function total_komplain_terbuka() {
        return $this->db->where('status', 'terbuka')->count_all_results('komplain');
    }
}
