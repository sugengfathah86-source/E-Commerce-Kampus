<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Produk Saya</h4>
    <a href="<?= base_url('toko/produk/tambah') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Produk
    </a>
</div>

<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table mb-0">
<thead><tr><th class="ps-3">Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
<tbody>
<?php if (empty($produk)): ?>
<tr><td colspan="6" class="text-center text-muted py-5">Belum ada produk. <a href="<?= base_url('toko/produk/tambah') ?>">Tambah produk pertama</a></td></tr>
<?php else: foreach ($produk as $p): ?>
<tr>
    <td class="ps-3 fw-semibold"><?= htmlspecialchars($p->nama_barang) ?></td>
    <td><?= htmlspecialchars($p->nama_kategori ?? '-') ?></td>
    <td><?= rupiah($p->harga) ?></td>
    <td><?= $p->stok ?></td>
    <td><span class="badge <?= $p->status == 'aktif' ? 'bg-success' : 'bg-secondary' ?>"><?= $p->status ?></span></td>
    <td class="text-center">
        <a href="<?= base_url('toko/produk/edit/' . $p->id) ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
        <a href="<?= base_url('toko/produk/hapus/' . $p->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus produk ini?')"><i class="bi bi-trash"></i></a>
    </td>
</tr>
<?php endforeach; endif; ?>
</tbody>
</table>
</div>
</div>
