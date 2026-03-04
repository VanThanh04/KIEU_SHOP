<?php
/**
 * front-page.php - KIỀU
 * Trang chủ: Hero Slider, Brand Intro, Categories, Product Grid, Promo Banners, AI Chat
 */

get_header();
?>

<!-- ========================================
     SECTION 1: HERO SLIDER
     ======================================== -->
<section class="hero-slider" aria-label="Bộ sưu tập nổi bật">

    <?php
    /**
     * Slides được quản lý qua WordPress Custom Fields hoặc ACF.
     * Để demo, lấy 3 sản phẩm featured làm slides.
     * Anh có thể thay bằng ảnh hero thật sau khi cài xong.
     */
    $hero_args = [
        'post_type'      => 'product',
        'posts_per_page' => 3,
        'meta_key'       => '_featured',
        'meta_value'     => 'yes',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    $hero_products = new WP_Query($hero_args);

    // Fallback: nếu chưa có featured products, lấy 3 sản phẩm mới nhất
    if (!$hero_products->have_posts()) {
        $hero_args = [
            'post_type'      => 'product',
            'posts_per_page' => 3,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];
        $hero_products = new WP_Query($hero_args);
    }

    $slide_index = 0;
    if ($hero_products->have_posts()):
        while ($hero_products->have_posts()): $hero_products->the_post();
            $product = wc_get_product(get_the_ID());
            $active_class = ($slide_index === 0) ? ' is-active' : '';
    ?>
    <div class="hero-slide<?php echo $active_class; ?>" data-slide="<?php echo $slide_index; ?>">
        <?php if (has_post_thumbnail()): ?>
            <?php the_post_thumbnail('kieu-hero', ['alt' => get_the_title(), 'loading' => $slide_index === 0 ? 'eager' : 'lazy']); ?>
        <?php endif; ?>
        <div class="hero-slide-content">
            <p class="hero-slide-label">Bộ Sưu Tập Mới</p>
            <h2 class="hero-slide-title"><?php the_title(); ?></h2>
            <p class="hero-slide-brand">KIỀU</p>
            <a href="<?php the_permalink(); ?>" class="hero-cta">Khám Phá Ngay &rarr;</a>
        </div>
    </div>
    <?php
        $slide_index++;
        endwhile;
        wp_reset_postdata();
    else:
        // Placeholder khi chưa có sản phẩm
    ?>
    <div class="hero-slide is-active">
        <div style="width:100%; height:clamp(400px,70vh,750px); background:linear-gradient(135deg,#8C2020 0%,#6B1818 100%); display:flex; align-items:center; justify-content:center;">
            <div style="text-align:center; color:white;">
                <p style="font-size:0.75rem; letter-spacing:0.3em; opacity:0.8; margin-bottom:1rem">BỘ SƯU TẬP MỚI</p>
                <h2 style="font-family:'Cormorant Garamond',serif; font-size:clamp(3rem,8vw,6rem); font-weight:700; line-height:1">KIỀU</h2>
                <p style="font-size:0.8rem; letter-spacing:0.4em; opacity:0.7; margin-top:1rem">ÁO DÀI CÁCH TÂN</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Nút prev/next -->
    <button class="hero-nav hero-nav-prev" id="heroPrev" aria-label="Slide trước">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button class="hero-nav hero-nav-next" id="heroNext" aria-label="Slide tiếp">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- Dots indicator -->
    <div class="hero-dots" id="heroDots">
        <?php for ($i = 0; $i < max($slide_index, 1); $i++): ?>
            <button class="hero-dot <?php echo $i === 0 ? 'is-active' : ''; ?>"
                    data-target="<?php echo $i; ?>"
                    aria-label="Slide <?php echo $i + 1; ?>"></button>
        <?php endfor; ?>
    </div>

</section>


<!-- ========================================
     SECTION 2: BRAND INTRO
     ======================================== -->
<section class="brand-intro" aria-label="Giới thiệu thương hiệu">
    <div class="gold-divider"><span>✦</span></div>
    <h2 class="section-title" style="font-style:italic; font-weight:400">
        Nơi truyền thống gặp gỡ hiện đại
    </h2>
    <div class="gold-divider"><span>✦</span></div>
    <p>
        KIỀU ra đời từ tình yêu với áo dài Việt Nam — trang phục mang linh hồn dân tộc. Chúng tôi không đơn thuần bán áo, mà kể những câu chuyện văn hóa qua từng đường kim, mũi chỉ. Mỗi thiết kế là sự hòa quyện tinh tế giữa nét cổ điển và hơi thở đương đại.
    </p>

</section>


<!-- ========================================
     SECTION 3: DANH MỤC (Circles)
     ======================================== -->
<?php
$categories = get_terms([
    'taxonomy'   => 'product_cat',
    'hide_empty' => false,
    'parent'     => 0,
    'number'     => 4,
]);
if ($categories && !is_wp_error($categories) && count($categories) > 1):
    // Lọc bỏ Uncategorized
    $categories = array_filter($categories, fn($c) => $c->name !== 'Uncategorized');
?>
<section class="categories-section">
    <div class="container">
        <div class="categories-grid">
            <?php foreach ($categories as $cat):
                $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);
                $image_url = $thumbnail_id
                    ? wp_get_attachment_image_url($thumbnail_id, 'kieu-category')
                    : get_template_directory_uri() . '/assets/images/placeholder-cat.jpg';
            ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="category-item">
                <div class="category-circle">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($cat->name); ?>" loading="lazy">
                </div>
                <span class="category-name"><?php echo esc_html($cat->name); ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ========================================
     SECTION 4: SẢN PHẨM NỔI BẬT
     ======================================== -->
