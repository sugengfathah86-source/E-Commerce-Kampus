<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <?php if ($penjual->foto_profil): ?>
                <img src="<?= htmlspecialchars($penjual->foto_profil) ?>" class="rounded-circle" width="90" height="90">
                <?php else: ?>
                <i class="bi bi-shop fs-1 text-primary"></i>
                <?php endif; ?>
            </div>
            <div class="col-md-7">
                <h4 class="fw-bold mb-1">
                    <?= htmlspecialchars($penjual->nama_toko ?: $penjual->nama) ?>
                    <?php if ($penjual->toko_verified): ?>
                    <i class="bi bi-patch-check-fill text-primary" style="font-size:1.2rem" title="Toko Terverifikasi"></i>
                    <?php endif; ?>
                </h4>
                <p class="text-muted mb-2"><?= htmlspecialchars($penjual->bio ?: 'Belum ada deskripsi toko.') ?></p>
                <div class="d-flex gap-3 small text-muted">
                    <span><i class="bi bi-people me-1"></i><?= $jumlah_follow ?> pengikut</span>
                    <span><i class="bi bi-box-seam me-1"></i><?= count($produk) ?> produk</span>
                    <?php if ($penjual->fakultas): ?>
                    <span><i class="bi bi-mortarboard me-1"></i><?= htmlspecialchars($penjual->fakultas) ?></span>
                    <?php endif; ?>
                </div>
                <?php if ($penjual->jam_buka): ?>
                <div class="small text-muted mt-1">
                    <i class="bi bi-clock me-1"></i>Buka <?= date('H:i', strtotime($penjual->jam_buka)) ?> - <?= date('H:i', strtotime($penjual->jam_tutup)) ?>
                    <?php if ($penjual->toko_libur): ?><span class="badge bg-danger ms-2">Sedang Libur</span><?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-md-3 text-end">
                <?php if (!$is_pemilik): ?>
                <div class="d-flex gap-2 justify-content-end flex-wrap">
                    <a href="<?= base_url('chat/mulai/' . $penjual->id) ?>" class="btn btn-outline-primary">
                        <i class="bi bi-chat-dots me-2"></i>Chat
                    </a>
                    <?= form_open('toko-publik/' . $penjual->id . '/follow') ?>
                    <button type="submit" class="btn <?= $is_following ? 'btn-outline-secondary' : 'btn-primary' ?>">
                        <i class="bi bi-<?= $is_following ? 'check-circle' : 'plus-circle' ?> me-2"></i>
                        <?= $is_following ? 'Mengikuti' : 'Follow Toko' ?>
                    </button>
                    <?= form_close() ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<h6 class="fw-semibold mb-3">Produk dari Toko Ini</h6>
<div class="row g-3">
<?php if (empty($produk)): ?>
<div class="col-12 text-center text-muted py-5">
    <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
    Toko ini belum memiliki produk aktif
</div>
<?php else: foreach ($produk as $p): ?>
<div class="col-md-3 col-6">
    <div class="card product-card h-100">
        <?php if ($p->foto): ?>
        <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($p->foto)) ?>" class="product-img w-100">
        <?php else: ?>
        <div class="product-img w-100 d-flex align-items-center justify-content-center text-muted">
            <i class="bi bi-image fs-1"></i>
        </div>
        <?php endif; ?>
        <div class="card-body">
            <h6 class="card-title mb-1"><?= htmlspecialchars($p->nama_barang) ?></h6>
            <p class="fw-bold text-primary mb-1"><?= rupiah($p->harga) ?></p>
            <?php if ($p->rating_count > 0): ?>
            <div class="small text-warning mb-2"><i class="bi bi-star-fill"></i> <?= $p->rating_avg ?> (<?= $p->rating_count ?>)</div>
            <?php endif; ?>
            <a href="<?= base_url('produk/detail/' . $p->id) ?>" class="btn btn-primary btn-sm w-100">Lihat Detail</a>
        </div>
    </div>
</div>
<?php endforeach; endif; ?>
</div>
