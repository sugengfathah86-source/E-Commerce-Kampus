<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('rupiah')) {
    function rupiah($angka) {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('tgl_indo')) {
    function tgl_indo($tanggal) {
        if (!$tanggal) return '-';
        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $d = date('j', strtotime($tanggal));
        $m = date('n', strtotime($tanggal));
        $y = date('Y', strtotime($tanggal));
        return "$d {$bulan[$m]} $y";
    }
}
