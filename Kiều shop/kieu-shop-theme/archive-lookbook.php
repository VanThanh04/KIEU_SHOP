<?php
/**
 * archive-lookbook.php — KIỀU (v2 — Masonry Editorial Style)
 * Trendy asymmetric grid — Vogue-Asia aesthetic
 */
get_header();

$paged     = get_query_var('paged') ?: 1;
$lookbooks = new WP_Query([
    'post_type'      => 'lookbook',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'orderby'        => 'date',
    'order'          => 'DESC',
]);

// Collect posts into array for layout control
$posts_arr = [];
if ($lookbooks->have_posts()):
    while ($lookbooks->have_posts()): $lookbooks->the_post();
        $posts_arr[] = [
            'id'    => get_the_ID(),
            'title' => get_the_title(),
            'date'  => get_the_date('d/m/Y'),
            'link'  => get_permalink(),
            'img'   => get_the_post_thumbnail_url(null, 'large'),
            'idx'   => count($posts_arr) + 1,
        ];
    endwhile;
    wp_reset_postdata();
endif;
?>

<main class="lookbook-main-v2">

    <!-- Typographic header -->
    <div class="lk-header">
        <div class="lk-header-text">
            <h1 class="lk-heading">LOOKBOOK</h1>
            <span class="lk-year">2025</span>
        </div>
        <p class="lk-subtitle">Những khoảnh khắc đẹp — phong cách sống</p>
        <div class="lk-header-line"></div>
    </div>

    <?php if (!empty($posts_arr)): ?>

    <!-- Masonry editorial grid (asymmetric) -->
    <div class="lk-masonry-wrap">

        <?php
        // Group posts into rows: row1 = [tall, tall, landscape] pattern
        // Then row2 = [3 equal portraits], row3 = [2 wide] etc.
        $chunk_layouts = [
            'featured', // first 3 posts — featured row
            'trio',     // next 3 — equal trio
            'duo',      // next 2  — wide duo
        ];

        $pos    = 0;
        $layout = 0;

        while ($pos < count($posts_arr)):
            $ltype = $chunk_layouts[$layout % count($chunk_layouts)];

            if ($ltype === 'featured'): // Featured: 2 tall left + 1 portrait right
                $p1 = $posts_arr[$pos]   ?? null;
                $p2 = $posts_arr[$pos+1] ?? null;
                $p3 = $posts_arr[$pos+2] ?? null;
                $pos += 3;
        ?>
        <div class="lk-row lk-row--featured">
            <?php if ($p1): ?>
            <a href="<?php echo esc_url($p1['link']); ?>" class="lk-item lk-item--tall">
                <?php if ($p1['img']): ?>
                    <img src="<?php echo esc_url($p1['img']); ?>" alt="<?php echo esc_attr($p1['title']); ?>" loading="lazy" class="lk-img">
                <?php else: ?>
                    <div class="lk-placeholder"><i class="fa-solid fa-camera"></i></div>
                <?php endif; ?>
                <div class="lk-overlay">
                    <span class="lk-counter"><?php printf('%02d / %02d', $p1['idx'], count($posts_arr)); ?></span>
                    <p class="lk-item-title"><?php echo esc_html($p1['title']); ?></p>
                    <span class="lk-arrow">→</span>
                </div>
                <div class="lk-label">ÁO DÀI CÁCH TÂN <?php printf('%02d', $p1['idx']); ?></div>
            </a>
            <?php endif; ?>
            <?php if ($p2): ?>
            <a href="<?php echo esc_url($p2['link']); ?>" class="lk-item lk-item--tall">
                <?php if ($p2['img']): ?>
                    <img src="<?php echo esc_url($p2['img']); ?>" alt="<?php echo esc_attr($p2['title']); ?>" loading="lazy" class="lk-img">
                <?php else: ?>
                    <div class="lk-placeholder"><i class="fa-solid fa-camera"></i></div>
                <?php endif; ?>
                <div class="lk-overlay">
                    <span class="lk-counter"><?php printf('%02d / %02d', $p2['idx'], count($posts_arr)); ?></span>
                    <p class="lk-item-title"><?php echo esc_html($p2['title']); ?></p>
                    <span class="lk-arrow">→</span>
                </div>
                <div class="lk-label">ÁO DÀI CÁCH TÂN <?php printf('%02d', $p2['idx']); ?></div>
            </a>
            <?php endif; ?>
            <?php if ($p3): ?>
            <a href="<?php echo esc_url($p3['link']); ?>" class="lk-item lk-item--portrait-right">
                <?php if ($p3['img']): ?>
                    <img src="<?php echo esc_url($p3['img']); ?>" alt="<?php echo esc_attr($p3['title']); ?>" loading="lazy" class="lk-img">
                <?php else: ?>
                    <div class="lk-placeholder"><i class="fa-solid fa-camera"></i></div>
                <?php endif; ?>
                <div class="lk-overlay">
                    <span class="lk-counter"><?php printf('%02d / %02d', $p3['idx'], count($posts_arr)); ?></span>
                    <p class="lk-item-title"><?php echo esc_html($p3['title']); ?></p>
                    <span class="lk-arrow">→</span>
                </div>
                <div class="lk-label">ÁO DÀI CÁCH TÂN <?php printf('%02d', $p3['idx']); ?></div>
            </a>
            <?php endif; ?>
        </div><!-- .lk-row--featured -->

            <?php elseif ($ltype === 'trio'): // Equal trio row
                $p1 = $posts_arr[$pos]   ?? null;
                $p2 = $posts_arr[$pos+1] ?? null;
                $p3 = $posts_arr[$pos+2] ?? null;
                $pos += 3;
        ?>
        <div class="lk-row lk-row--trio">
            <?php foreach (array_filter([$p1, $p2, $p3]) as $p): ?>
            <a href="<?php echo esc_url($p['link']); ?>" class="lk-item lk-item--equal">
                <?php if ($p['img']): ?>
                    <img src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr($p['title']); ?>" loading="lazy" class="lk-img">
                <?php else: ?>
                    <div class="lk-placeholder"><i class="fa-solid fa-camera"></i></div>
                <?php endif; ?>
                <div class="lk-overlay">
                    <span class="lk-counter"><?php printf('%02d / %02d', $p['idx'], count($posts_arr)); ?></span>
                    <p class="lk-item-title"><?php echo esc_html($p['title']); ?></p>
                    <span class="lk-arrow">→</span>
                </div>
                <div class="lk-label">ÁO DÀI CÁCH TÂN <?php printf('%02d', $p['idx']); ?></div>
            </a>
            <?php endforeach; ?>
        </div><!-- .lk-row--trio -->

            <?php else: // Duo row — 2 wide items
                $p1 = $posts_arr[$pos]   ?? null;
                $p2 = $posts_arr[$pos+1] ?? null;
                $pos += 2;
        ?>
        <div class="lk-row lk-row--duo">
            <?php foreach (array_filter([$p1, $p2]) as $p): ?>
            <a href="<?php echo esc_url($p['link']); ?>" class="lk-item lk-item--wide">
                <?php if ($p['img']): ?>
                    <img src="<?php echo esc_url($p['img']); ?>" alt="<?php echo esc_attr($p['title']); ?>" loading="lazy" class="lk-img">
                <?php else: ?>
                    <div class="lk-placeholder"><i class="fa-solid fa-camera"></i></div>
                <?php endif; ?>
                <div class="lk-overlay">
                    <span class="lk-counter"><?php printf('%02d / %02d', $p['idx'], count($posts_arr)); ?></span>
                    <p class="lk-item-title"><?php echo esc_html($p['title']); ?></p>
                    <span class="lk-arrow">→</span>
                </div>
                <div class="lk-label">ÁO DÀI CÁCH TÂN <?php printf('%02d', $p['idx']); ?></div>
            </a>
            <?php endforeach; ?>
        </div><!-- .lk-row--duo -->

            <?php endif; ?>
            <?php $layout++; endwhile; ?>

    </div><!-- .lk-masonry-wrap -->

    <!-- Pagination -->
    <?php
    $total_pages = $lookbooks->max_num_pages;
    if ($total_pages > 1):
    ?>
    <div class="lookbook-pagination">
        <?php echo paginate_links([
            'base'      => str_replace(PHP_INT_MAX, '%#%', esc_url(get_pagenum_link(PHP_INT_MAX))),
            'format'    => '?paged=%#%',
            'current'   => max(1, $paged),
            'total'     => $total_pages,
            'prev_text' => '← Trang trước',
            'next_text' => 'Trang tiếp →',
        ]); ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div style="text-align:center; padding:6rem 2rem; color:var(--color-text-muted);">
        <i class="fa-solid fa-camera-retro" style="font-size:3rem; opacity:0.2; display:block; margin-bottom:1.5rem"></i>
        <p>Lookbook đang được cập nhật. Quay lại sớm nhé!</p>
    </div>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
