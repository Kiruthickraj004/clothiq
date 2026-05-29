<?php
/**
 * Elementor theme compatibility.
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Is Elementor plugin loaded?
 */
function clothiq_is_elementor_active() {
    return did_action( 'elementor/loaded' );
}

/**
 * Is Elementor Pro active?
 */
function clothiq_is_elementor_pro_active() {
    return defined( 'ELEMENTOR_PRO_VERSION' ) || did_action( 'elementor_pro/init' );
}

/**
 * Was a post built with Elementor?
 *
 * @param int $post_id Post ID.
 */
function clothiq_is_elementor_built( $post_id = 0 ) {
    if ( ! clothiq_is_elementor_active() ) {
        return false;
    }

    $post_id = $post_id ? (int) $post_id : get_the_ID();
    if ( ! $post_id ) {
        return false;
    }

    return \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id );
}

/**
 * Render saved Elementor content for a post.
 *
 * @param int $post_id Post ID.
 */
function clothiq_get_elementor_content( $post_id ) {
    if ( ! clothiq_is_elementor_built( $post_id ) ) {
        return '';
    }

    return \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_id );
}

/**
 * Output Elementor Theme Builder location (Elementor Pro).
 *
 * @param string $location Theme location slug.
 */
function clothiq_elementor_theme_do_location( $location ) {
    if ( ! function_exists( 'elementor_theme_do_location' ) ) {
        return false;
    }

    return elementor_theme_do_location( $location );
}

/**
 * Should the theme skip its custom template and use Elementor output?
 *
 * @param int $post_id Post ID.
 */
function clothiq_use_elementor_canvas_content( $post_id = 0 ) {
    $post_id = $post_id ? (int) $post_id : get_queried_object_id();

    if ( ! $post_id || ! clothiq_is_elementor_built( $post_id ) ) {
        return false;
    }

    if ( clothiq_elementor_theme_do_location( 'single' ) ) {
        return true;
    }

    return true;
}

/**
 * Register Elementor Theme Builder locations (Elementor Pro).
 */
function clothiq_register_elementor_locations( $elementor_theme_manager ) {
    if ( ! clothiq_is_elementor_pro_active() ) {
        return;
    }

    if ( method_exists( $elementor_theme_manager, 'register_all_core_location' ) ) {
        $elementor_theme_manager->register_all_core_location();
    }
}
add_action( 'elementor/theme/register_locations', 'clothiq_register_elementor_locations' );

/**
 * Theme supports for Elementor layouts.
 */
function clothiq_elementor_theme_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
}
add_action( 'after_setup_theme', 'clothiq_elementor_theme_setup', 12 );

/**
 * Enable Elementor on WooCommerce products and other public types.
 */
function clothiq_add_elementor_post_type_support() {
    if ( ! clothiq_is_elementor_active() ) {
        return;
    }

    add_post_type_support( 'product', 'elementor' );
    add_post_type_support( 'page', 'elementor' );
    add_post_type_support( 'post', 'elementor' );
}
add_action( 'init', 'clothiq_add_elementor_post_type_support', 20 );

/**
 * Recommended Elementor content width (Site Settings can override).
 */
function clothiq_elementor_content_width() {
    return 1140;
}
add_filter( 'elementor/frontend/content_width', 'clothiq_elementor_content_width' );

/**
 * Enqueue compatibility styles on Elementor pages.
 */
function clothiq_elementor_enqueue_styles() {
    if ( ! clothiq_is_elementor_active() ) {
        return;
    }

    $load = false;

    if ( is_singular() && clothiq_is_elementor_built( get_queried_object_id() ) ) {
        $load = true;
    }

    if ( function_exists( 'is_shop' ) && is_shop() && clothiq_is_elementor_built( wc_get_page_id( 'shop' ) ) ) {
        $load = true;
    }

    if ( $load || clothiq_is_elementor_pro_active() ) {
        wp_enqueue_style(
            'clothiq-elementor',
            get_stylesheet_directory_uri() . '/assets/css/elementor.css',
            array( 'clothing-ecommerce-child-style' ),
            wp_get_theme()->get( 'Version' )
        );
    }
}
add_action( 'wp_enqueue_scripts', 'clothiq_elementor_enqueue_styles', 30 );

/**
 * Body classes for Elementor layouts.
 */
function clothiq_elementor_body_class( $classes ) {
    if ( is_singular() && clothiq_is_elementor_built( get_queried_object_id() ) ) {
        $classes[] = 'clothiq-elementor-page';
    }

    if ( function_exists( 'is_shop' ) && is_shop() && clothiq_is_elementor_built( wc_get_page_id( 'shop' ) ) ) {
        $classes[] = 'clothiq-elementor-shop';
    }

    return $classes;
}
add_filter( 'body_class', 'clothiq_elementor_body_class' );

/**
 * Full-width content when Elementor controls the page (reduce theme wrapper constraints).
 */
function clothiq_elementor_page_wrapper_open() {
    if ( ! is_singular() || ! clothiq_is_elementor_built( get_queried_object_id() ) ) {
        echo '<main id="content" class="site-main clothiq-site-main">';
        return;
    }

    echo '<main id="content" class="site-main clothiq-site-main clothiq-site-main--elementor">';
}
add_action( 'clothiq_before_page_content', 'clothiq_elementor_page_wrapper_open' );

function clothiq_elementor_page_wrapper_close() {
    echo '</main>';
}
add_action( 'clothiq_after_page_content', 'clothiq_elementor_page_wrapper_close' );

/**
 * WooCommerce: use full width wrapper only when theme template runs.
 */
function clothiq_woocommerce_wrapper_start() {
    if ( clothiq_is_elementor_built( get_queried_object_id() ) ) {
        echo '<main id="content" class="site-main clothiq-site-main clothiq-site-main--elementor woocommerce">';
        return;
    }

    if ( function_exists( 'is_shop' ) && is_shop() && clothiq_is_elementor_built( wc_get_page_id( 'shop' ) ) ) {
        echo '<main id="content" class="site-main clothiq-site-main clothiq-site-main--elementor woocommerce">';
        return;
    }

    echo '<main id="content" class="container py-4 woocommerce">';
}
add_action( 'woocommerce_before_main_content', 'clothiq_woocommerce_wrapper_start', 10 );

function clothiq_woocommerce_wrapper_end() {
    echo '</main>';
}
add_action( 'woocommerce_after_main_content', 'clothiq_woocommerce_wrapper_end', 10 );

/**
 * Replace parent theme WooCommerce wrapper with Elementor-aware wrapper.
 */
function clothiq_replace_woocommerce_wrapper() {
    remove_action( 'woocommerce_before_main_content', 'clothing_ecommerce_wc_wrapper_start', 10 );
    remove_action( 'woocommerce_after_main_content', 'clothing_ecommerce_wc_wrapper_end', 10 );
}
add_action( 'wp', 'clothiq_replace_woocommerce_wrapper', 5 );
