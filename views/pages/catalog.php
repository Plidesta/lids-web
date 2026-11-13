<?php
// views/pages/catalog.php
use App\Helpers\Security;
?>

<!-- HEADER HALAMAN: Copywriting Persuasif -->
<section class="bg-gradient-to-b from-unsulbar-maroon/10 via-transparent to-transparent dark:from-slate-800/50 py-10 border-b border-slate-200/60 dark:border-slate-700/60">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-800 dark:text-emerald-300 mb-3">
      <span>✨ Langsung dari Kumbung Budidaya</span>
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
      Katalog Produk Inovasi Aksara Unsulbar
    </h1>
    <p class="text-sm text-slate-600 dark:text-slate-300 mt-2 leading-relaxed">
      Dukung gerakan hilirisasi karya mahasiswa Unsulbar! Nikmati aneka olahan jamur tiram bergizi (nugget, bakso, kaldu, rendang, crispy), pupuk organik hayati hasil dekomposisi baglog, serta bibit & baglog budidaya berkualitas tinggi.
    </p>
  </div>
</section>

<!-- MAIN CONTENT: Search, Filter, & Catalog Grid -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">
    
    <!-- SIDEBAR: Filter Cerdas -->
    <aside class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-6 sticky top-36">
      
      <!-- Input Hidden untuk Kategori Terpilih -->
      <input type="hidden" id="selected-category-slug" value="<?= Security::e($filters['category'] ?? '') ?>">

      <!-- Filter 1: Pencarian Berbasis Teks -->
      <div>
        <label for="filter-search" class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
          Pencarian Produk
        </label>
        <div class="relative">
          <input type="text" id="filter-search" value="<?= Security::e($filters['search'] ?? '') ?>" placeholder="Cari nugget, bakso, pupuk, baglog..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-unsulbar-gold outline-none transition">
          <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </div>
      </div>

      <!-- Filter 2: Kategori Produk (Pills) -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2">
          Kategori Produk
        </label>
        <div class="space-y-1.5">
          <button data-category-filter="" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-between <?= empty($filters['category']) ? 'bg-unsulbar-maroon text-white shadow-md' : 'bg-slate-50 dark:bg-slate-700/40 text-slate-700 dark:text-slate-300 hover:bg-slate-100' ?>">
            <span>Semua Kategori</span>
            <span>🍄🌱📦</span>
          </button>
          <?php foreach ($categories as $cat): 
            $isActiveCat = (($filters['category'] ?? '') === $cat['slug']);
            $icon = '🌱';
            if ($cat['slug'] === 'olahan-pangan-jamur') $icon = '🍄';
            if ($cat['slug'] === 'produk-paket-budidaya') $icon = '📦';
          ?>
            <button data-category-filter="<?= Security::e($cat['slug']) ?>" class="w-full text-left px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center justify-between <?= $isActiveCat ? 'bg-unsulbar-maroon text-white shadow-md' : 'bg-slate-50 dark:bg-slate-700/40 text-slate-700 dark:text-slate-300 hover:bg-slate-100' ?>">
              <span class="truncate"><?= Security::e($cat['name']) ?></span>
              <span class="text-xs"><?= $icon ?></span>
            </button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Filter 3: Rentang Harga -->
      <div>
        <div class="flex justify-between items-center mb-2">
          <label for="filter-price-range" class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
            Rentang Harga
          </label>
          <span id="filter-price-display" class="text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold">
            <?= (!empty($filters['max_price']) && $filters['max_price'] < 60000) ? 'Maks. Rp ' . number_format($filters['max_price'], 0, ',', '.') : 'Semua Harga' ?>
          </span>
        </div>
        <input type="range" id="filter-price-range" min="5000" max="60000" step="5000" value="<?= (!empty($filters['max_price'])) ? $filters['max_price'] : '60000' ?>" class="w-full accent-unsulbar-maroon dark:accent-unsulbar-gold cursor-pointer">
        <div class="flex justify-between text-[10px] text-slate-400 mt-1">
          <span>Rp 5.000</span>
          <span>Rp 60.000+</span>
        </div>
      </div>

      <!-- Filter 4: Ketersediaan Stok -->
      <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
        <label class="flex items-center gap-3 cursor-pointer select-none">
          <input type="checkbox" id="filter-stock-only" <?= !empty($filters['in_stock_only']) ? 'checked' : '' ?> class="w-4 h-4 rounded text-unsulbar-maroon focus:ring-unsulbar-maroon dark:bg-slate-700">
          <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Hanya Stok Tersedia</span>
        </label>
      </div>

    </aside>

    <!-- MAIN PRODUCT GRID -->
    <div class="lg:col-span-3 space-y-6">
      
      <!-- Top Bar: Result Count & Sorting -->
      <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Menampilkan:</span>
          <span id="products-count-badge" class="px-2.5 py-1 rounded-lg bg-unsulbar-maroon/10 dark:bg-unsulbar-gold/10 text-unsulbar-maroon dark:text-unsulbar-gold font-extrabold text-xs">
            <?= count($products) ?> Produk
          </span>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <label for="filter-sort" class="text-xs text-slate-400 shrink-0">Urutkan:</label>
          <select id="filter-sort" class="w-full sm:w-auto px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-unsulbar-gold outline-none">
            <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Produk Terbaru</option>
            <option value="price_low" <?= ($filters['sort'] === 'price_low') ? 'selected' : '' ?>>Harga: Rendah ke Tinggi</option>
            <option value="price_high" <?= ($filters['sort'] === 'price_high') ? 'selected' : '' ?>>Harga: Tinggi ke Rendah</option>
            <option value="popular" <?= ($filters['sort'] === 'popular') ? 'selected' : '' ?>>Paling Banyak Diminati</option>
          </select>
        </div>
      </div>

      <!-- Grid Container -->
      <div id="products-grid-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 transition-opacity duration-200">
        <?php if (empty($products)): ?>
          <div class="col-span-full py-16 text-center">
            <div class="w-20 h-20 mx-auto mb-4 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-3xl">
              🍄
            </div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Produk Tidak Ditemukan</h3>
            <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto text-sm">
              Tidak ada produk yang cocok dengan kriteria pencarian Anda. Silakan reset filter atau cari dengan kata kunci lain.
            </p>
          </div>
        <?php else: ?>
          <?php foreach ($products as $product): 
            $priceFormatted = number_format($product['price'], 0, ',', '.');
            $isOutOfStock = ($product['stock'] <= 0);
            
            $catBadgeColor = 'bg-slate-700 text-white';
            if ($product['category_slug'] === 'olahan-pangan-jamur') $catBadgeColor = 'bg-emerald-600 text-white';
            elseif ($product['category_slug'] === 'pupuk-organik-limbah') $catBadgeColor = 'bg-amber-600 text-white';
            elseif ($product['category_slug'] === 'produk-paket-budidaya') $catBadgeColor = 'bg-sky-600 text-white';
          ?>
            <div class="interactive-card bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between group shadow-xs hover:shadow-xl transition-all duration-300">
              
              <!-- Gambar Produk & Badge Kategori -->
              <div class="relative bg-slate-100 dark:bg-slate-900 aspect-4/3 overflow-hidden">
                <img src="<?= Security::e($product['image_url']) ?>" alt="<?= Security::e($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                
                <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                  <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider <?= $catBadgeColor ?>">
                    <?= Security::e($product['category_name']) ?>
                  </span>
                  <?php if (!empty($product['is_featured'])): ?>
                    <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-unsulbar-gold text-slate-900 flex items-center gap-1 shadow-xs">
                      ★ Unggulan
                    </span>
                  <?php endif; ?>
                </div>

                <!-- Indikator Jumlah Stok Real-Time -->
                <div class="absolute bottom-3 right-3">
                  <span class="text-xs font-semibold px-2.5 py-1 rounded-lg backdrop-blur-md shadow-xs <?= $isOutOfStock ? 'bg-red-500/90 text-white' : ($product['stock'] < 10 ? 'bg-amber-500/90 text-white animate-pulse' : 'bg-slate-900/80 text-white') ?>">
                    <?= $isOutOfStock ? 'Stok Habis' : 'Sisa ' . $product['stock'] . ' ' . $product['unit'] ?>
                  </span>
                </div>
              </div>

              <!-- Body Info Produk -->
              <div class="p-5 flex-1 flex flex-col justify-between">
                <div>
                  <h3 class="font-bold text-base text-slate-900 dark:text-slate-100 leading-snug group-hover:text-unsulbar-maroon dark:group-hover:text-unsulbar-gold transition-colors line-clamp-1">
                    <?= Security::e($product['name']) ?>
                  </h3>
                  <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                    <?= Security::e($product['description']) ?>
                  </p>
                </div>

                <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 mt-4">
                  <div class="flex items-baseline justify-between mb-4">
                    <div>
                      <span class="text-[11px] text-slate-400">Harga Satuan</span>
                      <div class="text-lg font-black text-unsulbar-maroon dark:text-unsulbar-gold">
                        Rp <?= $priceFormatted ?>
                      </div>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">/<?= Security::e($product['unit']) ?></span>
                  </div>

                  <!-- Tombol Aksi: Tambah ke Keranjang & Beli Sekarang -->
                  <div class="grid grid-cols-2 gap-2">
                    <button onclick="Cart.add(<?= $product['id'] ?>, 1, false)" <?= $isOutOfStock ? 'disabled' : '' ?> class="interactive-btn py-2 px-2.5 rounded-xl border border-unsulbar-maroon/30 dark:border-unsulbar-gold/30 text-unsulbar-maroon dark:text-unsulbar-gold font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-unsulbar-maroon/5 dark:hover:bg-unsulbar-gold/10 disabled:opacity-40 disabled:cursor-not-allowed">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                      + Keranjang
                    </button>
                    <button onclick="Cart.add(<?= $product['id'] ?>, 1, true)" <?= $isOutOfStock ? 'disabled' : '' ?> class="interactive-btn py-2 px-2.5 rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs disabled:opacity-40 disabled:cursor-not-allowed">
                      Beli Sekarang
                    </button>
                  </div>
                </div>

              </div>

            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>

  </div>

</main>
