<?php

/**
 * Related posts widget
 * 
 * @since  2.2
 */

class EP_Related_Posts_Widget extends WP_Widget {

	/**
	 * Initialize the widget
	 *
	 * @since 6.4
	 */
	public function __construct() {
		$options = array( 'description' => esc_html__( 'Show related posts using ElasticPress. This widget will only appear on single post, page, and custom type pages.', 'elasticpress' ) );
		parent::__construct( 'ep-related-posts', esc_html__( 'ElasticPress - Related Posts', 'elasticpress' ), $options );
	}

	/**
	 * Display widget
	 *
	 * @param array $args
	 * @param array $instance
	 * @since  2.2
	 */
	public function widget( $args, $instance ) {

		if ( ! is_single() ) {
			return;
		}

		$related_posts = get_transient( 'ep_related_posts_' . get_the_ID() );

		if ( false === $related_posts ) {
			$related_posts = ep_find_related( get_the_ID(), $instance['num_posts'] );

			if ( empty( $related_posts ) ) {
				if ( ! defined( 'WP_DEBUG') || ! WP_DEBUG ) {
					set_transient( 'ep_related_posts_' . get_the_ID(), '', HOUR_IN_SECONDS ); // Let's not spam
				}
				return;
			}

			ob_start();

			echo $args['before_widget'];

			if ( ! empty( $instance['title'] ) ) {
				echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
			}
			?>

			<ul>
				<?php foreach ( $related_posts as $post ) :  ?>
					<li><a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>"><?php echo esc_html( get_the_title( $post->ID ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>

			<?php
			wp_reset_postdata();

			echo $args['after_widget'];

			$related_posts = ob_get_clean();
			
			if ( ! defined( 'WP_DEBUG') || ! WP_DEBUG ) {
				set_transient( 'ep_related_posts_' . get_the_ID(), $related_posts, HOUR_IN_SECONDS );
			}
		}

		echo $related_posts;
	}

	/**
	 * Display widget settings form
	 *
	 * @param array $instance
	 * @since  2.2
	 */
	public function form( $instance ) {
		$title = ( isset( $instance['title'] ) ) ? $instance['title'] : '';
		$num_posts = ( isset( $instance['num_posts'] ) ) ? $instance['num_posts'] : 5;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php esc_html_e( 'Title:', 'elasticpress' ); ?>
			</label>
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'num_posts' ); ?>">
				<?php esc_html_e( 'Number of Posts to Show:', 'elasticpress' ); ?>
			</label>
			
			<input class="widefat" id="<?php echo $this->get_field_id( 'num_posts' ); ?>" name="<?php echo $this->get_field_name( 'num_posts' ); ?>" type="text" value="<?php echo absint( $num_posts ); ?>" />
		</p>
		<?php
	}

	/**
	 * Update widget settings
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @since  2.2
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['num_posts'] = absint( $new_instance['num_posts'] );

		return $instance;
	}
}
