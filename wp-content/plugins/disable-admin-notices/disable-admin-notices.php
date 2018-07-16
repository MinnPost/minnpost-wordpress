<?php
	/**
	 * Plugin Name: Webcraftic Disable Admin Notices Individually
	 * Plugin URI: https://wordpress.org/plugins/disable-admin-notices/
	 * Description: Disable admin notices plugin gives you the option to hide updates warnings and inline notices in the admin panel.
	 * Author: Webcraftic <wordpress.webraftic@gmail.com>
	 * Version: 1.0.6
	 * Text Domain: disable-admin-notices
	 * Domain Path: /languages/
	 * Author URI: https://clearfy.pro
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( defined('WDN_PLUGIN_ACTIVE') || (defined('WCL_PLUGIN_ACTIVE') && !defined('LOADING_DISABLE_ADMIN_NOTICES_AS_ADDON')) ) {
		function wbcr_dan_admin_notice_error()
		{
			?>
			<div class="notice notice-error">
				<p><?php _e('We found that you have the "Clearfy - disable unused features" plugin installed, this plugin already has disable comments functions, so you can deactivate plugin "Disable admin notices"!'); ?></p>
			</div>
		<?php
		}

		add_action('admin_notices', 'wbcr_dan_admin_notice_error');

		return;
	} else {

		define('WDN_PLUGIN_ACTIVE', true);
		define('WDN_PLUGIN_DIR', dirname(__FILE__));
		define('WDN_PLUGIN_BASE', plugin_basename(__FILE__));
		define('WDN_PLUGIN_URL', plugins_url(null, __FILE__));

		
		
		if( !defined('LOADING_DISABLE_ADMIN_NOTICES_AS_ADDON') ) {
			require_once(WDN_PLUGIN_DIR . '/libs/factory/core/boot.php');
		}

		require_once(WDN_PLUGIN_DIR . '/includes/class.plugin.php');

		if( !defined('LOADING_DISABLE_ADMIN_NOTICES_AS_ADDON') ) {

			new WDN_Plugin(__FILE__, array(
				'prefix' => 'wbcr_dan_',
				'plugin_name' => 'wbcr_dan',
				'plugin_title' => __('Webcraftic disable admin notices', 'disable-admin-notices'),
				'plugin_version' => '1.0.6',
				'required_php_version' => '5.2',
				'required_wp_version' => '4.2',
				'plugin_build' => 'free',
				'updates' => WDN_PLUGIN_DIR . '/updates/'
			));
		}
	}