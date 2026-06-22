<div class="row justify-content-center">
<div class="col-md-7">
<div class="card border-0 shadow-sm">
<div class="card-body p-4">
    <div class="text-center mb-4">
        <?php if ($user->foto_profil): ?>
        <img src="<?= htmlspecialchars($user->foto_profil) ?>" class="rounded-circle mb-2" width="70" height="70">
        <?php else: ?>
        <i class="bi bi-person-circle fs-1 text-primary"></i>
        <?php endif; ?>
        <h5 class="fw-bold mt-2 mb-0"><?= htmlspecialchars($user->nama) ?></h5>
        <p class="text-muted small mb-0"><?= htmlspecialchars($user->email) ?></p>
        <span class="badge bg-<?= $user->role == 1 ? 'success' : 'secondary' ?> mt-2">
            <?= $user->role == 1 ? 'Pembeli & Penjual' : 'Pembeli' ?>
        </span>
        <small class="text-muted d-block mt-1"><i class="bi bi-info-circle"></i> Foto profil diambil otomatis dari akun Google kamu.</small>
    </div>

    <?= form_open('profil/update') ?>

    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
        <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($user->nama) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user->email) ?>" readonly>
        <small class="text-muted">Email tidak dapat diubah karena terhubung dengan akun Google.</small>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Bio</label>
        <textarea name="bio" class="form-control" rows="2" placeholder="Ceritakan sedikit tentang dirimu..."><?= htmlspecialchars($user->bio ?? '') ?></textarea>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <label class="form-label fw-semibold">Fakultas</label>
            <input type="text" name="fakultas" class="form-control" placeholder="Contoh: Fakultas Teknik" value="<?= htmlspecialchars($user->fakultas ?? '') ?>">
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Jurusan</label>
            <input type="text" name="jurusan" class="form-control" placeholder="Contoh: Teknik Komputer" value="<?= htmlspecialchars($user->jurusan ?? '') ?>">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-semibold">Alamat Default</label>
        <textarea name="alamat_default" class="form-control" rows="2" placeholder="Alamat ini akan jadi acuan saat checkout (opsional)"><?= htmlspecialchars($user->alamat_default ?? '') ?></textarea>
    </div>

    <?php if ($user->role == 1): ?>
    <hr>
    <h6 class="fw-semibold mb-3"><i class="bi bi-shop me-2"></i>Informasi Toko</h6>

    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Toko <span class="text-danger">*</span></label>
        <input type="text" name="nama_toko" class="form-control" required value="<?= htmlspecialchars($user->nama_toko) ?>">
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Nomor WhatsApp <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">+62</span>
            <input type="text" name="no_wa" class="form-control" required value="<?= htmlspecialchars($user->no_wa) ?>">
        </div>
        <small class="text-muted">Nomor ini dipakai pembeli untuk menghubungi toko kamu.</small>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <label class="form-label fw-semibold">Jam Buka</label>
            <input type="time" name="jam_buka" class="form-control" value="<?= $user->jam_buka ? substr($user->jam_buka, 0, 5) : '' ?>">
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Jam Tutup</label>
            <input type="time" name="jam_tutup" class="form-control" value="<?= $user->jam_tutup ? substr($user->jam_tutup, 0, 5) : '' ?>">
        </div>
        <small class="text-muted mt-1">Kosongkan jika toko buka 24 jam atau tidak tentu.</small>
    </div>

    <div class="form-check form-switch mb-4">
        <input class="form-check-input" type="checkbox" name="toko_libur" id="tokoLibur" value="1" <?= $user->toko_libur ? 'checked' : '' ?>>
        <label class="form-check-label fw-semibold" for="tokoLibur">Toko sedang libur</label>
        <div><small class="text-muted">Aktifkan jika kamu sementara tidak menerima pesanan (akan tampil sebagai badge di profil toko).</small></div>
    </div>
    <?php else: ?>
    <div class="alert alert-info small mt-3">
        <i class="bi bi-info-circle me-2"></i>Belum berjualan? <a href="<?= base_url('toko/buka') ?>">Buka toko sekarang</a>.
    </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
        <i class="bi bi-save me-2"></i>Simpan Perubahan
    </button>

    <?= form_close() ?>
</div>
</div>
</div>
</div>
