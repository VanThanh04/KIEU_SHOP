<?php
/**
 * page-vip-contact.php — KIỀU
 * Trang hỗ trợ VIP: chỉ hiển thị với VIP Customer.
 * Slug WordPress: "ho-tro-vip"
 * Template: "Hỗ Trợ VIP KIỀU"
 */
get_header();

// Redirect khách thường về homepage
if (!function_exists('kieu_is_current_user_vip') || !kieu_is_current_user_vip()):
    // Không phải VIP → hiện thông báo, không redirect cứng
?>
<main style="background:var(--color-bg); min-height:70vh; display:flex; align-items:center; justify-content:center;">
    <div style="text-align:center; padding:4rem 2rem;">
        <i class="fa-solid fa-lock" style="font-size:2.5rem; color:var(--color-border); display:block; margin-bottom:1.5rem"></i>
        <h2 style="font-family:var(--font-display); font-size:1.4rem; letter-spacing:0.08em; margin-bottom:0.75rem">
            Dành Riêng Cho Thành Viên VIP
        </h2>
        <p style="color:var(--color-text-muted); font-size:0.9rem; max-width:400px; margin:0 auto 2rem; line-height:1.7">
            Tính năng hỗ trợ trực tiếp chỉ dành cho khách hàng VIP.
            Để trở thành VIP, anh/chị cần chi tiêu <strong>≥ 3.000.000₫</strong> trong 12 tháng.
        </p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <a href="https://www.facebook.com/" target="_blank" rel="noopener" class="btn-primary">
                <i class="fa-brands fa-facebook-messenger"></i>&nbsp; Chat Facebook
            </a>
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="btn-outline">
                Xem Sản Phẩm →
            </a>
        </div>
    </div>
</main>
<?php
get_footer();
return;
endif; ?>

<!-- ==== VIP CONTACT PAGE ==== -->
<main class="vip-contact-main">

    <!-- Header -->
    <div class="vip-contact-header">
        <span class="vip-crown-icon"><i class="fa-solid fa-crown"></i></span>
        <p class="vip-contact-eyebrow">CHỈ DÀNH CHO THÀNH VIÊN VIP</p>
        <h1 class="section-title">Hỗ Trợ VIP Trực Tiếp</h1>
        <div class="gold-divider"><span>&#10022;</span></div>
        <p class="vip-contact-subtitle">
            Đội ngũ KIỀU luôn sẵn sàng hỗ trợ anh/chị ưu tiên.
        </p>
    </div>

    <!-- Admin cards -->
    <div class="vip-contact-cards">

        <!-- Card 1: Admin / Chủ shop -->
        <div class="vip-contact-card">
            <div class="vip-card-avatar-wrap">
                <div class="vip-card-avatar vip-card-avatar--admin">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <span class="vip-online-dot"></span>
            </div>
            <h3 class="vip-card-name">Kiều</h3>
            <p class="vip-card-role">Chủ Shop / Admin</p>
            <p class="vip-card-response">
                <i class="fa-regular fa-clock"></i> Phản hồi trong <strong>1 giờ</strong>
            </p>
            <a href="https://m.me/" target="_blank" rel="noopener" class="btn-primary vip-card-btn">
                <i class="fa-brands fa-facebook-messenger"></i> Nhắn Tin Ngay
            </a>
        </div>

        <!-- Card 2: Sub-admin -->
        <div class="vip-contact-card">
            <div class="vip-card-avatar-wrap">
                <div class="vip-card-avatar vip-card-avatar--sub">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <span class="vip-online-dot vip-online-dot--away"></span>
            </div>
            <h3 class="vip-card-name">Thu</h3>
            <p class="vip-card-role">Tư Vấn Viên / Sub-Admin</p>
            <p class="vip-card-response">
                <i class="fa-regular fa-clock"></i> Thứ 2 – Thứ 6: 9h – 18h
            </p>
            <a href="https://zalo.me/" target="_blank" rel="noopener" class="btn-outline vip-card-btn">
                Zalo: 0900 000 000
            </a>
        </div>

    </div><!-- .vip-contact-cards -->

    <!-- Alternative channels -->
    <div class="vip-contact-alt">
        <p>Hoặc liên hệ qua</p>
        <div class="vip-contact-channels">
            <a href="https://www.facebook.com/" target="_blank" rel="noopener" class="vip-channel-btn vip-channel-fb">
                <i class="fa-brands fa-facebook-messenger"></i> Messenger
            </a>
            <a href="https://zalo.me/" target="_blank" rel="noopener" class="vip-channel-btn vip-channel-zalo">
                Zalo Official
            </a>
        </div>
    </div>

    <!-- VIP perks reminder -->
    <div class="vip-contact-perks">
        <p class="perks-label">Quyền lợi VIP của anh/chị</p>
        <div class="perks-list">
            <span><i class="fa-solid fa-percent"></i> Giảm 10% tất cả đơn hàng</span>
            <span><i class="fa-solid fa-truck-fast"></i> Miễn phí vận chuyển</span>
            <span><i class="fa-solid fa-headset"></i> Hỗ trợ ưu tiên 24/7</span>
            <span><i class="fa-solid fa-bell"></i> Thông báo BST mới sớm 24h</span>
        </div>
    </div>

</main>

<?php get_footer(); ?>
