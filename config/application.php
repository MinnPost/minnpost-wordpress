<?php

/** @var string Directory containing the .env file */
$root_dir = dirname( __DIR__, 2 );

/** @var string Document Root */
$webroot_dir = __DIR__;

/**
 * Expose global env() function from oscarotero/env
 */
Env::init();

/**
 * Use Dotenv to set required environment variables and load .env file in root
 */
$dotenv = new Dotenv\Dotenv( $root_dir );
if ( file_exists( $root_dir . '/.env' ) ) {
	$dotenv->load();
	$dotenv->required( [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL' ] );
}

/**
 * Set up our global environment constant and load its config first
 * Default: production
 */
define( 'WP_ENV', env( 'WP_ENV' ) ?: 'production' );

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if ( file_exists( $env_config ) ) {
	require_once $env_config;
}

/**
 * URLs
 */
define( 'WP_HOME', env( 'WP_HOME' ) );
define( 'WP_SITEURL', env( 'WP_SITEURL' ) );

/**
 * DB settings
 */
define( 'DB_NAME', env( 'DB_NAME' ) );
define( 'DB_USER', env( 'DB_USER' ) );
define( 'DB_PASSWORD', env( 'DB_PASSWORD' ) );
define( 'DB_HOST', env( 'DB_HOST' ) ? : 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );
$table_prefix = env( 'DB_PREFIX' ) ?: 'wp_';

// write files instead of checking groups
define( 'FS_METHOD', 'direct' );

/**
 * Authentication Unique Keys and Salts
 */
define( 'AUTH_KEY', env( 'AUTH_KEY' ) );
define( 'SECURE_AUTH_KEY', env( 'SECURE_AUTH_KEY' ) );
define( 'LOGGED_IN_KEY', env( 'LOGGED_IN_KEY' ) );
define( 'NONCE_KEY', env( 'NONCE_KEY' ) );
define( 'AUTH_SALT', env( 'AUTH_SALT' ) );
define( 'SECURE_AUTH_SALT', env( 'SECURE_AUTH_SALT' ) );
define( 'LOGGED_IN_SALT', env( 'LOGGED_IN_SALT' ) );
define( 'NONCE_SALT', env( 'NONCE_SALT' ) );

/**
 * Custom Settings
 */

// object sync for salesforce
define( 'OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY', env( 'OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY' ) );
define( 'OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET', env( 'OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET' ) );
define( 'OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL', env( 'OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL' ) );
define( 'OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL', env( 'OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL' ) );
define( 'OBJECT_SYNC_SF_SALESFORCE_API_VERSION', env( 'OBJECT_SYNC_SF_SALESFORCE_API_VERSION' ) );
define( 'OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH', env( 'OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH' ) );
define( 'OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH', env( 'OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH' ) );

// payment processing
define( 'PAYMENT_PROCESSOR_URL', env( 'PAYMENT_PROCESSOR_URL' ) );

// mailchimp
define( 'FORM_PROCESSOR_MC_MAILCHIMP_API_KEY', env( 'FORM_PROCESSOR_MC_MAILCHIMP_API_KEY' ) );

// gravity forms
define( 'GF_LICENSE_KEY', env( 'GF_LICENSE_KEY' ) );

// redis
$redis_server = array(
	'host'     => '127.0.0.1',
	'port'     => 6379,
	'database' => env( 'WP_REDIS_DATABASE' ), // Optionally use a specific numeric Redis database. Default is 0.
);

// ElasticPress
define( 'EP_INDEX_PREFIX', env( 'EP_INDEX_PREFIX' ) );
define( 'EP_HOST', env( 'EP_HOST' ) );

// analytics
define( 'WP_ANALYTICS_TRACKING_ID', env( 'WP_ANALYTICS_TRACKING_ID' ) );

// minify merge refresh
define( 'MMR_CACHE_DIR', env( 'MMR_CACHE_DIR' ) );
define( 'MMR_CACHE_URL', env( 'MMR_CACHE_URL' ) );
