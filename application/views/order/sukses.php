<div class="row justify-content-center">
<div class="col-md-7">
<div class="text-center mb-4">
    <i class="bi bi-check-circle-fill text-success" style="font-size:4rem"></i>
    <h4 class="fw-bold mt-3">Pesanan Berhasil Dibuat!</h4>
    <p class="text-muted">Kode Order: <strong><?= htmlspecialchars($order->kode_order) ?></strong></p>
    <?php if ($order->batas_bayar): ?>
    <p class="small text-danger"><i class="bi bi-clock me-1"></i>Selesaikan pembayaran sebelum <?= date('d/m/Y H:i', strtotime($order->batas_bayar)) ?>, atau pesanan otomatis dibatalkan.</p>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white fw-semibold">Detail Pesanan</div>
    <ul class="list-group list-group-flush">
    <?php foreach ($items as $item): ?>
    <li class="list-group-item d-flex justify-content-between">
        <span>
            <?= htmlspecialchars($item->nama_barang) ?>
            <?php if ($item->nama_variasi): ?><small class="text-muted">(<?= htmlspecialchars($item->nama_variasi) ?>)</small><?php endif; ?>
            x<?= $item->qty ?>
        </span>
        <span><?= rupiah($item->subtotal) ?></span>
    </li>
    <?php endforeach; ?>
    <li class="list-group-item d-flex justify-content-between"><span>Ongkir</span><span><?= rupiah($order->ongkir) ?></span></li>
    <?php if ($order->diskon_voucher > 0): ?>
    <li class="list-group-item d-flex justify-content-between text-success"><span>Diskon Voucher</span><span>- <?= rupiah($order->diskon_voucher) ?></span></li>
    <?php endif; ?>
    <?php if ($order->poin_dipakai > 0): ?>
    <li class="list-group-item d-flex justify-content-between text-success"><span>Poin Dipakai</span><span>- <?= rupiah($order->poin_dipakai) ?></span></li>
    <?php endif; ?>
    <li class="list-group-item d-flex justify-content-between fw-bold"><span>Total</span><span class="text-primary"><?= rupiah($order->total) ?></span></li>
    </ul>
</div>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>Langkah selanjutnya: hubungi penjual via WhatsApp untuk konfirmasi pesanan dan info pembayaran (QRIS).
</div>

<a href="<?= htmlspecialchars($wa_link) ?>" target="_blank" class="btn btn-success w-100 py-3 mb-3 fs-5">
    <i class="bi bi-whatsapp me-2"></i>Hubungi Penjual via WhatsApp
</a>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Sudah Bayar? Upload Bukti Pembayaran</h6>
        <?= form_open_multipart('order/upload_bukti') ?>
        <input type="hidden" name="order_id" value="<?= $order->id ?>">
        <input type="file" name="bukti_bayar" class="form-control mb-2" accept=".jpg,.jpeg,.png" required>
        <button type="submit" class="btn btn-outline-primary w-100">Upload Bukti Bayar</button>
        <?= form_close() ?>
    </div>
</div>

<div class="text-center mt-4">
    <a href="<?= base_url('order/riwayat') ?>" class="btn btn-link">Lihat Riwayat Pesanan</a>
</div>
</div>
</div>
