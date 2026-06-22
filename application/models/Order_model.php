<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Order_model extends CI_Model {

    protected $table = 'orders';

    public function generate_kode() {
        return 'ORD' . date('Ymd') . strtoupper(substr(uniqid(), -6));
    }

    public function insert_order($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function insert_item($data) {
        return $this->db->insert('order_items', $data);
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    // Detail order untuk pembeli (cek kepemilikan)
    public function get_detail_for_buyer($id, $id_pembeli) {
        return $this->db->select('orders.*, users.nama as nama_penjual, users.nama_toko, users.no_wa,
                                   zones.area_name')
                        ->from($this->table)
                        ->join('users', 'users.id = orders.id_penjual')
                        ->join('shipping_zones as zones', 'zones.id = orders.id_zona', 'left')
                        ->where('orders.id', $id)
                        ->where('orders.id_pembeli', $id_pembeli)
                        ->get()
                        ->row();
    }

    // Detail order untuk penjual (cek kepemilikan)
    public function get_detail_for_seller($id, $id_penjual) {
        return $this->db->select('orders.*, users.nama as nama_pembeli, users.no_wa as wa_pembeli,
                                   users.email, zones.area_name')
                        ->from($this->table)
                        ->join('users', 'users.id = orders.id_pembeli')
                        ->join('shipping_zones as zones', 'zones.id = orders.id_zona', 'left')
                        ->where('orders.id', $id)
                        ->where('orders.id_penjual', $id_penjual)
                        ->get()
                        ->row();
    }

    public function get_items($id_order) {
        return $this->db->where('id_order', $id_order)->get('order_items')->result();
    }

    public function get_riwayat_pembeli($id_pembeli) {
        return $this->db->select('orders.*, users.nama as nama_penjual, users.nama_toko')
                        ->from($this->table)
                        ->join('users', 'users.id = orders.id_penjual')
                        ->where('orders.id_pembeli', $id_pembeli)
                        ->order_by('orders.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_riwayat_pembeli_by_range($id_pembeli, $dari, $sampai) {
        return $this->db->select('orders.*, users.nama as nama_penjual, users.nama_toko')
                        ->from($this->table)
                        ->join('users', 'users.id = orders.id_penjual')
                        ->where('orders.id_pembeli', $id_pembeli)
                        ->where('DATE(orders.created_at) >=', $dari)
                        ->where('DATE(orders.created_at) <=', $sampai)
                        ->order_by('orders.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_orders_for_seller($id_penjual, $status = '') {
        $this->db->select('orders.*, users.nama as nama_pembeli, users.no_wa as wa_pembeli')
                 ->from($this->table)
                 ->join('users', 'users.id = orders.id_pembeli')
                 ->where('orders.id_penjual', $id_penjual)
                 ->order_by('orders.created_at', 'DESC');
        if ($status !== '') {
            $this->db->where('orders.status', $status);
        }
        return $this->db->get()->result();
    }

    // Untuk halaman laporan/rekap penjual dengan filter rentang tanggal
    public function get_orders_by_range($id_penjual, $dari, $sampai) {
        return $this->db->select('orders.*, users.nama as nama_pembeli')
                        ->from($this->table)
                        ->join('users', 'users.id = orders.id_pembeli')
                        ->where('orders.id_penjual', $id_penjual)
                        ->where('DATE(orders.created_at) >=', $dari)
                        ->where('DATE(orders.created_at) <=', $sampai)
                        ->order_by('orders.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_rekap_produk_terjual($id_penjual, $dari, $sampai) {
        return $this->db->select('order_items.nama_barang, SUM(order_items.qty) as total_qty, SUM(order_items.subtotal) as total_pendapatan')
                        ->from('order_items')
                        ->join('orders', 'orders.id = order_items.id_order')
                        ->where('orders.id_penjual', $id_penjual)
                        ->where('orders.status', 'selesai')
                        ->where('DATE(orders.created_at) >=', $dari)
                        ->where('DATE(orders.created_at) <=', $sampai)
                        ->group_by('order_items.nama_barang')
                        ->order_by('total_qty', 'DESC')
                        ->get()
                        ->result();
    }

    public function update_status($id, $id_penjual, $status) {
        return $this->db->where('id', $id)->where('id_penjual', $id_penjual)->update($this->table, ['status' => $status]);
    }

    // ============ AUTO-CANCEL ORDER YANG TIDAK DIBAYAR ============
    // Dipanggil setiap dashboard/order list diakses (lightweight cron pengganti, lihat catatan di Toko::__construct)
    public function auto_cancel_expired() {
        $this->db->where('status', 'pending')
                 ->where('batas_bayar IS NOT NULL')
                 ->where('batas_bayar <', date('Y-m-d H:i:s'))
                 ->update($this->table, ['status' => 'dibatalkan']);
        return $this->db->affected_rows();
    }

    // Kembalikan stok untuk order yang baru dibatalkan otomatis (dipanggil terpisah karena butuh detail item)
    public function get_baru_dibatalkan_otomatis() {
        // Order yang berubah ke dibatalkan dalam 1 menit terakhir akibat auto_cancel_expired()
        return $this->db->where('status', 'dibatalkan')
                        ->where('batas_bayar IS NOT NULL')
                        ->where('batas_bayar >=', date('Y-m-d H:i:s', strtotime('-1 minute')))
                        ->get($this->table)
                        ->result();
    }

    public function update_bukti_bayar($id, $id_pembeli, $filename) {
        return $this->db->where('id', $id)->where('id_pembeli', $id_pembeli)
                        ->update($this->table, ['bukti_bayar' => $filename, 'status' => 'dikonfirmasi']);
    }

    // Dashboard stats
    public function count_produk_terjual($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->count_all_results($this->table);
    }

    public function total_omzet($id_penjual) {
        $result = $this->db->select_sum('total')
                           ->where('id_penjual', $id_penjual)
                           ->where('status', 'selesai')
                           ->get($this->table)
                           ->row();
        return $result->total ?? 0;
    }

    public function count_pending($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->where('status', 'pending')->count_all_results($this->table);
    }

    public function get_terbaru($id_penjual, $limit = 5) {
        return $this->db->select('orders.*, users.nama as nama_pembeli')
                        ->from($this->table)
                        ->join('users', 'users.id = orders.id_pembeli')
                        ->where('orders.id_penjual', $id_penjual)
                        ->order_by('orders.created_at', 'DESC')
                        ->limit($limit)
                        ->get()
                        ->result();
    }
}
