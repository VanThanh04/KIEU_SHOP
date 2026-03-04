<?php
/**
 * functions.php - KIỀU WordPress Theme
 * Đăng ký scripts, styles, WooCommerce support và các tính năng của theme
 */

// =============================================
// 1. SETUP THEME
// =============================================
function kieu_shop_setup() {
    // Cho phép dịch theme
    load_theme_textdomain('kieu-shop', get_template_directory() . '/languages');

    // Hỗ trợ các tính năng WordPress
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'gallery', 'caption']);
    add_theme_support('responsive-embeds');
    add_theme_support('custom-logo', [
        'height'      => 100,
        'width'       => 300,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // WooCommerce support
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Đăng ký vị trí menu
    register_nav_menus([
        'primary'  => 'Menu chính',
        'footer-1' => 'Footer - Danh mục',
        'footer-2' => 'Footer - Chính sách',
    ]);
}
add_action('after_setup_theme', 'kieu_shop_setup');

// =============================================
// 2. ENQUEUE SCRIPTS & STYLES
// =============================================
function kieu_shop_scripts() {
    // Google Fonts: Cormorant Garamond + Montserrat
    wp_enqueue_style(
        'kieu-shop-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=Montserrat:wght@300;400;500;600;700&display=swap',
        [],
        null
    );

    // Main CSS (style.css = CSS chính, không phải stylesheet WP)
    wp_enqueue_style(
        'kieu-shop-style',
        get_template_directory_uri() . '/style.css',
        ['kieu-shop-fonts'],
        wp_get_theme()->get('Version')
    );

    // WooCommerce custom CSS
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'kieu-shop-woocommerce',
            get_template_directory_uri() . '/assets/css/woocommerce.css',
            ['kieu-shop-style'],
            wp_get_theme()->get('Version')
        );
    }

    // Font Awesome (icons: giỏ hàng, search, social...)
    wp_enqueue_style(
        'font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
        [],
        '6.5.0'
    );

    // Hero Slider JS
    wp_enqueue_script(
        'kieu-shop-slider',
        get_template_directory_uri() . '/assets/js/slider.js',
        [],
        wp_get_theme()->get('Version'),
        true // load ở footer
    );

    // Main JS (menu, search overlay, size selector...)
    wp_enqueue_script(
        'kieu-shop-main',
        get_template_directory_uri() . '/assets/js/main.js',
        ['kieu-shop-slider'],
        wp_get_theme()->get('Version'),
        true
    );

    // Truyền AJAX URL vào JS (cần cho add-to-cart qua AJAX)
    wp_localize_script('kieu-shop-main', 'kieuShopData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('kieu_shop_nonce'),
        'cartUrl' => class_exists('WooCommerce') ? wc_get_cart_url() : '#',
    ]);
}
add_action('wp_enqueue_scripts', 'kieu_shop_scripts');

// =============================================
// 3. WOOCOMMERCE CUSTOMIZATIONS
// =============================================

// Bỏ sidebar mặc định của WooCommerce
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// Số sản phẩm hiển thị mỗi trang
add_filter('loop_shop_per_page', function() { return 12; }, 20);

// Số cột product grid trên shop page
add_filter('loop_shop_columns', function() { return 3; });

// Bỏ breadcrumb WooCommerce mặc định (dùng custom của mình)
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

// Bỏ wrapper mặc định WooCommerce, dùng của mình
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',  'woocommerce_output_content_wrapper_end', 10);

add_action('woocommerce_before_main_content', 'kieu_shop_woo_wrapper_start', 10);
add_action('woocommerce_after_main_content',  'kieu_shop_woo_wrapper_end', 10);

function kieu_shop_woo_wrapper_start() {
    echo '<main class="shop-page">';
}
function kieu_shop_woo_wrapper_end() {
    echo '</main>';
}

