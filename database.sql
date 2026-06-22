-- =============================================
-- ECOMMERCE KAMPUS - DATABASE LENGKAP
-- Versi: Bersih & Siap Pakai
-- =============================================

DROP DATABASE IF EXISTS ecommerce_kampus;
CREATE DATABASE ecommerce_kampus CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE ecommerce_kampus;

-- =============================================
-- 1. USERS
-- =============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(100) UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    nama VARCHAR(150) NOT NULL,
    foto_profil VARCHAR(255) DEFAULT NULL,
    password VARCHAR(255) DEFAULT NULL,
    no_wa VARCHAR(20) DEFAULT NULL,
    role TINYINT(1) DEFAULT 0,
    nama_toko VARCHAR(150) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    alamat_default TEXT DEFAULT NULL,
    fakultas VARCHAR(100) DEFAULT NULL,
    jurusan VARCHAR(100) DEFAULT NULL,
    toko_verified TINYINT(1) DEFAULT 0,
    jam_buka TIME DEFAULT NULL,
    jam_tutup TIME DEFAULT NULL,
    toko_libur TINYINT(1) DEFAULT 0,
    status_akun ENUM('aktif','suspend') DEFAULT 'aktif',
    poin INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- 2. KATEGORI
-- =============================================
CREATE TABLE kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- =============================================
-- 3. PRODUK
-- =============================================
CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjual INT NOT NULL,
    id_kategori INT,
    nama_barang VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    stok INT DEFAULT 0,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('aktif','nonaktif','pending_approval','ditolak') DEFAULT 'pending_approval',
    catatan_admin TEXT DEFAULT NULL,
    is_preorder TINYINT(1) DEFAULT 0,
    estimasi_preorder VARCHAR(100) DEFAULT NULL,
    total_terjual INT DEFAULT 0,
    rating_avg DECIMAL(2,1) DEFAULT 0,
    rating_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
);

-- =============================================
-- 4. PRODUK FOTO
-- =============================================
CREATE TABLE produk_foto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    urutan INT DEFAULT 0,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- =============================================
-- 5. PRODUK VARIASI
-- =============================================
CREATE TABLE produk_variasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT NOT NULL,
    nama_variasi VARCHAR(100) NOT NULL,
    stok INT DEFAULT 0,
    harga_tambahan DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- =============================================
-- 6. SHIPPING ZONES
-- =============================================
CREATE TABLE shipping_zones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjual INT NOT NULL,
    area_name VARCHAR(100) NOT NULL,
    fee DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 7. VOUCHERS
-- =============================================
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penjual INT DEFAULT NULL,
    kode VARCHAR(30) NOT NULL UNIQUE,
    tipe ENUM('persen','nominal') DEFAULT 'nominal',
    nilai DECIMAL(10,2) NOT NULL,
    min_belanja DECIMAL(10,2) DEFAULT 0,
    maks_potongan DECIMAL(10,2) DEFAULT NULL,
    kuota INT DEFAULT NULL,
    terpakai INT DEFAULT 0,
    berlaku_dari DATE DEFAULT NULL,
    berlaku_sampai DATE DEFAULT NULL,
    status ENUM('aktif','nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 8. ORDERS
-- =============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_order VARCHAR(30) NOT NULL UNIQUE,
    id_pembeli INT,
    id_penjual INT,
    id_zona INT DEFAULT NULL,
    id_voucher INT DEFAULT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    ongkir DECIMAL(10,2) DEFAULT 0,
    diskon_voucher DECIMAL(10,2) DEFAULT 0,
    poin_dipakai INT DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    alamat TEXT,
    bukti_bayar VARCHAR(255) DEFAULT NULL,
    is_preorder TINYINT(1) DEFAULT 0,
    status ENUM('pending','dikonfirmasi','diproses','selesai','dibatalkan') DEFAULT 'pending',
    batas_bayar TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (id_zona) REFERENCES shipping_zones(id) ON DELETE SET NULL,
    FOREIGN KEY (id_voucher) REFERENCES vouchers(id) ON DELETE SET NULL
);

