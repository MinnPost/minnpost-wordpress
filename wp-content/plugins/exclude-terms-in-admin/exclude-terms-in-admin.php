<?php
/*
Plugin Name: Exclude Terms in Admin
Description: Exclude specified terms from the edit and new post screens
Version: 0.0.3
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: exclude-terms-admin
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class Exclude_Terms_Admin {

	/**
	* @var string
	* The plugin version
	*/
	private $version;

	/**
	* @var string
	* The plugin slug
	*/
	private $slug;

	/**
	* @var string
	* The name of the settings section
	*/
	private $settings_section;

	/**
	* @var string
	* The prefix for saving options
	*/
	private $option_prefix;

	/**
	 * @var object
	 * Static property to hold an instance of the class; this seems to make it reusable
	 *
	 */
	static $instance = null;

	/**
	* Load the static $instance property that holds the instance of the class.
	* This instance makes the class reusable by other plugins
	*
	* @return object
	*
	*/
	static public function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Exclude_Terms_Admin();
		}
		return self::$instance;
	}

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version          = '0.0.3';
		$this->slug             = 'exclude-terms-admin';
		$this->settings_section = 'exclude_terms_settings';
		$this->option_prefix    = 'exclude_terms_admin_';

		$this->see_hidden_capability = 'see_hidden_terms';
		$this->hidden_terms          = "'55584', '55579', '55586', '55572', '55571', '55595', '55607', '55608', '55624', '55588', '55602', '55594', '55604', '55603', '55587', '55620', '55580', '55606', '95311', '55599', '55581', '55616', '55631', '55605', '55600', '55593', '55583', '55614', '55621', '55610', '55574', '55573', '55612', '55601'";

		$this->add_actions();
	}

	/**
	 * Add plugin actions
	 *
	 * @return void
	 */
	private function add_actions() {
		add_filter( 'list_terms_exclusions', array( $this, 'list_terms_exclusions' ), 10, 2 );
		add_shortcode( 'return_excluded_terms', array( $this, 'return_excluded_terms' ) );
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_settings_form' ) );
	}

	/**
	* Exclude terms from post edit screen
	*
	* @return string $exclusions
	*/
	public function list_terms_exclusions( $exclusions, $args ) {
		global $pagenow;
		$user_can_see = current_user_can( $this->see_hidden_capability );
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) && ! $user_can_see ) {
			$categories  = $this->get_excluded_categories( 'string' );
			$exclusions .= " AND t.term_id NOT IN ( $categories )";
		}
		return $exclusions;
	}

	/**
	* Return excluded terms in a shortcode. This lets them be assigned to a variable.
	*
	* @param array $attributes
	* @return string $hidden_terms
	*
	*/
	public function return_excluded_terms( $attributes, $content = null ) {
		return $this->get_excluded_categories( 'string' );
	}

	/**
	 * Get the category IDs that are excluded, either as an array or a string
	 *
	 * @param $format
	 * @return $categories
	 */
	private function get_excluded_categories( $format = '' ) {
		$categories     = array();
		$excluded_value = get_option( $this->option_prefix . 'categories_to_exclude', array() );
		if ( ! empty( $excluded_value ) ) {
			$exclude_ids = $excluded_value;
		} else {
			$exploded    = explode( ', ', $this->hidden_terms );
			$exclude_ids = array();
			foreach ( $exploded as $key => $exclude_category ) {
				$exclude_ids[] = (string) str_replace( "'", '', $exclude_category );
			}
		}
		if ( 'array' === $format ) {
			$categories = $exclude_ids;
		} elseif ( 'string' === $format ) {
			$category_ids = implode(
				', ',
				array_map(
					function( $string ) {
						return "'" . $string . "'";
					},
					$exclude_ids
				)
			);
			$categories   = $category_ids;
		}
		return $categories;
	}

	/**
	* Create WordPress admin options page
	*
	*/
	public function create_admin_menu() {
		$capability = 'manage_categories';
		add_submenu_page(
			'edit.php',
			__( 'Categories to Exclude', 'exclude-terms-admin' ),
			__( 'Categories to Exclude', 'exclude-terms-admin' ),
			$capability,
			$this->slug,
			array( $this, 'show_admin_page' ),
		);
	}

	/**
	* Display the admin settings page
	*
	* @return void
	*/
	public function show_admin_page() {
		$section = $this->settings_section;
		echo '<div class="wrap">';
		echo '<h1>' . esc_html( get_admin_page_title() ) . '</h1>';
		require_once( plugin_dir_path( __FILE__ ) . '/templates/settings.php' );
		echo '</div>';
	}

	/**
	* Create default WordPress admin settings form for setting the excluded categories
	*
	*/
	public function admin_settings_form() {
		$page    = $this->settings_section;
		$section = $page;
		$this->fields_settings( $page, $section );
	}

	/**
	* Fields for the Settings tab
	* This runs add_settings_section once, as well as add_settings_field and register_setting methods for each option
	*
	* @param string $page
	* @param string $section
	* @param string $input_callback
	*/
	private function fields_settings( $page, $section ) {
		add_settings_section( $page, ucwords( $page ), null, $page );
		$exclude_settings = array(
			'categories_to_exclude' => array(
				'title'    => __( 'Exclude These Categories From Category Lists', 'exclude-terms-admin' ),
				'callback' => array( $this, 'display_checkboxes' ),
				'page'     => $page,
				'section'  => $section,
				'args'     => array(
					'type'     => 'checkboxes',
					'validate' => 'sanitize_validate_text',
					'items'    => $this->get_category_list(),
				),
			),
		);
		foreach ( $exclude_settings as $key => $attributes ) {
			$id       = $this->option_prefix . $key;
			$name     = $this->option_prefix . $key;
			$title    = $attributes['title'];
			$callback = $attributes['callback'];
			$validate = $attributes['args']['validate'];
			$page     = $attributes['page'];
			$section  = $attributes['section'];
			$class    = isset( $attributes['class'] ) ? $attributes['class'] : '';
			$args     = array_merge(
				$attributes['args'],
				array(
					'title'     => $title,
					'id'        => $id,
					'label_for' => $id,
					'name'      => $name,
					'class'     => $class,
				)
			);
			add_settings_field( $id, $title, $callback, $page, $section, $args );
			register_setting( $page, $id, array( $this, $validate ) );
		}
	}

	/**
	 * Category list for the checkboxes
	 *
	 * @return $items
	 */
	private function get_category_list() {
		$items       = array();
		$exclude_ids = $this->get_excluded_categories( 'array' );
		if ( ! empty( $exclude_ids ) ) {
			$excluded_categories = get_categories(
				array(
					'orderby' => 'name',
					'order'   => 'ASC',
					'include' => $exclude_ids,
				)
			);
			foreach ( $excluded_categories as $excluded_category ) {
				$items[] = array(
					'text'    => $excluded_category->name,
					'id'      => $excluded_category->term_id,
					'default' => true,
				);
			}
		}
		$shown_categories = get_categories(
			array(
				'orderby' => 'name',
				'order'   => 'ASC',
				'exclude' => $exclude_ids,
			)
		);
		foreach ( $shown_categories as $shown_category ) {
			$items[] = array(
				'text'    => $shown_category->name,
				'id'      => $shown_category->term_id,
				'default' => false,
			);
		}
		return $items;
	}

	/**
	* Display for multiple checkboxes
	* Above method can handle a single checkbox as it is
	*
	* @param array $args
	*/
	public function display_checkboxes( $args ) {
		$type        = 'checkbox';
		$name        = $args['name'];
		$exclude_ids = $this->get_excluded_categories( 'array' );
		foreach ( $args['items'] as $key => $value ) {
			$text    = $value['text'];
			$id      = $value['id'];
			$desc    = isset( $value['desc'] ) ? $value['desc'] : '';
			$checked = '';
			if ( in_array( (string) $id, $exclude_ids, true ) ) {
				$checked = ' checked';
			} elseif ( empty( $exclude_ids ) ) {
				if ( isset( $value['default'] ) && true === $value['default'] ) {
					$checked = ' checked';
				}
			}
			echo sprintf(
				'<div class="checkbox"><label><input type="%1$s" value="%2$s" name="%3$s[]" id="%2$s"%4$s>%5$s</label></div>',
				esc_attr( $type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_html( $checked ),
				esc_html( $text )
			);
			if ( '' !== $desc ) {
				echo sprintf(
					'<p class="description">%1$s</p>',
					esc_html( $desc )
				);
			}
		}
	}
}

// Instantiate our class
$exclude_terms_admin = Exclude_Terms_Admin::get_instance();
