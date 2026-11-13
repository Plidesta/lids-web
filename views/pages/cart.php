<?php
// views/pages/cart.php
use App\Config\App;
use App\Helpers\Security;

$deliveryAddress = App::getDeliveryAddress();
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold uppercase tracking-wider">
        <span>🛒 Keranjang Belanja Anda</span>
      </div>
      <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
        Kelola Pesanan & Rincian Pembayaran
      </h1>
    </div>

    <!-- Tombol Header Alamat di Halaman Keranjang -->
    <div class="flex items-center gap-2">
      <button type="button" onclick="App.openAddressModal()" class="interactive-btn px-4 py-2.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-xs font-bold text-amber-900 dark:text-amber-300 hover:bg-amber-100 flex items-center gap-2 shadow-xs transition">
        <span>📍</span>
        <span>Atur Alamat Pengantaran</span>
      </button>
    </div>
  </div>

  <?php if (empty($cartSummary['items'])): ?>
    <!-- Keranjang Kosong -->
    <div class="bg-white dark:bg-slate-800 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-700 shadow-sm max-w-lg mx-auto">
      <div class="w-20 h-20 bg-amber-50 dark:bg-amber-950/40 rounded-full mx-auto flex items-center justify-center text-4xl mb-4">
        🛒
      </div>
      <h2 class="text-xl font-bold text-slate-900 dark:text-white">Keranjang Anda Masih Kosong</h2>
      <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 max-w-sm mx-auto leading-relaxed">
        Anda belum menambahkan produk pangan jamur, pupuk organik, atau paket budidaya ke keranjang. Jelajahi katalog dan dukung inovasi mahasiswa Unsulbar!
      </p>
      <a href="?page=catalog" class="interactive-btn inline-block mt-6 px-6 py-3 rounded-2xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-bold text-xs shadow-md">
        Mulai Belanja Sekarang →
      </a>
    </div>
  <?php else: ?>
    <!-- Ada Produk di Keranjang -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      
      <!-- KOLOM KIRI: ALAMAT & DAFTAR PRODUK -->
      <div class="lg:col-span-8 space-y-6">

        <!-- CARD ALAMAT PENGANTARAN TERPILIH (ALA E-COMMERCE MODERN) -->
        <div id="cart-address-card" class="bg-white dark:bg-slate-800 rounded-3xl p-5 sm:p-6 border-2 border-amber-200/80 dark:border-slate-700 shadow-sm relative overflow-hidden transition-all">
          <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            
            <div class="flex items-start gap-3.5">
              <div class="w-10 h-10 rounded-2xl bg-unsulbar-maroon text-white flex items-center justify-center shrink-0 text-lg shadow-md shadow-unsulbar-maroon/20">
                📍
              </div>
              
              <div class="space-y-1.5 flex-1 min-w-0">
                <!-- Label Alamat & Status Utama -->
                <div class="flex flex-wrap items-center gap-2">
                  <span class="text-[10px] font-extrabold uppercase tracking-wider text-unsulbar-maroon dark:text-unsulbar-gold">
                    Alamat Pengiriman
                  </span>
                  <span id="cart-display-label" class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-100 text-amber-900 dark:bg-amber-950/60 dark:text-amber-300">
                    <?= Security::e($deliveryAddress['label'] ?? 'Alamat Utama') ?>
                  </span>
                  <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                    ✓ Terhubung ke Kurir
                  </span>
                </div>

                <!-- Nama Penerima & No HP -->
                <div class="flex items-center gap-2 pt-0.5">
                  <h4 id="cart-display-recipient" class="font-extrabold text-sm sm:text-base text-slate-900 dark:text-white">
                    <?= Security::e($deliveryAddress['recipient_name'] ?: 'Konsumen Pembeli') ?>
                  </h4>
                  <span class="text-slate-300 dark:text-slate-600">•</span>
                  <span id="cart-display-phone" class="font-bold text-xs sm:text-sm text-slate-600 dark:text-slate-300">
                    <?= Security::e($deliveryAddress['phone'] ?: 'No. Telepon / WA') ?>
                  </span>
                </div>

                <!-- Teks Alamat Lengkap & Kota -->
                <p id="cart-display-address" class="text-xs sm:text-sm text-slate-700 dark:text-slate-200 leading-relaxed font-normal">
                  <?= Security::e($deliveryAddress['address']) ?><?= !empty($deliveryAddress['city']) ? ', ' . Security::e($deliveryAddress['city']) : '' ?>
                </p>

                <!-- Patokan Lokasi -->
                <div id="cart-display-benchmark-wrap" class="<?= empty($deliveryAddress['benchmark']) ? 'hidden' : '' ?> flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                  <span class="font-bold text-slate-700 dark:text-slate-300">📌 Patokan:</span>
                  <span id="cart-display-benchmark"><?= Security::e($deliveryAddress['benchmark'] ?? '') ?></span>
                </div>

                <!-- Catatan Tambahan Kurir -->
                <div id="cart-display-notes-wrap" class="<?= empty($deliveryAddress['notes']) ? 'hidden' : '' ?> flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                  <span class="font-bold text-slate-700 dark:text-slate-300">💬 Catatan:</span>
                  <span id="cart-display-notes"><?= Security::e($deliveryAddress['notes'] ?? '') ?></span>
                </div>

                <!-- Google Maps Link Kurir -->
                <div class="pt-1">
                  <a id="cart-display-maps" href="<?= Security::e($deliveryAddress['maps_url'] ?: '#') ?>" target="_blank" class="<?= empty($deliveryAddress['maps_url']) ? 'hidden' : 'inline-flex' ?> items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline bg-emerald-50 dark:bg-emerald-950/30 px-3 py-1 rounded-xl border border-emerald-200 dark:border-emerald-800">
                    <span>🗺️ Lihat Titik Rute Google Maps →</span>
                  </a>
                </div>

              </div>
            </div>

            <!-- Tombol Ubah Alamat -->
            <button type="button" onclick="App.openAddressModal()" class="interactive-btn shrink-0 px-4 py-2.5 rounded-2xl bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-xs font-extrabold text-slate-800 dark:text-slate-100 hover:bg-slate-50 dark:hover:bg-slate-600 flex items-center gap-1.5 shadow-xs transition">
              <span>✏️ Ubah Alamat</span>
            </button>

          </div>
        </div>

        <!-- Daftar Item (Tabel / List) -->
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
          <div class="flex justify-between items-center pb-4 border-b border-slate-100 dark:border-slate-700">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
              Daftar Produk (<?= $cartSummary['total_items'] ?> Item)
            </h2>
            <a href="?page=catalog" class="text-xs font-semibold text-unsulbar-maroon dark:text-unsulbar-gold hover:underline">
              + Tambah Produk Lain
            </a>
          </div>

          <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
            <?php foreach ($cartSummary['items'] as $item): 
              $priceFormatted = number_format($item['price'], 0, ',', '.');
              $subtotalFormatted = number_format($item['subtotal'], 0, ',', '.');
            ?>
              <div id="cart-row-<?= $item['id'] ?>" class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                
                <!-- Gambar & Info Item -->
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 bg-slate-100 dark:bg-slate-900 rounded-2xl overflow-hidden shrink-0 border border-slate-200 dark:border-slate-700">
                    <img src="<?= Security::e($item['image_url']) ?>" alt="<?= Security::e($item['name']) ?>" class="w-full h-full object-cover">
                  </div>
                  <div>
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white leading-snug">
                      <?= Security::e($item['name']) ?>
                    </h3>
                    <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                      Rp <?= $priceFormatted ?> / <?= Security::e($item['unit']) ?>
                    </div>
                  </div>
                </div>

                <!-- Kontrol Kuantitas & Subtotal Item -->
                <div class="flex items-center justify-between sm:justify-end gap-5 w-full sm:w-auto">
                  
                  <!-- Plus/Minus Counter -->
                  <div class="flex items-center border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-700/50 p-1">
                    <button type="button" onclick="Cart.updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)" class="w-7 h-7 rounded-lg bg-white dark:bg-slate-600 text-slate-700 dark:text-white font-bold flex items-center justify-center hover:bg-slate-100 transition shadow-xs text-xs">
                      -
                    </button>
                    <input id="item-qty-<?= $item['id'] ?>" type="number" readonly value="<?= $item['quantity'] ?>" class="w-10 text-center bg-transparent text-xs font-bold text-slate-900 dark:text-white outline-none">
                    <button type="button" onclick="Cart.updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)" class="w-7 h-7 rounded-lg bg-white dark:bg-slate-600 text-slate-700 dark:text-white font-bold flex items-center justify-center hover:bg-slate-100 transition shadow-xs text-xs">
                      +
                    </button>
                  </div>

                  <!-- Subtotal Item -->
                  <div class="text-right min-w-[95px]">
                    <span id="item-subtotal-<?= $item['id'] ?>" class="font-extrabold text-sm text-unsulbar-maroon dark:text-unsulbar-gold block">
                      Rp <?= $subtotalFormatted ?>
                    </span>
                  </div>

                  <!-- Tombol Hapus -->
                  <button type="button" onclick="Cart.remove(<?= $item['id'] ?>)" title="Hapus dari keranjang" class="p-2 text-slate-400 hover:text-red-500 transition rounded-xl hover:bg-red-50 dark:hover:bg-red-950/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                  </button>

                </div>

              </div>
            <?php endforeach; ?>
          </div>

        </div>

      </div>

      <!-- KOLOM KANAN: RINCIAN LENGKAP PEMBAYARAN SEBELUM CHECKOUT -->
      <div class="lg:col-span-4 bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-5 sticky top-36">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
          <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-900 dark:text-white">
            Rincian Pembayaran
          </h2>
          <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 font-bold">
            Transparan & Pasti
          </span>
        </div>

        <div class="space-y-3 text-xs sm:text-sm">
          <div class="flex justify-between text-slate-600 dark:text-slate-300">
            <span>Total Harga Produk (<?= $cartSummary['total_items'] ?> item)</span>
            <span id="summary-subtotal" class="font-bold text-slate-900 dark:text-white">
              Rp <?= number_format($cartSummary['subtotal'], 0, ',', '.') ?>
            </span>
          </div>

          <div class="flex justify-between text-slate-600 dark:text-slate-300">
            <span class="flex items-center gap-1">
              <span>Biaya Pengantaran Kurir</span>
              <span class="text-[10px] text-slate-400" title="Tarif kurir internal kampus Unsulbar">(Kampus)</span>
            </span>
            <span id="summary-shipping" class="font-bold text-slate-900 dark:text-white">
              Rp <?= number_format($cartSummary['shipping_fee'], 0, ',', '.') ?>
            </span>
          </div>

          <div class="flex justify-between text-slate-600 dark:text-slate-300">
            <span>Biaya Layanan & Penanganan</span>
            <span class="font-bold text-emerald-600 dark:text-emerald-400">
              GRATIS (Rp 0)
            </span>
          </div>

          <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-between items-baseline">
            <div>
              <span class="font-bold text-slate-900 dark:text-white text-sm block">Total Tagihan</span>
              <span class="text-[10px] text-slate-400">Sudah termasuk ongkir kurir</span>
            </div>
            <span id="summary-total" class="text-2xl font-black text-unsulbar-maroon dark:text-unsulbar-gold">
              Rp <?= number_format($cartSummary['total_amount'], 0, ',', '.') ?>
            </span>
          </div>
        </div>

        <div class="p-3.5 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700 text-[11px] space-y-1.5 text-slate-600 dark:text-slate-300">
          <p class="font-bold text-slate-800 dark:text-slate-200">ℹ️ Metode Pembayaran Tersedia:</p>
          <ul class="list-disc list-inside space-y-0.5 text-slate-500 dark:text-slate-400">
            <li><strong>COD (Tunai)</strong>: Bayar langsung ke kurir saat paket tiba.</li>
            <li><strong>QRIS Otomatis</strong>: BCA, Mandiri, BRI, GoPay, OVO, Dana.</li>
            <li><strong>Transfer Bank</strong>: Rekening resmi BRI, BSI, atau Mandiri.</li>
          </ul>
        </div>

        <a href="?page=checkout" class="interactive-btn w-full py-4 rounded-2xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-black text-sm flex items-center justify-center gap-2 shadow-lg shadow-unsulbar-maroon/25 transition">
          <span>Lanjut ke Checkout & Bayar</span>
          <svg class="w-4 h-4 text-unsulbar-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>

        <div class="text-[11px] text-slate-400 text-center leading-relaxed">
          🔒 Checkout terlindungi sistem enkripsi & verifikasi kurir real-time.
        </div>
      </div>

    </div>
  <?php endif; ?>

</main>
