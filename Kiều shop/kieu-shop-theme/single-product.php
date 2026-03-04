<?php
/**
 * single-product.php — KIỀU
 * Chi tiết sản phẩm: gallery ảnh, chọn size, VNPay, sản phẩm liên quan
 */
get_header();

while (have_posts()): the_post();
    $product = wc_get_product(get_the_ID());
    $images  = $product->get_gallery_image_ids(); // ảnh gallery phụ
    $main_id = get_post_thumbnail_id();
    if ($main_id) array_unshift($images, $main_id); // đặt ảnh chính lên đầu
    $regular_price = $product->get_regular_price();
    $sale_price    = $product->get_sale_price();
    $is_on_sale    = $product->is_on_sale();
?>

<!-- Breadcrumb -->
<nav class="breadcrumb" aria-label="Breadcrumb">
    <a href="<?php echo esc_url(home_url('/')); ?>">Trang chủ</a>
    <span>/</span>
    <?php
    $terms = get_the_terms(get_the_ID(), 'product_cat');
    if ($terms && !is_wp_error($terms)):
        $cat = $terms[0];
    ?>
    <a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a>
    <span>/</span>
    <?php endif; ?>
    <span><?php the_title(); ?></span>
</nav>

<!-- Product single grid -->
<div class="product-single">
    <div class="product-single-grid">

        <!-- ====== LEFT: Image Gallery ====== -->
        <div class="product-gallery">
            <!-- Ảnh lớn chính -->
            <div class="product-gallery-main" id="galleryMain">
                <?php if ($images): ?>
                    <img src="<?php echo esc_url(wp_get_attachment_image_url($images[0], 'kieu-product-portrait')); ?>"
                         alt="<?php the_title_attribute(); ?>"
                         id="galleryMainImg"
                         loading="eager">
                <?php endif; ?>
            </div>
            <!-- Thumbnails -->
            <?php if (count($images) > 1): ?>
            <div class="product-gallery-thumbs">
                <?php foreach ($images as $i => $img_id): ?>
                    <div class="product-gallery-thumb <?php echo $i === 0 ? 'is-active' : ''; ?>"
                         data-full="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'kieu-product-portrait')); ?>">
                        <img src="<?php echo esc_url(wp_get_attachment_image_url($img_id, 'thumbnail')); ?>"
                             alt="Xem ảnh <?php echo $i + 1; ?>"
                             loading="lazy">
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- ====== RIGHT: Product Info ====== -->
        <div class="product-info">

            <!-- Danh mục label nhỏ -->
            <?php
            $terms = get_the_terms(get_the_ID(), 'product_cat');
            if ($terms && !is_wp_error($terms)) {
                $cat = array_filter($terms, fn($t) => $t->name !== 'Uncategorized');
                if ($cat) {
                    $cat = reset($cat);
                    echo '<p class="product-category-label">' . esc_html(strtoupper($cat->name)) . '</p>';
                }
            }
            ?>

            <!-- Tên sản phẩm -->
            <h1 class="product-title"><?php the_title(); ?></h1>

            <!-- Giá + Divider -->
            <div class="product-price-single">
                <?php if ($is_on_sale && $regular_price): ?>
                    <del><?php echo wc_price($regular_price); ?></del>
                <?php endif; ?>
                <span><?php echo wc_price($product->get_price()); ?></span>
            </div>

            <!-- Đánh giá sao WooCommerce -->
            <?php if (get_option('woocommerce_enable_reviews') === 'yes'): ?>
            <div class="product-rating-row">
                <?php
                $rating = $product->get_average_rating();
                $count  = $product->get_review_count();
                if ($rating > 0):
                ?>
                <div class="product-stars">
                    <?php for ($s = 1; $s <= 5; $s++): ?>
                        <?php if ($s <= floor($rating)): ?>
                            <span class="star star-full">&#9733;</span>
                        <?php elseif ($s - $rating < 1 && $s - $rating > 0): ?>
                            <span class="star star-half">&#9733;</span>
                        <?php else: ?>
                            <span class="star star-empty">&#9733;</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <a href="#product-reviews" class="product-rating-count">(<?php echo $count; ?> đánh giá)</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="gold-divider" style="justify-content:flex-start; margin:1.25rem 0;"><span>&#10022;</span></div>

            <!-- Mô tả ngắn hiển thị trước actions -->
            <?php if ($short_desc = $product->get_short_description()): ?>
            <div class="product-short-desc">
                <?php echo wp_kses_post($short_desc); ?>
            </div>
            <?php endif; ?>

            <!-- Variations (nếu là variable product) -->
            <?php if ($product->is_type('variable')): ?>
                <?php
                $attributes  = $product->get_variation_attributes();
                foreach ($attributes as $attr_name => $attr_options):
                    $label = wc_attribute_label($attr_name);
                    $is_size = (stripos($attr_name, 'size') !== false || stripos($attr_name, 'kích') !== false || stripos($attr_name, 'cỡ') !== false);
                ?>
                <div class="product-size-group">
                    <div class="product-size-label">
                        <span class="label-text"><?php echo esc_html($label); ?></span>
                        <?php if ($is_size): ?>
                        <a href="#" class="kieu-size-guide-link">Hướng dẫn chọn size</a>
                        <?php endif; ?>
                    </div>
                    <div class="size-grid">
                        <?php foreach ($attr_options as $opt): ?>
                            <button type="button"
                                    class="size-btn"
                                    data-attr="<?php echo esc_attr(strtolower($attr_name)); ?>"
                                    data-value="<?php echo esc_attr($opt); ?>">
                                <?php echo esc_html($opt); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

            <?php else: ?>
                <!-- Simple product: hiển thị size cứng (nếu có) -->
                <?php
                $sizes_attr = get_post_meta(get_the_ID(), '_kieu_sizes', true);
                $sizes = $sizes_attr ? explode(',', $sizes_attr) : ['XS','S','M','L','XL'];
                ?>
                <div class="product-size-group">
                    <div class="product-size-label">
                        <span>Size</span>
                        <a href="#" class="kieu-size-guide-link">Hướng dẫn chọn size</a>
                    </div>
                    <div class="size-grid">
                        <?php foreach ($sizes as $size): ?>
                            <button type="button" class="size-btn" data-value="<?php echo esc_attr(trim($size)); ?>">
                                <?php echo esc_html(trim($size)); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Số lượng + Nút thêm vào giỏ -->
            <form class="product-add-form" method="post">
                <?php wp_nonce_field('add-to-cart', '_wpnonce'); ?>
                <input type="hidden" name="product_id" value="<?php echo get_the_ID(); ?>">

                <div class="product-actions">
                    <!-- Quantity -->
                    <div class="qty-selector">
                        <button type="button" class="qty-btn" id="qtyMinus" aria-label="Giảm">&minus;</button>
                        <input type="number" class="qty-input" name="quantity" id="qtyInput" value="1" min="1" max="99">
                        <button type="button" class="qty-btn" id="qtyPlus" aria-label="Tăng">+</button>
                    </div>
                    <!-- Add to cart -->
                    <button type="submit" class="btn-add-to-cart">
                        <i class="fa-solid fa-bag-shopping" style="margin-right:8px"></i>
                        Thêm vào giỏ hàng
                    </button>
                </div>

                <!-- Mua ngay — VNPay -->
                <a href="<?php echo esc_url(wc_get_checkout_url() . '?add-to-cart=' . get_the_ID()); ?>"
                   class="btn-vnpay-now"
                   onclick="this.href=this.href + '&quantity=' + document.getElementById('qtyInput').value;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-right:8px;vertical-align:middle"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                    Mua Ngay &mdash; Thanh Toán VNPay
                </a>
            </form>

            <!-- Mô tả ngắn -->
            <?php if ($short_desc = $product->get_short_description()): ?>
            <div class="product-short-desc" style="margin-top:1.5rem; font-size:0.9rem; line-height:1.8; color:var(--color-text-muted);">
                <?php echo wp_kses_post($short_desc); ?>
            </div>
            <?php endif; ?>

            <!-- Cam kết dịch vụ -->
            <div class="product-policy-row">
                <?php
                $promises = [
                    ['icon' => 'fa-truck-fast',    'text' => 'Giao hàng toàn quốc'],
                    ['icon' => 'fa-rotate-left',   'text' => 'Đổi trả trong 7 ngày'],
                    ['icon' => 'fa-shield-halved', 'text' => 'Hàng chính hãng 100%'],
                    ['icon' => 'fa-headset',       'text' => 'Hỗ trợ 24/7'],
                ];
                foreach ($promises as $p):
                ?>
                <div class="product-policy-item">
                    <i class="fa-solid <?php echo $p['icon']; ?>"></i>
                    <?php echo esc_html($p['text']); ?>
                </div>
                <?php endforeach; ?>
            </div>

        </div><!-- .product-info -->
    </div><!-- .product-single-grid -->

    <!-- ====== Mô tả đầy đủ ====== -->
    <?php if ($description = $product->get_description()): ?>
    <div class="product-description-full">
        <h3 class="product-desc-title">Mô tả sản phẩm</h3>
        <div class="product-desc-body">
            <?php echo wp_kses_post($description); ?>
        </div>
    </div>
    <?php endif; ?>

