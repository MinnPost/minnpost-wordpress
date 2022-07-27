<?php
/**
 * Code snippets admin main list page.
 *
 * @package WPCode
 */

/**
 * Class for the code snippets page.
 */
class WPCode_Admin_Page_Code_Snippets extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode';

	/**
	 * Instance of the code snippets table.
	 *
	 * @see WP_List_Table
	 * @var WPCode_Code_Snippets_Table
	 */
	private $snippets_table;

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Code Snippets', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Page-specific hooks, init the custom WP_List_Table.
	 *
	 * @return void
	 */
	public function page_hooks() {
		$this->process_message();
		add_action( 'current_screen', array( $this, 'init_table' ) );
		add_action( 'admin_init', array( $this, 'maybe_capture_filter' ) );
		add_action( 'load-toplevel_page_wpcode', array( $this, 'maybe_process_bulk_action' ) );
		add_filter( 'screen_options_show_screen', '__return_false' );
	}

	/**
	 * If the referer is set, remove and redirect.
	 *
	 * @return void
	 */
	public function maybe_capture_filter() {
		if ( ! empty( $_REQUEST['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) && isset( $_REQUEST['filter_action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect(
				remove_query_arg(
					array(
						'_wp_http_referer',
						'_wpnonce',
					),
					wp_unslash( $_SERVER['REQUEST_URI'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				)
			);
			exit;
		}
		if ( ! empty( $_REQUEST['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) && isset( $_REQUEST['filter_clear'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_safe_redirect(
				add_query_arg(
					'page',
					'wpcode',
					admin_url( 'admin.php' )
				)
			);

			exit;
		}
	}

	/**
	 * Listener for bulk actions.
	 *
	 * @return void
	 */
	public function maybe_process_bulk_action() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$ids    = isset( $_GET['snippet_id'] ) ? array_map( 'absint', (array) $_GET['snippet_id'] ) : array();
		$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : false;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( empty( $ids ) || empty( $action ) ) {
			return;
		}
		if ( empty( $_GET['_wpnonce'] ) ) {
			return;
		}

		if (
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'bulk-wpcode-snippets' ) &&
			! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'wpcode_' . $action . '_nonce' )
		) {
			return;
		}

		$update_status_actions = array( 'trash', 'untrash' );

		if ( in_array( $action, $update_status_actions, true ) ) {
			$newstatus = 'trash' === $action ? 'trash' : 'draft';
			foreach ( $ids as $id ) {
				wp_update_post(
					array(
						'ID'          => $id,
						'post_status' => $newstatus,
					)
				);
			}
		}
		if ( 'delete' === $action ) {
			foreach ( $ids as $id ) {
				wp_delete_post( $id );
			}
		}
		$message = array(
			rtrim( $action, 'e' ) . 'ed' => count( $ids ),
		);

		wpcode()->cache->cache_all_loaded_snippets();

		// Clear used library snippets.
		delete_transient( 'wpcode_used_library_snippets' );

		wp_safe_redirect(
			add_query_arg(
				$message,
				remove_query_arg(
					array(
						'action',
						'action2',
						'_wpnonce',
						'snippet_id',
						'paged',
						'_wp_http_referer',
					)
				)
			)
		);
		exit;

	}

	/**
	 * Init the custom table for the snippets list.
	 *
	 * @return void
	 */
	public function init_table() {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/pages/class-wpcode-code-snippets-table.php';

		$this->snippets_table = new WPCode_Code_Snippets_Table();
	}

	/**
	 * Output the custom table and page content.
	 *
	 * @return void
	 */
	public function output_content() {
		$this->snippets_table->prepare_items();

		?>
		<form id="wpcode-code-snippets-table" method="get" action="<?php echo esc_url( admin_url( 'admin.php?page=wpcode' ) ); ?>">
			<input type="hidden" name="page" value="wpcode"/>
			<?php
			$this->snippets_table->search_box( esc_html__( 'Search Snippets', 'insert-headers-and-footers' ), 'wpcode_snippet_search' );
			$this->snippets_table->views();
			$this->snippets_table->display();
			?>

		</form>
		<?php
	}

	/**
	 * Content of the bottom row of the header.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		$add_new_url = admin_url( 'admin.php?page=wpcode-snippet-manager' );
		?>
		<div class="wpcode-column wpcode-title-button">
			<h1><?php esc_html_e( 'All Snippets', 'insert-headers-and-footers' ); ?></h1>
			<a class="wpcode-button" href="<?php echo esc_url( $add_new_url ); ?>">
				<?php esc_html_e( 'Add New', 'insert-headers-and-footers' ); ?>
			</a>
		</div>
		<?php
	}

	/**
	 * Capture screen-specific messages and add notices.
	 *
	 * @return void
	 */
	public function process_message() {

		// phpcs:disable WordPress.Security.NonceVerification
		if ( ! empty( $_GET['trashed'] ) ) {
			$count  = absint( $_GET['trashed'] );
			$notice = sprintf( /* Translators: %d - Trashed snippets count. */
				_n( '%d snippet was successfully moved to Trash.', '%d snippets were successfully moved to Trash.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}

		if ( ! empty( $_GET['untrashed'] ) ) {
			$count  = absint( $_GET['untrashed'] );
			$notice = sprintf( /* translators: %d - Restored from trash snippets count. */
				_n( '%d snippet was successfully restored.', '%d snippet were successfully restored.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}

		if ( ! empty( $_GET['deleted'] ) ) {
			$count  = absint( $_GET['deleted'] );
			$notice = sprintf( /* translators: %d - Deleted snippets count. */
				_n( '%d snippet was successfully permanently deleted.', '%d snippets were successfully permanently deleted.', $count, 'insert-headers-and-footers' ),
				$count
			);
		}
		// phpcs:enable WordPress.Security.NonceVerification

		if ( isset( $notice ) ) {
			$this->set_success_message( $notice );
		}
	}
}
