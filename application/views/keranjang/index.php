<h4 class="fw-bold mb-4"><i class="bi bi-cart3 me-2"></i>Keranjang Belanja</h4>

<?php if (empty($grouped)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-cart-x fs-1 d-block mb-2 opacity-25"></i>
    Keranjang kamu masih kosong
    <div class="mt-3"><a href="<?= base_url('produk') ?>" class="btn btn-primary">Mulai Belanja</a></div>
</div>
<?php else: ?>

<?php foreach ($grouped as $id_penjual => $group): ?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white">
        <i class="bi bi-shop me-2 text-primary"></i>
        <strong><?= htmlspecialchars($group['nama_toko'] ?: $group['nama_penjual']) ?></strong>
    </div>
    <div class="table-responsive">
    <table class="table mb-0 align-middle">
        <tbody>
        <?php
        $subtotal_toko = 0;
        foreach ($group['items'] as $item):
            $harga_satuan = $item->harga_dasar + ($item->harga_tambahan ?? 0);
            $sub = $harga_satuan * $item->qty;
            $subtotal_toko += $sub;
            $stok_max = $item->id_variasi ? $item->stok_variasi : $item->stok_produk;
        ?>
        <tr>
            <td class="ps-3" width="60">
                <?php if ($item->foto): ?>
                <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($item->foto)) ?>" width="50" height="50" class="rounded" style="object-fit:cover">
                <?php else: ?>
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px"><i class="bi bi-image text-muted"></i></div>
                <?php endif; ?>
            </td>
            <td>
                <?= htmlspecialchars($item->nama_barang) ?>
                <?php if ($item->nama_variasi): ?><br><small class="text-muted"><?= htmlspecialchars($item->nama_variasi) ?></small><?php endif; ?>
                <?php if ($item->is_preorder): ?><span class="badge bg-warning text-dark ms-1">PO</span><?php endif; ?>
            </td>
            <td><?= rupiah($harga_satuan) ?></td>
            <td style="width:120px">
                <?= form_open('keranjang/update', ['class' => 'd-flex']) ?>
                <input type="hidden" name="cart_id" value="<?= $item->cart_id ?>">
                <input type="number" name="qty" value="<?= $item->qty ?>" min="1" <?= $item->is_preorder ? '' : 'max="' . $stok_max . '"' ?> class="form-control form-control-sm" onchange="this.form.submit()">
                <?= form_close() ?>
            </td>
            <td class="fw-semibold"><?= rupiah($sub) ?></td>
            <td class="text-center">
                <a href="<?= base_url('keranjang/hapus/' . $item->cart_id) ?>" class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="table-light">
                <td colspan="4" class="text-end fw-semibold">Subtotal Toko Ini</td>
                <td class="fw-bold text-primary"><?= rupiah($subtotal_toko) ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    </div>
    <div class="card-footer bg-white text-end">
        <a href="<?= base_url('order/checkout/' . $id_penjual) ?>" class="btn btn-primary">
            Checkout Toko Ini <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
<?php endforeach; ?>

<p class="text-muted small"><i class="bi bi-info-circle me-1"></i>Checkout dilakukan per toko karena setiap penjual memproses pesanannya masing-masing.</p>

<?php endif; ?>
