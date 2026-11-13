<?php
// views/layouts/header.php
use App\Config\App;
use App\Helpers\Security;

$currentUser = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?= Security::e(Security::csrfToken()) ?>">
  <title><?= Security::e($pageTitle ?? 'Aksara Mahasiswa Berdampak Unsulbar') ?></title>

  <!-- Favicon Logo Kegiatan -->
  <link rel="icon" type="image/png" href="assets/images/branding/aksara-logo.png">
  <link rel="icon" type="image/svg+xml" href="assets/images/branding/aksara-logo.png">

  <!-- Tailwind CSS CDN dengan Konfigurasi Brand Unsulbar -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            'unsulbar-maroon': {
              DEFAULT: '#7b1113',
              dark: '#5a0c0e',
              light: '#991b1b',
            },
            'unsulbar-gold': {
              DEFAULT: '#eab308',
              hover: '#ca8a04',
              light: '#fde047',
            },
            'organic-green': {
              DEFAULT: '#10b981',
              dark: '#059669',
              emerald: '#064e3b',
            }
          }
        }
      }
    }
  </script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/app.css">
</head>

<body class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 min-h-screen flex flex-col antialiased transition-colors duration-300">

  <!-- TOP HEADER (Header & Navigasi Atas) -->
  <header class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700/80 sticky top-0 z-40 transition-colors shadow-xs">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between min-h-[5.5rem] sm:min-h-[6.5rem] py-2 sm:py-3 gap-2 sm:gap-4">

        <!-- Sisi Kiri: Logo Kegiatan -->
        <a href="?page=dashboard" class="flex items-center gap-2 sm:gap-3 group shrink-0">
          <img src="assets/images/branding/aksara-logo.png" alt="Logo Aksara Berdampak" class="w-9 h-9 sm:w-11 sm:h-11 rounded-2xl shadow-md group-hover:scale-105 transition-transform duration-300 object-cover">
          <div class="hidden sm:block">
            <span class="text-[10px] font-bold tracking-widest uppercase text-organic-green dark:text-emerald-300 block">Program Inovasi</span>
            <span class="text-base font-extrabold text-unsulbar-maroon dark:text-unsulbar-gold tracking-tight group-hover:opacity-80 transition">AKSARA</span>
          </div>
        </a>

        <!-- Tengah: Tulisan Teks Utama & 5 Logo di Atas Tulisan Header -->
        <div class="flex flex-col items-center justify-center px-1 sm:px-2 text-center flex-1 max-w-2xl mx-auto">
          <!-- 5 Logo Di Atas Tulisan Header -->
          <div class="flex items-center justify-center gap-1.5 sm:gap-3.5 mb-1 sm:mb-1.5 flex-wrap">
            <img src="assets/images/branding/logo-1-kemendiktisaintek.png" alt="Kemendiktisaintek" class="h-4.5 sm:h-7 md:h-8 w-auto max-h-7 sm:max-h-8 object-contain hover:scale-105 transition-transform" title="Kementerian Pendidikan Tinggi, Sains, dan Teknologi">
            <img src="assets/images/branding/logo-2-risbang.png" alt="Ditjen Risbang" class="h-4.5 sm:h-7 md:h-8 w-auto max-h-7 sm:max-h-8 object-contain hover:scale-105 transition-transform" title="Direktorat Jenderal Riset dan Pengembangan">
            <img src="assets/images/branding/logo-3-diktisaintek.png" alt="Diktisaintek Berdampak" class="h-4.5 sm:h-7 md:h-8 w-auto max-h-7 sm:max-h-8 object-contain hover:scale-105 transition-transform" title="Diktisaintek Berdampak">
            <img src="assets/images/branding/logo-4-mahasiswa-berdampak.png" alt="Mahasiswa Berdampak" class="h-4.5 sm:h-7 md:h-8 w-auto max-h-7 sm:max-h-8 object-contain hover:scale-105 transition-transform" title="Mahasiswa Berdampak">
          </div>
          <h1 class="text-xs sm:text-base md:text-lg font-extrabold tracking-tight text-slate-800 dark:text-white flex items-center gap-1.5 leading-snug">
            <span>Aksara Mahasiswa Berdampak</span>
          </h1>
          <p class="text-[10px] sm:text-xs font-medium text-slate-500 dark:text-slate-400 hidden sm:block mt-0.5">
            Olahan Pangan Jamur • Pupuk Organik • Paket Budidaya
          </p>
        </div>

        <!-- Sisi Kanan: Switch Mode, Menu Profil & Logo Unsulbar -->
        <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">

          <!-- Tombol Switch Mode (Gelap / Terang) -->
          <button id="theme-toggle" type="button" aria-label="Ganti Tema" class="p-2 sm:p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition shadow-xs">
            <svg id="theme-moon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg id="theme-sun" class="w-4 h-4 hidden text-unsulbar-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
          </button>

          <!-- Kondisi Autentikasi: Profil Pengguna vs Tombol Masuk/Daftar -->
          <?php if ($currentUser): ?>
            <!-- Profil Masuk -->
            <div class="relative group">
              <button id="user-menu-btn" type="button" class="flex items-center gap-2 p-1.5 pl-3 rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:border-unsulbar-gold transition">
                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 max-w-[80px] sm:max-w-[120px] truncate">
                  <?= Security::e($currentUser['full_name']) ?>
                </span>
                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-unsulbar-maroon text-unsulbar-gold font-extrabold flex items-center justify-center text-xs shadow-inner">
                  <?= strtoupper(substr($currentUser['full_name'], 0, 1)) ?>
                </div>
              </button>
              <!-- Dropdown Menu -->
              <div id="user-menu-dropdown" class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 py-2 hidden group-hover:block transition-all z-50">
                <div class="px-4 py-2 border-b border-slate-100 dark:border-slate-700/60">
                  <p class="text-[10px] text-slate-400">Masuk sebagai:</p>
                  <p class="text-xs font-bold truncate text-slate-800 dark:text-slate-100"><?= Security::e($currentUser['email']) ?></p>
                  <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                    <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-black bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">ADMINISTRATOR</span>
                  <?php endif; ?>
                </div>
                
                <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                  <!-- Menu Monitoring Produk untuk Login Admin -->
                  <a href="?page=admin-products" class="block px-4 py-2 text-xs font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/40">
                    📊 Monitoring Produk & Stok
                  </a>
                  <a href="?page=admin-orders" class="block px-4 py-2 text-xs font-bold text-purple-600 dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40">
                    🛠️ Panel Kelola Pesanan
                  </a>
                  <div class="border-t border-slate-100 dark:border-slate-700/60 my-1"></div>
                <?php endif; ?>

                <a href="?page=tracking" class="block px-4 py-2 text-xs text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700">
                  📦 Riwayat Pesanan Saya
                </a>
                <div class="border-t border-slate-100 dark:border-slate-700/60 my-1"></div>
                <a href="?action=auth/logout" class="block px-4 py-2 text-xs font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40">
                  Keluar Akun (Logout)
                </a>
              </div>
            </div>
          <?php else: ?>
            <!-- Tombol Sign In / Sign Up -->
            <div class="flex items-center gap-1 sm:gap-1.5">
              <button data-open-auth="login" class="interactive-btn px-2.5 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold transition">
                Masuk
              </button>
              <button data-open-auth="register" class="interactive-btn px-3 py-1.5 text-xs font-bold rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white shadow-xs transition">
                Daftar
              </button>
            </div>
          <?php endif; ?>

          <!-- Logo Unsulbar (Sisi Kanan) -->
          <div class="hidden lg:flex items-center pl-2 border-l border-slate-200 dark:border-slate-700">
            <img src="assets/images/branding/Unsulbar-Logo.png" alt="Logo Unsulbar" class="w-8 h-8 rounded-full shadow-md object-cover hover:scale-105 transition-transform" title="Universitas Sulawesi Barat">
          </div>

        </div>

      </div>
    </div>
  </header>
