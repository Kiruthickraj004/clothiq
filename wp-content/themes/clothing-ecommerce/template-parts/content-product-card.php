<?php
// template-parts/content-product-card.php
defined('ABSPATH') || exit;
global $product;
if ( empty($product) || ! $product->is_visible() ) return;
?>
<div class="single-product-wrapper">
  <div class="product-img">
    <a href="<?php the_permalink(); ?>">
      <?php
        $main_id = $product->get_image_id();
        echo wp_get_attachment_image( $main_id, 'medium', false, ['alt' => get_the_title(), 'class' => 'primary-img'] );
        $gallery = $product->get_gallery_image_ids();
        if (!empty($gallery)) {
          echo wp_get_attachment_image( $gallery[0], 'medium', false, ['class' => 'hover-img'] );
        }
      ?>
    </a>
    <?php if ( $product->is_on_sale() ) : ?>
      <div class="product-badge offer-badge"><span><?php echo esc_html( '-' . round( ( ( $product->get_regular_price() - $product->get_price() ) / $product->get_regular_price() ) * 100 ) . '%' ); ?></span></div>
    <?php endif; ?>
    <div class="product-favourite">
      <a href="#" class="favme fa fa-heart"></a>
    </div>
  </div>
  <div class="product-description">
    <span><?php echo esc_html( wp_get_post_terms( $product->get_id(), 'product_brand', array('fields' => 'names') ? 'unknown' : '' ) ? wp_get_post_terms( $product->get_id(), 'product_brand', array('fields' => 'names'))[0] : '' ); ?></span>
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
