<?php
/*
Plugin Name: Appnexus ACM Provider
Plugin URI:
Description:
Version: 0.0.1
Author: Jonathan Stegall
Author URI: http://code.minnpost.com
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: appnexus-acm-provider
*/

class Appnexus_Async_ACM_Provider extends ACM_Provider {

	private $version;
	public $default_domain;
	public $server_path;
	public $default_url;

	public function __construct() {

		$this->version = '0.0.2';

		$this->load_admin();
		$this->default_domain = trim( get_option( 'appnexus_acm_provider_default_domain', '' ) );
		$this->server_path = trim( get_option( 'appnexus_acm_provider_server_path', '' ) );

		if ( '' !== $this->default_domain && '' !== $this->server_path ) {
			$use_https = get_option( 'appnexus_acm_provider_use_https', true );
			if ( true === $use_https || 'yes' === $use_https[0] ) {
				$protocol = 'https://';
			} else {
				$use_https = 'http://';
			}
			$this->default_url = $protocol . $this->default_domain . '/' . $this->server_path . '/';
		}

		// Default ad zones for Appnexus
		$this->ad_tag_ids = array(
			array(
				'tag'       => 'Top',
				'url_vars'  => array(
					'tag'       => 'Top',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'Right1',
				'url_vars'  => array(
					'tag'       => 'Right1',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x01',
				'url_vars'  => array(
					'tag'       => 'x01',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x02',
				'url_vars'  => array(
					'tag'       => 'x02',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x03',
				'url_vars'  => array(
					'tag'       => 'x03',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x04',
				'url_vars'  => array(
					'tag'       => 'x04',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x05',
				'url_vars'  => array(
					'tag'       => 'x05',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x06',
				'url_vars'  => array(
					'tag'       => 'x06',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x07',
				'url_vars'  => array(
					'tag'       => 'x07',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x08',
				'url_vars'  => array(
					'tag'       => 'x08',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x09',
				'url_vars'  => array(
					'tag'       => 'x09',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x10',
				'url_vars'  => array(
					'tag'       => 'x10',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x100',
				'url_vars'  => array(
					'tag'       => 'x100',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x101',
				'url_vars'  => array(
					'tag'       => 'x101',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x102',
				'url_vars'  => array(
					'tag'       => 'x102',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x103',
				'url_vars'  => array(
					'tag'       => 'x103',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x104',
				'url_vars'  => array(
					'tag'       => 'x104',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x105',
				'url_vars'  => array(
					'tag'       => 'x105',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x106',
				'url_vars'  => array(
					'tag'       => 'x106',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x107',
				'url_vars'  => array(
					'tag'       => 'x107',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x108',
				'url_vars'  => array(
					'tag'       => 'x108',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x109',
				'url_vars'  => array(
					'tag'       => 'x109',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'x110',
				'url_vars'  => array(
					'tag'       => 'x110',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'Middle',
				'url_vars'  => array(
					'tag'       => 'Middle',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'Middle3',
				'url_vars'  => array(
					'tag'       => 'Middle3',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'BottomRight',
				'url_vars'  => array(
					'tag'       => 'BottomRight',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'TopRight',
				'url_vars'  => array(
					'tag'       => 'TopRight',
				),
				'enable_ui_mapping' => true,
			),
			array(
				'tag'       => 'TopLeft',
				'url_vars'  => array(
					'tag'       => 'TopLeft',
				),
				'enable_ui_mapping' => true,
			),

			// An extra, special tag to make sure the <head> gets the output we need it to
			array(
				'tag'           => 'appnexus_head',
				'url_vars'      => array(),
			),

		);

		// Default fields for AppNexus
		$this->ad_code_args = array(
			array(
				'key'       => 'tag',
				'label'     => __( 'Tag', 'ad-code-manager' ),
				'editable'  => true,
				'required'  => true,
				'type'      => 'select',
				'options'   => array(
					// This is added later, through 'acm_ad_code_args' filter
				),
			),
			array(
				'key'       => 'tag_id',
				'label'     => __( 'Tag ID', 'ad-code-manager' ),
				'editable'  => true,
				'required'  => true,
			),
			array(
				'key'       => 'tag_name',
				'label'     => __( 'Tag Name', 'ad-code-manager' ),
				'editable'  => true,
				'required'  => true,
			),
		);

		add_filter( 'the_content', array( $this, 'insert_inline_ad' ), 10 );

		add_filter( 'acm_ad_code_args', array( $this, 'filter_ad_code_args' ) );
		add_filter( 'acm_output_html', array( $this, 'filter_output_html' ), 10, 2 );

		add_filter( 'acm_display_ad_codes_without_conditionals', '__return_true' );

		add_action( 'wp_head', array( $this, 'action_wp_head' ) );

		$this->whitelisted_script_urls = array( $this->default_domain );

		parent::__construct();
	}

	/**
	 * Register the tags available for mapping in the UI
	 */
	public function filter_ad_code_args( $ad_code_args ) {
		global $ad_code_manager;

		foreach ( $ad_code_args as $tag => $ad_code_arg ) {

			if ( 'tag' !== $ad_code_arg['key'] ) {
				continue;
			}

			// Get all of the tags that are registered, and provide them as options
			foreach ( (array) $ad_code_manager->ad_tag_ids as $ad_tag ) {
				if ( isset( $ad_tag['enable_ui_mapping'] ) && $ad_tag['enable_ui_mapping'] ) {
					$ad_code_args[ $tag ]['options'][ $ad_tag['tag'] ] = $ad_tag['tag'];
				}
			}
		}
		return $ad_code_args;
	}


	/**
	 * Filter the output HTML to automagically produce the <script> we need
	 */
	public function filter_output_html( $output_html, $tag_id ) {
		global $ad_code_manager;

		$ad_tags = $ad_code_manager->ad_tag_ids;
		$output_script = '';
		switch ( $tag_id ) {
			case 'appnexus_head': // need to get rid of this somehow anyway
				$output_script = '';
				break;
			default:
				$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag_id );
				if ( ! empty( $matching_ad_code ) ) {
					$output_script = $this->get_code_to_insert( $tag_id );
				}
		} // End switch().

		return $output_script;

	}

	/**
	 * Use an inline ad
	 */
	public function insert_inline_ad( $content = '' ) {
		// abort if this is not being called In The Loop.
		if ( ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}
		if ( ! is_single() ) {
			return $content;
		}
		// abort if this is not a normal post
		// we should change this to a list of post types
		global $wp_query;
		if ( 'post' !== $wp_query->queried_object->post_type ) {
			error_log('stop3');
			return $content;
		}

		/*
		* Abort if this post has the option set to not add ads.
		*/
		if ( 'on' === get_post_meta( $wp_query->queried_object->ID, 'scaip_prevent_shortcode_addition', true ) ) {
			error_log('stop4');
			return $content;
		}

		/*
		* Check that there isn't a line starting with `[cms_ad`. If there is, stop the automatic short code adding.
		*/
		if ( preg_match( '/^\[cms_ad/m', $content ) ) {
			return $content;
		}

		global $ad_code_manager;

		$top_offset = get_option( 'appnexus_acm_provider_auto_embed_top_offset', 1000 );
		$bottom_offset = get_option( 'appnexus_acm_provider_auto_embed_bottom_offset', 400 );
		$tag_id = get_option( 'appnexus_acm_provider_auto_embed_position', 'Middle' );

		$start_embeds_after = get_option( 'appnexus_acm_provider_start_embeds_after', 1000 );
		$repeat_embeds_every = get_option( 'appnexus_acm_provider_repeat_embeds_every', 1000 );
		$embeds_until = get_option( 'appnexus_acm_provider_embeds_until', 400 );
		$auto_embeds_name = get_option( 'appnexus_acm_provider_auto_embeds_name', 'x100' );

		$end = strlen( $content );
		$position = $end;

		$scaip_period = get_option( 'scaip_settings_period', 4 );
		$scaip_repetitions = get_option( 'scaip_settings_repetitions', 10 );
		$scaip_minimum_paragraphs = get_option( 'scaip_settings_min_paragraphs', 6 );

		$paragraph_positions = array();
		$last_position = -1;
		$paragraph_end = '</p>';

		// if we don't have an <p> tags, let's skip the ads
		if ( ! stripos( $content, $paragraph_end ) ) {
			//error_log('stop6');
			return $content;
		}

		while ( stripos( $content, $paragraph_end, $last_position + 1 ) !== false ) {
			// Get the position of the end of the next $paragraph_end.
			$last_position = stripos( $content, $paragraph_end, $last_position + 1 ) + 3; // what does the 3 mean?
			$paragraph_positions[] = $last_position;
		}

		// If the total number of paragraphs is bigger than the minimum number of paragraphs
		// It is assumed that $scaip_minimum_paragraphs > $scaip_period * $scaip_repetitions
		if ( count( $paragraph_positions ) >= $scaip_minimum_paragraphs ) {
			// How many shortcodes have been added?
			$n = 0;
			// Safety check number: stores the position of the last insertion.
			$previous_position = 0;
			$i = 0;
			while ( $i < count( $paragraph_positions ) && $n <= $scaip_repetitions ) {
				// Modulo math to only output shortcode after $scaip_period closing paragraph tags.
				// +1 because of zero-based indexing.
				if ( 0 === ( $i + 1 ) % $scaip_period && isset( $paragraph_positions[ $i ] ) ) {
					// make a shortcode using the number of the shorcode that will be added.
					// Using "" here so we can interpolate the variable.
					$shortcode = $this->get_code_to_insert( 'x' . ( 100 + (int) $n ) );
					//$shortcode = "[cms_ad:$n]";
					$position = $paragraph_positions[ $i ] + 1;
					// Safety check:
					// If the position we're adding the shortcode is at a lower point in the story than the position we're adding,
					// Then something has gone wrong and we should insert no more shortcodes.
					if ( $position > $previous_position ) {
						$content = substr_replace( $content, $shortcode, $paragraph_positions[ $i ] + 1, 0 );
						// Increase the saved last position.
						$previous_position = $position;
						// Increment number of shortcodes added to the post.
						$n++;
					}
					// Increase the position of later shortcodes by the length of the current shortcode.
					foreach ( $paragraph_positions as $j => $pp ) {
						if ( $j > $i ) {
							$paragraph_positions[ $j ] = $pp + strlen( $shortcode );
						}
					}
				}
				$i++;
			}
		}
		return $content;

	}

	public function get_code_to_insert( $tag_id ) {
		// get the code to insert
		global $ad_code_manager;
		$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag_id );
		if ( ! empty( $matching_ad_code ) ) {

			$output_html = '<iframe src="' . $this->default_url . 'adstream_sx.ads/MP' . strtok( $_SERVER['REQUEST_URI'], '?' ) . '1' . mt_rand() . '@' . $tag_id . '" frameborder="0"></iframe>';

			$output_html = apply_filters( 'easy_lazy_loader_html', $output_html );

			$output_html = '<div class="appnexus-ad ad-' . sanitize_title( $tag_id ) . '">' . $output_html . '</div>';

			/*if ( 4 === strlen( $tag_id ) && 0 === strpos( $tag_id, 'x10' ) ) {
				$output_html = '
					<div class="appnexus-ad ad-' . sanitize_title( $tag_id ) . '">
						<code><!--
						OAS_AD("' . $tag_id . '");
						//-->
						</code>
					</div>
				';
			}*/
		}
		// use the function we already have for the placeholder ad
		if ( function_exists( 'acm_no_ad_users' ) ) {
			if ( ! isset( $output_html ) ) {
				$output_html = '';
			}
			$output_html = acm_no_ad_users( $output_html, $tag_id );
		}
		return $output_html;
	}


	/**
	 * Add the initialization code in the head
	 */
	public function action_wp_head() {
		do_action( 'acm_tag', 'appnexus_head' );
	}

	/**
	 * Allow ad sizes to be defined as arrays or as basic width x height.
	 * The purpose of this is to solve for flex units, where multiple ad
	 * sizes may be required to load in the same ad unit.
	 */
	public function parse_ad_tag_sizes( $url_vars ) {
		if ( empty( $url_vars ) ) {
			return;
		}

		$unit_sizes_output = '';
		if ( ! empty( $url_vars['sizes'] ) ) {
			foreach ( $url_vars['sizes'] as $unit_size ) {
				$unit_sizes_output[] = array(
					(int) $unit_size['width'],
					(int) $unit_size['height'],
				);
			}
		} else { // fallback for old style width x height
			$unit_sizes_output = array(
				(int) $url_vars['width'],
				(int) $url_vars['height'],
			);
		}
		return $unit_sizes_output;
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
	}

	/**
	* Default display for <input> fields
	*
	* @param array $args
	*/
	public function create_admin_menu() {
		add_options_page( __( 'AppNexus Ad Settings', 'appnexus-acm-provider' ), __( 'AppNexus Ad Settings', 'appnexus-acm-provider' ), 'manage_options', 'appnexus-acm-provider', array( $this, 'show_admin_page' ) );
	}

	/**
	* Display a Settings link on the main Plugins page
	*
	* @return array $links
	*/
	public function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$settings = '<a href="' . get_admin_url() . 'options-general.php?page=appnexus-acm-provider">' . __( 'Settings', 'appnexus-acm-provider' ) . '</a>';
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
			<h1><?php _e( get_admin_page_title() , 'appnexus-acm-provider' ); ?></h1>
			<div id="main">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'appnexus-acm-provider' ) . do_settings_sections( 'appnexus-acm-provider' );
					?>
					<?php submit_button( __( 'Save settings', 'appnexus-acm-provider' ) ); ?>
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
		$page = 'appnexus-acm-provider';
		$section = 'appnexus-acm-provider';
		$input_callback = array( $this, 'display_input_field' );
		$checkbox_callback = array( $this, 'display_checkboxes' );
		add_settings_section( $page, null, null, $page );

		$settings = array(
			'default_domain' => array(
				'title' => __( 'Default Domain', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'The ad server domain', 'appnexus-acm-provider' ),
				),
			),
			'use_https' => array(
				'title' => __( 'Use HTTPS?', 'appnexus-acm-provider' ),
				'callback' => $checkbox_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'checkboxes',
					'desc' => 'Whether to use HTTPS on the domain',
					'items' => array(
						'yes' => array(
							'text' => 'Yes',
							'id' => 'yes',
							'desc' => '',
							'default' => true,
						),
					),
				),
			),
			'server_path' => array(
				'title' => __( 'Server Path', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'The server path, if applicable.', 'appnexus-acm-provider' ),
				),
			),
			'auto_embed_position' => array(
				'title' => __( 'Auto embed position', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'Position for the in-story ad, if it is not otherwise included.', 'appnexus-acm-provider' ),
				),
			),
			'auto_embed_top_offset' => array(
				'title' => __( 'Auto embed top character offset', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'How many characters from the top of the story to put the ad.', 'appnexus-acm-provider' ),
				),
			),
			'auto_embed_bottom_offset' => array(
				'title' => __( 'Auto embed bottom character offset', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'How many characters from the bottom of the story to put the ad.', 'appnexus-acm-provider' ),
				),
			),
			'start_embeds_after' => array(
				'title' => __( 'Start embed ads after', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'How many characters from the top of the story to start embed ads.', 'appnexus-acm-provider' ),
				),
			),
			'repeat_embeds_every' => array(
				'title' => __( 'Repeat embed ads every', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'Repeat embed ads this many characters in a story.', 'appnexus-acm-provider' ),
				),
			),
			'embeds_until' => array(
				'title' => __( 'Embed ads until', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'Stop embedding ads this many characters from the end of the story.', 'appnexus-acm-provider' ),
				),
			),
			'auto_embeds_name' => array(
				'title' => __( 'Auto embed ad name', 'appnexus-acm-provider' ),
				'callback' => $input_callback,
				'page' => $page,
				'section' => $section,
				'args' => array(
					'type' => 'text',
					'desc' => __( 'This is the beginning of the auto increment.', 'appnexus-acm-provider' ),
				),
			),
		);

		foreach ( $settings as $key => $attributes ) {
			$id = 'appnexus_acm_provider_' . $key;
			$name = 'appnexus_acm_provider_' . $key;
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
	* Default display for <input> fields
	*
	* @param array $args
	*/
	public function display_input_field( $args ) {
		$type   = $args['type'];
		$id     = $args['label_for'];
		$name   = $args['name'];
		$desc   = $args['desc'];
		$value  = esc_attr( get_option( $id, '' ) );
		echo '<input type="' . $type . '" value="' . $value . '" name="' . $name . '" id="' . $id . '"
		class="regular-text code" />';
		if ( '' !== $desc ) {
			echo '<p class="description">' . $desc . '</p>';
		}
	}

	/**
	* Default display for <input type="checkbox"> fields
	*
	* @param array $args
	*/
	public function display_checkboxes( $args ) {
		$type = 'checkbox';
		$name = $args['name'];
		$options = get_option( $name, array() );
		foreach ( $args['items'] as $key => $value ) {
			$text = $value['text'];
			$id = $value['id'];
			$desc = $value['desc'];
			$checked = '';
			if ( is_array( $options ) && in_array( (string) $key, $options, true ) ) {
				$checked = 'checked';
			} elseif ( is_array( $options ) && empty( $options ) ) {
				if ( isset( $value['default'] ) && true === $value['default'] ) {
					$checked = 'checked';
				}
			}
			echo sprintf( '<div class="checkbox"><label><input type="%1$s" value="%2$s" name="%3$s[]" id="%4$s"%5$s>%6$s</label></div>',
				esc_attr( $type ),
				esc_attr( $key ),
				esc_attr( $name ),
				esc_attr( $id ),
				esc_html( $checked ),
				esc_html( $text )
			);
			if ( '' !== $desc ) {
				echo sprintf( '<p class="description">%1$s</p>',
					esc_html( $desc )
				);
			}
		}
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
