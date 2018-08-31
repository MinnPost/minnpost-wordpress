<?php
/**
 * Plugin Name: Redis Option Cache clear
 * Description: Clear the cached value from Redis when an option is added, updated, or deleted.
 *
 */

/**
* Additional hooks to option updates to ensure they get refreshed in the
* Redis object-cache when they change.
*/
add_action( 'added_option', 'redis_fix_clear_alloptions_cache' );
add_action( 'updated_option', 'redis_fix_clear_alloptions_cache' );
add_action( 'deleted_option', 'redis_fix_clear_alloptions_cache' );


/**
* Fix a race condition in options caching
*
* See https://core.trac.wordpress.org/ticket/31245
* and https://github.com/tillkruss/redis-cache/issues/58
*
*/
if ( ! function_exists( 'redis_fix_clear_alloptions_cache' ) ) :
	function redis_fix_clear_alloptions_cache( $option ) {

		// error_log("added/updated/deleted option: $option");
		if ( false === wp_installing() ) {
			$alloptions = wp_load_alloptions(); // alloptions should be cached at this point
			// If option is part of the alloptions collection then clear it.
			if ( array_key_exists( $option, $alloptions ) ) {
				wp_cache_delete( $option, 'options' );
				// error_log("deleted from cache group 'options': $option");
			}
		}
	}
endif;
