<h4 class="fw-bold mb-4"><i class="bi bi-box-arrow-in-down me-2"></i>Kelola Titip Jual</h4>

<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="<?= base_url('konsinyasi/kelola') ?>" class="btn btn-sm <?= $filter_status === '' ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
    <?php foreach (['menunggu' => 'Menunggu', 'diterima' => 'Diterima', 'ditolak' => 'Ditolak', 'terjual' => 'Terjual'] as $key => $label): ?>
    <a href="<?= base_url('konsinyasi/kelola?status=' . $key) ?>" class="btn btn-sm <?= $filter_status === $key ? 'btn-primary' : 'btn-outline-secondary' ?>"><?= $label ?></a>
    <?php endforeach; ?>
</div>

<?php
$status_badge = ['menunggu' => 'secondary', 'diterima' => 'success', 'ditolak' => 'danger', 'terjual' => 'primary', 'selesai' => 'info'];
?>

<?php if (empty($konsinyasi)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-inbox fs-1 d-block mb-2 opacity-25"></i>
    Tidak ada pengajuan titip jual
</div>
<?php else: ?>

<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table mb-0 align-middle">
    <thead><tr><th class="ps-3">Barang</th><th>Penitip</th><th>Harga Jual</th><th>Qty</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($konsinyasi as $k): ?>
    <tr>
        <td class="ps-3 fw-semibold"><?= htmlspecialchars($k->nama_barang) ?></td>
        <td><?= htmlspecialchars($k->nama_penitip) ?></td>
        <td><?= rupiah($k->harga_jual) ?></td>
        <td><?= $k->qty ?></td>
        <td><span class="badge bg-<?= $status_badge[$k->status] ?? 'secondary' ?>"><?= $k->status ?></span></td>
        <td class="text-center">
            <a href="<?= base_url('konsinyasi/detail/' . $k->id) ?>" class="btn btn-sm btn-primary">Lihat</a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>

<?php endif; ?>
