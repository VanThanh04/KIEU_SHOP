# Phase 02: Trang Chi Tiết Sản Phẩm
Status: ⬜ Pending
Dependencies: Phase 01

## Objective
Hoàn thiện `single-product.php` — trang hiển thị thông tin 1 sản phẩm cụ thể với gallery, selector size/màu, và các nút hành động.

## Files to Create/Modify
- `kieu-shop-theme/single-product.php` — Hoàn thiện/tạo mới
- `kieu-shop-theme/style.css` — CSS cho product single page

## Implementation Steps

### Gallery (cột trái)
- [ ] Ảnh chính (aspect-ratio 3:4, phóng to khi click)
- [ ] 4 thumbnails hàng ngang (click đổi ảnh chính)
- [ ] Placeholder khi không có ảnh

### Product Info (cột phải)
- [ ] Breadcrumb: Trang chủ / Sản Phẩm / Tên SP
- [ ] Category label màu đỏ nhỏ (uppercase)
- [ ] Tên sản phẩm (Cormorant Garamond, lớn)
- [ ] Gold divider
- [ ] Giá: sale price đỏ + giá gốc gạch ngang (nếu on sale)
- [ ] Star rating WooCommerce (nếu có reviews)
- [ ] Mô tả ngắn (short description)

### Selectors
- [ ] Size: S M L XL (rectangular toggle buttons, active = red bg)
- [ ] Color swatches: tròn nhỏ (nếu có variation)
- [ ] Quantity: [ - ] [1] [ + ]

### Action Buttons
- [ ] "Thêm Vào Giỏ Hàng" — đỏ full width
- [ ] "Mua Ngay – Thanh Toán VNPay" — outline đỏ full width
- [ ] Policy row: 🚚 Giao hàng | ↩ Đổi trả 7 ngày | 🔒 An toàn

### Related Products
- [ ] "Sản Phẩm Liên Quan" heading + gold divider
- [ ] 3 product cards (same class as archive grid)

## Test Criteria
- [ ] Vào trang sản phẩm: ảnh hiện, giá hiện đúng định dạng VND
- [ ] Click size → visual feedback (button đổi màu đỏ)
- [ ] Click Add to Cart → cart count tăng lên trong header
- [ ] Mobile: 2 cột → 1 cột (stacked)

---
Next Phase: [Phase 03 — Checkout + VNPay](./phase-03-checkout-vnpay.md)
