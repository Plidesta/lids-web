<!-- views/components/auth-modal.php -->
<div id="auth-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/70 backdrop-blur-sm p-3 sm:p-6 transition-all duration-300">
  <div class="min-h-full flex items-center justify-center p-0 text-center">
    <div class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden my-4 sm:my-8 text-left animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Modal Header with Unsulbar Maroon Banner & Close Button -->
      <div class="bg-gradient-to-r from-unsulbar-maroon to-unsulbar-maroon-dark p-6 text-white text-center relative">
        <button data-close-auth onclick="App.closeAuthModal()" type="button" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/20 transition focus:outline-none z-10" title="Tutup Modal Login" aria-label="Tutup">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="w-12 h-12 bg-white/10 rounded-2xl mx-auto flex items-center justify-center mb-2 overflow-hidden border border-white/20">
          <img src="assets/images/branding/aksara-logo.png" alt="Aksara" class="w-10 h-10 object-cover rounded-xl">
        </div>
        <h3 class="text-xl font-extrabold tracking-tight">Aksara Berdampak Unsulbar</h3>
        <p class="text-xs text-amber-200 mt-1">Platform Pemesanan Produk Olahan Jamur Dan Pupuk Organik</p>
      </div>

      <!-- Tab Buttons (Masuk / Daftar) -->
      <div class="flex border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40">
        <button id="tab-btn-login" class="flex-1 py-3 text-xs sm:text-sm font-bold border-b-2 border-unsulbar-gold text-unsulbar-gold text-center transition">
          Masuk Akun
        </button>
        <button id="tab-btn-register" class="flex-1 py-3 text-xs sm:text-sm font-bold border-b-2 border-transparent text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-center transition">
          Daftar Baru
        </button>
      </div>

      <div class="p-6">
        
        <!-- PANE 1: FORM LOGIN -->
        <div id="auth-pane-login">
          <form id="form-login" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Alamat Email</label>
              <input type="email" name="email" required placeholder="nama@unsulbar.ac.id" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Kata Sandi</label>
              <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-400">Belum punya akun? <a href="#" onclick="App.switchAuthTab('register'); return false;" class="text-unsulbar-maroon dark:text-unsulbar-gold font-bold">Daftar disini</a></span>
            </div>
            <div class="flex gap-2 pt-2">
              <button type="submit" class="interactive-btn flex-1 py-3 rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-bold text-sm shadow-md transition">
                Masuk Sekarang
              </button>
              <button type="button" onclick="App.closeAuthModal()" class="interactive-btn px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs transition">
                Tutup
              </button>
            </div>
          </form>
        </div>

        <!-- PANE 2: FORM REGISTER -->
        <div id="auth-pane-register" class="hidden">
          <form id="form-register" class="space-y-3.5">
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nama Lengkap *</label>
              <input type="text" name="full_name" required placeholder="Contoh: Andi Siti Nurhaliza" class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Alamat Email *</label>
              <input type="email" name="email" required placeholder="mahasiswa@unsulbar.ac.id" class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Nomor WhatsApp Aktif *</label>
              <input type="tel" name="phone" required placeholder="081234567890" class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Alamat Pengantaran / Lokasi Kampus (Opsional)</label>
              <input type="text" name="address" placeholder="Misal: Asrama Putri B-12, atau Gedung Terpadu Lt. 2" class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div>
              <label class="block text-xs font-bold text-slate-600 dark:text-slate-300 mb-1">Kata Sandi (Min. 6 Karakter) *</label>
              <input type="password" name="password" required minlength="6" placeholder="••••••••" class="w-full px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-900 dark:text-white text-sm focus:ring-2 focus:ring-unsulbar-gold focus:border-transparent outline-none transition">
            </div>
            <div class="flex items-center justify-between text-xs">
              <span class="text-slate-400">Sudah punya akun? <a href="#" onclick="App.switchAuthTab('login'); return false;" class="text-unsulbar-maroon dark:text-unsulbar-gold font-bold">Masuk disini</a></span>
            </div>
            <div class="flex gap-2 pt-2">
              <button type="submit" class="interactive-btn flex-1 py-3 rounded-xl bg-organic-green hover:bg-organic-green-dark text-white font-bold text-sm shadow-md transition">
                Daftar Akun Baru
              </button>
              <button type="button" onclick="App.closeAuthModal()" class="interactive-btn px-4 py-3 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 font-bold text-xs transition">
                Tutup
              </button>
            </div>
          </form>
        </div>

      </div>

    </div>
  </div>
</div>
