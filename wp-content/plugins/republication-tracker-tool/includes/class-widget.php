<?php
/**
 * Republication Tracker Tool Settings.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Republication Tracker Tool Settings class.
 *
 * @since 1.0
 */
class Republication_Tracker_Tool_Widget extends WP_Widget {

	public $has_instance = false;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'republication_tracker_tool',
			'description' => esc_html__( 'Republication Tracker Tool', 'republication-tracker-tool' ),
		);
		parent::__construct( 'republication_tracker_tool', 'Republication Tracker Tool', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * If this is not a single post, don't output the widget. It won't work outside single posts.
	 *
	 * @param array $args Sidebar arguments.
	 * @param array $instance This instance of the widget.
	 */
	public function widget( $args, $instance ) {
		if ( ! is_single() ) {
			return;
		}

		global $post;

		// our post `republication-tracker-tool-hide-widget` meta is our default filter value
		$hide_republication_widget_on_post = apply_filters( 'hide_republication_widget', get_post_meta( $post->ID, 'republication-tracker-tool-hide-widget', true ), $post );

		// if `republication-tracker-tool-hide-widget` meta is set to true, don't show the shareable content widget
		// OR if the `hide_republication_widget` filter is set to true, don't show the shareable content widget
		if( true == $hide_republication_widget_on_post ) {
				
			return;
			
		}
		
		// define our path to grab file content from
		$republication_plugin_path = plugin_dir_path( __FILE__ );

		wp_enqueue_script( 'republication-tracker-tool-js', plugins_url( 'assets/widget.js', dirname( __FILE__ ) ), array( 'jquery' ), Republication_Tracker_Tool::VERSION, false );
		wp_enqueue_style( 'republication-tracker-tool-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array(), Republication_Tracker_Tool::VERSION );
		add_action( 'wp_ajax_my_action', 'my_action' );
		add_action( 'wp_ajax_nopriv_my_action', 'my_action' );

		echo wp_kses_post( $args['before_widget'] );

		if ( ! empty( $instance['title'] ) ) {
			echo wp_kses_post( $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'] ) ) . $args['after_title'] );
		}

		echo '<div class="license">';
			echo sprintf(
				'<p><button name="%1$s" id="cc-btn" class="republication-tracker-tool-button">%1$s</button></p>',
				esc_html__( 'Republish This Story', 'republication-tracker-tool' )
			);
			echo sprintf(
				'<p><a class="license" rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="%s" style="border-width:0" src="%s" /></a></p>',
				esc_html__( 'Creative Commons License', 'republication-tracker-tool' ),
				esc_url( plugin_dir_url( dirname( __FILE__ ) ) ).'assets/img/creative-commons-sharing.png'
			);
		echo '</div>';

		echo sprintf(
			'<div class="message">%s</div>',
			wp_kses_post( wpautop( esc_html( $instance['text'] ) ) )
		);

		echo wp_kses_post( $args['after_widget'] );

		// if has_instance is false, we can continue with displaying the modal
		if( isset( $this->has_instance ) && false === $this->has_instance ){

			// update has_instance so the next time the widget is created on the same page, it does not create a second modal
			$this->has_instance = true;

			printf(
				'<div id="republication-tracker-tool-modal" style="display:none;" data-postid="%1$s" data-pluginsdir="%2$s">%3$s</div>',
				esc_attr( $post->ID ),
				esc_attr( plugins_url() ),
				esc_html( include_once( $republication_plugin_path . 'shareable-content.php' ) )
			);

		}
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
		echo sprintf( '<p><em>%s</em></p>', esc_html__( 'This widget will only display on single articles.', 'republication-tracker-tool' ) );
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( 'Republish our articles for free, online or in print, under a Creative Commons license.', 'text_domain' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';

		return $instance;
	}
}
