<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-truck me-2"></i>Zona Ongkir</h4>
</div>

<div class="row">
<div class="col-md-5">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Tambah Zona Baru</div>
        <div class="card-body">
            <?= form_open('toko/zona/tambah') ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Area</label>
                <input type="text" name="area_name" class="form-control" placeholder="Contoh: Gedung Fakultas Teknik" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ongkos Kirim</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="fee" class="form-control" min="0" placeholder="0 untuk gratis ongkir" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-plus-circle me-2"></i>Tambah Zona
            </button>
            <?= form_close() ?>
        </div>
    </div>
</div>

<div class="col-md-7">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Zona Aktif Toko Kamu</div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th class="ps-3">Area</th><th>Ongkir</th><th class="text-center">Aksi</th></tr></thead>
                <tbody>
                <?php if (empty($zona)): ?>
                <tr><td colspan="3" class="text-center text-muted py-4">Belum ada zona ongkir</td></tr>
                <?php else: foreach ($zona as $z): ?>
                <tr>
                    <td class="ps-3 fw-semibold"><?= htmlspecialchars($z->area_name) ?></td>
                    <td><?= $z->fee == 0 ? '<span class="badge badge-free">Gratis Ongkir</span>' : rupiah($z->fee) ?></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editZona<?= $z->id ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <a href="<?= base_url('toko/zona/hapus/' . $z->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus zona ini?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal Edit -->
                <div class="modal fade" id="editZona<?= $z->id ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <?= form_open('toko/zona/update/' . $z->id) ?>
                            <div class="modal-header">
                                <h6 class="modal-title">Edit Zona</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Area</label>
                                    <input type="text" name="area_name" class="form-control" value="<?= htmlspecialchars($z->area_name) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ongkos Kirim</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="fee" class="form-control" min="0" value="<?= $z->fee ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            <?= form_close() ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
