<?php
// Shop page
defined( 'ABSPATH' ) || exit;
get_header();
?>
<?php
$breadcrumb_bg = get_template_directory_uri() . '/assets/img/bg-img/breadcumb.jpg';
$current_term = is_tax( 'product_cat' ) ? get_queried_object() : null;
$page_title = $current_term ? $current_term->name : ( is_shop() ? get_the_title( wc_get_page_id( 'shop' ) ) : single_post_title( '', false ) );
?>
<div class="breadcumb_area bg-img" style="background-image: url('<?php echo esc_url( $breadcrumb_bg ); ?>');">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12">
        <div class="page-title text-center">
          <h2><?php echo esc_html( $page_title ); ?></h2>
        </div>
      </div>
    </div>
  </div>
</div>

<section class="shop_grid_area section-padding-80">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-4 col-lg-3">
        <div class="shop_sidebar_area">
          <?php
            $possible_templates = ['sidebar-shop.php','template-parts/sidebar-shop.php'];
            $located = locate_template( $possible_templates, false, false );
            if ( $located ) {
                load_template( $located, true );
            } elseif ( is_active_sidebar( 'sidebar-1' ) ) {
                dynamic_sidebar( 'sidebar-1' );
            }
          ?>
        </div>
      </div>

      <div class="col-12 col-md-8 col-lg-9">
        <div class="shop_grid_product_area">

          <div class="row">
            <div class="col-12">
              <div class="product-topbar d-flex align-items-center justify-content-between">
                <div class="total-products">
                  <?php
                  wc_print_notices();
                  global $wp_query;
                  $total = isset( $wp_query->found_posts ) ? $wp_query->found_posts : 0;
                  ?>
                  <p><span><?php echo intval( $total ); ?></span> products found</p>
                </div>
                <div class="product-sorting d-flex">
                  <p>Sort by:</p>
                  <div>
                    <?php woocommerce_catalog_ordering(); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <?php if ( woocommerce_product_loop() ) :
                while ( have_posts() ) : the_post();
                    global $product;
                    get_template_part( 'template-parts/content', 'product-grid' );
                endwhile;
            else :
                wc_get_template( 'loop/no-products-found.php' );
            endif;
            ?>
          </div>

          <nav aria-label="navigation">
            <?php
            the_posts_pagination( array(
              'mid_size'  => 2,
              'prev_text' => '<i class="fa fa-angle-left"></i>',
              'next_text' => '<i class="fa fa-angle-right"></i>',
              'screen_reader_text' => 'Products navigation',
            ) );
            ?>
          </nav>
        </div>
      </div>
    </div>
  </div>
</section>
<?php get_footer(); ?>
