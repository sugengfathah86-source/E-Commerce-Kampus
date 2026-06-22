<?php
defined('BASEPATH') OR exit('No direct script access allowed');

#[\AllowDynamicProperties]
class Profil extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->require_login();
    }

    public function index() {
        $uid = $this->session->userdata('user_id');
        $user = $this->User_model->get_by_id($uid);

        $data['title'] = 'Profil Saya';
        $data['user']  = $user;

        $this->render('profil/index', $data);
    }

    public function update() {
        $uid = $this->session->userdata('user_id');
        $is_seller = $this->session->userdata('role') == 1;

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');

        if ($is_seller) {
            $this->form_validation->set_rules('nama_toko', 'Nama Toko', 'required|trim');
            $this->form_validation->set_rules('no_wa', 'Nomor WhatsApp', 'required|trim|regex_match[/^8[0-9]{8,13}$/]',
                ['regex_match' => 'Nomor WhatsApp tidak valid. Masukkan tanpa awalan 0/+62, contoh: 81234567890.']);
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('profil');
        }

        $update_data = [
            'nama'           => $this->input->post('nama', TRUE),
            'bio'            => $this->input->post('bio', TRUE),
            'alamat_default' => $this->input->post('alamat_default', TRUE),
            'fakultas'       => $this->input->post('fakultas', TRUE),
            'jurusan'        => $this->input->post('jurusan', TRUE),
        ];

        if ($is_seller) {
            $update_data['nama_toko'] = $this->input->post('nama_toko', TRUE);
            $update_data['no_wa']     = $this->input->post('no_wa', TRUE);

            $jam_buka  = $this->input->post('jam_buka', TRUE);
            $jam_tutup = $this->input->post('jam_tutup', TRUE);
            $update_data['jam_buka']   = $jam_buka ?: NULL;
            $update_data['jam_tutup']  = $jam_tutup ?: NULL;
            $update_data['toko_libur'] = $this->input->post('toko_libur') ? 1 : 0;
        }

        $this->User_model->update_profile($uid, $update_data);

        // Update session juga supaya langsung terlihat
        $this->session->set_userdata('nama', $update_data['nama']);

        $this->session->set_flashdata('success', 'Profil berhasil diperbarui!');
        redirect('profil');
    }
}
