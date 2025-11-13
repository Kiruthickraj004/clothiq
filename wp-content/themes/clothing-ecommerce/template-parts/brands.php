<?php
//brands
?>
<div class="brands-area d-flex align-items-center justify-content-between">
  <?php
  $brands = [
    'brand1' => '/assets/img/core-img/brand1.png',
    'brand2' => '/assets/img/core-img/brand2.png',
    'brand3' => '/assets/img/core-img/brand3.png',
    'brand4' => '/assets/img/core-img/brand4.png',
    'brand5' => '/assets/img/core-img/brand5.png',
    'brand6' => '/assets/img/core-img/brand6.png',
  ];
  foreach ( $brands as $slug => $path ) : ?>
    <div class="single-brands-logo">
      <img src="<?php echo esc_url( get_template_directory_uri() . $path ); ?>" alt="<?php echo esc_attr( $slug ); ?>">
    </div>
  <?php endforeach; ?>
</div>
