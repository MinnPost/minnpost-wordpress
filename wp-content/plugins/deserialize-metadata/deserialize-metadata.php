<?php
/*
Plugin Name: Deserialize Metadata
Plugin URI: https://wordpress.org/plugins/deserialize-metadata/
Description: When migrating from another system (i.e. Drupal), WordPress can require data that is currently serialized to be unserialized and stored in its own WordPress-specific tables/columns. This plugin can look for such data, and deserialize and store it, based on the plugin settings.
Version: 0.0.8
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
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
	* @var array
	*/
	private $wp_tables;

	/**
	 * @var object
	 *
	 */
	static $instance = null;

	/**
	* Load the static $instance property that holds the instance of the class.
	*
	* @return object
	*
	*/
	static public function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Deserialize_Metadata();
		}
		return self::$instance;
	}

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version   = '0.0.8';
		$this->config    = array();
		$this->wp_tables = array(
			'wp_posts'    => 'wp_posts',
			'wp_postmeta' => 'wp_postmeta',
		);

		$this->load_admin();

		add_filter( 'cron_schedules', array( $this, 'set_schedule_frequency' ) );

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
			if ( ( 'deserialize_metadata_schedule_number' === $option_name && $old_value !== $value ) || ( 'deserialize_metadata_schedule_unit' === $option_name && $old_value !== $value ) ) {
				// delete the old schedule and create the new one - this means user changed how often it should run
				$this->deactivate();
				$this->schedule();
			}
		}, 10, 3);
		add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 5 );
	}

	/**
	* Create WordPress admin options page
	*
	*/
	public function create_admin_menu() {
		add_options_page( __( 'Deserialize Metadata', 'deserialize-metadata' ), __( 'Deserialize Metadata', 'deserialize-metadata' ), 'manage_options', 'deserialize-metadata', array( $this, 'show_admin_page' ) );
	}

	/**
	* Display a Settings link on the main Plugins page
	*
	* @param array $links
	* @param string $file
	*
	* @return array $links
	*/
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$settings = '<a href="' . get_admin_url() . 'options-general.php?page=deserialize-metadata">' . __( 'Settings', 'deserialize-metadata' ) . '</a>';
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
			<h1><?php _e( get_admin_page_title() , 'deserialize-metadata' ); ?></h1>
			<div id="main">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'deserialize-metadata' ) . do_settings_sections( 'deserialize-metadata' );
					$deserialize_maps = get_option( 'deserialize_metadata_maps', '' );
					?>

					<table class="wp-list-table widefat striped fields">
						<thead>
							<tr>
								<th class="column-map_key"><?php _e( 'Map Key', 'deserialize-metadata' ); ?></th>
								<th class="column-wp_table"><?php _e( 'WordPress Table', 'deserialize-metadata' ); ?></th>
								<th class="column-wp_column"><?php _e( 'WordPress Column', 'deserialize-metadata' ); ?></th>
								<th class="column-unique"><?php _e( 'Unique?', 'deserialize-metadata' ); ?></th>
								<th class="column-actions"><?php _e( 'Actions', 'deserialize-metadata' ); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="5"><p class="description">The <strong>unique</strong> checkbox refers to whether or not you want the key to stay unique. When checked, the field will not be added if the given key already exists among custom fields of the specified post.</p></td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							if ( isset( $deserialize_maps ) && '' !== $deserialize_maps ) {
								foreach ( $deserialize_maps as $key => $value ) {
									?>
									<tr class="repeating">
										<td class="column-map_key">
											<input name="deserialize_metadata_maps[<?php echo $key; ?>][map_key]" type="text" value="<?php echo $value['map_key']; ?>" />
										</td>
										<td class="column-wp_table">
											<select name="deserialize_metadata_maps[<?php echo $key; ?>][wp_table]">
												<option value=""><?php _e( 'Choose table', 'deserialize-metadata' ); ?></option>
												<?php
												foreach ( $this->wp_tables as $wp_key => $wp_value ) {
													if ( $value['wp_table'] === $wp_key ) {
														$selected = ' selected';
													} else {
														$selected = '';
													}
													?>
													<option value="<?php echo $wp_key; ?>"<?php echo $selected; ?>><?php echo $wp_value; ?></option>
												<?php } ?>
											</select>
										</td>
										<td class="column-wp_column">
											<input name="deserialize_metadata_maps[<?php echo $key; ?>][wp_column]" type="text" value="<?php echo $value['wp_column']; ?>" />
										</td>
										<td class="column-unique">
											<?php
											if ( isset( $value['unique'] ) && '1' === $value['unique'] ) {
												$checked = ' checked';
											} else {
												$checked = '';
											}
											?>
											<input name="deserialize_metadata_maps[<?php echo $key; ?>][unique]" type="checkbox" value="1" <?php echo $checked; ?> />
										</td>
										<td class="column-actions">
											<a href="#" class="delete-this"><?php _e( 'Delete', 'deserialize-metadata' ); ?></a>
										</td>
									</tr>
									<?php
								}
							} else {
								?>
								<tr class="repeating">
									<td class="column-map_key">
										<input name="deserialize_metadata_maps[0][map_key]" type="text" value="" />
									</td>
									<td class="column-wp_table">
										<select name="deserialize_metadata_maps[0][wp_table]">
											<option value=""><?php _e( 'Choose table', 'deserialize-metadata' ); ?></option>
											<?php
											foreach ( $this->wp_tables as $key => $value ) {
												?>
												<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
											<?php } ?>
										</select>
									</td>
									<td class="column-wp_column">
										<input name="deserialize_metadata_maps[0][wp_column]" type="text" value="" />
									</td>
									<td class="column-unique">
										<input type="checkbox" name="deserialize_metadata_maps[0][unique]" value="1" />
									</td>
									<td>&nbsp;</td>
								</tr>
								<?php
							}
							?>
							<tr>
								<td colspan="4">
									<p><a href="#" class="repeat"><?php _e( 'Add Another Map', 'deserialize-metadata' ); ?></a></p>
								</td>
						</tbody>
					</table>

					<?php submit_button( __( 'Save settings', 'deserialize-metadata' ) ); ?>

				</form>

				<script>
				// Add a new repeating section
				var attrs = ['id', 'name'];
				function resetAttributeNames(section) { 
					var tags = section.find('input'), idx = section.index();
					tags.each(function() {
						var $this = jQuery(this);
						jQuery.each(attrs, function(i, attr) {
							var attr_val = $this.attr(attr);
							if (attr_val) {
								$this.attr(attr, attr_val.replace(/\[(\d+)\]/g, '['+(idx)+']'));
								$this.attr(attr, attr_val.replace(/\[(\d+)\]/g, '['+(idx)+']'));
							}
						});
					});
				}
												   
				jQuery('.repeat').click(function(e){
					e.preventDefault();
					var lastRepeatingGroup = jQuery('.repeating').last();
					var cloned = lastRepeatingGroup.clone(true);
					cloned.insertAfter(lastRepeatingGroup);
					cloned.find('input').val('');
					cloned.find('select').val('');
					cloned.find('input:radio').attr('checked', false);
					resetAttributeNames(cloned);
				});

				jQuery('.delete-this').click(function(e){
					e.preventDefault(); 
					jQuery(this).parent().parent('tr').remove();
				});

				</script>

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
		$page            = 'deserialize-metadata';
		$section         = 'deserialize-metadata';
		$input_callback  = array( $this, 'display_input_field' );
		$select_callback = array( $this, 'display_select' );
		add_settings_section( $page, null, null, $page );

		// temp uncomment for testing when we don't want to wait for schedule to run
		//$this->get_posts_with_serialized_metadata();

		$settings = array(
			'wp_imported_field' => array(
				'title'    => __( 'Imported Field', 'deserialize-metadata' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'The name of the imported field in the database', 'deserialize-metadata' ),
				),
			),
			'post_type'         => array(
				'title'    => __( 'Post Type', 'deserialize-metadata' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'What type of post uses this metadata?', 'deserialize-metadata' ),
				),
			),
			'post_status'       => array(
				'title'    => __( 'Post Status', 'deserialize-metadata' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'Post statuses to match', 'deserialize-metadata' ),
				),
			),
			'posts_per_page'    => array(
				'title'    => __( 'Posts Per Page', 'deserialize-metadata' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'text',
					'desc' => __( 'Maximum posts the query should load', 'deserialize-metadata' ),
				),
			),
			'schedule_number'   => array(
				'title'    => __( 'Run schedule every', 'deserialize-metadata' ),
				'callback' => $input_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type' => 'number',
					'desc' => '',
				),
			),
			'schedule_unit'     => array(
				'title'    => __( 'Time unit', 'deserialize-metadata' ),
				'callback' => $select_callback,
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type'  => 'select',
					'desc'  => '',
					'items' => array(
						'minutes' => __( 'Minutes', 'deserialize-metadata' ),
						'hours'   => __( 'Hours', 'deserialize-metadata' ),
						'days'    => __( 'Days', 'deserialize-metadata' ),
					),
				),
			),
		);

		foreach ( $settings as $key => $attributes ) {
			$id       = 'deserialize_metadata_' . $key;
			$name     = 'deserialize_metadata_' . $key;
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
		register_setting( $section, 'deserialize_metadata_maps' );

	}

	/**
	* Convert the schedule frequency from the admin settings into an array
	* interval must be in seconds for the class to use it
	*/
	public function set_schedule_frequency( $schedules ) {

		$name = '';
		// can try to get this value somehow later
		if ( '' !== $name ) {
			$name = '_' . $name;
		}

		// create an option in the core schedules array for each one the plugin defines
		$schedule_number = get_option( 'deserialize_metadata' . $name . '_schedule_number', '' );
		$schedule_unit   = get_option( 'deserialize_metadata' . $name . '_schedule_unit', '' );

		if ( '' === $schedule_number || '' === $schedule_unit ) {
			return $schedules;
		}

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

		$schedules[ $key ] = array(
			'interval' => $seconds * $schedule_number,
			'display'  => 'Every ' . $schedule_number . ' ' . $schedule_unit,
		);

		$this->schedule_frequency = $key;

		return $schedules;

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

		$schedule_number = get_option( 'deserialize_metadata' . $name . '_schedule_number', '' );
		$schedule_unit   = get_option( 'deserialize_metadata' . $name . '_schedule_unit', '' );

		if ( '' === $schedule_number || '' === $schedule_unit ) {
			return '';
		}

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
			echo '<p><code>' . __( 'Defined in wp-config.php', 'deserialize-metadata' ) . '</code></p>';
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
		echo '<select name="' . $name . '" id="' . $id . '"><option value="">' . __( 'Choose an option', 'deserialize-metadata' ) . '</option>';
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
	 * Create an action on plugin init so we can gather some config items for this plugin from the wp settings
	 * this sets the $this->config variable
	 *
	 * @return void
	 */
	private function config() {

		$maps = get_option( 'deserialize_metadata_maps', '' );
		if ( '' === $maps ) {
			return;
		}
		$config_maps = array();
		foreach ( $maps as $key => $map ) {
			$key                   = $map['map_key'];
			$config_maps[ "$key" ] = array(
				'wp_table'  => $map['wp_table'],
				'wp_column' => $map['wp_column'],
				'unique'    => isset( $map['unique'] ) ? $map['unique'] : 0,
			);
		}

		// this would theoretically allow us to support multiple imported, serialized fields if it became necessary
		// and the ui could support it
		$this->config = array(
			0 => array(
				'wp_imported_field' => get_option( 'deserialize_metadata_wp_imported_field', '' ),
				'post_type'         => get_option( 'deserialize_metadata_post_type', '' ),
				'post_status'       => get_option( 'deserialize_metadata_post_status', '' ),
				'posts_per_page'    => get_option( 'deserialize_metadata_posts_per_page', '' ),
				'schedule_number'   => get_option( 'deserialize_metadata_schedule_number', '' ),
				'schedule_unit'     => get_option( 'deserialize_metadata_schedule_unit', '' ),
				'maps'              => $config_maps,
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
	 * Scheule function
	 * This registers the method to get the WordPress data that needs to be unserialized
	 *
	 * @return void
	 */
	public function schedule() {
		foreach ( $this->config as $key => $value ) {
			// this would need to change to allow different schedules
			$schedule_frequency = $this->get_schedule_frequency_key();

			if ( ! wp_next_scheduled( 'deserialize_event' ) ) {
				wp_schedule_event( time(), $schedule_frequency, 'deserialize_event' );
			}

			add_action( 'deserialize_event', array( $this, 'get_posts_with_serialized_metadata' ) );
		}
	}

	/**
	 * Deactivate function
	 * This stops the regular repetition of the task
	 *
	 * @return void
	 */
	public function deactivate() {
		wp_clear_scheduled_hook( 'deserialize_event' );
	}

	/**
	 * Get WordPress posts that match our criteria for serialized metadata
	 * This also calls the create and delete methods to handle what to do with the data
	 *
	 * @return void
	 */
	public function get_posts_with_serialized_metadata() {
		foreach ( $this->config as $config ) {
			$offset = get_option( 'deserialize_metadata_last_post_checked', '0' );
			$key    = $config['wp_imported_field'];
			$maps   = $config['maps'];
			$args   = array(
				'post_type'      => $config['post_type'],
				'post_status'    => $config['post_status'],
				'posts_per_page' => (int) $config['posts_per_page'],
				//'offset'       => (int) $offset,
				'orderby'        => 'ID',
				'order'          => 'DESC',
				'meta_query'     => array(
					array(
						'key' => $key,
					),
				),
			);
			$query  = new WP_Query( $args );
			if ( $query->have_posts() ) {
				$count = $offset;
				while ( $query->have_posts() ) {
					$query->the_post();
					$post_id  = $query->post->ID;
					$metadata = get_post_meta( $post_id, $key, true );
					$this->create_fields( $post_id, $metadata, $maps );
					$this->delete_combined_field( $post_id, $key );
					$count++;
				}
				//error_log( 'new offset is ' . $config['posts_per_page'] + $count );
				update_option( 'deserialize_metadata_last_post_checked', $config['posts_per_page'] + $count );
			}
		}

	}

	/**
	 * Create fields to match the structure WordPress wants
	 * This structure is defined in $this->config
	 *
	 * @return void
	 */
	public function create_fields( $post_id, $metadata, $maps ) {
		if ( is_array( $metadata ) && ! empty( $metadata ) ) {
			foreach ( $metadata as $key => $value ) { // for each field, get its name and value
				if ( array_key_exists( $key, $maps ) ) { // if the field is in the settings list
					if ( 'wp_postmeta' === $maps[ $key ]['wp_table'] && '' !== $value && null !== $value ) { // if it belongs in the postmeta table
						// check to see if it has a value
						$pre_existing_value = get_post_meta( $post_id, $maps[ $key ]['wp_column'], true );
						if ( ! empty( $pre_existing_value ) ) {
							//error_log( 'meta field already exists on this post. the existing value is ' . $pre_existing_value . '. compare with new value of ' . $value );
						} else {
							add_post_meta( $post_id, $maps[ $key ]['wp_column'], $value, $maps[ $key ]['unique'] );
						}
					} elseif ( 'wp_posts' === $maps[ $key ]['wp_table'] && '' !== $value && null !== $value ) { // if it belongs in the post table
						$pre_existing_post  = get_post( $post_id, 'ARRAY_A' );
						$pre_existing_value = isset( $pre_existing_post[ $maps[ $key ]['wp_column'] ] ) ? $pre_existing_post[ $maps[ $key ]['wp_column'] ] : '';
						if ( ! empty( $pre_existing_value ) ) {
							//error_log( 'the field already exists on this post. the value is ' . $pre_existing_value . '. compare with new value of ' . $value );
						} else {
							$post = array(
								'ID'                       => $post_id,
								$maps[ $key ]['wp_column'] => $value,
							);
							wp_update_post( $post );
						}
					}
				}
			}
		}
	}

	/**
	 * Delete the original, serialized field so we don't trigger it if the plugin is activated again
	 *
	 * @return void
	 */
	public function delete_combined_field( $post_id, $key ) {
		delete_post_meta( $post_id, $key );
	}

	/// end class
}
// Instantiate our class
$deserialize_metadata = Deserialize_Metadata::get_instance();
