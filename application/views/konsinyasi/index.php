<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-box-arrow-in-up me-2"></i>Titip Jual Saya</h4>
    <a href="<?= base_url('konsinyasi/ajukan') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Ajukan Titip Jual
    </a>
</div>

<?php
$status_badge = ['menunggu' => 'secondary', 'diterima' => 'success', 'ditolak' => 'danger', 'terjual' => 'primary', 'selesai' => 'info'];
?>

<?php if (empty($konsinyasi)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
    Kamu belum pernah menitip barang
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach ($konsinyasi as $k): ?>
<div class="col-md-6">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <h6 class="fw-bold"><?= htmlspecialchars($k->nama_barang) ?></h6>
                <span class="badge bg-<?= $status_badge[$k->status] ?? 'secondary' ?>"><?= $k->status ?></span>
            </div>
            <p class="small text-muted mb-1"><i class="bi bi-shop me-1"></i>Dititip ke: <?= htmlspecialchars($k->nama_toko ?: $k->nama_penjual) ?></p>
            <p class="small mb-1">Harga titipan: <?= rupiah($k->harga_titipan) ?> &middot; Jual: <?= rupiah($k->harga_jual) ?></p>
            <p class="small text-muted mb-0">Jumlah: <?= $k->qty ?> &middot; <?= date('d/m/Y', strtotime($k->created_at)) ?></p>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
