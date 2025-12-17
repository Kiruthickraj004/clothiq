<?php
// footer.php
?>
<footer class="footer_area clearfix">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-6">
        <div class="single_widget_area d-flex mb-30">
          <div class="footer-logo mr-50">
            <a href="<?php echo esc_url(home_url('/')); ?>"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/core-img/logo2.png'); ?>" alt=""></a>
          </div>
          <div class="footer_menu">
            <ul>
              <li><a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">Shop</a></li>
              <li><a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">Blog</a></li>
              <li><a href="<?php echo esc_url(home_url('/contact')); ?>">Contact</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-md-12 text-center">
        <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>
 <script src="js/jquery/jquery-2.2.4.min.js"></script>
    <!-- Popper js -->
    <script src="js/popper.min.js"></script>
    <!-- Bootstrap js -->
    <script src="js/bootstrap.min.js"></script>
    <!-- Plugins js -->
    <script src="js/plugins.js"></script>
    <!-- Classy Nav js -->
    <script src="js/classy-nav.min.js"></script>
    <!-- Active js -->
    <script src="js/active.js"></script> 
<?php wp_footer(); ?>
</body>
</html>
