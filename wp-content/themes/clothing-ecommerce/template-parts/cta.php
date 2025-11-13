<?php
// template-parts/cta.php
?>
<div class="cta-area">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="cta-content bg-img background-overlay"
             style="background-image: url(<?php echo esc_url( get_template_directory_uri() . '/assets/img/bg-img/bg-5.jpg' ); ?>);">
          <div class="h-100 d-flex align-items-center justify-content-end">
            <div class="cta--text">
              <h6>-60%</h6>
              <h2>Global Sale</h2>
              <a href="<?php echo esc_url( wc_get_page_permalink('shop') ); ?>" class="btn essence-btn">Buy Now</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
