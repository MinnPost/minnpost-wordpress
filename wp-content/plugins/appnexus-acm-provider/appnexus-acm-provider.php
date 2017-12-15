<?php
/*
Plugin Name: Appnexus ACM Provider
Plugin URI:
Description:
Version: 0.0.3
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: appnexus-acm-provider
*/

class Appnexus_ACM_Provider extends ACM_Provider {

	private $version;
	public $default_domain;
	public $server_path;
	public $default_url;

	public function __construct() {

		$this->version = '0.0.3';

		$this->option_prefix = 'appnexus_acm_provider_';
		$this->slug = 'appnexus-acm-provider';

		global $ad_code_manager;
		$this->ad_code_manager = $ad_code_manager;

		// ACM Ad Panel
		$this->ad_panel = $this->ad_panel();

		// tags for AppNexus
		$this->ad_tag_ids = $this->ad_panel->ad_tag_ids();

		// Default fields for AppNexus
		$this->ad_code_args = $this->ad_panel->ad_code_args();

		// front end for rendering ads
		$this->front_end = $this->front_end();

		// admin settings
		$this->admin = $this->load_admin();

		parent::__construct();
	}

	/**
	* Load the admin panel
	* Creates the admin screen for the ACM Ad Code Manager
	*
	* @throws \Exception
	*/
	private function ad_panel() {
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-' . $this->slug . '-ad-panel.php' );
		$ad_panel = new Appnexus_ACM_Provider_Ad_Panel( $this->option_prefix, $this->version, $this->slug, $this->ad_code_manager );
		add_filter( 'acm_ad_code_args', array( $ad_panel, 'filter_ad_code_args' ) );
		return $ad_panel;
	}

	/**
	* load the front end
	* Renders and places the ads
	*
	* @throws \Exception
	*/
	private function front_end() {
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-' . $this->slug . '-front-end.php' );
		$front_end = new Appnexus_ACM_Provider_Front_End( $this->option_prefix, $this->version, $this->slug, $this->ad_code_manager, $this->ad_panel );
		return $front_end;
	}

	/**
	* load the admin stuff
	* creates admin menu to save the config options
	*
	* @throws \Exception
	*/
	private function load_admin() {
		require_once( plugin_dir_path( __FILE__ ) . 'classes/class-' . $this->slug . '-admin.php' );
		$admin = new Appnexus_ACM_Provider_Admin( $this->option_prefix, $this->version, $this->slug, $this->ad_panel, $this->front_end );
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
		return $admin;
	}

	/**
	* Display a Settings link on the main Plugins page
	*
	* @param array $links
	* @param string $file
	* @return array $links
	* These are the links that go with this plugin's entry
	*/
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$settings = '<a href="' . get_admin_url() . 'options-general.php?page=' . $this->slug . '">' . __( 'Settings', 'appnexus-acm-provider' ) . '</a>';
			array_unshift( $links, $settings );
		}
		return $links;
	}

}

class Appnexus_ACM_WP_List_Table extends ACM_WP_List_Table {
	function __construct() {
		parent::__construct( array(
			'singular' => 'appnexus_acm_wp_list_table', //Singular label
			'plural' => 'appnexus_acm_wp_list_table', //plural label, also this well be one of the table css class
			'ajax' => true,
		) );
	}

	/**
	 * @return array The columns that shall be used
	 */
	function filter_columns() {
		return array(
			'cb'             => '<input type="checkbox" />',
			'id'             => __( 'ID', 'ad-code-manager' ),
			'tag'            => __( 'Tag', 'ad-code-manager' ),
			'tag_id'         => __( 'Tag ID', 'ad-code-manager' ),
			'tag_name'       => __( 'Tag Name', 'ad-code-manager' ),
			'priority'       => __( 'Priority', 'ad-code-manager' ),
			'operator'       => __( 'Logical Operator', 'ad-code-manager' ),
			'conditionals'   => __( 'Conditionals', 'ad-code-manager' ),
		);
	}

	/**
	 * This is nuts and bolts of table representation
	 */
	function get_columns() {
		add_filter( 'acm_list_table_columns', array( $this, 'filter_columns' ) );
		return parent::get_columns();
	}

	/**
	 * Output the tag cell in the list table
	 */
	function column_tag( $item ) {
		$output = isset( $item['tag'] ) ? esc_html( $item['tag'] ) : esc_html( $item['url_vars']['tag'] );
		$output .= $this->row_actions_output( $item );
		return $output;
	}


}