<section class="products-section" aria-label="Sản phẩm nổi bật">
    <p class="section-subtitle">— Bộ sưu tập —</p>
    <h2 class="section-title">Sản Phẩm Nổi Bật</h2>
    <div class="gold-divider"><span>✦</span></div>

    <?php
    $featured_args = [
        'post_type'      => 'product',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    $featured_products = new WP_Query($featured_args);
    ?>

    <div class="product-grid">
        <?php
        if ($featured_products->have_posts()):
            while ($featured_products->have_posts()): $featured_products->the_post();
                $product = wc_get_product(get_the_ID());
                $is_on_sale = $product->is_on_sale();
                $is_new     = (time() - get_the_time('U')) < (60 * 60 * 24 * 30); // < 30 ngày
        ?>
        <a href="<?php the_permalink(); ?>" class="product-card">
            <div class="product-card-image">
                <?php if ($is_on_sale): ?>
                    <span class="product-badge">Sale</span>
                <?php elseif ($is_new): ?>
                    <span class="product-badge badge-new">MỚI</span>
                <?php endif; ?>

                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('kieu-product-portrait', ['alt' => get_the_title(), 'loading' => 'lazy']); ?>
                <?php else: ?>
                    <div style="width:100%;height:100%;background:#E8DDD0;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-image" style="font-size:2rem;color:#C9A96E;opacity:0.4"></i>
                    </div>
                <?php endif; ?>

                <!-- Quick add (hover) -->
                <div class="product-quick-add"
                     onclick="event.preventDefault(); kieuShop.quickAdd(<?php echo get_the_ID(); ?>, this)">
                    Thêm vào giỏ hàng
                </div>
            </div>
            <div class="product-card-info">
                <h3 class="product-card-name"><?php the_title(); ?></h3>
                <div class="product-card-price">
                    <?php if ($is_on_sale): ?>
                        <del><?php echo wc_price($product->get_regular_price()); ?></del>
                    <?php endif; ?>
                    <?php echo wc_price($product->get_price()); ?>
                </div>
            </div>
        </a>
        <?php
            endwhile;
            wp_reset_postdata();
        else:
        ?>
        <!-- Placeholder khi chưa có sản phẩm -->
        <?php for ($i = 0; $i < 6; $i++): ?>
        <div class="product-card">
            <div class="product-card-image" style="background:#E8DDD0; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-shirt" style="font-size:2.5rem; color:#C9A96E; opacity:0.5"></i>
            </div>
            <div class="product-card-info">
                <p class="product-card-name">Áo Dài Cách Tân <?php echo $i + 1; ?></p>
                <p class="product-card-price">3.500.000₫</p>
            </div>
        </div>
        <?php endfor; ?>
        <?php endif; ?>
    </div>

    <div class="section-view-all">
        <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="btn-outline">
            Xem tất cả sản phẩm
        </a>
    </div>
</section>


<!-- ========================================
     SECTION 5: PROMO BANNERS (2 cột)
     ======================================== -->
<section class="promo-banners" aria-label="Bộ sưu tập">
    <!-- Banner 1 -->
    <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="promo-banner">
        <?php
        // Lấy ảnh từ danh mục đầu tiên có thumbnail
        $promo_cats = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true, 'number' => 2]);
        $banner1_img = '';
        if ($promo_cats && !is_wp_error($promo_cats)) {
            foreach ($promo_cats as $pc) {
                $tid = get_term_meta($pc->term_id, 'thumbnail_id', true);
                if ($tid) { $banner1_img = wp_get_attachment_image_url($tid, 'kieu-hero'); break; }
            }
        }
        if ($banner1_img):
        ?>
        <img src="<?php echo esc_url($banner1_img); ?>" alt="Bộ sưu tập mới" loading="lazy">
        <?php else: ?>
        <div style="width:100%;height:100%;background:linear-gradient(160deg,#8C2020,#6B1818)"></div>
        <?php endif; ?>
        <div class="promo-banner-content">
            <p class="promo-banner-label">Bộ Sưu Tập</p>
            <h3 class="promo-banner-title">Áo Dài<br>Cách Tân</h3>
            <span class="promo-banner-link">Khám phá ngay →</span>
        </div>
    </a>

    <!-- Banner 2 -->
    <a href="<?php echo esc_url(get_post_type_archive_link('lookbook')); ?>" class="promo-banner">
        <div style="width:100%;height:100%;background:linear-gradient(160deg,#2A1A1A,#4A2020)"></div>
        <div class="promo-banner-content">
            <p class="promo-banner-label">Phong Cách</p>
            <h3 class="promo-banner-title">Lookbook<br>2025</h3>
            <span class="promo-banner-link">Xem ngay →</span>
        </div>
    </a>