-- =============================================
-- 9. ORDER ITEMS
-- =============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT NOT NULL,
    id_produk INT,
    id_variasi INT DEFAULT NULL,
    nama_barang VARCHAR(200) NOT NULL,
    nama_variasi VARCHAR(100) DEFAULT NULL,
    harga DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_order) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE SET NULL
);

-- =============================================
-- 10. VOUCHER USAGE
-- =============================================
CREATE TABLE voucher_usage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_voucher INT NOT NULL,
    id_pembeli INT NOT NULL,
    id_order INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_voucher) REFERENCES vouchers(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 11. CART
-- =============================================
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pembeli INT NOT NULL,
    id_produk INT NOT NULL,
    id_variasi INT DEFAULT NULL,
    qty INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE,
    FOREIGN KEY (id_variasi) REFERENCES produk_variasi(id) ON DELETE SET NULL
);

-- =============================================
-- 12. WISHLIST
-- =============================================
CREATE TABLE wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pembeli INT NOT NULL,
    id_produk INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (id_pembeli, id_produk),
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- =============================================
-- 13. FOLLOW TOKO
-- =============================================
CREATE TABLE follow_toko (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pembeli INT NOT NULL,
    id_penjual INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_follow (id_pembeli, id_penjual),
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 14. ULASAN
-- =============================================
CREATE TABLE ulasan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT NOT NULL,
    id_produk INT NOT NULL,
    id_pembeli INT NOT NULL,
    rating TINYINT(1) NOT NULL,
    komentar TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_ulasan (id_order, id_produk),
    FOREIGN KEY (id_order) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 15. KOMPLAIN
-- =============================================
CREATE TABLE komplain (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_order INT NOT NULL,
    id_pembeli INT NOT NULL,
    alasan VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('terbuka','ditinjau','selesai','ditolak') DEFAULT 'terbuka',
    tanggapan_admin TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_order) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 16. CHAT ROOMS
-- =============================================
CREATE TABLE chat_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_pembeli INT NOT NULL,
    id_penjual INT NOT NULL,
    last_message_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_room (id_pembeli, id_penjual),
    FOREIGN KEY (id_pembeli) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 17. CHAT MESSAGES
-- =============================================
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_room INT NOT NULL,
    id_sender INT NOT NULL,
    pesan TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_room) REFERENCES chat_rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (id_sender) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 18. NOTIFIKASI
-- =============================================
CREATE TABLE notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    tipe VARCHAR(30) NOT NULL,
    judul VARCHAR(200) NOT NULL,
    pesan TEXT,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- =============================================
-- 19. STOK LOG
-- =============================================
CREATE TABLE stok_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_produk INT NOT NULL,
    stok_lama INT NOT NULL,
    stok_baru INT NOT NULL,
    keterangan VARCHAR(200) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- =============================================
-- 20. KONSINYASI
-- =============================================
CREATE TABLE konsinyasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_penitip INT NOT NULL,
    id_penjual INT NOT NULL,
    id_produk INT DEFAULT NULL,
    nama_barang VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    harga_titipan DECIMAL(10,2) NOT NULL,
    harga_jual DECIMAL(10,2) NOT NULL,
    qty INT DEFAULT 1,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('menunggu','diterima','ditolak','terjual','selesai') DEFAULT 'menunggu',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_penitip) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_penjual) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE SET NULL
);

-- =============================================
-- DATA AWAL: KATEGORI
-- =============================================
INSERT INTO kategori (nama_kategori) VALUES
('Jajanan'), ('Jasa'), ('ATK'), ('Elektronik'), ('Lainnya');

-- =============================================
-- DATA AWAL: USER (1 admin/penjual)
-- =============================================
INSERT INTO users (email, nama, password, role, nama_toko, bio, toko_verified, status_akun) VALUES
('fathah.ikhwansyah@mhs.unsoed.ac.id', 'Fathah', '$2y$10$CmZxUYWA453uXUbbKkbbP.HSbPFV1vVGAX8mM2J9G6SK6.0umiALK', 1, 'Toko Fathah', 'Toko serba ada untuk mahasiswa UNSOED', 1, 'aktif');

