# Plan: Kiều Shop MVP
Created: 2026-03-03 08:43
Status: 🟡 In Progress

## Overview
E-commerce website bán áo dài cách tân. Platform: WordPress + WooCommerce.
Chiến lược: hoàn thiện theme hiện có (`kieu-shop-theme`), ưu tiên MVP trước.

## Quyết định chính
- **Thanh toán:** Chuyển khoản ngân hàng + VNPay (không COD)
- **Approach:** Hoàn thiện theme hiện có (KHÔNG làm lại từ đầu)
- **Customization áo dài:** Phase 2 (sau MVP)

## Tech Stack
- Platform: WordPress 6.x + WooCommerce 8.x
- Theme: `kieu-shop-theme` (custom)
- Payment: VNPay for WooCommerce plugin
- CSS: Vanilla CSS (design tokens đã có)
- Fonts: Cormorant Garamond (display) + Montserrat (body)

## Phases

| Phase | Tên | Trạng thái | Files chính |
|-------|-----|-----------|-------------|
| 01 | Hoàn thiện Homepage | ⬜ Pending | `front-page.php`, `style.css` |
| 02 | Trang Sản Phẩm (Detail) | ⬜ Pending | `single-product.php` |
| 03 | Giỏ Hàng & Checkout + VNPay | ⬜ Pending | `functions.php`, VNPay config |
| 04 | Footer, Blog/Lookbook, Liên Hệ | ⬜ Pending | `footer.php`, `single.php`, `page-contact.php` |
| 05 | Tối ưu & Deploy | ⬜ Pending | `functions.php`, hosting config |

## Quick Commands
- Bắt đầu phase 1: `/code phase-01`
- Xem tiến độ: `/next`
