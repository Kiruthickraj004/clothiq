<?php
// template-parts/top-categories.php
?>
<div class="top_catagory_area section-padding-80 clearfix">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-6 col-md-4">
        <div class="single_catagory_area d-flex align-items-center justify-content-center bg-img"
             style="background-image: url(<?php echo esc_url( get_template_directory_uri() . '/assets/img/bg-img/bg-2.jpg' ); ?>);">
          <div class="catagory-content">
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') . '?filter_cat=clothing' ); ?>">Clothing</a>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <div class="single_catagory_area d-flex align-items-center justify-content-center bg-img"
             style="background-image: url(<?php echo esc_url( get_template_directory_uri() . '/assets/img/bg-img/bg-3.jpg' ); ?>);">
          <div class="catagory-content">
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') . '?filter_cat=shoes' ); ?>">Shoes</a>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-md-4">
        <div class="single_catagory_area d-flex align-items-center justify-content-center bg-img"
             style="background-image: url(<?php echo esc_url( get_template_directory_uri() . '/assets/img/bg-img/bg-4.jpg' ); ?>);">
          <div class="catagory-content">
            <a href="<?php echo esc_url( wc_get_page_permalink('shop') . '?filter_cat=accessories' ); ?>">Accessories</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
