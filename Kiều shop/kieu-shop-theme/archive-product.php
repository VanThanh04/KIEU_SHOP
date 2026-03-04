<?php
/**
 * archive-product.php — Kiều Shop
 * Trang danh sách sản phẩm / Shop: header, toolbar, product grid 3 cột, pagination
 */
get_header();

// Lấy tên trang hiện tại (danh mục / tìm kiếm / shop chính)
if (is_product_category()) {
    $queried = get_queried_object();
    $page_title = $queried->name;
    $page_desc  = $queried->description;
} elseif (is_search()) {
    $page_title = 'Kết quả tìm kiếm: "' . get_search_query() . '"';
    $page_desc  = '';
} else {
    $page_title = 'Tất Cả Sản Phẩm';
    $page_desc  = 'Khám phá bộ sưu tập áo dài cách tân của Kiều Shop';
}
?>

<!-- Breadcrumb -->
<nav class="breadcrumb">
    <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
    <span>/</span>
    <?php if (is_product_category() && isset($queried)): ?>
        <span><?php echo esc_html($queried->name); ?></span>
    <?php else: ?>
        <span>Sản Phẩm</span>
    <?php endif; ?>
</nav>

<!-- Shop header: tiêu đề danh mục -->
<div class="shop-header">
    <h1 class="section-title"><?php echo esc_html($page_title); ?></h1>
    <?php if ($page_desc): ?>
        <p style="color:var(--color-text-muted); font-size:0.9rem; margin-top:0.5rem">
            <?php echo esc_html($page_desc); ?>
        </p>
    <?php endif; ?>
    <div class="gold-divider"><span>✦</span></div>
</div>

<!-- Toolbar: số sản phẩm + sắp xếp -->
<div class="shop-toolbar">
    <p class="shop-results">
        <?php
        global $wp_query;
        $total = $wp_query->found_posts;
        echo esc_html($total) . ' sản phẩm';
        ?>
    </p>
    <div class="shop-sort">
        <select name="orderby" id="shopOrderby" onchange="window.location.href=this.value">
            <?php
            $current_url = remove_query_arg('orderby');
            $sort_options = [
                ''           => 'Mặc định',
                'date'       => 'Mới nhất',
                'price'      => 'Giá: Thấp → Cao',
                'price-desc' => 'Giá: Cao → Thấp',
                'popularity' => 'Bán chạy nhất',
            ];
            $current_order = $_GET['orderby'] ?? '';
            foreach ($sort_options as $val => $label):
                $url = $val ? add_query_arg('orderby', $val, $current_url) : $current_url;
                $selected = selected($current_order, $val, false);
            ?>
                <option value="<?php echo esc_url($url); ?>" <?php echo $selected; ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<!-- Product grid -->
<main class="shop-page">
    <?php if (have_posts()): ?>
    <div class="product-grid">
        <?php
        while (have_posts()): the_post();
            $product    = wc_get_product(get_the_ID());
            $is_on_sale = $product->is_on_sale();
            $is_new     = (time() - get_the_time('U')) < (60 * 60 * 24 * 30);
        ?>
        <a href="<?php the_permalink(); ?>" class="product-card">
            <div class="product-card-image">
                <?php if ($is_on_sale): ?>
                    <span class="product-badge">Sale</span>
                <?php elseif ($is_new): ?>
                    <span class="product-badge">Mới</span>
                <?php endif; ?>

                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('kieu-product-portrait', ['alt' => get_the_title(), 'loading' => 'lazy']); ?>
                <?php else: ?>
                    <div style="width:100%;height:100%;min-height:300px;background:var(--color-border);display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-shirt" style="font-size:3rem;color:var(--color-gold);opacity:0.4"></i>
                    </div>
                <?php endif; ?>

                <div class="product-quick-add"
                     onclick="event.preventDefault(); kieuShop.quickAdd(<?php echo get_the_ID(); ?>, this)">
                    Thêm vào giỏ hàng
                </div>
            </div>
            <div class="product-card-info">
                <h2 class="product-card-name"><?php the_title(); ?></h2>
                <div class="product-card-price">
                    <?php if ($is_on_sale): ?>
                        <del><?php echo wc_price($product->get_regular_price()); ?></del>
                    <?php endif; ?>
                    <?php echo wc_price($product->get_price()); ?>
                </div>
            </div>
        </a>
        <?php endwhile; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php
        echo paginate_links([
            'type'      => 'list',
            'prev_text' => '<i class="fa-solid fa-chevron-left"></i>',
            'next_text' => '<i class="fa-solid fa-chevron-right"></i>',
        ]);
        ?>
    </div>

    <?php else: ?>
    <!-- Không có sản phẩm -->
    <div style="text-align:center; padding:5rem 1rem;">
        <i class="fa-solid fa-shirt" style="font-size:3rem; color:var(--color-gold); opacity:0.5; margin-bottom:1.5rem; display:block;"></i>
        <h2 style="font-family:'Cormorant Garamond',serif; font-size:1.5rem; margin-bottom:1rem;">Chưa có sản phẩm</h2>
        <p style="color:var(--color-text-muted); margin-bottom:2rem;">
            Hãy quay lại sau nhé, bộ sưu tập mới đang sắp ra mắt!
        </p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary">Về trang chủ</a>
    </div>
    <?php endif; ?>
</main>

<?php get_footer(); ?>
