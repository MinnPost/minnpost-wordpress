<?php

/**
 * Copyright (c) 2015 Khang Minh <contact@betterwp.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU GENERAL PUBLIC LICENSE VERSION 3.0 OR LATER
 */

abstract class BWP_Framework_V3
{
	/**
	 * Hold plugin options
	 *
	 * This should return the most up-to-date options, even after a form submit
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Hold plugin default options
	 *
	 * @var array
	 */
	public $options_default = array();

	/**
	 * Hold plugin site options (applied to whole site)
	 *
	 * @var array
	 */
	public $site_options = array();

	/**
	 * Hold plugin current options
	 *
	 * This should be used to get an option before it is modified by a form
	 * submit
	 *
	 * @var array
	 */
	public $current_options = array();

	/**
	 * Hold db option keys
	 */
	public $option_keys = array();

	/**
	 * Hold extra option keys
	 */
	public $extra_option_keys = array();

	/**
	 * Hold option pages
	 */
	public $option_pages = array();

	/**
	 * The current option page instance
	 *
	 * @var BWP_Option_Page_V3
	 */
	public $current_option_page;

	/**
	 * Key to identify plugin
	 */
	public $plugin_key;

	/**
	 * Full key to identify plugin
	 */
	public $plugin_full_key;

	/**
	 * Constant Key to identify plugin
	 */
	public $plugin_ckey;

	/**
	 * Domain Key to identify plugin
	 */
	public $plugin_dkey;

	/**
	 * Title of the plugin
	 */
	public $plugin_title;

	/**
	 * Homepage of the plugin
	 */
	public $plugin_url;

	/**
	 * Urls to various parts of homepage or other places
	 *
	 * Expect to have a format of array('relative' => bool, 'url' => url)
	 */
	public $urls = array();

	/**
	 * Plugin file
	 */
	public $plugin_file;

	/**
	 * Plugin folder
	 */
	public $plugin_folder;

	/**
	 * Plugin WP url
	 */
	public $plugin_wp_url;

	/**
	 * Version of the plugin
	 */
	public $plugin_ver = '';

	/**
	 * Message shown to user (Warning, Notes, etc.)
	 */
	public $notices = array();
	public $notice_shown = false;

	/**
	 * Error shown to user
	 */
	public $errors = array();
	public $error_shown = false;

	/**
	 * Capabilities to manage this plugin
	 */
	public $plugin_cap = 'manage_options';

	/**
	 * Whether or not to create filter for media paths
	 */
	public $need_media_filters;

	/**
	 * Form tabs to build
	 */
	public $form_tabs = array();

	/**
	 * Version constraints
	 */
	public $wp_ver;
	public $php_ver;

	/**
	 * Number of framework revisions
	 */
	public $revision = 166;

	/**
	 * Text domain
	 */
	public $domain = '';

	/**
	 * Other special variables
	 */
	protected $_menu_under_settings = false;
	protected $_simple_menu = false;

	/**
	 * The bridge to WP
	 *
	 * @var BWP_WP_Bridge
	 * @since rev 145
	 */
	protected $bridge;

	/**
	 * Cache for plugins
	 *
	 * @var BWP_Cache
	 * @since rev 158
	 */
	protected $cache;

	/**
	 * Combined assets used for production environment
	 *
	 * @var array
	 * @since rev 160
	 */
	protected $combined_assets = array();

	/**
	 * Construct a new plugin with appropriate meta
	 *
	 * @param array $meta
	 * @param BWP_WP_Bridge $bridge optional, default to null
	 * @since rev 142
	 */
	public function __construct(
		array $meta,
		BWP_WP_Bridge $bridge = null,
		BWP_Cache $cache = null)
	{
		$required = array(
			'title', 'version', 'domain'
		);

		foreach ($required as $required_meta)
		{
			if (!array_key_exists($required_meta, $meta))
			{
				throw new InvalidArgumentException(sprintf('Missing required meta (%s) to construct plugin', $required_meta));
			}
		}

		$this->plugin_title = $meta['title'];

		$this->set_version(isset($meta['php_version']) ? $meta['php_version'] : BWP_Version::$php_ver, 'php');
		$this->set_version(isset($meta['wp_version']) ? $meta['wp_version'] : BWP_Version::$wp_ver, 'wp');
		$this->set_version($meta['version']);

		$this->domain = $meta['domain'];

		$this->bridge = $bridge ? $bridge : new BWP_WP_Bridge();
		$this->cache  = $cache  ? $cache  : new BWP_Cache($this->bridge, $this->plugin_key);
	}

	/**
	 * Build base properties
	 */
	protected function build_properties($key, array $options, $plugin_file = '', $plugin_url = '', $need_media_filters = true)
	{
		$this->plugin_key  = strtolower($key);
		$this->plugin_ckey = strtoupper($key);
		$this->plugin_url  = $plugin_url;

		// @since rev 151 we add another property called plugin_full_key that
		// defaults to plugin_key (with underscores replaced with hyphens) but
		// can be used to construct other urls to plugin
		$this->plugin_full_key = str_replace('_', '-', $this->plugin_key);

		// @since rev 146 we allow filtering the default options when the
		// plugin is init
		$this->options_default = array_merge($options, $this->bridge->apply_filters($this->plugin_key . '_default_options', array()));

		$this->need_media_filters = (boolean) $need_media_filters;

		$this->plugin_file = $plugin_file;
		$this->plugin_folder = basename(dirname($plugin_file));

		$this->pre_init_actions();
		$this->init_actions();

		// Load locale
		$this->bridge->load_plugin_textdomain($this->domain, false, $this->plugin_folder . '/languages');
	}

	protected function add_option_key($key, $option, $title)
	{
		$this->option_keys[$key] = $option;
		$this->option_pages[$key] = $title;
	}

	protected function add_extra_option_key($key, $option, $title)
	{
		$this->extra_option_keys[$key] = $option;
		$this->option_pages[$key] = $title;
	}

	protected function get_dashicon($name, $fallback_text = '')
	{
		return $this->get_current_wp_version('3.8')
			? '<span class="dashicons dashicons-' . $name . '"></span>'
			: $fallback_text;
	}

	public function add_icon()
	{
		return '<div class="icon32" id="icon-bwp-plugin" '
			. 'style=\'background-image: url("'
			. constant($this->plugin_ckey . '_IMAGES')
			. '/icon_menu_32.png");\'><br></div>'  . "\n";
	}

