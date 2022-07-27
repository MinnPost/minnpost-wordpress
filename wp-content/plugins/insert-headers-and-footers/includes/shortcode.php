<?php
/**
 * Handle the generic WPCode shortcode.
 *
 * @package WPCode
 */

add_shortcode( 'wpcode', 'wpcode_shortcode_handler' );

/**
 * Generic handler for the shortcode.
 *
 * @param array $args The shortcode attributes.
 *
 * @return string
 */
function wpcode_shortcode_handler( $args ) {
	$atts = wp_parse_args(
		$args,
		array(
			'id' => 0,
		)
	);

	if ( 0 === $atts['id'] ) {
		return '';
	}

	return wpcode()->execute->get_snippet_output( absint( $atts['id'] ) );
}
