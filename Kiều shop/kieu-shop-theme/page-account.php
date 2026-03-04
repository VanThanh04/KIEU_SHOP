<?php
/**
 * page-account.php — KIỀU
 * Template trang tài khoản: Đăng nhập / Đăng ký / Dashboard với tabs
 * Assign page slug: "tai-khoan" in WordPress Admin → Pages → Add New
 */

get_header();
?>

<main class="kieu-account-main">

<?php if (is_user_logged_in()):
    $user       = wp_get_current_user();
    $is_vip     = in_array('vip_customer', (array) $user->roles, true);
    $active_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'dashboard';
?>

<!-- ====== ACCOUNT DASHBOARD ====== -->
<div class="kieu-account-wrap">

    <!-- Sidebar -->
    <aside class="kieu-account-sidebar">

        <!-- Avatar + greeting -->
        <div class="kieu-account-avatar">
            <?php echo get_avatar($user->ID, 72, '', $user->display_name, ['class' => 'account-avatar-img']); ?>
            <div>
                <p class="account-greeting">Xin chào,</p>
                <p class="account-name"><?php echo esc_html($user->display_name); ?></p>
                <?php if ($is_vip): ?>
                    <span class="vip-badge">&#10022; THÀNH VIÊN VIP</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Nav tabs -->
        <nav class="kieu-account-nav">
            <?php
            $tabs = [
                'dashboard'  => ['icon' => 'fa-house',          'label' => 'Tổng quan'],
                'orders'     => ['icon' => 'fa-bag-shopping',   'label' => 'Đơn hàng'],
                'addresses'  => ['icon' => 'fa-location-dot',   'label' => 'Địa chỉ'],
                'account'    => ['icon' => 'fa-user-pen',        'label' => 'Thông tin'],
            ];
            foreach ($tabs as $slug => $tab):
                $url    = add_query_arg('tab', $slug, get_permalink());
                $active = $active_tab === $slug ? ' is-active' : '';
            ?>
            <a href="<?php echo esc_url($url); ?>" class="kieu-account-nav-item<?php echo $active; ?>">
                <i class="fa-solid <?php echo $tab['icon']; ?>"></i>
                <?php echo esc_html($tab['label']); ?>
            </a>
            <?php endforeach; ?>
            <?php if ($is_vip): ?>
            <a href="<?php echo esc_url(add_query_arg('tab', 'vip', get_permalink())); ?>"
               class="kieu-account-nav-item kieu-vip-tab<?php echo $active_tab === 'vip' ? ' is-active' : ''; ?>">
                <i class="fa-solid fa-crown"></i>
                Quyền lợi VIP
            </a>
            <?php endif; ?>
            <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="kieu-account-nav-item kieu-logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                Đăng xuất
            </a>
        </nav>
    </aside>

    <!-- Main content panels -->
    <div class="kieu-account-content">

        <?php if ($active_tab === 'dashboard'): ?>
        <div class="kieu-account-panel">
            <h2 class="account-panel-title">Tổng Quan</h2>
            <div class="gold-divider" style="justify-content:flex-start;margin-bottom:1.5rem"><span>&#10022;</span></div>
            <p style="color:var(--color-text-muted); margin-bottom:2rem;">
                Chào mừng <strong><?php echo esc_html($user->display_name); ?></strong> quay trở lại KIỀU!
            </p>
            <!-- Quick stats -->
            <div class="account-stats-grid">
                <?php
                $order_count = wc_get_customer_order_count($user->ID);
                $total_spent = wc_get_customer_total_spent($user->ID);
                ?>
                <div class="account-stat-card">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="stat-number"><?php echo $order_count; ?></span>
                    <span class="stat-label">Đơn hàng</span>
                </div>
                <div class="account-stat-card">
                    <i class="fa-solid fa-coins"></i>
                    <span class="stat-number"><?php echo wc_price($total_spent); ?></span>
                    <span class="stat-label">Tổng chi tiêu</span>
                </div>
                <div class="account-stat-card">
                    <i class="fa-solid fa-star" style="color:var(--color-<?php echo $is_vip ? 'gold' : 'border'; ?>)"></i>
                    <span class="stat-number"><?php echo $is_vip ? 'VIP' : 'Thành viên'; ?></span>
                    <span class="stat-label">Hạng thành viên</span>
                </div>
            </div>
        </div>

        <?php elseif ($active_tab === 'orders'): ?>
        <div class="kieu-account-panel">
            <h2 class="account-panel-title">Đơn Hàng Của Tôi</h2>
            <div class="gold-divider" style="justify-content:flex-start;margin-bottom:1.5rem"><span>&#10022;</span></div>
            <?php
            // Dùng WooCommerce shortcode orders table
            $orders = wc_get_orders(['customer' => $user->ID, 'limit' => 10, 'orderby' => 'date', 'order' => 'DESC']);
            if ($orders): ?>
            <table class="kieu-orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><strong>#<?php echo $order->get_order_number(); ?></strong></td>
                        <td><?php echo wc_format_datetime($order->get_date_created()); ?></td>
                        <td><span class="order-status status-<?php echo esc_attr($order->get_status()); ?>"><?php echo wc_get_order_status_name($order->get_status()); ?></span></td>
                        <td><?php echo $order->get_formatted_order_total(); ?></td>
                        <td><a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="btn-view-order">Xem</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color:var(--color-text-muted);">Anh/chị chưa có đơn hàng nào. <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" style="color:var(--color-primary)">Mua sắm ngay →</a></p>
            <?php endif; ?>
        </div>

        <?php elseif ($active_tab === 'addresses'): ?>
        <div class="kieu-account-panel">
            <h2 class="account-panel-title">Địa Chỉ Giao Hàng</h2>
            <div class="gold-divider" style="justify-content:flex-start;margin-bottom:1.5rem"><span>&#10022;</span></div>
            <?php woocommerce_account_edit_address('billing'); ?>
        </div>

        <?php elseif ($active_tab === 'account'): ?>
        <div class="kieu-account-panel">
            <h2 class="account-panel-title">Thông Tin Tài Khoản</h2>
            <div class="gold-divider" style="justify-content:flex-start;margin-bottom:1.5rem"><span>&#10022;</span></div>
            <?php
            // Form thay đổi thông tin + mật khẩu
            do_action('woocommerce_edit_account_form_start');
            ?>
            <form class="kieu-edit-account-form" method="post">
                <div class="form-row">
                    <label>Tên hiển thị</label>
                    <input type="text" name="display_name" value="<?php echo esc_attr($user->display_name); ?>">
                </div>
                <div class="form-row">
                    <label>Email</label>
                    <input type="email" name="account_email" value="<?php echo esc_attr($user->user_email); ?>">
                </div>
                <hr style="border-color:var(--color-border);margin:1.5rem 0">
                <p style="font-size:0.8rem;color:var(--color-text-muted);margin-bottom:1rem">Để trống nếu không muốn đổi mật khẩu</p>
                <div class="form-row">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="password_1" autocomplete="new-password">
                </div>
                <div class="form-row">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="password_2" autocomplete="new-password">
                </div>
                <?php wp_nonce_field('save_account_details', 'save-account-nonce'); ?>
                <input type="hidden" name="action" value="save_account_details">
                <button type="submit" class="btn-primary" style="margin-top:1.5rem">Lưu Thay Đổi</button>
            </form>
        </div>

        <?php elseif ($active_tab === 'vip' && $is_vip): ?>
        <div class="kieu-account-panel">
            <h2 class="account-panel-title" style="color:var(--color-gold)">&#10022; Quyền Lợi VIP</h2>
            <div class="gold-divider" style="justify-content:flex-start;margin-bottom:1.5rem"><span>&#10022;</span></div>
            <div class="vip-benefits-grid">
                <div class="vip-benefit-item">
                    <i class="fa-solid fa-percent"></i>
                    <h4>Giá Ưu Đãi VIP</h4>
                    <p>Giảm <strong>10%</strong> tất cả sản phẩm, áp dụng tự động.</p>
                </div>
                <div class="vip-benefit-item">
                    <i class="fa-solid fa-truck-fast"></i>
                    <h4>Miễn Phí Vận Chuyển</h4>
                    <p>Free ship toàn quốc không giới hạn đơn hàng.</p>
                </div>
                <div class="vip-benefit-item">
                    <i class="fa-solid fa-bell"></i>
                    <h4>Ưu Tiên Thông Báo</h4>
                    <p>Nhận thông báo bộ sưu tập mới trước 24 giờ.</p>
                </div>
                <div class="vip-benefit-item">
                    <i class="fa-solid fa-headset"></i>
                    <h4>Hỗ Trợ Riêng</h4>
                    <p>Đường dây hỗ trợ VIP ưu tiên, phản hồi trong 2 giờ.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- .kieu-account-content -->
