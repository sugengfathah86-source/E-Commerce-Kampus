<h4 class="fw-bold mb-4"><i class="bi bi-check2-square me-2"></i>Moderasi Produk</h4>

<?php if (empty($produk)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-check-circle fs-1 d-block mb-2 opacity-25"></i>
    Tidak ada produk yang menunggu persetujuan
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach ($produk as $p): ?>
<div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
        <div class="row g-0">
            <div class="col-4">
                <?php if ($p->foto): ?>
                <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($p->foto)) ?>" class="w-100 h-100 rounded-start" style="object-fit:cover">
                <?php else: ?>
                <div class="bg-light h-100 d-flex align-items-center justify-content-center rounded-start"><i class="bi bi-image fs-1 text-muted"></i></div>
                <?php endif; ?>
            </div>
            <div class="col-8">
                <div class="card-body">
                    <h6 class="fw-bold"><?= htmlspecialchars($p->nama_barang) ?></h6>
                    <p class="small text-muted mb-1"><i class="bi bi-shop me-1"></i><?= htmlspecialchars($p->nama_toko ?: $p->nama_penjual) ?></p>
                    <p class="fw-semibold text-primary mb-2"><?= rupiah($p->harga) ?></p>
                    <p class="small mb-2" style="max-height:60px;overflow:hidden"><?= htmlspecialchars($p->deskripsi) ?></p>

                    <div class="d-flex gap-2">
                        <a href="<?= base_url('admin/produk/approve/' . $p->id) ?>" class="btn btn-sm btn-success" onclick="return confirm('Setujui produk ini?')">
                            <i class="bi bi-check-lg"></i> Setujui
                        </a>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $p->id ?>">
                            <i class="bi bi-x-lg"></i> Tolak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div class="modal fade" id="rejectModal<?= $p->id ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <?= form_open('admin/produk/reject/' . $p->id) ?>
                <div class="modal-header">
                    <h6 class="modal-title">Tolak Produk: <?= htmlspecialchars($p->nama_barang) ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Alasan Penolakan</label>
                    <textarea name="catatan" class="form-control" rows="3" placeholder="Jelaskan alasan penolakan untuk penjual..." required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Produk</button>
                </div>
                <?= form_close() ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
