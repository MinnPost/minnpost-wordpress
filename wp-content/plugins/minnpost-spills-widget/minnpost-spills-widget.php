<?php
/*
Plugin Name: MinnPost Spills
Description: This plugin creates a sidebar widget and endpoint URL that is able to display posts from a group of categories and/or tags
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-spills
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

use Brain\Cortex\Route\RouteCollectionInterface;
use Brain\Cortex\Route\QueryRoute;

class MinnpostSpills {

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version = '0.0.2';

		$this->load_admin();

		$this->template = 'minnpost-spill.php';

		//$this->config();
		//$this->schedule();

		// register the widget
		add_action( 'widgets_init', function() {
			register_widget( 'MinnpostSpills_Widget' );
		});

		// handle the permalinks
		$this->init();

	}

	private function init() {
		if ( ! is_admin() ) {
			if ( ! class_exists( 'Brain\Cortex' ) ) {
				require_once( 'vendor/autoload.php' );
			}
			Brain\Cortex::boot();

			add_action( 'cortex.routes', function( RouteCollectionInterface $routes ) {

				$widget_instances = get_option( 'widget_MinnpostSpills_Widget', false );
				$instances = array_values( $widget_instances );

				add_filter( 'get_the_archive_title', array( $this, 'set_wp_title' ) );
				add_filter( 'pre_get_document_title', array( $this, 'set_wp_title' ) );

				foreach ( $widget_instances as $instance ) {

					$title = sanitize_title( $instance['title'] );
					$key = array_search( $instance['title'], array_column( $instances, 'title' ), true );
					$match = $instances[ $key ];
					$query = array(
						'post_type' => 'post',
						'tax_query' => array(
							'relation' => 'OR',
						),
					);
					if ( ! empty( $match['widget_categories'] ) ) {
						$query['tax_query'][] = array(
							'taxonomy' => 'category',
							'field' => 'slug',
							'terms' => $match['widget_categories'],
						);
					}
					if ( ! empty( $match['widget_terms'] ) ) {
						if ( ! is_array( $match['widget_terms'] ) ) {
							$widget_terms = explode( ',', $match['widget_terms'] );
						} else {
							$widget_terms = $match['widget_terms'];
						}
						$query['tax_query'][] = array(
							'taxonomy' => 'post_tag',
							'field' => 'name',
							'terms' => $widget_terms,
						);
					}
					$routes->addRoute( new QueryRoute(
						$title,
						$query,
						[
							'template' => $this->template,
						]
					));
				}

			});
		}
	}

	private function load_admin() {
		if ( is_admin() ) {
			// This is required to be sure Walker_Category_Checklist class is available
			require_once ABSPATH . 'wp-admin/includes/template.php';
		}
	}

	public function set_wp_title( $title ) {
		global $template;
		if ( basename( $template ) === $this->template ) {

			$widget_instances = get_option( 'widget_MinnpostSpills_Widget', false );
			$instances = array_values( $widget_instances );

			add_filter( 'get_the_archive_title', array( $this, 'set_wp_title' ) );
			add_filter( 'pre_get_document_title', array( $this, 'set_wp_title' ) );

			$url = str_replace( '/', '', $_SERVER['REQUEST_URI'] );

			foreach ( $widget_instances as $instance ) {
				$slug = sanitize_title( $instance['title'] );
				if ( $slug === $url ) {
					$key = array_search( $instance['title'], array_column( $instances, 'title' ), true );
					$match = $instances[ $key ];
					return $match['title'];
				}
			}
			return $title;
		}
		return $title;
	}

}

// Instantiate our class
$minnpost_spills = new MinnpostSpills();

class MinnpostSpills_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct(
			'MinnpostSpills_Widget', __( 'MinnPost Spills Widget', 'minnpost-spills-widget' ),
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
	 * add script to admin page
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

		if ( ! isset( $instance['conditions']['action'] ) ) {
			return;
		}

		$title = apply_filters( 'widget_title', $instance['title'] );
		$url = isset( $instance['url'] ) && '' !== $instance['url'] ? $instance['url'] : '/' . sanitize_title( $instance['title'] ) . '/';
		$categories = $instance['widget_categories'];
		$terms = $instance['widget_terms'];
		$output_function = isset( $instance['output_function'] ) ? $instance['output_function'] : '';

		echo str_replace( 'widget MinnpostSpills-widget', 'm-widget m-minnpost-spills-widget', str_replace( '_Widget"', '-widget ' . sanitize_title( $title ) . '"', $before_widget ) );

		if ( isset( $output_function ) && function_exists( $output_function ) ) {
			$output = $output_function( $before_title, $title, $after_title, $categories, $terms );
		} else {
			if ( $title ) {
				$before_title = str_replace( 'widget-title', 'a-widget-title', $before_title );
				echo $before_title . '<a href="' . $url . '">' . $title . '</a>' . $after_title;
			}
			echo '<div class="m-widget-contents">';
				$output = $this->get_spill_posts( $categories, $terms );
			echo '</div>';
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
		if ( ! empty($new_instance['widget_categories'] ) ) {
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

		if ( isset( $instance['widget_categories'] ) ) {
			$categories = $instance['widget_categories'];
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
			$categories = false;
			$category_ids = false;
		}

		if ( isset( $instance['widget_terms'] ) ) {
			$terms = $instance['widget_terms'];
		} else {
			$terms = false;
		}

		if ( isset( $instance['output_function'] ) ) {
			$output_function = sanitize_text_field( $instance['output_function'] );
		} else {
			$output_function = '';
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
		<?php
	}

	/**
	* Load the spills for the posts
	* This outputs HTML
	*
	*/
	private function get_spill_posts( $categories, $terms ) {

		if ( ! empty( $categories ) ) {
			$slugs = array();
			foreach ( $categories as $category ) {
				if ( is_numeric( $category ) ) {
					$category = get_term_by( 'id', $category, 'category' );
					$slugs[] = $category->slug;
				} else {
					$slugs[] = $category;
				}
			}
			$the_query = new WP_Query(
				array(
					'posts_per_page' => 4,
					'category_name' => $slugs ? implode( ',', $slugs ) : '',
					'orderby' => 'date',
				)
			);
		}

		if ( ! empty( $terms ) ) {
			$the_query = new WP_Query(
				array(
					'posts_per_page' => 4,
					'tag' => $terms,
					'orderby' => 'date',
				)
			);
		}

		?>

		<?php if ( $the_query->have_posts() ) : ?>
			<!-- the loop -->
			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<article id="<?php the_ID(); ?>" class="m-post m-post-spill">
					<?php
					$url_array = explode( '/',get_permalink() );
					$category = $url_array[3];
					?>
					<p class="a-post-category a-spill-item-category"><?php echo get_category_by_slug( $category )->name; ?></p>
					<p class="a-post-title a-spill-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
				</article>
			<?php endwhile; ?>
			<!-- end of the loop -->

			<?php wp_reset_postdata(); ?>

		<?php endif; ?>

		<?php

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
			$this->id = $id;
		}

		function start_el( &$output, $cat, $depth = 0, $args = array(), $id = 0 ) {
			extract( $args );
			if ( empty( $taxonomy ) ) {
				$taxonomy = 'category';
			}
			$class = in_array( $cat->slug, $popular_cats ) ? ' class="popular-category"' : '';
			$id = $this->id . '-' . $cat->slug;
			$checked = checked( in_array( $cat->term_id, $selected_cats ), true, false );
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
