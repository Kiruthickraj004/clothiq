<?php
// single-product.php (theme)
defined( 'ABSPATH' ) || exit;

get_header();

global $post, $product;

if ( empty( $product ) || ! is_object( $product ) || ! ( $product instanceof \WC_Product ) ) {
    $product = wc_get_product( $post ? $post->ID : 0 );
}

if ( ! $product || ! ( $product instanceof \WC_Product ) ) {
    ?>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <p><?php esc_html_e( 'Product not found.', 'your-textdomain' ); ?></p>
        </div>
      </div>
    </div>
    <?php
    get_footer();
    return;
}

$main_id     = $product->get_image_id();
$gallery_ids = $product->get_gallery_image_ids();
$slides      = array();

if ( $main_id ) {
    $slides[] = $main_id;
}
if ( ! empty( $gallery_ids ) ) {
    foreach ( $gallery_ids as $gid ) {
        if ( ! $gid ) {
            continue;
        }
        if ( $main_id && intval( $gid ) === intval( $main_id ) ) {
            continue;
        }
        $slides[] = $gid;
    }
}

// Brand
$brand = '';
if ( taxonomy_exists( 'pa_brand' ) ) {
    $brand_terms = wp_get_post_terms( $product->get_id(), 'pa_brand' );
    if ( ! is_wp_error( $brand_terms ) && ! empty( $brand_terms ) ) {
        $brand = $brand_terms[0]->name;
    }
}
if ( ! $brand ) {
    $possible = $product->get_attribute( 'brand' );
    if ( $possible ) {
        $brand = $possible;
    }
}

// Short description
$short_desc = $product->get_short_description();
if ( ! $short_desc ) {
    $short_desc = get_the_excerpt( $product->get_id() );
}

// Attributes: sizes & colors
$attributes      = $product->get_attributes();
$sizes_options   = array();
$colors_options  = array();

foreach ( $attributes as $attr_key => $attr_obj ) {
    if ( is_a( $attr_obj, 'WC_Product_Attribute' ) ) {
        $attr_name = $attr_obj->get_name();
        $label     = $attr_obj->get_name();
        $is_tax    = $attr_obj->is_taxonomy();

        if ( $is_tax ) {
            $taxonomy = wc_attribute_taxonomy_name( str_replace( 'pa_', '', $attr_name ) );
            $terms    = wp_get_post_terms( $product->get_id(), $attr_name, array( 'fields' => 'names' ) );
            if ( is_array( $terms ) && ! empty( $terms ) ) {
                $lower = strtolower( $attr_name );
                if ( strpos( $lower, 'size' ) !== false ) {
                    $sizes_options = array_merge( $sizes_options, $terms );
                } elseif ( strpos( $lower, 'color' ) !== false ) {
                    $colors_options = array_merge( $colors_options, $terms );
                }
            }
        } else {
            $opts = $attr_obj->get_options();
            if ( is_array( $opts ) && ! empty( $opts ) ) {
                $lower = strtolower( $attr_name );
                if ( strpos( $lower, 'size' ) !== false ) {
                    $sizes_options = array_merge( $sizes_options, $opts );
                } elseif ( strpos( $lower, 'color' ) !== false ) {
                    $colors_options = array_merge( $colors_options, $opts );
                } else {
                    $label_lower = strtolower( $attr_obj->get_name() );
                    if ( strpos( $label_lower, 'size' ) !== false ) {
                        $sizes_options = array_merge( $sizes_options, $opts );
                    } elseif ( strpos( $label_lower, 'color' ) !== false ) {
                        $colors_options = array_merge( $colors_options, $opts );
                    }
                }
            }
        }
    } else {
        $label = is_array( $attr_obj ) && isset( $attr_obj['name'] ) ? $attr_obj['name'] : $attr_key;
        $value = is_array( $attr_obj ) && isset( $attr_obj['value'] ) ? $attr_obj['value'] : '';
        if ( $value ) {
            $opts  = array_map( 'trim', explode( '|', $value ) );
            $lower = strtolower( $label );
            if ( strpos( $lower, 'size' ) !== false ) {
                $sizes_options = array_merge( $sizes_options, $opts );
            } elseif ( strpos( $lower, 'color' ) !== false ) {
                $colors_options = array_merge( $colors_options, $opts );
            }
        }
    }
}

$sizes_options  = array_values( array_unique( array_filter( array_map( 'trim', $sizes_options ) ) ) );
$colors_options = array_values( array_unique( array_filter( array_map( 'trim', $colors_options ) ) ) );

