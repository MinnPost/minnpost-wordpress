<?php
/*
Plugin Name: MinnPost Spills Widget
Plugin URI: #
Description: This plugin creates a sidebar widget that is able to display posts from a group of categories and/or tags
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: minnpost-spills-widget
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
 
 
class MinnpostSpills_Widget extends WP_Widget {

	/**
	* @var object
	*/
	private $salesforce;

 
	public function __construct() {

		parent::__construct(
			'MinnpostSpills_Widget', __( 'MinnPost Spills Widget', 'minnpost-spills-widget' ),
			array(
				'classname'   => 'MinnpostSpills_Widget',
				'description' => __( 'Posts from a group of categories and/or tags.', 'minnpost-spills-widget' )
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
				"<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag",
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

		$title = apply_filters( 'widget_title', $instance['title'] );
		$categories = $instance['widget_categories'];
		$terms = $instance['widget_terms'];
		$output_function = $instance['output_function'];
		echo str_replace( 'widget MinnpostSpills-widget', 'widget minnpost-spills-widget', str_replace('_Widget"', '-widget ' . sanitize_title( $title ) . '"', $before_widget));

		if ( isset( $output_function ) && function_exists( $output_function ) ) {
			$output = $output_function( $before_title, $title, $after_title, $categories, $terms );
		} else {
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			echo '<div class="contents">';
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

		$instance = $old_instance;

		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( !empty($new_instance['widget_categories'] ) ) {
			$instance['widget_categories'] = $new_instance['widget_categories'];
		} else {
			$instance['widget_categories'] = array();
		}
		if ( !empty( $new_instance['widget_terms'] ) ) {
			$instance['widget_terms'] = $new_instance['widget_terms'];
		} else {
			$instance['widget_terms'] = array();
		}
		if ( !empty( $new_instance['output_function'] ) ) {
			$instance['output_function'] = $new_instance['output_function'];
		} else {
			$instance['output_function'] = array();
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

		if ( isset( $instance['widget_categories'] ) ) {
			$categories = $instance['widget_categories'];
		} else {
			$categories = false;
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
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:'); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'widget_categories' ); ?>"><?php _e( 'Categories:' ); ?></label> 
			<?php $checked_ontop = true; ?>
			<ul class="categorychecklist" style="height: 200px; overflow: auto; border: 1px solid #ddd; background: #fdfdfd; padding: 0 0.9em;">
				<?php wp_category_checklist( 0, 0, $categories, false, $category_walker, $checked_ontop ); ?>
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
	* Load the Salesforce object
	* Also make it available to this whole class
	*
	* @return $this->salesforce
	*
	*/
	private function get_spill_posts( $categories, $terms ) {

		if ( ! empty( $categories ) ) {
			$slugs = array();
			foreach ( $categories as $id ) {
				$category = get_term_by( 'id', $id, 'category' );
				$slugs[] = $category->slug;
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
				<?php
				$url_array = explode( '/',get_permalink() );
				$category = $url_array[3];
				?>
				<p class="spill-item-category"><?php echo get_category_by_slug( $category )->name; ?></p>
				<p class="spill-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
			<?php endwhile; ?>
			<!-- end of the loop -->

			<?php wp_reset_postdata(); ?>

		<?php endif; ?>

		<?php

	}
}

// This is required to be sure Walker_Category_Checklist class is available
require_once ABSPATH . 'wp-admin/includes/template.php';
/**
 * Custom walker to print category checkboxes for widget forms
 */
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
		$class = in_array( $cat->term_id, $popular_cats ) ? ' class="popular-category"' : '';
		$id = $this->id . '-' . $cat->term_id;
		$checked = checked( in_array( $cat->term_id, $selected_cats ), true, false );
		$output .= "\n<li id='{$taxonomy}-{$cat->term_id}'$class>"
			. '<label class="selectit"><input value="'
			. $cat->term_id . '" type="checkbox" name="' . $this->name
			. '[]" id="in-' . $id . '"' . $checked
			. disabled( empty( $args['disabled'] ), false, false ) . ' /> '
			. esc_html( apply_filters( 'the_category', $cat->name ) )
			. '</label>';
	}
}
/* Register the widget */
add_action( 'widgets_init', function() {
	register_widget( 'MinnpostSpills_Widget' );
});
