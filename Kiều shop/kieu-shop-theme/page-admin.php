<?php
/**
 * Template Name: Admin Panel KIỀU
 * Template Post Type: page
 *
 * page-admin.php — Bảng quản trị nội dung frontend KIỀU
 * Slug: quan-tri | URL: /quan-tri
 */

// Bảo vệ: redirect nếu không có quyền
if (!kieu_can_access_admin()) {
    wp_redirect(home_url('/tai-khoan'));
    exit;
}

$current_user = wp_get_current_user();
$is_super     = $current_user->has_cap('manage_options') || $current_user->has_cap('kieu_edit_homepage');
$is_sub       = $current_user->has_cap('kieu_view_admin_panel');
$active_tab   = sanitize_key($_GET['tab'] ?? 'dashboard');

// Load WordPress Media Library (phải gọi trước get_header)
wp_enqueue_media();

// Load admin-specific CSS + JS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'kieu-admin-panel',
        get_template_directory_uri() . '/assets/css/style-admin.css',
        [],
        wp_get_theme()->get('Version')
    );
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
}, 5);


// Lấy danh sách VIP users (cho tab VIP Support)
$vip_users = [];
if ($current_user->has_cap('kieu_vip_support') || $is_super) {
    $vip_users = get_users(['role' => 'vip_customer', 'orderby' => 'display_name']);
}

// Lấy danh sách lookbook posts
$lookbooks = get_posts(['post_type' => 'lookbook', 'numberposts' => -1, 'post_status' => 'publish,draft']);

get_header();
?>

