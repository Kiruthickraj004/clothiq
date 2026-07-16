<?php
/**
 * Child Theme Functions
 * 
 * This file is for child theme specific functionality.
 * Parent theme functions are loaded automatically.
 */

require_once get_stylesheet_directory() . '/inc/elementor.php';

add_action( 'after_setup_theme', function() {
    add_theme_support(
        'custom-logo',
        array(
            'height'      => 80,
            'width'       => 240,
            'flex-height' => true,
            'flex-width'  => true,
        )
    );
}, 11 );

/**
 * Site logo markup for the navbar (Customizer logo or theme fallback).
 */
function clothiq_get_navbar_logo_html() {
    if ( has_custom_logo() ) {
        return wp_get_attachment_image(
            get_theme_mod( 'custom_logo' ),
            'full',
            false,
            array(
                'class' => 'site-logo',
                'alt'   => get_bloginfo( 'name', 'display' ),
            )
        );
    }

    $fallback = get_template_directory_uri() . '/assets/img/core-img/logo-clothiq.png';
    if ( file_exists( get_template_directory() . '/assets/img/core-img/logo-clothiq.png' ) ) {
        return sprintf(
            '<img src="%s" alt="%s" class="site-logo" width="160" height="48">',
            esc_url( $fallback ),
            esc_attr( get_bloginfo( 'name', 'display' ) )
        );
    }

    return sprintf(
        '<span class="site-logo-text">%s</span>',
        esc_html( get_bloginfo( 'name', 'display' ) )
    );
}

/**
 * SVG icons for My Account navigation and dashboard cards.
 */
function clothiq_get_account_nav_icon( $endpoint ) {
    $icons = array(
        'dashboard'    => '<svg viewBox="0 0 24 24" stroke-width="1.8"><rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/></svg>',
        'orders'       => '<svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6l-1.5-3H3"/><circle cx="9" cy="19" r="1"/><circle cx="18" cy="19" r="1"/></svg>',
        'downloads'    => '<svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 3v12"/><path d="M7 10l5 5 5-5"/><path d="M5 21h14"/></svg>',
        'edit-address' => '<svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M12 21s7-4.5 7-11a7 7 0 1 0-14 0c0 6.5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>',
        'edit-account' => '<svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M5 21c1.5-4 13.5-4 14 0"/></svg>',
        'customer-logout' => '<svg viewBox="0 0 24 24" stroke-width="1.8"><path d="M10 17l-5-5 5-5"/><path d="M5 12h14"/><path d="M19 5v14"/></svg>',
    );

    return $icons[ $endpoint ] ?? '<svg viewBox="0 0 24 24" stroke-width="1.8"><circle cx="12" cy="12" r="8"/></svg>';
}

// Enqueue child theme styles after parent styles
add_action( 'wp_enqueue_scripts', function() {
    $parent_style = 'theme-root-style'; // This is the parent theme's main style
    $version      = wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'clothing-ecommerce-child-style',
        get_stylesheet_uri(),
        array( $parent_style ),
        $version
    );

    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        wp_enqueue_style(
            'clothiq-my-account',
            get_stylesheet_directory_uri() . '/assets/css/my-account.css',
            array( 'clothing-ecommerce-child-style' ),
            $version
        );
    }

    // Enqueue gallery script on single product page
    if ( is_product() ) {
        wp_enqueue_script(
            'product-gallery-slider',
            get_stylesheet_directory_uri() . '/assets/js/gallery.js',
            array(),
            $version,
            true
        );

        // Enqueue product options script
        wp_enqueue_script(
            'product-options',
            get_stylesheet_directory_uri() . '/assets/js/product-options.js',
            array( 'jquery' ),
            $version,
            true
        );
    }
}, 20 );

add_filter( 'body_class', function( $classes ) {
    if ( function_exists( 'is_account_page' ) && is_account_page() ) {
        $classes[] = 'clothiq-account-page';
    }
    return $classes;
} );

add_filter( 'woocommerce_account_menu_item_classes', function( $classes, $endpoint ) {
    $classes[] = 'clothiq-account-nav__item';
    return $classes;
}, 10, 2 );

/**
 * Get all available sizes in a category
 */
function get_category_available_sizes( $category_id ) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    );

    $products = get_posts( $args );
    $sizes = array();

    $size_labels = ['s' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'xxl' => 'XXL'];

    foreach ( $products as $product ) {
        $product_sizes = get_post_meta( $product->ID, '_custom_sizes', true );
        if ( is_array( $product_sizes ) ) {
            foreach ( $product_sizes as $size ) {
                if ( isset( $size_labels[ $size ] ) && ! in_array( $size, $sizes, true ) ) {
                    $sizes[] = $size;
                }
            }
        }
    }

    return $sizes;
}

/**
 * Get all available colors in a category for a specific size
 */
function get_category_available_colors( $category_id, $size = '' ) {
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $category_id,
            ),
        ),
    );

    if ( $size ) {
        $args['meta_query'] = array(
            array(
                'key'     => '_custom_sizes',
                'value'   => '"' . $size . '"',
                'compare' => 'LIKE',
            ),
        );
    }

    $products = get_posts( $args );
    $colors = array();
    $color_names = array();

    foreach ( $products as $product ) {
        $product_colors = get_post_meta( $product->ID, '_custom_colors', true );
        if ( is_array( $product_colors ) ) {
            foreach ( $product_colors as $color ) {
                if ( ! empty( $color['hex'] ) && ! in_array( $color['name'], $color_names, true ) ) {
                    $colors[] = array_merge( $color, array(
                        'product_id' => $product->ID,
                        'url'        => get_permalink( $product->ID ),
                    ) );
                    $color_names[] = $color['name'];
                }
            }
        }
    }

    return $colors;
}

/**
 * Whether the URL is a shop or product archive page.
 */
function clothiq_is_shop_catalog_url( $url ) {
    if ( empty( $url ) ) {
        return false;
    }

    $shop_url = wc_get_page_permalink( 'shop' );
    if ( $shop_url ) {
        $shop_path = wp_parse_url( $shop_url, PHP_URL_PATH );
        $url_path  = wp_parse_url( $url, PHP_URL_PATH );
        if ( $shop_path && $url_path && untrailingslashit( $url_path ) === untrailingslashit( $shop_path ) ) {
            return true;
        }
    }

    $path = wp_parse_url( $url, PHP_URL_PATH );
    if ( ! $path ) {
        return false;
    }

    return (bool) preg_match( '#/(shop|product-category|product-tag)(/|$)#', $path );
}

/**
 * Suppress "added to cart" notices on shop and catalog pages.
 */
add_filter( 'wc_add_to_cart_message_html', function( $message, $products ) {
    if ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) {
        return '';
    }

    $referer = wp_get_referer();
    if ( $referer && clothiq_is_shop_catalog_url( $referer ) ) {
        return '';
    }

    return $message;
}, 10, 2 );

add_action( 'wp', function() {
    if ( ! is_admin() && ( is_shop() || is_product_taxonomy() || is_post_type_archive( 'product' ) ) ) {
        wc_clear_notices( 'success' );
    }
}, 20 );

// Add your child theme customizations below this line
