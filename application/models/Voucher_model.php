<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Voucher_model extends CI_Model {

    protected $table = 'vouchers';

    public function get_by_kode($kode) {
        return $this->db->where('kode', $kode)->get($this->table)->row();
    }

    public function get_by_id($id) {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_by_seller($id_penjual) {
        return $this->db->where('id_penjual', $id_penjual)->order_by('created_at', 'DESC')->get($this->table)->result();
    }

    public function get_platform_vouchers() {
        return $this->db->where('id_penjual IS NULL')->order_by('created_at', 'DESC')->get($this->table)->result();
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

    public function sudah_dipakai($id_voucher, $id_pembeli) {
        return $this->db->where('id_voucher', $id_voucher)->where('id_pembeli', $id_pembeli)
                        ->get('voucher_usage')->row() !== NULL;
    }

    public function catat_pemakaian($id_voucher, $id_pembeli, $id_order) {
        $this->db->insert('voucher_usage', [
            'id_voucher' => $id_voucher,
            'id_pembeli' => $id_pembeli,
            'id_order'   => $id_order,
        ]);
        $this->db->set('terpakai', 'terpakai + 1', FALSE)->where('id', $id_voucher)->update($this->table);
    }

    /**
     * Validasi voucher secara lengkap: status, tanggal berlaku, kuota, kecocokan toko,
     * minimum belanja, dan one-time-use per pembeli.
     * Return: ['valid' => bool, 'pesan' => string, 'potongan' => float, 'voucher' => obj|null]
     */
    public function validasi($kode, $id_pembeli, $id_penjual, $subtotal) {
        $voucher = $this->get_by_kode($kode);

        if (!$voucher) {
            return ['valid' => false, 'pesan' => 'Kode voucher tidak ditemukan.', 'potongan' => 0, 'voucher' => null];
        }
        if ($voucher->status !== 'aktif') {
            return ['valid' => false, 'pesan' => 'Voucher ini sudah tidak aktif.', 'potongan' => 0, 'voucher' => null];
        }
        // Voucher toko (id_penjual terisi) hanya berlaku di toko itu. Voucher platform (NULL) berlaku semua toko.
        if ($voucher->id_penjual !== null && $voucher->id_penjual != $id_penjual) {
            return ['valid' => false, 'pesan' => 'Voucher ini tidak berlaku untuk toko ini.', 'potongan' => 0, 'voucher' => null];
        }
        if ($voucher->berlaku_dari && date('Y-m-d') < $voucher->berlaku_dari) {
            return ['valid' => false, 'pesan' => 'Voucher belum berlaku.', 'potongan' => 0, 'voucher' => null];
        }
        if ($voucher->berlaku_sampai && date('Y-m-d') > $voucher->berlaku_sampai) {
            return ['valid' => false, 'pesan' => 'Voucher sudah kedaluwarsa.', 'potongan' => 0, 'voucher' => null];
        }
        if ($voucher->kuota !== null && $voucher->terpakai >= $voucher->kuota) {
            return ['valid' => false, 'pesan' => 'Kuota voucher sudah habis.', 'potongan' => 0, 'voucher' => null];
        }
        if ($subtotal < $voucher->min_belanja) {
            return ['valid' => false, 'pesan' => 'Minimum belanja Rp ' . number_format($voucher->min_belanja, 0, ',', '.') . ' untuk pakai voucher ini.', 'potongan' => 0, 'voucher' => null];
        }
        if ($this->sudah_dipakai($voucher->id, $id_pembeli)) {
            return ['valid' => false, 'pesan' => 'Kamu sudah pernah memakai voucher ini.', 'potongan' => 0, 'voucher' => null];
        }

        // Hitung potongan
        if ($voucher->tipe === 'persen') {
            $potongan = $subtotal * ($voucher->nilai / 100);
            if ($voucher->maks_potongan !== null) {
                $potongan = min($potongan, $voucher->maks_potongan);
            }
        } else {
            $potongan = $voucher->nilai;
        }
        $potongan = min($potongan, $subtotal); // tidak boleh minus

        return ['valid' => true, 'pesan' => 'Voucher valid.', 'potongan' => $potongan, 'voucher' => $voucher];
    }
}
