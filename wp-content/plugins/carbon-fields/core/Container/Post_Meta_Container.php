<?php

namespace Carbon_Fields\Container;

use Carbon_Fields\Datastore\Meta_Datastore;
use Carbon_Fields\Datastore\Post_Meta_Datastore;
use Carbon_Fields\Exception\Incorrect_Syntax_Exception;

/**
 * Field container designed to extend WordPress custom fields functionality,
 * providing easier user interface to add, edit and delete text, media files,
 * location information and more.
 */
class Post_Meta_Container extends Container {
	/**
	 * ID of the post the container is working with
	 *
	 * @see init()
	 * @var int
	 */
	protected $post_id;

	/**
	 * List of default container settings
	 *
	 * @see init()
	 * @var array
	 */
	public $settings = array(
		'post_type' => array( 'post' ),
		'panel_context' => 'normal',
		'panel_priority' => 'high',
		'show_on' => array(
			'category' => null,
			'template_names' => array(),
			'not_in_template_names' => array(),
			'post_formats' => array(),
			'level_limit' => null,
			'tax_term_id' => null,
			'page_id' => null,
			'parent_page_id' => null,
			'post_path' => null,
		),
	);

	/**
	 * Create a new post meta fields container
	 *
	 * @param string $title Unique title of the container
	 **/
	public function __construct( $title ) {
		parent::__construct( $title );

		if ( ! $this->get_datastore() ) {
			$this->set_datastore( new Post_Meta_Datastore(), $this->has_default_datastore() );
		}
	}

	/**
	 * Check if all required container settings have been specified
	 *
	 * @param array $settings Container settings
	 **/
	public function check_setup_settings( &$settings = array() ) {
		if ( isset( $settings['show_on'] ) ) {
			$invalid_settings = array_diff_key( $settings['show_on'], $this->settings['show_on'] );
			if ( ! empty( $invalid_settings ) ) {
				Incorrect_Syntax_Exception::raise( 'Invalid show_on settings supplied to setup(): "' . implode( '", "', array_keys( $invalid_settings ) ) . '"' );
			}
		}

		if ( isset( $settings['show_on']['post_formats'] ) ) {
			$settings['show_on']['post_formats'] = (array) $settings['show_on']['post_formats'];
		}

		if ( isset( $settings['show_on']['post_path'] ) ) {
			$page = get_page_by_path( $settings['show_on']['post_path'] );

			if ( $page ) {
				$settings['show_on']['page_id'] = $page->ID;
			} else {
				$settings['show_on']['page_id'] = -1;
			}
		}

		// Transform category slug to taxonomy + term slug + term id
		if ( isset( $settings['show_on']['category'] ) ) {
			$term = get_term_by( 'slug', $settings['show_on']['category'], 'category' );

			if ( $term ) {
				$settings['show_on']['tax_slug'] = $term->taxonomy;
				$settings['show_on']['tax_term'] = $term->slug;
				$settings['show_on']['tax_term_id'] = $term->term_id;
			}
		}

		return parent::check_setup_settings( $settings );
	}

	/**
	 * Create DataStore instance, set post ID to operate with (if such exists).
	 * Bind attach() and save() to the appropriate WordPress actions.
	 **/
	public function init() {
		if ( isset( $_GET['post'] ) ) {
			$this->set_post_id( $_GET['post'] );
		}

		// force post_type to be array
		if ( ! is_array( $this->settings['post_type'] ) ) {
			$this->settings['post_type'] = array( $this->settings['post_type'] );
		}

		add_action( 'admin_init', array( $this, '_attach' ) );
		add_action( 'save_post', array( $this, '_save' ) );

		// support for attachments
		add_action( 'add_attachment', array( $this, '_save' ) );
		add_action( 'edit_attachment', array( $this, '_save' ) );
	}

