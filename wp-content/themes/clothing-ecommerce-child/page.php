<?php
/**
 * Page template — Elementor friendly.
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( clothiq_elementor_theme_do_location( 'single' ) ) {
    get_footer();
    return;
}

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $is_elementor = clothiq_is_elementor_built( get_the_ID() );
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class( $is_elementor ? 'clothiq-elementor-content' : '' ); ?>>
            <?php
            do_action( 'clothiq_before_page_content' );

            if ( ! $is_elementor ) {
                echo '<div class="container py-5">';
                echo '<h1 class="entry-title">' . esc_html( get_the_title() ) . '</h1>';
            }

            echo '<div class="entry-content">';
            the_content();
            echo '</div>';

            if ( ! $is_elementor ) {
                echo '</div>';
            }

            do_action( 'clothiq_after_page_content' );
            ?>
        </article>
        <?php
    endwhile;
endif;

get_footer();
