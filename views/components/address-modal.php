<?php
// views/components/address-modal.php
use App\Config\App;
use App\Helpers\Security;

$modalAddr = App::getDeliveryAddress();
$currentUser = $_SESSION['user'] ?? null;
?>

<!-- MODAL ALAMAT PENGANTARAN KONSUMEN ELEGAN ALA E-COMMERCE -->
<div id="address-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-sm p-3 sm:p-6 transition-all duration-200">
  <div class="min-h-full flex items-center justify-center p-0 text-center">
    <div class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden my-4 sm:my-8 text-left transform transition-all">
    
    <!-- Header Modal -->
    <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700 flex items-start justify-between bg-gradient-to-r from-amber-50/50 via-white to-emerald-50/50 dark:from-slate-800 dark:via-slate-800 dark:to-slate-800">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-2xl bg-amber-500/10 dark:bg-amber-400/20 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl shrink-0">
          📍
        </div>
        <div>
          <h3 class="text-base font-extrabold text-slate-900 dark:text-white leading-tight">
            Alamat Pengantaran Konsumen
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
            Sesuaikan detail penerima dan lokasi pengiriman pesanan Anda secara fleksibel.
          </p>
        </div>
      </div>
      <button type="button" onclick="App.closeAddressModal()" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>

    <!-- Form Alamat Pengantaran -->
    <form id="form-address-editor" class="p-6 space-y-4 max-h-[80vh] overflow-y-auto">
      
      <!-- Pilihan Label Alamat (Chips ala Tokopedia / Shopee) -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">
          Pilih Label Alamat:
        </label>
        <div class="flex flex-wrap gap-2" id="address-label-chips">
          <?php
          $labels = [
            'Rumah' => '🏠 Rumah',
            'Kantor' => '🏢 Kantor',
            'Kos / Asrama' => '🚪 Kos / Asrama',
            'Kampus' => '🎓 Kampus',
            'Lainnya' => '📍 Lainnya'
          ];
          $currentLabel = $modalAddr['label'] ?? 'Rumah';
          foreach ($labels as $key => $lbl):
            $isActive = (stripos($currentLabel, $key) !== false || $currentLabel === $key);
          ?>
            <button type="button" onclick="App.selectAddressLabel('<?= $key ?>')" data-label-chip="<?= $key ?>" class="px-3 py-1.5 rounded-xl text-xs font-bold border transition <?= $isActive ? 'border-unsulbar-maroon bg-unsulbar-maroon/10 text-unsulbar-maroon dark:border-unsulbar-gold dark:text-unsulbar-gold dark:bg-unsulbar-gold/10' : 'border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/60' ?>">
              <?= $lbl ?>
            </button>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="label" id="modal-input-label" value="<?= Security::e($currentLabel) ?>">
      </div>

      <!-- Baris 1: Nama Penerima & Nomor WhatsApp -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
            Nama Penerima *
          </label>
          <input type="text" name="recipient_name" id="modal-input-recipient" required value="<?= Security::e($modalAddr['recipient_name'] ?: ($currentUser['full_name'] ?? '')) ?>" placeholder="Contoh: Muh. Fadhil / Ibu Siti" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
        </div>

        <div>
          <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
            Nomor Telepon / WhatsApp *
          </label>
          <input type="tel" name="phone" id="modal-input-phone" required value="<?= Security::e($modalAddr['phone'] ?: ($currentUser['phone'] ?? '')) ?>" placeholder="081234567890 (Kurir akan kontak)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
        </div>
      </div>

      <!-- Baris 2: Kota / Kabupaten / Wilayah (Fleksibel Konsumen Luas) -->
      <div>
        <div class="flex items-center justify-between mb-1">
          <label class="text-xs font-bold text-slate-700 dark:text-slate-300">
            Kota / Kabupaten / Kecamatan *
          </label>
          <span class="text-[10px] text-slate-400">Tersedia pengiriman lokal & luar kota</span>
        </div>
        <input type="text" name="city" id="modal-input-city" required list="city-presets" value="<?= Security::e($modalAddr['city'] ?? 'Kabupaten Polewali Mandar') ?>" placeholder="Contoh: Kab. Majene, Kab. Polewali Mandar, Mamuju, Makassar..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
        <datalist id="city-presets">
          <option value="Kabupaten Majene, Sulawesi Barat">
          <option value="Kabupaten Polewali Mandar, Sulawesi Barat">
          <option value="Kabupaten Mamuju, Sulawesi Barat">
          <option value="Kabupaten Mamasa, Sulawesi Barat">
          <option value="Kabupaten Pasangkayu, Sulawesi Barat">
          <option value="Kota Makassar, Sulawesi Selatan">
        </datalist>
      </div>

      <!-- Baris 3: Alamat Lengkap Pengiriman -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
          Alamat Lengkap Pengantaran *
        </label>
        <textarea name="address" id="modal-input-address" required rows="2" placeholder="Nama Jalan, Nomor Rumah, RT/RW, Dusun / Lingkungan / Kelurahan..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500"><?= Security::e($modalAddr['address'] ?? '') ?></textarea>
      </div>

      <!-- Baris 4: Patokan Lokasi (Benchmark) -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
          Patokan Lokasi (Memudahkan Kurir Menemukan Rumah / Tempat)
        </label>
        <input type="text" name="benchmark" id="modal-input-benchmark" value="<?= Security::e($modalAddr['benchmark'] ?? '') ?>" placeholder="Misal: Depan toko kelontong Bu Ida, pagar besi hitam cat putih, lantai 2..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Baris 5: Deteksi GPS & Titik Peta Google Maps -->
      <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 space-y-2.5">
        <div class="flex items-center justify-between">
          <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
            <span>🗺️ Titik Lokasi Google Maps</span>
          </span>
          <button type="button" id="btn-modal-gps" onclick="App.detectUserGps('modal-input-maps', 'modal-input-address', 'modal-input-lat', 'modal-input-lng')" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] flex items-center gap-1 shadow-xs transition">
            <span>🎯 Deteksi GPS Saya</span>
          </button>
        </div>

        <div class="flex items-center gap-2">
          <input type="url" name="maps_url" id="modal-input-maps" value="<?= Security::e($modalAddr['maps_url'] ?? '') ?>" placeholder="https://www.google.com/maps/search/?api=1&query=..." class="w-full px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500 font-mono">
          <a id="btn-modal-test-maps" href="<?= Security::e($modalAddr['maps_url'] ?: 'https://maps.google.com') ?>" target="_blank" class="shrink-0 px-3 py-2 rounded-xl border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200" title="Buka di tab baru">
            Uji ↗
          </a>
        </div>
        <p class="text-[10px] text-slate-400">
          Tautan peta ini akan langsung otomatis dikirim ke kurir WhatsApp untuk rute turn-by-turn Google Maps.
        </p>

        <!-- Hidden Coordinates -->
        <input type="hidden" name="lat" id="modal-input-lat" value="<?= Security::e($modalAddr['lat'] ?? '') ?>">
        <input type="hidden" name="lng" id="modal-input-lng" value="<?= Security::e($modalAddr['lng'] ?? '') ?>">

        <!-- Input Jarak Pengantaran & Tarif Ongkir -->
        <div class="pt-2 border-t border-slate-200 dark:border-slate-600/60">
          <div class="flex items-center justify-between mb-1">
            <label class="text-xs font-bold text-slate-800 dark:text-slate-200">
              📏 Jarak Pengantaran dari Sentra Papandangan (KM)
            </label>
            <span class="text-[10px] font-bold text-amber-600 dark:text-amber-400">
              5 km pertama Rp 15.000, +Rp 2.000/km
            </span>
          </div>
          <div class="flex items-center gap-2">
            <input type="number" step="0.1" min="0.1" max="500" name="distance" id="modal-input-distance" value="<?= Security::e($modalAddr['distance'] ?? '5.0') ?>" placeholder="5.0" class="w-28 px-3 py-1.5 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-xs font-bold outline-none focus:ring-2 focus:ring-amber-500">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Kilometer (km)</span>
          </div>
          <p class="text-[10px] text-slate-400 mt-1">
            Otomatis terhitung dari patokan Desa Papandangan saat menekan <strong>🎯 Deteksi GPS</strong> atau memilih preset lokasi.
          </p>
        </div>
      </div>

      <!-- Baris 6: Catatan Pengiriman Kurir -->
      <div>
        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
          Catatan Tambahan untuk Kurir (Opsional)
        </label>
        <input type="text" name="notes" id="modal-input-notes" value="<?= Security::e($modalAddr['notes'] ?? '') ?>" placeholder="Misal: Titip di pos satpam, atau telepon dulu 10 menit sebelum tiba..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-900 dark:text-white text-xs outline-none focus:ring-2 focus:ring-amber-500">
      </div>

      <!-- Pilihan Cepat Preset Lokasi Populer (Kemudahan 1-Klik) -->
      <div class="pt-2 border-t border-slate-100 dark:border-slate-700/60">
        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">
          Pilihan Cepat Lokasi Pengantaran:
        </span>
        <div class="flex flex-wrap gap-1.5">
          <button type="button" onclick="App.applyPresetAddress('Sentra Papandangan', 'Desa Papandangan, Kec. Anreapi, Kab. Polewali Mandar', 'Kabupaten Polewali Mandar', 'Kumbung Produksi Jamur Tiram Mahasiswa Berdampak', 'https://www.google.com/maps/search/?api=1&query=-3.4116417,119.342125', '-3.4116417', '119.342125', 0.5)" class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
            🍄 Sentra Papandangan Polman (0.5 km)
          </button>
          <button type="button" onclick="App.applyPresetAddress('Kampus Unsulbar', 'Kampus Padhang-Padhang Unsulbar, Majene', 'Kabupaten Majene', 'Gedung Rektorat & Laboratorium Terpadu', 'https://www.google.com/maps/search/?api=1&query=-3.5284459,118.9654162', '-3.5284459', '118.9654162', 44.0)" class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
            🎓 Kampus Padhang-Padhang Majene (44 km)
          </button>
          <button type="button" onclick="App.applyPresetAddress('Lab Unsulbar', 'Gedung Laboratorium Terpadu Unsulbar, Majene', 'Kabupaten Majene', 'Ruang Inkubator Bisnis Jamur Tiram Lantai 1', 'https://www.google.com/maps/search/?api=1&query=-3.5284459,118.9654162', '-3.5284459', '118.9654162', 44.0)" class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-[11px] font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700">
            🔬 Lab Terpadu Unsulbar (44 km)
          </button>
        </div>
      </div>

      <!-- Checkbox Simpan Profil Akun -->
      <?php if ($currentUser): ?>
        <div class="pt-1">
          <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="save_to_profile" value="1" checked class="w-4 h-4 rounded text-unsulbar-maroon focus:ring-unsulbar-maroon dark:bg-slate-700">
            <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
              Jadikan alamat utama & simpan ke profil akun saya
            </span>
          </label>
        </div>
      <?php endif; ?>

      <!-- Tombol Aksi Batal & Simpan -->
      <div class="pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-end gap-2.5">
        <button type="button" onclick="App.closeAddressModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
          Batal
        </button>
        <button id="btn-save-address-modal" type="submit" class="interactive-btn px-5 py-2.5 rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white text-xs font-black shadow-md flex items-center gap-1.5 transition">
          <span>✓ Simpan & Terapkan Alamat</span>
        </button>
      </div>

    </form>

  </div>
  </div>
</div>
