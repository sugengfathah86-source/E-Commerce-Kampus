<h4 class="fw-bold mb-4"><i class="bi bi-bell me-2"></i>Notifikasi</h4>

<?php if (empty($notifikasi)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-bell-slash fs-1 d-block mb-2 opacity-25"></i>
    Belum ada notifikasi
</div>
<?php else: ?>

<div class="card border-0 shadow-sm">
<div class="list-group list-group-flush">
<?php foreach ($notifikasi as $n): ?>
<a href="<?= base_url('notifikasi/buka/' . $n->id) ?>" class="list-group-item list-group-item-action <?= !$n->is_read ? 'bg-light' : '' ?>">
    <div class="d-flex justify-content-between">
        <span class="fw-semibold"><?= htmlspecialchars($n->judul) ?></span>
        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($n->created_at)) ?></small>
    </div>
    <div class="small text-muted"><?= htmlspecialchars($n->pesan) ?></div>
</a>
<?php endforeach; ?>
</div>
</div>

<?php endif; ?>
