<?php
/**
 * Blog index — Elementor friendly.
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( clothiq_elementor_theme_do_location( 'archive' ) ) {
    get_footer();
    return;
}

echo '<div class="container py-5">';

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( 'mb-5' ); ?>>
            <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="entry-content">
                <?php the_content(); ?>
            </div>
        </article>
        <?php
    endwhile;
    the_posts_pagination();
else :
    echo '<p>' . esc_html__( 'No posts found.', 'clothing-ecommerce-child' ) . '</p>';
endif;

echo '</div>';

get_footer();
