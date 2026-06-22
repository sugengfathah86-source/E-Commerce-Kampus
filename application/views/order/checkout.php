<h4 class="fw-bold mb-4">Checkout - <?= htmlspecialchars($penjual->nama_toko ?: $penjual->nama) ?></h4>

<?php if ($ada_preorder): ?>
<div class="alert alert-warning"><i class="bi bi-clock-history me-2"></i>Pesanan ini mengandung produk Pre-Order. Waktu pengerjaan akan lebih lama dari biasanya.</div>
<?php endif; ?>

<div class="row">
<div class="col-md-7">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white fw-semibold">Ringkasan Pesanan</div>
        <ul class="list-group list-group-flush">
        <?php foreach ($items as $item): $harga_satuan = $item->harga_dasar + ($item->harga_tambahan ?? 0); ?>
        <li class="list-group-item d-flex justify-content-between">
            <span>
                <?= htmlspecialchars($item->nama_barang) ?>
                <?php if ($item->nama_variasi): ?><small class="text-muted">(<?= htmlspecialchars($item->nama_variasi) ?>)</small><?php endif; ?>
                x<?= $item->qty ?>
            </span>
            <span class="fw-semibold"><?= rupiah($harga_satuan * $item->qty) ?></span>
        </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <!-- Voucher -->
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label fw-semibold">Kode Voucher</label>
            <?= form_open('order/cek_voucher', ['class' => 'd-flex gap-2']) ?>
            <input type="hidden" name="id_penjual" value="<?= $id_penjual ?>">
            <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
            <input type="text" name="kode_voucher" class="form-control" placeholder="Masukkan kode voucher" value="<?= $this->session->flashdata('voucher_kode') ?>">
            <button type="submit" class="btn btn-outline-primary">Terapkan</button>
            <?= form_close() ?>
        </div>
    </div>

    <?= form_open('order/simpan') ?>
    <input type="hidden" name="id_penjual" value="<?= $id_penjual ?>">
    <input type="hidden" name="kode_voucher" value="<?= htmlspecialchars($this->session->flashdata('voucher_kode') ?? '') ?>">

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label fw-semibold">Pilih Zona Pengiriman <span class="text-danger">*</span></label>
            <?php if (empty($zona_list)): ?>
            <div class="alert alert-danger small mb-0">Toko ini belum mengatur zona pengiriman. Hubungi penjual.</div>
            <?php endif; ?>
            <?php foreach ($zona_list as $zona): ?>
            <div class="form-check border rounded p-2 mb-2">
                <input class="form-check-input" type="radio" name="id_zona" value="<?= $zona->id ?>" id="zona<?= $zona->id ?>" required onchange="updateTotal()" data-fee="<?= $zona->fee ?>">
                <label class="form-check-label d-flex justify-content-between w-100" for="zona<?= $zona->id ?>">
                    <span><?= htmlspecialchars($zona->area_name) ?></span>
                    <?php if ($zona->fee == 0): ?>
                    <span class="badge badge-free">Gratis Ongkir</span>
                    <?php else: ?>
                    <span class="fw-semibold"><?= rupiah($zona->fee) ?></span>
                    <?php endif; ?>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label fw-semibold">Alamat Lengkap <span class="text-danger">*</span></label>
            <textarea name="alamat" class="form-control" rows="3" placeholder="Contoh: Kost Wisma Asri No. 5, Blok C" required><?= set_value('alamat') ?></textarea>
        </div>
    </div>

    <?php if ($pembeli->poin > 0): ?>
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label fw-semibold">Gunakan Poin <small class="text-muted">(Poin kamu: <?= number_format($pembeli->poin) ?>)</small></label>
            <input type="number" name="poin_dipakai" id="poinDipakai" class="form-control" min="0" max="<?= min($pembeli->poin, $subtotal) ?>" value="0" onchange="updateTotal()">
            <small class="text-muted">1 poin = Rp 1. Maksimal sebesar subtotal pesanan.</small>
        </div>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-bag-check me-2"></i>Buat Pesanan
    </button>
    <?= form_close() ?>
</div>

<div class="col-md-5">
    <div class="card border-0 shadow-sm sticky-top" style="top:20px">
        <div class="card-body">
            <h6 class="fw-bold mb-3">Total Pembayaran</h6>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Subtotal</span>
                <span><?= rupiah($subtotal) ?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Ongkos Kirim</span>
                <span id="ongkirLabel"><?= rupiah(0) ?></span>
            </div>
            <?php
            $voucher_kode_aktif = $this->session->flashdata('voucher_kode');
            $diskon_voucher_tampil = 0;
            if ($voucher_kode_aktif) {
                $hasil = $this->Voucher_model->validasi($voucher_kode_aktif, $pembeli->id, $id_penjual, $subtotal);
                if ($hasil['valid']) { $diskon_voucher_tampil = $hasil['potongan']; }
            }
            ?>
            <?php if ($diskon_voucher_tampil > 0): ?>
            <div class="d-flex justify-content-between mb-2 text-success">
                <span>Diskon Voucher</span>
                <span id="voucherLabel">- <?= rupiah($diskon_voucher_tampil) ?></span>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between mb-2 text-success" id="poinRow" style="display:none">
                <span>Poin Dipakai</span>
                <span id="poinLabel">- Rp 0</span>
            </div>
            <hr>
            <div class="d-flex justify-content-between fw-bold fs-5">
                <span>Total</span>
                <span class="text-primary" id="totalLabel"><?= rupiah($subtotal - $diskon_voucher_tampil) ?></span>
            </div>
        </div>
    </div>
</div>
</div>

<script>
const subtotal = <?= $subtotal ?>;
const diskonVoucher = <?= $diskon_voucher_tampil ?>;

function updateTotal() {
    const zonaChecked = document.querySelector('input[name="id_zona"]:checked');
    const fee = zonaChecked ? parseInt(zonaChecked.dataset.fee) : 0;
    document.getElementById('ongkirLabel').textContent = 'Rp ' + fee.toLocaleString('id-ID');

    let poin = 0;
    const poinInput = document.getElementById('poinDipakai');
    if (poinInput) {
        poin = parseInt(poinInput.value) || 0;
        const maxPoin = subtotal - diskonVoucher;
        if (poin > maxPoin) { poin = maxPoin; poinInput.value = poin; }
        document.getElementById('poinRow').style.display = poin > 0 ? 'flex' : 'none';
        document.getElementById('poinLabel').textContent = '- Rp ' + poin.toLocaleString('id-ID');
    }

    const total = Math.max(0, subtotal + fee - diskonVoucher - poin);
    document.getElementById('totalLabel').textContent = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
