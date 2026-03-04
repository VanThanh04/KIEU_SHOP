<?php
/**
 * header.php - Kiều Shop
 * Hiển thị phần đầu trang: top bar social, logo giữa, hamburger menu, search, cart
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- ========================================
     HEADER
     ======================================== -->
<header class="site-header" id="site-header">

    <!-- Top Bar: Social Icons -->
    <div class="header-topbar">
        <a href="https://www.instagram.com/" target="_blank" rel="noopener" aria-label="Instagram">
            <i class="fa-brands fa-instagram"></i>
        </a>
        <a href="https://www.tiktok.com/" target="_blank" rel="noopener" aria-label="TikTok">
            <i class="fa-brands fa-tiktok"></i>
        </a>
        <a href="https://www.facebook.com/" target="_blank" rel="noopener" aria-label="Facebook">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
    </div>

    <!-- Main Header Row -->
    <div class="header-main">

        <!-- Left: Hamburger menu toggle -->
        <div class="header-left">
            <button class="header-menu-toggle" id="menuToggle" aria-label="Mở menu" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <!-- Center: Logo -->
        <?php kieu_shop_logo(); ?>

        <!-- Right: Search + Account + Cart -->
        <div class="header-right">
            <button class="header-icon-btn" id="searchToggle" aria-label="Tìm kiếm">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <!-- Account icon: crown nếu VIP, user thường nếu không -->
            <?php
            $account_url = get_permalink(get_page_by_path('tai-khoan')) ?: get_permalink(get_option('woocommerce_myaccount_page_id'));
            $is_vip      = function_exists('kieu_is_current_user_vip') && kieu_is_current_user_vip();
            ?>
            <a href="<?php echo esc_url($account_url); ?>"
               class="header-icon-btn header-account-btn<?php echo $is_vip ? ' is-vip' : ''; ?>"
               aria-label="<?php echo is_user_logged_in() ? 'Tài khoản' : 'Đăng nhập'; ?>">
                <?php if ($is_vip): ?>
                    <i class="fa-solid fa-crown" style="color:var(--color-gold)"></i>
                <?php elseif (is_user_logged_in()): ?>
                    <i class="fa-solid fa-user-check"></i>
                <?php else: ?>
                    <i class="fa-regular fa-user"></i>
                <?php endif; ?>
            </a>
            <?php if (class_exists('WooCommerce')): ?>
                <a href="<?php echo esc_url(wc_get_cart_url()); ?>" class="header-icon-btn" aria-label="Giỏ hàng">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <?php $count = WC()->cart->get_cart_contents_count(); ?>
                    <?php if ($count > 0): ?>
                        <span class="cart-count" id="cartCount"><?php echo esc_html($count); ?></span>
                    <?php else: ?>
                        <span class="cart-count" id="cartCount" style="display:none">0</span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>

    </div>
</header>

<!-- ========================================
     NAVIGATION DRAWER (Slide-in từ trái)
     ======================================== -->

<!-- Overlay mờ khi mở menu -->
<div class="nav-overlay" id="navOverlay"></div>

<!-- Menu drawer -->
<nav class="nav-drawer" id="navDrawer" aria-hidden="true">

    <!-- Nút đóng -->
    <button class="nav-close" id="navClose" aria-label="Đóng menu">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <!-- Logo trong drawer -->
    <div class="nav-logo">
        <?php kieu_shop_logo(); ?>
    </div>

    <!-- Menu items -->
    <ul class="nav-menu">

        <!-- Bộ sưu tập (có submenu) -->
        <li class="has-submenu">
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">
                Bộ Sưu Tập
                <i class="fa-solid fa-chevron-down" style="font-size:10px; transition:transform 0.3s"></i>
            </a>
            <ul class="nav-submenu">
                <?php
                // Lấy danh mục sản phẩm WooCommerce
                $categories = get_terms([
                    'taxonomy'   => 'product_cat',
                    'hide_empty' => true,
                    'parent'     => 0, // chỉ lấy cấp cha
                ]);
                if ($categories && !is_wp_error($categories)):
                    foreach ($categories as $cat):
                        if ($cat->name === 'Uncategorized') continue;
                ?>
                    <li>
                        <a href="<?php echo esc_url(get_term_link($cat)); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    </li>
                <?php endforeach; else: ?>
                    <!-- Mặc định nếu chưa có danh mục -->
                    <li><a href="#">Áo Dài Cách Tân</a></li>
                    <li><a href="#">Áo Dài Truyền Thống</a></li>
                    <li><a href="#">Bộ Sưu Tập Tết</a></li>
                    <li><a href="#">Bộ Sưu Tập Cưới</a></li>
                <?php endif; ?>
            </ul>
        </li>

        <!-- Sản phẩm -->
        <li>
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">
                Sản Phẩm
            </a>
        </li>

        <!-- Lookbook -->
        <li>
            <a href="<?php echo esc_url(get_post_type_archive_link('lookbook')); ?>">
                Lookbook
            </a>
        </li>

        <!-- Liên hệ -->
        <li>
            <a href="<?php echo esc_url(get_permalink(get_page_by_path('lien-he'))); ?>">
                Liên Hệ
            </a>
        </li>

    </ul>

    <!-- Auth links -->
    <div class="nav-auth">
        <?php if (is_user_logged_in()): ?>
            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>">
                Tài khoản của tôi
            </a>
            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="btn-login">
                Đăng xuất
            </a>
        <?php else: ?>
            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>">
                Đăng ký
            </a>
            <a href="<?php echo esc_url(get_permalink(get_option('woocommerce_myaccount_page_id'))); ?>" class="btn-login">
                Đăng nhập
            </a>
        <?php endif; ?>
    </div>

</nav>

<!-- ========================================
     SEARCH OVERLAY
     ======================================== -->
<div class="search-overlay" id="searchOverlay" aria-hidden="true">
    <form class="search-form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input
            type="search"
            class="search-input"
            id="searchInput"
            name="s"
            placeholder="Tìm kiếm áo dài..."
            autocomplete="off"
        >
        <input type="hidden" name="post_type" value="product">
        <button type="button" class="search-close" id="searchClose" aria-label="Đóng tìm kiếm">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </form>
</div>

<!-- ========================================
     MAIN CONTENT BẮT ĐẦU TỪ ĐÂY
     ======================================== -->
