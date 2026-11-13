<?php
// views/pages/admin-products.php
use App\Helpers\Security;

$totalProducts = count($products);
$totalStock = array_sum(array_column($products, 'stock'));
$lowStockCount = count(array_filter($products, fn($p) => (int)$p['stock'] < 10 && (int)$p['stock'] > 0));
$outOfStockCount = count(array_filter($products, fn($p) => (int)$p['stock'] <= 0));
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

  <!-- HEADER MONITORING -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-700">
    <div>
      <div class="inline-flex items-center gap-2 text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider">
        <span>📊 Panel Kontrol Administrator</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
        Monitoring Produk & Manajemen Stok
      </h1>
      <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
        Pantau ketersediaan produk secara langsung. Anda dapat menambah atau mengurangi kuantitas stok sesuai kondisi riil di kumbung dan dapur produksi.
      </p>
    </div>

    <!-- Filter & Pencarian Cepat -->
    <form action="" method="GET" class="flex flex-wrap items-center gap-2">
      <input type="hidden" name="page" value="admin-products">
      <input type="text" name="search" value="<?= Security::e($_GET['search'] ?? '') ?>" placeholder="Cari nama produk..." class="px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
      <select name="category" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
        <option value="">Semua Kategori</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= Security::e($cat['slug']) ?>" <?= (($_GET['category'] ?? '') === $cat['slug']) ? 'selected' : '' ?>>
            <?= Security::e($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition">
        Filter
      </button>
    </form>
  </div>

  <!-- STATISTIK RINGKAS STOK -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
        <span class="text-lg">📦</span>
      </div>
      <p class="text-2xl font-black text-slate-900 dark:text-white mt-2"><?= $totalProducts ?></p>
      <p class="text-[11px] text-slate-400 mt-0.5">3 Kategori Aktif</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Fisik Stok</span>
        <span class="text-lg">📈</span>
      </div>
      <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2"><?= number_format($totalStock, 0, ',', '.') ?></p>
      <p class="text-[11px] text-slate-400 mt-0.5">Unit tersedia di sistem</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Kritis</span>
        <span class="text-lg">⚠️</span>
      </div>
      <p class="text-2xl font-black text-amber-500 mt-2"><?= $lowStockCount ?></p>
      <p class="text-[11px] text-slate-400 mt-0.5">Kurang dari 10 unit</p>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl p-5 border border-slate-200 dark:border-slate-700 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Stok Habis</span>
        <span class="text-lg">🚫</span>
      </div>
      <p class="text-2xl font-black text-red-500 mt-2"><?= $outOfStockCount ?></p>
      <p class="text-[11px] text-slate-400 mt-0.5">Perlu produksi / restock</p>
    </div>
  </div>

  <!-- DAFTAR MONITORING PRODUK & KONTROL STOK -->
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-sm font-extrabold uppercase tracking-wider text-slate-700 dark:text-slate-300">
        Daftar Inventaris Produk (<?= count($products) ?> Item)
      </h2>
      <span class="text-xs text-slate-400">Tekan tombol +/- untuk update langsung ke database</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <?php foreach ($products as $prod): 
        $stock = (int)$prod['stock'];
        $isOut = ($stock <= 0);
        $isLow = ($stock > 0 && $stock < 10);
      ?>
        <div id="product-card-<?= $prod['id'] ?>" class="bg-white dark:bg-slate-800 rounded-3xl p-5 border border-slate-200/80 dark:border-slate-700 shadow-xs hover:shadow-md transition-all flex flex-col justify-between space-y-4">
          
          <!-- Bagian Atas: Gambar, Nama, Kategori, Harga -->
          <div>
            <div class="flex items-start gap-3.5">
              <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 shrink-0">
                <img src="<?= Security::e($prod['image_url']) ?>" alt="<?= Security::e($prod['name']) ?>" class="w-full h-full object-cover">
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-1">
                  <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider <?= ($prod['category_slug'] === 'olahan-pangan-jamur') ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : (($prod['category_slug'] === 'pupuk-organik-limbah') ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-sky-100 text-sky-800 dark:bg-sky-950 dark:text-sky-300') ?>">
                    <?= Security::e($prod['category_name']) ?>
                  </span>
                </div>
                <h3 class="font-bold text-sm text-slate-900 dark:text-white leading-snug line-clamp-1" title="<?= Security::e($prod['name']) ?>">
                  <?= Security::e($prod['name']) ?>
                </h3>
                <p class="text-xs font-black text-unsulbar-maroon dark:text-unsulbar-gold mt-1">
                  Rp <?= number_format($prod['price'], 0, ',', '.') ?> <span class="text-[11px] font-normal text-slate-400">/ <?= Security::e($prod['unit']) ?></span>
                </p>
              </div>
            </div>
          </div>

          <!-- Bagian Tengah: Indikator Stok Real-Time -->
          <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <div>
              <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 block">Stok Saat Ini:</span>
              <div class="flex items-baseline gap-1 mt-0.5">
                <span id="stock-val-<?= $prod['id'] ?>" class="text-2xl font-black text-slate-900 dark:text-white">
                  <?= $stock ?>
                </span>
                <span class="text-xs font-semibold text-slate-400">
                  <?= Security::e($prod['unit']) ?>
                </span>
              </div>
            </div>

            <!-- Status Badge -->
            <span id="stock-badge-<?= $prod['id'] ?>" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase <?= $isOut ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' : ($isLow ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300') ?>">
              <?= $isOut ? 'Habis' : ($isLow ? 'Stok Kritis' : 'Tersedia') ?>
            </span>
          </div>

          <!-- Bagian Bawah: Kontrol Cepat Tambah & Kurangi Stok -->
          <div class="space-y-3 pt-1 border-t border-slate-100 dark:border-slate-700/60">
            
            <!-- Quick Delta Adjust Buttons -->
            <div>
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Penyesuaian Cepat:</span>
              <div class="grid grid-cols-6 gap-1">
                <button type="button" onclick="StockManager.adjust(<?= $prod['id'] ?>, -10)" class="py-1 px-1 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition" title="Kurang 10">
                  -10
                </button>
                <button type="button" onclick="StockManager.adjust(<?= $prod['id'] ?>, -5)" class="py-1 px-1 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition" title="Kurang 5">
                  -5
                </button>
                <button type="button" onclick="StockManager.adjust(<?= $prod['id'] ?>, -1)" class="py-1 px-1 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition" title="Kurang 1">
                  -1
                </button>
                <button type="button" onclick="StockManager.adjust(<?= $prod['id'] ?>, 1)" class="py-1 px-1 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition" title="Tambah 1">
                  +1
                </button>
                <button type="button" onclick="StockManager.adjust(<?= $prod['id'] ?>, 5)" class="py-1 px-1 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition" title="Tambah 5">
                  +5
                </button>
                <button type="button" onclick="StockManager.adjust(<?= $prod['id'] ?>, 10)" class="py-1 px-1 rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-emerald-50 hover:text-emerald-600 dark:hover:bg-emerald-950/40 text-[11px] font-bold text-slate-600 dark:text-slate-300 transition" title="Tambah 10">
                  +10
                </button>
              </div>
            </div>

            <!-- Manual Exact Stock Input -->
            <form onsubmit="StockManager.setCustom(event, <?= $prod['id'] ?>)" class="flex items-center gap-1.5 pt-1">
              <div class="relative flex-1">
                <input id="input-stock-<?= $prod['id'] ?>" type="number" min="0" value="<?= $stock ?>" class="w-full px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-bold text-slate-900 dark:text-white outline-none focus:ring-2 focus:ring-amber-500">
              </div>
              <button type="submit" class="interactive-btn px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500 text-white font-bold text-xs whitespace-nowrap transition shadow-xs">
                Setel Stok
              </button>
            </form>

          </div>

        </div>
      <?php endforeach; ?>
    </div>
  </div>

</main>

<script>
const StockManager = {
  async adjust(productId, delta) {
    try {
      const res = await fetch('?action=admin/adjust-stock', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          product_id: productId,
          delta: delta
        })
      });

      const data = await res.json();
      if (data.status === 'success') {
        App.showToast(data.message, 'success', 'Stok Diperbarui');
        this.updateCardDom(productId, data.data.stock);
      } else {
        App.showToast(data.message || 'Gagal mengubah stok', 'error');
      }
    } catch (e) {
      App.showToast('Gagal menghubungi server.', 'error');
    }
  },

  async setCustom(e, productId) {
    e.preventDefault();
    const input = document.getElementById(`input-stock-${productId}`);
    if (!input) return;

    const newStock = parseInt(input.value, 10);
    if (isNaN(newStock) || newStock < 0) {
      App.showToast('Kuantitas stok harus angka positif.', 'error');
      return;
    }

    try {
      const res = await fetch('?action=admin/update-stock', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          product_id: productId,
          stock: newStock
        })
      });

      const data = await res.json();
      if (data.status === 'success') {
        App.showToast(data.message, 'success', 'Stok Diperbarui');
        this.updateCardDom(productId, newStock);
      } else {
        App.showToast(data.message || 'Gagal mengubah stok', 'error');
      }
    } catch (e) {
      App.showToast('Gagal menghubungi server.', 'error');
    }
  },

  updateCardDom(productId, newStock) {
    const valEl = document.getElementById(`stock-val-${productId}`);
    const inputEl = document.getElementById(`input-stock-${productId}`);
    const badgeEl = document.getElementById(`stock-badge-${productId}`);

    if (valEl) valEl.textContent = newStock;
    if (inputEl) inputEl.value = newStock;

    if (badgeEl) {
      if (newStock <= 0) {
        badgeEl.textContent = 'Habis';
        badgeEl.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
      } else if (newStock < 10) {
        badgeEl.textContent = 'Stok Kritis';
        badgeEl.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
      } else {
        badgeEl.textContent = 'Tersedia';
        badgeEl.className = 'px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
      }
    }
  }
};
</script>
