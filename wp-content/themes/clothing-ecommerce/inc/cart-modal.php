<?php
// inc/cart-modal.php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Render the cart modal HTML (returns string).
 * Markup intentionally follows the bootstrap template's classes & structure.
 */
function tch_render_cart_modal_html() {
    ob_start();
    ?>
    <div class="cart-bg-overlay" style="display:none;"></div>

    <div class="right-side-cart-area" style="display:none;">
        <!-- Cart Button (close) -->
        <div class="cart-button">
            <a href="#" id="rightSideCartClose"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/core-img/bag.svg' ); ?>" alt="" /> <span class="cart-count"><?php echo WC()->cart ? WC()->cart->get_cart_contents_count() : 0; ?></span></a>
        </div>

        <div class="cart-content d-flex">
            <!-- Cart List Area -->
            <div class="cart-list">
                <?php if ( WC()->cart && ! WC()->cart->is_empty() ) : 
                    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
                        $product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
                        if ( ! $product ) continue;
                        $product_id = $cart_item['product_id'];
                        $img = wp_get_attachment_image( $product->get_image_id(), 'thumbnail' );
                        $name = $product->get_name();
                        $qty = intval( $cart_item['quantity'] );
                        $price_html = wc_price( $product->get_price() ); // formatted price
                        $brand = get_the_terms( $product_id, 'pa_brand' );
                        $brand_name = '';
                        if ( ! is_wp_error( $brand ) && ! empty( $brand ) ) {
                            $brand_name = esc_html( $brand[0]->name );
                        } else {
                            $brand_name = esc_html( get_post_meta( $product_id, 'brand', true ) );
                        }
                        ?>
                        <div class="single-cart-item" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>">
                            <a href="<?php echo esc_url( get_permalink( $product_id ) ); ?>" class="product-image">
                                <?php echo $img ? $img : wc_placeholder_img( 'thumbnail' ); ?>
                                <!-- Cart Item Desc -->
                                <div class="cart-item-desc">
                                  <span class="product-remove">
                                    <a href="#" class="tch-remove-cart-item" data-cart-key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="<?php esc_attr_e( 'Remove item', 'your-textdomain' ); ?>">
                                      <i class="fa fa-close" aria-hidden="true"></i>
                                    </a>
                                  </span>
                                  <span class="badge"><?php echo $brand_name; ?></span>
                                  <h6><?php echo esc_html( $name ); ?></h6>
                                  <p class="size"><?php
                                    // show variation/size if present
                                    if ( ! empty( $cart_item['variation'] ) && is_array( $cart_item['variation'] ) ) {
                                        echo esc_html( implode( ', ', $cart_item['variation'] ) );
                                    } else {
                                        // fallback: show attribute or nothing
                                        echo '';
                                    }
                                  ?></p>
                                  <p class="color"><?php
                                    // If color stored in cart item meta, print it (optional)
                                    if ( ! empty( $cart_item['tch_color'] ) ) {
                                        echo esc_html( $cart_item['tch_color'] );
                                    }
                                  ?></p>
                                  <p class="price"><?php echo wp_kses_post( $price_html ); ?></p>
                                </div>
                            </a>
                        </div>
                    <?php endforeach;
                else : ?>
                    <p class="text-muted"><?php esc_html_e( 'Your cart is currently empty.', 'your-textdomain' ); ?></p>
                <?php endif; ?>
            </div>

            <!-- Cart Summary -->
            <div class="cart-amount-summary">
                <h2>Summary</h2>
                <ul class="summary-table">
                    <li><span><?php esc_html_e( 'subtotal:', 'your-textdomain' ); ?></span> <span class="tch-cart-subtotal"><?php echo WC()->cart ? WC()->cart->get_cart_subtotal() : wc_price(0); ?></span></li>
                    <li><span><?php esc_html_e( 'delivery:', 'your-textdomain' ); ?></span> <span><?php esc_html_e( 'Free', 'your-textdomain' ); ?></span></li>
                    <li><span><?php esc_html_e( 'discount:', 'your-textdomain' ); ?></span> <span class="tch-cart-discount"><?php
                        // show coupon discount summary if needed (left blank by default)
                        echo '';
                    ?></span></li>
                    <li><span><?php esc_html_e( 'total:', 'your-textdomain' ); ?></span> <span class="tch-cart-total"><?php echo WC()->cart ? WC()->cart->get_cart_total() : wc_price(0); ?></span></li>
                </ul>
                <div class="checkout-btn mt-100">
                    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn essence-btn"><?php esc_html_e( 'check out', 'your-textdomain' ); ?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * AJAX: Remove cart item
 */
add_action( 'wp_ajax_tch_remove_cart_item', 'tch_remove_cart_item' );
add_action( 'wp_ajax_nopriv_tch_remove_cart_item', 'tch_remove_cart_item' );
function tch_remove_cart_item() {
    check_ajax_referer( 'tch_cart_nonce', 'nonce' );

    if ( ! isset( $_POST['cart_key'] ) ) {
        wp_send_json_error( array( 'message' => 'Missing cart_key' ) );
    }

    $cart_key = sanitize_text_field( wp_unslash( $_POST['cart_key'] ) );

    if ( WC()->cart->remove_cart_item( $cart_key ) ) {
        WC()->cart->calculate_totals();
        $html = tch_render_cart_modal_html();
        $count = WC()->cart->get_cart_contents_count();
        $total = WC()->cart->get_cart_total();
        wp_send_json_success( array( 'html' => $html, 'count' => $count, 'total' => $total ) );
    }

    wp_send_json_error( array( 'message' => 'Could not remove item' ) );
}

/**
 * AJAX: Get cart fragment (modal html + counts)
 */
add_action( 'wp_ajax_tch_get_cart_fragment', 'tch_get_cart_fragment' );
add_action( 'wp_ajax_nopriv_tch_get_cart_fragment', 'tch_get_cart_fragment' );
function tch_get_cart_fragment() {
    check_ajax_referer( 'tch_cart_nonce', 'nonce' );
    $html = tch_render_cart_modal_html();
    $count = WC()->cart->get_cart_contents_count();
    $total = WC()->cart->get_cart_total();
    wp_send_json_success( array( 'html' => $html, 'count' => $count, 'total' => $total ) );
}

/**
 * Enqueue external JS for the cart modal and localize AJAX vars.
 */
add_action( 'wp_enqueue_scripts', function() {
    $handle = 'tch-cart-modal';
    $path = '/assets/js/cart-modal.js';
    $file = get_template_directory() . $path;
    $src  = get_template_directory_uri() . $path;

    if ( file_exists( $file ) ) {
        wp_register_script( $handle, $src, array( 'jquery' ), filemtime( $file ), true );
        wp_enqueue_script( $handle );
        wp_localize_script( $handle, 'tchCart', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'tch_cart_nonce' ),
        ) );
    }
});
