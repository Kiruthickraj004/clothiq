<?php
/**
 * My Account page layout
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$display_name = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
?>

<div class="clothiq-account">
	<header class="clothiq-account__hero">
		<p class="clothiq-account__eyebrow"><?php esc_html_e( 'My Account', 'clothing-ecommerce-child' ); ?></p>
		<h1 class="clothiq-account__title">
			<?php
			printf(
				/* translators: %s: customer display name */
				esc_html__( 'Hello, %s', 'clothing-ecommerce-child' ),
				esc_html( $display_name )
			);
			?>
		</h1>
		<p class="clothiq-account__subtitle">
			<?php esc_html_e( 'Manage your orders, downloads, addresses, and account settings in one place.', 'clothing-ecommerce-child' ); ?>
		</p>
	</header>

	<div class="clothiq-account__layout">
		<aside class="clothiq-account__sidebar" aria-label="<?php esc_attr_e( 'Account navigation', 'clothing-ecommerce-child' ); ?>">
			<?php do_action( 'woocommerce_account_navigation' ); ?>
		</aside>

		<div class="clothiq-account__main woocommerce-MyAccount-content">
			<?php do_action( 'woocommerce_account_content' ); ?>
		</div>
	</div>
</div>
