<?php

/**
 * Review class.
 *
 * @since 1.6.1
 */
class IHAF_Review {

	/**
	 * The HTML code for the review notice.
	 * Used to display the review notice at the proper time.
	 *
	 * @var string
	 */
	private $notice = '';

	/**
	 * Primary class constructor.
	 *
	 * @since 1.6.1
	 */
	public function __construct() {

		// Admin notice requesting review.
		add_action( 'admin_init', array( $this, 'review_request' ) );
		add_action( 'wp_ajax_ihaf_notice_dismiss', array( $this, 'review_dismiss' ) );
		add_action( 'admin_notices', array( $this, 'maybe_show_admin_notices' ) );

		// Admin footer text.
		add_filter( 'admin_footer_text', array( $this, 'admin_footer' ), 1, 2 );
	}

	/**
	 * Add admin notices as needed for reviews.
	 *
	 * @since 1.6.1
	 */
	public function review_request() {

		// Only consider showing the review request to admin users.
		if ( ! is_super_admin() ) {
			return;
		}

		// Verify that we can do a check for reviews.
		$notices = get_option( 'ihaf_admin_notices', array() );
		$time    = time();
		$load    = false;

		if ( empty( $notices['review_request'] ) ) {
			$notices['review_request'] = array(
				'time'      => $time,
				'dismissed' => false,
			);

			update_option( 'ihaf_admin_notices', $notices );

			return;
		}

		// Check if it has been dismissed or not.
		if ( ( isset( $notices['review_request']['dismissed'] ) && ! $notices['review_request']['dismissed'] ) ) {
			$load = true;
		}

		// If we cannot load, return early.
		if ( ! $load ) {
			return;
		}

		// Show the notice.
		$this->review_lite();
	}

	/**
	 * Maybe show Lite review request.
	 *
	 * @since 1.6.1
	 */
	public function review_lite() {

		// Fetch when plugin was initially installed.
		$activated = get_option( 'ihaf_activated', array() );

		if ( ! empty( $activated['lite'] ) ) {
			// Only continue if plugin has been installed for at least 7 days.
			if ( ( $activated['lite'] + ( DAY_IN_SECONDS * 7 ) ) > time() ) {
				return;
			}
		} else {
			$activated['lite'] = time();

			update_option( 'ihaf_activated', $activated );

			return;
		}

		// Only proceed with displaying if the user added a script using IHAF.
		if ( ! $this->has_updated_settings() ) {
			return;
		}

		ob_start();

		// We have a candidate! Output a review message.
		?>
		<p><?php esc_html_e( 'Hey, I noticed you have been using Insert Headers and Footers for some time - that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?', 'insert-headers-and-footers' ); ?></p>
		<p>
			<strong><?php echo wp_kses( __( '~ Syed Balkhi<br>Founder of WPBeginner', 'insert-headers-and-footers' ), array( 'br' => array() ) ); ?></strong>
		</p>
		<p>
			<a href="https://wordpress.org/support/plugin/insert-headers-and-footers/reviews/?filter=5#new-post" class="ihaf-notice-dismiss ihaf-review-out" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Ok, you deserve it', 'insert-headers-and-footers' ); ?></a><br>
			<a href="#" class="ihaf-notice-dismiss" rel="noopener noreferrer"><?php esc_html_e( 'Nope, maybe later', 'insert-headers-and-footers' ); ?></a><br>
			<a href="#" class="ihaf-notice-dismiss" rel="noopener noreferrer"><?php esc_html_e( 'I already did', 'insert-headers-and-footers' ); ?></a>
		</p>
		<?php

		$this->notice = ob_get_clean();
		// If we got this far, let's make sure we also load the needed js.
		$this->add_enqueue_scripts_hook();
	}

	/**
	 * If the notice is available, show it.
	 *
	 * @return void
	 */
	public function maybe_show_admin_notices() {
		if ( empty( $this->notice ) ) {
			// If no notice to show, bail early.
			return;
		}
		echo '<div class="ihaf-notice notice notice-info is-dismissible" id="ihaf-notice-review_request">';
		echo '<p>' . $this->notice . '</p>';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</div>';
	}

	/**
	 * Dismiss the review admin notice.
	 *
	 * @since 1.6.1
	 */
	public function review_dismiss() {

		check_ajax_referer( 'ihaf_admin_notice', 'nonce' );

		$notices = get_option( 'ihaf_admin_notices', array() );

		$notices['review_request'] = array(
			'time'      => time(),
			'dismissed' => true,
		);
		update_option( 'ihaf_admin_notices', $notices );

		wp_send_json_success();
	}

	/**
	 * When user is on a IHAF related admin page, display footer text
	 * that graciously asks them to rate us.
	 *
	 * @param string $text Footer text.
	 *
	 * @return string
	 * @since 1.6.1
	 *
	 */
	public function admin_footer( $text ) {

		global $current_screen;

		if ( ! empty( $current_screen->id ) && strpos( $current_screen->id, 'insert-headers-and-footers' ) !== false ) {
			$url  = 'https://wordpress.org/support/plugin/insert-headers-and-footers/reviews/?filter=5#new-post';
			$text = sprintf(
				wp_kses( /* translators: $1$s - IHAF plugin name; $2$s - WP.org review link; $3$s - WP.org review link. */
					__( 'Please rate %1$s <a href="%2$s" target="_blank" rel="noopener noreferrer">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%3$s" target="_blank" rel="noopener">WordPress.org</a> to help us spread the word. Thank you from the WPBeginner team!', 'insert-headers-and-footers' ),
					array(
						'a' => array(
							'href'   => array(),
							'target' => array(),
							'rel'    => array(),
						),
					)
				),
				'<strong>Insert Headers and Footers</strong>',
				$url,
				$url
			);
		}

		return $text;
	}

	/**
	 * Checks if the uses the plugin to load any scripts.
	 *
	 * @return bool
	 */
	public function has_updated_settings() {
		$header_option = get_option( 'ihaf_insert_header' );
		if ( ! empty( $header_option ) ) {
			return true;
		}
		$body_option = get_option( 'ihaf_insert_body' );
		if ( ! empty( $body_option ) ) {
			return true;
		}
		$footer_option = get_option( 'ihaf_insert_footer' );
		if ( ! empty( $footer_option ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Just adds an action to admin_enqueue_scripts.
	 *
	 * @return void
	 */
	public function add_enqueue_scripts_hook() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue scripts needed to dismiss the admin notice.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'ihaf-admin-notice', insert_headers_and_footers()->plugin->url . 'assets/js/admin/notice.min.js', array( 'jquery' ), insert_headers_and_footers()->plugin->version, true );
		wp_localize_script(
			'ihaf-admin-notice',
			'ihaf_admin_notices',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ihaf_admin_notice' ),
			)
		);
	}

}

new IHAF_Review();
