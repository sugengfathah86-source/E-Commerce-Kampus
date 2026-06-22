<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) : 'E-Commerce Kampus' ?> - KampusMart</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background: #f5f6fa; }
        .product-card { transition: transform .2s; border: none; border-radius: 12px; overflow: hidden; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 10px 25px rgba(0,0,0,.1); }
        .product-img { height: 160px; object-fit: cover; background: #eee; }
        .badge-free { background: #d1fae5; color: #065f46; }
        .navbar-brand { font-size: 1.3rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark" style="background:#4f46e5;">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= base_url('produk') ?>">
            <i class="bi bi-shop me-1"></i> KampusMart
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('produk') ?>">Belanja</a></li>
                <?php if ($this->session->userdata('logged_in')): ?>
                    <?php if ($this->session->userdata('role') == 2): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">
                            <i class="bi bi-shield-lock me-1"></i>Admin Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($this->session->userdata('role') == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('toko/dashboard') ?>">
                            <i class="bi bi-speedometer2 me-1"></i>Dashboard Penjual
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('toko/laporan') ?>">
                            <i class="bi bi-bar-chart-line me-1"></i>Laporan
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('toko/buka') ?>">
                            <i class="bi bi-shop-window me-1"></i>Mulai Berjualan
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('chat') ?>"><i class="bi bi-chat-dots me-1"></i>Pesan</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('konsinyasi') ?>"><i class="bi bi-box-arrow-in-up me-1"></i>Titip Jual</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('order/riwayat') ?>">Pesanan Saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('wishlist') ?>"><i class="bi bi-heart me-1"></i>Wishlist</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('mengikuti') ?>"><i class="bi bi-people me-1"></i>Mengikuti</a></li>
                <?php endif; ?>
            </ul>

            <?php if ($this->session->userdata('logged_in')): ?>
            <ul class="navbar-nav align-items-center">
                <li class="nav-item dropdown me-2">
                    <a class="nav-link position-relative dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-bell fs-5"></i>
                        <?php if (($notif_unread ?? 0) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                            <?= $notif_unread ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="width:300px">
                        <li><h6 class="dropdown-header">Notifikasi</h6></li>
                        <?php if (empty($notif_terbaru)): ?>
                        <li><span class="dropdown-item-text text-muted small">Belum ada notifikasi</span></li>
                        <?php else: foreach ($notif_terbaru as $n): ?>
                        <li>
                            <a class="dropdown-item small <?= !$n->is_read ? 'fw-semibold' : '' ?>" href="<?= base_url('notifikasi/buka/' . $n->id) ?>">
                                <?= htmlspecialchars($n->judul) ?>
                                <div class="text-muted" style="font-size:.7rem"><?= date('d/m H:i', strtotime($n->created_at)) ?></div>
                            </a>
                        </li>
                        <?php endforeach; endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center small text-primary" href="<?= base_url('notifikasi') ?>">Lihat Semua</a></li>
                    </ul>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link position-relative" href="<?= base_url('keranjang') ?>">
                        <i class="bi bi-cart3 fs-5"></i>
                        <?php if (($cart_count ?? 0) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.6rem">
                            <?= $cart_count ?>
                        </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <?php if ($this->session->userdata('foto')): ?>
                        <img src="<?= htmlspecialchars($this->session->userdata('foto')) ?>" class="rounded-circle" width="28" height="28">
                        <?php else: ?>
                        <i class="bi bi-person-circle fs-5"></i>
                        <?php endif; ?>
                        <span class="small"><?= htmlspecialchars($this->session->userdata('nama')) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= base_url('profil') ?>"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                        <li><a class="dropdown-item" href="<?= base_url('order/riwayat') ?>"><i class="bi bi-bag-check me-2"></i>Pesanan Saya</a></li>
                        <?php if ($this->session->userdata('role') == 1): ?>
                        <li><a class="dropdown-item" href="<?= base_url('toko/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard Toko</a></li>
                        <?php endif; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </li>
            </ul>
            <?php else: ?>
            <a href="<?= base_url('login') ?>" class="btn btn-light btn-sm">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container py-4">
<?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($this->session->flashdata('success')) ?>
        <button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($this->session->flashdata('error')) ?>
        <button class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<?= $content ?>

</div>

<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