?>

<section class="single_product_details_area d-flex align-items-center">
    <!-- Single Product Thumb -->
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

    <!-- Single Product Description -->
    <div class="single_product_desc clearfix">
        <?php if ( $brand ) : ?>
            <span><?php echo esc_html( $brand ); ?></span>
        <?php endif; ?>

        <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
            <h2><?php echo esc_html( $product->get_name() ); ?></h2>
        </a>

        <p class="product-price">
            <?php if ( $product->is_on_sale() ) : ?>
                <span class="old-price">
                    <?php echo wp_kses_post( wc_price( $product->get_regular_price() ) ); ?>
                </span>
                <?php echo wp_kses_post( wc_price( $product->get_sale_price() ) ); ?>
            <?php else : ?>
                <?php echo wp_kses_post( wc_price( $product->get_price() ) ); ?>
            <?php endif; ?>
        </p>

        <?php if ( $short_desc ) : ?>
            <p class="product-desc"><?php echo wp_kses_post( wpautop( $short_desc ) ); ?></p>
        <?php endif; ?>

        <div class="cart-form-wrapper">
            <?php
            if ( $product->is_type( 'variable' ) ) {

                // Let WooCommerce render the full variable add to cart form.
                echo '<form class="cart-form clearfix" method="post" enctype="multipart/form-data">';
                woocommerce_variable_add_to_cart();
                echo '</form>';

            } else {
                ?>
                <form class="cart-form clearfix"
                      method="post"
                      enctype="multipart/form-data"
                      action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>">

                    <div class="select-box d-flex mt-50 mb-30">
                        <?php if ( ! empty( $sizes_options ) ) : ?>
                            <select name="product_size" id="productSize" class="mr-5">
                                <?php foreach ( $sizes_options as $sopt ) : ?>
                                    <option value="<?php echo esc_attr( $sopt ); ?>"><?php echo esc_html( $sopt ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <select name="product_size" id="productSize" class="mr-5">
                                <option value="XL"><?php esc_html_e( 'Size: XL', 'your-textdomain' ); ?></option>
                                <option value="X"><?php esc_html_e( 'Size: X', 'your-textdomain' ); ?></option>
                                <option value="M"><?php esc_html_e( 'Size: M', 'your-textdomain' ); ?></option>
                                <option value="S"><?php esc_html_e( 'Size: S', 'your-textdomain' ); ?></option>
                            </select>
                        <?php endif; ?>

                        <?php if ( ! empty( $colors_options ) ) : ?>
                            <select name="product_color" id="productColor">
                                <?php foreach ( $colors_options as $copt ) : ?>
                                    <option value="<?php echo esc_attr( $copt ); ?>"><?php echo esc_html( $copt ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else : ?>
                            <select name="product_color" id="productColor">
                                <option value="Black"><?php esc_html_e( 'Color: Black', 'your-textdomain' ); ?></option>
                                <option value="White"><?php esc_html_e( 'Color: White', 'your-textdomain' ); ?></option>
                                <option value="Red"><?php esc_html_e( 'Color: Red', 'your-textdomain' ); ?></option>
                                <option value="Purple"><?php esc_html_e( 'Color: Purple', 'your-textdomain' ); ?></option>
                            </select>
                        <?php endif; ?>
                    </div>

                    <div class="cart-fav-box d-flex align-items-center">
                        <?php if ( $product->is_purchasable() && $product->is_in_stock() ) : ?>

                            <!-- Add to Cart -->
                            <button type="submit"
                                    name="add-to-cart"
                                    value="<?php echo esc_attr( $product->get_id() ); ?>"
                                    class="btn essence-btn">
                                <?php esc_html_e( 'Add to cart', 'your-textdomain' ); ?>
                            </button>

                            <!-- ✅ Buy Now (next to Add to cart) -->
                            <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>?add-to-cart=<?php echo esc_attr( $product->get_id() ); ?>"
                               class="btn essence-btn ml-2">
                                <?php esc_html_e( 'Buy Now', 'your-textdomain' ); ?>
                            </a>

                        <?php else : ?>
                            <p class="stock out-of-stock">
                                <?php esc_html_e( 'Out of stock', 'your-textdomain' ); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Wishlist / Favourite -->
                        <div class="product-favourite ml-4">
                            <a href="#" class="favme fa fa-heart"></a>
                        </div>
                    </div>

                </form>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<?php
get_footer();
