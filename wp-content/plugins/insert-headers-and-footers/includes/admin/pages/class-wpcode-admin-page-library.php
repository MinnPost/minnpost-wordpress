<?php
/**
 * Admin page for the snippets library.
 *
 * @package WPCode
 */

/**
 * WPCode_Admin_Page_Library class.
 */
class WPCode_Admin_Page_Library extends WPCode_Admin_Page {

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-library';
	/**
	 * We always show the library on this page.
	 *
	 * @var bool
	 */
	protected $show_library = true;

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Library', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Add page-specific hooks.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->process_message();
		add_action( 'admin_init', array( $this, 'maybe_add_from_library' ) );
	}

	/**
	 * Handle grabbing snippets from the library.
	 *
	 * @return void
	 */
	public function maybe_add_from_library() {
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'wpcode_add_from_library' ) ) {
			return;
		}
		$library_id = isset( $_GET['snippet_library_id'] ) ? absint( $_GET['snippet_library_id'] ) : 0;

		if ( empty( $library_id ) ) {
			return;
		}

		$snippet = wpcode()->library->create_new_snippet( $library_id );

		if ( $snippet ) {
			$url = add_query_arg(
				array(
					'page'       => 'wpcode-snippet-manager',
					'snippet_id' => $snippet->get_id(),
				),
				admin_url( 'admin.php' )
			);
		} else {
			$url = add_query_arg(
				array(
					'message' => 1,
				),
				remove_query_arg(
					array(
						'_wpnonce',
						'snippet_library_id',
					)
				)
			);
		}

		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Markup for the Library page content.
	 *
	 * @return void
	 */
	public function output_content() {
		$library_data = wpcode()->library->get_data();
		$categories   = $library_data['categories'];
		$snippets     = $library_data['snippets'];

		$this->get_library_markup( $categories, $snippets );
	}

	/**
	 * For this page we output just a title now.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<div class="wpcode-column">
			<h1><?php esc_html_e( 'Snippet Library', 'insert-headers-and-footers' ); ?></h1>
		</div>
		<?php
	}

	/**
	 * Process messages specific to this page.
	 *
	 * @return void
	 */
	public function process_message() {
		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! isset( $_GET['message'] ) ) {
			return;
		}

		$messages = array(
			1 => __( 'We encountered an error while trying to load the snippet data. Please try again.', 'insert-headers-and-footers' ),
		);
		$message  = absint( $_GET['message'] );
		// phpcs:enable WordPress.Security.NonceVerification

		if ( ! isset( $messages[ $message ] ) ) {
			return;
		}

		$this->set_error_message( $messages[ $message ] );

	}
}
