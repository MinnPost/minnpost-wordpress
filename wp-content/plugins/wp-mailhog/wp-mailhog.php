<?php
/**
 * @link
 * @since             1.0.0
 * @package           TODO
 *
 * @wordpress-plugin
 * Plugin Name:       Use MailHog
 * Description:       Configure WordPress on Valet to use MailHog
 * Version:           1.0.0
 * Tags: local, email
 */
add_action( 'phpmailer_init', 'bish_configMH', 10, 1 );
function bish_configMH( $phpmailer ) {
	// Define that we are sending with SMTP
	$phpmailer->isSMTP();
	// The hostname of the mailserver
	$phpmailer->Host = 'localhost';
	// Use SMTP authentication (true|false)
	$phpmailer->SMTPAuth = false;
	// SMTP port number
	// Mailhog normally run on port 1025
	$phpmailer->Port = WP_DEBUG ? '1025' : '25';
	// Username to use for SMTP authentication
	// $phpmailer->Username = 'yourusername';
	// Password to use for SMTP authentication
	// $phpmailer->Password = 'yourpassword';
	// The encryption system to use - ssl (deprecated) or tls
	// $phpmailer->SMTPSecure = 'tls';
	$phpmailer->From = get_option( 'site_email_from', get_option( 'admin_email' ) );
	$phpmailer->FromName = get_option( 'site_email_from_name', get_option( 'blogname' ) );
}