<?php
/**
 * Plugin Name: WPCode - Insert Headers, Footers, and Code Snippets
 * Plugin URI: https://www.wpcode.com/
 * Version: 2.0.1
 * Requires at least: 4.6
 * Requires PHP: 5.5
 * Tested up to: 6.0
 * Author: WPCode
 * Author URI: https://www.wpcode.com/
 * Description: Easily add code snippets in WordPress. Insert scripts to the header and footer, add PHP code snippets with conditional logic, insert ads pixel, custom content, and more.
 * License: GPLv2 or later
 *
 * Text Domain:         insert-headers-and-footers
 * Domain Path:         /languages
 *
 * @package WPCode
 */

/*
	Copyright 2019 WPBeginner

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Main WPCode Class
 */
class WPCode {

	/**
	 * Holds the instance of the plugin.
	 *
	 * @since 2.0.0
	 *
	 * @var WPCode The one true WPCode
	 */
	private static $instance;

	/**
	 * Plugin version.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $version = '';

	/**
	 * The auto-insert instance.
	 *
	 * @var WPCode_Auto_Insert
	 */
	public $auto_insert;

	/**
	 * The snippet execution instance.
	 *
	 * @var WPCode_Snippet_Execute
	 */
	public $execute;

	/**
	 * The error handling instance.
	 *
	 * @var WPCode_Error
	 */
	public $error;

	/**
	 * The conditional logic instance.
	 *
	 * @var WPCode_Conditional_Logic
	 */
	public $conditional_logic;

	/**
	 * The conditional logic instance.
	 *
	 * @var WPCode_Snippet_Cache
	 */
	public $cache;

	/**
	 * The snippet library.
	 *
	 * @var WPCode_Library
	 */
	public $library;

	/**
	 * The Snippet Generator.
	 *
	 * @var WPCode_Generator
	 */
	public $generator;

	/**
	 * The plugin settings.
	 *
	 * @var WPCode_Settings
	 */
	public $settings;

	/**
	 * The plugin importers.
	 *
	 * @var WPCode_Importers
	 */
	public $importers;
	/**
	 * The file cache class.
	 *
	 * @var WPCode_File_Cache
	 */
	public $file_cache;

	/**
	 * The notifications instance (admin-only).
	 *
	 * @var WPCode_Notifications
	 */
	public $notifications;

	/**
	 * Main instance of WPCode.
	 *
	 * @return WPCode
	 * @since 2.0.0
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WPCode ) ) {
			self::$instance = new WPCode();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->setup_constants();
		$this->includes();
		$this->load_components();

		add_action( 'init', array( $this, 'load_plugin_textdomain' ), 15 );
	}

	/**
	 * Set up global constants.
	 *
	 * @return void
	 */
	private function setup_constants() {

		define( 'WPCODE_FILE', __FILE__ );

		$plugin_headers = get_file_data( WPCODE_FILE, array( 'version' => 'Version' ) );

		define( 'WPCODE_VERSION', $plugin_headers['version'] );
		define( 'WPCODE_PLUGIN_BASENAME', plugin_basename( WPCODE_FILE ) );
		define( 'WPCODE_PLUGIN_URL', plugin_dir_url( WPCODE_FILE ) );
		define( 'WPCODE_PLUGIN_PATH', plugin_dir_path( WPCODE_FILE ) );

		$this->version = WPCODE_VERSION;
	}

	/**
	 * Require the files needed for the plugin.
	 *
	 * @return void
	 */
	private function includes() {
		// Load the safe mode logic first.
		require_once WPCODE_PLUGIN_PATH . 'includes/safe-mode.php';
		// Functions for global headers & footers output.
		require_once WPCODE_PLUGIN_PATH . 'includes/global-output.php';
		// Use the old class name for backwards compatibility.
		require_once WPCODE_PLUGIN_PATH . 'includes/legacy.php';
		// Register code snippets post type.
		require_once WPCODE_PLUGIN_PATH . 'includes/post-type.php';
		// The snippet class.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-snippet.php';
		// Auto-insert options.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-auto-insert.php';
		// Execute snippets.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-snippet-execute.php';
		// Handle PHP errors.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-error.php';
		// [wpcode] shortcode.
		require_once WPCODE_PLUGIN_PATH . 'includes/shortcode.php';
		// Conditional logic.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-conditional-logic.php';
		// Snippet Cache.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-snippet-cache.php';
		// Settings class.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-settings.php';
		// Custom capabilities.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-capabilities.php';
		// Install routines.
		require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-install.php';

		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			require_once WPCODE_PLUGIN_PATH . 'includes/icons.php'; // This is not needed in the frontend atm.
			require_once WPCODE_PLUGIN_PATH . 'includes/helpers.php'; // This is not needed in the frontend atm.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/admin-menu.php';
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/admin-scripts.php';
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/admin-ajax-handlers.php';
			// Always used just in the backend.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-generator.php';
			// Snippet Library.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-library.php';
			// Importers.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-importers.php';
			// File cache.
			require_once WPCODE_PLUGIN_PATH . 'includes/class-wpcode-file-cache.php';
			// The docs.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-docs.php';
			// Notifications class.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-notifications.php';
			// Upgrade page.
			require_once WPCODE_PLUGIN_PATH . 'includes/admin/class-wpcode-upgrade-welcome.php';
		}
	}

	/**
	 * Load components in the main plugin instance.
	 *
	 * @return void
	 */
	public function load_components() {
		$this->auto_insert       = new WPCode_Auto_Insert();
		$this->execute           = new WPCode_Snippet_Execute();
		$this->error             = new WPCode_Error();
		$this->conditional_logic = new WPCode_Conditional_Logic();
		$this->cache             = new WPCode_Snippet_Cache();
		$this->settings          = new WPCode_Settings();

		if ( is_admin() || ( defined( 'DOING_CRON' ) && DOING_CRON ) ) {
			$this->file_cache    = new WPCode_File_Cache();
			$this->library       = new WPCode_Library();
			$this->generator     = new WPCode_Generator();
			$this->importers     = new WPCode_Importers();
			$this->notifications = new WPCode_Notifications();
		}
	}

	/**
	 * Load the plugin translations.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		if ( is_user_logged_in() ) {
			unload_textdomain( 'insert-headers-and-footers' );
		}

		load_plugin_textdomain( 'insert-headers-and-footers', false, dirname( plugin_basename( WPCODE_FILE ) ) . '/languages/' );
	}
}

/**
 * Get the main instance of WPCode.
 *
 * @return WPCode
 */
function WPCode() {// phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return WPCode::instance();
}

WPCode();
