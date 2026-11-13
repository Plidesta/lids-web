<?php
// views/pages/dashboard.php
use App\Helpers\Security;
?>

<!-- HERO SECTION: Definisi & Visi Program -->
<section class="relative overflow-hidden bg-gradient-to-br from-unsulbar-maroon via-unsulbar-maroon-dark to-slate-900 text-white py-16 lg:py-24">
  <!-- Decorative Background Glow -->
  <div class="absolute -top-24 -right-24 w-96 h-96 bg-unsulbar-gold/20 rounded-full blur-3xl pointer-events-none"></div>
  <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-organic-green/20 rounded-full blur-3xl pointer-events-none"></div>

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
      
      <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-xs font-bold text-amber-300">
          <span class="w-2 h-2 rounded-full bg-organic-green animate-ping"></span>
          Inovasi Berkelanjutan Mahasiswa Unsulbar
        </div>

        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
          Hilirisasi Inovasi <br>
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-unsulbar-gold via-amber-200 to-emerald-300">
            Jamur & Pupuk Organik
          </span>
        </h1>

        <p class="text-sm sm:text-base text-slate-200 leading-relaxed max-w-2xl">
          Program <strong>"Aksara Mahasiswa Berdampak"</strong> mewujudkan integrasi sirkular ekonomi: Budidaya jamur tiram berbasis iot, mengolah jamur tiram segar menjadi aneka pangan bernutrisi tinggi yg dipasarkan melalui website, sekaligus mengolah limbah baglog sisa panen menjadi pupuk hayati organik bernilai tambah bagi pertanian.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
          <a href="?page=catalog" class="interactive-btn px-6 py-3.5 rounded-2xl bg-unsulbar-gold hover:bg-unsulbar-gold-hover text-slate-950 font-black text-sm flex items-center gap-2 shadow-lg shadow-unsulbar-gold/25 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            Belanja Produk Sekarang
          </a>
          <a href="#tentang-program" class="interactive-btn px-6 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-bold text-sm border border-white/20 backdrop-blur-sm transition">
            Visi Program
          </a>
        </div>

        <!-- Trust Badges -->
        <div class="grid grid-cols-3 gap-4 pt-6 border-t border-white/10">
          <div>
            <div class="text-2xl font-black text-unsulbar-gold">100%</div>
            <div class="text-[11px] text-slate-300">Bahan Alami & Higienis</div>
          </div>
          <div>
            <div class="text-2xl font-black text-emerald-400">Zero-Waste</div>
            <div class="text-[11px] text-slate-300">Limbah Jadi Pupuk Kompos</div>
          </div>
          <div>
            <div class="text-2xl font-black text-amber-300">Kilat</div>
            <div class="text-[11px] text-slate-300">Kurir bergerak cepat</div>
          </div>
        </div>
      </div>

      <!-- Hero Visual Card: Emblem Resmi Aksara (Sisi Kanan Hero) -->
      <div class="lg:col-span-5 flex justify-center">
        <div class="relative mx-auto max-w-md bg-white/10 backdrop-blur-xl p-6 rounded-3xl border border-white/20 shadow-2xl text-center group">
          <div class="relative mx-auto w-60 h-60 sm:w-72 sm:h-72 rounded-full overflow-hidden shadow-2xl border-4 border-unsulbar-gold/90 p-1 bg-gradient-to-br from-unsulbar-gold/40 via-amber-200/20 to-transparent">
            <img src="assets/images/branding/Unsulbar-Logo.png" alt="Aksara Mahasiswa Berdampak Unsulbar" class="w-full h-full object-cover rounded-full group-hover:scale-105 transition-transform duration-500 shadow-inner">
          </div>
          
          <div class="mt-5 space-y-1">
            <div class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-unsulbar-gold/20 text-unsulbar-gold text-[11px] font-bold border border-unsulbar-gold/30">
              <span>🏛️ Universitas Sulawesi Barat</span>
            </div>
            <h3 class="text-base font-extrabold text-white tracking-tight mt-1">Aksara Berdampak</h3>
            <p class="text-xs text-slate-200">Simbol Sinergi Ketahanan Pangan, Jamur Tiram & Sirkular Ekonomi Berbasis IoT</p>
          </div>
          
          <div class="mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs text-slate-300">
            <div class="flex items-center gap-2">
              <img src="assets/images/branding/aksara-logo.png" alt="Aksara" class="w-6 h-6 rounded-lg object-cover">
              <span>Program Inovasi Aksara berdampak</span>
            </div>
            <span class="text-emerald-400 font-bold">2026</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- SEKSI BANNER PROMOSI & BUNDLING MENARIK -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-20">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach ($promos as $promo): ?>
      <div class="interactive-card bg-gradient-to-r from-amber-500/10 to-emerald-500/10 dark:from-slate-800 dark:to-slate-800/80 rounded-3xl p-6 border border-amber-300/40 dark:border-slate-700 shadow-md backdrop-blur-md flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <span class="text-[10px] font-black tracking-wider uppercase px-2.5 py-1 rounded-md bg-unsulbar-gold text-slate-950 inline-block mb-2 shadow-sm">
            <?= Security::e($promo['badge']) ?>
          </span>
          <h3 class="text-base font-extrabold text-slate-900 dark:text-white"><?= Security::e($promo['title']) ?></h3>
          <p class="text-xs text-slate-600 dark:text-slate-300 mt-1"><?= Security::e($promo['subtitle']) ?></p>
        </div>
        <a href="<?= $promo['link'] ?>" class="interactive-btn px-4 py-2.5 rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white text-xs font-bold whitespace-nowrap shadow-sm">
          Ambil Promo →
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SEKSI PRODUK UNGGULAN (Olahan Pangan Jamur & Pupuk Organik) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-4">
    <div>
      <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-unsulbar-maroon dark:text-unsulbar-gold">
        <span class="w-2.5 h-2.5 rounded-full bg-unsulbar-maroon dark:bg-unsulbar-gold"></span>
        Katalog Unggulan
      </div>
      <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
        Produk Pilihan 
      </h2>
      <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
        Hasil inovasi langsung dari olahan pangan jamur tiram dan pupuk organik limbah baglog.
      </p>
    </div>
    <a href="?page=catalog" class="interactive-btn inline-flex items-center gap-2 text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold hover:underline">
      Lihat Semua Katalog Produk
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </a>
  </div>

  <!-- Grid Produk -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($featuredProducts as $product): 
      $priceFormatted = number_format($product['price'], 0, ',', '.');
      $isOutOfStock = ($product['stock'] <= 0);
    ?>
      <div class="interactive-card bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between group">
        
        <!-- Gambar & Badge -->
        <div class="relative bg-slate-100 dark:bg-slate-900 aspect-4/3 overflow-hidden">
          <img src="<?= Security::e($product['image_url']) ?>" alt="<?= Security::e($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
          <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
            <?php 
              $dashBadge = 'bg-slate-700 text-white';
              if ($product['category_slug'] === 'olahan-pangan-jamur') $dashBadge = 'bg-emerald-600 text-white';
              elseif ($product['category_slug'] === 'pupuk-organik-limbah') $dashBadge = 'bg-amber-600 text-white';
              elseif ($product['category_slug'] === 'produk-paket-budidaya') $dashBadge = 'bg-sky-600 text-white';
            ?>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider <?= $dashBadge ?>">
              <?= Security::e($product['category_name']) ?>
            </span>
          </div>
          <!-- Badge Stok -->
          <div class="absolute bottom-3 right-3">
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg backdrop-blur-md shadow-sm <?= $isOutOfStock ? 'bg-red-500/90 text-white' : 'bg-slate-900/80 text-white' ?>">
              <?= $isOutOfStock ? 'Stok Habis' : 'Sisa ' . $product['stock'] . ' ' . $product['unit'] ?>
            </span>
          </div>
        </div>

        <!-- Body Card -->
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
              <span class="text-xs text-slate-400">/<?= Security::e($product['unit']) ?></span>
            </div>

            <!-- Tombol Aksi -->
            <div class="grid grid-cols-2 gap-2">
              <button onclick="Cart.add(<?= $product['id'] ?>, 1, false)" <?= $isOutOfStock ? 'disabled' : '' ?> class="interactive-btn py-2 px-3 rounded-xl border border-unsulbar-maroon/30 dark:border-unsulbar-gold/30 text-unsulbar-maroon dark:text-unsulbar-gold font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-unsulbar-maroon/5 dark:hover:bg-unsulbar-gold/10 disabled:opacity-40 disabled:cursor-not-allowed">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                + Keranjang
              </button>
              <button onclick="Cart.add(<?= $product['id'] ?>, 1, true)" <?= $isOutOfStock ? 'disabled' : '' ?> class="interactive-btn py-2 px-3 rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm disabled:opacity-40 disabled:cursor-not-allowed">
                Beli Sekarang
              </button>
            </div>
          </div>

        </div>

      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- SEKSI TENTANG PROGRAM & VISI SIRKULAR -->
