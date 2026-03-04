<?php
/**
 * footer.php - KIỀU
 * Footer 4 cột trên nền đỏ truyền thống + Messenger float button
 */
?>

<!-- ========================================
     MESSENGER FLOAT BUTTON
     ======================================== -->
<a href="https://m.me/" class="messenger-float" target="_blank" rel="noopener" aria-label="Chat Messenger">
    <i class="fa-brands fa-facebook-messenger"></i>
</a>

<!-- ========================================
     FOOTER
     ======================================== -->
<footer class="site-footer">
    <div class="footer-grid">

        <!-- Cột 1: Logo + Giới thiệu + Social -->
        <div class="footer-col">
            <div class="footer-logo">
                <div class="logo-frame" style="display:inline-block; border:2px solid rgba(255,255,255,0.6); padding:6px 18px;">
                    <span class="logo-text" style="font-family:'Cormorant Garamond',Georgia,serif; font-size:1.6rem; font-weight:700; color:white; letter-spacing:0.15em;">KIỀU</span>
                </div>
            </div>
            <p class="footer-about">
                KIỀU — nơi áo dài truyền thống gặp gỡ hơi thở hiện đại. Chúng tôi mang đến những thiết kế áo dài cách tân tinh tế, tôn lên vẻ đẹp phụ nữ Việt Nam.
            </p>
            <div class="footer-social">
                <a href="https://www.instagram.com/" target="_blank" rel="noopener" aria-label="Instagram">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                <a href="https://www.tiktok.com/" target="_blank" rel="noopener" aria-label="TikTok">
                    <i class="fa-brands fa-tiktok"></i>
                </a>
                <a href="https://www.facebook.com/" target="_blank" rel="noopener" aria-label="Facebook">
                    <i class="fa-brands fa-facebook-f"></i>
                </a>
                <a href="https://www.youtube.com/" target="_blank" rel="noopener" aria-label="YouTube">
                    <i class="fa-brands fa-youtube"></i>
                </a>
            </div>
        </div>

        <!-- Cột 2: Danh mục sản phẩm -->
        <div class="footer-col">
            <h3 class="footer-col-title">Bộ Sưu Tập</h3>
            <ul class="footer-links">
                <?php
                // Lấy danh mục WooCommerce
                $cats = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'number'     => 6,
                    'parent'     => 0,
                ]);
                if ($cats && !is_wp_error($cats)):
                    foreach ($cats as $cat):
                        if ($cat->name === 'Uncategorized') continue;
                ?>
                    <li>
                        <a href="<?php echo esc_url(get_term_link($cat)); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    </li>
                <?php endforeach; else: ?>
                    <!-- Mặc định -->
                    <li><a href="#">Áo Dài Cách Tân</a></li>
                    <li><a href="#">Áo Dài Truyền Thống</a></li>
                    <li><a href="#">Bộ Sưu Tập Tết</a></li>
                    <li><a href="#">Bộ Sưu Tập Cưới</a></li>
                    <li><a href="#">Lookbook</a></li>
                    <li><a href="#">Sale</a></li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Cột 3: Chính sách -->
        <div class="footer-col">
            <h3 class="footer-col-title">Hỗ Trợ</h3>
            <ul class="footer-links">
                <li><a href="#">Chính sách đổi trả</a></li>
                <li><a href="#">Chính sách vận chuyển</a></li>
                <li><a href="#">Hướng dẫn chọn size</a></li>
                <li><a href="#">Hướng dẫn bảo quản</a></li>
                <li><a href="#">Câu hỏi thường gặp</a></li>
                <li><a href="#">Điều khoản dịch vụ</a></li>
            </ul>
        </div>

        <!-- Cột 4: Liên hệ + Địa chỉ -->
        <div class="footer-col">
            <h3 class="footer-col-title">Liên Hệ</h3>
            <div class="footer-contact">
                <p>
                    <i class="fa-solid fa-location-dot" style="color: var(--color-gold, #C9A96E); width:16px"></i>
                    <!-- Thay địa chỉ thật vào đây -->
                    123 Đường ABC, Quận 1, TP.HCM
                </p>
                <p>
                    <i class="fa-solid fa-phone" style="color: var(--color-gold, #C9A96E); width:16px"></i>
                    <a href="tel:+84900000000" style="color:rgba(255,255,255,0.7)">0900 000 000</a>
                </p>
                <p>
                    <i class="fa-solid fa-envelope" style="color: var(--color-gold, #C9A96E); width:16px"></i>
                    <a href="mailto:hello@kieuShop.vn" style="color:rgba(255,255,255,0.7)">hello@kieuShop.vn</a>
                </p>
                <p>
                    <i class="fa-regular fa-clock" style="color: var(--color-gold, #C9A96E); width:16px"></i>
                    Thứ 2 – Thứ 7: 9h00 – 20h00
                </p>
            </div>
        </div>

    </div>

    <!-- Footer bottom: copyright -->
    <div class="footer-bottom">
        <p>
            &copy; <?php echo date('Y'); ?> <strong>KIỀU</strong>. Bảo lưu mọi quyền.
            &nbsp;|&nbsp;
            Thiết kế với <i class="fa-solid fa-heart" style="color:#C9A96E; font-size:10px"></i> tôn vinh áo dài Việt Nam.
        </p>
    </div>

</footer>

<?php wp_footer(); ?>
</body>
</html>
