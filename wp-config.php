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
require_once( __DIR__ . '/vendor/autoload.php' );
$dotenv = new Dotenv\Dotenv( __DIR__ . '/..' );
$dotenv->load();

define( 'DB_NAME', $_ENV['DB_NAME'] );
define( 'DB_USER', $_ENV['DB_USER'] );
define( 'DB_PASSWORD', $_ENV['DB_PASSWORD'] );
define( 'DB_HOST', $_ENV['DB_HOST'] );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

// write files instead of checking groups
define( 'FS_METHOD', 'direct' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY', $_ENV['AUTH_KEY'] );
define( 'SECURE_AUTH_KEY', $_ENV['SECURE_AUTH_KEY'] );
define( 'LOGGED_IN_KEY', $_ENV['LOGGED_IN_KEY'] );
define( 'NONCE_KEY', $_ENV['NONCE_KEY'] );
define( 'AUTH_SALT', $_ENV['AUTH_SALT'] );
define( 'SECURE_AUTH_SALT', $_ENV['SECURE_AUTH_SALT'] );
define( 'LOGGED_IN_SALT', $_ENV['LOGGED_IN_SALT'] );
define( 'NONCE_SALT', $_ENV['NONCE_SALT'] );

// WordPress urls
define( 'WP_HOME', $_ENV['WP_HOME'] );
define( 'WP_SITEURL', $_ENV['WP_SITEURL'] );

// object sync for salesforce
define( 'OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY', $_ENV['OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY'] );
define( 'OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET', $_ENV['OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET'] );
define( 'OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL', $_ENV['OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL'] );
define( 'OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL', $_ENV['OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL'] );
define( 'OBJECT_SYNC_SF_SALESFORCE_API_VERSION', $_ENV['OBJECT_SYNC_SF_SALESFORCE_API_VERSION'] );
define( 'OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH', $_ENV['OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH'] );
define( 'OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH', $_ENV['OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH'] );

// payment processing
define( 'PAYMENT_PROCESSOR_URL', $_ENV['PAYMENT_PROCESSOR_URL'] );

// mailchimp
define( 'FORM_PROCESSOR_MC_MAILCHIMP_API_KEY', $_ENV['FORM_PROCESSOR_MC_MAILCHIMP_API_KEY'] );

// gravity forms
define( 'GF_LICENSE_KEY', $_ENV['GF_LICENSE_KEY'] );

// redis
define( 'WP_REDIS_DATABASE', $_ENV['WP_REDIS_DATABASE'] );

// ElasticPress
define( 'EP_INDEX_PREFIX', $_ENV['EP_INDEX_PREFIX'] );
define( 'EP_HOST', $_ENV['EP_HOST'] );

// analytics
define( 'WP_ANALYTICS_TRACKING_ID', $_ENV['WP_ANALYTICS_TRACKING_ID'] );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = $_ENV['DB_PREFIX'];

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
if ( 'true' === $_ENV['WP_DEBUG'] ) {
	define( 'WP_DEBUG', $_ENV['WP_DEBUG'] );
}
if ( 'true' === $_ENV['JETPACK_DEV_DEBUG'] ) {
	define( 'JETPACK_DEV_DEBUG', $_ENV['JETPACK_DEV_DEBUG'] );
}
if ( 'true' === $_ENV['SCRIPT_DEBUG'] ) {
	define( 'SCRIPT_DEBUG', $_ENV['SCRIPT_DEBUG'] );
}

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