<section id="tentang-program" class="bg-white dark:bg-slate-800/60 py-16 border-y border-slate-200/80 dark:border-slate-700/60">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
      
      <div class="space-y-5">
        <span class="text-xs font-bold uppercase tracking-wider text-organic-green dark:text-emerald-400">
          Visi & Nilai Berdampak
        </span>
        <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
          Menghubungkan Ketahanan Pangan dengan Kelestarian Lingkungan
        </h2>
        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
          Universitas Sulawesi Barat berkomitmen mendorong mahasiswa agar tidak hanya unggul secara akademis, tetapi juga menciptakan solusi riil yang langsung dirasakan manfaatnya oleh masyarakat.
        </p>
        <div class="space-y-3 pt-2">
          <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40">
            <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 text-base">🍄</div>
            <div>
              <h4 class="text-xs font-bold text-slate-900 dark:text-white">Pangan Sehat Berbasis Lokal</h4>
              <p class="text-xs text-slate-500 dark:text-slate-400">Mengembangkan olahan jamur kaya antioksidan dan protein nabati tinggi yang diproduksi higienis.</p>
            </div>
          </div>
          <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40">
            <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-950 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0 text-base">🌱</div>
            <div>
              <h4 class="text-xs font-bold text-slate-900 dark:text-white">Sirkular Ekonomi & Dekomposisi Baglog</h4>
              <p class="text-xs text-slate-500 dark:text-slate-400">Mengubah limbah media tanam pasca panen menjadi pupuk kompos hayati, menyuburkan kembali tanah pertanian petani lokal.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Diagram Sirkular Visual -->
      <div class="p-8 rounded-3xl bg-gradient-to-br from-slate-900 to-unsulbar-maroon text-white relative shadow-2xl">
        <h3 class="text-lg font-black text-unsulbar-gold mb-4 text-center">Siklus Sirkular Ekonomi Aksara</h3>
        <div class="space-y-4 text-xs">
          <div class="p-3 rounded-xl bg-white/10 flex items-center justify-between border border-white/10">
            <span class="font-bold">1. Media Serbuk Kayu Alami</span>
            <span class="text-amber-300">Sterilisasi & Inokulasi</span>
          </div>
          <div class="text-center text-unsulbar-gold text-lg">↓</div>
          <div class="p-3 rounded-xl bg-white/10 flex items-center justify-between border border-white/10">
            <span class="font-bold">2. Panen Jamur Tiram Segar</span>
            <span class="text-emerald-300">olahan pangan kaya serat</span>
          </div>
          <div class="text-center text-unsulbar-gold text-lg">↓</div>
          <div class="p-3 rounded-xl bg-white/10 flex items-center justify-between border border-white/10">
            <span class="font-bold">3. Limbah Baglog Pasca Panen</span>
            <span class="text-amber-300">Dekomposisi Trichoderma</span>
          </div>
          <div class="text-center text-unsulbar-gold text-lg">↓</div>
          <div class="p-3 rounded-xl bg-emerald-950/80 flex items-center justify-between border border-emerald-500/50">
            <span class="font-bold text-emerald-300">4. Pupuk Kompos Organik</span>
            <span class="text-emerald-200">Menyuburkan Lahan Tani</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- SEKSI GALERI DOKUMENTASI KEGIATAN MAHASISWA -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div class="text-center max-w-2xl mx-auto mb-12">
    <span class="text-xs font-bold uppercase tracking-wider text-unsulbar-maroon dark:text-unsulbar-gold">Dokumentasi Lapangan</span>
    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
      Aktivitas Mahasiswa di Laboratorium & Kumbung
    </h2>
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2">
      Bukti nyata karya riset dan implementasi pengabdian masyarakat oleh sivitas akademika Universitas Sulawesi Barat.
    </p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($activities as $act): ?>
      <div class="interactive-card bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
        <div class="relative aspect-4/3 bg-slate-100 dark:bg-slate-900 overflow-hidden">
          <img src="<?= Security::e($act['image']) ?>" alt="<?= Security::e($act['title']) ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
          <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-900/80 text-white backdrop-blur-sm">
            <?= Security::e($act['tag']) ?>
          </span>
        </div>
        <div class="p-4 flex-1 flex flex-col justify-between">
          <div>
            <h4 class="font-bold text-sm text-slate-900 dark:text-white"><?= Security::e($act['title']) ?></h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed"><?= Security::e($act['description']) ?></p>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
