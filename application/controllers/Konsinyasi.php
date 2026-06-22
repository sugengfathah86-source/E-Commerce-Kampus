<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Konsinyasi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Konsinyasi_model', 'User_model', 'Produk_model', 'Kategori_model', 'Notifikasi_model']);
        $this->require_login();
    }

    // ============ SISI PENITIP ============
    public function index() {
        $uid = $this->session->userdata('user_id');

        $data['title']       = 'Titip Jual Saya';
        $data['konsinyasi']  = $this->Konsinyasi_model->get_by_penitip($uid);

        $this->render('konsinyasi/index', $data);
    }

    public function ajukan() {
        $uid = $this->session->userdata('user_id');

        $data['title']    = 'Ajukan Titip Jual';
        $data['penjual']  = $this->User_model->get_all_users(1); // semua toko (role 1)

        $this->render('konsinyasi/ajukan', $data);
    }

    public function simpan() {
        $uid = $this->session->userdata('user_id');
        $id_penjual = (int) $this->input->post('id_penjual');

        if ($id_penjual == $uid) {
            $this->session->set_flashdata('error', 'Kamu tidak bisa menitip barang ke tokomu sendiri.');
            redirect('konsinyasi/ajukan');
        }

        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('harga_titipan', 'Harga Titipan', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('harga_jual', 'Harga Jual', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('qty', 'Jumlah', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('konsinyasi/ajukan');
        }

        $harga_titipan = (int) $this->input->post('harga_titipan', TRUE);
        $harga_jual = (int) $this->input->post('harga_jual', TRUE);

        if ($harga_jual < $harga_titipan) {
            $this->session->set_flashdata('error', 'Harga jual tidak boleh lebih kecil dari harga titipan.');
            redirect('konsinyasi/ajukan');
        }

        $foto_name = null;
        if (!empty($_FILES['foto']['name'])) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
                $foto_name = 'konsinyasi_' . time() . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], FCPATH . 'assets/uploads/produk/' . $foto_name);
            }
        }

        $this->Konsinyasi_model->insert([
            'id_penitip'    => $uid,
            'id_penjual'    => $id_penjual,
            'nama_barang'   => $this->input->post('nama_barang', TRUE),
            'deskripsi'     => $this->input->post('deskripsi', TRUE),
            'harga_titipan' => $harga_titipan,
            'harga_jual'    => $harga_jual,
            'qty'           => $this->input->post('qty', TRUE),
            'foto'          => $foto_name,
            'status'        => 'menunggu',
        ]);

        $this->Notifikasi_model->kirim(
            $id_penjual,
            'konsinyasi',
            'Ada Pengajuan Titip Jual',
            $this->session->userdata('nama') . ' ingin menitipkan barang di tokomu.',
            'konsinyasi/kelola'
        );

        $this->session->set_flashdata('success', 'Pengajuan titip jual berhasil dikirim ke toko!');
        redirect('konsinyasi');
    }

    // ============ SISI PENJUAL (yang menerima titipan) ============
    public function kelola() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $status = $this->input->get('status') ?: '';

        $data['title']      = 'Kelola Titip Jual';
        $data['konsinyasi'] = $this->Konsinyasi_model->get_by_penjual($uid, $status);
        $data['filter_status'] = $status;

        $this->render('konsinyasi/kelola', $data);
    }

    public function detail($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $item = $this->Konsinyasi_model->get_detail_for_seller($id, $uid);
        if (!$item) {
            redirect('konsinyasi/kelola');
        }

        $data['title']    = 'Detail Titip Jual';
        $data['item']     = $item;
        $data['kategori'] = $this->Kategori_model->get_all();

        $this->render('konsinyasi/detail', $data);
    }

    // Terima pengajuan + otomatis buat produk dari titipan (langsung pending_approval seperti produk biasa)
    public function terima($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $item = $this->Konsinyasi_model->get_detail_for_seller($id, $uid);
        if (!$item || $item->status !== 'menunggu') {
            redirect('konsinyasi/kelola');
        }

        $id_kategori = (int) $this->input->post('id_kategori');

        $this->db->trans_start();

        $this->Produk_model->insert([
            'id_penjual'  => $uid,
            'id_kategori' => $id_kategori > 0 ? $id_kategori : NULL,
            'nama_barang' => $item->nama_barang,
            'deskripsi'   => $item->deskripsi . "\n\n(Barang titipan dari " . $item->nama_penitip . ")",
            'harga'       => $item->harga_jual,
            'stok'        => $item->qty,
            'foto'        => $item->foto,
            'status'      => 'pending_approval',
        ]);
        $id_produk = $this->db->insert_id();

        $this->Konsinyasi_model->tautkan_produk($id, $id_produk);

        $this->db->trans_complete();

        $this->Notifikasi_model->kirim(
            $item->id_penitip,
            'konsinyasi',
            'Titip Jual Diterima!',
            'Toko menerima titipan barangmu: ' . $item->nama_barang,
            'konsinyasi'
        );

        $this->session->set_flashdata('success', 'Titipan diterima dan produk berhasil dibuat (menunggu persetujuan admin).');
        redirect('konsinyasi/kelola');
    }

    public function tolak($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $item = $this->Konsinyasi_model->get_detail_for_seller($id, $uid);
        if (!$item) {
            redirect('konsinyasi/kelola');
        }

        $this->Konsinyasi_model->update_status($id, $uid, 'ditolak');

        $this->Notifikasi_model->kirim(
            $item->id_penitip,
            'konsinyasi',
            'Titip Jual Ditolak',
            'Toko menolak titipan barangmu: ' . $item->nama_barang,
            'konsinyasi'
        );

        $this->session->set_flashdata('success', 'Pengajuan titip jual ditolak.');
        redirect('konsinyasi/kelola');
    }
}
