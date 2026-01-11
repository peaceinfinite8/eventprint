/**
 * Admin Product Live Search Component
 * Provides autocomplete search for admin product list
 */

(function () {
    'use strict';

    function initAdminProductSearch() {
        const searchInput = document.getElementById('adminProductSearch');
        const dropdown = document.getElementById('adminSearchDropdown');
        const categoryFilter = document.getElementById('categoryFilter');

        if (!searchInput || !dropdown) return;

        let searchTimeout;
        let cachedProducts = [];

        // Debounced search (300ms)
        searchInput.addEventListener('input', function (e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            searchTimeout = setTimeout(() => {
                if (query.length < 1) {
                    hideDropdown();
                    return;
                }
                performSearch(query);
            }, 300);
        });

        // Category filter change
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function () {
                const categoryId = this.value;
                const baseUrl = window.location.origin + window.location.pathname;
                const params = new URLSearchParams();

                if (categoryId) params.append('category_id', categoryId);
                if (searchInput.value.trim()) params.append('q', searchInput.value.trim());

                const url = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
                window.location.href = url;
            });
        }

        // Perform search via API
        async function performSearch(query) {
            try {
                showLoading();

                const categoryId = categoryFilter ? categoryFilter.value : '';
                const baseUrl = window.location.origin + '/eventprint';
                const url = baseUrl + '/api/products?q=' + encodeURIComponent(query) +
                    (categoryId ? '&category_id=' + categoryId : '');

                const response = await fetch(url);
                const data = await response.json();

                if (data.success && data.products) {
                    cachedProducts = data.products;
                    renderResults(data.products.slice(0, 8));
                } else {
                    renderEmpty();
                }
            } catch (error) {
                console.error('Search error:', error);
                renderError();
            }
        }

        function renderResults(products) {
            if (products.length === 0) {
                renderEmpty();
                return;
            }

            const baseUrl = window.location.origin + '/eventprint';
            dropdown.innerHTML = products.map(p => {
                const price = formatPrice(p.base_price || 0);
                const category = p.category_name || 'Uncategorized';
                const isActive = p.is_active == 1 || p.is_active === true || p.is_active === '1';

                return `
          <div class="admin-search-item" onclick="window.location.href='${baseUrl}/admin/products/edit/${p.id}'">
            <div class="d-flex justify-content-between align-items-start">
              <div class="flex-grow-1">
                <div class="fw-bold text-dark">${escapeHtml(p.name)}</div>
                <div class="small text-muted mt-1">
                  <span class="badge bg-light text-dark border">${escapeHtml(category)}</span>
                  <span class="ms-2">Stock: ${p.stock || 0}</span>
                </div>
              </div>
              <div class="text-end ms-3">
                <div class="fw-semibold text-primary">${price}</div>
                ${isActive ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'}
              </div>
            </div>
          </div>
        `;
            }).join('');

            showDropdown();
        }

        function renderEmpty() {
            dropdown.innerHTML = '<div class="admin-search-empty">No products found</div>';
            showDropdown();
        }

        function renderError() {
            dropdown.innerHTML = '<div class="admin-search-empty text-danger">Error loading results</div>';
            showDropdown();
        }

        function showLoading() {
            dropdown.innerHTML = '<div class="admin-search-empty"><i class="fa-solid fa-spinner fa-spin me-2"></i>Searching...</div>';
            showDropdown();
        }

        function showDropdown() {
            dropdown.style.display = 'block';
        }

        function hideDropdown() {
            dropdown.style.display = 'none';
        }

        // Click outside to close
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                hideDropdown();
            }
        });

        // Focus to show dropdown if has results
        searchInput.addEventListener('focus', function () {
            if (this.value.trim().length >= 1 && dropdown.innerHTML) {
                showDropdown();
            }
        });

        // Keyboard navigation
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideDropdown();
                this.blur();
            }
        });

        // Utility functions
        function formatPrice(price) {
            return 'Rp ' + parseFloat(price).toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdminProductSearch);
    } else {
        initAdminProductSearch();
    }
})();
