<?php
/**
 * Plugin Name: Product Size Availability
 * Description: Shows available sizes for a product.
 * Version: 1.0.0
 * Author: KiruthickRaj
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {

    if ( ! is_product() ) {
        return;
    }

    wp_enqueue_script(
        'size-availability-js',
        plugin_dir_url( __FILE__ ) . 'assets/size-availability.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );
});

add_action( 'woocommerce_product_options_general_product_data', function () {

    global $post;

    $sizes = array(
        's'   => 'S',
        'm'   => 'M',
        'l'   => 'L',
        'xl'  => 'XL',
        'xxl' => 'XXL',
    );

    $saved_sizes = get_post_meta( $post->ID, '_custom_sizes', true );
    if ( ! is_array( $saved_sizes ) ) {
        $saved_sizes = array();
    }

    echo '<div class="options_group">';
    echo '<p class="form-field"><label><strong>Available Sizes</strong></label></p>';

    foreach ( $sizes as $key => $label ) {
        ?>
        <p class="form-field">
            <label>
                <input type="checkbox"
                       name="_custom_sizes[]"
                       value="<?php echo esc_attr( $key ); ?>"
                       <?php checked( in_array( $key, $saved_sizes, true ) ); ?> />
                <?php echo esc_html( $label ); ?>
            </label>
        </p>
        <?php
    }

    echo '</div>';
});

add_action( 'woocommerce_admin_process_product_object', function ( $product ) {

    if ( isset( $_POST['_custom_sizes'] ) && is_array( $_POST['_custom_sizes'] ) ) {
        $sizes = array_map( 'sanitize_text_field', $_POST['_custom_sizes'] );
        $product->update_meta_data( '_custom_sizes', $sizes );
    } else {
        $product->update_meta_data( '_custom_sizes', array() );
    }
});

/* =========================================
 * ADMIN: Product Color Picker (Dynamic)
 * ========================================= */
add_action( 'woocommerce_product_options_general_product_data', function () {

    global $post;

    $colors = get_post_meta( $post->ID, '_custom_colors', true );
    if ( ! is_array( $colors ) ) {
        $colors = array();
    }

    echo '<div class="options_group">';
    echo '<p><strong>Product Colors</strong></p>';

    echo '<div id="custom-colors-wrapper">';

    foreach ( $colors as $index => $color ) {
        ?>
        <div class="custom-color-row">
            <input type="text"
                   name="_custom_colors[<?php echo $index; ?>][name]"
                   placeholder="Color name"
                   value="<?php echo esc_attr( $color['name'] ); ?>" />

            <input type="color"
                   name="_custom_colors[<?php echo $index; ?>][hex]"
                   value="<?php echo esc_attr( $color['hex'] ); ?>" />

            <button type="button" class="button remove-color">×</button>
        </div>
        <?php
    }

    echo '</div>';

    echo '<button type="button" class="button" id="add-custom-color">Add Color</button>';
    echo '</div>';
});

add_action( 'woocommerce_admin_process_product_object', function ( $product ) {

    if ( isset( $_POST['_custom_colors'] ) && is_array( $_POST['_custom_colors'] ) ) {

        $clean = array();

        foreach ( $_POST['_custom_colors'] as $color ) {
            if ( empty( $color['name'] ) || empty( $color['hex'] ) ) {
                continue;
            }

            $clean[] = array(
                'name' => sanitize_text_field( $color['name'] ),
                'hex'  => sanitize_hex_color( $color['hex'] ),
            );
        }

        $product->update_meta_data( '_custom_colors', $clean );
    } else {
        $product->update_meta_data( '_custom_colors', array() );
    }
});
/* =========================================
 * Enqueue admin assets for product edit page
 * ========================================= */
add_action( 'admin_enqueue_scripts', function ( $hook ) {

    // Only load on product edit/add pages
    if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
        return;
    }

    // Only for WooCommerce products
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'product' ) {
        return;
    }

    // JS
    wp_enqueue_script(
        'product-colors-admin-js',
        get_stylesheet_directory_uri() . '/assets/js/product-colors-admin.js',
        array( 'jquery' ),
        '1.0.0',
        true
    );

    // CSS
    wp_enqueue_style(
        'product-colors-admin-css',
        get_stylesheet_directory_uri() . '/assets/css/product-colors-admin.css',
        array(),
        '1.0.0'
    );
});

