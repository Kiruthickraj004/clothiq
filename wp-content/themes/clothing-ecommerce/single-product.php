<?php
defined( 'ABSPATH' ) || exit;
get_header();

/* Disable default WooCommerce layout */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

global $product, $post;

/* Ensure product object exists (fixes fatal error) */
if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
    $product = wc_get_product( $post->ID );
}

if ( ! $product ) {
    get_footer();
    return;
}

/* Images */
$main_id = $product->get_image_id();
$gallery = $product->get_gallery_image_ids();
$slides = [];

if ( $main_id ) $slides[] = $main_id;
foreach ( $gallery as $gid ) {
    if ( $gid !== $main_id ) $slides[] = $gid;
}

/* Brand */
$brand = '';
if ( taxonomy_exists( 'pa_brand' ) ) {
    $terms = wp_get_post_terms( $product->get_id(), 'pa_brand' );
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        $brand = $terms[0]->name;
    }
}

/* Sizes */
$custom_sizes = $product->get_meta( '_custom_sizes' );
$size_labels = ['s'=>'S','m'=>'M','l'=>'L','xl'=>'XL','xxl'=>'XXL'];
$sizes = [];

if ( is_array( $custom_sizes ) ) {
    foreach ( $custom_sizes as $s ) {
        if ( isset( $size_labels[$s] ) ) {
            $sizes[] = $size_labels[$s];
        }
    }
}
?>

<section class="modern-product container">

    <div class="product-grid">

        <!-- Gallery -->
        <div class="product-gallery">

            <div class="thumbnail-list">
                <?php foreach ( $slides as $img ): ?>
                    <img src="<?php echo esc_url( wp_get_attachment_image_url( $img, 'thumbnail' ) ); ?>"
                         class="thumb-img"
                         data-full="<?php echo esc_url( wp_get_attachment_image_url( $img, 'large' ) ); ?>">
                <?php endforeach; ?>
            </div>

            <div class="main-image">
                <?php 
                if ( ! empty( $slides ) ) {
                    echo wp_get_attachment_image( $slides[0], 'large', false, ['id'=>'mainProductImage'] );
                }
                ?>
            </div>

        </div>

        <!-- Product Info -->
        <div class="product-info">

            <?php if ( $brand ): ?>
                <span class="brand"><?php echo esc_html( $brand ); ?></span>
            <?php endif; ?>

            <h1><?php echo esc_html( $product->get_name() ); ?></h1>

            <div class="price"><?php echo $product->get_price_html(); ?></div>

            
            <section class="product-details container">
                <?php echo wpautop( $product->get_description() ); ?>
            </section>
            <!-- Size + Color -->
            <div class="product-options">

                <?php if ( ! empty( $sizes ) ): ?>
                    <div class="size-selector">
                        <select name="product_size" required>
                            <?php foreach ( $sizes as $s ): ?>
                                <option><?php echo esc_html( $s ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="colors">
                    <?php do_action( 'custom_product_color_swatches' ); ?>
                </div>

            </div>
            

            <form method="post">
                <div class="product-actions">

                    <?php if ( $product->is_purchasable() && $product->is_in_stock() ): ?>

                        <button type="submit"
                                name="add-to-cart"
                                value="<?php echo esc_attr( $product->get_id() ); ?>"
                                class="btn-primary">
                            Add to Cart
                        </button>

                        <a href="<?php echo esc_url( add_query_arg( 'buy_now', $product->get_id(), $product->get_permalink() ) ); ?>"
                           class="btn-outline">
                            Buy Now
                        </a>

                    <?php else: ?>
                        <p class="out-of-stock">Out of stock</p>
                    <?php endif; ?>

                </div>
            </form>

        </div>

    </div>

</section>

<div class="short-desc">
    <?php echo wpautop( $product->get_short_description() ); ?>
</div>

<?php get_footer(); ?>
