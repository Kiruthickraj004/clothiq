<?php
// template-parts/hero.php
?>
<section class="welcome_area bg-img background-overlay" style="background-image: url(<?php echo esc_url(get_template_directory_uri() . '/assets/img/bg-img/bg-1.jpg'); ?>);">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12">
        <div class="hero-content">
          <h6>asoss</h6>
          <h2>New Collection</h2>
          <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="btn essence-btn">view collection</a>
        </div>
      </div>
    </div>
  </div>
</section>
