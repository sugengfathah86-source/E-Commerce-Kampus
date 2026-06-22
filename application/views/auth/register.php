<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - E-Commerce Kampus</title>
    <link href="<?= base_url('assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .register-card {
            background: #fff; border-radius: 16px; padding: 40px;
            max-width: 440px; width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,.25);
        }
        .btn-primary-custom {
            background: #4f46e5; border: none; color: #fff;
            font-weight: 600; padding: 12px; border-radius: 8px;
            width: 100%; font-size: 1rem;
        }
        .btn-primary-custom:hover { background: #4338ca; color: #fff; }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 .2rem rgba(79,70,229,.25); }
        .divider { display: flex; align-items: center; gap: 10px; color: #aaa; margin: 16px 0; }
        .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #eee; }
        .btn-google {
            background: #fff; border: 1px solid #ddd; color: #333;
            font-weight: 600; padding: 12px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; text-decoration: none;
        }
        .btn-google:hover { background: #f8f9fa; }
    </style>
</head>
<body>
<div class="register-card">
    <div class="text-center mb-3">
        <i class="bi bi-shop fs-1 text-primary"></i>
    </div>
    <h4 class="fw-bold mb-1 text-center">Daftar Akun</h4>
    <p class="text-muted text-center mb-4">E-Commerce Kampus</p>

    <?php if ($this->session->flashdata('error')): ?>
    <div class="alert alert-danger small"><?= htmlspecialchars($this->session->flashdata('error')) ?></div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('success')): ?>
    <div class="alert alert-success small"><?= htmlspecialchars($this->session->flashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (isset($errors) && $errors): ?>
    <div class="alert alert-danger small">
        <?php foreach ($errors as $e): ?>
            <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('auth/register_process') ?>" method="POST">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

        <div class="mb-3">
            <label class="form-label fw-semibold">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap"
                   value="<?= set_value('nama') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Email</label>
            <input type="email" name="email" class="form-control" placeholder="email@mhs.unsoed.ac.id"
                   value="<?= set_value('email') ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Konfirmasi Password</label>
            <div class="input-group">
                <input type="password" name="konfirmasi_password" id="konfirmasi_password" class="form-control" placeholder="Ulangi password" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePass('konfirmasi_password', this)">
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-primary-custom btn">Daftar Sekarang</button>
    </form>

    <p class="text-center text-muted small mt-4">
        Sudah punya akun? <a href="<?= base_url('login') ?>" class="text-primary fw-semibold">Masuk di sini</a>
    </p>
</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
