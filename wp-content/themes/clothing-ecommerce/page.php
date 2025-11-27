<?php
// page.php — minimal page template for clothing-ecommerce theme
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container py-5">
  <?php
  if ( have_posts() ) :
    while ( have_posts() ) : the_post();
      // Page title (optional)
      echo '<h1 class="entry-title">' . esc_html( get_the_title() ) . '</h1>';
      // CRITICAL: prints page content (this runs shortcodes like [woocommerce_checkout])
      echo '<div class="entry-content">';
      the_content();
      echo '</div>';
    endwhile;
  endif;
  ?>
</div>

<?php get_footer(); ?>
