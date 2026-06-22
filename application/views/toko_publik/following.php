<h4 class="fw-bold mb-4"><i class="bi bi-people-fill me-2"></i>Toko yang Saya Ikuti</h4>

<?php if (empty($toko)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-shop fs-1 d-block mb-2 opacity-25"></i>
    Kamu belum mengikuti toko apapun
    <div class="mt-3"><a href="<?= base_url('produk') ?>" class="btn btn-primary">Jelajahi Produk</a></div>
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach ($toko as $t): ?>
<div class="col-md-3 col-6">
    <a href="<?= base_url('toko-publik/' . $t->id) ?>" class="card border-0 shadow-sm text-decoration-none h-100">
        <div class="card-body text-center">
            <?php if ($t->foto_profil): ?>
            <img src="<?= htmlspecialchars($t->foto_profil) ?>" class="rounded-circle mb-2" width="60" height="60">
            <?php else: ?>
            <i class="bi bi-shop fs-1 text-primary"></i>
            <?php endif; ?>
            <div class="fw-semibold">
                <?= htmlspecialchars($t->nama_toko ?: $t->nama) ?>
                <?php if ($t->toko_verified): ?><i class="bi bi-patch-check-fill text-primary small"></i><?php endif; ?>
            </div>
        </div>
    </a>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