</div><!-- .product-single -->


<!-- ====== Sản phẩm liên quan ====== -->
<?php
$related_ids = wc_get_related_products(get_the_ID(), 3);
if ($related_ids):
?>
<section class="products-section" aria-label="Sản phẩm liên quan">
    <h2 class="section-title">Có Thể Bạn Thích</h2>
    <div class="gold-divider"><span>✦</span></div>
    <div class="product-grid">
        <?php foreach ($related_ids as $rid):
            $rproduct = wc_get_product($rid);
        ?>
        <a href="<?php echo esc_url(get_permalink($rid)); ?>" class="product-card">
            <div class="product-card-image">
                <?php echo get_the_post_thumbnail($rid, 'kieu-product-portrait', ['alt' => get_the_title($rid), 'loading' => 'lazy']); ?>
                <div class="product-quick-add"
                     onclick="event.preventDefault(); kieuShop.quickAdd(<?php echo $rid; ?>, this)">
                    Thêm vào giỏ hàng
                </div>
            </div>
            <div class="product-card-info">
                <h3 class="product-card-name"><?php echo esc_html(get_the_title($rid)); ?></h3>
                <p class="product-card-price"><?php echo wc_price($rproduct->get_price()); ?></p>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php endwhile; ?>

<?php
// ── Modal Hướng dẫn chọn size ─────────────────────────────────────
$size_guide_img  = get_option('kieu_size_guide_image', '');
$size_guide_note = get_option('kieu_size_guide_note', '');
?>
<!-- Size Guide Modal -->
<div id="kieuSizeGuideModal" class="ksg-overlay" aria-hidden="true" role="dialog" aria-label="Hướng dẫn chọn size">
    <div class="ksg-modal">
        <button class="ksg-close" id="kieuSizeGuideClose" aria-label="Đóng">&times;</button>
        <h2 class="ksg-title">📏 Hướng dẫn chọn size</h2>

        <?php if ($size_guide_img): ?>
            <div class="ksg-image">
                <img src="<?php echo esc_url($size_guide_img); ?>" alt="Bảng size KIỀU" loading="lazy">
            </div>
        <?php else: ?>
            <!-- Bảng size mặc định nếu chưa upload ảnh -->
            <div class="ksg-default-table">
                <table>
                    <thead>
                        <tr><th>Size</th><th>Chiều cao</th><th>Cân nặng</th><th>Vòng ngực</th><th>Vòng eo</th><th>Vòng mông</th></tr>
                    </thead>
                    <tbody>
                        <tr><td>XS</td><td>150–155 cm</td><td>40–46 kg</td><td>78–82 cm</td><td>60–64 cm</td><td>84–88 cm</td></tr>
                        <tr><td>S</td><td>155–160 cm</td><td>46–52 kg</td><td>82–86 cm</td><td>64–68 cm</td><td>88–92 cm</td></tr>
                        <tr><td>M</td><td>158–163 cm</td><td>52–58 kg</td><td>86–90 cm</td><td>68–72 cm</td><td>92–96 cm</td></tr>
                        <tr><td>L</td><td>160–165 cm</td><td>58–65 kg</td><td>90–94 cm</td><td>72–76 cm</td><td>96–100 cm</td></tr>
                        <tr><td>XL</td><td>162–168 cm</td><td>65–72 kg</td><td>94–98 cm</td><td>76–80 cm</td><td>100–104 cm</td></tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if ($size_guide_note): ?>
            <p class="ksg-note"><?php echo nl2br(esc_html($size_guide_note)); ?></p>
        <?php else: ?>
            <p class="ksg-note">💡 Mẹo: Nếu số đo của bạn nằm giữa hai size, hãy chọn size lớn hơn để thoải mái hơn. Với áo dài, ưu tiên chọn theo vòng ngực và vòng eo.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.ksg-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.55);
    z-index: 99999;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}
