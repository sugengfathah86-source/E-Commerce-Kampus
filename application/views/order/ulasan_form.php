<div class="row justify-content-center">
<div class="col-md-7">
<h5 class="fw-bold mb-1">Beri Ulasan</h5>
<p class="text-muted small mb-4">Order: <strong><?= htmlspecialchars($order->kode_order) ?></strong></p>

<?= form_open('order/ulasan/simpan') ?>
<input type="hidden" name="id_order" value="<?= $order->id ?>">

<?php foreach ($items as $i => $item): ?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <input type="hidden" name="id_produk[]" value="<?= $item->id_produk ?>">
        <h6 class="fw-semibold mb-3"><?= htmlspecialchars($item->nama_barang) ?></h6>

        <div class="mb-3">
            <label class="form-label small">Rating</label>
            <div class="d-flex gap-1 fs-4 rating-stars" data-index="<?= $i ?>">
                <?php for ($s = 1; $s <= 5; $s++): ?>
                <i class="bi bi-star text-warning star-icon" data-value="<?= $s ?>" style="cursor:pointer"></i>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="rating[]" class="rating-input" data-index="<?= $i ?>" value="0" required>
        </div>

        <div class="mb-0">
            <label class="form-label small">Komentar</label>
            <textarea name="komentar[]" class="form-control" rows="2" placeholder="Bagaimana kualitas produk ini?"></textarea>
        </div>
    </div>
</div>
<?php endforeach; ?>

<button type="submit" class="btn btn-primary w-100 py-2">Kirim Semua Ulasan</button>
<?= form_close() ?>
</div>
</div>

<script>
document.querySelectorAll('.rating-stars').forEach(function(wrap) {
    const index = wrap.dataset.index;
    const input = document.querySelector(`.rating-input[data-index="${index}"]`);
    const stars = wrap.querySelectorAll('.star-icon');

    stars.forEach(function(star) {
        star.addEventListener('click', function() {
            const value = parseInt(this.dataset.value);
            input.value = value;
            stars.forEach(function(s) {
                s.className = parseInt(s.dataset.value) <= value ? 'bi bi-star-fill text-warning star-icon' : 'bi bi-star text-warning star-icon';
                s.style.cursor = 'pointer';
            });
        });
    });
});
</script>