</section>


<!-- ========================================
     SECTION 6: SẢN PHẨM MỚI NHẤT
     ======================================== -->
<section class="products-section" aria-label="Sản phẩm mới">
    <p class="section-subtitle">— Vừa ra mắt —</p>
    <h2 class="section-title">Hàng Mới Về</h2>
    <div class="gold-divider"><span>✦</span></div>

    <?php
    $new_args = [
        'post_type'      => 'product',
        'posts_per_page' => 3,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'offset'         => 6, // bỏ 6 sản phẩm đã hiển thị ở trên
    ];
    $new_products = new WP_Query($new_args);
    ?>
    <div class="product-grid">
        <?php
        if ($new_products->have_posts()):
            while ($new_products->have_posts()): $new_products->the_post();
                $product = wc_get_product(get_the_ID());
        ?>
        <a href="<?php the_permalink(); ?>" class="product-card">
            <div class="product-card-image">
                <span class="product-badge">Mới</span>
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('kieu-product-portrait', ['alt' => get_the_title(), 'loading' => 'lazy']); ?>
                <?php endif; ?>
                <div class="product-quick-add"
                     onclick="event.preventDefault(); kieuShop.quickAdd(<?php echo get_the_ID(); ?>, this)">
                    Thêm vào giỏ hàng
                </div>
            </div>
            <div class="product-card-info">
                <h3 class="product-card-name"><?php the_title(); ?></h3>
                <p class="product-card-price"><?php echo wc_price($product->get_price()); ?></p>
            </div>
        </a>
        <?php
            endwhile;
            wp_reset_postdata();
        endif;
        ?>
    </div>
</section>

<?php get_footer(); ?>

<!-- ========================================
     AI CHAT WIDGET (KIỀU trợ lý ảo)
     Widget này hiển thị trên tất cả các trang.
     Tidio chat embed tự động qua functions.php;
     Button phía dưới chỉ là fallback placeholder khi chưa cài Tidio.
     ======================================== -->
<?php if (!function_exists('tidio_is_active') || !tidio_is_active()): ?>
<div class="kieu-chat-widget" id="kieuChatWidget" aria-label="Chat với KIỀU AI">
    <div class="kieu-chat-tooltip">Chat với <strong>KIỀU</strong> AI &#10022;</div>
    <button class="kieu-chat-btn" id="kieuChatBtn" aria-label="Mở chat hỗ trợ">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><circle cx="9" cy="10" r="0.5" fill="currentColor"></circle><circle cx="12" cy="10" r="0.5" fill="currentColor"></circle><circle cx="15" cy="10" r="0.5" fill="currentColor"></circle></svg>
    </button>
</div>
<?php endif; ?>
