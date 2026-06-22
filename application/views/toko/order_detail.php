<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Pesanan <?= htmlspecialchars($order->kode_order) ?></h4>
    <a href="<?= base_url('toko/order') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

<div class="row">
<div class="col-md-7">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Informasi Pembeli</div>
        <div class="card-body">
            <div class="row mb-2"><div class="col-4 text-muted">Nama</div><div class="col-8 fw-semibold"><?= htmlspecialchars($order->nama_pembeli) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Email</div><div class="col-8"><?= htmlspecialchars($order->email) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Alamat</div><div class="col-8"><?= nl2br(htmlspecialchars($order->alamat)) ?></div></div>
            <div class="row"><div class="col-4 text-muted">Zona</div><div class="col-8"><?= htmlspecialchars($order->area_name) ?></div></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Item Pesanan</div>
        <ul class="list-group list-group-flush">
        <?php foreach ($items as $item): ?>
        <li class="list-group-item d-flex justify-content-between">
            <span><?= htmlspecialchars($item->nama_barang) ?> x<?= $item->qty ?></span>
            <span class="fw-semibold"><?= rupiah($item->subtotal) ?></span>
        </li>
        <?php endforeach; ?>
        <li class="list-group-item d-flex justify-content-between"><span>Ongkir</span><span><?= rupiah($order->ongkir) ?></span></li>
        <li class="list-group-item d-flex justify-content-between fw-bold"><span>Total</span><span class="text-primary"><?= rupiah($order->total) ?></span></li>
        </ul>
    </div>

    <?php if ($order->bukti_bayar): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Bukti Pembayaran</div>
        <div class="card-body text-center">
            <img src="<?= base_url('assets/uploads/bukti_bayar/' . htmlspecialchars($order->bukti_bayar)) ?>" class="img-fluid rounded" style="max-height:400px">
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="col-md-5">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Ubah Status Pesanan</div>
        <div class="card-body">
            <?= form_open('toko/order/update_status/' . $order->id) ?>
            <select name="status" class="form-select mb-3">
                <option value="pending" <?= $order->status == 'pending' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                <option value="dikonfirmasi" <?= $order->status == 'dikonfirmasi' ? 'selected' : '' ?>>Pembayaran Diterima</option>
                <option value="diproses" <?= $order->status == 'diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
                <option value="selesai" <?= $order->status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="dibatalkan" <?= $order->status == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-primary w-100">Update Status</button>
            <?= form_close() ?>

            <hr>

            <?php $wa_number = preg_replace('/^0/', '62', $order->wa_pembeli ?? ''); ?>
            <p class="small text-muted mb-2">Hubungi pembeli untuk konfirmasi:</p>
            <a href="https://wa.me/<?= htmlspecialchars($wa_number) ?>" target="_blank" class="btn btn-success w-100">
                <i class="bi bi-whatsapp me-2"></i>Chat Pembeli
            </a>
        </div>
    </div>
</div>
</div>
