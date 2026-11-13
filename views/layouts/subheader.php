<?php
// views/layouts/subheader.php
use App\Helpers\Security;

$activeTab = $activeTab ?? 'dashboard';
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = ($currentUser['role'] ?? '') === 'admin';

$navLinks = [
    'dashboard' => ['label' => 'Dashboard', 'url' => '?page=dashboard', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>'],
    'catalog'   => ['label' => 'Pemesanan', 'url' => '?page=catalog', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>'],
    'cart'      => ['label' => 'Keranjang', 'url' => '?page=cart', 'badge' => true, 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>'],
    'tracking'  => ['label' => 'Status Pesanan', 'url' => '?page=tracking', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>'],
    'contact'   => ['label' => 'Kontak', 'url' => '?page=contact', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>'],
];

if ($isAdmin) {
    // Menu Baru: Monitoring Produk untuk Admin
    $navLinks['admin_products'] = [
        'label' => 'Monitoring Produk', 
        'url' => '?page=admin-products', 
        'badge_admin' => 'Stok',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>'
    ];
    $navLinks['admin'] = [
        'label' => 'Kelola Pesanan', 
        'url' => '?page=admin-orders', 
        'badge_admin' => 'Order',
        'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>'
    ];
}
?>

<!-- SUB-HEADER NAVIGATION BAR -->
<nav class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-700/60 sticky top-20 sm:top-24 z-30 shadow-xs transition-colors">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between sm:justify-start gap-1 sm:gap-2 overflow-x-auto py-2.5 no-scrollbar">
      
      <?php foreach ($navLinks as $key => $link): 
        $isActive = ($activeTab === $key);
      ?>
        <a href="<?= $link['url'] ?>" 
           class="interactive-btn relative px-3 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-1.5 sm:gap-2 whitespace-nowrap transition-all <?= $isActive 
              ? 'bg-unsulbar-maroon text-white shadow-md shadow-unsulbar-maroon/20' 
              : 'text-slate-600 dark:text-slate-300 hover:text-unsulbar-maroon dark:hover:text-unsulbar-gold hover:bg-slate-100 dark:hover:bg-slate-700/60' ?>">
          
          <svg class="w-4 h-4 <?= $isActive ? 'text-unsulbar-gold' : 'text-slate-400 dark:text-slate-500' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <?= $link['icon'] ?>
          </svg>

          <span><?= $link['label'] ?></span>

          <?php if (!empty($link['badge'])): ?>
            <span class="cart-badge-count hidden ml-0.5 px-2 py-0.5 text-[10px] font-extrabold rounded-full bg-unsulbar-gold text-slate-900 shadow-xs">
              0
            </span>
          <?php endif; ?>

          <?php if (!empty($link['badge_admin'])): ?>
            <span class="ml-0.5 px-1.5 py-0.5 text-[9px] font-black uppercase rounded bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300">
              <?= $link['badge_admin'] ?>
            </span>
          <?php endif; ?>

          <?php if ($isActive): ?>
            <span class="absolute bottom-0 left-1/2 -translate-x-1/2 w-6 h-0.5 bg-unsulbar-gold rounded-full"></span>
          <?php endif; ?>
        </a>
      <?php endforeach; ?>

    </div>
  </div>
</nav>
