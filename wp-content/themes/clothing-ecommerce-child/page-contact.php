<?php
/**
 * Template Name: Contact Page
 *
 * Uses Elementor content when the page is built with Elementor;
 * otherwise shows the default theme contact layout.
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        if ( clothiq_is_elementor_built( get_the_ID() ) ) :
            echo '<div class="clothiq-elementor-content clothiq-contact-elementor">';
            the_content();
            echo '</div>';
        else :
            ?>
            <div class="contact-area d-flex align-items-center">
                <div class="google-map">
                    <div id="googleMap"></div>
                </div>
                <div class="contact-info">
                    <h2><?php esc_html_e( 'How to Find Us', 'clothing-ecommerce-child' ); ?></h2>
                    <p>
                        <?php
                        echo esc_html__(
                            'Mauris viverra cursus ante laoreet eleifend. Donec vel fringilla ante. Aenean finibus velit id urna vehicula, nec maximus est sollicitudin.',
                            'clothing-ecommerce-child'
                        );
                        ?>
                    </p>
                    <div class="contact-address mt-50">
                        <p><span><?php esc_html_e( 'address:', 'clothing-ecommerce-child' ); ?></span> tamilnadu, India.</p>
                        <p><span><?php esc_html_e( 'telephone:', 'clothing-ecommerce-child' ); ?></span> +12 34 567 890</p>
                        <p><a href="mailto:example@gmail.com">example@gmail.com</a></p>
                    </div>
                </div>
            </div>
            <?php
        endif;
    endwhile;
endif;

get_footer();
