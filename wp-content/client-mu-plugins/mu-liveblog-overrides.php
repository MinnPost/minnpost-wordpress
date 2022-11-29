<?php
/**
 * Plugin Name: Liveblog Overrides
 * Description: Override things in the liveblog plugin that run too early for other plugin or theme filters.
 *
 */

if ( ! function_exists( 'mu_override_liveblog_features' ) ) :
	add_filter( 'liveblog_features', 'mu_override_liveblog_features' );
	/**
	 * We can override liveblog features here.
	 *
	 * @param array $features the features to override.
	 * @return array $features the features to override.
	 * See https://github.com/Automattic/liveblog/issues/602#issuecomment-437332713
	 */
	function mu_override_liveblog_features( $features ) {
		$features = array( 'commands', 'emojis', 'authors' );
		return $features;
	}
endif;

if ( ! function_exists( 'wpcom_vip_liveblog_purge_on_new_entries' ) ) :
	add_action( 'liveblog_insert_entry', 'wpcom_vip_liveblog_purge_on_new_entries', 10, 2 );
	/**
	 * Purge the Varnish cache for a liveblog on each new entry.
	 *
	 * Ensures that a Liveblog page isn't cached with stale meta data during an
	 * active liveblog.
	 *
	 * @param  int $comment_id ID of the comment for this new entry.
	 * @param  int $post_id    ID for this liveblog post.
	 */
	function wpcom_vip_liveblog_purge_on_new_entries( int $comment_id, int $post_id ){

		if ( ! function_exists( 'wpcom_vip_purge_edge_cache_for_url' ) ) {
			return;
		}

		// Get the URL for this Liveblog post.
		$permalink = get_permalink( absint( $post_id ) );
		if ( ! $permalink ) {
			return;
		}

		// Purge the Varnish cache for the liveblog post so that new loads of the
		// post include the newest entries.
		wpcom_vip_purge_edge_cache_for_url( $permalink );

	}
endif;
