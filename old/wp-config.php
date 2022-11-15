<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'windspot_wp346' );

/** MySQL database username */
define( 'DB_USER', 'windspot_wp346' );

/** MySQL database password */
define( 'DB_PASSWORD', 'l-0S[T7pP9' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'mlokb8x8gckctu1bqczibjzc7umk4a9gqygdudlroc9alnuoskd5zwbkmskkapz6' );
define( 'SECURE_AUTH_KEY',  '6cldpcsopfh6obvwkd7yxpguxrv5muuhfcl7znqc9bxnfpov50z6wiaadm0pa85i' );
define( 'LOGGED_IN_KEY',    '5dkqsmdpktk6stykyyepsqtnfhlva51frhqi6pa2splj3faic62tynynvlmj2uii' );
define( 'NONCE_KEY',        'fxhbme82kxomjd1mcz3z5l4lhscrqsg56fs5lowzyzz1p26gnexck2oatldfsg7b' );
define( 'AUTH_SALT',        'gusvuwbcgswtariinwxvawsm0kd4hxlu12vtw2fq3ijtizyeuzdu61zypibd5btn' );
define( 'SECURE_AUTH_SALT', 'nqhziwp5sohh5lkpw3evgpaanz3q8otlyymx4sxrjkc0eoxprnp1jogqwhwoeygn' );
define( 'LOGGED_IN_SALT',   'txbzyuhpjkpgarbfxovbsvz7nldxbh1qpukjraracr49omzrhwmupjexlb6qxl4w' );
define( 'NONCE_SALT',       'ipw9prj77tenwctzawoz8qj9ntlchpnmrrp3kcrgmmncm5kx7x7kduy8jckmw7bn' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpk1_';

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
