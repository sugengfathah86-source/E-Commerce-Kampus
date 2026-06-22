<h4 class="fw-bold mb-4"><i class="bi bi-heart-fill text-danger me-2"></i>Wishlist Saya</h4>

<?php if (empty($wishlist)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-heart fs-1 d-block mb-2 opacity-25"></i>
    Belum ada produk yang disimpan
    <div class="mt-3"><a href="<?= base_url('produk') ?>" class="btn btn-primary">Mulai Belanja</a></div>
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach ($wishlist as $p): ?>
<div class="col-md-3 col-6">
    <div class="card product-card h-100">
        <div class="position-relative">
            <?php if ($p->foto): ?>
            <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($p->foto)) ?>" class="product-img w-100">
            <?php else: ?>
            <div class="product-img w-100 d-flex align-items-center justify-content-center text-muted">
                <i class="bi bi-image fs-1"></i>
            </div>
            <?php endif; ?>
            <?= form_open('wishlist/toggle/' . $p->id, ['class' => 'position-absolute top-0 end-0 m-2']) ?>
            <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width:34px;height:34px">
                <i class="bi bi-heart-fill text-danger"></i>
            </button>
            <?= form_close() ?>
        </div>
        <div class="card-body">
            <div class="small text-muted mb-1"><?= htmlspecialchars($p->nama_kategori ?? 'Lainnya') ?></div>
            <h6 class="card-title mb-1"><?= htmlspecialchars($p->nama_barang) ?></h6>
            <p class="fw-bold text-primary mb-1"><?= rupiah($p->harga) ?></p>
            <p class="small text-muted mb-2"><i class="bi bi-shop me-1"></i><?= htmlspecialchars($p->nama_toko ?: $p->nama_penjual) ?></p>
            <a href="<?= base_url('produk/detail/' . $p->id) ?>" class="btn btn-primary btn-sm w-100">Lihat Detail</a>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