	/**
	 * Perform save operation after successful is_valid_save() check.
	 * The call is propagated to all fields in the container.
	 *
	 * @param int $post_id ID of the post against which save() is ran
	 **/
	public function save( $post_id ) {
		// Unhook action to garantee single save
		remove_action( 'save_post', array( $this, '_save' ) );

		$this->set_post_id( $post_id );

		foreach ( $this->fields as $field ) {
			$field->set_value_from_input();
			$field->save();
		}

		do_action( 'carbon_after_save_custom_fields', $post_id );
		do_action( 'carbon_after_save_post_meta', $post_id );
	}

	/**
	 * Perform checks whether the current save() request is valid
	 * Possible errors are triggering save() for autosave requests
	 * or performing post save outside of the post edit page (like Quick Edit)
	 *
	 * @see is_valid_save_conditions()
	 * @param int $post_id ID of the post against which save() is ran
	 * @return bool
	 **/
	public function is_valid_save( $post_id = 0 ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		} else if ( ! isset( $_REQUEST[ $this->get_nonce_name() ] ) || ! wp_verify_nonce( $_REQUEST[ $this->get_nonce_name() ], $this->get_nonce_name() ) ) { // Input var okay.
			return false;
		} else if ( $post_id < 1 ) {
			return false;
		}