	protected function set_version($ver = '', $type = '')
	{
		switch ($type)
		{
			case '': $this->plugin_ver = $ver;
			break;
			case 'php': $this->php_ver = $ver;
			break;
			case 'wp': $this->wp_ver = $ver;
			break;
		}
	}

	public function get_version($type = '')
	{
		switch ($type)
		{
			case '': return $this->plugin_ver;
			break;
			case 'php': return $this->php_ver;
			break;
			case 'wp': return $this->wp_ver;
			break;
		}
	}

	/**
	 * Get or check if the current WP version is greater than a specified version
	 *
	 * @param string $version optional
	 */
	public function get_current_php_version($version = null)
	{
		return BWP_Version::get_current_php_version($version);
	}

	/**
	 * Get or check if the current WP version is greater than a specified version
	 *
	 * @param string $version optional
	 */
	public function get_current_wp_version($version = null)
	{
		$wp_version = $this->bridge->get_bloginfo('version');

		if ($version) {
			return version_compare($wp_version, $version, '>=');
		}

		return $wp_version;
	}

	protected function check_required_versions()
	{
		if (!$this->get_current_php_version($this->php_ver)
			|| !$this->get_current_wp_version($this->wp_ver)
		) {
			// show a warning in admin dashboard if either the required PHP
			// version or the required WP version is not met, and return false
			// to tell plugin to not init at all
			$this->bridge->add_action('admin_notices', array($this, 'warn_required_versions'));
			$this->bridge->add_action('network_admin_notices', array($this, 'warn_required_versions'));
			return false;
		}
		else
			return true;
	}

	public function warn_required_versions()
	{
		BWP_Version::warn_required_versions($this->plugin_title, $this->domain, $this->php_ver, $this->wp_ver);
	}

	public function show_header()
	{
?>
<div id="bwp-header">
	<h1 id="bwp-plugin-title"><?php esc_html_e($this->plugin_title); ?> (<?php echo $this->plugin_ver; ?>)</h1>

	<div id="bwp-plugin-info">
		<a class="button-secondary bwp-button" target="_blank"
			href="<?php echo $this->plugin_url . 'faq/?utm_source=' . $this->plugin_full_key . '&utm_campaign=header-2016&utm_medium=button'; ?>"
			title="<?php _e('Read this first before asking any question!', $this->domain) ?>"
			><span class="dashicons dashicons-editor-help"></span> <?php _e('FAQ', $this->domain); ?></a> &nbsp;
		<a class="button-secondary bwp-button" target="_blank"
			href="https://wordpress.org/support/plugin/<?php echo $this->domain; ?>"
			title="<?php _e('Got a problem with this plugin? Please say it out loud!', $this->domain) ?>"
			><span class="dashicons dashicons-sos"></span> <?php _e('Plugin Support', $this->domain); ?></a> &nbsp;
		<a class="button-secondary bwp-button" target="_blank"
			href="https://wordpress.org/support/view/plugin-reviews/<?php echo $this->domain; ?>?filter=5"
			title="<?php _e('Rate this plugin 5 stars if you like it, thank you!', $this->domain) ?>"
			><span class="dashicons dashicons-star-filled"></span> <?php _e('Rate this plugin 5 stars!', $this->domain); ?></a> &nbsp;

		<button class="bwp-button-paypal bwp-button button-secondary bwp-popover-switch"
			data-content-id="bwp-donation"
			data-placement="auto right"
			data-popover-class="bwp-popover-sm"
			type="button">
			<span class="via"><span class="dashicons dashicons-thumbs-up"></span> <?php _e('Send', $this->domain); ?></span>
			<span class="paypal-pal"><?php _e('Coffees', $this->domain); ?></span>!
		</button>

		<div id="bwp-donation">
			<?php _e('You can buy me some special coffees if you appreciate my work, thank you!', $this->domain); ?>
			<form class="paypal-form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<p style="margin-bottom: 3px;">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="NWBB8JUDW5VSY">
				<input type="hidden" name="lc" value="VN">
				<input type="hidden" name="button_subtype" value="services">
				<input type="hidden" name="no_note" value="0">
				<input type="hidden" name="cn" value="Would you like to say anything to me?">
				<input type="hidden" name="no_shipping" value="1">
				<input type="hidden" name="rm" value="1">
				<!-- <input type="hidden" name="return" value="http://betterwp.net"> -->
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="bn" value="PP-BuyNowBF:icon-paypal.gif:NonHosted">
				<input type="hidden" name="item_name" value="<?php printf('Donate to %s', $this->plugin_title); ?>" />
				<select name="amount">
					<option value="5.00"><?php _e('One cup $5.00', $this->domain); ?></option>
					<option value="10.00"><?php _e('Two cups $10.00', $this->domain); ?></option>
					<option value="25.00"><?php _e('Five cups! $25.00', $this->domain); ?></option>
					<option value="50.00"><?php _e('One LL-cup!!! $50.00', $this->domain); ?></option>
					<option value="100.00"><?php _e('... or any amount!', $this->domain); ?></option>
				</select>
				<span class="paypal-alternate-input" style="display: none;"><!-- --></span>
				&nbsp;
				<button class="bwp-button-paypal button-secondary" type="submit" name="submit">
					<span class="paypal-via"><?php _e('Via', $this->domain); ?></span>
					<span class="paypal-pay">Pay</span><span class="paypal-pal">Pal</span>
				</button>
				<!--<input class="paypal-submit" type="image" src="<?php echo $this->plugin_wp_url . 'vendor/kminh/bwp-framework/assets/option-page/images/icon-paypal.gif'; ?>" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />-->
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
			</p>
			</form>
		</div>
	</div>
<?php
		$donation_showable = $this->bridge->apply_filters('bwp_donation_showable', true);
		if (true == $donation_showable || self::is_multisite_admin())
		{
?>
	<div id="bwp-get-social" class="postbox">
		<h2 class="hndle"><span><?php _e('Share the love for this plugin!', $this->domain); ?></span></h2>
		<div class="inside">
			<div id="bwp-social-buttons" class="clearfix">
				<!-- Twitter buttons -->
				<div class="bwp-twitter-buttons">
					<a href="https://twitter.com/share"
						class="twitter-share-button"
						data-url="<?php echo $this->plugin_url ?>"
						data-text="<?php _e('Check out this cool plugin', $this->domain); ?> <?php echo $this->plugin_title; ?>"
						data-via="0dd0ne0ut"
						data-hashtags="bwp"
						data-dnt="true">Tweet</a>
					<a href="https://twitter.com/0dd0ne0ut"
						class="twitter-follow-button"
						data-show-screen-name="false"
						data-show-count="true"
						data-dnt="true">Follow Me!</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				</div>

				<!-- Facebook button -->
				<div class="bwp-fb-buttons">
					<div class="fb-like"
						data-href="<?php echo $this->plugin_url; ?>"
						data-layout="button_count"
						data-action="like"
						data-share="false"></div>
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) return;
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v2.5";
						fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));</script>
				</div>

				<!-- Google plus -->
				<div class="bwp-gplus-buttons">
					<div class="g-plusone" data-size="medium" data-href="<?php echo $this->plugin_url; ?>"></div>
					<script src="https://apis.google.com/js/platform.js" async defer></script>
				</div>
			</div>
		</div>
	</div>
