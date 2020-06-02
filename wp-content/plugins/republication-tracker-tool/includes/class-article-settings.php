<?php
/**
 * Republication Tracker Tool Article Settings.
 *
 * @since   1.0
 * @package Republication_Tracker_Tool
 */

/**
 * Republication Tracker Tool Article Settings class.
 *
 * @since 1.0
 */
class Republication_Tracker_Tool_Article_Settings {
	/**
	 * Parent plugin class.
	 *
	 * @var    Republication_Tracker_Tool
	 * @since  1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  1.0
	 *
	 * @param  Republication_Tracker_Tool $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  1.0
	 */
	public function hooks() {
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'manage_edit-post_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_edit-post_sortable_columns', array( $this, 'add_sortable_columns' ) );
		add_action( 'manage_posts_custom_column' , array( $this, 'custom_column_content' ), 10, 2 );
		add_action( 'save_post', array( $this, 'save_hide_widget_metabox' ), 10 );
	}


	/**
	 * Add custom metaboxes.
	 *
	 * @since 1.0
	 */
	public function register_meta_boxes() {

		add_meta_box(
			'republication-tracker-tool',
			esc_html__( 'Republication Tracker Tool', 'republication-tracker-tool' ),
			array( $this, 'render_metabox' ),
			array( 'post', 'page' ),
			'advanced',
			'default'
		);

		add_meta_box(
			'republication-tracker-tool-hide-widget',
			esc_html__( 'Hide Republication Widget', 'republication-tracker-tool' ),
			array( $this, 'render_hide_widget_metabox' ),
			array( 'post', 'page' ),
			'side',
			'default',
			array(
				'__block_editor_compatible_meta_box' => true,
			)
		);

	}

	/**
	 * Save the value of the hide widget metabox checkbox
	 * 
	 * @since 1.0.2
	 * @param int $post_id The post ID
	 */
	public function save_hide_widget_metabox( $post_id ){

		if( isset( $_POST[ 'republication-tracker-tool-hide-widget' ] ) ) {

			update_post_meta( $post_id, 'republication-tracker-tool-hide-widget', true );

		} else {

			update_post_meta( $post_id, 'republication-tracker-tool-hide-widget', false );

		}

	}

	/**
	 * Render a custom metabox
	 *
	 * @since 1.0
	 * @param obj $post Post object.
	 * @param obj $args Arguments object.
	 */
	public function render_metabox( $post, $args ) {
		$shares = get_post_meta( $post->ID, 'republication_tracker_tool_sharing', true );
		$total_count = 0;
		if( is_array( $shares) ){
			foreach ( $shares as $url => $count ) {
				$total_count = $total_count + $count;
			}
		}
		echo wp_kses_post( wpautop( 'Total number of views: ' . $total_count ) );
		if( is_array( $shares ) && !empty ( $shares ) ){
			echo '<table class="wp-list-table widefat fixed striped posts">';
				echo '<thead>';
					echo sprintf( '<th scope="col" id="url" class="manage-column column-primary"><span>%s</span><span class="sorting-indicator"></span></th>', esc_html__( 'Republished URL', 'republication-tracker-tool' ) );
					echo sprintf( '<th scope="col" id="views" class="manage-column ">%s</th>', esc_html__( 'Views', 'republication-tracker-tool' ) );
				echo '</thead>';
				echo '<tbody id="the-list">';
					foreach ( $shares as $url => $count ) {
						echo sprintf(
							'<tr><td class="column-primary" data-colname="URL"><a href="%1$s" target="_blank">%1$s</a></td><td class="views" data-colname="Views">%2$s</td></tr>',
							wp_kses_post( $url ),
							wp_kses_post( $count )
						);
					}
				echo '</tbody>';
				echo '<tfoot>';
					echo sprintf( '<th scope="col" id="url" class="manage-column column-primary"><span>%s</span><span class="sorting-indicator"></span></th>', esc_html__( 'Republished URL', 'republication-tracker-tool' ) );
					echo sprintf( '<th scope="col" id="views" class="manage-column">%s</th>', esc_html__( 'Views', 'republication-tracker-tool' ) );
				echo '</tfoot>';
			echo '</table>';
		} else {
			echo esc_html_e('There are no shares to display.', 'republication-tracker-tool');
		}
	}

	/**
	 * Render a custom metabox to check/uncheck whether or not the sharing widget should be hidden
	 *
	 * @since 1.0.2
	 * @param obj $post Post object.
	 * @param obj $args Arguments object.
	 */
	public function render_hide_widget_metabox( $post, $args ){

		$hide_republication_widget = get_post_meta( $post->ID, 'republication-tracker-tool-hide-widget', true );

		$checked = '';

		if( true == $hide_republication_widget ) {

			$checked = 'checked';

		}

		$hide_republication_widget = apply_filters( 'hide_republication_widget', $hide_republication_widget, $post );

		if( true == $hide_republication_widget ){
			echo '<p>The Republication sharing widget on this post is programatically disabled through the <code>hide_republication_widget</code> filter. <a href="https://github.com/INN/republication-tracker-tool/blob/master/docs/removing-republish-button-from-categories.md" target="_blank">Read more about this filter</a>.</p>';
		} else {

			echo '<label>';
				echo '<input type="checkbox" name="republication-tracker-tool-hide-widget" id="republication-tracker-tool-hide-widget" '.$checked.'>';
				echo __('Hide the Republication sharing widget on this post?', 'republication-tracker-tool');
			echo '</label>';

		}

	} 

	public function add_custom_columns( $columns ) {
		$columns['republication_tracker_tool'] = esc_html__( 'Total Views', 'republication-tracker-tool' );
		return $columns;
	}

	public function add_sortable_columns( $columns ) {
		$columns['republication_tracker_tool'] = esc_html__( 'Total Views', 'republication-tracker-tool' );
		return $columns;
	}

	public function custom_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'republication_tracker_tool':
				$shares = get_post_meta( $post_id, 'republication_tracker_tool_sharing', true );
				$total_count = 0;
				if ( $shares ) {
					foreach ( $shares as $url => $count ) {
						$total_count = $total_count + $count;
					}
				}
				echo sprintf( '%s', number_format( $total_count ) );
				break;
		}
	}

}
