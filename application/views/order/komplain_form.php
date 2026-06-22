<div class="row justify-content-center">
<div class="col-md-6">
<div class="card border-0 shadow-sm">
<div class="card-body p-4">
    <h5 class="fw-bold mb-1">Ajukan Komplain</h5>
    <p class="text-muted small mb-4">Order: <strong><?= htmlspecialchars($order->kode_order) ?></strong></p>

    <?= form_open_multipart('order/komplain/simpan') ?>
    <input type="hidden" name="id_order" value="<?= $order->id ?>">

    <div class="mb-3">
        <label class="form-label fw-semibold">Alasan Komplain <span class="text-danger">*</span></label>
        <select name="alasan" class="form-select" required>
            <option value="">-- Pilih Alasan --</option>
            <option value="Barang tidak sesuai">Barang tidak sesuai deskripsi</option>
            <option value="Barang rusak">Barang rusak/cacat</option>
            <option value="Barang tidak diterima">Barang tidak diterima</option>
            <option value="Penjual tidak responsif">Penjual tidak responsif</option>
            <option value="Lainnya">Lainnya</option>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi Masalah <span class="text-danger">*</span></label>
        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan masalah yang kamu alami secara detail..." required></textarea>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Lampiran Foto (opsional)</label>
        <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
        <small class="text-muted">Maks 2MB, format JPG/PNG</small>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-danger">Ajukan Komplain</button>
        <a href="<?= base_url('order/riwayat') ?>" class="btn btn-secondary">Batal</a>
    </div>
    <?= form_close() ?>
</div>
</div>
</div>
</div>
