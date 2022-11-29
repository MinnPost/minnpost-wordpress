<?php
/**
 * Lite-specific admin notices.
 */

add_action( 'admin_init', 'wpcode_maybe_add_library_connect_notice' );

/**
 * Show a prompt to connect to the WPCode Library to get access to more snippets.
 *
 * @return void
 */
function wpcode_maybe_add_library_connect_notice() {
	if ( wpcode()->library_auth->has_auth() || ! isset( $_GET['page'] ) || 0 !== strpos( $_GET['page'], 'wpcode' ) ) {
		return;
	}

	$settings_url = add_query_arg(
		array(
			'page' => 'wpcode-settings',
		),
		admin_url( 'admin.php' )
	);

	WPCode_Notice::info(
		sprintf(
			__( '%1$sConnect to the WPCode Library%2$s for to get access to %3$smore FREE snippets%4$s!', 'insert-headers-and-footers' ),
			'<a href="' . $settings_url . '" class="wpcode-start-auth">',
			'</a>',
			'<strong>',
			'</strong>'
		),
		array(
			'dismiss' => WPCode_Notice::DISMISS_GLOBAL,
			'slug'    => 'wpcode-library-connect-lite',
		)
	);
}
