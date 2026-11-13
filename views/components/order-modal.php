<!-- views/components/order-modal.php -->
<div id="order-success-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-950/80 backdrop-blur-sm p-3 sm:p-6 transition-all duration-300">
  <div class="min-h-full flex items-center justify-center p-0 text-center sm:p-0">
    <div class="relative w-full max-w-xl bg-white dark:bg-slate-800 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden my-4 sm:my-8 text-left animate-in fade-in zoom-in-95 duration-200">
      
      <!-- Header Modal Sukses & Tombol Close -->
      <div class="bg-gradient-to-r from-emerald-700 via-emerald-800 to-emerald-900 p-6 text-white text-center relative">
        <!-- Tombol Close Modal Struk (Pojok Kanan Atas) -->
        <button type="button" onclick="Cart.closeOrderModal()" class="absolute top-4 right-4 text-white/80 hover:text-white p-2 rounded-full hover:bg-white/20 transition focus:outline-none z-10" title="Tutup Modal Struk" aria-label="Tutup">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/20 rounded-2xl mx-auto flex items-center justify-center mb-2.5 shadow-inner">
          <svg class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
          </svg>
        </div>
        <h3 class="text-xl sm:text-2xl font-black tracking-tight">Pesanan Berhasil Dicatat!</h3>
        <p class="text-xs text-emerald-200 mt-1 max-w-md mx-auto">Data transaksi Anda telah tersimpan secara resmi di sistem database Aksara Unsulbar.</p>
      </div>

      <div class="p-4 sm:p-6 space-y-5">
        
        <!-- Ringkasan Info Order & Rincian Pembayaran (STRUK RESMI AKSARA UNSULBAR) -->
        <div id="modal-invoice-printable" class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700/80 space-y-3 text-xs sm:text-sm">
          
          <!-- Header Struk -->
          <div class="text-center pb-3 border-b border-slate-200 dark:border-slate-700">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-unsulbar-maroon/10 text-unsulbar-maroon dark:bg-unsulbar-gold/10 dark:text-unsulbar-gold mb-1">
              STRUK TRANSAKSI RESMI
            </span>
            <h4 class="font-black text-sm text-slate-900 dark:text-white uppercase tracking-wider">Aksara Mahasiswa Berdampak</h4>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Sentra Desa Papandangan, Kab. Polewali Mandar • Unsulbar</p>
          </div>

          <!-- Data Pemesan & Rute -->
          <div class="space-y-1.5 pb-3 border-b border-slate-200 dark:border-slate-700 text-xs">
            <div class="flex justify-between items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">Kode Transaksi:</span>
              <span id="modal-order-code" class="font-mono font-extrabold text-unsulbar-maroon dark:text-unsulbar-gold text-right">#AKS-...</span>
            </div>
            <div class="flex justify-between items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">Waktu Pemesanan:</span>
              <span id="modal-order-date" class="font-semibold text-slate-800 dark:text-slate-200 text-right">-</span>
            </div>
            <div class="flex justify-between items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">Nama Penerima:</span>
              <span id="modal-order-customer" class="font-bold text-slate-800 dark:text-slate-200 text-right">-</span>
            </div>
            <div class="flex justify-between items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">No. WhatsApp:</span>
              <span id="modal-order-phone" class="font-mono font-bold text-slate-800 dark:text-slate-200 text-right">-</span>
            </div>
            <div class="flex justify-between items-start gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">Alamat Kirim:</span>
              <span id="modal-order-address" class="font-semibold text-slate-700 dark:text-slate-300 text-right max-w-[220px] sm:max-w-xs break-words leading-relaxed">-</span>
            </div>
            <div class="flex justify-between items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">Metode Pembayaran:</span>
              <span id="modal-order-payment" class="font-bold text-slate-800 dark:text-slate-200 text-right">COD</span>
            </div>
            <div class="flex justify-between items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 shrink-0">Navigasi Rute Kurir:</span>
              <a id="modal-order-maps" href="#" target="_blank" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1 text-right">
                <span>🗺️ Buka Rute Google Maps →</span>
              </a>
            </div>
          </div>

          <!-- Rincian Item Produk di Struk (Scrollable & Tidak Terpotong) -->
          <div class="pb-3 border-b border-slate-200 dark:border-slate-700">
            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-1.5">
              Daftar Produk Pesanan:
            </span>
            <div id="modal-order-items" class="space-y-1.5 max-h-56 overflow-y-auto pr-1.5 custom-scrollbar">
              <!-- Otomatis diisi oleh JS -->
            </div>
          </div>

          <!-- Rincian Biaya Transparan: Subtotal + Ongkir + Penanganan = Total -->
          <div class="space-y-2 text-xs sm:text-sm">
            <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
              <span>Subtotal Produk (<span id="modal-order-qty-count">0</span> item)</span>
              <span id="modal-order-subtotal" class="font-bold text-slate-900 dark:text-white">Rp 0</span>
            </div>
            
            <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 gap-2">
              <div>
                <span>Biaya Pengantaran Kurir</span>
                <span id="modal-order-distance-badge" class="text-[10px] text-slate-400 block sm:inline sm:ml-1">(5 km pertama Rp 15.000, +Rp 2.000/km)</span>
              </div>
              <span id="modal-order-shipping" class="font-bold text-slate-900 dark:text-white shrink-0">Rp 15.000</span>
            </div>

            <div class="flex justify-between items-center text-slate-600 dark:text-slate-300 gap-2">
              <div>
                <span>Biaya Penanganan</span>
                <span id="modal-order-handling-rate" class="text-[10px] text-slate-400 block sm:inline sm:ml-1">(Rp 2.000 / pcs)</span>
              </div>
              <span id="modal-order-service" class="font-bold text-slate-900 dark:text-white shrink-0">Rp 0</span>
            </div>

            <div class="flex justify-between items-center pt-2.5 border-t border-slate-200 dark:border-slate-700">
              <div>
                <span class="font-black text-slate-800 dark:text-slate-100 text-sm block">TOTAL AKHIR</span>
                <span class="text-[10px] text-slate-400">Termasuk ongkir kurir & penanganan</span>
              </div>
              <span id="modal-order-total" class="text-xl font-black text-unsulbar-maroon dark:text-unsulbar-gold">Rp 0</span>
            </div>
          </div>

        </div>

        <!-- SECTION RINCIAN BUKTI PEMBAYARAN -->
        <div id="modal-proof-section" class="p-4 rounded-2xl bg-amber-50/60 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800 space-y-3">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="text-base">🧾</span>
              <span class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-200">Status Bukti Pembayaran:</span>
            </div>
            <span id="modal-proof-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200">
              Menunggu
            </span>
          </div>

          <!-- Jika bukti sudah diunggah -->
          <div id="modal-proof-preview-wrap" class="hidden flex items-center gap-3 p-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <img id="modal-proof-preview-img" src="" alt="Bukti Transfer" class="w-14 h-14 object-cover rounded-lg border border-slate-200">
            <div class="flex-1 text-xs">
              <span class="font-bold text-slate-800 dark:text-slate-100 block">Bukti Pembayaran Terlampir</span>
              <a id="modal-proof-link" href="#" target="_blank" class="text-emerald-600 dark:text-emerald-400 hover:underline font-semibold text-[11px]">
                Lihat Bukti Ukuran Penuh →
              </a>
            </div>
          </div>

          <!-- Form Cepat Upload Bukti Bayar jika belum diunggah -->
          <div id="modal-proof-upload-form" class="space-y-2">
            <p class="text-[11px] text-slate-600 dark:text-slate-300">
              Sudah melakukan transfer? Unggah struk / bukti pembayaran Anda di sini:
            </p>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
              <input type="file" id="modal-proof-file-input" accept="image/*,application/pdf" class="text-xs file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-amber-100 file:text-amber-900 hover:file:bg-amber-200 dark:file:bg-slate-700 dark:file:text-white flex-1">
              <button type="button" onclick="Cart.uploadModalProof()" class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs whitespace-nowrap transition shadow-xs">
                Unggah Bukti
              </button>
            </div>
          </div>
        </div>

        <!-- QRIS QR Code Section (jika metode bayar QRIS) -->
        <div id="modal-qris-container" class="hidden text-center p-4 bg-white dark:bg-slate-900/60 rounded-2xl border border-slate-200 dark:border-slate-700">
          <p class="text-xs font-bold uppercase tracking-wider text-slate-800 dark:text-slate-200 mb-2">Scan QRIS Dinamis Aksara Unsulbar</p>
          <img id="modal-qris-img" src="" alt="QRIS QR Code" class="w-48 h-48 mx-auto bg-white p-2 rounded-xl border border-slate-200 shadow-inner">
          <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2">Dapat dibayar menggunakan BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, LinkAja, atau ShopeePay.</p>
        </div>

        <!-- Pemicu WhatsApp Kurir Otomatis -->
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 text-center">
          <p class="text-xs text-emerald-800 dark:text-emerald-200 font-medium mb-3">
            Sistem telah menyusun rincian pesanan dan titik koordinat Google Maps Anda. Tekan tombol di bawah untuk langsung terhubung dengan kurir mahasiswa:
          </p>
          <a id="modal-btn-wa" href="#" target="_blank" class="interactive-btn w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 transition">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.529 1.839.78 2.791.78 3.181 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.768-5.766zm9.969 5.766c0 5.502-4.478 9.98-9.969 9.98-1.74 0-3.371-.453-4.801-1.246l-5.23 1.369 1.395-5.097c-.896-1.488-1.41-3.228-1.41-5.006 0-5.502 4.478-9.98 9.969-9.98 5.49 0 10.046 4.478 10.046 9.98z"/></svg>
            Kirim Data & Rute Maps ke WhatsApp Kurir
          </a>
        </div>

        <!-- Tombol Aksi: Cetak Struk, Lacak, Belanja Lagi & Tombol Tutup -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2">
          <button type="button" onclick="Cart.printOrderReceipt()" class="interactive-btn py-2.5 px-3 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-200 font-bold text-xs text-center hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center justify-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Struk
          </button>
          <a id="modal-btn-track" href="?page=tracking" class="interactive-btn py-2.5 px-3 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-800 dark:text-slate-100 font-bold text-xs text-center hover:bg-slate-200 transition flex items-center justify-center">
            Lacak Status
          </a>
          <a href="?page=catalog" class="interactive-btn py-2.5 px-3 rounded-xl bg-slate-900 dark:bg-slate-600 text-white font-bold text-xs text-center hover:bg-slate-800 transition flex items-center justify-center">
            Belanja Lagi
          </a>
          <button type="button" onclick="Cart.closeOrderModal()" class="interactive-btn py-2.5 px-3 rounded-xl bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/40 dark:hover:bg-red-950/70 dark:text-red-300 font-bold text-xs text-center transition flex items-center justify-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Tutup
          </button>
        </div>

      </div>

    </div>
  </div>
</div>