</div><!-- .kieu-account-wrap -->

<?php else: // Chưa đăng nhập — Hiện form Login/Register ?>

<!-- ====== LOGIN / REGISTER TABS ====== -->
<div class="kieu-auth-wrap">
    <div class="kieu-auth-box">
        <p class="section-subtitle">— Tài khoản —</p>
        <h2 class="section-title">Chào Mừng Đến KIỀU</h2>
        <div class="gold-divider"><span>&#10022;</span></div>

        <!-- Tab switcher -->
        <div class="auth-tabs">
            <button class="auth-tab-btn is-active" id="tabLoginBtn" onclick="kieuAuth.switchTab('login')">
                Đăng Nhập
            </button>
            <button class="auth-tab-btn" id="tabRegisterBtn" onclick="kieuAuth.switchTab('register')">
                Đăng Ký
            </button>
        </div>

        <!-- Login form -->
        <div class="auth-panel" id="authPanelLogin">
            <?php
            $args = [
                'echo'           => true,
                'redirect'       => get_permalink(),
                'form_id'        => 'loginform',
                'label_username' => 'Email / Tên đăng nhập',
                'label_password' => 'Mật khẩu',
                'label_remember' => 'Ghi nhớ đăng nhập',
                'label_log_in'   => 'Đăng Nhập',
                'remember'       => true,
            ];
            wp_login_form($args);
            // Nút Google Sign-In (Nextend Social Login)
            if (function_exists('kieu_google_login_button')) {
                kieu_google_login_button();
            }
            ?>
            <p class="auth-forgot">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">Quên mật khẩu?</a>
            </p>
        </div>

        <!-- Register form -->
        <div class="auth-panel" id="authPanelRegister" style="display:none">
            <form method="post" class="kieu-register-form">
                <div class="form-row">
                    <label>Họ và tên</label>
                    <input type="text" name="reg_display_name" required placeholder="Nguyễn Thị KIỀU">
                </div>
                <div class="form-row">
                    <label>Email</label>
                    <input type="email" name="reg_email" required placeholder="email@example.com">
                </div>
                <div class="form-row">
                    <label>Mật khẩu</label>
                    <input type="password" name="reg_password" required placeholder="Tối thiểu 8 ký tự">
                </div>
                <?php wp_nonce_field('kieu_register', 'kieu_register_nonce'); ?>
                <input type="hidden" name="action" value="kieu_register">
                <button type="submit" class="btn-primary" style="width:100%;margin-top:1rem">Tạo Tài Khoản</button>
                <p style="font-size:0.75rem;color:var(--color-text-muted);margin-top:1rem;text-align:center">
                    Bằng cách đăng ký, bạn đồng ý với <a href="#">Điều khoản dịch vụ</a> của KIỀU.
                </p>
            </form>
        </div>

    </div><!-- .kieu-auth-box -->
</div>

<script>
window.kieuAuth = {
    switchTab(t) {
        document.getElementById('authPanelLogin').style.display    = t === 'login'    ? '' : 'none';
        document.getElementById('authPanelRegister').style.display = t === 'register' ? '' : 'none';
        document.getElementById('tabLoginBtn').classList.toggle('is-active',    t === 'login');
        document.getElementById('tabRegisterBtn').classList.toggle('is-active', t === 'register');
    }
};
</script>

<?php endif; ?>

</main>

<?php get_footer(); ?>
