<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-bag-check me-2"></i>Pesanan Saya</h4>
    <a href="<?= base_url('order/riwayat/cetak') ?>" target="_blank" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
    </a>
</div>

<?php
$status_badge = ['pending' => 'secondary', 'dikonfirmasi' => 'info', 'diproses' => 'warning', 'selesai' => 'success', 'dibatalkan' => 'danger'];
$status_label = ['pending' => 'Menunggu Pembayaran', 'dikonfirmasi' => 'Bukti Bayar Diupload', 'diproses' => 'Sedang Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'];
?>

<?php if (empty($orders)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
    Belum ada pesanan
    <div class="mt-3"><a href="<?= base_url('produk') ?>" class="btn btn-primary">Mulai Belanja</a></div>
</div>
<?php else: ?>

<div class="row g-3">
<?php foreach ($orders as $o): ?>
<div class="col-12">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($o->kode_order) ?></div>
                    <div class="small text-muted">
                        <i class="bi bi-shop me-1"></i><?= htmlspecialchars($o->nama_toko ?: $o->nama_penjual) ?>
                        &middot; <?= date('d/m/Y H:i', strtotime($o->created_at)) ?>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge bg-<?= $status_badge[$o->status] ?? 'secondary' ?>"><?= $status_label[$o->status] ?? $o->status ?></span>
                    <div class="fw-bold text-primary mt-1"><?= rupiah($o->total) ?></div>
                </div>
            </div>

            <?php if ($o->status === 'pending'): ?>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="<?= base_url('order/sukses/' . $o->id) ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-whatsapp me-1"></i>Hubungi Penjual / Upload Bukti Bayar
                </a>
            </div>
            <?php endif; ?>

            <?php if (in_array($o->status, ['dikonfirmasi', 'diproses', 'selesai'])): ?>
            <div class="mt-3 d-flex gap-2 flex-wrap">
                <a href="<?= base_url('chat/mulai/' . $o->id_penjual) ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-chat-dots me-1"></i>Chat Penjual
                </a>
                <?php if ($o->status === 'selesai'): ?>
                <a href="<?= base_url('order/ulasan/form/' . $o->id) ?>" class="btn btn-sm btn-outline-warning">
                    <i class="bi bi-star me-1"></i>Beri Ulasan
                </a>
                <?php endif; ?>
                <?php if (!($sudah_komplain[$o->id] ?? false)): ?>
                <a href="<?= base_url('order/komplain/form/' . $o->id) ?>" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-exclamation-triangle me-1"></i>Ajukan Komplain
                </a>
                <?php else: ?>
                <span class="badge bg-light text-dark border align-self-center">Komplain sudah diajukan</span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php endif; ?>
