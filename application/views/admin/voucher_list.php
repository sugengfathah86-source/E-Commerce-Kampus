<h4 class="fw-bold mb-4"><i class="bi bi-ticket-perforated me-2"></i>Voucher Platform</h4>

<div class="row">
<div class="col-md-5">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Buat Voucher Baru</div>
        <div class="card-body">
            <?= form_open('admin/voucher/tambah') ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Kode Voucher</label>
                <input type="text" name="kode" class="form-control text-uppercase" placeholder="KAMPUS10" required>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label fw-semibold">Tipe</label>
                    <select name="tipe" class="form-select">
                        <option value="nominal">Nominal (Rp)</option>
                        <option value="persen">Persen (%)</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label fw-semibold">Nilai</label>
                    <input type="number" name="nilai" class="form-control" min="1" required>
                </div>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small">Min. Belanja</label>
                    <input type="number" name="min_belanja" class="form-control" min="0" value="0">
                </div>
                <div class="col-6">
                    <label class="form-label small">Maks. Potongan</label>
                    <input type="number" name="maks_potongan" class="form-control" min="0" placeholder="Tanpa batas">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small">Kuota Pemakaian</label>
                <input type="number" name="kuota" class="form-control" min="1" placeholder="Tanpa batas">
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small">Berlaku Dari</label>
                    <input type="date" name="berlaku_dari" class="form-control">
                </div>
                <div class="col-6">
                    <label class="form-label small">Berlaku Sampai</label>
                    <input type="date" name="berlaku_sampai" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Buat Voucher</button>
            <?= form_close() ?>
        </div>
    </div>
</div>

<div class="col-md-7">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Daftar Voucher Platform</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th class="ps-3">Kode</th><th>Nilai</th><th>Terpakai</th><th>Status</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                <?php if (empty($vouchers)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada voucher platform</td></tr>
                <?php else: foreach ($vouchers as $v): ?>
                <tr>
                    <td class="ps-3 fw-semibold"><?= htmlspecialchars($v->kode) ?></td>
                    <td><?= $v->tipe == 'persen' ? $v->nilai . '%' : rupiah($v->nilai) ?></td>
                    <td><?= $v->terpakai ?><?= $v->kuota ? ' / ' . $v->kuota : '' ?></td>
                    <td><span class="badge bg-<?= $v->status == 'aktif' ? 'success' : 'secondary' ?>"><?= $v->status ?></span></td>
                    <td class="text-center">
                        <?php if ($v->status == 'aktif'): ?>
                        <a href="<?= base_url('admin/voucher/nonaktifkan/' . $v->id) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan voucher ini?')">Nonaktifkan</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
