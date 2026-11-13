/**
 * public/assets/js/cart.js
 * Cart Operations, Real-Time Quantity Sync, Payment Proof Uploader & WhatsApp Courier Dispatch
 */

const Cart = {
  csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
  currentOrderData: null,

  async add(productId, quantity = 1, isBuyNow = false) {
    try {
      const res = await fetch('?action=cart/add', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
      });
      const data = await res.json();

      if (data.status === 'success') {
        App.showToast(data.message, 'success', 'Keranjang Belanja');
        App.updateCartBadge();

        if (isBuyNow) {
          window.location.href = '?page=checkout';
        }
      } else {
        App.showToast(data.message, 'error', 'Gagal Menambah');
      }
    } catch (err) {
      App.showToast('Gagal menghubungi server.', 'error');
    }
  },

  async updateQuantity(productId, newQty) {
    try {
      const res = await fetch('?action=cart/update', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId, quantity: newQty })
      });
      const data = await res.json();

      if (data.status === 'success') {
        App.updateCartBadge();
        if (newQty <= 0) {
          const row = document.getElementById(`cart-row-${productId}`);
          if (row) row.remove();
        }
        this.updateCartDomSummary(data.summary);

        if (!data.summary.items || data.summary.items.length === 0) {
          window.location.reload();
        }
      } else {
        App.showToast(data.message, 'error');
      }
    } catch (err) {
      App.showToast('Gagal memperbarui item.', 'error');
    }
  },

  async remove(productId) {
    if (!confirm('Apakah Anda yakin ingin menghapus produk ini dari keranjang?')) return;

    try {
      const res = await fetch('?action=cart/remove', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ product_id: productId })
      });
      const data = await res.json();

      if (data.status === 'success') {
        App.showToast(data.message, 'info', 'Dihapus');
        App.updateCartBadge();
        const row = document.getElementById(`cart-row-${productId}`);
        if (row) row.remove();
        this.updateCartDomSummary(data.summary);

        if (!data.summary.items || data.summary.items.length === 0) {
          window.location.reload();
        }
      }
    } catch (err) {
      App.showToast('Gagal menghapus produk.', 'error');
    }
  },

  updateCartDomSummary(summary) {
    if (!summary) return;

    const subtotal = Number(summary.subtotal || 0);
    const shipping = Number(summary.shipping_fee || 0);
    const handling = Number(summary.service_fee || summary.handling_fee || 0);
    const total = Number(summary.total_amount || (subtotal + shipping + handling));
    const totalItems = parseInt(summary.total_items || 0, 10);
    const distanceVal = summary.distance ? Number(summary.distance).toFixed(1) : '5.0';

    // 1. Elemen di Halaman Keranjang (Cart)
    const subtotalEl = document.getElementById('summary-subtotal');
    const shippingEl = document.getElementById('summary-shipping');
    const handlingEl = document.getElementById('summary-handling') || document.getElementById('summary-service');
    const totalEl = document.getElementById('summary-total');
    const cartItemsCountEl = document.getElementById('cart-summary-items-count');
    const cartDistBadge = document.getElementById('cart-display-distance');

    if (subtotalEl) subtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    if (shippingEl) shippingEl.textContent = 'Rp ' + shipping.toLocaleString('id-ID');
    if (handlingEl) handlingEl.textContent = 'Rp ' + handling.toLocaleString('id-ID');
    if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    if (cartItemsCountEl) cartItemsCountEl.textContent = totalItems;
    if (cartDistBadge) cartDistBadge.textContent = distanceVal;

    // 2. Elemen di Halaman Checkout
    const checkSubtotalEl = document.getElementById('checkout-summary-subtotal');
    const checkShippingEl = document.getElementById('checkout-summary-shipping');
    const checkHandlingEl = document.getElementById('checkout-summary-handling');
    const checkTotalEl = document.getElementById('checkout-summary-total');
    const checkItemsCountEl = document.getElementById('checkout-summary-items-count');
    const checkDistBadge = document.getElementById('checkout-summary-distance-badge');
    const checkHandlingBadge = document.getElementById('checkout-summary-handling-badge');

    if (checkSubtotalEl) checkSubtotalEl.textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    if (checkShippingEl) checkShippingEl.textContent = 'Rp ' + shipping.toLocaleString('id-ID');
    if (checkHandlingEl) checkHandlingEl.textContent = 'Rp ' + handling.toLocaleString('id-ID');
    if (checkTotalEl) checkTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    if (checkItemsCountEl) checkItemsCountEl.textContent = totalItems;
    if (checkDistBadge) checkDistBadge.textContent = `(Jarak: ${distanceVal} km)`;
    if (checkHandlingBadge) checkHandlingBadge.textContent = `(${totalItems} pcs @ Rp 2.000)`;

    // 3. Update masing-masing baris item produk
    if (summary.items) {
      summary.items.forEach(item => {
        const itemSubtotalEl = document.getElementById(`item-subtotal-${item.id}`);
        const qtyInputEl = document.getElementById(`item-qty-${item.id}`);
        if (itemSubtotalEl) {
          itemSubtotalEl.textContent = 'Rp ' + Number(item.subtotal).toLocaleString('id-ID');
        }
        if (qtyInputEl) {
          qtyInputEl.value = item.quantity;
        }
      });
    }
  },

  async refreshSummaryAfterAddressSync(distance) {
    try {
      const res = await fetch('?action=cart/get');
      const data = await res.json();
      if (data.status === 'success' && data.data) {
        this.updateCartDomSummary(data.data);
      }
    } catch (e) {
      // ignore
    }
  },

  /* ----------------- CHECKOUT METODE BAYAR TOGGLE ----------------- */
  onPaymentMethodChange(method) {
    const qrisGuide = document.getElementById('payment-guide-qris');
    const transferGuide = document.getElementById('payment-guide-transfer');

    if (qrisGuide) {
      if (method === 'QRIS') {
        qrisGuide.classList.remove('hidden');
      } else {
        qrisGuide.classList.add('hidden');
      }
    }

    if (transferGuide) {
      if (method === 'TRANSFER') {
        transferGuide.classList.remove('hidden');
      } else {
        transferGuide.classList.add('hidden');
      }
    }
  },

  /* ----------------- PRATINJAU FILE BUKTI BAYAR ----------------- */
  previewProof(input) {
    const file = input.files?.[0];
    const previewContainer = document.getElementById('proof-preview-container');
    const previewImg = document.getElementById('proof-preview-img');
    const filenameEl = document.getElementById('proof-preview-filename');

    if (!file || !previewContainer) return;

    if (filenameEl) filenameEl.textContent = file.name;

    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        if (previewImg) previewImg.src = e.target.result;
      };
      reader.readAsDataURL(file);
    } else {
      if (previewImg) previewImg.src = 'assets/images/branding/aksara-logo.png';
    }

    previewContainer.classList.remove('hidden');
  },

  clearProof() {
    const input = document.getElementById('input-payment-proof');
    const previewContainer = document.getElementById('proof-preview-container');
    if (input) input.value = '';
    if (previewContainer) previewContainer.classList.add('hidden');
  },

  /* ----------------- CHECKOUT FORM SUBMISSION ----------------- */
  initCheckout() {
    const successModal = document.getElementById('order-success-modal');
    if (successModal) {
      successModal.addEventListener('click', (e) => {
        if (e.target === successModal || e.target.classList.contains('min-h-full')) {
          this.closeOrderModal();
        }
      });
    }

    const form = document.getElementById('checkout-form');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const submitBtn = document.getElementById('btn-submit-order');
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
          <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
          Merekam Pesanan...
        `;
      }

      try {
        const formData = new FormData(form);

        const res = await fetch('?action=order/checkout', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': this.csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });

        const data = await res.json();

        if (data.status === 'success') {
          App.showToast('Pesanan berhasil dibuat secara resmi!', 'success', 'Selesai');
          App.updateCartBadge();
          this.currentOrderData = data;
          this.showOrderSuccessModal(data);
        } else {
          App.showToast(data.message || 'Pemesanan gagal.', 'error', 'Pemesanan Gagal');
          if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<span>Konfirmasi & Teruskan ke Kurir WA</span>`;
          }
        }
      } catch (err) {
        App.showToast('Terjadi gangguan jaringan.', 'error');
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = `<span>Konfirmasi & Teruskan ke Kurir WA</span>`;
        }
      }
    });
  },

  showOrderSuccessModal(data) {
    const modal = document.getElementById('order-success-modal');
    if (!modal) {
      if (data.whatsapp_url) window.location.href = data.whatsapp_url;
      return;
    }

    const codeEl = document.getElementById('modal-order-code');
    const customerEl = document.getElementById('modal-order-customer');
    const addressEl = document.getElementById('modal-order-address');
    const mapsLinkEl = document.getElementById('modal-order-maps');
    const totalEl = document.getElementById('modal-order-total');
    const paymentEl = document.getElementById('modal-order-payment');
    const waBtn = document.getElementById('modal-btn-wa');
    const trackBtn = document.getElementById('modal-btn-track');
    const qrisSection = document.getElementById('modal-qris-container');
    const qrisImg = document.getElementById('modal-qris-img');

    const proofBadge = document.getElementById('modal-proof-badge');
    const proofPreviewWrap = document.getElementById('modal-proof-preview-wrap');
    const proofPreviewImg = document.getElementById('modal-proof-preview-img');
    const proofLink = document.getElementById('modal-proof-link');
    const proofUploadForm = document.getElementById('modal-proof-upload-form');

    const ord = data.order;
    const items = ord.items || [];
    let totalPcs = 0;
    items.forEach(it => {
      totalPcs += (parseInt(it.quantity, 10) || 1);
    });
    if (!totalPcs && ord.total_items) totalPcs = ord.total_items;

    const distance = ord.delivery_distance ? Number(ord.delivery_distance).toFixed(1) : '5.0';

    if (codeEl) codeEl.textContent = '#' + ord.order_code;
    const dateEl = document.getElementById('modal-order-date');
    if (dateEl) {
      const now = new Date();
      dateEl.textContent = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) + ' WITA';
    }
    if (customerEl) customerEl.textContent = ord.customer_name;
    const phoneEl = document.getElementById('modal-order-phone');
    if (phoneEl) phoneEl.textContent = ord.customer_phone;
    if (addressEl) addressEl.textContent = ord.customer_address;
    if (mapsLinkEl) {
      mapsLinkEl.href = ord.maps_url || '#';
      if (!ord.maps_url) mapsLinkEl.classList.add('hidden');
    }

    // Render daftar item di struk
    const itemsContainer = document.getElementById('modal-order-items');
    if (itemsContainer && items.length > 0) {
      itemsContainer.innerHTML = items.map((it, idx) => `
        <div class="flex justify-between items-center text-xs py-1 border-b border-dashed border-slate-200 dark:border-slate-700/60">
          <div>
            <span class="font-bold text-slate-800 dark:text-slate-100">${idx + 1}. ${it.product_name}</span>
            <span class="text-[10px] text-slate-400 block">${it.quantity}x @ Rp ${Number(it.unit_price).toLocaleString('id-ID')}</span>
          </div>
          <span class="font-bold text-slate-900 dark:text-white">Rp ${Number(it.subtotal).toLocaleString('id-ID')}</span>
        </div>
      `).join('');
    }

    // Render rincian biaya di struk
    const subtotalEl = document.getElementById('modal-order-subtotal');
    const shippingEl = document.getElementById('modal-order-shipping');
    const serviceEl = document.getElementById('modal-order-service');
    const qtyCountEl = document.getElementById('modal-order-qty-count');
    const distBadgeEl = document.getElementById('modal-order-distance-badge');
    const handlingRateEl = document.getElementById('modal-order-handling-rate');

    if (qtyCountEl) qtyCountEl.textContent = totalPcs;
    if (subtotalEl) subtotalEl.textContent = 'Rp ' + Number(ord.subtotal).toLocaleString('id-ID');
    if (distBadgeEl) distBadgeEl.textContent = `(Jarak: ${distance} km • 5 km pertama Rp 15.000, +Rp 2.000/km)`;
    if (shippingEl) shippingEl.textContent = 'Rp ' + Number(ord.shipping_fee).toLocaleString('id-ID');
    if (handlingRateEl) handlingRateEl.textContent = `(${totalPcs} pcs @ Rp 2.000)`;
    if (serviceEl) serviceEl.textContent = 'Rp ' + Number(ord.service_fee || (totalPcs * 2000)).toLocaleString('id-ID');
    if (totalEl) totalEl.textContent = 'Rp ' + Number(ord.total_amount).toLocaleString('id-ID');
    if (paymentEl) paymentEl.textContent = ord.payment_method;
    if (trackBtn) trackBtn.href = data.tracking_url;

    // Rincian Bukti Bayar
    if (ord.payment_proof) {
      if (proofBadge) {
        proofBadge.textContent = 'Sudah Diunggah ✓';
        proofBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200';
      }
      if (proofPreviewWrap) proofPreviewWrap.classList.remove('hidden');
      if (proofPreviewImg) proofPreviewImg.src = ord.payment_proof;
      if (proofLink) proofLink.href = ord.payment_proof;
      if (proofUploadForm) proofUploadForm.classList.add('hidden');
    } else {
      if (proofBadge) {
        proofBadge.textContent = (ord.payment_method === 'COD') ? 'Tunai COD' : 'Belum Diunggah';
        proofBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-200';
      }
      if (proofPreviewWrap) proofPreviewWrap.classList.add('hidden');
      if (proofUploadForm) {
        if (ord.payment_method === 'COD') {
          proofUploadForm.classList.add('hidden');
        } else {
          proofUploadForm.classList.remove('hidden');
        }
      }
    }

    if (waBtn) {
      waBtn.href = data.whatsapp_url;
      waBtn.onclick = () => {
        window.open(data.whatsapp_url, '_blank');
      };
    }

    if (data.qris_data && qrisSection && qrisImg) {
      qrisSection.classList.remove('hidden');
      qrisImg.src = data.qris_data.qr_code_url;
    } else if (qrisSection) {
      qrisSection.classList.add('hidden');
    }

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Buka WhatsApp kurir otomatis setelah 1.5 detik
    setTimeout(() => {
      if (data.whatsapp_url) {
        window.open(data.whatsapp_url, '_blank');
      }
    }, 1500);
  },

  async uploadModalProof() {
    const input = document.getElementById('modal-proof-file-input');
    const file = input?.files?.[0];
    if (!file) {
      App.showToast('Silakan pilih file bukti pembayaran terlebih dahulu.', 'error');
      return;
    }

    const orderCode = this.currentOrderData?.order?.order_code;
    if (!orderCode) {
      App.showToast('Kode pesanan tidak ditemukan.', 'error');
      return;
    }

    const formData = new FormData();
    formData.append('order_code', orderCode);
    formData.append('payment_proof', file);

    try {
      const res = await fetch('?action=order/upload-proof', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      });
      const data = await res.json();

      if (data.status === 'success') {
        App.showToast('Bukti pembayaran berhasil diunggah!', 'success');
        const proofBadge = document.getElementById('modal-proof-badge');
        const proofPreviewWrap = document.getElementById('modal-proof-preview-wrap');
        const proofPreviewImg = document.getElementById('modal-proof-preview-img');
        const proofLink = document.getElementById('modal-proof-link');
        const proofUploadForm = document.getElementById('modal-proof-upload-form');

        if (proofBadge) {
          proofBadge.textContent = 'Sudah Diunggah ✓';
          proofBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-800';
        }
        if (proofPreviewWrap) proofPreviewWrap.classList.remove('hidden');
        if (proofPreviewImg) proofPreviewImg.src = data.proof_url;
        if (proofLink) proofLink.href = data.proof_url;
        if (proofUploadForm) proofUploadForm.classList.add('hidden');
      } else {
        App.showToast(data.message || 'Gagal mengunggah bukti.', 'error');
      }
    } catch (e) {
      App.showToast('Gagal menghubungi server.', 'error');
    }
  },

  closeOrderModal() {
    const modal = document.getElementById('order-success-modal');
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  },

  printOrderReceipt() {
    window.print();
  }
};

document.addEventListener('DOMContentLoaded', () => {
  Cart.initCheckout();
});
