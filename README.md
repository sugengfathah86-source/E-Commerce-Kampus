# 🛒 KampusMart — E-Commerce Marketplace Mahasiswa

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white"/>
  <img src="https://img.shields.io/badge/CodeIgniter-3.x-EF4223?style=for-the-badge&logo=codeigniter&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white"/>
</p>

<p align="center">
  Platform marketplace khusus mahasiswa Universitas Jenderal Soedirman (UNSOED) untuk jual beli produk dan jasa di lingkungan kampus.
</p>

<p align="center">
  <a href="http://kelompok10-kelas-b.tekkom.web.id/">🌐 Live Demo</a>
</p>

---

## 👥 Tim Pengembang

| Nama | NIM | Peran |
|------|-----|-------|
| Fathah Ikhwansyah | H1H024063 | Backend & Database |
| Ibnu Abbas | H1H024038 | Frontend & Fitur Transaksi |

**Mata Kuliah:** Pemrograman Web II  
**Dosen Pengampu:** Mohammad Irham Akbar  
**Kelas:** Pemrograman Web I B  
**Universitas:** Universitas Jenderal Soedirman

---

## 📋 Tentang Aplikasi

KampusMart adalah aplikasi e-commerce berbasis web yang dirancang khusus sebagai marketplace bagi mahasiswa UNSOED. Aplikasi ini memungkinkan mahasiswa untuk:

- 🛍️ **Berbelanja** produk dari sesama mahasiswa
- 🏪 **Membuka toko** dan berjualan online
- 💬 **Berkomunikasi** langsung dengan penjual via chat
- ⭐ **Menyimpan** produk favorit di wishlist
- 🎟️ **Menggunakan voucher** dan poin untuk diskon

---

## ✨ Fitur Utama

### 👤 Autentikasi
- Registrasi manual (khusus email `@mhs.unsoed.ac.id`)
- Login dengan email & password
- Login via Google OAuth 2.0

### 🛍️ Pembeli
- Jelajahi produk berdasarkan kategori (Jajanan, Jasa, ATK, Elektronik, Lainnya)
- Pencarian dan filter produk (kategori, harga min/max)
- Keranjang belanja
- Checkout dengan voucher diskon & poin
- Upload bukti pembayaran
- Riwayat pesanan
- Wishlist produk favorit
- Follow toko favorit
- Chat dengan penjual
- Notifikasi real-time

### 🏪 Penjual
- Dashboard toko
- Manajemen produk (tambah, edit, hapus)
- Variasi produk & manajemen stok
- Zona pengiriman (shipping zones)
- Manajemen pesanan masuk
- Laporan penjualan dengan export CSV
- Sistem konsinyasi (titip jual)
- Pembuatan voucher diskon

---

## 🛠️ Teknologi

| Kategori | Teknologi |
|----------|-----------|
| Backend | PHP 8.x, CodeIgniter 3 |
| Frontend | Bootstrap 5.3, Bootstrap Icons |
| Database | MySQL 8.0 |
| Web Server | Apache |
| Dev Tools | VS Code, Git, phpMyAdmin |
| Hosting | VPS Hestia Control Panel |
| Auth | Google OAuth 2.0 |

---

## 🗄️ Struktur Database

Database `ecommerce_kampus` terdiri dari **20 tabel**:

```
users           — Data pengguna (pembeli & penjual)
kategori        — Kategori produk
produk          — Data produk
produk_foto     — Foto produk (multiple)
produk_variasi  — Variasi produk (ukuran, warna, dll)
shipping_zones  — Zona pengiriman per penjual
vouchers        — Data voucher diskon
voucher_usage   — Riwayat pemakaian voucher
orders          — Data pesanan
order_items     — Detail item per pesanan
cart            — Keranjang belanja
wishlist        — Produk favorit pembeli
follow_toko     — Toko yang diikuti pembeli
ulasan          — Ulasan & rating produk
komplain        — Komplain pesanan
chat_rooms      — Room chat pembeli-penjual
chat_messages   — Pesan chat
notifikasi      — Notifikasi pengguna
stok_log        — Log perubahan stok
konsinyasi      — Sistem titip jual
```

---

## 🚀 Cara Instalasi (Localhost)

### Prasyarat
- PHP 8.x
- MySQL 8.0
- Apache (XAMPP / Laragon)
- Composer (opsional)

### Langkah Instalasi

**1. Clone repository**
```bash
git clone https://github.com/username/ecommerce-kampus.git
cd ecommerce-kampus
```

**2. Pindahkan ke folder web server**
```
# XAMPP
C:\xampp\htdocs\ecommerce_kampus\

# Laragon
C:\laragon\www\ecommerce_kampus\
```

**3. Import database**
```bash
mysql -u root -p < ecommerce_kampus_clean.sql
```
Atau import via phpMyAdmin: buka `http://localhost/phpmyadmin` → Import → pilih file `ecommerce_kampus_clean.sql`

**4. Konfigurasi database**

Edit `application/config/database.php`:
```php
$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'root';
$db['default']['password'] = '';
$db['default']['database'] = 'ecommerce_kampus';
```

**5. Konfigurasi base URL**

Edit `application/config/config.php`:
```php
# XAMPP
$config['base_url'] = 'http://localhost/ecommerce_kampus/';

# Laragon
$config['base_url'] = 'http://ecommerce_kampus.test/';
```

**6. Konfigurasi Google OAuth (opsional)**

Edit `application/config/google_oauth.php`:
```php
$config['google_client_id']     = 'YOUR_CLIENT_ID';
$config['google_client_secret'] = 'YOUR_CLIENT_SECRET';
$config['google_redirect_uri']  = 'http://localhost/ecommerce_kampus/auth/google/callback';
```

**7. Akses aplikasi**
```
http://localhost/ecommerce_kampus/
```

---

## 📁 Struktur Folder

```
ecommerce_kampus/
├── application/
│   ├── config/          — Konfigurasi (database, routes, oauth)
│   ├── controllers/     — Controller (Auth, Produk, Toko, Cart, dll)
│   ├── models/          — Model (User_model, Produk_model, dll)
│   ├── views/           — Tampilan (auth, produk, toko, cart, dll)
│   └── helpers/         — Helper custom (rupiah, tgl_indo)
├── assets/
│   ├── css/             — Bootstrap & custom CSS
│   ├── js/              — JavaScript
│   └── uploads/         — Upload foto produk & bukti bayar
├── system/              — Core CodeIgniter 3
├── .htaccess            — URL rewriting
└── index.php            — Entry point
```

---

## 👤 Akun Default

Setelah import database, gunakan akun berikut untuk login:

| Role | Email | Password |
|------|-------|----------|
| Penjual | fathah.ikhwansyah@mhs.unsoed.ac.id | (password saat registrasi) |

> ⚠️ Registrasi hanya bisa menggunakan email `@mhs.unsoed.ac.id`

---

## 📸 Screenshot

> *Screenshot akan ditambahkan setelah aplikasi live*

---

## 📄 Lisensi

Project ini dibuat untuk keperluan tugas akhir mata kuliah Pemrograman Web II  
Universitas Jenderal Soedirman © 2026
