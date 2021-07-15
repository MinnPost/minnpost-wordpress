<?php
/**
 * Plugin Name:     Republication Tracker Tool
 * Description:     Allow readers to share your content via a creative commons license.
 * Author:          INN Labs
 * Author URI:      https://labs.inn.org
 * Text Domain:     republication-tracker-tool
 * Domain Path:     /languages
 * Version:         1.2.2
 *
 * @package         Republication_Tracker_Tool
 */

require plugin_dir_path( __FILE__ ) . 'includes/class-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-article-settings.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-widget.php';

/**
* Main initiation class.
*
* @since  1.0
*/
final class Republication_Tracker_Tool {

	/**
	 * Current version.
	 *
	 * @var    string
	 * @since  1.0
	 */
	const VERSION = '1.0.1';

	/**
	 * URL of plugin directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $url = '';

	/**
	 * Path of plugin directory.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $path = '';

	/**
	 * Plugin basename.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $basename = '';

	/**
	 * Singleton instance of plugin.
	 *
	 * @var    Republication_Tracker_Tool
	 * @since  1.0
	 */
	protected static $single_instance = null;

	/**
	 * Instance of Republication_Tracker_Tool_Settings
	 *
	 * @since 1.0
	 * @var Republication_Tracker_Tool_Settings
	 */
	protected $settings;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since   1.0
	 * @return  Republication_Tracker_Tool A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin.
	 *
	 * @since  1.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}

	/**
	 * Add hooks and filters.
	 * Priority needs to be
	 * < 10 for CPT_Core,
	 * < 5 for Taxonomy_Core,
	 * and 0 for Widgets because widgets_init runs at init priority 1.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Init hooks
	 *
	 * @since  1.0
	 */
	public function init() {

		// Load translated strings for plugin.
		load_plugin_textdomain( 'republication-tracker-tool', false, dirname( $this->basename ) . '/languages/' );

		$this->settings         = new Republication_Tracker_Tool_Settings( $this );
		$this->article_settings = new Republication_Tracker_Tool_Article_Settings( $this );

		add_action( 'widgets_init', array( $this, 'register_widgets' ) );

		add_filter( 'plugin_row_meta', array( $this, '_plugin_row_meta' ), 10, 2 );

		add_filter( 'query_vars', array( $this, 'enable_pixel_query_vars' ) );

		// fire our pixel is the correct param is set
		add_filter(
			'template_include',
			function( $template ) {
				// if the params are set, use our pixel functions
				if ( isset( $_GET['republication-pixel'] ) && isset( $_GET['post'] ) && isset( $_GET['ga'] ) ) {
					return include_once plugin_dir_path( __FILE__ ) . 'includes/pixel.php';
					// else, continue with whatever template was being loaded
				} else {
					return $template;
				}
			},
			99
		);

	}


	/**
	 * Register our widgets.
	 *
	 * @since 1.0
	 */
	public function register_widgets() {
		register_widget( 'Republication_Tracker_Tool_Widget' );
	}


	/**
	 * Activate the plugin.
	 *
	 * @since  1.0
	 */
	public function _activate() {}

	/**
	 * Deactivate the plugin.
	 *
	 * @since  1.0
	 */
	public function _deactivate() {}

	public function _plugin_row_meta( $links, $file ) {

		if ( strpos( $file, 'republication-tracker-tool/republication-tracker-tool.php' ) !== false ) {

			$new_links = array(
				'donate'        => '<a href="options-reading.php">Settings</a>',
				'documentation' => '<a href="https://github.com/Automattic/republication-tracker-tool/tree/master/docs" target="_blank">Documentation</a>',
			);

			$links = array_merge( $links, $new_links );

		}

		return $links;

	}

	public function enable_pixel_query_vars( $vars ) {

		$vars[] .= 'republication-pixel';
		$vars[] .= 'GA';
		$vars[] .= 'post';

		return $vars;

	}

	/**
	 * Create tracking pixel HTML markup.
	 *
	 * @param $post_id Id of the post to track.
	 */
	public static function create_tracking_pixel_markup( $post_id ) {
		$analytics_id = get_option( 'republication_tracker_tool_analytics_id' );
		return sprintf(
			// %1$s is the javascript source, %2$s is the post ID, %3$s is the plugins URL
			'<img id="republication-tracker-tool-source" src="%1$s/?republication-pixel=true&post=%2$s&ga=%3$s" style="width:1px;height:1px;">',
			esc_attr( get_site_url() ),
			esc_attr( $post_id ),
			esc_attr( $analytics_id )
		);
	}

	/**
	 * Get attribution text, which will be inserted at the end of the copyable content.
	 *
	 * @param $post The shared post.
	 */
	public static function create_content_footer( $post = null ) {
		$pixel = self::create_tracking_pixel_markup( $post->ID );

		$display_attribution = get_option( 'republication_tracker_tool_display_attribution', 'on' );
		if ( 'on' === $display_attribution && null !== $post ) {
			$site_icon_markup = '';
			$site_icon_url    = get_site_icon_url( 150 );
			if ( ! empty( $site_icon_url ) ) {
				$site_icon_markup = sprintf(
					'<img src="%1$s" style="width:1em;height:1em;margin-left:10px;">',
					esc_attr( $site_icon_url ),
				);
			}

			return wpautop(
				sprintf(
				// translators: %1$s is a URL, %2$s is the site home URL, and %3$s is the site title.
					esc_html__( 'This <a target="_blank" href="%1$s">article</a> first appeared on <a target="_blank" href="%2$s">%3$s</a> and is republished here under a Creative Commons license.', 'republication-tracker-tool' ),
					get_permalink( $post ),
					home_url(),
					esc_html( get_bloginfo() )
				) . htmlentities( $site_icon_markup ) . htmlentities( $pixel )
			);
		}
		return htmlentities( $pixel );
	}
}

/**
 * Grab the Republication_Tracker_Tool object and return it.
 * Wrapper for Republication_Tracker_Tool::get_instance().
 *
 * @since  1.0
 * @return Republication_Tracker_Tool  Singleton instance of plugin class.
 */
function Republication_Tracker_Tool() {
	return Republication_Tracker_Tool::get_instance();
}

add_action( 'plugins_loaded', array( Republication_Tracker_Tool(), 'hooks' ) );

// Activation and deactivation.
register_activation_hook( __FILE__, array( Republication_Tracker_Tool(), '_activate' ) );
register_deactivation_hook( __FILE__, array( Republication_Tracker_Tool(), '_deactivate' ) );
