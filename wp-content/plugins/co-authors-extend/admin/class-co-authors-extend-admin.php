<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      0.0.1
 *
 * @package    Coauthors_Extend
 * @subpackage Coauthors_Extend/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coauthors_Extend
 * @subpackage Coauthors_Extend/admin
 * @author     Jonathan Stegall <jonathan@jonathanstegall.com>
 */
class Coauthors_Extend_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	function capx_filter_guest_author_fields( $fields_to_return, $groups ) {

		// remove fields
		foreach ( $fields_to_return as $key => $value ) {
			if ($value['key'] == 'jabber' || $value['key'] == 'aim' || $value['key'] == 'yahooim') {
				unset( $fields_to_return[$key] );
			}
		}

		// add fields
		if ( in_array( 'all', $groups ) || in_array( 'contact-info', $groups ) ) {
			$fields_to_return[] = array(
				'key'      => 'twitter',
				'label'    => 'Twitter URL',
				'group'    => 'contact-info'
			);
		}
		if ( in_array( 'all', $groups ) || in_array( 'name', $groups ) ) {
			$fields_to_return[] = array(
				'key'      => 'job-title',
				'label'    => 'Job Title',
				'group'    => 'name'
			);
		} 
		if ( in_array( 'all', $groups ) || in_array( 'about', $groups ) ) {
			$fields_to_return[] = array(
				'key'      => 'teaser',
				'label'    => 'Teaser',
				'group'    => 'about',
				'textearea_rows' => 15
			);
		} 
		
		return $fields_to_return;
	}

}
