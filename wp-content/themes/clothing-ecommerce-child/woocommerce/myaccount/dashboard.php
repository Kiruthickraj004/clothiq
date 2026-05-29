<?php
/**
 * My Account dashboard
 *
 * @package Clothing_Ecommerce_Child
 */

defined( 'ABSPATH' ) || exit;

$allowed_html = array(
	'a' => array( 'href' => array() ),
);

$cards = array(
	array(
		'endpoint' => 'orders',
		'title'    => __( 'Orders', 'clothing-ecommerce-child' ),
		'desc'     => __( 'Track, view, and manage your purchases.', 'clothing-ecommerce-child' ),
	),
	array(
		'endpoint' => 'downloads',
		'title'    => __( 'Downloads', 'clothing-ecommerce-child' ),
		'desc'     => __( 'Access your digital products and files.', 'clothing-ecommerce-child' ),
	),
	array(
		'endpoint' => 'edit-address',
		'title'    => __( 'Addresses', 'clothing-ecommerce-child' ),
		'desc'     => wc_shipping_enabled()
			? __( 'Update billing and shipping addresses.', 'clothing-ecommerce-child' )
			: __( 'Update your billing address.', 'clothing-ecommerce-child' ),
	),
	array(
		'endpoint' => 'edit-account',
		'title'    => __( 'Account details', 'clothing-ecommerce-child' ),
		'desc'     => __( 'Change your name, email, and password.', 'clothing-ecommerce-child' ),
	),
);
?>

<div class="clothiq-dashboard">
	<div class="clothiq-dashboard__panel">
		<p class="clothiq-dashboard__greeting">
			<?php
			printf(
				wp_kses(
					/* translators: 1: user display name 2: logout url */
					__( 'You are signed in as <strong>%1$s</strong>. <a href="%2$s">Log out</a>', 'clothing-ecommerce-child' ),
					array(
						'strong' => array(),
						'a'      => array( 'href' => array() ),
					)
				),
				esc_html( $current_user->display_name ),
				esc_url( wc_logout_url() )
			);
			?>
		</p>
		<p class="clothiq-dashboard__intro">
			<?php esc_html_e( 'Use the shortcuts below or the menu on the left to manage your account.', 'clothing-ecommerce-child' ); ?>
		</p>
	</div>

	<div class="clothiq-dashboard__grid">
		<?php foreach ( $cards as $card ) : ?>
			<a class="clothiq-account-card" href="<?php echo esc_url( wc_get_account_endpoint_url( $card['endpoint'] ) ); ?>">
				<span class="clothiq-account-card__icon" aria-hidden="true"><?php echo clothiq_get_account_nav_icon( $card['endpoint'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="clothiq-account-card__title"><?php echo esc_html( $card['title'] ); ?></span>
				<span class="clothiq-account-card__desc"><?php echo esc_html( $card['desc'] ); ?></span>
				<span class="clothiq-account-card__arrow" aria-hidden="true">&rarr;</span>
			</a>
		<?php endforeach; ?>
	</div>
</div>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
