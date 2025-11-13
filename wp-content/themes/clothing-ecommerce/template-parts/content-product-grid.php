<?php
// template-parts/content-product-grid.php
defined( 'ABSPATH' ) || exit;
global $product;
if ( empty( $product ) || ! $product->is_visible() ) {
    return;
}
?>
<div class="col-12 col-sm-6 col-lg-4">
  <div class="single-product-wrapper">
    <div class="product-img">
      <a href="<?php the_permalink(); ?>">
        <?php
        echo wp_get_attachment_image( $product->get_image_id(), 'shop_catalog', false, array( 'alt' => get_the_title(), 'class' => '' ) );
        $gallery = $product->get_gallery_image_ids();
        if ( ! empty( $gallery ) ) {
            echo wp_get_attachment_image( $gallery[0], 'shop_catalog', false, array( 'class' => 'hover-img' ) );
        }
        ?>
      </a>
      <?php if ( $product->is_on_sale() ) : ?>
        <div class="product-badge offer-badge">
            <span><?php
                $regular = (float) $product->get_regular_price();
                $price   = (float) $product->get_price();
                if ( $regular > 0 && $price < $regular ) {
                    echo '-' . round( ( ( $regular - $price ) / $regular ) * 100 ) . '%';
                } else {
                    echo 'Sale';
                }
            ?></span>
        </div>
      <?php endif; ?>
      <div class="product-favourite">
        <a href="#" class="favme fa fa-heart"></a>
      </div>
    </div>
    <div class="product-description">
      <span class="product-brand"><?php
          $cats = wc_get_product_category_list( $product->get_id(), ', ', '', '' );
          echo $cats ? wp_strip_all_tags( $cats ) : '';
      ?></span>
      <a href="<?php the_permalink(); ?>">
        <h6><?php the_title(); ?></h6>
      </a>
      <p class="product-price"><?php echo $product->get_price_html(); ?></p>
      <div class="hover-content">
        <div class="add-to-cart-btn">
          <?php
          echo apply_filters( 'woocommerce_loop_add_to_cart_link',
              sprintf( '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
                  esc_url( $product->add_to_cart_url() ),
                  esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
                  esc_attr( isset( $args['class'] ) ? $args['class'] : 'btn essence-btn' ),
                  isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
                  esc_html( $product->add_to_cart_text() )
              ),
          $product );
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
