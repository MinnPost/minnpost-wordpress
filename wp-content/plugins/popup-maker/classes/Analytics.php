<?php
/*******************************************************************************
 * Copyright (c) 2017, WP Popup Maker
 ******************************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controls the basic analytics methods for Popup Maker
 *
 */
class PUM_Analytics {

	/**
	 *
	 */
	public static function init() {
		if ( pum_get_option( 'disable_analytics' ) || popmake_get_option( 'disable_popup_open_tracking' ) ) {
			return;
		}

		add_action( 'rest_api_init', array( __CLASS__, 'register_endpoints' ) );
		add_action( 'wp_ajax_pum_analytics', array( __CLASS__, 'ajax_request' ) );
		add_action( 'wp_ajax_nopriv_pum_analytics', array( __CLASS__, 'ajax_request' ) );
	}

	/**
	 * @param $event
	 *
	 * @return mixed
	 */
	public static function event_keys( $event ) {
		$keys = array( $event, $event . 'ed' );

		switch ( $event ) {
			case 'conversion':
				$keys[1] = 'conversion';
				break;
		}

		return apply_filters( 'pum_analytics_event_keys', $keys, $event );
	}

	/**
	 * @param $args
	 */
	public static function track( $args ) {
		if ( empty ( $args['pid'] ) || $args['pid'] <= 0 ) {
			return;
		}

		$event = sanitize_text_field( $args['event'] );

		$popup = pum_get_popup( $args['pid'] );

		if ( ! pum_is_popup( $popup ) || ! in_array( $event, apply_filters( 'pum_analytics_valid_events', array( 'open', 'conversion' ) ) ) ) {
			return;
		}

		$popup->increase_event_count( $event );

		if ( has_action( 'pum_analytics_' . $event ) ) {
			do_action( 'pum_analytics_' . $event, $popup->ID, $args );
		}

	}

	/**
	 *
	 */
	public static function ajax_request() {

		$args = wp_parse_args( $_REQUEST, array(
			'event'  => null,
			'pid'    => null,
			'method' => null,
		) );

		self::track( $args );

		switch ( $args['method'] ) {
			case 'image':
				self::serve_pixel();
				break;

			case 'json':
				self::serve_json();
				break;

			default:
				self::serve_no_content();
				break;
		}

	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|mixed
	 */
	public static function analytics_endpoint( WP_REST_Request $request ) {
		$args = $request->get_params();

		if ( ! $args || empty( $args['pid'] ) ) {
			return new WP_Error( 'missing_params', __( 'Missing Parameters.' ), array( 'status' => 404 ) );
		}

		self::track( $args );

		self::serve_no_content();

		return true;
	}

	/**
	 * @param $param
	 *
	 * @return bool
	 */
	public static function endpoint_absint( $param ) {
		return is_numeric( $param );
	}

	/**
	 *
	 */
	public static function register_endpoints() {
		$version   = 1;
		$namespace = 'pum/v' . $version;

		register_rest_route( $namespace, 'analytics', array(
			'methods'  => 'GET',
			'callback' => array( __CLASS__, 'analytics_endpoint' ),
			'args'     => array(
				'event' => array(
					'required'    => true,
					'description' => __( 'Event Type', 'popup-maker' ),
					'type'        => 'string',
				),
				'pid'   => array(
					'required'            => true,
					'description'         => __( 'Popup ID', 'popup-maker' ),
					'type'                => 'integer',
					'validation_callback' => array( __CLASS__, 'endpoint_absint' ),
					'sanitize_callback'   => 'absint',
				),
			),
		) );
	}

	/**
	 * Creates and returns a 1x1 tracking gif to the browser.
	 */
	public static function serve_pixel() {
		$gif = self::get_file( Popup_Maker::$DIR . 'assets/images/beacon.gif' );
		header( 'Content-Type: image/gif' );
		header( 'Content-Length: ' . strlen( $gif ) );
		exit( $gif );
	}

	/**
	 * @param $path
	 *
	 * @return bool|string
	 */
	public static function get_file( $path ) {

		if ( function_exists( 'realpath' ) ) {
			$path = realpath( $path );
		}

		if ( ! $path || ! @is_file( $path ) ) {
			return '';
		}

		return @file_get_contents( $path );
	}

	/**
	 * Returns a 204 no content header.
	 */
	public static function serve_no_content() {
		header( "HTTP/1.0 204 No Content" );
		header( 'Content-Type: image/gif' );
		header( 'Content-Length: 0' );
		exit;
	}

	/**
	 * Serves a proper json response.
	 *
	 * @param mixed $data
	 */
	public static function serve_json( $data = 0 ) {
		header( 'Content-Type: application/json' );
		echo PUM_Utils_Array::safe_json_encode( $data );
		exit;
	}

}

