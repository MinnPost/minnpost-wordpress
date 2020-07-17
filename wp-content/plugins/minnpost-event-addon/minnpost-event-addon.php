<?php
/*
Plugin Name: MinnPost Event Calendar Addon
Description: This plugin adds on to The Event Calendar using its filters for MinnPost-specific functionality
Version: 0.0.1
Author: MinnPost
Author URI: https://code.minnpost.com
Text Domain: minnpost-event-addon
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class MinnPostEventAddon {

	/**
	 * The name of the Context location we'll read to know if the visitor wishes to see only events from a certain year
	 */
	const CONTEXT_LOCATION = 'year_only';

	/**
	 * The name of the query variable appended to a View URL to indicate whether to show all events or only those from a certain year
	 */
	const REQUEST_VAR = 'tribe-bar-year';

	/**
	 * The name of the query variable used to set the display to past
	 */
	const PAST_VAR = 'eventDisplay';

	/**
	 * The value of the query variable used to set the display to past
	 */
	const PAST_VALUE = 'past';

	/**
	 * Plugin constructor.
	 * The method will register the plugin instance in the `year_only` to allow global access to the instance.
	 */
	public function __construct() {
		// Hook the plugin to the filters and actions required by it to work.
		$this->hook();
	}

	/**
	 * Hooks the filters and actions required for the plugin to work.
	 */
	public function hook() {

		if ( file_exists( __DIR__ . '/includes/minnpost-event-get-years.php' ) ) {
			require_once __DIR__ . '/includes/minnpost-event-get-years.php';
		}

		// filter the plugin context
		add_filter( 'tribe_context_locations', array( $this, 'filter_context_locations' ) );
		// filter the url query arguments
		add_filter( 'tribe_events_views_v2_url_query_args', array( $this, 'filter_view_url_query_args' ) );
		// modify the plugin query arguments
		add_filter( 'tribe_events_views_v2_view_repository_args', array( $this, 'filter_view_repository_args' ) );
		// past event templates should reverse the chronology
		add_filter( 'tribe_events_views_v2_view_list_template_vars', array( $this, 'past_reverse_chronological_order' ) );
		add_filter( 'tribe_events_views_v2_view_photo_template_vars', array( $this, 'past_reverse_chronological_order' ) );
		// past templates are not detected correctly on the url
		add_filter( 'tribe_is_past', array( $this, 'fix_is_past' ) );

		// page titles
		add_filter( 'tribe_get_events_title', array( $this, 'alter_event_archive_titles' ), 11, 2 );

		// widget
		add_action( 'widgets_init', array( $this, 'widget' ) );
	}

	/**
	 * Adds the value we want to the Context.
	 *
	 * This will allow us to call `tribe_context()->get( value )` to filter events
	 *
	 * @param array<string,array> $context_locations A list of Context "locations".
	 *
	 * @return array<string,array> The filtered Context locations.
	 */
	public function filter_context_locations( array $context_locations = array() ) {
		$context_locations[ self::CONTEXT_LOCATION ] = array(
			'read' => array(
				\Tribe__Context::REQUEST_VAR   => self::REQUEST_VAR,
				\Tribe__Context::LOCATION_FUNC => array(
					'view_data',
					static function ( $data ) {
						return is_array( $data ) && ! empty( $data[ self::REQUEST_VAR ] ) ? true : \Tribe__Context::NOT_FOUND;
					},
				),
				\Tribe__Context::FUNC          => static function () {
					$url = tribe_get_request_var( 'url', false );
					if ( empty( $url ) ) {
						return \Tribe__Context::NOT_FOUND;
					}
					$query_string = wp_parse_url( $url, PHP_URL_QUERY );
					wp_parse_str( $query_string, $query_args );
					return ! empty( $query_args[ self::REQUEST_VAR ] ) ? true : \Tribe__Context::NOT_FOUND;
				},
			),
		);
		return $context_locations;
	}

	/**
	 * Filters the View query arguments, those the View will add to its URL, to make sure the user choice to show
	 * only events that allow walk-in ticket purchases or not is reflected in the URL.
	 *
	 * @param array<string,string> $query_args The list of query arguments the View will append to its URL.
	 *
	 * @return array<string,string> The filtered list of query arguments the View will append to its URL
	 *
	 */
	public function filter_view_url_query_args( array $query_args = array() ) {
		if ( tribe_context()->get( self::CONTEXT_LOCATION, false ) ) {
			$query_args[ self::REQUEST_VAR ] = tribe_context()->get( self::CONTEXT_LOCATION );
		} else {
			unset( $query_args[ self::REQUEST_VAR ] );
		}
		return $query_args;
	}

	/**
	 * Filters the View repository arguments, the one the View will use to fetch events, to take into account the user choice to show only events in the given year.
	 *
	 * @param array<string,mixed> $repository_args The original list of repository arguments the View will use to fetch events.
	 *
	 * @return array<string,mixed> The filtered list of View repository arguments. We'll add one related to the year only if the user wants to only show events from the given year
	 */
	public function filter_view_repository_args( array $repository_args = array() ) {
		if ( tribe_context()->get( self::CONTEXT_LOCATION, false ) ) {
			$repository_args['meta_query'] = array(
				'relation'     => 'AND',
				'starts_after' => array(
					'key'     => '_EventStartDate',
					'compare' => '>=',
					'value'   => tribe_context()->get( self::CONTEXT_LOCATION ) . '-01-01 00:00:00',
				),
				'ends_before'  => array(
					'key'     => '_EventEndDate',
					'compare' => '<=',
					'value'   => tribe_context()->get( self::CONTEXT_LOCATION ) . '-12-31 23:59:59',
				),
			);
		}
		$repository_args['posts_per_page'] = -1;
		return $repository_args;
	}

	/**
	 * Reverse chronology on past event templates
	 *
	 * @param array $template_vars
	 * @return array $template_vars
	 */
	public function past_reverse_chronological_order( $template_vars ) {
		if ( ! empty( $template_vars['is_past'] ) ) {
			$template_vars['events'] = array_reverse( $template_vars['events'] );
		}
		return $template_vars;
	}

	/**
	 * Fix the conditional to tell if a list page is showing past events or not
	 *
	 * @param bool $is_past
	 * @return bool $is_past
	 */
	public function fix_is_past( $is_past ) {
		if ( self::PAST_VALUE === tribe_context()->get( 'event_display_mode' ) ) {
			$is_past = true;
		}
		return $is_past;
	}

	/**
	 * Event archive titles
	 *
	 * @param string $original_recipe_title
	 * @param int $depth
	 * @return string $title
	 */
	public function alter_event_archive_titles( $original_recipe_title, $depth ) {
		// Default Title
		$title = __( 'MinnPost Events', 'minnpost-event-addon' );
		// If there's a date selected in the tribe bar, show the date range of the currently showing events
		if ( tribe_is_past() ) {
			$title = __( 'Past Events', 'minnpost-event-addon' );
			if ( tribe_context()->get( self::CONTEXT_LOCATION, false ) ) {
				$title = sprintf(
					// translators: 1 is the year
					__( 'Past Events: %1$s', 'minnpost-event-addon' ),
					tribe_context()->get( self::CONTEXT_LOCATION )
				);
			}
		}
		return $title;
	}

	public function widget() {
		include_once( 'minnpost-event-years-widget.php' );
		register_widget( 'MinnpostEventYears_Widget' );
	}

}

// Instantiate the plugin.
$minnpost_event_addon = new MinnPostEventAddon;
