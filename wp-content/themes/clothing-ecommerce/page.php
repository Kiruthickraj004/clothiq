<?php
// page.php
defined( 'ABSPATH' ) || exit;
get_header();
?>

<div class="container py-5">
  <?php
  if ( have_posts() ) :
    while ( have_posts() ) : the_post();
      // echo '<h1 class="entry-title">' . esc_html( get_the_title() ) . '</h1>';
      echo '<div class="entry-content">';
      the_content();
      echo '</div>';
    endwhile;
  endif;
  ?>
</div>

<?php get_footer(); ?>
