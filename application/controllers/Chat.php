<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Chat extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Chat_model', 'User_model', 'Notifikasi_model']);
        $this->require_login();
    }

    // Daftar percakapan — otomatis tampilkan dari sisi pembeli & penjual (jika dual-role)
    public function index() {
        $uid = $this->session->userdata('user_id');
        $is_seller = $this->session->userdata('role') == 1;

        $data['title'] = 'Pesan';
        $data['rooms_pembeli'] = $this->Chat_model->get_rooms_for_pembeli($uid);
        $data['rooms_penjual'] = $is_seller ? $this->Chat_model->get_rooms_for_penjual($uid) : [];

        $this->render('chat/index', $data);
    }

    // Mulai/lanjutkan chat dengan penjual tertentu (dipanggil dari halaman produk/toko publik)
    public function mulai($id_penjual) {
        $uid = $this->session->userdata('user_id');

        if ($uid == $id_penjual) {
            redirect('chat');
        }

        $room = $this->Chat_model->get_or_create_room($uid, $id_penjual);
        redirect('chat/room/' . $room->id);
    }

    public function room($id_room) {
        $uid = $this->session->userdata('user_id');

        if (!$this->Chat_model->room_milik_user($id_room, $uid)) {
            $this->session->set_flashdata('error', 'Kamu tidak punya akses ke percakapan ini.');
            redirect('chat');
        }

        $room = $this->Chat_model->get_room_by_id($id_room);
        $this->Chat_model->tandai_dibaca($id_room, $uid);

        // Tentukan siapa lawan bicara
        $id_lawan = ($room->id_pembeli == $uid) ? $room->id_penjual : $room->id_pembeli;
        $lawan = $this->User_model->get_by_id($id_lawan);

        $data['title']    = 'Chat dengan ' . ($lawan->nama_toko ?: $lawan->nama);
        $data['room']     = $room;
        $data['lawan']    = $lawan;
        $data['messages'] = $this->Chat_model->get_messages($id_room);

        $this->render('chat/room', $data);
    }

    public function kirim() {
        $id_room = (int) $this->input->post('id_room');
        $pesan = trim($this->input->post('pesan', TRUE));
        $uid = $this->session->userdata('user_id');

        if ($pesan === '' || !$this->Chat_model->room_milik_user($id_room, $uid)) {
            redirect('chat');
        }

        $this->Chat_model->kirim_pesan($id_room, $uid, $pesan);

        $room = $this->Chat_model->get_room_by_id($id_room);
        $id_penerima = ($room->id_pembeli == $uid) ? $room->id_penjual : $room->id_pembeli;

        $this->Notifikasi_model->kirim(
            $id_penerima,
            'chat',
            'Pesan Baru',
            $this->session->userdata('nama') . ' mengirim pesan baru.',
            'chat/room/' . $id_room
        );

        redirect('chat/room/' . $id_room);
    }
}