<div class="kieu-admin-wrap">

    <!-- ===== SIDEBAR ===== -->
    <aside class="kieu-admin-sidebar" id="kieuAdminSidebar">
        <div class="kad-brand">
            <div class="kad-logo">KIỀU</div>
            <p class="kad-role"><?php echo esc_html($current_user->has_cap('kieu_edit_homepage') ? 'Admin' : 'Sub-Admin'); ?></p>
        </div>

        <nav class="kad-nav">
            <a href="?tab=dashboard" class="kad-nav-item <?php echo $active_tab === 'dashboard' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i> Tổng quan
            </a>

            <?php if ($is_super || $current_user->has_cap('kieu_edit_homepage')): ?>
            <a href="?tab=homepage" class="kad-nav-item <?php echo $active_tab === 'homepage' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-house"></i> Trang chủ
            </a>
            <?php endif; ?>

            <?php if ($is_super || $current_user->has_cap('kieu_edit_lookbook')): ?>
            <a href="?tab=lookbook" class="kad-nav-item <?php echo $active_tab === 'lookbook' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-camera-retro"></i> Lookbook
            </a>
            <?php endif; ?>

            <?php if ($is_super || $current_user->has_cap('kieu_edit_banners')): ?>
            <a href="?tab=banners" class="kad-nav-item <?php echo $active_tab === 'banners' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-rectangle-ad"></i> Banner & Media
            </a>
            <?php endif; ?>

            <?php if ($is_super || $current_user->has_cap('kieu_edit_products')): ?>
            <a href="?tab=products" class="kad-nav-item <?php echo $active_tab === 'products' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-shirt"></i> Sản phẩm
            </a>
            <?php endif; ?>

            <?php if ($is_super || $current_user->has_cap('kieu_vip_support')): ?>
            <a href="?tab=vip" class="kad-nav-item <?php echo $active_tab === 'vip' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-crown"></i> VIP Support
            </a>
            <?php endif; ?>

            <?php if ($is_super || $current_user->has_cap('kieu_manage_settings')): ?>
            <a href="?tab=settings" class="kad-nav-item <?php echo $active_tab === 'settings' ? 'is-active' : ''; ?>">
                <i class="fa-solid fa-gear"></i> Cài đặt
            </a>
            <?php endif; ?>
        </nav>

        <div class="kad-footer-links">
            <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="kad-nav-item">
                <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem website
            </a>
            <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="kad-nav-item kad-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT ===== -->
    <main class="kieu-admin-main">

        <!-- Top bar -->
        <header class="kad-topbar">
            <button class="kad-sidebar-toggle" id="kadSidebarToggle" aria-label="Toggle menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h1 class="kad-page-title">
                <?php
                $titles = [
                    'dashboard' => 'Tổng quan',
                    'homepage'  => 'Chỉnh sửa Trang chủ',
                    'lookbook'  => 'Quản lý Lookbook',
                    'banners'   => 'Quản lý Banner',
                    'products'  => 'Sản phẩm',
                    'vip'       => 'VIP Support',
                    'settings'  => 'Cài đặt',
                ];
                echo esc_html($titles[$active_tab] ?? 'Admin');
                ?>
            </h1>
            <div class="kad-user-info">
                <?php echo get_avatar($current_user->ID, 32, '', '', ['class' => 'kad-avatar']); ?>
                <span><?php echo esc_html($current_user->display_name); ?></span>
            </div>
        </header>

        <!-- Notification area -->
        <div id="kadNotice" class="kad-notice" style="display:none"></div>

        <!-- ─── TAB: DASHBOARD ─── -->
        <?php if ($active_tab === 'dashboard'): ?>
        <div class="kad-panel">
            <div class="kad-stats-grid">
                <?php
                $product_count  = wp_count_posts('product')->publish ?? 0;
                $lookbook_count = wp_count_posts('lookbook')->publish ?? 0;
                $vip_count      = count(get_users(['role' => 'vip_customer', 'fields' => ['ID']]));
                $order_count    = function_exists('wc_get_orders') ? count(wc_get_orders(['status' => 'processing', 'limit' => -1])) : 0;
                ?>
                <div class="kad-stat-card">
                    <i class="fa-solid fa-shirt"></i>
                    <span class="kad-stat-num"><?php echo $product_count; ?></span>
                    <span class="kad-stat-label">Sản phẩm</span>
                </div>
                <div class="kad-stat-card">
                    <i class="fa-solid fa-camera-retro"></i>
                    <span class="kad-stat-num"><?php echo $lookbook_count; ?></span>
                    <span class="kad-stat-label">Lookbook</span>
                </div>
                <div class="kad-stat-card" style="border-color:var(--color-gold)">
                    <i class="fa-solid fa-crown" style="color:var(--color-gold)"></i>
                    <span class="kad-stat-num"><?php echo $vip_count; ?></span>
                    <span class="kad-stat-label">VIP Members</span>
                </div>
                <div class="kad-stat-card">
                    <i class="fa-solid fa-bag-shopping"></i>
                    <span class="kad-stat-num"><?php echo $order_count; ?></span>
                    <span class="kad-stat-label">Đơn đang xử lý</span>
                </div>
            </div>

            <h3 class="kad-section-title">Truy cập nhanh</h3>
            <div class="kad-quick-links">
                <?php if ($current_user->has_cap('kieu_edit_lookbook')): ?>
                <a href="?tab=lookbook&action=new" class="kad-quick-btn"><i class="fa-solid fa-plus"></i> Thêm Lookbook</a>
                <?php endif; ?>
                <?php if ($current_user->has_cap('kieu_vip_support')): ?>
                <a href="?tab=vip" class="kad-quick-btn"><i class="fa-solid fa-crown"></i> VIP Members</a>
                <?php endif; ?>
                <?php if ($is_super): ?>
                <a href="<?php echo admin_url(); ?>" class="kad-quick-btn" target="_blank"><i class="fa-solid fa-wrench"></i> WP Admin</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ─── TAB: HOMEPAGE ─── -->
        <?php elseif ($active_tab === 'homepage' && ($is_super || $current_user->has_cap('kieu_edit_homepage'))): ?>
        <div class="kad-panel">
            <p class="kad-hint">Thay đổi sẽ hiện ngay trên trang chủ sau khi nhấn Lưu.</p>

            <div class="kad-form-section">
                <h3 class="kad-section-title">Hero Slider</h3>
                <div class="kad-field">
                    <label>Tiêu đề chính</label>
                    <input type="text" id="kieu_hero_title" class="kad-input kad-save-option"
                           data-key="kieu_hero_title"
                           value="<?php echo esc_attr(get_option('kieu_hero_title', 'Vẻ Đẹp Việt Nam')); ?>">
                </div>
                <div class="kad-field">
                    <label>Phụ đề</label>
                    <input type="text" id="kieu_hero_subtitle" class="kad-input kad-save-option"
                           data-key="kieu_hero_subtitle"
                           value="<?php echo esc_attr(get_option('kieu_hero_subtitle', 'Áo dài cách tân — Nơi truyền thống gặp gỡ hiện đại')); ?>">
                </div>
                <div class="kad-field">
                    <label>Ảnh Hero 1</label>
                    <div class="kad-media-field" data-option-key="kieu_hero_image_1">
                        <?php $img1 = get_option('kieu_hero_image_1'); ?>
                        <div class="kad-media-preview"><?php if ($img1): ?><img src="<?php echo esc_url($img1); ?>" alt=""><?php endif; ?></div>
                        <button type="button" class="kad-btn-outline kad-pick-media">Chọn ảnh</button>
                    </div>
                </div>
            </div>

            <div class="kad-form-section">
                <h3 class="kad-section-title">Giới thiệu KIỀU</h3>
                <div class="kad-field">
                    <label>Nội dung giới thiệu</label>
                    <textarea id="kieu_intro_text" class="kad-textarea kad-save-option"
                              data-key="kieu_intro_text"
                              rows="4"><?php echo esc_textarea(get_option('kieu_intro_text', '')); ?></textarea>
                </div>
            </div>

            <button class="kad-btn-primary kad-save-all">💾 Lưu tất cả thay đổi</button>
        </div>

        <!-- ─── TAB: LOOKBOOK ─── -->
        <?php elseif ($active_tab === 'lookbook' && ($is_super || $current_user->has_cap('kieu_edit_lookbook'))): ?>
        <div class="kad-panel">

            <?php if (isset($_GET['action']) && $_GET['action'] === 'new' || isset($_GET['edit'])): ?>
            <!-- Form thêm/sửa lookbook -->
            <?php
            $edit_id   = intval($_GET['edit'] ?? 0);
            $edit_post = $edit_id ? get_post($edit_id) : null;
            ?>
            <div class="kad-form-section">
                <h3 class="kad-section-title"><?php echo $edit_id ? 'Sửa' : 'Thêm mới'; ?> Lookbook</h3>
                <input type="hidden" id="lookbookPostId" value="<?php echo $edit_id; ?>">

                <div class="kad-field">
                    <label>Tiêu đề <span style="color:red">*</span></label>
                    <input type="text" id="lookbookTitle" class="kad-input"
                           value="<?php echo esc_attr($edit_post ? $edit_post->post_title : ''); ?>">
                </div>
                <div class="kad-field">
                    <label>Mô tả</label>
                    <textarea id="lookbookContent" class="kad-textarea" rows="4"><?php echo esc_textarea($edit_post ? $edit_post->post_content : ''); ?></textarea>
                </div>
                <div class="kad-field">
                    <label>Ảnh đại diện</label>
                    <div class="kad-media-field" id="lookbookThumbnailField">
                        <?php if ($edit_id && has_post_thumbnail($edit_id)): ?>
                            <div class="kad-media-preview"><img src="<?php echo get_the_post_thumbnail_url($edit_id, 'medium'); ?>" alt=""></div>
                        <?php else: ?>
                            <div class="kad-media-preview"></div>
                        <?php endif; ?>
                        <input type="hidden" id="lookbookThumbnailId" value="<?php echo intval(get_post_thumbnail_id($edit_id)); ?>">
                        <button type="button" class="kad-btn-outline" id="lookbookPickMedia">Chọn ảnh</button>
                    </div>
                </div>

                <div class="kad-actions">
                    <button class="kad-btn-primary" id="saveLookbookBtn">💾 Lưu bài</button>
                    <a href="?tab=lookbook" class="kad-btn-outline">Hủy</a>
                </div>
            </div>

            <?php else: ?>
            <!-- Danh sách lookbook -->
            <div class="kad-table-header">
                <a href="?tab=lookbook&action=new" class="kad-btn-primary"><i class="fa-solid fa-plus"></i> Thêm mới</a>
            </div>
            <table class="kad-table">
                <thead>
                    <tr><th>Tiêu đề</th><th>Ngày tạo</th><th>Thao tác</th></tr>
                </thead>
                <tbody>
                <?php if ($lookbooks): foreach ($lookbooks as $lb): ?>
                <tr data-id="<?php echo $lb->ID; ?>">
                    <td>
                        <strong><?php echo esc_html($lb->post_title); ?></strong>
                        <?php if (has_post_thumbnail($lb->ID)): ?>
                            <img src="<?php echo get_the_post_thumbnail_url($lb->ID, 'thumbnail'); ?>" class="kad-list-thumb" alt="">
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html(get_the_date('d/m/Y', $lb->ID)); ?></td>
                    <td class="kad-actions-col">
                        <a href="?tab=lookbook&edit=<?php echo $lb->ID; ?>" class="kad-btn-sm">Sửa</a>
                        <button class="kad-btn-sm kad-btn-danger kad-delete-lookbook" data-id="<?php echo $lb->ID; ?>">Xóa</button>
                        <a href="<?php echo get_permalink($lb->ID); ?>" target="_blank" class="kad-btn-sm">Xem</a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="3" style="text-align:center;color:var(--color-text-muted);padding:2rem">Chưa có bài lookbook nào.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <!-- ─── TAB: BANNERS ─── -->
        <?php elseif ($active_tab === 'banners' && ($is_super || $current_user->has_cap('kieu_edit_banners'))): ?>
        <div class="kad-panel">
            <div class="kad-form-section">
                <h3 class="kad-section-title">Banner Promo</h3>
                <div class="kad-field">
                    <label>Text banner</label>
                    <input type="text" id="kieu_banner_text" class="kad-input kad-save-option"
                           data-key="kieu_banner_text"
                           value="<?php echo esc_attr(get_option('kieu_banner_text', 'Miễn phí vận chuyển cho đơn từ 500.000₫')); ?>">
                </div>
                <div class="kad-field">
                    <label>Ảnh banner</label>
                    <div class="kad-media-field" data-option-key="kieu_banner_image">
                        <?php $bimg = get_option('kieu_banner_image'); ?>
                        <div class="kad-media-preview"><?php if ($bimg): ?><img src="<?php echo esc_url($bimg); ?>" alt=""><?php endif; ?></div>
                        <button type="button" class="kad-btn-outline kad-pick-media">Chọn ảnh</button>
                    </div>
                </div>
                <button class="kad-btn-primary kad-save-all">💾 Lưu Banner</button>
            </div>

            <!-- Upload ảnh/video -->
            <div class="kad-form-section" style="margin-top:2rem">
                <h3 class="kad-section-title">Upload Media</h3>
                <div class="kad-upload-zone" id="kadUploadZone">
                    <i class="fa-solid fa-cloud-arrow-up" style="font-size:2rem;color:var(--color-primary);margin-bottom:0.5rem"></i>
                    <p>Kéo thả ảnh/video vào đây hoặc</p>
                    <label class="kad-btn-outline" style="cursor:pointer">
                        Chọn file <input type="file" id="kadFileInput" accept="image/*,video/*" multiple style="display:none">
                    </label>
                </div>
                <div class="kad-media-grid" id="kadMediaGrid">
                    <?php
                    $media_items = get_posts(['post_type' => 'attachment', 'numberposts' => 30, 'orderby' => 'date', 'order' => 'DESC', 'post_status' => 'inherit', 'post_mime_type' => 'image']);
                    foreach ($media_items as $m):
                        $url = wp_get_attachment_url($m->ID);
                    ?>
                    <div class="kad-media-item">
                        <img src="<?php echo esc_url(wp_get_attachment_image_url($m->ID, 'thumbnail') ?: $url); ?>" alt="<?php echo esc_attr($m->post_title); ?>">
                        <div class="kad-media-item-url"><?php echo esc_html(basename($url)); ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ─── TAB: PRODUCTS ─── -->
        <?php elseif ($active_tab === 'products' && ($is_super || $current_user->has_cap('kieu_edit_products'))): ?>
        <?php
        // Lấy tất cả sản phẩm WooCommerce
        $all_products = wc_get_products(['limit' => -1, 'orderby' => 'title', 'order' => 'ASC', 'status' => ['publish','draft']]);
        ?>
        <div class="kad-panel">
            <div class="kad-table-header">
                <p class="kad-hint" style="margin:0">Click <strong>Sửa</strong> để chỉnh sửa sản phẩm ngay tại đây.</p>
                <button class="kad-btn-primary" id="kadToggleAddProduct">
                    <i class="fa-solid fa-plus"></i> Thêm sản phẩm
                </button>
            </div>

            <!-- Form thêm sản phẩm mới (ẩn mặc định) -->
            <div id="kadAddProductForm" class="kad-inline-editor" style="display:none;margin-bottom:1.5rem;border:2px solid #C9A96E;border-radius:8px">
                <h3 class="kad-section-title" style="margin-bottom:1rem">➕ Thêm sản phẩm mới</h3>
                <div class="kad-inline-grid">
                    <div class="kad-col-main">
                        <div class="kad-field">
                            <label>Tên sản phẩm <span style="color:#C62828">*</span></label>
                            <input type="text" class="kad-input" id="newPName" placeholder="Áo Dài Kieu XYZ">
                        </div>
                        <div class="kad-inline-prices">
                            <div class="kad-field">
                                <label>Giá gốc (VND)</label>
                                <input type="number" class="kad-input" id="newPPrice" placeholder="350000">
                            </div>
                            <div class="kad-field">
                                <label>Giá sale</label>
                                <input type="number" class="kad-input" id="newPSale" placeholder="">
                            </div>
                            <div class="kad-field">
                                <label>Tồn kho</label>
                                <input type="number" class="kad-input" id="newPStock" placeholder="10">
                            </div>
                        </div>
                        <div class="kad-field">
                            <label>Mô tả ngắn</label>
                            <textarea class="kad-textarea" id="newPDesc" rows="3" placeholder="Mô tả ngắn về sản phẩm..."></textarea>
                        </div>
                        <div class="kad-field">
                            <label>Trạng thái</label>
                            <select class="kad-input" id="newPStatus">
                                <option value="publish">Hiển thị</option>
                                <option value="draft">Ẩn (Draft)</option>
                            </select>
                        </div>
                    </div>
                    <div class="kad-col-thumb">
                        <label style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#444;display:block;margin-bottom:0.5rem">Ảnh đại diện</label>
                        <div class="kad-product-thumb-preview">
                            <img src="" alt="" id="newPThumbImg" style="display:none">
                            <div class="kad-thumb-placeholder" id="newPThumbPlaceholder">
                                <i class="fa-solid fa-image"></i>
                                <span>Chưa có ảnh</span>
                            </div>
                        </div>
                        <input type="hidden" id="newPThumbId" value="">
                        <button type="button" class="kad-btn-outline" id="newPPickThumb" style="width:100%;margin-top:0.5rem">
                            <i class="fa-solid fa-image"></i> Chọn ảnh
                        </button>
                    </div>
                </div>
                <div class="kad-inline-actions">
                    <button class="kad-btn-primary" id="kadSaveNewProduct">
                        <i class="fa-solid fa-floppy-disk"></i> Tạo sản phẩm
                    </button>
                    <button class="kad-btn-outline" id="kadCancelAddProduct">Hủy</button>
                </div>
            </div>

            <table class="kad-table" id="kadProductTable">
                <thead>
                    <tr>
                        <th style="width:60px">Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Tồn kho</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($all_products): foreach ($all_products as $p): ?>
                <?php
                $pid        = $p->get_id();
                $thumb_url  = get_the_post_thumbnail_url($pid, 'thumbnail') ?: '';
                $price      = $p->get_regular_price();
                $sale_price = $p->get_sale_price();
                $stock      = $p->get_stock_quantity();
                $status     = $p->get_status();
                $short_desc = $p->get_short_description();
                $thumb_id   = get_post_thumbnail_id($pid);
                ?>
                <tr class="kad-product-row" data-id="<?php echo $pid; ?>">
                    <td>
                        <?php if ($thumb_url): ?>
                            <img src="<?php echo esc_url($thumb_url); ?>" class="kad-list-thumb" alt="">
                        <?php else: ?>
                            <div style="width:40px;height:40px;background:#F0EDED;border-radius:4px;display:flex;align-items:center;justify-content:center">
                                <i class="fa-solid fa-image" style="color:#CCC;font-size:1rem"></i>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo esc_html($p->get_name()); ?></strong></td>
                    <td>
                        <?php if ($sale_price): ?>
                            <span style="text-decoration:line-through;color:#aaa;font-size:0.78rem"><?php echo number_format((float)$price, 0, ',', '.'); ?>₫</span>
                            <strong style="color:#8C2020"> <?php echo number_format((float)$sale_price, 0, ',', '.'); ?>₫</strong>
                        <?php else: ?>
                            <?php echo number_format((float)$price, 0, ',', '.'); ?>₫
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($stock !== null): ?>
                            <span style="color:<?php echo $stock > 0 ? '#2E7D32' : '#C62828'; ?>">
                                <?php echo $stock > 0 ? $stock . ' cái' : 'Hết hàng'; ?>
                            </span>
                        <?php else: ?>
                            <span style="color:#888">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="kad-status-badge <?php echo $status === 'publish' ? 'active' : 'draft'; ?>">
                            <?php echo $status === 'publish' ? 'Hiện' : 'Ẩn'; ?>
                        </span>
                    </td>
                    <td class="kad-actions-col">
                        <button class="kad-btn-sm kad-edit-product"
                                data-id="<?php echo $pid; ?>"
                                data-name="<?php echo esc_attr($p->get_name()); ?>"
                                data-price="<?php echo esc_attr($price); ?>"
                                data-sale="<?php echo esc_attr($sale_price); ?>"
                                data-stock="<?php echo esc_attr($stock ?? ''); ?>"
                                data-desc="<?php echo esc_attr(wp_strip_all_tags($short_desc)); ?>"
                                data-thumb="<?php echo esc_attr($thumb_url); ?>"
                                data-thumb-id="<?php echo esc_attr($thumb_id); ?>"
                                data-status="<?php echo esc_attr($status); ?>">
                            ✏️ Sửa
                        </button>
                        <a href="<?php echo esc_url(get_permalink($pid)); ?>" target="_blank" class="kad-btn-sm">Xem</a>
                    </td>
                </tr>
                <!-- Inline edit panel (ẩn mặc định) -->
                <tr class="kad-product-edit-row" id="editRow-<?php echo $pid; ?>" style="display:none">
                    <td colspan="6" style="padding:0">
                        <div class="kad-inline-editor">
                            <div class="kad-inline-grid">
                                <!-- Cột trái: thông tin -->
                                <div class="kad-col-main">
                                    <div class="kad-field">
                                        <label>Tên sản phẩm</label>
                                        <input type="text" class="kad-input kp-name" value="<?php echo esc_attr($p->get_name()); ?>">
                                    </div>
                                    <div class="kad-inline-prices">
                                        <div class="kad-field">
                                            <label>Giá gốc (VND)</label>
                                            <input type="number" class="kad-input kp-price" value="<?php echo esc_attr($price); ?>" placeholder="350000">
                                        </div>
                                        <div class="kad-field">
                                            <label>Giá sale (để trống nếu không)</label>
                                            <input type="number" class="kad-input kp-sale" value="<?php echo esc_attr($sale_price); ?>" placeholder="">
                                        </div>
                                        <div class="kad-field">
                                            <label>Tồn kho</label>
                                            <input type="number" class="kad-input kp-stock" value="<?php echo esc_attr($stock ?? ''); ?>" placeholder="10">
                                        </div>
                                    </div>
                                    <div class="kad-field">
                                        <label>Mô tả ngắn</label>
                                        <textarea class="kad-textarea kp-desc" rows="3"><?php echo esc_textarea(wp_strip_all_tags($short_desc)); ?></textarea>
                                    </div>
                                    <div class="kad-field">
                                        <label>Trạng thái</label>
                                        <select class="kad-input kp-status">
                                            <option value="publish" <?php selected($status, 'publish'); ?>>Hiển thị</option>
                                            <option value="draft" <?php selected($status, 'draft'); ?>>Ẩn</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Cột phải: ảnh -->
                                <div class="kad-col-thumb">
                                    <label style="font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#444;display:block;margin-bottom:0.5rem">Ảnh đại diện</label>
                                    <div class="kad-product-thumb-preview">
                                        <?php if ($thumb_url): ?>
                                            <img src="<?php echo esc_url($thumb_url); ?>" alt="" id="pThumb-<?php echo $pid; ?>">
                                        <?php else: ?>
                                            <img src="" alt="" id="pThumb-<?php echo $pid; ?>" style="display:none">
                                        <?php endif; ?>
                                        <div class="kad-thumb-placeholder <?php echo $thumb_url ? 'hidden' : ''; ?>" id="pThumbPlaceholder-<?php echo $pid; ?>">
                                            <i class="fa-solid fa-image"></i>
                                            <span>Chưa có ảnh</span>
                                        </div>
                                    </div>
                                    <input type="hidden" class="kp-thumb-id" value="<?php echo esc_attr($thumb_id); ?>">
                                    <button type="button" class="kad-btn-outline kad-pick-product-thumb" style="width:100%;margin-top:0.5rem">
                                        <i class="fa-solid fa-image"></i> Thay ảnh
                                    </button>
                                </div>
                            </div>
                            <div class="kad-inline-actions">
                                <button class="kad-btn-primary kad-save-product" data-id="<?php echo $pid; ?>">
                                    <i class="fa-solid fa-floppy-disk"></i> Lưu sản phẩm
                                </button>
                                <button class="kad-btn-outline kad-cancel-edit" data-id="<?php echo $pid; ?>">Hủy</button>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="6" style="text-align:center;color:var(--color-text-muted);padding:2rem">Chưa có sản phẩm nào.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- ─── TAB: VIP SUPPORT ─── -->
        <?php elseif ($active_tab === 'vip' && ($is_super || $current_user->has_cap('kieu_vip_support'))): ?>
        <div class="kad-panel">
            <p class="kad-hint">Danh sách khách hàng VIP — anh/chị có thể xem thông tin và thêm ghi chú chăm sóc.</p>

            <?php if ($vip_users): foreach ($vip_users as $vip): ?>
            <?php
            $notes   = get_user_meta($vip->ID, 'kieu_vip_notes', true) ?: [];
            $spend   = kieu_get_annual_spend($vip->ID);
            $orders  = wc_get_orders(['customer' => $vip->ID, 'limit' => 3, 'orderby' => 'date', 'order' => 'DESC']);
            ?>
            <div class="kad-vip-card">
                <div class="kad-vip-card-header">
                    <?php echo get_avatar($vip->ID, 48, '', '', ['class' => 'kad-vip-avatar']); ?>
                    <div class="kad-vip-info">
                        <strong><?php echo esc_html($vip->display_name); ?></strong>
                        <span><?php echo esc_html($vip->user_email); ?></span>
                        <span class="kad-vip-spend">Chi tiêu 12T: <strong><?php echo number_format($spend, 0, ',', '.'); ?>₫</strong></span>
                    </div>
                    <div class="kad-vip-crown"><i class="fa-solid fa-crown"></i> VIP</div>
                </div>

                <?php if ($orders): ?>
                <div class="kad-vip-orders">
                    <p style="font-size:0.78rem;color:var(--color-text-muted);margin-bottom:0.5rem">Đơn hàng gần nhất:</p>
                    <?php foreach ($orders as $ord): ?>
                    <div class="kad-vip-order-row">
                        <span>#<?php echo $ord->get_order_number(); ?></span>
                        <span><?php echo wc_format_datetime($ord->get_date_created()); ?></span>
                        <span><?php echo $ord->get_formatted_order_total(); ?></span>
                        <span class="order-status status-<?php echo esc_attr($ord->get_status()); ?>"><?php echo wc_get_order_status_name($ord->get_status()); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="kad-vip-notes">
                    <?php if ($notes): foreach (array_reverse($notes) as $n): ?>
                    <div class="kad-note-item">
                        <span class="kad-note-by"><?php echo esc_html($n['by']); ?></span>
                        <span class="kad-note-time"><?php echo esc_html(date('d/m/Y H:i', strtotime($n['time']))); ?></span>
                        <p><?php echo esc_html($n['note']); ?></p>
                    </div>
                    <?php endforeach; endif; ?>
                    <div class="kad-add-note">
                        <textarea class="kad-textarea kad-note-input" data-user-id="<?php echo $vip->ID; ?>"
                                  rows="2" placeholder="Thêm ghi chú chăm sóc..."></textarea>
                        <button class="kad-btn-sm kad-save-note" data-user-id="<?php echo $vip->ID; ?>">Lưu ghi chú</button>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
            <p style="text-align:center;color:var(--color-text-muted);padding:3rem">Chưa có thành viên VIP nào.</p>
            <?php endif; ?>
        </div>

        <!-- ─── TAB: SETTINGS ─── -->
        <?php elseif ($active_tab === 'settings' && ($is_super || $current_user->has_cap('kieu_manage_settings'))): ?>
        <div class="kad-panel">
            <div class="kad-form-section">
                <h3 class="kad-section-title">Thông tin liên hệ & Ngân hàng</h3>
                <?php
                $setting_fields = [
                    ['key' => 'kieu_phone',       'label' => 'Số điện thoại',       'placeholder' => '0900 000 000'],
                    ['key' => 'kieu_address',      'label' => 'Địa chỉ showroom',    'placeholder' => '123 Đường ABC, Q.1, TP.HCM'],
                    ['key' => 'kieu_facebook',     'label' => 'Link Facebook',        'placeholder' => 'https://facebook.com/kieu.aodai'],
                    ['key' => 'kieu_instagram',    'label' => 'Link Instagram',       'placeholder' => 'https://instagram.com/kieu.aodai'],
                    ['key' => 'kieu_bank_name',    'label' => 'Ngân hàng',           'placeholder' => 'Vietcombank'],
                    ['key' => 'kieu_bank_number',  'label' => 'Số tài khoản',        'placeholder' => '1234567890'],
                    ['key' => 'kieu_bank_holder',  'label' => 'Tên chủ tài khoản',  'placeholder' => 'NGUYEN THI KIEU'],
                ];
                foreach ($setting_fields as $sf):
                ?>
                <div class="kad-field">
                    <label><?php echo esc_html($sf['label']); ?></label>
                    <input type="text" class="kad-input kad-save-option"
                           data-key="<?php echo esc_attr($sf['key']); ?>"
                           value="<?php echo esc_attr(get_option($sf['key'], '')); ?>"
                           placeholder="<?php echo esc_attr($sf['placeholder']); ?>">
                </div>
                <?php endforeach; ?>
                <button class="kad-btn-primary kad-save-all">💾 Lưu cài đặt</button>
            </div>

            <!-- Hướng dẫn chọn size -->
            <div class="kad-form-section" style="margin-top:2rem">
                <h3 class="kad-section-title">📏 Hướng dẫn chọn size</h3>
                <p class="kad-hint">Ảnh này sẽ hiện trong popup khi khách hàng click "Hướng dẫn chọn size" trên trang sản phẩm.</p>

                <div class="kad-field">
                    <label>Ảnh bảng size</label>
                    <div class="kad-media-field" data-option-key="kieu_size_guide_image">
                        <?php $sg_img = get_option('kieu_size_guide_image', ''); ?>
                        <div class="kad-media-preview">
                            <?php if ($sg_img): ?>
                                <img src="<?php echo esc_url($sg_img); ?>" alt="Size Guide" style="max-height:180px;object-fit:contain">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="kad-btn-outline kad-pick-media" style="margin-top:.5rem">
                            <i class="fa-solid fa-image"></i>
                            <?php echo $sg_img ? 'Thay ảnh bảng size' : 'Upload ảnh bảng size'; ?>
                        </button>
                        <?php if ($sg_img): ?>
                            <button type="button" class="kad-btn-outline" style="margin-top:.5rem;color:#888"
                                    onclick="if(confirm('Xóa ảnh size guide?')){jQuery.post(kieuAdminData.ajaxUrl,{action:'kieu_save_option',nonce:kieuAdminData.nonce,option_key:'kieu_size_guide_image',option_value:''}).done(function(){location.reload();})}">
                                <i class="fa-solid fa-trash"></i> Xóa ảnh
                            </button>
                        <?php endif; ?>
                    </div>
                    <p style="font-size:0.75rem;color:#888;margin-top:0.4rem">Để trống → tự động dùng bảng size mặc định (XS–XL với số đo chuẩn).</p>
                </div>

                <div class="kad-field" style="margin-top:1rem">
                    <label>Ghi chú / Mẹo chọn size</label>
                    <textarea id="kieu_size_guide_note" class="kad-textarea kad-save-option"
                              data-key="kieu_size_guide_note"
                              rows="3"
                              placeholder="VD: Nếu số đo nằm giữa 2 size, hãy chọn size lớn hơn..."><?php echo esc_textarea(get_option('kieu_size_guide_note', '')); ?></textarea>
                </div>
                <button class="kad-btn-primary kad-save-all" style="margin-top:1rem">💾 Lưu hướng dẫn size</button>
            </div>
        </div>


        <?php endif; ?>

    </main>
</div><!-- .kieu-admin-wrap -->

<!-- Hidden file inputs cho image picker fallback -->
<input type="file" id="kadProductImageInput"  accept="image/*" style="position:absolute;left:-9999px;opacity:0;width:1px;height:1px">
<input type="file" id="kadGeneralImageInput"  accept="image/*" style="position:absolute;left:-9999px;opacity:0;width:1px;height:1px">

<?php get_footer(); ?>
