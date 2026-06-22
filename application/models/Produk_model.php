<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Produk_model extends CI_Model {

    protected $table = 'produk';

    public function get_all_aktif($id_kategori = 0, $keyword = '', $harga_min = null, $harga_max = null, $id_fakultas_penjual = null) {
        $this->db->select('produk.*, users.nama as nama_penjual, users.nama_toko, users.fakultas, users.toko_libur, kategori.nama_kategori')
                 ->from($this->table)
                 ->join('users', 'users.id = produk.id_penjual')
                 ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                 ->where('produk.status', 'aktif')
                 ->order_by('produk.created_at', 'DESC');

        if ($id_kategori > 0) {
            $this->db->where('produk.id_kategori', $id_kategori);
        }
        if ($keyword !== '') {
            $this->db->like('produk.nama_barang', $keyword);
        }
        if ($harga_min !== null) {
            $this->db->where('produk.harga >=', $harga_min);
        }
        if ($harga_max !== null) {
            $this->db->where('produk.harga <=', $harga_max);
        }
        if ($id_fakultas_penjual !== null && $id_fakultas_penjual !== '') {
            $this->db->where('users.fakultas', $id_fakultas_penjual);
        }

        return $this->db->get()->result();
    }

    // Rekomendasi sederhana: produk dari kategori yang paling sering dibeli user ini.
    // Kalau user belum pernah beli apapun, kembalikan produk terlaris secara umum.
    public function get_rekomendasi($id_pembeli, $limit = 8) {
        if ($id_pembeli) {
            $kategori_favorit = $this->db->select('produk.id_kategori')
                ->from('order_items')
                ->join('orders', 'orders.id = order_items.id_order')
                ->join('produk', 'produk.id = order_items.id_produk')
                ->where('orders.id_pembeli', $id_pembeli)
                ->where('produk.id_kategori IS NOT NULL')
                ->group_by('produk.id_kategori')
                ->order_by('COUNT(*)', 'DESC')
                ->limit(1)
                ->get()
                ->row();

            if ($kategori_favorit) {
                return $this->db->select('produk.*, users.nama_toko, users.nama as nama_penjual')
                    ->from($this->table)
                    ->join('users', 'users.id = produk.id_penjual')
                    ->where('produk.status', 'aktif')
                    ->where('produk.id_kategori', $kategori_favorit->id_kategori)
                    ->order_by('produk.total_terjual', 'DESC')
                    ->limit($limit)
                    ->get()
                    ->result();
            }
        }

        // Fallback: produk terlaris secara umum
        return $this->db->select('produk.*, users.nama_toko, users.nama as nama_penjual')
            ->from($this->table)
            ->join('users', 'users.id = produk.id_penjual')
            ->where('produk.status', 'aktif')
            ->order_by('produk.total_terjual', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }

    public function get_detail($id) {
        return $this->db->select('produk.*, users.nama as nama_penjual, users.nama_toko, users.no_wa, kategori.nama_kategori')
                        ->from($this->table)
                        ->join('users', 'users.id = produk.id_penjual')
                        ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                        ->where('produk.id', $id)
                        ->get()
                        ->row();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_by_seller($id_penjual) {
        return $this->db->select('produk.*, kategori.nama_kategori')
                        ->from($this->table)
                        ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                        ->where('produk.id_penjual', $id_penjual)
                        ->order_by('produk.created_at', 'DESC')
                        ->get()
                        ->result();
    }

    // Dipakai di halaman profil toko publik — hanya produk aktif yang ditampilkan ke pembeli lain
    public function get_by_seller_public($id_penjual) {
        return $this->db->select('produk.*, kategori.nama_kategori')
                        ->from($this->table)
                        ->join('kategori', 'kategori.id = produk.id_kategori', 'left')
                        ->where('produk.id_penjual', $id_penjual)
                        ->where('produk.status', 'aktif')
                        ->order_by('produk.created_at', 'DESC')
                        ->get()
                        ->result();
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

    public function kurangi_stok($id, $qty) {
        $this->db->set('stok', 'stok - ' . (int) $qty, FALSE)->where('id', $id)->update($this->table);
    }

    public function count_by_seller($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->count_all_results($this->table);
    }

    // ============ ADMIN: APPROVAL PRODUK ============
    public function get_pending_approval() {
        return $this->db->select('produk.*, users.nama as nama_penjual, users.nama_toko')
                        ->from($this->table)
                        ->join('users', 'users.id = produk.id_penjual')
                        ->where('produk.status', 'pending_approval')
                        ->order_by('produk.created_at', 'ASC')
                        ->get()
                        ->result();
    }

    public function approve($id) {
        return $this->db->where('id', $id)->update($this->table, ['status' => 'aktif', 'catatan_admin' => NULL]);
    }

    public function reject($id, $catatan) {
        return $this->db->where('id', $id)->update($this->table, ['status' => 'ditolak', 'catatan_admin' => $catatan]);
    }

    public function count_pending_approval() {
        return $this->db->where('status', 'pending_approval')->count_all_results($this->table);
    }

    // ============ STOK ALERT ============
    // Produk dengan stok menipis (<=5) milik penjual tertentu, untuk notifikasi dashboard
    public function get_stok_menipis($id_penjual, $batas = 5) {
        return $this->db->where('id_penjual', $id_penjual)
                        ->where('status', 'aktif')
                        ->where('stok <=', $batas)
                        ->where('stok >', 0)
                        ->order_by('stok', 'ASC')
                        ->get($this->table)
                        ->result();
    }

    // ============ COUNTER TERJUAL (dipanggil setiap order selesai) ============
    public function tambah_terjual($id, $qty) {
        $this->db->set('total_terjual', 'total_terjual + ' . (int) $qty, FALSE)->where('id', $id)->update($this->table);
    }
}
