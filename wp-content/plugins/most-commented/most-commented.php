<?php
/**
 * Plugin Name: Most Commented Widget
 * Plugin URI: https://wordpress.org/plugins/most-commented/
 * Description: Widget to display posts/pages with the most comments.
 * Version: 3.0
 * Author: Nick Momrik
 * Author URI: http://nickmomrik.com/
 * Updated by MinnPost to fix code standards/PHP 7 compatibility, and to use WP_Query instead of get_results.
 *
 * @package Most_Commented_Widget
 */

/**
 * Extend WP_Widget to make the Most Commented Widget.
 */
class Most_Commented_Widget extends WP_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		WP_Widget::__construct( 'most-commented', __( 'Most Commented', 'most-commented' ) );
	}

	/**
	 * Echoes the widget content.
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function widget( $args, $instance ) {

		$widget_id     = $args['widget_id'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];
		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];

		// title of the widget.
		$title = apply_filters( 'widget_title', $instance['title'] );

		// description of the widget.
		$description = isset( $instance['description'] ) ? apply_filters( 'the_content', $instance['description'] ) : '';

		// whether to display the comment count.
		$show_comment_count = isset( $instance['show_comment_count'] ) ? (bool) $instance['show_comment_count'] : false;

		// whether to check password posts.
		$show_pass_post = (bool) $instance['show_pass_post'];

		// how many days to go back. if the value is zero, it checks all posts.
		$duration = intval( $instance['duration'] );
		if ( ! in_array( $duration, array( 0, 1, 7, 30, 365 ), true ) ) {
			$duration = 0;
		}

		// how many posts to load.
		$num_posts = intval( $instance['num_posts'] );
		if ( $num_posts < 1 ) {
			$num_posts = 5;
		}

		// what post type to use.
		$post_type = $instance['post_type'];
		if ( ! in_array( $post_type, array( 'post', 'page', 'both' ), true ) ) {
			$post_type = 'both';
		}

		// whether to echo or return the widget's content.
		$echo = ( array_key_exists( 'echo', $instance ) ) ? $instance['echo'] : true;

		// content before and after the widget.
		if ( array_key_exists( 'before', $instance ) ) {
			$before = $instance['before'];
			$after  = $instance['after'];
		} else {
			$before = '<li>';
			$after  = '</li>';
		}

		$most_commented_output = '';
		$most_commented_ids    = get_transient( $widget_id );
		if ( false === $most_commented_ids ) {
			// if there's not a cached list of ids, generate a WP_Query based on the widget settings.
			$most_commented_args = array(
				'post_status'    => 'publish',
				'orderby'        => 'comment_count',
				'comment_count'  => array(
					'value'   => 1,
					'compare' => '>=',
				),
				'posts_per_page' => $num_posts,
				'fields'         => 'ids',
				'cache'          => true,
				'es'             => true,
			);

			if ( 'both' !== $post_type ) {
				$most_commented_args['post_type'] = $post_type;
			}

			if ( false === $show_pass_post ) {
				$most_commented_args['has_password'] = false;
			}

			if ( $duration > 0 ) {
				$most_commented_args['date_query'] = array(
					'after' => $duration . ' days ago',
				);
			}

			// filter args.
			$most_commented_args = apply_filters( 'most_commented_widget_args_pre_cache', $most_commented_args, $post_type, $show_pass_post, $duration );

			// run query.
			$most_commented_query = new WP_Query( $most_commented_args );
			$most_commented_ids   = $most_commented_query->posts;

			if ( true === $most_commented_args['cache'] ) {
				set_transient( $widget_id, $most_commented_ids, 1800 );
			}
		}

		// these are the cached ids. we do still have to make sure the order is correct.
		$most_commented_args_ids = array(
			'post__in' => $most_commented_ids,
			'orderby'  => 'comment_count',
		);

		// filter final query args.
		$most_commented_args_ids = apply_filters( 'most_commented_widget_args_ids', $most_commented_args_ids, '', '', '' );
		// run query. by default the arguments are an array of ids.
		$most_commented_query = new WP_Query( $most_commented_args_ids );

		// clear the cache if need be.
		if ( isset( $most_commented_args_ids['cache'] ) && false === $most_commented_args_ids['cache'] ) {
			delete_transient( $widget_id );
		}

		if ( $most_commented_query->have_posts() ) {
			while ( $most_commented_query->have_posts() ) {
				$most_commented_query->the_post();
				$post = $most_commented_query->post;

				$comment_count = wp_count_comments( $post->ID );

				$comment_count_output = '';
				if ( true === $show_comment_count ) {
					$comment_count_output = ' (' . $comment_count->approved . ')';
				}

				$most_commented_output .= $before . '<a href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>' . $comment_count_output . $after;
			}
			wp_reset_postdata();
		} else {
			$most_commented_output .= $before . 'None found' . $after;
		}

		// finish output with widget settings values.
		if ( ! array_key_exists( 'not_widget', $instance ) ) {
			if ( $title ) {
				$title = $before_title . $title . $after_title;
			}
			$most_commented_output = $before_widget . $title . $description . '<ol>' . $most_commented_output . '</ol>' . $after_widget;
		}

		// either echo or return the complete widget's output.
		if ( true === $echo ) {
			echo wp_kses_post( $most_commented_output );
		} else {
			return $most_commented_output;
		}
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * This function should check that `$new_instance` is set correctly. The newly-calculated
	 * value of `$instance` should be returned. If false is returned, the instance won't be
	 * saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		$new_instance['show_comment_count'] = isset( $new_instance['show_comment_count'] );
		$new_instance['show_pass_post']     = isset( $new_instance['show_pass_post'] );

		wp_cache_delete( $this->id );

		return $new_instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title              = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$description        = isset( $instance['description'] ) ? esc_attr( $instance['description'] ) : '';
		$show_comment_count = isset( $instance['show_comment_count'] ) ? (bool) $instance['show_comment_count'] : false;
		$show_pass_post     = isset( $instance['show_pass_post'] ) ? (bool) $instance['show_pass_post'] : '';
		$duration           = isset( $instance['duration'] ) ? intval( $instance['duration'] ) : 0;
		if ( ! in_array( $duration, array( 0, 1, 7, 30, 365 ), true ) ) {
			$duration = 0;
		}

		$num_posts = isset( $instance['num_posts'] ) ? intval( $instance['num_posts'] ) : 0;
		if ( $num_posts < 1 ) {
			$num_posts = 5;
		}

		$post_type = isset( $instance['post_type'] ) ? $instance['post_type'] : 'both';
		if ( ! in_array( $post_type, array( 'post', 'page', 'both' ), true ) ) {
			$post_type = 'both';
		}
		?>
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo esc_html__( 'Title:', 'most-commented' ); ?> <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>"><?php echo esc_html__( 'Description:', 'most-commented' ); ?><textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>"><?php echo esc_html( $description ); ?></textarea></label></p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>"><?php echo esc_html__( 'Display:', 'most-commented' ); ?>
				<select id="<?php echo esc_attr( $this->get_field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_type' ) ); ?>">
				<?php
				$post_type_choices = array(
					'post' => __( 'Posts', 'most-commented' ),
					'page' => __( 'Pages', 'most-commented' ),
					'both' => __( 'Posts & Pages', 'most-commented' ),
				);
				foreach ( $post_type_choices as $post_type_value => $post_type_text ) {
					echo "<option value='" . esc_attr( $post_type_value ) . "' " . ( $post_type === $post_type_value ? 'selected="selected"' : '' ) . '>' . esc_html( $post_type_text ) . "</option>\n";
				}
				?>
				</select>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'num_posts' ) ); ?>"><?php echo esc_html__( 'Maximum number of results:', 'most-commented' ); ?>
				<select id="<?php echo esc_attr( $this->get_field_id( 'num_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'num_posts' ) ); ?>">
				<?php
				for ( $i = 1; $i <= 20; ++$i ) {
					echo '<option value="' . esc_attr( $i ) . '"' . ( (int) esc_attr( $num_posts ) === $i ? 'selected="selected"' : '' ) . '>' . esc_attr( $i ) . '</option>';
				}
				?>
				</select>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'duration' ) ); ?>"><?php echo esc_html__( 'Limit to:', 'most-commented' ); ?>
				<select id="<?php echo esc_attr( $this->get_field_id( 'duration' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'duration' ) ); ?>">
				<?php
				$duration_choices = array(
					1   => __( '1 Day', 'most-commented' ),
					7   => __( '7 Days', 'most-commented' ),
					30  => __( '30 Days', 'most-commented' ),
					365 => __( '365 Days', 'most-commented' ),
					0   => __( 'All Time', 'most-commented' ),
				);
				foreach ( $duration_choices as $duration_num => $duration_text ) {
					echo '<option value="' . esc_attr( $duration_num ) . '"' . ( $duration === $duration_num ? "selected='selected'" : '' ) . '>' . esc_html( $duration_text ) . '</option>';
				}
				?>
				</select>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_comment_count' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_comment_count' ) ); ?>" class="checkbox" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_comment_count' ) ); ?>"<?php echo checked( $show_comment_count ); ?> /> <?php echo esc_html__( 'Show comment count', 'most-commented' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_pass_post' ) ); ?>">
				<input id="<?php echo esc_attr( $this->get_field_id( 'show_pass_post' ) ); ?>" class="checkbox" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_pass_post' ) ); ?>"<?php echo checked( $show_pass_post ); ?> /> <?php echo esc_html__( 'Include password protected posts/pages', 'most-commented' ); ?>
			</label>
		</p>
		<?php
	}

}

add_action(
	'widgets_init',
	function () {
		return register_widget( 'Most_Commented_Widget' );
	},
	10
);

if ( ! function_exists( 'mdv_most_commented' ) ) {
	/**
	 * Initialize the plugin
	 *
	 * @param int    $num_posts how many posts to show.
	 * @param string $before what to show before a post.
	 * @param string $after what to show after a post.
	 * @param bool   $show_pass_post whether to show password protected posts.
	 * @param int    $duration how far back in days.
	 * @param bool   $echo whether to echo the widget.
	 * @param string $post_type post, page, or both.
	 * @return object $most_commented->widget instane of the class.
	 */
	function mdv_most_commented( $num_posts = 5, $before = '<li>', $after = '</li>', $show_pass_post = false, $duration = 0, $echo = true, $post_type = 'both' ) {
		$options        = array(
			'num_posts'      => $num_posts,
			'before'         => $before,
			'after'          => $after,
			'show_pass_post' => $show_pass_post,
			'duration'       => $duration,
			'echo'           => $echo,
			'post_type'      => $post_type,
			'not_widget'     => true,
		);
		$args           = array( 'widget_id' => 'most_commented_widget_' . md5( var_export( $options, true ) ) );
		$most_commented = new Most_Commented_Widget();

		if ( $echo ) {
			$most_commented->widget( $args, $options );
		} else {
			return $most_commented->widget( $args, $options );
		}
	}
}
