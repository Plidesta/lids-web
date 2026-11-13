/**
 * public/assets/js/app.js
 * Core Controller: Theme Switcher, Toast Engine, Auth Modal, Delivery Address Manager & Geolocation GPS
 */

const App = {
  csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',

  init() {
    this.initTheme();
    this.initUserDropdown();
    this.initAuthModal();
    this.initAddressModal();
    this.initTrackingProofUpload();
    this.updateCartBadge();
  },

  /* ----------------- USER PROFILE DROPDOWN (Mobile & Desktop) ----------------- */
  initUserDropdown() {
    const btn = document.getElementById('user-menu-btn');
    const dropdown = document.getElementById('user-menu-dropdown');
    if (btn && dropdown) {
      btn.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('hidden');
      });
      document.addEventListener('click', (e) => {
        if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
          dropdown.classList.add('hidden');
        }
      });
    }
  },

  /* ----------------- THEME SWITCHER (Dark/Light Mode) ----------------- */
  initTheme() {
    const themeBtn = document.getElementById('theme-toggle');
    const htmlEl = document.documentElement;

    const savedTheme = localStorage.getItem('aksara_theme') || 
      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');

    if (savedTheme === 'dark') {
      htmlEl.classList.add('dark');
    } else {
      htmlEl.classList.remove('dark');
    }
    this.updateThemeIcon(savedTheme === 'dark');

    if (themeBtn) {
      themeBtn.addEventListener('click', () => {
        const isDark = htmlEl.classList.toggle('dark');
        localStorage.setItem('aksara_theme', isDark ? 'dark' : 'light');
        this.updateThemeIcon(isDark);
      });
    }
  },

  updateThemeIcon(isDark) {
    const sunIcon = document.getElementById('theme-sun');
    const moonIcon = document.getElementById('theme-moon');
    if (sunIcon && moonIcon) {
      if (isDark) {
        sunIcon.classList.remove('hidden');
        moonIcon.classList.add('hidden');
      } else {
        sunIcon.classList.add('hidden');
        moonIcon.classList.remove('hidden');
      }
    }
  },

  /* ----------------- TOAST POP-UP ANIMATION ENGINE ----------------- */
  showToast(message, type = 'success', title = '') {
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none px-4';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `flex items-start gap-3 p-4 rounded-2xl shadow-2xl border backdrop-blur-md max-w-sm w-full pointer-events-auto transition-all animate-in fade-in slide-in-from-bottom-3 duration-200 ${
      type === 'success' 
        ? 'bg-emerald-900/95 text-emerald-50 border-emerald-600' 
        : type === 'error'
        ? 'bg-red-900/95 text-red-50 border-red-600'
        : 'bg-slate-900/95 text-slate-50 border-slate-700'
    }`;

    const iconMap = {
      success: `<svg class="w-5 h-5 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`,
      error: `<svg class="w-5 h-5 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>`,
      info: `<svg class="w-5 h-5 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`
    };

    toast.innerHTML = `
      ${iconMap[type] || iconMap.info}
      <div class="flex-1 text-xs">
        ${title ? `<p class="font-extrabold text-white text-sm mb-0.5">${title}</p>` : ''}
        <p class="leading-relaxed font-medium">${message}</p>
      </div>
      <button onclick="this.parentElement.remove()" class="text-white/60 hover:text-white transition p-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 300);
    }, 4500);
  },

  /* ----------------- COPY TO CLIPBOARD HELPER ----------------- */
  copyToClipboard(text, successMsg = 'Teks berhasil disalin!') {
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(() => {
        this.showToast(successMsg, 'success', 'Tersalin');
      }).catch(() => {
        this.fallbackCopy(text, successMsg);
      });
    } else {
      this.fallbackCopy(text, successMsg);
    }
  },

  fallbackCopy(text, successMsg) {
    const input = document.createElement('textarea');
    input.value = text;
    document.body.appendChild(input);
    input.select();
    try {
      document.execCommand('copy');
      this.showToast(successMsg, 'success', 'Tersalin');
    } catch (e) {
      this.showToast('Gagal menyalin otomatis.', 'error');
    }
    document.body.removeChild(input);
  },

  /* ----------------- AUTH MODAL CONTROLLER ----------------- */
  closeAuthModal() {
    const modal = document.getElementById('auth-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  },

  initAuthModal() {
    const modal = document.getElementById('auth-modal');
    if (!modal) return;

    document.querySelectorAll('[data-open-auth]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const mode = btn.getAttribute('data-open-auth') || 'login';
        this.switchAuthTab(mode);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });
    });

    document.querySelectorAll('[data-close-auth]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.closeAuthModal();
      });
    });

    // Backdrop click to close auth modal
    modal.addEventListener('click', (e) => {
      if (e.target === modal || e.target.classList.contains('min-h-full')) {
        this.closeAuthModal();
      }
    });

    // Global Escape Key to close all active popups
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        this.closeAuthModal();
        this.closeAddressModal();
        if (window.Cart && typeof window.Cart.closeOrderModal === 'function') {
          window.Cart.closeOrderModal();
        }
      }
    });

    const tabLogin = document.getElementById('tab-btn-login');
    const tabRegister = document.getElementById('tab-btn-register');
    if (tabLogin && tabRegister) {
      tabLogin.addEventListener('click', () => this.switchAuthTab('login'));
      tabRegister.addEventListener('click', () => this.switchAuthTab('register'));
    }

    const formLogin = document.getElementById('form-login');
    if (formLogin) {
      formLogin.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = formLogin.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Memverifikasi...';

        try {
          const formData = new FormData(formLogin);
          formData.append('csrf_token', this.csrfToken);

          const res = await fetch('?action=auth/login', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          const data = await res.json();

          if (data.status === 'success') {
            this.showToast(data.message, 'success', 'Login Berhasil');
            setTimeout(() => window.location.reload(), 900);
          } else {
            this.showToast(data.message, 'error', 'Gagal Masuk');
          }
        } catch (err) {
          this.showToast('Gagal menghubungi server.', 'error');
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Masuk Sekarang';
        }
      });
    }

    const formRegister = document.getElementById('form-register');
    if (formRegister) {
      formRegister.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = formRegister.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mendaftarkan...';

        try {
          const formData = new FormData(formRegister);
          formData.append('csrf_token', this.csrfToken);

          const res = await fetch('?action=auth/register', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
          });
          const data = await res.json();

          if (data.status === 'success') {
            this.showToast(data.message, 'success', 'Akun Terdaftar');
            setTimeout(() => window.location.reload(), 900);
          } else {
            this.showToast(data.message, 'error', 'Pendaftaran Gagal');
          }
        } catch (err) {
          this.showToast('Gagal menghubungi server.', 'error');
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Daftar Akun Baru';
        }
      });
    }
  },

  switchAuthTab(mode) {
    const paneLogin = document.getElementById('auth-pane-login');
    const paneRegister = document.getElementById('auth-pane-register');
    const tabLogin = document.getElementById('tab-btn-login');
    const tabRegister = document.getElementById('tab-btn-register');

    if (!paneLogin || !paneRegister) return;

    if (mode === 'register') {
      paneLogin.classList.add('hidden');
      paneRegister.classList.remove('hidden');
      tabRegister?.classList.add('border-unsulbar-gold', 'text-unsulbar-gold');
      tabRegister?.classList.remove('border-transparent', 'text-slate-400');
      tabLogin?.classList.remove('border-unsulbar-gold', 'text-unsulbar-gold');
      tabLogin?.classList.add('border-transparent', 'text-slate-400');
    } else {
      paneLogin.classList.remove('hidden');
      paneRegister.classList.add('hidden');
      tabLogin?.classList.add('border-unsulbar-gold', 'text-unsulbar-gold');
      tabLogin?.classList.remove('border-transparent', 'text-slate-400');
      tabRegister?.classList.remove('border-unsulbar-gold', 'text-unsulbar-gold');
      tabRegister?.classList.add('border-transparent', 'text-slate-400');
    }
  },

  /* ----------------- ALAMAT PENGANTARAN & GPS MAPS (ALA E-COMMERCE) ----------------- */
  openAddressModal() {
    const modal = document.getElementById('address-modal');
    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');

      // Sinkronkan input modal dengan data terkini di checkout/cart jika ada
      const checkName = document.getElementById('checkout-customer-name')?.value;
      const checkPhone = document.getElementById('checkout-customer-phone')?.value;
      const checkAddr = document.getElementById('checkout-address-input')?.value;
      const checkMaps = document.getElementById('checkout-maps-input')?.value;
      const checkNotes = document.getElementById('checkout-notes-input')?.value;

      if (checkName && !document.getElementById('modal-input-recipient')?.value) {
        document.getElementById('modal-input-recipient').value = checkName;
      }
      if (checkPhone && !document.getElementById('modal-input-phone')?.value) {
        document.getElementById('modal-input-phone').value = checkPhone;
      }
      if (checkAddr && !document.getElementById('modal-input-address')?.value) {
        document.getElementById('modal-input-address').value = checkAddr;
      }
      if (checkMaps && !document.getElementById('modal-input-maps')?.value) {
        document.getElementById('modal-input-maps').value = checkMaps;
      }
      if (checkNotes && !document.getElementById('modal-input-notes')?.value) {
        document.getElementById('modal-input-notes').value = checkNotes;
      }
    }
  },

  closeAddressModal() {
    const modal = document.getElementById('address-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  },

  selectAddressLabel(label) {
    const input = document.getElementById('modal-input-label');
    if (input) input.value = label;

    document.querySelectorAll('#address-label-chips button').forEach(btn => {
      const chipKey = btn.getAttribute('data-label-chip');
      if (chipKey === label) {
        btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold border transition border-unsulbar-maroon bg-unsulbar-maroon/10 text-unsulbar-maroon dark:border-unsulbar-gold dark:text-unsulbar-gold dark:bg-unsulbar-gold/10';
      } else {
        btn.className = 'px-3 py-1.5 rounded-xl text-xs font-bold border transition border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/60';
      }
    });
  },

  applyPresetAddress(label, address, city, benchmark, mapsUrl, lat, lng, distance = null) {
    this.selectAddressLabel(label);

    const addrInput = document.getElementById('modal-input-address');
    const cityInput = document.getElementById('modal-input-city');
    const benchInput = document.getElementById('modal-input-benchmark');
    const mapsInput = document.getElementById('modal-input-maps');
    const latInput = document.getElementById('modal-input-lat');
    const lngInput = document.getElementById('modal-input-lng');
    const distInput = document.getElementById('modal-input-distance');
    const testMapsBtn = document.getElementById('btn-modal-test-maps');

    let distVal = distance;
    if ((distVal === null || distVal === undefined) && lat && lng) {
      distVal = this.calculateDistance(-3.4116417, 119.342125, parseFloat(lat), parseFloat(lng));
    }
    if (!distVal) distVal = 5.0;

    if (addrInput) addrInput.value = address;
    if (cityInput) cityInput.value = city;
    if (benchInput) benchInput.value = benchmark;
    if (mapsInput) mapsInput.value = mapsUrl;
    if (latInput) latInput.value = lat;
    if (lngInput) lngInput.value = lng;
    if (distInput) distInput.value = distVal;
    if (testMapsBtn) testMapsBtn.href = mapsUrl || 'https://maps.google.com';

    this.showToast(`Preset lokasi "${label}" (${distVal} km) diterapkan. Anda masih dapat menyesuaikannya!`, 'info', 'Lokasi Dipilih');
  },

  syncAddressDom(data) {
    if (!data) return;

    const fullAddrWithCity = (data.address || '') + (data.city && !data.address.includes(data.city) ? ', ' + data.city : '');
    const distanceVal = data.distance ? Number(data.distance).toFixed(1) : '5.0';

    // 1. SINKRONISASI KE HALAMAN KERANJANG (CART)
    const cartLabel = document.getElementById('cart-display-label');
    const cartRecipient = document.getElementById('cart-display-recipient');
    const cartPhone = document.getElementById('cart-display-phone');
    const cartAddr = document.getElementById('cart-display-address');
    const cartBench = document.getElementById('cart-display-benchmark');
    const cartBenchWrap = document.getElementById('cart-display-benchmark-wrap');
    const cartNotes = document.getElementById('cart-display-notes');
    const cartNotesWrap = document.getElementById('cart-display-notes-wrap');
    const cartMaps = document.getElementById('cart-display-maps');
    const cartDist = document.getElementById('cart-display-distance');

    if (cartLabel) cartLabel.textContent = data.label || 'Alamat Utama';
    if (cartRecipient) cartRecipient.textContent = data.recipient_name || 'Konsumen Pembeli';
    if (cartPhone) cartPhone.textContent = data.phone || '';
    if (cartAddr) cartAddr.textContent = fullAddrWithCity;
    if (cartBench) cartBench.textContent = data.benchmark || '';
    if (cartBenchWrap) {
      if (data.benchmark) cartBenchWrap.classList.remove('hidden');
      else cartBenchWrap.classList.add('hidden');
    }
    if (cartNotes) cartNotes.textContent = data.notes || '';
    if (cartNotesWrap) {
      if (data.notes) cartNotesWrap.classList.remove('hidden');
      else cartNotesWrap.classList.add('hidden');
    }
    if (cartDist) cartDist.textContent = distanceVal;
    if (cartMaps) {
      if (data.maps_url) {
        cartMaps.href = data.maps_url;
        cartMaps.classList.remove('hidden');
      } else {
        cartMaps.classList.add('hidden');
      }
    }

    // 2. SINKRONISASI KE KARTU CHECKOUT (CHECKOUT PREVIEW CARD)
    const checkCardLabel = document.getElementById('checkout-card-label');
    const checkCardRecipient = document.getElementById('checkout-card-recipient');
    const checkCardPhone = document.getElementById('checkout-card-phone');
    const checkCardAddr = document.getElementById('checkout-card-address');
    const checkCardBench = document.getElementById('checkout-card-benchmark');
    const checkCardBenchWrap = document.getElementById('checkout-card-benchmark-wrap');
    const checkCardNotes = document.getElementById('checkout-card-notes');
    const checkCardNotesWrap = document.getElementById('checkout-card-notes-wrap');
    const checkCardMaps = document.getElementById('checkout-card-maps');

    if (checkCardLabel) checkCardLabel.textContent = data.label || 'Alamat Utama';
    if (checkCardRecipient) checkCardRecipient.textContent = data.recipient_name || 'Penerima Pesanan';
    if (checkCardPhone) checkCardPhone.textContent = data.phone || '';
    if (checkCardAddr) checkCardAddr.textContent = fullAddrWithCity;
    if (checkCardBench) checkCardBench.textContent = data.benchmark || '';
    if (checkCardBenchWrap) {
      if (data.benchmark) checkCardBenchWrap.classList.remove('hidden');
      else checkCardBenchWrap.classList.add('hidden');
    }
    if (checkCardNotes) checkCardNotes.textContent = data.notes || '';
    if (checkCardNotesWrap) {
      if (data.notes) checkCardNotesWrap.classList.remove('hidden');
      else checkCardNotesWrap.classList.add('hidden');
    }
    if (checkCardMaps) {
      if (data.maps_url) {
        checkCardMaps.href = data.maps_url;
        checkCardMaps.classList.remove('hidden');
      } else {
        checkCardMaps.classList.add('hidden');
      }
    }

    const checkCardDist = document.getElementById('checkout-card-distance');
    if (checkCardDist) checkCardDist.textContent = distanceVal;

    // 3. SINKRONISASI KE FORMULIR CHECKOUT INPUT
    const formName = document.getElementById('checkout-customer-name');
    const formPhone = document.getElementById('checkout-customer-phone');
    const formAddr = document.getElementById('checkout-address-input');
    const formMaps = document.getElementById('checkout-maps-input');
    const formNotes = document.getElementById('checkout-notes-input');
    const formLabel = document.getElementById('checkout-input-label');
    const formCity = document.getElementById('checkout-input-city');
    const formBench = document.getElementById('checkout-input-benchmark');
    const formLat = document.getElementById('checkout-lat');
    const formLng = document.getElementById('checkout-lng');
    const formDist = document.getElementById('checkout-distance');

    if (formName && data.recipient_name) formName.value = data.recipient_name;
    if (formPhone && data.phone) formPhone.value = data.phone;
    if (formAddr && data.address) formAddr.value = data.address;
    if (formMaps && data.maps_url) formMaps.value = data.maps_url;
    if (formNotes && data.notes) formNotes.value = data.notes;
    if (formLabel && data.label) formLabel.value = data.label;
    if (formCity && data.city) formCity.value = data.city;
    if (formBench && data.benchmark) formBench.value = data.benchmark;
    if (formLat && data.lat) formLat.value = data.lat;
    if (formLng && data.lng) formLng.value = data.lng;
    if (formDist && data.distance) formDist.value = distanceVal;

    // 4. Update Header jika ada
    const headerLabel = document.getElementById('header-address-label');
    if (headerLabel) headerLabel.textContent = data.address;

    // 5. Update kalkulasi biaya bila sedang di halaman Cart atau Checkout
    if (typeof Cart !== 'undefined' && Cart.refreshSummaryAfterAddressSync) {
      Cart.refreshSummaryAfterAddressSync(parseFloat(distanceVal));
    }
  },

  initAddressModal() {
    const form = document.getElementById('form-address-editor');
    const oldForm = document.getElementById('form-header-address');
    const activeForm = form || oldForm;

    const addrModal = document.getElementById('address-modal');
    if (addrModal) {
      addrModal.addEventListener('click', (e) => {
        if (e.target === addrModal || e.target.classList.contains('min-h-full')) {
          this.closeAddressModal();
        }
      });
    }

    // Deteksi GPS di Halaman Checkout
    const checkoutGpsBtn = document.getElementById('btn-checkout-gps');
    if (checkoutGpsBtn) {
      checkoutGpsBtn.addEventListener('click', () => {
        this.detectUserGps('checkout-maps-input', 'checkout-address-input', 'checkout-lat', 'checkout-lng');
      });
    }

    // Live sync ketika konsumen mengetik langsung di formulir checkout
    const checkNameInput = document.getElementById('checkout-customer-name');
    const checkPhoneInput = document.getElementById('checkout-customer-phone');
    const checkAddrInput = document.getElementById('checkout-address-input');

    if (checkNameInput) {
      checkNameInput.addEventListener('input', (e) => {
        const el = document.getElementById('checkout-card-recipient');
        if (el) el.textContent = e.target.value.trim() || 'Penerima Pesanan';
      });
    }
    if (checkPhoneInput) {
      checkPhoneInput.addEventListener('input', (e) => {
        const el = document.getElementById('checkout-card-phone');
        if (el) el.textContent = e.target.value.trim() || 'No. Telepon / WA';
      });
    }
    if (checkAddrInput) {
      checkAddrInput.addEventListener('input', (e) => {
        const el = document.getElementById('checkout-card-address');
        if (el) el.textContent = e.target.value.trim() || 'Alamat Pengantaran';
      });
    }

    // Submit Alamat dari Modal (Cart & Checkout Shared Modal)
    if (activeForm) {
      activeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = document.getElementById('btn-save-address-modal');
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            Menyimpan...
          `;
        }

        const formData = new FormData(activeForm);
        const payload = Object.fromEntries(formData.entries());

        try {
          const res = await fetch('?action=cart/save-address', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
          });
          const data = await res.json();

          if (data.status === 'success') {
            const savedAddress = data.data.address || payload;
            this.syncAddressDom(savedAddress);
            this.showToast('Alamat pengiriman berhasil disinkronkan!', 'success', 'Alamat Diperbarui');
            this.closeAddressModal();
          } else {
            this.showToast(data.message || 'Gagal menyimpan alamat.', 'error');
          }
        } catch (err) {
          this.showToast('Gagal menghubungi server.', 'error');
        } finally {
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span>✓ Simpan & Terapkan Alamat</span>`;
          }
        }
      });
    }
  },

  /* ----------------- RUMUS ONGKIR & JARAK GPS ----------------- */
  calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const roadDist = (R * c) * 1.2; // Faktor kontur jalan raya pesisir/berkelok Majene
    return Math.max(0.1, Math.round(roadDist * 10) / 10);
  },

  calculateOngkir(distanceKm) {
    const dist = parseFloat(distanceKm) || 5.0;
    if (dist <= 5.0) {
      return 15000;
    }
    const extraKm = Math.ceil(dist - 5.0);
    return 15000 + (extraKm * 2000);
  },

  calculateHandlingFee(totalItems) {
    const qty = parseInt(totalItems, 10) || 0;
    return qty * 2000;
  },

  detectUserGps(targetMapsInputId, targetAddressInputId = null, targetLatId = null, targetLngId = null) {
    if (!navigator.geolocation) {
      this.showToast('Browser Anda tidak mendukung deteksi lokasi GPS.', 'error');
      return;
    }

    this.showToast('Mendeteksi koordinat GPS perangkat Anda...', 'info');

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        const accuracy = Math.round(pos.coords.accuracy);

        const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;

        const mapsInput = document.getElementById(targetMapsInputId);
        if (mapsInput) mapsInput.value = mapsUrl;

        if (targetLatId) {
          const latInput = document.getElementById(targetLatId);
          if (latInput) latInput.value = lat;
        }
        if (targetLngId) {
          const lngInput = document.getElementById(targetLngId);
          if (lngInput) lngInput.value = lng;
        }

        // Hitung jarak dari Sentra Produksi Desa Papandangan, Kab. Polewali Mandar
        const distKm = this.calculateDistance(-3.4116417, 119.342125, lat, lng);

        const modalDistInput = document.getElementById('modal-input-distance');
        if (modalDistInput) modalDistInput.value = distKm;
        const checkDistInput = document.getElementById('checkout-distance');
        if (checkDistInput) checkDistInput.value = distKm;
        const checkCardDist = document.getElementById('checkout-card-distance');
        if (checkCardDist) checkCardDist.textContent = distKm.toFixed(1);
        const cartCardDist = document.getElementById('cart-display-distance');
        if (cartCardDist) cartCardDist.textContent = distKm.toFixed(1);

        if (targetAddressInputId) {
          const addrInput = document.getElementById(targetAddressInputId);
          if (addrInput && !addrInput.value.trim()) {
            addrInput.value = `Koordinat Titik GPS: ${lat.toFixed(6)}, ${lng.toFixed(6)} (Akurasi: ±${accuracy}m)`;
          }
        }

        // Sinkronkan juga preview kartu checkout jika sedang di checkout
        const checkCardMaps = document.getElementById('checkout-card-maps');
        if (checkCardMaps) {
          checkCardMaps.href = mapsUrl;
          checkCardMaps.classList.remove('hidden');
        }

        const testModalBtn = document.getElementById('btn-modal-test-maps');
        if (testModalBtn) {
          testModalBtn.href = mapsUrl;
        }

        if (typeof Cart !== 'undefined' && Cart.refreshSummaryAfterAddressSync) {
          Cart.refreshSummaryAfterAddressSync(distKm);
        }

        const calculatedFee = this.calculateOngkir(distKm);
        this.showToast(`GPS terdeteksi! Jarak: ${distKm} km (Ongkir: Rp ${calculatedFee.toLocaleString('id-ID')}). Akurasi ±${accuracy}m.`, 'success', 'GPS Berhasil');
      },
      (err) => {
        let msg = 'Izin lokasi tidak diberikan.';
        if (err.code === 2) msg = 'Posisi GPS tidak dapat ditentukan.';
        if (err.code === 3) msg = 'Waktu deteksi GPS habis.';
        this.showToast(msg + ' Silakan gunakan link Google Maps manual.', 'error');
      },
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
  },

  /* ----------------- FORM UPLOAD BUKTI TRACKING ----------------- */
  initTrackingProofUpload() {
    const form = document.getElementById('form-upload-tracking-proof');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.textContent = 'Mengunggah...';

      try {
        const formData = new FormData(form);
        const res = await fetch('?action=order/upload-proof', {
          method: 'POST',
          body: formData,
          headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.status === 'success') {
          this.showToast(data.message, 'success', 'Bukti Diunggah');
          setTimeout(() => window.location.reload(), 1000);
        } else {
          this.showToast(data.message || 'Gagal mengunggah bukti.', 'error');
        }
      } catch (e) {
        this.showToast('Gagal mengunggah bukti ke server.', 'error');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Unggah Sekarang';
      }
    });
  },

  async updateCartBadge() {
    try {
      const res = await fetch('?action=cart/get');
      const data = await res.json();
      if (data.status === 'success' && data.data) {
        const count = data.data.total_items || 0;
        document.querySelectorAll('.cart-badge-count').forEach(el => {
          el.textContent = count;
          if (count > 0) {
            el.classList.remove('hidden');
          } else {
            el.classList.add('hidden');
          }
        });
      }
    } catch (e) {
      // ignore
    }
  }
};

document.addEventListener('DOMContentLoaded', () => App.init());
