<?php
/**
 * Custom Essence-style checkout form
 *
 * This file overrides:
 * woocommerce/templates/checkout/form-checkout.php
 */

defined( 'ABSPATH' ) || exit;

// Get checkout object from WooCommerce
$checkout = WC()->checkout();
?>

<?php
// Before checkout form hook (coupons, notices, etc.)
do_action( 'woocommerce_before_checkout_form', $checkout );

// If registration is required and not logged in, show message and bail.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) : ?>
    <p class="woocommerce-info">
        <?php esc_html_e( 'You must be logged in to checkout.', 'woocommerce' ); ?>
    </p>
    <?php return; ?>
<?php endif; ?>

<!-- ##### Breadcumb Area Start ##### -->
<div class="breadcumb_area bg-img" style="background-image: url('<?php echo esc_url( get_template_directory_uri() . '/assets/img/bg-img/breadcumb.jpg' ); ?>');">
    <div class="container h-100">
        <div class="row h-100 align-items-center">
            <div class="col-12">
                <div class="page-title text-center">
                    <h2><?php esc_html_e( 'Checkout', 'your-textdomain' ); ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ##### Breadcumb Area End ##### -->

<!-- ##### Checkout Area Start ##### -->
<div class="checkout_area section-padding-80">
    <div class="container">
        <div class="row">

            <!-- Left: Billing / Shipping -->
            <div class="col-12 col-md-6">
                <div class="checkout_details_area mt-50 clearfix">

                    <div class="cart-page-heading mb-30">
                        <h5><?php esc_html_e( 'Billing Address', 'your-textdomain' ); ?></h5>
                    </div>

                    <form name="checkout"
                          method="post"
                          class="checkout woocommerce-checkout"
                          action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
                          enctype="multipart/form-data">

                        <div class="row" id="customer_details">
                            <div class="col-12">
                                <?php
                                /**
                                 * WooCommerce core prints billing fields here
                                 * (first name, last name, address, phone, email, etc.)
                                 */
                                do_action( 'woocommerce_checkout_billing' );
                                ?>
                            </div>

                            <div class="col-12">
                                <?php
                                /**
                                 * Shipping fields (if enabled).
                                 * If you don’t use shipping, Woo will handle that.
                                 */
                                do_action( 'woocommerce_checkout_shipping' );
                                ?>
                            </div>
                        </div>

                </div>
            </div>

            <!-- Right: Order summary + payment -->
            <div class="col-12 col-md-6 col-lg-5 ml-lg-auto">
                <div class="order-details-confirmation">

                    <div class="cart-page-heading">
                        <h5><?php esc_html_e( 'Your Order', 'your-textdomain' ); ?></h5>
                        <p><?php esc_html_e( 'The Details', 'your-textdomain' ); ?></p>
                    </div>

                    <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

                    <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

                    <div id="order_review" class="woocommerce-checkout-review-order">
                        <?php
                        /**
                         * This prints the full order review:
                         * - products
                         * - subtotal, shipping, tax, total
                         * - payment methods
                         * - place order button
                         *
                         * We’ll style it with CSS to match Essence.
                         */
                        do_action( 'woocommerce_checkout_order_review' );
                        ?>
                    </div>

                    <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

                </div>
            </div>

                    </form> <!-- closes <form name="checkout"> -->

        </div>
    </div>
</div>
<!-- ##### Checkout Area End ##### -->

<?php
// After checkout form hook
do_action( 'woocommerce_after_checkout_form', $checkout );
