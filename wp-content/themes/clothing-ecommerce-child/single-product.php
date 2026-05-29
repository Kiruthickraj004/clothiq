<?php
defined( 'ABSPATH' ) || exit;
get_header();

if ( clothiq_elementor_theme_do_location( 'single' ) ) {
    get_footer();
    return;
}

$clothiq_product_id = get_queried_object_id();
if ( $clothiq_product_id && clothiq_is_elementor_built( $clothiq_product_id ) ) {
    echo '<div class="clothiq-elementor-content clothiq-product-elementor">';
    while ( have_posts() ) {
        the_post();
        the_content();
    }
    echo '</div>';
    get_footer();
    return;
}

/* Disable default WooCommerce layout */
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_product_summary', 'woocommerce_output_related_products', 20 );

global $product, $post;

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

/* Get product category */
$product_categories = wp_get_post_terms( $product->get_id(), 'product_cat' );
$category_id = ! empty( $product_categories ) && ! is_wp_error( $product_categories )
    ? $product_categories[0]->term_id
    : 0;

/* Get available sizes and colors from category */
$available_sizes = $category_id ? get_category_available_sizes( $category_id ) : [];
$size_labels = ['s' => 'S', 'm' => 'M', 'l' => 'L', 'xl' => 'XL', 'xxl' => 'XXL'];

/* Get current product sizes */
$product_sizes = $product->get_meta( '_custom_sizes' );
$default_size = ! empty( $product_sizes ) ? $product_sizes[0] : '';

/* Get available colors for default size */
$available_colors = $category_id ? get_category_available_colors( $category_id, $default_size ) : [];

/* Get current product colors */
$product_colors = $product->get_meta( '_custom_colors' );
$default_color = ! empty( $product_colors ) ? $product_colors[0] : [];
$current_product_id = $product->get_id();
?>

<div class="single-product-page">
    <div class="container">
        <div class="product-layout">

            <!-- Gallery Section -->
            <div class="product-gallery-section">
                <div class="product-gallery-wrapper">
                    <button class="gallery-nav prev-slide">&lt;</button>

                    <div class="main-gallery">
                        <div class="gallery-slides">
                            <?php foreach ( $slides as $idx => $img ): ?>
                                <div class="gallery-slide <?php echo $idx === 0 ? 'active' : ''; ?>" data-index="<?php echo $idx; ?>">
                                    <?php echo wp_get_attachment_image( $img, 'large' ); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="gallery-nav next-slide">&gt;</button>
                </div>

                <?php if ( count( $slides ) > 1 ): ?>
                    <div class="gallery-thumbnails">
                        <?php foreach ( $slides as $idx => $img ): ?>
                            <img src="<?php echo esc_url( wp_get_attachment_image_url( $img, 'thumbnail' ) ); ?>"
                                 class="thumbnail <?php echo $idx === 0 ? 'active' : ''; ?>"
                                 data-index="<?php echo $idx; ?>"
                                 alt="">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info Section -->
            <div class="product-info-section">

                <?php if ( $brand ): ?>
                    <div class="product-brand"><?php echo esc_html( strtoupper( $brand ) ); ?></div>
                <?php endif; ?>

                <h1 class="product-title"><?php echo esc_html( $product->get_name() ); ?></h1>

                <div class="product-price-wrapper">
                    <?php echo $product->get_price_html(); ?>
                </div>

                <div class="product-short-description">
                    <?php echo wpautop( $product->get_short_description() ); ?>
                </div>

                <!-- Size & Color Options -->
                <div class="product-selectors">

                    <?php if ( ! empty( $available_sizes ) ): ?>
                        <div class="selector-group selector-group--size">
                            <label>SIZE: <span class="size-value"><?php echo esc_html( isset( $size_labels[ $default_size ] ) ? $size_labels[ $default_size ] : strtoupper( $default_size ) ); ?></span></label>
                            <div class="size-options" role="group" aria-label="<?php esc_attr_e( 'Select size', 'clothing-ecommerce-child' ); ?>">
                                <?php foreach ( $available_sizes as $size ):
                                    $size_label = $size_labels[ $size ] ?? strtoupper( $size );
                                    $is_active  = $size === $default_size;
                                    ?>
                                    <button type="button"
                                            class="size-option<?php echo $is_active ? ' is-active' : ''; ?>"
                                            data-size="<?php echo esc_attr( $size ); ?>"
                                            data-size-label="<?php echo esc_attr( $size_label ); ?>"
                                            aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>">
                                        <?php echo esc_html( $size_label ); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden"
                                   name="product_size"
                                   class="size-select"
                                   value="<?php echo esc_attr( $default_size ); ?>"
                                   data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $available_colors ) ): ?>
                        <div class="selector-group">
                            <label>COLOR: <span class="color-value"><?php echo esc_html( strtoupper( $default_color['name'] ?? 'SELECT' ) ); ?></span></label>

                            <div class="color-swatches-inline">
                                <?php foreach ( $available_colors as $color ):
                                    $is_active = ( isset( $color['product_id'] ) && (int) $color['product_id'] === $current_product_id )
                                        || ( isset( $default_color['hex'] ) && $default_color['hex'] === $color['hex'] );
                                    $swatch_url = ! empty( $color['url'] ) ? $color['url'] : '#';
                                    ?>
                                    <a href="<?php echo esc_url( $swatch_url ); ?>"
                                       class="color-swatch<?php echo $is_active ? ' active' : ''; ?>"
                                       style="background-color: <?php echo esc_attr( $color['hex'] ); ?>"
                                       data-color-hex="<?php echo esc_attr( $color['hex'] ); ?>"
                                       data-color-name="<?php echo esc_attr( $color['name'] ); ?>"
                                       title="<?php echo esc_attr( $color['name'] ); ?>"
                                       <?php echo $is_active ? 'aria-current="true"' : ''; ?>>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- Add to Cart -->
                <form method="post" class="add-to-cart-form">
                    <div class="product-actions-wrapper">

                        <?php if ( $product->is_purchasable() && $product->is_in_stock() ): ?>
                            <button type="submit"
                                    name="add-to-cart"
                                    value="<?php echo esc_attr( $product->get_id() ); ?>"
                                    class="btn-add-to-cart">
                                ADD TO CART
                            </button>
                            <button type="button" class="btn-wishlist" title="Add to Wishlist" aria-label="Add to Wishlist">
                                <svg class="wishlist-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                                </svg>
                            </button>
                        <?php else: ?>
                            <p class="out-of-stock">Out of stock</p>
                        <?php endif; ?>

                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

<?php get_footer(); ?>
