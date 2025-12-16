<?php
defined( 'ABSPATH' ) || exit;
$checkout = WC()->checkout();
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) : ?>
    <p class="woocommerce-info"><?php esc_html_e( 'You must be logged in to checkout.', 'woocommerce' ); ?></p>
<?php
    return;
endif;
?>
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
<div class="checkout_area section-padding-80">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="checkout_details_area mt-50 clearfix">

                    <div class="cart-page-heading mb-30">
                        <h5><?php esc_html_e( 'Billing Address', 'your-textdomain' ); ?></h5>
                    </div>

                    <?php
                    do_action( 'woocommerce_before_checkout_form', $checkout );
                    ?>
                    <form name="checkout"
                          method="post"
                          class="checkout woocommerce-checkout"
                          action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
                          enctype="multipart/form-data">

                        <?php if ( $checkout->get_checkout_fields() ) : ?>

                            <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

                            <div class="row" id="customer_details">

                                <div class="col-12">
                                    <?php
                                    do_action( 'woocommerce_checkout_billing' );
                                    ?>
                                </div>

                                <div class="col-12">
                                    <?php
                                    do_action( 'woocommerce_checkout_shipping' );
                                    ?>
                                </div>

                            </div> <!-- #customer_details -->

                            <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

                        <?php endif; ?>

                </div>
            </div>
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
                        do_action( 'woocommerce_checkout_order_review' );
                        ?>
                    </div>

                    <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

                </div>
            </div>

                    </form>

        </div>
    </div>
</div>
<?php
do_action( 'woocommerce_after_checkout_form', $checkout );
