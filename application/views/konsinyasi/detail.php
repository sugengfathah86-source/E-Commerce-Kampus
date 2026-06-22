<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Titip Jual</h4>
    <a href="<?= base_url('konsinyasi/kelola') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row">
<div class="col-md-7">
    <div class="card border-0 shadow-sm mb-3">
        <?php if ($item->foto): ?>
        <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($item->foto)) ?>" class="w-100" style="max-height:250px;object-fit:cover">
        <?php endif; ?>
        <div class="card-body">
            <h5 class="fw-bold"><?= htmlspecialchars($item->nama_barang) ?></h5>
            <p class="text-muted"><?= nl2br(htmlspecialchars($item->deskripsi)) ?></p>

            <div class="row mb-2"><div class="col-4 text-muted">Penitip</div><div class="col-8 fw-semibold"><?= htmlspecialchars($item->nama_penitip) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">No. WA Penitip</div><div class="col-8"><?= htmlspecialchars($item->wa_penitip ?: '-') ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Harga Titipan</div><div class="col-8"><?= rupiah($item->harga_titipan) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Harga Jual</div><div class="col-8 fw-semibold text-primary"><?= rupiah($item->harga_jual) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Komisi Toko</div><div class="col-8"><?= rupiah($item->harga_jual - $item->harga_titipan) ?> / item</div></div>
            <div class="row"><div class="col-4 text-muted">Jumlah</div><div class="col-8"><?= $item->qty ?></div></div>
        </div>
    </div>
</div>

<div class="col-md-5">
    <?php if ($item->status === 'menunggu'): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Terima Pengajuan</div>
        <div class="card-body">
            <?= form_open('konsinyasi/terima/' . $item->id) ?>
            <label class="form-label small">Kategori Produk</label>
            <select name="id_kategori" class="form-select mb-3">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori as $k): ?>
                <option value="<?= $k->id ?>"><?= htmlspecialchars($k->nama_kategori) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="text-muted d-block mb-3">Menerima akan otomatis membuat produk baru di tokomu (status menunggu persetujuan admin).</small>
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-check-circle me-2"></i>Terima Titipan
            </button>
            <?= form_close() ?>
        </div>
    </div>

    <a href="<?= base_url('konsinyasi/tolak/' . $item->id) ?>" class="btn btn-outline-danger w-100" onclick="return confirm('Tolak pengajuan ini?')">
        <i class="bi bi-x-circle me-2"></i>Tolak Pengajuan
    </a>
    <?php else: ?>
    <div class="alert alert-info">
        Status pengajuan ini: <strong><?= $item->status ?></strong>
    </div>
    <?php endif; ?>
</div>
</div>
