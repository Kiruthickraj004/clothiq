<?php
/**
 * Theme header — Elementor Theme Builder location with theme fallback.
 *
 * @package Clothing_Ecommerce_Child
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
if ( ! clothiq_elementor_theme_do_location( 'header' ) ) {
    get_template_part( 'template-parts/header', 'default' );
}
