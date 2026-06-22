<h4 class="fw-bold mb-4"><i class="bi bi-speedometer2 me-2"></i>Dashboard Admin</h4>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#667eea,#764ba2)">
            <div class="card-body">
                <div class="small opacity-75">Total Pengguna</div>
                <div class="fs-3 fw-bold"><?= number_format($total_user) ?></div>
                <div class="small opacity-75"><?= $total_penjual ?> penjual aktif</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#43e97b,#38f9d7)">
            <div class="card-body">
                <div class="small opacity-75">Total GMV</div>
                <div class="fs-5 fw-bold"><?= rupiah($total_gmv) ?></div>
                <div class="small opacity-75">dari transaksi selesai</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white" style="background:linear-gradient(135deg,#4facfe,#00f2fe)">
            <div class="card-body">
                <div class="small opacity-75">Total Produk</div>
                <div class="fs-3 fw-bold"><?= number_format($total_produk) ?></div>
                <div class="small opacity-75"><?= $total_transaksi ?> transaksi</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('admin/produk-pending') ?>" class="card border-0 shadow-sm text-white text-decoration-none" style="background:linear-gradient(135deg,#f093fb,#f5576c)">
            <div class="card-body">
                <div class="small opacity-75">Perlu Ditinjau</div>
                <div class="fs-3 fw-bold"><?= $pending_approval ?> <small class="fs-6">produk</small></div>
                <div class="small opacity-75"><?= $komplain_terbuka ?> komplain terbuka</div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Omzet Platform (6 Bulan Terakhir)</div>
            <div class="card-body">
                <canvas id="chartOmzet" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Kategori Terpopuler</div>
            <ul class="list-group list-group-flush">
            <?php if (empty($kategori_populer)): ?>
            <li class="list-group-item text-center text-muted py-4">Belum ada data</li>
            <?php else: foreach ($kategori_populer as $k): ?>
            <li class="list-group-item d-flex justify-content-between">
                <span><?= htmlspecialchars($k->nama_kategori) ?></span>
                <span class="badge bg-primary"><?= $k->jumlah_produk ?> produk</span>
            </li>
            <?php endforeach; endif; ?>
            </ul>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white fw-semibold">Pengguna Terbaru</div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th class="ps-3">Nama</th><th>Email</th><th>Role</th><th>Bergabung</th></tr></thead>
                    <tbody>
                    <?php foreach ($user_terbaru as $u): ?>
                    <tr>
                        <td class="ps-3"><?= htmlspecialchars($u->nama) ?></td>
                        <td><small><?= htmlspecialchars($u->email) ?></small></td>
                        <td><span class="badge bg-<?= $u->role == 2 ? 'danger' : ($u->role == 1 ? 'success' : 'secondary') ?>">
                            <?= $u->role == 2 ? 'Admin' : ($u->role == 1 ? 'Penjual' : 'Pembeli') ?>
                        </span></td>
                        <td><small><?= date('d/m/Y', strtotime($u->created_at)) ?></small></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const omzetData = <?= json_encode($omzet_per_bulan) ?>;
const labels = omzetData.map(d => d.bulan);
const values = omzetData.map(d => parseFloat(d.total));

new Chart(document.getElementById('chartOmzet'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Omzet (Rp)',
            data: values,
            borderColor: '#4f46e5',
            backgroundColor: 'rgba(79,70,229,.1)',
            fill: true,
            tension: 0.3
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } } }
    }
});
</script>
