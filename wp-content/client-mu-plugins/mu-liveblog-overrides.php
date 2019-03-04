<?php
/**
 * Plugin Name: Liveblog Overrides
 * Description: Override things in the liveblog plugin that run too early for other plugin or theme filters.
 *
 */

/**
* We can override liveblog features here.
*
* See https://github.com/Automattic/liveblog/issues/602#issuecomment-437332713
*
*/
if ( ! function_exists( 'mu_override_liveblog_features' ) ) :
	add_filter( 'liveblog_features', 'mu_override_liveblog_features' );
	function mu_override_liveblog_features( $features ) {
		$features = array( 'commands', 'emojis', 'authors' );
		return $features;
	}
endif;
