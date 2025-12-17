<?php
// functions.php
add_action('after_setup_theme', function(){
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption']);
    register_nav_menus(['primary' => 'Primary Menu']);

    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    add_image_size('shop_catalog', 360, 360, true);
    add_image_size('shop_single', 800, 800, true);
    add_image_size('shop_thumbnail', 150, 150, true);
});

add_action('wp_enqueue_scripts', function(){
    $ver = wp_get_theme()->get('Version') ?: time();
    wp_enqueue_style('essence-core', get_template_directory_uri() . '/assets/css/core-style.css', [], $ver);
    wp_enqueue_style('owl-carousel', get_template_directory_uri() . '/assets/css/owl.carousel.min.css', ['essence-core'], '2.3.4');
    wp_enqueue_style('owl-theme', get_template_directory_uri() . '/assets/css/owl.theme.default.min.css', ['owl-carousel'], '2.3.4');
    wp_enqueue_style('essence-style', get_template_directory_uri() . '/assets/css/style.css', ['essence-core','owl-carousel'], $ver);
    wp_enqueue_style('theme-root-style', get_stylesheet_uri(), ['essence-style'], $ver);
    wp_enqueue_script('popper', get_template_directory_uri() . '/assets/js/popper.min.js', [], '2.11.0', true);
    wp_enqueue_script('bootstrap-js', get_template_directory_uri() . '/assets/js/bootstrap.min.js', ['jquery','popper'], '5.3.0', true);
    wp_enqueue_script('essence-plugins', get_template_directory_uri() . '/assets/js/plugins.js', ['jquery'], $ver, true);
    wp_enqueue_script('classy-nav', get_template_directory_uri() . '/assets/js/classy-nav.min.js', ['jquery'], $ver, true);
    wp_enqueue_script('owl-carousel', get_template_directory_uri() . '/assets/js/owl.carousel.min.js', ['jquery'], '2.3.4', true);
    wp_enqueue_script('theme-js', get_template_directory_uri() . '/assets/js/active.js', ['jquery','essence-plugins','owl-carousel','classy-nav','bootstrap-js'], $ver, true);
    wp_localize_script('theme-js', 'themeVars', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'site_url' => home_url('/'),
        'theme_dir' => get_template_directory_uri(),
    ]);
});
add_action('widgets_init', function(){
    register_sidebar([
        'name' => 'Primary Sidebar',
        'id' => 'sidebar-1',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h5>',
        'after_title' => '</h5>',
    ]);
});
add_action('woocommerce_before_main_content', function(){
    echo '<main id="content" class="container py-4">';
}, 10);
add_action('woocommerce_after_main_content', function(){
    echo '</main>';
}, 10);
add_action( 'wp_enqueue_scripts', function() {
    if ( ! function_exists( 'is_woocommerce' ) ) {
        return;
    }

    if ( is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag() || is_post_type_archive( 'product' ) ) {
        if ( ! wp_style_is( 'woocommerce-layout', 'enqueued' ) ) {
            wp_enqueue_style( 'woocommerce-layout' );
        }
        if ( ! wp_style_is( 'woocommerce-smallscreen', 'enqueued' ) ) {
            wp_enqueue_style( 'woocommerce-smallscreen' );
        }
        if ( ! wp_style_is( 'woocommerce-general', 'enqueued' ) ) {
            wp_enqueue_style( 'woocommerce-general' );
        }
        if ( wp_script_is( 'wc-price-slider', 'registered' ) ) {
            wp_enqueue_script( 'wc-price-slider' );
        } else {
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-slider' );
        }
        wp_enqueue_script( 'jquery' );
    }
}, 20 );
require_once get_template_directory() . '/inc/cart-modal.php';

add_filter( 'woocommerce_add_to_cart_redirect', 'tch_buy_now_redirect_to_checkout' );
function tch_buy_now_redirect_to_checkout( $url ) {
    if ( isset( $_REQUEST['buy_now'] ) && (int) $_REQUEST['buy_now'] === 1 ) {
        return wc_get_checkout_url();
    }
    return $url;
}





