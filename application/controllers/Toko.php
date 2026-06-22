<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Toko extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['User_model', 'Produk_model', 'Kategori_model', 'Order_model', 'Zona_model', 'ProdukFoto_model', 'ProdukVariasi_model', 'Notifikasi_model']);

        // Auto-cancel order yang sudah lewat batas bayar + kembalikan stoknya
        // (pengganti cron job sederhana, dijalankan setiap kali penjual mengakses area Toko)
        $this->_jalankan_auto_cancel();
    }

    private function _jalankan_auto_cancel() {
        $expired = $this->db->where('status', 'pending')
                            ->where('batas_bayar IS NOT NULL')
                            ->where('batas_bayar <', date('Y-m-d H:i:s'))
                            ->get('orders')
                            ->result();

        foreach ($expired as $order) {
            $this->db->trans_start();
            $this->Order_model->update_status($order->id, $order->id_penjual, 'dibatalkan');
            $this->_kembalikan_stok_order($order->id);
            $this->db->trans_complete();
        }
    }

    // ============ BUKA TOKO (upgrade jadi penjual) ============
    public function buka_toko() {
        $this->require_login();

        if ($this->session->userdata('role') == 1) {
            redirect('toko/dashboard');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('nama_toko', 'Nama Toko', 'required|trim');
            $this->form_validation->set_rules('no_wa', 'Nomor WhatsApp', 'required|trim|regex_match[/^8[0-9]{8,13}$/]',
                ['regex_match' => 'Nomor WhatsApp tidak valid. Masukkan tanpa awalan 0/+62, contoh: 81234567890.']);

            if ($this->form_validation->run() === FALSE) {
                $this->session->set_flashdata('error', validation_errors());
                redirect('toko/buka');
            }

            $uid = $this->session->userdata('user_id');
            $this->User_model->jadikan_penjual(
                $uid,
                $this->input->post('nama_toko', TRUE),
                $this->input->post('no_wa', TRUE)
            );

            // Buat zona ongkir default supaya toko langsung bisa terima order
            $this->Zona_model->buat_zona_default($uid);

            $this->session->set_userdata('role', 1);
            $this->session->set_flashdata('success', 'Selamat! Toko kamu sudah aktif. Atur zona ongkir & mulai tambahkan produk pertamamu.');
            redirect('toko/dashboard');
        }

        $data['title'] = 'Buka Toko';
        $this->render('toko/buka_toko', $data);
    }

    // ============ DASHBOARD ============
    public function dashboard() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $data['title']         = 'Dashboard Penjual';
        $data['total_produk']  = $this->Produk_model->count_by_seller($uid);
        $data['total_omzet']   = $this->Order_model->total_omzet($uid);
        $data['jumlah_order']  = $this->Order_model->count_produk_terjual($uid);
        $data['order_pending'] = $this->Order_model->count_pending($uid);
        $data['orders']        = $this->Order_model->get_terbaru($uid, 5);

        $this->render('toko/dashboard', $data);
    }

    // ============ CRUD PRODUK ============
    public function produk_list() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $data['title']  = 'Kelola Produk';
        $data['produk'] = $this->Produk_model->get_by_seller($uid);

        $this->render('toko/produk_list', $data);
    }

    public function produk_tambah() {
        $this->require_seller();

        $data['title']    = 'Tambah Produk';
        $data['kategori'] = $this->Kategori_model->get_all();
        $data['variasi']  = [];

        $this->render('toko/produk_form', $data);
    }

    public function produk_simpan() {
        $this->require_seller();

        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('harga', 'Harga', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('stok', 'Stok', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('toko/produk/tambah');
        }

        $foto_name = $this->_upload_foto_produk('foto');
        if ($foto_name === FALSE) {
            redirect('toko/produk/tambah');
        }

        $uid = $this->session->userdata('user_id');
        $id_kategori = (int) $this->input->post('id_kategori', TRUE);
        $is_preorder = $this->input->post('is_preorder') ? 1 : 0;

        $this->db->trans_start();

        $this->Produk_model->insert([
            'id_penjual'        => $uid,
            'id_kategori'       => $id_kategori > 0 ? $id_kategori : NULL,
            'nama_barang'       => $this->input->post('nama_barang', TRUE),
            'deskripsi'         => $this->input->post('deskripsi', TRUE),
            'harga'             => $this->input->post('harga', TRUE),
            'stok'              => $this->input->post('stok', TRUE),
            'foto'              => $foto_name,
            'is_preorder'       => $is_preorder,
            'estimasi_preorder' => $is_preorder ? $this->input->post('estimasi_preorder', TRUE) : NULL,
            'status'            => 'pending_approval',
        ]);
        $id_produk = $this->db->insert_id();

        $this->_simpan_galeri_tambahan($id_produk);
        $this->_simpan_variasi($id_produk);

        $this->db->trans_complete();

        $this->session->set_flashdata('success', 'Produk berhasil ditambahkan! Menunggu persetujuan admin sebelum tampil ke publik.');
        redirect('toko/produk');
    }

    public function produk_edit($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $produk = $this->Produk_model->get_by_id_and_seller($id, $uid);
        if (!$produk) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('toko/produk');
        }

        $data['title']    = 'Edit Produk';
        $data['produk']   = $produk;
        $data['kategori'] = $this->Kategori_model->get_all();
        $data['galeri']   = $this->ProdukFoto_model->get_by_produk($id);
        $data['variasi']  = $this->ProdukVariasi_model->get_by_produk($id);

        $this->render('toko/produk_form', $data);
    }

    public function produk_update($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $produk = $this->Produk_model->get_by_id_and_seller($id, $uid);
        if (!$produk) {
            $this->session->set_flashdata('error', 'Produk tidak ditemukan.');
            redirect('toko/produk');
        }

        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('harga', 'Harga', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('toko/produk/edit/' . $id);
        }

        $foto_name = $produk->foto;
        if (!empty($_FILES['foto']['name'])) {
            $uploaded = $this->_upload_foto_produk('foto');
            if ($uploaded === FALSE) {
                redirect('toko/produk/edit/' . $id);
            }
            $foto_name = $uploaded;
        }

        $id_kategori = (int) $this->input->post('id_kategori', TRUE);
        $is_preorder = $this->input->post('is_preorder') ? 1 : 0;

        // Edit signifikan (nama/harga) butuh approval ulang, status lain (aktif/nonaktif) tetap dihormati
        $status_baru = $this->input->post('status', TRUE);

        $this->db->trans_start();

        $this->Produk_model->update($id, $uid, [
            'nama_barang'       => $this->input->post('nama_barang', TRUE),
            'id_kategori'       => $id_kategori > 0 ? $id_kategori : NULL,
            'harga'             => $this->input->post('harga', TRUE),
            'stok'              => $this->input->post('stok', TRUE),
            'deskripsi'         => $this->input->post('deskripsi', TRUE),
            'foto'              => $foto_name,
            'is_preorder'       => $is_preorder,
            'estimasi_preorder' => $is_preorder ? $this->input->post('estimasi_preorder', TRUE) : NULL,
            'status'            => in_array($status_baru, ['aktif', 'nonaktif']) ? $status_baru : $produk->status,
        ]);

        $this->_simpan_galeri_tambahan($id);
        $this->_kelola_variasi_update($id);

        $this->db->trans_complete();

        $this->session->set_flashdata('success', 'Produk berhasil diperbarui!');
        redirect('toko/produk');
    }

    public function produk_hapus($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $this->Produk_model->delete($id, $uid);

        $this->session->set_flashdata('success', 'Produk berhasil dihapus.');
        redirect('toko/produk');
    }

    // Hapus 1 foto dari galeri (AJAX-style redirect biasa)
    public function foto_hapus($id_foto) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $foto = $this->ProdukFoto_model->get_by_id($id_foto);
        if ($foto) {
            $produk = $this->Produk_model->get_by_id_and_seller($foto->id_produk, $uid);
            if ($produk) {
                $this->ProdukFoto_model->delete($id_foto, $foto->id_produk);
                $path = FCPATH . 'assets/uploads/produk/' . $foto->foto;
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }

        redirect('toko/produk/edit/' . ($foto->id_produk ?? ''));
    }

    private function _upload_foto_produk($field_name) {
        if (empty($_FILES[$field_name]['name'])) {
            return NULL;
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES[$field_name]['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $this->session->set_flashdata('error', 'Format foto harus JPG, PNG, atau WEBP.');
            return FALSE;
        }
        if ($_FILES[$field_name]['size'] > 2 * 1024 * 1024) {
            $this->session->set_flashdata('error', 'Ukuran foto maksimal 2MB.');
            return FALSE;
        }

        $foto_name = 'produk_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = FCPATH . 'assets/uploads/produk/' . $foto_name;

        if (!move_uploaded_file($_FILES[$field_name]['tmp_name'], $upload_path)) {
            $this->session->set_flashdata('error', 'Gagal mengunggah foto.');
            return FALSE;
        }

        return $foto_name;
    }

    // Galeri: maks 4 foto tambahan, field name="galeri[]" (multiple file input)
    private function _simpan_galeri_tambahan($id_produk) {
        if (empty($_FILES['galeri']['name'][0])) {
            return;
        }

        $existing_count = $this->ProdukFoto_model->count_by_produk($id_produk);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $urutan = $existing_count;

        foreach ($_FILES['galeri']['name'] as $i => $name) {
            if ($urutan >= 4 || empty($name)) {
                continue;
            }
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed)) {
                continue;
            }
            if ($_FILES['galeri']['size'][$i] > 2 * 1024 * 1024) {
                continue;
            }

            $foto_name = 'galeri_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = FCPATH . 'assets/uploads/produk/' . $foto_name;

            if (move_uploaded_file($_FILES['galeri']['tmp_name'][$i], $upload_path)) {
                $this->ProdukFoto_model->insert($id_produk, $foto_name, $urutan);
                $urutan++;
            }
        }
    }

    // Variasi (saat tambah produk baru): array nama_variasi[], stok_variasi[], harga_tambahan[]
    private function _simpan_variasi($id_produk) {
        $nama_variasi = $this->input->post('nama_variasi');
        if (empty($nama_variasi)) {
            return;
        }

        $stok_variasi  = $this->input->post('stok_variasi');
        $harga_tambahan = $this->input->post('harga_tambahan');

        foreach ($nama_variasi as $i => $nama) {
            $nama = trim($nama);
            if ($nama === '') {
                continue;
            }
            $this->ProdukVariasi_model->insert([
                'id_produk'      => $id_produk,
                'nama_variasi'   => $nama,
                'stok'           => (int) ($stok_variasi[$i] ?? 0),
                'harga_tambahan' => (int) ($harga_tambahan[$i] ?? 0),
            ]);
        }
    }

    // Variasi (saat edit): variasi_id[] (0 = baru) dipasangkan index-nya dengan nama_variasi[] dst.
    // Variasi yang dihapus di form dikirim terpisah via variasi_hapus[] (id yang dihapus)
    private function _kelola_variasi_update($id_produk) {
        $variasi_hapus = $this->input->post('variasi_hapus');
        if (!empty($variasi_hapus)) {
            foreach ($variasi_hapus as $id_v) {
                $this->ProdukVariasi_model->delete((int) $id_v, $id_produk);
            }
        }

        $variasi_id    = $this->input->post('variasi_id');
        $nama_variasi  = $this->input->post('nama_variasi');
        $stok_variasi  = $this->input->post('stok_variasi');
        $harga_tambahan = $this->input->post('harga_tambahan');

        if (empty($nama_variasi)) {
            return;
        }

        foreach ($nama_variasi as $i => $nama) {
            $nama = trim($nama);
            if ($nama === '') {
                continue;
            }
            $id_v = (int) ($variasi_id[$i] ?? 0);
            $payload = [
                'nama_variasi'   => $nama,
                'stok'           => (int) ($stok_variasi[$i] ?? 0),
                'harga_tambahan' => (int) ($harga_tambahan[$i] ?? 0),
            ];

            if ($id_v > 0) {
                $this->ProdukVariasi_model->update($id_v, $payload);
            } else {
                $payload['id_produk'] = $id_produk;
                $this->ProdukVariasi_model->insert($payload);
            }
        }
    }


    // ============ KELOLA ORDER (PENJUAL) ============
    public function order_list() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');
        $status = $this->input->get('status') ?: '';

        $data['title']         = 'Kelola Pesanan';
        $data['orders']        = $this->Order_model->get_orders_for_seller($uid, $status);
        $data['filter_status'] = $status;

        $this->render('toko/order_list', $data);
    }

    public function order_detail($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $order = $this->Order_model->get_detail_for_seller($id, $uid);
        if (!$order) {
            $this->session->set_flashdata('error', 'Pesanan tidak ditemukan.');
            redirect('toko/order');
        }

        $data['title'] = 'Detail Pesanan';
        $data['order'] = $order;
        $data['items'] = $this->Order_model->get_items($id);

        $this->render('toko/order_detail', $data);
    }

    public function order_update_status($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $status_baru = $this->input->post('status');
        $valid = ['pending', 'dikonfirmasi', 'diproses', 'selesai', 'dibatalkan'];

        if (in_array($status_baru, $valid)) {
            $order = $this->Order_model->get_detail_for_seller($id, $uid);

            if ($order && $order->status !== $status_baru) {
                $this->db->trans_start();

                $this->Order_model->update_status($id, $uid, $status_baru);

                if ($status_baru === 'selesai' && $order->status !== 'selesai') {
                    $items = $this->Order_model->get_items($id);
                    foreach ($items as $item) {
                        if ($item->id_produk) {
                            $this->Produk_model->tambah_terjual($item->id_produk, $item->qty);
                        }
                    }
                    // Reward poin: 1% dari total belanja (dibulatkan ke bawah)
                    $poin_reward = (int) floor($order->total * 0.01);
                    if ($poin_reward > 0) {
                        $this->User_model->tambah_poin($order->id_pembeli, $poin_reward);
                    }
                }

                if ($status_baru === 'dibatalkan' && $order->status !== 'dibatalkan') {
                    $this->_kembalikan_stok_order($id);
                }

                $this->db->trans_complete();

                $status_label = [
                    'dikonfirmasi' => 'Pembayaran kamu sudah diterima penjual.',
                    'diproses'     => 'Pesanan kamu sedang diproses penjual.',
                    'selesai'      => 'Pesanan kamu telah selesai. Jangan lupa beri ulasan!',
                    'dibatalkan'   => 'Pesanan kamu dibatalkan oleh penjual.',
                ];
                if (isset($status_label[$status_baru])) {
                    $this->Notifikasi_model->kirim(
                        $order->id_pembeli,
                        'order_update',
                        'Status Pesanan ' . $order->kode_order . ' Diperbarui',
                        $status_label[$status_baru],
                        'order/riwayat'
                    );
                }
            }

            $this->session->set_flashdata('success', 'Status pesanan berhasil diperbarui.');
        }

        redirect('toko/order/detail/' . $id);
    }

    private function _kembalikan_stok_order($id_order) {
        $items = $this->Order_model->get_items($id_order);
        foreach ($items as $item) {
            if ($item->id_variasi) {
                $this->ProdukVariasi_model->kurangi_stok($item->id_variasi, -$item->qty); // negatif = nambah balik
            } elseif ($item->id_produk) {
                $this->Produk_model->kurangi_stok($item->id_produk, -$item->qty);
            }
        }
    }

    // ============ LAPORAN / REKAP PENJUALAN ============
    public function laporan() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $dari   = $this->input->get('dari') ?: date('Y-m-01');
        $sampai = $this->input->get('sampai') ?: date('Y-m-d');

        $data['title']        = 'Laporan Penjualan';
        $data['dari']         = $dari;
        $data['sampai']       = $sampai;
        $data['orders']       = $this->Order_model->get_orders_by_range($uid, $dari, $sampai);
        $data['rekap_produk'] = $this->Order_model->get_rekap_produk_terjual($uid, $dari, $sampai);

        $total_omzet = 0;
        $total_selesai = 0;
        foreach ($data['orders'] as $o) {
            if ($o->status === 'selesai') {
                $total_omzet += $o->total;
                $total_selesai++;
            }
        }
        $data['total_omzet']   = $total_omzet;
        $data['total_selesai'] = $total_selesai;
        $data['total_order']   = count($data['orders']);

        $this->render('toko/laporan', $data);
    }

    public function laporan_export() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $dari   = $this->input->get('dari') ?: date('Y-m-01');
        $sampai = $this->input->get('sampai') ?: date('Y-m-d');

        $orders = $this->Order_model->get_orders_by_range($uid, $dari, $sampai);

        $filename = 'laporan_penjualan_' . $dari . '_sd_' . $sampai . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM agar Excel membaca UTF-8 dengan benar
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, ['Kode Order', 'Pembeli', 'Subtotal', 'Ongkir', 'Total', 'Status', 'Tanggal']);

        foreach ($orders as $o) {
            fputcsv($output, [
                $o->kode_order,
                $o->nama_pembeli,
                $o->subtotal,
                $o->ongkir,
                $o->total,
                $o->status,
                date('Y-m-d H:i', strtotime($o->created_at)),
            ]);
        }

        fclose($output);
        exit;
    }

    // ============ ZONA ONGKIR (per penjual) ============
    public function zona_list() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $data['title'] = 'Zona Ongkir';
        $data['zona']  = $this->Zona_model->get_by_seller($uid);

        $this->render('toko/zona_list', $data);
    }

    public function zona_tambah() {
        $this->require_seller();

        $this->form_validation->set_rules('area_name', 'Nama Area', 'required|trim');
        $this->form_validation->set_rules('fee', 'Ongkir', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('toko/zona');
        }

        $this->Zona_model->insert([
            'id_penjual' => $this->session->userdata('user_id'),
            'area_name'  => $this->input->post('area_name', TRUE),
            'fee'        => $this->input->post('fee', TRUE),
        ]);

        $this->session->set_flashdata('success', 'Zona ongkir berhasil ditambahkan!');
        redirect('toko/zona');
    }

    public function zona_update($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $this->form_validation->set_rules('area_name', 'Nama Area', 'required|trim');
        $this->form_validation->set_rules('fee', 'Ongkir', 'required|integer|greater_than_equal_to[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('toko/zona');
        }

        $this->Zona_model->update($id, $uid, [
            'area_name' => $this->input->post('area_name', TRUE),
            'fee'       => $this->input->post('fee', TRUE),
        ]);

        $this->session->set_flashdata('success', 'Zona ongkir berhasil diperbarui!');
        redirect('toko/zona');
    }

    public function zona_hapus($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        if ($this->Zona_model->count_by_seller($uid) <= 1) {
            $this->session->set_flashdata('error', 'Minimal harus ada 1 zona ongkir agar toko bisa menerima pesanan.');
            redirect('toko/zona');
        }

        $this->Zona_model->delete($id, $uid);
        $this->session->set_flashdata('success', 'Zona ongkir berhasil dihapus.');
        redirect('toko/zona');
    }

    // ============ VOUCHER TOKO ============
    public function voucher_list() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $this->load->model('Voucher_model');

        $data['title']    = 'Voucher Toko';
        $data['vouchers'] = $this->Voucher_model->get_by_seller($uid);

        $this->render('toko/voucher_list', $data);
    }

    public function voucher_tambah() {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');

        $this->load->model('Voucher_model');

        $this->form_validation->set_rules('kode', 'Kode Voucher', 'required|trim|is_unique[vouchers.kode]');
        $this->form_validation->set_rules('tipe', 'Tipe', 'required|in_list[persen,nominal]');
        $this->form_validation->set_rules('nilai', 'Nilai', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('toko/voucher');
        }

        $this->Voucher_model->insert([
            'id_penjual'     => $uid,
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

        $this->session->set_flashdata('success', 'Voucher berhasil dibuat!');
        redirect('toko/voucher');
    }

    public function voucher_nonaktifkan($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');
        $this->load->model('Voucher_model');

        $this->Voucher_model->update($id, $uid, ['status' => 'nonaktif']);
        $this->session->set_flashdata('success', 'Voucher dinonaktifkan.');
        redirect('toko/voucher');
    }

    public function voucher_hapus($id) {
        $this->require_seller();
        $uid = $this->session->userdata('user_id');
        $this->load->model('Voucher_model');

        $this->Voucher_model->delete($id, $uid);
        $this->session->set_flashdata('success', 'Voucher dihapus.');
        redirect('toko/voucher');
    }
}
