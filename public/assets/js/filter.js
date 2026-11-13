/**
 * public/assets/js/filter.js
 * Real-time Product Search & Smart Dynamic Filters
 */

const ProductFilter = {
  debounceTimer: null,

  init() {
    const searchInput = document.getElementById('filter-search');
    const categoryInputs = document.querySelectorAll('input[name="filter_category"], [data-category-filter]');
    const priceRangeInput = document.getElementById('filter-price-range');
    const inStockInput = document.getElementById('filter-stock-only');
    const sortSelect = document.getElementById('filter-sort');

    if (searchInput) {
      searchInput.addEventListener('input', () => {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.applyFilter(), 250);
      });
    }

    if (categoryInputs.length > 0) {
      categoryInputs.forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const slug = btn.getAttribute('data-category-filter') || '';
          
          document.querySelectorAll('[data-category-filter]').forEach(b => {
            const bSlug = b.getAttribute('data-category-filter') || '';
            if (bSlug === slug) {
              b.classList.add('bg-unsulbar-maroon', 'text-white', 'shadow-md');
              b.classList.remove('bg-slate-50', 'dark:bg-slate-700/40', 'bg-slate-100', 'dark:bg-slate-700/60', 'text-slate-700', 'dark:text-slate-300');
            } else {
              b.classList.remove('bg-unsulbar-maroon', 'text-white', 'shadow-md');
              b.classList.add('bg-slate-50', 'dark:bg-slate-700/40', 'text-slate-700', 'dark:text-slate-300');
            }
          });

          document.getElementById('selected-category-slug').value = slug;
          this.applyFilter();
        });
      });
    }

    if (priceRangeInput) {
      const priceDisplay = document.getElementById('filter-price-display');
      priceRangeInput.addEventListener('input', () => {
        const val = Number(priceRangeInput.value);
        if (priceDisplay) {
          priceDisplay.textContent = val >= 60000 ? 'Semua Harga' : 'Maks. Rp ' + val.toLocaleString('id-ID');
        }
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => this.applyFilter(), 200);
      });
    }

    if (inStockInput) {
      inStockInput.addEventListener('change', () => this.applyFilter());
    }

    if (sortSelect) {
      sortSelect.addEventListener('change', () => this.applyFilter());
    }
  },

  async applyFilter() {
    const searchVal = document.getElementById('filter-search')?.value || '';
    const categoryVal = document.getElementById('selected-category-slug')?.value || '';
    const priceVal = document.getElementById('filter-price-range')?.value || '';
    const stockVal = document.getElementById('filter-stock-only')?.checked ? '1' : '';
    const sortVal = document.getElementById('filter-sort')?.value || 'newest';

    const params = new URLSearchParams();
    if (searchVal) params.append('search', searchVal);
    if (categoryVal) params.append('category', categoryVal);
    if (priceVal && Number(priceVal) < 60000) params.append('max_price', priceVal);
    if (stockVal) params.append('in_stock', '1');
    if (sortVal) params.append('sort', sortVal);

    const productGrid = document.getElementById('products-grid-container');
    const countBadge = document.getElementById('products-count-badge');
    if (!productGrid) return;

    productGrid.style.opacity = '0.4';

    try {
      const res = await fetch(`?action=api/products&${params.toString()}`);
      const data = await res.json();

      if (data.status === 'success') {
        if (countBadge) {
          countBadge.textContent = `${data.count} Produk`;
        }
        this.renderProducts(data.data, productGrid);
      }
    } catch (e) {
      console.error(e);
    } finally {
      productGrid.style.opacity = '1';
    }
  },

  renderProducts(products, container) {
    if (products.length === 0) {
      container.innerHTML = `
        <div class="col-span-full py-16 text-center">
          <div class="w-20 h-20 mx-auto mb-4 bg-amber-50 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-3xl">
            🍄
          </div>
          <h3 class="text-xl font-bold text-slate-800 dark:text-slate-100 mb-2">Produk Tidak Ditemukan</h3>
          <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto text-sm">
            Kombinasi kata kunci atau filter yang Anda pilih saat ini tidak cocok dengan katalog kami. Coba atur ulang filter pencarian Anda.
          </p>
        </div>
      `;
      return;
    }

    container.innerHTML = products.map(product => {
      const priceFormatted = Number(product.price).toLocaleString('id-ID');
      const isOutOfStock = product.stock <= 0;
      
      let badgeClass = 'bg-slate-700 text-white';
      if (product.category_slug === 'olahan-pangan-jamur') {
        badgeClass = 'bg-emerald-600 text-white';
      } else if (product.category_slug === 'pupuk-organik-limbah') {
        badgeClass = 'bg-amber-600 text-white';
      } else if (product.category_slug === 'produk-paket-budidaya') {
        badgeClass = 'bg-sky-600 text-white';
      }

      return `
        <div class="interactive-card bg-white dark:bg-slate-800 rounded-3xl overflow-hidden border border-slate-200/80 dark:border-slate-700 flex flex-col justify-between group shadow-xs hover:shadow-xl transition-all duration-300">
          <div class="relative bg-slate-100 dark:bg-slate-900 aspect-4/3 overflow-hidden">
            <img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
              <span class="text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider ${badgeClass}">
                ${product.category_name}
              </span>
              ${product.is_featured ? `
                <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-unsulbar-gold text-slate-900 flex items-center gap-1 shadow-xs">
                  ★ Unggulan
                </span>
              ` : ''}
            </div>

            <div class="absolute bottom-3 right-3">
              <span class="text-xs font-semibold px-2.5 py-1 rounded-lg backdrop-blur-md shadow-xs ${
                isOutOfStock 
                  ? 'bg-red-500/90 text-white' 
                  : product.stock < 10 
                  ? 'bg-amber-500/90 text-white animate-pulse' 
                  : 'bg-slate-900/80 text-white'
              }">
                ${isOutOfStock ? 'Stok Habis' : `Sisa ${product.stock} ${product.unit}`}
              </span>
            </div>
          </div>

          <div class="p-5 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="font-bold text-base text-slate-900 dark:text-slate-100 leading-snug group-hover:text-unsulbar-maroon dark:group-hover:text-unsulbar-gold transition-colors line-clamp-1">
                ${product.name}
              </h3>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                ${product.description}
              </p>
            </div>

            <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 mt-4">
              <div class="flex items-baseline justify-between mb-4">
                <div>
                  <span class="text-[11px] text-slate-400">Harga Satuan</span>
                  <div class="text-lg font-black text-unsulbar-maroon dark:text-unsulbar-gold">
                    Rp ${priceFormatted}
                  </div>
                </div>
                <span class="text-xs text-slate-400 font-medium">/${product.unit}</span>
              </div>

              <div class="grid grid-cols-2 gap-2">
                <button onclick="Cart.add(${product.id}, 1, false)" ${isOutOfStock ? 'disabled' : ''} class="interactive-btn py-2 px-2.5 rounded-xl border border-unsulbar-maroon/30 dark:border-unsulbar-gold/30 text-unsulbar-maroon dark:text-unsulbar-gold font-bold text-xs flex items-center justify-center gap-1.5 hover:bg-unsulbar-maroon/5 dark:hover:bg-unsulbar-gold/10 disabled:opacity-40 disabled:cursor-not-allowed">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                  + Keranjang
                </button>
                <button onclick="Cart.add(${product.id}, 1, true)" ${isOutOfStock ? 'disabled' : ''} class="interactive-btn py-2 px-2.5 rounded-xl bg-unsulbar-maroon hover:bg-unsulbar-maroon-dark text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs disabled:opacity-40 disabled:cursor-not-allowed">
                  Beli Sekarang
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');
  }
};

document.addEventListener('DOMContentLoaded', () => ProductFilter.init());
