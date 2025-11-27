<?php
define('WP_CACHE', true); // WP-Optimize Cache
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */
// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'clothing-ecommerce' );
/** Database username */
define( 'DB_USER', 'root' );
/** Database password */
define( 'DB_PASSWORD', '' );
/** Database hostname */
define( 'DB_HOST', 'localhost' );
/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );
/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );
/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '6]WkyBnGW*k!+BWUca;Y{W9qNVr>4N%qQwe&8 3<OX|pGfn62nB4^pj}xC{DxT|T' );
define( 'SECURE_AUTH_KEY',  'pWneBh0!-%Wk31t?l.QVwqTNc!AEI91yOq8S ~IW$ik|g;^LWrGIv%@$lvlEFv/q' );
define( 'LOGGED_IN_KEY',    'Gwj) NfP8ukw0+Zr>-4Y*9 <_9@2rw{nB)/Hh{@2@* Wo#0Tpyk>aVv$/W@kI>o#' );
define( 'NONCE_KEY',        '>*8wjBHzK9%~O-qtQ):8BnKL>u2f8R]la#[@LHdQ-qbnk/^:lm-ixq(+aRyk^$3M' );
define( 'AUTH_SALT',        ':fXqQk9Vxcz$W3TZud+&e8i52} 2Z &3q:r@Q-^fQ}M1_9QS3?L+33ZjYCU<c>Br' );
define( 'SECURE_AUTH_SALT', '6BO8IHN>@k z!~,K;.s5cH#5h0BhE<*(`XsVOs5.;mAUs%myqyD^<ktb7W2#xUav' );
define( 'LOGGED_IN_SALT',   '_y]0R,/Zl7QR9H/3+h6fSF!+~ue+7)[v^OV/x=B0?P,xW)g=X>;G@mh`oM.jr.;Y' );
define( 'NONCE_SALT',       'crV{S5Er^0##=jG+)5-|B65oP?|HgjD&+QNMaV2`K>qgxE.b_od9~=xs 3-arg#n' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';
/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
/* Add any custom values between this line and the "stop editing" line. */
/* That's all, stop editing! Happy publishing. */
/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}
/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';