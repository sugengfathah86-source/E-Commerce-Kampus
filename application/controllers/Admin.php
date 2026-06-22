<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Admin extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['User_model', 'Produk_model', 'Statistik_model', 'Voucher_model', 'Komplain_model']);
        $this->require_admin();
    }

    protected function require_admin() {
        $this->require_login();
        if ($this->session->userdata('role') != 2) {
            $this->session->set_flashdata('error', 'Akses ditolak. Halaman ini khusus admin.');
            redirect('produk');
        }
    }

    public function dashboard() {
        $data['title']             = 'Dashboard Admin';
        $data['total_user']        = $this->Statistik_model->total_user();
        $data['total_penjual']     = $this->Statistik_model->total_penjual();
        $data['total_produk']      = $this->Statistik_model->total_produk();
        $data['total_transaksi']   = $this->Statistik_model->total_transaksi();
        $data['total_gmv']         = $this->Statistik_model->total_gmv();
        $data['omzet_per_bulan']   = $this->Statistik_model->omzet_per_bulan();
        $data['kategori_populer']  = $this->Statistik_model->kategori_terpopuler();
        $data['user_terbaru']      = $this->Statistik_model->user_terbaru();
        $data['pending_approval']  = $this->Produk_model->count_pending_approval();
        $data['komplain_terbuka']  = $this->Statistik_model->total_komplain_terbuka();

        $this->render('admin/dashboard', $data);
    }

    // ============ MODERASI PRODUK ============
    public function produk_pending() {
        $data['title']  = 'Moderasi Produk';
        $data['produk'] = $this->Produk_model->get_pending_approval();

        $this->render('admin/produk_pending', $data);
    }

    public function produk_approve($id) {
        $this->Produk_model->approve($id);
        $this->session->set_flashdata('success', 'Produk berhasil disetujui dan kini tampil ke publik.');
        redirect('admin/produk-pending');
    }

    public function produk_reject($id) {
        $catatan = $this->input->post('catatan', TRUE) ?: 'Tidak memenuhi ketentuan platform.';
        $this->Produk_model->reject($id, $catatan);
        $this->session->set_flashdata('success', 'Produk ditolak.');
        redirect('admin/produk-pending');
    }

    // ============ KELOLA USER ============
    public function users() {
        $filter = $this->input->get('role');
        $filter_role = ($filter !== '' && $filter !== null) ? (int) $filter : null;

        $data['title'] = 'Kelola Pengguna';
        $data['users'] = $this->User_model->get_all_users($filter_role);
        $data['filter_role'] = $filter_role;

        $this->render('admin/users', $data);
    }

    public function user_suspend($id) {
        if ($id == $this->session->userdata('user_id')) {
            $this->session->set_flashdata('error', 'Kamu tidak bisa men-suspend akunmu sendiri.');
            redirect('admin/users');
        }
        $this->User_model->suspend($id);
        $this->session->set_flashdata('success', 'Akun berhasil disuspend.');
        redirect('admin/users');
    }

    public function user_aktifkan($id) {
        $this->User_model->aktifkan($id);
        $this->session->set_flashdata('success', 'Akun berhasil diaktifkan kembali.');
        redirect('admin/users');
    }

    public function user_verifikasi($id) {
        $this->User_model->set_verified($id, true);
        $this->session->set_flashdata('success', 'Toko berhasil diverifikasi.');
        redirect('admin/users');
    }

    public function user_batal_verifikasi($id) {
        $this->User_model->set_verified($id, false);
        $this->session->set_flashdata('success', 'Verifikasi toko dicabut.');
        redirect('admin/users');
    }

    // ============ VOUCHER PLATFORM ============
    public function voucher_list() {
        $data['title']    = 'Voucher Platform';
        $data['vouchers'] = $this->Voucher_model->get_platform_vouchers();

        $this->render('admin/voucher_list', $data);
    }

    public function voucher_tambah() {
        $this->form_validation->set_rules('kode', 'Kode Voucher', 'required|trim|is_unique[vouchers.kode]');
        $this->form_validation->set_rules('tipe', 'Tipe', 'required|in_list[persen,nominal]');
        $this->form_validation->set_rules('nilai', 'Nilai', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/voucher');
        }

        $this->Voucher_model->insert([
            'id_penjual'     => NULL,
            'kode'           => strtoupper($this->input->post('kode', TRUE)),
            'tipe'           => $this->input->post('tipe', TRUE),
            'nilai'          => $this->input->post('nilai', TRUE),
            'min_belanja'    => $this->input->post('min_belanja', TRUE) ?: 0,
            'maks_potongan'  => $this->input->post('maks_potongan', TRUE) ?: NULL,
            'kuota'          => $this->input->post('kuota', TRUE) ?: NULL,
            'berlaku_dari'   => $this->input->post('berlaku_dari', TRUE) ?: NULL,
            'berlaku_sampai' => $this->input->post('berlaku_sampai', TRUE) ?: NULL,
            'status'         => 'aktif',
        ]);

        $this->session->set_flashdata('success', 'Voucher platform berhasil dibuat!');
        redirect('admin/voucher');
    }

    public function voucher_nonaktifkan($id) {
        $this->db->where('id', $id)->update('vouchers', ['status' => 'nonaktif']);
        $this->session->set_flashdata('success', 'Voucher dinonaktifkan.');
        redirect('admin/voucher');
    }

    // ============ KOMPLAIN / DISPUTE ============
    public function komplain_list() {
        $data['title']    = 'Komplain Pengguna';
        $data['komplain'] = $this->Komplain_model->get_all();

        $this->render('admin/komplain_list', $data);
    }

    public function komplain_detail($id) {
        $komplain = $this->Komplain_model->get_detail($id);
        if (!$komplain) {
            redirect('admin/komplain');
        }

        $data['title']    = 'Detail Komplain';
        $data['komplain'] = $komplain;

        $this->render('admin/komplain_detail', $data);
    }

    public function komplain_tanggapi($id) {
        $status = $this->input->post('status');
        $tanggapan = $this->input->post('tanggapan', TRUE);

        $this->Komplain_model->update($id, [
            'status'          => $status,
            'tanggapan_admin' => $tanggapan,
        ]);

        $this->session->set_flashdata('success', 'Tanggapan berhasil disimpan.');
        redirect('admin/komplain/detail/' . $id);
    }
}
