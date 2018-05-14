<?php
/*
Plugin Name: Merge Serialized Fields
Plugin URI: https://wordpress.org/plugins/merge-serialized-fields/
Description:
Version: 0.0.3
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: merge-serialized-fields
*/

// Start up the plugin
class Merge_Serialized_Fields {

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

		$this->version = '0.0.3';

		$this->load_admin();

		$this->config();
		$this->schedule();
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	}

	/**
	* load the admin stuff
	* creates admin menu to save the config options
	*
	* @throws \Exception
	*/
	private function load_admin() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_settings_form' ) );
		add_action( 'updated_option', function( $option_name, $old_value, $value ) {
			if ( 'merge_serialized_fields_schedule' === $option_name && $old_value !== $value ) {
				// delete the old schedule and create the new one - this means user changed how often it should run
				$this->deactivate();
				$this->schedule();
			}
		}, 10, 3);
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 5 );
	}

	/**
	* Default display for <input> fields
	*
	* @param array $args
	*/
	public function create_admin_menu() {
		add_options_page( __( 'Merge Serialized Fields', 'merge-serialized-fields' ), __( 'Merge Serialized Fields', 'merge-serialized-fields' ), 'manage_options', 'merge-serialized-fields', array( $this, 'show_admin_page' ) );
	}

	/**
	* Display a Settings link on the main Plugins page
	*
	* @return array $links
	*/
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$settings = '<a href="' . get_admin_url() . 'options-general.php?page=merge-serialized-fields">' . __( 'Settings', 'merge-serialized-fields' ) . '</a>';
			// make the 'Settings' link appear first
			array_unshift( $links, $settings );
		}
		return $links;
	}

	/**
	* Display the admin settings page
	*
	* @return void
	*/
	public function show_admin_page() {
		?>
		<div class="wrap">
			<h1><?php _e( get_admin_page_title() , 'merge-serialized-fields' ); ?></h1>
			<div id="main">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'merge-serialized-fields' ) . do_settings_sections( 'merge-serialized-fields' );
					?>
					<?php submit_button( __( 'Save settings', 'merge-serialized-fields' ) ); ?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	* Register items for the settings api
	* @return void
	*
	*/
	public function admin_settings_form() {
		$page    = 'merge-serialized-fields';
		$section = 'merge-serialized-fields';

		$input_callback  = array( $this, 'display_input_field' );
		$select_callback = array( $this, 'display_select' );
		add_settings_section( $page, null, null, $page );

		// temp uncomment for testing when we don't want to wait for schedule to run
		//$this->get_fields_to_merge();

		$settings = array(
			'wp_field_to_merge'     => array(
				'title'    => __( 'Field Name', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'The name of the field to merge in the database', 'merge-serialized-fields' ),
				),
			),
			'wp_filter_field'       => array(
				'title'    => __( 'Field Name', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'The name of the field to filter in the database. This could be meta_key, if you are doing metadata.', 'merge-serialized-fields' ),
				),
			),
			'wp_filter_field_value' => array(
				'title'    => __( 'Field Value(s)', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'The value of the filter field. This is useful if you want to merge a meta key, such as wp_capabilities. You can comma separate to use multiple fields.', 'merge-serialized-fields' ),
				),
			),
			'wp_table'              => array(
				'title'    => __( 'WordPress Database Table', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'What table contains the field you want to merge?', 'merge-serialized-fields' ),
				),
			),
			'group_by'              => array(
				'title'    => __( 'Field to Group By', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'What field do you want to use to group the items? This could be a user ID, for example.', 'merge-serialized-fields' ),
				),
			),
			'primary_key'           => array(
				'title'    => __( 'Table Primary Key', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'Use this if the table has a primary key, so the query can set which field to merge, and delete the rest.', 'merge-serialized-fields' ),
				),
			),
			'items_per_load'        => array(
				'title'    => __( 'Items Per Load', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'Maximum items the query should load per run', 'merge-serialized-fields' ),
				),
			),
			'schedule_number'       => array(
				'title'    => __( 'Run schedule every', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'number',
					'desc' => '',
				),
			),
			'schedule_unit'         => array(
				'title'    => __( 'Time unit', 'merge-serialized-fields' ),
				'callback' => $select_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type'  => 'select',
					'desc'  => '',
					'items' => array(
						'minutes' => __( 'Minutes', 'merge-serialized-fields' ),
						'hours'   => __( 'Hours', 'merge-serialized-fields' ),
						'days'    => __( 'Days', 'merge-serialized-fields' ),
					),
				),
			),
			'last_row_checked'      => array(
				'title'    => __( 'Current offset ID', 'merge-serialized-fields' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'number',
					'desc' => '',
				),
			),
		);

		foreach ( $settings as $key => $attributes ) {
			$id       = 'merge_serialized_fields_' . $key;
			$name     = 'merge_serialized_fields_' . $key;
			$title    = $attributes['title'];
			$callback = $attributes['callback'];
			$page     = $attributes['page'];
			$section  = $attributes['section'];
			$args     = array_merge(
				$attributes['args'],
				array(
					'title'     => $title,
					'id'        => $id,
					'label_for' => $id,
					'name'      => $name,
				)
			);
			add_settings_field( $id, $title, $callback, $page, $section, $args );
			register_setting( $section, $id );
		}

	}

	/**
	* Convert the schedule frequency from the admin settings into an array
	* interval must be in seconds for the class to use it
	*
	*/
	public function get_schedule_frequency_key( $name = '' ) {

		if ( '' !== $name ) {
			$name = '_' . $name;
		}

		$schedule_number = get_option( 'merge_serialized_fields' . $name . '_schedule_number', '' );
		$schedule_unit   = get_option( 'merge_serialized_fields' . $name . '_schedule_unit', '' );

		switch ( $schedule_unit ) {
			case 'minutes':
				$seconds = 60;
				break;
			case 'hours':
				$seconds = 3600;
				break;
			case 'days':
				$seconds = 86400;
				break;
			default:
				$seconds = 0;
		}

		$key = $schedule_unit . '_' . $schedule_number;

		return $key;

	}

	/**
	* Default display for <input> fields
	*
	* @param array $args
	*/
	public function display_input_field( $args ) {
		$type = $args['type'];
		$id   = $args['label_for'];
		$name = $args['name'];
		$desc = $args['desc'];
		if ( ! isset( $args['constant'] ) || ! defined( $args['constant'] ) ) {
			$value = esc_attr( get_option( $id, '' ) );
			echo '<input type="' . $type . '" value="' . $value . '" name="' . $name . '" id="' . $id . '"
			class="regular-text code" />';
			if ( '' !== $desc ) {
				echo '<p class="description">' . $desc . '</p>';
			}
		} else {
			echo '<p><code>' . __( 'Defined in wp-config.php', 'merge-serialized-fields' ) . '</code></p>';
		}
	}

	/**
	* Display for <select>
	*
	* @param array $args
	*/
	public function display_select( $args ) {
		$name          = $args['name'];
		$id            = $args['label_for'];
		$desc          = $args['desc'];
		$current_value = get_option( $name );
		echo '<select name="' . $name . '" id="' . $id . '"><option value="">' . __( 'Choose an option', 'merge-serialized-fields' ) . '</option>';
		foreach ( $args['items'] as $key => $value ) {
			$selected = '';
			if ( $current_value === $key ) {
				$selected = 'selected';
			}
			echo '<option value="' . $key . '"  ' . $selected . '>' . $value . '</option>';
		}
		echo '</select>';
		if ( '' !== $desc ) {
			echo '<p class="description">' . $desc . '</p>';
		}
	}

	/**
	 * Get fields that should be merged
	 *
	 * @return void
	 */
	public function get_fields_to_merge() {
		foreach ( $this->config as $config ) {
			global $wpdb;
			$offset           = '';
			$last_row_checked = get_option( 'merge_serialized_fields_last_row_checked', '0' );
			//$last_row_checked = 0;
			if ( '0' !== $last_row_checked ) {
				$offset = ' OFFSET ' . $last_row_checked;
			}
			$merge_rows = $wpdb->get_results( 'SELECT ' . $config['group_by'] . ' FROM ' . $config['wp_table'] . ' WHERE ' . $config['wp_filter_field'] . ' = "' . $config['wp_filter_field_value'] . '" LIMIT ' . $config['items_per_load'] . $offset, OBJECT );
			foreach ( $merge_rows as $key => $merge_row ) {
				if ( ( count( $merge_rows ) - 1 ) === $key ) {
					update_option( 'merge_serialized_fields_last_row_checked', count( $merge_rows ) + $last_row_checked );
				}
				if ( ! is_array( $merge_row->{$config['group_by']} ) ) {
					$id = $merge_row->{$config['group_by']};
				}

				$merge_items = $wpdb->get_results( 'SELECT ' . $config['primary_key'] . ', ' . $config['wp_field_to_merge'] . ' FROM ' . $config['wp_table'] . ' WHERE ' . $config['wp_filter_field'] . ' = "' . $config['wp_filter_field_value'] . '" AND ' . $config['group_by'] . ' = "' . $id . '"', OBJECT );
				if ( count( $merge_items ) > 1 ) {
					$merged_array = [];
					foreach ( $merge_items as $key => $value ) {
						$last_item_checked = $value->{$config['primary_key']};
						if ( 0 === $key ) {
							$id_to_update = $value->{$config['primary_key']};
						}
						//error_log( 'key is ' . $key . ' and count is ' . ( count( $merge_items ) - 1 ) . ' and id is ' . $last_item_checked );
						if ( ( count( $merge_items ) - 1 ) === $key ) {
							//error_log( 'new value should be ' .  ( $last_row_checked + $last_item_checked ) );
							update_option( 'merge_serialized_fields_last_row_checked', ( $last_row_checked + $last_item_checked ) );
						}
						$value = $value->{$config['wp_field_to_merge']};
						if ( ! is_array( maybe_unserialize( $value ) ) ) {
							continue;
						}
						$merged_array = array_merge( $merged_array, maybe_unserialize( $value ) );
						if ( array_key_exists( 'comment_moderator', $merged_array ) ) {
							if ( array_key_exists( 'administrator', $merged_array ) ) {
								unset( $merged_array['comment_moderator'] );
							}
							if ( array_key_exists( 'editor', $merged_array ) ) {
								unset( $merged_array['comment_moderator'] );
							}
							if ( array_key_exists( 'business', $merged_array ) ) {
								unset( $merged_array['comment_moderator'] );
							}
							if ( array_key_exists( 'contributor', $merged_array ) ) {
								unset( $merged_array['comment_moderator'] );
							}
							if ( array_key_exists( 'author', $merged_array ) ) {
								unset( $merged_array['comment_moderator'] );
							}
						}
					}
					//error_log( 'merged value is ' . serialize( $merged_array ), true );
					$merged_serialized = serialize( $merged_array );
				}
			} // End foreach(). then run sql to combine the fields
		} // End foreach().
		if ( isset( $id_to_update ) && isset( $merged_serialized ) ) {
			$table                 = $config['wp_table'];
			$wp_field_to_merge     = $config['wp_field_to_merge'];
			$primary_key           = $config['primary_key'];
			$group_by              = $config['group_by'];
			$wp_filter_field       = $config['wp_filter_field'];
			$wp_filter_field_value = $config['wp_filter_field_value'];

			$update = $wpdb->query( "UPDATE $table SET $wp_field_to_merge = '$merged_serialized' WHERE $primary_key = '$id_to_update'" );
			$delete = $wpdb->query( "DELETE FROM $table WHERE $group_by = '$id' AND $primary_key != '$id_to_update' AND $wp_filter_field = '$wp_filter_field_value'" );
			//error_log('update is ' . $update);
		}
	}

	/**
	 * Create an action on plugin init so we can gather some config items for this plugin from the wp settings
	 * this sets the $this->config variable
	 *
	 * @return void
	 */
	private function config() {

		// this would theoretically allow us to support multiple imported, serialized fields if it became necessary
		// and the ui could support it

		$wp_filter_field_value = get_option( 'merge_serialized_fields_wp_filter_field_value', '' );

		if ( false !== strpos( $wp_filter_field_value, ',' ) ) {
			$wp_filter_field_values = explode( ',', $wp_filter_field_value );
			$this->config           = array();
			foreach ( $wp_filter_field_values as $key => $value ) {
				$this->config[ $key ] = array(
					'wp_field_to_merge'     => get_option( 'merge_serialized_fields_wp_field_to_merge', '' ),
					'wp_filter_field'       => get_option( 'merge_serialized_fields_wp_filter_field', '' ),
					'wp_filter_field_value' => $value,
					'wp_table'              => get_option( 'merge_serialized_fields_wp_table', '' ),
					'group_by'              => get_option( 'merge_serialized_fields_group_by', '' ),
					'primary_key'           => get_option( 'merge_serialized_fields_primary_key', '' ),
					'items_per_load'        => get_option( 'merge_serialized_fields_items_per_load', '' ),
					'schedule_number'       => get_option( 'merge_serialized_fields_schedule_number', '' ),
					'schedule_unit'         => get_option( 'merge_serialized_fields_schedule_unit', '' ),
				);
			}
		} else {
			$this->config = array(
				0 => array(
					'wp_field_to_merge'     => get_option( 'merge_serialized_fields_wp_field_to_merge', '' ),
					'wp_filter_field'       => get_option( 'merge_serialized_fields_wp_filter_field', '' ),
					'wp_filter_field_value' => get_option( 'merge_serialized_fields_wp_filter_field_value', '' ),
					'wp_table'              => get_option( 'merge_serialized_fields_wp_table', '' ),
					'group_by'              => get_option( 'merge_serialized_fields_group_by', '' ),
					'primary_key'           => get_option( 'merge_serialized_fields_primary_key', '' ),
					'items_per_load'        => get_option( 'merge_serialized_fields_items_per_load', '' ),
					'schedule_number'       => get_option( 'merge_serialized_fields_schedule_number', '' ),
					'schedule_unit'         => get_option( 'merge_serialized_fields_schedule_unit', '' ),
				),
			);
		}
	}

	/**
	 * Scheule function
	 * This registers the method to get the WordPress data that needs to be unserialized
	 *
	 * @return void
	 */
	public function schedule() {

		foreach ( $this->config as $key => $value ) {
			// this would need to change to allow different schedules
			$schedule_frequency = $this->get_schedule_frequency_key();

			if ( ! wp_next_scheduled( 'merge_serialized_event' ) ) {
				wp_schedule_event( time(), $schedule_frequency, 'merge_serialized_event' );
			}

			add_action( 'merge_serialized_event',
				array(
					$this,
					'get_fields_to_merge',
				)
			);
		}
	}

	/**
	 * Deactivate function
	 * This stops the regular repetition of the task
	 *
	 * @return void
	 */
	public function deactivate() {
		wp_clear_scheduled_hook( 'merge_serialized_event' );
	}

}
// Instantiate our class
$merge_serialized_fields = new Merge_Serialized_Fields();
