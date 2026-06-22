<div class="row justify-content-center">
<div class="col-md-8">
<div class="card border-0 shadow-sm">
<div class="card-body p-4">
    <h5 class="fw-bold mb-1"><?= isset($produk) ? 'Edit Produk' : 'Tambah Produk Baru' ?></h5>
    <?php if (!isset($produk)): ?>
    <p class="text-muted small mb-4"><i class="bi bi-info-circle me-1"></i>Produk baru akan menunggu persetujuan admin sebelum tampil ke publik.</p>
    <?php else: ?>
    <div class="mb-4"></div>
    <?php endif; ?>

    <?php
    $action = isset($produk) ? base_url('toko/produk/update/' . $produk->id) : base_url('toko/produk/simpan');
    echo form_open_multipart($action);
    ?>

    <div class="mb-3">
        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
        <input type="text" name="nama_barang" class="form-control" required
               value="<?= htmlspecialchars(isset($produk) ? $produk->nama_barang : set_value('nama_barang')) ?>">
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Kategori</label>
            <select name="id_kategori" class="form-select">
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategori as $k): ?>
                <option value="<?= $k->id ?>" <?= (isset($produk) && $produk->id_kategori == $k->id) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k->nama_kategori) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Harga Dasar <span class="text-danger">*</span></label>
            <input type="number" name="harga" class="form-control" required min="1"
                   value="<?= isset($produk) ? $produk->harga : '' ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Stok <?= isset($produk) ? '' : '<span class="text-danger">*</span>' ?></label>
            <input type="number" name="stok" class="form-control" <?= isset($produk) ? '' : 'required' ?> min="0"
                   value="<?= isset($produk) ? $produk->stok : '0' ?>">
            <small class="text-muted">Jika punya variasi, isi 0 di sini — stok dihitung per variasi.</small>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"><?= htmlspecialchars(isset($produk) ? $produk->deskripsi : set_value('deskripsi')) ?></textarea>
    </div>

    <!-- Pre-Order -->
    <div class="card bg-light border-0 mb-3">
        <div class="card-body py-3">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="is_preorder" id="isPreorder" value="1"
                       <?= (isset($produk) && $produk->is_preorder) ? 'checked' : '' ?>
                       onchange="document.getElementById('estimasiWrap').style.display = this.checked ? 'block' : 'none'">
                <label class="form-check-label fw-semibold" for="isPreorder">Produk ini adalah Pre-Order</label>
            </div>
            <div id="estimasiWrap" style="display: <?= (isset($produk) && $produk->is_preorder) ? 'block' : 'none' ?>">
                <label class="form-label small">Estimasi Waktu Pengerjaan</label>
                <input type="text" name="estimasi_preorder" class="form-control" placeholder="Contoh: 3-5 hari kerja"
                       value="<?= htmlspecialchars(isset($produk) ? $produk->estimasi_preorder : '') ?>">
            </div>
        </div>
    </div>

    <?php if (isset($produk)): ?>
    <div class="mb-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
            <option value="aktif" <?= $produk->status == 'aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="nonaktif" <?= $produk->status == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
            <?php if ($produk->status == 'pending_approval'): ?>
            <option value="pending_approval" selected disabled>Menunggu Persetujuan Admin</option>
            <?php elseif ($produk->status == 'ditolak'): ?>
            <option value="ditolak" selected disabled>Ditolak Admin</option>
            <?php endif; ?>
        </select>
        <?php if ($produk->status == 'ditolak' && $produk->catatan_admin): ?>
        <div class="alert alert-danger small mt-2 mb-0"><i class="bi bi-x-circle me-1"></i>Alasan ditolak: <?= htmlspecialchars($produk->catatan_admin) ?></div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="mb-3">
        <label class="form-label fw-semibold">Foto Utama</label>
        <?php if (isset($produk) && $produk->foto): ?>
        <div class="mb-2"><img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($produk->foto)) ?>" width="100" class="rounded"></div>
        <?php endif; ?>
        <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
        <small class="text-muted"><?= isset($produk) ? 'Kosongkan jika tidak ingin mengubah foto. ' : '' ?>Maks 2MB, format JPG/PNG/WEBP</small>
    </div>

    <!-- Galeri Foto Tambahan -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Galeri Foto Tambahan <small class="text-muted">(maks. 4)</small></label>
        <?php if (isset($galeri) && !empty($galeri)): ?>
        <div class="d-flex gap-2 flex-wrap mb-2">
            <?php foreach ($galeri as $g): ?>
            <div class="position-relative">
                <img src="<?= base_url('assets/uploads/produk/' . htmlspecialchars($g->foto)) ?>" width="70" height="70" class="rounded border" style="object-fit:cover">
                <a href="<?= base_url('toko/produk/foto/hapus/' . $g->id) ?>" class="btn btn-danger btn-sm position-absolute top-0 end-0 p-0"
                   style="width:20px;height:20px;font-size:.6rem;line-height:1" onclick="return confirm('Hapus foto ini?')">✕</a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <input type="file" name="galeri[]" class="form-control" accept=".jpg,.jpeg,.png,.webp" multiple>
        <small class="text-muted">Boleh pilih beberapa foto sekaligus. Total maksimal 4 foto galeri.</small>
    </div>

    <!-- Variasi Produk -->
    <div class="mb-4">
        <label class="form-label fw-semibold">Variasi Produk <small class="text-muted">(opsional, contoh: Ukuran S/M/L)</small></label>
        <div id="variasiList">
            <?php if (!empty($variasi)): foreach ($variasi as $v): ?>
            <div class="row g-2 mb-2 align-items-center variasi-row">
                <input type="hidden" name="variasi_id[]" value="<?= $v->id ?>">
                <div class="col-5">
                    <input type="text" name="nama_variasi[]" class="form-control form-control-sm" placeholder="Nama variasi" value="<?= htmlspecialchars($v->nama_variasi) ?>">
                </div>
                <div class="col-3">
                    <input type="number" name="stok_variasi[]" class="form-control form-control-sm" placeholder="Stok" min="0" value="<?= $v->stok ?>">
                </div>
                <div class="col-3">
                    <input type="number" name="harga_tambahan[]" class="form-control form-control-sm" placeholder="+Harga" min="0" value="<?= $v->harga_tambahan ?>">
                </div>
                <div class="col-1">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="hapusVariasi(this, <?= $v->id ?>)"><i class="bi bi-trash"></i></button>
                </div>
            </div>
            <?php endforeach; endif; ?>
        </div>
        <div id="variasiHapusWrap"></div>
        <button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="tambahVariasiRow()">
            <i class="bi bi-plus-circle me-1"></i>Tambah Variasi
        </button>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary"><?= isset($produk) ? 'Simpan Perubahan' : 'Simpan Produk' ?></button>
        <a href="<?= base_url('toko/produk') ?>" class="btn btn-secondary">Batal</a>
    </div>

    <?= form_close() ?>
</div>
</div>
</div>
</div>

<script>
function tambahVariasiRow() {
    const wrap = document.getElementById('variasiList');
    const row = document.createElement('div');
    row.className = 'row g-2 mb-2 align-items-center variasi-row';
    row.innerHTML = `
        <input type="hidden" name="variasi_id[]" value="0">
        <div class="col-5"><input type="text" name="nama_variasi[]" class="form-control form-control-sm" placeholder="Nama variasi"></div>
        <div class="col-3"><input type="number" name="stok_variasi[]" class="form-control form-control-sm" placeholder="Stok" min="0"></div>
        <div class="col-3"><input type="number" name="harga_tambahan[]" class="form-control form-control-sm" placeholder="+Harga" min="0"></div>
        <div class="col-1"><button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="hapusVariasiBaru(this)"><i class="bi bi-trash"></i></button></div>
    `;
    wrap.appendChild(row);
}

function hapusVariasiBaru(btn) {
    btn.closest('.variasi-row').remove();
}

function hapusVariasi(btn, id) {
    const wrap = document.getElementById('variasiHapusWrap');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'variasi_hapus[]';
    input.value = id;
    wrap.appendChild(input);
    btn.closest('.variasi-row').remove();
}
</script>
