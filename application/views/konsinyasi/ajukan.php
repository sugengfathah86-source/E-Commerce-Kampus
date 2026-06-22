<div class="row justify-content-center">
<div class="col-md-7">
<div class="card border-0 shadow-sm">
<div class="card-body p-4">
    <h5 class="fw-bold mb-1">Ajukan Titip Jual</h5>
    <p class="text-muted small mb-4">Titipkan barangmu untuk dijual lewat toko mahasiswa lain.</p>

    <?= form_open_multipart('konsinyasi/simpan') ?>

    <div class="mb-3">
        <label class="form-label fw-semibold">Pilih Toko Penitipan <span class="text-danger">*</span></label>
        <select name="id_penjual" class="form-select" required>
            <option value="">-- Pilih Toko --</option>
            <?php foreach ($penjual as $p): ?>
            <?php if ($p->id != $this->session->userdata('user_id')): ?>
            <option value="<?= $p->id ?>"><?= htmlspecialchars($p->nama_toko ?: $p->nama) ?></option>
            <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
        <input type="text" name="nama_barang" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Kondisi barang, alasan dijual, dll."></textarea>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold">Harga Titipan <span class="text-danger">*</span></label>
            <input type="number" name="harga_titipan" class="form-control" min="1" required>
            <small class="text-muted">Uang yang kamu terima</small>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Harga Jual <span class="text-danger">*</span></label>
            <input type="number" name="harga_jual" class="form-control" min="1" required>
            <small class="text-muted">Harga ke pembeli (selisihnya komisi toko)</small>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="qty" class="form-control" min="1" value="1" required>
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Foto Barang</label>
        <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted">Maks 2MB, format JPG/PNG/WEBP</small>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2">Kirim Pengajuan</button>
    <?= form_close() ?>
</div>
</div>
</div>
</div>
