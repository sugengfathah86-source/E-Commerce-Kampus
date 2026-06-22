<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'Produk';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Profil
$route['profil'] = 'Profil/index';
$route['profil/update'] = 'Profil/update';

// Wishlist
$route['wishlist'] = 'Wishlist/index';
$route['wishlist/toggle/(:num)'] = 'Wishlist/toggle/$1';

// Notifikasi
$route['notifikasi'] = 'Notifikasi/index';
$route['notifikasi/buka/(:num)'] = 'Notifikasi/buka/$1';

// Chat
$route['chat'] = 'Chat/index';
$route['chat/mulai/(:num)'] = 'Chat/mulai/$1';
$route['chat/room/(:num)'] = 'Chat/room/$1';
$route['chat/kirim'] = 'Chat/kirim';

// Konsinyasi / Titip Jual
$route['konsinyasi'] = 'Konsinyasi/index';
$route['konsinyasi/ajukan'] = 'Konsinyasi/ajukan';
$route['konsinyasi/simpan'] = 'Konsinyasi/simpan';
$route['konsinyasi/kelola'] = 'Konsinyasi/kelola';
$route['konsinyasi/detail/(:num)'] = 'Konsinyasi/detail/$1';
$route['konsinyasi/terima/(:num)'] = 'Konsinyasi/terima/$1';
$route['konsinyasi/tolak/(:num)'] = 'Konsinyasi/tolak/$1';

// Toko Publik & Follow
$route['toko-publik/(:num)'] = 'TokoPublik/index/$1';
$route['toko-publik/(:num)/follow'] = 'TokoPublik/follow/$1';
$route['mengikuti'] = 'TokoPublik/following';

// Admin Panel
$route['admin'] = 'Admin/dashboard';
$route['admin/dashboard'] = 'Admin/dashboard';
$route['admin/produk-pending'] = 'Admin/produk_pending';
$route['admin/produk/approve/(:num)'] = 'Admin/produk_approve/$1';
$route['admin/produk/reject/(:num)'] = 'Admin/produk_reject/$1';
$route['admin/users'] = 'Admin/users';
$route['admin/users/suspend/(:num)'] = 'Admin/user_suspend/$1';
$route['admin/users/aktifkan/(:num)'] = 'Admin/user_aktifkan/$1';
$route['admin/users/verifikasi/(:num)'] = 'Admin/user_verifikasi/$1';
$route['admin/users/batal-verifikasi/(:num)'] = 'Admin/user_batal_verifikasi/$1';
$route['admin/voucher'] = 'Admin/voucher_list';
$route['admin/voucher/tambah'] = 'Admin/voucher_tambah';
$route['admin/voucher/nonaktifkan/(:num)'] = 'Admin/voucher_nonaktifkan/$1';
$route['admin/komplain'] = 'Admin/komplain_list';
$route['admin/komplain/detail/(:num)'] = 'Admin/komplain_detail/$1';
$route['admin/komplain/tanggapi/(:num)'] = 'Admin/komplain_tanggapi/$1';

// Auth & Google OAuth
$route['login'] = 'Auth/login';
$route['logout'] = 'Auth/logout';
$route['register'] = 'Auth/register';
$route['auth/login_process'] = 'Auth/login_process';
$route['auth/register_process'] = 'Auth/register_process';
$route['auth/google'] = 'Auth/google_login';
$route['auth/google/callback'] = 'Auth/google_callback';

// Produk (halaman utama / marketplace)
$route['produk'] = 'Produk/index';
$route['produk/detail/(:num)'] = 'Produk/detail/$1';

// Toko (penjual)
$route['toko/buka'] = 'Toko/buka_toko';
$route['toko/dashboard'] = 'Toko/dashboard';
$route['toko/produk'] = 'Toko/produk_list';
$route['toko/produk/tambah'] = 'Toko/produk_tambah';
$route['toko/produk/simpan'] = 'Toko/produk_simpan';
$route['toko/produk/edit/(:num)'] = 'Toko/produk_edit/$1';
$route['toko/produk/update/(:num)'] = 'Toko/produk_update/$1';
$route['toko/produk/hapus/(:num)'] = 'Toko/produk_hapus/$1';
$route['toko/produk/foto/hapus/(:num)'] = 'Toko/foto_hapus/$1';
$route['toko/order'] = 'Toko/order_list';
$route['toko/order/detail/(:num)'] = 'Toko/order_detail/$1';
$route['toko/order/update_status/(:num)'] = 'Toko/order_update_status/$1';
$route['toko/laporan'] = 'Toko/laporan';
$route['toko/laporan/export'] = 'Toko/laporan_export';
$route['toko/zona'] = 'Toko/zona_list';
$route['toko/zona/tambah'] = 'Toko/zona_tambah';
$route['toko/zona/update/(:num)'] = 'Toko/zona_update/$1';
$route['toko/zona/hapus/(:num)'] = 'Toko/zona_hapus/$1';
$route['toko/voucher'] = 'Toko/voucher_list';
$route['toko/voucher/tambah'] = 'Toko/voucher_tambah';
$route['toko/voucher/nonaktifkan/(:num)'] = 'Toko/voucher_nonaktifkan/$1';
$route['toko/voucher/hapus/(:num)'] = 'Toko/voucher_hapus/$1';

// Keranjang
$route['keranjang'] = 'Keranjang/index';
$route['keranjang/tambah'] = 'Keranjang/tambah';
$route['keranjang/update'] = 'Keranjang/update';
$route['keranjang/hapus/(:num)'] = 'Keranjang/hapus/$1';

// Order (pembeli)
$route['order/checkout/(:num)'] = 'Order/checkout/$1';
$route['order/cek_voucher'] = 'Order/cek_voucher';
$route['order/simpan'] = 'Order/simpan';
$route['order/sukses/(:num)'] = 'Order/sukses/$1';
$route['order/upload_bukti'] = 'Order/upload_bukti';
$route['order/riwayat'] = 'Order/riwayat';
$route['order/riwayat/cetak'] = 'Order/riwayat_cetak';
$route['order/komplain/form/(:num)'] = 'Order/komplain_form/$1';
$route['order/komplain/simpan'] = 'Order/komplain_simpan';
$route['order/ulasan/form/(:num)'] = 'Order/ulasan_form/$1';
$route['order/ulasan/simpan'] = 'Order/ulasan_simpan';
