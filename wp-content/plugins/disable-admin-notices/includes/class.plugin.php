<?php
	/**
	 * Hide my wp core class
	 * @author Webcraftic <wordpress.webraftic@gmail.com>
	 * @copyright (c) 19.02.2018, Webcraftic
	 * @version 1.0
	 */

	// Exit if accessed directly
	if( !defined('ABSPATH') ) {
		exit;
	}

	if( !class_exists('WDN_Plugin') ) {
		
		if( !class_exists('WDN_PluginFactory') ) {
			if( defined('LOADING_DISABLE_ADMIN_NOTICES_AS_ADDON') ) {
				class WDN_PluginFactory {
					
				}
			} else {
				class WDN_PluginFactory extends Wbcr_Factory400_Plugin {
					
				}
			}
		}
		
		class WDN_Plugin extends WDN_PluginFactory {
			
			/**
			 * @var Wbcr_Factory400_Plugin
			 */
			private static $app;
			
			/**
			 * @var bool
			 */
			private $as_addon;
			
			/**
			 * @param string $plugin_path
			 * @param array $data
			 * @throws Exception
			 */
			public function __construct($plugin_path, $data)
			{
				$this->as_addon = isset($data['as_addon']);
				
				if( $this->as_addon ) {
					$plugin_parent = isset($data['plugin_parent'])
						? $data['plugin_parent']
						: null;
					
					if( !($plugin_parent instanceof Wbcr_Factory400_Plugin) ) {
						throw new Exception('An invalid instance of the class was passed.');
					}
					
					self::$app = $plugin_parent;
				} else {
					self::$app = $this;
				}
				
				if( !$this->as_addon ) {
					parent::__construct($plugin_path, $data);
				}

				$this->setTextDomain();
				$this->setModules();
				
				$this->globalScripts();
				
				if( is_admin() ) {
					$this->adminScripts();
				}
			}
			
			/**
			 * @return Wbcr_Factory400_Plugin
			 */
			public static function app()
			{
				return self::$app;
			}

			protected function setTextDomain()
			{
				// Localization plugin
				load_plugin_textdomain('disable-admin-notices', false, dirname(WDN_PLUGIN_BASE) . '/languages/');
			}
			
			protected function setModules()
			{
				if( !$this->as_addon ) {
					self::app()->load(array(
						array('libs/factory/bootstrap', 'factory_bootstrap_400', 'admin'),
						array('libs/factory/forms', 'factory_forms_400', 'admin'),
						array('libs/factory/pages', 'factory_pages_401', 'admin'),
						array('libs/factory/clearfy', 'factory_clearfy_200', 'all')
					));
				}
			}
			
			private function registerPages()
			{
				if( $this->as_addon ) {
					return;
				}
				self::app()->registerPage('WDN_NoticesPage', WDN_PLUGIN_DIR . '/admin/pages/notices.php');
				self::app()->registerPage('WDN_MoreFeaturesPage', WDN_PLUGIN_DIR . '/admin/pages/more-features.php');
			}
			
			private function adminScripts()
			{
				require(WDN_PLUGIN_DIR . '/admin/options.php');

				if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_dan_hide_notices' ) {
					require(WDN_PLUGIN_DIR . '/admin/ajax/hide-notice.php');
				}

				if( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['action']) && $_REQUEST['action'] == 'wbcr_dan_restore_notice' ) {
					require(WDN_PLUGIN_DIR . '/admin/ajax/restore-notice.php');
				}

				require(WDN_PLUGIN_DIR . '/admin/boot.php');

				$this->registerPages();
			}
			
			private function globalScripts()
			{
				require(WDN_PLUGIN_DIR . '/includes/classes/class.configurate-notices.php');
				new WDN_ConfigHideNotices(self::$app);
			}
		}
	}