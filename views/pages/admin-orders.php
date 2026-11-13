<?php
// views/pages/admin-orders.php
use App\Helpers\Security;

$statusBadgeMap = [
    'PENDING'   => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
    'PROCESSED' => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
    'SHIPPING'  => 'bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300',
    'COMPLETED' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
];
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-700">
    <div>
      <div class="inline-flex items-center gap-2 text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wider">
        <span>🛠️ Panel Pengelola & Admin</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
        Manajemen Pesanan Masuk
      </h1>
      <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
        Kelola verifikasi bukti pembayaran, rute maps kurir, dan pembaruan status transaksi secara real-time.
      </p>
    </div>

    <!-- Filter & Pencarian Cepat -->
    <form action="" method="GET" class="flex flex-wrap items-center gap-2">
      <input type="hidden" name="page" value="admin-orders">
      <input type="text" name="search" value="<?= Security::e($_GET['search'] ?? '') ?>" placeholder="Cari kode/nama..." class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-purple-500">
      <select name="status" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-purple-500">
        <option value="">Semua Status</option>
        <option value="PENDING" <?= (($_GET['status'] ?? '') === 'PENDING') ? 'selected' : '' ?>>Menunggu</option>
        <option value="PROCESSED" <?= (($_GET['status'] ?? '') === 'PROCESSED') ? 'selected' : '' ?>>Diproses</option>
        <option value="SHIPPING" <?= (($_GET['status'] ?? '') === 'SHIPPING') ? 'selected' : '' ?>>Dalam Pengiriman</option>
        <option value="COMPLETED" <?= (($_GET['status'] ?? '') === 'COMPLETED') ? 'selected' : '' ?>>Selesai</option>
      </select>
      <button type="submit" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-sm">
        Filter
      </button>
    </form>
  </div>

  <!-- TABEL PESANAN -->
  <div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 dark:bg-slate-900/60 uppercase tracking-wider text-slate-400 font-extrabold border-b border-slate-200 dark:border-slate-700">
          <tr>
            <th class="py-3.5 px-4">Kode Transaksi</th>
            <th class="py-3.5 px-4">Pembeli & Kontak</th>
            <th class="py-3.5 px-4">Alamat & Google Maps</th>
            <th class="py-3.5 px-4">Total</th>
            <th class="py-3.5 px-4">Metode & Bukti</th>
            <th class="py-3.5 px-4">Status Pesanan</th>
            <th class="py-3.5 px-4 text-center">Aksi Kelola</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
          <?php if (empty($orders)): ?>
            <tr>
              <td colspan="7" class="py-12 text-center text-slate-400">
                Tidak ada pesanan yang sesuai dengan filter.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($orders as $ord): ?>
              <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-700/30 transition">
                <td class="py-4 px-4 font-mono font-bold text-unsulbar-maroon dark:text-unsulbar-gold whitespace-nowrap">
                  #<?= Security::e($ord['order_code']) ?>
                  <span class="block text-[10px] text-slate-400 font-sans mt-0.5">
                    <?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?>
                  </span>
                </td>
                
                <td class="py-4 px-4">
                  <span class="font-bold text-slate-800 dark:text-slate-100 block">
                    <?= Security::e($ord['customer_name']) ?>
                  </span>
                  <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $ord['customer_phone']) ?>" target="_blank" class="text-[11px] text-emerald-600 dark:text-emerald-400 hover:underline">
                    📱 <?= Security::e($ord['customer_phone']) ?>
                  </a>
                </td>

                <td class="py-4 px-4 max-w-xs">
                  <p class="text-slate-700 dark:text-slate-300 line-clamp-2">
                    <?= Security::e($ord['customer_address']) ?>
                  </p>
                  <?php if (!empty($ord['maps_url'])): ?>
                    <a href="<?= Security::e($ord['maps_url']) ?>" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline mt-1">
                      <span>🗺️ Rute Maps Kurir ↗</span>
                    </a>
                  <?php endif; ?>
                </td>

                <td class="py-4 px-4 whitespace-nowrap font-black text-slate-900 dark:text-white">
                  Rp <?= number_format($ord['total_amount'], 0, ',', '.') ?>
                </td>

                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs">
                    <?= Security::e($ord['payment_method']) ?>
                  </span>
                  <?php if (!empty($ord['payment_proof'])): ?>
                    <a href="<?= Security::e($ord['payment_proof']) ?>" target="_blank" class="inline-block mt-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                      🔍 Lihat Bukti Bayar
                    </a>
                  <?php else: ?>
                    <span class="inline-block mt-1 text-[10px] text-slate-400">
                      <?= ($ord['payment_method'] === 'COD') ? 'Tunai COD' : 'Belum Ada Bukti' ?>
                    </span>
                  <?php endif; ?>
                </td>

                <td class="py-4 px-4 whitespace-nowrap">
                  <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $statusBadgeMap[$ord['order_status']] ?? 'bg-slate-100' ?>">
                    <?= Security::e($ord['order_status']) ?>
                  </span>
                  <span class="block text-[10px] text-slate-400 mt-1">
                    Bayar: <strong class="<?= ($ord['payment_status'] === 'PAID') ? 'text-emerald-500' : 'text-amber-500' ?>"><?= Security::e($ord['payment_status']) ?></strong>
                  </span>
                </td>

                <td class="py-4 px-4 whitespace-nowrap text-center">
                  <div class="inline-flex items-center gap-1.5">
                    <select onchange="Admin.changeStatus(<?= $ord['id'] ?>, this.value)" class="text-[11px] font-semibold py-1 px-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 outline-none">
                      <option value="PENDING" <?= ($ord['order_status'] === 'PENDING') ? 'selected' : '' ?>>Menunggu</option>
                      <option value="PROCESSED" <?= ($ord['order_status'] === 'PROCESSED') ? 'selected' : '' ?>>Diproses</option>
                      <option value="SHIPPING" <?= ($ord['order_status'] === 'SHIPPING') ? 'selected' : '' ?>>Kirim Kurir</option>
                      <option value="COMPLETED" <?= ($ord['order_status'] === 'COMPLETED') ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    <a href="?page=tracking&code=<?= urlencode($ord['order_code']) ?>" target="_blank" class="p-1 text-slate-400 hover:text-purple-600 transition" title="Lihat Detail Transaksi">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<script>
const Admin = {
  async changeStatus(orderId, newStatus) {
    let paymentStatus = null;
    if (newStatus === 'COMPLETED') {
      paymentStatus = 'PAID';
    }

    try {
      const res = await fetch('?action=order/update-status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          order_id: orderId,
          order_status: newStatus,
          payment_status: paymentStatus
        })
      });

      const data = await res.json();
      if (data.status === 'success') {
        App.showToast('Status pesanan berhasil diperbarui!', 'success');
        setTimeout(() => window.location.reload(), 800);
      } else {
        App.showToast(data.message || 'Gagal mengubah status', 'error');
      }
    } catch (e) {
      App.showToast('Gagal menghubungi server.', 'error');
    }
  }
};
</script>
