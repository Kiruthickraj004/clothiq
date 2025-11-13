<?php
/**
 * single-product.php
 * Custom single product template adapted from the Bootstrap "Essence" layout you provided.
 *
 * Place in your theme (preferably child theme) at: /woocommerce/single-product.php
 */

defined( 'ABSPATH' ) || exit;

get_header();

/** @var WC_Product $product */
global $product;
if ( ! $product ) {
    $product = wc_get_product( get_the_ID() );
}

$breadcrumb_bg = get_template_directory_uri() . '/assets/img/bg-img/breadcumb.jpg';
?>

<!-- Breadcrumb / Title area (optional) -->
<div class="breadcumb_area bg-img" style="background-image: url('<?php echo esc_url( $breadcrumb_bg ); ?>');">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12">
        <div class="page-title text-center">
          <h2><?php echo esc_html( get_the_title() ); ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Standard WooCommerce notices (cart / checkout messages)
wc_print_notices();
?>

<section class="single_product_details_area d-flex align-items-center container" style="padding-top:30px;padding-bottom:40px;">
  <div class="row w-100">

    <!-- Single Product Thumb -->
    <div class="col-12 col-md-6 single_product_thumb clearfix">
      <?php
      $gallery_ids = $product ? $product->get_gallery_image_ids() : array();
      $main_image_id = $product ? $product->get_image_id() : 0;

      // Ensure main image is first
      $slides = array();
      if ( $main_image_id ) {
          $slides[] = $main_image_id;
      }
      if ( ! empty( $gallery_ids ) ) {
          foreach ( $gallery_ids as $gid ) {
              if ( $gid === $main_image_id ) continue;
              $slides[] = $gid;
          }
      }

      if ( empty( $slides ) && $main_image_id ) {
          $slides[] = $main_image_id;
      }
      ?>
      <div class="product_thumbnail_slides owl-carousel">
        <?php if ( ! empty( $slides ) ) : ?>
          <?php foreach ( $slides as $att_id ) : ?>
            <div class="single-slide">
              <?php echo wp_get_attachment_image( $att_id, 'large' ); ?>
            </div>
          <?php endforeach; ?>
        <?php else : ?>
          <div class="single-slide">
            <?php echo wc_placeholder_img( 'large' ); ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Single Product Description -->
    <div class="col-12 col-md-6 single_product_desc clearfix">
      <?php
      // Brand (pa_brand) - show first term if available
      $brands = array();
      if ( taxonomy_exists( 'pa_brand' ) ) {
          $brands = wp_get_post_terms( $product->get_id(), 'pa_brand', array( 'fields' => 'names' ) );
      }
      if ( ! empty( $brands ) ) {
          echo '<span class="product-brand">' . esc_html( $brands[0] ) . '</span>';
      }
      ?>

      <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>">
        <h2><?php echo esc_html( $product->get_name() ); ?></h2>
      </a>

      <p class="product-price">
        <?php echo $product ? wp_kses_post( $product->get_price_html() ) : ''; ?>
      </p>

      <div class="product-desc">
        <?php
        // short description
        $short = apply_filters( 'woocommerce_short_description', get_the_excerpt() );
        if ( $short ) {
            echo wp_kses_post( wpautop( $short ) );
        } else {
            // fallback to content trimmed
            echo wp_kses_post( wpautop( wp_trim_words( get_the_content(), 30, '...' ) ) );
        }
        ?>
      </div>

      <!-- Form -->
      <div class="product-order-wrap mt-4">

        <?php
        // If product is variable, use default template to handle variation selection & add-to-cart (keeps variation logic)
        if ( $product && $product->is_type( 'variable' ) ) {

            /**
             * Render the standard WooCommerce variable add-to-cart template which includes
             * selects for attributes, price changes and variation add-to-cart handling.
             * This avoids re-implementing variation handling.
             */
            woocommerce_template_single_add_to_cart();

        } else {

            // For simple products we'll render size/color selects (when attributes exist) for UX parity.
            // Note: for real per-variation pricing/stock you'll want variable products and the above handler.

            // Gather attribute terms (if attribute taxonomies exist)
            $size_terms  = array();
            $color_terms = array();

            if ( taxonomy_exists( 'pa_size' ) ) {
                $size_terms = wp_get_post_terms( $product->get_id(), 'pa_size', array( 'fields' => 'names' ) );
            }

            if ( taxonomy_exists( 'pa_color' ) ) {
                $color_terms = wp_get_post_terms( $product->get_id(), 'pa_color', array( 'fields' => 'names' ) );
            }
            ?>

            <form class="cart-form clearfix" method="post" enctype='multipart/form-data'>
              <div class="select-box d-flex mt-3 mb-3">
                <?php if ( ! empty( $size_terms ) ) : ?>
                  <select name="tch_size" id="productSize" class="mr-3 form-control" style="max-width:180px;">
                    <option value=""><?php esc_html_e( 'Select size', 'yourthemename' ); ?></option>
                    <?php foreach ( $size_terms as $st ) : ?>
                      <option value="<?php echo esc_attr( $st ); ?>"><?php echo esc_html( $st ); ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php endif; ?>

                <?php if ( ! empty( $color_terms ) ) : ?>
                  <select name="tch_color" id="productColor" class="form-control" style="max-width:180px;">
                    <option value=""><?php esc_html_e( 'Select color', 'yourthemename' ); ?></option>
                    <?php foreach ( $color_terms as $ct ) : ?>
                      <option value="<?php echo esc_attr( $ct ); ?>"><?php echo esc_html( $ct ); ?></option>
                    <?php endforeach; ?>
                  </select>
                <?php endif; ?>
              </div>

              <div class="cart-fav-box d-flex align-items-center">
                <?php if ( $product && $product->is_purchasable() && $product->is_in_stock() ) : ?>
                  <button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="btn essence-btn">
                    <?php echo esc_html__( 'Add to cart', 'yourthemename' ); ?>
                  </button>
                <?php else : ?>
                  <button type="button" class="btn btn-secondary" disabled><?php echo esc_html__( 'Unavailable', 'yourthemename' ); ?></button>
                <?php endif; ?>

                <div class="product-favourite ml-4">
                  <a href="#" class="favme fa fa-heart" aria-hidden="true"></a>
                </div>
              </div>
            </form>

        <?php } // end simple product else ?>

      </div><!-- .product-order-wrap -->

      <?php
      /**
       * Extra hooks: payment icons, meta, sharing etc. You can extend here.
       * do_action( 'woocommerce_single_product_summary' ) is intentionally not used fully so we keep the exact layout.
       */
      ?>

    </div><!-- .single_product_desc -->

  </div><!-- .row -->
</section>

<?php
get_footer();
?>

<script type="text/javascript">
(function($){
  $(function(){
    // Init owl-carousel if available
    if ( typeof $.fn.owlCarousel !== 'undefined' ) {
      $('.product_thumbnail_slides').owlCarousel({
        items: 1,
        loop: true,
        nav: true,
        dots: true,
        autoplay: false,
        navText: ['<i class="fa fa-angle-left"></i>','<i class="fa fa-angle-right"></i>']
      });
    } else {
      // fallback: make slides visible (no JS carousel)
      $('.product_thumbnail_slides .single-slide').css('display','block');
    }
  });
})(jQuery);
</script>
