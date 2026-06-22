<div class="row justify-content-center">
<div class="col-md-6">
<div class="card shadow-sm border-0">
<div class="card-body p-4">
    <div class="text-center mb-4">
        <i class="bi bi-shop-window fs-1 text-primary"></i>
        <h4 class="fw-bold mt-2">Buka Toko Kamu</h4>
        <p class="text-muted">Mulai jualan dan kelola dagangan langsung dari sini</p>
    </div>

    <?= form_open('toko/buka') ?>
    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Toko</label>
        <input type="text" name="nama_toko" class="form-control" placeholder="Contoh: Toko Snack Hemat" required
               value="<?= set_value('nama_toko') ?>">
    </div>
    <div class="mb-4">
        <label class="form-label fw-semibold">Nomor WhatsApp</label>
        <div class="input-group">
            <span class="input-group-text">+62</span>
            <input type="text" name="no_wa" class="form-control" placeholder="81234567890" required
                   value="<?= set_value('no_wa') ?>">
        </div>
        <small class="text-muted">Nomor ini akan dihubungi pembeli untuk konfirmasi pesanan.</small>
    </div>
    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-rocket-takeoff me-2"></i>Buka Toko Sekarang
    </button>
    <?= form_close() ?>
</div>
</div>
</div>
</div>