-- =============================================
-- DATA AWAL: PRODUK (50 produk dummy)
-- =============================================
INSERT INTO produk (id_penjual, id_kategori, nama_barang, deskripsi, harga, stok, status) VALUES
-- JAJANAN
(1, 1, 'Bakso Bakar Pedas', 'Bakso bakar dengan bumbu pedas khas mahasiswa', 5000, 50, 'aktif'),
(1, 1, 'Cireng Isi Keju', 'Cireng crispy isi keju mozzarella lumer', 8000, 30, 'aktif'),
(1, 1, 'Es Teh Manis Jumbo', 'Es teh segar ukuran jumbo 600ml', 3000, 100, 'aktif'),
(1, 1, 'Pisang Goreng Crispy', 'Pisang goreng dengan tepung crispy gurih', 6000, 40, 'aktif'),
(1, 1, 'Mie Ayam Spesial', 'Mie ayam dengan topping ayam melimpah dan pangsit', 12000, 25, 'aktif'),
(1, 1, 'Sate Ayam 10 Tusuk', 'Sate ayam bumbu kacang lengkap dengan lontong', 15000, 20, 'aktif'),
(1, 1, 'Boba Brown Sugar', 'Minuman boba brown sugar dengan susu segar', 12000, 40, 'aktif'),
(1, 1, 'Seblak Kuah Pedas', 'Seblak kuah dengan level pedas pilihan', 12000, 30, 'aktif'),
(1, 1, 'Tteokbokki Gochujang', 'Tteokbokki korea dengan saus gochujang original', 18000, 20, 'aktif'),
(1, 1, 'Siomay Bandung', 'Siomay bandung lengkap dengan bumbu kacang', 12000, 30, 'aktif'),
-- JASA
(1, 2, 'Jasa Print Hitam Putih', 'Print dokumen hitam putih A4 per lembar', 500, 999, 'aktif'),
(1, 2, 'Jasa Print Berwarna', 'Print dokumen berwarna A4 per lembar', 1500, 999, 'aktif'),
(1, 2, 'Jasa Desain Poster', 'Desain poster untuk acara atau seminar', 35000, 20, 'aktif'),
(1, 2, 'Jasa Desain Logo', 'Desain logo untuk organisasi kampus', 75000, 10, 'aktif'),
(1, 2, 'Jasa Ketik Dokumen', 'Ketik ulang dokumen per halaman', 2000, 100, 'aktif'),
(1, 2, 'Jasa Les Matematika', 'Les matematika dasar dan lanjut per pertemuan', 40000, 15, 'aktif'),
(1, 2, 'Jasa Les Bahasa Inggris', 'Les bahasa inggris percakapan dan grammar', 35000, 15, 'aktif'),
(1, 2, 'Jasa Analisis Data SPSS', 'Olah data statistik menggunakan SPSS per proyek', 75000, 10, 'aktif'),
(1, 2, 'Jasa Laundry Kiloan', 'Laundry baju kiloan antar jemput dalam kampus', 7000, 50, 'aktif'),
(1, 2, 'Jasa Ojek Kampus', 'Antar jemput dalam area kampus UNSOED', 5000, 20, 'aktif'),
-- ATK
(1, 3, 'Pulpen Pilot G2 Hitam', 'Pulpen gel pilot G2 hitam smooth writing', 8000, 100, 'aktif'),
(1, 3, 'Pulpen Pilot G2 Biru', 'Pulpen gel pilot G2 biru smooth writing', 8000, 100, 'aktif'),
(1, 3, 'Buku Tulis Sidu 58 Lembar', 'Buku tulis sidu isi 58 lembar garis', 5000, 200, 'aktif'),
(1, 3, 'Stabilo Boss 4 Warna', 'Set stabilo boss 4 warna terang', 18000, 50, 'aktif'),
(1, 3, 'Sticky Note Warna-warni', 'Sticky note 4 warna isi 100 lembar per warna', 12000, 80, 'aktif'),
(1, 3, 'Penghapus Faber Castell', 'Penghapus faber castell dust free', 5000, 150, 'aktif'),
(1, 3, 'Pensil 2B Faber Castell', 'Pensil 2B faber castell isi 12 batang', 20000, 80, 'aktif'),
(1, 3, 'Kertas HVS A4 70gr 1 Rim', 'Kertas HVS A4 70gr isi 500 lembar 1 rim', 45000, 30, 'aktif'),
(1, 3, 'Notebook Aesthetic Hardcover', 'Notebook hardcover aesthetic 100 lembar', 35000, 40, 'aktif'),
(1, 3, 'Planner 2026 Mingguan', 'Planner 2026 format mingguan dengan stiker', 45000, 25, 'aktif'),
-- ELEKTRONIK
(1, 4, 'Earphone Bass Boost', 'Earphone dengan bass kuat cocok gaming', 35000, 25, 'aktif'),
(1, 4, 'Earphone Gaming RGB', 'Earphone gaming dengan lampu RGB dan mic', 55000, 20, 'aktif'),
(1, 4, 'Kabel Data Type-C 1m', 'Kabel data fast charging Type-C 1 meter', 15000, 60, 'aktif'),
(1, 4, 'Power Bank 10000mAh Slim', 'Power bank slim 10000mAh dual output fast charge', 120000, 15, 'aktif'),
(1, 4, 'Power Bank 20000mAh', 'Power bank 20000mAh dengan layar digital', 180000, 10, 'aktif'),
(1, 4, 'Mouse Wireless Logitech', 'Mouse wireless logitech M170 silent click', 85000, 15, 'aktif'),
(1, 4, 'Flashdisk 32GB Sandisk', 'Flashdisk sandisk 32GB USB 2.0', 65000, 25, 'aktif'),
(1, 4, 'Hub USB 4 Port', 'USB hub 4 port USB 3.0 slim', 45000, 20, 'aktif'),
(1, 4, 'Cooling Pad Laptop', 'Cooling pad laptop 2 kipas dengan LED', 75000, 15, 'aktif'),
(1, 4, 'Webcam HD 1080p', 'Webcam HD 1080p untuk kuliah online', 150000, 10, 'aktif'),
-- LAINNYA
(1, 5, 'Masker Medis 1 Box', 'Masker medis 3 ply isi 50 pcs', 20000, 40, 'aktif'),
(1, 5, 'Totebag Kanvas Polos', 'Totebag kanvas polos siap sablon', 25000, 35, 'aktif'),
(1, 5, 'Kaos Polos Cotton Combed', 'Kaos polos cotton combed 30s semua ukuran', 55000, 30, 'aktif'),
(1, 5, 'Jaket Kampus UNSOED', 'Jaket kampus UNSOED hoodie kualitas premium', 120000, 15, 'aktif'),
(1, 5, 'Tumbler Stainless 500ml', 'Tumbler stainless steel 500ml anti karat', 65000, 20, 'aktif'),
(1, 5, 'Hand Sanitizer 100ml', 'Hand sanitizer gel 100ml alkohol 70%', 12000, 80, 'aktif'),
(1, 5, 'Payung Lipat Mini', 'Payung lipat mini anti angin UV protection', 35000, 30, 'aktif'),
(1, 5, 'Vitamin C Effervescent', 'Vitamin C 1000mg effervescent rasa jeruk isi 10', 15000, 60, 'aktif'),
(1, 5, 'Buku Novel Best Seller', 'Novel best seller Indonesia terbaru', 65000, 15, 'aktif'),
(1, 5, 'Kaktus Mini Pot Lucu', 'Kaktus mini dalam pot lucu untuk meja belajar', 15000, 30, 'aktif');