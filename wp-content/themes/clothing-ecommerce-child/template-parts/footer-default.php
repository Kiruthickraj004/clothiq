<?php
/**
 * Default theme footer (fallback when Elementor footer location is not used).
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;
?>

<footer class="footer_area">
  <!-- Main Footer Section -->
  <div class="footer_main_section">
    <div class="container">
      <div class="row footer_widget_area">
        
        <!-- About Section -->
        <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0">
          <div class="footer_widget footer_about_widget">
            <div class="footer_logo mb-3">
              <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer_brand">
                <?php
                if ( has_custom_logo() ) {
                    echo wp_get_attachment_image( get_theme_mod( 'custom_logo' ), 'medium', false, array( 'alt' => get_bloginfo( 'name' ), 'class' => 'footer_logo_img' ) );
                } else {
                    ?>
                    <span class="footer_brand_text"><?php bloginfo( 'name' ); ?></span>
                    <?php
                }
                ?>
              </a>
            </div>
            <p class="footer_description"><?php bloginfo( 'description' ); ?></p>
            <ul class="footer_social_links">
              <li><a href="#" class="social_link facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
              <li><a href="#" class="social_link twitter" title="Twitter"><i class="fab fa-twitter"></i></a></li>
              <li><a href="#" class="social_link instagram" title="Instagram"><i class="fab fa-instagram"></i></a></li>
              <li><a href="#" class="social_link pinterest" title="Pinterest"><i class="fab fa-pinterest-p"></i></a></li>
            </ul>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0">
          <div class="footer_widget footer_links_widget">
            <h4 class="footer_widget_title">Quick Links</h4>
            <ul class="footer_links_list">
              <?php if ( function_exists( 'wc_get_page_permalink' ) ) : ?>
                <li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'Shop All', 'clothing-ecommerce-child' ); ?></a></li>
              <?php endif; ?>
              <li><a href="<?php echo esc_url( home_url( '/about' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'About Us', 'clothing-ecommerce-child' ); ?></a></li>
              <?php if ( get_option( 'page_for_posts' ) ) : ?>
                <li><a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'Blog', 'clothing-ecommerce-child' ); ?></a></li>
              <?php endif; ?>
              <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'Contact Us', 'clothing-ecommerce-child' ); ?></a></li>
            </ul>
          </div>
        </div>

        <!-- Customer Service -->
        <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0">
          <div class="footer_widget footer_service_widget">
            <h4 class="footer_widget_title">Customer Service</h4>
            <ul class="footer_links_list">
              <li><a href="<?php echo esc_url( home_url( '/shipping-returns' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'Shipping & Returns', 'clothing-ecommerce-child' ); ?></a></li>
              <li><a href="<?php echo esc_url( home_url( '/faq' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'FAQ', 'clothing-ecommerce-child' ); ?></a></li>
              <li><a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'Privacy Policy', 'clothing-ecommerce-child' ); ?></a></li>
              <li><a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><i class="fas fa-angle-right"></i> <?php esc_html_e( 'Terms & Conditions', 'clothing-ecommerce-child' ); ?></a></li>
            </ul>
          </div>
        </div>

        <!-- Newsletter & Contact -->
        <div class="col-12 col-sm-6 col-lg-3 mb-4 mb-lg-0">
          <div class="footer_widget footer_contact_widget">
            <h4 class="footer_widget_title">Get in Touch</h4>
            <ul class="footer_contact_info">
              <li>
                <i class="fas fa-map-marker-alt"></i>
                <span>123 Fashion Street, Style City, SC 12345</span>
              </li>
              <li>
                <i class="fas fa-phone"></i>
                <a href="tel:+1234567890">+1 (234) 567-890</a>
              </li>
              <li>
                <i class="fas fa-envelope"></i>
                <a href="mailto:info@clothiq.com">info@clothiq.com</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Payment Methods & Bottom Footer -->
  <div class="footer_bottom_section">
    <div class="container">
      <div class="row footer_bottom_content">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
          <div class="footer_payment_methods">
            <span class="payment_label"><?php esc_html_e( 'We Accept:', 'clothing-ecommerce-child' ); ?></span>
            <ul class="payment_icons">
              <li><i class="fab fa-cc-visa" title="Visa"></i></li>
              <li><i class="fab fa-cc-mastercard" title="Mastercard"></i></li>
              <li><i class="fab fa-cc-paypal" title="PayPal"></i></li>
              <li><i class="fab fa-cc-amex" title="American Express"></i></li>
            </ul>
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="footer_copyright">
            <p>&copy; <span class="copy_year"><?php echo esc_html( gmdate( 'Y' ) ); ?></span> <strong><?php bloginfo( 'name' ); ?></strong>. <?php esc_html_e( 'All rights reserved.', 'clothing-ecommerce-child' ); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
