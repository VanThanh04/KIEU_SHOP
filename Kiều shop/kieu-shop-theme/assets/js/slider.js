/**
 * slider.js — Kiều Shop Hero Slider
 * Tự động chạy slide, hỗ trợ prev/next, dots indicator và swipe trên mobile
 */

(function () {
    'use strict';

    // ==============================
    // HERO SLIDER
    // ==============================
    const slider = {
        slides: null,
        dots: null,
        current: 0,
        total: 0,
        autoTimer: null,
        interval: 5000, // 5 giây chuyển slide

        init() {
            this.slides = document.querySelectorAll('.hero-slide');
            this.dots = document.querySelectorAll('.hero-dot');
            this.total = this.slides.length;

            if (this.total <= 1) return; // 1 slide thì không cần làm gì

            // Gắn sự kiện nút prev/next
            const prevBtn = document.getElementById('heroPrev');
            const nextBtn = document.getElementById('heroNext');
            if (prevBtn) prevBtn.addEventListener('click', () => this.prev());
            if (nextBtn) nextBtn.addEventListener('click', () => this.next());

            // Gắn sự kiện dots
            this.dots.forEach((dot, i) => {
                dot.addEventListener('click', () => this.goTo(i));
            });

            // Tự động chạy
            this.startAuto();

            // Swipe support (mobile)
            this.initSwipe();
        },

        goTo(index) {
            // Bỏ active class
            this.slides[this.current].classList.remove('is-active');
            if (this.dots[this.current]) this.dots[this.current].classList.remove('is-active');

            // Cập nhật index mới
            this.current = (index + this.total) % this.total;

            // Thêm active class
            this.slides[this.current].classList.add('is-active');
            if (this.dots[this.current]) this.dots[this.current].classList.add('is-active');
        },

        next() {
            this.goTo(this.current + 1);
            this.resetAuto();
        },

        prev() {
            this.goTo(this.current - 1);
            this.resetAuto();
        },

        startAuto() {
            // Tự động next sau mỗi interval
            this.autoTimer = setInterval(() => this.next(), this.interval);
        },

        resetAuto() {
            // Reset timer khi user tương tác
            clearInterval(this.autoTimer);
            this.startAuto();
        },

        initSwipe() {
            // Hỗ trợ swipe trái/phải trên mobile
            const el = document.querySelector('.hero-slider');
            if (!el) return;

            let startX = 0;
            el.addEventListener('touchstart', (e) => {
                startX = e.changedTouches[0].clientX;
            }, { passive: true });

            el.addEventListener('touchend', (e) => {
                const diff = startX - e.changedTouches[0].clientX;
                // Swipe > 50px mới tính
                if (Math.abs(diff) > 50) {
                    diff > 0 ? this.next() : this.prev();
                }
            }, { passive: true });
        }
    };

    // Chạy khi DOM ready
    document.addEventListener('DOMContentLoaded', () => slider.init());

})();
