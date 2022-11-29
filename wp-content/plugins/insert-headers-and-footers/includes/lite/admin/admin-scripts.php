<?php
/**
 * Load lite-specific scripts here.
 *
 * @package WPCode
 */

add_action( 'admin_enqueue_scripts', 'wpcode_admin_scripts_global_lite' );

/**
 * Load version-specific global scripts.
 *
 * @return void
 */
function wpcode_admin_scripts_global_lite() {
	// Don't load global admin scripts if headers & footers mode is enabled.
	if ( wpcode()->settings->get_option('headers_footers_mode') ) {
		return;
	}
	wpcode_admin_scripts_global();
}
