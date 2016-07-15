<?php
/*
Plugin Name: Deserialize Metadata
Description: In the process of migrating from Drupal to WordPress by SQL queries, it's likely that some metadata fields that are serialized in Drupal will be migrated into the WordPress database, but will need to be separated into fields.
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: deserialize-metadata
*/

// Start up the plugin
class Deserialize_Metadata {

	/**
	* @var string
	*/
	private $version;

	/**
	* @var array
	*/
	private $config;

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version = '0.0.1';
		$this->config = array();

		$this->config();
		$this->activate( $this->version );

	}

	/**
	 * Create an action on plugin init so we can gather some config items for this plugin
	 *
	 * @return void
	 */
	private function config() {
		//add_action( 'init', array( $this, 'get_config_data' ) );
		$this->config = array(
			0 => array(
				'wp_imported_field' => '_wp_imported_metadata',
				'post_type' => 'any',
				'post_status' => 'any',
				'maps' => array(
					'alt' => array(
						'wp_table' => 'wp_postmeta',
						'wp_column' => '_wp_attachment_image_alt',
						'unique' => true
					),
					'description' => array(
						'wp_table' => 'wp_posts',
						'wp_column' => 'post_content',
						'unique' => true
					),
					'title' => array(
						'wp_table' => 'wp_posts',
						'wp_column' => 'post_title',
						'unique' => true
					),
				),
			),
		);
	}

	/**
	 * Set a default config array to indicate what fields to deserialize, and what to do with them afterwards
	 * This method would set up the apply_filters hook so users can add/modify the config in their own functions.php
	 * We do this because this plugin only does things on activate
	 * This seems to be impossible though. Might as well keep it around in here for now in case that's not accurate.
	 *
	 * @return void
	 */
	public function get_config_data() {
    	$this->config = apply_filters( 'config_deserialize_metadata', $this->config );
	}

	/**
	 * Activate function
	 * This registers the method to get the WordPress data that needs to be unserialized
	 *
	 * @return void
	 */
	private function activate( $version ) {
		register_activation_hook( __FILE__, array( $this, 'get_posts_with_serialized_metadata' ) );
	}

	/**
	 * Get WordPress posts that match our criteria for serialized metadata
	 * This also calls the create and delete methods to handle what to do with the data
	 *
	 * @return void
	 */
	public function get_posts_with_serialized_metadata() {
		foreach ( $this->config as $config ) {
			$key = $config['wp_imported_field'];
			$maps = $config['maps'];
			$args = array( 'post_type' => $config['post_type'], 'post_status' => $config['post_status'], 'meta_query' => array( array( 'key' => $key ) ) );
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id = $query->post->ID;
					$metadata = get_post_meta( $post_id, $key, true );
					$this->create_fields( $post_id, $metadata, $maps );
					$this->delete_combined_field( $post_id, $key );
				}
			}
		}

	}

	/**
	 * Create fields to match the structure WordPress wants
	 * This structure is defined in $this->config
	 *
	 * @return void
	 */
	private function create_fields( $post_id, $metadata, $maps ) {
		foreach ( $metadata as $key => $value ) {
			if ( array_key_exists( $key, $maps ) ) {
				//error_log('create a row on the ' . $maps[$key]['wp_table'] . ' field in the ' . $maps[$key]['wp_column'] . ' column with the value ' . $value);
				if ( $maps[$key]['wp_table'] === 'wp_postmeta' ) {
					add_post_meta( $post_id, $maps[$key]['wp_column'], $value, $maps[$key]['unique'] );
				} else if ( $maps[$key]['wp_table'] === 'wp_posts' && $value != '' ) {
					$post = array(
						'ID' => $post_id,
						$maps[$key]['wp_column'] => $value
					);
					wp_update_post( $post );
				}
			}
		}
	}

	/**
	 * Delete the original, serialized field so we don't trigger it if the plugin is activated again
	 *
	 * @return void
	 */
	private function delete_combined_field( $post_id, $key ) {
		delete_post_meta( $post_id, $key );
	}

/// end class
}
// Instantiate our class
$Deserialize_Metadata = new Deserialize_Metadata();