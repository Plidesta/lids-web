<?php
// views/pages/checkout.php
use App\Config\App;
use App\Helpers\Security;

$currentUser = $_SESSION['user'] ?? null;
$deliveryAddress = App::getDeliveryAddress();
$bankAccounts = App::BANK_ACCOUNTS;
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  <div class="mb-8">
    <div class="inline-flex items-center gap-2 text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold uppercase tracking-wider">
      <span>Penyelesaian Pesanan</span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
      Checkout & Pengiriman Kurir Kampus
    </h1>
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
      Lengkapi data pengantaran dan rincian pembayaran. Pesanan Anda akan langsung terhubung ke kurir mahasiswa Unsulbar via WhatsApp beserta navigasi Google Maps.
    </p>
  </div>

  <form id="checkout-form" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Hidden CSRF Token -->
    <input type="hidden" name="csrf_token" value="<?= Security::e(Security::csrfToken()) ?>">
    <input type="hidden" id="checkout-lat" name="delivery_lat" value="">
    <input type="hidden" id="checkout-lng" name="delivery_lng" value="">

    <!-- KOLOM KIRI: Data Penerima & Metode Bayar & Bukti Bayar -->
    <div class="lg:col-span-7 space-y-6">
      
      <!-- Box 1: Informasi Penerima & Lokasi Maps (Otomatis Sinkron Ala E-Commerce) -->
      <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-5">
        
        <!-- Header Bagian Alamat -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700 gap-3">
          <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-xl bg-unsulbar-maroon text-white flex items-center justify-center text-xs font-bold">1</span>
            <div>
              <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Alamat Pengiriman Kurir</h2>
              <p class="text-[11px] text-slate-400">Otomatis sinkron dengan alamat di keranjang belanja</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button type="button" id="btn-checkout-gps" class="interactive-btn px-3 py-1.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-300 dark:border-emerald-800 text-xs font-bold text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 flex items-center gap-1 transition">
              <span>🎯 Deteksi GPS Saya</span>
            </button>
            <button type="button" onclick="App.openAddressModal()" class="interactive-btn px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold flex items-center gap-1 shadow-xs transition">
              <span>✏️ Ubah Alamat</span>
            </button>
          </div>
        </div>

        <!-- KARTU ALAMAT PENGIRIMAN TERPILIH (PREVIEW ALA E-COMMERCE) -->
        <div id="checkout-address-card" class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-amber-50/70 via-white to-emerald-50/70 dark:from-slate-700/60 dark:via-slate-700/40 dark:to-slate-700/60 border border-amber-200 dark:border-slate-600 space-y-2">
          <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
              <span id="checkout-card-label" class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase bg-amber-200 text-amber-900 dark:bg-amber-950 dark:text-amber-300">
                <?= Security::e($deliveryAddress['label'] ?? 'Alamat Utama') ?>
              </span>
              <span class="text-xs font-black text-slate-900 dark:text-white" id="checkout-card-recipient">
                <?= Security::e(!empty($deliveryAddress['recipient_name']) ? $deliveryAddress['recipient_name'] : ($currentUser['full_name'] ?? 'Penerima Pesanan')) ?>
              </span>
              <span class="text-slate-300 dark:text-slate-600">•</span>
              <span class="text-xs font-bold text-slate-600 dark:text-slate-300 font-mono" id="checkout-card-phone">
                <?= Security::e(!empty($deliveryAddress['phone']) ? $deliveryAddress['phone'] : ($currentUser['phone'] ?? '08...')) ?>
              </span>
            </div>
            <button type="button" onclick="App.openAddressModal()" class="text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold hover:underline">
              Ganti Alamat Lain →
            </button>
          </div>

          <!-- Teks Alamat -->
          <p id="checkout-card-address" class="text-xs text-slate-700 dark:text-slate-200 leading-relaxed font-medium">
            <?= Security::e($deliveryAddress['address'] ?? '') ?><?= !empty($deliveryAddress['city']) ? ', ' . Security::e($deliveryAddress['city']) : '' ?>
          </p>

          <!-- Patokan -->
          <div id="checkout-card-benchmark-wrap" class="<?= empty($deliveryAddress['benchmark']) ? 'hidden' : '' ?> flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
            <span class="font-bold text-slate-700 dark:text-slate-300">📌 Patokan:</span>
            <span id="checkout-card-benchmark"><?= Security::e($deliveryAddress['benchmark'] ?? '') ?></span>
          </div>

          <!-- Google Maps & Catatan -->
          <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-amber-100 dark:border-slate-600/60">
            <a id="checkout-card-maps" href="<?= Security::e($deliveryAddress['maps_url'] ?: '#') ?>" target="_blank" class="<?= empty($deliveryAddress['maps_url']) ? 'hidden' : 'inline-flex' ?> items-center gap-1 text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
              <span>🗺️ Link Peta Google Maps Terpasang (Buka Rute) ↗</span>
            </a>
            <span id="checkout-card-notes-wrap" class="<?= empty($deliveryAddress['notes']) ? 'hidden' : 'inline-block' ?> text-[11px] text-slate-500 dark:text-slate-400 italic">
              💬 Catatan: "<span id="checkout-card-notes"><?= Security::e($deliveryAddress['notes'] ?? '') ?></span>"
            </span>
          </div>
        </div>

        <!-- FORMULIR INPUT TERSEMBUNYI / OTOMATIS SINKRON -->
        <div class="space-y-4 pt-2">
          <input type="hidden" id="checkout-input-label" name="label" value="<?= Security::e($deliveryAddress['label'] ?? 'Rumah') ?>">
          <input type="hidden" id="checkout-input-city" name="city" value="<?= Security::e($deliveryAddress['city'] ?? 'Kab. Majene') ?>">
          <input type="hidden" id="checkout-input-benchmark" name="benchmark" value="<?= Security::e($deliveryAddress['benchmark'] ?? '') ?>">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Lengkap Penerima *</label>
              <input type="text" id="checkout-customer-name" name="customer_name" required value="<?= Security::e(!empty($deliveryAddress['recipient_name']) ? $deliveryAddress['recipient_name'] : ($currentUser['full_name'] ?? '')) ?>" placeholder="Contoh: Muh. Fadhil Ramadhan" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-unsulbar-gold outline-none transition">
            </div>

            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nomor WhatsApp Aktif *</label>
              <input type="tel" id="checkout-customer-phone" name="customer_phone" required value="<?= Security::e(!empty($deliveryAddress['phone']) ? $deliveryAddress['phone'] : ($currentUser['phone'] ?? '')) ?>" placeholder="081234567890 (Kurir akan menghubungi via WA)" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-unsulbar-gold outline-none transition">
            </div>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-bold text-slate-600 dark:text-slate-300">Alamat Lengkap Pengantaran *</label>
              <span class="text-[10px] text-slate-400">Jalan, RT/RW, Dusun/Desa, Gedung/Kamar</span>
            </div>
            <textarea id="checkout-address-input" name="customer_address" required rows="2" placeholder="Sebutkan detail alamat pengiriman secara jelas..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-unsulbar-gold outline-none transition"><?= Security::e($deliveryAddress['address'] ?? '') ?></textarea>
          </div>

          <div>
            <div class="flex items-center justify-between mb-1">
              <label class="text-xs font-bold text-slate-600 dark:text-slate-300">
                🗺️ Tautan Google Maps (Terkirim Otomatis ke Kurir)
              </label>
              <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">Terkoneksi WhatsApp Kurir</span>
            </div>
            <input type="url" id="checkout-maps-input" name="maps_url" value="<?= Security::e($deliveryAddress['maps_url'] ?? '') ?>" placeholder="https://www.google.com/maps/search/?api=1&query=..." class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700/50 text-slate-900 dark:text-white text-xs focus:ring-2 focus:ring-unsulbar-gold outline-none transition font-mono">
          </div>

          <div>
            <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Catatan Tambahan untuk Kurir (Opsional)</label>
            <input type="text" id="checkout-notes-input" name="notes" value="<?= Security::e($deliveryAddress['notes'] ?? '') ?>" placeholder="Misal: Titip di satpam gedung, atau kabari sebelum sampai" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs sm:text-sm focus:ring-2 focus:ring-unsulbar-gold outline-none transition">
          </div>

          <?php if ($currentUser): ?>
            <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
              <label class="flex items-center gap-2 cursor-pointer select-none">
                <input type="checkbox" name="save_address_to_profile" value="1" checked class="w-4 h-4 rounded text-unsulbar-maroon focus:ring-unsulbar-maroon dark:bg-slate-700">
                <span class="text-xs font-medium text-slate-700 dark:text-slate-300">Simpan alamat dan lokasi ini ke profil akun saya</span>
              </label>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Box 2: Pilihan Metode Pembayaran Lengkap -->
      <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-700">
          <span class="w-7 h-7 rounded-xl bg-unsulbar-maroon text-white flex items-center justify-center text-xs font-bold">2</span>
          <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Pilih Metode Pembayaran</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
          
          <!-- Pilihan COD -->
          <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer hover:border-unsulbar-gold has-[:checked]:border-unsulbar-maroon has-[:checked]:bg-unsulbar-maroon/5 dark:has-[:checked]:border-unsulbar-gold transition">
            <input type="radio" name="payment_method" value="COD" checked class="sr-only" onchange="Cart.onPaymentMethodChange('COD')">
            <div class="text-2xl mb-1">💵</div>
            <span class="font-extrabold text-xs text-slate-900 dark:text-white">COD (Tunai)</span>
            <span class="text-[11px] text-slate-400 mt-1">Bayar tunai ke kurir saat paket tiba di tangan Anda.</span>
          </label>

          <!-- Pilihan QRIS -->
          <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer hover:border-unsulbar-gold has-[:checked]:border-unsulbar-maroon has-[:checked]:bg-unsulbar-maroon/5 dark:has-[:checked]:border-unsulbar-gold transition">
            <input type="radio" name="payment_method" value="QRIS" class="sr-only" onchange="Cart.onPaymentMethodChange('QRIS')">
            <div class="text-2xl mb-1">📱</div>
            <span class="font-extrabold text-xs text-slate-900 dark:text-white">QRIS Dinamis</span>
            <span class="text-[11px] text-slate-400 mt-1">BCA, Mandiri, BRI, GoPay, OVO, Dana, ShopeePay.</span>
          </label>

          <!-- Pilihan Transfer Bank -->
          <label class="relative flex flex-col p-4 rounded-2xl border-2 border-slate-200 dark:border-slate-700 cursor-pointer hover:border-unsulbar-gold has-[:checked]:border-unsulbar-maroon has-[:checked]:bg-unsulbar-maroon/5 dark:has-[:checked]:border-unsulbar-gold transition">
            <input type="radio" name="payment_method" value="TRANSFER" class="sr-only" onchange="Cart.onPaymentMethodChange('TRANSFER')">
            <div class="text-2xl mb-1">🏦</div>
            <span class="font-extrabold text-xs text-slate-900 dark:text-white">Transfer Bank</span>
            <span class="text-[11px] text-slate-400 mt-1">Rekening resmi BRI, BSI, atau Bank Mandiri.</span>
          </label>

        </div>

        <!-- Detail Instruksi QRIS (Muncul jika QRIS dipilih) -->
        <div id="payment-guide-qris" class="hidden p-5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 text-center space-y-3">
          <p class="text-xs font-bold text-amber-900 dark:text-amber-300 uppercase tracking-wider">
            QRIS Pembayaran Resmi Aksara Unsulbar
          </p>
          <div class="inline-block p-2 bg-white rounded-2xl shadow-inner border border-slate-200">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=00020101021226670014ID.LINKAJA.WWW01189360091100202610280215ID10202610280010303UMI51440014ID.CO.QRIS.WWW0215ID10202610280010303UMI5204541153033605802ID5916Aksara%20Unsulbar6006Majene61059141262070703A016304" alt="QRIS Aksara Unsulbar" class="w-44 h-44 object-contain">
          </div>
          <p class="text-[11px] text-slate-600 dark:text-slate-300">
            Pindai kode QRIS di atas dengan aplikasi mobile banking (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, Dana, LinkAja).
          </p>
        </div>

        <!-- Detail Rekening Transfer Bank (Muncul jika Transfer dipilih) -->
        <div id="payment-guide-transfer" class="hidden p-4 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 space-y-3">
          <p class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">
            Pilihan Rekening Resmi Aksara Unsulbar:
          </p>
          <div class="space-y-2">
            <?php foreach ($bankAccounts as $key => $bank): ?>
              <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-600 flex items-center justify-between">
                <div>
                  <span class="font-bold text-xs text-slate-900 dark:text-white"><?= Security::e($bank['bank']) ?></span>
                  <div class="flex items-center gap-2 mt-0.5">
                    <span class="font-mono text-xs font-black text-unsulbar-maroon dark:text-unsulbar-gold"><?= Security::e($bank['account_number']) ?></span>
                    <span class="text-[10px] text-slate-400">a.n <?= Security::e($bank['account_name']) ?></span>
                  </div>
                </div>
                <button type="button" onclick="App.copyToClipboard('<?= Security::e($bank['account_number']) ?>', 'Nomor rekening <?= Security::e($bank['bank']) ?> disalin!')" class="px-2.5 py-1 text-[11px] font-bold rounded-lg border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition">
                  Salin
                </button>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

      </div>

      <!-- Box 3: Unggah Bukti Pembayaran -->
      <div id="box-payment-proof" class="bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-700">
          <div class="flex items-center gap-2">
            <span class="w-7 h-7 rounded-xl bg-organic-green text-white flex items-center justify-center text-xs font-bold">3</span>
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">Rincian & Bukti Pembayaran</h2>
          </div>
          <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
            JPG / PNG / PDF
          </span>
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
            Unggah Foto / Tangkapan Layar Bukti Transfer (Opsional saat ini)
          </label>
          <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-600 border-dashed rounded-2xl hover:border-unsulbar-gold transition relative group">
            <div class="space-y-2 text-center">
              <svg class="mx-auto h-10 w-10 text-slate-400 group-hover:text-unsulbar-maroon dark:group-hover:text-unsulbar-gold transition" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              <div class="flex text-xs text-slate-600 dark:text-slate-400 justify-center">
                <label for="input-payment-proof" class="relative cursor-pointer font-bold text-unsulbar-maroon dark:text-unsulbar-gold hover:underline">
                  <span>Pilih file bukti bayar</span>
                  <input id="input-payment-proof" name="payment_proof" type="file" accept="image/*,application/pdf" class="sr-only" onchange="Cart.previewProof(this)">
                </label>
                <p class="pl-1">atau seret ke sini</p>
              </div>
              <p class="text-[10px] text-slate-400">Format JPG, PNG, WEBP, atau PDF hingga 5MB</p>
            </div>
          </div>

          <!-- Pratinjau File Bukti -->
          <div id="proof-preview-container" class="hidden mt-3 p-3 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <img id="proof-preview-img" src="" alt="Bukti" class="w-12 h-12 object-cover rounded-xl border border-emerald-300">
              <div>
                <span id="proof-preview-filename" class="text-xs font-bold text-emerald-900 dark:text-emerald-200 line-clamp-1"></span>
                <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-medium">✓ Bukti siap dikirim bersama pesanan</span>
              </div>
            </div>
            <button type="button" onclick="Cart.clearProof()" class="text-xs font-bold text-red-500 hover:text-red-700 px-2 py-1">
              Batal
            </button>
          </div>

          <p class="text-[11px] text-slate-400 mt-2">
            💡 <em>Catatan:</em> Untuk metode COD Anda tidak perlu mengunggah bukti. Jika menggunakan QRIS/Transfer dan belum sempat transfer sekarang, Anda dapat mengunggah bukti bayar nanti melalui menu <strong>Status Pesanan</strong>.
          </p>
        </div>
      </div>

    </div>

    <!-- KOLOM KANAN: RINCIAN LENGKAP TAGIHAN & KONFIRMASI -->
    <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-3xl p-6 border border-slate-200 dark:border-slate-700 shadow-sm space-y-6 sticky top-36">
      <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700 pb-3">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-900 dark:text-white">
          Rincian Lengkap Tagihan
        </h2>
        <span class="text-[11px] font-bold text-slate-400">Sebelum Checkout</span>
      </div>

      <!-- Daftar Singkat Item -->
      <div class="max-h-60 overflow-y-auto space-y-3 pr-2 divide-y divide-slate-100 dark:divide-slate-700/60">
        <?php foreach ($cartSummary['items'] as $item): ?>
          <div class="pt-2 flex items-center justify-between text-xs">
            <div class="flex items-center gap-3">
              <img src="<?= Security::e($item['image_url']) ?>" alt="<?= Security::e($item['name']) ?>" class="w-11 h-11 rounded-xl object-cover bg-slate-100 border border-slate-200 dark:border-slate-700">
              <div>
                <p class="font-bold text-slate-900 dark:text-white line-clamp-1"><?= Security::e($item['name']) ?></p>
                <p class="text-slate-400"><?= $item['quantity'] ?>x @ Rp <?= number_format($item['price'], 0, ',', '.') ?></p>
              </div>
            </div>
            <span class="font-extrabold text-slate-900 dark:text-white whitespace-nowrap">
              Rp <?= number_format($item['subtotal'], 0, ',', '.') ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- Detail Perhitungan Lengkap -->
      <div class="pt-4 border-t border-slate-100 dark:border-slate-700 space-y-2.5 text-xs sm:text-sm">
        <div class="flex justify-between text-slate-600 dark:text-slate-300">
          <span>Subtotal Produk (<?= $cartSummary['total_items'] ?> item)</span>
          <span class="font-bold text-slate-900 dark:text-white">
            Rp <?= number_format($cartSummary['subtotal'], 0, ',', '.') ?>
          </span>
        </div>

        <div class="flex justify-between text-slate-600 dark:text-slate-300">
          <span>Biaya Pengantaran Kurir Unsulbar</span>
          <span class="font-bold text-slate-900 dark:text-white">
            Rp <?= number_format($cartSummary['shipping_fee'], 0, ',', '.') ?>
          </span>
        </div>

        <div class="flex justify-between text-slate-600 dark:text-slate-300">
          <span>Biaya Penanganan / Layanan</span>
          <span class="font-bold text-emerald-600 dark:text-emerald-400">
            Rp 0 (GRATIS)
          </span>
        </div>

        <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex justify-between items-baseline">
          <div>
            <span class="font-black text-slate-900 dark:text-white text-base">TOTAL AKHIR</span>
            <span class="text-[10px] text-slate-400 block">Termasuk ongkir kurir kampus</span>
          </div>
          <span class="text-2xl font-black text-unsulbar-maroon dark:text-unsulbar-gold">
            Rp <?= number_format($cartSummary['total_amount'], 0, ',', '.') ?>
          </span>
        </div>
      </div>

      <!-- Tombol Submit & WhatsApp Trigger -->
      <button id="btn-submit-order" type="submit" class="interactive-btn w-full py-4 rounded-2xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-black text-sm flex items-center justify-center gap-2 shadow-xl shadow-unsulbar-maroon/25 transition">
        <span>Konfirmasi & Teruskan ke Kurir WA</span>
        <svg class="w-5 h-5 text-unsulbar-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </button>

      <div class="p-3 bg-slate-50 dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700 text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed space-y-1">
        <p class="font-bold text-slate-800 dark:text-slate-200">✅ Keuntungan Pemesanan Aksara:</p>
        <p>• Transaksi tercatat aman langsung ke database resmi.</p>
        <p>• Peta lokasi pengantaran Anda langsung otomatis dikirim ke Google Maps kurir.</p>
        <p>• Anda dapat melacak status pesanan secara live kapan saja.</p>
      </div>

    </div>

  </form>

</main>
