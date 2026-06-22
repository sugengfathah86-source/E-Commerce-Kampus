<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Order extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Keranjang_model', 'Order_model', 'Zona_model', 'User_model', 'Produk_model', 'ProdukVariasi_model', 'Voucher_model', 'Notifikasi_model']);
        $this->require_login();
    }

    public function checkout($id_penjual) {
        $uid = $this->session->userdata('user_id');
        $items = $this->Keranjang_model->get_items_by_seller($uid, $id_penjual);

        if (empty($items)) {
            $this->session->set_flashdata('error', 'Tidak ada item untuk checkout.');
            redirect('keranjang');
        }

        $subtotal = 0;
        $ada_preorder = false;
        foreach ($items as $item) {
            $harga_satuan = $item->harga_dasar + ($item->harga_tambahan ?? 0);
            $subtotal += $harga_satuan * $item->qty;
            if ($item->is_preorder) {
                $ada_preorder = true;
            }
        }

        $penjual = $this->User_model->get_by_id($id_penjual);
        $pembeli = $this->User_model->get_by_id($uid);

        $data['title']        = 'Checkout';
        $data['items']        = $items;
        $data['subtotal']     = $subtotal;
        $data['penjual']      = $penjual;
        $data['pembeli']      = $pembeli;
        $data['id_penjual']   = $id_penjual;
        $data['zona_list']    = $this->Zona_model->get_by_seller($id_penjual);
        $data['ada_preorder'] = $ada_preorder;

        $this->render('order/checkout', $data);
    }

    // Cek voucher via AJAX-style form post sebelum submit order final (validasi + hitung potongan)
    public function cek_voucher() {
        $kode = trim($this->input->post('kode_voucher', TRUE));
        $id_penjual = (int) $this->input->post('id_penjual');
        $subtotal = (float) $this->input->post('subtotal');
        $uid = $this->session->userdata('user_id');

        $hasil = $this->Voucher_model->validasi($kode, $uid, $id_penjual, $subtotal);

        if (!$hasil['valid']) {
            $this->session->set_flashdata('error', $hasil['pesan']);
        } else {
            $this->session->set_flashdata('voucher_kode', $kode);
            $this->session->set_flashdata('success', 'Voucher berhasil dipakai! Potongan: ' . $this->rupiah($hasil['potongan']));
        }

        redirect('order/checkout/' . $id_penjual);
    }

    public function simpan() {
        $uid = $this->session->userdata('user_id');
        $id_penjual = (int) $this->input->post('id_penjual');

        $this->form_validation->set_rules('id_zona', 'Zona Pengiriman', 'required|integer');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('order/checkout/' . $id_penjual);
        }

        $items = $this->Keranjang_model->get_items_by_seller($uid, $id_penjual);
        if (empty($items)) {
            $this->session->set_flashdata('error', 'Keranjang kosong.');
            redirect('keranjang');
        }

        // Validasi ulang stok sebelum diproses (mencegah overselling jika ada pembeli lain checkout bersamaan)
        foreach ($items as $item) {
            $produk_terkini = $this->Produk_model->get_by_id($item->produk_id);
            if (!$produk_terkini || $produk_terkini->status !== 'aktif') {
                $this->session->set_flashdata('error', "Produk \"{$item->nama_barang}\" sudah tidak tersedia.");
                redirect('keranjang');
            }
            if (!$produk_terkini->is_preorder) {
                $batas = $item->id_variasi
                    ? $this->ProdukVariasi_model->get_by_id($item->id_variasi)->stok
                    : $produk_terkini->stok;
                if ($batas < $item->qty) {
                    $this->session->set_flashdata('error', "Stok \"{$item->nama_barang}\" tidak cukup. Sisa stok: {$batas}.");
                    redirect('keranjang');
                }
            }
        }

        $id_zona = (int) $this->input->post('id_zona');
        $zona = $this->Zona_model->get_by_id_and_seller($id_zona, $id_penjual);

        if (!$zona) {
            $this->session->set_flashdata('error', 'Zona pengiriman tidak valid untuk toko ini.');
            redirect('order/checkout/' . $id_penjual);
        }

        $subtotal = 0;
        $is_preorder_order = false;
        foreach ($items as $item) {
            $harga_satuan = $item->harga_dasar + ($item->harga_tambahan ?? 0);
            $subtotal += $harga_satuan * $item->qty;
            if ($item->is_preorder) {
                $is_preorder_order = true;
            }
        }
        $ongkir = $zona->fee;

        // Voucher (opsional)
        $kode_voucher = trim($this->input->post('kode_voucher', TRUE));
        $id_voucher = null;
        $diskon_voucher = 0;
        if ($kode_voucher !== '') {
            $hasil_voucher = $this->Voucher_model->validasi($kode_voucher, $uid, $id_penjual, $subtotal);
            if ($hasil_voucher['valid']) {
                $id_voucher = $hasil_voucher['voucher']->id;
                $diskon_voucher = $hasil_voucher['potongan'];
            }
        }

        // Poin (opsional) — 1 poin = Rp 1, maksimal dipakai sebesar subtotal setelah diskon voucher
        $poin_dipakai = max(0, (int) $this->input->post('poin_dipakai'));
        $pembeli = $this->User_model->get_by_id($uid);
        $poin_dipakai = min($poin_dipakai, $pembeli->poin, $subtotal - $diskon_voucher);

        $total = $subtotal + $ongkir - $diskon_voucher - $poin_dipakai;
        $total = max(0, $total);

        $kode_order = $this->Order_model->generate_kode();
        $batas_bayar = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $this->db->trans_start();

        $order_id = $this->Order_model->insert_order([
            'kode_order'     => $kode_order,
            'id_pembeli'     => $uid,
            'id_penjual'     => $id_penjual,
            'id_zona'        => $id_zona,
            'id_voucher'     => $id_voucher,
            'subtotal'       => $subtotal,
            'ongkir'         => $ongkir,
            'diskon_voucher' => $diskon_voucher,
            'poin_dipakai'   => $poin_dipakai,
            'total'          => $total,
            'alamat'         => $this->input->post('alamat', TRUE),
            'is_preorder'    => $is_preorder_order ? 1 : 0,
            'status'         => 'pending',
            'batas_bayar'    => $is_preorder_order ? NULL : $batas_bayar, // pre-order tidak auto-cancel
        ]);

        $cart_ids = [];
        foreach ($items as $item) {
            $harga_satuan = $item->harga_dasar + ($item->harga_tambahan ?? 0);
            $item_subtotal = $harga_satuan * $item->qty;

            $this->Order_model->insert_item([
                'id_order'     => $order_id,
                'id_produk'    => $item->produk_id,
                'id_variasi'   => $item->id_variasi,
                'nama_barang'  => $item->nama_barang,
                'nama_variasi' => $item->nama_variasi,
                'harga'        => $harga_satuan,
                'qty'          => $item->qty,
                'subtotal'     => $item_subtotal,
            ]);

            if ($item->id_variasi) {
                $this->ProdukVariasi_model->kurangi_stok($item->id_variasi, $item->qty);
            } else {
                $this->Produk_model->kurangi_stok($item->produk_id, $item->qty);
            }

            $cart_ids[] = $item->cart_id;
        }

        if ($id_voucher) {
            $this->Voucher_model->catat_pemakaian($id_voucher, $uid, $order_id);
        }
        if ($poin_dipakai > 0) {
            $this->User_model->kurangi_poin($uid, $poin_dipakai);
        }

        $this->Keranjang_model->delete_items($cart_ids);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->session->set_flashdata('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
            redirect('keranjang');
        }

        $this->Notifikasi_model->kirim(
            $id_penjual,
            'order_baru',
            'Pesanan Baru Masuk!',
            "Order {$kode_order} senilai " . $this->rupiah($total) . " menunggu konfirmasi kamu.",
            'toko/order/detail/' . $order_id
        );

        redirect('order/sukses/' . $order_id);
    }

    public function sukses($id) {
        $uid = $this->session->userdata('user_id');
        $order = $this->Order_model->get_detail_for_buyer($id, $uid);

        if (!$order) {
            redirect('produk');
        }

        $items = $this->Order_model->get_items($id);

        // Generate pesan WhatsApp otomatis
        $pesan = "Halo, ada orderan baru!\n\n";
        $pesan .= "Kode Order: {$order->kode_order}\n";
        $pesan .= "Nama Pembeli: " . $this->session->userdata('nama') . "\n\n";
        $pesan .= "Detail Barang:\n";
        foreach ($items as $item) {
            $pesan .= "- {$item->nama_barang} x{$item->qty} = Rp " . number_format($item->subtotal, 0, ',', '.') . "\n";
        }
        $pesan .= "\nSubtotal: Rp " . number_format($order->subtotal, 0, ',', '.') . "\n";
        $pesan .= "Ongkir ({$order->area_name}): Rp " . number_format($order->ongkir, 0, ',', '.') . "\n";
        $pesan .= "Total: Rp " . number_format($order->total, 0, ',', '.') . "\n\n";
        $pesan .= "Alamat: {$order->alamat}\n\n";
        $pesan .= "Mohon konfirmasi pesanan saya. Terima kasih!";

        $wa_number = preg_replace('/^0/', '62', $order->no_wa ?? '');
        $wa_link = "https://wa.me/{$wa_number}?text=" . urlencode($pesan);

        $data['title']   = 'Pesanan Berhasil';
        $data['order']   = $order;
        $data['items']   = $items;
        $data['wa_link'] = $wa_link;

        $this->render('order/sukses', $data);
    }

    public function upload_bukti() {
        $uid = $this->session->userdata('user_id');
        $order_id = (int) $this->input->post('order_id');

        $order = $this->Order_model->get_detail_for_buyer($order_id, $uid);
        if (!$order) {
            $this->session->set_flashdata('error', 'Order tidak ditemukan.');
            redirect('order/riwayat');
        }

        if (empty($_FILES['bukti_bayar']['name'])) {
            $this->session->set_flashdata('error', 'Pilih file bukti bayar terlebih dahulu.');
            redirect('order/sukses/' . $order_id);
        }

        $allowed = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $this->session->set_flashdata('error', 'Format file harus JPG atau PNG.');
            redirect('order/sukses/' . $order_id);
        }
        if ($_FILES['bukti_bayar']['size'] > 2 * 1024 * 1024) {
            $this->session->set_flashdata('error', 'Ukuran file maksimal 2MB.');
            redirect('order/sukses/' . $order_id);
        }

        $file_name = 'bukti_' . $order->kode_order . '_' . time() . '.' . $ext;
        $upload_path = FCPATH . 'assets/uploads/bukti_bayar/' . $file_name;

        if (move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $upload_path)) {
            $this->Order_model->update_bukti_bayar($order_id, $uid, $file_name);
            $this->session->set_flashdata('success', 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi penjual.');
        } else {
            $this->session->set_flashdata('error', 'Gagal mengunggah file. Coba lagi.');
        }

        redirect('order/riwayat');
    }

    public function riwayat() {
        $uid = $this->session->userdata('user_id');

        $this->load->model('Komplain_model');

        $data['title']  = 'Pesanan Saya';
        $data['orders'] = $this->Order_model->get_riwayat_pembeli($uid);

        // Tandai order mana yang sudah dikomplain (supaya tombol komplain tidak muncul dobel)
        $sudah_komplain = [];
        foreach ($data['orders'] as $o) {
            $sudah_komplain[$o->id] = $this->Komplain_model->sudah_komplain($o->id);
        }
        $data['sudah_komplain'] = $sudah_komplain;

        $this->render('order/riwayat', $data);
    }

    // Halaman cetak/PDF riwayat transaksi — dirender sebagai HTML rapi, pembeli tinggal
    // pakai Ctrl+P / "Simpan sebagai PDF" dari browser (tidak butuh library PDF eksternal)
    public function riwayat_cetak() {
        $uid = $this->session->userdata('user_id');

        $dari   = $this->input->get('dari') ?: date('Y-m-01');
        $sampai = $this->input->get('sampai') ?: date('Y-m-d');

        $orders = $this->Order_model->get_riwayat_pembeli_by_range($uid, $dari, $sampai);

        $total_belanja = 0;
        foreach ($orders as $o) {
            if ($o->status === 'selesai') {
                $total_belanja += $o->total;
            }
        }

        $data['nama_pembeli']  = $this->session->userdata('nama');
        $data['orders']        = $orders;
        $data['dari']          = $dari;
        $data['sampai']        = $sampai;
        $data['total_belanja'] = $total_belanja;

        $this->load->view('order/riwayat_cetak', $data);
    }

    // ============ KOMPLAIN ============
    public function komplain_form($id_order) {
        $uid = $this->session->userdata('user_id');
        $this->load->model('Komplain_model');

        $order = $this->Order_model->get_detail_for_buyer($id_order, $uid);
        if (!$order) {
            redirect('order/riwayat');
        }
        if ($this->Komplain_model->sudah_komplain($id_order)) {
            $this->session->set_flashdata('error', 'Kamu sudah mengajukan komplain untuk order ini.');
            redirect('order/riwayat');
        }

        $data['title'] = 'Ajukan Komplain';
        $data['order'] = $order;

        $this->render('order/komplain_form', $data);
    }

    public function komplain_simpan() {
        $uid = $this->session->userdata('user_id');
        $id_order = (int) $this->input->post('id_order');
        $this->load->model('Komplain_model');

        $this->form_validation->set_rules('alasan', 'Alasan', 'required|trim');
        $this->form_validation->set_rules('deskripsi', 'Deskripsi', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('order/komplain/form/' . $id_order);
        }

        $order = $this->Order_model->get_detail_for_buyer($id_order, $uid);
        if (!$order) {
            redirect('order/riwayat');
        }

        $foto_name = null;
        if (!empty($_FILES['foto']['name'])) {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png']) && $_FILES['foto']['size'] <= 2 * 1024 * 1024) {
                $foto_name = 'komplain_' . time() . '_' . uniqid() . '.' . $ext;
                @mkdir(FCPATH . 'assets/uploads/komplain', 0755, true);
                move_uploaded_file($_FILES['foto']['tmp_name'], FCPATH . 'assets/uploads/komplain/' . $foto_name);
            }
        }

        $this->Komplain_model->insert([
            'id_order'   => $id_order,
            'id_pembeli' => $uid,
            'alasan'     => $this->input->post('alasan', TRUE),
            'deskripsi'  => $this->input->post('deskripsi', TRUE),
            'foto'       => $foto_name,
        ]);

        $this->session->set_flashdata('success', 'Komplain berhasil diajukan. Tim kami akan meninjau dalam 1-2 hari kerja.');
        redirect('order/riwayat');
    }

    // ============ ULASAN / RATING ============
    public function ulasan_form($id_order) {
        $uid = $this->session->userdata('user_id');
        $this->load->model('Ulasan_model');

        $order = $this->Order_model->get_detail_for_buyer($id_order, $uid);
        if (!$order || $order->status !== 'selesai') {
            $this->session->set_flashdata('error', 'Ulasan hanya bisa diberikan untuk pesanan yang sudah selesai.');
            redirect('order/riwayat');
        }

        $items = $this->Order_model->get_items($id_order);
        $belum_diulas = [];
        foreach ($items as $item) {
            if ($item->id_produk && !$this->Ulasan_model->sudah_diulas($id_order, $item->id_produk)) {
                $belum_diulas[] = $item;
            }
        }

        if (empty($belum_diulas)) {
            $this->session->set_flashdata('success', 'Semua produk di pesanan ini sudah diberi ulasan.');
            redirect('order/riwayat');
        }

        $data['title'] = 'Beri Ulasan';
        $data['order'] = $order;
        $data['items'] = $belum_diulas;

        $this->render('order/ulasan_form', $data);
    }

    public function ulasan_simpan() {
        $uid = $this->session->userdata('user_id');
        $id_order = (int) $this->input->post('id_order');
        $this->load->model('Ulasan_model');

        $order = $this->Order_model->get_detail_for_buyer($id_order, $uid);
        if (!$order || $order->status !== 'selesai') {
            redirect('order/riwayat');
        }

        $produk_ids = $this->input->post('id_produk');
        $ratings    = $this->input->post('rating');
        $komentars  = $this->input->post('komentar');

        if (empty($produk_ids)) {
            redirect('order/riwayat');
        }

        foreach ($produk_ids as $i => $id_produk) {
            $rating = (int) ($ratings[$i] ?? 0);
            if ($rating < 1 || $rating > 5) {
                continue;
            }
            if ($this->Ulasan_model->sudah_diulas($id_order, $id_produk)) {
                continue;
            }

            $this->Ulasan_model->insert([
                'id_order'   => $id_order,
                'id_produk'  => $id_produk,
                'id_pembeli' => $uid,
                'rating'     => $rating,
                'komentar'   => $komentars[$i] ?? '',
            ]);
        }

        $this->session->set_flashdata('success', 'Terima kasih atas ulasanmu!');
        redirect('order/riwayat');
    }
}
