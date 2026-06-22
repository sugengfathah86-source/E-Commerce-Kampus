<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Detail Komplain</h4>
    <a href="<?= base_url('admin/komplain') ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="row">
<div class="col-md-7">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Informasi Komplain</div>
        <div class="card-body">
            <div class="row mb-2"><div class="col-4 text-muted">Order</div><div class="col-8 fw-semibold"><?= htmlspecialchars($komplain->kode_order) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Pembeli</div><div class="col-8"><?= htmlspecialchars($komplain->nama_pembeli) ?> (<?= htmlspecialchars($komplain->email) ?>)</div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Total Order</div><div class="col-8"><?= rupiah($komplain->total) ?></div></div>
            <div class="row mb-2"><div class="col-4 text-muted">Alasan</div><div class="col-8 fw-semibold"><?= htmlspecialchars($komplain->alasan) ?></div></div>
            <div class="row"><div class="col-4 text-muted">Deskripsi</div><div class="col-8"><?= nl2br(htmlspecialchars($komplain->deskripsi)) ?></div></div>
        </div>
    </div>

    <?php if ($komplain->foto): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Lampiran Foto</div>
        <div class="card-body text-center">
            <img src="<?= base_url('assets/uploads/komplain/' . htmlspecialchars($komplain->foto)) ?>" class="img-fluid rounded" style="max-height:350px">
        </div>
    </div>
    <?php endif; ?>
</div>

<div class="col-md-5">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Tanggapan Admin</div>
        <div class="card-body">
            <?= form_open('admin/komplain/tanggapi/' . $komplain->id) ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="terbuka" <?= $komplain->status == 'terbuka' ? 'selected' : '' ?>>Terbuka</option>
                    <option value="ditinjau" <?= $komplain->status == 'ditinjau' ? 'selected' : '' ?>>Sedang Ditinjau</option>
                    <option value="selesai" <?= $komplain->status == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="ditolak" <?= $komplain->status == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Tanggapan</label>
                <textarea name="tanggapan" class="form-control" rows="4" placeholder="Tulis tanggapan untuk pembeli..."><?= htmlspecialchars($komplain->tanggapan_admin) ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Tanggapan</button>
            <?= form_close() ?>
        </div>
    </div>
</div>
</div>
