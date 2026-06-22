<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-bar-chart-line me-2"></i>Laporan Penjualan</h4>
    <a href="<?= base_url('toko/laporan/export?dari=' . $dari . '&sampai=' . $sampai) ?>" class="btn btn-success">
        <i class="bi bi-download me-2"></i>Export CSV
    </a>
</div>

<!-- Filter Tanggal -->
<div class="card border-0 shadow-sm p-3 mb-3">
    <?= form_open('toko/laporan', ['method' => 'get', 'class' => 'd-flex gap-2 align-items-end flex-wrap']) ?>
    <div>
        <label class="form-label fw-semibold mb-1 small">Dari Tanggal</label>
        <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
    </div>
    <div>
        <label class="form-label fw-semibold mb-1 small">Sampai Tanggal</label>
        <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
    </div>
    <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-2"></i>Filter</button>
    <?= form_close() ?>
</div>

<!-- Summary -->
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <div class="card-body text-white">
                <div class="small opacity-75">Periode</div>
                <div class="fw-bold" style="font-size:1rem"><?= date('d/m/Y', strtotime($dari)) ?> — <?= date('d/m/Y', strtotime($sampai)) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="card-body text-white">
                <div class="small opacity-75">Total Order (Selesai)</div>
                <div class="fs-3 fw-bold"><?= $total_selesai ?> <small class="fs-6 opacity-75">/ <?= $total_order ?> total</small></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">
            <div class="card-body text-white">
                <div class="small opacity-75">Total Omzet</div>
                <div class="fs-4 fw-bold"><?= rupiah($total_omzet) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Rekap Produk Terjual -->
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Produk Terlaris</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th class="ps-3">Produk</th><th class="text-center">Terjual</th><th class="text-end">Pendapatan</th></tr></thead>
                    <tbody>
                    <?php if (empty($rekap_produk)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4">Belum ada penjualan pada periode ini</td></tr>
                    <?php else: foreach ($rekap_produk as $r): ?>
                    <tr>
                        <td class="ps-3"><?= htmlspecialchars($r->nama_barang) ?></td>
                        <td class="text-center"><?= $r->total_qty ?></td>
                        <td class="text-end fw-semibold"><?= rupiah($r->total_pendapatan) ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Riwayat Order -->
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Riwayat Order</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th class="ps-3">Kode</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
                    <tbody>
                    <?php
                    $status_badge = ['pending' => 'secondary', 'dikonfirmasi' => 'info', 'diproses' => 'warning', 'selesai' => 'success', 'dibatalkan' => 'danger'];
                    ?>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada order pada periode ini</td></tr>
                    <?php else: foreach ($orders as $o): ?>
                    <tr>
                        <td class="ps-3"><?= htmlspecialchars($o->kode_order) ?></td>
                        <td><?= htmlspecialchars($o->nama_pembeli) ?></td>
                        <td class="fw-semibold"><?= rupiah($o->total) ?></td>
                        <td><span class="badge bg-<?= $status_badge[$o->status] ?? 'secondary' ?>"><?= $o->status ?></span></td>
                        <td><small><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></small></td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
