# Phase 03: Giỏ Hàng, Checkout & VNPay
Status: ⬜ Pending
Dependencies: Phase 02

## Objective
Cấu hình WooCommerce checkout đẹp theo design theme + tích hợp VNPay. Khách chỉ có 2 lựa chọn: Chuyển khoản ngân hàng OR VNPay.

## Files to Create/Modify
- `kieu-shop-theme/functions.php` — WooCommerce customizations
- `kieu-shop-theme/style.css` — WooCommerce checkout styles
- WordPress Admin — Cài plugin VNPay

## Implementation Steps

### 1. WooCommerce Cart Style
- [ ] Style trang giỏ hàng (cart.php hoặc WC default)
- [ ] Bảng sản phẩm trong giỏ: ảnh thumbnail, tên, giá, số lượng, xóa
- [ ] Order total summary box bên phải
- [ ] Nút "Tiến Hành Thanh Toán" màu đỏ

### 2. Checkout Form Style
- [ ] Style form billing fields (họ tên, SĐT, email, địa chỉ)
- [ ] Input styling theo design tokens (border đỏ khi focus)
- [ ] Order summary box bên phải

### 3. Cài đặt thanh toán trong functions.php
- [ ] Chỉ giữ lại: `bacs` (chuyển khoản) + `vnpay` gateway
- [ ] Style radio button chọn phương thức thanh toán
- [ ] Logo VNPay bên cạnh option

### 4. VNPay Plugin Setup
Plugin: **VNPay for WooCommerce** (tìm trên wordpress.org hoặc nhà phát hành VNPay)
- [ ] Cài và activate plugin
- [ ] Điền Merchant ID (`vnp_TmnCode`)
- [ ] Điền Hash Secret (`vnp_HashSecret`)
- [ ] Test môi trường sandbox trước
- [ ] Sau khi test OK → chuyển sang production

### 5. Cấu hình shipping
- [ ] Giá ship mặc định (ví dụ: 30,000đ nội thành, 50,000đ tỉnh)
- [ ] Free ship trên 2,000,000đ (tùy anh)

### 6. Trang Order Received (Thank you page)
- [ ] Custom styled thank you page
- [ ] Hiển thị mã đơn hàng + hướng dẫn chuyển khoản

## ⚠️ Lưu ý VNPay
Anh cần đăng ký tài khoản merchant với VNPay để lấy credentials thật:
- Website: https://sandbox.vnpayment.vn/apis/

## Test Criteria
- [ ] Giỏ hàng hiển thị sản phẩm đúng, có thể tăng/giảm số lượng
- [ ] Checkout form validate (không điền SĐT → báo lỗi)
- [ ] Chọn VNPay → redirect đến trang vnpayment.vn sandbox
- [ ] Sau khi thanh toán sandbox → về trang thank-you với mã đơn hàng

---
Next Phase: [Phase 04 — Footer + Pages](./phase-04-footer-pages.md)
