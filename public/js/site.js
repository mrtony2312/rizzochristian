(function () {
    'use strict';

    function qsa(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

    /* ---------- Generic off-canvas panel / modal toggling ---------- */
    function openPanel(target) {
        var panel = document.getElementById(target);
        if (!panel) return;

        // Catalog filter drawer (mobile/tablet)
        if (panel.classList.contains('catalog-sidebar')) {
            panel.classList.add('is-open', 'offscreen-panel--open');
            panel.style.display = 'block';
            panel.style.visibility = 'visible';
            document.body.classList.add('offscreen-panel-open');
            return;
        }

        panel.classList.add('is-open');
        if (panel.classList.contains('modal')) {
            panel.classList.add('modal--open');
        }
        panel.style.visibility = 'visible';
        panel.style.pointerEvents = 'auto';
        var container = panel.querySelector('.panel__container, .modal__notices, .modal__quickview, .modal__content');
        if (container) {
            container.style.transform = 'translateX(0)';
            container.style.opacity = '1';
        }
        var backdrop = panel.querySelector('.panel__backdrop, .modal__backdrop');
        if (backdrop) backdrop.style.opacity = '1';
        panel.style.display = 'block';
        document.body.classList.add('offscreen-panel-open', 'modal-opened');
    }

    function closePanel(panel) {
        if (!panel) return;
        panel.classList.remove('is-open', 'modal--open', 'loading');
        var container = panel.querySelector('.panel__container, .modal__notices, .modal__quickview');
        if (container && panel.classList.contains('offscreen-panel')) {
            container.style.transform = 'translateX(100%)';
        }
        panel.style.display = 'none';
        document.body.classList.remove('offscreen-panel-open', 'modal-opened');
    }

    document.addEventListener('click', function (e) {
        var toggle = e.target.closest('[data-toggle="off-canvas"], [data-toggle="modal"]');
        if (toggle && toggle.dataset.target) {
            // Quick-view has its own AJAX heler below.
            if (toggle.dataset.target === 'quick-view-modal') {
                return;
            }
            e.preventDefault();
            openPanel(toggle.dataset.target);
            return;
        }
        var closeBtn = e.target.closest('.panel__button-close, .panel__backdrop, .modal__backdrop, .modal__button-close, .sidebar__backdrop');
        if (closeBtn) {
            var panel = closeBtn.closest('.offscreen-panel, .modal, .catalog-sidebar');
            if (panel) {
                e.preventDefault();
                if (panel.classList.contains('catalog-sidebar')) {
                    panel.classList.remove('is-open', 'offscreen-panel--open');
                    panel.style.display = '';
                    document.body.classList.remove('offscreen-panel-open');
                } else {
                    closePanel(panel);
                }
            }
        }
    });

    /* ---------- AJAX add-to-cart / remove-from-cart ---------- */
    function updateCartCount(count) {
        qsa('[data-cart-counter]').forEach(function (el) {
            el.textContent = count;
            el.classList.toggle('hidden', count <= 0);
        });
        qsa('[data-cart-counter-paren]').forEach(function (el) {
            el.textContent = '(' + count + ')';
        });
    }

    function updateMiniCart(html) {
        var el = document.getElementById('mini-cart-content');
        if (el) el.innerHTML = html;
    }

    function showToast(message, type) {
        var container = document.getElementById('toast-container');
        if (!container) return;
        var toast = document.createElement('div');
        toast.textContent = message;
        toast.style.cssText = 'pointer-events:auto;background:' + (type === 'error' ? '#c0392b' : '#1f1a17') + ';color:#fff;padding:12px 18px;border-radius:6px;box-shadow:0 4px 14px rgba(0,0,0,.18);font-size:14px;max-width:320px;opacity:0;transform:translateY(-8px);transition:opacity .2s ease,transform .2s ease;';
        container.appendChild(toast);
        requestAnimationFrame(function () {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });
        window.setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-8px)';
            window.setTimeout(function () { toast.remove(); }, 200);
        }, 2500);
    }

    function showAddedPopup(name) {
        showToast('«' + name + '» è stato aggiunto al carrello.');
    }

    function isCartForm(form) {
        var action = form.getAttribute('action') || '';
        return action.indexOf('/carrello/add/') !== -1 || action.indexOf('/carrello/remove/') !== -1 || form.classList.contains('ajax-cart-form');
    }

    /* ---------- Remove-from-cart links (mini-cart + cart page) ---------- */
    document.addEventListener('click', function (e) {
        var link = e.target.closest('.ajax-remove-from-cart');
        if (!link) return;
        e.preventDefault();

        var url = link.dataset.url;
        if (!url) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
                'X-HTTP-Method-Override': 'DELETE',
            },
            credentials: 'same-origin',
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (typeof data.count !== 'undefined') updateCartCount(data.count);
                if (data.mini_cart_html) updateMiniCart(data.mini_cart_html);

                if (window.location.pathname.replace(/\/$/, '') === '/cart') {
                    window.location.reload();
                }
            })
            .catch(function () {});
    });

    /* ---------- Anteprima rapida modal (AJAX) ---------- */
    document.addEventListener('click', function (e) {
        var trigger = e.target.closest('[data-toggle="modal"][data-target="quick-view-modal"], .motta-button--quickview');
        if (!trigger) return;
        e.preventDefault();
        e.stopPropagation();

        var url = trigger.dataset.url;
        var modal = document.getElementById('quick-view-modal');
        var container = modal ? modal.querySelector('.modal__product') : null;
        if (!url || !container) return;

        container.innerHTML = '<div class="motta-loading-spinner" style="padding:60px;text-align:center;">Caricamento&hellip;</div>';
        if (modal) modal.classList.add('loading');
        openPanel('quick-view-modal');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }, credentials: 'same-origin' })
            .then(function (r) {
                if (!r.ok) throw new Error('quickview failed');
                return r.text();
            })
            .then(function (html) {
                container.innerHTML = html;
                if (modal) modal.classList.remove('loading');
                if (!window.isAuthenticated) syncGuestWishlistButtons();
            })
            .catch(function () {
                container.innerHTML = '<p style="padding:40px;">Impossibile caricare il prodotto.</p>';
                if (modal) modal.classList.remove('loading');
            });
    });

    /* ---------- Wishlist toggle (AJAX) ---------- */
    function csrfToken() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.content : '';
    }

    function updateWishlistCount(count) {
        qsa('[data-wishlist-counter]').forEach(function (el) { el.textContent = count; });
    }

    /* ---------- Guest wishlist (localStorage) ---------- */
    function getGuestWishlist() {
        try {
            return JSON.parse(window.localStorage.getItem('wishlist_ids') || '[]').map(String);
        } catch (e) {
            return [];
        }
    }

    function setGuestWishlist(ids) {
        try { window.localStorage.setItem('wishlist_ids', JSON.stringify(ids.map(String))); } catch (e) {}
    }

    function toggleGuestWishlist(productId) {
        var id = String(productId);
        var ids = getGuestWishlist();
        var idx = ids.indexOf(id);
        var added;
        if (idx === -1) {
            ids.push(id);
            added = true;
        } else {
            ids.splice(idx, 1);
            added = false;
        }
        setGuestWishlist(ids);
        return { added: added, count: ids.length };
    }

    function syncGuestWishlistButtons() {
        var ids = getGuestWishlist();
        qsa('.wishlist-toggle[data-product_id]').forEach(function (btn) {
            btn.classList.toggle('is-active', ids.indexOf(String(btn.dataset.product_id)) !== -1);
        });
        updateWishlistCount(ids.length);
    }

    if (!window.isAuthenticated) {
        document.addEventListener('DOMContentLoaded', syncGuestWishlistButtons);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.wishlist-toggle');
        if (!btn) return;
        e.preventDefault();

        if (!window.isAuthenticated) {
            var productId = btn.dataset.product_id;
            if (!productId) return;
            var result = toggleGuestWishlist(productId);
            qsa('.wishlist-toggle[data-product_id="' + productId + '"]').forEach(function (b) {
                b.classList.toggle('is-active', result.added);
            });
            updateWishlistCount(result.count);
            return;
        }

        var url = btn.dataset.url || btn.getAttribute('href');
        if (!url) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            credentials: 'same-origin',
        })
            .then(function (r) {
                if (r.status === 401) {
                    return r.json().then(function (data) {
                        if (data.redirect) window.location.href = data.redirect;
                    });
                }
                return r.json().then(function (data) {
                    if (!data.success) return;
                    var productId = btn.dataset.product_id;
                    qsa('.wishlist-toggle[data-product_id="' + productId + '"]').forEach(function (b) {
                        b.classList.toggle('is-active', data.added);
                    });
                    if (typeof data.count !== 'undefined') updateWishlistCount(data.count);
                });
            })
            .catch(function () {});
    });

    /* ---------- Motta-style filter checkboxes + AJAX catalog ---------- */
    (function () {
        var form = document.querySelector('[data-catalog-filters]');
        if (!form) return;

        var wrap = document.querySelector('[data-catalog-wrap]');
        var results = document.querySelector('[data-catalog-results]');
        var loader = document.querySelector('[data-catalog-loader]');
        var countEl = document.querySelector('[data-catalog-count]');
        var activatedWrap = document.querySelector('[data-catalog-activated]');
        var sortForm = document.querySelector('[data-catalog-sort]');
        var requestId = 0;

        function setLoading(isLoading) {
            if (!wrap || !loader) return;
            wrap.classList.toggle('is-loading', isLoading);
            if (isLoading) {
                loader.hidden = false;
            } else {
                loader.hidden = true;
            }
        }

        function syncSelectedClasses() {
            qsa('[data-filter-checkbox]', form).forEach(function (item) {
                var input = item.querySelector('input[type="checkbox"]');
                item.classList.toggle('selected', !!(input && input.checked));
            });
        }

        function buildUrl() {
            var params = new URLSearchParams();
            var formData = new FormData(form);
            formData.forEach(function (value, key) {
                if (value === null || value === undefined) return;
                var str = String(value).trim();
                if (str === '') return;
                params.append(key, str);
            });
            if (sortForm) {
                var orderby = sortForm.querySelector('[name="orderby"]');
                if (orderby && orderby.value && orderby.value !== 'menu_order') {
                    params.set('orderby', orderby.value);
                }
            }
            var qs = params.toString();
            return form.action + (qs ? '?' + qs : '');
        }

        function applyCatalog(data) {
            if (results && typeof data.products_html === 'string') {
                results.innerHTML = data.products_html;
            }
            if (countEl && typeof data.count_html === 'string') {
                countEl.textContent = data.count_html;
            }
            if (activatedWrap && typeof data.activated_html === 'string') {
                activatedWrap.innerHTML = data.activated_html;
            }
            syncSelectedClasses();
            if (!window.isAuthenticated && typeof syncGuestWishlistButtons === 'function') {
                syncGuestWishlistButtons();
            }
            var toolbar = document.getElementById('motta-toolbar-view');
            if (toolbar) {
                var current = toolbar.querySelector('a.current[data-view]');
                if (current) {
                    var view = current.dataset.view;
                    var grid = document.querySelector('[data-catalog-results] ul.products');
                    if (grid && view) {
                        grid.className = grid.className.replace(/\b(columns-\d+|list)\b/g, '').trim();
                        grid.classList.add(view === 'list' ? 'list' : 'columns-' + view.replace('grid-', ''));
                    }
                }
            }
        }

        function fetchCatalog(pushUrl) {
            var url = buildUrl();
            var currentRequest = ++requestId;
            setLoading(true);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin',
            })
                .then(function (r) {
                    if (!r.ok) throw new Error('catalog filter failed');
                    return r.json();
                })
                .then(function (data) {
                    if (currentRequest !== requestId) return;
                    applyCatalog(data);
                    if (pushUrl !== false) {
                        window.history.pushState({ catalog: true }, '', url);
                    }
                })
                .catch(function () {
                    if (currentRequest !== requestId) return;
                    window.location.href = url;
                })
                .finally(function () {
                    if (currentRequest === requestId) setLoading(false);
                });
        }

        document.addEventListener('click', function (e) {
            var item = e.target.closest('[data-filter-checkbox]');
            if (item && form.contains(item)) {
                if (e.target.closest('a')) return;
                e.preventDefault();
                var input = item.querySelector('input[type="checkbox"]');
                if (!input) return;
                input.checked = !input.checked;
                item.classList.toggle('selected', input.checked);
                // Only clear custom min/max when toggling a price range (not categories)
                if (input.name === 'price_range[]') {
                    var minInput = form.querySelector('[name="min_price"]');
                    var maxInput = form.querySelector('[name="max_price"]');
                    if (minInput) minInput.value = '';
                    if (maxInput) maxInput.value = '';
                }
                fetchCatalog(true);
                return;
            }

            var clearBtn = e.target.closest('[data-filter-clear]');
            if (clearBtn) {
                e.preventDefault();
                var clearHref = clearBtn.getAttribute('href');
                // Category pages: Cancella tutto returns to shop (href). Shop: clear in-place.
                if (clearHref && clearHref !== '#' && !form.querySelector('input[name="product_cat[]"]')) {
                    window.location.href = clearHref;
                    return;
                }
                qsa('input[type="checkbox"]', form).forEach(function (cb) { cb.checked = false; });
                var min = form.querySelector('[name="min_price"]');
                var max = form.querySelector('[name="max_price"]');
                if (min) min.value = '';
                if (max) max.value = '';
                syncSelectedClasses();
                fetchCatalog(true);
                return;
            }

            var removeBtn = e.target.closest('[data-filter-remove]');
            if (removeBtn) {
                e.preventDefault();
                var type = removeBtn.dataset.filterRemove;
                if (type === 'category') {
                    // Navigate to shop while keeping price filters (href already has qs)
                    var categoryHref = removeBtn.getAttribute('href');
                    if (categoryHref && categoryHref !== '#') {
                        window.location.href = categoryHref;
                        return;
                    }
                } else if (type === 'product_cat') {
                    var catValue = removeBtn.dataset.value;
                    qsa('input[name="product_cat[]"]', form).forEach(function (cb) {
                        if (cb.value === catValue) cb.checked = false;
                    });
                } else if (type === 'price_range') {
                    var value = removeBtn.dataset.value;
                    qsa('input[name="price_range[]"]', form).forEach(function (cb) {
                        if (cb.value === value) cb.checked = false;
                    });
                } else if (type === 'price_custom') {
                    var minEl = form.querySelector('[name="min_price"]');
                    var maxEl = form.querySelector('[name="max_price"]');
                    if (minEl) minEl.value = '';
                    if (maxEl) maxEl.value = '';
                }
                syncSelectedClasses();
                fetchCatalog(true);
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            // Apply custom min/max: uncheck range boxes so server uses min/max
            qsa('input[name="price_range[]"]', form).forEach(function (cb) { cb.checked = false; });
            syncSelectedClasses();
            fetchCatalog(true);
        });

        if (sortForm) {
            sortForm.addEventListener('change', function (e) {
                if (!e.target.matches('[name="orderby"]')) return;
                e.preventDefault();
                fetchCatalog(true);
            });
            sortForm.addEventListener('submit', function (e) {
                e.preventDefault();
                fetchCatalog(true);
            });
        }

        window.addEventListener('popstate', function () {
            // Reload filters from URL on back/forward
            window.location.reload();
        });
    })();

    /* ---------- Catalog grid / list view toggle ---------- */
    (function () {
        var toolbar = document.getElementById('motta-toolbar-view');
        if (!toolbar) return;

        function applyView(view) {
            var grid = document.querySelector('[data-catalog-results] ul.products, ul.products');
            if (!grid) return;
            grid.className = grid.className.replace(/\b(columns-\d+|list)\b/g, '').trim();
            grid.classList.add(view === 'list' ? 'list' : 'columns-' + view.replace('grid-', ''));

            qsa('a[data-view]', toolbar).forEach(function (link) {
                link.classList.toggle('current', link.dataset.view === view);
            });
        }

        toolbar.addEventListener('click', function (e) {
            var link = e.target.closest('a[data-view]');
            if (!link) return;
            e.preventDefault();
            var view = link.dataset.view;
            try { window.localStorage.setItem('catalogView', view); } catch (err) {}
            applyView(view);
        });

        var saved = null;
        try { saved = window.localStorage.getItem('catalogView'); } catch (err) {}
        if (saved) applyView(saved);
    })();

    /* ---------- Checkout UI helpers ---------- */
    document.addEventListener('click', function (e) {
        var toggleAddress = e.target.closest('[data-toggle-address2]');
        if (toggleAddress) {
            e.preventDefault();
            var wrap = document.querySelector('[data-address2-wrap]');
            if (wrap) wrap.style.display = 'block';
            toggleAddress.remove();
        }
    });

    document.addEventListener('change', function (e) {
        if (!e.target.matches('[data-toggle-notes]')) return;
        var notesWrap = document.querySelector('[data-notes-wrap]');
        if (!notesWrap) return;
        notesWrap.hidden = !e.target.checked;
    });

    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || !isCartForm(form)) return;

        e.preventDefault();

        var action = form.getAttribute('action');
        var isAdd = action.indexOf('/carrello/add/') !== -1;
        var isBuyNow = e.submitter && e.submitter.name === 'buy-now';
        var formData = new FormData(form);

        var submitBtn = form.querySelector('[type="submit"]');
        if (submitBtn) submitBtn.classList.add('loading');

        fetch(action, {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (typeof data.count !== 'undefined') updateCartCount(data.count);
                if (data.mini_cart_html) updateMiniCart(data.mini_cart_html);

                if (isAdd && isBuyNow && window.checkoutUrl) {
                    window.location.href = window.checkoutUrl;
                    return;
                }

                // if we're on the full cart page, refresh it so totals/rows stay correct
                if (window.location.pathname.replace(/\/$/, '') === '/cart') {
                    window.location.reload();
                    return;
                }

                if (isAdd && data.product_name) {
                    showAddedPopup(data.product_name);
                }
            })
            .catch(function () {
                // fall back to a normal page submission if the AJAX call fails
                form.submit();
            })
            .finally(function () {
                if (submitBtn) submitBtn.classList.remove('loading');
            });
    });

    /* ---------- Quantity stepper (-/+) ---------- */
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.qty-btn--minus, .qty-btn--plus');
        if (!btn) return;
        e.preventDefault();
        var input = btn.parentElement.querySelector('input[type="number"]');
        if (!input) return;
        var min = parseInt(input.min, 10) || 1;
        var value = parseInt(input.value, 10) || min;
        value = btn.classList.contains('qty-btn--plus') ? value + 1 : Math.max(min, value - 1);
        input.value = value;
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    /* ---------- Mini-cart quantity update (AJAX) ---------- */
    var qtyUpdateTimer = null;

    function updateCartItemQty(stepper, qty) {
        var url = stepper && stepper.dataset.updateUrl;
        if (!url) return;

        fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
            },
            credentials: 'same-origin',
            body: JSON.stringify({ quantity: qty }),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (typeof data.count !== 'undefined') updateCartCount(data.count);
                if (data.mini_cart_html) updateMiniCart(data.mini_cart_html);
            })
            .catch(function () {});
    }

    document.addEventListener('change', function (e) {
        var input = e.target.closest('.cart-panel-qty .ajax-cart-qty');
        if (!input) return;
        var stepper = input.closest('.cart-panel-qty');
        var qty = Math.max(1, parseInt(input.value, 10) || 1);
        input.value = qty;
        window.clearTimeout(qtyUpdateTimer);
        qtyUpdateTimer = window.setTimeout(function () {
            updateCartItemQty(stepper, qty);
        }, 250);
    });

    /* ---------- Product gallery thumbnail swap ---------- */
    document.addEventListener('click', function (e) {
        var thumb = e.target.closest('.product-gallery__thumb-btn');
        if (!thumb) return;
        e.preventDefault();
        var main = document.getElementById('product-gallery-main-img');
        if (main && thumb.dataset.full) main.src = thumb.dataset.full;
        qsa('.product-gallery__thumb-btn').forEach(function (b) {
            b.style.borderColor = b === thumb ? '#1f1a17' : 'transparent';
        });
    });

    /* ---------- Product tabs (Descrizione / Recensioni) ---------- */
    document.addEventListener('click', function (e) {
        var tabLink = e.target.closest('.woocommerce-tabs .tabs > li > a, .woocommerce-tabs .tabs > li[role="tab"] > a');
        if (!tabLink) return;
        e.preventDefault();
        var targetId = tabLink.getAttribute('href');
        if (!targetId || targetId.charAt(0) !== '#') return;
        var wrapper = tabLink.closest('.woocommerce-tabs');
        if (!wrapper) return;

        qsa('li', wrapper.querySelector('.tabs')).forEach(function (li) { li.classList.remove('active'); });
        tabLink.closest('li').classList.add('active');

        qsa('.wc-tab', wrapper).forEach(function (panel) {
            panel.style.display = ('#' + panel.id === targetId) ? 'block' : 'none';
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        qsa('.woocommerce-tabs').forEach(function (wrapper) {
            var tabs = qsa('.wc-tab', wrapper);
            tabs.forEach(function (panel, i) { panel.style.display = i === 0 ? 'block' : 'none'; });
            var firstLi = wrapper.querySelector('.tabs > li');
            if (firstLi) firstLi.classList.add('active');
        });
    });

    /* ---------- Header search: Tutte category picker + Categorie menu ---------- */
    (function () {
        function closeSearchCategories(form) {
            if (!form) return;
            form.classList.remove('categories--open');
            var panel = form.querySelector('.header-search__categories');
            if (panel) panel.classList.remove('header-search__categories--open');
        }

        function openSearchCategories(form) {
            if (!form) return;
            qsa('.header-search__form').forEach(closeSearchCategories);
            form.classList.add('categories--open');
            var panel = form.querySelector('.header-search__categories');
            if (panel) panel.classList.add('header-search__categories--open');
        }

        function setSearchCategory(form, slug, label) {
            var input = form.querySelector('input.category-name');
            var text = form.querySelector('.header-search__categories-text');
            if (input) input.value = slug || '0';
            if (text) text.textContent = label || 'Tutte';
            qsa('.header-search__categories-container a', form).forEach(function (a) {
                a.classList.toggle('active', a.dataset.slug === String(slug || '0'));
            });
            closeSearchCategories(form);
        }

        document.addEventListener('click', function (e) {
            var catTitle = e.target.closest('.header-category__title');
            if (catTitle) {
                e.preventDefault();
                var menu = catTitle.closest('.header-category-menu');
                if (!menu) return;
                var willOpen = !menu.classList.contains('motta-open');
                qsa('.header-category-menu.motta-open').forEach(function (m) {
                    if (m !== menu) m.classList.remove('motta-open');
                });
                menu.classList.toggle('motta-open', willOpen);
                return;
            }

            var label = e.target.closest('.header-search__categories-label');
            if (label) {
                e.preventDefault();
                var form = label.closest('.header-search__form');
                if (!form) return;
                if (form.classList.contains('categories--open')) {
                    closeSearchCategories(form);
                } else {
                    openSearchCategories(form);
                }
                return;
            }

            var closeCats = e.target.closest('.header-search__categories-close');
            if (closeCats) {
                e.preventDefault();
                closeSearchCategories(closeCats.closest('.header-search__form'));
                return;
            }

            var catLink = e.target.closest('.header-search__categories-container a[data-slug]');
            if (catLink) {
                e.preventDefault();
                var formFromLink = catLink.closest('.header-search__form');
                if (!formFromLink) return;
                setSearchCategory(formFromLink, catLink.dataset.slug, catLink.textContent.trim());
                return;
            }

            if (!e.target.closest('.header-search__form') && !e.target.closest('.header-category-menu')) {
                qsa('.header-search__form').forEach(closeSearchCategories);
                qsa('.header-category-menu.motta-open').forEach(function (m) {
                    m.classList.remove('motta-open');
                });
            }
        });

        document.addEventListener('submit', function (e) {
            var form = e.target.closest('.header-search__form, .search-modal__form');
            if (!form) return;

            var catInput = form.querySelector('input.category-name, input[name="product_cat"]');
            if (catInput && (catInput.value === '0' || catInput.value === '')) {
                catInput.disabled = true;
                window.setTimeout(function () { catInput.disabled = false; }, 0);
            }

            var searchInput = form.querySelector('[name="s"]');
            var term = searchInput ? String(searchInput.value || '').trim() : '';
            var slug = catInput && !catInput.disabled ? String(catInput.value || '') : '';

            // Empty search + specific category → go straight to category page
            if (!term && slug && slug !== '0') {
                e.preventDefault();
                window.location.href = '/categoria-prodotto/' + encodeURIComponent(slug) + '/';
            }
        });
    })();

    /* Never replace the catalog hero on Motta filter AJAX — image stays slider-zermatt-pellets-2.jpg */
    if (window.jQuery) {
        window.jQuery(document.body).on('motta_products_filter_request_success', function () {
            // Keep existing .page-header--products; Motta would otherwise swap it.
            var header = document.querySelector('.page-header--products');
            if (header) {
                header.setAttribute('data-hero-locked', '1');
            }
        });
        // Neutralize Motta's header replace by stubbing after Motta init if present
        if (window.motta && typeof window.motta.changeCatalogElementsFiltered === 'function') {
            var originalChange = window.motta.changeCatalogElementsFiltered;
            window.motta.changeCatalogElementsFiltered = function () {
                originalChange.apply(this, arguments);
                // Restore locked background if Motta injected inline styles
                document.querySelectorAll('.page-header--products .page-header__image').forEach(function (el) {
                    el.style.removeProperty('background-image');
                });
            };
        }
    }

    function italianizeSliderLabels() {
        qsa('.swiper-button-prev, .motta-swiper-button-prev').forEach(function (el) {
            el.setAttribute('aria-label', 'Diapositiva precedente');
        });
        qsa('.swiper-button-next, .motta-swiper-button-next').forEach(function (el) {
            el.setAttribute('aria-label', 'Diapositiva successiva');
        });
        qsa('.swiper-pagination-bullet').forEach(function (el, index) {
            el.setAttribute('aria-label', 'Vai alla diapositiva ' + (index + 1));
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        italianizeSliderLabels();
        window.setTimeout(italianizeSliderLabels, 600);
    });
})();
