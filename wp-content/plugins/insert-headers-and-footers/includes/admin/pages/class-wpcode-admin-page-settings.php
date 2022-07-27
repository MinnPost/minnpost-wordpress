<?php
/**
 * Settings admin page.
 *
 * @package WPCode
 */

/**
 * Class for the Settings admin page.
 */
class WPCode_Admin_Page_Settings extends WPCode_Admin_Page {

	/**
	 * The page slug to be used when adding the submenu.
	 *
	 * @var string
	 */
	public $page_slug = 'wpcode-settings';

	/**
	 * The action used for the nonce.
	 *
	 * @var string
	 */
	private $action = 'wpcode-settings';

	/**
	 * The nonce name field.
	 *
	 * @var string
	 */
	private $nonce_name = 'wpcode-settings_nonce';

	/**
	 * Call this just to set the page title translatable.
	 */
	public function __construct() {
		$this->page_title = __( 'Settings', 'insert-headers-and-footers' );
		parent::__construct();
	}

	/**
	 * Register hook on admin init just for this page.
	 *
	 * @return void
	 */
	public function page_hooks() {
		add_action( 'admin_init', array( $this, 'submit_listener' ) );
	}

	/**
	 * Wrap this page in a form tag.
	 *
	 * @return void
	 */
	public function output() {
		?>
		<form action="<?php echo esc_url( $this->get_page_action_url() ); ?>" method="post">
			<?php parent::output(); ?>
		</form>
		<?php
	}

	/**
	 * The Settings page output.
	 *
	 * @return void
	 */
	public function output_content() {
		$header_and_footers = wpcode()->settings->get_option( 'headers_footers_mode' );

		$description = __( 'This allows you to disable all Code Snippets functionality and have a single "Headers & Footers" item under the settings menu.', 'insert-headers-and-footers' );

		$description .= '<br />';
		$description .= sprintf(
		// Translators: Placeholders make the text bold.
			__( '%1$sNOTE:%2$s Please use this setting with caution. It will disable all custom snippets that you add using the new snippet management interface.', 'insert-headers-and-footers' ),
			'<strong>',
			'</strong>'
		);

		$this->metabox_row(
			__( 'Headers & Footers mode', 'insert-headers-and-footers' ),
			$this->get_checkbox_toggle(
				$header_and_footers,
				'headers_footers_mode',
				$description
			),
			'headers_footers_mode'
		);

		wp_nonce_field( $this->action, $this->nonce_name );
	}


	/**
	 * For this page we output a title and the save button.
	 *
	 * @return void
	 */
	public function output_header_bottom() {
		?>
		<div class="wpcode-column">
			<h1><?php esc_html_e( 'Settings', 'insert-headers-and-footers' ); ?></h1>
		</div>
		<div class="wpcode-column">
			<button class="wpcode-button" type="submit">
				<?php esc_html_e( 'Save Changes', 'insert-headers-and-footers' ); ?>
			</button>
		</div>
		<?php
	}

	/**
	 * If the form is submitted attempt to save the values.
	 *
	 * @return void
	 */
	public function submit_listener() {
		if ( ! isset( $_REQUEST[ $this->nonce_name ] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST[ $this->nonce_name ] ), $this->action ) ) {
			// Nonce is missing, so we're not even going to try.
			return;
		}

		$settings = array(
			'headers_footers_mode' => isset( $_POST['headers_footers_mode'] ),
		);

		wpcode()->settings->bulk_update_options( $settings );

		if ( true === $settings['headers_footers_mode'] ) {
			wp_safe_redirect(
				add_query_arg(
					array(
						'page'    => 'wpcode-headers-footers',
						'message' => 1,
					),
					admin_url( 'options-general.php' )
				)
			);
			exit;
		}

		$this->set_success_message( __( 'Settings Saved.', 'insert-headers-and-footers' ) );
	}
}
