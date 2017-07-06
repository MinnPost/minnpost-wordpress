<?php
/*
Plugin Name: Migrate Random Things
Plugin URI: https://wordpress.org/plugins/migrate-random-things
Description:
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: migrate-random-things
*/

// Start up the plugin
class Migrate_Random_Things {

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
			if ( $option_name === 'migrate_random_things_schedule' && $old_value !== $value ) {
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
		add_options_page( __( 'Migrate Random Things', 'migrate-random-things' ), __( 'Migrate Random Things', 'migrate-random-things' ), 'manage_options', 'migrate-random-things', array( $this, 'show_admin_page' ) );
	}

	/**
	* Display a Settings link on the main Plugins page
	*
	* @return array $links
	*/
	public function plugin_action_links( $links, $file ) {
		if ( $file == plugin_basename( __FILE__ ) ) {
			$settings = '<a href="' . get_admin_url() . 'options-general.php?page=migrate-random-things">' . __('Settings', 'migrate-random-things' ) . '</a>';
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
			<h1><?php _e( get_admin_page_title() , 'migrate-random-things' ); ?></h1>
			<div id="main">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'migrate-random-things' )  . do_settings_sections( 'migrate-random-things' );
					?>
					<?php submit_button( __( 'Save settings', 'migrate-random-things' ) ); ?>
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
		$page = 'migrate-random-things';
		$section = 'migrate-random-things';
		$input_callback = array( $this, 'display_input_field' );
		$select_callback = array( $this, 'display_select' );
		add_settings_section( $page, null, null, $page );

		// temp when we don't want to wait for schedule to run
		//$this->get_things_to_migrate();

		$settings = array(
			'menu_table' => array(
				'title' => __( 'Menu Table Name', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'The name of the table for menus', 'migrate-random-things' ),
				),
			),
			'menu_items_table' => array(
				'title' => __( 'Menu Items Table', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'The name of the table with the individual menu items.', 'migrate-random-things' ),
				),
			),
			/*'wp_filter_field_value' => array(
				'title' => __( 'Field Value(s)', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'The value of the filter field. This is useful if you want to merge a meta key, such as wp_capabilities. You can comma separate to use multiple fields.', 'migrate-random-things' ),
				),
			),
			'wp_table' => array(
				'title' => __( 'WordPress Database Table', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'What table contains the field you want to merge?', 'migrate-random-things' ),
				),
			),
			'group_by' => array(
				'title' => __( 'Field to Group By', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'What field do you want to use to group the items? This could be a user ID, for example.', 'migrate-random-things' ),
				),
			),
			'primary_key' => array(
				'title' => __( 'Table Primary Key', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'Use this if the table has a primary key, so the query can set which field to merge, and delete the rest.', 'migrate-random-things' ),
				),
			),
			'items_per_load' => array(
				'title' => __( 'Items Per Load' , 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'Maximum items the query should load per run', 'migrate-random-things' ),
				),
			),*/
			'schedule_number' => array(
				'title' => __( 'Run schedule every', 'migrate-random-things' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'number',
					'desc' => '',
				),
			),
			'schedule_unit' => array(
				'title' => __( 'Time unit', 'migrate-random-things' ),
				'callback' => $select_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'select',
					'desc' => '',
					'items' => array(
						'minutes' => __( 'Minutes', 'migrate-random-things' ),
						'hours' => __( 'Hours', 'migrate-random-things' ),
						'days' => __( 'Days', 'migrate-random-things' ),
					),
				),
			),
		);

		foreach ( $settings as $key => $attributes ) {
			$id = 'migrate_random_things_' . $key;
			$name = 'migrate_random_things_' . $key;
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
					'name' => $name,
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

		$schedule_number = get_option( 'migrate_random_things' . $name . '_schedule_number', '' );
		$schedule_unit = get_option( 'migrate_random_things' . $name . '_schedule_unit', '' );

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
		$type   = $args['type'];
		$id     = $args['label_for'];
		$name   = $args['name'];
		$desc   = $args['desc'];
		if ( ! isset( $args['constant'] ) || ! defined( $args['constant'] ) ) {
			$value  = esc_attr( get_option( $id, '' ) );
			echo '<input type="' . $type . '" value="' . $value . '" name="' . $name . '" id="' . $id . '"
			class="regular-text code" />';
			if ( '' !== $desc ) {
				echo '<p class="description">' . $desc . '</p>';
			}
		} else {
			echo '<p><code>' . __( 'Defined in wp-config.php', 'migrate-random-things' ) . '</code></p>';
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
		echo '<select name="' . $name . '" id="' . $id . '"><option value="">' . __( 'Choose an option', 'migrate-random-things' ) . '</option>';
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
	 * Get things to migrate
	 *
	 * @return void
	 */
	public function get_things_to_migrate() {
		foreach ( $this->config as $config ) {
			global $wpdb;

			$menus = get_option( 'migrate_random_things_menu_table', '' );
			$menu_items = get_option( 'migrate_random_things_menu_items_table', '' );

			if ( '' !== $menus && '' !== $menu_items ) {
				if ( $wpdb->get_var( "SHOW TABLES LIKE '$menus'" ) === $menus && $wpdb->get_var( "SHOW TABLES LIKE '$menu_items'" ) === $menu_items ) {
					$menu_rows = $wpdb->get_results( 'SELECT * FROM ' . $menus . ' ORDER BY id' );
					foreach ( $menu_rows as $menu ) {
						// order by parent, then id so we get all the items without a parent before we try to add their children
						$items = $wpdb->get_results( 'SELECT `id`, `menu-item-title`, `menu-item-url`, `menu-item-status`, `menu-item-parent`, `menu-item-parent-id` FROM ' . $menu_items . ' WHERE `menu-name` = "' . $menu->name . '" ORDER BY `menu-item-parent`, id' );
						$menu_exists = wp_get_nav_menu_object( $menu->name );

						// If it doesn't exist, let's create it.
						if ( ! $menu_exists ) {
							$menu_id = wp_create_nav_menu( $menu->name );
						} else {
							$menu_id = $menu_exists->term_id;
							$existing_items = wp_get_nav_menu_items( $menu->name );
						}

						foreach ( $items as $key => $item ) {

							if ( isset( $existing_items ) ) {
								if ( isset( $existing_items[ $key ]->title ) && $existing_items[ $key ]->title === $item->{'menu-item-title'} ) {
									// menu item exists already
									continue;
								}
							}

							$url = $item->{'menu-item-url'};
							if ( 0 !== strpos( $url, 'http' ) ) {
								$url = home_url( $url );
							}

							$parent_id = 0;
							$parent_title = $item->{'menu-item-parent'};
							if ( null !== $parent_title ) {
								$parent_id = $item->{'menu-item-parent-id'};

								if ( null === $parent_id ) {
									// we couldn't load a nav menu item for the parent value
									continue 2;
								}
							}

							$args = array(
								'menu-item-parent-id' => $parent_id,
								'menu-item-status' => $item->{'menu-item-status'},
							);

							// we need to figure out if it is a category, page, etc before we create it
							$is_term = get_term_by( 'name', $item->{'menu-item-title'}, 'category', 'ARRAY_A' );
							$is_page = get_page_by_title( $item->{'menu-item-title'}, 'ARRAY_A', 'page' );
							$is_post = get_page_by_title( $item->{'menu-item-title'}, 'ARRAY_A', 'post' );
							if ( false !== $is_term && 0 !== (int) $is_term['term_id'] ) {
								$args['menu-item-type'] = 'taxonomy';
								$args['menu-item-object'] = 'category';
								$args['menu-item-object-id'] = (int) $is_term['term_id'];
							} elseif ( false !== $is_page && 0 !== (int) $is_page['ID'] ) {
								$args['menu-item-type'] = 'post_type';
								$args['menu-item-object'] = 'page';
								$args['menu-item-object-id'] = (int) $is_page['ID'];
							} elseif ( false !== $is_post && 0 !== (int) $is_post['ID'] ) {
								$args['menu-item-type'] = 'post_type';
								$args['menu-item-object'] = 'post';
								$args['menu-item-object-id'] = (int) $is_post['ID'];
							} else {
								// otherwise it is a custom link
								$args['menu-item-title'] = esc_html( $item->{'menu-item-title'} );
								$args['menu-item-type'] = 'custom';
								$args['menu-item-url'] = $url;
							}

							$menu_item_id = wp_update_nav_menu_item( $menu_id, 0, $args );
							$update = $wpdb->query( 'UPDATE ' . $menu_items . ' SET `menu-item-parent-id` = ' . $menu_item_id . ' WHERE `menu-item-parent` = "' . $item->{'menu-item-title'} . '"' );

							$ran_already = get_option( 'menu_check_ran', false );

							if ( false === $ran_already ) {
								$delete = $wpdb->query( 'DELETE FROM ' . $menu_items . ' WHERE `menu-name` = "' . $menu->name . '" AND `menu-item-title` = "' . $item->{'menu-item-title'} . '" AND `menu-item-url` = "' . $item->{'menu-item-url'} . '" AND `menu-item-parent-id` IS NULL' );
							} else {
								$delete = $wpdb->query( 'DELETE FROM ' . $menu_items . ' WHERE `menu-name` = "' . $menu->name . '" AND `menu-item-title` = "' . $item->{'menu-item-title'} . '" AND `menu-item-url` = "' . $item->{'menu-item-url'} . '"' );
							}
						} // End foreach().

						$locations = get_theme_mod( 'nav_menu_locations' );

						$locations[ $menu->placement ] = $menu_id;
						set_theme_mod( 'nav_menu_locations', $locations );

						$remaining_menu_items = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $menu_items . ' WHERE `menu-name` = "' . $menu->name . '"' );
						if ( 0 === (int) $remaining_menu_items ) {
							$delete = $wpdb->query( 'DELETE FROM ' . $menus . ' WHERE `name` = "' . $menu->name . '"' );
						}

						update_option( 'menu_check_ran', true );

					} // End foreach().
				} // End if().
			} // End if().
		} // End foreach().

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

		$wp_filter_field_value = get_option( 'migrate_random_things_wp_filter_field_value', '' );

		if ( false !== strpos( $wp_filter_field_value, ',' ) ) {
			$wp_filter_field_values = explode( ',', $wp_filter_field_value );
			$this->config = array();
			foreach ( $wp_filter_field_values as $key => $value ) {
				$this->config[ $key ] = array(
					'wp_field_to_merge' => get_option( 'migrate_random_things_wp_field_to_merge', '' ),
					'wp_filter_field' => get_option( 'migrate_random_things_wp_filter_field', '' ),
					'wp_filter_field_value' => $value,
					'wp_table' => get_option( 'migrate_random_things_wp_table', '' ),
					'group_by' => get_option( 'migrate_random_things_group_by', '' ),
					'primary_key' => get_option( 'migrate_random_things_primary_key', '' ),
					'items_per_load' => get_option( 'migrate_random_things_items_per_load', '' ),
					'schedule_number' => get_option( 'migrate_random_things_schedule_number', '' ),
					'schedule_unit' => get_option( 'migrate_random_things_schedule_unit', '' ),
				);
			}
		} else {
			$this->config = array(
				0 => array(
					'wp_field_to_merge' => get_option( 'migrate_random_things_wp_field_to_merge', '' ),
					'wp_filter_field' => get_option( 'migrate_random_things_wp_filter_field', '' ),
					'wp_filter_field_value' => get_option( 'migrate_random_things_wp_filter_field_value', '' ),
					'wp_table' => get_option( 'migrate_random_things_wp_table', '' ),
					'group_by' => get_option( 'migrate_random_things_group_by', '' ),
					'primary_key' => get_option( 'migrate_random_things_primary_key', '' ),
					'items_per_load' => get_option( 'migrate_random_things_items_per_load', '' ),
					'schedule_number' => get_option( 'migrate_random_things_schedule_number', '' ),
					'schedule_unit' => get_option( 'migrate_random_things_schedule_unit', '' ),
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

			if ( ! wp_next_scheduled( 'migrate_random_event' ) ) {
				wp_schedule_event( time(), $schedule_frequency, 'migrate_random_event' );
			}

			add_action( 'migrate_random_event',
				array(
					$this,
					'get_things_to_migrate',
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
		wp_clear_scheduled_hook( 'migrate_random_event' );
		delete_option( 'menu_check_ran' );
	}

}
// Instantiate our class
$migrate_random_things = new Migrate_Random_Things();
