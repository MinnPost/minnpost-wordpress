<?php
/**
 * Admin pages abstract class.
 *
 * @package WPCode
 */

/**
 * Class Admin_Page
 */
abstract class WPCode_Admin_Page {

	/**
	 * The page slug.
	 *
	 * @var string
	 */
	public $page_slug = '';

	/**
	 * The page title.
	 *
	 * @var string
	 */
	public $page_title = '';

	/**
	 * The menu title, defaults to the page title.
	 *
	 * @var string
	 */
	public $menu_title;

	/**
	 * If there's an error message, let's store it here.
	 *
	 * @var string
	 */
	public $message_error;

	/**
	 * If there's a success message, store it here.
	 *
	 * @var string
	 */
	public $message_success;
	/**
	 * The code type to be used by CodeMirror.
	 *
	 * @var string
	 */
	public $code_type = 'html';
	/**
	 * Whether the current user can edit the code on the current page.
	 *
	 * @var bool
	 */
	protected $can_edit = false;
	/**
	 * If true, the snippet library is shown, otherwise, we display
	 * the snippet editor.
	 *
	 * @var bool
	 */
	protected $show_library = false;

	/**
	 * The current view.
	 *
	 * @var string
	 */
	public $view = '';

	/**
	 * The available views for this page.
	 *
	 * @var array
	 */
	public $views = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! isset( $this->menu_title ) ) {
			$this->menu_title = $this->page_title;
		}

		$this->hooks();
	}

	/**
	 * Add hooks to register the page and output content.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', array( $this, 'add_page' ) );
		$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		// Only load if we are actually on the desired page.
		if ( $this->page_slug !== $page ) {
			return;
		}
		add_action( 'wpcode_admin_page', array( $this, 'output' ) );
		add_action( 'wpcode_admin_page', array( $this, 'output_footer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'page_scripts' ) );
		add_filter( 'admin_body_class', array( $this, 'page_specific_body_class' ) );
		add_filter( 'wpcode_admin_js_data', array( $this, 'maybe_add_library_data' ) );

		$this->setup_views();
		$this->set_current_view();
		$this->page_hooks();
	}

	/**
	 * Override in child class to define page-specific hooks that will run only
	 * after checks have been passed.
	 *
	 * @return void
	 */
	public function page_hooks() {

	}

	/**
	 * Add the submenu page.
	 *
	 * @return void
	 */
	public function add_page() {
		add_submenu_page( 'wpcode', $this->page_title, $this->menu_title, 'wpcode_edit_snippets', $this->page_slug, 'wpcode_admin_menu_page' );
	}

	/**
	 * If the page has views, this is where you should assign them to $this->views.
	 *
	 * @return void
	 */
	protected function setup_views() {

	}

	/**
	 * Set the current view from the query param also checking it's a registered view for this page.
	 *
	 * @return void
	 */
	protected function set_current_view() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( ! isset( $_GET['view'] ) ) {
			return;
		}
		$view = sanitize_text_field( wp_unslash( $_GET['view'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		if ( array_key_exists( $view, $this->views ) ) {
			$this->view = $view;
		}
	}

	/**
	 * Output the page content.
	 *
	 * @return void
	 */
	public function output() {
		$this->output_header();
		?>
		<div class="wpcode-content">
			<?php
			$this->output_content();
			do_action( "wpcode_admin_page_content_{$this->page_slug}", $this );
			?>
		</div>
		<?php
	}

	/**
	 * Output of the header markup for admin pages.
	 *
	 * @return void
	 */
	public function output_header() {
		?>
		<div class="wpcode-header">
			<div class="wpcode-header-top">
				<div class="wpcode-header-left">
					<?php $this->output_header_left(); ?>
				</div>
				<div class="wpcode-header-right">
					<?php $this->output_header_right(); ?>
				</div>
			</div>
			<div class="wpcode-header-bottom">
				<?php $this->output_header_bottom(); ?>
			</div>
		</div>
		<?php $this->maybe_output_message(); ?>
		<?php
	}

	/**
	 * Output footer markup, mostly used for overlays that are fixed.
	 *
	 * @return void
	 */
	public function output_footer() {
		?>
		<div class="wpcode-modal-overlay"></div>
		<div class="wpcode-notifications-overlay"></div>
		<div class="wpcode-docs-overlay" id="wpcode-docs-overlay">
			<?php $this->logo_image( 'wpcode-help-logo' ); ?>
			<button id="wpcode-help-close" class="wpcode-button-just-icon" type="button">
				<?php wpcode_icon( 'close', 19, 19 ); ?>
			</button>
			<div class="wpcode-docs-content">
				<div id="wpcode-help-search" class="wpcode-search-empty">
					<label>
						<span class="screen-reader-text"><?php esc_html_e( 'Search docs', 'insert-headers-and-footers' ); ?></span>
						<?php wpcode_icon( 'search' ); ?>
						<input type="text" class="wpcode-input-text"/>
					</label>
					<div id="wpcode-help-search-clear" title="<?php esc_attr_e( 'Clear', 'insert-headers-and-footers' ); ?>">
						<?php wpcode_icon( 'close', 14, 14 ); ?>
					</div>
				</div>
				<div id="wpcode-help-no-result" style="display: none;">
					<ul class="wpcode-help-docs">
						<li>
							<span><?php esc_html_e( 'No docs found', 'insert-headers-and-footers' ); ?></span>
						</li>
					</ul>
				</div>
				<div id="wpcode-help-result">
					<ul class="wpcode-help-docs"></ul>
				</div>
				<?php
				$docs = new WPCode_Docs();
				$docs->get_categories_accordion();
				?>
				<div class="wpcode-help-footer">
					<div class="wpcode-help-footer-box">
						<?php wpcode_icon( 'file', 48, 48 ); ?>
						<h3><?php esc_html_e( 'View Documentation', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'Browse documentation, reference material, and tutorials for WPCode.', 'insert-headers-and-footers' ); ?></p>
						<a class="wpcode-button wpcode-button-secondary" href="<?php echo esc_url( wpcode_utm_url( 'https://wpcode.com/docs', 'docs', 'footer' ) ); ?>" target="_blank"><?php esc_html_e( 'View All Documentation', 'insert-headers-and-footers' ); ?></a>
					</div>
					<div class="wpcode-help-footer-box">
						<?php wpcode_icon( 'support', 48, 48 ); ?>
						<h3><?php esc_html_e( 'Get Support', 'insert-headers-and-footers' ); ?></h3>
						<p><?php esc_html_e( 'Submit a ticket and our world class support team will be in touch soon.', 'insert-headers-and-footers' ); ?></p>
						<a class="wpcode-button wpcode-button-secondary" href="https://wordpress.org/support/plugin/insert-headers-and-footers/" target="_blank"><?php esc_html_e( 'Submit a Support Ticket', 'insert-headers-and-footers' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<div class="wpcode-notifications-drawer" id="wpcode-notifications-drawer">
			<div class="wpcode-notifications-header">
				<h3 id="wpcode-active-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of active notifications.
							__( 'New Notifications (%s)', 'insert-headers-and-footers' )
						),
						'<span id="wpcode-notifications-count">' . absint( wpcode()->notifications->get_count() ) . '</span>'
					);
					?>
				</h3>
				<h3 id="wpcode-dismissed-title">
					<?php
					printf(
						wp_kses_post(
						// Translators: Placeholder for the number of dismissed notifications.
							__( 'Notifications (%s)', 'insert-headers-and-footers' )
						),
						'<span id="wpcode-notifications-dismissed-count">' . absint( wpcode()->notifications->get_dismissed_count() ) . '</span>'
					);
					?>
				</h3>
				<button type="button" class="wpcode-button-text" id="wpcode-notifications-show-dismissed">
					<?php esc_html_e( 'Dismissed Notifications', 'insert-headers-and-footers' ); ?>
				</button>
				<button type="button" class="wpcode-button-text" id="wpcode-notifications-show-active">
					<?php esc_html_e( 'Active Notifications', 'insert-headers-and-footers' ); ?>
				</button>
				<button type="button" class="wpcode-just-icon-button wpcode-notifications-close"><?php wpcode_icon( 'close', 12, 12, '0 0 16 16' ); ?></button>
			</div>
			<div class="wpcode-notifications-list">
				<ul class="wpcode-notifications-active">
					<?php
					$notifications = wpcode()->notifications->get_active_notifications();
					foreach ( $notifications as $notification ) {
						$this->get_notification_markup( $notification );
					}
					?>
				</ul>
				<ul class="wpcode-notifications-dismissed">
					<?php
					$notifications = wpcode()->notifications->get_dismissed_notifications();
					foreach ( $notifications as $notification ) {
						$this->get_notification_markup( $notification );
					}
					?>
				</ul>
			</div>
			<div class="wpcode-notifications-footer">
				<button type="button" class="wpcode-button-text wpcode-notification-dismiss" id="wpcode-dismiss-all" data-id="all"><?php esc_html_e( 'Dismiss all', 'insert-headers-and-footers' ); ?></button>
			</div>
		</div>
		<?php
	}

	/**
	 * Get the notification HTML markup for displaying in a list.
	 *
	 * @param array $notification The notification array.
	 *
	 * @return void
	 */
	public function get_notification_markup( $notification ) {
		$type = ! empty( $notification['icon'] ) ? $notification['icon'] : 'info';
		?>
		<li>
			<div class="wpcode-notification-icon"><?php wpcode_icon( $type, 18, 18 ); ?></div>
			<div class="wpcode-notification-content">
				<h4><?php echo esc_html( $notification['title'] ); ?></h4>
				<p><?php echo wp_kses_post( $notification['content'] ); ?></p>
				<p class="wpcode-start"><?php echo esc_html( $notification['start'] ); ?></p>
				<div class="wpcode-notification-actions">
					<?php
					$main_button = ! empty( $notification['btns']['main'] ) ? $notification['btns']['main'] : false;
					$alt_button  = ! empty( $notification['btns']['alt'] ) ? $notification['btns']['alt'] : false;
					if ( $main_button ) {
						?>
						<a href="<?php echo esc_url( $main_button['url'] ); ?>" class="wpcode-button wpcode-button-small" target="_blank">
							<?php echo esc_html( $main_button['text'] ); ?>
						</a>
						<?php
					}
					if ( $alt_button ) {
						?>
						<a href="<?php echo esc_url( $alt_button['url'] ); ?>" class="wpcode-button wpcode-button-secondary wpcode-button-small" target="_blank">
							<?php echo esc_html( $alt_button['text'] ); ?>
						</a>
						<?php
					}
					?>
					<button type="button" class="wpcode-button-text wpcode-notification-dismiss" data-id="<?php echo esc_attr( $notification['id'] ); ?>"><?php esc_html_e( 'Dismiss', 'insert-headers-and-footers' ); ?></button>
				</div>
			</div>
		</li>
		<?php
	}

	/**
	 * Left side of the header, usually just the logo in this area.
	 *
	 * @return void
	 */
	public function output_header_left() {
		$this->logo_image();
	}

	/**
	 * Logo image.
	 *
	 * @param string $id Id of the image.
	 *
	 * @return void
	 */
	public function logo_image( $id = 'wpcode-header-logo' ) {
		$logo_src = WPCODE_PLUGIN_URL . 'admin/images/wpcode-logo.png';
		// Translators: This simply adds the plugin name before the logo text.
		$alt = sprintf( __( '%s logo', 'insert-headers-and-footers' ), 'WPCode' )
		?>
		<img src="<?php echo esc_url( $logo_src ); ?>" width="132" alt="<?php echo esc_attr( $alt ); ?>" id="<?php echo esc_attr( $id ); ?>"/>
		<?php
	}

	/**
	 * Top right area of the header, by default the notifications and help icons.
	 *
	 * @return void
	 */
	public function output_header_right() {
		$notifications_count = wpcode()->notifications->get_count();
		$dismissed_count     = wpcode()->notifications->get_dismissed_count();
		$data_count          = '';
		if ( $notifications_count > 0 ) {
			$data_count = sprintf(
				'data-count="%d"',
				absint( $notifications_count )
			);
		}
		?>
		<button
				type="button"
				id="wpcode-notifications-button"
				class="wpcode-button-just-icon wpcode-notifications-inbox wpcode-open-notifications"
				data-dismissed="<?php echo esc_attr( $dismissed_count ); ?>"
			<?php echo $data_count; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<?php wpcode_icon( 'inbox', 15, 16 ); ?>
		</button>
		<button class="wpcode-text-button-icon wpcode-show-help" type="button">
			<?php wpcode_icon( 'help', 21 ); ?>
			<?php esc_html_e( 'Help', 'insert-headers-and-footers' ); ?>
		</button>
		<?php
	}

	/**
	 * This is the menu area but on some pages it's just at title.
	 * Tabs could also be used here.
	 *
	 * @return void
	 */
	public function output_header_bottom() {

	}

	/**
	 * Checks if an error or success message is available and outputs using the specific format.
	 *
	 * @return void
	 */
	public function maybe_output_message() {
		$error_message   = $this->get_error_message();
		$success_message = $this->get_success_message();

		?>
		<div class="wrap" id="wpcode-notice-area">
			<?php
			if ( $error_message ) {
				?>
				<div class="error fade notice is-dismissible">
					<p><?php echo wp_kses_post( $error_message ); ?></p>
				</div>
				<?php
			}
			if ( $success_message ) {
				?>
				<div class="updated fade notice is-dismissible">
					<p><?php echo wp_kses_post( $success_message ); ?></p>
				</div>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * If no message is set return false otherwise return the message string.
	 *
	 * @return false|string
	 */
	public function get_error_message() {
		return ! empty( $this->message_error ) ? $this->message_error : false;
	}

	/**
	 * If no message is set return false otherwise return the message string.
	 *
	 * @return false|string
	 */
	public function get_success_message() {
		return ! empty( $this->message_success ) ? $this->message_success : false;
	}

	/**
	 * This is the main page content and you can't get away without it.
	 *
	 * @return void
	 */
	abstract public function output_content();

	/**
	 * If you need to page-specific scripts override this function.
	 * Hooked to 'admin_enqueue_scripts'.
	 *
	 * @return void
	 */
	public function page_scripts() {
	}

	/**
	 * Set a success message to display it in the appropriate place.
	 * Let's use a function so if we decide to display multiple messages in the
	 * same instance it's easy to change the variable to an array.
	 *
	 * @param string $message The message to store as success message.
	 *
	 * @return void
	 */
	public function set_success_message( $message ) {
		$this->message_success = $message;
	}

	/**
	 * Set an error message to display it in the appropriate place.
	 * Let's use a function so if we decide to display multiple messages in the
	 * same instance it's easy to change the variable to an array.
	 *
	 * @param string $message The message to store as error message.
	 *
	 * @return void
	 */
	public function set_error_message( $message ) {
		$this->message_error = $message;
	}

	/**
	 * Add a page-specific body class using the page slug variable..
	 *
	 * @param string $body_class The body class to append.
	 *
	 * @return string
	 */
	public function page_specific_body_class( $body_class ) {

		$body_class .= ' ' . $this->page_slug;

		return $body_class;
	}

	/**
	 * Get the page url to be used in a form action.
	 *
	 * @return string
	 */
	public function get_page_action_url() {
		$args = array(
			'page' => $this->page_slug,
		);
		if ( ! empty( $this->view ) ) {
			$args['view'] = $this->view;
		}

		return add_query_arg( $args, admin_url( 'admin.php' ) );
	}

	/**
	 * If called, this loads CodeMirror on the current admin page with checks.
	 *
	 * @return array|false
	 */
	public function load_code_mirror() {
		if ( ! function_exists( 'wp_enqueue_code_editor' ) ) {
			return false;
		}
		$editor_args = array( 'type' => $this->get_mime_from_code_type() );
		if ( ! $this->can_edit || ! current_user_can( 'wpcode_edit_snippets' ) ) {
			$editor_args['codemirror']['readOnly'] = true;
		}

		// Enqueue code editor and settings for manipulating HTML.
		return wp_enqueue_code_editor( $editor_args );
	}

	/**
	 * Convert generic code type to MIME used by CodeMirror.
	 *
	 * @param string $code_type Optional parameter, if not passed it returns the mime for the currently set $code_type.
	 *
	 * @return string
	 * @see $code_type
	 */
	protected function get_mime_from_code_type( $code_type = '' ) {
		if ( empty( $code_type ) ) {
			$code_type = isset( $this->code_type ) ? $this->code_type : '';
		}

		return wpcode()->execute->get_mime_for_code_type( $code_type );
	}

	/**
	 * Metabox-style layout for admin pages.
	 *
	 * @param string $title The metabox title.
	 * @param string $content The metabox content.
	 * @param string $help The helper text (optional) - if set, a help icon will show up next to the title.
	 *
	 * @return void
	 */
	public function metabox( $title, $content, $help = '' ) {
		?>
		<div class="wpcode-metabox">
			<div class="wpcode-metabox-title">
				<div class="wpcode-metabox-title-text">
					<?php echo esc_html( $title ); ?>
					<?php $this->help_icon( $help ); ?>
				</div>
				<div class="wpcode-metabox-title-toggle">
					<button class="wpcode-metabox-button-toggle" type="button">
						<svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M1.41 7.70508L6 3.12508L10.59 7.70508L12 6.29508L6 0.295079L-1.23266e-07 6.29508L1.41 7.70508Z" fill="#454545"/>
						</svg>
					</button>
				</div>
			</div>
			<div class="wpcode-metabox-content">
				<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Output a help icon with the text passed to it.
	 *
	 * @param string $text The tooltip text.
	 *
	 * @return void
	 */
	public function help_icon( $text = '' ) {
		if ( empty( $text ) ) {
			return;
		}
		?>
		<span class="wpcode-help-tooltip">
			<?php wpcode_icon( 'help', 16, 16, '0 0 20 20' ); ?>
			<span class="wpcode-help-tooltip-text"><?php echo wp_kses_post( $text ); ?></span>
		</span>
		<?php
	}

	/**
	 * Get a WPCode metabox row.
	 *
	 * @param string $label The label of the field.
	 * @param string $input The field input (html).
	 * @param string $id The id for the row.
	 * @param string $show_if_id Conditional logic id, automatically hide if the value of the field with this id doesn't match show if value.
	 * @param string $show_if_value Value(s) to match against, can be comma-separated string for multiple values.
	 *
	 * @return void
	 */
	public function metabox_row( $label, $input, $id = '', $show_if_id = '', $show_if_value = '' ) {
		$show_if_rules = '';
		if ( ! empty( $show_if_id ) ) {
			$show_if_rules = sprintf( 'data-show-if-id="%1$s" data-show-if-value="%2$s"', $show_if_id, $show_if_value );
		}
		?>
		<div class="wpcode-metabox-form-row" <?php echo $show_if_rules; ?>>
			<div class="wpcode-metabox-form-row-label">
				<label for="<?php echo esc_attr( $id ); ?>">
					<?php echo esc_html( $label ); ?>
				</label>
			</div>
			<div class="wpcode-metabox-form-row-input">
				<?php echo $input; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Get a checkbox wrapped with markup to be displayed as a toggle.
	 *
	 * @param bool   $checked Is it checked or not.
	 * @param string $name The name for the input.
	 * @param string $description Field description (optional).
	 *
	 * @return string
	 */
	public function get_checkbox_toggle( $checked, $name, $description = '' ) {
		$markup = '<label class="wpcode-checkbox-toggle">';

		$markup .= '<input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" />';
		$markup .= '<span class="wpcode-checkbox-toggle-slider"></span>';
		$markup .= '</label>';

		if ( ! empty( $description ) ) {
			$markup .= '<p class="description">' . wp_kses_post( $description ) . '</p>';
		}

		return $markup;
	}

	/**
	 * Build the markup for the snippet item. Also used as a template for the js.
	 *
	 * @param array  $snippet The snippet object.
	 * @param string $category The active category to display by default.
	 *
	 * @return void
	 */
	public function get_library_snippet_item( $snippet = array(), $category = '*' ) {
		$title                 = '';
		$url                   = '';
		$description           = '';
		$used_library_snippets = wpcode()->library->get_used_library_snippets();
		$button_text           = __( 'Use snippet', 'insert-headers-and-footers' );
		$pill_text             = '';
		if ( ! empty( $snippet ) ) {
			$url = add_query_arg(
				array(
					'page'   => 'wpcode-snippet-manager',
					'custom' => true,
				),
				admin_url( 'admin.php' )
			);
			if ( 0 !== $snippet['library_id'] ) {
				if ( ! empty( $used_library_snippets[ $snippet['library_id'] ] ) ) {
					$url         = add_query_arg(
						array(
							'page'       => 'wpcode-snippet-manager',
							'snippet_id' => absint( $used_library_snippets[ $snippet['library_id'] ] ),
						),
						admin_url( 'admin.php' )
					);
					$button_text = __( 'Edit snippet', 'insert-headers-and-footers' );
					$pill_text   = __( 'Used', 'insert-headers-and-footers' );
				} else {
					$url = wp_nonce_url(
						add_query_arg(
							array(
								'snippet_library_id' => absint( $snippet['library_id'] ),
								'page'               => 'wpcode-library',
							),
							admin_url( 'admin.php' )
						),
						'wpcode_add_from_library'
					);
				}
			}
			$title       = $snippet['title'];
			$description = $snippet['note'];
		}
		$id            = $snippet['library_id'];
		$button_2_text = '';
		if ( ! empty( $snippet['code'] ) ) {
			$button_2_text = __( 'Preview', 'insert-headers-and-footers' );
		}
		$categories = isset( $snippet['categories'] ) ? $snippet['categories'] : array();

		$this->get_list_item( $id, $title, $description, $url, $button_text, $categories, $button_2_text, 'wpcode-library-preview-button', $pill_text, 'blue', $category );
	}

	/**
	 * Get a list item markup, used for library & generators.
	 *
	 * @param string $id The id used for the data-id param (used for filtering).
	 * @param string $title The title of the item.
	 * @param string $description The item description.
	 * @param string $url The URL for the action button.
	 * @param string $button_text The action button text.
	 * @param array  $categories The categories of this object (for filtering).
	 * @param string $button_2_text (optional) 2nd button text. If left empty, the 2nd button will not be shown.
	 * @param string $button_2_class (optional) 2nd button class.
	 * @param string $pill_text (optional) Display a "pill" with some text in the top right corner.
	 * @param string $pill_class (optional) Custom CSS class for the pill.
	 * @param string $selected_category (optional) Slug of the category selected by default.
	 *
	 * @return void
	 */
	public function get_list_item( $id, $title, $description, $url, $button_text, $categories = array(), $button_2_text = '', $button_2_class = '', $pill_text = '', $pill_class = 'blue', $selected_category = '*' ) {
		$item_class = array(
			'wpcode-list-item',
		);
		if ( ! empty( $pill_text ) ) {
			$item_class[] = 'wpcode-list-item-has-pill';
		}
		$style = '';
		if ( '*' !== $selected_category && ! in_array( $selected_category, $categories, true ) ) {
			$style = 'display:none;';
		}
		?>
		<li class="<?php echo esc_attr( implode( ' ', $item_class ) ); ?>" data-id="<?php echo esc_attr( $id ); ?>" data-categories='<?php echo wp_json_encode( $categories ); ?>' style="<?php echo esc_attr( $style ); ?>">
			<h3 title="<?php echo esc_attr( $title ); ?>"><?php echo esc_html( $title ); ?></h3>
			<?php if ( ! empty( $pill_text ) ) { ?>
				<span class="wpcode-list-item-pill wpcode-list-item-pill-<?php echo esc_attr( $pill_class ); ?>"><?php echo esc_html( $pill_text ); ?></span>
			<?php } ?>
			<div class="wpcode-list-item-actions">
				<div class="wpcode-list-item-description">
					<p><?php echo esc_html( $description ); ?></p>
				</div>
				<div class="wpcode-list-item-buttons">
					<a href="<?php echo esc_url( $url ); ?>" class="wpcode-button wpcode-item-use-button">
						<?php echo esc_html( $button_text ); ?>
					</a>
					<?php if ( ! empty( $button_2_text ) ) { ?>
						<button class="wpcode-button wpcode-button-secondary <?php echo esc_attr( $button_2_class ); ?>" type="button">
							<?php echo esc_html( $button_2_text ); ?>
						</button>
					<?php } ?>
				</div>
			</div>
		</li>
		<?php
	}

	/**
	 * Output the library markup from an array of categories and an array of snippets.
	 *
	 * @param array $categories The snippet categories to show.
	 * @param array $snippets The snippets to show.
	 *
	 * @return void
	 */
	public function get_library_markup( $categories, $snippets ) {
		$selected_category = isset( $categories[0]['slug'] ) ? $categories[0]['slug'] : '*';
		?>
		<div class="wpcode-items-metabox wpcode-metabox">
			<?php $this->get_items_list_sidebar( $categories, __( 'All Snippets', 'insert-headers-and-footers' ), __( 'Search Snippets', 'insert-headers-and-footers' ), $selected_category ); ?>
			<div class="wpcode-items-list">
				<?php
				if ( empty( $snippets ) ) {
					?>
					<div class="wpcode-alert wpcode-alert-warning">
						<?php printf( '<h4>%s</h4>', esc_html__( 'We encountered a problem loading the Snippet Library items, please try again later.', 'insert-headers-and-footers' ) ); ?>
					</div>
					<?php
				}
				?>
				<ul class="wpcode-items-list-category">
					<?php
					foreach ( $snippets as $snippet ) {
						$this->get_library_snippet_item( $snippet, $selected_category );
					}
					?>
				</ul>
			</div>
		</div>
		<?php
		$this->library_preview_modal_content();
	}

	/**
	 * Get the items list sidebar with optional search form.
	 *
	 * @param array  $categories The array of categories to display as filters - each item needs to have the "slug" and "name" keys.
	 * @param string $all_text Text to display on the all items button in the categories list.
	 * @param string $search_label The search label, if left empty the search form is hidden.
	 * @param string $selected_category Slug of the category selected by default.
	 *
	 * @return void
	 */
	public function get_items_list_sidebar( $categories, $all_text = '', $search_label = '', $selected_category = '' ) {
		?>
		<div class="wpcode-items-sidebar">
			<?php if ( ! empty( $search_label ) ) { ?>
				<div class="wpcode-items-search">
					<label for="wpcode-items-search">
						<span class="screen-reader-text"><?php echo esc_html( $search_label ); ?></span>
						<?php wpcode_icon( 'search', 16, 16 ); ?>
					</label>
					<input type="search" id="wpcode-items-search" placeholder="<?php echo esc_html( $search_label ); ?>"/>
				</div>
			<?php } ?>
			<ul class="wpcode-items-categories-list wpcode-items-filters">
				<li>
					<button type="button" data-category="*" class="<?php echo empty( $selected_category ) ? 'wpcode-active' : ''; ?>"><?php echo esc_html( $all_text ); ?></button>
				</li>
				<?php
				foreach ( $categories as $category ) {
					// Mark the first category as active.
					$class = $category['slug'] === $selected_category ? 'wpcode-active' : '';
					?>
					<li>
						<button type="button" class="<?php echo esc_attr( $class ); ?>" data-category="<?php echo esc_attr( $category['slug'] ); ?>"><?php echo esc_html( $category['name'] ); ?></button>
					</li>
				<?php } ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Get the preview modal markup.
	 *
	 * @return void
	 */
	public function library_preview_modal_content() {
		?>
		<div class="wpcode-library-preview wpcode-modal" id="wpcode-library-preview">
			<div class="wpcode-library-preview-header">
				<button type="button" class="wpcode-just-icon-button wpcode-close-modal"><?php wpcode_icon( 'close', 15, 14 ); ?></button>
				<h2><?php esc_html_e( 'Preview Snippet', 'insert-headers-and-footers' ); ?></h2>
			</div>
			<div class="wpcode-library-preview-content">
				<h3>
					<label for="wpcode-code-preview" id="wpcode-preview-title"><?php esc_html_e( 'Code Preview', 'insert-headers-and-footers' ); ?></label>
				</h3>
				<textarea id="wpcode-code-preview"></textarea>
			</div>
			<div class="wpcode-library-preview-buttons">
				<a class="wpcode-button wpcode-button-wide" id="wpcode-preview-use-code"><?php esc_html_e( 'Use Snippet', 'insert-headers-and-footers' ); ?></a>
			</div>
		</div>
		<?php
		$this->code_type = 'text';
		$settings        = $this->load_code_mirror();

		$settings['codemirror']['readOnly'] = 'nocursor';
		wp_add_inline_script( 'code-editor', sprintf( 'jQuery( function() { window.wpcode_editor = wp.codeEditor.initialize( "wpcode-code-preview", %s ); } );', wp_json_encode( $settings ) ) );
	}

	/**
	 * Load library data in JS.
	 *
	 * @param array $data The library data.
	 *
	 * @return array
	 */
	public function maybe_add_library_data( $data ) {
		if ( $this->show_library ) {
			$data['library'] = wpcode()->library->get_data();
		}

		return $data;
	}

	/**
	 * Get the full URL for a view of an admin page.
	 *
	 * @param string $view The view slug.
	 *
	 * @return string
	 */
	public function get_view_link( $view ) {
		return add_query_arg(
			array(
				'page' => $this->page_slug,
				'view' => $view,
			),
			admin_url( 'admin.php' )
		);
	}
}
