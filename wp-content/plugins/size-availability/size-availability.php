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
