<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Riwayat Transaksi - <?= htmlspecialchars($nama_pembeli) ?></title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 13px; color: #222; padding: 30px; }
    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; }
    .header h2 { color: #4f46e5; margin-bottom: 4px; }
    .info-row { display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 12px; color: #555; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #f3f4f6; font-size: 12px; }
    td { font-size: 12px; }
    .text-end { text-align: right; }
    .total-row { font-weight: bold; background: #f9fafb; }
    .status-selesai { color: #059669; font-weight: 600; }
    .status-dibatalkan { color: #dc2626; }
    .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #888; }
    .no-print { text-align: center; margin-top: 20px; }
    @media print {
        .no-print { display: none; }
        body { padding: 0; }
    }
</style>
</head>
<body>

<div class="header">
    <h2>Riwayat Transaksi</h2>
    <div>KampusMart - E-Commerce Kampus</div>
</div>

<div class="info-row">
    <div>
        <strong>Nama:</strong> <?= htmlspecialchars($nama_pembeli) ?><br>
        <strong>Periode:</strong> <?= date('d/m/Y', strtotime($dari)) ?> - <?= date('d/m/Y', strtotime($sampai)) ?>
    </div>
    <div>
        <strong>Dicetak:</strong> <?= date('d/m/Y H:i') ?><br>
        <strong>Total Belanja (Selesai):</strong> Rp <?= number_format($total_belanja, 0, ',', '.') ?>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Kode Order</th>
            <th>Toko</th>
            <th>Tanggal</th>
            <th class="text-end">Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($orders)): ?>
        <tr><td colspan="5" style="text-align:center;color:#888">Tidak ada transaksi pada periode ini</td></tr>
        <?php else: foreach ($orders as $o): ?>
        <tr>
            <td><?= htmlspecialchars($o->kode_order) ?></td>
            <td><?= htmlspecialchars($o->nama_toko ?: $o->nama_penjual) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></td>
            <td class="text-end">Rp <?= number_format($o->total, 0, ',', '.') ?></td>
            <td class="status-<?= $o->status ?>"><?= ucfirst($o->status) ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody>
    <?php if (!empty($orders)): ?>
    <tfoot>
        <tr class="total-row">
            <td colspan="3" class="text-end">Total Belanja (Status Selesai)</td>
            <td class="text-end">Rp <?= number_format($total_belanja, 0, ',', '.') ?></td>
            <td></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

<div class="footer">Dokumen ini dibuat otomatis oleh sistem KampusMart.</div>

<div class="no-print">
    <button onclick="window.print()" style="padding:10px 24px;font-size:14px;cursor:pointer;background:#4f46e5;color:#fff;border:none;border-radius:6px;">
        🖨️ Cetak / Simpan sebagai PDF
    </button>
</div>

<script>window.onload = () => window.print();</script>
</body>
</html>
