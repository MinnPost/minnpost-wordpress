<?php
/*
Plugin Name: MinnPost Spills
Description: This plugin creates a sidebar widget and endpoint URL that is able to display posts from a group of categories and/or tags
Version: 0.0.10
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-spills
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

//use Brain\Cortex\Route\RouteCollectionInterface;
//use Brain\Cortex\Route\QueryRoute;

class MinnpostSpills {

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version = '0.0.10';

		$this->load_admin();

		$this->template = 'minnpost-spill.php';

		// register the widget
		add_action(
			'widgets_init',
			function() {
				register_widget( 'MinnpostSpills_Widget' );
			}
		);

	}

	/**
	 * Make sure the admin template is loaded so we can use the category checklist in the settings
	 *
	 */
	private function load_admin() {
		if ( is_admin() ) {
			// This is required to be sure Walker_Category_Checklist class is available
			require_once ABSPATH . 'wp-admin/includes/template.php';
		}
	}
}

// Instantiate our class
$minnpost_spills = new MinnpostSpills();

// widget class
class MinnpostSpills_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct(
			'MinnpostSpills_Widget',
			__( 'MinnPost Spills Widget', 'minnpost-spills-widget' ),
			array(
				'classname'   => 'MinnpostSpills_Widget',
				'description' => __( 'Posts from a group of categories and/or tags.', 'minnpost-spills-widget' ),
			)
		);

		// Register hooks
		add_action( 'admin_enqueue_scripts', array( $this, 'add_suggest_script' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'add_script_config' ), 30 );

	}

	/**
	 * Add script to admin page
	 */
	function add_suggest_script() {
		wp_enqueue_script( 'suggest' );
	}

	/**
	 * add autosuggest script to admin page
	 */
	function add_script_config() {
		?>
		<script>
		function setSuggest() {
			jQuery('.mp-spills-terms').suggest(
				"<?php echo get_bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag",
				{
					multiple:true, 
					multipleSep: ","
				}
			);
		}
		$(document).ready(function() {
			setSuggest();
		});
		$(document).on('widget-updated widget-added', function() {
			setSuggest(); 
		});
		</script>
		<?php
	}

	/**
	* Front-end display of widget.
	*
	* @see WP_Widget::widget()
	*
	* @param array $args     Widget arguments.
	* @param array $instance Saved values from database.
	*/
	public function widget( $args, $instance ) {

		extract( $args );

		$title             = apply_filters( 'widget_title', $instance['title'] );
		$slug              = str_replace( '/', '', $instance['title'] );
		$url               = isset( $instance['url'] ) && '' !== $instance['url'] ? $instance['url'] : '/' . sanitize_title( $slug ) . '/';
		$content           = isset( $instance['content'] ) ? $instance['content'] : '';
		$button_label      = isset( $instance['button_label'] ) ? $instance['button_label'] : '';
		$number_of_posts   = isset( $instance['number_of_posts'] ) ? $instance['number_of_posts'] : '';
		$categories        = $instance['widget_categories'];
		$terms             = $instance['widget_terms'];
		$output_function   = isset( $instance['output_function'] ) ? $instance['output_function'] : '';
		$use_elasticsearch = ( isset( $instance['bypass_elasticsearch'] ) && '' !== $instance['bypass_elasticsearch'] ) ? false : true;

		echo str_replace( 'widget MinnpostSpills-widget', 'm-widget m-minnpost-spills-widget', str_replace( '_Widget"', '-widget ' . sanitize_title( $title ) . '"', $before_widget ) );

		if ( isset( $output_function ) && function_exists( $output_function ) ) {
			$output = $output_function( $before_title, $title, $after_title, $content, $categories, $terms, $use_elasticsearch, $number_of_posts );
		} else {
			$query   = $this->get_spill_posts( $categories, $terms, $use_elasticsearch, $number_of_posts );
			$display = apply_filters( 'minnpost_spills_display_spill_posts', '', $query, $before_title, $title, $after_title, $instance );
			if ( '' === $display ) {
				if ( $title ) {
					$before_title = str_replace( 'widget-title', 'a-widget-title', $before_title );
					echo $before_title . '<a href="' . $url . '">' . $title . '</a>' . $after_title;
				}
				echo '<div class="m-widget-contents>';
				echo $content;

				if ( isset( $query ) && $query->have_posts() ) {
					$output = '';
					while ( $query->have_posts() ) {
						$query->the_post();
						$output .= '<article id="' . get_the_ID() . '" class="m-post m-post-spill">';
						$output .= '<p class="a-post-title a-spill-item-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></p>';
						$output .= '</article>';
					}
					wp_reset_postdata();
					if ( '' !== $button_label ) {
						$output .= '<a href="' . $url . '" class="a-button">' . $button_label . '</a>';
					}
				}
				$output .= '</div>';
			} else {
				$output = $display;
			}
			echo $output;
		}

		echo $after_widget;

	}

	/**
	* Sanitize widget form values as they are saved.
	*
	* @see WP_Widget::update()
	*
	* @param array $new_instance Values just sent to be saved.
	* @param array $old_instance Previously saved values from database.
	*
	* @return array Updated safe values to be saved.
	*/
	public function update( $new_instance, $old_instance ) {

		// need a way to clear the widget's cache when its settings get updated

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( ! empty( $new_instance['url'] ) ) {
			$instance['url'] = $new_instance['url'];
		} else {
			$instance['url'] = '';
		}
		if ( ! empty( $new_instance['content'] ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = '';
		}
		if ( ! empty( $new_instance['button_label'] ) ) {
			$instance['button_label'] = $new_instance['button_label'];
		} else {
			$instance['button_label'] = '';
		}
		if ( ! empty( $new_instance['number_of_posts'] ) ) {
			$instance['number_of_posts'] = $new_instance['number_of_posts'];
		} else {
			$instance['number_of_posts'] = '';
		}
		if ( ! empty( $new_instance['widget_categories'] ) ) {
			$instance['widget_categories'] = $new_instance['widget_categories'];
		} else {
			$instance['widget_categories'] = array();
		}
		if ( ! empty( $new_instance['widget_terms'] ) ) {
			$instance['widget_terms'] = $new_instance['widget_terms'];
		} else {
			$instance['widget_terms'] = array();
		}
		if ( ! empty( $new_instance['output_function'] ) ) {
			$instance['output_function'] = $new_instance['output_function'];
		} else {
			$instance['output_function'] = '';
		}

		if ( ! empty( $new_instance['bypass_elasticsearch'] ) ) {
			$instance['bypass_elasticsearch'] = $new_instance['bypass_elasticsearch'];
		} else {
			$instance['bypass_elasticsearch'] = '';
		}

		return $instance;

	}

	/**
	* Back-end widget form.
	*
	* @see WP_Widget::form()
	*
	* @param array $instance Previously saved values from database.
	*/
	public function form( $instance ) {

		if ( isset( $instance['title'] ) ) {
			$title = sanitize_text_field( $instance['title'] );
		} else {
			$title = '';
		}

		if ( isset( $instance['url'] ) ) {
			$url = esc_url_raw( $instance['url'] );
		} else {
			$url = '';
		}

		if ( isset( $instance['content'] ) ) {
			$content = sanitize_text_field( $instance['content'] );
		} else {
			$content = '';
		}

		if ( isset( $instance['button_label'] ) ) {
			$button_label = sanitize_text_field( $instance['button_label'] );
		} else {
			$button_label = '';
		}

		if ( isset( $instance['number_of_posts'] ) ) {
			$number_of_posts = sanitize_text_field( $instance['number_of_posts'] );
		} else {
			$number_of_posts = '';
		}

		if ( isset( $instance['widget_categories'] ) && '' !== $instance['widget_categories'] ) {
			$categories   = $instance['widget_categories'];
			$category_ids = array();
			foreach ( $categories as $category ) {
				if ( isset( $category ) && ! is_numeric( $category ) ) {
					$id = get_category_by_slug( $category )->term_id;
				} else {
					$id = $category;
				}
				if ( isset( $id ) ) {
					$category_ids[] = $id;
				}
			}
		} else {
			$categories   = false;
			$category_ids = false;
		}

		if ( isset( $instance['widget_terms'] ) && '' !== $instance['widget_terms'] ) {
			$terms = $instance['widget_terms'];
		} else {
			$terms = false;
		}

		if ( isset( $instance['output_function'] ) ) {
			$output_function = sanitize_text_field( $instance['output_function'] );
		} else {
			$output_function = '';
		}

		if ( isset( $instance['bypass_elasticsearch'] ) ) {
			$bypass_elasticsearch = sanitize_text_field( $instance['bypass_elasticsearch'] );
		} else {
			$bypass_elasticsearch = '';
		}

		// Instantiate the walker passing name and id as arguments to constructor
		$category_walker = new Walker_Category_Checklist_Widget(
			$this->get_field_name( 'widget_categories' ),
			$this->get_field_id( 'widget_categories' )
		);

		?>
		<div>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e( 'Link URL:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" type="text" value="<?php echo $url; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" type="text" value="<?php echo $content; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'button_label' ); ?>"><?php _e( 'Button Label:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'button_label' ); ?>" name="<?php echo $this->get_field_name( 'button_label' ); ?>" type="text" value="<?php echo $button_label; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'number_of_posts' ); ?>"><?php _e( 'Number of Posts:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'number_of_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_of_posts' ); ?>" type="tel" value="<?php echo $number_of_posts; ?>" />
			<p class="description"><?php echo __( 'If no value is here, the default number will be 5.', 'minnpost-largo' ); ?></p>
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'widget_categories' ); ?>"><?php _e( 'Categories:' ); ?></label> 
			<?php $checked_ontop = true; ?>
			<ul class="categorychecklist" style="height: 200px; overflow: auto; border: 1px solid #ddd; background: #fdfdfd; padding: 0 0.9em;">
				<?php wp_category_checklist( 0, 0, $category_ids, false, $category_walker, $checked_ontop ); ?>
			</ul>
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'widget_terms' ); ?>"><?php _e( 'Terms:' ); ?></label> 
			<input class="mp-spills-terms widefat" id="<?php echo $this->get_field_id( 'widget_terms' ); ?>" name="<?php echo $this->get_field_name( 'widget_terms' ); ?>" type="text" value="<?php echo is_array( $terms ) ? implode( ',', $terms ) : $terms; ?>" />
		</div>

		<div>
			<label for="<?php echo $this->get_field_id( 'output_function' ); ?>"><?php _e( 'Custom Output Function:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'output_function' ); ?>" name="<?php echo $this->get_field_name( 'output_function' ); ?>" type="text" value="<?php echo $output_function; ?>" />
		</div>
		<div>
			<label class="selectit" style="display: block;" for="<?php echo $this->get_field_id( 'bypass_elasticsearch' ); ?>"><?php _e( 'Bypass Elasticsearch For This Query:' ); ?></label>
			<?php $checked_bypass = ( isset( $bypass_elasticsearch ) && '' !== $bypass_elasticsearch ) ? ' checked' : ''; ?>
			<input id="<?php echo $this->get_field_id( 'bypass_elasticsearch' ); ?>" name="<?php echo $this->get_field_name( 'bypass_elasticsearch' ); ?>" type="checkbox" value="1"<?php echo $checked_bypass; ?>/>
		</div>
		<?php
	}

	/**
	* Load the spills for the posts
	* @param array $categories
	* @param array $terms
	* @param bool $use_elasticsearch
	* @param string $number_of_posts
	* @return $output
	*
	*/
	private function get_spill_posts( $categories = '', $terms = '', $use_elasticsearch = false, $number_of_posts = '' ) {

		if ( file_exists( __DIR__ . '/includes/minnpost-spill-query.php' ) ) {
			require_once __DIR__ . '/includes/minnpost-spill-query.php';
		}
		$args      = minnpost_spill_get_query_args( $categories, $terms, $use_elasticsearch, $number_of_posts );
		$the_query = new WP_Query( $args );
		return $the_query;
	}
}

/**
 * Custom walker to print category checkboxes for widget forms
 */
if ( class_exists( 'Walker_Category_Checklist' ) ) {
	class Walker_Category_Checklist_Widget extends Walker_Category_Checklist {

		private $name;
		private $id;

		function __construct( $name = '', $id = '' ) {
			$this->name = $name;
			$this->id   = $id;
		}

		function start_el( &$output, $cat, $depth = 0, $args = array(), $id = 0 ) {
			extract( $args );
			if ( empty( $taxonomy ) ) {
				$taxonomy = 'category';
			}
			$class   = in_array( $cat->slug, $popular_cats, true ) ? ' class="popular-category"' : '';
			$id      = $this->id . '-' . $cat->slug;
			$checked = checked( in_array( $cat->term_id, $selected_cats, true ), true, false );
			$output .= "\n<li id='{$taxonomy}-{$cat->slug}'$class>"
				. '<label class="selectit"><input value="'
				. $cat->slug . '" type="checkbox" name="' . $this->name
				. '[]" id="in-' . $id . '"' . $checked
				. disabled( empty( $args['disabled'] ), false, false ) . ' /> '
				. esc_html( apply_filters( 'the_category', $cat->name ) )
				. '</label>';
		}
	}
}
