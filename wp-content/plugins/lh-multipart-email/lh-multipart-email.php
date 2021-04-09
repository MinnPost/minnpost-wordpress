<?php
/**
 * Plugin Name: LH Multipart Email
 * Plugin URI: https://lhero.org/portfolio/lh-multipart-email/
 * Description: Makes all html emails Html and plain text multipart emails
 * Version: 1.12
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com/
 * Text Domain: lh_multipart_email
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
if ( ! class_exists( 'LH_multipart_email_plugin' ) ) {
	/**
	 * Class LH_multipart_email_plugin
	 *
	 * @see: http://wordpress.stackexchange.com/a/191974
	 * @see: http://stackoverflow.com/a/2564472
	 */
	class LH_multipart_email_plugin {
	    
	    
	    private static $instance;
	    
	    static function write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }
	    
	    
		public function phpmailer_init( $phpmailer ) {
			if ( ( $phpmailer->ContentType == 'text/html' ) && empty( $phpmailer->AltBody ) ) {
				if ( ! class_exists( 'Html2Text\Html2Text' ) && ! class_exists( 'Html2Text\Html2TextException' ) ) {
					require_once( "includes/Html2Text.php" );
					require_once( "includes/Html2TextException.php" );
				}
				$phpmailer->AltBody = Html2Text\Html2Text::convert( $phpmailer->Body );
			}
		}
		/**
		 * 
         * This section fixes a problem with sending multiple html mails in one go.
         * See comments below.
		 * When multiple mails are sent in a single GET/POST,
		 * WordPress will reuse the global $phpmailer object.
		 * Before usage it'll clear any properties it fills itself, but
		 * since we're adding an AltBody WordPress has no knowledge of,
		 * it's never cleared when the $phpmailer object is reused.
		 *
		 * This results in any 2nd or later mails sent right after the first
		 * to keep the AltBody that was set on the first mail, effectively
		 * reusing it.
		 *
		 * To prevent this, we're making use of the 'wp_mail' filter, which
		 * is applied by WordPress early on; well before WP checks if it needs
		 * to initialize the $phpmailer object.
		 *
		 * Using this filter we don't touch the $atts being passed,
		 * but if the $phpmailer global is a valid PHPMailer object at this
		 * point in time, it means WordPress is trying to send more
		 * than one mail or another plugin has already initialized it.
		 *
		 * If it's already initialized and an AltBody exists, we need to
		 * clear it or destroy the $phpmailer object, forcing WordPress
		 * to reinitialize it.
		 *
		 * @param array $wp_mail_atts
		 *
		 * @return array
		 */
		 
		 
		public function force_phpmailer_reinit_for_multiple_mails( $wp_mail_atts ) {
			global $phpmailer;
			
            if ( $phpmailer instanceof PHPMailer\PHPMailer\PHPMailer && $phpmailer->alternativeExists() ) {
				// AltBody property is set, so WordPress must already have used this
				// $phpmailer object just now to send another mail
				
				//self::write_log('foobar this ran');
				
				$this->reinitialize_phpmailer();
			}
			return $wp_mail_atts;
		}
		protected function reinitialize_phpmailer() {
			global $phpmailer;
			// Clear the AltBody property, or, if filter returns true,
			// allow the object to be destroyed instead.
			if ( apply_filters( 'lh-multipart-email_destroy-phpmailer', false ) ) {
				$phpmailer = null; // destroy object
				// Support for WP Mail SMTP ( https://wordpress.org/plugins/wp-mail-smtp/ )
				if ( defined( 'WPMS_PLUGIN_VER' ) && class_exists( '\WPMailSMTP\MailCatcher' ) ) {
					$phpmailer = new \WPMailSMTP\MailCatcher();
				}
			} else {
				$phpmailer->AltBody = '';
			}
			// In case other code needs access to the $phpmailer object after we're done with it
			do_action( 'lh-multipart-email_phpmailer_reinitialized' );
		}
		
		
    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        if (null === self::$instance) {
            self::$instance = new self();
        }
 
        return self::$instance;
    }
		
		
		
		public function __construct() {
			add_filter( 'wp_mail', array( $this, 'force_phpmailer_reinit_for_multiple_mails' ), -1, 1 );
			add_action( 'phpmailer_init', array( $this, 'phpmailer_init' ), 1000, 1 );
		}
	}
	$lh_multipart_email_instance = LH_multipart_email_plugin::get_instance();
}