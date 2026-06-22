<h4 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i>Kelola Pesanan</h4>

<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="<?= base_url('toko/order') ?>" class="btn btn-sm <?= $filter_status === '' ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
    <?php foreach (['pending' => 'Pending', 'dikonfirmasi' => 'Bukti Bayar', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan'] as $key => $label): ?>
    <a href="<?= base_url('toko/order?status=' . $key) ?>" class="btn btn-sm <?= $filter_status === $key ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table mb-0 align-middle">
<thead><tr><th class="ps-3">Kode</th><th>Pembeli</th><th>Total</th><th>Bukti Bayar</th><th>Status</th><th>Tanggal</th><th class="text-center">Aksi</th></tr></thead>
<tbody>
<?php
$status_badge = ['pending' => 'secondary', 'dikonfirmasi' => 'info', 'diproses' => 'warning', 'selesai' => 'success', 'dibatalkan' => 'danger'];
?>
<?php if (empty($orders)): ?>
<tr><td colspan="7" class="text-center text-muted py-5">Tidak ada pesanan</td></tr>
<?php else: foreach ($orders as $o): ?>
<tr>
    <td class="ps-3 fw-semibold"><?= htmlspecialchars($o->kode_order) ?></td>
    <td><?= htmlspecialchars($o->nama_pembeli) ?></td>
    <td class="fw-semibold"><?= rupiah($o->total) ?></td>
    <td>
        <?php if ($o->bukti_bayar): ?>
        <a href="<?= base_url('assets/uploads/bukti_bayar/' . htmlspecialchars($o->bukti_bayar)) ?>" target="_blank" class="btn btn-sm btn-outline-info"><i class="bi bi-image"></i> Lihat</a>
        <?php else: ?>
        <span class="text-muted small">Belum upload</span>
        <?php endif; ?>
    </td>
    <td><span class="badge bg-<?= $status_badge[$o->status] ?? 'secondary' ?>"><?= $o->status ?></span></td>
    <td><small><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></small></td>
    <td class="text-center">
        <a href="<?= base_url('toko/order/detail/' . $o->id) ?>" class="btn btn-sm btn-primary">Kelola</a>
    </td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
</div>
