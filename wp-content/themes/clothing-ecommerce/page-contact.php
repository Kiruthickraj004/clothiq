<?php
/**
 * Template Name: Contact Page
 */
defined( 'ABSPATH' ) || exit;
get_header();
?>

<!-- ##### Contact Area Start ##### -->
<div class="contact-area d-flex align-items-center">

    <!-- Google Map -->
    <div class="google-map">
        <div id="googleMap"></div>
    </div>

    <!-- Contact Info -->
    <div class="contact-info">
        <h2><?php esc_html_e( 'How to Find Us', 'clothing-ecommerce' ); ?></h2>

        <p>
            <?php
            echo esc_html__(
                'Mauris viverra cursus ante laoreet eleifend. Donec vel fringilla ante. Aenean finibus velit id urna vehicula, nec maximus est sollicitudin.',
                'clothing-ecommerce'
            );
            ?>
        </p>

        <div class="contact-address mt-50">
            <p>
                <span><?php esc_html_e( 'address:', 'clothing-ecommerce' ); ?></span>
                tamilnadu, India.
            </p>
            <p>
                <span><?php esc_html_e( 'telephone:', 'clothing-ecommerce' ); ?></span>
                +12 34 567 890
            </p>
            <p>
                <a href="mailto:example@gmail.com">example@gmail.com</a>
            </p>
        </div>
    </div>

</div>
<!-- ##### Contact Area End ##### -->

<?php get_footer(); ?>
