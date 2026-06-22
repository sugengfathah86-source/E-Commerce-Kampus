<h4 class="fw-bold mb-4"><i class="bi bi-exclamation-triangle me-2"></i>Komplain Pengguna</h4>

<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table mb-0 align-middle">
    <thead><tr><th class="ps-3">Order</th><th>Pembeli</th><th>Alasan</th><th>Status</th><th>Tanggal</th><th class="text-center">Aksi</th></tr></thead>
    <tbody>
    <?php if (empty($komplain)): ?>
    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada komplain</td></tr>
    <?php else: ?>
    <?php
    $status_badge = ['terbuka' => 'danger', 'ditinjau' => 'warning', 'selesai' => 'success', 'ditolak' => 'secondary'];
    ?>
    <?php foreach ($komplain as $k): ?>
    <tr>
        <td class="ps-3"><?= htmlspecialchars($k->kode_order) ?></td>
        <td><?= htmlspecialchars($k->nama_pembeli) ?></td>
        <td><?= htmlspecialchars($k->alasan) ?></td>
        <td><span class="badge bg-<?= $status_badge[$k->status] ?? 'secondary' ?>"><?= $k->status ?></span></td>
        <td><small><?= date('d/m/Y H:i', strtotime($k->created_at)) ?></small></td>
        <td class="text-center">
            <a href="<?= base_url('admin/komplain/detail/' . $k->id) ?>" class="btn btn-sm btn-primary">Tinjau</a>
        </td>
    </tr>
    <?php endforeach; endif; ?>
    </tbody>
</table>
</div>
</div>