		return $this->is_valid_save_conditions( $post_id );
	}

	/**
	 * Perform checks whether the current save() request is valid
	 * Possible errors are triggering save() for autosave requests
	 * or performing post save outside of the post edit page (like Quick Edit)
	 *
	 * @param int $post_id ID of the post against which save() is ran
	 * @return bool
	 **/
	public function is_valid_save_conditions( $post_id ) {
		$valid = true;
		$post = get_post( $post_id );

		// Check post type
		if ( ! in_array( $post->post_type, $this->settings['post_type'] ) ) {
			return false;
		}

		// Check show on conditions
		foreach ( $this->settings['show_on'] as $condition => $value ) {
			if ( is_null( $value ) ) {
				continue;
			}

			switch ( $condition ) {
				// show_on_post_format
				case 'post_formats':
					if ( empty( $value ) || $post->post_type != 'post' ) {
						break;
					}

					$current_format = get_post_format( $post_id );
					if ( ! in_array( $current_format, $value ) ) {
						$valid = false;
						break 2;
					}

					break;

				// show_on_taxonomy_term or show_on_category
				case 'category':
					$this->show_on_category( $value );

					/* fall-through intended */
				case 'tax_term_id':
					$current_terms = wp_get_object_terms( $post_id, $this->settings['show_on']['tax_slug'], array( 'fields' => 'ids' ) );

					if ( ! is_array( $current_terms ) || ! in_array( $this->settings['show_on']['tax_term_id'], $current_terms ) ) {
						$valid = false;
						break 2;
					}

					break;

				// show_on_level
				case 'level_limit':
					$post_level = count( get_post_ancestors( $post_id ) ) + 1;

					if ( $post_level != $value ) {
						$valid = false;
						break 2;
					}

					break;

				// show_on_page
				case 'page_id':
					if ( $post_id != $value ) {
						$valid = false;
						break 2;
					}

					break;

				// show_on_page_children
				case 'parent_page_id':
					if ( $post->post_parent != $value ) {
						$valid = false;
						break 2;
					}

					break;

				// show_on_template
				case 'template_names':
					if ( empty( $value ) || $post->post_type != 'page' ) {
						break;
					}
					$current_template = get_post_meta( $post_id, '_wp_page_template', 1 );

					if ( ! in_array( $current_template, $value ) ) {
						$valid = false;
						break 2;
					}

					break;

				// hide_on_template
				case 'not_in_template_names':
					if ( empty( $value ) || $post->post_type != 'page' ) {
						break;
					}
					$current_template = get_post_meta( $post_id, '_wp_page_template', 1 );

					if ( in_array( $current_template, $value ) ) {
						$valid = false;
						break 2;
					}

					break;
			}
		}

		return $valid;
	}

	/**
	 * Add meta box for each of the container post types
	 **/
	public function attach() {
		foreach ( $this->settings['post_type'] as $post_type ) {
			add_meta_box(
				$this->id,
				$this->title,
				array( $this, 'render' ),
				$post_type,
				$this->settings['panel_context'],
				$this->settings['panel_priority']
			);
		}

		foreach ( $this->settings['post_type'] as $post_type ) {
			add_filter( "postbox_classes_{$post_type}_{$this->id}", array( $this, 'postbox_classes' ) );
		}
	}

	/**
	 * Classes to add to the post meta box
	 */
	public function postbox_classes( $classes ) {
		$classes[] = 'carbon-box';
		return $classes;
	}

	/**
	 * Perform checks whether the container should be attached during the current request
	 *
	 * @return bool True if the container is allowed to be attached
	 **/
	public function is_valid_attach() {
		global $pagenow;

		if ( $pagenow !== 'post.php' && $pagenow !== 'post-new.php' ) {
			return false;
		}

		// Post types check
		if ( ! empty( $this->settings['post_type'] ) ) {
			$post_type = '';

			if ( $this->post_id ) {
				$post_type = get_post_type( $this->post_id );
			} elseif ( ! empty( $_GET['post_type'] ) ) {
				$post_type = $_GET['post_type'];
			} elseif ( $pagenow === 'post-new.php' ) {
				$post_type = 'post';
			}

			if ( ! $post_type || ! in_array( $post_type, $this->settings['post_type'] ) ) {
				return false;
			}
		}

		// Check show on conditions
		foreach ( $this->settings['show_on'] as $condition => $value ) {
			if ( is_null( $value ) ) {
				continue;
			}

			switch ( $condition ) {
				case 'page_id':
					if ( $value < 1 || $this->post_id != $value ) {
						return false;
					}
					break;
				case 'parent_page_id':
					// Check if such page exists
					if ( $value < 1 ) {
						return false;
					}
					break;
			}
		}

		return true;
	}

	/**
	 * Revert the result of attach()
	 **/
	public function detach() {
		parent::detach();

		remove_action( 'admin_init', array( $this, '_attach' ) );
		remove_action( 'save_post', array( $this, '_save' ) );

		// unregister field names
		foreach ( $this->fields as $field ) {
			$this->drop_unique_field_name( $field->get_name() );
		}
	}

	/**
	 * Output the container markup
	 **/
	public function render() {
		include \Carbon_Fields\DIR . '/templates/Container/post_meta.php';
	}

	/**
	 * Set the post ID the container will operate with.
	 *
	 * @param int $post_id
	 **/
	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
		$this->get_datastore()->set_id( $post_id );
	}

	/**
	 * Show the container only on pages whose parent is referenced by $parent_page_path.
	 *
	 * @param string $parent_page_path
	 * @return object $this
	 **/
	public function show_on_page_children( $parent_page_path ) {
		$page = get_page_by_path( $parent_page_path );

		$this->show_on_post_type( 'page' );

		if ( $page ) {
			$this->settings['show_on']['parent_page_id'] = $page->ID;
		} else {
			$this->settings['show_on']['parent_page_id'] = -1;
		}

		return $this;
	}

	/**
	 * Show the container only on particular page referenced by it's path.
	 *
	 * @param int|string $page page ID or page path
	 * @return object $this
	 **/
	public function show_on_page( $page ) {
		$page_id = absint( $page );

		if ( $page_id && $page_id == $page ) {
			$page_obj = get_post( $page_id );
		} else {
			$page_obj = get_page_by_path( $page );
		}

		$this->show_on_post_type( 'page' );

		if ( $page_obj ) {
			$this->settings['show_on']['page_id'] = $page_obj->ID;
		} else {
			$this->settings['show_on']['page_id'] = -1;
		}

		return $this;
	}

	/**
	 * Show the container only on posts from the specified category.
	 *
	 * @see show_on_taxonomy_term()
	 *
	 * @param string $category_slug
	 * @return object $this
	 **/
	public function show_on_category( $category_slug ) {
		$this->settings['show_on']['category'] = $category_slug;

		return $this->show_on_taxonomy_term( $category_slug, 'category' );
	}

	/**
	 * Show the container only on pages whose template has filename $template_path.
	 *
	 * @param string|array $template_path
	 * @return object $this
	 **/
	public function show_on_template( $template_path ) {
		// Backwards compatibility where only pages support templates
		if ( version_compare( get_bloginfo( 'version' ), '4.7', '<' ) ) {
			$this->show_on_post_type( 'page' );
		}

		if ( is_array( $template_path ) ) {
			foreach ( $template_path as $path ) {
				$this->show_on_template( $path );
			}

			return $this;
		}

		$this->settings['show_on']['template_names'][] = $template_path;

		return $this;
	}

	/**
	 * Hide the container from pages whose template has filename $template_path.
	 *
	 * @param string|array $template_path
	 * @return object $this
	 **/
	public function hide_on_template( $template_path ) {
		if ( is_array( $template_path ) ) {
			foreach ( $template_path as $path ) {
				$this->hide_on_template( $path );
			}
			return $this;
		}

		$this->settings['show_on']['not_in_template_names'][] = $template_path;

		return $this;
	}

	/**
	 * Show the container only on hierarchical posts of level $level.
	 * Levels start from 1 (top level post)
	 *
	 * @param int $level
	 * @return object $this
	 **/
	public function show_on_level( $level ) {
		if ( $level < 0 ) {
			Incorrect_Syntax_Exception::raise( 'Invalid level limitation (' . $level . ')' );
		}

		$this->settings['show_on']['level_limit'] = $level;

		return $this;
	}

	/**
	 * Show the container only on posts which have term $term_slug from the $taxonomy_slug taxonomy.
	 *
	 * @param string $taxonomy_slug
	 * @param string $term_slug
	 * @return object $this
	 **/
	public function show_on_taxonomy_term( $term_slug, $taxonomy_slug ) {
		$term = get_term_by( 'slug', $term_slug, $taxonomy_slug );

		$this->settings['show_on']['tax_slug'] = $taxonomy_slug;
		$this->settings['show_on']['tax_term'] = $term_slug;
		$this->settings['show_on']['tax_term_id'] = $term ? $term->term_id : null;

		return $this;
	}

	/**
	 * Show the container only on posts from the specified format.
	 * Learn more about {@link http://codex.wordpress.org/Post_Formats Post Formats (Codex)}
	 *
	 * @param string|array $post_format Name of the format as listed on Codex
	 * @return object $this
	 **/
	public function show_on_post_format( $post_format ) {
		if ( is_array( $post_format ) ) {
			foreach ( $post_format as $format ) {
				$this->show_on_post_format( $format );
			}
			return $this;
		}

		if ( $post_format === 'standard' ) {
			$post_format = 0;
		}

		$this->settings['show_on']['post_formats'][] = strtolower( $post_format );

		return $this;
	}

	/**
	 * Show the container only on posts from the specified type(s).
	 *
	 * @param string|array $post_types
	 * @return object $this
	 **/
	public function show_on_post_type( $post_types ) {
		$post_types = (array) $post_types;

		$this->settings['post_type'] = $post_types;

		return $this;
	}

	/**
	 * Sets the meta box container context
	 *
	 * @see https://codex.wordpress.org/Function_Reference/add_meta_box
	 * @param string $context ('normal', 'advanced' or 'side')
	 */
	public function set_context( $context ) {
		$this->settings['panel_context'] = $context;

		return $this;
	}

	/**
	 * Sets the meta box container priority
	 *
	 * @see https://codex.wordpress.org/Function_Reference/add_meta_box
	 * @param string $priority ('high', 'core', 'default' or 'low')
	 */
	public function set_priority( $priority ) {
		$this->settings['panel_priority'] = $priority;

		return $this;
	}
} // END Post_Meta_Container
