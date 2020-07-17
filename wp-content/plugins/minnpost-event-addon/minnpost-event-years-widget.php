<?php

// widget class
class MinnpostEventYears_Widget extends WP_Widget {

	/**
	 * The name of the query variable appended to a View URL to indicate whether to show all events or only those from a certain year
	 */
	const REQUEST_VAR = 'tribe-bar-year';

	public function __construct() {

		parent::__construct(
			'MinnpostEventYears_Widget',
			__( 'MinnPost Event Years Widget', 'minnpost-event-years-widget' ),
			array(
				'classname'   => 'MinnpostEventYears_Widget',
				'description' => __( 'Display links to events by year', 'minnpost-event-years-widget' ),
			)
		);

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

		$title   = apply_filters( 'widget_title', $instance['title'] );
		$content = isset( $instance['content'] ) ? $instance['content'] : '';
		$cache   = isset( $instance['bypass_cache'] ) ? false : true;

		echo str_replace( 'widget MinnpostEventYears-widget', 'm-widget m-minnpost-event-years-widget', str_replace( '_Widget"', '-widget ' . sanitize_title( $title ) . '"', $before_widget ) );

		if ( $title ) {
			$before_title = str_replace( 'widget-title', 'a-widget-title', $before_title );
			echo $before_title . $title . $after_title;
		}
		echo '<div class="m-widget-contents">';
		echo $content;
		if ( file_exists( __DIR__ . '/includes/minnpost-event-get-years.php' ) ) {
			require_once __DIR__ . '/includes/minnpost-event-get-years.php';
		}
		$output = minnpost_event_addon_event_years( self::REQUEST_VAR, 'DESC', true, $cache );
		echo '</div>';

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
		if ( ! empty( $new_instance['content'] ) ) {
			$instance['content'] = $new_instance['content'];
		} else {
			$instance['content'] = '';
		}
		if ( ! empty( $new_instance['bypass_cache'] ) ) {
			$instance['bypass_cache'] = $new_instance['bypass_cache'];
		} else {
			$instance['bypass_cache'] = '';
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

		if ( isset( $instance['content'] ) ) {
			$content = sanitize_text_field( $instance['content'] );
		} else {
			$content = '';
		}

		if ( isset( $instance['bypass_cache'] ) ) {
			$bypass_cache = sanitize_text_field( $instance['bypass_cache'] );
		} else {
			$bypass_cache = '';
		}

		?>
		<div>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</div>
		<div>
			<label for="<?php echo $this->get_field_id( 'content' ); ?>"><?php _e( 'Content:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'content' ); ?>" name="<?php echo $this->get_field_name( 'content' ); ?>" type="text" value="<?php echo $content; ?>" />
		</div>
		<div>
			<label class="selectit" style="display: block;" for="<?php echo $this->get_field_id( 'bypass_cache' ); ?>"><?php _e( 'Bypass Cache For This Query:' ); ?></label>
			<?php $checked_bypass = ( isset( $bypass_cache ) && '' !== $bypass_cache ) ? ' checked' : ''; ?>
			<input id="<?php echo $this->get_field_id( 'bypass_cache' ); ?>" name="<?php echo $this->get_field_name( 'bypass_cache' ); ?>" type="checkbox" value="1"<?php echo $checked_bypass; ?>/>
		</div>
		<?php
	}
}
