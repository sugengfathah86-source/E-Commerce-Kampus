# E-Commerce Kampus (CodeIgniter 3)

Marketplace sederhana khusus mahasiswa, dibangun menggunakan **CodeIgniter 3** dengan pola arsitektur **MVC**, database **MySQL**, dan login terbatas hanya untuk email kampus melalui **Google Sign-In (OAuth 2.0)**.

---

## Fitur Utama

### Autentikasi
- Login menggunakan akun Google (OAuth 2.0)
- Validasi domain — hanya email `@mhs.unsoed.ac.id` yang dapat masuk
- Pendaftaran otomatis saat login pertama kali

### Dual Role (Pembeli & Penjual)
- Setiap user awalnya berstatus **pembeli**
- Tombol **"Buka Toko"** mengubah status menjadi **pembeli + penjual** dalam akun yang sama
- Penjual mendapat akses ke **Dashboard Toko**

### Marketplace
- Daftar produk dengan filter kategori & pencarian
- Detail produk per item
- Keranjang belanja (dikelompokkan per toko — satu order = satu penjual)

### Checkout & Pengiriman
- Sistem **zona pengiriman** (Area Kampus = gratis ongkir, Kost Dekat Kampus, Luar Kampus)
- Ongkir dihitung otomatis berdasarkan zona dipilih

### Pembayaran Semi-Manual
- Setelah checkout, link **WhatsApp ke penjual** otomatis terisi detail pesanan
- Pembeli dapat **upload bukti bayar** (screenshot QRIS)
- Penjual update status pesanan: `pending` → `dikonfirmasi` → `diproses` → `selesai`

### Dashboard Penjual
- Ringkasan: total produk, total omzet, jumlah order, order pending
- CRUD produk (dengan upload foto)
- Kelola pesanan masuk + lihat bukti bayar + update status

---

## Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | CodeIgniter 3 (pola MVC) |
| Database | MySQL, akses via Query Builder (Active Record) |
| Autentikasi | Google OAuth 2.0 (cURL native, tanpa SDK eksternal) |
| Tampilan | Bootstrap 5 (CDN) |

---

## Struktur Database

| Tabel | Keterangan |
|---|---|
| `users` | Data akun (pembeli/penjual), dibedakan kolom `role` |
| `kategori` | Kategori produk |
| `produk` | Data barang dagangan |
| `shipping_zones` | Daftar zona pengiriman & ongkirnya |
| `cart` | Keranjang belanja sementara |
| `orders` | Header transaksi |
| `order_items` | Detail item per transaksi |

**Relasi (JOIN) yang dipakai:**
- `produk` JOIN `users` (nama penjual), JOIN `kategori` (nama kategori)
- `orders` JOIN `users` (nama pembeli & penjual), JOIN `shipping_zones` (nama zona)
- `cart` JOIN `produk` JOIN `users`

---

## Struktur Folder (MVC)

```
ecommerce_ci3/
├── application/
│   ├── controllers/
│   │   ├── Auth.php          → Login, logout, Google OAuth
│   │   ├── Produk.php        → Marketplace, detail produk
│   │   ├── Toko.php          → Dashboard, CRUD produk, kelola pesanan
│   │   ├── Keranjang.php     → Keranjang belanja
│   │   └── Order.php         → Checkout, riwayat, upload bukti bayar
│   ├── models/
│   │   ├── User_model.php
│   │   ├── Produk_model.php
│   │   ├── Kategori_model.php
│   │   ├── Keranjang_model.php
│   │   ├── Zona_model.php
│   │   └── Order_model.php
│   ├── views/
│   │   ├── auth/, produk/, toko/, keranjang/, order/
│   │   └── layouts/main.php  → Layout utama (navbar + footer)
│   ├── core/
│   │   └── MY_Controller.php → Base controller (auth helper)
│   └── config/
│       ├── database.php
│       ├── routes.php
│       └── google_oauth.php  → Kredensial Google OAuth
├── system/                    → Core CodeIgniter 3
├── assets/uploads/            → Folder upload foto produk & bukti bayar
├── database.sql               → Skema database
└── PANDUAN_GOOGLE_SSO.md       → Cara membuat Google Client ID & Secret
```

---

## Cara Instalasi

### 1. Persyaratan
- PHP 7.4+ dengan ekstensi `curl` dan `mysqli` aktif
- MySQL / MariaDB
- Laragon (atau XAMPP)
- Apache `mod_rewrite` aktif

### 2. Setup Database
1. Buka phpMyAdmin
2. Import file `database.sql` (otomatis membuat database `ecommerce_kampus` beserta semua tabel dan data awal)

### 3. Konfigurasi Database
Edit `application/config/database.php`:
```php
'hostname' => 'localhost',
'username' => 'root',
'password' => '',   // sesuaikan
'database' => 'ecommerce_kampus',
```

### 4. Konfigurasi base_url
Edit `application/config/config.php`:
```php
$config['base_url'] = 'http://ecommerce_kampus.test/';
```
> Sesuaikan dengan domain Laragon kamu, atau `http://localhost/ecommerce_ci3/` jika pakai XAMPP/subfolder.

### 5. Setup Google Sign-In
Ikuti panduan lengkap di **PANDUAN_GOOGLE_SSO.md** untuk mendapatkan Client ID & Secret, lalu isi di `application/config/google_oauth.php`.

### 6. Jalankan
Letakkan folder project di `www` (Laragon), lalu akses:
```
http://ecommerce_kampus.test/
```

---

## Alur Penggunaan

**Sebagai Pembeli:**
1. Login dengan email kampus via Google
2. Cari & pilih produk → tambah ke keranjang
3. Checkout per toko → pilih zona pengiriman → isi alamat
4. Hubungi penjual via WhatsApp (link otomatis)
5. Upload bukti bayar setelah transfer/QRIS

**Sebagai Penjual:**
1. Klik "Mulai Berjualan" → isi nama toko & nomor WhatsApp
2. Tambah produk dari Dashboard Toko
3. Pantau pesanan masuk → cek bukti bayar → update status pesanan

---

## Keamanan yang Diterapkan
- Validasi domain email mencegah orang luar kampus mendaftar
- Password tidak disimpan sama sekali (autentikasi murni via Google OAuth)
- State parameter OAuth untuk mencegah CSRF saat proses login
- Query Builder CodeIgniter (Active Record) — aman dari SQL Injection, tidak ada query mentah
- Validasi kepemilikan data (produk/order) di setiap query — penjual hanya bisa mengubah produk/order miliknya sendiri
- Form validation library CodeIgniter di semua input form
