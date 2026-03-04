<?php
/**
 * single.php — KIỀU
 * Template cho blog post / Lookbook entry
 */
get_header();
?>

<main style="background:var(--color-bg); min-height:70vh; padding-bottom:5rem;">

    <?php while (have_posts()): the_post(); ?>

    <!-- Hero ảnh bìa -->
    <?php if (has_post_thumbnail()): ?>
    <div class="single-hero">
        <?php the_post_thumbnail('full', ['class' => 'single-hero-img', 'loading' => 'eager']); ?>
        <div class="single-hero-overlay">
            <div class="single-hero-meta">
                <span class="single-category">
                    <?php echo get_the_category_list(', '); ?>
                </span>
                <h1 class="single-title"><?php the_title(); ?></h1>
                <p class="single-date">
                    <i class="fa-regular fa-calendar"></i>
                    <?php echo get_the_date('d/m/Y'); ?>
                    &nbsp;·&nbsp;
                    <i class="fa-regular fa-clock"></i>
                    <?php echo ceil(str_word_count(strip_tags(get_the_content())) / 200); ?> phút đọc
                </p>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Không có ảnh: text header -->
    <div style="text-align:center; padding:4rem 1rem 2rem;">
        <?php $cats = get_the_category(); if ($cats): ?>
            <p class="section-subtitle">— <?php echo esc_html($cats[0]->name); ?> —</p>
        <?php endif; ?>
        <h1 class="section-title"><?php the_title(); ?></h1>
        <div class="gold-divider"><span>&#10022;</span></div>
        <p style="color:var(--color-text-muted);font-size:0.85rem;margin-top:0.75rem">
            <?php echo get_the_date('d/m/Y'); ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Nội dung bài viết -->
    <div class="single-wrap">

        <article class="single-content">
            <?php the_content(); ?>

            <!-- Tags -->
            <?php $tags = get_the_tags(); if ($tags): ?>
            <div class="single-tags">
                <?php foreach ($tags as $tag): ?>
                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="single-tag">
                        #<?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Chia sẻ -->
            <div class="single-share">
                <span>Chia sẻ:</span>
                <?php $share_url = urlencode(get_permalink()); $share_title = urlencode(get_the_title()); ?>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank" rel="noopener" class="share-btn share-fb">
                    <i class="fa-brands fa-facebook-f"></i> Facebook
                </a>
                <a href="https://zalo.me/share/url?url=<?php echo $share_url; ?>&title=<?php echo $share_title; ?>" target="_blank" rel="noopener" class="share-btn share-zalo">
                    Zalo
                </a>
            </div>
        </article>

        <!-- Sidebar: bài liên quan -->
        <aside class="single-sidebar">
            <h3 class="sidebar-title">Bài Viết Khác</h3>
            <?php
            $related = new WP_Query([
                'post_type'      => 'post',
                'posts_per_page' => 4,
                'post__not_in'   => [get_the_ID()],
                'orderby'        => 'rand',
            ]);
            while ($related->have_posts()): $related->the_post();
            ?>
            <a href="<?php the_permalink(); ?>" class="sidebar-post-item">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('thumbnail', ['class' => 'sidebar-post-thumb', 'loading' => 'lazy']); ?>
                <?php endif; ?>
                <div>
                    <p class="sidebar-post-title"><?php the_title(); ?></p>
                    <p class="sidebar-post-date"><?php echo get_the_date('d/m/Y'); ?></p>
                </div>
            </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </aside>

    </div><!-- .single-wrap -->

    <?php endwhile; ?>

</main>

<?php get_footer(); ?>
