<?php

// function to get the title of the referring url
function wprtt_get_referring_page_title( $url ) {

	$response = wp_remote_get( $url );

	// if there was no issue grabbing the url, continue
	if ( ! is_wp_error( $response ) ) {

		// find the title element inside of the response body
		$response = preg_match( '/<title.[^>]*>(.*)<\/title>/siU', $response['body'], $title_matches );

		// if a title element was found, let's get the text from it
		if ( $title_matches ) {

			// clean up title: remove EOL's and excessive whitespace.
			$title = preg_replace( '/\s+/', ' ', $title_matches[1] );
			$title = trim( $title );
			$title = rawurlencode( $title );

			// return our found title
			return $title;

			// if there were no title matches found, don't continue
		} else {

			return;

		}
	}

}

if ( isset( $_GET['post'] ) ) {

	// set up all of our post vars we want to track
	$shared_post_id = absint( $_GET['post'] );
	$shared_post    = get_post( $shared_post_id );

	$shared_post_slug      = rawurlencode( $shared_post->post_name );
	$shared_post_permalink = get_permalink( $shared_post_id );

	if ( array_key_exists( 'HTTP_REFERER', $_SERVER ) ) {

		if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
			$url = esc_url_raw( $_SERVER['HTTP_REFERER'] );
		}

		$url_title = wprtt_get_referring_page_title( $url );
		$url_title = str_replace( ' ', '%20', $url_title );

		$url_host = parse_url( $url, PHP_URL_HOST );
		$url_path = parse_url( $url, PHP_URL_PATH );

	} else {

		$url       = '';
		$url_title = '';
		$url_host  = '';

	}

	// If the request is coming from WP Admin, bail out (when the copied content is inserted into the WP editor, the pixel will be pinged).
	if ( false !== stripos( $url, '/wp-admin/' ) ) {
		exit;
	}

	$value = get_post_meta( $shared_post_id, 'republication_tracker_tool_sharing', true );
	if ( $value ) {
		if ( isset( $value[ $url ] ) ) {
			$value[ $url ]++;
		} else {
			$value[ $url ] = 1;
		}
	} else {
		$value = array(
			$url => 1,
		);
	}
	$update = update_post_meta( $shared_post_id, 'republication_tracker_tool_sharing', $value );

	// if our google analytics tag is set, let's push data to it
	if ( isset( $_GET['ga'] ) && ! empty( $_GET['ga'] ) ) {

		// our base url to ping GA at
		$analytics_ping_url = 'https://www.google-analytics.com/collect?v=1';

		// create all of our necessary params to track
		// the docs for these params can be found at: https://developers.google.com/analytics/devguides/collection/protocol/v1/parameters
		$analytics_ping_params = array(
			'tid' => sanitize_text_field( $_GET['ga'] ), // Tracking ID/ Web Property ID.
			'cid' => '555', // Client ID.
			't'   => 'pageview', // Hit type.
			'dl'  => $shared_post_permalink, // Document location URL.
			'dh'  => $url_host, // Document Host Name.
			'dp'  => $shared_post_slug, // Document Path.
			'dr'  => $url, // Document Referrer.
			'dt'  => $url_title, // Document Title.
			'an'  => 'Republication', // Application Name.
			'aid' => $url_host, // Application ID.
			'av'  => 'Republication Tracker v1', // Application Version.
		);

		// create query based on our params array
		$analytics_ping_params = http_build_query( $analytics_ping_params );

		$response = wp_remote_post( $analytics_ping_url . '&' . $analytics_ping_params );

	}
}

header( 'Content-Type: image/png' );
// A transparent 1x1 px .gif image.
echo base64_decode( 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAAA1BMVEUAAACnej3aAAAAAXRSTlMAQObYZgAAAApJREFUCNdjYAAAAAIAAeIhvDMAAAAASUVORK5CYII=' );
exit;