<?php
		}
?>
</div>
<?php
	}

	public function show_sidebar_right()
	{
		$sidebar_showable = $this->bridge->apply_filters('bwp_info_showable', true); // @deprecated rev 164
		$sidebar_showable = $this->bridge->apply_filters('bwp_sidebar_showable', $sidebar_showable); // @since rev 164
		$feed_showable    = $this->bridge->apply_filters('bwp_feed_showable', true); // @since rev 164
		$ad_showable      = $this->bridge->apply_filters('bwp_ad_showable', true);

		$heading_level = $this->get_current_wp_version('4.4') ? 'h2' : 'h3';

		if (true == $sidebar_showable || self::is_multisite_admin())
		{
?>
<div id="bwp-sidebar-right">
<?php
			if (true == $ad_showable)
			{
?>

	<div id="bwp-ads" class="postbox">
		<<?php echo $heading_level; ?> class="hndle">
			<span><?php _e('Need a highly customizable theme?', $this->domain); ?></span>
		</<?php echo $heading_level; ?>>
		<div class="inside">
			<div style="width: 250px; margin: 0 auto;">
				<a href="http://bit.ly/bwp-optimizer-012016"
					target="_blank"><img src="<?php echo $this->plugin_wp_url . 'vendor/kminh/bwp-framework/assets/option-page/images/ad_lt_250x250.png'; ?>"
				/></a>
			</div>
		</div>
	</div>
<?php
			}

			if (true == $feed_showable)
			{
?>
	<div id="bwp-gems" class="postbox" data-plugin-key="<?php echo $this->plugin_full_key; ?>">
		<<?php echo $heading_level; ?> class="hndle">
			<span><?php _e('BWP Gems', $this->domain); ?></span>
		</<?php echo $heading_level; ?>>
		<div class="inside">
			<em class="bwp-loader"><?php _e('loading...', $this->domain); ?></em>
			<ul class="bwp-feed"><!-- --></ul>
		</div>
	</div>

	<div id="bwp-news" class="postbox" data-plugin-key="<?php echo $this->plugin_full_key; ?>">
		<<?php echo $heading_level; ?> class="hndle">
			<span><?php _e('BWP News', $this->domain); ?></span>
		</<?php echo $heading_level; ?>>
		<div class="inside">
			<em class="bwp-loader"><?php _e('loading...', $this->domain); ?></em>
			<ul class="bwp-feed"><!-- --></ul>
		</div>

		<div class="bwp-feed-buttons">
			<a class="button-secondary bwp-button bwp-button-rss" href="http://feeds.feedburner.com/BetterWPnet">
				<span class="dashicons dashicons-rss"></span>
				<?php _e('Subscribe', $this->domain); ?>
			</a>

			&nbsp;

			<a class="button-secondary bwp-button bwp-button-twitter" href="http://twitter.com/0dd0ne0ut">
				<span class="dashicons dashicons-twitter"></span>
				<?php _e('Follow', $this->domain); ?>
			</a>
		</div>
	</div>
<?php
			}
?>
</div>
<?php
		}
	}

	public function show_version()
	{
		if (empty($this->plugin_ver)) return '';

		return '<a class="nav-tab version" title="'
			. sprintf(esc_attr(__('You are using version %s!', $this->domain)), $this->plugin_ver)
			. '">' . $this->plugin_ver . '</a>';
	}

	protected function pre_init_actions()
	{
		$pre_init_priority = $this->bridge->apply_filters($this->plugin_key . '_pre_init_priority', 10);

		// @since rev 166 we start the early init process after all plugins
		// are loadded, this is to make sure that:
		// 1. Other plugins can hook to BWP hooks and can compete with BWP
		//    plugins when using built-in WordPress hooks as well.
		// 2. All pluggable functions are available and "plugged" when needed.
		$this->bridge->add_action('plugins_loaded', array($this, 'pre_init'), $pre_init_priority);
	}

	protected function init_actions()
	{
		// @since rev 140, sometimes we need to hook to the 'init' action with
		// a specific priority
		$init_priority = $this->bridge->apply_filters($this->plugin_key . '_init_priority', 10);

		$this->bridge->add_action('init', array($this, 'build_wp_properties'), $init_priority);
		$this->bridge->add_action('init', array($this, 'init'), $init_priority);

		// register backend hooks
		$this->bridge->add_action('admin_init', array($this, 'init_admin_page'), 1);
		$this->bridge->add_action('admin_menu', array($this, 'init_admin_menu'), 1);
	}

	public function build_wp_properties()
	{
		// set the plugin WP url here so it can be filtered
		if (defined('BWP_USE_SYMLINKS'))
			// make use of symlinks on development environment
			$this->plugin_wp_url = $this->bridge->trailingslashit($this->bridge->plugins_url($this->plugin_folder));
		else
			// this should allow other package to include BWP plugins while
			// retaining correct URLs pointing to assets
			$this->plugin_wp_url = $this->bridge->trailingslashit($this->bridge->plugin_dir_url($this->plugin_file));
	}

	public function pre_init()
	{
		$this->pre_init_build_constants();
		$this->pre_init_build_options();
		$this->pre_init_update_plugin();
		$this->pre_init_properties();
		$this->load_libraries();
		$this->pre_init_hooks();

		// support installation and uninstallation
		$this->bridge->register_activation_hook($this->plugin_file, array($this, 'install'));
		$this->bridge->register_deactivation_hook($this->plugin_file, array($this, 'uninstall'));
	}

	public function init()
	{
		$this->bridge->do_action($this->plugin_key . '_pre_init');

		$this->build_constants();
		$this->init_update_plugin();
		$this->init_build_options();
		$this->init_shared_properties();
		$this->init_properties();
		$this->init_hooks();
		$this->register_framework_media();
		$this->enqueue_media();

		if ($this->is_admin_page())
		{
			// @since rev 164 enqueue common javascript when inside an admin
			// page by default
			$this->bridge->wp_enqueue_script('bwp-op');

			// @since rev 164 split the sidebar
			$this->bridge->add_action('bwp_option_action_before_main', array($this, 'show_sidebar_right'), 12);
			$this->bridge->add_action('bwp_option_action_before_tabs', array($this, 'show_header'), 12);
		}

		$this->bridge->do_action($this->plugin_key . '_loaded');
	}

	protected function pre_init_build_constants()
	{
		// only build constants once
		if (defined($this->plugin_ckey . '_PLUGIN_DIR'))
			return;

		// absolute path to plugin folders
		define($this->plugin_ckey . '_PLUGIN_DIR', dirname($this->plugin_file));
		define($this->plugin_ckey . '_PLUGIN_SRC', dirname($this->plugin_file) . '/src');
		// url to plugin bwp website
		define($this->plugin_ckey . '_PLUGIN_URL', $this->plugin_url);
		// the capability needed to configure this plugin
		define($this->plugin_ckey . '_CAPABILITY', $this->plugin_cap);

		// define registered option keys, to be used when building option pages
		// and build options
		foreach ($this->option_keys as $key => $option)
		{
			define(strtoupper($key), $option);
		}
		foreach ($this->extra_option_keys as $key => $option)
		{
			define(strtoupper($key), $option);
		}
	}

	protected function build_constants()
	{
		// only build constants once
		if (defined($this->plugin_ckey . '_IMAGES'))
			return;

		// these constants are only available once plugin_wp_url is available
		if (true == $this->need_media_filters)
		{
			define($this->plugin_ckey . '_IMAGES',
				$this->bridge->apply_filters($this->plugin_key . '_image_path',
				$this->plugin_wp_url . 'assets/images'));
			define($this->plugin_ckey . '_CSS',
				$this->bridge->apply_filters($this->plugin_key . '_css_path',
				$this->plugin_wp_url . 'assets/css'));
			define($this->plugin_ckey . '_JS',
				$this->bridge->apply_filters($this->plugin_key . '_js_path',
				$this->plugin_wp_url . 'assets/js'));
			define($this->plugin_ckey . '_DIST',
				$this->bridge->apply_filters($this->plugin_key . '_dist_path',
				$this->plugin_wp_url . 'assets/dist'));
		}
		else
		{
			define($this->plugin_ckey . '_IMAGES', $this->plugin_wp_url . 'assets/images');
			define($this->plugin_ckey . '_CSS', $this->plugin_wp_url . 'assets/css');
			define($this->plugin_ckey . '_JS', $this->plugin_wp_url . 'assets/js');
			define($this->plugin_ckey . '_DIST', $this->plugin_wp_url . 'assets/dist');
		}
	}

	protected function pre_init_build_options()
	{
		$this->build_options();
	}

	protected function init_build_options()
	{
		// make sure at this stage current options and options are the same
		$this->current_options = $this->options;
	}

	protected function build_options()
	{
		// Get all options and merge them
		$options = $this->options_default;

		foreach ($this->option_keys as $option)
		{
			$db_options = $this->bridge->get_option($option);
			$db_options = self::normalize_options($db_options);

			// check for obsolete keys and remove them from db
			if ($obsolete_keys = array_diff_key($db_options, $this->options_default))
			{
				foreach ($obsolete_keys as $obsolete_key => $value) {
					unset($db_options[$obsolete_key]);
				}

				// commit the removal
				$this->bridge->update_option($option, $db_options);
			}

			$options = array_merge($options, $db_options);

			// also check for global options if in Multi-site
			if (self::is_multisite())
			{
				$db_options = $this->bridge->get_site_option($option);
				$db_options = self::normalize_options($db_options);
				$site_option_need_update = false;

				// merge site options into the options array, overwrite
				// any options with same keys
				$temp = array();
				foreach ($db_options as $k => $o)
				{
					if (in_array($k, $this->site_options))
					{
						$temp[$k] = $o;
					}
					else
					{
						// remove obsolete options
						$site_option_need_update = true;
						unset($db_options[$k]);
					}
				}

				$options = array_merge($options, $temp);

				if ($site_option_need_update)
					$this->bridge->update_site_option($option, $temp);
			}
		}

		$this->options         = $options;
		$this->current_options = $options;
	}

	/**
	 * Update options with a specific key
	 *
	 * This should update $options but not $current_options. If there are site
	 * options in $options, they should use the default options.
	 *
	 * @param string $option_key
	 * @param array $options all options under the option key
	 */
	public function update_options($option_key, array $options)
	{
		$this->bridge->update_option($option_key, $options);

		// update $options property, but don't update site options as they
		// should be handled by update_site_options()
		if (self::is_multisite())
		{
			foreach ($options as $name => $value)
			{
				if (in_array($name, $this->site_options))
					unset($options[$name]);
			}
		}

		$this->options = array_merge($this->options, $options);
	}

	/**
	 * Update site options with a specific key, when allowed to
	 *
	 * This should pick site options found in $options and update accordingly.
	 * This should also update $options property.
	 *
	 * @param string $option_key
	 * @param array $options all options under the option key
	 */
	public function update_site_options($option_key, array $options)
	{
		if (!self::is_multisite_admin() || !self::is_on_main_blog())
			return;

		$site_options = array();

		foreach ($this->site_options as $site_option_name)
		{
			if (array_key_exists($site_option_name, $options))
				$site_options[$site_option_name] = $options[$site_option_name];
		}

		// update site options only if there are options to update
		if (count($site_options) > 0)
		{
			$this->bridge->update_site_option($option_key, $site_options);
			$this->options = array_merge($this->options, $site_options);
		}
	}

	/**
	 * Update some options under a specific key
	 *
	 * This will update per blog options and site options. This will also
	 * accept options that have not been persisted in db.
	 *
	 * @param string $option_key
	 * @param array $new_options only the new options that need updating
	 */
	public function update_some_options($option_key, array $new_options)
	{
		$db_options = $this->bridge->get_option($option_key);

		$db_options = !$db_options || !is_array($db_options)
			? array() : $db_options;

		$db_options = array_merge($db_options, $new_options);

		$this->update_options($option_key, $db_options);
		$this->update_site_options($option_key, $db_options);
	}

	/**
	 * @deprecated rev152
	 */
	protected function update_plugin_options($option_key, array $new_options)
	{
		$this->update_some_options($option_key, $new_options);
	}

	/**
	 * Get current options by their keys
	 *
	 * @param array $option_keys
	 */
	public function get_options_by_keys(array $option_keys)
	{
		$options = array();

		foreach ($option_keys as $key) {
			if (array_key_exists($key, $this->options)) {
				$options[$key] = $this->options[$key];
			}
		}

		return $options;
	}

	protected function pre_init_properties()
	{
		/* intentionally left blank */
	}

	/**
	 * Init properties that are shared across different plugins
	 */
	protected function init_shared_properties()
	{
		$this->cache->set('timezone', $this->get_current_timezone(), true);
	}

	protected function init_properties()
	{
		/* intentionally left blank */
	}

	protected function load_libraries()
	{
		/* intentionally left blank */
	}

	protected function update_plugin($when = '')
	{
		if (!$this->bridge->is_admin())
			return;

		$current_version = $this->plugin_ver;
		$db_version = $this->bridge->get_option($this->plugin_key . '_version');

		if (!$db_version || version_compare($db_version, $current_version, '<'))
		{
			if ('pre_init' == $when)
			{
				$action_hook = $this->plugin_key . '_upgrade';
				$this->upgrade_plugin($db_version, $current_version);
			}
			else
			{
				$action_hook = $this->plugin_key . '_init_upgrade';
				$this->init_upgrade_plugin($db_version, $current_version);
			}

			// fire an action when plugin updates itself
			$this->bridge->do_action($action_hook, $db_version, $current_version);

			// only mark as upgraded when this is init update
			if ('init' == $when)
				$this->bridge->update_option($this->plugin_key . '_version', $current_version);
		}
	}

	protected function pre_init_update_plugin()
	{
		$this->update_plugin('pre_init');
	}

	protected function init_update_plugin()
	{
		$this->update_plugin('init');
	}

	protected function pre_init_hooks()
	{
		/* intentionally left blank */
	}

	protected function init_hooks()
	{
		/* intentionally left blank */
	}

	/**
	 * Get correct media src based on environment
	 *
	 * @param string $handle      media handle
	 * @param string $dev_src     the source used in development environment
	 * @param string $prod_src    the source used in production environment, this
	 *                            will be used instead of replacing $dev_src's
	 *                            extension when provided
	 * @param string $prod_handle the handle used in production environment
	 *
	 * @return mixed string
	 */
	protected function get_src_by_environment($handle, $dev_src, $prod_src = null, $prod_handle = null)
	{
		if (! BWP_Framework_Util::is_debugging())
		{
			$prod_src = $prod_src
				? $prod_src
				: str_replace(
					array('.js', '.css'),
					array('.min.js', '.min.css'),
					$dev_src
				);

			if ($prod_handle)
			{
				// init combined asset to hold combined dependencies later on
				if (!isset($this->combined_assets[$prod_handle]))
					$this->combined_assets[$prod_handle] = array();

				// combined assets should be added for the handle matching
				// prod_handle only
				if ($handle !== $prod_handle)
					return false;
			}

			return $prod_src;
		}

		return $dev_src;
	}

	/**
	 * Get dependencies based on environment
	 *
	 * @param array $deps current dependencies of the item being checked
	 * @param string $prod_handle the handle used in production environment
	 *
	 * @return array
	 */
	protected function get_deps_by_environment(array $deps, $prod_handle = null)
	{
		if ($prod_handle && ! BWP_Framework_Util::is_debugging())
		{
			// due to a bug in the enqueueing system, we need to merge
			// dependencies for combined assets for the prod_handle because
			// any dependencies attached to other handles that have the same
			// prod_handle will not be taken into account when their sources
			// are set to false
			//
			// @link https://core.trac.wordpress.org/ticket/25247
			$this->combined_assets[$prod_handle] = array_merge(
				$deps, $this->combined_assets[$prod_handle]
			);

			return $this->combined_assets[$prod_handle];
		}

		return $deps;
	}

	/**
	 * Register a BWP media file
	 *
	 * @param string $prod_src the source to use when debug is off
	 * @param string $prod_handle the handle to use when debug is off
	 */
	protected function register_media_file(
		$handle,
		$src,
		array $deps = array(),
		$version = false,
		$prod_src = null,
		$prod_handle = null
	) {
		$method = strpos($src, '.js') !== false ? 'wp_register_script' : 'wp_register_style';
		$group  = strpos($src, '.js') !== false ? true : 'all'; // in footer or 'all' media

		$this->bridge->$method(
			$handle,
			$this->get_src_by_environment($handle, $src, $prod_src, $prod_handle),
			$this->get_deps_by_environment($deps, $prod_handle),
			$version ? $version : $this->plugin_ver,
			$group
		);
	}

	/**
	 * Enqueue a BWP media file
	 *
	 * @param string $prod_src the source to use when debug is off
	 * @param string $prod_handle the handle to use when debug is off
	 */
	protected function enqueue_media_file(
		$handle,
		$src,
		array $deps = array(),
		$version = false,
		$prod_src = null,
		$prod_handle = null
	) {
		$method = strpos($src, '.js') !== false ? 'wp_enqueue_script' : 'wp_enqueue_style';
		$group  = strpos($src, '.js') !== false ? true : 'all'; // in footer or 'all' media

		$this->bridge->$method(
			$handle,
			$this->get_src_by_environment($handle, $src, $prod_src, $prod_handle),
			$this->get_deps_by_environment($deps, $prod_handle),
			$version ? $version : $this->plugin_ver,
			$group
		);
	}

	/**
	 * Register some framework media that plugins can depend on anytime
	 */
	protected function register_framework_media()
	{
		$asset_url = $this->plugin_wp_url . 'vendor/kminh/bwp-framework/assets';

		// select2
		$this->register_media_file('bwp-placeholders',
			$asset_url . '/vendor/placeholders/placeholders.jquery.js',
			array(), $this->revision,
			$asset_url . '/vendor/select2/js/select2.min.js',
			'bwp-select2'
		);
		$this->register_media_file('bwp-select2',
			$asset_url . '/vendor/select2/js/select2.js',
			array('bwp-placeholders'), $this->revision,
			$asset_url . '/vendor/select2/js/select2.min.js',
			'bwp-select2'
		);
		$this->register_media_file('bwp-select2',
			$asset_url . '/vendor/select2/css/select2.css',
			array('wp-admin'), $this->revision
		);

		// datatables
		$this->register_media_file('bwp-datatables',
			$asset_url . '/vendor/datatables/js/jquery.dataTables.js',
			array('jquery'), $this->revision
		);
		$this->register_media_file('bwp-datatables',
			$asset_url . '/vendor/datatables/css/jquery.dataTables.css',
			array(), $this->revision
		);

		// bootstrap
		$this->register_media_file('bwp-bootstrap',
			$asset_url . '/vendor/bootstrap/js/bootstrap.js',
			array('jquery'), $this->revision
		);

		// bootstrap modal
		$this->register_media_file('bwp-bootbox',
			$asset_url . '/vendor/bootbox.js/bootbox.js',
			array('bwp-bootstrap'), $this->revision,
			$asset_url . '/option-page/dist/js/modal.min.js',
			'bwp-op-modal'
		);
		$this->register_media_file('bwp-op-bootbox',
			$asset_url . '/option-page/js/bootbox.js',
			array('jquery', 'bwp-bootbox'), $this->revision,
			$asset_url . '/option-page/dist/js/modal.min.js',
			'bwp-op-modal'
		);
		$this->register_media_file('bwp-op-modal',
			$asset_url . '/option-page/js/modal.js',
			array('bwp-op-bootbox', 'bwp-op-common'),
			$this->revision,
			$asset_url . '/option-page/dist/js/modal.min.js',
			'bwp-op-modal'
		);

		// jquery ui
		$this->register_media_file('bwp-jquery-ui',
			$asset_url . '/option-page/css/jquery-ui/jquery-ui.css',
			array(), $this->revision
		);

		// input mask
		$this->register_media_file('bwp-inputmask',
			$asset_url . '/vendor/inputmask/jquery.inputmask.bundle.js',
			array('jquery'), $this->revision
		);

		// codemirror - css
		$this->register_media_file('bwp-codemirror-base',
			$asset_url . '/vendor/codemirror/codemirror.css',
			array(), $this->revision,
			$asset_url . '/vendor/codemirror/codemirror.min.css',
			'bwp-codemirror'
		);
		$this->register_media_file('bwp-codemirror',
			$asset_url . '/vendor/codemirror/theme/neo.css',
			array('bwp-codemirror-base'), $this->revision,
			$asset_url . '/vendor/codemirror/codemirror.min.css',
			'bwp-codemirror'
		);
		// codemirror - js
		$this->register_media_file('bwp-codemirror',
			$asset_url . '/vendor/codemirror/codemirror.js',
			array(), $this->revision,
			$asset_url . '/option-page/dist/js/code-editor.min.js',
			'bwp-op-codemirror'
		);
		$this->register_media_file('bwp-codemirror-css',
			$asset_url . '/vendor/codemirror/mode/css/css.js',
			array('bwp-codemirror'), $this->revision,
			$asset_url . '/option-page/dist/js/code-editor.min.js',
			'bwp-op-codemirror'
		);
		$this->register_media_file('bwp-codemirror-addon-placeholder',
			$asset_url . '/vendor/codemirror/addon/display/placeholder.js',
			array(), $this->revision,
			$asset_url . '/option-page/dist/js/code-editor.min.js',
			'bwp-op-codemirror'
		);
		$this->register_media_file('bwp-codemirror-addon-active-line',
			$asset_url . '/vendor/codemirror/addon/selection/active-line.js',
			array(), $this->revision,
			$asset_url . '/option-page/dist/js/code-editor.min.js',
			'bwp-op-codemirror'
		);
		$this->register_media_file('bwp-codemirror-htmlmixed',
			$asset_url . '/vendor/codemirror/mode/htmlmixed/htmlmixed.js',
			array('bwp-codemirror'), $this->revision,
			$asset_url . '/option-page/dist/js/code-editor.min.js',
			'bwp-op-codemirror'
		);
		$this->register_media_file('bwp-op-codemirror',
			$asset_url . '/option-page/js/codemirror.js',
			array('bwp-codemirror'),
			$this->revision,
			$asset_url . '/option-page/dist/js/code-editor.min.js',
			'bwp-op-codemirror'
		);

		// bwp common
		$this->register_media_file('bwp-op-common',
			$asset_url . '/option-page/js/common.js',
			array('jquery'), $this->revision,
			$asset_url . '/option-page/dist/js/common.min.js'
		);

		// bwp op
		$this->register_media_file('bwp-anchorjs',
			$asset_url . '/vendor/anchorjs/anchor.js',
			array(), $this->revision,
			$asset_url . '/option-page/dist/js/op.min.js',
			'bwp-op'
		);
		$this->register_media_file('bwp-op-popover',
			$asset_url . '/option-page/js/popover.js',
			array('bwp-bootstrap'), $this->revision,
			$asset_url . '/option-page/dist/js/op.min.js',
			'bwp-op'
		);
		$this->register_media_file('bwp-op-toggle',
			$asset_url . '/option-page/js/toggle.js',
			array('jquery'), $this->revision,
			$asset_url . '/option-page/dist/js/op.min.js',
			'bwp-op'
		);
		$this->register_media_file('bwp-op',
			$asset_url . '/option-page/js/op.js',
			array(
				'bwp-op-common',
				'bwp-anchorjs',
				'bwp-op-popover',
				'bwp-op-toggle'
			),
			$this->revision,
			$asset_url . '/option-page/dist/js/op.min.js',
			'bwp-op'
		);
	}

	protected function enqueue_media()
	{
		/* intentionally left blank */
	}

	public function install()
	{
		/* intentionally left blank */
	}

	public function uninstall()
	{
		/* intentionally left blank */
	}

	/**
	 * @since rev 157
	 */
	public function upgrade_plugin($from, $to)
	{
		/* intentionally left blank */
	}

	/**
	 * @since rev 157
	 */
	public function init_upgrade_plugin($from, $to)
	{
		/* intentionally left blank */
	}

	protected function is_admin_page($page = '')
	{
		if ($this->bridge->is_admin() && !empty($_GET['page'])
			&& (in_array($_GET['page'], $this->option_keys)
				|| in_array($_GET['page'], $this->extra_option_keys))
			&& (empty($page)
				|| (!empty($page) && $page == $_GET['page']))
		) {
			return true;
		}
	}

	protected function get_current_admin_page()
	{
		if ($this->is_admin_page()) {
			return $this->bridge->wp_unslash($_GET['page']);
		}

		return '';
	}

	public function get_admin_page_url($page = '')
	{
		$page = $page ? $page : $this->get_current_admin_page();
		$option_script = !$this->_menu_under_settings && !$this->_simple_menu
			? 'admin.php'
			: 'options-general.php';

		return $this->bridge->add_query_arg(array('page' => $page), admin_url($option_script));
	}

	/**
	 * Redirect internally, only when headers have not been sent
	 *
	 * @param mixed string|null $url default to current admin page
	 */
	public function safe_redirect($url = null)
	{
		// @since rev 153, to avoid errors when WP_DEBUG is turned on and
		// there are errors from other plugins
		if (headers_sent())
			return;

		$this->bridge->wp_safe_redirect($this->get_admin_page_url());

		exit;
	}

	public function plugin_action_links($links, $file)
	{
		$option_keys = array_values($this->option_keys);

		if (false !== strpos($this->bridge->plugin_basename($this->plugin_file), $file))
		{
			$links[] = '<a href="' . $this->get_admin_page_url($option_keys[0]) . '">'
				. __('Settings') . '</a>';
		}

		return $links;
	}

	private function init_session()
	{
		if (!isset($_SESSION) || (function_exists('session_status') && session_status() === PHP_SESSION_NONE))
		{
			// do not init a session if headers are already sent
			if (headers_sent())
				return;

			session_start();
		}
	}

	public function init_admin_page()
	{
		// not an admin page of this plugin, do nothing
		if (!$this->is_admin_page())
			return;

		$this->current_option_page = new BWP_Option_Page_V3(
			$this->get_current_admin_page(), $this
		);

		$this->init_session();
		$this->build_option_page();
		$this->current_option_page->handle_form_actions();

		$notices    = $this->get_flash('notice');
		$errors     = $this->get_flash('error');
		$containers = $this->get_container_flash();

		foreach ($notices as $notice) {
			$this->add_notice($notice);
		}

		foreach ($errors as $error) {
			$this->add_error($error);
		}

		foreach ($containers as $name => $container_data) {
			$this->current_option_page->add_form_container($name, $container_data);
		}
	}

	public function init_admin_menu()
	{
		$this->_menu_under_settings = $this->bridge->apply_filters('bwp_menus_under_settings', false);

		$this->bridge->add_filter('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);

		if ($this->is_admin_page())
		{
			// build tabs
			$this->build_tabs();

			$asset_url = $this->plugin_wp_url . 'vendor/kminh/bwp-framework/assets/option-page';

			// enqueue style sheets and scripts for the option page
			$this->enqueue_media_file('bwp-option-page',
				$asset_url . '/css/style.css',
				self::is_multisite() || class_exists('JCP_UseGoogleLibraries') ? array('wp-admin') : array(),
				$this->revision,
				$asset_url . '/dist/css/op.min.css'
			);

			$this->enqueue_media_file('bwp-paypal-js',
				$asset_url . '/js/paypal.js', array('jquery'), $this->revision,
				$asset_url . '/js/paypal.js'
			);
		}

		$this->build_menus();
	}

	/**
	 * Build the Menus
	 */
	protected function build_menus()
	{
		/* intentionally left blank */
	}

	protected function build_tabs()
	{
		$option_script = !$this->_menu_under_settings
			? 'admin.php'
			: 'options-general.php';

		foreach ($this->option_pages as $key => $page)
		{
			$pagelink = !empty($this->option_keys[$key])
				? $this->option_keys[$key]
				: $this->extra_option_keys[$key];

			$this->form_tabs[$page] = $this->bridge->admin_url($option_script)
				. '?page=' . $pagelink;
		}
	}

	/**
	 * Build the option pages
	 */
	protected function build_option_page()
	{
		/* intentionally left blank */
	}

	public function show_option_page()
	{
		/* filled by plugin */
	}

	/**
	 * Add a flash message that is shown only once
	 *
	 * @since rev 144
	 * @param string $key the key to group this message
	 * @param string $message the message to display
	 * @param bool $append append to the group or replace
	 */
	protected function add_flash($key, $message, $append = true)
	{
		if (!isset($_SESSION))
			return;

		$flash_key = 'bwp_op_flash_' . $key;

		if (!isset($_SESSION[$flash_key]) || !is_array($_SESSION[$flash_key]))
			$_SESSION[$flash_key] = array();

		if ($append)
			$_SESSION[$flash_key][] = $message;
		else
			$_SESSION[$flash_key] = array($message);
	}

	public function add_notice_flash($message, $append = true)
	{
		$this->add_flash('notice', $message, $append);
	}

	public function add_error_flash($message, $append = true)
	{
		$this->add_flash('error', $message, $append);
	}

	/**
	 * Add a flash message that should be put in a form container
	 *
	 * @param string $field the field that owns the container
	 * @param string $message
	 */
	public function add_container_flash($field, $message)
	{
		if (!isset($_SESSION))
			return;

		$flash_key = 'bwp_op_flash_container';

		if (!isset($_SESSION[$flash_key]) || !is_array($_SESSION[$flash_key]))
			$_SESSION[$flash_key] = array();

		$_SESSION[$flash_key][$field] = $message;
	}

	/**
	 * Get all flash messages that share a key
	 *
	 * @since rev 144
	 * @return array
	 */
	protected function get_flash($key)
	{
		$flash_key = 'bwp_op_flash_' . $key;

		if (!isset($_SESSION[$flash_key]))
		{
			$flashes = array();
		}
		else
		{
			$flashes =  (array) $_SESSION[$flash_key];
			unset($_SESSION[$flash_key]);
		}

		return $flashes;
	}

	protected function get_container_flash()
	{
		$flash_key = 'bwp_op_flash_container';

		if (!isset($_SESSION[$flash_key]))
		{
			$flashes = array();
		}
		else
		{
			$flashes = (array) $_SESSION[$flash_key];
			unset($_SESSION[$flash_key]);
		}

		return $flashes;
	}

	protected function show_dismiss_button()
	{
		if ($this->get_current_wp_version('4.2')) :
?>
<button class="notice-dismiss" type="button">
	<span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span>
</button>
<?php
		endif;
	}

	public function add_notice($notice)
	{
		if (!in_array($notice, $this->notices))
		{
			$this->notices[] = $notice;
			$this->bridge->add_action('bwp_option_action_before_form', array($this, 'show_notices'));
		}
	}

	public function show_notices()
	{
		if (false == $this->notice_shown)
		{
			foreach ($this->notices as $notice)
			{
				$first = !isset($first) ? ' first ' : '';
?>
<div class="updated notice is-dismissible below-h2<?php echo $first ?>">
	<p><?php echo $notice; ?></p>
	<?php $this->show_dismiss_button(); ?>
</div>
<?php
			}

			$this->notice_shown = true;
		}
	}

	public function add_error($error)
	{
		if (!in_array($error, $this->errors))
		{
			$this->errors[] = $error;
			$this->bridge->add_action('bwp_option_action_before_form', array($this, 'show_errors'));
		}
	}

	public function has_error()
	{
		return count($this->errors) > 0;
	}

	public function show_errors()
	{
		if (false == $this->error_shown)
		{
			foreach ($this->errors as $error)
			{
				$first = !isset($first) ? ' first ' : '';
?>
<div class="error notice is-dismissible below-h2<?php echo $first ?>">
	<p><?php echo $error; ?></p>
	<?php $this->show_dismiss_button(); ?>
</div>
<?php
			}

			$this->error_shown = true;
		}
	}

	public function add_url($key, $url, $relative = true)
	{
		$this->urls[$key] = array(
			'relative' => $relative,
			'url' => $url
		);
	}

	public function get_url($key)
	{
		if (isset($this->urls[$key]))
		{
			$url = $this->urls[$key];
			if ($url['relative'])
				return $this->bridge->trailingslashit($this->plugin_url) . $url['url'];

			return $url['url'];
		}

		return '';
	}

	/**
	 * @return BWP_WP_Bridge
	 */
	public function get_bridge()
	{
		return $this->bridge;
	}

	/**
	 * @return BWP_Cache
	 */
	public function get_cache()
	{
		return $this->cache;
	}

	/**
	 * Get current timezone set by user
	 *
	 * @return DateTimeZone
	 * @since rev 157
	 */
	public function get_current_timezone()
	{
		if ($timezone = $this->cache->get('timezone', true))
			return $timezone;

		// use timezone_string if set
		if ($timezone_string = $this->bridge->get_option('timezone_string'))
		{
			try {
				return new DateTimeZone($timezone_string);
			} catch (Exception $e) {
				// continue finding the timezone
			}
		}

		$timezone_offset = (float) $this->bridge->get_option('gmt_offset');

		// before PHP 5.2 it's impossible to get timezone from offset, return
		// UTC here if PHP version not > 5.2.0, or there's no offset set
		// @todophp remove this when dropping support for PHP < 5.3.2
		if (!$this->get_current_php_version('5.2.0') || empty($timezone_offset))
			return new DateTimeZone('UTC');

		// create DateTimeZone from offset converted to hours in minute format
		$timezone_offset = (int) (3600 * $timezone_offset);
		$timezones = DateTimeZone::listAbbreviations();

		foreach ($timezones as $timezone)
		{
			foreach ($timezone as $city)
			{
				if ($city['offset'] === $timezone_offset)
				{
					try {
						return new DateTimeZone($city['timezone_id']);
					} catch (Exception $e) {
						// failed, return UTC
						return new DateTimeZone('UTC');
					}
				}
			}
		}

		// as last effort, return UTC
		return new DateTimeZone('UTC');
	}

	/**
	 * Check whether an option key is valid
	 *
	 * @param string $key
	 * @return bool
	 */
	public function is_option_key_valid($key)
	{
		$options = $this->bridge->get_option($key);

		if ($options && is_array($options))
			return true;

		return false;
	}

	public static function is_multisite()
	{
		return BWP_Framework_Util::is_multisite();
	}

	public static function is_subdomain_install()
	{
		return BWP_Framework_Util::is_subdomain_install();
	}

	public static function is_super_admin()
	{
		return BWP_Framework_Util::is_super_admin();
	}

	public static function is_site_admin()
	{
		return BWP_Framework_Util::is_site_admin();
	}

	public static function is_multisite_admin()
	{
		return BWP_Framework_Util::is_multisite_admin();
	}

	public static function is_on_main_blog()
	{
		return BWP_Framework_Util::is_on_main_blog();
	}

	public static function can_update_site_option()
	{
		return BWP_Framework_Util::can_update_site_option();
	}

	public static function is_apache()
	{
		return BWP_Framework_Util::is_apache();
	}

	public static function is_nginx()
	{
		return BWP_Framework_Util::is_nginx();
	}

	protected function add_cap($cap)
	{
		$this->plugin_cap = $cap;
	}

	/**
	 * Get contents from a template file, with data filled with provided $data
	 *
	 * @param string $template_file_path path to template file, starting from
	 *                                   plugin's src directory, a leading
	 *                                   slash is not required.
	 * @param array $data
	 *
	 * @since rev 160
	 */
	protected function get_template_contents($template_file_path, array $data = array())
	{
		ob_start();

		include_once constant($this->plugin_ckey . '_PLUGIN_SRC') . '/' . ltrim($template_file_path, '/');

		$output = ob_get_clean();

		return $output;
	}

	protected static function normalize_options($options)
	{
		return $options && is_array($options) ? $options : array();
	}
}