.ksg-overlay.is-open { display: flex; animation: ksgFadeIn .2s ease; }
@keyframes ksgFadeIn { from { opacity:0 } to { opacity:1 } }

.ksg-modal {
    background: #fff;
    border-radius: 12px;
    max-width: 740px;
    width: 100%;
    max-height: 88vh;
    overflow-y: auto;
    padding: 2rem;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    animation: ksgSlideUp .25s ease;
}
@keyframes ksgSlideUp { from { transform: translateY(20px); opacity:0 } to { transform: translateY(0); opacity:1 } }

.ksg-close {
    position: absolute; top: 1rem; right: 1rem;
    background: none; border: none; font-size: 1.6rem;
    cursor: pointer; color: #888; line-height: 1;
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.ksg-close:hover { background: #f4f0eb; color: #333; }

.ksg-title {
    font-family: 'Cormorant Garamond', serif;
    font-size: 1.5rem;
    color: #2C1810;
    margin-bottom: 1.25rem;
    padding-bottom: .75rem;
    border-bottom: 1px solid #E8E4DF;
}
.ksg-image img { width: 100%; border-radius: 8px; display: block; }

.ksg-default-table { overflow-x: auto; }
.ksg-default-table table { width: 100%; border-collapse: collapse; font-size: .9rem; }
.ksg-default-table th {
    background: #2C1810; color: #C9A96E;
    padding: .6rem 1rem; text-align: left; font-weight: 600;
    letter-spacing: .04em; font-size: .78rem; text-transform: uppercase;
}
.ksg-default-table td { padding: .6rem 1rem; border-bottom: 1px solid #F0EDED; }
.ksg-default-table tr:last-child td { border-bottom: none; }
.ksg-default-table tr:hover td { background: #FDF9F5; }
.ksg-default-table td:first-child { font-weight: 700; color: #8C2020; }

.ksg-note {
    margin-top: 1rem;
    font-size: .85rem;
    color: #666;
    background: #FDF9F5;
    border-left: 3px solid #C9A96E;
    padding: .75rem 1rem;
    border-radius: 0 6px 6px 0;
    line-height: 1.6;
}

/* Link chọn size */
.product-size-label a.kieu-size-guide-link {
    font-size: .72rem;
    color: #8C2020;
    text-decoration: underline;
    letter-spacing: .06em;
    text-transform: uppercase;
    cursor: pointer;
}
</style>

<script>
(function() {
    const modal  = document.getElementById('kieuSizeGuideModal');
    const btnClose = document.getElementById('kieuSizeGuideClose');
    if (!modal) return;

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    document.addEventListener('click', function(e) {
        if (e.target.closest('.kieu-size-guide-link')) {
            e.preventDefault();
            openModal();
        }
    });

    btnClose.addEventListener('click', closeModal);

    modal.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
})();
</script>

<?php get_footer(); ?>
