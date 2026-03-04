<?php get_header(); ?>
<main class="container" style="padding:3rem 1rem; min-height:60vh;">
    <?php if (have_posts()): while (have_posts()): the_post(); ?>
        <article>
            <h1 style="font-family:'Cormorant Garamond',serif; margin-bottom:1rem;"><?php the_title(); ?></h1>
            <div style="line-height:1.8;"><?php the_content(); ?></div>
        </article>
    <?php endwhile; endif; ?>
</main>
<?php get_footer(); ?>
