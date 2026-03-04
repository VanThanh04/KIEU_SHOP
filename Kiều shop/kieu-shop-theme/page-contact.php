<?php
/**
 * Template Name: Liên hệ KIỀU
 * Template Post Type: page
 *
 * page-contact.php — KIỀU
 * Trang liên hệ: thông tin + form gửi tin nhắn
 * Slug WordPress: "lien-he"
 */
get_header();
?>

<main style="background:var(--color-bg); min-height:70vh; padding-bottom:5rem;">

<!-- Header trang -->
<div style="text-align:center; padding:4rem 1rem 2rem;">
    <p class="section-subtitle">— Chúng tôi lắng nghe —</p>
    <h1 class="section-title">Liên Hệ Với KIỀU</h1>
    <div class="gold-divider"><span>&#10022;</span></div>
</div>

<div class="contact-wrap">

    <!-- Cột trái: Thông tin liên hệ -->
    <div class="contact-info-col">

        <h2 class="contact-col-title">Thông Tin</h2>

        <div class="contact-info-list">
            <div class="contact-info-item">
                <div class="contact-info-icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div>
                    <p class="contact-info-label">Showroom</p>
                    <!-- Anh thay địa chỉ thật vào đây -->
                    <p>123 Đường ABC, Quận 1<br>TP. Hồ Chí Minh</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-info-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div>
                    <p class="contact-info-label">Điện thoại</p>
                    <p><a href="tel:+84900000000">0900 000 000</a></p>
                    <p style="font-size:0.78rem; color:var(--color-text-muted)">Thứ 2 – Thứ 7: 9:00 – 20:00</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-info-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <p class="contact-info-label">Email</p>
                    <p><a href="mailto:hello@kieu.vn">hello@kieu.vn</a></p>
                    <p style="font-size:0.78rem; color:var(--color-text-muted)">Phản hồi trong 24 giờ</p>
                </div>
            </div>

            <div class="contact-info-item">
                <div class="contact-info-icon" style="background:linear-gradient(135deg,#405DE6,#5851DB,#833AB4,#C13584,#E1306C);">
                    <i class="fa-brands fa-instagram" style="color:white"></i>
                </div>
                <div>
                    <p class="contact-info-label">Instagram / TikTok</p>
                    <a href="https://www.instagram.com/" target="_blank" rel="noopener">@kieu.aodai</a>
                </div>
            </div>
        </div>

        <!-- Map placeholder (anh nhúng Google Maps sau) -->
        <div class="contact-map-placeholder">
            <i class="fa-solid fa-map-location-dot" style="font-size:2rem; color:var(--color-primary); opacity:0.4"></i>
            <p style="font-size:0.82rem; color:var(--color-text-muted); margin-top:0.5rem">
                Google Maps<br><small>Nhúng embed code vào đây</small>
            </p>
        </div>

    </div><!-- .contact-info-col -->

    <!-- Cột phải: Form liên hệ -->
    <div class="contact-form-col">
        <h2 class="contact-col-title">Gửi Tin Nhắn</h2>

        <?php
        // Hiển thị thông báo nếu form vừa gửi
        if (isset($_GET['sent']) && $_GET['sent'] === '1'):
        ?>
        <div class="contact-success-msg">
            <i class="fa-solid fa-circle-check"></i>
            Cảm ơn anh/chị! Chúng tôi sẽ phản hồi trong 24 giờ.
        </div>
        <?php endif; ?>

        <form class="contact-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="kieu_contact_form">
            <?php wp_nonce_field('kieu_contact', 'kieu_contact_nonce'); ?>

            <div class="contact-form-row contact-form-row--2col">
                <div class="contact-form-field">
                    <label>Họ và tên <span>*</span></label>
                    <input type="text" name="contact_name" required placeholder="Nguyễn Thị Lan">
                </div>
                <div class="contact-form-field">
                    <label>Điện thoại</label>
                    <input type="tel" name="contact_phone" placeholder="0900 000 000">
                </div>
            </div>

            <div class="contact-form-field">
                <label>Email <span>*</span></label>
                <input type="email" name="contact_email" required placeholder="email@example.com">
            </div>

            <div class="contact-form-field">
                <label>Chủ đề</label>
                <select name="contact_subject">
                    <option value="">— Chọn chủ đề —</option>
                    <option value="order">Hỏi về đơn hàng</option>
                    <option value="product">Tư vấn sản phẩm</option>
                    <option value="size">Hướng dẫn chọn size</option>
                    <option value="custom">Đặt may riêng</option>
                    <option value="other">Khác</option>
                </select>
            </div>

            <div class="contact-form-field">
                <label>Tin nhắn <span>*</span></label>
                <textarea name="contact_message" rows="5" required placeholder="Anh/chị muốn hỏi gì với KIỀU?"></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width:100%; margin-top:0.5rem">
                Gửi Tin Nhắn &rarr;
            </button>
        </form>
    </div><!-- .contact-form-col -->

</div><!-- .contact-wrap -->

</main>

<?php get_footer(); ?>
