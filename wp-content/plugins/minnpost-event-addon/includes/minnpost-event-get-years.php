<?php
/**
* Load the years that have event posts
* Returns or outputs html.
*
* @param int $url_year
* @param string $order
* @param bool $output
* @param bool $cache
*
*/
function minnpost_event_addon_event_years( $url_year, $order = 'DESC', $output = false, $cache = false ) {
	$years       = array();
	$post_type   = 'tribe_events';
	$cache_key   = md5( 'event_years_' . $order );
	$cache_group = 'minnpost_events_addon';
	$years       = wp_cache_get( $cache_key, $cache_group );
	if ( false === $years ) {
		global $wpdb;
		$result = array();
		if ( 'ASC' === $order ) {
			$query_years = $wpdb->get_results( $wpdb->prepare( "SELECT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = %s GROUP BY YEAR(post_date) ASC", $post_type ) );
		} else {
			$query_years = $wpdb->get_results( $wpdb->prepare( "SELECT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = %s GROUP BY YEAR(post_date) DESC", $post_type ) );
		}
		if ( is_array( $query_years ) && count( $query_years ) > 0 ) {
			foreach ( $query_years as $year ) {
				$result[] = json_decode( json_encode( $year ), true );
			}
		}
		$years = array_column( $result, 'YEAR(post_date)' );
		wp_cache_set( $cache_key, $years, $cache_group, DAY_IN_SECONDS * 1 );
	}

	if ( true === $output ) {
		$output = '';
		if ( ! empty( $years ) ) {
			$output .= '<ol class="m-pagination-list tribe-event-years">';
			foreach ( $years as $year ) {
				$output .= '<li><a href="' . site_url( '/events/list/?eventDisplay=past&' . $url_year . '=' . $year ) . '">' . $year . '</a></li>';
			}
			$output .= '</ol>';
		}
		echo $output;
	} else {
		return $years;
	}
}
