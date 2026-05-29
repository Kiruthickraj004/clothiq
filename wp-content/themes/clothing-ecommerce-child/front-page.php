<?php
/**
 * Front page — uses Elementor when the homepage is built with Elementor.
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( clothiq_elementor_theme_do_location( 'single' ) ) {
    get_footer();
    return;
}

$front_page_id = (int) get_option( 'page_on_front' );

if ( $front_page_id && clothiq_is_elementor_built( $front_page_id ) ) {
    echo '<div class="clothiq-elementor-content clothiq-front-page-elementor">';
    echo clothiq_get_elementor_content( $front_page_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo '</div>';
    get_footer();
    return;
}

if ( 'page' === get_option( 'show_on_front' ) && $front_page_id ) {
    $query = new WP_Query( array(
        'page_id' => $front_page_id,
        'post_type' => 'page',
    ) );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            do_action( 'clothiq_before_page_content' );
            echo '<div class="entry-content">';
            the_content();
            echo '</div>';
            do_action( 'clothiq_after_page_content' );
        }
        wp_reset_postdata();
        get_footer();
        return;
    }
}

// Theme default homepage sections.
get_template_part( 'template-parts/hero' );
get_template_part( 'template-parts/top-categories' );
get_template_part( 'template-parts/cta' );
?>

<section class="new_arrivals_area section-padding-80 clearfix">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="section-heading text-center">
          <h2><?php esc_html_e( 'Popular Products', 'clothing-ecommerce-child' ); ?></h2>
        </div>
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="row popular-products-grid">
          <?php
          $products = wc_get_products(
              array(
                  'limit'   => 4,
                  'status'  => 'publish',
                  'orderby' => 'date',
                  'order'   => 'DESC',
              )
          );

          if ( ! empty( $products ) ) :
              foreach ( $products as $prod ) :
                  $product            = wc_get_product( $prod->get_id() );
                  $post               = get_post( $product->get_id() );
                  $GLOBALS['product'] = $product;
                  setup_postdata( $post );
                  ?>
                  <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <?php get_template_part( 'template-parts/content', 'product-card' ); ?>
                  </div>
                  <?php
              endforeach;
              wp_reset_postdata();
          else :
              echo '<p class="text-center">' . esc_html__( 'No products found', 'clothing-ecommerce-child' ) . '</p>';
          endif;
          ?>
        </div>
      </div>
    </div>
  </div>
</section>

<?php
get_template_part( 'template-parts/brands' );
get_footer();
