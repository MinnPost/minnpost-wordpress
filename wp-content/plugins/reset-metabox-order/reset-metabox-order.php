<?php
/*
Plugin Name: Reset Metabox Order
Description: Allow WordPress users to reset the metabox order for the post edit screen.
Version: 0.0.1
Author: Jonathan Stegall
Author URI: https://code.minnpost.com
Text Domain: reset-metabox-order
License: GPL2+
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

class Reset_Metabox_Order {

	/**
	 * @var string
	 * The plugin version
	*/
	private $version;

	/**
	 * @var string
	 * The setting name for the version
	*/
	private $version_option_name;

	/**
	 * @var object
	 * Static property to hold an instance of the class; this seems to make it reusable
	 *
	 */
	static $instance = null;

	/**
	 * Load the static $instance property that holds the instance of the class.
	 * This instance makes the class reusable by other plugins
	*
	 * @return object
	*
	*/
	static public function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Reset_Metabox_Order();
		}
		return self::$instance;
	}

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	public function __construct() {

		$this->version             = '0.0.3';
		$this->version_option_name = 'reset_metabox_order_version';
		$this->slug                = 'reset-metabox-order';

		$this->add_actions();

	}

	private function add_actions() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		}
	}

	/**
	 * Reset user metabox order.
	 *
	*/
	public function user_metabox_order() {

		$user_id = get_current_user_id();
		if ( 0 === $user_id ) {
			return false;
		}

		global $wpdb;
		$sql = 'delete from wp_usermeta where user_id = %d and meta_key like "%s"';
		$vars = array($user_id, 'meta-box%' );
		$query = $wpdb->prepare($sql, $vars);
		$result = $wpdb->query( $wpdb->prepare($sql, $vars) );

		if ( false !== $result ) {
			return $result;
		} else {
			return $query;
		}
	}

	/**
	* Create WordPress admin options page
	*
	*/
	public function create_admin_menu() {
		$capability = 'edit_posts';
		add_submenu_page( 'edit.php', 'Reset Metabox Order', 'Reset Metabox Order', $capability, $this->slug, array( $this, 'show_admin_page' ) );
	}

	/**
	* Display the admin settings page
	*
	* @return void
	*/
	public function show_admin_page() {
		$url_data = filter_input_array( INPUT_GET, FILTER_SANITIZE_STRING );
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( get_admin_page_title(), 'reset-metabox-order' ); ?></h1>
			<?php if ( empty( $url_data['action'] ) ) : ?>
				<form method="get" action="edit.php">
					<input type="hidden" name="action" value="reset-metabox-order" ?>
					<input type="hidden" name="page" value="<?php echo esc_attr( $this->slug ); ?>" ?>
					<h3><?php echo esc_html__( 'Press the button to reset metabox order for the logged in user.', 'reset-metabox-order' ); ?></h3>
					<?php
						submit_button( esc_html__( 'Refresh', 'reset-metabox-order' ), 'primary', 'submit' );
					?>
				</form>
			<?php else : ?>
				<?php if ( 'reset-metabox-order' === $url_data['action'] ) : ?>
					<?php
					$result = $this->user_metabox_order();
					?>
					<?php if ( false !== $result ) : ?>
						<p><?php echo __( 'Metabox order has been reset by deleting ' . $result . ' rows. If you change the order again, you can revisit this page and press the button.', 'reset-metabox-order' ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

}

// start doing stuff.
add_action( 'plugins_loaded', array( 'Reset_Metabox_Order', 'get_instance' ) );
