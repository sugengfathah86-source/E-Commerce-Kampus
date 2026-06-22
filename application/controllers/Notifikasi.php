<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Notifikasi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Notifikasi_model');
        $this->require_login();
    }

    public function index() {
        $uid = $this->session->userdata('user_id');
        $this->Notifikasi_model->tandai_semua_dibaca($uid);

        $data['title']        = 'Notifikasi';
        $data['notifikasi']   = $this->Notifikasi_model->get_by_user($uid, 50);

        $this->render('notifikasi/index', $data);
    }

    public function buka($id) {
        $uid = $this->session->userdata('user_id');
        $this->load->model('Notifikasi_model');

        $notif = $this->db->where('id', $id)->where('id_user', $uid)->get('notifikasi')->row();
        $this->Notifikasi_model->tandai_dibaca($id, $uid);

        redirect($notif && $notif->link ? $notif->link : 'notifikasi');
    }
}
