<?php
/* front-page.php */
get_header();
get_template_part('template-parts/hero');
get_template_part('template-parts/top-categories');   
get_template_part('template-parts/cta');  
?>

<section class="new_arrivals_area section-padding-80 clearfix">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="section-heading text-center">
          <h2>Popular Products</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="row popular-products-grid">
          <?php
          $args = array(
            'limit' => 4,
            'status' => 'publish',
            'orderby' => 'date',
            'order' => 'DESC',
          );

          $products = wc_get_products( $args );

          if ( ! empty( $products ) ) :
            foreach ( $products as $prod ) :
              $product = wc_get_product( $prod->get_id() );
              $post = get_post( $product->get_id() );
              setup_postdata( $post );
              $GLOBALS['product'] = $product;
              ?>
              <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <?php get_template_part( 'template-parts/content', 'product-card' ); ?>
              </div>

              <?php
            endforeach;
            wp_reset_postdata();
          else:
            echo '<p class="text-center">No products found</p>';
          endif;
          ?>
        </div>

      </div>
    </div>
  </div>
</section>
<?php get_template_part('template-parts/brands');  ?>
<?php get_footer(); ?>
