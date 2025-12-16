<?php
// header.php
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php

if ( function_exists( 'tch_render_cart_modal_html' ) ) {
    echo tch_render_cart_modal_html();
}
?>

<header class="header_area">
  <div class="classy-nav-container breakpoint-off d-flex align-items-center justify-content-between">
    <nav class="classy-navbar" id="essenceNav">
      <a class="nav-brand" href="<?php echo esc_url(home_url('/')); ?>">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/core-img/logo.png'); ?>" alt="<?php bloginfo('name'); ?>">
      </a>
      <div class="classy-navbar-toggler">
        <span class="navbarToggler"><span></span><span></span><span></span></span>
      </div>
      <div class="classy-menu">
        <div class="classycloseIcon">
          <div class="cross-wrap"><span class="top"></span><span class="bottom"></span></div>
        </div>

        <div class="classynav">
          <?php
          wp_nav_menu([
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => '',
            'items_wrap' => '<ul>%3$s</ul>',
            'fallback_cb' => false,
          ]);
          ?>
        </div>
      </div>
    </nav>

    <div class="header-meta d-flex clearfix justify-content-end">
      <div class="search-area">
        <?php get_search_form(); ?>
      </div>

      <div class="favourite-area">
        <a href="#"><img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/core-img/heart.svg'); ?>" alt="favourites"></a>
      </div>

      <div class="user-login-info">
        <?php

        if ( function_exists( 'wc_get_page_permalink' ) && wc_get_page_permalink( 'myaccount' ) ) {
            $account_url = wc_get_page_permalink( 'myaccount' );
        } else {
            $account_url = wp_login_url();
        }
        ?>
        <a href="<?php echo esc_url( $account_url ); ?>">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/core-img/user.svg'); ?>" alt="user">
        </a>
      </div>

      <div class="cart-area">
        <?php
        $cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : '#';
        $count = 0;
        if ( function_exists( 'WC' ) && WC()->cart ) {
            $count = WC()->cart->get_cart_contents_count();
        }
        ?>
        <a href="<?php echo esc_url( $cart_url ); ?>" id="essenceCartBtn" class="essence-cart-contents">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/core-img/bag.svg'); ?>" alt="cart">
          <span class="cart-count"><?php echo intval( $count ); ?></span>
        </a>
      </div>
    </div>
  </div>
</header>
