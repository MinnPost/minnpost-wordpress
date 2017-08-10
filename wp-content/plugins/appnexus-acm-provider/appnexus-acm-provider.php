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

		add_filter( 'the_content', array( $this, 'insert_inline_ad' ) );

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
			case 'appnexus_head':
				$tags = array();
				foreach ( (array) $ad_tags as $tag ) {
					if ( 'appnexus_head' !== $tag['tag'] ) {
						$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag['tag'] );
						if ( ! empty( $matching_ad_code ) ) {
							array_push( $tags, $tag['tag'] );
						}
					}
				}
				$output_script = "
				<!-- OAS HEADER SETUP begin -->
				<script>
				  /* <![CDATA[ */
				  // Configuration
				  var OAS_url = '" . $this->default_url . "';
				  var OAS_sitepage = 'MP' + window.location.pathname;
				  var OAS_listpos = '" . implode( ',', $tags ) . "';
				  var OAS_query = '';
				  var OAS_target = '_top';
				  
				  var OAS_rns = (Math.random() + \"\").substring(2, 11);
				  document.write('<scr' + 'ipt src=\"' + OAS_url + 'adstream_mjx.ads/' + OAS_sitepage + '/1' + OAS_rns + '@' + OAS_listpos + '?' + OAS_query + '\">' + '<\/script>');
				  
				  function OAS_AD(pos) {
				    if (typeof OAS_RICH != 'undefined') {
				      OAS_RICH(pos);
				    }
				  }
				  /* ]]> */
				</script>  
				<!-- OAS HEADER SETUP end --> 
				";

				break;
			default:
				$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag_id );
				if ( ! empty( $matching_ad_code ) ) {
					$output_script = '
						<div class="appnexus-ad ad-' . sanitize_title( $tag_id ) . '">
							<script>OAS_AD("' . $tag_id . '");</script>
						</div>
					';
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
		// abort if this is not a normal post
		// we should change this to a list of post types
		global $wp_query;
		if ( 'post' !== $wp_query->queried_object->post_type ) {
			return $content;
		}

		/*
		* Abort if this post has the option set to not add ads.
		*/
		if ( 'on' === get_post_meta( $wp_query->queried_object->ID, 'scaip_prevent_shortcode_addition', true ) ) {
			return $content;
		}

		/*
		* Check that there isn't a line starting with `[ad`. If there is, abort! The content must be passed to the shortcode parser without adding more shortcodes. The user may have set a shortcode manually or set the `[ad no]` shortcode.
		*/
		if ( preg_match( '/^\[ad/m', $content ) ) {
			return $content;
		}

		global $ad_code_manager;

		$top_offset = get_option( 'appnexus_acm_provider_auto_embed_top_offset', 1000 );
		$bottom_offset = get_option( 'appnexus_acm_provider_auto_embed_bottom_offset', 400 );
		$tag_id = get_option( 'appnexus_acm_provider_auto_embed_position', 'Middle' );

		$end = strlen( $content );
		$position = $end;

		// if the body is longer than the minimum ad spot find a break.
	    // otherwise place the ad at the end
	    if ( $position > $top_offset ) {
	    	// find the break point
	    	$breakpoints = array(
	    		'</p>' => 4,
	    		'<br />' => 6,
	    		'<br/>' => 5,
	    		'<br>' => 4,
	    		'<!--pagebreak-->' => 0,
	    		'<p>' => 0,
	    	);

	    	// We use strpos on the reversed needle and haystack for speed.
			foreach ( $breakpoints as $point => $offset ) {
				$length = stripos( $content, $point, $top_offset );
				if ( false !== $length ) {
					$position = min( $position, $length + $offset );
				}
			}
	    }

	    // If the position is at or near the end of the article.
	    if ( $position > $end - $bottom_offset ) {
	    	$position = $end;
	    }

	    // get the code for the ad
		$matching_ad_code = $ad_code_manager->get_matching_ad_code( $tag_id );
		if ( ! empty( $matching_ad_code ) ) {
			$output_html = '
				<div class="appnexus-ad ad-' . sanitize_title( $tag_id ) . '">
					<script>OAS_AD("' . $tag_id . '");</script>
				</div>
			';
		}

		// use the function we already have for the placeholder ad
		if ( function_exists( 'minnpost_no_ad_users' ) ) {
			$output_html = minnpost_no_ad_users( $output_html, $tag_id );
		}

		// put it into the post's content
		$content = substr_replace( $content, $output_html, $position, 0 );
		return $content;
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
