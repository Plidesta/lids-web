<?php
// views/layouts/footer.php
?>
  <!-- FOOTER RESMI UNSULBAR & AKSARA BERDAMPAK -->
  <footer class="mt-auto bg-white dark:bg-slate-800/90 border-t border-slate-200 dark:border-slate-700/80 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <!-- Kolom 1: Profil Program -->
        <div class="md:col-span-2 space-y-4">
          <div class="flex items-center gap-3">
            <img src="assets/images/branding/aksara-logo.png" alt="Aksara" class="w-11 h-11 rounded-2xl shadow object-cover">
            <img src="assets/images/branding/Unsulbar-Logo.png" alt="Emblem Aksara Unsulbar" class="w-11 h-11 rounded-full shadow object-cover">
            <div>
              <span class="text-sm font-extrabold text-unsulbar-maroon dark:text-unsulbar-gold block">Aksara Mahasiswa Berdampak</span>
              <span class="text-xs text-slate-500 dark:text-slate-400">Universitas Sulawesi Barat</span>
            </div>
          </div>
          <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed pr-6">
            Program hilirisasi inovasi agro-industri sirkular komoditas jamur tiram Berbasis IoT dan Pengolahan pupuk organik limbah baglog. Dikelola oleh kolaborasi mahasiswa lintas program studi Unsulbar untuk mendorong kewirausahaan dan solusi ramah lingkungan bagi masyarakat.
          </p>
          <div class="flex items-center gap-2 text-xs font-semibold text-organic-green dark:text-emerald-400">
            <span> Sirkular Ekonomi</span>
            <span>•</span>
            <span> Pangan Sehat</span>
            <span>•</span>
            <span> Berbasis Teknologi</span>
          </div>
        </div>

        <!-- Kolom 2: Navigasi Cepat -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Navigasi Halaman</h4>
          <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-300">
            <li><a href="?page=dashboard" class="hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold transition">Dashboard Utama</a></li>
            <li><a href="?page=catalog" class="hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold transition">Pemesanan & Katalog</a></li>
            <li><a href="?page=cart" class="hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold transition">Keranjang Belanja</a></li>
            <li><a href="?page=tracking" class="hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold transition">Status & Pelacakan</a></li>
            <li><a href="?page=contact" class="hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold transition">Kontak & Tim Mahasiswa</a></li>
          </ul>
        </div>

        <!-- Kolom 3: Lokasi & Kontak Resmi -->
        <div>
          <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">Lokasi Kegiatan</h4>
          <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-3">
            Kampus Padhang-Padhang, Gedung Laboratorium Terpadu & Desa Papandangan, kec Anreapi, kab Polewali Mandar, Prov Sulawesi Barat<br>
            Kabupaten Majene, Sulawesi Barat 91412
          </p>
          <div class="text-xs space-y-1">
            <p class="text-slate-500">WhatsApp Hotline: <span class="text-slate-800 dark:text-slate-200 font-semibold">+62 821-9000-1234</span></p>
            <p class="text-slate-500">Email: <span class="text-slate-800 dark:text-slate-200 font-semibold">aksara@unsulbar.ac.id</span></p>
          </div>
        </div>

      </div>

      <!-- Bottom Bar -->
      <div class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-700/60 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4">
        <p>© <?= date('Y') ?> Aksara Mahasiswa Berdampak. Universitas Sulawesi Barat. All rights reserved.</p>
        <p class="flex items-center gap-1">
          <span>Dikembangkan dengan standar keamanan</span>
          <span class="font-bold text-unsulbar-maroon dark:text-unsulbar-gold">OWASP & Enkripsi SSL</span>
        </p>
      </div>
    </div>
  </footer>

  <!-- Include Modals & Toasts -->
  <?php require_once dirname(__DIR__) . '/components/toast.php'; ?>
  <?php require_once dirname(__DIR__) . '/components/auth-modal.php'; ?>
  <?php require_once dirname(__DIR__) . '/components/address-modal.php'; ?>
  <?php require_once dirname(__DIR__) . '/components/order-modal.php'; ?>

  <!-- Scripts -->
  <script src="assets/js/app.js"></script>
  <script src="assets/js/cart.js"></script>
  <script src="assets/js/filter.js"></script>

</body>
</html>
