<div class="row">
    <div class="col-md-5">
        <?php if ($produk->foto): ?>
        <img id="mainImage" src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($produk->foto)) ?>" class="img-fluid rounded shadow-sm w-100 mb-2" style="max-height:380px;object-fit:cover;">
        <?php else: ?>
        <div class="bg-light rounded d-flex align-items-center justify-content-center mb-2" style="height:300px;">
            <i class="bi bi-image fs-1 text-muted"></i>
        </div>
        <?php endif; ?>

        <?php if (!empty($galeri)): ?>
        <div class="d-flex gap-2 flex-wrap">
            <?php if ($produk->foto): ?>
            <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($produk->foto)) ?>" width="60" height="60" class="rounded border" style="object-fit:cover;cursor:pointer" onclick="document.getElementById('mainImage').src=this.src">
            <?php endif; ?>
            <?php foreach ($galeri as $g): ?>
            <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($g->foto)) ?>" width="60" height="60" class="rounded border" style="object-fit:cover;cursor:pointer" onclick="document.getElementById('mainImage').src=this.src">
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="col-md-7">
        <div class="d-flex justify-content-between align-items-start">
            <span class="badge bg-light text-dark mb-2"><?= htmlspecialchars($produk->nama_kategori ?? 'Lainnya') ?></span>
            <?= form_open('wishlist/toggle/' . $produk->id) ?>
            <button type="submit" class="btn btn-sm btn-light rounded-circle shadow-sm" style="width:38px;height:38px">
                <i class="bi bi-heart<?= $is_wishlisted ? '-fill text-danger' : '' ?>"></i>
            </button>
            <?= form_close() ?>
        </div>

        <h3 class="fw-bold">
            <?= htmlspecialchars($produk->nama_barang) ?>
            <?php if ($produk->is_preorder): ?><span class="badge bg-warning text-dark">Pre-Order</span><?php endif; ?>
        </h3>

        <?php if ($produk->rating_count > 0): ?>
        <div class="mb-2">
            <span class="text-warning"><i class="bi bi-star-fill"></i> <?= $produk->rating_avg ?></span>
            <span class="text-muted small">(<?= $produk->rating_count ?> ulasan &middot; <?= $produk->total_terjual ?> terjual)</span>
        </div>
        <?php endif; ?>

        <h4 class="text-primary fw-bold mb-3"><?= rupiah($produk->harga) ?></h4>

        <p class="text-muted"><?= nl2br(htmlspecialchars($produk->deskripsi)) ?></p>

        <?php if ($produk->is_preorder && $produk->estimasi_preorder): ?>
        <div class="alert alert-warning small"><i class="bi bi-clock-history me-2"></i>Estimasi pengerjaan: <?= htmlspecialchars($produk->estimasi_preorder) ?></div>
        <?php endif; ?>

        <a href="<?= base_url('toko-publik/' . $produk->id_penjual) ?>" class="d-flex align-items-center gap-2 mb-3 p-3 bg-white rounded shadow-sm text-decoration-none">
            <i class="bi bi-shop fs-4 text-primary"></i>
            <div>
                <div class="fw-semibold text-dark"><?= htmlspecialchars($produk->nama_toko ?: $produk->nama_penjual) ?></div>
                <div class="small text-muted">Lihat profil toko <i class="bi bi-chevron-right"></i></div>
            </div>
        </a>

        <?= form_open('keranjang/tambah') ?>
        <input type="hidden" name="id_produk" value="<?= $produk->id ?>">

        <?php if (!empty($variasi)): ?>
        <div class="mb-3">
            <label class="form-label fw-semibold">Pilih Variasi</label>
            <select name="id_variasi" class="form-select" required>
                <?php foreach ($variasi as $v): ?>
                <option value="<?= $v->id ?>" <?= $v->stok <= 0 ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($v->nama_variasi) ?>
                    <?= $v->harga_tambahan > 0 ? ' (+' . number_format($v->harga_tambahan, 0, ',', '.') . ')' : '' ?>
                    <?= $v->stok <= 0 ? ' - Stok Habis' : ' (stok ' . $v->stok . ')' ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php else: ?>
        <p class="small text-muted">Stok tersedia: <?= $produk->stok ?></p>
        <?php endif; ?>

        <?php if ($produk->stok > 0 || $produk->is_preorder): ?>
        <div class="d-flex gap-2">
            <input type="number" name="qty" value="1" min="1" max="<?= $produk->stok ?: 99 ?>" class="form-control" style="width:100px">
            <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-cart-plus me-2"></i><?= $produk->is_preorder ? 'Pre-Order Sekarang' : 'Tambah ke Keranjang' ?>
            </button>
        </div>
        <?php else: ?>
        <button class="btn btn-secondary w-100" disabled>Stok Habis</button>
        <?php endif; ?>
        <?= form_close() ?>
    </div>
</div>

<!-- Ulasan -->
<div class="mt-5">
    <h5 class="fw-bold mb-3">Ulasan Pembeli (<?= count($ulasan) ?>)</h5>

    <?php if (empty($ulasan)): ?>
    <p class="text-muted">Belum ada ulasan untuk produk ini.</p>
    <?php else: ?>
    <div class="row g-3">
        <?php foreach ($ulasan as $u): ?>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?php if ($u->foto_profil): ?>
                        <img src="<?= htmlspecialchars($u->foto_profil) ?>" width="32" height="32" class="rounded-circle">
                        <?php else: ?>
                        <i class="bi bi-person-circle fs-4 text-muted"></i>
                        <?php endif; ?>
                        <div>
                            <div class="fw-semibold small"><?= htmlspecialchars($u->nama_pembeli) ?></div>
                            <div class="text-warning small">
                                <?php for ($i = 0; $i < $u->rating; $i++): ?><i class="bi bi-star-fill"></i><?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    <p class="small mb-0"><?= htmlspecialchars($u->komentar) ?></p>
                    <?php if ($u->foto): ?>
                    <img src="<?= base_url('assets/uploads/ulasan/' . htmlspecialchars($u->foto)) ?>" class="rounded mt-2" width="80" height="80" style="object-fit:cover">
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
