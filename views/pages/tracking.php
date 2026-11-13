<?php
// views/pages/tracking.php
use App\Helpers\Security;

// Status Pipeline Configuration
$statusSteps = [
    'PENDING'   => ['label' => 'Menunggu Pembayaran', 'step' => 1, 'desc' => 'Pesanan tercatat, menunggu konfirmasi pembayaran/penjadwalan kurir'],
    'PROCESSED' => ['label' => 'Diproses', 'step' => 2, 'desc' => 'Produk disiapkan dan dikemas oleh tim produksi mahasiswa'],
    'SHIPPING'  => ['label' => 'Dalam Pengiriman', 'step' => 3, 'desc' => 'Pesanan sedang dibawa oleh kurir menuju lokasi pengantaran'],
    'COMPLETED' => ['label' => 'Selesai', 'step' => 4, 'desc' => 'Pesanan telah diterima oleh pembeli. Transaksi tuntas'],
];

$currentStepNum = 0;
if ($order) {
    $currentStepNum = $statusSteps[$order['order_status']]['step'] ?? 1;
}
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  <div class="mb-8 text-center max-w-2xl mx-auto">
    <div class="inline-flex items-center gap-2 text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold uppercase tracking-wider">
      <span>Pelacakan Pesanan Real-Time</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
      Cek Status Pengiriman & Bukti Pembayaran
    </h1>
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2">
      Masukkan kode unik pesanan Anda (contoh: <code>AKS-20261109-1001</code>) untuk melihat posisi pengiriman terkini dan rincian transaksi Anda secara transparan.
    </p>

    <!-- Bilah Pencarian Kode Pesanan -->
    <form action="" method="GET" class="mt-6 flex gap-2 max-w-md mx-auto">
      <input type="hidden" name="page" value="tracking">
      <input type="text" name="code" value="<?= Security::e($orderCode) ?>" placeholder="Contoh: AKS-20261109-1001" required class="flex-1 px-4 py-3 rounded-2xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-unsulbar-gold outline-none shadow-sm">
      <button type="submit" class="interactive-btn px-6 py-3 rounded-2xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-bold text-xs shadow-md">
        Lacak
      </button>
    </form>
  </div>

  <?php if (!empty($orderCode) && !$order): ?>
    <!-- Pesanan Tidak Ditemukan -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-10 text-center border border-slate-200 dark:border-slate-700 shadow-sm max-w-md mx-auto">
      <div class="text-4xl mb-3">🔍</div>
      <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pesanan Tidak Ditemukan</h3>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
        Kode pesanan <strong><?= Security::e($orderCode) ?></strong> tidak ditemukan di database. Pastikan format kode sudah sesuai.
      </p>
    </div>
  <?php elseif ($order): ?>
    <!-- DETAIL STATUS PESANAN -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-700 shadow-md max-w-4xl mx-auto space-y-8">
      
      <!-- Header Kode Pesanan & Badge Status -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-100 dark:border-slate-700 gap-4">
        <div>
          <span class="text-xs text-slate-400">Kode Transaksi Resmi</span>
          <h2 class="text-2xl font-black text-slate-900 dark:text-white font-mono">
            #<?= Security::e($order['order_code']) ?>
          </h2>
          <p class="text-xs text-slate-500 mt-0.5">
            Dibuat pada: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WITA
          </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
          <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider <?= ($order['order_status'] === 'COMPLETED') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' ?>">
            <?= $statusSteps[$order['order_status']]['label'] ?? $order['order_status'] ?>
          </span>
          <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">
            Metode: <?= Security::e($order['payment_method']) ?>
          </span>
          <button type="button" onclick="window.print()" class="px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-600 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 flex items-center gap-1">
            🖨️ Cetak
          </button>
        </div>
      </div>

      <!-- VISUAL TRACKER: 4 Langkah Transparan -->
      <div>
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">Status Progres Pengiriman</h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 relative">
          <?php foreach ($statusSteps as $statusKey => $stepInfo): 
            $isReached = ($currentStepNum >= $stepInfo['step']);
            $isCurrent = ($currentStepNum === $stepInfo['step']);
          ?>
            <div class="relative p-4 rounded-2xl border transition-all <?= $isCurrent 
                ? 'bg-unsulbar-maroon/5 dark:bg-unsulbar-gold/10 border-unsulbar-maroon dark:border-unsulbar-gold shadow-sm' 
                : ($isReached 
                    ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800' 
                    : 'bg-slate-50 dark:bg-slate-900/40 border-slate-200 dark:border-slate-800 opacity-60') ?>">
              
              <div class="flex items-center gap-2 mb-2">
                <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold <?= $isReached 
                    ? 'bg-emerald-600 text-white' 
                    : 'bg-slate-300 dark:bg-slate-700 text-slate-600' ?>">
                  <?= $isReached ? '✓' : $stepInfo['step'] ?>
                </span>
                <span class="text-xs font-bold text-slate-900 dark:text-white">
                  <?= $stepInfo['label'] ?>
                </span>
              </div>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-snug">
                <?= $stepInfo['desc'] ?>
              </p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- SEKSI RINCIAN BUKTI PEMBAYARAN -->
      <div class="p-5 rounded-3xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
          <div class="flex items-center gap-2">
            <span class="text-xl">🧾</span>
            <div>
              <h3 class="text-xs font-extrabold uppercase tracking-wider text-amber-900 dark:text-amber-200">
                Rincian Bukti Pembayaran
              </h3>
              <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Status Pembayaran: <strong class="<?= ($order['payment_status'] === 'PAID') ? 'text-emerald-600' : 'text-amber-600' ?>"><?= Security::e($order['payment_status']) ?></strong>
              </p>
            </div>
          </div>

          <span class="px-3 py-1 rounded-full text-xs font-black uppercase <?= ($order['payment_status'] === 'PAID') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-900 dark:bg-amber-900 dark:text-amber-200' ?>">
            <?= ($order['payment_status'] === 'PAID') ? 'LUNAS / TERKONFIRMASI' : ($order['payment_method'] === 'COD' ? 'BAYAR SAAT SAMPAI (COD)' : 'MENUNGGU VERIFIKASI') ?>
          </span>
        </div>

        <?php if (!empty($order['payment_proof'])): ?>
          <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
              <a href="<?= Security::e($order['payment_proof']) ?>" target="_blank" class="block w-16 h-16 rounded-xl overflow-hidden border border-slate-200 shrink-0">
                <img src="<?= Security::e($order['payment_proof']) ?>" alt="Bukti Pembayaran" class="w-full h-full object-cover hover:scale-105 transition">
              </a>
              <div>
                <p class="text-xs font-bold text-slate-900 dark:text-white">File Bukti Terlampir</p>
                <p class="text-[11px] text-slate-400">Diunggah: <?= date('d M Y, H:i', strtotime($order['payment_proof_uploaded_at'] ?? $order['created_at'])) ?> WITA</p>
                <a href="<?= Security::e($order['payment_proof']) ?>" target="_blank" class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                  Lihat Gambar Ukuran Penuh ↗
                </a>
              </div>
            </div>
            <button type="button" onclick="document.getElementById('tracking-proof-upload-box').classList.toggle('hidden')" class="interactive-btn px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-600 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition">
              Unggah Ulang / Ganti Bukti
            </button>
          </div>
        <?php endif; ?>

        <!-- Form Unggah Bukti Bayar (Muncul jika belum ada atau tombol ganti ditekan) -->
        <div id="tracking-proof-upload-box" class="<?= !empty($order['payment_proof']) ? 'hidden' : '' ?> bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700 space-y-3">
          <p class="text-xs font-bold text-slate-800 dark:text-slate-200">
            Unggah Bukti Transfer / Struk Pembayaran:
          </p>
          <form id="form-upload-tracking-proof" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <input type="hidden" name="order_code" value="<?= Security::e($order['order_code']) ?>">
            <input type="file" name="payment_proof" required accept="image/*,application/pdf" class="text-xs file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200 dark:file:bg-slate-700 dark:file:text-white flex-1">
            <button type="submit" class="interactive-btn px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition whitespace-nowrap">
              Unggah Sekarang
            </button>
          </form>
        </div>
      </div>

      <!-- Informasi Pengantaran & Item Produk -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-700">
        
        <!-- Info Penerima & Kurir -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Pengantaran</h4>
          <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-700 text-xs space-y-2">
            <p><strong class="text-slate-700 dark:text-slate-300">Penerima:</strong> <?= Security::e($order['customer_name']) ?></p>
            <p><strong class="text-slate-700 dark:text-slate-300">No. WhatsApp:</strong> <?= Security::e($order['customer_phone']) ?></p>
            <p><strong class="text-slate-700 dark:text-slate-300">Alamat:</strong> <?= Security::e($order['customer_address']) ?></p>
            <?php if (!empty($order['maps_url'])): ?>
              <p>
                <strong class="text-slate-700 dark:text-slate-300">Peta Rute:</strong> 
                <a href="<?= Security::e($order['maps_url']) ?>" target="_blank" class="text-emerald-600 dark:text-emerald-400 font-bold hover:underline">
                  🗺️ Buka di Google Maps →
                </a>
              </p>
            <?php endif; ?>
            <?php if (!empty($order['notes'])): ?>
              <p><strong class="text-slate-700 dark:text-slate-300">Catatan:</strong> <em><?= Security::e($order['notes']) ?></em></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Rincian Produk & Biaya -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Rincian Tagihan & Produk</h4>
          <div class="space-y-2 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-slate-700 text-xs">
            <?php foreach ($orderItems as $item): ?>
              <div class="flex justify-between items-center py-1">
                <span><?= Security::e($item['product_name']) ?> <span class="text-slate-400">(<?= $item['quantity'] ?>x)</span></span>
                <span class="font-bold text-slate-800 dark:text-slate-200">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
              </div>
            <?php endforeach; ?>
            <div class="flex justify-between items-center py-1 border-t border-slate-200 dark:border-slate-700 text-slate-500">
              <span>Biaya Pengantaran Kurir</span>
              <span>Rp <?= number_format($order['shipping_fee'], 0, ',', '.') ?></span>
            </div>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-baseline font-bold text-sm">
              <span>Total Tagihan</span>
              <span class="text-base text-unsulbar-maroon dark:text-unsulbar-gold font-black">
                Rp <?= number_format($order['total_amount'], 0, ',', '.') ?>
              </span>
            </div>
          </div>
        </div>

      </div>

      <!-- Riwayat Log Aktivitas Real-Time -->
      <?php if (!empty($orderLogs)): ?>
        <div class="pt-4 border-t border-slate-100 dark:border-slate-700">
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Catatan Log Aktivitas</h4>
          <div class="space-y-2">
            <?php foreach ($orderLogs as $log): ?>
              <div class="flex items-start gap-3 text-xs bg-slate-50 dark:bg-slate-900/30 p-3 rounded-xl">
                <span class="text-emerald-500 font-bold mt-0.5">•</span>
                <div class="flex-1">
                  <span class="font-bold text-slate-800 dark:text-slate-200"><?= Security::e($log['notes']) ?></span>
                </div>
                <span class="text-[10px] text-slate-400 shrink-0">
                  <?= date('d M, H:i', strtotime($log['created_at'])) ?>
                </span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

    </div>
  <?php endif; ?>

  <!-- Riwayat Pesanan User yang Sedang Login -->
  <?php if (!empty($userOrders)): ?>
    <div class="mt-12 max-w-4xl mx-auto">
      <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-4">
        Riwayat Transaksi Akun Anda (<?= count($userOrders) ?> Pesanan)
      </h3>
      <div class="space-y-3">
        <?php foreach ($userOrders as $uOrder): ?>
          <a href="?page=tracking&code=<?= urlencode($uOrder['order_code']) ?>" class="interactive-card block bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-sm text-unsulbar-maroon dark:text-unsulbar-gold">
                  #<?= Security::e($uOrder['order_code']) ?>
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                  <?= Security::e($uOrder['order_status']) ?>
                </span>
                <?php if (!empty($uOrder['payment_proof'])): ?>
                  <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                    Bukti ✓
                  </span>
                <?php endif; ?>
              </div>
              <p class="text-xs text-slate-400 mt-1">
                <?= date('d F Y', strtotime($uOrder['created_at'])) ?> • Metode: <?= Security::e($uOrder['payment_method']) ?>
              </p>
            </div>
            <div class="text-right">
              <span class="text-sm font-black text-slate-900 dark:text-white">
                Rp <?= number_format($uOrder['total_amount'], 0, ',', '.') ?>
              </span>
              <span class="text-xs text-unsulbar-maroon dark:text-unsulbar-gold font-bold block">
                Lihat Detail Progres →
              </span>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

</main>
