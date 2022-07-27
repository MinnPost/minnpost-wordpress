<?php
/**
 * Generic helpers used in the plugin.
 *
 * @package WPCode
 */

/**
 * Get a URL with UTM parameters.
 *
 * @param string $url The URL to add the params to.
 * @param string $medium The marketing medium.
 * @param string $campaign The campaign.
 *
 * @return string
 */
function wpcode_utm_url( $url, $medium = '', $campaign = '' ) {
	return add_query_arg(
		array(
			'utm_source'   => 'plugin',
			'utm_medium'   => sanitize_key( $medium ),
			'utm_campaign' => sanitize_key( $campaign ),
			'utm_content'  => WPCode()->version,
		),
		$url
	);
}
