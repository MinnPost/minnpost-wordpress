<?php
/*
Plugin Name: Merge Serialized Fields
Plugin URI: https://wordpress.org/plugins/merge-serialized-fields/
Description: 
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
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

		$this->version = '0.0.1';

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
    		if ( $option_name === 'merge_serialized_fields_schedule' && $old_value !== $value ) {
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
		if ( $file == plugin_basename( __FILE__ ) ) {
			$settings = '<a href="' . get_admin_url() . 'options-general.php?page=merge-serialized-fields">' . __('Settings', 'merge-serialized-fields' ) . '</a>';
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
					settings_fields( 'merge-serialized-fields' )  . do_settings_sections( 'merge-serialized-fields' );
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
		$page = 'merge-serialized-fields';
		$section = 'merge-serialized-fields';
		$input_callback = array( $this, 'display_input_field' );
		$select_callback = array( $this, 'display_select' );
		add_settings_section( $page, null, null, $page );

		$settings = array(
            'wp_field_to_merge' => array(
                'title' => __( 'Field Name', 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'The name of the field to merge in the database', 'merge-serialized-fields' ),
                ),
                
            ),
            'wp_filter_field' => array(
                'title' => __( 'Field Name', 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'The name of the field to filter in the database. This could be meta_key, if you are doing metadata.', 'merge-serialized-fields' ),
                ),
                
            ),
            'wp_filter_field_value' => array(
                'title' => __( 'Field Value(s)', 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'The value of the filter field. This is useful if you want to merge a meta key, such as wp_capabilities. You can comma separate to use multiple fields.', 'merge-serialized-fields' ),
                ),
                
            ),
            'wp_table' => array(
                'title' => __( 'WordPress Database Table', 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'What table contains the field you want to merge?', 'merge-serialized-fields' ),
                ),
            ),
            'group_by' => array(
                'title' => __( 'Field to Group By', 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'What field do you want to use to group the items? This could be a user ID, for example.', 'merge-serialized-fields' ),
                ),
            ),
            'primary_key' => array(
                'title' => __( 'Table Primary Key', 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'Use this if the table has a primary key, so the query can set which field to merge, and delete the rest.', 'merge-serialized-fields' ),
                ),
            ),
            'items_per_load' => array(
                'title' => __( 'Items Per Load' , 'merge-serialized-fields' ),
                'callback' => $input_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'type' => 'text',
                    'desc' => __( 'Maximum items the query should load per run', 'merge-serialized-fields' ),
                ),
            ),
            'schedule' => array(
                'title' => __( 'Schedule', 'merge-serialized-fields' ),
                'callback' => $select_callback,
                'page' => $page,
                'section' => $section,
                'args' => array(
                    'desc' => __( 'How often the plugin should find and process data', 'merge-serialized-fields' ),
                    'items' => array(
                    	'hourly' => __( 'Hourly', 'merge-serialized-fields' ),
                    	'twicedaily' => __( 'Twice Daily', 'merge-serialized-fields' ),
                    	'daily' => __( 'Daily', 'merge-serialized-fields' )
                    ) // values from https://codex.wordpress.org/Function_Reference/wp_schedule_event
                ),
            ),
        );

        foreach( $settings as $key => $attributes ) {
            $id = 'merge_serialized_fields_' . $key;
            $name = 'merge_serialized_fields_' . $key;
            $title = $attributes['title'];
            $callback = $attributes['callback'];
            $page = $attributes['page'];
            $section = $attributes['section'];
            $args = array_merge(
                $attributes['args'],
                array(
                    'title' => $title,
                    'id' => $id,
                    'label_for' => $id,
                    'name' => $name
                )
            );
            add_settings_field( $id, $title, $callback, $page, $section, $args );
            register_setting( $section, $id );
        }

	}

	/**
    * Default display for <input> fields
    *
    * @param array $args
    */
    public function display_input_field( $args ) {
        $type   = $args['type'];
        $id     = $args['label_for'];
        $name   = $args['name'];
        $desc   = $args['desc'];
        if ( !isset( $args['constant'] ) || !defined( $args['constant'] ) ) {
            $value  = esc_attr( get_option( $id, '' ) );
            echo '<input type="' . $type. '" value="' . $value . '" name="' . $name . '" id="' . $id . '"
            class="regular-text code" />';
            if ( $desc != '' ) {
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
        $name = $args['name'];
        $id = $args['label_for'];
        $desc = $args['desc'];
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
        if ( $desc != '' ) {
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
			$merge_rows = $wpdb->get_results( 'SELECT ' . $config['group_by'] . ' FROM ' . $config['wp_table'] . ' WHERE ' . $config['wp_filter_field'] . ' = "' . $config['wp_filter_field_value'] . '" LIMIT ' . $config['items_per_load'], OBJECT );
			foreach ( $merge_rows as $merge_row ) {
				$id = $merge_row->$config['group_by'];
				$merge_items = $wpdb->get_results( 'SELECT ' . $config['primary_key'] . ', ' . $config['wp_field_to_merge'] . ' FROM ' . $config['wp_table'] . ' WHERE ' . $config['wp_filter_field'] . ' = "' . $config['wp_filter_field_value'] . '" AND ' . $config['group_by'] . ' = "' . $id . '" LIMIT ' . $config['items_per_load'], OBJECT );
				if (count( $merge_items) > 1 ) {
					$merged_array = [];
					foreach ( $merge_items as $key => $value ) {
						if ( $key === 0 ) {
							$id_to_update = $value->$config['primary_key'];
						}
						$value = $value->$config['wp_field_to_merge'];
						$merged_array = array_merge( $merged_array, unserialize( $value ) );
					}
					$merged_serialized = serialize( $merged_array );
				}
			}
			// then run sql to combine the fields
		}
		if ( isset( $id_to_update ) && isset( $merged_serialized ) ) {
			$table = $config['wp_table'];
			$wp_field_to_merge = $config['wp_field_to_merge'];
			$primary_key = $config['primary_key'];
			$group_by = $config['group_by'];
			$wp_filter_field = $config['wp_filter_field'];
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

		if ( FALSE !== strpos( $wp_filter_field_value, ',' ) ) {
			$wp_filter_field_values = explode( ',', $wp_filter_field_value );
			$this->config = array();
			foreach ( $wp_filter_field_values as $key => $value ) {
				$this->config[$key] = array(
					'wp_field_to_merge' => get_option( 'merge_serialized_fields_wp_field_to_merge', '' ),
					'wp_filter_field' => get_option( 'merge_serialized_fields_wp_filter_field', '' ),
					'wp_filter_field_value' => $value,
					'wp_table' => get_option( 'merge_serialized_fields_wp_table', '' ),
					'group_by' => get_option( 'merge_serialized_fields_group_by', '' ),
					'primary_key' => get_option( 'merge_serialized_fields_primary_key', '' ),
					'items_per_load' => get_option( 'merge_serialized_fields_items_per_load', '' ),
					'schedule' => get_option( 'merge_serialized_fields_schedule', '' )
				);
			}
		} else {
			$this->config = array(
				0 => array(
					'wp_field_to_merge' => get_option( 'merge_serialized_fields_wp_field_to_merge', '' ),
					'wp_filter_field' => get_option( 'merge_serialized_fields_wp_filter_field', '' ),
					'wp_filter_field_value' => get_option( 'merge_serialized_fields_wp_filter_field_value', '' ),
					'wp_table' => get_option( 'merge_serialized_fields_wp_table', '' ),
					'group_by' => get_option( 'merge_serialized_fields_group_by', '' ),
					'primary_key' => get_option( 'merge_serialized_fields_primary_key', '' ),
					'items_per_load' => get_option( 'merge_serialized_fields_items_per_load', '' ),
					'schedule' => get_option( 'merge_serialized_fields_schedule', '' )
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
		foreach ($this->config as $key => $value) {
			if (! wp_next_scheduled ( 'merge_serialized_event' ) ) {
				wp_schedule_event( time(), $value['schedule'], 'merge_serialized_event' );
		    }
		    add_action( 'merge_serialized_event', array( $this, 'get_fields_to_merge') );
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
$Merge_Serialized_Fields = new Merge_Serialized_Fields();