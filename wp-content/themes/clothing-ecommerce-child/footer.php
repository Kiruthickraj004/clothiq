<?php
/**
 * Theme footer — Elementor Theme Builder location with theme fallback.
 *
 * @package Clothing_Ecommerce_Child
 */

if ( ! clothiq_elementor_theme_do_location( 'footer' ) ) {
    get_template_part( 'template-parts/footer', 'default' );
}

wp_footer();
?>
</body>
</html>