// =============================================
// 4. CUSTOM WIDGET AREAS
// =============================================
function kieu_shop_widgets_init() {
    register_sidebar([
        'name'          => 'Footer - Về KIỀU',
        'id'            => 'footer-about',
        'description'   => 'Widget area cho cột đầu tiên của footer',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="footer-col-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'kieu_shop_widgets_init');

// =============================================
// 5. HELPER FUNCTIONS
// =============================================

/**
 * Lấy tên danh mục sản phẩm đầu tiên
 */
function kieu_shop_get_product_category($product_id) {
    $terms = get_the_terms($product_id, 'product_cat');
    if ($terms && !is_wp_error($terms)) {
        return $terms[0]->name;
    }
    return '';
}

/**
 * Format giá tiền VN
 */
function kieu_shop_price($price) {
    return number_format($price, 0, ',', '.') . '₫';
}

/**
 * Render logo frame (dùng ở nhiều nơi)
 */
function kieu_shop_logo($class = '') {
    $logo_class = $class ? 'site-logo ' . $class : 'site-logo';
    ?>
    <div class="<?php echo esc_attr($logo_class); ?>">
        <a href="<?php echo esc_url(home_url('/')); ?>">
            <div class="logo-frame">
                <span class="logo-text">KIỀU</span>
            </div>
        </a>
    </div>
    <?php
}

// =============================================
// 6. IMAGE SIZE
// =============================================
add_image_size('kieu-product-portrait', 480, 640, true);  // 3:4 dọc
add_image_size('kieu-hero',             1920, 900, true); // Hero banner
add_image_size('kieu-category',         400, 400, true);  // Circle category

// =============================================
// 7. CUSTOM POST TYPE: LOOKBOOK (tuỳ chọn)
// =============================================
function kieu_shop_register_lookbook() {
    register_post_type('lookbook', [
        'labels' => [
            'name'          => 'Lookbook',
            'singular_name' => 'Lookbook',
            'add_new_item'  => 'Thêm Lookbook mới',
            'edit_item'     => 'Sửa Lookbook',
        ],
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-camera',
        'supports'      => ['title', 'editor', 'thumbnail'],
        'show_in_rest'  => true,
        'rewrite'       => ['slug' => 'lookbook'],
    ]);
}
add_action('init', 'kieu_shop_register_lookbook');

// =============================================
// 8. AJAX: QUICK ADD TO CART
// =============================================
function kieu_shop_quick_add_to_cart() {
    // Kiểm tra nonce bảo mật
    check_ajax_referer('kieu_shop_nonce', 'nonce');

    $product_id = intval($_POST['product_id'] ?? 0);
    if (!$product_id) {
        wp_send_json_error(['message' => 'Invalid product']);
    }

    // Thêm vào giỏ hàng WooCommerce
    $added = WC()->cart->add_to_cart($product_id);
    if ($added) {
        wp_send_json_success([
            'message'    => 'Đã thêm vào giỏ hàng!',
            'cart_count' => WC()->cart->get_cart_contents_count(),
            'cart_url'   => wc_get_cart_url(),
        ]);
    } else {
        wp_send_json_error(['message' => 'Không thể thêm sản phẩm này.']);
    }
}
add_action('wp_ajax_kieu_quick_add',        'kieu_shop_quick_add_to_cart');
add_action('wp_ajax_nopriv_kieu_quick_add', 'kieu_shop_quick_add_to_cart');

// =============================================
// 9. PHASE 03: CHECKOUT & PAYMENT
// =============================================

/**
 * Chỉ giữ lại 2 phương thức thanh toán: Chuyển khoản (bacs) + VNPay.
 * VNPay plugin tự đăng ký gateway id 'vnpay' sau khi cài.
 */
add_filter('woocommerce_available_payment_gateways', function($gateways) {
    $allowed = ['bacs', 'vnpay', 'vnpayqr'];
    foreach ($gateways as $id => $gateway) {
        if (!in_array($id, $allowed, true)) {
            unset($gateways[$id]);
        }
    }
    return $gateways;
});

/**
 * Hiển thị thông tin chuyển khoản ngân hàng có style riêng.
 * Anh chỉnh số TK, tên, ngân hàng dưới đây.
 */
add_action('woocommerce_thankyou_bacs', function($order_id) {
    if (!$order_id) return;
    ?>
    <div class="kieu-bank-info">
        <h3><i class="fa-solid fa-building-columns"></i> Thông Tin Chuyển Khoản</h3>
        <table class="kieu-bank-table">
            <tr><td>Ngân hàng</td><td><strong>Vietcombank (VCB)</strong></td></tr>
            <tr><td>Số tài khoản</td><td><strong>0123456789</strong></td></tr>
            <tr><td>Chủ tài khoản</td><td><strong>NGUYEN THI KIEU</strong></td></tr>
            <tr><td>Nội dung CK</td><td><strong>KIỀU <?php echo esc_html($order_id); ?></strong></td></tr>
        </table>
        <p class="kieu-bank-note">
            <i class="fa-solid fa-circle-info"></i>
            Vui lòng chuyển khoản trong vòng <strong>24 giờ</strong>.
            Đơn hàng sẽ được xử lý sau khi xác nhận thanh toán.
        </p>
    </div>
    <?php
});

/**
 * Thêm layout class vào body của trang checkout để style 2 cột.
 */
add_filter('body_class', function($classes) {
    if (function_exists('is_checkout') && is_checkout()) {
        $classes[] = 'kieu-checkout-page';
    }
    if (function_exists('is_cart') && is_cart()) {
        $classes[] = 'kieu-cart-page';
    }
    if (function_exists('is_account_page') && is_account_page()) {
        $classes[] = 'kieu-account-page';
    }
    return $classes;
});

/**
 * Hiển thị banner hướng dẫn VNPay trên trang checkout.
 */
add_action('woocommerce_before_checkout_form', function() {
    ?>
    <div class="kieu-checkout-header">
        <p class="section-subtitle">&#8212; Thanh toán an toàn &#8212;</p>
        <h2 class="section-title">Hoàn Tất Đơn Hàng</h2>
        <div class="gold-divider"><span>&#10022;</span></div>
    </div>
    <?php
}, 5);

/**
 * Thay label nút "Đặt hàng".
 */
add_filter('woocommerce_order_button_text', function() {
    return 'Đặt Hàng &rarr;';
});

// =============================================
// 10. PHASE 02.5: VIP CUSTOMER SYSTEM
// =============================================

/**
 * Đăng ký role VIP Customer khi theme kích hoạt.
 */
function kieu_shop_register_vip_role() {
    add_role('vip_customer', 'VIP Customer', [
        'read'                   => true,
        'edit_posts'             => false,
        'delete_posts'           => false,
        'upload_files'           => false,
        'woocommerce_manage_orders' => false,
    ]);
}
add_action('after_setup_theme', 'kieu_shop_register_vip_role');

/**
 * Giảm giá 10% tự động cho VIP Customer.
 */
add_action('woocommerce_cart_calculate_fees', function($cart) {
    if (is_admin() && !defined('DOING_AJAX')) return;
    if (!is_user_logged_in()) return;

    $user = wp_get_current_user();
    if (!in_array('vip_customer', (array) $user->roles, true)) return;

    $discount = $cart->get_subtotal() * 0.10;
    if ($discount > 0) {
        $cart->add_fee('Giảm giá VIP (10%)', -$discount);
    }
});

/**
 * Miễn phí vận chuyển cho VIP Customer.
 */
add_filter('woocommerce_shipping_packages', function($packages) {
    if (!is_user_logged_in()) return $packages;
    $user = wp_get_current_user();
    if (!in_array('vip_customer', (array) $user->roles, true)) return $packages;

    // Ép free shipping bằng cách đặt phí = 0
    foreach ($packages as &$package) {
        foreach ($package['rates'] as &$rate) {
            $rate->cost  = 0;
            $rate->taxes = [];
        }
    }
    return $packages;
});

/**
 * Admin: thêm cột "VIP" trong bảng Users.
 */
add_filter('manage_users_columns', function($columns) {
    $columns['vip_status'] = '&#10022; VIP';
    return $columns;
});

add_filter('manage_users_custom_column', function($output, $column_name, $user_id) {
    if ($column_name !== 'vip_status') return $output;
    $user   = get_userdata($user_id);
    $is_vip = in_array('vip_customer', (array) $user->roles, true);
    $nonce  = wp_create_nonce('kieu_toggle_vip_' . $user_id);
    $label  = $is_vip ? '&#10022; VIP' : 'Thường';
    $class  = $is_vip ? 'button button-small' : 'button button-small button-secondary';
    $action = $is_vip ? 'remove' : 'add';
    return "<a href='?kieu_vip_action={$action}&user_id={$user_id}&_wpnonce={$nonce}' class='{$class}'>{$label}</a>";
}, 10, 3);

/**
 * Admin: xử lý toggle VIP khi admin click nút trong Users list.
 */
add_action('admin_init', function() {
    if (!isset($_GET['kieu_vip_action'], $_GET['user_id'], $_GET['_wpnonce'])) return;
    if (!current_user_can('edit_users')) wp_die('Không có quyền.');

    $action  = sanitize_key($_GET['kieu_vip_action']);
    $user_id = intval($_GET['user_id']);
    if (!wp_verify_nonce($_GET['_wpnonce'], 'kieu_toggle_vip_' . $user_id)) wp_die('Nonce sai.');

    $user = new WP_User($user_id);
    if ($action === 'add') {
        $user->add_role('vip_customer');
    } elseif ($action === 'remove') {
        $user->remove_role('vip_customer');
    }

    wp_redirect(admin_url('users.php'));
    exit;
});

/**
 * Xử lý form đăng ký từ page-account.php.
 */
add_action('init', function() {
    if (!isset($_POST['action']) || $_POST['action'] !== 'kieu_register') return;
    if (!wp_verify_nonce($_POST['kieu_register_nonce'] ?? '', 'kieu_register')) return;

    $display_name = sanitize_text_field($_POST['reg_display_name'] ?? '');
    $email        = sanitize_email($_POST['reg_email'] ?? '');
    $password     = $_POST['reg_password'] ?? '';

    // Kiểm tra độ mạnh mật khẩu tối thiểu 8 ký tự
    if (!$email || !$password || strlen($password) < 8) {
        wp_redirect(add_query_arg('reg_error', '1', wp_get_referer()));
        exit;
    }

    $username = explode('@', $email)[0] . '_' . wp_rand(100, 999);
    $user_id  = wp_create_user($username, $password, $email);

    if (!is_wp_error($user_id)) {
        wp_update_user(['ID' => $user_id, 'display_name' => $display_name]);
        $user = new WP_User($user_id);
        $user->set_role('customer');
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);
        wp_redirect(get_permalink(get_page_by_path('tai-khoan')));
        exit;
    }
});

/**
 * Trả về label VIP cho header/cart.
 */
function kieu_is_current_user_vip(): bool {
    if (!is_user_logged_in()) return false;
    $user = wp_get_current_user();
    return in_array('vip_customer', (array) $user->roles, true);
}

// =============================================
// 11. PHASE 04: AI CHATBOT (Tidio) + PAGES
// =============================================

/**
 * Tidio AI Chatbot Integration.
 *
 * Hướng dẫn:
 * 1. Đăng ký tài khoản miễn phí tại https://www.tidio.com
 * 2. Vào Settings → Developer → Public Key
 * 3. Copy Public Key (ví dụ: "abc123xyz") vào hằng số dưới đây
 * 4. Tidio sẽ tự embed và có Lyro AI trả lời tự động
 */
define('KIEU_TIDIO_PUBLIC_KEY', ''); // ← Paste Tidio Public Key vào đây

add_action('wp_footer', function() {
    $key = KIEU_TIDIO_PUBLIC_KEY;
    if (empty($key)) {
        // Fallback: nút chat placeholder (từ front-page.php)
        return;
    }
    // Tidio embed script chính thức
    echo '<script src="//code.tidio.co/' . esc_attr($key) . '.js" async></script>' . "\n";
    // Tuỳ chỉnh màu Tidio theo brand KIỀU
    echo '<script>
    window.tidioChatApiReady = function() {
        tidioChatApi.setColorScheme("custom");
        tidioChatApi.setCustomCssProperty("--tidio-chat-icon-color", "#2D6B7A");
    };
    </script>' . "\n";
});

/**
 * Đăng ký page templates tuỳ chỉnh.
 */
add_filter('theme_page_templates', function($templates) {
    $templates['page-account.php'] = 'Tài khoản KIỀU';
    $templates['page-contact.php'] = 'Liên hệ KIỀU';
    return $templates;
});

/**
 * Xử lý form liên hệ từ page-contact.php.
 */
add_action('admin_post_kieu_contact_form',        'kieu_handle_contact_form');
add_action('admin_post_nopriv_kieu_contact_form', 'kieu_handle_contact_form');

function kieu_handle_contact_form() {
    if (!wp_verify_nonce($_POST['kieu_contact_nonce'] ?? '', 'kieu_contact')) {
        wp_die('Lỗi bảo mật.');
    }

    $name    = sanitize_text_field($_POST['contact_name'] ?? '');
    $email   = sanitize_email($_POST['contact_email'] ?? '');
    $phone   = sanitize_text_field($_POST['contact_phone'] ?? '');
    $subject = sanitize_text_field($_POST['contact_subject'] ?? '');
    $message = sanitize_textarea_field($_POST['contact_message'] ?? '');

    if (!$name || !$email || !$message) {
        wp_redirect(add_query_arg('sent', '0', wp_get_referer()));
        exit;
    }

    $to      = get_option('admin_email');
    $subj    = '[KIỀU] Liên hệ từ ' . $name . ' — ' . $subject;
    $body    = "Từ: {$name} <{$email}>\nSĐT: {$phone}\nChủ đề: {$subject}\n\n{$message}";
    $headers = ['Content-Type: text/plain; charset=UTF-8', 'Reply-To: ' . $email];

    wp_mail($to, $subj, $body, $headers);

    wp_redirect(add_query_arg('sent', '1', wp_get_referer()));
    exit;
}

/**
 * Thêm VNPay trust badges vào footer (sau wp_footer).
 */
add_action('wp_footer', function() {
    // Chỉ hiện badge nếu VNPay plugin đang active
    if (!class_exists('WC_VNPay_Gateway') && !defined('VNPAY_VERSION')) return;
    echo '<div style="display:none" id="vnpay-trust-badge" aria-hidden="true">Powered by VNPay</div>';
}, 100);

// =============================================
// 12. PHASE 05: SEO + PERFORMANCE + SECURITY
// =============================================

/**
 * SEO Open Graph + Twitter Card Meta Tags.
 * Tự động thêm meta tags cho mọi trang — Facebook, Zalo, Google preview.
 */
add_action('wp_head', function() {
    global $post;

    $site_name  = 'KIỀU — Áo Dài Cách Tân';
    $site_url   = home_url('/');
    $logo_url   = get_template_directory_uri() . '/assets/images/kieu-og-image.jpg';

    // Xác định dữ liệu theo loại trang
    if (is_front_page()) {
        $title       = 'KIỀU — Áo Dài Cách Tân Việt Nam';
        $description = 'Khám phá bộ sưu tập áo dài cách tân KIỀU — nơi truyền thống gặp gỡ hiện đại. Thiết kế tinh tế, chất liệu cao cấp.';
        $image       = $logo_url;
        $url         = $site_url;
        $type        = 'website';

    } elseif (is_singular('product') && isset($post)) {
        $product     = wc_get_product($post->ID);
        $title       = get_the_title() . ' — KIỀU';
        $description = wp_strip_all_tags($product ? $product->get_short_description() : get_the_excerpt());
        $image       = get_the_post_thumbnail_url($post->ID, 'large') ?: $logo_url;
        $url         = get_permalink();
        $type        = 'product';

    } elseif (is_singular() && isset($post)) {
        $title       = get_the_title() . ' — KIỀU';
        $description = wp_strip_all_tags(get_the_excerpt() ?: substr(get_the_content(), 0, 160));
        $image       = get_the_post_thumbnail_url($post->ID, 'large') ?: $logo_url;
        $url         = get_permalink();
        $type        = 'article';

    } elseif (is_archive() || is_shop()) {
        $title       = 'Tất Cả Sản Phẩm — KIỀU';
        $description = 'Toàn bộ bộ sưu tập áo dài cách tân KIỀU. Xem hết sản phẩm, chọn size, đặt hàng online.';
        $image       = $logo_url;
        $url         = get_permalink() ?: $site_url;
        $type        = 'website';

    } else {
        return; // không thêm cho các trang còn lại
    }

    // Truncate description
    $description = mb_substr(strip_tags($description), 0, 160);

    echo "\n<!-- KIỀU SEO Meta Tags (Phase 05) -->\n";
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";

    // Open Graph
    echo '<meta property="og:type"        content="' . esc_attr($type)        . '">' . "\n";
    echo '<meta property="og:title"       content="' . esc_attr($title)       . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url"         content="' . esc_url($url)          . '">' . "\n";
    echo '<meta property="og:image"       content="' . esc_url($image)        . '">' . "\n";
    echo '<meta property="og:image:width"  content="1200">' . "\n";
    echo '<meta property="og:image:height" content="630">' . "\n";
    echo '<meta property="og:site_name"   content="' . esc_attr($site_name)   . '">' . "\n";
    echo '<meta property="og:locale"      content="vi_VN">' . "\n";

    // Twitter Card
    echo '<meta name="twitter:card"        content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title"       content="' . esc_attr($title)       . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="twitter:image"       content="' . esc_url($image)        . '">' . "\n";
    echo "<!-- /KIỀU SEO -->\n\n";
}, 5);

/**
 * JSON-LD Schema Markup — Organization + Product.
 * Google sẽ hiện thêm thông tin phong phú trong kết quả tìm kiếm.
 */
add_action('wp_head', function() {
    global $post;

    // Organization schema (mọi trang)
    $org = [
        '@context' => 'https://schema.org',
        '@type'    => 'ClothingStore',
        'name'     => 'KIỀU',
        'url'      => home_url('/'),
        'logo'     => get_template_directory_uri() . '/assets/images/kieu-og-image.jpg',
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'telephone'   => '+84-900-000-000',
            'contactType' => 'customer service',
            'availableLanguage' => 'Vietnamese',
        ],
        'address' => [
            '@type'           => 'PostalAddress',
            'addressCountry'  => 'VN',
            'addressLocality' => 'TP. Hồ Chí Minh',
        ],
        'sameAs' => [
            'https://www.instagram.com/',
            'https://www.facebook.com/',
        ],
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";

    // Product schema (trang chi tiết sản phẩm)
    if (!is_singular('product') || !isset($post)) return;
    $product = wc_get_product($post->ID);
    if (!$product) return;

    $availability = $product->is_in_stock()
        ? 'https://schema.org/InStock'
        : 'https://schema.org/OutOfStock';

    $prod_schema = [
        '@context'    => 'https://schema.org/',
        '@type'       => 'Product',
        'name'        => get_the_title(),
        'description' => wp_strip_all_tags($product->get_short_description()),
        'image'       => get_the_post_thumbnail_url($post->ID, 'large') ?: '',
        'brand'       => ['@type' => 'Brand', 'name' => 'KIỀU'],
        'sku'         => $product->get_sku() ?: (string) $product->get_id(),
        'offers'      => [
            '@type'         => 'Offer',
            'url'           => get_permalink(),
            'priceCurrency' => get_woocommerce_currency(),
            'price'         => $product->get_price(),
            'availability'  => $availability,
            'seller'        => ['@type' => 'Organization', 'name' => 'KIỀU'],
        ],
    ];

    $rating = $product->get_average_rating();
    $count  = $product->get_review_count();
    if ($rating > 0 && $count > 0) {
        $prod_schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => number_format((float) $rating, 1),
            'reviewCount' => (int) $count,
        ];
    }

    echo '<script type="application/ld+json">' . wp_json_encode($prod_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>\n";
}, 6);

/**
 * Performance: xoá scripts không cần thiết của WordPress.
 */
add_action('init', function() {
    // Bỏ emoji scripts (tiết kiệm ~5KB)
    remove_action('wp_head',             'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles',     'print_emoji_styles');
    remove_action('admin_print_styles',  'print_emoji_styles');
    remove_filter('the_content_feed',    'wp_staticize_emoji');
    remove_filter('comment_text_rss',    'wp_staticize_emoji');
    remove_filter('wp_mail',             'wp_staticize_emoji_for_email');

    // Bỏ generator tag (bảo mật)
    remove_action('wp_head', 'wp_generator');

    // Bỏ RSD link (không cần)
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
});

/**
 * Performance: lazy load iframes (YouTube, Google Maps embed).
 */
add_filter('the_content', function($content) {
    return str_replace('<iframe ', '<iframe loading="lazy" ', $content);
});

/**
 * Security: Tắt XML-RPC (nguồn brute-force phổ biến).
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Security: Ẩn phiên bản WordPress khỏi mọi nơi.
 * (wp_generator đã được remove ở dòng 695 trong init hook)
 */
add_filter('the_generator', '__return_empty_string');
add_filter('style_loader_src',  'kieu_shop_remove_version_query', 15, 1);
add_filter('script_loader_src', 'kieu_shop_remove_version_query', 15, 1);

function kieu_shop_remove_version_query($src) {
    if ($src && strpos($src, 'ver=') !== false) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}

/**
 * Performance: Cache headers cho static assets.
 */
add_action('send_headers', function() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
});

// =============================================
// 13. VIP AUTO-THRESHOLD (3M VND / 12 THÁNG)
// =============================================

define('KIEU_VIP_THRESHOLD', 3000000); // 3,000,000 VND

/**
 * Tính tổng chi tiêu của user trong 12 tháng gần nhất.
 */
function kieu_get_annual_spend(int $user_id): float {
    $one_year_ago = date('Y-m-d H:i:s', strtotime('-12 months'));
    $orders = wc_get_orders([
        'customer'   => $user_id,
        'status'     => ['completed'],
        'date_after' => $one_year_ago,
        'limit'      => -1,
        'return'     => 'objects',
    ]);
    $total = 0.0;
    foreach ($orders as $order) {
        $total += (float) $order->get_total();
    }
    return $total;
}

/**
 * Khi đơn hàng hoàn thành → kiểm tra và tự động nâng VIP.
 */
add_action('woocommerce_order_status_completed', function(int $order_id) {
    $order   = wc_get_order($order_id);
    if (!$order) return;
    $user_id = $order->get_customer_id();
    if (!$user_id) return;

    $spend = kieu_get_annual_spend($user_id);
    $user  = new WP_User($user_id);

    $already_vip = in_array('vip_customer', (array) $user->roles, true);

    if ($spend >= KIEU_VIP_THRESHOLD && !$already_vip) {
        $user->add_role('vip_customer');
        // Gửi email chúc mừng VIP
        $name = $user->display_name ?: $user->user_email;
        wp_mail(
            $user->user_email,
            'Chúc mừng! Anh/chị đã trở thành Thành Viên VIP của KIỀU ✦',
            "Chào {$name},\n\nCảm ơn sự tin yêu của anh/chị. "
            . "Với mức chi tiêu trong 12 tháng, anh/chị đã đủ điều kiện hạng VIP của KIỀU!\n\n"
            . "Quyền lợi VIP:\n"
            . "✦ Giảm giá 10% tất cả sản phẩm\n"
            . "✦ Miễn phí vận chuyển\n"
            . "✦ Hỗ trợ ưu tiên trực tiếp\n"
            . "✦ Thông báo bộ sưu tập mới sớm 24 giờ\n\n"
            . "Trân trọng,\nKIỀU",
            ['Content-Type: text/plain; charset=UTF-8']
        );
    }
});

/**
 * WP Cron hàng ngày: kiểm tra và hạ cấp VIP nếu chi tiêu < ngưỡng.
 */
add_action('kieu_vip_daily_check', function() {
    $vip_users = get_users(['role' => 'vip_customer', 'fields' => ['ID', 'display_name', 'user_email']]);
    foreach ($vip_users as $u) {
        // Bỏ qua admin
        $wp_user = new WP_User($u->ID);
        if ($wp_user->has_cap('manage_options')) continue;

        $spend = kieu_get_annual_spend((int) $u->ID);
        if ($spend < KIEU_VIP_THRESHOLD) {
            $wp_user->remove_role('vip_customer');
            // Thông báo hạ cấp
            wp_mail(
                $u->user_email,
                'Thông báo về hạng thành viên KIỀU',
                "Chào {$u->display_name},\n\n"
                . "Do tổng chi tiêu trong 12 tháng qua chưa đạt mức 3.000.000₫, "
                . "hạng VIP của anh/chị đã tạm thời được điều chỉnh về Thành viên thường.\n\n"
                . "Anh/chị hoàn toàn có thể quay lại hạng VIP bằng cách "
                . "tiếp tục mua sắm tại KIỀU.\n\n"
                . "Chân thành cảm ơn,\nKIỀU",
                ['Content-Type: text/plain; charset=UTF-8']
            );
        }
    }
});

// Lên lịch WP Cron nếu chưa lên
add_action('wp', function() {
    if (!wp_next_scheduled('kieu_vip_daily_check')) {
        wp_schedule_event(time(), 'daily', 'kieu_vip_daily_check');
    }
});

// Huỷ lịch khi deactivate theme
add_action('switch_theme', function() {
    wp_clear_scheduled_hook('kieu_vip_daily_check');
});

// =============================================
// 14. GOOGLE SIGN-IN (Plugin Integration)
// =============================================

/**
 * Đăng ký template trang VIP Contact.
 * (Thêm vào danh sách template đã có trong phase 04)
 */
add_filter('theme_page_templates', function($templates) {
    $templates['page-vip-contact.php'] = 'Hỗ Trợ VIP KIỀU';
    return $templates;
});

/**
 * Thêm nút "Tiếp tục với Google" vào form đăng nhập WordPress.
 *
 * YÊU CẦU plugin: "Login with Google" (nextendweb/nextend-social-login)
 * hoặc "Google Site Kit" / "WP Social Login"
 *
 * Nếu plugin active → hiện nút Google styled theo KIỀU brand.
 * Nếu chưa cài plugin → hiện thông báo hướng dẫn (chỉ trong admin).
 */
add_action('login_form', 'kieu_google_login_button');
add_action('woocommerce_login_form_end', 'kieu_google_login_button');

function kieu_google_login_button() {
    // Kiểm tra Nextend Social Login plugin
    if (class_exists('Nextend\SocialLogin\SocialLogin') || defined('NEXTEND_SOCIAL_LOGIN_VERSION')) {
        // Plugin đã active — nó sẽ tự render nút, ta chỉ thêm divider
        echo '<div class="kieu-or-divider"><span>HOẶC</span></div>';
        return;
    }

    // Plugin chưa cài — hiện nút placeholder
    echo '<div class="kieu-or-divider"><span>HOẶC</span></div>';
    echo '<a href="' . esc_url(admin_url('plugin-install.php?s=nextend+social+login&tab=search&type=term')) . '"
            class="kieu-google-btn"
            title="Cài plugin Nextend Social Login để kích hoạt đăng nhập Google"
            ' . (is_admin() ? '' : 'style="pointer-events:none;opacity:0.6"') . '>
        <svg width="18" height="18" viewBox="0 0 18 18" aria-hidden="true">
            <path fill="#4285F4" d="M17.64 9.2c0-.637-.057-1.251-.164-1.84H9v3.481h4.844c-.209 1.125-.843 2.078-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615z"/>
            <path fill="#34A853" d="M9 18c2.43 0 4.467-.806 5.956-2.184l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711H.957v2.332A8.997 8.997 0 0 0 9 18z"/>
            <path fill="#FBBC05" d="M3.964 10.706A5.41 5.41 0 0 1 3.682 9c0-.593.102-1.17.282-1.706V4.962H.957A8.996 8.996 0 0 0 0 9c0 1.452.348 2.827.957 4.038l3.007-2.332z"/>
            <path fill="#EA4335" d="M9 3.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C13.463.891 11.426 0 9 0A8.997 8.997 0 0 0 .957 4.962L3.964 6.294C4.672 4.167 6.656 3.58 9 3.58z"/>
        </svg>
        Tiếp tục với Google
    </a>';
}

/**
 * Thêm thông báo nhắc admin cài plugin Google Sign-In.
 */
add_action('admin_notices', function() {
    if (defined('NEXTEND_SOCIAL_LOGIN_VERSION') || !current_user_can('install_plugins')) return;
    echo '<div class="notice notice-info is-dismissible">
        <p><strong>KIỀU Theme:</strong> Để kích hoạt nút "Đăng nhập bằng Google", hãy cài plugin
        <a href="' . esc_url(admin_url('plugin-install.php?s=nextend+social+login&tab=search&type=term')) . '">
        Nextend Social Login</a> → chọn Google provider → nhập Google Client ID/Secret.</p>
    </div>';
});

// =============================================
// 15. CUSTOM ADMIN PANEL + RBAC
// =============================================

/**
 * Đăng ký roles và capabilities cho hệ thống Admin tùy chỉnh.
 */
function kieu_register_admin_roles() {
    // Admin KIỀU — full access
    if (!get_role('kieu_admin')) {
        add_role('kieu_admin', 'Admin KIỀU', [
            'read'                  => true,
            'upload_files'          => true,
            'edit_posts'            => true,
            'edit_others_posts'     => true,
            'delete_posts'          => true,
            'publish_posts'         => true,
            'manage_options'        => false, // Không cho toàn quyền WP
            // Custom caps
            'kieu_view_admin_panel' => true,
            'kieu_edit_homepage'    => true,
            'kieu_edit_products'    => true,
            'kieu_edit_lookbook'    => true,
            'kieu_edit_banners'     => true,
            'kieu_manage_media'     => true,
            'kieu_manage_settings'  => true,
            'kieu_vip_support'      => true,
        ]);
    }

    // Sub-Admin — Lookbook + Banner + VIP Support only
    if (!get_role('kieu_sub_admin')) {
        add_role('kieu_sub_admin', 'Sub-Admin KIỀU', [
            'read'                  => true,
            'upload_files'          => true,
            'edit_posts'            => true,
            'publish_posts'         => true,
            // Custom caps
            'kieu_view_admin_panel' => true,
            'kieu_edit_homepage'    => false,
            'kieu_edit_products'    => false,
            'kieu_edit_lookbook'    => true,
            'kieu_edit_banners'     => true,
            'kieu_manage_media'     => true,
            'kieu_manage_settings'  => false,
            'kieu_vip_support'      => true,
        ]);
    }
}
add_action('init', 'kieu_register_admin_roles');

/**
 * Helper: kiểm tra user có thể xem admin panel không.
 */
function kieu_can_access_admin(): bool {
    if (!is_user_logged_in()) return false;
    $user = wp_get_current_user();
    return $user->has_cap('kieu_view_admin_panel') || $user->has_cap('manage_options');
}

/**
 * Đăng ký template admin panel.
 */
add_filter('theme_page_templates', function($templates) {
    $templates['page-admin.php'] = 'Admin Panel KIỀU';
    return $templates;
});

// ── AJAX: Lưu option (text/settings) ───────────────────────────────────
add_action('wp_ajax_kieu_save_option', function() {
    check_ajax_referer('kieu_admin_nonce', 'nonce');
    $user = wp_get_current_user();
    $key  = sanitize_key($_POST['option_key'] ?? '');
    $val  = sanitize_textarea_field(wp_unslash($_POST['option_value'] ?? ''));

    // Whitelist các key được phép sửa
    $allowed_keys = [
        'kieu_hero_title', 'kieu_hero_subtitle', 'kieu_hero_image_1',
        'kieu_hero_image_2', 'kieu_hero_image_3',
        'kieu_intro_text', 'kieu_intro_image',
        'kieu_banner_text', 'kieu_banner_image',
        'kieu_phone', 'kieu_address', 'kieu_facebook', 'kieu_instagram',
        'kieu_bank_name', 'kieu_bank_number', 'kieu_bank_holder',
        'kieu_size_guide_image', 'kieu_size_guide_note',
    ];

    // Kiểm tra capability theo key
    $homepage_keys = ['kieu_hero_title','kieu_hero_subtitle','kieu_hero_image_1','kieu_hero_image_2','kieu_hero_image_3','kieu_intro_text','kieu_intro_image'];
    $settings_keys = ['kieu_phone','kieu_address','kieu_facebook','kieu_instagram','kieu_bank_name','kieu_bank_number','kieu_bank_holder','kieu_size_guide_image','kieu_size_guide_note'];

    if (!in_array($key, $allowed_keys, true)) {
        wp_send_json_error(['message' => 'Key không hợp lệ.']);
    }
    if (in_array($key, $homepage_keys) && !$user->has_cap('kieu_edit_homepage')) {
        wp_send_json_error(['message' => 'Không có quyền sửa homepage.']);
    }
    if (in_array($key, $settings_keys) && !$user->has_cap('kieu_manage_settings')) {
        wp_send_json_error(['message' => 'Không có quyền sửa cài đặt.']);
    }

    update_option($key, $val);
    wp_send_json_success(['message' => 'Đã lưu!', 'key' => $key]);
});

// ── AJAX: Upload media ───────────────────────────────────────────────────
add_action('wp_ajax_kieu_upload_media', function() {
    check_ajax_referer('kieu_admin_nonce', 'nonce');
    if (!current_user_can('kieu_manage_media') && !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Không có quyền upload.']);
    }

    if (empty($_FILES['file'])) {
        wp_send_json_error(['message' => 'Không có file.']);
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attachment_id = media_handle_upload('file', 0);
    if (is_wp_error($attachment_id)) {
        wp_send_json_error(['message' => $attachment_id->get_error_message()]);
    }

    wp_send_json_success([
        'attachment_id' => $attachment_id,
        'url'           => wp_get_attachment_url($attachment_id),
        'thumb'         => wp_get_attachment_image_url($attachment_id, 'thumbnail'),
    ]);
});

// ── AJAX: Lưu / tạo bài Lookbook ─────────────────────────────────────────
add_action('wp_ajax_kieu_save_lookbook', function() {
    check_ajax_referer('kieu_admin_nonce', 'nonce');
    if (!current_user_can('kieu_edit_lookbook') && !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Không có quyền sửa Lookbook.']);
    }

    $post_id      = intval($_POST['post_id'] ?? 0);
    $title        = sanitize_text_field(wp_unslash($_POST['title'] ?? ''));
    $content      = wp_kses_post(wp_unslash($_POST['content'] ?? ''));
    $thumbnail_id = intval($_POST['thumbnail_id'] ?? 0);

    if (!$title) wp_send_json_error(['message' => 'Tiêu đề không được trống.']);

    $data = [
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'lookbook',
    ];

    if ($post_id) {
        $data['ID'] = $post_id;
        $result = wp_update_post($data, true);
    } else {
        $result = wp_insert_post($data, true);
    }

    if (is_wp_error($result)) {
        wp_send_json_error(['message' => $result->get_error_message()]);
    }

    if ($thumbnail_id) set_post_thumbnail($result, $thumbnail_id);

    wp_send_json_success(['message' => 'Đã lưu!', 'post_id' => $result]);
});

// ── AJAX: Xóa bài Lookbook ────────────────────────────────────────────────
add_action('wp_ajax_kieu_delete_lookbook', function() {
    check_ajax_referer('kieu_admin_nonce', 'nonce');
    if (!current_user_can('kieu_edit_lookbook') && !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Không có quyền xóa.']);
    }
    $post_id = intval($_POST['post_id'] ?? 0);
    if (!$post_id) wp_send_json_error(['message' => 'Thiếu post_id.']);

    $result = wp_trash_post($post_id);
    if (!$result) wp_send_json_error(['message' => 'Không xóa được.']);

    wp_send_json_success(['message' => 'Đã xóa bài lookbook.']);
});

// ── AJAX: Thêm ghi chú CSKH VIP ──────────────────────────────────────────
add_action('wp_ajax_kieu_save_vip_note', function() {
    check_ajax_referer('kieu_admin_nonce', 'nonce');
    if (!current_user_can('kieu_vip_support') && !current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Không có quyền.']);
    }
    $user_id = intval($_POST['user_id'] ?? 0);
    $note    = sanitize_textarea_field(wp_unslash($_POST['note'] ?? ''));
    if (!$user_id) wp_send_json_error(['message' => 'Thiếu user_id.']);

    $existing = get_user_meta($user_id, 'kieu_vip_notes', true) ?: [];
    $existing[] = [
        'note'    => $note,
        'by'      => wp_get_current_user()->display_name,
        'time'    => current_time('mysql'),
    ];
    update_user_meta($user_id, 'kieu_vip_notes', $existing);

    wp_send_json_success(['message' => 'Đã lưu ghi chú.']);
});

/**
 * Enqueue admin panel assets.
 */
add_action('wp_enqueue_scripts', function() {
    // Chỉ load khi user có quyền xem admin panel
    if (!kieu_can_access_admin()) return;

    // Kiểm tra có phải đang xem page-admin.php
    global $post;
    if (!$post || get_page_template_slug($post->ID) !== 'page-admin.php') return;

    wp_enqueue_style(
        'kieu-admin-panel',
        get_template_directory_uri() . '/assets/css/style-admin.css',
        ['kieu-shop-style'],
        wp_get_theme()->get('Version')
    );
    wp_enqueue_media(); // WordPress Media Library
    wp_enqueue_script(
        'kieu-admin-js',
        get_template_directory_uri() . '/assets/js/admin.js',
        ['jquery'],
        wp_get_theme()->get('Version'),
        true
    );
    wp_localize_script('kieu-admin-js', 'kieuAdminData', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('kieu_admin_nonce'),
        'siteUrl' => home_url('/'),
    ]);
});

// ── AJAX: Lưu sản phẩm WooCommerce ──────────────────────────────────────
add_action('wp_ajax_kieu_save_product', function() {
    check_ajax_referer('kieu_admin_nonce', 'nonce');

    $user = wp_get_current_user();
    if (!$user->has_cap('kieu_edit_products') && !$user->has_cap('manage_options')) {
        wp_send_json_error(['message' => 'Không có quyền sửa sản phẩm.']);
    }

    $product_id = intval($_POST['product_id'] ?? 0);

    // Nếu product_id = 0 → tạo mới
    if ($product_id === 0) {
        $product = new WC_Product_Simple();
    } else {
        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error(['message' => 'Không tìm thấy sản phẩm.']);
        }
    }

    // Cập nhật thông tin sản phẩm
    $name       = sanitize_text_field(wp_unslash($_POST['name'] ?? ''));
    $price      = sanitize_text_field($_POST['price'] ?? '');
    $sale_price = sanitize_text_field($_POST['sale_price'] ?? '');
    $stock      = isset($_POST['stock']) && $_POST['stock'] !== '' ? intval($_POST['stock']) : null;
    $desc       = sanitize_textarea_field(wp_unslash($_POST['description'] ?? ''));
    $status     = sanitize_key($_POST['status'] ?? 'publish');
    $thumb_id   = intval($_POST['thumbnail_id'] ?? 0);

    if ($name) $product->set_name($name);
    $product->set_regular_price($price);
    $product->set_sale_price($sale_price !== '' ? $sale_price : '');
    $product->set_short_description($desc);
    $product->set_status(in_array($status, ['publish','draft'], true) ? $status : 'publish');

    // Stock
    if ($stock !== null) {
        $product->set_manage_stock(true);
        $product->set_stock_quantity($stock);
    }

    // Ảnh đại diện
    if ($thumb_id) {
        $product->set_image_id($thumb_id);
    }

    $saved = $product->save();
    if (!$saved || is_wp_error($saved)) {
        wp_send_json_error(['message' => 'Không lưu được sản phẩm.']);
    }

    wp_send_json_success([
        'message'    => 'Đã lưu sản phẩm: ' . $product->get_name(),
        'product_id' => $product->get_id(),
        'thumb_url'  => wp_get_attachment_image_url($product->get_image_id(), 'thumbnail'),
        'price'      => number_format((float)$product->get_regular_price(), 0, ',', '.'),
        'sale'       => number_format((float)$product->get_sale_price(), 0, ',', '.'),
    ]);
});

