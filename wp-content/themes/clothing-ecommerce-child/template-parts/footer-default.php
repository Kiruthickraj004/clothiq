<?php
/**
 * Default theme footer (fallback when Elementor footer location is not used).
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;
?>

<footer class="footer_area clearfix">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-6">
        <div class="single_widget_area d-flex mb-30">
          <div class="footer-logo mr-50">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
              <?php
              if ( has_custom_logo() ) {
                  echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'alt' => get_bloginfo( 'name' ) ) );
              } else {
                  ?>
                  <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/core-img/logo2.png' ); ?>" alt="<?php bloginfo( 'name' ); ?>">
                  <?php
              }
              ?>
            </a>
          </div>
          <div class="footer_menu">
            <ul>
              <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Shop', 'clothing-ecommerce-child' ); ?></a></li>
              <?php endif; ?>
              <?php if ( get_option( 'page_for_posts' ) ) : ?>
                <li><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"><?php esc_html_e( 'Blog', 'clothing-ecommerce-child' ); ?></a></li>
              <?php endif; ?>
              <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact', 'clothing-ecommerce-child' ); ?></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-5">
      <div class="col-md-12 text-center">
        <p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'clothing-ecommerce-child' ); ?></p>
      </div>
    </div>
  </div>
</footer>
