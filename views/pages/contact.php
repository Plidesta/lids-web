<?php
// views/pages/contact.php
use App\Helpers\Security;
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
  
  <!-- HEADER KONTAK -->
  <div class="text-center max-w-2xl mx-auto">
    <div class="inline-flex items-center gap-2 text-xs font-bold text-unsulbar-maroon dark:text-unsulbar-gold uppercase tracking-wider">
      <span>Hubungi Kami</span>
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
      Pusat Komunikasi & Tim Aksara Unsulbar
    </h1>
    <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2">
      Siap melayani pertanyaan seputar pemesanan produk jamur, pupuk kompos hayati, kemitraan petani, dan edukasi budidaya.
    </p>
  </div>

  <!-- GRID 1: INFO KONTAK & GOOGLE MAPS LOKASI KEGIATAN -->
  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-stretch">
    
    <!-- Info Kontak Resmi -->
    <div class="lg:col-span-5 bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between space-y-6">
      <div>
        <div class="flex items-center gap-3 mb-4">
          <img src="assets/images/branding/aksara-logo.png" alt="Emblem Aksara" class="w-10 h-10 rounded-full border-2 border-unsulbar-gold object-cover shadow">
          <h2 class="text-lg font-extrabold text-slate-900 dark:text-white">Akasara berdampak</h2>
        </div>
        
        <div class="space-y-4 text-xs sm:text-sm">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-unsulbar-maroon/10 dark:bg-unsulbar-gold/10 text-unsulbar-maroon dark:text-unsulbar-gold flex items-center justify-center shrink-0 font-bold">
              📍
            </div>
            <div>
              <p class="font-bold text-slate-800 dark:text-slate-200">Alamat Kegiatan & Sentra Budidaya</p>
              <p class="text-slate-500 dark:text-slate-400 mt-0.5">
                Desa Papandangan, Kec. Anreapi, Kab. Polewali Mandar, Sulawesi Barat<br>
                (Sentra Kumbung Budidaya Jamur Tiram & Rumah Dekomposisi Pupuk Organik)
              </p>
            </div>
          </div>

          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 font-bold">
              💬
            </div>
            <div>
              <p class="font-bold text-slate-800 dark:text-slate-200">WhatsApp Hotline Kurir</p>
              <p class="text-slate-500 dark:text-slate-400 mt-0.5">+62 821-9000-1234 (Aktif 08.00 - 20.00 WITA)</p>
            </div>
          </div>

          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-950 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 font-bold">
              ✉️
            </div>
            <div>
              <p class="font-bold text-slate-800 dark:text-slate-200">Email Resmi</p>
              <p class="text-slate-500 dark:text-slate-400 mt-0.5">aksara@unsulbar.ac.id</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Action WA -->
      <a href="https://wa.me/6282190001234?text=Halo%20Tim%20Aksara%20Unsulbar,%20saya%20ingin%20bertanya%20seputar%20produk%20dan%20pemesanan." target="_blank" class="interactive-btn w-full py-3.5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-md">
        <span>Chat Langsung via WhatsApp</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
      </a>
    </div>

    <!-- Integrasi Google Maps Lokasi Desa Papandangan, Kab. Polewali Mandar -->
    <div class="lg:col-span-7 bg-white dark:bg-slate-800 rounded-3xl p-4 sm:p-6 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-3 px-2">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
          Peta Lokasi Desa Papandangan, Kab. Polewali Mandar
        </h3>
        <span class="text-[11px] text-organic-green dark:text-emerald-400 font-bold">Sentra Budidaya Jamur & Pupuk</span>
      </div>
      <div class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <iframe 
          title="Lokasi Desa Papandangan, Kec. Anreapi, Kab. Polewali Mandar"
          src="https://maps.google.com/maps?q=-3.4116417,119.342125+(Desa+Papandangan,+Kec.+Anreapi,+Kab.+Polewali+Mandar)&amp;t=&amp;z=15&amp;ie=UTF8&amp;iwloc=&amp;output=embed" 
          width="100%" 
          height="100%" 
          style="border:0;" 
          allowfullscreen="" 
          loading="lazy" 
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
    </div>

  </div>

  <!-- SEKSI PROFIL HIMPUNAN MAHASISWA & KONTRIBUSI KOLABORATIF -->
  <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 sm:p-10 border border-slate-200 dark:border-slate-700 shadow-sm space-y-8">
    <div class="text-center max-w-2xl mx-auto">
      <span class="text-xs font-bold uppercase tracking-wider text-organic-green dark:text-emerald-400">Kolaborasi Himpunan Mahasiswa</span>
      <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight mt-1">
        Sinergi Multidisiplin Tim Aksara Berdampak
      </h2>
      <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2">
        Kegiatan ini diinisiasi oleh kolaborasi himpunan mahasiswa lintas program studi Universitas Sulawesi Barat untuk memecahkan persoalan ketahanan pangan dan pengelolaan limbah organik secara terintegrasi.
      </p>
    </div>

    <!-- Card Profil Tim & Himpunan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
      <?php foreach ($teamMember as $member): ?>
        <div class="interactive-card bg-slate-50 dark:bg-slate-700/40 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-700 text-center space-y-3">
          <div class="w-16 h-16 rounded-full mx-auto overflow-hidden bg-unsulbar-maroon p-1 border-2 border-unsulbar-gold">
            <img src="<?= Security::e($member['avatar']) ?>" alt="<?= Security::e($member['name']) ?>" class="w-full h-full object-cover">
          </div>
          <div>
            <h4 class="font-bold text-sm text-slate-900 dark:text-white"><?= Security::e($member['name']) ?></h4>
            <p class="text-xs font-semibold text-unsulbar-maroon dark:text-unsulbar-gold mt-0.5"><?= Security::e($member['role']) ?></p>
            <p class="text-[11px] text-slate-400 mt-1"><?= Security::e($member['dept']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  

  <!-- SEKSI PENYEMATAN DOKUMENTASI VIDEO (Embedded Video Player) -->
  <div class="bg-slate-900 text-white rounded-3xl p-6 sm:p-10 shadow-xl overflow-hidden relative">
    <div class="text-center max-w-2xl mx-auto mb-8">
      <span class="text-xs font-bold uppercase tracking-wider text-unsulbar-gold">Dokumentasi Video</span>
      <h2 class="text-2xl sm:text-3xl font-black tracking-tight mt-1">
        Perjalanan Riset & Produksi Mahasiswa
      </h2>
      <p class="text-xs sm:text-sm text-slate-300 mt-2">
        Saksikan bagaimana mahasiswa Unsulbar mengelola kumbung jamur tiram, meracik olahan krispi, hingga mendaur ulang limbah menjadi kompos berkualitas.
      </p>
    </div>

    <!-- Video Embed Container (16:9 Aspect Ratio) -->
    <div class="max-w-4xl mx-auto rounded-2xl overflow-hidden shadow-2xl border border-white/20 aspect-video bg-black relative">
      <iframe 
        class="w-full h-full"
        src="https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1" 
        title="Dokumentasi Video Aksara Mahasiswa Berdampak Unsulbar" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
        allowfullscreen>
      </iframe>
    </div>
  </div>

</main>
