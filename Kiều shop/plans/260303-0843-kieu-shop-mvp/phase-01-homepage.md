# Phase 01: Hoàn Thiện Homepage
Status: ⬜ Pending
Dependencies: Không

## Objective
Tạo trang chủ (`front-page.php`) đúng nghĩa cho Kiều Shop — hero slider, categories, sản phẩm nổi bật, promo banners. File hiện tại chỉ là placeholder.

## Files to Create/Modify
- `kieu-shop-theme/front-page.php` — Tạo mới hoàn chỉnh
- `kieu-shop-theme/style.css` — Bổ sung CSS nếu thiếu

## Sections cần implement

### 1. Hero Slider
- [ ] 2-3 slides với ảnh full width (aspect-ratio: landscape)
- [ ] Text overlay: label + large title + brand name
- [ ] Prev/Next buttons + dot indicators
- [ ] CSS animation slide-in
- [ ] JS autoplay 5s, pause on hover

### 2. Brand Intro
- [ ] Gold divider (đã có CSS `.gold-divider`)
- [ ] Câu quote serif italic: "Áo dài cách tân – kết hợp vẻ đẹp truyền thống với hơi thở hiện đại"
- [ ] Section padding top/bottom

### 3. Categories Grid
- [ ] 4 circles: Áo Dài Cách Tân, Truyền Thống, BST Tết, BST Cưới
- [ ] Link đến category WooCommerce tương ứng
- [ ] Hover: gold border + zoom image
- [ ] Fallback nếu chưa có ảnh category

### 4. Sản Phẩm Nổi Bật
- [ ] WooCommerce query: 6 sản phẩm mới nhất
- [ ] 3 cột grid (dùng class `.product-grid` đã có)
- [ ] Badge MỚI/Sale, quick-add hover
- [ ] Nút "Xem Tất Cả" centered

### 5. Promo Banners (tùy chọn)
- [ ] 2 cột song song với ảnh background
- [ ] Text overlay: label + title + link

## Test Criteria
- [ ] Trang chủ load không lỗi PHP
- [ ] Hero slider tự chạy sau 5s
- [ ] Categories link đến đúng trang
- [ ] 6 sản phẩm WooCommerce hiện đúng (nếu đã có sản phẩm)
- [ ] Responsive: mobile 1 cột, tablet 2 cột, desktop 3-4 cột

---
Next Phase: [Phase 02 — Product Single](./phase-02-product-single.md)
