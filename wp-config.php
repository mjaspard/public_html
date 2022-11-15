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
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'windspot_WPMVK');

/** MySQL database username */
define('DB_USER', 'windspot_WPMVK');

/** MySQL database password */
define('DB_PASSWORD', 'AOvZe74y8RLYHxKjZ');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'a0337d8d66836473d13d38803b6ce99808700d2a7835ed8ef13371fb1f0109d7');
define('SECURE_AUTH_KEY', 'b910fcc3e0a2e0993e3b8b147c68bde365dc786acf6c6e4a05822765f33b1796');
define('LOGGED_IN_KEY', '5cf49036a872562acdc047204731def431cfdb7fa97f6203552e0a8195b82548');
define('NONCE_KEY', '39c2b7ae6d52274f95d987958ef8fbd240e273c333effbe14ba2cffae4da7d78');
define('AUTH_SALT', '1fe3b39faeaf673ed8e1d8eaf588b3bef1ab33a24409d1bdcc18620894f4a099');
define('SECURE_AUTH_SALT', '1376db537b286198ee65820ff8c038d7180d26a8d9612105497b3ec2e1103182');
define('LOGGED_IN_SALT', 'f592dc719b7b812bbfc4b5efec87ca5c344497cbbbfab397519b176c29e09ef4');
define('NONCE_SALT', '8e045ab7e4a2baf002ef964c69b094b2e1635f975bff8e813edcb80ea731b739');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = '_MVK_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);


// Settings modified by hosting provider
define( 'WP_CRON_LOCK_TIMEOUT', 120   );
define( 'AUTOSAVE_INTERVAL',    300   );
define( 'WP_POST_REVISIONS',    5     );
define( 'EMPTY_TRASH_DAYS',     7     );
define( 'WP_AUTO_UPDATE_CORE',  true  );
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
