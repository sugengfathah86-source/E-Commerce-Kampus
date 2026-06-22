<h4 class="fw-bold mb-4"><i class="bi bi-chat-dots me-2"></i>Pesan</h4>

<?php if (!empty($rooms_penjual)): ?>
<h6 class="fw-semibold mb-2 text-muted">Sebagai Penjual</h6>
<div class="card border-0 shadow-sm mb-4">
<div class="list-group list-group-flush">
<?php foreach ($rooms_penjual as $r): ?>
<a href="<?= base_url('chat/room/' . $r->id) ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
    <?php if ($r->foto_lawan): ?>
    <img src="<?= htmlspecialchars($r->foto_lawan) ?>" width="40" height="40" class="rounded-circle">
    <?php else: ?>
    <i class="bi bi-person-circle fs-3 text-muted"></i>
    <?php endif; ?>
    <div class="flex-fill">
        <div class="fw-semibold"><?= htmlspecialchars($r->nama_lawan) ?></div>
        <small class="text-muted">Terakhir: <?= date('d/m/Y H:i', strtotime($r->last_message_at)) ?></small>
    </div>
</a>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>

<h6 class="fw-semibold mb-2 text-muted">Sebagai Pembeli</h6>
<?php if (empty($rooms_pembeli)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-chat fs-1 d-block mb-2 opacity-25"></i>
    Belum ada percakapan
</div>
<?php else: ?>
<div class="card border-0 shadow-sm">
<div class="list-group list-group-flush">
<?php foreach ($rooms_pembeli as $r): ?>
<a href="<?= base_url('chat/room/' . $r->id) ?>" class="list-group-item list-group-item-action d-flex align-items-center gap-3">
    <?php if ($r->foto_lawan): ?>
    <img src="<?= htmlspecialchars($r->foto_lawan) ?>" width="40" height="40" class="rounded-circle">
    <?php else: ?>
    <i class="bi bi-shop fs-3 text-muted"></i>
    <?php endif; ?>
    <div class="flex-fill">
        <div class="fw-semibold"><?= htmlspecialchars($r->nama_toko ?: $r->nama_lawan) ?></div>
        <small class="text-muted">Terakhir: <?= date('d/m/Y H:i', strtotime($r->last_message_at)) ?></small>
    </div>
</a>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>
