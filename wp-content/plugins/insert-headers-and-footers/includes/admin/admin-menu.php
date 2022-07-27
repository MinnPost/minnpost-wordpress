<?php
/**
 * Add WPCode admin menus.
 *
 * @package WPCode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'wpcode_register_admin_menu', 9 );
add_filter( 'plugin_action_links_' . WPCODE_PLUGIN_BASENAME, 'wpcode_add_plugin_action_links' );

/**
 * Register the admin menu items.
 *
 * @return void
 */
function wpcode_register_admin_menu() {
	if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
		wpcode_load_admin_pages();

		return;
	}
	$svg         = get_wpcode_icon( 'logo', 36, 34, '-10 -6 80 80' );
	$wpcode_icon = 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

	add_menu_page( __( 'Code Snippets', 'insert-headers-and-footers' ), __( 'Code Snippets', 'insert-headers-and-footers' ), 'wpcode_edit_snippets', 'wpcode', 'wpcode_admin_menu_page', $wpcode_icon, '81.45687234432916' );

	wpcode_load_admin_pages();
}

/**
 * Generic handler for the admin menu page.
 *
 * @return void
 */
function wpcode_admin_menu_page() {
	do_action( 'wpcode_admin_page' );
}

/**
 * Load the admin pages.
 *
 * @return void
 */
function wpcode_load_admin_pages() {
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page.php';
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-headers-footers.php';

	if ( wpcode()->settings->get_option( 'headers_footers_mode' ) ) {
		new WPCode_Admin_Page_Headers_Footers( true );

		return;
	}

	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-code-snippets.php';
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-snippet-manager.php';
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-library.php';
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-generator.php';
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-tools.php';
	require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-admin-page-settings.php';

	new WPCode_Admin_Page_Code_Snippets();
	new WPCode_Admin_Page_Snippet_Manager();
	new WPCode_Admin_Page_Headers_Footers();
	new WPCode_Admin_Page_Library();
	new WPCode_Admin_Page_Generator();
	new WPCode_Admin_Page_Tools();
	new WPCode_Admin_Page_Settings();
}

/**
 * Add a link to the code snippets list in the plugins list view.
 *
 * @param array $links The links specific to our plugin.
 *
 * @return array
 */
function wpcode_add_plugin_action_links( $links ) {
	$wpcode_links = array(
		sprintf(
			'<a href="%1$s">%2$s</a>',
			add_query_arg(
				array(
					'page' => 'wpcode',
				),
				admin_url( 'admin.php' )
			),
			esc_html__( 'Code Snippets', 'insert-headers-and-footers' )
		),
	);

	return array_merge( $wpcode_links, $links );
}
