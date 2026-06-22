<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Chat_model extends CI_Model {

    public function get_or_create_room($id_pembeli, $id_penjual) {
        $room = $this->db->where('id_pembeli', $id_pembeli)->where('id_penjual', $id_penjual)->get('chat_rooms')->row();
        if ($room) {
            return $room;
        }
        $this->db->insert('chat_rooms', ['id_pembeli' => $id_pembeli, 'id_penjual' => $id_penjual]);
        $id = $this->db->insert_id();
        return $this->db->where('id', $id)->get('chat_rooms')->row();
    }

    public function get_room_by_id($id) {
        return $this->db->where('id', $id)->get('chat_rooms')->row();
    }

    // Daftar percakapan untuk pembeli (lawan bicara = penjual)
    public function get_rooms_for_pembeli($id_pembeli) {
        return $this->db->select('chat_rooms.*, users.nama as nama_lawan, users.nama_toko, users.foto_profil as foto_lawan')
                        ->from('chat_rooms')
                        ->join('users', 'users.id = chat_rooms.id_penjual')
                        ->where('chat_rooms.id_pembeli', $id_pembeli)
                        ->order_by('chat_rooms.last_message_at', 'DESC')
                        ->get()
                        ->result();
    }

    // Daftar percakapan untuk penjual (lawan bicara = pembeli)
    public function get_rooms_for_penjual($id_penjual) {
        return $this->db->select('chat_rooms.*, users.nama as nama_lawan, users.foto_profil as foto_lawan')
                        ->from('chat_rooms')
                        ->join('users', 'users.id = chat_rooms.id_pembeli')
                        ->where('chat_rooms.id_penjual', $id_penjual)
                        ->order_by('chat_rooms.last_message_at', 'DESC')
                        ->get()
                        ->result();
    }

    public function get_messages($id_room) {
        return $this->db->select('chat_messages.*, users.nama as nama_sender')
                        ->from('chat_messages')
                        ->join('users', 'users.id = chat_messages.id_sender')
                        ->where('id_room', $id_room)
                        ->order_by('chat_messages.created_at', 'ASC')
                        ->get()
                        ->result();
    }

    public function kirim_pesan($id_room, $id_sender, $pesan) {
        $this->db->insert('chat_messages', [
            'id_room'   => $id_room,
            'id_sender' => $id_sender,
            'pesan'     => $pesan,
        ]);
        $this->db->where('id', $id_room)->update('chat_rooms', ['last_message_at' => date('Y-m-d H:i:s')]);
        return $this->db->insert_id();
    }

    public function tandai_dibaca($id_room, $id_user) {
        // Tandai semua pesan di room ini sebagai dibaca, kecuali yang dikirim oleh user ini sendiri
        $this->db->where('id_room', $id_room)->where('id_sender !=', $id_user)->update('chat_messages', ['is_read' => 1]);
    }

    public function count_unread_total($id_user, $is_penjual) {
        // Hitung pesan belum dibaca di semua room milik user ini
        $kolom_room = $is_penjual ? 'id_penjual' : 'id_pembeli';
        return $this->db->select('COUNT(chat_messages.id) as total')
                        ->from('chat_messages')
                        ->join('chat_rooms', 'chat_rooms.id = chat_messages.id_room')
                        ->where("chat_rooms.$kolom_room", $id_user)
                        ->where('chat_messages.id_sender !=', $id_user)
                        ->where('chat_messages.is_read', 0)
                        ->get()
                        ->row()
                        ->total ?? 0;
    }

    public function room_milik_user($id_room, $id_user) {
        $room = $this->get_room_by_id($id_room);
        return $room && ($room->id_pembeli == $id_user || $room->id_penjual == $id_user);
    }
}
