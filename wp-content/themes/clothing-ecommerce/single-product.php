<?php
/**
 * Single Product Template
 */

defined( 'ABSPATH' ) || exit;

get_header();

global $post, $product;

if ( empty( $product ) || ! is_object( $product ) || ! ( $product instanceof WC_Product ) ) {
    $product = wc_get_product( $post ? $post->ID : 0 );
}

if ( ! $product || ! ( $product instanceof WC_Product ) ) {
    get_footer();
    return;
}

/* ==============================
 * Product Images
 * ============================== */
$main_id     = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$slides      = array();

if ( $main_id ) {
    $slides[] = $main_id;
}
foreach ( $gallery_ids as $gid ) {
    if ( $gid && $gid !== $main_id ) {
        $slides[] = $gid;
    }
}

/* ==============================
 * Brand
 * ============================== */
$brand = '';
if ( taxonomy_exists( 'pa_brand' ) ) {
    $terms = wp_get_post_terms( $product->get_id(), 'pa_brand' );
    if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
        $brand = $terms[0]->name;
    }
}

/* ==============================
 * Short Description
 * ============================== */
$short_desc = $product->get_short_description();
if ( ! $short_desc ) {
    $short_desc = get_the_excerpt( $product->get_id() );
}

/* ==============================
 * CUSTOM SIZES (FROM META)
 * ============================== */
$sizes_options = array();

$custom_sizes = $product->get_meta( '_custom_sizes' );

$size_labels = array(
    's'   => 'S',
    'm'   => 'M',
    'l'   => 'L',
    'xl'  => 'XL',
    'xxl' => 'XXL',
);

if ( is_array( $custom_sizes ) && ! empty( $custom_sizes ) ) {
    foreach ( $custom_sizes as $size_key ) {
        if ( isset( $size_labels[ $size_key ] ) ) {
            $sizes_options[] = $size_labels[ $size_key ];
        }
    }
}
?>

<section class="single_product_details_area d-flex align-items-center">

    <!-- Product Images -->
    <div class="single_product_thumb clearfix">
        <div class="product_thumbnail_slides owl-carousel">
            <?php
            if ( empty( $slides ) ) {
                echo wc_placeholder_img( 'full' );
            } else {
                foreach ( $slides as $att_id ) {
                    echo wp_get_attachment_image( $att_id, 'full' );
                }
            }
            ?>
        </div>
    </div>

    <!-- Product Info -->
    <div class="single_product_desc clearfix">

        <?php if ( $brand ) : ?>
            <span><?php echo esc_html( $brand ); ?></span>
        <?php endif; ?>

        <h2><?php echo esc_html( $product->get_name() ); ?></h2>

        <p class="product-price">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="old-price"><?php echo wc_price( $product->get_regular_price() ); ?></span>
                <?php echo wc_price( $product->get_sale_price() ); ?>
            <?php else : ?>
                <?php echo wc_price( $product->get_price() ); ?>
            <?php endif; ?>
        </p>

        <?php if ( $short_desc ) : ?>
            <p class="product-desc"><?php echo wp_kses_post( wpautop( $short_desc ) ); ?></p>
        <?php endif; ?>

        <div class="cart-form-wrapper">

            <?php if ( $product->is_type( 'variable' ) ) : ?>

                <form class="cart-form clearfix" method="post" enctype="multipart/form-data">
                    <?php woocommerce_variable_add_to_cart(); ?>
                </form>

            <?php else : ?>

                <form class="cart-form clearfix"
                      method="post"
                      action="<?php echo esc_url( $product->get_permalink() ); ?>">

                    <div class="select-box d-flex mt-50 mb-30">

                        <?php if ( ! empty( $sizes_options ) ) : ?>
                            <select name="product_size" id="productSize" class="mr-5">
                                <?php foreach ( $sizes_options as $size ) : ?>
                                    <option value="<?php echo esc_attr( $size ); ?>">
                                        <?php echo esc_html( $size ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                    </div>
                    <?php do_action( 'custom_product_color_swatches' );?>
                    <div class="cart-fav-box d-flex align-items-center">

                        <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>

                            <!-- Add to Cart -->
                            <button type="submit"
                                    name="add-to-cart"
                                    value="<?php echo esc_attr( $product->get_id() ); ?>"
                                    class="btn essence-btn">
                                <?php esc_html_e( 'Add to cart', 'your-textdomain' ); ?>
                            </button>

                            <!-- Buy Now -->
                            <a href="<?php echo esc_url(add_query_arg('buy_now',$product->get_id(),$product->get_permalink())); ?>"class="btn essence-btn ml-2">Buy Now</a>
                        <?php else : ?>

                            <p class="stock out-of-stock">
                                <?php esc_html_e( 'Out of stock', 'your-textdomain' ); ?>
                            </p>

                        <?php endif; ?>

                        <!-- Wishlist -->
                        <div class="product-favourite ml-4">
                            <a href="#" class="favme fa fa-heart"></a>
                        </div>

                    </div>

                </form>

            <?php endif; ?>

        </div>
    </div>
</section>

<?php
get_footer();
