<?php
/**
 * Plugin Name: Bulk Price Updater
 * Description: Update WooCommerce product prices by parent & child category and size.
 * Version: 1.1.0
 * Author: KiruthickRaj
 */

defined( 'ABSPATH' ) || exit;

/* =========================================
 * ADMIN MENU
 * ========================================= */
add_action( 'admin_menu', function () {
    add_submenu_page(
        'woocommerce',
        'Bulk Price Updater',
        'Bulk Price Updater',
        'manage_woocommerce',
        'bulk-price-updater',
        'bpu_render_admin_page'
    );
});

/* =========================================
 * ENQUEUE ADMIN SCRIPT
 * ========================================= */
add_action( 'admin_enqueue_scripts', function ( $hook ) {

    if ( $hook !== 'woocommerce_page_bulk-price-updater' ) {
        return;
    }

    wp_enqueue_script(
        'bpu-admin-js',
        plugin_dir_url( __FILE__ ) . 'assets/admin.js',
        array( 'jquery' ),
        '1.1.0',
        true
    );

    wp_localize_script( 'bpu-admin-js', 'bpuData', array(
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'bpu_ajax_nonce' ),
    ));
});

/* =========================================
 * ADMIN PAGE UI
 * ========================================= */
function bpu_render_admin_page() {

    $parents = get_terms( array(
        'taxonomy'   => 'product_cat',
        'parent'     => 0,
        'hide_empty' => false,
    ) );
    ?>
    <div class="wrap">
        <h1>Bulk Price Updater</h1>

        <form method="post">
            <?php wp_nonce_field( 'bpu_update_prices', 'bpu_nonce' ); ?>

            <table class="form-table">
                <tr>
                    <th>Parent Category</th>
                    <td>
                        <select id="bpu_parent_cat" required>
                            <option value="">Select parent</option>
                            <?php foreach ( $parents as $parent ) : ?>
                                <option value="<?php echo esc_attr( $parent->term_id ); ?>">
                                    <?php echo esc_html( $parent->name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>Child Category</th>
                    <td>
                        <select name="product_cat" id="bpu_child_cat" required>
                            <option value="">Select child</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>Product Size</th>
                    <td>
                        <select name="product_size" required>
                            <option value="">Select size</option>
                            <option value="s">S</option>
                            <option value="m">M</option>
                            <option value="l">L</option>
                            <option value="xl">XL</option>
                            <option value="xxl">XXL</option>
                        </select>
                    </td>
                </tr>

                <tr>
                    <th>New Price (₹)</th>
                    <td>
                        <input type="number" name="new_price" step="0.01" required />
                    </td>
                </tr>

                <tr>
                    <th>Discount Percentage (%)</th>
                    <td>
                        <input type="number"name="discount_percent"step="0.01"min="0"max="100"placeholder="e.g. 20"required />
                    </td>
                </tr>
            </table>

            <?php submit_button( 'Update Prices' ); ?>
        </form>
    </div>
    <?php
}

add_action( 'wp_ajax_bpu_get_child_categories', function () {

    check_ajax_referer( 'bpu_ajax_nonce', 'nonce' );

    $parent_id = intval( $_POST['parent_id'] );

    $children = get_terms( array(
        'taxonomy'   => 'product_cat',
        'parent'     => $parent_id,
        'hide_empty' => false,
    ) );

    foreach ( $children as $child ) {
        echo '<option value="' . esc_attr( $child->slug ) . '">' . esc_html( $child->name ) . '</option>';
    }

    wp_die();
});

add_action( 'admin_init', function () {

    if (
        ! isset( $_POST['bpu_nonce'] ) ||
        ! wp_verify_nonce( $_POST['bpu_nonce'], 'bpu_update_prices' )
    ) {
        return;
    }

    if ( empty( $_POST['product_cat'] ) || empty( $_POST['product_size'] ) || empty( $_POST['new_price'] ) ) {
        return;
    }

    if (
    empty( $_POST['product_cat'] ) ||
    empty( $_POST['product_size'] ) ||
    empty( $_POST['new_price'] ) ||
    ! isset( $_POST['discount_percent'] )
    ) {
    return;
    }


    $category = sanitize_text_field( $_POST['product_cat'] );
    $size     = sanitize_text_field( $_POST['product_size'] );
    $price    = floatval( $_POST['new_price'] );
    $discount = floatval( $_POST['discount_percent'] );

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        ),
        'meta_query' => array(
            array(
                'key'     => '_custom_sizes',
                'value'   => '"' . $size . '"',
                'compare' => 'LIKE',
            ),
        ),
    );

    $products = get_posts( $args );

    foreach ( $products as $post ) {

        $product = wc_get_product( $post->ID );
        if ( ! $product ) {
            continue;
        }
        $product->set_regular_price( $price );
        $discount_amount = ( $price * $discount ) / 100;
        $sale_price      = $price - $discount_amount;
        $sale_price = round( $sale_price / 10 ) * 10;
        $product->set_sale_price( $sale_price );
        $product->set_price( $sale_price );

        $product->save();
    }

    add_action( 'admin_notices', function () use ( $products ) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo count( $products ); ?> products updated successfully.</p>
        </div>
        <?php
    });
});
