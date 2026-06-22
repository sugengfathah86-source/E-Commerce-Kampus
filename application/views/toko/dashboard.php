<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard Toko: <?= htmlspecialchars($this->session->userdata('nama')) ?></h4>
    <a href="<?= base_url('toko/produk/tambah') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Produk
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <div class="card-body">
                <div class="small opacity-75">Total Produk</div>
                <div class="fs-3 fw-bold"><?= $total_produk ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">
            <div class="card-body">
                <div class="small opacity-75">Total Omzet</div>
                <div class="fs-5 fw-bold"><?= rupiah($total_omzet) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="card-body">
                <div class="small opacity-75">Total Order</div>
                <div class="fs-3 fw-bold"><?= $jumlah_order ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
            <div class="card-body">
                <div class="small opacity-75">Menunggu Konfirmasi</div>
                <div class="fs-3 fw-bold"><?= $order_pending ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <a href="<?= base_url('toko/produk') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-box-seam fs-1 text-primary"></i>
                <div>
                    <div class="fw-semibold">Kelola Produk</div>
                    <div class="small text-muted">Tambah, edit, atau hapus produk dagangan</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6">
        <a href="<?= base_url('toko/order') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-receipt fs-1 text-success"></i>
                <div>
                    <div class="fw-semibold">Kelola Pesanan</div>
                    <div class="small text-muted">Lihat & proses pesanan masuk</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 mt-3">
        <a href="<?= base_url('toko/laporan') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-bar-chart-line fs-1 text-info"></i>
                <div>
                    <div class="fw-semibold">Laporan Penjualan</div>
                    <div class="small text-muted">Rekap omzet & produk terlaris, export CSV</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 mt-3">
        <a href="<?= base_url('toko/voucher') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-ticket-perforated fs-1 text-warning"></i>
                <div>
                    <div class="fw-semibold">Voucher Toko</div>
                    <div class="small text-muted">Buat kode promo untuk pelanggan</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 mt-3">
        <a href="<?= base_url('toko/zona') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-truck fs-1 text-secondary"></i>
                <div>
                    <div class="fw-semibold">Zona Ongkir</div>
                    <div class="small text-muted">Atur area & biaya pengiriman</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 mt-3">
        <a href="<?= base_url('chat') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-chat-dots fs-1 text-primary"></i>
                <div>
                    <div class="fw-semibold">Pesan dari Pembeli</div>
                    <div class="small text-muted">Balas chat pelanggan</div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6 mt-3">
        <a href="<?= base_url('konsinyasi/kelola') ?>" class="card border-0 shadow-sm text-decoration-none h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="bi bi-box-arrow-in-down fs-1 text-success"></i>
                <div>
                    <div class="fw-semibold">Kelola Titip Jual</div>
                    <div class="small text-muted">Tinjau pengajuan konsinyasi dari mahasiswa lain</div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white fw-semibold">Pesanan Terbaru</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th class="ps-3">Kode</th><th>Pembeli</th><th>Total</th><th>Status</th><th>Tanggal</th></tr></thead>
            <tbody>
                <?php if (empty($orders)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
                <?php else: foreach ($orders as $o): ?>
                <tr>
                    <td class="ps-3"><?= htmlspecialchars($o->kode_order) ?></td>
                    <td><?= htmlspecialchars($o->nama_pembeli) ?></td>
                    <td class="fw-semibold"><?= rupiah($o->total) ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($o->status) ?></span></td>
                    <td><small><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></small></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
