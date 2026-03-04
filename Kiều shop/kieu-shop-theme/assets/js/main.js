/**
 * main.js — Kiều Shop
 * Xử lý: Navigation drawer, Search overlay, Size selector,
 *        Gallery thumbnails, Quantity buttons, Quick Add to Cart (AJAX)
 */

(function () {
    'use strict';

    // ==============================
    // NAVIGATION DRAWER (Hamburger menu)
    // ==============================
    const menuToggle = document.getElementById('menuToggle');
    const navDrawer = document.getElementById('navDrawer');
    const navOverlay = document.getElementById('navOverlay');
    const navClose = document.getElementById('navClose');

    function openNav() {
        navDrawer.classList.add('is-open');
        navOverlay.classList.add('is-open');
        navDrawer.setAttribute('aria-hidden', 'false');
        menuToggle.setAttribute('aria-expanded', 'true');
        // Chặn scroll body
        document.body.style.overflow = 'hidden';
    }

    function closeNav() {
        navDrawer.classList.remove('is-open');
        navOverlay.classList.remove('is-open');
        navDrawer.setAttribute('aria-hidden', 'true');
        menuToggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (menuToggle) menuToggle.addEventListener('click', openNav);
    if (navClose) navClose.addEventListener('click', closeNav);
    if (navOverlay) navOverlay.addEventListener('click', closeNav);
    // Đóng khi nhấn Escape
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') { closeNav(); closeSearch(); } });

    // Submenu toggle (mobile)
    document.querySelectorAll('.nav-menu .has-submenu > a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const submenu = link.nextElementSibling;
            const icon = link.querySelector('i');
            if (submenu) {
                submenu.classList.toggle('is-open');
                if (icon) icon.style.transform = submenu.classList.contains('is-open') ? 'rotate(180deg)' : '';
            }
        });
    });

    // ==============================
    // SEARCH OVERLAY
    // ==============================
    const searchToggle = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.getElementById('searchClose');
    const searchInput = document.getElementById('searchInput');

    function openSearch() {
        searchOverlay.classList.add('is-open');
        searchOverlay.setAttribute('aria-hidden', 'false');
        setTimeout(() => { if (searchInput) searchInput.focus(); }, 100);
    }

    function closeSearch() {
        searchOverlay.classList.remove('is-open');
        searchOverlay.setAttribute('aria-hidden', 'true');
    }

    if (searchToggle) searchToggle.addEventListener('click', openSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);
    // Click ngoài overlay để đóng
    if (searchOverlay) {
        searchOverlay.addEventListener('click', (e) => {
            if (e.target === searchOverlay) closeSearch();
        });
    }

    // ==============================
    // SIZE SELECTOR
    // ==============================
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            // Bỏ active tất cả trong cùng group
            const group = btn.closest('.size-grid');
            if (group) {
                group.querySelectorAll('.size-btn').forEach(b => b.classList.remove('is-active'));
            }
            btn.classList.add('is-active');
        });
    });

    // ==============================
    // GALLERY THUMBNAILS (trang sản phẩm)
    // ==============================
    const galleryThumbs = document.querySelectorAll('.product-gallery-thumb');
    const galleryMainImg = document.getElementById('galleryMainImg');

    galleryThumbs.forEach(thumb => {
        thumb.addEventListener('click', () => {
            // Cập nhật active thumb
            galleryThumbs.forEach(t => t.classList.remove('is-active'));
            thumb.classList.add('is-active');

            // Cập nhật ảnh lớn
            const fullUrl = thumb.getAttribute('data-full');
            if (galleryMainImg && fullUrl) {
                galleryMainImg.src = fullUrl;
                galleryMainImg.style.opacity = '0';
                galleryMainImg.style.transition = 'opacity 0.3s ease';
                setTimeout(() => { galleryMainImg.style.opacity = '1'; }, 50);
            }
        });
    });

    // ==============================
    // QUANTITY BUTTONS
    // ==============================
    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');

    if (qtyInput && qtyMinus && qtyPlus) {
        qtyMinus.addEventListener('click', () => {
            const val = parseInt(qtyInput.value, 10);
            if (val > 1) qtyInput.value = val - 1;
        });
        qtyPlus.addEventListener('click', () => {
            const val = parseInt(qtyInput.value, 10);
            const max = parseInt(qtyInput.getAttribute('max'), 10) || 99;
            if (val < max) qtyInput.value = val + 1;
        });
    }

    // ==============================
    // QUICK ADD TO CART (AJAX)
    // ==============================
    window.kieuShop = window.kieuShop || {};

    window.kieuShop.quickAdd = async function (productId, el) {
        // Kiểm tra biến toàn cục từ WordPress (functions.php localize_script)
        if (typeof kieuShopData === 'undefined') return;

        const originalText = el.textContent;
        el.textContent = 'Đang thêm...';
        el.style.background = 'var(--color-text)';

        try {
            const formData = new FormData();
            formData.append('action', 'kieu_quick_add');
            formData.append('nonce', kieuShopData.nonce);
            formData.append('product_id', productId);

            const response = await fetch(kieuShopData.ajaxUrl, {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                el.textContent = '✓ Đã thêm!';
                el.style.background = '#2a7a3b';

                // Cập nhật số lượng giỏ hàng trên header
                const cartCount = document.getElementById('cartCount');
                if (cartCount && data.data.cart_count !== undefined) {
                    cartCount.textContent = data.data.cart_count;
                    cartCount.style.display = '';
                }

                // Reset sau 2 giây
                setTimeout(() => {
                    el.textContent = originalText;
                    el.style.background = '';
                }, 2000);
            } else {
                el.textContent = 'Thử lại';
                el.style.background = '#c0392b';
                setTimeout(() => {
                    el.textContent = originalText;
                    el.style.background = '';
                }, 2000);
            }
        } catch (error) {
            console.error('Quick add error:', error);
            el.textContent = originalText;
            el.style.background = '';
        }
    };

    // ==============================
    // HEADER: ẩn/hiện khi scroll
    // ==============================
    let lastScrollY = window.scrollY;
    const siteHeader = document.getElementById('site-header');

    window.addEventListener('scroll', () => {
        const currentY = window.scrollY;
        if (siteHeader) {
            if (currentY > lastScrollY && currentY > 100) {
                // Scroll xuống → ẩn header
                siteHeader.style.transform = 'translateY(-100%)';
                siteHeader.style.transition = 'transform 0.3s ease';
            } else {
                // Scroll lên → hiện header
                siteHeader.style.transform = 'translateY(0)';
            }
        }
        lastScrollY = currentY;
    }, { passive: true });

})();
