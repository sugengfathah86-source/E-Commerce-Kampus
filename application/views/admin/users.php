<h4 class="fw-bold mb-4"><i class="bi bi-people me-2"></i>Kelola Pengguna</h4>

<div class="mb-3 d-flex gap-2">
    <a href="<?= base_url('admin/users') ?>" class="btn btn-sm <?= $filter_role === null ? 'btn-primary' : 'btn-outline-secondary' ?>">Semua</a>
    <a href="<?= base_url('admin/users?role=0') ?>" class="btn btn-sm <?= $filter_role === 0 ? 'btn-primary' : 'btn-outline-secondary' ?>">Pembeli</a>
    <a href="<?= base_url('admin/users?role=1') ?>" class="btn btn-sm <?= $filter_role === 1 ? 'btn-primary' : 'btn-outline-secondary' ?>">Penjual</a>
</div>

<div class="card border-0 shadow-sm">
<div class="table-responsive">
<table class="table mb-0 align-middle">
    <thead><tr><th class="ps-3">Nama</th><th>Email</th><th>Role</th><th>Status</th><th>Bergabung</th><th class="text-center">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($users as $u): ?>
    <tr>
        <td class="ps-3">
            <?= htmlspecialchars($u->nama) ?>
            <?php if ($u->toko_verified): ?><i class="bi bi-patch-check-fill text-primary small" title="Toko Terverifikasi"></i><?php endif; ?>
            <?php if ($u->nama_toko): ?><br><small class="text-muted"><?= htmlspecialchars($u->nama_toko) ?></small><?php endif; ?>
        </td>
        <td><small><?= htmlspecialchars($u->email) ?></small></td>
        <td><span class="badge bg-<?= $u->role == 2 ? 'danger' : ($u->role == 1 ? 'success' : 'secondary') ?>">
            <?= $u->role == 2 ? 'Admin' : ($u->role == 1 ? 'Penjual' : 'Pembeli') ?>
        </span></td>
        <td><span class="badge bg-<?= $u->status_akun == 'aktif' ? 'success' : 'danger' ?>"><?= $u->status_akun ?></span></td>
        <td><small><?= date('d/m/Y', strtotime($u->created_at)) ?></small></td>
        <td class="text-center">
            <div class="btn-group">
                <?php if ($u->role == 1 && !$u->toko_verified): ?>
                <a href="<?= base_url('admin/users/verifikasi/' . $u->id) ?>" class="btn btn-sm btn-outline-primary" title="Verifikasi Toko"><i class="bi bi-patch-check"></i></a>
                <?php elseif ($u->role == 1 && $u->toko_verified): ?>
                <a href="<?= base_url('admin/users/batal-verifikasi/' . $u->id) ?>" class="btn btn-sm btn-outline-secondary" title="Cabut Verifikasi"><i class="bi bi-patch-minus"></i></a>
                <?php endif; ?>

                <?php if ($u->status_akun == 'aktif'): ?>
                <a href="<?= base_url('admin/users/suspend/' . $u->id) ?>" class="btn btn-sm btn-outline-danger" title="Suspend" onclick="return confirm('Suspend akun ini?')"><i class="bi bi-slash-circle"></i></a>
                <?php else: ?>
                <a href="<?= base_url('admin/users/aktifkan/' . $u->id) ?>" class="btn btn-sm btn-outline-success" title="Aktifkan"><i class="bi bi-check-circle"></i></a>
                <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
