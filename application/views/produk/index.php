<div class="row mb-3">
    <div class="col-md-8">
        <?= form_open('produk', ['class' => 'd-flex gap-2']) ?>
        <input type="text" name="q" class="form-control" placeholder="🔍 Cari produk..." value="<?= htmlspecialchars($keyword) ?>">
        <?php if ($id_kategori): ?><input type="hidden" name="kategori" value="<?= $id_kategori ?>"><?php endif; ?>
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filterHarga">
            <i class="bi bi-sliders"></i>
        </button>
        <button class="btn btn-primary">Cari</button>
        <?= form_close() ?>
    </div>
</div>

<!-- Filter Harga (collapsible) -->
<div class="collapse <?= ($harga_min !== null || $harga_max !== null || $fakultas) ? 'show' : '' ?>" id="filterHarga">
    <div class="card border-0 shadow-sm p-3 mb-3">
        <?= form_open('produk', ['class' => 'd-flex gap-2 align-items-end flex-wrap']) ?>
        <?php if ($keyword): ?><input type="hidden" name="q" value="<?= htmlspecialchars($keyword) ?>"><?php endif; ?>
        <?php if ($id_kategori): ?><input type="hidden" name="kategori" value="<?= $id_kategori ?>"><?php endif; ?>
        <div>
            <label class="form-label small fw-semibold mb-1">Harga Minimum</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="harga_min" class="form-control" placeholder="0" value="<?= htmlspecialchars($harga_min ?? '') ?>">
            </div>
        </div>
        <div>
            <label class="form-label small fw-semibold mb-1">Harga Maksimum</label>
            <div class="input-group">
                <span class="input-group-text">Rp</span>
                <input type="number" name="harga_max" class="form-control" placeholder="Tanpa batas" value="<?= htmlspecialchars($harga_max ?? '') ?>">
            </div>
        </div>
        <?php if (!empty($daftar_fakultas)): ?>
        <div>
            <label class="form-label small fw-semibold mb-1">Fakultas Penjual</label>
            <select name="fakultas" class="form-select">
                <option value="">Semua Fakultas</option>
                <?php foreach ($daftar_fakultas as $f): ?>
                <option value="<?= htmlspecialchars($f->fakultas) ?>" <?= $fakultas == $f->fakultas ? 'selected' : '' ?>><?= htmlspecialchars($f->fakultas) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary">Terapkan</button>
        <?php if ($harga_min !== null || $harga_max !== null || $fakultas): ?>
        <a href="<?= base_url('produk' . ($id_kategori ? '?kategori=' . $id_kategori : '')) ?>" class="btn btn-outline-danger">Reset Filter</a>
        <?php endif; ?>
        <?= form_close() ?>
    </div>
</div>

<div class="mb-4 d-flex gap-2 flex-wrap">
    <a href="<?= base_url('produk') ?>" class="btn btn-sm <?= $id_kategori == 0 ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <i class="bi bi-grid me-1"></i>Semua
    </a>
    <?php
    $kategori_icon = [
        'jajanan'    => 'bi-cup-straw',
        'jasa'       => 'bi-tools',
        'atk'        => 'bi-pencil',
        'elektronik' => 'bi-laptop',
        'lainnya'    => 'bi-three-dots',
    ];
    foreach ($kategori as $kat):
        $icon = $kategori_icon[strtolower($kat->nama_kategori)] ?? 'bi-tag';
    ?>
    <a href="<?= base_url('produk?kategori=' . $kat->id) ?>" class="btn btn-sm <?= $id_kategori == $kat->id ? 'btn-primary' : 'btn-outline-secondary' ?>">
        <i class="bi <?= $icon ?> me-1"></i><?= htmlspecialchars($kat->nama_kategori) ?>
    </a>
    <?php endforeach; ?>
</div>

<?php if (!empty($rekomendasi) && !$id_kategori && !$keyword && $harga_min === null && $harga_max === null && !$fakultas): ?>
<div class="mb-4">
    <h6 class="fw-semibold mb-3"><i class="bi bi-stars text-warning me-2"></i>Rekomendasi Untukmu</h6>
    <div class="d-flex gap-3 overflow-auto pb-2">
        <?php foreach ($rekomendasi as $p): ?>
        <div style="min-width:180px;max-width:180px">
            <a href="<?= base_url('produk/detail/' . $p->id) ?>" class="card product-card text-decoration-none h-100">
                <?php if ($p->foto): ?>
                <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($p->foto)) ?>" class="product-img w-100" style="height:120px">
                <?php else: ?>
                <div class="product-img w-100 d-flex align-items-center justify-content-center text-muted" style="height:120px"><i class="bi bi-image"></i></div>
                <?php endif; ?>
                <div class="card-body p-2">
                    <div class="small text-truncate"><?= htmlspecialchars($p->nama_barang) ?></div>
                    <div class="fw-bold text-primary small"><?= rupiah($p->harga) ?></div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="row g-3">
    <?php if (empty($produk)): ?>
        <div class="col-12 text-center text-muted py-5">
            <i class="bi bi-box-seam fs-1 d-block mb-2 opacity-25"></i>
            Belum ada produk yang ditemukan
        </div>
    <?php else: ?>
        <?php foreach ($produk as $p): ?>
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
                    <?php if ($p->is_preorder): ?>
                    <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Pre-Order</span>
                    <?php elseif ($p->toko_libur): ?>
                    <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Toko Libur</span>
                    <?php endif; ?>
                    <?php if ($p->stok <= 5 && $p->stok > 0): ?>
                    <span class="badge bg-danger position-absolute top-0 end-0 m-2">Sisa <?= $p->stok ?></span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="small text-muted mb-1"><?= htmlspecialchars($p->nama_kategori ?? 'Lainnya') ?></div>
                    <h6 class="card-title mb-1"><?= htmlspecialchars($p->nama_barang) ?></h6>
                    <p class="fw-bold text-primary mb-1"><?= rupiah($p->harga) ?></p>
                    <?php if ($p->rating_count > 0): ?>
                    <div class="small text-warning mb-1"><i class="bi bi-star-fill"></i> <?= $p->rating_avg ?> <span class="text-muted">(<?= $p->rating_count ?>)</span></div>
                    <?php endif; ?>
                    <p class="small text-muted mb-2"><i class="bi bi-shop me-1"></i><?= htmlspecialchars($p->nama_toko ?: $p->nama_penjual) ?></p>
                    <a href="<?= base_url('produk/detail/' . $p->id) ?>" class="btn btn-primary btn-sm w-100">Lihat Detail</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
