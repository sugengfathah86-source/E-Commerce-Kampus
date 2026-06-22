<div class="d-flex align-items-center gap-2 mb-3">
    <a href="<?= base_url('chat') ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <h5 class="fw-bold mb-0"><?= htmlspecialchars($lawan->nama_toko ?: $lawan->nama) ?></h5>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body" style="height:400px;overflow-y:auto" id="chatBox">
        <?php if (empty($messages)): ?>
        <p class="text-center text-muted py-5">Mulai percakapan dengan <?= htmlspecialchars($lawan->nama) ?></p>
        <?php else: foreach ($messages as $m): $is_me = $m->id_sender == $this->session->userdata('user_id'); ?>
        <div class="d-flex mb-2 <?= $is_me ? 'justify-content-end' : 'justify-content-start' ?>">
            <div class="p-2 rounded <?= $is_me ? 'bg-primary text-white' : 'bg-light' ?>" style="max-width:70%">
                <div class="small"><?= htmlspecialchars($m->pesan) ?></div>
                <div class="small <?= $is_me ? 'text-white-50' : 'text-muted' ?>" style="font-size:.65rem"><?= date('H:i', strtotime($m->created_at)) ?></div>
            </div>
        </div>
        <?php endforeach; endif; ?>
    </div>
    <div class="card-footer bg-white">
        <?= form_open('chat/kirim', ['class' => 'd-flex gap-2']) ?>
        <input type="hidden" name="id_room" value="<?= $room->id ?>">
        <input type="text" name="pesan" class="form-control" placeholder="Tulis pesan..." required autofocus>
        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
        <?= form_close() ?>
    </div>
</div>

<script>
const chatBox = document.getElementById('chatBox');
chatBox.scrollTop = chatBox.scrollHeight;
</script>
