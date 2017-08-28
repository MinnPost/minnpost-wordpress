<?php
/*
Plugin Name:	WP-SpamShield
Plugin URI:		https://www.redsandmarketing.com/plugins/wp-spamshield/
Description:	An extremely powerful and user-friendly all-in-one anti-spam plugin that <strong>eliminates comment spam, trackback spam, contact form spam, and registration spam</strong>. No CAPTCHA's, challenge questions, or other inconvenience to website visitors. Enjoy running a WordPress site without spam! Includes a spam-blocking contact form feature.
Version:		1.9.17
Author:			Scott Allen
Author URI:		https://www.redsandmarketing.com/
License:		GPL2+
License URI:	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:	wp-spamshield
Domain Path:	/languages
*/

/*
	Copyright © 2007-2017 Scott Allen				https://www.redsandmarketing.com/contact/
	Copyright © 2014-2017 Red Sand Media Group		https://www.redsandmarketing.com/
	Copyright © 2016-2017 Blackhawk Cybersecurity	https://blackhawkcybersec.com/

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 2 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program. If not, see
	https://www.gnu.org/licenses/gpl-2.0.html or write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* PLUGIN - BEGIN */

/* Make sure plugin remains secure if called directly */
if( !defined( 'ABSPATH' ) ) {
	if( !headers_sent() ) { @header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', TRUE, 403 ); }
	die( 'ERROR: This plugin requires WordPress and will not function if called directly.' );
}
/* WPSS_DEBUG - Do not change value unless tech support asks you to - for debugging only. Change in wp-config.php. */
if( !defined( 'WPSS_DEBUG' ) ) { define( 'WPSS_DEBUG', FALSE ); }
/* Prevents unintentional error display if WP_DEBUG not enabled. */
if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG ) { @ini_set( 'display_errors', 0 ); @error_reporting( 0 ); }

define( 'WPSS_VERSION',					'1.9.17'				);
define( 'WPSS_WP_VERSION',				$GLOBALS['wp_version']	);
define( 'WPSS_REQUIRED_WP_VERSION',		'4.0'					);
define( 'WPSS_REQUIRED_PHP_VERSION',	'5.3'					);
define( 'WPSS_PHP_TEST_MAX',			'7.1'					); /* MAX PHP VERSION TESTED */


/**
 *	First Unhooked Action
 */
rs_wpss_getenv();

/**
 *  Setting essential PATH, URL, and other constants required by plugin
 */
if( !defined( 'WPSS_EDGE' ) ) 					{ define( 'WPSS_EDGE', FALSE ); }
if( !defined( 'WPSS_DEBUG_SERVER_NAME' ) )		{ define( 'WPSS_DEBUG_SERVER_NAME', '.redsandmarketing.com' ); }
if( !defined( 'WPSS_DEBUG_SERVER_NAME_REV' ) )	{ define( 'WPSS_DEBUG_SERVER_NAME_REV', strrev( WPSS_DEBUG_SERVER_NAME ) ); }
if( !defined( 'WPSS_MDBUG_SERVER_NAME' ) ) 		{ define( 'WPSS_MDBUG_SERVER_NAME', '.redsandmarketing.com' ); }
if( !defined( 'WPSS_MDBUG_SERVER_NAME_REV' ) )	{ define( 'WPSS_MDBUG_SERVER_NAME_REV', strrev( WPSS_MDBUG_SERVER_NAME ) ); }
if( !defined( 'WPSS_EOL' ) ) 					{ $wpss_eol	= ( defined( 'PHP_EOL' ) && ( "\r\n" === PHP_EOL || "\n" === PHP_EOL ) ) ? PHP_EOL : WP_SpamShield::eol(); define( 'WPSS_EOL', $wpss_eol ); }
if( !defined( 'WPSS_DS' ) ) 					{ $wpss_ds	= ( defined( 'DIRECTORY_SEPARATOR' ) && ( '/' === DIRECTORY_SEPARATOR || '\\' === DIRECTORY_SEPARATOR ) ) ? DIRECTORY_SEPARATOR : WP_SpamShield::ds(); define( 'WPSS_DS', $wpss_ds ); }
if( !defined( 'WPSS_PS' ) ) 					{ $wpss_ps	= ( defined( 'PATH_SEPARATOR' ) && ( ':' === PATH_SEPARATOR || ';' === PATH_SEPARATOR ) ) ? PATH_SEPARATOR : WP_SpamShield::ps(); define( 'WPSS_PS', $wpss_ps ); }
if( !defined( 'WPSS_REQUEST_METHOD' ) ) 		{ define( 'WPSS_REQUEST_METHOD', WP_SpamShield::request_method() ); }
if( !defined( 'WPSS_MEMORY_LIMIT' ) ) 			{ define( 'WPSS_MEMORY_LIMIT', '128M' ); }
if( !defined( 'WPSS_SITE_URL' ) ) 				{ define( 'WPSS_SITE_URL', untrailingslashit( strtolower( home_url() ) ) ); }
if( !defined( 'WPSS_SITE_WP_URL' ) ) 			{ define( 'WPSS_SITE_WP_URL', untrailingslashit( strtolower( site_url() ) ) ); }
if( !defined( 'WPSS_SITE_DOMAIN' ) ) 			{ define( 'WPSS_SITE_DOMAIN', rs_wpss_get_domain( WPSS_SITE_URL ) ); }
if( !defined( 'WPSS_CONTENT_DIR_URL' ) ) 		{ define( 'WPSS_CONTENT_DIR_URL', WP_CONTENT_URL ); }
if( !defined( 'WPSS_CONTENT_DIR_PATH' ) ) 		{ define( 'WPSS_CONTENT_DIR_PATH', WP_CONTENT_DIR ); }
if( !defined( 'WPSS_PLUGINS_DIR_URL' ) ) 		{ define( 'WPSS_PLUGINS_DIR_URL', WP_PLUGIN_URL ); }
if( !defined( 'WPSS_PLUGINS_DIR_PATH' ) ) 		{ define( 'WPSS_PLUGINS_DIR_PATH', WP_PLUGIN_DIR ); }
if( !defined( 'WPSS_ADMIN_URL' ) ) 				{ define( 'WPSS_ADMIN_URL', untrailingslashit( admin_url() ) ); }
if( !defined( 'WPSS_COMMENTS_POST_URL' ) ) 		{ define( 'WPSS_COMMENTS_POST_URL', WPSS_SITE_WP_URL . '/wp-comments-post.php' ); }
if( !defined( 'WPSS_PLUGIN_BASENAME' ) ) 		{ define( 'WPSS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
if( !defined( 'WPSS_PLUGIN_FILE_BASENAME' ) ) 	{ define( 'WPSS_PLUGIN_FILE_BASENAME', trim( basename( __FILE__ ), '/' ) ); }
if( !defined( 'WPSS_PLUGIN_NAME' ) ) 			{ define( 'WPSS_PLUGIN_NAME', trim( dirname( WPSS_PLUGIN_BASENAME ), '/' ) ); }
if( !defined( 'WPSS_PLUGIN_URL' ) ) 			{ define( 'WPSS_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) ); }
if( !defined( 'WPSS_PLUGIN_FILE_URL' ) ) 		{ define( 'WPSS_PLUGIN_FILE_URL',  WPSS_PLUGIN_URL.'/'.WPSS_PLUGIN_FILE_BASENAME ); }
if( !defined( 'WPSS_PLUGIN_COUNTER_URL' ) ) 	{ define( 'WPSS_PLUGIN_COUNTER_URL', WPSS_PLUGIN_URL . '/counter' ); }
if( !defined( 'WPSS_PLUGIN_CSS_URL' ) ) 		{ define( 'WPSS_PLUGIN_CSS_URL', WPSS_PLUGIN_URL . '/css' ); }
if( !defined( 'WPSS_PLUGIN_DATA_URL' ) ) 		{ define( 'WPSS_PLUGIN_DATA_URL', WPSS_PLUGIN_URL . '/data' ); }
if( !defined( 'WPSS_PLUGIN_IMG_URL' ) ) 		{ define( 'WPSS_PLUGIN_IMG_URL', WPSS_PLUGIN_URL . '/img' ); }
if( !defined( 'WPSS_PLUGIN_JS_URL' ) ) 			{ define( 'WPSS_PLUGIN_JS_URL', WPSS_PLUGIN_URL . '/js' ); }
if( !defined( 'WPSS_PLUGIN_PATH' ) ) 			{ define( 'WPSS_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) ); }
if( !defined( 'WPSS_PLUGIN_FILE_PATH' ) ) 		{ define( 'WPSS_PLUGIN_FILE_PATH', WPSS_PLUGIN_PATH.'/'.WPSS_PLUGIN_FILE_BASENAME ); }
if( !defined( 'WPSS_PLUGIN_COUNTER_PATH' ) ) 	{ define( 'WPSS_PLUGIN_COUNTER_PATH', WPSS_PLUGIN_PATH . '/counter' ); }
if( !defined( 'WPSS_PLUGIN_CSS_PATH' ) ) 		{ define( 'WPSS_PLUGIN_CSS_PATH', WPSS_PLUGIN_PATH . '/css' ); }
if( !defined( 'WPSS_PLUGIN_DATA_PATH' ) ) 		{ define( 'WPSS_PLUGIN_DATA_PATH', WPSS_PLUGIN_PATH . '/data' ); }
if( !defined( 'WPSS_PLUGIN_IMG_PATH' ) ) 		{ define( 'WPSS_PLUGIN_IMG_PATH', WPSS_PLUGIN_PATH . '/img' ); }
if( !defined( 'WPSS_PLUGIN_INCL_PATH' ) ) 		{ define( 'WPSS_PLUGIN_INCL_PATH', WPSS_PLUGIN_PATH . '/includes' ); }
if( !defined( 'WPSS_PLUGIN_JS_PATH' ) ) 		{ define( 'WPSS_PLUGIN_JS_PATH', WPSS_PLUGIN_PATH . '/js' ); }
if( !defined( 'WPSS_PLUGIN_LANG_PATH' ) ) 		{ define( 'WPSS_PLUGIN_LANG_PATH', WPSS_PLUGIN_PATH . '/languages' ); }
if( !defined( 'WPSS_PLUGIN_ADMIN_URL' ) ) 		{ define( 'WPSS_PLUGIN_ADMIN_URL', WPSS_ADMIN_URL . '/options-general.php?page=' . WPSS_PLUGIN_NAME ); }
if( !defined( 'WPSS_I18N_LANG_PATH' ) ) 		{ define( 'WPSS_I18N_LANG_PATH', basename( dirname( __FILE__ ) ) . '/languages' ); }
if( !defined( 'WPSS_SERVER_NAME' ) ) 			{ define( 'WPSS_SERVER_NAME', rs_wpss_get_server_name() ); }
if( !defined( 'WPSS_SERVER_ADDR' ) ) 			{ define( 'WPSS_SERVER_ADDR', rs_wpss_get_server_addr() ); }
if( !defined( 'WPSS_SERVER_NAME_REV' ) ) 		{ define( 'WPSS_SERVER_NAME_REV', strrev( WPSS_SERVER_NAME ) ); }
if( !defined( 'WPSS_SERVER_NAME_NODOT' ) ) 		{ $wpss_server_name_nodot = str_replace( '.', '', WPSS_SERVER_NAME ); define( 'WPSS_SERVER_NAME_NODOT', $wpss_server_name_nodot ); }
if( !defined( 'WPSS_SERVER_HOSTNAME' ) ) 		{ define( 'WPSS_SERVER_HOSTNAME', rs_wpss_get_server_hostname() ); }
if( !defined( 'WPSS_HASH_ALT' ) ) 				{ $wpss_alt_prefix	= rs_wpss_md5( WPSS_SERVER_NAME_NODOT ); define( 'WPSS_HASH_ALT', $wpss_alt_prefix ); }
if( !defined( 'WPSS_HASH' ) )					{ $wpss_hash_prefix	= defined( 'COOKIEHASH' ) ? COOKIEHASH : rs_wpss_md5( WPSS_SITE_URL ); define( 'WPSS_HASH', $wpss_hash_prefix ); }
if( !defined( 'WPSS_THIS_URL' ) ) 				{ define( 'WPSS_THIS_URL', WP_SpamShield::get_url() ); }
if( !defined( 'WPSS_REF2XJS' ) ) 				{ define( 'WPSS_REF2XJS', 'r3f5x9JS' ); }
if( !defined( 'WPSS_JSONST' ) ) 				{ define( 'WPSS_JSONST', 'JS04X7' ); }
if( !defined( 'WPSS_SPH' ) ) 					{ define( 'WPSS_SPH', 240 ); }
if( !defined( 'WPSS_RGX_TLD' ) ) 				{ define( 'WPSS_RGX_TLD', "(\.[a-z]{2,3}){1,2}[a-z]*" ); }
if( !defined( 'WPSS_RGX_IPSTR' ) ) 				{ define( 'WPSS_RGX_IPSTR', "([0-9]{1,3}[x\.\-]){4}" ); }
if( !defined( 'WPSS_RGX_IPCSTR' ) ) 			{ define( 'WPSS_RGX_IPCSTR', "([0-9]{1,3}[x\.\-]){2}[0-9]{1,3}" ); }
if( !defined( 'WPSS_RGX_IP' ) ) 				{ define( 'WPSS_RGX_IP', "([0-9]{1,3}[x\.\-]){3}[0-9]{1,3}" ); }
if( !defined( 'WPSS_RGX_IPCVAL' ) ) 			{ define( 'WPSS_RGX_IPCVAL', "(([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])\.){3}" ); }
if( !defined( 'WPSS_RGX_IPVAL' ) ) 				{ define( 'WPSS_RGX_IPVAL', WPSS_RGX_IPCVAL."([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" ); }
if( !defined( 'WPSS_RGX_FREEMAIL' ) ) 			{ define( 'WPSS_RGX_FREEMAIL', "((163|aim|aol|arcor|care2|fastmail|g(oogle)?mail|gmx|hotmail|hushmail|inbox|live|lycos|mail|myway|outlook|protonmail|rambler|rediffmail|yahoo|yandex|ymail|zoho(mail)?)".WPSS_RGX_TLD."|(150ml|(150|16|2|50)mail|cluemail|deliveredbysent|fast(\-email|em(ailer)?|imap|messaging)|fmailbox|fmgirl|fmguy|icloud|imap\-mail|india|inoutbox|internet\-e\-mail|mac|mail(\-central|\-page|andftp|as|bolt|can|ftp|haven|ite|might|new|reflect)|me|moose\-mail|my(fast|mac)mail|petml|postinbox|pro(inbox|message)|rocketmail|rushpost|sent|ssl\-mail|swift\-mail|the\-quickest|theinternetemail|wildmail|xsmail|your\-mail)\.com|(4email|airpost|allmail|email(corner|engine|groups|user)|fastmailbox|ftml|hailmail|internet(emails|mailing)|jetemail|justemail|mail(c|force|sent|up)|ml1|nospammail|ownmail|postpro|realemail|speedpost|the\-fastest|usa|veryspeedy|warpmail|yepmail)\.net|(123mail|elitemail|email(engine|plus)|fast\-mail|imapmail|internet\-mail|letterboxes|mail(ingaddress|works)|speedymail)\.org|(sent)\.as|(sent)\.at|(reallyfast|veryfast)\.biz|(eml|fastest|imap).cc|(fmail)\.co\.uk|(freenet|web)\.de|(f\-m)\.fm|(reallyfast)\.info|(mailservice)\.ms|(fea|mm)\.st|(bestmail|fastemail|h\-mail)\.us)" ); }
if( !defined( 'WPSS_DATE_BASIC' ) ) 			{ define( 'WPSS_DATE_BASIC', 'Y-m-d' ); }
if( !defined( 'WPSS_DATE_FULL' ) ) 				{ define( 'WPSS_DATE_FULL', 'Y-m-d H:i:s' ); }
if( !defined( 'WPSS_DATE_LONG' ) ) 				{ define( 'WPSS_DATE_LONG', 'Y-m-d (D) H:i:s e' ); }
if( !defined( 'WPSS_DATE_C_YYMM' ) ) 			{ define( 'WPSS_DATE_C_YYMM', date( 'ym' ) ); }
if( !defined( 'WPSS_CKON' ) ) 					{ define( 'WPSS_CKON', 'CKON'.WPSS_DATE_C_YYMM ); }
if( !defined( 'WPSS_SJECT' ) ) 					{ define( 'WPSS_SJECT', 'SJECT'.WPSS_DATE_C_YYMM ); }
if( !defined( 'WPSS_F0' ) ) 					{ define( 'WPSS_F0', (int) -999999999 ); }		/* Hook Priority - First */
if( !defined( 'WPSS_L0' ) ) 					{ define( 'WPSS_L0', (int) abs( WPSS_F0 ) ); }	/* Hook Priority - Last */
if( !defined( 'WPSS_RSM_URL' ) ) 				{ define( 'WPSS_RSM_URL', 'https://www.redsandmarketing.com/' ); }
if( !defined( 'WPSS_HOME_URL' ) ) 				{ define( 'WPSS_HOME_URL', WPSS_RSM_URL.'plugins/'.WPSS_PLUGIN_NAME.'/' ); }
if( !defined( 'WPSS_SUPPORT_URL' ) ) 			{ define( 'WPSS_SUPPORT_URL', WPSS_RSM_URL.'plugins/'.WPSS_PLUGIN_NAME.'/support/' ); }
if( !defined( 'WPSS_WP_URL' ) ) 				{ define( 'WPSS_WP_URL', 'https://wordpress.org/extend/plugins/'.WPSS_PLUGIN_NAME.'/' ); }
if( !defined( 'WPSS_WP_RATING_URL' ) ) 			{ define( 'WPSS_WP_RATING_URL', 'https://wordpress.org/support/plugin/'.WPSS_PLUGIN_NAME.'/reviews/#new-post' ); }
if( !defined( 'WPSS_DONATE_URL' ) ) 			{ define( 'WPSS_DONATE_URL', WPSS_RSM_URL.'go/donate/'.WPSS_PLUGIN_NAME.'/' ); }

/* INCLUDE POPULAR CACHE PLUGINS HERE (23) */
$popular_cache_plugins_default 					= array ( 'batcache', 'cache-enabler', 'cachify', 'comet-cache', 'db-cache-reloaded', 'db-cache-reloaded-fix', 'gator-cache', 'hyper-cache', 'hyper-cache-extended', 'lite-cache', 'litespeed-cache', 'mo-cache', 'quick-cache', 'simple-cache', 'w3-total-cache', 'wp-cachecom', 'wp-fast-cache', 'wp-fastest-cache', 'wp-fastest-cache-premium', 'wp-hummingbird', 'wp-rocket', 'wp-speed-of-light', 'wp-super-cache', 'zencache', 'zencache-pro', );
if( !defined( 'WPSS_POPULAR_CACHE_PLUGINS' ) )	{ define( 'WPSS_POPULAR_CACHE_PLUGINS', serialize( $popular_cache_plugins_default ) ); }
/* SET THE DEFAULT CONSTANT VALUES HERE */
$wpss_options_default							= array ( 'comment_logging' => 0, 'comment_logging_all' => 0, 'comment_logging_start_date' => 0, 'comment_logging_end_date' => 0, 'enhanced_comment_blacklist' => 0, 'enable_whitelist' => 0, 'comment_min_length' => 15, 'block_all_trackbacks' => 0, 'block_all_pingbacks' => 0, 'allow_proxy_users' => 1, 'hide_extra_data' => 0, 'registration_shield_disable' => 0, 'registration_shield_level_1' => 0, 'disable_cf7_shield' => 0, 'disable_gf_shield' => 0, 'disable_misc_form_shield' => 0, 'disable_email_encode' => 0, 'allow_comment_author_keywords' => 0, 'auto_update_plugin' => 1, 'auto_purge_cache' => 1, 'disable_security_alerts' => 0, 'promote_plugin_link' => 0, 'form_include_website' => 1, 'form_require_website' => 0, 'form_include_phone' => 1, 'form_require_phone' => 0, 'form_include_company' => 0, 'form_require_company' => 0, 'form_include_drop_down_menu' => 0, 'form_require_drop_down_menu' => 0, 'form_drop_down_menu_title' => '', 'form_drop_down_menu_item_1' => '', 'form_drop_down_menu_item_2' => '', 'form_drop_down_menu_item_3' => '', 'form_drop_down_menu_item_4' => '', 'form_drop_down_menu_item_5' => '', 'form_drop_down_menu_item_6' => '', 'form_drop_down_menu_item_7' => '', 'form_drop_down_menu_item_8' => '', 'form_drop_down_menu_item_9' => '', 'form_drop_down_menu_item_10' => '', 'form_message_width' => 40, 'form_message_height' => 10, 'form_message_min_length' => 25, 'form_response_thank_you_message' => __( 'Your message was sent successfully. Thank you.', 'wp-spamshield' ), 'form_include_user_meta' => 1, 'form_mail_encode' => 1, );
if( !defined( 'WPSS_OPTIONS_DEFAULT' ) ) 		{ define( 'WPSS_OPTIONS_DEFAULT', serialize( $wpss_options_default ) ); }
$wpss_depr_options_default 						= array( 'wpss_version' => '', 'init_user_approve_run' => '', 'install_status' => '', 'warning_status' => '', 'regalert_status' => '', 'last_admin' => '', 'wpss_admins' => array(), 'reg_count' => 0, 'spam_count' => 0, 'wpssmid_cache' => array(), 'wpss_procdat' => array( 'total_tracked' => 0, 'total_wpss_time' => 0, 'avg_wpss_proc_time' => 0, 'total_comment_proc_time' => 0, 'avg_comment_proc_time' => 0, 'total_wpss_avg_tracked' => 0, 'total_avg_wpss_proc_time' => 0, 'avg2_wpss_proc_time' => 0 ), );
if( !defined( 'WPSS_DEPR_OPTIONS_DEFAULT' ) ) 	{ define( 'WPSS_DEPR_OPTIONS_DEFAULT', serialize( $wpss_depr_options_default ) ); }

unset( $wpss_eol, $wpss_ds, $wpss_server_name_nodot, $wpss_alt_prefix, $wpss_hash_prefix, $popular_cache_plugins_default, $wpss_options_default, $wpss_depr_options_default );
rs_wpss_set_memory();
$wpss_ecom_urls = array( '/cart/', '/catalog/', '/checkout/', '/products/', '/shop/', '/shoppe/', '/store/', );

/* Includes - BEGIN */
/* $include_files = array( 'advanced', 'class.blacklists', 'class.admin', 'class.compatibility', 'class.filters', 'class.security', 'class.session', 'class.utils', 'class.widget', ); */
$include_files = array( 'advanced', 'class.blacklists', 'class.compatibility', 'class.security', 'class.utils', 'class.widget', );
foreach( $include_files as $f ) {
	require_once( WPSS_PLUGIN_INCL_PATH.WPSS_DS.$f.'.php' );
}
unset( $include_files, $f );
if( !defined( 'WPSS_INCL_DONE' ) && class_exists( 'WPSS_BL' ) && class_exists( 'WPSS_Compatibility' ) && class_exists( 'WPSS_Security' ) && class_exists( 'WPSS_Utils' ) && class_exists( 'WP_SpamShield_Counter_CG' ) ) {
	define('WPSS_INCL_DONE', TRUE );
}
/* Includes - END */

/* SET ADVANCED OPTIONS - Can be overridden in wp-config.php. Advanced users only. */
if( !defined( 'WPSS_COMPAT_MODE' ) ) 			{ define( 'WPSS_COMPAT_MODE',		FALSE ); }	/* Force-enable Compatiility Mode.	Reference: https://www.redsandmarketing.com/plugins/wp-spamshield/advanced-configuration/?acnf=compatibility_mode#acnf_compatibility_mode			*/
if( !defined( 'WPSS_TEMP_BL_DISABLE' ) ) 		{ define( 'WPSS_TEMP_BL_DISABLE',	FALSE ); }	/* Disable Temporay Blacklist.		Reference: https://www.redsandmarketing.com/plugins/wp-spamshield/advanced-configuration/?acnf=disable_temp_blacklist#acnf_disable_temp_blacklist	*/
if( !defined( 'WPSS_TEMP_BL_CF_ONLY' ) ) 		{ define( 'WPSS_TEMP_BL_CF_ONLY',	FALSE ); }
if( !defined( 'WPSS_IP_BAN_ENABLE' ) ) 			{ define( 'WPSS_IP_BAN_ENABLE',		FALSE ); }	/* Enable Automatic IP Ban. BETA	Reference: None yet. Coming soon.	*/
if( !defined( 'WPSS_IP_BAN_CLEAR' ) ) 			{ define( 'WPSS_IP_BAN_CLEAR',		FALSE ); }	/* Clear IP Ban List. BETA			Reference: None yet. Coming soon.	*/
if( !defined( 'WPSS_AUTOUP_DISABLE' ) ) 		{ define( 'WPSS_AUTOUP_DISABLE',	FALSE ); }	/* Force-disable Automatic Update.	Reference: None yet. Coming soon.	*/
if( !defined( 'WPSS_CUSTOM_ECOM' ) ) 			{ define( 'WPSS_CUSTOM_ECOM',		FALSE ); }	/* Set Custom Ecom. BETA			Reference: None yet. Coming soon.	*/
if( !defined( 'WPSS_CUSTOM_CART_URL' ) )		{ define( 'WPSS_CUSTOM_CART_URL',	FALSE ); }	/* Set Custom Cart URL. BETA		Reference: None yet. Coming soon.	*/
else {
	if( WP_SpamShield::preg_match( "~^/[a-z0-9\-_]+/$~i", WPSS_CUSTOM_CART_URL ) ) {
		$wpss_ecom_urls[] = WPSS_CUSTOM_CART_URL;
	}
}
if( !defined( 'WPSS_ECOM_URLS' ) ) 				{ define( 'WPSS_ECOM_URLS', serialize( $wpss_ecom_urls ) ); }
if( !defined( 'WPSS_INIT_SPAM_COUNT' ) ) 		{ define( 'WPSS_INIT_SPAM_COUNT', FALSE ); }
else {
	$GLOBALS['wpss_init_spam_count'] = is_int( WPSS_INIT_SPAM_COUNT ) ? WPSS_INIT_SPAM_COUNT : 0;
}
unset( $wpss_ecom_urls );


/**
 *	Early Security Check
 */
if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) ) {
	$_SERVER['WPSS_SEC_THREAT'] = TRUE;
	if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && WP_SpamShield::is_mdbug() ) {
		if( !headers_sent() ) { @header( $_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden', TRUE, 403 ); }
		die( '403 Forbidden' );
	}
}


/* Standard Functions - BEGIN */

/**
 *	Get Environment Variables, or load a specific one.
 *	Ensures compatibility with servers (eg. IIS) that aren't set to populate $_ENV[] automatically.
 *	@dependencies	...
 *	@since			1.9.6.2
 */
function rs_wpss_getenv( $e = FALSE, $add_vars = array() ) {
	global $_WPSS_ENV; if( empty( $_WPSS_ENV ) || !is_array( $_WPSS_ENV ) ) { $_WPSS_ENV = array(); }
	$_WPSS_ENV = (array) $_WPSS_ENV + (array) $_ENV;
	$vars = array( 'REMOTE_ADDR', 'SERVER_ADDR', 'LOCAL_ADDR', 'SERVER_NAME', 'HTTP_HOST', );
	$vars = !empty( $add_vars ) ? (array) $vars + (array) $add_vars : $vars;
	if( !empty( $e ) ) { $vars[] = $e; }
	foreach( $vars as $i => $k ) {
		if( empty( $_WPSS_ENV[$k] ) ) {
			$_WPSS_ENV[$k] = $_ENV[$k] = '';
			if( function_exists( 'getenv' ) ) {
				$_WPSS_ENV[$k] = $_ENV[$k] = @getenv( $k );
			}
			if( empty( $_WPSS_ENV[$k] ) && !empty( $_SERVER[$k] ) ) {
				$_WPSS_ENV[$k] = $_ENV[$k] = $_SERVER[$k];
			}
		}
		if( !empty( $_WPSS_ENV[$k] ) && FALSE !== strpos( $k, '_ADDR' ) && ( FALSE !== strpos( $_WPSS_ENV[$k], '.' ) || FALSE !== strpos( $_WPSS_ENV[$k], ':' ) ) ) {
			$_WPSS_ENV[$k] = $_ENV[$k] = @WP_SpamShield::sanitize_ip( $_WPSS_ENV[$k] );
		}
	}
	unset( $i, $k );
	if( empty( $_WPSS_ENV['LOCAL_ADDR'] ) && !empty( $_SERVER['SERVER_ADDR'] ) ) {
		$_WPSS_ENV['LOCAL_ADDR'] = $_ENV['LOCAL_ADDR'] = $_SERVER['SERVER_ADDR'];
	}
	return FALSE !== $e ? $_WPSS_ENV[$e] : $_WPSS_ENV;
}

function rs_wpss_maybe_start_session( $force = FALSE ) {
	if( rs_wpss_is_cli() || rs_wpss_is_doing_cron() || rs_wpss_is_installing() ) { return NULL; }
	global $wpss_session_id,$wpss_session_name,$wpss_session_active,$wpss_cache_check; if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
	if( 'POST' !== WPSS_REQUEST_METHOD && empty( $force ) && !is_admin() ) {
		if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$cache_check_status	= $wpss_cache_check['cache_check_status'];
		if( $cache_check_status === 'ACTIVE' || TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return; }
	}
	/**
	 *	Start SESSION if conditions are met...
	 */
	if( empty( $wpss_session_id ) && !headers_sent() ) {
		@session_start();
		$wpss_session_id	= @session_id();
		$wpss_session_name	= @session_name();
	}
}

function rs_wpss_end_session() {
	global $wpss_session_name; if( empty( $wpss_session_name ) ) { $wpss_session_name = @session_name(); }
	if( !rs_wpss_is_session_active() ) { return NULL; }
	$_SESSION = array(); @session_destroy();
	if( empty( $_COOKIE ) ) { return; }
	if( isset( $_COOKIE[$wpss_session_name] ) ) {
		$params = @session_get_cookie_params(); /* https://secure.php.net/manual/en/function.session-get-cookie-params.php */
		if( empty( $params ) || !is_array( $params ) ) {
			$params = array( 'path' => '/', 'domain' => rs_wpss_get_cookie_domain(), 'secure' => WP_SpamShield::is_https(), 'httponly' => TRUE );
		}
		/* Delete session cookie */
		@setcookie( $wpss_session_name, '', time() - WEEK_IN_SECONDS,  $params['path'], $params['domain'], $params['secure'], $params['httponly'] );
	}
}

function rs_wpss_is_session_active() {
	global $wpss_session_active,$wpss_session_id,$wpss_session_name;
	if( isset( $wpss_session_active ) && is_bool( $wpss_session_active ) ) { return $wpss_session_active; }
	$wpss_session_id		= ( !empty( $wpss_session_id ) ) ? $wpss_session_id : @session_id();
	$wpss_session_active	= ( isset( $_SESSION ) || $wpss_session_id );
	if( TRUE === $wpss_session_active ) {
		$wpss_session_name	= ( !empty( $wpss_session_name ) ) ? $wpss_session_name : @session_name();
	}
	return $wpss_session_active;
}

function rs_wpss_login( $user_login, $user = NULL ) {
	if( !is_object( $user ) || empty( $user ) || empty( $user->ID ) ) { $user = wp_get_current_user(); }
	if( !is_object( $user ) || empty( $user ) || empty( $user->ID ) ) { return; }
	$user_id = $user->ID; rs_wpss_update_user_ip( $user_id ); rs_wpss_remove_expired_admins();
}

function rs_wpss_logout() {
	rs_wpss_end_session();
}

function rs_wpss_update_user_ip( $user_id = NULL, $add_admin_ip = FALSE, $admin_ips = NULL ) {
	if( empty( $user_id ) ){ global $current_user; $current_user = wp_get_current_user(); $user_id = $current_user->ID; }
	if( empty( $user_id ) ){ return; }
	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */
	if( !WP_SpamShield::is_valid_ip( $ip ) ) { return; }
	$timenow		= time();
	$user_ip		= get_user_meta( $user_id, 'wpss_user_ip', TRUE );
	if( empty( $user_ip ) ) { $user_ip = array(); }
	$user_ip[$ip]	= array( 'server' => $reverse_dns, 'time' => $timenow );
	update_user_meta( $user_id, 'wpss_user_ip', $user_ip );
	if( !empty( $add_admin_ip ) ) {
		if( empty( $admin_ips ) ) { $admin_ips = get_option( 'spamshield_admins' ); }
		if( empty( $admin_ips ) ) { $admin_ips = array(); }
		$admin_ips[$ip] = $timenow;
		update_option( 'spamshield_admins', $admin_ips );
		update_option( 'spamshield_last_admin', $ip );
	}
}

/**
 *	Boost memory limits
 *	WordPress' default is 40M, but it requires at least 64M to run smoothly on many sites. 128M+ is better.
 *	@dependencies	...
 *	@since			...
 */
function rs_wpss_set_memory() {
	if( function_exists( 'memory_get_usage' ) && rs_wpss_is_function_enabled( 'ini_set' ) ) {
		$current_limit		= ini_get( 'memory_limit' );
		$current_limit_int	= intval( $current_limit );
		if( FALSE !== strpos( $current_limit, 'G' ) ) { $current_limit_int *= 1024; }
		$wpss_limit_int		= intval( WPSS_MEMORY_LIMIT );
		if( FALSE !== strpos( WPSS_MEMORY_LIMIT,'G' ) ) { $wpss_limit_int *= 1024; }
		if( -1 != $current_limit && ( -1 == WPSS_MEMORY_LIMIT || $current_limit_int < $wpss_limit_int ) ) {
			@ini_set( 'memory_limit', WPSS_MEMORY_LIMIT );
		}
	}
}

/**
 *	Prepare form data before emailing.
 *	TO DO: Possibly integrate htmlspecialchars_decode() & nl2br()
 *	@dependencies	...
 *	@since			...
 */
function rs_wpss_prep_cf_email( $str ) {
	$str = preg_replace( "~(\\*['’]|&(apos|#x0+27|#0?39|rsquo);)~i", '’', $str );
	$str = str_replace( array( "\\'", '\’', "'", '’', '&apos;', '&rsquo;', '&#x00027;', '&#039;', '&#39;', ), '’', $str );
	return $str;
}

function rs_wpss_is_function_enabled( $func ) {
	if( !function_exists( $func ) ) { return FALSE; }
	$d = WP_SpamShield::casetrans( 'lower', trim( @ini_get( 'disable_functions' ), ", \t\n\r\0\x0B" ) );
    $s = WP_SpamShield::casetrans( 'lower', trim( @ini_get( 'suhosin.executor.func.blacklist' ), ", \t\n\r\0\x0B" ) );
	if( empty( $d ) && empty( $s ) ) { return TRUE; }
	$ds = str_replace( array( '  ', ' ', ',,' ), array( '', '', ',' ), $d.','.$s );
	$arr = !empty( $ds ) ? (array) @explode( ',', $ds ) : array(); $arr = array_filter( $arr );
	if( !empty( $arr ) && in_array( $func, $arr, TRUE ) ) { return FALSE; }
	return TRUE;
}

/**
 *	Check if a string is UTF8
 *	@dependencies	...
 *	@since			...
 */
function rs_wpss_is_utf8( $str ) {
	return ( WP_SpamShield::preg_match( "~~u", $str ) );
}

/**
 *	Count words in a string
 *	For our purposes, this is more accurate than "str_word_count( $str, 0 )"
 *	@dependencies	rs_wpss_strlen()
 *	@since			1.0.0
 *	@modified		1.9.9.8.2	Simplified code
 */
function rs_wpss_count_words( $str ) {
	return ( empty( $str ) || 0 === rs_wpss_strlen( trim( $str ) ) ) ? 0 : count( preg_split( "~\s+~", $str ) );
}

/**
 *	Use this function instead of mb_strlen because some servers have mb_ functions disabled by default
 *	BUT mb_strlen is superior to strlen, so use it whenever possible
 *	@dependencies	...
 *	@since			...
 */
function rs_wpss_strlen( $str ) {
	return function_exists( 'mb_strlen' ) ? mb_strlen( $str, 'UTF-8' ) : strlen( $str );
}

/**
 *	Use this function to quickly get the approximate byte size of an array
 *	@dependencies	rs_wpss_strlen()
 *	@since			1.9.9.9.4
 */
function rs_wpss_arrlen( $arr ) {
	return rs_wpss_strlen( implode( "\n", $arr ) );
}

function rs_wpss_substr_count( $haystack, $needle, $offset = 0, $length = NULL ) {
	/* Has error correction built in */
	$haystack_len = rs_wpss_strlen( $haystack );
	$needle_len = rs_wpss_strlen( $needle );
	if( $offset >= $haystack_len || $offset < 0 ) { $offset = 0; }
	if( empty( $length ) || $length <= 0 ) { $length = $haystack_len; }
	$haystack_len_offset_diff = $haystack_len - $offset;
	if( $length > $haystack_len_offset_diff ) { $length = $haystack_len_offset_diff; }
	$needle_instances = 0;
	if( !empty( $needle ) && !empty( $haystack ) && $needle_len <= $haystack_len ) {
		$needle_instances = substr_count( $haystack, $needle, $offset, $length );
	}
	return $needle_instances;
}

function rs_wpss_preg_quote( $str ) {
	/* Prep for use in Regex, this plugin uses '~' as a delimiter exclusively, can be changed */
	$rgx_str = preg_quote( $str, '~' );
	$rgx_str = preg_replace( "~\s+~", "\s+", $rgx_str );
	return $rgx_str;
}

function rs_wpss_md5( $str ) {
	/* Use this function instead of hash for compatibility. BUT hash is faster than md5 for multiple iterations, so use it whenever possible. */
	return function_exists( 'hash' ) ? hash( 'md5', $str ) : md5( $str );
}

function rs_wpss_get_wpss_eid( $args ) {
	/**
	 *	Creates unique temporary IDs from hash of data for both contact forms and comments.
	 *	@since 1.7.7
	 *	$type: 'comment','contact'
	 *	$args: name, email, url, content
	 *	'eid' - Entity ID; 'ecid' - Entity Content ID
	 */
	$wpsseid				= array( 'eid' => '', 'ecid' => '' );
	$wpsseid_args_data_str	= implode( '', $args );
	$wpsseid['eid']			= rs_wpss_md5( $wpsseid_args_data_str );
	$wpsseid['ecid']		= rs_wpss_md5( $args['content'] );
	return $wpsseid;
}

function rs_wpss_create_nonce( $action, $name = '_wpss_nonce' ) {
	/**
	 *	Creates a different nonce system than WordPress.
	 *	24 hours or 1 time use.
	 *	Difference vs WP nonces: Nonce must exist in database, is not tied to a user ID, and is truly 1 time use.
	 *	WP nonces don't work for every application. If a comment is posted, and a notification email is sent to admin with link to blacklist the IP, this works better.
	 */
	$i						= wp_nonce_tick();
	$timenow				= time();
	$nonce					= substr( rs_wpss_md5( $i . $action . $name . WPSS_HASH . $timenow ), -12, 10 );
	$spamshield_nonces		= get_option( 'spamshield_nonces' );
	if( empty( $spamshield_nonces ) ) { $spamshield_nonces = array(); }
	else {
		foreach( $spamshield_nonces as $i => $n ) {
			if( $n['expire'] <= $timenow ) { unset( $spamshield_nonces[$i] ); }
		}
	}
	$expire					= $timenow + 86400; /* 24 hours */
	$spamshield_nonces[]	= array( 'nonce' => $nonce, 'action' => $action, 'name' => $name, 'expire' => $expire );
	update_option( 'spamshield_nonces', $spamshield_nonces, FALSE );
	return $nonce;
}

function rs_wpss_verify_nonce( $value, $action, $name = '_wpss_nonce' ) {
	/**
	 *	Verify a WP-SpamShield nonce.
	 *	$value	= value of nonce you're testing for
	 *	$action	= descriptive string used internally for what you're trying to do
	 *	$name	= identifier of nonce
	 */
	$nonce_valid = FALSE;
	$timenow = time();
	$spamshield_nonces = get_option( 'spamshield_nonces' );
	if( empty( $spamshield_nonces ) ) { return FALSE; }
	foreach( $spamshield_nonces as $i => $n ) {
		if( $n['nonce'] === $value && $n['action'] === $action && $n['expire'] > $timenow ) {
			unset( $spamshield_nonces[$i] );
			$nonce_valid = TRUE;
		} elseif( $n['expire'] <= $timenow ) { unset( $spamshield_nonces[$i] ); }
	}
	update_option( 'spamshield_nonces', $spamshield_nonces, FALSE );
	return $nonce_valid;
}

function rs_wpss_purge_nonces() {
	/**
	 *	Purge expired nonces. Keep the nonce cache clean.
	 */
	$timenow = time();
	$spamshield_nonces = get_option( 'spamshield_nonces' );
	if( empty( $spamshield_nonces ) ) { return FALSE; }
	foreach( $spamshield_nonces as $i => $n ) {
		if( $n['expire'] <= $timenow ) { unset( $spamshield_nonces[$i] ); }
	}
	update_option( 'spamshield_nonces', $spamshield_nonces, FALSE );
	return TRUE;
}

function rs_wpss_microtime() {
	$t = microtime( TRUE );
	if( empty( $t ) ) { $t = time(); @WP_SpamShield::append_log_data( NULL, NULL, 'PHP microtime() function is either disabled or not functioning properly on this server.' ); }
	return $t;
}

function rs_wpss_timer( $start = NULL, $end = NULL, $show_seconds = FALSE, $precision = 8, $no_format = FALSE, $raw = FALSE, $benchmark = FALSE ) {
	/**
	 *	$precision will default to 8 but can be set to anything - 1,2,3,4,5,6,etc.
	 *	Use $no_format when clean numbers are needed for calculations. International formatting throws a wrench into things.
	 */
	if( empty( $start ) ) { return NULL; }
	if( empty( $end ) ) { $end = microtime( TRUE ); }
	$total_time = $end - $start;
	if( empty( $no_format ) ) {
		$total_time_for = rs_wpss_number_format( $total_time, $precision );
		if( !empty( $show_seconds ) ) { $total_time_for .= ' seconds'; }
	} elseif( empty( $raw ) ) {
		$total_time_for = number_format( $total_time, $precision );
	} else { $total_time_for = $total_time; }
	if( TRUE === $benchmark ) {
		@WP_SpamShield::append_log_data( NULL, NULL, '$start_time: "'	. $start			. '" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . WP_SpamShield::wp_memory_used() . ' | VER: ' . WPSS_VERSION );
		@WP_SpamShield::append_log_data( NULL, NULL, '$end_time: "'		. $end				. '" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . WP_SpamShield::wp_memory_used() . ' | VER: ' . WPSS_VERSION );
		@WP_SpamShield::append_log_data( NULL, NULL, '$total_time: "'	. $total_time_for	. '" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . WP_SpamShield::wp_memory_used() . ' | VER: ' . WPSS_VERSION );
		return $total_time_for;
	}
	return $total_time_for;
}

function rs_wpss_timer_bm( $start ) {
	$total_time = rs_wpss_timer( $start, NULL, FALSE, 6, TRUE, FALSE, TRUE );
	return $total_time;
}

function rs_wpss_number_format( $number, $precision = NULL ) {
	/* $precision will default to NULL but can be set to anything - 1,2,3,4,5,6,etc. */
	if( function_exists( 'number_format_i18n' ) ) { $number_for = number_format_i18n( $number, $precision ); }
	else { $number_for = number_format( $number, $precision ); }
	return $number_for;
}

function rs_wpss_date_diff( $start, $end ) {
	$start_ts		= strtotime( $start );
	$end_ts			= strtotime( $end );
	$diff			= ( $end_ts - $start_ts );
	$start_array	= explode( '-', $start );
	$start_year		= $start_array[0];
	$end_array		= explode( '-', $end );
	$end_year		= $end_array[0];
	$years			= ( $end_year - $start_year );
	$extra_days		= ( ( $years % 4 ) == 0 ) ? ( ( ( $end_year - $start_year ) / 4 ) - 1 ) : ( ( $end_year - $start_year ) / 4 );
	$extra_days		= round( $extra_days );
	return round( $diff / 86400 ) + $extra_days;
}

/**
 *  Drop-in replacement for PHP function scandir()
 *  Has sanitation and error correction built-in
 *  @dependencies	none
 *  @used by		none (as of 1.9.8.8.8)
 *  @since			...
 */
function rs_wpss_scandir( $dir ) {
	if( empty( $dir ) || ! @is_string( $dir ) ) { return $dir; }
	@clearstatcache();
	$dot_files	= array( '..', '.' );
	$dir_listr	= (array) @scandir( $dir );
	$dir_list	= array_values( array_diff( $dir_listr, $dot_files ) );
	return $dir_list;
}

/**
 *  Get domain from URL
 *  Filter URLs with nothing after http
 *  $email_domain will run through rs_wpss_get_email_domain()
 *  @dependencies	rs_wpss_fix_url(), WP_SpamShield::parse_url(), WP_SpamShield::is_wp_ver(), 
 *  @used by		rs_wpss_get_cookie_domain(), 
 *  @since			...
 */
function rs_wpss_get_domain( $url, $email_domain = FALSE ) {
	if( empty( $url ) || !is_string( $url ) || WP_SpamShield::preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url	= rs_wpss_fix_url( $url );
	/* NOW start parsing */
	$parsed = WP_SpamShield::parse_url( $url );
	if( empty( $parsed['host'] ) ) { return ''; }
	$domain	= WP_SpamShield::casetrans( 'lower', $parsed['host'] );
	$domain = ( !empty( $email_domain ) ) ? rs_wpss_get_email_domain( $domain ) : $domain;
	return $domain;
}

/**
 *  Get email domain for use in email addresses
 *  Strip 'www.' & 'm.' from beginning of domain
 *  @dependencies	none
 *  @used by		rs_wpss_get_domain(), rs_wpss_get_cookie_domain(), ...
 *  @since			...
 */
function rs_wpss_get_email_domain( $domain ) {
	if( empty( $domain ) ) { return ''; }
	$domain = preg_replace( "~^(ww[w0-9]|m)\.~i", '', $domain );
	return $domain;
}

/**
 *  Get domain for use in cookies - ex: .domain.com
 *  Works with all subdomains
 *  @dependencies	rs_wpss_get_domain(), rs_wpss_get_email_domain()
 *  @used by		...
 *  @since			...
 */
function rs_wpss_get_cookie_domain( $domain = NULL ) {
	if( empty( $domain ) ) {
		$url		= WPSS_THIS_URL;
		$domain		= rs_wpss_get_domain( $url );
	}
	$email_domain	= rs_wpss_get_email_domain( $domain );
	$cookie_domain	= '.'.$email_domain;
	return $cookie_domain;
}

function rs_wpss_get_query_string( $url ) {
	/**
	 *  Get query string from URL
	 *  Filter URLs with nothing after http
	 */
	if( empty( $url ) || WP_SpamShield::preg_match( "~^https?\:*/*$~i", $url ) ) { return ''; }
	/* Fix poorly formed URLs so as not to throw errors when parsing */
	$url = rs_wpss_fix_url( $url );
	/* NOW start parsing */
	$parsed = WP_SpamShield::parse_url( $url );
	/* Filter URLs with no query string */
	if( empty( $parsed['query'] ) ) { return ''; }
	$query_str = $parsed['query'];
	return $query_str;
}

function rs_wpss_get_query_args( $url ) {
	/**
	 *  Get query string array from URL
	 */
	if( empty( $url ) ) { return array(); }
	$query_str = rs_wpss_get_query_string( $url );
	parse_str( $query_str, $args );
	return $args;
}

function rs_wpss_parse_links( $haystack, $type = 'url' ) {
	/**
	 *  Parse a body of content for links - extracts URLs and Anchor Text
	 *  $type: 'url' for URLs, 'domain' for just Domains, 'url_at' for URLs from Anchor Text Links only, 'anchor_text' for Anchor Text
	 *  Returns an array
	 */
	$parse_links_rgx = "~(<\s*a\s+[a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*\s*href|\[(url|link))\s*\=\s*['\"]?\s*(https?\://[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*['\"]?\s*[a-z0-9\-_\.\?\='\"\:;\(\)\{\}\s]*\s*(>|\])([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*)(<|\[)\s*\/\s*a\s*(>|(url|link)\])~iu";
	$search_http_rgx ="~(?:^|\s+)(https?\://[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)(?:$|\s+)~iu";
	preg_match_all( $parse_links_rgx, $haystack, $matches_links, PREG_PATTERN_ORDER );
	$parsed_links_matches 			= $matches_links[3]; /* Array containing URLs parsed from Anchor Text Links in haystack text */
	$parsed_anchortxt_matches		= $matches_links[5]; /* Array containing Anchor Text parsed from Anchor Text Links in haystack text */
	if( $type === 'url' || $type === 'domain' ) {
		$url_haystack = preg_replace( "~\s~", ' - ', $haystack ); /* Workaround Added 1.3.8 */
		preg_match_all( $search_http_rgx, $url_haystack, $matches_http, PREG_PATTERN_ORDER );
		$parsed_http_matches 		= $matches_http[1]; /* Array containing URLs parsed from haystack text */
		$parsed_urls_all_raw 		= array_merge( $parsed_links_matches, $parsed_http_matches );
		$parsed_urls_all			= array_unique( $parsed_urls_all_raw );
		if( $type === 'url' ) { $results = $parsed_urls_all; }
		elseif( $type === 'domain' ) {
			$parsed_urls_all_domains = array();
			foreach( $parsed_urls_all as $u => $url_raw ) {
				$url = WPSS_Func::lower( trim( stripslashes( $url_raw ) ) );
				if( empty( $url ) ) { continue; }
				$domain = rs_wpss_get_domain( $url );
				if( !WPSS_PHP::in_array( $domain, $parsed_urls_all_domains ) ) { $parsed_urls_all_domains[] = $domain; }
			}
			$results = $parsed_urls_all_domains;
		}
	} elseif( $type === 'url_at' ) { $results = $parsed_links_matches; }
	elseif( $type === 'anchor_text' ) { $results = $parsed_anchortxt_matches; }
	return $results;
}

/**
 *  Extract spammy emails from a body of text content
 *  Returns an array
 *  @dependencies	...
 *  @used by		...
 *  @since			1.9.9.9.9
 */
function rs_wpss_extract_emails( $haystack ) {
	if( FALSE === strpos( $haystack, '@' ) ) { return array(); }
	$rgx_ptn = "~\b((?:mailto\:)?([a-z0-9]+[\w\-\.\+]+\@".WPSS_RGX_FREEMAIL."))\b~iu";
	preg_match_all( $rgx_ptn, $haystack, $matches, PREG_PATTERN_ORDER );
	$emails = ( !empty( $matches[2] ) && is_array( $matches[2] ) ) ? $matches[2] : array(); /* Array containing email addresses extracted from haystack text */
	foreach( $emails as $i => $v ) {
		$emails[$i] = str_replace( 'mailto:', '', $v );
	}
	return $emails;
}

function rs_wpss_fix_url( $url = NULL, $rem_frag = FALSE, $rem_query = FALSE, $rev = FALSE ) {
	/**
	 *  Fix poorly formed URLs so as not to throw errors or cause problems
	 */
	if( empty( $url ) ) { return ''; }
	$url = trim( $url );
	/* Too many forward slashes or colons after http */
	$url = preg_replace( "~^(https?)\:+/+~i", "$1://", $url);
	/* Too many dots */
	$url = preg_replace( "~\.+~i", ".", $url);
	/* Too many slashes after the domain */
	$url = preg_replace( "~([a-z0-9]+)/+([a-z0-9]+)~i", "$1/$2", $url);
	/* Remove fragments */
	if( !empty( $rem_frag ) && strpos( $url, '#' ) !== FALSE ) { $url_arr = explode( '#', $url ); $url = $url_arr[0]; }
	/* Remove query string completely */
	if( !empty( $rem_query ) && strpos( $url, '?' ) !== FALSE ) { $url_arr = explode( '?', $url ); $url = $url_arr[0]; }
	/* Reverse */
	if( !empty( $rev ) ) { $url = strrev($url); }
	return $url;
}

/**
 *  Make URL schemeless / protocol-relative
 *  Use judiciously to prevent SEO + proxy issues
 *  @dependencies	none
 *  @since			1.9.9.3
 */
function rs_wpss_get_schemeless_url( $url ) {
	return str_replace( array( 'https://', 'http://' ), '//', $url);
}

function rs_wpss_append_url( $url = NULL ) {
	/** 
	 *  Add essential data to link URLs to aid tech support
	 *	@since 1.9.7
	 */
	if( empty( $url ) ) { return ''; }
	global $wpss_active_plugins; $wpss_active_plugins = rs_wpss_get_active_plugins(); $num_plugins = count( $wpss_active_plugins );
	return add_query_arg( array( 'wpssv' => WPSS_VERSION, 'wpv' => WPSS_WP_VERSION, 'phv' => PHP_VERSION, 'npl' => $num_plugins, ), $url );
}

function rs_wpss_get_rewrite_base() {
	$root_url  = WP_SpamShield::is_https() ? 'https://' : 'http://';
	$root_url .= WPSS_SERVER_NAME;
	$tmp = str_replace( $root_url, '', WPSS_SITE_URL );
	$rewrite_base = !empty( $tmp ) ? trailingslashit( $tmp ) : '/';
	return $rewrite_base;
}

function rs_wpss_get_server_name() {
	global $_WPSS_ENV;
	if( defined( 'WPSS_SERVER_NAME' ) && NULL !== WPSS_SERVER_NAME ) {
		if( empty( $_SERVER['SERVER_NAME'] ) || empty( $_SERVER['HTTP_HOST'] ) || WPSS_SERVER_NAME !== $_SERVER['SERVER_NAME'] || WPSS_SERVER_NAME !== $_SERVER['HTTP_HOST'] ) {
			$_WPSS_ENV['HTTP_HOST']	= $_WPSS_ENV['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = WPSS_SERVER_NAME;
		}
		return WPSS_SERVER_NAME;
	}
	/**
	 *	Primary mitigation for CVE-2017-8295: https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2017-8295 | Added 1.9.9.9.9
	 */
	if( empty( $_SERVER['SERVER_NAME'] ) || ( isset( $_SERVER['HTTP_HOST'], $_SERVER['SERVER_NAME'] ) && $_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST'] ) ) {
		$server_name = @rs_wpss_get_domain( untrailingslashit( WP_SpamShield::casetrans( 'lower', home_url() ) ) );
		if ( !empty( $server_name ) ) {
			$_WPSS_ENV['HTTP_HOST']	= $_WPSS_ENV['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $server_name;
		}
	} else {
		if(		!empty( $_SERVER['SERVER_NAME'] )	) { $server_name = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME']; }
		elseif(	!empty( $_WPSS_ENV['SERVER_NAME'] )	) { $server_name = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $_WPSS_ENV['SERVER_NAME']; }
		$server_name = WP_SpamShield::casetrans( 'lower', trim( $server_name ) );
		if ( empty( $server_name ) || '.' === $server_name ) {
			$server_name = ( defined( 'WPSS_SITE_DOMAIN' ) ) ? WPSS_SITE_DOMAIN : '';
		}
	}
	$server_name = ( empty( $server_name ) ) ? @rs_wpss_get_domain( untrailingslashit( WP_SpamShield::casetrans( 'lower', home_url() ) ) ) : $server_name;
	if ( !empty( $server_name ) ) {
		$_WPSS_ENV['HTTP_HOST']	= $_WPSS_ENV['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $server_name;
	}
	return $server_name;
}

function rs_wpss_get_server_addr() {
	global $_WPSS_ENV;
	if( defined( 'WPSS_SERVER_ADDR' ) && NULL !== WPSS_SERVER_ADDR ) {
		if( empty( $_SERVER['SERVER_ADDR'] ) || empty( $_SERVER['LOCAL_ADDR'] ) ) {
			$_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'] = $_WPSS_ENV['SERVER_ADDR'] = $_WPSS_ENV['LOCAL_ADDR'] = WPSS_SERVER_ADDR;
		}
		return WPSS_SERVER_ADDR;
	}
	if(		!empty( $_SERVER['SERVER_ADDR'] )	)	{ $server_addr = $_SERVER['SERVER_ADDR']; }
	elseif(	!empty( $_WPSS_ENV['SERVER_ADDR'] )	)	{ $server_addr = $_SERVER['SERVER_ADDR'] = $_WPSS_ENV['SERVER_ADDR']; }
	elseif(	!empty( $_SERVER['LOCAL_ADDR'] )	)	{ $server_addr = $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR']; } /* IIS compatibility */
	elseif(	!empty( $_WPSS_ENV['LOCAL_ADDR'] )	)	{ $server_addr = $_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'] = $_WPSS_ENV['LOCAL_ADDR']; }
	else {
		$server_name = defined( 'WPSS_SERVER_NAME' ) ? WPSS_SERVER_NAME : @rs_wpss_get_server_name();
		$server_addr = !empty( $server_name ) ? @rs_wpss_get_forward_dns( $server_name ) : '';
	}
	$server_addr = WP_SpamShield::casetrans( 'lower', trim( $server_addr ) );
	$server_addr = ( empty( $server_addr ) || '.' === $server_addr ) ? '' : $server_addr;
	if ( !empty( $server_addr ) ) {
		$_SERVER['SERVER_ADDR'] = $_SERVER['LOCAL_ADDR'] = $_WPSS_ENV['SERVER_ADDR'] = $_WPSS_ENV['LOCAL_ADDR'] = $server_addr;
	}
	return !empty( $server_addr ) ? $server_addr : '127.0.0.1';
}

function rs_wpss_get_server_hostname( $sanitize = FALSE, $server_hostname = NULL ) {
	if( FALSE === $sanitize || empty( $server_hostname ) ) {
		$server_hostname = function_exists( 'php_uname' ) ? @php_uname( 'n' ) : @gethostname();
	}
	if( TRUE === $sanitize ) {
		$server_hostname = ( rs_wpss_strlen( $server_hostname ) < 6 || FALSE === strpos( $server_hostname, '.' ) ) ? '' : $server_hostname;
	}
	return $server_hostname;
}

/**
 *  Return C-Block of an IP Address (IPv4 only)
 *  @dependencies	WP_SpamShield::is_valid_ip()
 *  @since			1.9.6.8
 */
function rs_wpss_get_ip_cbl( $ip = NULL ) {
	if( empty( $ip ) || FALSE === strpos( $ip, '.' ) || !WP_SpamShield::is_valid_ip( $ip ) ) { return ''; }
	$ip_arr = explode( '.', $ip ); unset( $ip_arr[3] ); $ip_c = implode( '.', $ip_arr ) . '.';
	return $ip_c;
}

/**
 *  Compare two IP address C-Blocks to see if they match (IPv4 only)
 *  @dependencies	rs_wpss_get_ip_cbl()
 *  @since			...
 */
function rs_wpss_compare_ip_cbl( $ip_1 = NULL, $ip_2 = NULL ) {
	if( empty( $ip_1 ) || empty( $ip_2 ) ) { return FALSE; }
	$ip_1_cbl	= rs_wpss_get_ip_cbl( $ip_1 );
	$ip_2_cbl	= rs_wpss_get_ip_cbl( $ip_2 );
	if( $ip_1_cbl === $ip_2_cbl ) { return TRUE; }
	return FALSE;
}

/**
 *  Get the Reverse DNS ( hostname/domain ) of an IP address
 *  Returns domain
 *  Be sure to run any IPs through WP_SpamShield::sanitize_ip() first before sending here
 *  @dependencies	WP_SpamShield::is_valid_ip(), rs_wpss_is_session_active()
 *  @used by		...
 *  @since			1.0.0
 */
function rs_wpss_get_reverse_dns( $ip ) {
	global $wpss_reverse_dns_cache;
	if( !WP_SpamShield::is_valid_ip( $ip ) ) { return ''; }
	if( !empty( $wpss_reverse_dns_cache[$ip] ) ) { return $wpss_reverse_dns_cache[$ip]; }
	$wpss_hash	= ( defined( 'WPSS_HASH' ) ) ? WPSS_HASH : COOKIEHASH;
	$sess_var	= 'wpss_reverse_dns_cache_'.$wpss_hash;
	if( rs_wpss_is_session_active() ) {
		if( !empty( $wpss_reverse_dns_cache ) && is_array( $wpss_reverse_dns_cache ) && isset( $wpss_reverse_dns_cache[$ip] ) ) {
			if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
			$_SESSION[$sess_var][$ip] = $wpss_reverse_dns_cache[$ip];
			return $wpss_reverse_dns_cache[$ip];
		}
		if( !empty( $_SESSION[$sess_var] )  && is_array( $_SESSION[$sess_var] ) && isset( $_SESSION[$sess_var][$ip] ) ) {
			if( empty( $wpss_reverse_dns_cache ) || !is_array( $wpss_reverse_dns_cache ) ) { $wpss_reverse_dns_cache = array(); }
			$wpss_reverse_dns_cache[$ip] = $_SESSION[$sess_var][$ip];
			return $wpss_reverse_dns_cache[$ip];
		}
		if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
	}
	if( empty( $wpss_reverse_dns_cache ) || !is_array( $wpss_reverse_dns_cache ) ) { $wpss_reverse_dns_cache = array(); }
	$rev_dns	= trim( (string) @gethostbyaddr( $ip ) ); /* Should be a domain, IP is acceptable */
	$rev_dns	= $wpss_reverse_dns_cache[$ip] = ( empty( $rev_dns ) || '.' === $rev_dns || 'localhost' === $rev_dns ) ? '' : $rev_dns;
	if( rs_wpss_is_session_active() ) {
		$_SESSION[$sess_var][$ip]	= $wpss_reverse_dns_cache[$ip];
	}
	return $wpss_reverse_dns_cache[$ip];
}

/**
 *  Get the Forward DNS (IP) of a domain (hostname)
 *  Returns IP address
 *  @dependencies	...
 *  @since			1.0.0 as rs_wpss_get_reverse_dns_ip()
 *  @renamed		1.9.9.8.8
 */
function rs_wpss_get_forward_dns( $domain ) {
	global $wpss_forward_dns_cache; $domain = trim( $domain ); 
	if( empty( $domain ) || $domain === '.' || $domain === 'localhost' ) { return ''; }
	if( !empty( $wpss_forward_dns_cache[$domain] ) ) { return $wpss_forward_dns_cache[$domain]; }
	$wpss_hash	= ( defined( 'WPSS_HASH' ) ) ? WPSS_HASH : COOKIEHASH;
	$sess_var	= 'wpss_forward_dns_cache_'.$wpss_hash;
	if( rs_wpss_is_session_active() ) {
		if( !empty( $wpss_forward_dns_cache ) && is_array( $wpss_forward_dns_cache ) && isset( $wpss_forward_dns_cache[$domain] ) ) {
			if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
			$_SESSION[$sess_var][$domain] = $wpss_forward_dns_cache[$domain];
			return $wpss_forward_dns_cache[$domain];
		}
		if( !empty( $_SESSION[$sess_var] )  && is_array( $_SESSION[$sess_var] ) && isset( $_SESSION[$sess_var][$domain] ) ) {
			if( empty( $wpss_forward_dns_cache ) || !is_array( $wpss_forward_dns_cache ) ) { $wpss_forward_dns_cache = array(); }
			$wpss_forward_dns_cache[$domain] = $_SESSION[$sess_var][$domain];
			return $wpss_forward_dns_cache[$domain];
		}
		if( empty( $_SESSION[$sess_var] ) || !is_array( $_SESSION[$sess_var] ) ) { $_SESSION[$sess_var] = array(); }
	}
	if( empty( $wpss_forward_dns_cache ) || !is_array( $wpss_forward_dns_cache ) ) { $wpss_forward_dns_cache = array(); }
	$fwd_dns	= trim( (string) @gethostbyname( $domain ) ); /* Should be an IP address, domain is not acceptable */
	$fwd_dns	= $wpss_forward_dns_cache[$domain] = ( empty( $fwd_dns ) || !WP_SpamShield::is_valid_ip( $fwd_dns ) ) ? '' : $fwd_dns;
	if( rs_wpss_is_session_active() ) {
		$_SESSION[$sess_var][$domain]	= $fwd_dns;
	}
	return $wpss_forward_dns_cache[$domain];
}

/**
 *  IP / Proxy Information
 *  @dependencies	...
 *  @since			1.0.0
 */
function rs_wpss_ip_proxy_info() {
	global $wpss_ip_proxy_info; if( !empty( $wpss_ip_proxy_info ) && is_array( $wpss_ip_proxy_info ) ) { return $wpss_ip_proxy_info; }
	$ip							= WP_SpamShield::get_ip_addr();
	$ip_proxy_via				= ( !empty( $_SERVER['HTTP_VIA'] )					) ? trim( $_SERVER['HTTP_VIA'] ) : '';
	$ip_proxy_via_lc			= WP_SpamShield::casetrans( 'lower', $ip_proxy_via );
	$masked_ip					= '';
	$masked_ip					= ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] )		) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_X_FORWARDED_FOR'] ) 		: '';
	$masked_ip					= ( $masked_ip === $ip || !WP_SpamShield::is_valid_ip( $masked_ip ) ) ? '' : $masked_ip;
	$http_x_forwarded			= ( !empty( $_SERVER['HTTP_X_FORWARDED'] )			) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_X_FORWARDED'] )			: '';
	$http_forwarded_for			= ( !empty( $_SERVER['HTTP_FORWARDED_FOR'] )		) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_FORWARDED_FOR'] )		: '';
	$http_forwarded				= ( !empty( $_SERVER['HTTP_FORWARDED'] )			) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_FORWARDED'] )			: '';
	$http_x_real_ip				= ( !empty( $_SERVER['HTTP_X_REAL_IP'] )			) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_X_REAL_IP'] )			: '';
	$http_x_sucuri_clientip		= ( !empty( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] )	) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] )	: '';
	$http_cf_connecting_ip		= ( !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] )		) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_CF_CONNECTING_IP'] )		: '';
	$http_incap_client_ip		= ( !empty( $_SERVER['HTTP_INCAP_CLIENT_IP'] )		) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_INCAP_CLIENT_IP'] )		: '';
	$http_client_ip				= ( !empty( $_SERVER['HTTP_CLIENT_IP'] )			) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_CLIENT_IP'] )			: '';
	$rev_dns					= $reverse_dns = $_SERVER['X_REVDNS'] = rs_wpss_get_reverse_dns( $ip );
	$fwd_dns					= ( $rev_dns === $ip ) ? $ip : '';
	$fwd_dns					= ( empty( $fwd_dns ) && !empty( $rev_dns )			) ? rs_wpss_get_forward_dns( $rev_dns ) : $fwd_dns;
	$fwd_dns					= $forward_dns = $_SERVER['X_FWDDNS'] = ( empty( $fwd_dns ) ) ? '[No Data]' : $fwd_dns;
	$fwd_dns_rgx				= rs_wpss_preg_quote( $fwd_dns );
	$rev_dns_lc					= $reverse_dns_lc = WP_SpamShield::casetrans( 'lower', $rev_dns );
	$rev_dns_lc_rgx				= rs_wpss_preg_quote( $rev_dns_lc );
	$rev_dns_lc_rev				= strrev( $rev_dns_lc );
	/* Forward-Confirmed Reverse DNS Test (FCrDNS) */
	$fcrdns						= $_SERVER['X_FCRDNS'] = ( WPSS_Filters::fcrdns( $ip, $rev_dns, $fwd_dns, TRUE ) ) ? '[Verified]' : '[Possibly Spoofed/Misconfigured]';
	/* Detect Use of Proxy */
	$txn_rgx					= "~(t[o0]r(\-|\.)?([e3]?x+(\-|\.)?[i1]t|r[e3]l[a4]y|[a-z]*pr[o0]x+(y|[i1][e3])|[a-z0-9\-]*n[o0]d[e3]|g[a4]t[e3]w[a4]y|[s5]rv|[s5][e3]rv[e3]r)|([e3]?x+(\-|\.)?[i1]t|r[e3]l[a4]y|[a-z]*pr[o0]x+(y|[i1][e3])|[a-z0-9\-]*n[o0]d[e3]|g[a4]t[e3]w[a4]y|[s5]rv|[s5][e3]rv[e3]r)[s5]*[0-9]*(\-|\.)?t[o0]r(\-|\.)|(^|\-|\.)t[o0]r([e3][a4]d[o0]r|[e3]r[o0])?(\-?[0-9]+)?(\-|\.)|(^|\-|\.)[e3]x+[i1]t\-?[0-9]*\.|^t[o0]r[s5]rv[a-z0-9]*\.|\.t[o0]r([a-z]*pr[o0]x+(y|[i1][e3])|[s5]rv|[s5][e3]rv[e3]r|r[e3]l[a4]y)[s5]*".WPSS_RGX_TLD."$|\.[i1]pr[e3]d[a4]t[o0]r".WPSS_RGX_TLD."$|\.privintl\.org$|^v[e3]kt[o0]rt[0-9]+\.[a-z]{2,}[s5][e3]rv[e3]r[s5]*[a-z0-9\-]*".WPSS_RGX_TLD."$|^exit\.[a-z0-9]+\.linode\.rm\.wtf$)~i";
	if( !empty( $rev_dns ) && $ip !== $rev_dns && !WP_SpamShield::is_valid_ip( $rev_dns ) && preg_match( $txn_rgx, $rev_dns ) ) {
		$tor_exit_node			= $_SERVER['X_TOR_EXIT_NODE'] = $_SERVER['WPSS_TOR_EXIT_NODE'] = TRUE;
	}
	$ip_proxy_chrome_compress	= FALSE;
	if( !empty( $ip_proxy_via ) || !empty( $masked_ip ) || !empty( $tor_exit_node ) ) {
		if( empty( $masked_ip ) ) { $masked_ip = '[No Data]'; }
		$ip_proxy = 'PROXY DETECTED'; $ip_proxy_short = 'PROXY'; $ip_proxy_data = $ip.' | MASKED IP: '.$masked_ip; $proxy_status = 'TRUE';
		/* Google Chrome Compression Check */
		if( strpos( $ip_proxy_via_lc, 'chrome-compression-proxy' ) !== FALSE && WP_SpamShield::preg_match( "~^google\-proxy\-(.*)\.google\.com$~i", $rev_dns ) ) { $ip_proxy_chrome_compress = TRUE; }
	} else { $ip_proxy = $ip_proxy_short = 'No Proxy'; $ip_proxy_data = $ip; $proxy_status = 'FALSE'; }
	if( !empty( $rev_dns_lc ) && !isset( $_SERVER['REMOTE_HOST'] ) ) { $_SERVER['REMOTE_HOST'] = $rev_dns_lc; }
	$use_masked_ip = FALSE;
	if( '[Verified]' === $fcrdns ) {
		if( FALSE !== strpos( $rev_dns, '.ip.incapdns.net' ) ) { /* Detect Incapsula, and disable rs_wpss_ubl_cache() - 1.8.9.6 */
			$options = array( 'surrogate' => TRUE, 'ubl_cache_disable' => TRUE, ); WP_SpamShield::update_option( $options );
		}
		if( 'PROXY DETECTED' === $ip_proxy && WP_SpamShield::is_valid_ip( $masked_ip ) && ( FALSE !== strpos( $rev_dns, '.websense.net' ) ) ) {
			/* Whitelist security / web filtering proxy products - Forcepoint Websense, ... */
			$use_masked_ip = TRUE;
		}
	}
	$wpss_ip_proxy_info = compact( 'ip', 'reverse_dns', 'rev_dns', 'reverse_dns_lc', 'rev_dns_lc', 'rev_dns_lc_rgx', 'rev_dns_lc_rev', 'forward_dns', 'fwd_dns', 'fwd_dns_rgx', 'fcrdns', 'ip_proxy_via', 'ip_proxy_via_lc', 'masked_ip', 'http_x_forwarded', 'http_forwarded_for', 'http_forwarded', 'http_x_real_ip', 'http_x_sucuri_clientip', 'http_cf_connecting_ip', 'http_incap_client_ip', 'http_client_ip', 'ip_proxy', 'ip_proxy_short', 'ip_proxy_data', 'proxy_status', 'ip_proxy_chrome_compress', 'use_masked_ip' );
	return $wpss_ip_proxy_info;
}

function rs_wpss_get_ns( $domain ) {
	$domain = rs_wpss_get_email_domain( $domain );
	while( !empty( $domain ) && !WP_SpamShield::preg_match( "~^([a-z]{2,3})(\.[a-z]{2,3})[a-z]*$~i", $domain ) && empty( $ns ) ) {
		$dns_ns = @dns_get_record( $domain, DNS_NS ); $ns = array();
		if( !empty( $dns_ns ) && is_array( $dns_ns ) ) {
			foreach( $dns_ns as $i => $a ) {
				if( empty( $a['target'] ) ) { continue; } else { $ns[] = $a['target']; }
			}
			if( !empty( $ns ) ) { break; }
		}
		$dom_els = explode( '.', $domain ); unset( $dom_els[0] );
		$domain = implode( '.', $dom_els );
	}
	return !empty( $ns ) ? $ns : FALSE;
}

function rs_wpss_get_server_x_req_w() {
	return !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ? WP_SpamShield::casetrans( 'lower', $_SERVER['HTTP_X_REQUESTED_WITH'] ) : '';
}

/**
 *  Get array of variables from query string
 *  @dependencies	rs_wpss_get_query_string()
 *  @since			...
 */
function rs_wpss_get_query_arr( $url ) {
	$query_str = rs_wpss_get_query_string( $url );
	if( !empty( $query_str ) ) { $query_arr = explode( '&', $query_str ); } else { $query_arr = ''; }
	return $query_arr;
}

/**
 *  For removing specific query argument(s)
 *  If you need URL fragments removed, or the entire query string removed, use rs_wpss_fix_url()
 *  @dependencies	...
 *  @since			...
 */
function rs_wpss_remove_query( $url, $skip_wp_args = FALSE ) {
	$query_arr = rs_wpss_get_query_arr( $url );
	if( empty( $query_arr ) ) { return $url; }
	$remove_args = array();
	foreach( $query_arr as $i => $query_arg ) {
		$query_arg_arr = explode( '=', $query_arg );
		$key = $query_arg_arr[0];
		if( !empty( $skip_wp_args ) && ( $key === 'p' || $key === 'page_id' ) ) { continue; } /* DO NOT ADD 'cpage', only 'p' and 'page_id'!! */
		$remove_args[] = $key;
	}
	$clean_url = remove_query_arg( $remove_args, $url );
	return $clean_url;
}

/**
 *  Gives User-Agent with filters
 *  If blank, gives an initialized var to eliminate need for testing if isset() everywhere
 *  Default is sanitized - use raw for testing, and sanitized for output
 *  Added option for raw & lowercase in 1.5
 *  @dependencies	...
 *  @since			...
 */
function rs_wpss_get_user_agent( $raw = FALSE, $lowercase = FALSE ) {
	if( !empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$user_agent = ( !empty ( $raw ) ) ? trim( $_SERVER['HTTP_USER_AGENT'] ) : WP_SpamShield::sanitize_string( $_SERVER['HTTP_USER_AGENT'] );
		if( !empty ( $lowercase ) )	{ $user_agent = WP_SpamShield::casetrans( 'lower', $user_agent ); }
	} else { $user_agent = ''; }
	return $user_agent;
}

function rs_wpss_get_http_accept( $raw = FALSE, $lowercase = FALSE, $lang = FALSE, $encd = FALSE ) {
	/**
	 *  Gives $_SERVER['HTTP_ACCEPT'], $_SERVER['HTTP_ACCEPT_LANGUAGE'], and $_SERVER['HTTP_ACCEPT_ENCODING'] with filters
	 *  Default is sanitized
	 */
	$http_accept = $http_accept_language = $http_accept_encoding = '';
	if( !empty( $_SERVER['HTTP_ACCEPT'] ) )				{ $http_accept			= $_SERVER['HTTP_ACCEPT']; }
	if( !empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) )	{ $http_accept_language	= $_SERVER['HTTP_ACCEPT_LANGUAGE']; }
	if( !empty( $_SERVER['HTTP_ACCEPT_ENCODING'] ) )	{ $http_accept_encoding	= $_SERVER['HTTP_ACCEPT_ENCODING']; }
	$raw_serv_var							= $http_accept;
	if( !empty( $lang ) ) { $raw_serv_var	= $http_accept_language; }
	if( !empty( $encd ) ) { $raw_serv_var	= $http_accept_encoding; }
	if( !empty( $raw_serv_var ) ) {
		$http_accept_var = ( !empty ( $raw ) ) ? trim( $raw_serv_var ) : WP_SpamShield::sanitize_string( $raw_serv_var );
		if( !empty ( $lowercase ) ) { $http_accept_var = WP_SpamShield::casetrans( 'lower', $http_accept_var ); }
	} else { $http_accept_var = ''; }
	return $http_accept_var;
}

function rs_wpss_get_referrer( $raw = FALSE, $lowercase = FALSE, $init = FALSE ) {
	/**
	 *  Gives $_SERVER['HTTP_REFERER'] with filters
	 *  Default is sanitized
	 */
	global $wpss_referrer; if( !empty( $wpss_referrer ) ) { return $wpss_referrer; }
	$http_referrer	= $init_referrer = '';
	$site_domain	= WPSS_SERVER_NAME;
	if( !empty( $_SERVER['HTTP_REFERER'] ) ){ $http_referrer = trim( $_SERVER['HTTP_REFERER'] ); }
	if( !empty( $_COOKIE['JCS_INENREF'] ) )	{
		$init_referrer			= $_COOKIE['JCS_INENREF'];
		$init_referrer_no_query	= rs_wpss_fix_url( $init_referrer, TRUE, TRUE ); /* Remove query string and fragments */
		if( strpos( $init_referrer_no_query, $site_domain ) !== FALSE ) { $init_referrer = ''; } /* Tracking referrals from other sites only */
	}
	if( empty( $init_referrer ) && !empty( $_COOKIE['_referrer_og'] ) )	{
		$init_referrer			= $_COOKIE['_referrer_og'];
		$init_referrer_no_query	= rs_wpss_fix_url( $init_referrer, TRUE, TRUE ); /* Remove query string and fragments */
		if( strpos( $init_referrer_no_query, $site_domain ) !== FALSE ) { $init_referrer = ''; } /* Tracking referrals from other sites only */
	}
	$wpss_referrer = $http_referrer;
	if( !empty( $init ) ) { $wpss_referrer = $init_referrer; }
	if( !empty( $wpss_referrer ) ) {
		$wpss_referrer = !empty ( $raw ) ? trim( $wpss_referrer ) : esc_url_raw( $wpss_referrer );
		if( !empty ( $lowercase ) )	{ $wpss_referrer = WP_SpamShield::casetrans( 'lower', $wpss_referrer ); }
	} else { $wpss_referrer = ''; }
	return $wpss_referrer;
}

/**
 *	Returns type of error - JavaScript/Cookies Layer or Algorithmic Layer - 'jsck' or 'algo'
 *	@dependencies	...
 *	@since			1.8.9.6
 */
function rs_wpss_get_error_type( $error_code ) {
	if( empty( $error_code ) ) { return FALSE; }
	return ( FALSE !== strpos( $error_code, 'COOKIE-' ) || FALSE !== strpos( $error_code, 'REF-2-1023-' ) || FALSE !== strpos( $error_code, 'JSONST-1000-' ) || FALSE !== strpos( $error_code, 'FVFJS-' ) || FALSE !== strpos( $error_code, 'JQHFT-' ) ) ? 'jsck' : 'algo';
}

/**
 *	Checks if URL from user input is valid
 *	Use for built-in contact form
 *	@dependencies	WP_SpamShield::casetrans(), WP_SpamShield::is_valid_ip()
 *	@used by		...
 *	@since			1.9.7
 */
function rs_wpss_is_valid_url( $url ) {
	if( empty( $url ) || FALSE === strpos( $url, '.' ) ) { return FALSE; }
	$url = WP_SpamShield::casetrans( 'lower', $url );
	if( WP_SpamShield::is_valid_ip( $url ) ) { return FALSE; }
	if( $url !== esc_url_raw( $url ) ) { $url_san = esc_url_raw( $url ); return FALSE; }
	if( function_exists( 'filter_var' ) && filter_var( $url, FILTER_VALIDATE_URL ) ) { return TRUE; }
	return FALSE;
}

/**
 *	Checks if valid first, then checks email against blacklists
 *	Use for one-stop email check
 *	@since			1.9.5.8
 *	@return			bool
 */
function rs_wpss_is_valid_email( $email ) {
	if( empty( $email ) || !is_string( $email ) ) { return FALSE; }
	$email = WP_SpamShield::casetrans( 'lower', $email );
	if( !is_email( $email ) || WPSS_Filters::email_blacklist_chk( $email ) ) { return FALSE; }
	return TRUE;
}

/**
 *	Checks if email domain exists (has MX records)
 *	Use for contact form, registration, anything
 *	@since			1.9.8.5
 */
function rs_wpss_email_domain_exists( $email ) {
	if( empty( $email ) || !is_string( $email ) ) { return FALSE; }
	$email = WPSS_Func::lower( $email );
	if( !is_email( $email ) ) { return FALSE; }
	if( rs_wpss_is_free_email( $email ) ) { return TRUE; }
	$email_domain = rs_wpss_parse_email( $email, 'domain' );
	if ( @checkdnsrr( $email_domain.'.', 'MX' ) ) { return TRUE; }
	return FALSE;
}

/**
 *	Checks if domain exists (has ANY records)
 *	Use for contact form, anything
 *	@since			1.9.8.5
 */
function rs_wpss_domain_exists( $domain ) {
	if( empty( $domain ) ) { return FALSE; }
	$domain = WPSS_Func::lower( $domain );
	if ( @checkdnsrr( $domain.'.', 'ANY' ) ) { return TRUE; }
	if ( @checkdnsrr( $domain.'.', 'NS' ) ) { return TRUE; }
	return FALSE;
}

/**
 *  This adds some additional validation to the built-in WordPress is_email() function.
 *  Adds: Max length checks (according to RFC), ESP-specific validation for Yahoo and Gmail
 *	Note: Not adding DNS checks in this function -- it does these later, during anti-spam checks.
 *  @dependencies	...
 *  @since			1.9.5.6
 */
function rs_wpss_adv_validate_email( $is_email, $email, $message = NULL ) {
	if( empty( $email ) || FALSE === $is_email ) { return FALSE; }
	if( !empty( $message ) ) { return $is_email; }
	$email = rs_wpss_sanitize_gmail( $email, FALSE, FALSE );
	/* Default PHP Filter */
	if( function_exists( 'filter_var' ) && ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) { return FALSE; }
	/* General RFC Tests */
	if( rs_wpss_strlen( $email ) > 254 ) { return FALSE; }
	$parsed = rs_wpss_parse_email( $email, NULL, FALSE ); extract( $parsed );
	$local_strlen = rs_wpss_strlen( $local );
	if( $local_strlen > 64 ) { return FALSE; }
	/* ESP-Specific Tests */
	if( !empty( $provider ) ) {
		if( $provider === 'gmail' || $provider === 'googlemail' ) {
			if( $local_strlen < 6 || FALSE !== strpos( $local, '-' ) ) { return FALSE; }
		} elseif( $provider === 'yahoo' ) {
			if( WP_SpamShield::preg_match( "~[^0-9a-z_\-\.]+~i", $local ) || rs_wpss_substr_count( $local, '.' ) > 1 ) { return FALSE; }
		}
	}
	return $is_email;
}

function rs_wpss_is_free_email( $email ) {
	return ( WP_SpamShield::preg_match( "~\@".WPSS_RGX_FREEMAIL."$~i", $email ) );
}

/**
 *	Sanitizes Gmail/Googlemail addresses for validation and spam checks
 *	Remove dots and alias (section of local part after '+')
 *	$rem_alias - Local part - Remove alias only - leave dots
 *	@since			1.9.5.6
 */
function rs_wpss_sanitize_gmail( $email, $rem_alias = FALSE, $chk_is_email = TRUE ) {
	if( empty( $email ) ) { return ''; }
	$parsed = rs_wpss_parse_email( $email, NULL, $chk_is_email ); extract( $parsed );
	if( !empty( $provider ) && ( $provider === 'gmail' || $provider === 'googlemail' ) ) {
		if( FALSE !== strpos( $local, '+' ) ) { $l = explode( '+', $local ); $local = $l[0]; }
		if( FALSE === $rem_alias ) { $local = str_replace( '.', '', $local ); }
	}
	$email = $local.'@'.$domain;
	return $email;
}

/**
 *	Parses email address similar to PHP parse_url() - https://secure.php.net/manual/en/function.parse-url.php
 *	Returns an associative array containing the various components of the email address.
 *	Use $component to get a specific component: 'local', 'domain', 'provider' (if major free email, returns provider)
 *	@since			1.9.5.6
 */
function rs_wpss_parse_email( $email, $component = NULL, $chk_is_email = TRUE ) {
	if( TRUE === $chk_is_email ) { if( !is_email( $email ) ) { return array(); } }
	$e = explode( '@', $email );
	$parsed = array( 'local' => $e[0], 'domain' => $e[1], 'provider' => '', );
	if( rs_wpss_is_free_email( $email ) ) { $p = explode( '.', $e[1] ); $parsed['provider'] = $p[0]; }
	if( !empty( $component ) && ( $component === 'local' || $component === 'domain' || $component === 'provider' ) ) { return $parsed[$component]; }
	return $parsed;
}

/**
 *	Check if domain is a Google Domain
 *	Google Domains - updated at: https://www.google.com/supported_domains
 *	@since			1.7.8
 */
function rs_wpss_is_google_domain( $domain ) {
	$google_domains =
		array(
			'.com','.ad','.ae','.com.af','.com.ag','.com.ai','.al','.am','.co.ao','.com.ar','.as','.at','.com.au','.az','.ba','.com.bd','.be','.bf','.bg','.com.bh','.bi','.bj','.com.bn','.com.bo','.com.br','.bs','.bt','.co.bw','.by','.com.bz','.ca','.cd','.cf','.cg','.ch','.ci','.co.ck','.cl','.cm','.cn','.com.co','.co.cr','.com.cu','.cv','.com.cy','.cz','.de','.dj','.dk','.dm','.com.do','.dz','.com.ec','.ee','.com.eg','.es','.com.et','.fi','.com.fj','.fm','.fr','.ga','.ge','.gg','.com.gh','.com.gi','.gl','.gm','.gp','.gr','.com.gt','.gy','.com.hk','.hn','.hr','.ht','.hu','.co.id','.ie','.co.il','.im','.co.in','.iq','.is','.it','.je','.com.jm','.jo','.co.jp','.co.ke','.com.kh','.ki','.kg','.co.kr','.com.kw','.kz','.la','.com.lb','.li','.lk','.co.ls','.lt','.lu','.lv','.com.ly','.co.ma','.md','.me','.mg','.mk','.ml','.com.mm','.mn','.ms','.com.mt','.mu','.mv','.mw','.com.mx','.com.my','.co.mz','.com.na','.com.nf','.com.ng','.com.ni','.ne','.nl','.no','.com.np','.nr','.nu','.co.nz','.com.om','.com.pa','.com.pe','.com.pg','.com.ph','.com.pk','.pl','.pn','.com.pr','.ps','.pt','.com.py','.com.qa','.ro','.ru','.rw','.com.sa','.com.sb','.sc','.se','.com.sg','.sh','.si','.sk','.com.sl','.sn','.so','.sm','.sr','.st','.com.sv','.td','.tg','.co.th','.com.tj','.tk','.tl','.tm','.tn','.to','.com.tr','.tt','.com.tw','.co.tz','.com.ua','.co.ug','.co.uk','.com.uy','.co.uz','.com.vc','.co.ve','.vg','.co.vi','.com.vn','.vu','.ws','.rs','.co.za','.co.zm','.co.zw','.cat',
		);
	$google_dom_base	= array( 'google', 'blogger', );
	$tmp_slug			= 'XXGOOGLEXX';
	foreach( $google_domains as $i => $ext ) {
		$domain_tmp_slug	= $tmp_slug.$ext;
		$regex_tmp			= WPSS_Filters::get_rgx_ptrn( $domain_tmp_slug, '', 'domain' );
		$regex_check_phrase	= str_replace( $tmp_slug, '('.implode( '|', $google_dom_base ).')', $regex_tmp );
		if( WP_SpamShield::preg_match( $regex_check_phrase, $domain ) ) { return TRUE; }
	}
	return FALSE;
}

function rs_wpss_rbkmd( $dat, $mod = 'en', $exp = FALSE, $imp = FALSE, $case = NULL, $del = '~' ) {
	if( !empty( $imp ) && is_array( $dat ) ) { $dat = implode(  $del, $dat ); }
	$lft = '.!:;1234567890|abcdefghijklmnopqrstuvwxyz{}()<>~@#$%^&*?,_-+= \/';
	$rgt = 'ghiJVWXyz@#$%^&*?,_-+=1234567890ABCdefGHIjklMNOpqrSTUvwxYZabcDEF';
	if( $mod === 'en' ) { $mod_dat = strtr( $dat, $lft, $rgt ); } else { $mod_dat = strtr( $dat, $rgt, $lft ); }
	if( !empty( $case ) ) { $mod_dat = WP_SpamShield::casetrans( $case, $mod_dat ); }
	if( !empty( $exp ) ) { $mod_dat = explode(  $del, $mod_dat ); }
	return $mod_dat;
}

function rs_wpss_get_active_plugins( $sort = TRUE ) {
	global $wpss_active_plugins;
	if( empty( $wpss_active_plugins ) || !is_array( $wpss_active_plugins ) ) { $wpss_active_plugins = get_option( 'active_plugins' ); }
	if( TRUE === $sort ) { $wpss_active_plugins = WP_SpamShield::sort_unique( $wpss_active_plugins ); }
	return $wpss_active_plugins;
}

function rs_wpss_get_active_network_plugins() {
	global $wpss_active_network_plugins;
	if( empty( $wpss_active_network_plugins ) || !is_array( $wpss_active_network_plugins ) ) {
		$wpss_active_network_plugins = get_site_option( 'active_sitewide_plugins' );
		if( !empty( $wpss_active_network_plugins ) && is_array( $wpss_active_network_plugins ) ) {
			$wpss_active_network_plugins = WP_SpamShield::sort_unique( array_flip( $wpss_active_network_plugins ) );
		}
	}
	return $wpss_active_network_plugins;
}

function rs_wpss_is_user_admin() {
	global $wpss_user_can_manage_options,$wpss_is_user_logged_in;
	if( empty( $_COOKIE ) || rs_wpss_ck_sess_o() ) { $wpss_user_can_manage_options = $wpss_is_user_logged_in = 'NO'; return FALSE; }
	if( empty( $wpss_user_can_manage_options ) ) { $wpss_user_can_manage_options = current_user_can( 'manage_options' ) ? 'YES' : 'NO'; }
	if( $wpss_user_can_manage_options === 'YES' ) { $wpss_is_user_logged_in = 'YES'; $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	$wpss_user_can_manage_options === 'NO';
	return FALSE;
}

function rs_wpss_is_user_logged_in() {
	/**
	 *  Wrapper for is_user_logged_in() when only checking current user
	 *  Much faster check if user has no cookies or only session cookie --> minimize DB queries
	 */
	global $wpss_is_user_logged_in,$wpss_user_can_manage_options;
	if( empty( $_COOKIE ) || rs_wpss_ck_sess_o() ) { $wpss_is_user_logged_in = $wpss_user_can_manage_options = 'NO'; return FALSE; }
	if( empty( $wpss_is_user_logged_in ) ) { $wpss_is_user_logged_in = is_user_logged_in() ? 'YES' : 'NO'; }
	if( $wpss_is_user_logged_in === 'YES' ) { $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	$wpss_is_user_logged_in = $wpss_user_can_manage_options = 'NO';
	return FALSE;
}

function rs_wpss_valid_sess_ck() {
	global $wpss_session_id,$wpss_session_name,$wpss_valid_sess_ck;
	if( !empty( $wpss_valid_sess_ck ) ) { $wpss_valid_sess_ck = TRUE; return TRUE; }
	if( empty( $_COOKIE ) || !rs_wpss_is_session_active() ) { $wpss_valid_sess_ck = FALSE; return FALSE; }
	$wpss_valid_sess_ck = FALSE;
	if( empty( $wpss_session_id ) ) 	{ $wpss_session_id		= @session_id(); }
	if( empty( $wpss_session_name ) ) 	{ $wpss_session_name	= @session_name(); }
	if( empty( $wpss_session_name ) )	{ $wpss_session_name	= 'PHPSESSID'; }
	if( !empty( $wpss_session_id ) && !empty( $wpss_session_name ) && !empty( $_COOKIE[$wpss_session_name] ) && $_COOKIE[$wpss_session_name] === $wpss_session_id ) { $wpss_valid_sess_ck = TRUE; }
	return $wpss_valid_sess_ck;
}

function rs_wpss_ck_sess_o() {
	/* Checks if user has only a valid session cookie - common with more recent bots */
	if( empty( $_COOKIE ) || count( $_COOKIE ) >= 2 ) { return FALSE; }
	return ( rs_wpss_valid_sess_ck() || count( $_COOKIE ) === 1 );
}

function rs_wpss_error_txt( $case = 'UC' ) {
	/* $case = 'def' (default - unaltered), 'UC' (uppercase) */
	$txt = 'Error';
	if( !rs_wpss_is_lang_en_us() ) {
		$txt = trim( str_replace( array( '&rsaquo;', 'WordPress &raquo; ', ), array( '&raquo;', ' ', ), __( 'WordPress &rsaquo; Error' ) ) );
		if( rs_wpss_strlen( $txt ) > 17 ) { $txt = trim( str_replace( ':', '', __( 'ERROR:' ) ) ); }
	}
	if( empty( $txt ) || !is_string( $txt ) ) { $txt = 'Error'; }
	return $case === 'UC' ? WPSS_Func::upper( $txt ) : $txt;
}

function rs_wpss_first_name_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR.WPSS_DS.'admin-'.$locale.'.mo' );
	}
	$txt = __( 'First Name' );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_last_name_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR.WPSS_DS.'admin-'.$locale.'.mo' );
	}
	$txt = __( 'Last Name' );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_disp_name_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR.WPSS_DS.'admin-'.$locale.'.mo' );
	}
	$txt = __( 'Display name publicly as' );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

/**
 *	User login text
 *	@dependencies	...
 *	@since			1.9.9.9.9
 */
function rs_wpss_user_login_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	global $wpss_loaded_languages;
	if( !rs_wpss_is_lang_en_us() && empty( $wpss_loaded_languages['admin'] ) ) {
		$locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR.WPSS_DS.'admin-'.$locale.'.mo' );
	}
	$txt = __( 'Username' );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_enter_your_x_txt( $case = 'def' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = __( 'Please enter your %s', 'wp-spamshield' );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_plug_hmpage_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = trim( str_replace( '&#187;', '', __( 'Plugin Homepage &#187;' ) ) );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_plug_wppage_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = trim( str_replace( '&#187;', '', __( 'WordPress.org Plugin Page &#187;' ) ) );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_donate_txt( $case = 'def' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = rs_wpss_is_lang_en_us() ? 'Donate to WP-SpamShield' : trim( str_replace( '&#187;', '', __( 'Donate to this plugin &#187;' ) ) );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_tsg_txt( $case = 'UCW' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = rs_wpss_is_lang_en_us() ? 'Troubleshooting Guide' : __( 'Troubleshooting' );
	return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_blocked_txt( $case = 'def' ) {
	/* $case = 'def' (default - unaltered), 'UCW' (uppercase words) */
	$txt = __( 'SPAM BLOCKED', 'wp-spamshield' ); return $case === 'UCW' ? WP_SpamShield::casetrans( 'ucwords', $txt ) : $txt;
}

function rs_wpss_doc_txt() {
	return __( 'Documentation' );
}

function rs_wpss_is_lang_en_us( $strict = TRUE ) {
	/**
	 *	Test if site is set to use English (US) - the default - or another language/localization
	 *	Strict		- English (US), no translation being used
	 *	Not strict	- English, but localized translations may be in use
	 */
	global $wpss_locale, $wpss_lang_en_us;
	$wpss_locale		= ( !empty( $wpss_locale ) && is_string( $wpss_locale ) ) ? $wpss_locale : get_locale();
	$rgx_ptn			= ( TRUE === $strict ) ? "^(en(_us)?)?$" : "^(en(_[a-z]{2})?)?$";
	$wpss_lang_en_us	= ( WP_SpamShield::preg_match( "~".$rgx_ptn."~i", $wpss_locale ) );
	return $wpss_lang_en_us;
}

function rs_wpss_is_lang_t7() {
	/* Added 1.9.9.9.8 */
	global $wpss_locale, $wpss_lang_t7;
	$wpss_locale		= ( !empty( $wpss_locale ) && is_string( $wpss_locale ) ) ? $wpss_locale : get_locale();
	$rgx_ptn			= "^((en|de|es|fr|it|pt|nl)(_[a-z]{2})?)?$";
	$wpss_lang_t7		= ( WP_SpamShield::preg_match( "~".$rgx_ptn."~i", $wpss_locale ) );
	return $wpss_lang_t7;
}

function rs_wpss_wf_geoiploc( $ip = NULL, $disp = FALSE ) {
	/**
	 *  If WordFence installed, get GEO IP Location data
	 *  @since 1.9.5.2
	 */
	global $wpss_geoiploc_data, $wpss_geolocation;
	if( ( class_exists( 'wfUtils' ) && WPSS_Compatibility::is_plugin_active( 'wordfence' ) ) || function_exists( 'rs_wpss_alt_geoiploc' ) ) {
		/* $start = microtime( TRUE ); */
		if( empty( $ip ) ) { $ip = WP_SpamShield::get_ip_addr(); }
		if( ( rs_wpss_is_session_active() && ( ( empty( $_SESSION['wpss_geoiploc_data_'.WPSS_HASH] ) && empty( $wpss_geoiploc_data ) ) || ( empty( $_SESSION['wpss_geolocation_'.WPSS_HASH] ) && empty( $wpss_geolocation ) ) || ( empty( $_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] ) ) || ( !empty( $_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] ) && $_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] !== $ip ) ) ) || ( !rs_wpss_is_session_active() && ( empty( $wpss_geoiploc_data ) || empty( $wpss_geolocation ) ) ) ) {
			if( class_exists( 'wfUtils' ) && method_exists( 'wfUtils', 'getIPGeo' ) ) {
				$wpss_geoiploc_data = wfUtils::getIPGeo( $ip );
			} elseif( function_exists( 'rs_wpss_alt_geoiploc' ) ) {
				$wpss_geoiploc_data = rs_wpss_alt_geoiploc( $ip );
			} else { return ''; }
			/**
			 *  $wpss_geoiploc_data = array( 'IP' => $ip, 'city' => $city, 'region' => $region, 'countryName' => $countryName, 'countryCode' => $countryCode, 'lat' => $lat, 'lon' => $long );
			 */
			if( empty( $wpss_geoiploc_data ) || !is_array( $wpss_geoiploc_data ) ) { return ''; }
			extract( $wpss_geoiploc_data );
			$city_region = !empty( $city ) && !empty( $region ) ? ' - '.$city.', '.$region : '';
			if( rs_wpss_is_session_active() ) {
				$_SESSION['wpss_geoiploc_ip_'.WPSS_HASH] = $ip;
			}
			if( FALSE === $disp ) {
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			}
		}
		if( TRUE === $disp ) {
			if( rs_wpss_is_session_active() && !empty( $_SESSION['wpss_geolocation_'.WPSS_HASH] ) ) {
				$wpss_geolocation = $_SESSION['wpss_geolocation_'.WPSS_HASH];
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geolocation;
			} elseif( !empty( $wpss_geolocation ) ) {
				if( rs_wpss_is_session_active() ) {
					$_SESSION['wpss_geolocation_'.WPSS_HASH] = $wpss_geolocation;
				}
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geolocation;
			} else {
				$wpss_geolocation = $countryCode.' - '.$countryName.$city_region;
				if( rs_wpss_is_session_active() ) {
					$_SESSION['wpss_geolocation_'.WPSS_HASH] = $wpss_geolocation;
				}
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geolocation;
			}
		} else {
			if( rs_wpss_is_session_active() && !empty( $_SESSION['wpss_geoiploc_data_'.WPSS_HASH] ) ) {
				$wpss_geoiploc_data = $_SESSION['wpss_geoiploc_data_'.WPSS_HASH];
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			} elseif( !empty( $wpss_geoiploc_data ) ) {
				if( rs_wpss_is_session_active() ) {
					$_SESSION['wpss_geoiploc_data_'.WPSS_HASH] = $wpss_geoiploc_data;
				}
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			} else {
				$wpss_geoiploc_data = $countryCode.' - '.$countryName.$city_region;
				if( rs_wpss_is_session_active() ) {
					$_SESSION['wpss_geoiploc_data_'.WPSS_HASH] = $wpss_geoiploc_data;
				}
				/* $rs_wpss_timer_bm( $start ); */
				return $wpss_geoiploc_data;
			}
		}
	}
	return '';
}

function rs_wpss_wf_geoiploc_short( $ip = NULL ) {
	global $wpss_geoloc_short;
	if( empty ( $wpss_geoloc_short ) ) {
		global $wpss_geolocation; if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
		if( empty( $wpss_geolocation ) ) { return ''; }
		$tmp = explode( ' - ', $wpss_geolocation  );
		if( isset( $tmp[2] ) ) { unset( $tmp[2] ); }
		$wpss_geoloc_short = implode( ' - ', $tmp );
	}
	return $wpss_geoloc_short;
}

function rs_wpss_login_init() {
	$GLOBALS['wpss_is_login_page'] = TRUE;
}

function rs_wpss_wc_login_init() {
	global $wpss_is_wc_login_page; $wpss_is_wc_login_page = TRUE;
}

function rs_wpss_wc_registration_init() {
	global $wpss_is_wc_registration_page,$wpss_wc_reg_form_active;
	$wpss_is_wc_registration_page = $wpss_wc_reg_form_active = TRUE;
	remove_action( 'register_form', 'rs_wpss_register_form_append', 1 );
	add_action( 'woocommerce_register_form', 'rs_wpss_register_form_append', 1 );
}

function rs_wpss_is_login_page() {
	global $pagenow, $wpss_is_login_page, $wpss_is_wc_login_page;
    if( $pagenow === 'wp-login.php' || $pagenow === 'wp-register.php' || !empty( $wpss_is_login_page ) || !empty( $wpss_is_wc_login_page ) ) { $wpss_is_login_page = TRUE; return TRUE; }
    if( strpos( $_SERVER['PHP_SELF'], '/wp-login.php' ) !== FALSE || strpos( $_SERVER['PHP_SELF'], '/wp-register.php' ) !== FALSE ) { $wpss_is_login_page = TRUE; return TRUE; }
    if( rs_wpss_is_wc_login_page() ) { $wpss_is_login_page = TRUE; return TRUE; }
    return FALSE;
}

function rs_wpss_is_wc_login_page() {
	/**
	 *  Check if login page for WooCommerce
	 *  @since 1.9.5.5
	 */
	global $wpss_is_wc_login_page;
    if( !empty( $wpss_is_wc_login_page ) ) { return TRUE; }
	if( WPSS_Compatibility::is_woocom_enabled() ) {
		$url_rev	= rs_wpss_fix_url( WPSS_THIS_URL, TRUE, TRUE, TRUE );
		$str1		= strrev( '/my-account/' );
		$str2		= strrev( '/my-account' );
		if( strpos( $url_rev, $str1 ) === 0 || strpos( $url_rev, $str2 ) === 0 ) { $wpss_is_wc_login_page = TRUE; return TRUE; }
		if( function_exists( 'wc_get_page_permalink' ) && FALSE !== strpos( WPSS_THIS_URL, wc_get_page_permalink( 'myaccount' ) ) ) { $wpss_is_wc_login_page = TRUE; return TRUE; }
	}
    return FALSE;
}

function rs_wpss_is_multisite_register_page() {
	global $pagenow, $wpss_is_multisite_register_page;
    if( $pagenow === 'wp-signup.php' ) { $wpss_is_multisite_register_page = TRUE; return TRUE; }
    if( FALSE !== strpos( $_SERVER['PHP_SELF'], '/wp-signup.php' ) ) { $wpss_is_multisite_register_page = TRUE; return TRUE; }
    return FALSE;
}

function rs_wpss_is_wc_registration_page() {
	/**
	 *  Check if registration page for WooCommerce
	 *  @since 1.9.6.2
	 */
	global $wpss_is_wc_registration_page,$wpss_wc_reg_form_active;
    if( !empty( $wpss_is_wc_registration_page ) || !empty( $wpss_wc_reg_form_active ) ) { $wpss_is_wc_registration_page = TRUE; return TRUE; }
	if( rs_wpss_is_wc_login_page() ) {
		$wc_reg_enabled = get_option( 'woocommerce_enable_myaccount_registration' );
		if( $wc_reg_enabled === 'yes' || $wc_reg_enabled == TRUE ) { $wpss_is_wc_registration_page = TRUE; return TRUE; }
	}
    return FALSE;
}

/**
 *	Check if registration page for 3rd party plugin/theme
 *	@dependencies	...
 *	@since			1.9.5.2
 */
function rs_wpss_is_3p_register_page() {
	if( class_exists( 'BuddyPress' ) || defined( 'APP_FRAMEWORK_DIR' ) ) {
		$url_rev	= rs_wpss_fix_url( WPSS_THIS_URL, TRUE, TRUE, TRUE );
		$str1		= strrev( '/register/' );
		$str2		= strrev( '/register' );
		if( strpos( $url_rev, $str1 ) === 0 || strpos( $url_rev, $str2 ) === 0 ) { return TRUE; }
	}
    return FALSE;
}

function rs_wpss_is_xmlrpc() {
	return ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST );
}

function rs_wpss_is_pingback() {
    return ( defined( 'WPSS_XMLRPC_PINGBACK' ) && WPSS_XMLRPC_PINGBACK );
}

function rs_wpss_is_doing_cron() {
	return ( defined( 'DOING_CRON' ) && DOING_CRON );
}

/**
 *  Check if WP-CLI is running 
 *	@dependencies	none
 *	@since			1.9.9.9.4
 */
function rs_wpss_is_cli() {
	return ( ( defined( 'WP_CLI' ) && WP_CLI ) || 0 === strpos( PHP_SAPI, 'cli' ) );
}

/**
 *  Check if WP is currently installling
 *	@dependencies	WP_SpamShield::is_wp_ver()
 *	@since			1.9.9.9.4
 */
function rs_wpss_is_installing() {
	if( WP_SpamShield::is_wp_ver( '4.4' ) ) {
		return ( wp_installing() );
	}
	return ( defined( 'WP_INSTALLING' ) && WP_INSTALLING );
}

function rs_wpss_is_admin_sproc( $admin = FALSE ) {
	global $wpss_is_admin_sproc;
	if( rs_wpss_is_cli() || rs_wpss_is_doing_cron() || rs_wpss_is_installing() || rs_wpss_invalid_browser_footprint() ) { $wpss_is_admin_sproc = 'NO'; return FALSE; }
	if( empty( $wpss_is_admin_sproc ) ) {
		$t = rs_wpss_rbkmd(WPSS_BL::iapdat(),'de',TRUE,FALSE,'upper');
		if( defined( $t[0] ) && ( isset( $_GET[$t[1]] ) || isset( $_GET[$t[2]] ) ) ) {
			if( TRUE === $admin ) { $wpss_is_admin_sproc = 'YES'; return TRUE; } elseif( rs_wpss_is_user_admin() ) { $wpss_is_admin_sproc = 'YES'; return TRUE; }
		}
	}
	if( 'YES' === $wpss_is_admin_sproc ) { $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	$wpss_is_admin_sproc = 'NO';
    return FALSE;
}

/**
 *	Check if WP is doing REST. get_rest_url() was added in WP ver 4.4
 *	@dependencies	WP_SpamShield::is_wp_ver()
 *	@since			...
 */
function rs_wpss_is_doing_rest() {
	if( defined( 'REST_REQUEST' ) && REST_REQUEST ) { return TRUE; }
	if( !function_exists( 'get_rest_url' ) || !WP_SpamShield::is_wp_ver( '4.4' ) ) { return FALSE; }
	$api_root = get_rest_url();	/* See: wp-includes/rest-api.php */
	return ( FALSE !== strpos( WPSS_THIS_URL, $api_root ) );
}

/**
 *	Non-form REST. Check if WP is doing REST request(s) not generated by HTML forms.
 *	@dependencies	WP_SpamShield::is_wp_ver()
 *	@since			1.9.12
 */
function rs_wpss_is_doing_nf_rest() {
	return ( rs_wpss_is_doing_nojax_rest() && !WPSS_Compatibility::is_cf7_doing_rest() && !WPSS_Compatibility::is_jp_doing_rest( TRUE ) );
}

/**
 *	Check if WP is doing AJAX
 *	@dependencies	WP_SpamShield::is_wp_ver()
 *	@since			...
 */
function rs_wpss_is_doing_ajax() {
	if( WP_SpamShield::is_wp_ver( '4.7' ) ) {
		return ( wp_doing_ajax() );
	}
	return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
}

/**
 *	Check if WP is doing AJAX through the REST API
 *	@dependencies	rs_wpss_is_doing_rest(), rs_wpss_is_ajax_request(), ...
 *	@since			1.9.13
 */
function rs_wpss_is_doing_ajax_rest() {
	return ( rs_wpss_is_doing_rest() && rs_wpss_is_ajax_request() );
}

/**
 *	Check if WP is processing non-AJAX request through the REST API
 *	@dependencies	rs_wpss_is_doing_rest(), rs_wpss_is_ajax_request(), ...
 *	@since			1.9.14
 */
function rs_wpss_is_doing_nojax_rest() {
	return ( rs_wpss_is_doing_rest() && !rs_wpss_is_ajax_request() );
}

/**
 *	Detect if AJAX request needs anti-spam check
 *	@dependencies	...
 *	@since			1.9.9.9.7
 */
function rs_wpss_ajax_check_request() {
	global $wpss_ajax_check_request;
	if( isset( $wpss_ajax_check_request ) && is_bool( $wpss_ajax_check_request ) ) { return $wpss_ajax_check_request; }
	if( rs_wpss_is_doing_ajax() && rs_wpss_is_ajax_request() && !empty( $_POST ) && is_array( $_POST ) ) {
		$a = ( !empty( $_POST['action'] ) ) ? trim( WPSS_Func::lower( $_POST['action'] ) ) : '';
		foreach( $_POST as $k => $v ) {
			$v = trim( WPSS_Func::lower( $v ) ); $k = trim( WPSS_Func::lower( $k ) );
			if( 'submit' === $v ) { $wpss_ajax_check_request = TRUE; return TRUE; }
		}
		if( !empty( $a ) && WP_SpamShield::preg_match( "~(form|guestbook|[ck]onta[ck]t|[ck]om+ent|regist(er|ration))~i", $a ) ) {
			$wpss_ajax_check_request = TRUE; return TRUE;
		}
	}
	$wpss_ajax_check_request = FALSE;
	return $wpss_ajax_check_request;
}

/**
 *	Check if request is AJAX heartbeat
 *	@dependencies	...
 *	@since			1.9.9.9.7
 */
function rs_wpss_is_heartbeat() {
	return ( isset( $_POST['action'], $_POST['screen_id'], $_POST['_nonce'] ) && 'heartbeat' === $_POST['action'] && rs_wpss_is_doing_ajax() && rs_wpss_is_ajax_request() );
}

/**
 *	Check if WooCommerce is doing AJAX
 *	TO DO: Move to WPSS_Compatibility class
 *	@dependencies	rs_wpss_is_ajax_request(), WPSS_Compatibility::is_woocom_enabled()
 *	@since			1.9.9.9.7
 */
function rs_wpss_is_wc_doing_ajax() {
	return (
		( defined( 'WC_DOING_AJAX' ) && WC_DOING_AJAX )
		||
		( !empty( $_GET['wc-ajax'] ) && rs_wpss_is_ajax_request() && WPSS_Compatibility::is_woocom_enabled() )
	);
}

/**
 *	Check if WooCommerce AJAX endpoint
 *	TO DO: Move to WPSS_Compatibility class
 *	@dependencies	WPSS_Compatibility::is_woocom_enabled(), ...
 *	@since			1.9.9.9.7
 */
function rs_wpss_is_wc_ajax_endpoint() {
	return ( !empty( $_GET['wc-ajax'] ) && WPSS_Compatibility::is_woocom_enabled() );
}

function rs_wpss_is_ajax_request() {
	$server_x_req_w = rs_wpss_get_server_x_req_w();
	return ( $server_x_req_w === 'xmlhttprequest' );
}

/**
 *	Check if WooCommerce AJAX request
 *	@dependencies	rs_wpss_is_wc_ajax_endpoint(), rs_wpss_is_wc_doing_ajax()
 *	@since			1.9.9.9.7
 */
function rs_wpss_is_wc_ajax_request() {
	global $wpss_is_wc_ajax_request;
	if( isset( $wpss_is_wc_ajax_request ) && is_bool( $wpss_is_wc_ajax_request ) ) { return $wpss_is_wc_ajax_request; }
	$wpss_is_wc_ajax_request	= ( rs_wpss_is_wc_ajax_endpoint() && rs_wpss_is_wc_doing_ajax() );
	return $wpss_is_wc_ajax_request;
}

/**
 *	Check if AJAX JSON request
 *	@dependencies	rs_wpss_get_http_accept(), rs_wpss_is_ajax_request()
 *	@since			1.9.16
 */
function rs_wpss_is_ajax_json_request() {
	$http_accept = str_replace( ' ', '', rs_wpss_get_http_accept() );
	return ( rs_wpss_is_ajax_request() && 0 === strpos( $http_accept, 'application/json,text/javascript' ) );
}

/**
 *	Check if JSON request
 *	@dependencies	rs_wpss_get_http_accept()
 *	@since			...
 */
function rs_wpss_is_json_request() {
	$http_accept = str_replace( ' ', '', rs_wpss_get_http_accept() );
	return ( 0 === strpos( $http_accept, 'application/json' ) );
}

function rs_wpss_is_comment_request() {
	global $wpss_is_comment_request;
	if( !empty( $wpss_is_comment_request ) ) {
		return ( 'YES' === $wpss_is_comment_request );
	}
	if( rs_wpss_is_comments_post_url() ) {
		if( isset( $_POST['comment'] ) && ( isset( $_POST['comment_post_ID'] ) || isset( $_POST['comment_post_id'] ) ) ) {
			if( get_option( 'require_name_email' ) ) {
				if( isset( $_POST['author'], $_POST['email'] ) ) { $wpss_is_comment_request = 'YES'; return TRUE; }
			} else { $wpss_is_comment_request = 'YES'; return TRUE; }
		}
	}
	$wpss_is_comment_request = 'NO';
	return FALSE;
}

function rs_wpss_is_comments_post_url() {
	return ( FALSE !== strpos( WPSS_THIS_URL, '/wp-comments-post.php' ) || FALSE !== strpos( WPSS_THIS_URL, WPSS_COMMENTS_POST_URL ) );
}

function rs_wpss_is_doing_reg() {
	global $wpss_reg_inprog;
    return !empty( $wpss_reg_inprog );
}

function rs_wpss_is_doing_contact() {
	global $wpss_contact_inprog;
    return !empty( $wpss_contact_inprog );
}

function rs_wpss_is_doing_comment() {
	global $wpss_comment_inprog;
    return !empty( $wpss_comment_inprog );
}

function rs_wpss_is_doing_pingback() {
	global $wpss_pingback_inprog;
    return !empty( $wpss_pingback_inprog );
}

/**
 *	Check if request is from the local website/server
 *	@dependencies	WP_SpamShield::get_ip_addr(), rs_wpss_compare_ip_cbl()
 *	@since			1.9.7.8
 */
function rs_wpss_is_local_request() {
	$ip = WP_SpamShield::get_ip_addr();
	return ( $ip === WPSS_SERVER_ADDR || rs_wpss_compare_ip_cbl( $ip, WPSS_SERVER_ADDR ) );
}

function rs_wpss_is_robots() {
	return ( WPSS_THIS_URL === WPSS_SITE_URL.'/robots.txt' );
}

function rs_wpss_is_search() {
	return ( !empty( $GLOBALS['wp_query'] ) && is_search() );
}

/**
 *	Adds some enhancements to WordPress' 404 error handling.
 *	Enhances security (block bad visitors via 403 instead of 404), helps SEO, and reduce server load due to better error handling.
 *	TO DO: Move to WPSS_Security class
 *	@dependencies	...
 *	@used by		...
 *	@since			...
 */
function rs_wpss_mod_status_header( $status_header, $code, $description, $protocol ) {
	remove_filter( 'wpss_filter_404', 'rs_wpss_mod_status_header', 100 );
	global $wpss_mod_status_header_run,$wp_query;
	if( empty( $wpss_mod_status_header_run ) ) { $wpss_mod_status_header_run = TRUE; } else { return $status_header; }
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return $status_header; }
	$code = $new_code = intval( $code );
	if( $code != 404 || $wp_query->posts || is_admin() || is_robots() || rs_wpss_is_local_request() || rs_wpss_is_doing_cron() || rs_wpss_is_installing() || rs_wpss_is_cli() || WPSS_Compatibility::is_edd_doing_api() ) { return $status_header; }
	if( $code == 404 ) {
		$url = WPSS_THIS_URL;
		if(
				( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && rs_wpss_is_session_active() && !rs_wpss_is_user_logged_in() && !rs_wpss_is_admin_sproc() )
			&&	( !WP_SpamShield::preg_match( "~(/(autodiscover/autodiscover|mail/config\-v1\.[1-9]|browserconfig|bingsiteauth)\.xml$|/manifest\.json|/favicon\.ico|/google[\w]+\.html|(android-chrome|apple\-touch\-icon(\-precomposed)?|favicon|mstile)(\-\d+x\d+)?\.png|/apple\-app\-site\-association)~i", $url ) )
		) {
			if( !isset( $_SESSION['wpss_404_hits_'.WPSS_HASH] ) ) { $_SESSION['wpss_404_hits_'.WPSS_HASH] = 1; } else { ++$_SESSION['wpss_404_hits_'.WPSS_HASH]; }
			if( !isset( $_SESSION['wpss_404_urls_'.WPSS_HASH] ) ) { $_SESSION['wpss_404_urls_'.WPSS_HASH] = array(); }
			$_SESSION['wpss_404_urls_'.WPSS_HASH][] = $url;
			$wpss_404_limit = 7;	/* Excessive number of 404s is a sign of probing */
			if( !empty( $_SESSION['wpss_404_hits_'.WPSS_HASH] ) && $_SESSION['wpss_404_hits_'.WPSS_HASH] >= $wpss_404_limit ) { $new_code = 403; }
		}
	}
	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) || ( rs_wpss_is_session_active() && !empty( $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] ) ) ) {
		$new_code = 403;	/* Block bad visitors */
	}
	if ( $new_code !== $code ) {
		$code = $new_code; $description = get_status_header_desc( $code );
	}
	$status_header = $protocol.' '.$code.' '.$description;
	if( $code >= 400 ) {
		@header( 'X-Robots-Tag: noindex', TRUE );	/* Helps SEO - Tell search engines not to index: "4XX Client Error", & "5XX Server Error" HTTP Statuses */
		if( 403 == $code || 406 == $code ) {
			WP_SpamShield::wp_die( $description, TRUE, $code );
		}
	}
	return $status_header;
}

function rs_wpss_is_firefox( $waterfox = FALSE ) {
	global $is_gecko; $is_firefox; $is_waterfox;
	$user_agent = rs_wpss_get_user_agent( TRUE, FALSE );
	if( !empty( $is_gecko ) && FALSE !== strpos( $user_agent, 'Firefox' ) && FALSE === strpos( $user_agent, 'SeaMonkey' ) ) {
		if( !empty( $waterfox ) ) {
			$is_waterfox = ( FALSE !== strpos( $user_agent, 'Waterfox' ) ) ? TRUE : FALSE; return $is_waterfox;
		}
		$is_firefox = TRUE; return TRUE;
	}
	$is_firefox = FALSE; return FALSE;
}

function rs_wpss_is_ms_edge() {
	global $is_edge;
	$user_agent = rs_wpss_get_user_agent( TRUE, FALSE );
	if( FALSE !== strpos( $user_agent, 'Edge' ) && FALSE !== strpos( $user_agent, 'AppleWebKit' ) && FALSE !== strpos( $user_agent, 'Chrome' ) && FALSE !== strpos( $user_agent, 'Safari' ) && 0 === strpos( $user_agent, 'Mozilla/5.0' ) ) { $is_edge = TRUE; return TRUE; }
    return FALSE;
}

function rs_wpss_get_browser() {
	global $is_chrome, $is_waterfox, $is_firefox, $is_iphone, $is_macIE, $is_winIE, $is_IE, $is_edge, $is_opera, $is_safari, $is_lynx, $is_NS4;
	if( !empty( $is_chrome ) ) {
		$user_agent = rs_wpss_get_user_agent( TRUE, FALSE );
		if( FALSE !== strpos( $user_agent, ' OPR/' ) ) { $is_opera = TRUE; $is_chrome = FALSE; }
	}
	$browser = '';
	$major_browsers = array( 'Chrome' => $is_chrome, 'Waterfox' => rs_wpss_is_firefox( TRUE ), 'Firefox' => rs_wpss_is_firefox(), 'iPhone' => $is_iphone, 'Mac Internet Explorer' => $is_macIE, 'Windows Internet Explorer' => $is_winIE, 'Internet Explorer' => $is_IE, 'Edge' => rs_wpss_is_ms_edge(), 'Opera' => $is_opera, 'Safari' => $is_safari, 'Lynx' => $is_lynx, 'Netscape 4' => $is_NS4, );
	foreach( $major_browsers as $n => $s ) { if( !empty( $s ) ) { $browser = $n; break; } }
    return $browser;
}

/**
 *	Checks for obvious invalid browser footprints
 *	Use for registration, comments, contact forms, 3P contact forms
 *	HTTP_USER_AGENT, HTTP_ACCEPT, HTTP_ACCEPT_LANGUAGE, HTTP_ACCEPT_ENCODING
 *	@dependencies	rs_wpss_get_http_accept()
 *	@since			1.9.8.9
 */
function rs_wpss_invalid_browser_footprint() {
	global $wpss_invalid_browser_footprint;
	if( isset( $wpss_invalid_browser_footprint ) && is_bool( $wpss_invalid_browser_footprint ) ) { return $wpss_invalid_browser_footprint; }
	$http_accept					= trim( rs_wpss_get_http_accept( TRUE, TRUE								) );
	$http_accept_language			= trim( rs_wpss_get_http_accept( TRUE, TRUE, TRUE						) );
	$http_accept_encoding			= trim( rs_wpss_get_http_accept( TRUE, TRUE, FALSE, TRUE				) );
	$http_proto						= trim( WP_SpamShield::casetrans( 'lower', $_SERVER['SERVER_PROTOCOL']	) );
	$wpss_invalid_browser_footprint	=
		(
				'http/1.0' === $http_proto || 0 === strpos( $http_proto, 'http/0.' )
			||	!WP_SpamShield::preg_match( "~^text/html,\s?application/xhtml\+xml,\s?(application/xml;|image/jxr,|\*/\*)~i", $http_accept )
			||	!WP_SpamShield::preg_match( "~^[a-z]{2}(\-[a-z]{2})?(,\s?[a-z]{2}(\-[a-z]{2})?;\s?q\=0\.[1-9](,\s?[a-z]{2}(\-[a-z]{2})?;\s?q\=0\.[1-9])*)?$~i", $http_accept_language )
			||	!WP_SpamShield::preg_match( "~^gzip,\s?deflate(?:,\s?sdch)?(?:,\s?br)?$~i", $http_accept_encoding ) 
		)
		? TRUE : FALSE;
	if( TRUE === $wpss_invalid_browser_footprint ) {
		$enable_ibf_filter = WP_SpamShield::get_option( 'enable_ibf_filter' );
		if(
				empty( $enable_ibf_filter ) || TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' )
			||	rs_wpss_is_local_request() || rs_wpss_is_json_request() || rs_wpss_is_doing_ajax() || rs_wpss_is_ajax_request() || rs_wpss_is_doing_cron() || rs_wpss_is_xmlrpc() || rs_wpss_is_doing_rest() || rs_wpss_is_installing() || rs_wpss_is_cli() 
		) {
			$wpss_invalid_browser_footprint	= FALSE; return FALSE;
		}
		global $wpss_cache_check; if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$cache_check_status	= $wpss_cache_check['cache_check_status'];
		if( $cache_check_status === 'ACTIVE' ) {
			$wpss_invalid_browser_footprint	= FALSE; return FALSE;
		}
	}
	return $wpss_invalid_browser_footprint;
}

function rs_wpss_comments_open() {
	return ( @is_singular() && @comments_open() );
}

/**
 *	Encode email addresses so harvester bots can't read and grab them
 *	@dependencies	...
 *	@since			1.9.0.5
 */
function rs_wpss_encode_emails( $string ) {
	if( rs_wpss_is_user_logged_in() ) { return $string; }
	$spamshield_options = WP_SpamShield::get_option();
	
	if( !empty( $spamshield_options['disable_email_encode'] ) ) { return $string; }
	if( strpos( $string, '@' ) === FALSE && strpos( $string, '[at]' ) === FALSE ) { return $string; }

	/* BYPASS - HOOK */
	$encode_emails_bypass = apply_filters( 'wpss_encode_emails_bypass', FALSE );
	if( !empty( $encode_emails_bypass ) ) { return $string; }

	$encode_method	= 'rs_wpss_encode_str';
	$regex_phrase	= '{
			(?:mailto:)?
			(?:
				[-!#$%&*+/=?^_`.{|}~\w\x80-\xFF]+
			|
				".*?"
			)
			(?:\@|\ *\[at\]\ *)
			(?:
				[-a-z0-9\x80-\xFF]+((?:\.|\ *\[dot\]\ *)[-a-z0-9\x80-\xFF]+)*(?:\.|\ *\[dot\]\ *)[a-z]+
			|
				\[[\d.a-fA-F:]+\]
			)
		}xi';
	return preg_replace_callback( $regex_phrase, create_function( '$matches', 'return '.$encode_method.'($matches[0]);' ), $string );
}

/**
 *  Function to encode email addresses into random HTML entities
 *	@dependencies	...
 *  @since			1.9.0.5
 */
function rs_wpss_encode_str( $string ) {

	$chars	= str_split( $string );
	$seed	= mt_rand( 0, (int) abs( crc32( $string ) / rs_wpss_strlen( $string ) ) );
	foreach( $chars as $key => $char ) {
		$ord = ord( $char );
		if( $ord < 128 ) {
			$r = ( $seed * ( 1 + $key ) ) % 100;
			if( $r > 60 && $char !== '@' ) { ; } elseif( $r < 45 ) { $chars[$key] = '&#x'.dechex( $ord ).';'; } else { $chars[$key] = '&#'.$ord.';'; }
		}
	}
	return implode( '', $chars );
}

/* Standard Functions - END */

/**
 *  @hook			action|plugins_loaded|1
 *	@dependencies	...
 *  @since			...
 */
function rs_wpss_load_languages() {
	if( rs_wpss_is_lang_en_us() ) { return; }
	load_plugin_textdomain( WPSS_PLUGIN_NAME, FALSE, WPSS_I18N_LANG_PATH );
}

/**
 *  @hook			action|load_textdomain|10
 *	@dependencies	...
 *  @since			...
 */
function rs_wpss_check_loaded_languages( $domain, $mofile ) {
	if( rs_wpss_is_lang_en_us() ) { return; }
	global $wpss_loaded_languages;
	if( empty( $wpss_loaded_languages ) || !is_array( $wpss_loaded_languages ) ) { $wpss_loaded_languages = array(); }
	if( empty( $wpss_loaded_languages[$domain] ) || !is_array( $wpss_loaded_languages[$domain] ) ) { $wpss_loaded_languages[$domain] = array(); }
	$wpss_loaded_languages[$domain][$mofile] = TRUE;
	$locale = get_locale();
	if( 'default' === $domain && $mofile === WP_LANG_DIR . "/admin-$locale.mo" ) {
		$wpss_loaded_languages['admin'] = TRUE;
	}
}

/**
 *  @hook			action|init|1
 *	@dependencies	...
 *  @since			...
 */
function rs_wpss_first_action() {

	if( rs_wpss_is_admin_sproc() ) { return; }
	rs_wpss_maybe_start_session();
	/* Add all commands after this */

	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) || !empty( $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] ) ) {
		$_SERVER['WPSS_SEC_THREAT'] = TRUE;
		if( rs_wpss_is_session_active() ) { $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] = TRUE; }
		if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && WP_SpamShield::is_mdbug() ) {
			if( !headers_sent() ) { @header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403); }
			die( '403 Forbidden' );
		}
	}

	if( rs_wpss_is_session_active() ) {

		/* Add Vars Here */
		$key_main_page_hits		= 'wpss_page_hits_'.WPSS_HASH;
		$key_main_pages_hist 	= 'wpss_pages_hit_'.WPSS_HASH;
		$key_main_hits_per_page	= 'wpss_pages_hit_count_'.WPSS_HASH;
		$key_first_ref			= 'wpss_referer_init_'.WPSS_HASH;
		$current_ref			= rs_wpss_get_referrer();
		$key_auth_hist 			= 'wpss_author_history_'.WPSS_HASH;
		$key_comment_auth 		= 'comment_author_'.WPSS_HASH;
		$key_email_hist			= 'wpss_author_email_history_'.WPSS_HASH;
		$key_auth_url_hist 		= 'wpss_author_url_history_'.WPSS_HASH;

		if( empty( $_SESSION['wpss_user_ip_init_'.WPSS_HASH] ) ) { $_SESSION['wpss_user_ip_init_'.WPSS_HASH] = WP_SpamShield::get_ip_addr(); }
		if( empty( $_SESSION['wpss_user_agent_init_'.WPSS_HASH] ) ) { $_SESSION['wpss_user_agent_init_'.WPSS_HASH] = rs_wpss_get_user_agent(); }

		$_SESSION['wpss_version_'.WPSS_HASH] 			= WPSS_VERSION;
		$_SESSION['wpss_site_url_'.WPSS_HASH_ALT] 		= WPSS_SITE_URL;
		$_SESSION['wpss_plugin_url_'.WPSS_HASH_ALT] 	= WPSS_PLUGIN_URL;
		$_SESSION['wpss_user_ip_current_'.WPSS_HASH]	= WP_SpamShield::get_ip_addr();
		$_SESSION['wpss_user_agent_current_'.WPSS_HASH] = rs_wpss_get_user_agent();

		if( !is_admin() && !current_user_can( 'moderate_comments' ) ) {
			/* Page hits */
			if( empty( $_SESSION[$key_main_page_hits] ) ) { $_SESSION[$key_main_page_hits] = 0; }
			++$_SESSION[$key_main_page_hits];
			/* Pages visited history */
			if( empty( $_SESSION[$key_main_pages_hist] ) ) {
				$_SESSION[$key_main_pages_hist] = array(); $_SESSION[$key_main_hits_per_page] = array();
			}
			$_SESSION[$key_main_pages_hist][] = WPSS_THIS_URL;
			/* Initial Referrer - Where Visitor Entered Site // External Referrer --> Landing Page */
			if( empty( $_SESSION[$key_first_ref] ) ) {
				if( !empty( $_COOKIE['JCS_INENREF'] ) && FALSE === strpos( $_COOKIE['JCS_INENREF'], WPSS_SERVER_NAME ) ) {
					$_SESSION[$key_first_ref] = $_COOKIE['JCS_INENREF'];
				} elseif( !empty( $current_ref ) && FALSE === strpos( $current_ref, WPSS_SERVER_NAME ) ) { $_SESSION[$key_first_ref] = $current_ref; }
			}
			if( !empty( $_COOKIE[$key_comment_auth] ) ) {
				$stored_author_data 	= rs_wpss_get_author_data();
				$stored_author 			= $stored_author_data['comment_author'];
				$stored_author_email	= $stored_author_data['comment_author_email'];
				$stored_author_url 		= $stored_author_data['comment_author_url'];
				if( empty( $_SESSION[$key_auth_hist] ) && !empty( $stored_author ) ) {
					$_SESSION[$key_auth_hist] = array(); $_SESSION[$key_auth_hist][] = $stored_author;
				}
				if( empty( $_SESSION[$key_email_hist] ) && !empty( $stored_author_email ) ) {
					$_SESSION[$key_email_hist] = array(); $_SESSION[$key_email_hist][] = $stored_author_email;
				}
				if( empty( $_SESSION[$key_auth_url_hist] ) && !empty( $stored_author_url ) ) {
					$_SESSION[$key_auth_url_hist] = array(); $_SESSION[$key_auth_url_hist][] = $stored_author_url;
				}
			}
		}

	}
}

function rs_wpss_check_cache_status() {
	/* TEST FOR CACHING */
	global $wpss_cache_check,$wpss_active_plugins;
	if( !empty( $wpss_cache_check ) && !empty( $wpss_cache_check['cache_check_status'] ) ) { return $wpss_cache_check; }
	$wpss_active_plugins				= rs_wpss_get_active_plugins();
	$wpss_active_plugins_ser			= WP_SpamShield::casetrans( 'lower', serialize( $wpss_active_plugins ) );
	$wpss_caching_status				= ( defined( 'WP_CACHE' ) && TRUE === WP_CACHE ) ? 'ACTIVE' : 'INACTIVE';
	$wpss_caching_enabled_status		= ( defined( 'ENABLE_CACHE' ) && TRUE === ENABLE_CACHE ) ? 'ACTIVE' : 'INACTIVE';
	/* Check if any popular cache plugins are active */
	$popular_cache_plugins				= unserialize( WPSS_POPULAR_CACHE_PLUGINS );
	$popular_cache_plugins_active		= array();
	$popular_cache_plugins_temp			= FALSE;
	foreach( $popular_cache_plugins as $i => $plugin ) {
		if( FALSE !== strpos( $wpss_active_plugins_ser, $plugin ) ) {
			$popular_cache_plugins_temp = TRUE;
			$popular_cache_plugins_active[] = $plugin;
		}
	}
	if( TRUE === $popular_cache_plugins_temp ) { $wpss_caching_plugin_status = 'ACTIVE'; } else { $wpss_caching_plugin_status = 'INACTIVE'; }
	/* Check for Server Caching ( Varnish, etc. ) */
	$wpss_server_caching_status = WPSS_Compatibility::is_surrogate() ? 'ACTIVE' : 'INACTIVE';
	/* Add more here... */

	/* Overall test if caching is active. */
	if( $wpss_caching_status === 'ACTIVE' || $wpss_caching_enabled_status === 'ACTIVE' || $wpss_caching_plugin_status === 'ACTIVE' || $wpss_server_caching_status === 'ACTIVE' ) { $cache_check_status = 'ACTIVE'; } else { $cache_check_status = 'INACTIVE'; }
	/**
	 *  NOT USING FOR NOW
	 *  $wpss_caching_plugins_active	= serialize( $popular_cache_plugins_active );
	 *  $wpss_all_plugins_active		= serialize( $wpss_active_plugins );
	 */
	$wpss_cache_check = array(
		'cache_check_status'			=> $cache_check_status,
		/**
		 *  NOT USING FOR NOW
		 *  'caching_status'			=> $wpss_caching_status,
		 *  'caching_enabled_status'	=> $wpss_caching_enabled_status,
		 *  'caching_plugin_status'		=> $wpss_caching_plugin_status,
		 *  'caching_plugins_active'	=> $wpss_caching_plugins_active,
		 *  'all_plugins_active'		=> $wpss_all_plugins_active,
		 */
		);
	return $wpss_cache_check;
}

function rs_wpss_count() {
	$spam_count = get_option( 'spamshield_count' );
	if( empty( $spam_count ) ) { $spam_count = 0; }
	return $spam_count;
}

function rs_wpss_increment_count( $type = 'jsck' ) {
	/* $type: 'algo', 'jsck' */
	$max_attempts = 7;
	$max_js_errors = 3;
	update_option( 'spamshield_count', rs_wpss_count() + 1 );
	if( rs_wpss_is_session_active() ) {
		if( empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) ) 		{ $_SESSION['user_spamshield_count_'.WPSS_HASH] = 0; }
		if( empty( $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] ) )	{ $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] = 0; }
		if( empty( $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] ) )	{ $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] = 0; }
		++$_SESSION['user_spamshield_count_'.WPSS_HASH];
		++$_SESSION['user_spamshield_count_'.$type.'_'.WPSS_HASH];
		if( $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] >= $max_attempts && $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] < $max_js_errors ) { rs_wpss_ubl_cache( 'set' ); } /* Changed 1.8.9.6 */
	}
}

function rs_wpss_reg_count() {
	$rs_wpss_reg_count = get_option( 'spamshield_reg_count' );
	if( empty( $rs_wpss_reg_count ) ) { $rs_wpss_reg_count = 0; }
	return $rs_wpss_reg_count;
}

function rs_wpss_increment_reg_count() {
	update_option( 'spamshield_reg_count', rs_wpss_reg_count() + 1, FALSE );
	if( rs_wpss_is_session_active() ) {
		if( empty( $_SESSION['user_spamshield_reg_count_'.WPSS_HASH] ) ) { $_SESSION['user_spamshield_reg_count_'.WPSS_HASH] = 0; }
		++$_SESSION['user_spamshield_reg_count_'.WPSS_HASH];
	}
}

function rs_wpss_procdat( $method = 'get' ) {
	/* $method: 'reset','get' */
	$wpss_proc_data = array( 'total_tracked' => 0, 'total_wpss_time' => 0, 'avg_wpss_proc_time' => 0, 'total_comment_proc_time' => 0, 'avg_comment_proc_time' => 0, 'total_wpss_avg_tracked' => 0, 'total_avg_wpss_proc_time' => 0, 'avg2_wpss_proc_time' => 0 );
	if( $method === 'reset' ) { update_option( 'spamshield_procdat', $wpss_proc_data ); } else { return $wpss_proc_data; }
}


/* Counters - BEGIN */

function spamshield_counter( $counter_option = 0 ) {
	/**
	 *  As of 1.8.4, this calls WPSS_Old_Counters::counter_short()
	 *  Display Counter
	 *  Implementation: <?php if( function_exists('spamshield_counter') ) { spamshield_counter(1); } ?>
	 */
	$atts = array();
	$atts['style'] = $counter_option;
	echo WPSS_Old_Counters::counter_short( $atts );
}

function spamshield_counter_sm( $counter_sm_option = 1 ) {
	/**
	 *  As of 1.8.4, this calls WPSS_Old_Counters::counter_sm_short()
	 *  Display Small Counter
	 *  Implementation: <?php if( function_exists('spamshield_counter_sm') ) { spamshield_counter_sm(1); } ?>
	 */
	$atts = array();
	$atts['style'] = $counter_sm_option;
	echo WPSS_Old_Counters::counter_sm_short( $atts );
}

/* Old counter functions were here. Moved to include in V1.9.3 */


/* Widgets */
function rs_wpss_load_widgets() {
	if( rs_wpss_is_admin_sproc() ) { return; }
	register_widget( 'WP_SpamShield_Counter_LG' );
	register_widget( 'WP_SpamShield_Counter_CG' );
	register_widget( 'WP_SpamShield_End_Blog_Spam' );
}

/* Widget classes were here. Moved to include in V1.8.4 */

/* Counters - END */


/**
 *	Reset the Blocked Spam Log
 *	$admin_ips	- Optional
 *  $get_fws	- File writeable status - returns bool
 *  $clr_hta	- Reset .htaccess only, don't reset log
 *  $mk_log		- Make log log file if none exists
 */
function rs_wpss_log_reset( $admin_ips = NULL, $get_fws = FALSE, $clr_hta = FALSE, $mk_log = FALSE ) {
	$admin_ips = !empty( $admin_ips ) && is_array( $admin_ips ) ? $admin_ips : get_option( 'spamshield_admins' );
	$admin_ips = rs_wpss_remove_expired_admins( $admin_ips );
	if( !empty( $admin_ips ) && is_array( $admin_ips ) ) {
		$admin_ips	= array_map( 'intval', $admin_ips );
		$admin_ips	= WP_SpamShield::sort_unique( array_flip( $admin_ips ) );
	} elseif( rs_wpss_is_user_admin() ) {
		$current_ip	= WP_SpamShield::get_ip_addr();
		rs_wpss_update_user_ip( NULL, TRUE, $admin_ips );
		if( WP_SpamShield::is_valid_ip( $current_ip ) ) {
			$admin_ips	= (array) $current_ip;
		}
	}
	if( empty( $admin_ips ) || !is_array( $admin_ips ) ) {
		$last_admin_ip	= get_option( 'spamshield_last_admin' );
		if( !empty( $last_admin_ip ) && WP_SpamShield::is_valid_ip( $last_admin_ip ) ) {
			$admin_ips	= (array) $last_admin_ip;
		}
	}
	$log_key	= rs_wpss_get_log_key();
	$log_key_uc	= WPSS_Func::upper( $log_key );
	$log_filnm	= ( WP_SpamShield::is_mdbug() ) ? 'temp-comments-log.txt' : 'temp-comments-log-'.$log_key.'.txt';
	$log_filns	= array( '', $log_filnm, 'temp-comments-log.init.txt', '.htaccess', 'htaccess.txt', 'htaccess.init.txt' ); /* Filenames - log, log_empty, htaccess, htaccess,_orig, htaccess_empty */
	$log_perlr	= array( 775, 664, 664, 664, 664, 664 ); /* Permission level recommended */
	$log_perlm	= array( 755, 644, 644, 644, 644, 644 ); /* Permission level minimum */
	$log_files	= array(); /* Log files with full paths */
	foreach( $log_filns as $f => $filn ) { $log_files[] = WPSS_PLUGIN_DATA_PATH.'/'.$filn; }
	/* 1 - Create temp-comments-log-{random hash}.txt if it doesn't exist */
	@clearstatcache();
	if( ! @file_exists( $log_files[1] ) ) {
		WPSS_PHP::chmod( $log_files[2], 664 );
		@copy( $log_files[2], $log_files[1] );
		WPSS_PHP::chmod( $log_files[1], 664 );
	}
	if( !empty( $mk_log ) ) { return FALSE; }
	/* 2 - Create .htaccess if it doesn't exist */
	@clearstatcache();
	if( ! @file_exists( $log_files[3] ) ) {
		WPSS_PHP::chmod( $log_files[0], 775 ); WPSS_PHP::chmod( $log_files[4], 664 ); WPSS_PHP::chmod( $log_files[5], 664 );
		@rename( $log_files[4], $log_files[3] ); @copy( $log_files[5], $log_files[4] );
		foreach( $log_files as $f => $file ) { WPSS_PHP::chmod( $file, $log_perlr[$f] ); }
	}
	/* 3 - Check file permissions and fix */
	@clearstatcache();
	$log_perms = array(); /* File permissions */
	foreach( $log_files as $f => $file ) { $log_perms[] = WPSS_PHP::fileperms( $file ); }
	foreach( $log_perlr as $p => $perlr ) {
		if( $log_perms[$p] < $perlr || !wp_is_writable( $log_files[$p] ) ) {
			foreach( $log_files as $f => $file ) { WPSS_PHP::chmod( $file, $log_perlr[$f] ); } /* Correct the permissions... */
			break;
		}
	}
	/* 4 - Clear files by copying fresh versions to existing files */
	if( empty( $clr_hta ) ) {
		if( @file_exists( $log_files[1] ) && @file_exists( $log_files[2] ) ) { @copy( $log_files[2], $log_files[1] ); } /* Log file */
	}
	if( @file_exists( $log_files[3] ) && @file_exists( $log_files[5] ) ) { @copy( $log_files[5], $log_files[3] ); } /* .htaccess file */
	/* 5 - Write .htaccess */
	$htaccess_data	= $access_ap22 = '';
	$access_ap24	= 'Require all denied'.WPSS_EOL;
	if( !empty( $admin_ips ) && is_array( $admin_ips ) ) {
		$ip_rgx = '^('.str_replace( array( '.', ':', ), array( '\.', '\:', ), implode( '|', $admin_ips ) ).')$';
		$htaccess_data .= '<IfModule mod_setenvif.c>'.WPSS_EOL."\t".'SetEnvIf Remote_Addr '.$ip_rgx.' WPSS_ACCESS_'.$log_key_uc.WPSS_EOL.'</IfModule>'.WPSS_EOL.WPSS_EOL;
		$access_ap22 = "\t\t".'Allow from env=WPSS_ACCESS_'.$log_key_uc.WPSS_EOL;
		$access_ap24 = 'Require env WPSS_ACCESS_'.$log_key_uc.WPSS_EOL;
	}
	$htaccess_data .= '<Files '.$log_filnm.'>'.WPSS_EOL;
	$htaccess_data .= "\t".'# Apache 2.2'.WPSS_EOL."\t".'<IfModule !mod_authz_core.c>'.WPSS_EOL."\t\t".'Order deny,allow'.WPSS_EOL."\t\t".'Deny from all'.WPSS_EOL.$access_ap22."\t".'</IfModule>'.WPSS_EOL.WPSS_EOL;
	$htaccess_data .= "\t".'# Apache 2.4'.WPSS_EOL."\t".'<IfModule mod_authz_core.c>'.WPSS_EOL."\t\t".$access_ap24."\t".'</IfModule>'.WPSS_EOL.WPSS_EOL;
	$htaccess_data .= "\t".'ForceType "text/plain; charset=UTF-8"'.WPSS_EOL;
	$htaccess_data .= '</Files>'.WPSS_EOL;
	$htaccess_fp = @fopen( $log_files[3],'a+' );
	@fwrite( $htaccess_fp, $htaccess_data );
	@fclose( $htaccess_fp );
	/* 6 - If $get_fws (File Writeable Status), repeat #3 again and return status */
	if( !empty( $get_fws ) ) {
		@clearstatcache();
		$log_perms = array(); /* File permissions */
		foreach( $log_files as $f => $file ) { $log_perms[] = WPSS_PHP::fileperms( $file ); }
		foreach( $log_perlm as $p => $perlm ) {
			if( $log_perms[$p] < $perlm || !wp_is_writable( $log_files[$p] ) ) { return FALSE; }
		}
		return TRUE;
	}

}

function rs_wpss_modify_advanced( $feature, $old_val = NULL, $new_val = NULL ) {
	/**
	 *  Modify Advanced Features
	 *  @since 1.9.1
	 */
	if( empty( $old_val ) || empty( $new_val ) || $new_val === $old_val ) { return FALSE; }
	$file = WPSS_PLUGIN_INCL_PATH.'/advanced.php';
	WPSS_PHP::chmod( $file, 775 );
	$phrase_old = 'define( \''.$feature.'\', '.$old_val.' );';
	$phrase_new = 'define( \''.$feature.'\', '.$new_val.' );';
	$file_old = @file_get_contents( $file );
	$file_new = str_replace( $phrase_old, $phrase_new, $file_old );
	if( $file_new === $file_old ) { return FALSE; }
	@file_put_contents( $file, $file_new );
	WPSS_PHP::chmod( $file, 644 );
	$file_mod = @file_get_contents( $file );
	$GLOBALS['wpss_modify_advanced'] = $success = ( !empty( $file_mod ) && $file_mod === $file_new );
	/* TO DO: Add DB options for this, and remove config file edit when doing multisite compat */
	return $success;
}

/**
 *  Check jscripts.php for 403/404/500 errors and fix by switching plugin into Compatibility Mode
 *  @dependencies	rs_wpss_get_http_status(), WP_SpamShield::casetrans(), WPSS_Compatibility::is_surrogate(), rs_wpss_modify_advanced(), WP_SpamShield::update_option()
 *  @since			1.9.1
 *  @modified		1.9.7	Added check for Surrogates: Varnish, Cloudflare (Rocket Loader), etc.
 */
function rs_wpss_jscripts_403_fix() {
	global $is_apache,$is_IIS,$is_iis7,$is_nginx;
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return array( 'blocked' => FALSE, 'compat_mode' => TRUE, 'surrogate' => FALSE, ); }
	$blocked = $compat_mode = $surrogate = FALSE;
	$url = WPSS_PLUGIN_JS_URL.'/jscripts.php';
	$hdr_data = WP_SpamShield::get_headers( $url, 'all' ); extract( $hdr_data );
	$surros = array( 'X-Proxy-Cache', 'X-Cache', 'Server: cloudflare-nginx', 'Server: Pagely Gateway/', 'CF-RAY: ', 'CF-Connecting-IP: ', 'CF-IPCountry: ', 'CF-Visitor: ', 'Via: 1.1 varnish', 'X-Varnish: ', 'X-Varnish-Cache: ', 'X-Sucuri-Cache', 'X-Sucuri-ClientIP: ', 'Incap-Client-IP: ', 'X-CDN: Incapsula', 'X-Amz-Cf-Id: ', 'AkamaiGHost', 'CloudFront', 'NetDNA-cache', 'Sucuri/Cloudproxy', 'accelerator', 'gateway', 'proxy', ); /* Response Headers to flag */
	if( !empty( $headers ) && is_array( $headers ) ) {
		foreach( $headers as $i => $hdr ) {
			$hdr = WPSS_Func::lower( $hdr );
			foreach( $surros as $i => $surro ) {
				$surro = WP_SpamShield::casetrans( 'lower', $surro );
				if( strpos( $hdr, $surro ) === 0 ) { $surrogate = TRUE; break 2; }
			}
		}
	}
	if( empty( $surrogate ) && WPSS_Compatibility::is_surrogate() ) { $surrogate = TRUE; }
	if( !empty( $status ) && ( '403' == $status || '404' == $status || '500' == $status ) ) { $blocked = TRUE; }
	if( TRUE === $blocked || TRUE === $surrogate || TRUE === $is_nginx ) { $GLOBALS['wpss_jit_compat_mode'] = $compat_mode = rs_wpss_modify_advanced( 'WPSS_COMPAT_MODE', 'FALSE', 'TRUE' ); }
	WP_SpamShield::update_option( array( 'surrogate' => $surrogate ) );
	$status = compact( 'blocked', 'compat_mode', 'surrogate' );
	return $status;
}

function rs_wpss_update_session_data( $spamshield_options = array(), $extra_data = NULL ) {
	/* $_SESSION['wpss_spamshield_options_'.WPSS_HASH] 	= $spamshield_options; */
	if( empty( $spamshield_options ) || is_admin() || !rs_wpss_is_session_active() || rs_wpss_is_doing_cron() ) { return FALSE; }
	global $wpss_session_data_updated;
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return NULL; }
	$wpss_session_data_updated						= FALSE;
	$_SESSION['wpss_version_'.WPSS_HASH]			= WPSS_VERSION;
	$_SESSION['wpss_site_url_'.WPSS_HASH_ALT]		= WPSS_SITE_URL;
	$_SESSION['wpss_plugin_url_'.WPSS_HASH_ALT]		= WPSS_PLUGIN_URL;
	$_SESSION['wpss_user_ip_current_'.WPSS_HASH]	= WP_SpamShield::get_ip_addr();
	$_SESSION['wpss_user_agent_current_'.WPSS_HASH]	= rs_wpss_get_user_agent();
	/* Initial Referrer - Where Visitor Entered Site // External Referrer --> Landing Page */
	$key_first_ref	= 'wpss_referer_init_'.WPSS_HASH;
	$current_ref	= rs_wpss_get_referrer();
	if( empty( $_SESSION[$key_first_ref] ) ) {
		if( !empty( $_COOKIE['JCS_INENREF'] ) && FALSE === strpos( $_COOKIE['JCS_INENREF'], WPSS_SERVER_NAME ) ) {
			$_SESSION[$key_first_ref] = $_COOKIE['JCS_INENREF'];
		} elseif( !empty( $current_ref ) && FALSE === strpos( $current_ref, WPSS_SERVER_NAME ) ) { $_SESSION[$key_first_ref] = $current_ref; }
	}
	return TRUE;
}

function rs_wpss_get_key_values( $ignore_cache = FALSE ) {
	/**
	 *  Get Key Values: Cookie, JS, jQuery
	 *  Default is dynamically generate keys tied to website and session.
	 *  Checks caching status and serves static keys for JS form fields if caching enabled.
	 *  Param $ignore_cache can be used to skip the cache check, for non-cached pages like registration form.
	 */
	global $wpss_session_id;
	if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
	/* Cookie key - dynamic */
	$wpss_ck_key_phrase		= 'wpss_ckkey_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_ck_val_phrase		= 'wpss_ckval_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_ck_key 			= rs_wpss_md5( $wpss_ck_key_phrase );
	$wpss_ck_val 			= rs_wpss_md5( $wpss_ck_val_phrase );
	/* JavaScript key - dynamic */
	$wpss_js_key_phrase		= 'wpss_jskey_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_js_val_phrase		= 'wpss_jsval_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_js_key 			= rs_wpss_md5( $wpss_js_key_phrase );
	$wpss_js_val 			= rs_wpss_md5( $wpss_js_val_phrase );
	/* jQuery key - dynamic */
	$wpss_jq_key_phrase		= 'wpss_jqkey_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_jq_val_phrase		= 'wpss_jqval_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id;
	$wpss_jq_key 			= rs_wpss_md5( $wpss_jq_key_phrase );
	$wpss_jq_val 			= rs_wpss_md5( $wpss_jq_val_phrase );
	/* JavaScript key - static */
	$cache_check_status		= 'NOT CHECKED';
	if( empty( $ignore_cache ) ) {
		$wpss_js_ke2_phrase	= 'wpss_jske2_'.WPSS_SERVER_NAME_NODOT.'_'.WPSS_REF2XJS.'_'.WPSS_JSONST.'_'.WPSS_VERSION.'_'.WPSS_WP_VERSION.'_'.PHP_VERSION;
		$wpss_js_va2_phrase	= 'wpss_jsva2_'.WPSS_SERVER_NAME_NODOT.'_'.WPSS_REF2XJS.'_'.WPSS_JSONST.'_'.WPSS_VERSION.'_'.WPSS_WP_VERSION.'_'.PHP_VERSION;
		global $wpss_cache_check; if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$cache_check_status	= $wpss_cache_check['cache_check_status'];
		if( $cache_check_status === 'ACTIVE' || TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) {
			$wpss_js_key 	= rs_wpss_md5( $wpss_js_ke2_phrase );
			$wpss_js_val 	= rs_wpss_md5( $wpss_js_va2_phrase );
		}
	}
	/* Store and return keys and values */
	$wpss_key_values = compact( 'wpss_ck_key', 'wpss_ck_val', 'wpss_js_key', 'wpss_js_val', 'wpss_jq_key', 'wpss_jq_val', 'cache_check_status' );
	return $wpss_key_values;
}

function rs_wpss_get_log_key( $update = TRUE ) {
	/**
	 *  Get Log Key
	 *  Default is dynamically generated keys tied to website, session ID, time, random number, admin IP
	 */
	$spamshield_options = WP_SpamShield::get_option();
	if( !empty( $spamshield_options['log_key'] ) && WP_SpamShield::preg_match( "~^[a-f0-9]{32}$~", $spamshield_options['log_key'] ) ) {
		$log_key = $spamshield_options['log_key'];
	} else {
		global $wpss_session_id;
		if( empty( $wpss_session_id ) ) { $wpss_session_id = @session_id(); }
		$mtimenow			= microtime( TRUE );
		$rando				= mt_rand( 1000, 9999 );
		if( rs_wpss_is_user_admin() ) {
			$log_ip			= WP_SpamShield::get_ip_addr();
		} else {
			$last_admin_ip	= get_option( 'spamshield_last_admin' );
			$log_ip			= !empty( $last_admin_ip ) ? $last_admin_ip : WPSS_SERVER_ADDR;
		}
		$log_key_phrase		= 'wpss_log_key_'.WPSS_SERVER_NAME_NODOT.'_'.$wpss_session_id.'_'.$mtimenow.'_'.$rando.'_'.$log_ip;
		$log_key 			= rs_wpss_md5( $log_key_phrase );
		if( TRUE === $update ) { $spamshield_options['log_key'] = $log_key; WP_SpamShield::update_option( $spamshield_options ); }
	}
	return $log_key;
}

function rs_wpss_get_log_session_data() {
	$noda 					= '[No Data]';
	$key_total_page_hits	= 'wpss_page_hits_js_'.WPSS_HASH;
	$key_last_ref			= 'wpss_jscripts_referer_last_'.WPSS_HASH;
	$key_pages_hist 		= 'wpss_jscripts_referers_history_'.WPSS_HASH;
	$key_hits_per_page		= 'wpss_jscripts_referers_history_count_'.WPSS_HASH;
	$key_ip_hist 			= 'wpss_jscripts_ip_history_'.WPSS_HASH;
	$key_init_ip			= 'wpss_user_ip_init_'.WPSS_HASH;
	$key_pt_hist 			= 'wpss_jscripts_pt_history_'.WPSS_HASH;
	$key_init_pt			= 'wpss_user_pt_init_'.WPSS_HASH;
	$key_init_mt			= 'wpss_time_init_'.WPSS_HASH;
	$key_init_dt			= 'wpss_timestamp_init_'.WPSS_HASH;
	$key_first_ref			= 'wpss_referer_init_'.WPSS_HASH;
	$key_auth_hist 			= 'wpss_author_history_'.WPSS_HASH;
	$key_comment_auth 		= 'comment_author_'.WPSS_HASH;
	$key_email_hist 		= 'wpss_author_email_history_'.WPSS_HASH;
	$key_comment_email		= 'comment_author_email_'.WPSS_HASH;
	$key_auth_url_hist 		= 'wpss_author_url_history_'.WPSS_HASH;
	$key_comment_url		= 'comment_author_url_'.WPSS_HASH;
	$key_comment_acc		= 'wpss_comments_accepted_'.WPSS_HASH;
	$key_comment_den		= 'wpss_comments_denied_'.WPSS_HASH;
	$key_comment_stat_curr	= 'wpss_comments_status_current_'.WPSS_HASH;
	$key_append_log_data	= 'wpss_append_log_data_'.WPSS_HASH;
	global $wpss_session_id,$wpss_session_name,$wpss_session_active;

	if( !isset( $wpss_session_active ) )	{ $wpss_session_active	= rs_wpss_is_session_active(); }
	if( !isset( $wpss_session_name ) )		{ $wpss_session_name	= ( !empty( $wpss_session_active ) ) ? @session_name()	: ''; }
	if( !isset( $wpss_session_id ) )		{ $wpss_session_id		= ( !empty( $wpss_session_active ) ) ? @session_id()	: ''; }

	if( !empty( $wpss_session_name ) && !empty( $_COOKIE[$wpss_session_name] ) ) {
		$wpss_session_ck		= $_COOKIE[$wpss_session_name];
		$wpss_session_verified	= ( rs_wpss_valid_sess_ck() ) ? '[Verified]' : '[Not Verified]';
	} else {
		$wpss_session_ck		= '[No Session Cookie]';
		$wpss_session_verified	= '[Not Verified]';
	}
	if( !empty( $_SESSION[$key_total_page_hits] ) ) { $wpss_page_hits = $_SESSION[$key_total_page_hits]; }
	else { $wpss_page_hits = $noda; }
	if( !empty( $_SESSION[$key_last_ref] ) ) { $wpss_last_page_hit = $_SESSION[$key_last_ref]; }
	else { $wpss_last_page_hit = $noda; }
	$wpss_pages_history = '';
	if( !empty( $_SESSION[$key_pages_hist] ) ) { $wpss_pages_history_data = $_SESSION[$key_pages_hist]; }
	else { $wpss_pages_history = $noda; }
	if( $wpss_pages_history !== $noda ) {
		$wpss_pages_history = WPSS_EOL;
		foreach( $wpss_pages_history_data as $page ) { $wpss_pages_history .= "['".$page."']".WPSS_EOL; }
	}
	$wpss_hits_per_page = '';
	if( !empty( $_SESSION[$key_hits_per_page] ) ) { $wpss_hits_per_page_data = $_SESSION[$key_hits_per_page]; }
	else { $wpss_hits_per_page = $noda; }
	if( $wpss_hits_per_page !== $noda ) {
		$wpss_hits_per_page = WPSS_EOL;
		foreach( $wpss_hits_per_page_data as $page => $hits ) { $wpss_hits_per_page .= "['".$hits."'] ['".$page."']".WPSS_EOL; }
	}
	if( !empty( $_SESSION[$key_init_ip] ) ) { $wpss_user_ip_init = $_SESSION[$key_init_ip]; } else { $wpss_user_ip_init = $noda; }
	if( !empty( $_SESSION[$key_ip_hist] ) ) { $wpss_ip_history = implode(', ', $_SESSION[$key_ip_hist]); } else { $wpss_ip_history = $noda; }
	if( !empty( $_SESSION[$key_init_pt] ) ) { $wpss_user_pt_init = $_SESSION[$key_init_pt]; } else { $wpss_user_pt_init = $noda; }
	if( !empty( $_SESSION[$key_pt_hist] ) ) { $wpss_pt_history = implode(', ', $_SESSION[$key_pt_hist]); } else { $wpss_pt_history = $noda; }
	if( !empty( $_SESSION[$key_init_mt] ) ) { $wpss_time_init = $_SESSION[$key_init_mt]; } else { $wpss_time_init = ''; }
	$ck_key_init_dt 	= 'NCS_INENTIM'; /* Initial Entry Time - PHP Cookie is backup to session var */
	$ck_key_init_dt_js 	= 'JCS_INENTIM'; /* Initial Entry Time - JS Cookie is backup to PHP Cookie */
	if( !empty( $_COOKIE[$ck_key_init_dt] ) ) { $wpss_ck_timestamp_init_int = $_COOKIE[$ck_key_init_dt]; }
	elseif( !empty( $_COOKIE[$ck_key_init_dt_js] ) ) { $wpss_ck_timestamp_init_int = round($_COOKIE[$ck_key_init_dt_js]/1000); }
	else { $wpss_ck_timestamp_init_int = ''; }
	if( !empty( $_SESSION[$key_init_dt] ) ) { $wpss_timestamp_init = $_SESSION[$key_init_dt]; }
	elseif( !empty( $wpss_ck_timestamp_init_int ) ) { $wpss_timestamp_init = $wpss_ck_timestamp_init_int; } else { $wpss_timestamp_init = ''; }
	if( !empty( $_SESSION[$key_first_ref] ) ) { $wpss_referer_init = $_SESSION[$key_first_ref]; } elseif( !empty( $_COOKIE['JCS_INENREF'] ) ) { $wpss_referer_init = $_SESSION[$key_first_ref] = $_COOKIE['JCS_INENREF']; } else { $wpss_referer_init = $noda; }
	$wpss_referer_init_js = rs_wpss_get_referrer( FALSE, TRUE, TRUE ); /* Initial referrer, aka Referring Site - Changed 1.7.9 */
	if( empty( $wpss_referer_init_js ) ) { $wpss_referer_init_js = $noda; }
	if( !empty( $_SESSION[$key_auth_hist] ) ) { $wpss_author_history = implode(', ', $_SESSION[$key_auth_hist]); }
	elseif( !empty( $_COOKIE[$key_comment_auth] ) ) { $wpss_author_history = $_COOKIE[$key_comment_auth]; }
	else { $wpss_author_history = $noda; }
	if( !empty( $_SESSION[$key_email_hist] ) ) { $wpss_author_email_history = implode(', ', $_SESSION[$key_email_hist]); }
	elseif( !empty( $_COOKIE[$key_comment_email] ) ) { $wpss_author_email_history = $_COOKIE[$key_comment_email]; }
	else { $wpss_author_email_history = $noda; }
	if( !empty( $_SESSION[$key_auth_url_hist] ) ) { $wpss_author_url_history = implode(', ', $_SESSION[$key_auth_url_hist]); }
	elseif( !empty( $_COOKIE[$key_comment_url] ) ) { $wpss_author_url_history = $_COOKIE[$key_comment_url]; }
	else { $wpss_author_url_history = $noda; }
	if( !empty( $_SESSION[$key_comment_acc] ) ) { $wpss_comments_accepted = $_SESSION[$key_comment_acc]; } else { $wpss_comments_accepted = $noda; }
	if( !empty( $_SESSION[$key_comment_den] ) ) { $wpss_comments_denied = $_SESSION[$key_comment_den]; } else { $wpss_comments_denied = $noda; }
	/* Current status */
	if( !empty( $_SESSION[$key_comment_stat_curr] ) ) { $wpss_comments_status_current = $_SESSION[$key_comment_stat_curr]; } else { $wpss_comments_status_current = $noda; }
	if( !empty( $_SESSION[$key_append_log_data] ) ) { $wpss_append_log_data = $_SESSION[$key_append_log_data]; } else { $wpss_append_log_data = $noda; }
	$wpss_log_session_data = compact( 'wpss_session_id', 'wpss_session_name', 'wpss_session_active', 'wpss_session_ck', 'wpss_session_verified', 'wpss_page_hits', 'wpss_last_page_hit', 'wpss_pages_history', 'wpss_hits_per_page', 'wpss_user_ip_init', 'wpss_ip_history', 'wpss_user_pt_init', 'wpss_pt_history', 'wpss_time_init', 'wpss_timestamp_init', 'wpss_referer_init', 'wpss_referer_init_js', 'wpss_author_history', 'wpss_author_email_history', 'wpss_author_url_history', 'wpss_comments_accepted', 'wpss_comments_denied', 'wpss_comments_status_current', 'wpss_append_log_data' );
	return $wpss_log_session_data;
}

function rs_wpss_log_data( $wpss_log_data_array, $wpss_log_data_errors, $wpss_log_entry_type = 'comment', $wpss_log_contact_form_data = NULL, $wpss_log_contact_form_id = NULL, $wpss_log_contact_form_mcid = NULL ) {
	/**
	 *  Example:
	 *  Comment:			rs_wpss_log_data( $commentdata, $wpss_error_code )
	 *  Contact Form:		rs_wpss_log_data( $cf_author_data, $wpss_error_code, 'contact form', $cf_msg, $cf_mid, $cf_mcid );
	 *  Registration:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'register' );
	 *  BuddyPress Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'bp-register' );
	 *  WooCommerce Reg:	rs_wpss_log_data( $register_author_data, $wpss_error_code, 'wc-register' );
	 *  s2Member Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 's2-register' );
	 *  WP-Members Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'wpm-register' );
	 *  Affiliates Reg:		rs_wpss_log_data( $register_author_data, $wpss_error_code, 'aff-register' );
	 *  Contact Form 7:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'contact form 7', $cf7_serial_post );
	 *  Gravity Forms:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'gravity forms', $gf_serial_post );
	 *  Miscellaneous Form:	rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'misc form', $msc_serial_post );
	 *  JetPack Form:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'jetpack form', $msc_serial_post );
	 *  Ninja Forms:		rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'ninja forms', $msc_serial_post );
	 *  Mailchimp Signup:	rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'mailchimp form', $msc_serial_post );
	 *  Guestbook:			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'guestbook form', $msc_serial_post );
	 *  TO DO: 				Add types for EPC and XML-RPC
	 */

	global $current_user,$wpss_active_plugins,$wpss_active_network_plugins,$wpss_cache_check,$wpss_geolocation;
	$wpss_log_session_data = rs_wpss_get_log_session_data(); extract( $wpss_log_session_data );

	$noda = '[No Data]'; $thick_line = str_repeat( '═', 100 ); $thin_line = str_repeat( '─', 100 );

	/* Timer - BEGIN*/
	$wpss_time_end					= microtime( TRUE );
	if( empty( $wpss_time_init ) && !empty( $wpss_timestamp_init ) ) { $wpss_time_init = $wpss_timestamp_init; }
	if( !empty( $wpss_time_init ) ) {
		$wpss_time_on_site			= rs_wpss_timer( $wpss_time_init, $wpss_time_end, TRUE, 2 );
	} else { $wpss_time_on_site 	= $noda; }
	if( !empty( $wpss_timestamp_init ) ) {
		$wpss_site_entry_time		= get_date_from_gmt( date( WPSS_DATE_FULL, $wpss_timestamp_init ), WPSS_DATE_LONG ); /* Added 1.7.3 */
	} else { $wpss_site_entry_time 	= $noda; }
	/* Timer - END */

	$wpss_php_memory_limit = @WP_SpamShield::wp_memory_used( ini_get( 'memory_limit' ) );
	$plugin_user_agent = 'WP-SpamShield/' . WPSS_VERSION . ' (WordPress/' . WPSS_WP_VERSION . ') PHP/' . PHP_VERSION . ' (' . $_SERVER['SERVER_SOFTWARE'] . ')';
	define( 'WPSS_PHP_MEM_LIMIT', $wpss_php_memory_limit ); unset( $wpss_php_memory_limit );

	rs_wpss_log_reset( NULL, FALSE, FALSE, TRUE ); /* Create log file if it doesn't exist */

	$wpss_log_key				= rs_wpss_get_log_key();
	$wpss_log_filnm				= ( WP_SpamShield::is_mdbug() ) ? 'temp-comments-log.txt' : 'temp-comments-log-'.$wpss_log_key.'.txt';
	$wpss_log_file				= WPSS_PLUGIN_DATA_PATH.'/'.$wpss_log_filnm;
	$wpss_log_max_filesize		= ( WP_SpamShield::is_mdbug() ) ? 20 * MB_IN_BYTES : 2 * MB_IN_BYTES; /* 2 MB */

	if( empty( $wpss_log_entry_type ) ) { $wpss_log_entry_type = 'comment'; }
	$wpss_log_entry_type_display 			= ( $wpss_log_entry_type === 'misc form' && WPSS_Utils::is_csp_report() ) ? 'CSP REPORT' : WPSS_Func::upper( $wpss_log_entry_type );
	$wpss_log_entry_type_display_len		= rs_wpss_strlen( $wpss_log_entry_type_display );
	$wpss_log_entry_type_display_len_begin	= $wpss_log_entry_type_display_len + 6;
	$wpss_log_entry_type_display_len_end	= $wpss_log_entry_type_display_len + 4;
	$wpss_log_rem_spaces_begin				= 92 - $wpss_log_entry_type_display_len_begin;	/* 100 - 8 - x */
	$wpss_log_rem_spaces_end				= 92 - $wpss_log_entry_type_display_len_end;	/* 100 - 8 - x */
	$wpss_log_entry_type_ucwords			= WP_SpamShield::casetrans( 'ucwords', $wpss_log_entry_type );
	$wpss_log_entry_type_ucwords_ref_disp	= preg_replace( "~\sform~i", "", $wpss_log_entry_type_ucwords );

	$wpss_display_name = $wpss_user_firstname = $wpss_user_lastname = $wpss_user_email = $wpss_user_url = $wpss_user_login = $wpss_user_id = $wpss_rsds = $bclm_off = $bclm_oc = '';
	$wpss_user_logged_in 		= FALSE;
	if( rs_wpss_is_user_logged_in() ) {
		$current_user			= wp_get_current_user();
		$wpss_display_name 		= $current_user->display_name;
		$wpss_user_firstname 	= $current_user->user_firstname;
		$wpss_user_lastname 	= $current_user->user_lastname;
		$wpss_user_email		= $current_user->user_email;
		$wpss_user_url			= $current_user->user_url;
		$wpss_user_login 		= $current_user->user_login;
		$wpss_user_id	 		= $current_user->ID;
		$wpss_user_logged_in	= TRUE;
	}

	$spamshield_options = WP_SpamShield::get_option();

	$wpss_active_plugins			= rs_wpss_get_active_plugins();
	$wpss_active_plugins_str		= implode( ', ', $wpss_active_plugins );
	if( is_multisite() ) {
		if( empty( $wpss_active_network_plugins ) ) { $wpss_active_network_plugins = rs_wpss_get_active_network_plugins(); }
		if( !empty( $wpss_active_network_plugins ) ) { $wpss_active_network_plugins_str = implode( ', ', $wpss_active_network_plugins ); }
	}

	$wpss_php_uname 				= function_exists( 'php_uname' ) ? @php_uname() : PHP_OS .' '. @gethostname();
	$timenow						= time();
	$comment_logging 				= $spamshield_options['comment_logging'];
	$comment_logging_all 			= $spamshield_options['comment_logging_all'];
	$comment_logging_start_date		= $spamshield_options['comment_logging_start_date'] = ( !empty( $spamshield_options['comment_logging_start_date'] ) && $spamshield_options['comment_logging_start_date'] > 1451606400 ) ? $spamshield_options['comment_logging_start_date'] : $timenow;
	$wpss_javascript_page_referrer	= !empty( $wpss_log_data_array['javascript_page_referrer'] ) ? $wpss_log_data_array['javascript_page_referrer'] : '';
	$wpss_jsonst					= !empty( $wpss_log_data_array['jsonst'] ) ? $wpss_log_data_array['jsonst'] : '';

	/**
	 *  Display local time in logs. Won't match other time logs, because those need to be UTC.
	 *  MINUTE_IN_SECONDS, HOUR_IN_SECONDS, DAY_IN_SECONDS , WEEK_IN_SECONDS, MONTH_IN_SECONDS, YEAR_IN_SECONDS
	 */
	$timenow_display			= current_time( 'timestamp', 0 );
	$reset_interval_rsds		= 10 * YEAR_IN_SECONDS;
	$reset_interval_default		= ( WP_SpamShield::is_mdbug() ) ? $reset_interval_rsds : WEEK_IN_SECONDS; /* Default is one week */
	$reset_interval_override	= $reset_interval_default; /* Change for TESTING only, and it will override defaults */
	$reset_interval				= ( $reset_interval_override !== $reset_interval_default ) ? $reset_interval_override : $reset_interval_default;
	$comment_logging_end_date	= $spamshield_options['comment_logging_end_date'] = ( !empty( $spamshield_options['comment_logging_end_date'] ) && $spamshield_options['comment_logging_end_date'] > 1451606400 ) ? $spamshield_options['comment_logging_end_date'] : $comment_logging_start_date + $reset_interval;
	/**
	 *  Automatically turns off Blocked Spam Logging Mode if 1 of 2 conditions met:
	 *  	1) Over X amount of time since starting
	 *  	2) Filesize exceeds max
	 */
	if( $timenow >= $comment_logging_end_date ) { $bclm_off = TRUE; $bclm_oc = 'T'; }
	elseif( @file_exists( $wpss_log_file ) && filesize( $wpss_log_file ) >= $wpss_log_max_filesize ) { $bclm_off = TRUE; $bclm_oc = 'FS'; }
	if( !empty( $bclm_off ) ) { /* Turns Blocked Spam Logging Mode off and clears vaues */
		WP_SpamShield::update_option( array( 'comment_logging' => 0, 'comment_logging_all' => 0, 'comment_logging_start_date' => 0, 'comment_logging_end_date' => 0, ) );
		if( !empty( $wpss_rsds ) ) { @WP_SpamShield::append_log_data( NULL, NULL, 'Blocked Spam Logging Mode has been disabled. '.'['.$bclm_oc.']' ); }
	} else {
		/* LOG DATA */
		WP_SpamShield::update_option( array( 'comment_logging' => $comment_logging, 'comment_logging_all' => $comment_logging_all, 'comment_logging_start_date' => $comment_logging_start_date, 'comment_logging_end_date' => $comment_logging_end_date, ) );
		if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		$wpss_log_datum			= date( WPSS_DATE_LONG, $timenow_display );
		$wpss_is_ajax			= rs_wpss_is_ajax_request();
		$wpss_is_comment		= rs_wpss_is_comment_request();
		$wpss_compat_on			= ( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) ? 'ON' : 'OFF';
		$wpss_cache_on			= ( $wpss_cache_check['cache_check_status'] === 'ACTIVE' ) ? 'ON' : 'OFF';
		$wpss_log_data			= '';
		$wpss_log_data			.= $thick_line.WPSS_EOL;
		$wpss_log_data			.= '────┬─'.str_repeat('─',$wpss_log_entry_type_display_len_begin).'─┬'.str_repeat('─',$wpss_log_rem_spaces_begin).WPSS_EOL;
		$wpss_log_data		   	.= '    │ '.$wpss_log_entry_type_display.' BEGIN'.' │'.WPSS_EOL;
		$wpss_log_data			.= '────┴─'.str_repeat('─',$wpss_log_entry_type_display_len_begin).'─┴'.str_repeat('─',$wpss_log_rem_spaces_begin).WPSS_EOL;

		$submitter_ip_address = WP_SpamShield::get_ip_addr();
		$submitter_ip_address_short_l = trim( substr( $submitter_ip_address, 0, 6) );
		$submitter_ip_address_short_r = trim( substr( $submitter_ip_address, -6, 2) );
		$submitter_ip_address_obfuscated = $submitter_ip_address_short_l.'****'.$submitter_ip_address_short_r.'.***';

		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */

		if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
		$port = ( !empty( $_SERVER['REMOTE_PORT'] ) ) ? $_SERVER['REMOTE_PORT'] : '';
		$browser_plugins	= ( !empty( $_COOKIE['_wpss_p_'] ) ) ? WPSS_Func::b64de( preg_replace( "~^N\:\d+\ |\ ~i", '', $_COOKIE['_wpss_p_'] ) ) : ''; /* Mostly Chrome */
		$browser_history_n	= ( !empty( $_COOKIE['_wpss_h_'] ) ) ? WP_SpamShield::sanitize_string( $_COOKIE['_wpss_h_'] ) : ''; /* Number of Browser History Entries */

		$wpss_spamshield_count = rs_wpss_number_format( rs_wpss_count() );
		if( $wpss_log_entry_type === 'comment' || $wpss_log_entry_type === 'contact form' ) { $body_content_length = rs_wpss_number_format( $wpss_log_data_array['body_content_len'] ); } else { $body_content_length = ''; }
		if( $wpss_log_entry_type === 'comment' ) {
			/* Comment Post Info */
			$comment_author_email = $wpss_log_data_array['comment_author_email']; $comment_types_allowed = '';
			if( !empty( $wpss_log_data_array['comment_post_comments_open'] ) ) {
				$comment_post_comments_open = 'Open'; $comment_types_allowed .= 'comments';
			} else { $comment_post_comments_open = 'Closed'; }
			if( !empty( $wpss_log_data_array['comment_post_pings_open'] ) ) {
				$comment_post_pings_open = 'Open';
				if( !empty( $comment_types_allowed ) ) { $comment_types_allowed .= ','; }
				$comment_types_allowed .= 'pingbacks,trackbacks';
			} else { $comment_post_pings_open = 'Closed'; }
			if( empty( $comment_types_allowed ) ) { $comment_types_allowed = 'none, comments closed'; }
			if( empty( $wpss_log_data_array['comment_post_type'] ) ) { $wpss_log_data_array['comment_post_type'] = 'Post'; }
			$comment_post_type	= WP_SpamShield::casetrans( 'ucwords', $wpss_log_data_array['comment_post_type'] );
			$comment_type		= ( !empty( $wpss_log_data_array['comment_type'] ) ) ? $wpss_log_data_array['comment_type'] : 'comment';
			/* Comment Data */
			$wpss_log_data		.= $thin_line.WPSS_EOL;
			$log_data_comment	=
				array(
					'Date/Time'							=> $wpss_log_datum,
					'Comment Post ID'					=> $wpss_log_data_array['comment_post_ID'],
					'Comment Post Title'				=> $wpss_log_data_array['comment_post_title'],
					'Comment Post URL'					=> $wpss_log_data_array['comment_post_url'],
					'Comment Post Type'					=> $comment_post_type,
					$comment_post_type.' Allows Types'	=> $comment_types_allowed,
					'Comment Type'						=> $comment_type.'%%{END_GROUP}%%',
					'Comment Author'					=> $wpss_log_data_array['comment_author'],
					'Comment Author Email'				=> $comment_author_email,
					'Comment Author URL'				=> $wpss_log_data_array['comment_author_url'],
					'Comment Content'					=> '%%{MULTILINE}%%'."['comment_content_begin']".WPSS_EOL.$wpss_log_data_array['comment_content'].WPSS_EOL."['comment_content_end']".WPSS_EOL.$thin_line,
					'WPSSCID'							=> $wpss_log_data_array['comment_wpss_cid'],
					'WPSSCCID'							=> $wpss_log_data_array['comment_wpss_ccid'],
				);
			$wpss_log_data = WP_SpamShield::build_log_data( $log_data_comment, $wpss_log_data );
		} elseif( strpos( $wpss_log_entry_type, 'register' ) !== FALSE ) {
			/* Registration Data */
			$wpss_log_data		.= $thin_line.WPSS_EOL;
			$log_data_register	=
				array(
					'Date/Time'		=> $wpss_log_datum,
					'User ID'		=> ( !empty( $wpss_log_data_array['ID'] ) ) ? $wpss_log_data_array['ID'] : '[None]',
					'User Login'	=> $wpss_log_data_array['user_login'],
					'Display Name'	=> $wpss_log_data_array['display_name'],
					'First Name'	=> $wpss_log_data_array['user_firstname'],
					'Last Name'		=> $wpss_log_data_array['user_lastname'],
					'User Email'	=> $wpss_log_data_array['user_email'],
					'User URL'		=> ( !empty( $wpss_log_data_array['user_url'] ) ) ? $wpss_log_data_array['user_url'] : '[None]',
				);
			$wpss_log_data = WP_SpamShield::build_log_data( $log_data_register, $wpss_log_data );
		} elseif( $wpss_log_entry_type === 'contact form' ) {
			$wpss_log_cf_subject = !empty( $_POST['wpss_contact_subject'] ) ? rs_wpss_prep_cf_email( stripslashes( sanitize_text_field( $_POST['wpss_contact_subject'] ) ) ) : '';
			/* Contact Form Data */
			$wpss_log_data		.= $thin_line.WPSS_EOL;
			$log_data_contact	=
				array(
					'Date/Time'			=> $wpss_log_datum.'%%{END_GROUP}%%',
					'Subject'			=> $wpss_log_cf_subject.'%%{END_GROUP}%%',
					'Contact Content'	=> '%%{MULTILINE}%%'."['contact_content_begin']".WPSS_EOL.$wpss_log_contact_form_data.WPSS_EOL."['contact_content_end']".WPSS_EOL.$thin_line,
					'WPSSMID'			=> $wpss_log_contact_form_id,
					'WPSSMCID'			=> $wpss_log_contact_form_mcid,
				);
			$wpss_log_data = WP_SpamShield::build_log_data( $log_data_contact, $wpss_log_data );
		} elseif( strpos( $wpss_log_entry_type, 'form' ) !== FALSE ) {
			$form_post_data_arr		= json_decode( $wpss_log_contact_form_data, TRUE );
			$form_post_data_disp	= '';
			foreach( $form_post_data_arr as $k => $v ) {
				if( is_array($v) ) { $v = implode( '|', $v ); }
				$form_post_data_disp .= $k.': '.trim(stripslashes($v)).WPSS_EOL;
			}
			/* Misc Form Data */
			$wpss_log_data			.= $thin_line.WPSS_EOL;
			$log_data_misc_form		=
				array(
					'Date/Time'			=> $wpss_log_datum.'%%{END_GROUP}%%',
					'Misc Form Content'	=> '%%{MULTILINE}%%'."['misc_form_content_begin']".WPSS_EOL.$form_post_data_disp.WPSS_EOL."['misc_form_content_end']",
				);
			$wpss_log_data = WP_SpamShield::build_log_data( $log_data_misc_form, $wpss_log_data );
		}
		$wpss_sessions_enabled		= ( isset( $_SESSION ) ) ? 'Enabled' : 'Disabled';
		$wpss_session_status		= ( rs_wpss_is_session_active() ) ? 'ACTIVE' : 'INACTIVE';
		$wpss_server_content_type	= ( !empty( $_SERVER['CONTENT_TYPE'] ) ) ? $_SERVER['CONTENT_TYPE'] : '';
		$wpss_http_status			= ( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG && $wpss_log_entry_type === 'misc form' ) ? WP_SpamShield::get_http_status( WPSS_THIS_URL ) : '';

		/* Sanitized versions for output */
		$wpss_http_accept			= rs_wpss_get_http_accept();
		$wpss_http_accept_language 	= rs_wpss_get_http_accept( FALSE, FALSE, TRUE );
		$wpss_http_accept_encoding 	= rs_wpss_get_http_accept( FALSE, FALSE, FALSE, TRUE );
		$server_x_req_w 			= rs_wpss_get_server_x_req_w();
		$wpss_http_user_agent 		= rs_wpss_get_user_agent();
		$wpss_http_browser 			= rs_wpss_get_browser();
		$wpss_http_referer			= rs_wpss_get_referrer(); /* Not original ref - Comment Processor Referrer */

		/* DEBUG ONLY */
		$wpss_log_data_serial_session	= ( !empty( $_SESSION ) ) ? @WPSS_PHP::json_encode( $_SESSION ) : '';
		$wpss_log_data_serial_cookie	= ( !empty( $_COOKIE ) ) ? @WPSS_PHP::json_encode( $_COOKIE ) : '';
		$wpss_log_data_serial_get		= ( !empty( $_GET ) || !empty( $query_str ) ) ? @WPSS_PHP::json_encode( $_GET ) : '';
		$wpss_log_data_serial_post		= '';
		if( !empty( $_POST ) ) {
			$wpss_log_data_post_raw		= $_POST;
			switch ( $wpss_log_entry_type ) {
				case 'comment'		: unset( $wpss_log_data_post_raw['comment'] ); break;
				case 'contact form'	: unset( $wpss_log_data_post_raw['wpss_contact_message'] ); break;
			}
			$wpss_log_data_serial_post 	= @WPSS_PHP::json_encode($wpss_log_data_post_raw);
		}
		$wpss_server_http_connection	= ( !empty( $_SERVER['HTTP_CONNECTION'] ) ) ? $_SERVER['HTTP_CONNECTION'] : '';
		if( !empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) )		{ $wpss_user_spamshield_count = $_SESSION['user_spamshield_count_'.WPSS_HASH]; }		else { $wpss_user_spamshield_count = 0; }
		if( !empty( $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] ) )	{ $wpss_jsck_spamshield_count = $_SESSION['user_spamshield_count_jsck_'.WPSS_HASH]; }	else { $wpss_jsck_spamshield_count = 0; }
		if( !empty( $_SESSION['user_spamshield_count_algo_'.WPSS_HASH] ) )	{ $wpss_algo_spamshield_count = $_SESSION['user_spamshield_count_algo_'.WPSS_HASH]; }	else { $wpss_algo_spamshield_count = 0; }

		$wpss_log_data_errors_count	= ( 0 === strpos( $wpss_log_data_errors, 'No Error' ) ) ? 0 : rs_wpss_count_words( $wpss_log_data_errors );
		$wpss_log_data_errors		= ( empty( $wpss_log_data_errors ) ) ? 'No Error' : $wpss_log_data_errors;

		/* Common Data */
		$wpss_log_data		.= $thin_line.WPSS_EOL;
		$log_data_common	=
			array(
				'User ID'					=> '%%{NO_OUTPUT_EMPTY}%%'. ( ( !empty( $wpss_user_id ) && FALSE !== $wpss_user_logged_in ) ? $wpss_user_id : '' ),
				'User-Agent'				=> $wpss_http_user_agent,
				'Browser'					=> ( !empty( $wpss_http_browser ) ) ? $wpss_http_browser : 'Not Detected',
				'Location'					=> '%%{NO_OUTPUT_EMPTY}%%'. ( ( !empty( $wpss_geolocation ) ) ? $wpss_geolocation : '' ),
				'IP Address'				=> $ip."'] ['http://ipaddressdata.com/".$ip,
				'Port'						=> $port,
				'Reverse DNS'				=> $reverse_dns,
				'Forward DNS'				=> $forward_dns,
				'FCrDNS Verified'			=> $fcrdns,
				'Proxy Info'				=> $ip_proxy,
				'Proxy Data'				=> $ip_proxy_data,
				'Proxy Status'				=> $proxy_status,
				'HTTP_VIA'					=> ( !empty( $ip_proxy_via ) ) ? $ip_proxy_via : '[None]',
				'HTTP_X_FORWARDED_FOR'		=> ( !empty( $masked_ip ) ) ? $masked_ip : '[None]',
				'HTTP_X_FORWARDED'			=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_x_forwarded,
				'HTTP_FORWARDED_FOR'		=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_forwarded_for,
				'HTTP_FORWARDED'			=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_forwarded,
				'HTTP_X_REAL_IP'			=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_x_real_ip,
				'HTTP_X_SUCURI_CLIENTIP'	=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_x_sucuri_clientip,
				'HTTP_CF_CONNECTING_IP'		=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_cf_connecting_ip,
				'HTTP_INCAP_CLIENT_IP'		=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_incap_client_ip,
				'HTTP_CLIENT_IP'			=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$http_client_ip,
				'SERVER_PROTOCOL'			=> $_SERVER['SERVER_PROTOCOL'],
				'HTTP_ACCEPT'				=> $wpss_http_accept,
				'HTTP_ACCEPT_LANGUAGE'		=> $wpss_http_accept_language,
				'HTTP_ACCEPT_ENCODING'		=> $wpss_http_accept_encoding,
				'CONTENT_TYPE'				=> $wpss_server_content_type,
				'HTTP_X_REQUESTED_WITH'		=> $server_x_req_w,
				'IS_AJAX'					=> $wpss_is_ajax,
				'IS_COMMENT'				=> $wpss_is_comment,
				'HTTP_X_REQUESTED_WITH'		=> $server_x_req_w,
				'HTTP STATUS'				=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'.$wpss_http_status,
				'URL'						=> WPSS_THIS_URL,
				'Form Processor Ref'		=> ( !empty( $wpss_http_referer ) ) ? $wpss_http_referer : '[None]',
				'JS Page Ref'				=> ( !empty( $wpss_javascript_page_referrer ) ) ? $wpss_javascript_page_referrer : '[None]',
				'JSONST'					=> ( ( !empty( $wpss_jsonst ) ) ? $wpss_jsonst : '[None]' ) .'%%{END_GROUP}%%',
				'PHP Session Status'		=> '%%{DEBUG_ONLY}%%'.$wpss_session_status,
				'PHP Session ID'			=> '%%{DEBUG_ONLY}%%'.$wpss_session_id,
				'PHP Session Cookie'		=> '%%{DEBUG_ONLY}%%'.$wpss_session_ck,
				'Sess ID/CK Match'			=> '%%{DEBUG_ONLY}%%'.$wpss_session_verified,
				'Page Hits'					=> '%%{DEBUG_ONLY}%%'.$wpss_page_hits,
				'Last Page Hit'				=> '%%{DEBUG_ONLY}%%'.$wpss_last_page_hit,
				'Hits Per Page'				=> '%%{DEBUG_ONLY}%%'.'%%{MULTILINE}%%'."['hits_per_page_begin']".$wpss_hits_per_page."['hits_per_page_end']",
				'Original IP'				=> '%%{DEBUG_ONLY}%%'.$wpss_user_ip_init,
				'IP History'				=> '%%{DEBUG_ONLY}%%'.$wpss_ip_history,
				'Port History'				=> '%%{DEBUG_ONLY}%%'. str_replace( '[No Data], ', '', $wpss_pt_history ) .', '.$port,
				'Time on Site'				=> '%%{DEBUG_ONLY}%%'.$wpss_time_on_site,
				'Site Entry Time'			=> '%%{DEBUG_ONLY}%%'.$wpss_site_entry_time,
				'Landing Page'				=> '%%{DEBUG_ONLY}%%'.$wpss_referer_init,		/* TO DO: Re-work this */
				'Original Referrer'			=> '%%{DEBUG_ONLY}%%'.$wpss_referer_init_js,
				'Clicky Referrer'			=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'. ( ( !empty( $_COOKIE['_referrer_og'] ) ) ? $_COOKIE['_referrer_og'] : '' ),
				'JCS_INENREF Referrer'		=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'. ( ( !empty( $_COOKIE['JCS_INENREF'] ) ) ? $_COOKIE['JCS_INENREF'] : '' ),
				'Author History'			=> '%%{DEBUG_ONLY}%%'.$wpss_author_history,
				'Email History'				=> '%%{DEBUG_ONLY}%%'.$wpss_author_email_history,
				'URL History'				=> '%%{DEBUG_ONLY}%%'.$wpss_author_url_history,
				'Entries Accepted'			=> '%%{DEBUG_ONLY}%%'.$wpss_comments_accepted,
				'Entries Denied'			=> '%%{DEBUG_ONLY}%%'.$wpss_comments_denied,
				'Spam Count'				=> '%%{DEBUG_ONLY}%%'.$wpss_spamshield_count,
				'User Spam Count'			=> '%%{DEBUG_ONLY}%%'.$wpss_user_spamshield_count,
				'JSCK Spam Count'			=> '%%{DEBUG_ONLY}%%'.$wpss_jsck_spamshield_count,
				'ALGO Spam Count'			=> '%%{DEBUG_ONLY}%%'.$wpss_algo_spamshield_count,
				'Current Status'			=> '%%{DEBUG_ONLY}%%'.$wpss_comments_status_current,
				'REQUEST_METHOD'			=> '%%{DEBUG_ONLY}%%'.WPSS_REQUEST_METHOD,
				'HTTP_CONNECTION'			=> '%%{DEBUG_ONLY}%%'.$wpss_server_http_connection,
				'Content Length'			=> '%%{DEBUG_ONLY}%%'.'%%{NO_OUTPUT_EMPTY}%%'. ( ( $wpss_log_entry_type === 'comment' || $wpss_log_entry_type === 'contact form' ) ? $body_content_length : '' ),
				'$_COOKIE Data'				=> '%%{DEBUG_ONLY}%%'.$wpss_log_data_serial_cookie,
				'$_GET Data'				=> '%%{DEBUG_ONLY}%%'.$wpss_log_data_serial_get,
				'MOD $_POST Data'			=> '%%{DEBUG_ONLY}%%'.$wpss_log_data_serial_post,
				'Browser Plugins'			=> '%%{DEBUG_ONLY}%%'.$browser_plugins,
				'Browser History'			=> '%%{DEBUG_ONLY}%%'.$browser_history_n,
				'Mem Used'					=> '%%{DEBUG_ONLY}%%'. WP_SpamShield::wp_memory_used(),
				'Extra Data'				=> '%%{DEBUG_ONLY}%%'.$wpss_append_log_data.'%%{END_GROUP}%%',
			);
		$wpss_log_data = WP_SpamShield::build_log_data( $log_data_common, $wpss_log_data );	
		if( $wpss_log_entry_type === 'comment' ) {
			/* Comment Data */
			if( empty( $wpss_log_data_array['total_time_jsck_filter'] ) ) {
				$wpss_total_time_jsck_filter 		= 0;
			} else {
				$wpss_total_time_jsck_filter 		= $wpss_log_data_array['total_time_jsck_filter'];
			}
			$wpss_total_time_jsck_filter_disp 		= rs_wpss_number_format( $wpss_total_time_jsck_filter, 6 );
			if( empty( $wpss_log_data_array['total_time_content_filter'] ) ) {
				$wpss_total_time_content_filter 	= 0;
			} else {
				$wpss_total_time_content_filter 	= $wpss_log_data_array['total_time_content_filter'];
			}
			$wpss_total_time_content_filter_disp 	= rs_wpss_number_format( $wpss_total_time_content_filter, 6 );
			$wpss_start_time_comment_processing 	= $wpss_log_data_array['start_time_comment_processing'];
			/* Timer End - Comment Processing */
			$wpss_end_time_comment_processing 		= microtime( TRUE );
			$wpss_total_time_wpss_processing 		= ( $wpss_total_time_jsck_filter + $wpss_total_time_content_filter );
			$wpss_total_time_wpss_processing_disp	= rs_wpss_number_format( $wpss_total_time_wpss_processing, 6 );
			$wpss_total_time_comment_processing 	= rs_wpss_timer( $wpss_start_time_comment_processing, $wpss_end_time_comment_processing, FALSE, 6, TRUE );
			$wpss_total_time_comment_proc_disp		= rs_wpss_number_format( $wpss_total_time_comment_processing, 6 );
			$wpss_total_time_wp_processing			= ( $wpss_total_time_comment_processing - $wpss_total_time_wpss_processing );
			$wpss_total_time_wp_processing_disp		= rs_wpss_number_format( $wpss_total_time_wp_processing, 6 );
			if( !empty( $wpss_total_time_jsck_filter_disp ) || !empty( $wpss_total_time_content_filter_disp ) || !empty( $wpss_total_time_wpss_processing_disp ) ) {
				$wpss_log_data .= "JS/C Processing Time:   ['".$wpss_total_time_jsck_filter_disp." seconds'] Time for JS/Cookies Layer to test for spam".WPSS_EOL;
				$wpss_log_data .= "Algo Processing Time:   ['".$wpss_total_time_content_filter_disp." seconds'] Time for Algorithmic Layer to test for spam".WPSS_EOL;
				$wpss_log_data .= "WPSS Processing Time:   ['".$wpss_total_time_wpss_processing_disp." seconds'] Total time for WP-SpamShield to test for spam".WPSS_EOL;
			}
			/* DEBUG Data - Begin */
			if( WP_SpamShield::is_debug() ) {
				$wpss_total_time_part_1 			= ( isset( $wpss_log_data_array['total_time_part_1'] ) ) ? $wpss_log_data_array['total_time_part_1'] : 0;
				$wpss_total_time_part_1_disp		= rs_wpss_number_format( $wpss_total_time_part_1, 6 );
				$wpss_proc_data						= get_option( 'spamshield_procdat' );
				if( empty( $wpss_proc_data ) || !isset( $wpss_proc_data['total_wpss_time'] ) || !isset( $wpss_proc_data['total_comment_proc_time'] ) ) {
					$wpss_proc_data					= array( 'total_tracked' => 0, 'total_wpss_time' => 0, 'avg_wpss_proc_time' => 0, 'total_comment_proc_time' => 0, 'avg_comment_proc_time' => 0, 'total_wpss_avg_tracked' => 0, 'total_avg_wpss_proc_time' => 0, 'avg2_wpss_proc_time' => 0 );
				}
				if( !isset( $wpss_proc_data['total_wpss_avg_tracked'] ) ) { $wpss_proc_data['total_wpss_avg_tracked'] = 0; }
				if( !isset( $wpss_proc_data['total_avg_wpss_proc_time'] ) ) { $wpss_proc_data['total_avg_wpss_proc_time'] = 0; }
				if( !isset( $wpss_proc_data['avg2_wpss_proc_time'] ) ) { $wpss_proc_data['avg2_wpss_proc_time'] = 0; }
				$wpss_proc_data_total_tracked 				= ( $wpss_proc_data['total_tracked'] + 1 );
				$wpss_proc_data_total_wpss_time 			= ( $wpss_proc_data['total_wpss_time'] + $wpss_total_time_wpss_processing );
				$wpss_proc_data_avg_wpss_proc_time 			= ( $wpss_proc_data_total_wpss_time / $wpss_proc_data_total_tracked );
				$wpss_proc_data_total_comment_proc_time 	= ( $wpss_proc_data['total_comment_proc_time'] + $wpss_total_time_comment_processing );
				$wpss_proc_data_avg_comment_proc_time 		= ( $wpss_proc_data_total_comment_proc_time / $wpss_proc_data_total_tracked );
				$wpss_proc_data_total_wpss_avg_tracked 		= ( $wpss_proc_data['total_wpss_avg_tracked'] + 1 );
				$wpss_proc_data_total_avg_wpss_proc_time	= ( $wpss_proc_data['total_avg_wpss_proc_time'] + $wpss_proc_data_avg_wpss_proc_time );
				$wpss_proc_data_avg2_wpss_proc_time 		= ( $wpss_proc_data_total_avg_wpss_proc_time / $wpss_proc_data_total_wpss_avg_tracked );
				$wpss_proc_data =
					array(
						'total_tracked' 			=> $wpss_proc_data_total_tracked,
						'total_wpss_time' 			=> $wpss_proc_data_total_wpss_time,
						'avg_wpss_proc_time' 		=> $wpss_proc_data_avg_wpss_proc_time,
						'total_comment_proc_time' 	=> $wpss_proc_data_total_comment_proc_time,
						'avg_comment_proc_time' 	=> $wpss_proc_data_avg_comment_proc_time,
						'total_wpss_avg_tracked' 	=> $wpss_proc_data_total_wpss_avg_tracked,
						'total_avg_wpss_proc_time' 	=> $wpss_proc_data_total_avg_wpss_proc_time,
						'avg2_wpss_proc_time' 		=> $wpss_proc_data_avg2_wpss_proc_time,
					);
				update_option( 'spamshield_procdat', $wpss_proc_data );

				$wpss_proc_data_avg_wpss_proc_time_disp 	= rs_wpss_number_format( $wpss_proc_data_avg_wpss_proc_time, 6 );
				$wpss_proc_data_avg2_wpss_proc_time_disp 	= rs_wpss_number_format( $wpss_proc_data_avg2_wpss_proc_time, 6 );
				$wpss_proc_data_avg_comment_proc_time_disp 	= rs_wpss_number_format( $wpss_proc_data_avg_comment_proc_time, 6 );
				$wpss_log_data .= "WP Processing Time:     ['".$wpss_total_time_wp_processing_disp." seconds'] Time for other WordPress processes".WPSS_EOL;
				$wpss_log_data .= "Total Processing Time:  ['".$wpss_total_time_comment_proc_disp." seconds'] Total time for WordPress to process comment".WPSS_EOL;
				$wpss_log_data .= "Avg WPSS Proc Time:     ['".$wpss_proc_data_avg_wpss_proc_time_disp." seconds'] Average total time for WP-SpamShield to test for spam".WPSS_EOL;
				$wpss_log_data .= "FAvg WPSS Proc Time:    ['".$wpss_proc_data_avg2_wpss_proc_time_disp." seconds'] Fuzzy Average total WPSS time".WPSS_EOL;
				$wpss_log_data .= "Avg Total Proc Time:    ['".$wpss_proc_data_avg_comment_proc_time_disp." seconds'] Average total time for WordPress to process comments".WPSS_EOL;
			}
			/* DEBUG Data - End */
			$wpss_log_data .= $thin_line.WPSS_EOL;
		}

		/* Common Data */
		$wpss_log_data .= "Failed Tests:           ['".$wpss_log_data_errors_count."']".WPSS_EOL;
		$wpss_log_data .= "Failed Test Codes:      ['".$wpss_log_data_errors."']".WPSS_EOL;
		$wpss_log_data .= "Spam Count:             ['".$wpss_spamshield_count."']".WPSS_EOL;
		$wpss_log_data .= $thin_line.WPSS_EOL;
		$wpss_log_data .= "Compatibility Mode:     ['".$wpss_compat_on."']".WPSS_EOL;
		$wpss_log_data .= "Caching:                ['".$wpss_cache_on."']".WPSS_EOL;
		$wpss_log_data .= "Debugging Data:         ['PHP MemLimit: ".WPSS_PHP_MEM_LIMIT."; WP MemLimit: ".WP_MEMORY_LIMIT."; Sessions: ".$wpss_sessions_enabled."']".WPSS_EOL;
		$wpss_log_data .= "Site Server Name:       ['".WPSS_SERVER_NAME."']".WPSS_EOL;
		$wpss_log_data .= "Site Server IP:         ['".WPSS_SERVER_ADDR."']".WPSS_EOL;
		$wpss_log_data .= $thin_line.WPSS_EOL;
		$wpss_log_data .= "Active Plugins:         ['".$wpss_active_plugins_str."']".WPSS_EOL;
		/* Multisite */
		if( is_multisite() && !empty( $wpss_active_network_plugins ) ) {
			$wpss_log_data .= "Active Net Plugins:     ['".$wpss_active_network_plugins_str."']".WPSS_EOL;
		}

		$wpss_log_data .= $thin_line.WPSS_EOL;
		$wpss_log_data .= $plugin_user_agent.WPSS_EOL;
		$wpss_log_data .= $wpss_php_uname.WPSS_EOL;
		$wpss_log_data .= '────┬─'.str_repeat('─',$wpss_log_entry_type_display_len_end).'─┬'.str_repeat('─',$wpss_log_rem_spaces_end).WPSS_EOL;
		$wpss_log_data .= '    │ '.$wpss_log_entry_type_display.' END'.' │'.WPSS_EOL;
		$wpss_log_data .= '────┴─'.str_repeat('─',$wpss_log_entry_type_display_len_end).'─┴'.str_repeat('─',$wpss_log_rem_spaces_end).WPSS_EOL;
		$wpss_log_data .= $thick_line.WPSS_EOL;
		$wpss_log_data .= WPSS_EOL.WPSS_EOL;

		$wpss_log_data = WP_SpamShield::filter_null( $wpss_log_data );

		@file_put_contents( $wpss_log_file, $wpss_log_data, FILE_APPEND | LOCK_EX );
	}
}

function rs_wpss_comment_form_append() {
	if( rs_wpss_is_admin_sproc() ) { return; }
	$spamshield_options		= WP_SpamShield::get_option();
	$promote_plugin_link 	= !empty( $spamshield_options['promote_plugin_link'] ) ? 1 : 0;
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || WPSS_Compatibility::comment_form() ) {
		$wpss_key_values 	= rs_wpss_get_key_values();
		$wpss_js_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_js_val 		= $wpss_key_values['wpss_js_val'];
		global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
		if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize' ); }
		if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
		echo WPSS_EOL.$ao_noop_open.'<script type=\'text/javascript\'>'.WPSS_EOL.'/* <![CDATA[ */'.WPSS_EOL.WPSS_REF2XJS.'=escape(document[\'referrer\']);'.WPSS_EOL.'hf1N=\''.$wpss_js_key.'\';'.WPSS_EOL.'hf1V=\''.$wpss_js_val.'\';'.WPSS_EOL.'document.write("<input type=\'hidden\' name=\''.WPSS_REF2XJS.'\' value=\'"+'.WPSS_REF2XJS.'+"\' /><input type=\'hidden\' name=\'"+hf1N+"\' value=\'"+hf1V+"\' />");'.WPSS_EOL.'/* ]]> */'.WPSS_EOL.'</script>'.$ao_noop_close;
	}
	echo WPSS_EOL.'<noscript><input type="hidden" name="'.WPSS_JSONST.'" value="NS1" /></noscript>'.WPSS_EOL;

	if( !empty( $promote_plugin_link ) ) {
		$sip5c = '0'; $sip5c = substr(WPSS_SERVER_ADDR, 4, 1); /* Server IP 5th Char */
		$ppl_code = array( '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9, '.' => 10 );
		if( WP_SpamShield::preg_match( "~^[0-9\.]$~", $sip5c ) ) { $int = $ppl_code[$sip5c]; } else { $int = 0; }
		echo WPSS_Promo_Links::comment_promo_link($int).WPSS_EOL;
	}
	$wpss_js_disabled_msg 	= __( 'Currently you have JavaScript disabled. In order to post comments, please make sure JavaScript and Cookies are enabled, and reload the page.', 'wp-spamshield' );
	$wpss_js_enable_msg 	= __( 'Click here for instructions on how to enable JavaScript in your browser.', 'wp-spamshield' );
	echo '<noscript><p><strong>'.$wpss_js_disabled_msg.'</strong> <a href="http://enable-javascript.com/" rel="nofollow external" >'.$wpss_js_enable_msg.'</a></p></noscript>'.WPSS_EOL;

	/* If need to add anything else to comment area, start here */

}

function rs_wpss_get_author_cookie_data() {
	/* Get Comment Author Data Stored in Cookies */
	$key_comment_auth	= 'comment_author_'.WPSS_HASH;
	$key_comment_email	= 'comment_author_email_'.WPSS_HASH;
	$key_comment_url 	= 'comment_author_url_'.WPSS_HASH;
	if( !empty( $_COOKIE[$key_comment_auth] ) ) {
		$comment_author = $_COOKIE[$key_comment_auth];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_auth] = $comment_author; }
	} else { $comment_author = ''; }
	if( !empty( $_COOKIE[$key_comment_email] ) ) {
		$comment_author_email = $_COOKIE[$key_comment_email];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_email] = $comment_author_email; }
	} else { $comment_author_email = ''; }
	if( !empty( $_COOKIE[$key_comment_url] ) ) {
		$comment_author_url = $_COOKIE[$key_comment_url];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_url] = $comment_author_url; }
	} else { $comment_author_url = ''; }
	$author_data = array( 'comment_author' => $comment_author, 'comment_author_email' => $comment_author_email, 'comment_author_url' => $comment_author_url );
	return $author_data;
}

function rs_wpss_get_author_data() {
	/* Get Comment Author Data Stored in Cookies and Session Vars */
	$key_comment_auth	= 'comment_author_'.WPSS_HASH;
	$key_comment_email	= 'comment_author_email_'.WPSS_HASH;
	$key_comment_url 	= 'comment_author_url_'.WPSS_HASH;
	if( !empty( $_COOKIE[$key_comment_auth] ) ) {
		$comment_author = $_COOKIE[$key_comment_auth];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_auth] = $comment_author; }
	} elseif( rs_wpss_is_session_active() && !empty( $_SESSION[$key_comment_auth] ) ) {
		$comment_author = $_SESSION[$key_comment_auth];
	} else { $comment_author = ''; }
	if( !empty( $_COOKIE[$key_comment_email] ) ) {
		$comment_author_email = $_COOKIE[$key_comment_email];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_email] = $comment_author_email; }
	} elseif( rs_wpss_is_session_active() && !empty( $_SESSION[$key_comment_email] ) ) {
		$comment_author_email = $_SESSION[$key_comment_email];
	} else { $comment_author_email = ''; }
	if( !empty( $_COOKIE[$key_comment_url] ) ) {
		$comment_author_url = $_COOKIE[$key_comment_url];
		if( rs_wpss_is_session_active() ) { $_SESSION[$key_comment_url] = $comment_author_url; }
	} elseif( rs_wpss_is_session_active() && !empty( $_SESSION[$key_comment_url] ) ) {
		$comment_author_url = $_SESSION[$key_comment_url];
	} else { $comment_author_url = ''; }
	$author_data = array( 'comment_author' => $comment_author, 'comment_author_email' => $comment_author_email, 'comment_author_url' => $comment_author_url );
	return $author_data;
}

function rs_wpss_update_accept_status( $commentdata, $status = 'r', $line = NULL, $error_code = NULL ) {
	$timenow_display		= current_time( 'timestamp', 0 );
	$wpss_datum 			= date( WPSS_DATE_LONG, $timenow_display );
	$key_comment_acc 		= 'wpss_comments_accepted_'.WPSS_HASH;
	$key_comment_den 		= 'wpss_comments_denied_'.WPSS_HASH;
	$key_comment_den_jsck	= 'wpss_comments_denied_jsck_'.WPSS_HASH;
	$key_comment_den_algo	= 'wpss_comments_denied_algo_'.WPSS_HASH;
	$key_comment_stat_curr	= 'wpss_comments_status_current_'.WPSS_HASH;
	$key_auth_hist 			= 'wpss_author_history_'.WPSS_HASH;
	$key_email_hist 		= 'wpss_author_email_history_'.WPSS_HASH;
	$key_auth_url_hist 		= 'wpss_author_url_history_'.WPSS_HASH;
	if( empty( $_SESSION[$key_comment_acc] ) ) 		{ $_SESSION[$key_comment_acc] = 0; }
	if( empty( $_SESSION[$key_comment_den] ) ) 		{ $_SESSION[$key_comment_den] = 0; }
	if( empty( $_SESSION[$key_comment_den_jsck] ) )	{ $_SESSION[$key_comment_den_jsck] = 0; }
	if( empty( $_SESSION[$key_comment_den_algo] ) )	{ $_SESSION[$key_comment_den_algo] = 0; }
	if( !empty( $line ) ) { $line .= ' '; }
	if( $status === 'r' ) {
		++$_SESSION[$key_comment_den];
		$_SESSION[$key_comment_stat_curr] = '[REJECTED '.$line.$wpss_datum.']';
		$error_type = rs_wpss_get_error_type( $error_code ); /* 1.8.9.6 */
		rs_wpss_increment_count( $error_type ); /* 1.8 */
	} elseif( $status === 'a' ) {
		++$_SESSION[$key_comment_acc];
		$_SESSION[$key_comment_stat_curr] = '[ACCEPTED '.$line.$wpss_datum.']';
		$_SESSION['user_spamshield_count_'.WPSS_HASH] = 0; /* 1.8 */
		$_SESSION['user_spamshield_count_jsck_'.WPSS_HASH] = 0; /* 1.8.9.6 */
		$_SESSION['user_spamshield_count_algo_'.WPSS_HASH] = 0;
		$http_proto = trim( WPSS_Func::lower( $_SERVER['SERVER_PROTOCOL'] ) );
		if( ( 'http/1.1' === $http_proto || 0 === strpos( $http_proto, 'http/2' ) ) && !rs_wpss_is_user_logged_in() ) {
			WP_SpamShield::update_option( array( 'enable_ibf_filter' => 1 ) );
		}
	} else { $_SESSION[$key_comment_stat_curr] = '[ERROR '.$line.' '.$wpss_datum.']'; }
	if( strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) !== 0 ) { $_SESSION[$key_comment_stat_curr] = ''; }
	$wpss_comment_author 		= $commentdata['comment_author'];
	$wpss_comment_author_email	= $commentdata['comment_author_email'];
	$wpss_comment_author_url 	= $commentdata['comment_author_url'];
	if( empty ( $wpss_comment_author ) )		{ $wpss_comment_author 			= ''; }
	if( empty ( $wpss_comment_author_email ) )	{ $wpss_comment_author_email	= ''; }
	if( empty ( $wpss_comment_author_url ) )	{ $wpss_comment_author_url 		= ''; }
	$_SESSION['wpss_comment_author_'.WPSS_HASH] = $wpss_comment_author;
	if( empty( $_SESSION[$key_auth_hist] ) ) { $_SESSION[$key_auth_hist] = array(); }
	$_SESSION[$key_auth_hist][] = $wpss_comment_author;
	$_SESSION['wpss_comment_author_email_'.WPSS_HASH] = $wpss_comment_author_email;
	if( empty( $_SESSION[$key_email_hist] ) ) { $_SESSION[$key_email_hist] = array(); }
	$_SESSION[$key_email_hist][] = $wpss_comment_author_email;
	$_SESSION['wpss_comment_author_url_'.WPSS_HASH] = $wpss_comment_author_url;
	if( empty( $_SESSION[$key_auth_url_hist] ) ) { $_SESSION[$key_auth_url_hist] = array(); }
	$_SESSION[$key_auth_url_hist][] = $wpss_comment_author_url;
	/**
	 *  if( WP_SpamShield::is_debug() ) { $_SESSION['wpss_commentdata_'.WPSS_HASH] = $commentdata; }
	 *  To pass the $commentdata values through SESSION vars to denied_post functions because WP hook won't allow us to.
	 */
	$_SESSION['wpss_commentdata_'.WPSS_HASH] = $commentdata;
}

function rs_wpss_contact_shortcode( $atts = array(), $content = NULL ) {
	/* Implementation: [spamshieldcontact] */
	if( rs_wpss_is_admin_sproc() ) { return NULL; }
	if( is_page() && in_the_loop() && is_main_query() && ( !is_home() && !is_feed() && !is_archive() && !rs_wpss_is_search() && !WP_SpamShield::is_404() ) ) {
		$content = ''; $atts = array(); $shortcode_check = 'shortcode';
		$content_new_shortcode = rs_wpss_contact_form( $content, $atts, $shortcode_check );
		return $content_new_shortcode;
	}
	return NULL;
}

function rs_wpss_contact_form( $content = NULL, $atts = array(), $shortcode_check = NULL ) {
	/**
	 *	The Contact Form
	 *
	 *	Function can be used as either a filter or shortcode. If shortcode, use rs_wpss_contact_shortcode() as wrapper.
	 *	$shortcode_check: determines if function is being used as filter or shortcode - 'shortcode' | NULL (filter)
	 *
	 */
	/* extract( $atts ); */

	/* If not called from shortcode, and $content is empty, or admin process, then do nothing and return $content unfiltered */
	if( ( empty( $shortcode_check ) && empty( $content ) ) || rs_wpss_is_admin_sproc() ) { return $content; }
	$contact_repl_text = array( '<!--spamshield-contact-->', '<!--spamfree-contact-->' ); /* Legacy method */

	/* If not called from shortcode, and not called from legacy method, then do nothing and return $content unfiltered */
	if( empty( $shortcode_check ) && FALSE === strpos( $content, $contact_repl_text[0] ) && FALSE === strpos( $content, $contact_repl_text[1] ) ) { return $content; }

	/* If not called from appropriate section of code, then do nothing and return $content unfiltered */
	if( !is_page() || !in_the_loop() && !is_main_query() || is_home() || is_feed() || is_archive() || rs_wpss_is_search() || WP_SpamShield::is_404() ) { return $content; }

	$email_domain					= rs_wpss_get_email_domain( WPSS_SERVER_NAME );
	$contact_sender_email			= 'wpspamshield.noreply@'.$email_domain;
	$contact_sender_name			= __( 'Contact Form', 'wp-spamshield' );

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	$user_agent 					= rs_wpss_get_user_agent( TRUE, FALSE );
	$user_agent_lc 					= WPSS_Func::lower( $user_agent );
	$user_agent_lc_word_count 		= rs_wpss_count_words( $user_agent_lc );
	$http_accept 					= rs_wpss_get_http_accept( TRUE, FALSE );
	$http_accept_lc					= WPSS_Func::lower( $http_accept );
	$http_accept_language			= rs_wpss_get_http_accept( TRUE, FALSE, TRUE );
	$http_accept_language_lc		= WPSS_Func::lower( $http_accept_language );
	$http_accept_encoding			= rs_wpss_get_http_accept( TRUE, FALSE, FALSE, TRUE );
	$http_accept_encoding_lc		= WPSS_Func::lower( $http_accept_encoding );
	$cf_url							= WPSS_THIS_URL;
	$cf_baseurl						= remove_query_arg( array( 'form', ), $cf_url );
	$cf_action_url					= esc_url( add_query_arg( array( 'form' => 'response', ), $cf_url ) );
	$get_form 						= !empty( $_GET['form'] ) ? $_GET['form'] : '';
	$post_jsonst 					= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_ref2xjs 					= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_jsonst_lc 				= WPSS_Func::lower( $post_jsonst );
	$post_ref2xjs_lc				= WPSS_Func::lower( $post_ref2xjs );
	$ref2xjs_lc						= WPSS_Func::lower( WPSS_REF2XJS );
	$wpss_error_code 				= $cf_content = '';
	if( is_page() && in_the_loop() && is_main_query() && ( !is_home() && !is_feed() && !is_archive() && !rs_wpss_is_search() && !WP_SpamShield::is_404() ) ) { /* Modified 1.7.7, 1.9.5.6, 1.9.9.1 */
		/* MAKE SURE WE ONLY SHOW THE FORM IN THE RIGHT PLACE */
		$spamshield_options = WP_SpamShield::get_option();
		extract( $spamshield_options );
		$wpss_ck_key_bypass 				= $wpss_js_key_bypass = FALSE;
		$wpss_key_values 					= rs_wpss_get_key_values();	extract( $wpss_key_values );
		$wpss_jsck_cookie_val				= !empty( $_COOKIE[$wpss_ck_key] )	? $_COOKIE[$wpss_ck_key]	: '';
		$wpss_jsck_field_val				= !empty( $_POST[$wpss_js_key] )	? $_POST[$wpss_js_key]		: '';
		$wpss_jsck_jquery_val				= !empty( $_POST[$wpss_jq_key] )	? $_POST[$wpss_jq_key]		: '';
		$form_response_thank_you_message	= trim( stripslashes( $spamshield_options['form_response_thank_you_message']) );
		$form_require_website_sess_ovr		= 0; /* SESSION Override - Added 1.7.8 */
		if( rs_wpss_is_session_active() ) {
			if( !empty( $_SESSION['form_require_website_'.WPSS_HASH] ) ) { $form_require_website_sess_ovr = 1; } else { $_SESSION['form_require_website_'.WPSS_HASH] = 0; }
		}
		if( empty( $form_require_website ) && !empty( $form_require_website_sess_ovr ) ) { $form_require_website = 1; }
		$form_include =
			array(
				'website'	=> array( 'i' => $form_include_website,	'r' => $form_require_website,	),
				'phone'		=> array( 'i' => $form_include_phone,	'r' => $form_require_phone,		),
				'company'	=> array( 'i' => $form_include_company,	'r' => $form_require_company,	),
			);
		$form_drop_down_menu_item			= array( '', $form_drop_down_menu_item_1, $form_drop_down_menu_item_2, $form_drop_down_menu_item_3, $form_drop_down_menu_item_4, $form_drop_down_menu_item_5, $form_drop_down_menu_item_6, $form_drop_down_menu_item_7, $form_drop_down_menu_item_8, $form_drop_down_menu_item_9, $form_drop_down_menu_item_10 );
		$form_message_width					= ( !empty( $form_message_width )		&& is_int( $form_message_width )		&& $form_message_width >= 40 )		? $form_message_width		: 40;
		$form_message_height				= ( !empty( $form_message_height )		&& is_int( $form_message_height )		&& $form_message_height >= 5 )		? $form_message_height		: 10;
		$form_message_min_length			= ( !empty( $form_message_min_length )	&& is_int( $form_message_min_length )	&& $form_message_min_length >= 15 )	? $form_message_min_length	: 25;
		$form_message_max_length			= 25600; /* 25kb */

		if( $get_form === 'response' && ( 'POST' !== WPSS_REQUEST_METHOD || empty($_POST) ) ) {
			/**
			 *  1 - PRE-CHECK FOR BLANK FORMS
			 *  REQUEST_METHOD not POST, or empty $_POST - Not a legitimate contact form submission - likely a bot scraping/spamming the site, or wrong content-type
			 *  @since v 1.5.5 to conserve server resources
			 */
			if( !defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', TRUE ); }
			$error_txt = rs_wpss_error_txt();
			$wpss_error = $error_txt.':';
			$cf_content = '<p><strong>'.$wpss_error.' ' . __( 'Please return to the contact form and fill out all required fields.', 'wp-spamshield' ) . '</strong></p><p>&nbsp;</p>'.WPSS_EOL;
			$content_new = str_replace($content, $cf_content, $content);
			$content_shortcode = $cf_content;
		} elseif( $get_form === 'response' ) {
			/**
			 *  2 - RESPONSE PAGE - FORM HAS BEEN SUBMITTED
			 *  CONTACT FORM BACK END - BEGIN
			 */
			if( !defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', TRUE ); }
			global $wpss_contact_inprog; $wpss_contact_inprog = TRUE;
			$wpss_whitelist = $wp_blacklist = $message_spam = $blank_field = $invalid_value = $restricted_url = $restricted_email = $bad_email = $bad_phone = $bad_company = $message_short = $message_long = $cf_jsck_error = $cf_badrobot_error = $cf_spam_loc = $cf_domain_spam_loc = $generic_spam_company = $free_email_address = 0;
			$combo_spam_signal_1 = $combo_spam_signal_2  = $combo_spam_signal_3 = $bad_phone_spammer = 0;
			$wpss_user_blacklisted_prior_cf = 0;
			/* TO DO: Add here */

			/* PROCESSING CONTACT FORM - BEGIN */
			$contact_name = $contact_email = $contact_website = $wpss_contact_phone = $wpss_contact_company = $contact_drop_down_menu = $contact_subject = $contact_message = $raw_contact_message = '';

			$wpss_contact_time	= microtime( TRUE );
			$cf_author_data		= array();

			if( WP_SpamShield::is_debug() ) {
				global $wpss_geolocation; if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
			} else {
				global $wpss_geoloc_short; if( empty ( $wpss_geoloc_short ) ) { $wpss_geoloc_short = rs_wpss_wf_geoiploc_short( $ip ); }
			}

			if( !empty( $_POST['wpss_contact_name'] ) ) {
				$contact_name 						= rs_wpss_prep_cf_email( stripslashes( sanitize_text_field( $_POST['wpss_contact_name'] ) ) );
			}
			if( !empty( $_POST['wpss_contact_email'] ) ) {
				$contact_email 						= sanitize_email( $_POST['wpss_contact_email'] );
			}
			$contact_email_lc 						= WPSS_Func::lower( $contact_email );
			$contact_email_lc_rev 					= strrev( $contact_email_lc );
			if( !empty( $_POST['wpss_contact_website'] ) ) {
				$contact_website 					= esc_url_raw($_POST['wpss_contact_website']);
			}
			$contact_website_lc 					= WPSS_Func::lower( $contact_website );
			$wpss_contact_domain 					= rs_wpss_get_domain( $contact_website_lc );
			$wpss_contact_domain_rev 				= strrev( $wpss_contact_domain );
			if( !empty( $_POST['wpss_contact_phone'] ) ) {
				$wpss_contact_phone 				= sanitize_text_field( $_POST['wpss_contact_phone'] );
			}
			if( !empty( $_POST['wpss_contact_company'] ) ) {
				$wpss_contact_company 				= rs_wpss_prep_cf_email( stripslashes( sanitize_text_field( $_POST['wpss_contact_company'] ) ) );
			}
			$wpss_contact_company_lc				= WPSS_Func::lower( $wpss_contact_company );
			$wpss_common_spam_countries				= array( 'india', 'china', 'russia', 'ukraine', 'pakistan', 'turkey', 'philippines', 'indonesia', ); /* Most common sources of human spam */
			$wpss_common_spam_ccodes				= array( 'IN', 'CN', 'RU', 'UA', 'PK', 'TR', 'PH', 'ID', ); $wpss_rgx_spam_ccodes = implode( '|', $wpss_common_spam_ccodes );
			$wpss_common_spam_phone_pre				= array( '91', '86', '7', '38', '92', '90', '63', '62', '88', '85', '84' ); $wpss_rgx_spam_phone_pre = implode( '|', $wpss_common_spam_phone_pre );
			$wpss_contact_company_lc_nc				= trim( str_replace( $wpss_common_spam_countries, '', $wpss_contact_company_lc ) );	/* Remove country names for testing */

			if( !empty( $_POST['wpss_contact_drop_down_menu'] ) ) {
				$contact_drop_down_menu				= sanitize_text_field( $_POST['wpss_contact_drop_down_menu'] );
			}
			if( !empty( $_POST['wpss_contact_subject'] ) ) {
				$contact_subject 					= rs_wpss_prep_cf_email( stripslashes( sanitize_text_field( $_POST['wpss_contact_subject'] ) ) );
			}
			$contact_subject_lc 					= WPSS_Func::lower( $contact_subject );
			if( !empty( $_POST['wpss_contact_message'] ) ) {
				$contact_message 					= stripslashes( WP_SpamShield::sanitize_textarea( $_POST['wpss_contact_message'] ) );	/* body_content */
				$raw_contact_message 				= trim( $_POST['wpss_contact_message'] );		/* body_content_unsan */
			}
			$contact_msg_lc 						= WP_SpamShield::casetrans( 'lower', $contact_message );	/* body_content_lc */
			$raw_contact_msg_lc 					= WP_SpamShield::casetrans( 'lower', $raw_contact_message );
			$raw_contact_msg_lc_unslash				= stripslashes( $raw_contact_msg_lc );
			$contact_msg_extracted_urls 			= rs_wpss_parse_links( $raw_contact_msg_lc_unslash, 'url' );	/* Parse message content for all URLs */
			$wpss_contact_num_links 				= count( $contact_msg_extracted_urls );	/* Count extracted URLS from body content - Added 1.8.4 */
			$wpss_contact_num_limit					= 10;	/* Max number of links in message body content */

			$message_length							= rs_wpss_strlen( $contact_message );
			$cf_author_data['body_content_len']		= $message_length;

			$cf_author_data['comment_author']		= $contact_name;
			$cf_author_data['comment_author_email']	= $contact_email_lc;
			$cf_author_data['comment_author_url']	= $contact_website_lc;

			$contact_id_str 						= $contact_email_lc.'_'.$ip.'_'.$wpss_contact_time; /* Email/IP/Time */
			$contact_id_hash 						= rs_wpss_md5( $contact_id_str );
			$key_contact_status						= 'contact_status_'.$contact_id_hash;

			/* Update Session Vars */
			$key_comment_auth 						= 'comment_author_'.WPSS_HASH;
			$key_comment_email						= 'comment_author_email_'.WPSS_HASH;
			$key_comment_url						= 'comment_author_url_'.WPSS_HASH;
			if( rs_wpss_is_session_active() ) {
				$_SESSION[$key_comment_auth] 		= $contact_name;
				$_SESSION[$key_comment_email]		= $contact_email_lc;
				$_SESSION[$key_comment_url] 		= $contact_website_lc;
				$_SESSION[$key_contact_status] 		= 'INITIATED';
			}

			/* Add New Tests for Logging - BEGIN */
			if( !empty( $post_ref2xjs ) ) {
				$ref2xJS = WPSS_Func::lower( addslashes( urldecode( $post_ref2xjs ) ) );
				$ref2xJS = str_replace( '%3a', ':', $ref2xJS );
				$ref2xJS = str_replace( ' ', '+', $ref2xJS );
				$wpss_javascript_page_referrer = esc_url_raw( $ref2xJS );
			} else { $wpss_javascript_page_referrer = '[None]'; }

			if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) { $wpss_jsonst = $post_jsonst; } else { $wpss_jsonst = '[None]'; }

			$cf_author_data['javascript_page_referrer']	= $wpss_javascript_page_referrer;
			$cf_author_data['jsonst']					= $wpss_jsonst;

			unset( $wpss_javascript_page_referrer, $wpss_jsonst );

			/* Add New Tests for Logging - END */

			/* PROCESSING CONTACT FORM - END */

			/* FORM INFO - BEGIN */

			if( !empty( $form_message_recipient ) && is_email( $form_message_recipient ) ) {
				$cf_recipient	= $form_message_recipient;
			} else {
				$cf_recipient 	= get_option('admin_email');
			}
			$cf_subject 		= '[' . __( 'Website Contact', 'wp-spamshield' ) . '] '.$contact_subject;
			$cf_msg_headers		= '';
			$cf_msg_headers_arr =
				array(
					'From'			=> $contact_sender_name.' <'.$contact_sender_email.'>',
					'Reply-To'		=> $contact_name.' <'.$contact_email_lc.'>',
					'Content-Type'	=> 'text/plain',
				);
			foreach( $cf_msg_headers_arr as $k => $v ) {
				$cf_msg_headers .= $k.': '.$v."\r\n";
			}
			unset( $cf_msg_headers_arr );
			/* Another option: "Content-Type: text/html" */

			/* FORM INFO - END */

			/* TEST TO PREVENT CONTACT FORM SPAM - BEGIN */

			/* Check if user is blacklisted prior to submitting contact form */
			if( rs_wpss_ubl_cache() ) {
				$wpss_user_blacklisted_prior_cf = 1;
			}

			/* TESTING CONTACT FORM SUBMISSION FOR SPAM - BEGIN */

			/* JS/CK Tests - BEGIN */
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* 1.9.1 */
				$wpss_ck_key_bypass = TRUE;
			}
			if( FALSE === $wpss_ck_key_bypass ) {
				if( $wpss_jsck_cookie_val !== $wpss_ck_val ) {
					$wpss_error_code .= ' CF-COOKIE-2';
					$cf_jsck_error = TRUE;
				}
			}
			if( FALSE === $wpss_js_key_bypass ) { /* 1.8.9 */
				if( $wpss_jsck_field_val !== $wpss_js_val ) {
					$wpss_error_code .= ' CF-FVFJS-2';
					$cf_jsck_error = TRUE;
				}
			}
			if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) {
				$wpss_error_code .= ' CF-JSONST-1000-2';
				$cf_jsck_error = TRUE;
			}
			/* JS/CK Tests - END */

			/**
			 *  WPSS Whitelist Check - BEGIN
			 *  Test WPSS Whitelist if option set
			 */
			if( !empty( $spamshield_options['enable_whitelist'] ) && empty( $wpss_error_code ) && rs_wpss_whitelist_check( $contact_email_lc ) ) { $wpss_whitelist = 1; }
			/* WPSS Whitelist Check - END */

			/* TO DO: REWORK SO THAT IF FAILS COOKIE TEST, TESTS ARE COMPLETE */

			/* ERROR CHECKING */
			$cf_blacklist_status	= $contact_response_status_message_addendum = '';
			$subject_blacklisted	= $content_blacklisted = $content_spammy = $content_spam_terms = $content_spam_terms_group_2 = $name_blacklisted = $email_blacklisted = $domain_blacklisted = $website_shortened_url = $website_long_url = $website_exploit_url = $content_excess_links = $content_shortened_url = $content_exploit_url = FALSE;

			/* Subject Spam Phrases */
			$cf_spam_subj_arr		= array( 'link request', 'link exchange', 'seo service $99 per month', 'seo services $99 per month', 'seo services @ $99 per month', 'partnership with offshore development center', 'guest post opportunity', 'guest posting opportunity', 'link building on high quality', );

			/* Content Spam Phrases */
			$cf_spam_term_limit		= 15;
			$cf_spam_term_limits	=
				array(
					'link' => 7, 'link building' => 3, 'link exchange' => 2, 'link request' => 1, 'link building service' => 2, 'link building experts india' => 0, 'india' => 1, 'traffic exchange' => 1, 'outsource some seo business' => 0, 
					/* Start Group 2*/
					'can you outsource some seo business to us? we will work according to you and your clients and for a long term relationship we can start our SEO services in only $99 per month per website. looking forward for your positive reply' => 0, 
					'can you outsource some seo business to us' => 0, 
				);

			/* Check if Subject seems spammy */
			if( rs_wpss_magic_parser( $cf_spam_subj_arr, $contact_subject_lc ) ) { $subject_blacklisted = TRUE; }

			/* Check if Content seems spammy */
			if( WPSS_Filters::cf_content_blacklist_chk( $contact_msg_lc ) ) {
				$content_blacklisted = TRUE; $content_spammy = TRUE;
				$wpss_error_code .= ' CF-10400C-BL';
			}
			/* Check Spam Terms in Content */
			$cf_spam_term_total = 0; $i = 0;
			foreach( $cf_spam_term_limits as $t => $l ) {
				++$i; $c = (int) rs_wpss_magic_parser( (array) $t, $contact_msg_lc, TRUE );
				if( $c > $l ) { 
					$content_spam_terms = TRUE; if( $i >= 10 ) { $content_spam_terms_group_2 = TRUE; }
				}
				$cf_spam_term_total += $c;
			}
			unset( $i, $t, $l, $c );

			/* Check if Name is Keyword Spam */
			if( empty( $wpss_whitelist ) && WPSS_Filters::anchortxt_blacklist_chk( $contact_name ) ) {
				$name_blacklisted = TRUE;
				$wpss_error_code .= ' CF-10500A-BL';
			}
			/* Check if email is blacklisted */
			if( empty( $wpss_whitelist ) && WPSS_Filters::email_blacklist_chk( $contact_email_lc ) ) {
				$email_blacklisted = TRUE;
				$wpss_error_code .= ' CF-9200E-BL';
			}
			/* Website - Check if domain is blacklisted */
			if( empty( $wpss_whitelist ) && WPSS_Filters::domain_blacklist_chk( $wpss_contact_domain ) ) {
				$domain_blacklisted = TRUE;
				$wpss_error_code .= ' CF-10500AU-BL';
			}
			/* Website - URL Shortener Check - Added in 1.3.8 */
			if( empty( $wpss_whitelist ) && WPSS_Filters::urlshort_blacklist_chk( $contact_website_lc ) ) {
				$website_shortened_url = TRUE;
				$wpss_error_code .= ' CF-10501AU-BL';
			}
			/* Website - Excessively Long URL Check (Obfuscated & Exploit) - Added in 1.3.8 */
			if( empty( $wpss_whitelist ) && WPSS_Filters::long_url_chk( $contact_website_lc ) ) {
				$website_long_url = TRUE;
				$wpss_error_code .= ' CF-10502AU-BL';
			}
			/**
			 *  Spam URL Check -  Check for URL Shorteners, Bogus Long URLs, and Misc Spam Domains
				if( empty( $wpss_whitelist ) && WPSS_Filters::at_link_spam_url_chk( $contact_website_lc ) ) {
					$website_spam_url = TRUE;
					$wpss_error_code .= ' CF-10510AU-BL';
				} else { $website_spam_url = FALSE; }
			 */

			/* Add Misc Spam URLs next... */

			/* Exploit URL Check - Ignores Whitelist */
			if( WPSS_Filters::exploit_url_chk( $contact_website_lc ) ) {
				$website_exploit_url = TRUE;
				$wpss_error_code .= ' CF-15000AU-XPL'; /* Added in 1.4 */
			}

			/* Body Content - Check for excessive number of links in message ( body_content ) - Added 1.8.4 */
			if( empty( $wpss_whitelist ) && $wpss_contact_num_links > $wpss_contact_num_limit ) {
				$content_excess_links = TRUE; $content_spammy = TRUE;
				$wpss_error_code .= ' CF-1-HT';
			}

			/* Body Content - Parse URLs and check for URL Shortener Links - Added in 1.3.8 */
			if( empty( $wpss_whitelist ) && WPSS_Filters::cf_link_spam_url_chk( $raw_contact_msg_lc_unslash, $contact_email_lc ) ) {
				$content_shortened_url = TRUE;
				$wpss_error_code .= ' CF-10530CU-BL';
			}

			/* Check all URL's in Body Content for Exploits - Ignores Whitelist */
			if( WPSS_Filters::exploit_url_chk( $contact_msg_extracted_urls ) ) {
				$content_exploit_url = TRUE;
				$wpss_error_code .= ' CF-15000CU-XPL'; /* Added in 1.4 */
			}

			/*  Check if Contact Form Content Contains *Only* URLs - Ignores Whitelist */
			if( !empty( $contact_msg_extracted_urls ) && is_array( $contact_msg_extracted_urls ) ) {
				$contact_msg_temp = str_replace( $contact_msg_extracted_urls, '', $raw_contact_msg_lc_unslash );
				$contact_msg_temp = trim( str_replace( array( "\s", WPSS_EOL, "\t", ' ', ), '', $contact_msg_temp ) );
				if( empty( $contact_msg_temp ) ) {
					$message_spam = 1;
					$wpss_error_code .= ' 10410CU'; /* Added in 1.9.8.2 */
				}
			}

			if( strpos( $rev_dns_lc_rev, 'ni.' ) === 0 || strpos( $rev_dns_lc_rev, 'ur.' ) === 0 || strpos( $rev_dns_lc_rev, 'kp.' ) === 0 || strpos( $rev_dns_lc_rev, 'nc.' ) === 0 || strpos( $rev_dns_lc_rev, 'au.' ) === 0 || strpos( $rev_dns_lc_rev, 'rt.' ) === 0 || WP_SpamShield::preg_match( "~^1\.22\.2(19|20|23)\.~", $ip ) || strpos( $rev_dns_lc_rev, '.aidni-tenecap.' ) || WP_SpamShield::preg_match( "~\.(".$wpss_rgx_spam_ccodes.")$~i", $rev_dns_lc ) ) {
				$cf_spam_loc = 1;
				/* TO DO: Add more, switch to full Regex */
			} elseif( strpos( $contact_email_lc_rev , 'ni.' ) === 0 || strpos( $contact_email_lc_rev , 'ur.' ) === 0 || strpos( $contact_email_lc_rev , 'kp.' ) === 0 || strpos( $contact_email_lc_rev , 'nc.' ) === 0 || strpos( $contact_email_lc_rev , 'au.' ) === 0 || strpos( $contact_email_lc_rev , 'rt.' ) === 0 ) {
				$cf_spam_loc = 2;
				/* TO DO: Add more, switch to Regex */
			} elseif( strpos( $wpss_contact_domain_rev , 'ni.' ) === 0 || strpos( $wpss_contact_domain_rev , 'ur.' ) === 0 || strpos( $wpss_contact_domain_rev , 'kp.' ) === 0 || strpos( $wpss_contact_domain_rev , 'nc.' ) === 0 || strpos( $wpss_contact_domain_rev , 'au.' ) === 0 || strpos( $wpss_contact_domain_rev , 'rt.' ) === 0 ) {
				$cf_spam_loc = 3;
				/* TO DO: Add more, switch to Regex */
			} elseif( WP_SpamShield::preg_match( "~^(\+\s*(".$wpss_rgx_spam_phone_pre.")|(".$wpss_rgx_spam_phone_pre.")\b)~i", $wpss_contact_phone ) ) {
				$cf_spam_loc = 4;
			} else {
				global $wpss_geoiploc_data; if( empty ( $wpss_geoiploc_data ) ) { $wpss_geoiploc_data = rs_wpss_wf_geoiploc( $ip ); }
				if( !empty ( $wpss_geoiploc_data ) ) { extract( $wpss_geoiploc_data ); }
				if( !empty( $countryCode ) && WPSS_PHP::in_array( $countryCode, $wpss_common_spam_ccodes ) ) {
					$cf_spam_loc = 9;
					/* TO DO: Add more, switch to full Regex */
				}
			}
			if( strpos( WPSS_SERVER_NAME_REV, 'ni.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'ur.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'kp.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'nc.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'au.' ) === 0 || strpos( WPSS_SERVER_NAME_REV, 'rt.' ) === 0 ) {
				$cf_domain_spam_loc = 1;
				/* TO DO: Add more, switch to Regex */
			}
			if( !empty( $form_include_company ) && !empty( $wpss_contact_company_lc ) && WP_SpamShield::preg_match( "~(^|\b\s+(.*\s+)?\b)(.*(india|delhi|mumbai|chennai).*)?(se(o|m)|(search\s*engine|internet|web)\s*(optimi[zs](a[tc]ion|ing|er)|market(ing|er))|it|informa[tc]ions?\s*tech?nolog(y|i[ea]?)|(se(o|m)|((search\s*engine|internet|web)\s*)?(optimi[zs](a[tc]ion|ing|er)|market(ing|er))|web\s*(design(er|ing)?|develop(ment|er|ing))|(content\s*|copy\s*)?(writ|right)(er?|ing)|it|informa[tc]ions?\s*tech?nolog(y|i[ea]?))s?\s*(comp(an|na)y|firm|services?|freelanc(er?|ing))|(comp(an|na)y|firm|services?|freelanc(er?|ing))\s*(se(o|m)|((search\s*engine|internet|web)\s*)?(optimi[zs](a[tc]ion|ing|er)|market(ing|er))|web\s*(design(er|ing)?|develop(ment|er|ing))|(content\s*|copy\s*)?(writ|right)(er?|ing)|it|informa[tc]ions?\s*tech?nolog(y|i[ea]?))s?)(.*(india|delhi|mumbai|chennai).*)?(\b\s+(.*\s+)?\b|$)~", $wpss_contact_company_lc_nc ) ) {
				$generic_spam_company = 1;
			}
			if( rs_wpss_is_free_email( $contact_email_lc ) ) { $free_email_address = 1; }

			/* Combo Tests - Pre */
			if( WP_SpamShield::preg_match( "~((reply|email\s+us)\s+back\s+to\s+get\s+(a\s+)?full\s+proposal\.$|can\s+you\s+outsource\s+some\s+seo\s+business\s+to\s+us|humble\s+request\s+we\s+are\s+not\s+spammers\.|if\s+by\s+sending\s+this\s+email\s+we\s+have\s+made\s+(an\s+)?offense\s+to\s+you|if\s+you\s+are\s+not\s+interested\s+then\s+please\s+(do\s+)?reply\s+back\s+as|in\s+order\s+to\s+stop\s+receiving\s+(such\s+)?emails\s+from\s+us\s+in\s+(the\s+)?future\s+please\s+reply\s+with|if\s+you\s+do\s+not\s+wish\s+to\s+receive\s+further\s+emails\s+(kindly\s+)?reply\s+with)~", $contact_msg_lc ) ) {
				$combo_spam_signal_1 = 1; 
			}
			if( WP_SpamShield::preg_match( "~(^|\b\s+(.*\s+)?\b)(get|want)\s+more\s+(customer|client|visitor)s?\s+(and|\&|or)\s+(customer|client|visitor)s?\?+(\b\s+(.*\s+)?\b|$)~", $contact_subject_lc ) ) { $combo_spam_signal_2 = 1; }

			if( WP_SpamShield::preg_match( "~(?:^|[,;\.\!\?\s]+)(india|delhi|mumbai|chennai)(?:[,;\.\!\?\s]+|$)~", $contact_msg_lc ) ) {
				preg_match_all( "~(?:^|[,;\.\!\?\s]+)(SEO)(?:[,;\.\!\?\s]+|$)~", $contact_message, $matches_raw, PREG_PATTERN_ORDER );
				$spam_signal_3_matches 			= $matches_raw[1]; /* Array containing matches parsed from haystack text ($contact_message) */
				$spam_signal_3_matches_count	= count( $spam_signal_3_matches );
				/* Changed from 7 to 2 occurrences - 1.6.2 */
				if( $spam_signal_3_matches_count > 1 ) { $combo_spam_signal_3 = 1; }
			}
			if( WP_SpamShield::preg_match( "~^(01[2-9]){3}0$~", $wpss_contact_phone ) ) { $bad_phone_spammer = 1; }
			/* Combo Tests */
			if( empty( $wpss_whitelist ) && ( $cf_spam_term_total > $cf_spam_term_limit || TRUE === $content_spam_terms ) && !empty( $cf_spam_loc ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM1';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing requests will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			} elseif( empty( $wpss_whitelist ) && ( !empty( $subject_blacklisted ) || !empty( $content_blacklisted ) || !empty( $content_spammy ) || !empty( $content_spam_terms_group_2 ) || !empty( $name_blacklisted ) || !empty( $email_blacklisted ) || !empty( $domain_blacklisted ) || !empty( $website_shortened_url ) || !empty( $website_long_url ) || !empty( $website_exploit_url ) || !empty( $content_excess_links ) || !empty( $content_shortened_url ) || !empty( $content_exploit_url ) ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM2';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			} elseif( empty( $wpss_whitelist ) && !empty( $cf_spam_loc ) && empty ( $cf_domain_spam_loc ) && !empty( $free_email_address ) && ( !empty( $generic_spam_company ) || !empty( $combo_spam_signal_1 ) || !empty( $combo_spam_signal_2 ) || !empty( $bad_phone_spammer ) ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM3';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/* Blacklist on failure - future attempts blocked */
				rs_wpss_ubl_cache( 'set' );
			} elseif( empty( $wpss_whitelist ) && !empty( $generic_spam_company ) && !empty( $combo_spam_signal_3 ) ) {
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM4';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/* Blacklist on failure - future attempts blocked */
				rs_wpss_ubl_cache( 'set' );
			} elseif( empty( $wpss_whitelist ) && !empty( $generic_spam_company ) && !empty( $free_email_address ) ) {
				/* BOTH are odd as legit companies include their name and don't use free email */
				$message_spam = 1;
				$wpss_error_code .= ' CF-MSG-SPAM5';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, and SEO outsourcing/offshoring spam will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/* Blacklist on failure - future attempts blocked */
				rs_wpss_ubl_cache( 'set' );
			}

			if( empty( $contact_name ) || empty( $contact_email ) || empty( $contact_subject ) || empty( $contact_message ) || ( !empty( $form_include_website ) && !empty( $form_require_website ) && empty( $contact_website ) ) || ( !empty( $form_include_phone ) && !empty( $form_require_phone ) && empty( $wpss_contact_phone ) ) || ( !empty( $form_include_company ) && !empty( $form_require_company ) && empty( $wpss_contact_company ) ) || ( !empty( $form_include_drop_down_menu ) && !empty( $form_drop_down_menu_title ) && !empty( $form_drop_down_menu_item_1 ) && !empty( $form_drop_down_menu_item_2 ) && empty( $contact_drop_down_menu ) ) ) {
				$blank_field=1;
				$wpss_error_code .= ' CF-BLANKFIELD';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'At least one required field was left blank.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			if( WP_SpamShield::is_debug() ) {
				if( $wpss_contact_domain === WPSS_SERVER_NAME && ( !rs_wpss_is_admin_ip( $ip ) || !empty( $cf_spam_loc ) ) ) {
					$invalid_value=1;
					$restricted_url=1;
					$wpss_error_code .= ' CF-RESTR-URL';
					/* TO DO: TRANSLATE */
					$contact_response_status_message_addendum .= '&bull; ' .  __( 'Please enter a valid website.', 'wp-spamshield' ) . ' ' . __( 'Please use <em>your</em> company website URL, not ours.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
					/**
					 *  Bump user spam count to 5
					 */
					if( rs_wpss_is_session_active() && ( empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) || $_SESSION['user_spamshield_count_'.WPSS_HASH] < 5 ) ) {
						$_SESSION['user_spamshield_count_'.WPSS_HASH] = 5;
					}
				}
				$wpss_debug_server_rgx = rs_wpss_preg_quote( ltrim( WPSS_DEBUG_SERVER_NAME, '.' ) );
				if( WP_SpamShield::preg_match( "~@".$wpss_debug_server_rgx."$~", $contact_email ) && ( !rs_wpss_is_admin_ip( $ip ) || !empty( $cf_spam_loc ) ) ) {
					$invalid_value=1;
					$restricted_email=1;
					$wpss_error_code .= ' CF-RESTR-EMAIL';
					/* TO DO: TRANSLATE */
					$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid email address.' ) . ' ' . __( 'Please use <em>your</em> email address, not one of ours.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
					/**
					 *  Bump user spam count to 5
					 */
					if( rs_wpss_is_session_active() && ( empty( $_SESSION['user_spamshield_count_'.WPSS_HASH] ) || $_SESSION['user_spamshield_count_'.WPSS_HASH] < 5 ) ) {
						$_SESSION['user_spamshield_count_'.WPSS_HASH] = 5;
					}
				}
			}
			if( !is_email( $contact_email ) ) {
				$invalid_value = 1;
				$bad_email = 1;
				$wpss_error_code .= ' CF-INVAL-EMAIL';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid email address.' ) . '<br />&nbsp;<br />';
			}
			if( empty( $bad_email ) && !rs_wpss_email_domain_exists( $contact_email ) ) {
				$invalid_value = 1;
				$bad_email = 1;
				$wpss_error_code .= ' CF-INVAL-EMAIL-D';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid email address.' ) . '<br />&nbsp;<br />';
			}

			/* TO DO: RE-WORK THIS SECTION */
			$wpss_contact_phone_clean	= preg_replace( "~\D+~", '', $wpss_contact_phone );
			$phone_length				= rs_wpss_strlen( $wpss_contact_phone_clean );	/* Min = 5 */
			$phone_1c					= isset( $wpss_contact_phone_clean[0] ) ? $wpss_contact_phone_clean[0] : '0';	/* 1st character */
			$wpss_contact_phone_zero	= preg_replace( "~(0?((01){3,}|(10){3,}|(012){3,}|(013){3,}|(120){3,}|(130){3,}|1234567?|2345678?)0?|[\+\-\(\)\s]|n/?a|none|".$phone_1c.")~i", '', $wpss_contact_phone );
			if( !empty( $form_require_phone ) && !empty( $form_include_phone ) && ( empty( $wpss_contact_phone_zero ) || !empty( $bad_phone_spammer ) || $phone_length < 5 || strpos( $wpss_contact_phone, '123456' ) === 0 || strpos( $wpss_contact_phone, '0123456' ) === 0 || strpos( $wpss_contact_phone, '1234567' ) !== FALSE ) ) {
				$invalid_value = 1; $bad_phone = 1;
				$wpss_error_code .= ' CF-INVAL-PHONE';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid phone number.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			$wpss_contact_company_zero	= preg_replace( "~([0\+\-\(\)\s]|n/?a)~i", '', $wpss_contact_company_lc );
			if( !empty( $form_require_company ) && !empty( $form_include_company ) && ( empty( $wpss_contact_company_zero ) || WP_SpamShield::preg_match( "~(^https?\:/+|^(0+|compan(y|ie)|confidential|empty|expert|fuck([\s\.\-_]*(off|you))?|hack(er|ing)?|invalid|it|na|n/a|nada|negative|nein|no|none?|nothing|null?|nyet|private?|personal|restricted|secret|seo|(un|not\s*)employed|unknown|void|web)$)~", $wpss_contact_company_lc ) ) ) {
				$invalid_value = 1;	$bad_company = 1;
				$wpss_error_code .= ' CF-INVAL-COMPANY';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid company.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			/* Spammers using one of Google's official domains as their URL */
			if( !empty( $form_include_website ) && ( !empty( $generic_spam_company ) && FALSE === strpos( $reverse_dns_lc, 'google' )  && FALSE === strpos( $reverse_dns_lc, 'blogger' ) && !WP_SpamShield::is_google_ip( $ip) ) && rs_wpss_is_google_domain( $wpss_contact_domain ) ) {
				$invalid_value = 1; $bad_website = 1;
				$wpss_error_code .= ' CF-INVAL-URL-G';
				/* TO DO: TRANSLATE */
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid website.', 'wp-spamshield' ) . ' ' . __( 'Please use <em>your</em> company website URL, not Google\'s.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/**
				 *  The only reason we're even putting up with these fools is to honeypot them.
				 *  Also, now makes website field required temporarily for this SESSION.
				 */
				if( rs_wpss_is_session_active() ) {
					$_SESSION['form_require_website_'.WPSS_HASH] = 1;
				}
			}
			if( !empty( $form_include_website ) && !empty( $contact_website ) && ( !rs_wpss_is_valid_url( $contact_website_lc ) || $cf_baseurl === $contact_website_lc ) ) {
				$invalid_value = 1;	$bad_website = 1;
				$wpss_error_code .= ' CF-INVAL-URL';
				/* TO DO: TRANSLATE */
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid website.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/**
				 *  Now makes website field required temporarily for this SESSION. Honeypot spammers.
				 */
				if( rs_wpss_is_session_active() ) {
					$_SESSION['form_require_website_'.WPSS_HASH] = 1;
				}
			}
			if( empty( $bad_website ) && !empty( $form_include_website ) && !empty( $contact_website ) && !rs_wpss_domain_exists( $wpss_contact_domain ) ) {
				$invalid_value = 1;	$bad_website = 1;
				$wpss_error_code .= ' CF-INVAL-URL-D';
				/* TO DO: TRANSLATE */
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Please enter a valid website.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
				/**
				 *  Now makes website field required temporarily for this SESSION. Honeypot spammers.
				 */
				if( rs_wpss_is_session_active() ) {
					$_SESSION['form_require_website_'.WPSS_HASH] = 1;
				}
			}

			if( $message_length < $form_message_min_length ) {
				$message_short = 1;
				$wpss_error_code .= ' CF-MSG-SHORT';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message too short. Please enter a complete message.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			if( $message_length > $form_message_max_length ) {
				$message_long = 1;
				$wpss_error_code .= ' CF-MSG-LONG';
				$contact_response_status_message_addendum .= '&bull; ' . __( 'Message too long. Please enter a shorter message.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}

			/**
			 *  BAD ROBOT TEST - BEGIN
			 *  This replaces previous CF-REF-2-1023 test and previous WPSS_Filters::revdns_filter() here.
			 */

			$bad_robot_filter_data 	 = WPSS_Filters::bad_robot_blacklist_chk( 'contact', '', $ip, $reverse_dns, $contact_name, $contact_email_lc );
			$cf_filter_status		 = $bad_robot_filter_data['status'];
			$bad_robot_blacklisted 	 = $bad_robot_filter_data['blacklisted'];

			if( !empty( $bad_robot_blacklisted ) ) {
				$message_spam 		 = 1;
				$wpss_error_code 	.= $bad_robot_filter_data['error_code'];
				$cf_badrobot_error 	 = TRUE;
				$cf_blacklist_status = '3'; /* Implement */
				$contact_response_status_message_addendum = '&bull; ' . __( 'Message appears to be spam.', 'wp-spamshield' ) . ' ' . __( 'Please note that link requests, link exchange requests, SEO outsourcing/offshoring spam, and automated contact form submissions will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			}
			/* BAD ROBOT TEST - END */


			/* Enhanced Comment Blacklist - CF-0-ECBL */
			if( empty( $wpss_whitelist ) && !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
				if( rs_wpss_blacklist_check( '', $contact_email_lc, '', '', $ip, '', $reverse_dns_lc ) ) {
					$message_spam = 1; $wp_blacklist = 1;
					$wpss_error_code .= ' CF-0-ECBL';
					$contact_response_status_message_addendum = '&bull; ' . __( 'Your message has been blocked based on the website owner\'s blacklist settings.', 'wp-spamshield' ) . ' ' . __( 'If you feel this is in error, please contact the site owner by some other method.', 'wp-spamshield' );
					if( !empty( $cf_spam_loc ) && empty ( $cf_domain_spam_loc ) ) {
						$contact_response_status_message_addendum .= ' ' . __( 'Please note that link requests, link exchange requests, SEO outsourcing/offshoring spam, and automated contact form submissions will be automatically deleted, and are not an acceptable use of this contact form.', 'wp-spamshield' );
					}
					$contact_response_status_message_addendum .= '<br />&nbsp;<br />';
				}
			}

			/**
			 *  Custom Filter - CF-CUSTOM
			 *  Allows user to add a custom filter in another plugin, functions.php, etc
			 *  Only fires if no filters have been tripped yet.
			 *  @since 1.9.7.8
			 */
			if( empty( $wpss_whitelist ) && empty( $wpss_error_code ) ) {
				$contact_form_data = array(
					'status'	=> FALSE,	/* Haven't determined it to be spam yet... If custom filter trips, change to TRUE */
					'name'		=> $contact_name,
					'email'		=> $contact_email_lc,
					'url'		=> $contact_website_lc,
					'domain'	=> $wpss_contact_domain,
					'ip'		=> $ip,
					'revdns'	=> $reverse_dns_lc,
					'company'	=> $wpss_contact_company,
					'subject'	=> $contact_subject_lc,
					'ddmenu'	=> $contact_drop_down_menu,
					'message'	=> $contact_msg_lc,
					'response'	=> __( 'That action is currently not allowed.' ), /* Change this to custom response message. */
				);
				$contact_form_data = apply_filters( 'wpss_cf_custom_filter_check', $contact_form_data );
				if( TRUE === $contact_form_data['status'] ) {
					$message_spam = 1;
					$wpss_error_code .= ' CF-CUSTOM';
					$contact_response_status_message_addendum = $contact_form_data['response'] . '<br />&nbsp;<br />';
				}
			}

			/**
			 *  FINAL TEST
			 *  TEST 0-POST - See if user has already been blacklisted this session (before submission of this form), or a previous session, included for cases where caching is active
			 */
			if( !empty( $wpss_user_blacklisted_prior_cf ) ) {
				/* User is blacklisted prior to submitting contact form */
				$message_spam = 1;
				$user_blacklisted = TRUE;
				$wpss_error_code .= ' CF-0-POST-BL';
				$cf_blacklist_status = '3'; /* Implement */
				rs_wpss_ubl_cache( 'set' );
				$contact_response_status_message_addendum = '&bull; ' . __( 'Contact form has been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) . '<br />&nbsp;<br />';
			} else { $user_blacklisted = FALSE; }

			/**
			 *  Track # of submissions this session
			 *  Must go after spam tests
			 */
			if( rs_wpss_is_session_active() ) {
				$_SESSION['wpss_cf_submissions_'.WPSS_HASH]	= ( !empty( $_SESSION['wpss_cf_submissions_'.WPSS_HASH] ) ) ? $_SESSION['wpss_cf_submissions_'.WPSS_HASH] : 0;
				++$_SESSION['wpss_cf_submissions_'.WPSS_HASH];
			}

			/* TESTING SUBMISSION FOR SPAM - END */

			/* Sanitized versions for output */
			$cf_http_accept_encoding	= $cf_http_accept_language = $cf_http_accept = $cf_http_referer = '';
			$cf_http_accept_encoding	= rs_wpss_get_http_accept( FALSE, FALSE, FALSE, TRUE );
			$cf_http_accept_language	= rs_wpss_get_http_accept( FALSE, FALSE, TRUE );
			$cf_http_accept				= rs_wpss_get_http_accept();
			$cf_http_user_agent			= rs_wpss_get_user_agent();
			$cf_http_browser			= rs_wpss_get_browser();
			$cf_http_referer			= rs_wpss_get_referrer( FALSE, TRUE, TRUE ); /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */

			/* MESSAGE CONTENT - BEGIN */
			$cf_msg_1 = $cf_msg_2 = $cf_msg_3 = '';
			$contact_msg_eml = str_replace( "\n", "\r\n", $contact_message );

			$cf_msg_1 .= __( 'Message', 'wp-spamshield' ) . ': '."\r\n";
			$cf_msg_1 .= $contact_msg_eml."\r\n\r\n";
			$cf_msg_1 .= __( 'Name' ) . ': '.$contact_name."\r\n";
			$cf_msg_1 .= __( 'Email' ) . ': '.$contact_email_lc."\r\n";
			$form_include['phone']['d'] = $wpss_contact_phone; $form_include['company']['d'] = $wpss_contact_company; $form_include['website']['d'] = $contact_website_lc;
			foreach( $form_include as $k => $v ) {
				if( $k === 'website' ) { $text = __( 'Website' ); $type = 'url'; } else { $text = __( WP_SpamShield::casetrans( 'ucfirst', $k ), 'wp-spamshield' ); $type = 'text'; }
				if( !empty( $v['i'] ) ) { $cf_msg_1 .= $text.': '.$v['d']."\r\n"; }
			}
			if( !empty( $form_include_drop_down_menu ) && !empty( $form_drop_down_menu_title ) && !empty( $form_drop_down_menu_item_1 ) && !empty( $form_drop_down_menu_item_2 ) ) {
				$cf_msg_1 .= $form_drop_down_menu_title.": ".$contact_drop_down_menu."\r\n";
			}

			$cf_msg_2 .= "\r\n";
			if( !empty( $form_include_user_meta ) ) {
				$cf_msg_2 .= "\r\n";
				$cf_msg_2 .= __( 'Website Generating This Email', 'wp-spamshield' ) . ': '.WPSS_SITE_URL."\r\n";
				$cf_msg_2 .= __( 'Referrer', 'wp-spamshield' ) . ': '.$cf_http_referer."\r\n"; /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
				$cf_msg_2 .= __( 'User-Agent (Browser/OS)', 'wp-spamshield' ) . ": ".$cf_http_user_agent."\r\n";
				if( !empty( $cf_http_browser ) ) {
					$cf_msg_2 .= __( 'Browser', 'wp-spamshield' ) . ": ".$cf_http_browser."\r\n";
				}
				if( WP_SpamShield::is_debug() ) {
					if( !empty ( $wpss_geolocation ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
						$cf_msg_2 .= __( 'Location', 'wp-spamshield' ) . ': '.$wpss_geolocation."\r\n";
					}
				} else {
					if( !empty ( $wpss_geoloc_short ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
						$cf_msg_2 .= __( 'Country', 'wp-spamshield' ) . ': '.$wpss_geoloc_short."\r\n";
					}
				}
				$cf_msg_2 .= __( 'IP Address', 'wp-spamshield' ) . ': '.$ip."\r\n";
				$cf_msg_2 .= __( 'Server', 'wp-spamshield' ) . ': '.$reverse_dns."\r\n";
				$cf_msg_2 .= __( 'IP Address Lookup', 'wp-spamshield' ) . ': http://ipaddressdata.com/'.$ip."\r\n";
				if( !current_user_can( 'manage_options' ) ) {
					$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
					$cf_msg_2 .= "\r\n".$blacklist_text.' '. WP_SpamShield::blacklist_url( $ip ) ."\r\n";
				}
			}

			$cf_msg_3 .= "\r\n\r\n";

			$cf_msg = rs_wpss_prep_cf_email( $cf_msg_1.$cf_msg_2.$cf_msg_3 );
			$cf_msg_cc = rs_wpss_prep_cf_email( $cf_msg_1.$cf_msg_3 );
			/* MESSAGE CONTENT - END */

			/**
			 *  CREATE MESSAGE WPSSID - BEGIN
			 *  @since 1.7.7
			 */
			$wpsseid_args	= array( 'name' => $contact_name, 'email' => $contact_email_lc, 'url' => $contact_website_lc, 'content' => $contact_message );
			$wpsseid		= rs_wpss_get_wpss_eid( $wpsseid_args );
			$cf_mid 		= $wpsseid['eid'];
			$cf_mcid		= $wpsseid['ecid'];
			/* CREATE MESSAGE WPSSID - END */

			if( empty( $blank_field ) && empty( $invalid_value ) && empty( $message_short ) && empty( $message_long ) && empty( $message_spam ) && empty( $cf_jsck_error ) && empty( $server_blacklisted ) && empty( $cf_badrobot_error ) && empty( $user_blacklisted ) ) {

				/* SEND MESSAGE */

				/* Verify if Already Sent - to Prevent Duplicates - Added in 1.6 */
				$key_contact_forms_submitted = 'contact_forms_submitted_'.WPSS_HASH;
				if( rs_wpss_is_session_active() && empty( $_SESSION[$key_contact_forms_submitted] ) ) {
					$_SESSION[$key_contact_forms_submitted] = array();
				}
				$spamshield_wpssmid_cache = get_option( 'spamshield_wpssmid_cache' );
				if( empty( $spamshield_wpssmid_cache ) ) {
					$spamshield_wpssmid_cache = array();
				}
				if( ( !rs_wpss_is_session_active() || ( !empty( $_SESSION[$key_contact_status] ) && $_SESSION[$key_contact_status] !== 'SENT' && !WPSS_PHP::in_array( $cf_mid, $_SESSION[$key_contact_forms_submitted] ) ) ) && !WPSS_PHP::in_array( $cf_mid, $spamshield_wpssmid_cache ) ) {
					WP_SpamShield::mail( $cf_recipient, $cf_subject, $cf_msg, $cf_msg_headers, NULL, 'contact' );
					if( rs_wpss_is_session_active() ) {
						$_SESSION[$key_contact_status]				= 'SENT';
						$_SESSION[$key_contact_forms_submitted][]	= $cf_mid;
					}
					$spamshield_wpssmid_cache[]					= $cf_mid;
					update_option( 'spamshield_wpssmid_cache', $spamshield_wpssmid_cache );
				} elseif( rs_wpss_is_session_active() && WPSS_PHP::in_array( $cf_mid, $_SESSION[$key_contact_forms_submitted] ) ) {
					if( !WPSS_PHP::in_array( $cf_mid, $spamshield_wpssmid_cache ) ) {
						$spamshield_wpssmid_cache[] = $cf_mid;
						update_option( 'spamshield_wpssmid_cache', $spamshield_wpssmid_cache );
					}
					@WP_SpamShield::append_log_data( NULL, NULL, 'Duplicate contact form submission. Message not sent. WPSSMID: '.$cf_mid.' WPSSMCID: '.$cf_mcid.' [S]' );
				} elseif( WPSS_PHP::in_array( $cf_mid, $spamshield_wpssmid_cache ) ) {
					if( rs_wpss_is_session_active() ) {
						$_SESSION[$key_contact_forms_submitted][] = $cf_mid;
					}
					@WP_SpamShield::append_log_data( NULL, NULL, 'Duplicate contact form submission. Message not sent. WPSSMID: '.$cf_mid.' WPSSMCID: '.$cf_mcid.' [D]' );
				}

				$contact_response_status	= 'thank-you';
				$wpss_error_code			= 'No Error';
				rs_wpss_update_accept_status( $cf_author_data, 'a', 'Line: '.__LINE__ );
				if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
					rs_wpss_log_data( $cf_author_data, $wpss_error_code, 'contact form', $cf_msg, $cf_mid, $cf_mcid );
				}
			} else {
				$wpss_error_code = trim( $wpss_error_code );
				if( TRUE === $user_blacklisted ) {
					@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Contact form has been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
				}
				rs_wpss_update_accept_status( $cf_author_data, 'r', 'Line: '.__LINE__, $wpss_error_code );
				$contact_response_status = 'error';
				if( !empty( $spamshield_options['comment_logging'] ) ) {
					rs_wpss_log_data( $cf_author_data, $wpss_error_code, 'contact form', $cf_msg, $cf_mid, $cf_mcid );
				}
			}

			/* TEST TO PREVENT CONTACT FORM SPAM - END */

			$form_response_thank_you_message_default = '<p>' . __( 'Your message was sent successfully. Thank you.', 'wp-spamshield' ) . '</p><p>&nbsp;</p>';
			$form_response_thank_you_message = __( $form_response_thank_you_message, 'wp-spamshield' );

			$error_txt = rs_wpss_error_txt();
			$wpss_error = $error_txt.':';
			$wpss_js_disabled_msg_short = __( 'Currently you have JavaScript disabled.', 'wp-spamshield' );

			if( $contact_response_status === 'thank-you' ) {
				if( !empty( $form_response_thank_you_message ) ) { $cf_content .= '<p>'.$form_response_thank_you_message.'</p><p>&nbsp;</p>'.WPSS_EOL; }
				else { $cf_content .= $form_response_thank_you_message_default.WPSS_EOL; }
			} else {
				/* Back URL was here...moved */
				if( !empty( $message_spam ) ) {
					$contact_response_status_message_addendum .= '<noscript><br />&nbsp;<br />&bull; '.$wpss_js_disabled_msg_short.'</noscript>'.WPSS_EOL;
					$cf_content .= '<p><strong>'.$wpss_error.' <br />&nbsp;<br />'.$contact_response_status_message_addendum.'</strong></p><p>&nbsp;</p>'.WPSS_EOL;
				} else {
					$contact_response_status_message_addendum .= '<noscript><br />&nbsp;<br />&bull; '.$wpss_js_disabled_msg_short.'</noscript>'.WPSS_EOL;
					$cf_content .= '<p><strong>'.$wpss_error.' ' . __( 'Please return to the contact form and fill out all required fields.', 'wp-spamshield' );
					$cf_content .= ' ' . __( 'Please make sure JavaScript and Cookies are enabled in your browser.', 'wp-spamshield' );
					$cf_content .= '<br />&nbsp;<br />'.$contact_response_status_message_addendum.'</strong></p><p>&nbsp;</p>'.WPSS_EOL;
				}
				/* Log error messages when debug is on */
				if( rs_wpss_get_error_type( $wpss_error_code ) === 'algo'  ) {
					@WP_SpamShield::append_log_data( NULL, NULL, '$cf_content: "'.$cf_content.'" Line: '.__LINE__.' | Func: '.__FUNCTION__.' | MEM USED: ' . WP_SpamShield::wp_memory_used() . ' | VER: ' . WPSS_VERSION );
				}
			}
			$content_new		= str_replace( $content, $cf_content, $content );
			$content_shortcode	= $cf_content;
			/* CONTACT FORM BACK END - END */
		} else {
			/**
			 *  3 - ALL OTHER CASES
			 *  CONTACT FORM FRONT END - BEGIN
			 */

			$cf_content .= '<form id="wpss_contact_form" name="wpss_contact_form" action="'.$cf_action_url.'" method="post" style="text-align:left;" >'.WPSS_EOL;
			$cf_req = 'required="required" ';
			$cf_content .= '<p><label><strong>' . __( 'Name' ) . '</strong> *<br />'.WPSS_EOL;
			$cf_content .= '<input type="text" id="wpss_contact_name" name="wpss_contact_name" value="" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
			$cf_content .= '<p><label><strong>' . __( 'Email' ) . '</strong> *<br />'.WPSS_EOL;
			$cf_content .= '<input type="email" id="wpss_contact_email" name="wpss_contact_email" value="" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
			foreach( $form_include as $k => $v ) {
				if( $k === 'website' ) { $text = __( 'Website' ); $type = 'url'; $value = ''; }
				else { $text = __( WP_SpamShield::casetrans( 'ucfirst', $k ), WPSS_PLUGIN_NAME ); $type = 'text'; $value = ''; }
				if( !empty( $v['i'] ) ) {
					$cf_req = ''; $cf_content .= '<p><label><strong>'.$text.'</strong> ';
					if( !empty( $v['r'] ) ) { $cf_content .= '*'; $cf_req = 'required="required" '; }
					$cf_content .= '<br />'.WPSS_EOL.'<input type="'.$type.'" id="wpss_contact_'.$k.'" name="wpss_contact_'.$k.'" value="'.$value.'" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
				}
			}
			if( !empty( $form_include_drop_down_menu ) && !empty( $form_drop_down_menu_title ) && !empty( $form_drop_down_menu_item_1 ) && !empty( $form_drop_down_menu_item_2 ) ) {
				$cf_req = '';
				$cf_content .= '<p><label><strong>'.$form_drop_down_menu_title.'</strong> ';
				if( !empty( $form_require_drop_down_menu ) ) {
					$cf_content .= '*';
					$cf_req = 'required="required" ';
				}
				$cf_content .= '<br />'.WPSS_EOL;
				$cf_content .= '<select id="wpss_contact_drop_down_menu" name="wpss_contact_drop_down_menu" '.$cf_req.'> '.WPSS_EOL;
				$cf_content .= '<option value="" selected="selected">' . __( 'Select' ) . '</option> '.WPSS_EOL;
				$cf_content .= '<option value="">--------------------------</option> '.WPSS_EOL;
				$i = 1;
				while( $i <= 10 ) {
					if( !empty( $form_drop_down_menu_item[$i] ) ) { $cf_content .= '<option value="'.$form_drop_down_menu_item[$i].'">'.$form_drop_down_menu_item[$i].'</option> '.WPSS_EOL; }
					++$i;
				}
				$cf_content .= '</select> '.WPSS_EOL;
				$cf_content .= '</label></p>'.WPSS_EOL;
			}
			$cf_req = 'required="required" ';
			$cf_content .= '<p><label><strong>' . __( 'Subject', 'wp-spamshield' ) . '</strong> *<br />'.WPSS_EOL;
    		$cf_content .= '<input type="text" id="wpss_contact_subject" name="wpss_contact_subject" value="" size="40" '.$cf_req.'/> </label></p>'.WPSS_EOL;
			$cf_content .= '<p><label><strong>' . __( 'Message', 'wp-spamshield' ) . '</strong> *<br />'.WPSS_EOL;
			$cf_content .= '<textarea id="wpss_contact_message" name="wpss_contact_message" cols="'.$form_message_width.'" rows="'.$form_message_height.'" minlength="'.$form_message_min_length.'" maxlength="25600" '.$cf_req.'></textarea> </label></p>'.WPSS_EOL;
			$cf_content .= '<noscript><input type="hidden" name="'.WPSS_JSONST.'" value="NS2" /></noscript>'.WPSS_EOL;
			$wpss_js_disabled_msg 	= __( 'Currently you have JavaScript disabled. In order to use this contact form, please make sure JavaScript and Cookies are enabled, and reload the page.', 'wp-spamshield' );
			$wpss_js_enable_msg 	= __( 'Click here for instructions on how to enable JavaScript in your browser.', 'wp-spamshield' );
			$cf_content .= '<noscript><p><strong>'.$wpss_js_disabled_msg.'</strong> <a href="http://enable-javascript.com/" rel="nofollow external" >'.$wpss_js_enable_msg.'</a></p></noscript>'.WPSS_EOL;
			$cf_content .= '<p><input type="submit" id="wpss_contact_submit" name="wpss_contact_submit" value="' . __( 'Send Message', 'wp-spamshield' ) . '" /></p>'.WPSS_EOL;
			$cf_content .= '<p>' . sprintf( __( 'Required fields are marked %s' ), '*' ) . '</p>'.WPSS_EOL;
			$cf_content .= '<p>&nbsp;</p>'.WPSS_EOL;
			if( !empty( $promote_plugin_link ) ) {
				$sip5c = '0';
				$sip5c = substr( WPSS_SERVER_ADDR, 4, 1 ); /* Server IP 5th Char */
				$ppl_code = array( '0' => 2, '1' => 2, '2' => 2, '3' => 2, '4' => 2, '5' => 2, '6' => 1, '7' => 0, '8' => 2, '9' => 2, '.' => 2 );
				if( WP_SpamShield::preg_match( "~^[0-9\.]$~", $sip5c ) ) {
					$int = $ppl_code[$sip5c];
				} else { $int = 0; }
				$cf_content .= WPSS_Promo_Links::contact_promo_link($int).WPSS_EOL;
				$cf_content .= '<p>&nbsp;</p>'.WPSS_EOL;
			}
			$cf_content .= '</form>'.WPSS_EOL;

			/* PRE-TESTS, WILL DISABLE CONTACT FORM */
			$cf_blacklist_status = '';

			/**
			 *  TEST 0-PRE - See if user has already been blacklisted this session.
			 *  As of 1.8.4, this is only test that will shut down contact form BEFORE it's submitted.
			 */

			if( rs_wpss_ubl_cache() ) {
				$cf_blacklist_status = '3'; /* Was '2', changed to '3' in 1.8.4 */
				$wpss_error_code .= ' CF-0-PRE-BL';
			}

			$wpss_error_code = trim( $wpss_error_code );

			/* DISABLE CONTACT FORM IF BLACKLISTED */
			if( !empty( $cf_blacklist_status ) && $cache_check_status !== 'ACTIVE' ) {
				$cf_content = '<strong>' . __( 'Contact form has been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) . '</strong>';
				@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Contact form has been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
			}
			$content_new = str_replace( $contact_repl_text, $cf_content, $content );
			$content_shortcode = $cf_content;
			/* CONTACT FORM FRONT END - END */
		}
	} else { $wpss_contact_inprog = FALSE; return $content; }
	if( $get_form === 'response' ) { $content_new = str_replace($content, $cf_content, $content); $content_shortcode = $cf_content; }
	else { $content_new = str_replace( $contact_repl_text, $cf_content, $content); $content_shortcode = $cf_content; }
	if( $shortcode_check === 'shortcode' && !empty( $content_shortcode ) ) { $content_new = $content_shortcode; }
	$wpss_contact_inprog = FALSE;
	return $content_new;
}



/* BLACKLISTS - BEGIN */

function rs_wpss_ubl_cache( $method = 'chk' ) {
	/**
	 *  Check if user has been added to temporary user blacklist cache.
	 *  Helps prevent brute-force spam.
	 *  @since		1.8		Temporarily disabled in 1.8.9.2 for testing, re-enabled with modifications in 1.8.9.6
	 *  @params		$method: 'set'|'chk'
	 */
	global $wpss_ubl_cache_disable, $wpss_ubl_cache;
	if( TRUE === WPSS_TEMP_BL_DISABLE || ( rs_wpss_is_session_active() && !empty( $_SESSION['wpss_clear_blacklisted_user_'.WPSS_HASH] ) ) ) { rs_wpss_clear_ubl_cache(); $wpss_ubl_cache_disable = FALSE; return FALSE; }
	if( !empty( $wpss_ubl_cache_disable ) ) { rs_wpss_clear_ubl_cache(); $wpss_ubl_cache_disable = FALSE; return FALSE; }
	$wpss_ubl_cache_disable = WP_SpamShield::get_option( 'ubl_cache_disable' );
	if( !empty( $wpss_ubl_cache_disable ) ) { rs_wpss_clear_ubl_cache(); return FALSE; }
	$ip = WP_SpamShield::get_ip_addr();
	if( $ip === WPSS_SERVER_ADDR ) { return FALSE; } /* Skip website IP address */
	if( rs_wpss_compare_ip_cbl( $ip, WPSS_SERVER_ADDR ) ) { return FALSE; } /* Skip anything on same C-Block as website */
	if( rs_wpss_is_admin_ip( $ip ) || rs_wpss_whitelist_check( NULL, $ip ) ) { return FALSE; }
	$blacklist_status	= FALSE;
	$wpss_lang_ck_key	= 'UBR_LANG'; $wpss_lang_ck_val = 'default';
	$wpss_ubl_cache		= WP_SpamShield::get_option( 'ubl_cache' );
	if( empty( $wpss_ubl_cache ) || !is_array( $wpss_ubl_cache ) ) { $wpss_ubl_cache = array(); }
	/* Check */
	if( ( rs_wpss_is_session_active() && !empty( $_SESSION['wpss_blacklisted_user_'.WPSS_HASH] ) ) || ( !empty( $_COOKIE[$wpss_lang_ck_key] ) && $_COOKIE[$wpss_lang_ck_key] === $wpss_lang_ck_val ) || ( !empty( $ip ) && WPSS_PHP::in_array( $ip, $wpss_ubl_cache ) ) || WPSS_Filters::referrer_blacklist_chk() ) { $blacklist_status = TRUE; }
	/* Set */
	if( !empty( $blacklist_status ) || !empty( $_SERVER['WPSS_SEC_THREAT'] ) || $method === 'set' ) {
		if( count( $wpss_ubl_cache ) >= 100 ) { $wpss_ubl_cache = array(); }
		if( !empty( $ip ) && !WPSS_PHP::in_array( $ip, $wpss_ubl_cache ) ) {
			$wpss_ubl_cache[] = $ip;
		}
		if( rs_wpss_is_session_active() ) {
			$_SESSION['wpss_blacklisted_user_'.WPSS_HASH] = TRUE;
		}
		WP_SpamShield::update_option( array( 'ubl_cache' => $wpss_ubl_cache, ) );
	}
	return $blacklist_status;
}

function rs_wpss_clear_ubl_cache() {
	/**
	 *  Clear temporary user blacklist cache and delete previously set blacklist cookies and session data.
	 *  @since 1.9.0.6
	 */
	WP_SpamShield::update_option( array( 'ubl_cache' => array(), ) );
	if( rs_wpss_is_session_active() ) {
		unset( $_SESSION['wpss_blacklisted_user_'.WPSS_HASH], $_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH], $_SERVER['WPSS_SEC_THREAT'] );
		$_SESSION['wpss_clear_blacklisted_user_'.WPSS_HASH]	= TRUE;
	}
	return;
}

function rs_wpss_is_admin_ip( $ip ) {
	/**
	 *  Check if IP matches an admin IP...not to be used for authentication, but can be used to whitelist admin IPs.
	 *  @since 1.9.2
	 */
	if( empty( $ip ) || !WP_SpamShield::is_valid_ip( $ip ) ) { return FALSE; }
	$admin_ips = get_option( 'spamshield_admins' );
	if( empty( $admin_ips ) || !is_array( $admin_ips ) ) { $admin_ips = array(); update_option( 'spamshield_admins', $admin_ips ); return FALSE; }
	$admin_ips = rs_wpss_remove_expired_admins( $admin_ips );
	if( empty( $admin_ips ) ) { return FALSE; }
	if( isset( $admin_ips[$ip] ) ) { $_SERVER['WPSS_SEC_THREAT'] = FALSE; return TRUE; }
	return FALSE;
}

function rs_wpss_remove_expired_admins( $admin_ips = NULL ) {
	/**
	 *  Checks for expired admin IPs and validates IPs
	 */
	$admin_ips = empty( $admin_ips ) ? get_option( 'spamshield_admins' ) : $admin_ips;
	if( empty( $admin_ips ) || !is_array( $admin_ips ) ) { $admin_ips = array(); }
	$timenow = time();
	$timelim = 2 * WEEK_IN_SECONDS;	/* Time limit: 2 weeks */
	foreach( $admin_ips as $ip => $t ) {
		$time_since_active = rs_wpss_timer( $t, $timenow, FALSE, NULL, TRUE, TRUE );
		if( $time_since_active > $timelim || !WP_SpamShield::is_valid_ip( $ip ) ) { unset( $admin_ips[$ip] ); }
	}
	if( rs_wpss_is_user_admin() ) {
		$current_ip = WP_SpamShield::get_ip_addr();
		if( WP_SpamShield::is_valid_ip( $current_ip ) ) {
			$admin_ips[$current_ip] = $timenow;
		}
	}
	update_option( 'spamshield_admins', $admin_ips );
	return $admin_ips;
}

function rs_wpss_regexify( $var ) {
	if( is_array( $var ) ) {
		$output = array();
		foreach( $var as $i => $string ) {
			$output[] = rs_wpss_regex_alpha_replace( $string );
		}
	} elseif( is_string( $var) ) {
		$output = rs_wpss_regex_alpha_replace( $var );
	} else { $output = $var; }
	return $output;
}

function rs_wpss_regex_alpha_replace( $string ) {
	/**
	 *  Preps international text for evaluating with regex
	 *  Translates 1337 (LEET) as well
	 *  TO DO: Possibly integrate remove_accents() WP function
	 */
	$tmp_string = WPSS_Func::lower( trim( $string ) );
	$input = array( /* 26 */
		"~(^|[\s\.]+|\b\s+\b)(online|internet|web(\s*(site|page))?)\s*gambling([\s]+|\b\s+\b|$)~i", "~(^|[\s\.]+|\b\s+\b)gambling\s*(online|internet|web(\s*(site|page))?)([\s]+|\b\s+\b|$)~i",
		"~(?!^online|internet|web(\s*(site|page))?$)(^|[\s\.]+|\b\s+\b)(online|internet|web(\s*(site|page))?)([\s]+|\b\s+\b|$)~i", "~(?!^india|china|russia|ukraine$)(^|[\s\.]+|\b\s+\b)(india|china|russia|ukraine)([\s]+|\b\s+\b|$)~i",
		"~(?!^offshore|outsource|data\s+entry$)(^|[\s\.]+|\b\s+\b)(offshore|outsource|data\s+entry)([\s]+|\b\s+\b|$)~i", "~ph~i", "~(^|[\s\.]+|\b\s+\b)porn~i", "~ual([\s]+|\b\s+\b|$)~i", "~al([\s]+|\b\s+\b|$)~i", "~ay([\s]+|\b\s+\b|$)~i", "~ck([\s]+|\b\s+\b|$)~i",
		"~(ct|x)ion([\s]+|\b\s+\b|$)~i", "~te([\s]+|\b\s+\b|$)~i", "~(?!te$)e([\s]+|\b\s+\b|$)~i", "~er([\s]+|\b\s+\b|$)~i", "~ey([\s]+|\b\s+\b|$)~i", "~ic([\s]+|\b\s+\b|$)~i", "~ign([\s]+|\b\s+\b|$)~i", "~iou?r([\s]+|\b\s+\b|$)~i", "~ism([\s]+|\b\s+\b|$)~i", "~ous([\s]+|\b\s+\b|$)~i",
		"~oy([\s]+|\b\s+\b|$)~i", "~ss([\s]+|\b\s+\b|$)~i", "~tion([\s]+|\b\s+\b|$)~i", "~y([\s]+|\b\s+\b|$)~i", "~([abcdghklmnoprtw])([\s]+|\b\s+\b|$)~i",
	);
	$output = array( /* 26 */
		" (online|internet|web( (site|page))?)s? (bet(ting|s)?|blackjack|casinos?|gambl(e|ing)|poker) ", " (bet(ting|s)?|blackjack|casinos?|gambl(e|ing)|poker) (online|internet|web( (site|page))?)s? ",
		" (online|internet|web( (site|page))?)s? ", " (india|china|russia|ukraine) ", " (offshor(e(d|r|s|n|ly)?|ing)s?|outsourc(e(d|r|s|n|ly)?|ing)s?|data entry) ", "(ph|f)", "p(or|ro)n", "u(a|e)l(ly|s)? ",
		"al(ly|s)? ", "ays? ", "ck(e(d|r)?|ing)?s? ", "(ct|cc|x)ions? ", "t(e(d|r|s|n|ly)?|ing|ion)?s? ", "(e(d|r|s|n|ly)?|ing|ation)s? ", "(er|ing)s? ", "eys? ", "i(ck?|que)(s|ly)? ", "ign(e(d|r))?s? ",
		"iou?rs? ", "is(m|t) ", "ous(ly)? ", "oys? ", "ss(es)? ", "(t|c)ions? ", "(y|ie(d|r|s)?) ", "$1s? ",
	);
	$tmp_string = preg_replace( $input, $output, $tmp_string );
	$tmp_string = WPSS_Func::lower( trim( $tmp_string ) );
	$the_replacements = array(
		" "	=> "([\s\.,\;\:\?\!\/\|\@\(\)\[\]\{\}\-_]*)", "-" => "([\s\.,\;\:\?\!\/\|\@\(\)\[\]\{\}\-_]*)", "a"	=> "([a4\@àáâãäåæāăą])", "b" => "([b8ßƀƃƅ])", "c" => "([c¢©çćĉċč])", "d" => "([dďđ])",
		"e" => "([e3èéêëēĕėęěǝ])", "g" => "([g9ĝğġģ])", "h" => "([hĥħ])", "i" => "([i1yìíîïĩīĭį])", "k" => "([kķĸ])", "j" => "([jĵ])", "l" => "([l1ĺļľŀł])", "n" => "([nñńņňŉ])", "o" => "([o0ðòóôõöōŏőœ])",
		"r"	=> "([r®ŕŗř])", "s"	=> "([s5\$śŝşš])", "t" => "([t7ťŧţ])", "u" => "([uùúûüũūŭůűų])", "w" => "([wŵ])", "y" => "([y1i¥ýÿŷ])", "z" => "([z2sźżž])",
	);
	if( !WP_SpamShield::preg_match( "~^NOMOD~i", $tmp_string ) ){
		$new_string = strtr( $tmp_string, $the_replacements );
	} else {
		$new_string = preg_replace( "~^NOMOD~i", "", $tmp_string );
	}
	return $new_string;
}

/**
 *	The Magic Parser
 *	Magically parse a large string of text for a number of keyphrases
 *	The "magic" is that this will check for all kinds of text variations, accents, plurals, 1337 (LEET), etc.
 *	Extremely accurate...same mechanism used in the WPSS_Filters::anchortxt_blacklist_chk() function
 *	@since			1.9.7.8
 *	@param			array	$keyphrase_needles	The array containing keyphrases to search haystack for
 *	@param			string	$haystack			The string of text to search. This works well for large chunks of text such as contact form submissions.
 *	@return			bool	TRUE if haystack contains any of the keyphrase needles, FALSE if it does not
 */
function rs_wpss_magic_parser( $keyphrase_needles = array(), $haystack = NULL, $count = FALSE ) {
	if( empty( $keyphrase_needles ) || empty( $haystack ) || !is_array( $keyphrase_needles ) || !is_string( $haystack ) ) { return FALSE; }
	foreach( $keyphrase_needles as $i => $keyphrase_needle ) {
		$keyphrase_needle_rgx	= rs_wpss_regexify( $keyphrase_needle );
		$regex_check_phrase		= WPSS_Filters::get_rgx_ptrn( $keyphrase_needle_rgx, '', 'authorkw' );
		if( TRUE === $count ) {
			return ( @preg_match_all( $regex_check_phrase, $haystack ) );
		} elseif( WP_SpamShield::preg_match( $regex_check_phrase, $haystack ) ) { return TRUE; }
	}
	return FALSE;
}

/* BLACKLISTS - END */

function rs_wpss_precheck_pingback_spam( $pagelinkedfrom, $pagelinkedto ) {
	/**
	 *  This will knock out almost 100% of Pingback Spam, faster than WordPress' standard checks, with lower server load
	 *  @since 1.9.8.5
	 */
	if( !defined( 'WPSS_XMLRPC_PINGBACK' ) ) { define( 'WPSS_XMLRPC_PINGBACK', TRUE ); }
	global $wpss_pingback_inprog; $wpss_pingback_inprog = TRUE;
	if( rs_wpss_is_local_request() ) { return $pagelinkedfrom; }
	$comment_post_ID = url_to_postid( $pagelinkedto );
	$commentdata = array(
		'comment_type'					=> 'pingback',
		'comment_author'				=> '',
		'comment_author_email'			=> '',
		'comment_author_url'			=> $pagelinkedfrom,
		'comment_content'				=> '',
		'comment_post_url'				=> $pagelinkedto,
		'comment_post_ID'				=> $comment_post_ID,
		'comment_post_title'			=> @get_the_title( $comment_post_ID ),
		'comment_post_type'				=> @get_post_type( $comment_post_ID ),
		'comment_post_comments_open'	=> @comments_open( $comment_post_ID ),
		'comment_post_pings_open'		=> @pings_open( $comment_post_ID ),
		'comment_wpss_cid'				=> '',
		'comment_wpss_ccid'				=> '',
		'body_content_len'				=> 0,
		);

	/* Timer Start - Comment Processing */
	$commentdata['start_time_comment_processing'] = $commentdata['start_time_content_filter'] = microtime( TRUE );

	$spamshield_options = WP_SpamShield::get_option();

	/* 1ST - Skiddie UA Check */
	if( WPSS_Filters::skiddie_ua_check( NULL, 'pingback' ) ) {
		$content_filter_status	= $commentdata['content_filter_status'] = 3;
		$wpss_error_code		= $commentdata['wpss_error_code'] = 'PUA1004';
		$commentdata = rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
		return '';
	}

	/* 2ND - TR2 - Pingback False IP Check */
	$commentdata			= rs_wpss_trackback_ip_filter( $commentdata, $spamshield_options );
	$content_filter_status	= $commentdata['content_filter_status'];
	$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
	if( !empty( $content_filter_status ) ) {
		rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
		return '';
	}

	/* 3RD - Pingback Pre Filter */
	$commentdata 			= rs_wpss_pingback_pre_filter( $commentdata, $spamshield_options );
	$content_filter_status 	= $commentdata['content_filter_status'];
	$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
	if( !empty( $content_filter_status ) ) {
		rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
		return '';
	}

	return $pagelinkedfrom;
}

function rs_wpss_check_comment_spam( $commentdata ) {
	/* Timer Start - Comment Processing */
	$commentdata['start_time_comment_processing'] = microtime( TRUE );

	$spamshield_options = WP_SpamShield::get_option();

	$wpss_error_code 	= $wpss_js_key_bypass = '';
	$bypass_tests 		= FALSE;

	/* Add New Tests for Logging - BEGIN */
	$post_jsonst 		= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_jsonst_lc 	= WPSS_Func::lower( $post_jsonst );
	$post_ref2xjs_lc 	= WPSS_Func::lower( $post_ref2xjs ); /* For both logging and testing */
	$ref2xjs_lc			= WPSS_Func::lower( WPSS_REF2XJS ); /* For testing later on, not logging */
	if( !empty( $post_ref2xjs ) ) {
		$ref2xJS = WPSS_Func::lower( addslashes( urldecode( $post_ref2xjs ) ) );
		$ref2xJS = str_replace( '%3a', ':', $ref2xJS );
		$ref2xJS = str_replace( ' ', '+', $ref2xJS );
		$wpss_javascript_page_referrer = esc_url_raw( $ref2xJS );
	} else { $wpss_javascript_page_referrer = '[None]'; }

	if( $post_jsonst_lc  === 'ns1' || $post_jsonst_lc  === 'ns2' || $post_jsonst_lc  === 'ns3' || $post_jsonst_lc  === 'ns4' ) { $wpss_jsonst = $post_jsonst; } else { $wpss_jsonst = '[None]'; }

	$commentdata['comment_post_title']			= @get_the_title( $commentdata['comment_post_ID'] );
	$commentdata['comment_post_url']			= @get_permalink( $commentdata['comment_post_ID'] );
	$commentdata['comment_post_type']			= @get_post_type( $commentdata['comment_post_ID'] );
	$commentdata['comment_post_comments_open']	= @comments_open( $commentdata['comment_post_ID'] );
	$commentdata['comment_post_pings_open']		= @pings_open( $commentdata['comment_post_ID'] );
	$commentdata['javascript_page_referrer']	= $wpss_javascript_page_referrer;
	$commentdata['jsonst']						= $wpss_jsonst;

	$wpss_comment_author_email_lc				= WPSS_Func::lower( $commentdata['comment_author_email'] );

	$commentdata_comment_content_lc_unslash		= stripslashes( WPSS_Func::lower( $commentdata['comment_content'] ) );
	$commentdata['body_content_len']			= rs_wpss_strlen( $commentdata_comment_content_lc_unslash );
	unset( $commentdata_comment_content_lc_unslash, $wpss_javascript_page_referrer, $wpss_jsonst );

	/**
	 *  CREATE COMMENT WPSSID - BEGIN
	 *  @since 1.7.7
	 */
	$wpsseid_args 						= array( 'name' => $commentdata['comment_author'], 'email' => $commentdata['comment_author_email'], 'url' => $commentdata['comment_author_url'], 'content' => $commentdata['comment_content'] );
	$wpsseid 							= rs_wpss_get_wpss_eid( $wpsseid_args );
	$commentdata['comment_wpss_cid']	= $wpsseid['eid'];
	$commentdata['comment_wpss_ccid']	= $wpsseid['ecid'];
	/* CREATE COMMENT WPSSID - END */

	/* Add New Tests for Logging - END */


	/* User Authorization - BEGIN */

	/* Don't use elseif() for these tests - we are stacking $wpss_error_code_addendum results */

	$wpss_error_code_addendum = '';
	/* 1) is_admin() - If in Admin, don't test so user can respond directly to comments through admin */
	if( is_admin() ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 1-ADMIN';
		}

	/* 2) current_user_can( 'moderate_comments' ) - If user has Admin or Editor level access, don't test */
	if( current_user_can( 'moderate_comments' ) ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 2-MODCOM';
		}

	if( current_user_can( 'publish_posts' ) ) {
		/* Added Author Requirement - current_user_can( 'publish_posts' ) - v 1.4.7 */
		global $current_user;
		$current_user			= wp_get_current_user();
		$wpss_display_name 		= $current_user->display_name;
		$wpss_user_firstname 	= $current_user->user_firstname;
		$wpss_user_lastname 	= $current_user->user_lastname;
		$wpss_user_email		= $current_user->user_email;
		$wpss_user_url			= $current_user->user_url;
		$wpss_user_login 		= $current_user->user_login;
		$wpss_user_id	 		= $current_user->ID;

		if( !empty( $wpss_user_email ) ) {
			$wpss_user_email_parts				= explode( '@', $wpss_user_email );
			if( !empty( $wpss_user_email_parts[1] ) ) {
			$wpss_user_email_domain				= $wpss_user_email_parts[1];
			} else { $wpss_user_email_domain 		= ''; }
			$wpss_user_email_domain_no_w3		= preg_replace( "~^(ww[w0-9]|m)\.~i", "", $wpss_user_email_domain );
			$wpss_user_email_domain_no_w3_rgx	= rs_wpss_preg_quote( $wpss_user_email_domain_no_w3 );
		}
		if( !empty( $wpss_user_url ) ) {
			$wpss_user_domain					= rs_wpss_get_domain( $wpss_user_url );
			$wpss_user_domain_no_w3 			= preg_replace( "~^(ww[w0-9]|m)\.~i", "", $wpss_user_domain );
			$wpss_user_domain_no_w3_rgx			= rs_wpss_preg_quote( $wpss_user_domain_no_w3 );
		}
		$wpss_server_domain_no_w3 				= preg_replace( "~^(ww[w0-9]|m)\.~i", "", WPSS_SERVER_NAME );

		/* 3) If user is logged in, Author, and email is from same domain as website, don't test */
		if( !empty( $wpss_user_email_domain_no_w3 ) && WP_SpamShield::preg_match( "~(^|\.)".$wpss_user_email_domain_no_w3_rgx."$~i", $wpss_server_domain_no_w3 ) ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 3-AEMLDOM';
		}
		/* 4) If user is logged in, Author, and url is same domain as website, don't test */
		if( !empty( $wpss_user_domain_no_w3 ) && WP_SpamShield::preg_match( "~(^|\.)".$wpss_user_domain_no_w3_rgx."$~i", $wpss_server_domain_no_w3 ) ) {
			$bypass_tests = TRUE;
			$wpss_error_code_addendum .= ' 4-AURLDOM';
		}

	}

	/* 5) Whitelist */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check( $wpss_comment_author_email_lc ) ) {
		$bypass_tests = TRUE;
		$wpss_error_code_addendum .= ' 5-WHITELIST';
	}

	if( TRUE === $bypass_tests ) {
		$wpss_error_code = 'No Error';
		/* $wpss_error_code .= $wpss_error_code_addendum; */
	}
	/* Timer End - Part 1 */
	$wpss_end_time_part_1 = microtime( TRUE );
	$wpss_total_time_part_1 = rs_wpss_timer( $commentdata['start_time_comment_processing'], $wpss_end_time_part_1, FALSE, 6, TRUE );
	$commentdata['total_time_part_1'] = $wpss_total_time_part_1;
	/* User Authorization - END */

	if( TRUE !== $bypass_tests ) {
		/* ONLY IF NOT ADMINS, EDITORS - BEGIN */

		/* First Do JS/Cookies Test */

		/**
		 *  JS/Cookies TEST - BEGIN
		 *  Rework this
		 */
		if( $commentdata['comment_type'] !== 'trackback' && $commentdata['comment_type'] !== 'pingback' ) {
			/* If Comment is not a trackback or pingback */
			global $wpss_comment_inprog; $wpss_comment_inprog = TRUE;

			/* Timer Start - JS/Cookies Filter */
			$wpss_start_time_jsck_filter			= microtime( TRUE );
			$commentdata['start_time_jsck_filter']	= $wpss_start_time_jsck_filter;

			$wpss_ck_key_bypass = $wpss_js_key_bypass = FALSE;
			$jp_comments = ( WPSS_Compatibility::is_jp_active() && !empty( $_GET['for'] ) && 'jetpack' === $_GET['for'] );
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { $wpss_ck_key_bypass = TRUE; }
			if( FALSE === $wpss_ck_key_bypass ) {
				if( empty( $_COOKIE ) ) {
					/* JS/CK Test 00 - Added 1.9.8.5 - Failed 0 Cookie Test - Part of the JavaScript/Cookies Layer */
					$wpss_error_code .= ' COOKIE-0';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
				if( rs_wpss_ck_sess_o() ) {
					/* JS/CK Test 01 - Added 1.9.8.5 - Only has SESSION Cookie */
					$wpss_error_code .= ' COOKIE-01';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
			}
			$wpss_key_values 		= rs_wpss_get_key_values(); extract( $wpss_key_values );
			$wpss_jsck_cookie_val	= !empty( $_COOKIE[$wpss_ck_key] )	? $_COOKIE[$wpss_ck_key]	: '';
			$wpss_jsck_field_val	= !empty( $_POST[$wpss_js_key] )	? $_POST[$wpss_js_key]		: '';
			$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] )	? $_POST[$wpss_jq_key]		: '';

			if( rs_wpss_admin_jp_fix( TRUE ) || TRUE === $jp_comments ) { /* Check if JP Comments active - made compatible 1.9.2 */
				$wpss_js_key_bypass = TRUE;
			}
			if( TRUE === $jp_comments ) {
				if( ( empty( $_COOKIE[WPSS_SJECT] ) || $_COOKIE[WPSS_SJECT] !== WPSS_CKON ) && empty( $_COOKIE[JCS_INENTIM] ) ) {
					$wpss_error_code .= ' COOKIE-1-JP';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
			} else {
				if( FALSE === $wpss_ck_key_bypass ) {
					if( $wpss_jsck_cookie_val !== $wpss_ck_val ) {
						/**
						 *  JS/CK Test 01
						 *  Failed the Cookie Test - Part of the JavaScript/Cookies Layer
						 */
						$wpss_error_code .= ' COOKIE-1';
						$commentdata['wpss_error_code'] = trim( $wpss_error_code );
						rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
					}
				}
			}
			/**
			 *  ALSO PART OF BAD ROBOTS TEST - BEGIN - Will have to add modifier for JS/CK TESTS only and add callback & args to WPSS_Filters::bad_robot_blacklist_chk() before it can be implemented here
			 *  Test JS Referrer for Obvious Scraping Spambots
			 */
			if( !empty( $post_ref2xjs ) && strpos( $post_ref2xjs_lc, $ref2xjs_lc ) !== FALSE ) {
				/* JS/CK Test 02 */
				$wpss_error_code .= ' REF-2-1023-1';
				$commentdata['wpss_error_code'] = trim( $wpss_error_code );
				rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
			}
			/* ALSO PART OF BAD ROBOTS TEST - END */
			/* JavaScript Off NoScript Test - JSONST - will only be sent by Scraping Spambots */
			if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc  === 'ns2' || $post_jsonst_lc  === 'ns3' || $post_jsonst_lc  === 'ns4' ) {
				/* JS/CK Test 03 */
				$wpss_error_code .= ' JSONST-1000-1';
				$commentdata['wpss_error_code'] = trim( $wpss_error_code );
				rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
			}

			if( FALSE === $wpss_js_key_bypass ) {
				if( $wpss_jsck_field_val !== $wpss_js_val ) {
					/**
					 *  JS/CK Test 04
					 *  Failed the FVFJS Test
					 *  Part of the JavaScript/Cookies Layer
					 */
					$wpss_error_code .= ' FVFJS-1';
					$commentdata['wpss_error_code'] = trim( $wpss_error_code );
					rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
				}
			}
			/* Timer End - JS/Cookies Filter */
			$wpss_end_time_jsck_filter				= microtime( TRUE );
			$wpss_total_time_jsck_filter			= rs_wpss_timer( $wpss_start_time_jsck_filter, $wpss_end_time_jsck_filter, FALSE, 6, TRUE );
			$commentdata['total_time_jsck_filter']	= $wpss_total_time_jsck_filter;
			if( !empty( $wpss_error_code ) ) {
				$commentdata['wpss_error_code'] = trim( $wpss_error_code );
				rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code );
			}
		}
		/* JS/Cookies TEST - END */

		/* 2ND - Trackbacks/Pingbacks */
		if( 'trackback' === $commentdata['comment_type'] || 'pingback' === $commentdata['comment_type'] ) {
			$wpss_comment_inprog = FALSE;
			/* 1ST - TR1 - Trackback/Pingback Content Filter */
			$commentdata 				= rs_wpss_trackback_content_filter( $commentdata, $spamshield_options );
			$content_filter_status 		= $commentdata['content_filter_status'];
			$wpss_error_code			= trim( $commentdata['wpss_error_code'] );
			if( !empty( $content_filter_status ) ) { /* Same actions as TR2 - Needs Trackback exit filter similar to rs_wpss_exit_jsck_filter() */
				rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
				if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
				rs_wpss_denied_post_response( $commentdata, 'vague' );
			}
			/* 2ND - TR2 - Trackback False IP Check */
			if( 'trackback' === $commentdata['comment_type'] ) {
				$commentdata			= rs_wpss_trackback_ip_filter( $commentdata, $spamshield_options );
				$content_filter_status	= $commentdata['content_filter_status'];
				$wpss_error_code		= trim( $commentdata['wpss_error_code'] );
				if( !empty( $content_filter_status ) ) { /* Same actions as TR1 - Needs Trackback exit filter similar to rs_wpss_exit_jsck_filter() */
					rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
					if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
					rs_wpss_denied_post_response( $commentdata, 'vague' );
				}
			}
		} else { /* Comments only - No Pingbacks or Trackbacks */
			$wpss_comment_inprog = TRUE;

			/* 3RD - (Was 1ST), test if comment is too short or too long */
			$commentdata				= rs_wpss_content_length( $commentdata, $spamshield_options );
			$content_short_status		= $commentdata['content_short_status'];
			$content_long_status		= $commentdata['content_long_status']; /* Added 1.7.9 */

			if( empty( $content_short_status ) && empty( $content_long_status ) ) {
				/* If it doesn't fail the comment length tests, run it through the content filter. This is where the magic happens... */

				/* 4TH - Full Comment Content Filter (No Pingbacks or Trackbacks) */
				$commentdata			= rs_wpss_comment_content_filter( $commentdata, $spamshield_options );
				$content_filter_status	= $commentdata['content_filter_status'];
				/* Now we have a lot more power to work with */
			}
			$wpss_comment_inprog = FALSE;
		}

		$wpss_error_code = trim( $commentdata['wpss_error_code'] );

		if( !empty( $content_short_status ) ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'short' );
		} elseif( !empty( $content_long_status ) ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'long' );
		} elseif( $content_filter_status == '2' ) {
			/**
			 *  Up to 1.8.2: Only comment filter using this is WPSS_Filters::revdns_filter(). Used in WPSS_Filters::bad_robot_blacklist_chk() and CF.
			 *  1.8.4 on: No filters are using this.
			 */
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'network' );
		} elseif( $content_filter_status == '3' ) { /* Added 1.8 */
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'vague' );
		} elseif( $content_filter_status == '10' ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'proxy' );
		} elseif( $content_filter_status == '100' ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_response( $commentdata, 'blacklist' );
		} elseif( !empty( $content_filter_status ) ) {
			rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
			if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
			rs_wpss_denied_post_content_filter( $commentdata );
		}

		/* ONLY IF NOT ADMINS, EDITORS - END */
	}

	/* No Error - Not a Spam Comment - Accepted */
	if( !empty( $spamshield_options['comment_logging_all'] ) && ( empty( $wpss_error_code ) || ( !empty( $wpss_error_code ) && strpos( $wpss_error_code, 'No Error' ) === 0 ) ) ) {
		$wpss_error_code = 'No Error';
		$commentdata['wpss_error_code'] = trim( $wpss_error_code );
		rs_wpss_update_accept_status( $commentdata, 'a', 'Line: '.__LINE__ );
		rs_wpss_log_data( $commentdata, $wpss_error_code );
	}
	$wpss_comment_inprog = FALSE;
	return $commentdata;
}


/* REJECT BLOCKED SPAM COMMENTS - BEGIN */

function rs_wpss_denied_post_js_cookie( $commentdata = array() ) {
	$error_txt	= rs_wpss_error_txt();
	$error_msg_alt	= '<strong>'.$error_txt.':</strong> ' . __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' );
	$error_msg	= '<span style="font-size:12px;"><strong>'.$error_txt.': ' . __( 'JavaScript and Cookies are required in order to post a comment.', 'wp-spamshield' ) . '</strong><br /><br />'.WPSS_EOL;
	$error_msg	.= '<noscript>' . __( 'Status: JavaScript is currently disabled.', 'wp-spamshield' ) . '<br /><br /></noscript>'.WPSS_EOL;
	$error_msg	.= '<strong>' . __( 'Please be sure JavaScript and Cookies are enabled in your browser. Then, please hit the back button on your browser, and try posting your comment again. (You may need to reload the page.)', 'wp-spamshield' ) . '</strong><br /><br />'.WPSS_EOL;
	$error_msg	.= '<br /><hr noshade />'.WPSS_EOL;
	if( ( !empty( $_COOKIE[WPSS_SJECT] ) && $_COOKIE[WPSS_SJECT] === WPSS_CKON ) || !empty( $_COOKIE['JCS_INENTIM'] ) ) {
		$error_msg	.= __( 'If you feel you have received this message in error (for example if JavaScript and Cookies are in fact enabled and you have tried to post several times), there is most likely a technical problem (could be a plugin conflict or misconfiguration). Please contact the author of this site, and let them know they need to look into it.', 'wp-spamshield' ) . '<br />'.WPSS_EOL;
		$error_msg	.= '<hr noshade /><br />'.WPSS_EOL;
	}
	$error_msg	.= '</span>'.WPSS_EOL;
	WP_SpamShield::wp_die( $error_msg, TRUE );
}

function rs_wpss_denied_post_content_filter( $commentdata = array() ) {
	$error_txt = rs_wpss_error_txt();
	$error_msg_alt = '<span style="font-size:12px;"><strong>'.$error_txt.':</strong> ' . __( 'Comments have been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) . '</span>'; /* Stop spammers without revealing why. */
	$error_msg = '<span style="font-size:12px;"><strong>'.$error_txt.': ' . __( 'Your comment appears to be spam.', 'wp-spamshield' ) . '</strong><br /><br />'.WPSS_EOL;
	if( $commentdata['wpss_error_code'] === '10500A-BL' && strpos( $commentdata['comment_author'], '@' ) !== FALSE ) {
		$error_msg .= sprintf( __( '"%1$s" appears to be spam. Please enter a different value in the <strong> %2$s </strong> field.', 'wp-spamshield' ), sanitize_text_field($commentdata['comment_author']), __( 'Name' ) ) . '<br /><br />'.WPSS_EOL;
	}
	$error_msg .= __( 'Please go back and check all parts of your comment submission (including name, email, website, and comment content).', 'wp-spamshield' ) . '</span>'.WPSS_EOL;
	if( rs_wpss_is_user_logged_in() ) {
		$error_msg .= '<br /><br />'.WPSS_EOL;
		$error_msg .= '<span style="font-size:12px;">' . __( 'If you are a logged in user, and you are seeing this message repeatedly, then you may need to check your registered user information for spam data.', 'wp-spamshield' ) . '</span>'.WPSS_EOL;
	}
	WP_SpamShield::wp_die( $error_msg, TRUE );
}

function rs_wpss_denied_post_response( $commentdata = array(), $error ) {
	$error_txt = rs_wpss_error_txt();
	$error_msgs = array(
		'short'		=> '<span style="font-size:12px;"><strong>'.$error_txt.':</strong> ' . apply_filters( 'wpss_blocked_comment_response_short', __( 'Your comment was too short. Please go back and try your comment again.', 'wp-spamshield' ) ) . '</span>',
		'long'		=> '<span style="font-size:12px;"><strong>'.$error_txt.':</strong> ' . apply_filters( 'wpss_blocked_comment_response_long', __( 'Your comment was too long. Please go back and try your comment again.', 'wp-spamshield' ) ) . '</span>',
		'network'	=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_network', __( 'Your location has been identified as part of a reported spam network. Comments have been disabled to prevent spam.', 'wp-spamshield' ) ) . '</strong></span>',
		'vague'		=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_vague', __( 'Comments have been temporarily disabled to prevent spam. Please try again later.', 'wp-spamshield' ) ) . '</strong><br /><br /></span>',
		'proxy'		=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_proxy_l1', __( 'Your comment has been blocked because the website owner has set their spam filter to not allow comments from users behind proxies.', 'wp-spamshield' ) ) . '</strong><br /><br />' . apply_filters( 'wpss_blocked_comment_response_proxy_l2', __( 'If you are a regular commenter or you feel that your comment should not have been blocked, please contact the site owner and ask them to modify this setting.', 'wp-spamshield' ) ) . '</span>',
		'blacklist'	=> '<span style="font-size:12px;"><strong>'.$error_txt.': ' . apply_filters( 'wpss_blocked_comment_response_blacklist_l1', __( 'Your comment has been blocked based on the website owner\'s blacklist settings.', 'wp-spamshield' ) ) . '</strong><br /><br />' . apply_filters( 'wpss_blocked_comment_response_blacklist_l2', __( 'If you feel this is in error, please contact the site owner by some other method.', 'wp-spamshield' ) ) . '</span>',
	);
	$error_msg = $error_msgs[$error];
	WP_SpamShield::wp_die( $error_msg, TRUE );
}

/* REJECT BLOCKED SPAM COMMENTS - END */


/* COMMENT SPAM FILTERS - BEGIN */

function rs_wpss_content_length( $commentdata, $spamshield_options ) {
	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime( TRUE );
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}
	$content_short_status 						= $content_long_status = $wpss_error_code = ''; /* Must go before tests */
	$commentdata_comment_content				= $commentdata['comment_content'];
	$commentdata_comment_content_lc				= WPSS_Func::lower( $commentdata_comment_content );
	$commentdata_comment_content_lc_unslash	= stripslashes($commentdata_comment_content_lc);
	$comment_length 							= $commentdata['body_content_len'];
	$comment_min_length							= ( !empty( $spamshield_options['comment_min_length'] ) && is_int( $spamshield_options['comment_min_length'] ) ) ? $spamshield_options['comment_min_length'] : '15';
	$comment_max_length 						= 15360; /* 15kb */
	$commentdata_comment_type					= $commentdata['comment_type'];
	if( $commentdata_comment_type !== 'trackback' && $commentdata_comment_type !== 'pingback' ) {
		if( $comment_length < $comment_min_length ) {
			$content_short_status = TRUE; $wpss_error_code .= ' SHORT15-'.$comment_min_length;
		}
		if( $comment_length > $comment_max_length ) {
			$content_long_status = TRUE; $wpss_error_code .= ' LONG15K';
		}
	}
	if( !empty( $wpss_error_code ) ) {
		$wpss_error_code = trim( $wpss_error_code );
		/* Timer End - Content Filter */
		$wpss_end_time_content_filter 				= microtime( TRUE );
		$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
		$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	}
	$commentdata['content_short_status'] 		= $content_short_status;
	$commentdata['content_long_status'] 		= $content_long_status;
	$commentdata['wpss_error_code'] 			= $wpss_error_code;
	return $commentdata;
}

function rs_wpss_exit_jsck_filter( $commentdata, $spamshield_options, $wpss_error_code ) {
	/**
	 *  Exit JS/CK Filter
	 *  This fires when a JavaScript/Cookies spam test is failed.
	 */
	$commentdata['wpss_error_code']				= $wpss_error_code = trim( $wpss_error_code );
	/* Timer End - Content Filter */
	$wpss_end_time_jsck_filter 					= microtime( TRUE );
	$wpss_total_time_jsck_filter 				= rs_wpss_timer( $commentdata['start_time_jsck_filter'], $wpss_end_time_jsck_filter, FALSE, 6, TRUE );
	$commentdata['total_time_jsck_filter']		= $wpss_total_time_jsck_filter;
	rs_wpss_update_accept_status( $commentdata, 'r', 'Line: '.__LINE__, $wpss_error_code );
	if( !empty( $spamshield_options['comment_logging'] ) ) { rs_wpss_log_data( $commentdata, $wpss_error_code ); }
	rs_wpss_denied_post_js_cookie( $commentdata );
}

function rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status ) {
	/**
	 *  Exit Content Filter
	 *  This fires when an algo spam test is failed.
	 */
	$commentdata['wpss_error_code']				= $wpss_error_code = trim( $wpss_error_code );
	if( ( FALSE !== strpos( $wpss_error_code, '0-BL' ) || FALSE !== strpos( $wpss_error_code, '0-ECBL' ) ) && FALSE === strpos( $wpss_error_code, '00-BL' ) && FALSE === strpos( $wpss_error_code, '00-ECBL' ) ) {
		@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Comments have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
	}
	/* Timer End - Content Filter */
	$wpss_end_time_content_filter 				= microtime( TRUE );
	$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
	$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	$commentdata['content_filter_status']		= $content_filter_status;
	return $commentdata;
}

function rs_wpss_pingback_pre_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Pingback Pre Filter
	 *  This will knock out most of Pingback Spam, faster than WordPress' standard checks, with lower server load
	 */

	$content_filter_status 						= $wpss_error_code = ''; /* Must go before tests */
	$block_all_pingbacks 						= $spamshield_options['block_all_pingbacks'];
	$commentdata_comment_type					= $commentdata['comment_type'];
	$commentdata_comment_author					= $commentdata['comment_author'];
	$commentdata_comment_author_unslash			= stripslashes( $commentdata_comment_author );
	$commentdata_comment_author_lc				= WPSS_Func::lower( $commentdata_comment_author );
	$commentdata_comment_author_lc_unslash		= stripslashes( $commentdata_comment_author_lc );
	$commentdata_comment_author_url				= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc			= WPSS_Func::lower( $commentdata_comment_author_url );
	$commentdata_comment_author_url_domain_lc	= rs_wpss_get_domain( $commentdata_comment_author_url_lc );
	$commentdata_comment_content				= $commentdata['comment_content'];
	$commentdata_comment_content_lc				= WPSS_Func::lower( $commentdata_comment_content );
	$commentdata_comment_content_lc_unslash		= stripslashes( $commentdata_comment_content_lc );

	$commentdata_remote_addr					= $ip = WP_SpamShield::get_ip_addr();
	$commentdata_remote_addr_lc					= WPSS_Func::lower( $commentdata_remote_addr );
	$commentdata_user_agent 					= rs_wpss_get_user_agent( TRUE, FALSE );
	$commentdata_user_agent_lc					= WPSS_Func::lower( $commentdata_user_agent );
	$commentdata_user_agent_lc_word_count 		= rs_wpss_count_words( $commentdata_user_agent_lc );

	if( !empty( $block_all_pingbacks ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' P-BLOCKING-PINGBACKS';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	/* REVDNS FILTER */
	$rev_dns_filter_data = WPSS_Filters::revdns_filter( 'trackback', $content_filter_status, $ip, $reverse_dns_lc, $commentdata_comment_author_lc_unslash );
	$revdns_blacklisted = $rev_dns_filter_data['blacklisted'];
	if( !empty( $revdns_blacklisted ) ) {
		$content_filter_status = $rev_dns_filter_data['status'];
		$wpss_error_code .= $rev_dns_filter_data['error_code'];
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Blacklisted Domains Check */
	if( WPSS_Filters::domain_blacklist_chk( $commentdata_comment_author_url_domain_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' P-10500AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check for URL Shorteners, Bogus Long URLs, Social Media, and Misc Spam Domains */
	if( WPSS_Filters::at_link_spam_url_chk( $commentdata_comment_author_url_lc, 'trackback' ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' P-10510AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	$commentdata['wpss_error_code'] = trim( $wpss_error_code );
	$commentdata['content_filter_status'] = $content_filter_status;
	return $commentdata;

}

function rs_wpss_trackback_content_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Trackback Content Filter
	 *  This will knock out 98% of Trackback Spam
	 *  Keeping this separate and before trackback IP filter because it's fast
	 *  If passes this, then next filter will take out the rest
	 */

	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime( TRUE );
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}

	$content_filter_status 						= $wpss_error_code = ''; /* Must go before tests */

	$block_all_trackbacks 						= $spamshield_options['block_all_trackbacks'];
	$block_all_pingbacks 						= $spamshield_options['block_all_pingbacks'];

	$commentdata_comment_type					= $commentdata['comment_type'];

	$commentdata_comment_author					= $commentdata['comment_author'];
	$commentdata_comment_author_unslash			= stripslashes( $commentdata_comment_author );
	$commentdata_comment_author_lc				= WPSS_Func::lower( $commentdata_comment_author );
	$commentdata_comment_author_lc_unslash		= stripslashes( $commentdata_comment_author_lc );
	$commentdata_comment_author_url				= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc			= WPSS_Func::lower( $commentdata_comment_author_url );
	$commentdata_comment_author_url_domain_lc	= rs_wpss_get_domain( $commentdata_comment_author_url_lc );
	$commentdata_comment_content				= $commentdata['comment_content'];
	$commentdata_comment_content_unslash		= stripslashes( $commentdata_comment_content );
	$commentdata_comment_content_lc				= WPSS_Func::lower( $commentdata_comment_content );
	$commentdata_comment_content_lc_unslash		= stripslashes( $commentdata_comment_content_lc );
	$commentdata_comment_content_uc				= WPSS_Func::upper( $commentdata_comment_content );
	$commentdata_comment_content_uc_unslash		= stripslashes( $commentdata_comment_content_uc );
	/**
	 *  For 1-HT Test - Other version using rs_wpss_parse_links() is more robust but not needed yet - current implementation is faster.
	 */

	$commentdata_remote_addr					= WP_SpamShield::get_ip_addr();
	$commentdata_remote_addr_lc					= WPSS_Func::lower( $commentdata_remote_addr );
	$commentdata_user_agent 					= rs_wpss_get_user_agent( TRUE, FALSE );
	$commentdata_user_agent_lc					= WPSS_Func::lower( $commentdata_user_agent );
	$commentdata_user_agent_lc_word_count 		= rs_wpss_count_words( $commentdata_user_agent_lc );
	$trackback_length 							= $commentdata['body_content_len'];
	$trackback_max_length 						= 3072; /* 3kb */

	/* TO DO: Re-do this section - some old that needs to be regexified. BEGIN*/
	$commentdata_comment_author_lc_spam_strong = '<strong>'.$commentdata_comment_author_lc_unslash.'</strong>'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot1 = '...</strong>'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot2 = '...</b>'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot3 = '<strong>...'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_strong_dot4 = '<b>...'; /* Trackbacks */
	$commentdata_comment_author_lc_spam_a1 = $commentdata_comment_author_lc_unslash.'</a>'; /* Trackbacks/Pingbacks */
	$commentdata_comment_author_lc_spam_a2 = $commentdata_comment_author_lc_unslash.' </a>'; /* Trackbacks/Pingbacks */
	/* TO DO: Re-do this section - some old that needs to be regexified. END*/

	if( rs_wpss_is_local_request() && $commentdata['comment_type'] === 'pingback' ) { $local_pingback = TRUE; } else { $local_pingback = FALSE; }

	if( !empty( $block_all_trackbacks ) && $commentdata['comment_type'] === 'trackback' ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' BLOCKING-TRACKBACKS ';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	if( !empty( $block_all_pingbacks ) && $commentdata['comment_type'] === 'pingback' ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' BLOCKING-PINGBACKS';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check Length */
	if( $trackback_length > $trackback_max_length ) {
		/* There is no reason for an exceptionally long Trackback or Pingback. */
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-LONG3K';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Test User-Agents */

	/* TUA1001,TUA1002 - REMOVED 1.9.8.7 - WPSS_Filters::skiddie_ua_check() now includes this */

	if( WPSS_Filters::skiddie_ua_check( $commentdata_user_agent_lc, 'trackback' ) ) {
		/* There is no reason for a Trackback/Pingback to use one of these UA strings. Commonly used to attack/spam WP. */
		$content_filter_status = '3';
		$wpss_error_code .= ' TUA1004';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TRACKBACK/PINGBACK SPECIFIC TESTS -  BEGIN */

	/* TRACKBACK COOKIE TEST - Trackbacks can't have cookies, but some fake ones do. SMH. */
	if( !empty( $_COOKIE ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-COOKIE';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Body Content - Check for excessive number of links (any) in trackback ( body_content ) */
	$trackback_count_http 	= rs_wpss_substr_count( $commentdata_comment_content_lc_unslash, 'http://' );
	$trackback_count_https 	= rs_wpss_substr_count( $commentdata_comment_content_lc_unslash, 'https://' );
	$trackback_num_links 	= $trackback_count_http + $trackback_count_https;
	$trackback_num_limit 	= 0;
	if( empty( $local_pingback ) && $trackback_num_links > $trackback_num_limit ) { /* Not using rs_wpss_parse_links() since this should be zero anyway, this is faster */
		/* Genuine trackbacks should have text only, not hyperlinks */
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-1-HT';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	if( WP_SpamShield::preg_match( "~\[\.{1,3}\]\s*\[\.{1,3}\]~i", $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T200-1';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/**
	 *  T3000-1 - WordPress UA for a Trackback
	 *  DEPRECATED - Removed 1.7.5
	 */

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	$local_pingback_proxy = FALSE;
	if( !empty( $local_pingback ) && $ip_proxy === 'PROXY DETECTED' ) {
		/* For sites using proxies like Cloudflare, etc. - Added 1.9.6.8 */
		$local_pingback_proxy = TRUE;
	}

	if( empty( $local_pingback ) && empty( $local_pingback_proxy ) && $ip_proxy === 'PROXY DETECTED' ) {
		/* Check to see if Trackback/Pingback is using proxy. (With exceptions for sites using proxies such as Cloudflare.) Real ones don't since they come directly from a website/server. (Or they hide their tracks better.) */
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T1011-FPD-1';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	if( empty( $local_pingback ) ) {
		/* REVDNS FILTER */
		$rev_dns_filter_data = WPSS_Filters::revdns_filter( 'trackback', $content_filter_status, $ip, $reverse_dns_lc, $commentdata_comment_author_lc_unslash );
		$revdns_blacklisted = $rev_dns_filter_data['blacklisted'];
		if( !empty( $revdns_blacklisted ) ) {
			$content_filter_status = $rev_dns_filter_data['status'];
			$wpss_error_code .= $rev_dns_filter_data['error_code'];
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/**
	 *  MISC - T1020-1, T2003-1, T2003-1, T2004-1, T2005-1, T2006-1, T2007-1-1, T2007-2-1, T2010-1, T3001-1, T3002-1, T3003-1-1, T3003-2-1, T3003-3-1, T9000 Variants
	 *  DEPRECATED - Removed 1.7.5
	 */

	/* Blacklisted Domains Check */
	if( WPSS_Filters::domain_blacklist_chk( $commentdata_comment_author_url_domain_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-10500AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check for URL Shorteners, Bogus Long URLs, Social Media, and Misc Spam Domains */
	if( WPSS_Filters::at_link_spam_url_chk( $commentdata_comment_author_url_lc, 'trackback' ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' T-10510AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check Author URL for Exploits */
	if( WPSS_Filters::exploit_url_chk( $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' T-15000AU-XPL'; /* Added in 1.9.8.6 - From rs_wpss_comment_content_filter() */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TRACKBACK/PINGBACK SPECIFIC TESTS -  END */

	/**
	 *  return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	 */

	/* After rs_wpss_exit_content_filter() implemented, can remove following code - BEGIN */
	if( !empty( $wpss_error_code ) ) {
		$wpss_error_code = trim( $wpss_error_code );
		/* Timer End - Content Filter */
		$wpss_end_time_content_filter 				= microtime( TRUE );
		$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
		$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	}
	/* After rs_wpss_exit_content_filter() implemented, can remove previous code - END */

	$commentdata['wpss_error_code'] = trim( $wpss_error_code );
	$commentdata['content_filter_status'] = $content_filter_status;
	return $commentdata;
}

function rs_wpss_trackback_ip_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Trackback/Pingback IP Filter
	 *  This will knock out 99.99% of Trackback/Pingback Spam
	 *  WordPress does not validate IP addresses of incoming Pingbacks & Trackbacks
	 *  WordPress checks URL of Pingbacks for link to this blog, but not Trackbacks
	 *  Keeping this separate and before content filter because it's fast
	 *  If passes this, then content filter will take out the rest
	 */

	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime( TRUE );
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}

	$content_filter_status 				= $wpss_error_code = ''; /* Must go before tests */

	$commentdata_remote_addr			= WP_SpamShield::get_ip_addr();
	$commentdata_remote_addr_lc			= WPSS_Func::lower( $commentdata_remote_addr );
	$commentdata_comment_type			= $commentdata['comment_type'];
	$commentdata_comment_author_url		= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc	= WPSS_Func::lower( $commentdata_comment_author_url );

	/* Check to see if IP Trackback client IP matches IP of Server where link is supposedly coming from */
	$trackback_domain	= rs_wpss_get_domain( $commentdata_comment_author_url_lc );
	$trackback_ip		= rs_wpss_get_forward_dns( $trackback_domain );
	if( rs_wpss_is_local_request() && $commentdata['comment_type'] === 'pingback' ) { $local_pingback = TRUE; } else { $local_pingback = FALSE; }
	if( empty( $local_pingback ) && $commentdata_remote_addr_lc !== $trackback_ip ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '3'; }
		$wpss_error_code .= ' TP1000-FIP-1';
	}

	if( !empty( $wpss_error_code ) ) {
		$wpss_error_code = trim( $wpss_error_code );
		/* Timer End - Content Filter */
		$wpss_end_time_content_filter 				= microtime( TRUE );
		$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
		$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;
	}

	$commentdata['wpss_error_code']					= trim( $wpss_error_code );
	$commentdata['content_filter_status']			= $content_filter_status;
	return $commentdata;
}

function rs_wpss_comment_content_filter( $commentdata, $spamshield_options ) {
	/**
	 *  Comment Content Filter aka "The Algorithmic Layer"
	 *  Blocking the Obvious to Improve Human Spam Defense
	 */
	if( 'trackback' === $commentdata['comment_type'] || 'pingback' === $commentdata['comment_type'] ) { return $commentdata; }

	/* Timer Start  - Content Filter */
	if( empty( $commentdata['start_time_content_filter'] ) ) {
		$wpss_start_time_content_filter				= microtime( TRUE );
		$commentdata['start_time_content_filter']	= $wpss_start_time_content_filter;
	}

	$content_filter_status = $wpss_error_code = ''; /* Must go before tests */

	/* TEST 0 - See if user has already been blacklisted this session */
	if( !rs_wpss_is_user_logged_in() && ( rs_wpss_ubl_cache() ) ) {
		$wpss_error_code .= ' 0-BL'; $content_filter_status = '3';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TEST HTA1000 - See if .htaccess already determined this is a robot */
	if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) ) {
		$_SERVER['WPSS_SEC_THREAT'];
		$wpss_error_code .= ' HTA1000'; $content_filter_status = '3';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_ref2xjs_lc	= WPSS_Func::lower( $post_ref2xjs );

	/* CONTENT FILTERING - BEGIN */

	$commentdata_comment_post_id					= $commentdata['comment_post_ID'];
	$commentdata_comment_post_title					= $commentdata['comment_post_title'];
	$commentdata_comment_post_title_lc				= WPSS_Func::lower( $commentdata_comment_post_title );
	$commentdata_comment_post_title_lc_rgx 			= rs_wpss_preg_quote( $commentdata_comment_post_title_lc );
	$commentdata_comment_post_url					= $commentdata['comment_post_url'];
	$commentdata_comment_post_url_lc				= WPSS_Func::lower( $commentdata_comment_post_url );
	$commentdata_comment_post_url_lc_rgx 			= rs_wpss_preg_quote( $commentdata_comment_post_url_lc );

	$commentdata_comment_post_type					= $commentdata['comment_post_type']; 	/* Possible results: 'post', 'page', 'attachment', 'revision', 'nav_menu_item' */

	/* Next two are boolean */
	$commentdata_comment_post_comments_open			= $commentdata['comment_post_comments_open'];
	$commentdata_comment_post_pings_open			= $commentdata['comment_post_pings_open'];

	$commentdata_comment_author						= $commentdata['comment_author'];
	$commentdata_comment_author_unslash				= stripslashes( $commentdata_comment_author );
	$commentdata_comment_author_lc					= WPSS_Func::lower( $commentdata_comment_author );
	$commentdata_comment_author_lc_rgx 				= rs_wpss_preg_quote( $commentdata_comment_author_lc );
	$commentdata_comment_author_lc_words 			= rs_wpss_count_words( $commentdata_comment_author_lc );
	$commentdata_comment_author_lc_space 			= ' '.$commentdata_comment_author_lc.' ';
	$commentdata_comment_author_lc_unslash			= stripslashes( $commentdata_comment_author_lc );
	$commentdata_comment_author_lc_unslash_rgx 		= rs_wpss_preg_quote( $commentdata_comment_author_lc_unslash );
	$commentdata_comment_author_lc_unslash_words 	= rs_wpss_count_words( $commentdata_comment_author_lc_unslash );
	$commentdata_comment_author_lc_unslash_space 	= ' '.$commentdata_comment_author_lc_unslash.' ';
	$commentdata_comment_author_email				= $commentdata['comment_author_email'];
	$commentdata_comment_author_email_lc			= WPSS_Func::lower( $commentdata_comment_author_email );
	$commentdata_comment_author_email_lc_rgx 		= rs_wpss_preg_quote( $commentdata_comment_author_email_lc );
	$commentdata_comment_author_url					= $commentdata['comment_author_url'];
	$commentdata_comment_author_url_lc				= WPSS_Func::lower( $commentdata_comment_author_url );
	$commentdata_comment_author_url_lc_rgx 			= rs_wpss_preg_quote( $commentdata_comment_author_url_lc );
	$commentdata_comment_author_url_domain_lc		= rs_wpss_get_domain( $commentdata_comment_author_url_lc );

	$commentdata_comment_content					= $commentdata['comment_content'];
	$commentdata_comment_content_unslash			= stripslashes( $commentdata_comment_content );
	$commentdata_comment_content_lc					= WPSS_Func::lower( $commentdata_comment_content );
	$commentdata_comment_content_lc_unslash			= stripslashes( $commentdata_comment_content_lc );
	$commentdata_comment_content_lc_unslash_strlen	= rs_wpss_strlen( $commentdata_comment_content_lc_unslash );
	$commentdata_comment_content_uc					= WPSS_Func::upper( $commentdata_comment_content );
	$commentdata_comment_content_uc_unslash			= stripslashes( $commentdata_comment_content_uc );
	$commentdata_comment_content_extracted_urls 	= rs_wpss_parse_links( $commentdata_comment_content_lc_unslash, 'url' ); /* Parse comment content for all URLs */
	$commentdata_comment_content_extracted_urls_at 	= rs_wpss_parse_links( $commentdata_comment_content_lc_unslash, 'url_at' ); /* Parse comment content for Anchor Text Link URLs */
	$commentdata_comment_content_num_links 			= count( $commentdata_comment_content_extracted_urls ); /* Count extracted URLS from body content - Added 1.8.4 */
	$commentdata_comment_content_num_limit			= 3; /* Max number of links in comment body content */
	$commentdata_comment_content_extracted_emails	= rs_wpss_extract_emails( $commentdata_comment_content_lc_unslash );
	$commentdata_comment_content_num_emails			= count( $commentdata_comment_content_extracted_emails );

	$replace_apostrophes							= array('’','`','&acute;','&grave;','&#39;','&#96;','&#101;','&#145;','&#146;','&#158;','&#180;','&#207;','&#208;','&#8216;','&#8217;');
	$commentdata_comment_content_lc_norm_apost 		= str_replace($replace_apostrophes,"'",$commentdata_comment_content_lc_unslash);

	$commentdata_comment_type						= $commentdata['comment_type'];

	/*
	if( $commentdata_comment_type !== 'pingback' && $commentdata_comment_type !== 'trackback' ) {
		$commentdata_comment_type = 'comment';
	}
	*/

	$commentdata_user_agent 			= rs_wpss_get_user_agent( TRUE, FALSE );
	$commentdata_user_agent_lc			= WPSS_Func::lower( $commentdata_user_agent );

	$http_accept						= rs_wpss_get_http_accept( TRUE, TRUE );
	$http_accept_language 				= rs_wpss_get_http_accept( TRUE, TRUE, TRUE );
	$http_accept_encoding 				= rs_wpss_get_http_accept( TRUE, TRUE, FALSE, TRUE );

	$commentdata_remote_addr			= WP_SpamShield::get_ip_addr();
	$commentdata_remote_addr_rgx 		= rs_wpss_preg_quote( $commentdata_remote_addr );
	$commentdata_remote_addr_lc			= WPSS_Func::lower( $commentdata_remote_addr );
	$commentdata_remote_addr_lc_rgx 	= rs_wpss_preg_quote( $commentdata_remote_addr_lc );

	$commentdata_referrer				= rs_wpss_get_referrer();
	$commentdata_referrer_lc			= WPSS_Func::lower( $commentdata_referrer );
	$commentdata_php_self				= $_SERVER['PHP_SELF'];
	$commentdata_php_self_lc			= WPSS_Func::lower( $commentdata_php_self );

	$blog_server_ip 					= WPSS_SERVER_ADDR;
	$blog_server_name 					= WPSS_SERVER_NAME;

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	/**
	 *  Post Type Filter - INVALTY
	 *  Removed V 1.1.7 - Found Exception
	 */

	/* Simple Filters */

	/* BEING DEPRECATED... */
	$blacklist_word_combo_total_limit = 10; /* you may increase to 30+ if blog's topic is adult in nature - DEPRECATED */
	$blacklist_word_combo_total = 0;

	/* Body Content - Check for excessive number of links in message ( body_content ) - 1.8.4 */
	if( $commentdata_comment_content_num_links > $commentdata_comment_content_num_limit ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 1-HT';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Body Content - Language Mismatch */
	if( WPSS_Filters::lang_mismatch( $commentdata_comment_content_lc_unslash ) || WPSS_Filters::lang_mismatch_pct( $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' L200C';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/**
	 *  Authors Only - Do not use on Trackbacks/Pingbacks
	 *  Removed Filters 300-423 and replaced with Regex
	 */

	/* Author - Language Mismatch */
	if( WPSS_Filters::lang_mismatch( $commentdata_comment_author_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' L200A';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Author Blacklist Check - Invalid Author Names - Stopping Human Spam - Do not use on Trackbacks/Pingbacks */
	if( WPSS_Filters::anchortxt_blacklist_chk( $commentdata_comment_author_lc_unslash, '', 'author', $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500A-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Regular Expression Tests - 2nd Gen - Comment Author/Author URL - BEGIN */

	/* 10500-13000 - Complex Test for terms in Comment Author/URL - $commentdata_comment_author_lc_unslash/$commentdata_comment_author_url_domain_lc */

	/* Blacklisted Domains Check */
	if( WPSS_Filters::domain_blacklist_chk( $commentdata_comment_author_url_domain_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check for URL Shorteners, Bogus Long URLs, and Misc Spam Domains */
	if( WPSS_Filters::at_link_spam_url_chk( $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10510AU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Testing for a unique identifying string from the comment content in the Author URL Domain */
	WP_SpamShield::preg_match( "~\s+([a-z0-9]{6,})$~i", $commentdata_comment_content_lc_unslash, $wpss_str_matches ); /* preg_match() pass by ref &$matches */
	if( !empty( $wpss_str_matches[1] ) ) { $wpss_spammer_id_string = $wpss_str_matches[1]; } else { $wpss_spammer_id_string = ''; }
	$commentdata_comment_author_url_domain_lc_elements = explode( '.', $commentdata_comment_author_url_domain_lc );
	$commentdata_comment_author_url_domain_lc_elements_count = count( $commentdata_comment_author_url_domain_lc_elements ) - 1;
	if( !empty ( $wpss_spammer_id_string ) ) {
		$i = 0;
		/* The following line to prevent exploitation: */
		$i_max = 20;
		while( $i < $commentdata_comment_author_url_domain_lc_elements_count && $i < $i_max ) {
			if( !empty( $commentdata_comment_author_url_domain_lc_elements[$i] ) ) {
				if( $commentdata_comment_author_url_domain_lc_elements[$i] === $wpss_spammer_id_string ) {
					if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
					$wpss_error_code .= ' 10511AUA';
					return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
				}
			}
			++$i;
		}
	}

	/**
	 *  Potential Exploits
	 */

	/* Check Author URL for Exploits */
	if( WPSS_Filters::exploit_url_chk( $commentdata_comment_author_url_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 15000AU-XPL'; /* Added in 1.4 - Replacing 15001AU-XPL and 15002AU-XPL, and adds additional protection */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Regular Expression Tests - 2nd Gen - Comment Author/Author URL - END */

	$blacklist_word_combo_limit = 7;
	$blacklist_word_combo = 0;

	$i = 0;

	/* Regular Expression Tests - 2nd Gen - Comment Content - BEGIN */

	/* Miscellaneous Patterns that Keep Repeating */
	if( WP_SpamShield::preg_match( "~^([0-9]{6})\s([0-9]{6})(.*)\s([0-9]{6})$~i", $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10401C';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Blacklisted Anchor Text Check - Links in Content - Stopping Human Spam - Not for Trackbacks/Pingbacks */
	if( WPSS_Filters::anchortxt_blacklist_chk( $commentdata_comment_content_lc_unslash, '', 'content' ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500CAT-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Blacklisted Domains Check - Links in Content */
	if( WPSS_Filters::link_blacklist_chk( $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10500CU-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* Check Anchor Text Links for URL Shorteners, Bogus Long URLs, and Misc Spam Domains */
	if( WPSS_Filters::at_link_spam_url_chk( $commentdata_comment_content_extracted_urls_at ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 10510CU-BL'; /* Replacing 10510CU-MSC */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check all URLs in Comment Content for Exploits */
	if( WPSS_Filters::exploit_url_chk( $commentdata_comment_content_extracted_urls ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 15000CU-XPL';	/* Added in 1.4 */
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Check if Comment Content Contains *Only* URLs */
	if( !empty( $commentdata_comment_content_extracted_urls ) && is_array( $commentdata_comment_content_extracted_urls ) ) {
		$commentdata_comment_content_temp = str_replace( $commentdata_comment_content_extracted_urls, '', $commentdata_comment_content_lc_unslash );
		$commentdata_comment_content_temp = trim( str_replace( array( "\s", WPSS_EOL, "\t", ' ', ), '', $commentdata_comment_content_temp ) );
		if( empty( $commentdata_comment_content_temp ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 10410CU';	/* Added in 1.9.8.2 */
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/* Check if Comment Content Contains Blacklisted Email Addresses */
	if( !empty( $commentdata_comment_content_extracted_emails ) && is_array( $commentdata_comment_content_extracted_emails ) ) {
		foreach( $commentdata_comment_content_extracted_emails as $i => $v ) {
			if( WPSS_Filters::email_blacklist_chk( $v ) ) {
				if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
				$wpss_error_code .= ' 10600CE-BL';	/* Added in 1.9.9.9.9 */
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
	}

	/* Regular Expression Tests - 2nd Gen - Comment Content - END */


	/**
	 *  Comment Author Tests
	 */

	/* Words in Comment Author Repeated in Content - With Keyword Density */
	$repeated_terms_filters			= array( '.', '-', ':' );
	$repeated_terms_temp_phrase		= str_replace( $repeated_terms_filters, '', $commentdata_comment_author_lc_unslash );
	$repeated_terms_test			= explode( ' ', $repeated_terms_temp_phrase );
	$repeated_terms_test_count		= count( $repeated_terms_test );
	$comment_content_total_words	= rs_wpss_count_words( $commentdata_comment_content_lc_unslash );
	$i = 0;
	while( $i < $repeated_terms_test_count ) {
		if( !empty( $repeated_terms_test[$i] ) ) {
			$repeated_terms_in_content_count = rs_wpss_substr_count( $commentdata_comment_content_lc_unslash, $repeated_terms_test[$i] );
			$repeated_terms_in_content_str_len = rs_wpss_strlen( $repeated_terms_test[$i] );
			if( $repeated_terms_in_content_count > 1 && $comment_content_total_words < $repeated_terms_in_content_count ) {
				$repeated_terms_in_content_count = 1;
			}
			$repeated_terms_in_content_density = ( $repeated_terms_in_content_count / $comment_content_total_words ) * 100;
			if( $repeated_terms_in_content_count >= 5 && $repeated_terms_in_content_str_len >= 4 && $repeated_terms_in_content_density > 40 ) {
				if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
				$wpss_error_code .= ' 9000-'.$i;
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
		++$i;
	}
	/* Comment Author ends in .com or a valid TLD */
	if( WP_SpamShield::preg_match( "~".WPSS_RGX_TLD."$~i", $commentdata_comment_author_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 9002';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Comment Author and URL Tests */
	if( !empty( $commentdata_comment_author_url_lc ) && !empty( $commentdata_comment_author_lc_unslash ) ) {

		/* Comment Author and Comment Author URL appearing in Content - REGEX VERSION */
		if( WP_SpamShield::preg_match( "~(<\s*a\s+([a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*)\s*href|\[(url|link))\s*\=\s*(['\"])?\s*".$commentdata_comment_author_url_lc_rgx."([a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]*)(['\"])?(>|\])".$commentdata_comment_author_lc_unslash_rgx."(<|\[)\s*\/\s*a\s*(>|(url|link)\])~i", $commentdata_comment_content_lc_unslash ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9100';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		if( $commentdata_comment_author_url_lc === $commentdata_comment_author_lc_unslash && !WP_SpamShield::preg_match( "~https?\:/+~i", $commentdata_comment_author_url_lc ) && WP_SpamShield::preg_match( "~(<\s*a\s+([a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*)\s*href|\[(url|link))\s*\=\s*(['\"])?\s*(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*(['\"])?\s*(>|\])".$commentdata_comment_author_lc_unslash_rgx."(<|\[)\s*\/\s*a\s*(>|(url|link)\])~i", $commentdata_comment_content_lc_unslash ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9101';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		if( WP_SpamShield::preg_match( "~^((ww[w0-9]|m)\.)?".$commentdata_comment_author_lc_unslash_rgx."$~i", $commentdata_comment_author_url_domain_lc ) && !WP_SpamShield::preg_match( "~https?\:/+~i", $commentdata_comment_author_lc_unslash ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9102';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
		if( $commentdata_comment_author_url_lc === $commentdata_comment_author_lc_unslash && !WP_SpamShield::preg_match( "~https?\:/+~i", $commentdata_comment_author_url_lc ) && WP_SpamShield::preg_match( "~(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)~i", $commentdata_comment_content_lc_unslash ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' 9103';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/**
	 *  Email Filters
	 *  New Test with Blacklists
	 */
	if( WPSS_Filters::email_blacklist_chk( $commentdata_comment_author_email_lc ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 9200E-BL';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TEST REFERRERS 1 - TO THE COMMENT PROCESSOR */
	if( strpos( WPSS_COMMENTS_POST_URL, $commentdata_php_self_lc ) !== FALSE && $commentdata_referrer_lc === WPSS_COMMENTS_POST_URL ) {
		/* Often spammers send the referrer as the URL for the wp-comments-post.php page. */
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' REF-1-1011';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* TEST REFERRERS 2 - SPAMMERS SEARCHING FOR PAGES TO COMMENT ON */
	if( !empty( $post_ref2xjs ) ) {
		$ref2xJS = addslashes( urldecode( $post_ref2xjs ) );
		$ref2xJS = str_replace( '%3A', ':', $ref2xJS );
		$ref2xJS = str_replace( ' ', '+', $ref2xJS );
		$ref2xJS = esc_url_raw( $ref2xJS );
		$ref2xJS_lc = WPSS_Func::lower( $ref2xJS );
		if( WP_SpamShield::preg_match( "~\.google".WPSS_RGX_TLD."~i", $ref2xJS ) && strpos( $ref2xJS_lc, 'leave a comment' ) !== FALSE ) {
			/* make test more robust for other versions of google & search query */
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' REF-2-1021';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/**
	 *  TEST REFERRERS 3 - TO THE PAGE BEING COMMENTED ON
	 *  DISABLED IN V1.5.9
	 */

	/* Spam Network - BEGIN */

	/**
	 *  PART OF BAD ROBOTS TEST - BEGIN
	 *  Test User-Agents
	 */

	/* UA1001 - REMOVED 1.9.8.7 - UA1003 - REMOVED 1.9.8.6 - WPSS_Filters::skiddie_ua_check() now includes this */

	if( WPSS_Filters::skiddie_ua_check( $commentdata_user_agent_lc, 'comment' ) ) {
		/* There is no reason for a human to use one of these UA strings. Commonly used to attack/spam WP. */
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' UA1004';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}


	/**
	 *  Test HTTP_ACCEPT
	 */
	if( empty( $http_accept ) ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HA1001';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* HA1002 REMOVED - 1.9.0.3 */
	if( $http_accept === '*' ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HA1003';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	if( ( !empty( $_SERVER['WPSS_HA1004'] ) || $http_accept === '*/*' ) && !rs_wpss_is_ajax_request() ) {
		$content_filter_status = '3'; /* Added V1.9.8.5 */
		$wpss_error_code .= ' HA1004';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* More complex test for invalid 'HTTP_ACCEPT' */
	$http_accept_mod_1			= preg_replace( "~([\s\;]+)~", ",", $http_accept );
	$http_accept_elements		= explode( ',', $http_accept_mod_1 );
	$http_accept_elements_count	= count($http_accept_elements);
	$i = 0;
	/* The following line to prevent exploitation: */
	$i_max = 20;
	while( $i < $http_accept_elements_count && $i < $i_max ) {
		if( !empty( $http_accept_elements[$i] ) ) {
			if( $http_accept_elements[$i] === '*' ) {
				$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
				$wpss_error_code .= ' HA1010';
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
		++$i;
	}

	/* Test HTTP_ACCEPT_LANGUAGE */
	if( empty( $http_accept_language ) ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HAL1001';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	if( $http_accept_language === '*' ) {
		$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
		$wpss_error_code .= ' HAL1002';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}
	/* More complex test for invalid 'HTTP_ACCEPT_LANGUAGE' */
	$http_accept_language_mod_1				= preg_replace( "~([\s\;]+)~", ",", $http_accept_language );
	$http_accept_language_elements			= explode( ',', $http_accept_language_mod_1 );
	$http_accept_language_elements_count	= count($http_accept_language_elements);
	$i = 0;
	/* The following line to prevent exploitation: */
	$i_max = 20;
	while( $i < $http_accept_language_elements_count && $i < $i_max ) {
		if( !empty( $http_accept_language_elements[$i] ) ) {
			if( $http_accept_language_elements[$i] === '*' && strpos( $commentdata_user_agent_lc, 'links (' ) !== 0 ) {
				$content_filter_status = '3'; /* Was 1, changed to 3 - V1.8.4 */
				$wpss_error_code .= ' HAL1010';
				return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
			}
		}
		++$i;
	}

	if( $http_accept_language === 'en-US,*' && $http_accept_encoding === 'gzip' ) { /* PhantomJS Sig */
		$wpss_error_code .= ' '.$pref.'HALE1001'; $blacklisted = TRUE;
	}

	/**
	 *  Test PROXY STATUS if option
	 *  Google Chrome Data Compression Proxy Bypass (Data Saver)
	 */
	if( 'PROXY DETECTED' === $ip_proxy && TRUE !== $ip_proxy_chrome_compress && empty( $spamshield_options['allow_proxy_users'] ) ) {
		$content_filter_status = '10';
		$wpss_error_code .= ' PROXY1001';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Test Reverse DNS Hosts */
	$rev_dns_filter_data = WPSS_Filters::revdns_filter( 'comment', $content_filter_status, $ip, $reverse_dns_lc, $commentdata_comment_author_lc_unslash, $commentdata_comment_author_email_lc );
	$revdns_blacklisted = $rev_dns_filter_data['blacklisted'];
	if( !empty( $revdns_blacklisted ) ) {
		$content_filter_status = $rev_dns_filter_data['status'];
		$wpss_error_code .= $rev_dns_filter_data['error_code'];
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Spam Network - END */


	/**
	 *  Comment Content Tests
	 */

	/* Comment Author Email appearing in Content */
	if( !empty( $commentdata_comment_author_email_lc ) && WP_SpamShield::preg_match( "~".$commentdata_comment_author_email_lc_rgx."~i", $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 9201CC-AE';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Comment Author URL appearing in Content */
	if( !empty( $commentdata_comment_author_url_lc ) && WP_SpamShield::preg_match( "~".$commentdata_comment_author_url_lc_rgx."~i", $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 9501CC-AU';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}

	/* Comment Content First 25 Chars are all Caps */
	if( $commentdata_comment_content_lc_unslash_strlen > 25 && substr( $commentdata_comment_content_unslash, 0, 25 ) === substr( $commentdata_comment_content_uc_unslash, 0, 25 ) && WP_SpamShield::preg_match( "~[a-z]+~i", $commentdata_comment_content_lc_unslash ) ) {
		if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
		$wpss_error_code .= ' 9502CC';
		return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
	}


	/**
	 *  Comment Content
	 *  Miscellaneous Preg Match Tests - Changed to regex in V1.8.4
	 *  TO DO: Switch to Magic Parser - rs_wpss_magic_parser()
	 */
	$wpss_misc_spam_phrases_to_check =
		array(
			'5000' => "~\[\.+\]\s+\[\.+\]~",
			'5001' => "~^<new\s+comment>$~i",
			/* 5002 - Removed in V1.8.4 */
			'5003' => "~^([a-z0-9\s\.,!]{0,12})?((he.a?|h([ily]{1,2}))(\s+there)?|howdy|hello|bonjour|good\s+day)([\.,!])?\s+(([ily]{1,2})\s+know\s+)?th([ily]{1,2})s\s+([ily]{1,2})s\s+([a-z\s]{3,12}|somewhat|k([ily]{1,2})nd\s*of)?(of{1,2}\s+)?of{1,2}\s+top([ily]{1,2})c\s+(but|however)\s+([ily]{1,2})\s+(was\s+wonder([ily]{1,2})nn?g?|need\s+some\s+adv([ily]{1,2})ce)~i",
			'5004' => "~^th([ily]{1,2})s[\s\-]*([ily]{1,2})s[\s\-]*k([ily]{1,2})nd[\s\-]*of[\s\-]*off[\s\-]*top([ily]{1,2})c[\s\-]*but~i",
			'5010' => "~^hello[\s\-]*to[\s\-]*all[\s\-]*my[\s\-]*sisters\.~i",
			'5011' => "~^greetings[\s\-]*to[\s\-]*the[\s\-]*general[\s\-]*public~i",
			'5012' => "~^hello[\s\-]*everyone[\s\-]*here[\s\-]*in[\s\-]*this[\s\-]*forum~i",
			'5013' => "~^hello[\s\-]*my[\s\-]*name[\s\-]*is~i",
			'5014' => "~^dear[\s\-]*friends,?[\s\-]*how[\s\-]*can[\s\-]*i[\s\-]*explain[\s\-]*this~i",
			'5015' => "~^good[\s\-]*day[\s\-]*to[\s\-]*you[\s\-]*all[\s\-]*friends,[\s\-]*my[\s\-]*name[\s\-]*is~i",
			'5016' => "~^i[\s\-]*am[\s\-]*here[\s\-]*to[\s\-]*(give|share)[\s\-]*my[\s\-]*testimony~i",
			'5017' => "~(whats*[\s\.\-]*app|email|call|contact|phone|dial|ring)[\s\-]*(me|hi[ms]|hers?|them|theirs?)[\s\-]+(number[\s\-]+)?((on|at|in|now|asap|right[\s\-]*away)[\s\-]*)?[\s\-\:\.]*([\+\(]?[\d]{7,}|\(?\+234\)?[\d\-\(\)\ ]{8,}\b)~i",
			'5018' => "~(me|hi[ms]|hers?|them|theirs?)[\s\-]+(number[\s\-]+)?((on|at|in|now|asap|right[\s\-]*away)[\s\-]*)?(whats*[\s\.\-]*app)[\s\-]*(number[\s\-]+)?[\s\-\:\.]*([\+\(]?[\d]{7,}|\(?\+234\)?[\d\-\(\)\ ]{8,}\b)~i",
			'5019' => "~(\@".WPSS_RGX_FREEMAIL."[\s\-\.]+or|call|number|whats*[\s\.\-]*app)([\s\.\-]*(him|her|them)[\s\.\-]*)?((on|at|in|now|asap|right[\s\-]*away)[\s\.\-]*)?[\s\-\:\.]*\(?\+234\)?[\d\-\(\)\ ]{8,}\b~i",
		);
	foreach( $wpss_misc_spam_phrases_to_check as $ec => $rgx_phrase ) {
		if( WP_SpamShield::preg_match( $rgx_phrase, $commentdata_comment_content_lc_unslash ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '1'; }
			$wpss_error_code .= ' '.$ec;
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}


	/* BOILERPLATE: Add common boilerplate/template spam phrases... Add Blacklist functions */

	/* Enhanced Comment Blacklist - 0-ECBL */
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $content_filter_status ) ) {
		if( rs_wpss_blacklist_check( $commentdata_comment_author_lc_unslash, $commentdata_comment_author_email_lc, $commentdata_comment_author_url_lc, $commentdata_comment_content_lc_unslash, $ip, $commentdata_user_agent_lc, '' ) ) {
			if( empty( $content_filter_status ) ) { $content_filter_status = '100'; }
			$wpss_error_code .= ' 0-ECBL';
			return rs_wpss_exit_content_filter( $commentdata, $spamshield_options, $wpss_error_code, $content_filter_status );
		}
	}

	/* Timer End - Content Filter */
	$wpss_end_time_content_filter 				= microtime( TRUE );
	$wpss_total_time_content_filter 			= rs_wpss_timer( $commentdata['start_time_content_filter'], $wpss_end_time_content_filter, FALSE, 6, TRUE );
	$commentdata['total_time_content_filter']	= $wpss_total_time_content_filter;

	$wpss_error_code							= ( empty( $wpss_error_code ) ) ? 'No Error' : trim( $wpss_error_code );

	/**
	 *  $spamshield_error_data = array( $wpss_error_code, $blacklist_word_combo, $blacklist_word_combo_total );
	 */

	$commentdata['wpss_error_code']			= trim( $wpss_error_code );
	$commentdata['content_filter_status']	= $content_filter_status;

	return $commentdata;

	/* CONTENT FILTERING - END */
}

/* COMMENT SPAM FILTERS - END */

/* SPAM CHECK FOR OTHER PLUGINS AND FORMS - BEGIN */

/**
 *	Checks all miscellaneous form POST submissions for spam
 *	Misc Form Spam Check - Layer 1
 *	@since			1.8.9.9
 *	@hook			action|init|2
 */
function rs_wpss_misc_form_spam_check() {
	if( rs_wpss_is_user_admin() || rs_wpss_is_admin_sproc() ) { return; }
	$spamshield_options = WP_SpamShield::get_option();
	if( !empty( $spamshield_options['disable_misc_form_shield'] ) ) { return; }
	/* WPSS Whitelist Check - IP Only */
	if( rs_wpss_whitelist_check() ) { return; }
	$url_lc		= WPSS_Func::lower( WPSS_THIS_URL );
	$req_uri	= $_SERVER['REQUEST_URI'];
	$req_uri_lc	= WPSS_Func::lower( $req_uri );
	/* BYPASS - GENERAL */
	if( empty( $_POST ) || 'POST' !== WPSS_REQUEST_METHOD || isset( $_POST[WPSS_REF2XJS] ) || isset( $_POST[WPSS_JSONST] ) || isset( $_POST['wpss_contact_message'] ) || isset( $_POST['signup_username'] ) || isset( $_POST['signup_email'] ) || isset( $_POST['ws_plugin__s2member_registration'] ) || isset( $_POST['_wpcf7_version'] ) || isset( $_POST['gform_submit'] ) || isset( $_POST['gform_unique_id'] ) ) { return; }
	if( rs_wpss_is_login_page() ) { return; }
	if( is_admin() && !WP_SpamShield::is_admin_post() && !rs_wpss_is_doing_ajax() && !rs_wpss_is_login_page() ) { return; }
	if( current_user_can( 'moderate_comments' ) ) { return; }
	if( rs_wpss_is_user_logged_in() ) { return; } /* May remove later */
	if( rs_wpss_is_doing_cron() || rs_wpss_is_xmlrpc() || rs_wpss_is_doing_nf_rest() || rs_wpss_is_heartbeat() || rs_wpss_is_installing() || rs_wpss_is_cli() ) { return; }
	if( rs_wpss_is_comment_request() || is_trackback() ) { return; }
	$post_count = count( $_POST );
	if( $post_count == 4 && isset( $_POST['excerpt'], $_POST['url'], $_POST['title'], $_POST['blog_name'] ) ) { return; }
	$ip = WP_SpamShield::get_ip_addr();
	if( $ip === WPSS_SERVER_ADDR ) { return; } /* Skip website IP address */
	if( rs_wpss_compare_ip_cbl( $ip, WPSS_SERVER_ADDR ) ) { return FALSE; } /* Skip anything on same C-Block as website */
	$ecom_urls = unserialize( WPSS_ECOM_URLS );
	$admin_url = WPSS_ADMIN_URL.'/';
	if( $post_count >= 5 && isset( $_POST['log'], $_POST['pwd'], $_POST['wp-submit'], $_POST['testcookie'], $_POST['redirect_to'] ) && $_POST['redirect_to'] === $admin_url ) { return; }
	if( $post_count >= 5 && isset( $_POST['log'], $_POST['pwd'], $_POST['login'], $_POST['testcookie'], $_POST['redirect_to'] ) ) { return; }
	if( $post_count >= 5 && isset( $_POST['username'], $_POST['password'], $_POST['login'], $_POST['_wpnonce'], $_POST['_wp_http_referer'] ) && rs_wpss_is_wc_login_page() ) { return; }

	if( WPSS_Compatibility::misc_form_bypass() ) { return; }

	/* BYPASS - HOOK */
	$mfsc_bypass = apply_filters( 'wpss_misc_form_spam_check_bypass', FALSE );
	if( !empty( $mfsc_bypass ) ) { return; }

	$msc_filter_status		= $wpss_error_code = $log_pref = '';
	$msc_jsck_error			= $msc_badrobot_error = FALSE;
	$form_type				= 'misc form';
	$pref					= 'MSC-';
	$errors_3p				= array();
	$error_txt 				= rs_wpss_error_txt();
	$server_email_domain	= rs_wpss_get_email_domain( WPSS_SERVER_NAME );
	$msc_serial_post 		= @WPSS_PHP::json_encode( $_POST );
	$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

	/* Check for Specific Contact Form Plugins */
	if( defined( 'JETPACK__VERSION' ) && isset( $_POST['action'] ) && $_POST['action'] === 'grunion-contact-form' ) { $form_type = 'jetpack form'; $pref = 'JP-'; }
	elseif( defined( 'NF_PLUGIN_VERSION' ) && isset( $_POST['_ninja_forms_display_submit'] ) ) { $form_type = 'ninja forms'; $pref = 'NF-'; }
	elseif( ( defined( 'MC4WP_VERSION' ) || defined( 'MC4WP_LITE_VERSION' ) ) && ( isset( $_POST['_mc4wp_form_id'] ) || isset( $_POST['_mc4wp_form_submit'] ) ) ) { $form_type = 'mailchimp form'; $pref = 'MCF-'; }
	elseif( WPSS_Compatibility::is_plugin_active( 'gwolle-gb' ) && !empty( $_POST['gwolle_gb_function'] ) && 'add_entry' === $_POST['gwolle_gb_function'] ) { $form_type = 'guestbook form'; $pref = 'GBK-'; }

	/* JS/JQUERY CHECK */
	if( !rs_wpss_is_ajax_request() ) {
		$wpss_key_values 		= rs_wpss_get_key_values();
		$wpss_jq_key 			= $wpss_key_values['wpss_jq_key'];
		$wpss_jq_val 			= $wpss_key_values['wpss_jq_val'];
		if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* Fall back to FVFJS Keys instead of jQuery keys from jscripts.php */
			$wpss_jq_key 		= $wpss_key_values['wpss_js_key'];
			$wpss_jq_val 		= $wpss_key_values['wpss_js_val'];
		}
		$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] ) ? $_POST[$wpss_jq_key] : '';
		if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
			$wpss_error_code		.= ' '.$pref.'JQHFT-5';
			$msc_jsck_error			= TRUE;
			$err_cod				= 'jsck_error';
			$err_msg				= __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' );
			$errors_3p[$err_cod]	= $err_msg;
		}
	}

	if( !isset( $_POST['wp-submit'] ) ) { /* Don't use on default WordPress Login, Registration, or Forgot Email pages */

		/* EMAIL BLACKLIST */
		$collected_emails = array();
		if( $form_type === 'mailchimp form' ) {
			foreach( $_POST as $k => $v ) {
				if( !is_string( $v ) ) { continue; }
				$k_lc = WPSS_Func::lower( $k );
				$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
				if( strpos( $k_lc, 'email' ) !== FALSE ) {
					if( !is_email( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'9200E-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod = 'blacklist_email_error';
							$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
							$errors_3p[$err_cod] = $err_msg;
						}
						break;							
					} elseif( is_email( $v_lc ) ) {
						$email_domain = rs_wpss_parse_email( $v_lc, 'domain' );
						if( $email_domain === $server_email_domain ) { continue; }
						if( WPSS_Filters::email_blacklist_chk( $v_lc ) ) {
							$wpss_error_code .= ' '.$pref.'9200E-BL';
							if( $msc_jsck_error !== TRUE ) {
								$err_cod = 'blacklist_email_error';
								$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
								$errors_3p[$err_cod] = $err_msg;
							}
							break;
						}
						$collected_emails[] = $v_lc;
					}
				}
			}
		} else {
			foreach( $_POST as $k => $v ) {
				if( !is_string( $v ) ) { continue; }
				$k_lc = WPSS_Func::lower( $k );
				$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
				if( strpos( $k_lc, 'email' ) !== FALSE && is_email( $v_lc ) ) {
					$email_domain = rs_wpss_parse_email( $v_lc, 'domain' );
					if( $email_domain === $server_email_domain ) { continue; }
					if( WPSS_Filters::email_blacklist_chk( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'9200E-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod = 'blacklist_email_error';
							$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
							$errors_3p[$err_cod] = $err_msg;
						}
						break;
					}
					$collected_emails[] = $v_lc;
				}
			}
		}

		if( $form_type === 'jetpack form' || $form_type === 'ninja forms' || $form_type === 'guestbook form' ) {
			foreach( $_POST as $k => $v ) {
				if( !is_string( $v ) ) { continue; }
				$k_lc = WPSS_Func::lower( $k );
				$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
				/* CONTACT FORM CONTENT BLACKLIST */
				if( FALSE !== strpos( $k_lc, 'message' ) || FALSE !== strpos( $k_lc, 'comment' ) || FALSE !== strpos( $k_lc, 'gwolle_gb_content' ) ) {
					if( WPSS_Filters::cf_content_blacklist_chk( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'10400C-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod				= 'blacklist_content_error';
							$err_msg				= __( 'Message appears to be spam.', 'wp-spamshield' );
							$errors_3p[$err_cod]	= $err_msg;
						}
						break;
					}
				}
				/* KEYWORD SPAM BLACKLIST */
				if( FALSE !== strpos( $k_lc, '_author_name' ) || FALSE !== strpos( $k_lc, 'gwolle_gb_subject' ) ) {
					if( WPSS_Filters::anchortxt_blacklist_chk( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'10500A-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod = 'blacklist_keyword_error';
							$err_msg = __( 'Message appears to be spam.', 'wp-spamshield' );
							$errors_3p[$err_cod] = $err_msg;
						}
						break;
					}
				}
				/* DOMAIN BLACKLIST */
				if( FALSE !== strpos( $k_lc, '_author_website' ) ) {
					if( WPSS_Filters::domain_blacklist_chk( $v_lc ) ) {
						$wpss_error_code .= ' '.$pref.'10500AU-BL';
						if( $msc_jsck_error !== TRUE ) {
							$err_cod = 'blacklist_domain_error';
							$err_msg = __( 'Message appears to be spam.', 'wp-spamshield' );
							$errors_3p[$err_cod] = $err_msg;
						}
						break;
					}
				}
				/* Add more... */
			}
		}

		/* BAD ROBOT BLACKLIST */
		$bad_robot_filter_data	= WPSS_Filters::bad_robot_blacklist_chk( $form_type, $msc_filter_status );
		$msc_filter_status		= $bad_robot_filter_data['status'];
		$bad_robot_blacklisted	= $bad_robot_filter_data['blacklisted'];
		if( !empty( $bad_robot_blacklisted ) ) {
			$wpss_error_code	.= $bad_robot_filter_data['error_code'];
			$msc_badrobot_error	= TRUE;
			if( $msc_jsck_error !== TRUE ) {
				$err_cod = 'badrobot_error';
				$err_msg = __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod] = $err_msg;
			}
		}

		/* Enhanced Comment Blacklist - MSC-0-ECBL */
		if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
			if( empty( $collected_emails ) ) {
				if( rs_wpss_blacklist_check() ) {
					$wpss_error_code .= ' '.$pref.'0-ECBL';
					$err_cod = 'blacklist_ecbl_error';
					$err_msg = __( 'That action is currently not allowed.' );
					$errors_3p[$err_cod] = $err_msg;
				}
			} else {
				foreach( $collected_emails as $k => $v ) {
					if( rs_wpss_blacklist_check( '', $v ) ) {
						$wpss_error_code .= ' '.$pref.'0-ECBL';
						$err_cod = 'blacklist_ecbl_error';
						$err_msg = __( 'That action is currently not allowed.' );
						$errors_3p[$err_cod] = $err_msg;
						break;
					}
				}
			}
		}

		/* BLACKLISTED USER */
		if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
			$wpss_error_code .= ' '.$pref.'0-BL';
			$err_cod = 'blacklisted_user_error';
			$err_msg = __( 'That action is currently not allowed.' );
			$errors_3p[$err_cod] = $err_msg;
		}

	}

	/* Done with Tests */

	$wpss_error_code = trim( $wpss_error_code );
	if( ( FALSE !== strpos( $wpss_error_code, '0-BL' ) || FALSE !== strpos( $wpss_error_code, '0-ECBL' ) ) && FALSE === strpos( $wpss_error_code, '00-BL' ) && FALSE === strpos( $wpss_error_code, '00-ECBL' ) ) {
		@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Miscellaneous forms have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
	}

	if( !empty( $wpss_error_code ) ) {
		rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
		/* If enabled, run security check to make sure this POST submission wasn't a security threat: vulnerability probe or hack attempt */
		if( TRUE === WPSS_IP_BAN_ENABLE ) {
			$wpss_security = new WPSS_Security();
			if( $wpss_security->check_post_sec() ) {
				global $wpss_sec_threat;
				$wpss_sec_threat = $_SERVER['WPSS_SEC_THREAT'] = TRUE;
				if( rs_wpss_is_session_active() ) {
					$_SESSION['WPSS_SEC_THREAT_'.WPSS_HASH] = TRUE;
				}
			}
		}
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $msc_serial_post );
		}
		if( TRUE === WPSS_IP_BAN_ENABLE ) {
			if( !empty( $wpss_sec_threat ) || !empty( $_SERVER['WPSS_SEC_THREAT'] ) ) { WPSS_Security::ip_ban(); }
		}
	} else {
		rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $msc_serial_post );
		}
	}

	/* Now output error message */
	if( !empty( $wpss_error_code ) ) {
		$error_msg = '';
		foreach( $errors_3p as $c => $m ) {
			$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
		}
		WP_SpamShield::wp_die( $error_msg, TRUE );
	}

}


/* New CF7 Validation Functions Go Here in Future Releases */


/**
 *	Checks Contact Form 7 submissions for spam
 *	@dependencies	...
 *	@since			1.8.9.9
 */
function rs_wpss_cf7_spam_check( $spam ) {
	if( rs_wpss_is_user_admin() ) { return $spam; }
	$spamshield_options = WP_SpamShield::get_option();
	if( !empty( $spamshield_options['disable_cf7_shield'] ) ) { return $spam; }

	/* WPSS Whitelist Check - IP Only */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check() ) { return $spam; }

	/* BYPASS - HOOK */
	$cf7sc_bypass = apply_filters( 'wpss_cf7_spam_check_bypass', FALSE );
	if( !empty( $cf7sc_bypass ) ) { return $spam; }

	$cf7_filter_status		= $wpss_error_code = '';
	$cf7_jsck_error			= $cf7_badrobot_error = FALSE;
	$pref					= 'CF7-';
	$server_email_domain	= rs_wpss_get_email_domain( WPSS_SERVER_NAME );
	$cf7_serial_post 		= @WPSS_PHP::json_encode( $_POST );
	$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

	/* JS/JQUERY CHECK */
	$wpss_key_values 		= rs_wpss_get_key_values();
	$wpss_jq_key 			= $wpss_key_values['wpss_jq_key'];
	$wpss_jq_val 			= $wpss_key_values['wpss_jq_val'];
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* Fall back to FVFJS Keys instead of jQuery keys from jscripts.php */
		$wpss_jq_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_jq_val 		= $wpss_key_values['wpss_js_val'];
	}
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] ) ? $_POST[$wpss_jq_key] : '';
	if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
		add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_js', 10, 2 );
		$wpss_error_code	.= ' '.$pref.'JQHFT-6';
		$cf7_jsck_error		= TRUE;
	}

	/* EMAIL BLACKLIST */
	$collected_emails = array();
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		$k_lc = WPSS_Func::lower( $k );
		$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
		if( strpos( $k_lc, 'email' ) !== FALSE && is_email( $v_lc ) ) {
			$email_domain = rs_wpss_parse_email( $v_lc, 'domain' );
			if( $email_domain === $server_email_domain ) { continue; }
			if( WPSS_Filters::email_blacklist_chk( $v_lc ) ) {
				if( empty( $wpss_error_code ) ) {
					add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_email_bl', 10, 2 );
				}
				$wpss_error_code .= ' '.$pref.'9200E-BL'; break;
			}
			$collected_emails[] = $v_lc;
		}
	}

	/* FORM FIELDS - OTHER TESTS */
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		$k_lc = WPSS_Func::lower( $k );
		$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
		/* CONTACT FORM CONTENT BLACKLIST */
		if( FALSE !== strpos( $k_lc, 'message' ) && WPSS_Filters::cf_content_blacklist_chk( $v_lc ) ) {
			if( empty( $wpss_error_code ) ) {
				add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_cf_content_bl', 10, 2 );
			}
			$wpss_error_code .= ' '.$pref.'10400C-BL'; break;
		}
		/* URL BLACKLISTS */
		if( empty( $wpss_error_code ) && FALSE === strpos( $v_lc, WPSS_SITE_DOMAIN ) && ( FALSE !== strpos( $k_lc, '-website' ) || 0 === strpos( $v_lc, 'http://' ) || 0 === strpos( $v_lc, 'https://' ) ) ) {
			/* Check if domain is blacklisted */
			if( WPSS_Filters::domain_blacklist_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'10500AU-BL';
			}
			/* URL Shortener Check */
			elseif( WPSS_Filters::urlshort_blacklist_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'10501AU-BL';
			}
			/* Excessively Long URL Check (Obfuscated & Exploit) */
			elseif( WPSS_Filters::long_url_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'10502AU-BL';
			}
			/* Spam URL Check -  Check for URL Shorteners, Bogus Long URLs, and Misc Spam Domains */
			elseif( WPSS_Filters::at_link_spam_url_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'10510AU-BL';
			}
			/* Exploit URL Check */
			elseif( WPSS_Filters::exploit_url_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'15000AU-XPL';
			}
			if( !empty( $wpss_error_code ) ) {
				if( !has_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague' ) ) {
					add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
				}
				break;
			}
		}
		/* Add more tests here... */
	}

	/* BAD ROBOT BLACKLIST */
	$bad_robot_filter_data = WPSS_Filters::bad_robot_blacklist_chk( 'contact form 7', $cf7_filter_status );
	if( !empty( $bad_robot_filter_data['blacklisted'] ) ) {
		if( empty( $wpss_error_code ) ) {
			add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
		}
		$wpss_error_code .= $bad_robot_filter_data['error_code'];
		$cf7_badrobot_error = TRUE;
	}

	/* Enhanced Comment Blacklist - CF7-0-ECBL */
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
		if( empty( $collected_emails ) ) {
			if( rs_wpss_blacklist_check() ) {
				add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
				$wpss_error_code .= ' '.$pref.'0-ECBL';
			}
		} else {
			foreach( $collected_emails as $k => $v ) {
				if( rs_wpss_blacklist_check( '', $v ) ) {
					add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
					$wpss_error_code .= ' '.$pref.'0-ECBL'; break;
				}
			}
		}
	}

	/* BLACKLISTED USER */
	if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
		add_filter( 'wpcf7_display_message', 'rs_wpss_cf7_spam_message_vague', 10, 2 );
		$wpss_error_code .= ' '.$pref.'0-BL';
	}

	$wpss_error_code = trim( $wpss_error_code );
	if( ( FALSE !== strpos( $wpss_error_code, '0-BL' ) || FALSE !== strpos( $wpss_error_code, '0-ECBL' ) ) && FALSE === strpos( $wpss_error_code, '00-BL' ) && FALSE === strpos( $wpss_error_code, '00-ECBL' ) ) {
		@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Contact Form 7 forms have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
	}

	if( !empty( $wpss_error_code ) ) {
		$spam = TRUE;
		rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'contact form 7', $cf7_serial_post );
		}
	} else {
		rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, 'contact form 7', $cf7_serial_post );
		}
	}

	return $spam;
}

function rs_wpss_cf7_spam_message_js( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - JS Error
	 *  @since 1.8.9.9
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_javascript', __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' ) ); }
	return $err_msg;
}

function rs_wpss_cf7_spam_message_email_bl( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - Email Blacklist
	 *  @since 1.9.1
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_bl_email', __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' ) ); }
	return $err_msg;
}

function rs_wpss_cf7_spam_message_cf_content_bl( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - Contact Form Content Blacklist
	 *  @since 1.9.1
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_bl_content', __( 'Message appears to be spam.', 'wp-spamshield' ) ); }
	return $err_msg;
}

function rs_wpss_cf7_spam_message_vague( $err_msg, $status = 'spam' ) {
	/**
	 *  Updates Contact Form 7 spam response message - Generic
	 *  @since 1.9.1
	 */
	if( $status === 'spam' ) { $err_msg = apply_filters( 'wpss_cf7_blocked_form_response_vague', __( 'That action is currently not allowed.' ) ); }
	return $err_msg;
}

function rs_wpss_gf_form_append( $form_string, $form ) {
	/**
	 *  Adds code to Gravity Forms
	 *  @since 1.9.5.2
	 */
	if( rs_wpss_is_user_admin() || rs_wpss_is_admin_sproc() || WPSS_Compatibility::is_builder_active() ) { return $form_string; }
	$spamshield_options = WP_SpamShield::get_option();
	if( !empty( $spamshield_options['disable_gf_shield'] ) ) { return $form_string; }

	/* BYPASS - HOOK */
	$gffa_bypass = apply_filters( 'wpss_gf_form_append_bypass', FALSE );
	if( !empty( $gffa_bypass ) ) { return $form_string; }

	$wpss_string = WP_SpamShield::insert_footer_js( TRUE );
	$form_string = str_replace( '</form>', $wpss_string.'</form>', $form_string );
	global $wpss_gf_form_active; $wpss_gf_form_active = TRUE;
	return $form_string;
}

function rs_wpss_gf_spam_check( $form ) {
	/**
	 *  Checks Gravity Forms submissions for spam
	 *  @since 1.8.9.9, Modified 1.9.5
	 */
	if( rs_wpss_is_user_admin() ) { return $form; }
	$spamshield_options = WP_SpamShield::get_option();
	if( !empty( $spamshield_options['disable_gf_shield'] ) ) { return $form; }

	/* WPSS Whitelist Check - IP Only */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check() ) { return $form; }

	/* BYPASS - HOOK */
	$gfsc_bypass = apply_filters( 'wpss_gf_spam_check_bypass', FALSE );
	if( !empty( $gfsc_bypass ) ) { return $form; }

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	$user_agent = rs_wpss_get_user_agent();

	/* BYPASS - Ecommerce Plugins */
	if( ( !empty( $_POST['add-to-cart'] ) || !empty( $_POST['add_to_cart'] ) || !empty( $_POST['addtocart'] ) || !empty( $_POST['product-id'] ) || !empty( $_POST['product_id'] ) || !empty( $_POST['productid'] ) || ( $user_agent === 'PayPal IPN ( https://www.paypal.com/ipn )' && WP_SpamShield::preg_match( "~^(ipn|ipnpb|notify|reports)(\.sandbox)?\.paypal\.com$~", $reverse_dns ) ) ) && ( WPSS_Compatibility::is_ecom_enabled() && !WPSS_Compatibility::is_woocom_enabled() ) ) { return $form; }

	$gf_filter_status		= $wpss_error_code = '';
	$gf_jsck_error			= $gf_badrobot_error = FALSE;
	$form_type				= 'gravity forms';
	$pref					= 'GF-';
	$errors_3p				= array();
	$error_txt 				= rs_wpss_error_txt();
	$server_email_domain	= rs_wpss_get_email_domain( WPSS_SERVER_NAME );
	$gf_serial_post 		= @WPSS_PHP::json_encode( $_POST );
	$form_auth_dat 			= array( 'comment_author' => '', 'comment_author_email' => '', 'comment_author_url' => '' );

	/* JS/JQUERY CHECK */
	$wpss_key_values 		= rs_wpss_get_key_values();
	$wpss_jq_key 			= $wpss_key_values['wpss_jq_key'];
	$wpss_jq_val 			= $wpss_key_values['wpss_jq_val'];
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* Fall back to FVFJS Keys instead of jQuery keys from jscripts.php */
		$wpss_jq_key 		= $wpss_key_values['wpss_js_key'];
		$wpss_jq_val 		= $wpss_key_values['wpss_js_val'];
	}
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] ) ? $_POST[$wpss_jq_key] : '';
	if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
		$wpss_error_code		.= ' '.$pref.'JQHFT-7';
		$gf_jsck_error			= TRUE;
		$err_cod				= 'jsck_error';
		$err_msg				= __( 'Sorry, there was an error. Please be sure JavaScript and Cookies are enabled in your browser and try again.', 'wp-spamshield' );
		$errors_3p[$err_cod]	= $err_msg;
	}

	/* EMAIL BLACKLIST */
	$collected_emails = array();
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		$k_lc = WPSS_Func::lower( $k );
		$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
		if( is_email( $v_lc ) ) {
			$email_domain = rs_wpss_parse_email( $v_lc, 'domain' );
			if( $email_domain === $server_email_domain ) { continue; }
			if( WPSS_Filters::email_blacklist_chk( $v_lc ) ) {
				$wpss_error_code .= ' '.$pref.'9200E-BL';
				if( $gf_jsck_error !== TRUE ) {
					$err_cod				= 'blacklist_email_error';
					$err_msg				= __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
					$errors_3p[$err_cod]	= $err_msg;
				}
				break;
			}
			$collected_emails[] = $v_lc;
		}
	}

	/* CONTACT FORM CONTENT BLACKLIST */
	foreach( $_POST as $k => $v ) {
		if( !is_string( $v ) ) { continue; }
		/* $k_lc = WPSS_Func::lower( $k ); */
		$v_lc = WPSS_Func::lower( trim( stripslashes( $v ) ) );
		if( FALSE !== strpos( $k_lc, 'message' ) && WPSS_Filters::cf_content_blacklist_chk( $v_lc ) ) {
			$wpss_error_code .= ' '.$pref.'10400C-BL';
			if( $gf_jsck_error !== TRUE ) {
				$err_cod				= 'blacklist_content_error';
				$err_msg				= __( 'Message appears to be spam.', 'wp-spamshield' );
				$errors_3p[$err_cod]	= $err_msg;
			}
			break;
		}
	}

	/* BAD ROBOT BLACKLIST */
	$bad_robot_filter_data = WPSS_Filters::bad_robot_blacklist_chk( $form_type, $gf_filter_status );
	$gf_filter_status = $bad_robot_filter_data['status'];
	$bad_robot_blacklisted = $bad_robot_filter_data['blacklisted'];
	if( !empty( $bad_robot_blacklisted ) ) {
		$wpss_error_code .= $bad_robot_filter_data['error_code'];
		$gf_badrobot_error = TRUE;
		if( $gf_jsck_error !== TRUE ) {
			$err_cod				= 'badrobot_error';
			$err_msg				= __( 'That action is currently not allowed.' );
			$errors_3p[$err_cod]	= $err_msg;
		}
	}

	/* Enhanced Comment Blacklist - GF-0-ECBL */
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
		if( empty( $collected_emails ) ) {
			if( rs_wpss_blacklist_check() ) {
				$wpss_error_code .= ' '.$pref.'0-ECBL';
				$err_cod				= 'blacklist_ecbl_error';
				$err_msg				= __( 'That action is currently not allowed.' );
				$errors_3p[$err_cod]	= $err_msg;
			}
		} else {
			foreach( $collected_emails as $k => $v ) {
				if( rs_wpss_blacklist_check( '', $v ) ) {
					$wpss_error_code .= ' '.$pref.'0-ECBL';
					$err_cod				= 'blacklist_ecbl_error';
					$err_msg				= __( 'That action is currently not allowed.' );
					$errors_3p[$err_cod]	= $err_msg;
					break;
				}
			}
		}
	}

	/* BLACKLISTED USER */
	if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
		$wpss_error_code .= ' '.$pref.'0-BL';
		$err_cod				= 'blacklisted_user_error';
		$err_msg				= __( 'That action is currently not allowed.' );
		$errors_3p[$err_cod]	= $err_msg;
	}

	$wpss_error_code = trim( $wpss_error_code );
	if( ( FALSE !== strpos( $wpss_error_code, '0-BL' ) || FALSE !== strpos( $wpss_error_code, '0-ECBL' ) ) && FALSE === strpos( $wpss_error_code, '00-BL' ) && FALSE === strpos( $wpss_error_code, '00-ECBL' ) ) {
		@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Gravity Forms have been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
	}

	if( !empty( $wpss_error_code ) ) {
		$spam = TRUE;
		rs_wpss_update_accept_status( $form_auth_dat, 'r', 'Line: '.__LINE__, $wpss_error_code );
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $gf_serial_post );
		}
	} else {
		rs_wpss_update_accept_status( $form_auth_dat, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $form_auth_dat, $wpss_error_code, $form_type, $gf_serial_post );
		}
	}

	/* Now output error message */
	if( !empty( $wpss_error_code ) ) {
		$error_msg = '';
		foreach( $errors_3p as $c => $m ) {
			$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
		}
		WP_SpamShield::wp_die( $error_msg, TRUE );
	}

}

/* SPAM CHECK FOR OTHER PLUGINS AND FORMS - END */


function rs_wpss_comment_moderation_addendum( $text, $comment_id ) {
	$spamshield_options = WP_SpamShield::get_option();
	if( !current_user_can( 'manage_options' ) ) {
		$ip = WP_SpamShield::get_ip_addr();
		$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
		$text .= "\r\n".$blacklist_text.' '. WP_SpamShield::blacklist_url( $ip ) ."\r\n";
	}
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		$text = rs_wpss_extra_notification_data( $text, $spamshield_options );
	}
	return $text;
}

function rs_wpss_comment_notification_addendum( $text, $comment_id ) {
	$spamshield_options = WP_SpamShield::get_option();
	if( !current_user_can( 'manage_options' ) ) {
		$ip = WP_SpamShield::get_ip_addr();
		$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
		$text .= "\r\n".$blacklist_text.' '. WP_SpamShield::blacklist_url( $ip ) ."\r\n";
	}
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		$text = rs_wpss_extra_notification_data( $text, $spamshield_options );
	}
	return $text;
}

function rs_wpss_cf7_contact_form_addendum( $components ) {
	if( strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) !== 0 ) { return $components; }
	$spamshield_options = WP_SpamShield::get_option();
	extract( $components );
	$text = $body;
	if( !WP_SpamShield::is_support_url() || FALSE !== strpos( $subject, '| ADMIN |' ) ) {
		if( empty( $spamshield_options['hide_extra_data'] ) ) {
			$text = rs_wpss_extra_notification_data( $text, $spamshield_options, TRUE );
		}
		if( !current_user_can( 'manage_options' ) ) {
			$ip = WP_SpamShield::get_ip_addr();
			$blacklist_text = __( 'Blacklist the IP Address:', 'wp-spamshield' );
			$text .= "\r\n".$blacklist_text.' '. WP_SpamShield::blacklist_url( $ip ) ."\r\n";
		}
	}
	$body = $text;
	$components = compact( 'subject', 'sender', 'body', 'recipient', 'additional_headers', 'attachments' );
	return $components;
}

function rs_wpss_comment_moderation_check( $emails, $comment_id ) {
	/* Check user roles of email recipients to make sure only admins receive modified emails. */
	if( empty( $emails ) ) { return $emails; }
	foreach( (array) $emails as $i => $email ) {
		if( !WP_SpamShield::is_email_admin( $email ) ) { return $emails; }
	}
	add_filter( 'comment_moderation_text', 'rs_wpss_comment_moderation_addendum', 10, 2 );
	return $emails;
}

function rs_wpss_comment_notification_check( $emails, $comment_id ) {
	/**
	 *  Check user roles of email recipients to make sure only admins receive modified emails.
	 *  Necessitated by other plugins adding their own email notification functions and carelessly using duplicate WordPress filter hooks without proper validation or authentication.
	 */
	if( empty( $emails ) ) { return $emails; }
	foreach( (array) $emails as $i => $email ) {
		if( !WP_SpamShield::is_email_admin( $email ) ) { return $emails; }
	}
	add_filter( 'comment_notification_text', 'rs_wpss_comment_notification_addendum', 10, 2 );
	return $emails;
}

function rs_wpss_extra_notification_data( $text, $spamshield_options = NULL, $cf7 = FALSE ) {
	if( empty( $spamshield_options ) ) {
		$spamshield_options = WP_SpamShield::get_option();
	}
	$post_jsonst 		= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_jsonst_lc		= WPSS_Func::lower( $post_jsonst );
	$post_ref2xjs_lc	= WPSS_Func::lower( $post_ref2xjs );

	$eml_eol = "\r\n"; /* Added 1.9.7 */
	if( !empty( $cf7 ) ) { $text .= $eml_eol; }
	$thin_line = str_repeat( '─', 78 );

	/* IP / PROXY INFO - BEGIN */
	global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
	extract( $wpss_ip_proxy_info );
	/* IP / PROXY INFO - END */

	if( WP_SpamShield::is_debug() ) {
		global $wpss_geolocation; if( empty ( $wpss_geolocation ) ) { $wpss_geolocation = rs_wpss_wf_geoiploc( $ip, TRUE ); }
	} else {
		global $wpss_geoloc_short; if( empty ( $wpss_geoloc_short ) ) { $wpss_geoloc_short = rs_wpss_wf_geoiploc_short( $ip ); }
	}

	/* Sanitized versions for output */
	$wpss_http_user_agent 			= rs_wpss_get_user_agent();
	$wpss_http_browser 				= rs_wpss_get_browser();
	$wpss_http_referer				= rs_wpss_get_referrer( FALSE, TRUE, TRUE ); /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
	if( empty( $spamshield_options['hide_extra_data'] ) ) {
		if( !empty( $cf7 ) ) { $text .= $eml_eol; }
		$text .= $eml_eol;
		$text .= $thin_line.$eml_eol;
		$text .= __( 'Additional Technical Data Added by WP-SpamShield', 'wp-spamshield' ) . $eml_eol;
		$text .= $thin_line.$eml_eol;
		/* DEBUG ONLY - BEGIN */
		if( WP_SpamShield::is_debug() ) {
			/* CF7 Only */
			if( !empty( $cf7 ) ) {
				$wpss_log_session_data = rs_wpss_get_log_session_data();
				extract( $wpss_log_session_data );
				$noda = '[No Data]';
				/* Timer - BEGIN*/
				$wpss_time_end					= microtime( TRUE );
				if( empty( $wpss_time_init ) && !empty( $wpss_timestamp_init ) ) {
					$wpss_time_init = $wpss_timestamp_init;
				}
				if( !empty( $wpss_time_init ) ) {
					$wpss_time_on_site			= rs_wpss_timer( $wpss_time_init, $wpss_time_end, TRUE, 2 );
				} else { $wpss_time_on_site 	= $noda; }
				if( !empty( $wpss_timestamp_init ) ) {
					$wpss_site_entry_time		= get_date_from_gmt( date( WPSS_DATE_FULL, $wpss_timestamp_init ), WPSS_DATE_LONG ); /* Added 1.7.3 */
				} else { $wpss_site_entry_time 	= $noda; }
				/* Timer - END */
				$wpss_hits_per_page = str_replace( WPSS_EOL, $eml_eol, $wpss_hits_per_page );
				$text .= "Pages Visited: ".$wpss_hits_per_page;
				$text .= "Time on Site: ['".$wpss_time_on_site."']".$eml_eol;
			}
			if( !empty( $post_ref2xjs ) ) {
				$ref2xJS = addslashes( urldecode( $post_ref2xjs ) );
				$ref2xJS = str_replace( '%3A', ':', $ref2xJS );
				$ref2xJS = str_replace( ' ', '+', $ref2xJS );
				$ref2xJS = esc_url_raw( $ref2xJS );
				$text .= $eml_eol."JS Page Referrer Check: $ref2xJS".$eml_eol;
			}
			if( !empty( $post_jsonst ) ) {
				$JSONST = sanitize_text_field( $post_jsonst );
				$text .= $eml_eol."JSONST: $JSONST".$eml_eol;
			}
		}
		/* DEBUG ONLY - END */
		else {
			if( !empty( $post_ref2xjs ) ) {
				$ref2xJS = addslashes( urldecode( $post_ref2xjs ) );
				$ref2xJS = str_replace( '%3A', ':', $ref2xJS );
				$ref2xJS = str_replace( ' ', '+', $ref2xJS );
				$ref2xJS = esc_url_raw( $ref2xJS );
				$text .= $eml_eol . __( 'Page Referrer Check', 'wp-spamshield' ) . ': '.$ref2xJS.$eml_eol;
			}
		}
		$text .= $eml_eol;
		$text .= __( 'Referrer', 'wp-spamshield' ) . ': '.$wpss_http_referer.$eml_eol.$eml_eol; /* Initial referrer, aka "Referring Site" - Changed 1.7.9 */
		if( WP_SpamShield::is_debug() && ( !empty( $_COOKIE['_referrer_og'] ) || !empty( $_COOKIE['_referrer_og'] ) ) ) { /* DEBUG ONLY */
			if( !empty( $_COOKIE['_referrer_og'] ) ) {
				$text .= __( 'Clicky Referrer', 'wp-spamshield' ) . ': '.$_COOKIE['_referrer_og'].$eml_eol;
			} elseif( !empty( $_COOKIE['JCS_INENREF'] ) ) {
				$text .= __( 'JCS_INENREF Referrer', 'wp-spamshield' ) . ': '.$_COOKIE['JCS_INENREF'].$eml_eol;
			}
			$text .= $eml_eol;
		}
		$text .= __( 'User-Agent (Browser/OS)', 'wp-spamshield' ) . ': '.$wpss_http_user_agent.$eml_eol;
		if( !empty( $wpss_http_browser ) ) {
			$text .= __( 'Browser', 'wp-spamshield' ) . ': '.$wpss_http_browser.$eml_eol;
		}
		if( WP_SpamShield::is_debug() ) {
			if( !empty ( $wpss_geolocation ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
				$text .= __( 'Location', 'wp-spamshield' ) . ': '.$wpss_geolocation.$eml_eol;
			}
		} else {
			if( !empty ( $wpss_geoloc_short ) && rs_wpss_is_lang_en_us() ) { /* English only for now; TO DO: TRANSLATE */
				$text .= __( 'Country', 'wp-spamshield' ) . ': '.$wpss_geoloc_short.$eml_eol;
			}
		}
		$text .= __( 'IP Address', 'wp-spamshield' ) . ': '.$ip.$eml_eol;
		$text .= __( 'Server', 'wp-spamshield' ) . ': '.$reverse_dns.$eml_eol;
		$text .= __( 'IP Address Lookup', 'wp-spamshield' ) . ': http://ipaddressdata.com/'.$ip."\r\n\r\n";
		$text .= '(' . __( 'This data is helpful if you need to submit a spam sample.', 'wp-spamshield' ) . ')'.$eml_eol;
	}
	return $text;
}

function rs_wpss_add_ip_to_blacklist( $ip_to_blacklist ) {
	$blacklist_keys 		= trim( stripslashes( get_option( 'blacklist_keys' ) ) );
	$blacklist_keys_update	= $blacklist_keys.WPSS_EOL.$ip_to_blacklist;
	rs_wpss_update_bw_list_keys( 'black', $blacklist_keys_update );
}

/**
 *	Does comment or contact form contain blacklisted characters, words, IP addresses, or email addresses.
 *	Fires at end of ( Comment | Contact Form | Registration | CF7 | GF | Misc Form ) filters.
 *	Upgrade from WordPress' built-in and flawed wp_blacklist_check() function.
 *	Removed User-Agent filter from wp_blacklist_check() - Not a good idea to let users play with User-Agent filtering, most people don't realize this will be tested, leading to false-positives.
 *	Also, it's not in the documentation...nowhere in the WP Dashboard does it mention testing against User-Agents.
 *	@dependencies	...
 *	@since			1.5.4
 *	@param			string	$author			The author name of the submitter
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: author name.
 *	@param			string	$email			The email of the submitter
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's email.
 *	@param			string	$url			The url used in the submission
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's URL.
 *	@param			string	$content 		The submitted content
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: content.
 *	@param			string	$user_ip		The submitter IP address
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's IP address.
 *	@param			string	$user_agent		The submitter's browser / user agent
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's browser / user agent. - Ignored
 *	@param			string	$user_server	The submitter's server (reverse DNS of IP)
 *											Comment | Contact Form | Registration | CF7 | GF | Misc Form: author's server (reverse DNS of IP).
 *	@return			bool	TRUE if submission contains blacklisted content, FALSE if submission does not
 */
function rs_wpss_blacklist_check( $author = NULL, $email = NULL, $url = NULL, $content = NULL, $user_ip = NULL, $user_agent = NULL, $user_server = NULL ) {
	$blacklist_keys = trim( stripslashes( get_option( 'blacklist_keys' ) ) );
	if( empty( $blacklist_keys ) ) { return FALSE; } /* If blacklist keys are empty */

	if( empty( $user_ip ) || empty( $user_server ) ) {
		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */
		$user_ip = $ip; $user_server = $reverse_dns_lc;
	}

	if( FALSE !== strpos( $blacklist_keys, '[WPSS-ECBL][COUNTRY]' ) ) {
		global $wpss_geoiploc_data; if( empty ( $wpss_geoiploc_data ) ) { $wpss_geoiploc_data = rs_wpss_wf_geoiploc( $user_ip ); }
		if( !empty ( $wpss_geoiploc_data ) ) { extract( $wpss_geoiploc_data ); }
	}
	$blacklist_keys_arr = explode( WPSS_EOL, $blacklist_keys );
	foreach( (array) $blacklist_keys_arr as $key ) {
		$key = trim( $key );
		/* Skip empty lines */
		if( empty( $key ) ) { continue; }
		/* Do some escaping magic so that '~' chars in the spam words don't break things: */
		$key_pq = rs_wpss_preg_quote( $key );
		$pattern_rgx = "~".$key_pq."~i";
		if( 0 === strpos( $key, '[WPSS-ECBL]' ) ) {
			$key = str_replace( '[WPSS-ECBL]', '', $key );
			if( !WP_SpamShield::is_support_url() ) { /* Only if not on support URL - @since 1.9.9.5 */
				if( 0 === strpos( $key, '[SERVER]' ) && !empty( $user_server ) ) {
					$key = str_replace( '[SERVER]', '', $key );
					$referrer = rs_wpss_get_referrer( FALSE, TRUE, TRUE );
					$ref_domain = rs_wpss_get_domain( $referrer );
					if( 0 === strpos( $key, '[REF]' ) && !empty( $ref_domain ) ) { /* Added 1.8.1 */
						$key = str_replace( '[REF]', '', $key );
						$key_pq = rs_wpss_preg_quote( $key );
						if( WP_SpamShield::preg_match( "~".$key_pq."$~i", $ref_domain ) ) { return TRUE; }
					} elseif( 0 === strpos( $key, '.' ) || 0 === strpos( $key, '-' ) ) {
						$key_pq = rs_wpss_preg_quote( $key );
						if( WP_SpamShield::preg_match( "~".$key_pq."$~i", $user_server ) ) { return TRUE; }
					} elseif( $key === $user_server ) { return TRUE; }
				} elseif( 0 === strpos( $key, '[COUNTRY]' ) && !empty( $countryCode ) ) {
					/**
					 *	Country Blocking - Ex: '[WPSS-ECBL][COUNTRY]AA,BB,CC'
					 *	@since 1.9.5.2
					 *	Full list of ISO Country codes:
					 *	https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2
					 *	http://www.nationsonline.org/oneworld/country_code_list.htm
					 */
					$key = str_replace( array( '[COUNTRY]', ' ' ), array( '', '' ), $key );
					if( WP_SpamShield::preg_match( "~([A-Z]{2},?)+~", $key ) ) {
						$key_arr = explode( ',', $key );
						if( $key === $countryCode || WPSS_PHP::in_array( $countryCode, $key_arr ) ) { return TRUE; }
					}
				}
			}
		} elseif( is_email( $key ) ) {
			if( !empty( $email ) && rs_wpss_sanitize_gmail( $key ) === rs_wpss_sanitize_gmail( $email ) ) { return TRUE; }
		} elseif( WP_SpamShield::is_valid_ip( $key, '', TRUE ) ) { /* IP C-block */
			if( !empty( $user_ip ) && strpos( $user_ip, $key ) === 0 ) { return TRUE; }
		} elseif( WP_SpamShield::is_valid_ip( $key ) ) { /* Complete IP Address */
			if( !empty( $user_ip ) && $key === $user_ip ) { return TRUE; }
		} elseif(
			   ( !empty( $author ) 	&& WP_SpamShield::preg_match( $pattern_rgx, $author ) )
			|| ( !empty( $url ) 	&& WP_SpamShield::preg_match( $pattern_rgx, $url ) )
			|| ( !empty( $content )	&& WP_SpamShield::preg_match( $pattern_rgx, $content ) )
			) {
			return TRUE;
		}
	}
	return FALSE;
}

/**
 *	Fires at beginning of contact form, comment content, and other filters.
 *	Bypasses filters if TRUE.
 *	Still subject to cookies test on contact form to prevent potential abuse by bots.
 *	@since			1.7.4
 *	@param			string	$email	Comment or Contact Form author's email.
 *	@return			bool	TRUE if submission contains blacklisted content, FALSE if submission does not
 */
function rs_wpss_whitelist_check( $email = NULL, $ip = NULL, $ignore_ip = FALSE ) {
	if( ( empty( $email ) && WP_SpamShield::is_valid_ip( $ip ) ) || ( FALSE === $ignore_ip && rs_wpss_is_admin_ip( $ip ) ) || ( TRUE === $ignore_ip && WP_SpamShield::is_email_admin( $email ) ) ) { return TRUE; }
	$whitelist_enabled	= WP_SpamShield::get_option( 'enable_whitelist' );
	if( empty( $whitelist_enabled ) ) { return FALSE; }
	if( FALSE === $ignore_ip ) {
		$ip = ( WP_SpamShield::is_valid_ip( $ip ) ) ? $ip : WP_SpamShield::get_ip_addr();
	}
	$whitelist_keys		= trim( stripslashes( get_option( 'spamshield_whitelist_keys' ) ) );
	if( empty( $whitelist_keys ) ) { return FALSE; } /* If whitelist keys are empty */
	$whitelist_keys		= str_replace( array( "\r\n", "\r", "\f", "\v", "\n\n\n", "\n\n" ), "\n", $whitelist_keys );
	$whitelist_keys_arr	= explode( "\n", $whitelist_keys );
	foreach( (array) $whitelist_keys_arr as $key ) {
		$key = trim( $key );
		/* Skip empty lines */
		if( empty( $key ) ) { continue; }
		if( !empty( $email ) && is_email( $key ) && $key === $email ) { return TRUE; }
		if( FALSE === $ignore_ip && WP_SpamShield::is_valid_ip( $key ) && $key === $ip ) { return TRUE; }
	}
	return FALSE;
}

function rs_wpss_get_bw_list_keys( $list ) {
	/**
	 *	Get blacklist or whitelist keys
	 *	$list - 'white' or 'black'
	 */
	$opname = array( 'white' => 'spamshield_whitelist_keys', 'black' => 'blacklist_keys' );
	$keys	= trim( stripslashes( get_option( $opname[$list] ) ) );
	$keys	= str_replace( array( "\r\n", "\r", "\f", "\v", "\n\n\n", "\n\n" ), "\n", $keys );
	$arr	= explode( "\n", $keys );
	$tmp	= WP_SpamShield::sort_unique( $arr );
	$keys	= implode( "\n", $tmp );
	return $keys;
}

function rs_wpss_update_bw_list_keys( $list, $keys ) {
	/**
	 *	Update blacklist or whitelist keys
	 *	$list - 'white' or 'black'
	 */
	$opname = array( 'white' => 'spamshield_whitelist_keys', 'black' => 'blacklist_keys' );
	$keys	= str_replace( array( "\r\n", "\r", "\f", "\v", "\n\n\n", "\n\n" ), "\n", $keys );
	$arr	= explode( "\n", $keys );
	$tmp	= WP_SpamShield::sort_unique( $arr );
	$keys	= implode( "\n", $tmp );
	update_option( $opname[$list], $keys, FALSE );
}


/* Spam Registration Protection - BEGIN */

function rs_wpss_wc_before_register() {
	$wc_reg_enabled = get_option( 'woocommerce_enable_myaccount_registration' );
	if( 'yes' !== $wc_reg_enabled && TRUE != $wc_reg_enabled ) { return; }
	global $wpss_wc_reg_form_active; $wpss_wc_reg_form_active = TRUE;
}

function rs_wpss_register_form_append() {
	if( rs_wpss_is_user_logged_in() ) { return; }
	global $wpss_wc_reg_form_active,$wpss_reg_form_complete;
	$spamshield_options = WP_SpamShield::get_option();

	/* Check if registration spam shield is disabled, or this function has already been run (usually by 3rd party plugin) */
	if( !empty( $spamshield_options['registration_shield_disable'] ) || !empty( $wpss_reg_form_complete ) ) { return; }

	/* BYPASS - HOOK */
	$reg_form_bypass = apply_filters( 'wpss_registration_form_bypass', FALSE );
	if( !empty( $reg_form_bypass ) ) { return; }

	/* BYPASS CHECKS COMPLETE - NOW START */

	$reg_form_append = $wpss_reg_form_complete = $buddypress_status = $wc_status = $s2member_status = $affiliates_status = $wpmembers_status = FALSE;
	$i18n_cap = !rs_wpss_is_lang_en_us() ? 'def' : '';
	$wc_style = $wc_req = ''; $end_line1 = '<br />'; $end_line2 = '</label>'; $reg_input_class = 'input';
	if( !empty( $wpss_wc_reg_form_active ) || rs_wpss_is_wc_login_page() || rs_wpss_is_wc_registration_page() ) { /* Check if we're on a WooCommerce Registration page */
		$wc_status = TRUE; $wpss_wc_reg_inprog = TRUE; $wc_style = ' class="form-row form-row-wide"'; $wc_req = ''; $reg_input_class = 'input-text'; $end_line1 = '</label>'; $end_line2 = '';
	}
	if( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) ) { $s2member_status = TRUE; }
	if( defined( 'AFFILIATES_CORE_VERSION' ) ) { $affiliates_status = TRUE; }
	if( defined( 'WPMEM_VERSION' ) ) { $wpmembers_status = TRUE; }

	if( FALSE === $s2member_status && FALSE === $affiliates_status ) {
		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$new_fields = array(
			'first_name' 	=> rs_wpss_first_name_txt( $i18n_cap ),
			'last_name' 	=> rs_wpss_last_name_txt( $i18n_cap ),
			'disp_name' 	=> rs_wpss_disp_name_txt( $i18n_cap ),
			);
		if( TRUE === $wpmembers_status ) {
			unset( $new_fields['first_name'], $new_fields['last_name'] );
		}

		if( TRUE === $wc_status ) {
			unset( $new_fields['disp_name'] );
		}

		foreach( $new_fields as $k => $v ) {
			$reg_form_append .= '	<p'.$wc_style.'>
		<label for="'.$k.'">'.$v.$wc_req.$end_line1.'
		<input type="text" name="'.$k.'" id="'.$k.'" class="'.$reg_input_class.'" value="" size="25" />'.$end_line2.'
	</p>
';
		}
	}

	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || TRUE === $wc_status || TRUE === $affiliates_status || rs_wpss_is_3p_register_page() ) {
		$wpss_key_values 		= rs_wpss_get_key_values();
		$wpss_js_key 			= $wpss_key_values['wpss_js_key'];
		$wpss_js_val 			= $wpss_key_values['wpss_js_val'];
		global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
		if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize' ); }
		if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
		$reg_form_append .= WPSS_EOL."\t".$ao_noop_open.'<script type=\'text/javascript\'>'.WPSS_EOL."\t".'/* <![CDATA[ */'.WPSS_EOL."\t".WPSS_REF2XJS.'=escape(document[\'referrer\']);'.WPSS_EOL."\t".'hf3N=\''.$wpss_js_key.'\';'.WPSS_EOL."\t".'hf3V=\''.$wpss_js_val.'\';'.WPSS_EOL."\t".'document.write("<input type=\'hidden\' name=\''.WPSS_REF2XJS.'\' value=\'"+'.WPSS_REF2XJS.'+"\' /><input type=\'hidden\' name=\'"+hf3N+"\' value=\'"+hf3V+"\' />");'.WPSS_EOL."\t".'/* ]]> */'.WPSS_EOL."\t".'</script>'.$ao_noop_close;
	}
	$reg_form_append .= WPSS_EOL."\t".'<noscript><input type="hidden" name="'.WPSS_JSONST.'" value="NS3" /></noscript>'.WPSS_EOL."\t";
	$wpss_js_disabled_msg 	= __( 'Currently you have JavaScript disabled. In order to register, please make sure JavaScript and Cookies are enabled, and reload the page.', 'wp-spamshield' );
	$wpss_js_enable_msg 	= __( 'Click here for instructions on how to enable JavaScript in your browser.', 'wp-spamshield' );
	$reg_form_append .= '<noscript><p><strong>'.$wpss_js_disabled_msg.'</strong> <a href="http://enable-javascript.com/" rel="nofollow external" >'.$wpss_js_enable_msg.'</a><br /><br /></p></noscript>'.WPSS_EOL."\t";

	/* If need to add anything else to registration area, start here */


	/* FORM COMPLETE */
	$wpss_wc_reg_form_active = FALSE;
	$wpss_reg_form_complete = TRUE;

	if( TRUE === $affiliates_status ) { return $reg_form_append; } else { echo $reg_form_append; }
}

if( !function_exists('wp_new_user_notification') ) {
	function wp_new_user_notification( $user_id, $deprecated = NULL, $notify = '' ) {
		/**
		 *  WPSS Redefined: Copied from includes/pluggable.php in WordPress core and added filters.
		 *  @param int		$user_id		User ID.
		 *  @param null		$deprecated		Not used (argument deprecated).
		 *  @param string	$notify			Optional. Type of notification that should happen. Accepts 'admin' or an empty string (admin only), 'user', or 'both' (admin and user). Default empty.
		 *	Modified in 1.9.5.7 to add compatibility for WP 4.3+ and maintain backwards compatibility for earlier WP versions.
		 *  Modified in 1.9.6.2 for WP 4.3.1+
		 *  Modified in 1.9.7.4 to fix backwards compatibility for WP versions < 4.3.
		 *  Modified in 1.9.9.1 for 4.6+: The `$notify` parameter accepts 'user' for sending notification only to the user created.
		 */
		global $wpdb, $wp_hasher;
		if( WP_SpamShield::is_wp_ver('4.3.1') && $deprecated !== NULL ) {
			_deprecated_argument( __FUNCTION__, '4.3.1' );
		}
		$user = get_userdata( $user_id );
		$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		if ( 'user' !== $notify ) {
			$admin_message	= sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			$admin_message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
			$admin_message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "\r\n";
			$admin_message	= apply_filters( 'wpss_signup_notification_text_admin', $admin_message, $user_id, $user );
			WP_SpamShield::mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $admin_message );
		}
		if( WP_SpamShield::is_wp_ver('4.3') && !WP_SpamShield::is_wp_ver('4.3.1') ) {
			$notify = $deprecated; /* 4.3 only - '$deprecated' is '$notify' */
		}
		/* '$deprecated' was the pre-4.3 '$plaintext_pass'. An empty '$plaintext_pass' didn't send a user notifcation. */
		if( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) { return; } /* Changed WP 4.4 */
		$key = wp_generate_password( 20, false );
		do_action( 'retrieve_password_key', $user->user_login, $key );
		if( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
		$user_message  = sprintf(__('Username: %s'), $user->user_login) . "\r\n";
		if( !WP_SpamShield::is_wp_ver('4.3') ) {
			$user_message .= sprintf(__('Password: %s'), $deprecated) . "\r\n"; /* $deprecated is the pre-4.3 '$plaintext_pass' */
		} else { /* 4.3+ */
			$user_message .= __( 'To set your password, visit the following address:' ) . "\r\n\r\n";
			$user_message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login') . ">\r\n\r\n";
		}
		$user_subject_text = 'Your username and password info';
		$user_message .= wp_login_url() . "\r\n";
		$user_message = apply_filters( 'wpss_signup_notification_text_user', $user_message, $user_id, $user );
		WP_SpamShield::mail( $user->user_email, sprintf( __( '[%s] '.$user_subject_text ), $blogname ), $user_message );
	}

	/* WPSS Added */

	function rs_wpss_modify_signup_notification_admin( $text, $user_id, $user ) {
		/* Check if registration spam shield is disabled */
		$spamshield_options = WP_SpamShield::get_option();
		if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return $text; }

		$wpss_display_name 		= $user->display_name;
		$wpss_user_firstname 	= $user->user_firstname;
		$wpss_user_lastname 	= $user->user_lastname;
		$wpss_user_email		= $user->user_email;
		$wpss_user_url			= $user->user_url;
		$wpss_user_login 		= $user->user_login;
		$wpss_user_id	 		= $user->ID;

		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$disp_name_txt 			= rs_wpss_disp_name_txt( $i18n_cap );
		$first_name_txt 		= rs_wpss_first_name_txt( $i18n_cap );
		$last_name_txt 			= rs_wpss_last_name_txt( $i18n_cap );
		$user_id_txt 			= __( 'User ID', 'wp-spamshield' );

		$text .= "\r\n";
		$text .= sprintf( $disp_name_txt	.': %s', $wpss_display_name		) . "\r\n\r\n";
		$text .= sprintf( $first_name_txt	.': %s', $wpss_user_firstname	) . "\r\n\r\n";
		$text .= sprintf( $last_name_txt	.': %s', $wpss_user_lastname	) . "\r\n\r\n";
		$text .= sprintf( $user_id_txt		.': %s', $wpss_user_id			) . "\r\n\r\n";
		$text .= "\r\n";

		$text = rs_wpss_extra_notification_data( $text, $spamshield_options );

		return $text;
	}

	function rs_wpss_modify_signup_notification_user( $text, $user_id, $user ) {
		/* Check if registration spam shield is disabled */
		$spamshield_options = WP_SpamShield::get_option();
		if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return $text; }

		/* Add three new fields */
		$wpss_display_name 		= $user->display_name;
		$wpss_user_firstname 	= $user->user_firstname;
		$wpss_user_lastname 	= $user->user_lastname;
		$wpss_user_email		= $user->user_email;
		$wpss_user_url			= $user->user_url;
		$wpss_user_login 		= $user->user_login;
		$wpss_user_id	 		= $user->ID;

		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR . "/admin-$locale.mo" ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$disp_name_txt 			= rs_wpss_disp_name_txt( $i18n_cap );
		$first_name_txt 		= rs_wpss_first_name_txt( $i18n_cap );
		$last_name_txt 			= rs_wpss_last_name_txt( $i18n_cap );

		$text .= "\r\n\r\n";
		$text .= sprintf( $disp_name_txt	.': %s', $wpss_display_name		) . "\r\n";
		$text .= sprintf( $first_name_txt	.': %s', $wpss_user_firstname	) . "\r\n";
		$text .= sprintf( $last_name_txt	.': %s', $wpss_user_lastname	) . "\r\n";
		$text .= "\r\n";

		return $text;
	}

}

function rs_wpss_sanitize_new_user_email( $email ) {
	return rs_wpss_sanitize_gmail( $email, TRUE );
}

function rs_wpss_wc_check_new_user( $errors ) {
	/* WooCommerce wrapper for rs_wpss_check_new_user() */
	if( isset( $_GET['action'] ) && $_GET['action'] === 'woocommerce_checkout' ) { return $errors; }
	$ecom_urls	= unserialize( WPSS_ECOM_URLS );
	if( isset( $_POST['email_2'] ) ) { unset( $_POST['email_2'] ); }
	$req_uri_lc	= WPSS_Func::lower( $_SERVER['REQUEST_URI'] );
	foreach( $ecom_urls as $k => $u ) {
		if( strpos( $req_uri_lc, $u ) !== FALSE ) { return $errors; }
	}
	global $wpss_wc_reg_inprog; $wpss_wc_reg_inprog = TRUE; /* WooCommerce registration is in progress */
	$errors		= rs_wpss_check_new_user( $errors );
	return $errors;
}

function rs_wpss_check_new_user( $errors = NULL, $user_login = NULL, $user_email = NULL ) {
	/* Error checking for new user registration */
	global $wpss_reg_err_chk_complete,$wpss_reg_inprog,$wpss_wc_reg_inprog;

	if( rs_wpss_is_user_logged_in() || !empty( $wpss_reg_err_chk_complete ) ) { return $errors; }
	$req_uri_lc	= WPSS_Func::lower( $_SERVER['REQUEST_URI'] );

	if( !empty( $wpss_wc_reg_inprog ) || WPSS_Compatibility::is_woocom_enabled() ) {
		/* Check if we're on a WooCommerce Checkout Page */
		if( ( isset( $_GET['action'] ) && $_GET['action'] === 'woocommerce_checkout' ) ) { return $errors; }
		$ecom_urls = unserialize( WPSS_ECOM_URLS );
		foreach( $ecom_urls as $k => $u ) {
			if( strpos( $req_uri_lc, $u ) !== FALSE ) { return $errors; }
		}
	} elseif( WPSS_Compatibility::is_ecom_enabled() ) {
		/* Check if we're on another e-commerce Checkout or Shopping Cart Page */
		$ecom_urls = unserialize( WPSS_ECOM_URLS );
		foreach( $ecom_urls as $k => $u ) {
			if( strpos( $req_uri_lc, $u ) !== FALSE ) { return $errors; }
		}
	}

	$spamshield_options = WP_SpamShield::get_option();
	if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return $errors; }

	/* BYPASS - HOOK */
	$reg_check_bypass = apply_filters( 'wpss_registration_check_bypass', FALSE );
	if( !empty( $reg_check_bypass ) ) { return $errors; }


	/* BYPASS CHECKS COMPLETE - NOW START */

	if( empty( $errors ) || !is_object( $errors ) ) { $errors = new WP_Error; }

	$reg_filter_status		= $wpss_error_code = $log_pref = '';
	$reg_jsck_error			= $reg_badrobot_error = $wpss_reg_err_chk_complete = $buddypress_status = $wc_status = $s2member_status = $wpmembers_status = $affiliates_status = FALSE;
	$wpss_reg_inprog		= TRUE;
	$ns_val					= 'NS3';
	$pref					= 'R-';
	$errors_3p				= array(); /* Error array for 3rd party plugins that don't follow WordPress standards for registration processing: BuddyPress, ... */
	$error_txt 				= rs_wpss_error_txt();

	if( class_exists( 'BuddyPress' ) ) {
		if( empty( $user_login ) && isset( $_POST['signup_username'] ) ) {
			$user_login			= WPSS_Func::lower( sanitize_user( wp_unslash( $_POST['signup_username'] ) ) );
			$buddypress_status	= TRUE;
			$log_pref			= 'bp-';
		}
		if( empty( $user_email ) && isset( $_POST['signup_email'] ) ) {
			$user_email			= WPSS_Func::lower( sanitize_email( wp_unslash( $_POST['signup_email'] ) ) );
			$buddypress_status	= TRUE;
			$log_pref			= 'bp-';
		}
	}

	if( !empty( $wpss_wc_reg_inprog ) ) { $wc_status = TRUE; $log_pref = 'wc-'; }
	if( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) ) { $s2member_status = TRUE; $log_pref = 's2-'; }
	if( defined( 'AFFILIATES_CORE_VERSION' ) ) { $affiliates_status = TRUE; $log_pref = 'aff-'; }
	if( defined( 'WPMEM_VERSION' ) ) { $wpmembers_status = TRUE; $log_pref = 'wpm-'; }

	if( TRUE === $wc_status ) {
		$user_login			= '';
		if( empty( $user_login ) && isset( $_POST['username'] ) ) {
			$user_login		= WPSS_Func::lower( sanitize_user( wp_unslash( $_POST['username'] ) ) );
		}
		if( empty( $user_email ) && isset( $_POST['email'] ) ) {
			$user_email		= WPSS_Func::lower( sanitize_email( wp_unslash( $_POST['email'] ) ) );
		}
		if( isset( $_POST['email_2'] ) ) { unset( $_POST['email_2'] ); }
		if( empty( $user_login ) || empty( $user_email ) ) { return $errors; }
		$wc_ajax_request	= rs_wpss_is_wc_ajax_request();
	}

	if( TRUE === $affiliates_status ) {
		if( empty( $user_login ) && isset( $_POST['user_login'] ) ) {
			$user_login		= WPSS_Func::lower( sanitize_user( wp_unslash( $_POST['user_login'] ) ) );
		}
		if( empty( $user_email ) && isset( $_POST['user_email'] ) ) {
			$user_email		= WPSS_Func::lower( sanitize_email( wp_unslash( $_POST['user_email'] ) ) );
		}
	}

	/* WPSS Whitelist Check */
	if( !empty( $spamshield_options['enable_whitelist'] ) && rs_wpss_whitelist_check( $user_email ) ) { return $errors; }

	if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR.WPSS_DS.'admin-'.$locale.'.mo' ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
	$new_fields = array(
		'first_name' 	=> rs_wpss_first_name_txt( $i18n_cap ),
		'last_name' 	=> rs_wpss_last_name_txt( $i18n_cap ),
		'disp_name' 	=> rs_wpss_disp_name_txt( $i18n_cap ),
	);
	$check_fields = (array) $new_fields + array(
		'user_login'	=> rs_wpss_user_login_txt( $i18n_cap ),
	);
	$user_data = array(
		'user_login'	=> $user_login,
		'user_email'	=> $user_email,
	);
	foreach( $new_fields as $k => $v ) {
		$user_data[$k] = ( isset( $_POST[$k] ) ) ? sanitize_text_field( wp_unslash( $_POST[$k] ) ) : '';
	}

	if( FALSE === $buddypress_status && FALSE === $wc_status && FALSE === $s2member_status && FALSE === $affiliates_status ) {
		/* Check New Fields for Blanks */
		$enter_your_x_txt = rs_wpss_enter_your_x_txt();
		foreach( $new_fields as $k => $v ) {
			$k_uc = WPSS_Func::upper( $k );
			if( empty( $_POST[$k]) ) {
				$errors->add( 'empty_'.$k, '<strong>'.$error_txt.':</strong> ' . sprintf( $enter_your_x_txt . '.', $v ) );
				$wpss_error_code .= ' R-BLANK-'.$k_uc;
			}
		}
	}

	/* BAD ROBOT TEST - BEGIN */
	$bad_robot_filter_data 	 = WPSS_Filters::bad_robot_blacklist_chk( 'register', $reg_filter_status, '', '', $user_data['disp_name'], $user_email );
	$reg_filter_status		 = $bad_robot_filter_data['status'];
	$bad_robot_blacklisted 	 = $bad_robot_filter_data['blacklisted'];
	if( !empty( $bad_robot_blacklisted ) ) {
		$wpss_error_code 	.= $bad_robot_filter_data['error_code'];
		$reg_badrobot_error	 = TRUE;
	}
	/* BAD ROBOT TEST - END */

	/* BAD ROBOTS */
	if( $reg_badrobot_error !== FALSE ) {
		$err_cod = 'badrobot_error';
		$err_msg = __( 'User registration is currently not allowed.' );
		if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
	}

	/* JS/COOKIES CHECK */
	$wpss_ck_key_bypass 	= $wpss_js_key_bypass = FALSE;
	$wpss_key_values 		= rs_wpss_get_key_values();
	extract( $wpss_key_values );
	$wpss_jsck_cookie_val	= !empty( $_COOKIE[$wpss_ck_key] )	? $_COOKIE[$wpss_ck_key]	: '';
	$wpss_jsck_field_val	= !empty( $_POST[$wpss_js_key] )	? $_POST[$wpss_js_key]		: '';
	$wpss_jsck_jquery_val	= !empty( $_POST[$wpss_jq_key] )	? $_POST[$wpss_jq_key]		: '';
	if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { /* 1.9.1 */
		$wpss_ck_key_bypass = TRUE;
	}
	if( empty( $wc_ajax_request ) ) {
		if( FALSE === $wpss_ck_key_bypass ) { /* 1.8.9 */
			/* If jscripts.php is disabled, these would be skipped - Compatibility Mode */
			if( $wpss_jsck_cookie_val !== $wpss_ck_val ) {
				$wpss_error_code .= ' '.$pref.'COOKIE-3';
				$reg_jsck_error = TRUE;
			}
			if( $wpss_jsck_jquery_val !== $wpss_jq_val ) {
				$wpss_error_code .= ' '.$pref.'JQHFT-3';
				$reg_jsck_error = TRUE;
			}
		}
		if( FALSE === $wpss_js_key_bypass ) {
			if( $wpss_jsck_field_val !== $wpss_js_val ) {
				$wpss_error_code .= ' '.$pref.'FVFJS-3';
				$reg_jsck_error = TRUE;
			}
		}
	}
	$post_jsonst 	= !empty( $_POST[WPSS_JSONST] ) ? trim( $_POST[WPSS_JSONST] ) : '';
	$post_jsonst_lc	= WPSS_Func::lower( $post_jsonst );
	if( FALSE === $buddypress_status ) {
		if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) {
			$wpss_error_code .= ' '.$pref.'JSONST-1000-3';
			$reg_jsck_error = TRUE;
		}
	}

	if( $reg_jsck_error !== FALSE && $reg_badrobot_error !== TRUE ) {
		$err_cod = 'jsck_error';
		$err_msg = __( 'JavaScript and Cookies are required in order to register. Please be sure JavaScript and Cookies are enabled in your browser, and reload the page.', 'wp-spamshield' ); /* NEEDS TRANSLATION */
		if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
	}

	if( FALSE === $wc_status ) {
		/* EMAIL BLACKLIST */
		if( WPSS_Filters::email_blacklist_chk( $user_email ) ) {
			$wpss_error_code .= ' '.$pref.'9200E-BL';
			if( $reg_badrobot_error !== TRUE && $reg_jsck_error !== TRUE ) {
				$err_cod = 'blacklist_email_error';
				$err_msg = __( 'Sorry, that email address is not allowed!' ) . ' ' . __( 'Please enter a valid email address.' );
				if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
			}
		}
	}

	if( FALSE === $buddypress_status && FALSE === $wc_status && FALSE === $s2member_status && FALSE === $affiliates_status ) {
		/* AUTHOR KEYPHRASE BLACKLIST */
		foreach( $user_data as $k => $v ) {
			$k_uc = WPSS_Func::upper( $k );
			if( ( $k === 'user_login' || $k === 'first_name' || $k === 'last_name' || $k === 'disp_name' ) && WPSS_Filters::anchortxt_blacklist_chk( $v, NULL, $k ) ) {
				$wpss_error_code .= ' '.$pref.'10500A-BL-'.$k_uc;
				if( $reg_badrobot_error !== TRUE && $reg_jsck_error !== TRUE ) {
					$cfk = $check_fields[$k];
					$errors->add( 'blacklist_'.$k.'_error', '<strong>'.$error_txt.':</strong> ' . sprintf( __( '"%1$s" appears to be spam. Please enter a different value in the <strong> %2$s </strong> field.', 'wp-spamshield' ), sanitize_text_field( $v ), $cfk ) );
				}
			}
		}
	}

	/* Enhanced Comment Blacklist - R-0-ECBL*/
	if( !empty( $spamshield_options['enhanced_comment_blacklist'] ) && empty( $wpss_error_code ) ) {
		if( rs_wpss_blacklist_check( '', $user_email ) ) {
			$wpss_error_code .= ' '.$pref.'0-ECBL';
			$err_cod = 'blacklist_ecbl_error';
			$err_msg = __( 'User registration is currently not allowed.' );
			if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
		}
	}

	if( FALSE === $wc_status ) {
		/* BLACKLISTED USER */
		if( empty( $wpss_error_code ) && rs_wpss_ubl_cache() ) {
			$wpss_error_code .= ' '.$pref.'0-BL';
			$err_cod = 'blacklisted_user_error';
			$err_msg = __( 'User registration is currently not allowed.' );
			if( TRUE === $buddypress_status ) { $errors_3p[$err_cod] = $err_msg; } else { $errors->add( $err_cod, '<strong>'.$error_txt.':</strong> ' . $err_msg ); }
		}
	}

	/* Done with Tests */

	/* Now Log the Errors, if any */

	$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
	$post_ref2xjs_lc	= WPSS_Func::lower( $post_ref2xjs );
	if( !empty( $post_ref2xjs ) ) {
		$ref2xJS = WPSS_Func::lower( addslashes( urldecode( $post_ref2xjs ) ) );
		$ref2xJS = str_replace( '%3a', ':', $ref2xJS );
		$ref2xJS = str_replace( ' ', '+', $ref2xJS );
		$wpss_javascript_page_referrer = esc_url_raw( $ref2xJS );
	} else { $wpss_javascript_page_referrer = '[None]'; }

	if( $post_jsonst_lc === 'ns1' || $post_jsonst_lc === 'ns2' || $post_jsonst_lc === 'ns3' || $post_jsonst_lc === 'ns4' || $post_jsonst_lc === 'ns5' ) { $wpss_jsonst = $post_jsonst; } else { $wpss_jsonst = '[None]'; }

	$user_id = 'None'; /* Possibly change to '' */

	$register_author_data = array(
		'display_name' 				=> $user_data['disp_name'],
		'user_firstname' 			=> $user_data['first_name'],
		'user_lastname' 			=> $user_data['last_name'],
		'user_email' 				=> $user_email,
		'user_login' 				=> $user_login,
		'ID' 						=> $user_id,
		'comment_author'			=> $user_data['disp_name'],
		'comment_author_email'		=> $user_email,
		'comment_author_url'		=> '',
		'javascript_page_referrer'	=> $wpss_javascript_page_referrer,
		'jsonst'					=> $wpss_jsonst,
	);
	if( empty( $register_author_data['comment_author'] ) && !empty( $user_login ) ) { $register_author_data['comment_author'] = $user_login; }

	unset( $wpss_javascript_page_referrer, $wpss_jsonst );

	$wpss_error_code = trim( $wpss_error_code );
	if( ( FALSE !== strpos( $wpss_error_code, '0-BL' ) || FALSE !== strpos( $wpss_error_code, '0-ECBL' ) ) && FALSE === strpos( $wpss_error_code, '00-BL' ) && FALSE === strpos( $wpss_error_code, '00-ECBL' ) ) {
		@WP_SpamShield::append_log_data( NULL, NULL, 'Blacklisted user detected. Registration has been temporarily disabled to prevent spam. ERROR CODE: '.$wpss_error_code );
	}

	if( !empty( $wpss_error_code ) ) {
		if( TRUE === $buddypress_status ) {
			$wpss_error_code = str_replace( 'R-', 'BPR-', $wpss_error_code );
		} elseif( TRUE === $wc_status ) {
			$wpss_error_code = str_replace( 'R-', 'WCR-', $wpss_error_code );
		} elseif( TRUE === $s2member_status ) {
			$wpss_error_code = str_replace( 'R-', 'S2R-', $wpss_error_code );
		} elseif( TRUE === $wpmembers_status ) {
			$wpss_error_code = str_replace( 'R-', 'WPMR-', $wpss_error_code );
		} elseif( TRUE === $affiliates_status ) {
			$wpss_error_code = str_replace( 'R-', 'AFFR-', $wpss_error_code );
		}
		rs_wpss_update_accept_status( $register_author_data, 'r', 'Line: '.__LINE__, $wpss_error_code );
		rs_wpss_increment_reg_count();
		if( !empty( $spamshield_options['comment_logging'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	} elseif( TRUE === $buddypress_status ) {
		rs_wpss_update_accept_status( $register_author_data, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	}

	/* Now return the error values, or output error message */
	if( TRUE === $wc_status ) { $wpss_wc_reg_inprog = FALSE; }
	if( !empty( $wpss_error_code ) ) {
		if( TRUE === $buddypress_status ) {
			$error_msg = '';
			foreach( $errors_3p as $c => $m ) {
				$error_msg .= '<strong>'.$error_txt.':</strong> '.$m.'<br /><br />'.WPSS_EOL;
			}
			WP_SpamShield::wp_die( $error_msg, TRUE );
		}
	} elseif( TRUE === $wc_status ) {
		rs_wpss_update_accept_status( $register_author_data, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	}
	$wpss_reg_inprog = FALSE; $wpss_reg_err_chk_complete = TRUE;
	return $errors;
}

function rs_wpss_user_register( $user_id ) {
	if( rs_wpss_is_login_page() || rs_wpss_is_3p_register_page() ) {
		$spamshield_options = WP_SpamShield::get_option();
		$buddypress_status 		= $s2member_status = $wpmembers_status = FALSE;
		$log_pref 				= '';

		/* Check if registration spam shield is disabled - Added in 1.6.9 */
		if( !empty( $spamshield_options['registration_shield_disable'] ) ) { return; }

		if( defined( 'WS_PLUGIN__S2MEMBER_VERSION' ) ) { $s2member_status = TRUE; $log_pref = 's2-'; }
		if( defined( 'AFFILIATES_CORE_VERSION' ) ) { $affiliates_status = TRUE; $log_pref = 'aff-'; }
		if( defined( 'WPMEM_VERSION' ) ) { $wpmembers_status = TRUE; $log_pref = 'wpm-'; }

		if( !rs_wpss_is_lang_en_us() ) { $locale = get_locale(); @load_textdomain( 'default', WP_LANG_DIR.WPSS_DS.'admin-'.$locale.'.mo' ); $i18n_cap = 'def'; } else { $i18n_cap = ''; }
		$new_fields = array(
			'first_name' 	=> rs_wpss_first_name_txt( $i18n_cap ),
			'last_name' 	=> rs_wpss_last_name_txt( $i18n_cap ),
			'disp_name' 	=> rs_wpss_disp_name_txt( $i18n_cap ),
		);
		$user_data = array();
		foreach( $new_fields as $k => $v ) {
			if( isset( $_POST[$k] ) ) { $user_data[$k] = sanitize_text_field( wp_unslash( $_POST[$k] ) ); } else { $user_data[$k] = ''; }
		}
		if( !empty( $user_data ) ) {
			$user_data['ID']			= $user_id;
			$user_data['display_name']	= $user_data['disp_name'];
			unset( $user_data['disp_name'] );
			wp_update_user( $user_data );
		}

		$wpss_display_name = $wpss_user_firstname = $wpss_user_lastname = $wpss_user_email = $wpss_user_url = $wpss_user_login = '';

		$user_info = get_userdata( $user_id );

		if( isset( $user_info->display_name ) ) 	{ $wpss_display_name	= $user_info->display_name; }
		if( isset( $user_info->user_firstname ) ) 	{ $wpss_user_firstname	= $user_info->user_firstname; }
		if( isset( $user_info->user_lastname ) ) 	{ $wpss_user_lastname	= $user_info->user_lastname; }
		if( isset( $user_info->user_email ) ) 		{ $wpss_user_email		= $user_info->user_email; }
		if( isset( $user_info->user_url ) ) 		{ $wpss_user_url		= $user_info->user_url; }
		if( isset( $user_info->user_login ) ) 		{ $wpss_user_login		= $user_info->user_login; }

		$wpss_comment_author 		= $wpss_display_name;
		$wpss_comment_author_email	= $wpss_user_email;
		$wpss_comment_author_url 	= $wpss_user_url;

		$register_author_data = array(
			'display_name' 			=> $wpss_display_name,
			'user_firstname' 		=> $wpss_user_firstname,
			'user_lastname' 		=> $wpss_user_lastname,
			'user_email' 			=> $wpss_user_email,
			'user_url' 				=> $wpss_user_url,
			'user_login' 			=> $wpss_user_login,
			'ID' 					=> $user_id,
			'comment_author'		=> $wpss_display_name,
			'comment_author_email'	=> $wpss_user_email,
			'comment_author_url'	=> $wpss_user_url,
			);

		$wpss_error_code = 'No Error';

		rs_wpss_update_user_ip( $user_id );

		rs_wpss_update_accept_status( $register_author_data, 'a', 'Line: '.__LINE__ );
		if( !empty( $spamshield_options['comment_logging'] ) && !empty( $spamshield_options['comment_logging_all'] ) ) {
			rs_wpss_log_data( $register_author_data, $wpss_error_code, $log_pref.'register' );
		}
	}
}

/* Spam Registration Protection - END */

/* Admin Functions - BEGIN */

function rs_wpss_admin_jp_fix( $get_jp_com_status = FALSE ) {
	/**
	 *	Fix compatibility with JetPack if active
	 *	The JP Comments module modifies WordPress' comment system core functionality, incapacitating MANY fine plugins.
	 *	Compatibility with JetPack Comments added in 1.9.2, so this only runs when in Compatibility Mode.
	 */
	global $wpss_jp_active,$wpss_jp_active_mods,$wpss_jp_fix;
	if( FALSE === $get_jp_com_status && ( is_multisite() || !empty( $wpss_jp_fix ) ) ) { return; }
	if( empty( $wpss_jp_active ) ) { $wpss_jp_active = WPSS_Compatibility::is_jp_active(); }
	if( !empty( $wpss_jp_active ) ) {
		if( empty( $wpss_jp_active_mods ) ) { $wpss_jp_active_mods = get_option( 'jetpack_active_modules' ); }
		if( !empty( $wpss_jp_active_mods ) && is_array( $wpss_jp_active_mods ) ) { $jp_com_key = array_search( 'comments', $wpss_jp_active_mods, TRUE ); } else { $wpss_jp_fix = TRUE; return FALSE; }
		if( isset( $jp_com_key ) && is_int( $jp_com_key ) ) {
			if( TRUE === $get_jp_com_status ) { return TRUE; }
			$jp_num_active_mods = count( $wpss_jp_active_mods );
			if( empty( $wpss_jp_active_mods ) ) { $jp_num_active_mods = 0; }
			if( $jp_num_active_mods < 2 ) { $wpss_jp_active_mods = array(); } else { unset( $wpss_jp_active_mods[$jp_com_key] ); }
			update_option( 'jetpack_active_modules', $wpss_jp_active_mods );
			@WP_SpamShield::append_log_data( NULL, NULL, 'JetPack Comments module deactivated.' );
		}
	}
	$wpss_jp_fix = TRUE;
}

function rs_wpss_admin_ao_fix() {
	/**
	 *	Fix compatibility with Autoptimize if active
	 *	@since 1.8.9.9, Modified in 1.9.7.3
	 *	The Autoptimize plugin forces the WP-SpamShield head JavaScript into the footer, which will cause problems. This fix automatically adds WP-SpamShield to the list of ignored scripts.
	 */
	global $wpss_ao_active,$wpss_ao_js,$wpss_ao_js_exc,$wpss_ao_js_frc_hd,$wpss_ao_fix;
	if( is_multisite() || !empty( $wpss_ao_fix ) ) { return; }
	if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize' ); }
	if( !empty( $wpss_ao_active ) ) {
		if( empty( $wpss_ao_js ) ) { $wpss_ao_js = get_option( 'autoptimize_js' ); } /* See if "Optimize JavaScript Code" enabled */
		if( empty( $wpss_ao_js ) ) { $wpss_ao_fix = TRUE; return; }
		if( empty( $wpss_ao_js_frc_hd ) ) { $wpss_ao_js_frc_hd = get_option( 'autoptimize_js_forcehead' ); } /* Setting for Force JavaScript in head */
		if( !empty( $wpss_ao_js_frc_hd ) ) { $wpss_ao_fix = TRUE; return; }
		if( empty( $wpss_ao_js_exc ) ) { $wpss_ao_js_exc = get_option( 'autoptimize_js_exclude' ); } /* Setting for JavaScript exclusion */
		$ao_js_exc		= trim( $wpss_ao_js_exc, ", \t\n\r\0\x0B" );
		$exc_phrs		= array( '1899' => 'wp-spamshield', '192' => '.php,jquery', '1973' => 'wp-spamshield,jquery.js,jquery-migrate.min.js,', ); $exc_phr = $exc_phrs['1973'];
		$ao_js_exc_mod	= trim( implode( ',', array_unique( array_values( array_filter( explode( ',', str_replace( array( $exc_phrs['1899'].'1', $exc_phrs['192'], ), array( '', '', ), $ao_js_exc ) ) ) ) ) ), ", \t\n\r\0\x0B" );
		if( FALSE !== strpos( $ao_js_exc, $exc_phr ) && $wpss_ao_js_exc === $ao_js_exc_mod ) { $wpss_ao_fix = TRUE; return; } else { $ao_js_exc = $ao_js_exc_mod; }
		if( FALSE === strpos( $ao_js_exc, $exc_phr ) ) { $s = empty( $ao_js_exc ) ? '' : ','; $ao_js_exc .= $s.$exc_phr; }
		$ao_js_exc = implode( ',', array_unique( explode( ',', $ao_js_exc ) ) );
		if( $ao_js_exc !== $wpss_ao_js_exc ) {
			update_option( 'autoptimize_js_exclude', $ao_js_exc );
			@WP_SpamShield::append_log_data( NULL, NULL, 'Autoptimize JavaScript exclusion setting appended.' );
		}
	}
	$wpss_ao_fix = TRUE;
}

function rs_wpss_admin_fscf_fix() {
	/**
	 *	Fix compatibility with Fast Secure Contact Form if active
	 *	The 'enable_submit_oneclick' option causes problems with jQuery, and the CAPTCHA option is just unnecessary. This fix automatically corrects these settings in all saved forms.
	 */
	global $wpss_fscf_active,$wpss_fscf_fix;
	$spamshield_options = WP_SpamShield::get_option();
	if( is_multisite() || !empty( $spamshield_options['disable_misc_form_shield'] ) || !empty( $wpss_fscf_fix ) ) { return; }
	if( empty( $wpss_fscf_active ) ) { $wpss_fscf_active = WPSS_Compatibility::is_plugin_active( 'si-contact-form' ); }
	if( !empty( $wpss_fscf_active ) ) {
		$wpss_fscf_cg = get_option( 'fs_contact_global' );
		if( !empty( $wpss_fscf_cg['form_list'] ) ) {
			foreach( $wpss_fscf_cg['form_list'] as $k => $form ) {
				$form_options = get_option( 'fs_contact_form'.$k );
				if( 'true' === $form_options['captcha_enable'] || 'true' === $form_options['enable_submit_oneclick'] ) {
					$form_options['captcha_enable'] = 'false'; $form_options['enable_submit_oneclick'] = 'false';
					update_option( 'fs_contact_form'.$k, $form_options ); $updated = TRUE;
				}
			}
		}
		if( !empty( $updated ) ) {
			@WP_SpamShield::append_log_data( NULL, NULL, 'Fast Secure Contact Form settings updated.' );
		}
	}
	$wpss_fscf_fix = TRUE;
}

/* Admin Functions - END */


/**
 *	WP_SpamShield CLASS - BEGIN
 */

class WP_SpamShield {

	/* Initialize Class Variables */
	static protected	$pref				= 'WPSS_';
	static protected	$debug_server		= '.redsandmarketing.com';
	static protected	$dev_url			= 'https://www.redsandmarketing.com/';
	static public		$_ENV				= array();
	static private		$spamshield_options	= array();		/* Memory cache of WP-SpamShield options			*/
	static private		$default_options	= array();		/* Memory cache of WP-SpamShield default options	*/
	static private		$stored_options		= array();		/* Memory cache of options stored to DB				*/
	static private		$session			= array();		/* Memory cache of current SESSION vars				*/
	static protected	$ip_dns_params		= array( 'server_hostname' => WPSS_SERVER_HOSTNAME, 'server_addr' => WPSS_SERVER_ADDR, 'server_name' => WPSS_SERVER_NAME, 'domain' => WPSS_SITE_DOMAIN );
	static protected	$php_version		= PHP_VERSION;
	static protected	$wp_ver				= WPSS_WP_VERSION;
	static protected	$plugin_name		= WPSS_PLUGIN_NAME;
	static protected	$rgx_tld			= WPSS_RGX_TLD;
	static protected	$web_host			= NULL;
	static protected	$web_host_proxy		= NULL;
	static protected	$ip_addr			= NULL;
	static protected	$rev_dns_cache		= NULL;
	static protected	$fwd_dns_cache		= NULL;

	function __construct() {
		if( TRUE !== WPSS_DEBUG && TRUE !== WP_DEBUG ) { @ini_set( 'display_errors', 0 ); @error_reporting( 0 ); } /* Prevents error display, but will display errors if WP_DEBUG turned on. */
		global $pagenow;
		/* Activation */
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		/* Updation */
		self::register_updation_hook( array( $this, 'updation' ) );
		/* Actions */
		foreach( array( -100 => array( 'WPSS_Compatibility', 'supported' ), -90 => array( 'WPSS_Compatibility', 'unsupported' ), 1 => 'rs_wpss_load_languages', 2 => array( $this, 'create_updation_hook' ), 100 => array( 'WPSS_Compatibility', 'conflict_check' ) ) as $o => $a ) { add_action( 'plugins_loaded', $a, $o ); }
		add_action( 'load_textdomain', 'rs_wpss_check_loaded_languages', 10, 2 );
		foreach( array( WPSS_F0 => array( 'WPSS_Security', 'security_init' ), -999 => array( 'WPSS_Security', 'check_request_method' ), -990 => array( 'WPSS_Security', 'early_post_intercept' ), 1 => 'rs_wpss_first_action', 2 => 'rs_wpss_misc_form_spam_check' ) as $o => $a ) { add_action( 'init', $a, $o ); }
		add_action( 'login_init', 'rs_wpss_login_init', -100 );
		add_action( 'widgets_init', 'rs_wpss_load_widgets' );
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
		foreach(
			array(
				-999	=> array( 'c' => 'WPSS_Security', 'f' => 'check_admin_sec', 'is' => NULL, 'not' => NULL ),
				-100	=> array( 'c' => $this, 'f' => 'hide_cpn_notices', 'is' => NULL, 'not' => NULL ),
				-10		=> array( 'c' => $this, 'f' => 'check_requirements', 'is' => NULL, 'not' => NULL ),
				10		=> array( 'c' => $this, 'f' => 'disable_plugin_edit', 'is' => 'plugin-editor.php', 'not' => NULL ),
			)
			as $o => $a
		) {
			if( ( !empty( $a['is'] ) && $a['is'] !== $pagenow ) || ( !empty( $a['not'] ) && $a['not'] === $pagenow ) ){ continue; }
			add_action( 'admin_init', array( $a['c'], $a['f'] ), $o );
		}
		foreach( array( 'wp_enqueue_scripts', 'login_enqueue_scripts' ) as $a ) { add_action( $a, array( $this, 'enqueue_scripts' ), 100 ); }
		foreach( array( 'wp_head', 'login_head' ) as $a ) { add_action( $a, array( $this, 'insert_head_js' ), 9999 ); }
		add_action( 'comment_form', 'rs_wpss_comment_form_append', 10 );
		foreach( array( 'wp_footer', 'login_footer' ) as $a ) { add_action( $a, array( $this, 'insert_footer_js' ) ); }
		add_action( 'activity_box_end', array( $this,'dashboard_counter') );
		if( WP_SpamShield::is_wp_ver( '4.6' ) ) {
			add_action( 'admin_print_footer_scripts-post-new.php', array( $this, 'editor_add_quicktags' ), 10 );
			add_action( 'admin_print_footer_scripts-post.php', array( $this, 'editor_add_quicktags' ), 10 );
		} else { 
			add_action( 'admin_print_footer_scripts', array( $this, 'editor_add_quicktags' ), 10 );
		}
		add_action( 'register_form', 'rs_wpss_register_form_append', 1 );
		add_action( 'user_register', 'rs_wpss_user_register', 1 );
		add_action( 'pre_user_email', 'rs_wpss_sanitize_new_user_email', 999 );
		add_action( 'wp_logout', 'rs_wpss_logout' );
		add_action( 'wp_login', 'rs_wpss_login', 10, 2 );
		/* Filters */
		add_filter( 'is_email', 'rs_wpss_adv_validate_email', 999, 3 );
		add_filter( 'wp_mail', array( 'WPSS_Security', 'mail_init' ), 999, 1 );
		add_filter( 'lostpassword_post', array( 'WPSS_Security', 'password_reset' ), 999, 1 );
		add_filter( 'auto_update_plugin', array( 'WPSS_Security', 'auto_update' ), 100, 2 );
		if( 'plugins.php' === $pagenow && is_multisite() ) { add_filter( 'all_plugins', array( $this, 'network_admin_remove_plugin' ) ); }
		if( 'plugins.php' === $pagenow ) {
			add_filter( 'plugin_action_links_'.WPSS_PLUGIN_BASENAME, array( $this, 'action_links' ), 10, 4 );
			add_filter( 'plugin_row_meta', array( $this, 'meta_links' ), 10000, 4 );
		}
		if( 'edit-comments.php' === $pagenow ) {
			add_filter( 'comment_row_actions', array( $this, 'comment_edit_links' ), 200, 2 );
		}
		if( rs_wpss_is_xmlrpc() ) { add_filter( 'xmlrpc_methods', array( 'WPSS_Security', 'disable_xmlrpc_multicall' ), 100 ); }
		add_filter( 'status_header', array( $this, 'add_404_hook' ), 500, 4 );
		add_filter( 'wpss_filter_404', 'rs_wpss_mod_status_header', 100, 4 );
		add_filter( 'the_content', 'rs_wpss_contact_form', 10 );
		foreach( array( 'the_content', 'the_excerpt', 'widget_text', 'comment_text', 'comment_excerpt' ) as $f ) { add_filter( $f, 'rs_wpss_encode_emails', 9999 ); }
		add_filter( 'pingback_ping_source_uri', 'rs_wpss_precheck_pingback_spam', -100, 2 );
		add_filter( 'preprocess_comment', 'rs_wpss_check_comment_spam', -100 );
		add_filter( 'comment_notification_recipients', 'rs_wpss_comment_notification_check', 9999, 2 );
		add_filter( 'comment_moderation_recipients', 'rs_wpss_comment_moderation_check', 9999, 2 );
		add_filter( 'registration_errors', 'rs_wpss_check_new_user', 9999, 3 );
		if( isset( $_POST[WPSS_REF2XJS] ) ) { /* Don't add if registrations not processed by WPSS, or missing token */
			if( function_exists( 'rs_wpss_modify_signup_notification_admin' ) ) {
				add_filter( 'wpss_signup_notification_text_admin', 'rs_wpss_modify_signup_notification_admin', 1, 3 );
			}
			if( function_exists( 'rs_wpss_modify_signup_notification_user' ) ) {
				add_filter( 'wpss_signup_notification_text_user', 'rs_wpss_modify_signup_notification_user', 1, 3 );
			}
		}
		/* Actions & Filters - 3rd Party Plugins */
		if( defined( 'WC_VERSION' ) || defined( 'WOOCOMMERCE_VERSION' ) || class_exists( 'WooCommerce' ) ) {
			add_action( 'woocommerce_before_customer_login_form', 'rs_wpss_wc_login_init', -100 );
			add_action( 'woocommerce_before_customer_login_form', 'rs_wpss_wc_before_register', 10 );
			add_action( 'woocommerce_register_form_start', 'rs_wpss_wc_registration_init', -100 );
			add_filter( 'woocommerce_registration_errors', 'rs_wpss_wc_check_new_user', -10 );
		}
		if( class_exists( 'BuddyPress' ) ) { add_filter( 'bp_signup_validate', 'rs_wpss_check_new_user', 999 ); }
		if( defined( 'WPCF7_VERSION' ) ) {
			add_filter( 'wpcf7_spam', 'rs_wpss_cf7_spam_check' );
			add_filter( 'wpcf7_mail_components', 'rs_wpss_cf7_contact_form_addendum' );
		}
		if( class_exists( 'GFForms' ) ) {
			add_filter( 'gform_get_form_filter', 'rs_wpss_gf_form_append', 10, 2 );
			add_filter( 'gform_pre_submission', 'rs_wpss_gf_spam_check' ); /* Modded 1.9.5 */
		}
		if( class_exists( 'WPEngine_PHPCompat' ) ) {
			add_filter( 'phpcompat_whitelist', array( 'WPSS_Compatibility', 'php_compat' ), 10  );
		}
		/* Shortcodes */
		add_shortcode( 'spamshieldcountersm', array('WPSS_Old_Counters','counter_sm_short') );
		add_shortcode( 'spamshieldcounter', array('WPSS_Old_Counters','counter_short') );
		add_shortcode( 'spamshieldcontact', 'rs_wpss_contact_shortcode' );
		/* Deactivation */
		register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

	}


	/* Common Functions - Required for Setup - BEGIN */

	/**
	 *  Convert case using multibyte version (superior) if available, if not, use default functions
	 *  Replaces PHP functions strtolower(), strtoupper(), ucfirst(), ucwords()
	 *  Usage:
	 *  - WP_SpamShield::casetrans( 'lower', $str ); // Ver 1.9.9.8.2+
	 *  Replaces:
	 *  - rs_wpss_casetrans( 'lower', $str ); // Ver 1.8.4 - 1.9.9.8.1
	 *	@dependencies	...
	 *	@used by		many, ...
	 *  @since			1.8.4 as rs_wpss_casetrans()
	 *  @moved			1.9.9.8.2 to WPSS_PHP class
	 *  @moved			1.9.9.9.4 to WP_SpamShield class
	 */
	static public function casetrans( $type, $str ) {
		if( empty( $str ) || empty( $type ) || !is_string( $str ) || !is_string( $type ) ) { return $str; }
		switch( $type ) {
			case 'upper':
				return ( function_exists( 'mb_strtoupper' ) ) ? mb_strtoupper( $str, 'UTF-8' ) : strtoupper( $str );
			case 'lower':
				return ( function_exists( 'mb_strtolower' ) ) ? mb_strtolower( $str, 'UTF-8' ) : strtolower( $str );
			case 'ucfirst':
				if( function_exists( 'mb_strtoupper' ) && function_exists( 'mb_substr' ) ) {
					$strtmp = mb_strtoupper( mb_substr( $str, 0, 1, 'UTF-8' ), 'UTF-8' ) . mb_substr( $str, 1, NULL, 'UTF-8' );
					/* 1.9.5.1 - Added workaround for strange PHP bug in mb_substr() on some servers */
					return ( rs_wpss_strlen( $str ) === rs_wpss_strlen( $strtmp ) ) ? $strtmp : ucfirst( $str );
				} else { return ucfirst( $str ); }
			case 'ucwords':
				return ( function_exists( 'mb_convert_case' ) ) ? mb_convert_case( $str, MB_CASE_TITLE, 'UTF-8' ) : ucwords( $str );
				/**
				 *  Note differences in results between ucwords() and this.
				 *  ucwords() will capitalize first characters without altering other characters, whereas this will lowercase everything, but capitalize the first character of each word.
				 *  This works better for our purposes, but be aware of differences.
				 */
			default:
				return $str;
		}
	}

	/**
	 *	Check if current install is a specific PHP version or later. For compatibility checks, etc.
	 *	@dependencies	none
	 *	@used by		...
	 *	@since			1.9.9.5 as rs_wpss_is_php_ver()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 */
	static public function is_php_ver( $ver ) {
		return version_compare( PHP_VERSION, $ver, '>=' );
	}

	/**
	 *	Check if current install is a specific WordPress version or later. For compatibility checks, etc.
	 *	@dependencies	none
	 *	@used by		...
	 *	@since			1.9.5.7 as rs_wpss_is_wp_ver()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 */
	static public function is_wp_ver( $ver ) {
		return version_compare( WPSS_WP_VERSION, $ver, '>=' );
	}

	/**
	 *	Check if current install is a specific WP-SpamShield version or later.
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::is_beta()
	 *	@since			1.9.9.9.4
	 */
	static public function is_wpss_ver( $ver ) {
		return version_compare( WPSS_VERSION, $ver, '>=' );
	}

	/**
	 *	Make sure beta features only function under certain conditions.
	 *	@dependencies	WP_SpamShield::is_wpss_ver(), WP_SpamShield::is_mdbug(), WP_SpamShield::is_debug()
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 */
	static public function is_beta( $ver, $mdbug = FALSE ) {
		return ( self::is_wpss_ver( $ver ) || ( ( TRUE === $mdbug && self::is_mdbug() ) || self::is_debug() ) );
	}

	/**
	 *	Check if MDBUG_SERVER
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 */
	static public function is_mdbug() {
		return ( 0 === strpos( WPSS_SERVER_NAME_REV, WPSS_MDBUG_SERVER_NAME_REV ) );
	}

	/**
	 *	Check if DEBUG_SERVER
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 */
	static public function is_debug() {
		return ( 0 === strpos( WPSS_SERVER_NAME_REV, WPSS_DEBUG_SERVER_NAME_REV ) );
	}

	/**
	 *	Replace a filter/action
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 */
	static public function replace_filter( $hook, $old_callback, $new_callback, $old_priority = 10, $new_priority = 10, $args = 1  ) {
		self::remove_filter( $hook, $old_callback, $old_priority );
		add_action( $hook, $new_callback, $new_priority, $args );
	}

	/**
	 *	Remove a filter/action, with built-in error correction
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 */
	static public function remove_filter( $hook, $callback, $priority = NULL ) {
		if( NULL === $priority || !is_int( $priority ) ) {
			$priority = has_action( $hook, $callback ); /* boolean|integer */
			if( FALSE === $priority || !is_int( $priority ) ) { return FALSE; }
		}
		if( remove_filter( $hook, $callback, $priority ) ) {
			return TRUE;
		} else {
			$priority = has_action( $hook, $callback ); /* boolean|integer */
			if( FALSE === $priority || !is_int( $priority ) ) { return FALSE; }
		}
		return ( remove_filter( $hook, $callback, $priority ) );
	}

	/**
	 *	Get Log File Name
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 */
	static public function get_log_filename() {
		return ( self::is_mdbug() ) ? 'temp-comments-log.txt' : 'temp-comments-log-' . rs_wpss_get_log_key() . '.txt';
	}

	/**
	 *	Detect https/http
	 *	Use instead of WP function is_ssl(), as this is more accurate
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::get_url(), rs_wpss_get_rewrite_base(), rs_wpss_gf_spam_check(), WPSS_Compatibility::misc_form_bypass()
	 *	@since			... as rs_wpss_is_ssl()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 */
	static public function is_https() {
		if( !empty( $_SERVER['HTTPS'] )						&& 'off'	!==	$_SERVER['HTTPS'] )						{ return TRUE; }
		if( !empty( $_SERVER['SERVER_PORT'] )				&& '443'	 ==	$_SERVER['SERVER_PORT'] )				{ return TRUE; }
		if( !empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] )	&& 'https'	===	$_SERVER['HTTP_X_FORWARDED_PROTO'] )	{ return TRUE; }
		if( !empty( $_SERVER['HTTP_X_FORWARDED_SSL'] )		&& 'off'	!==	$_SERVER['HTTP_X_FORWARDED_SSL'] )		{ return TRUE; }
		return FALSE;
	}

	/**
	 *	Get the URL of current page/post/etc
	 *	@dependencies	WP_SpamShield::is_https()
	 *	@used by		constant 'WPSS_THIS_URL'
	 *	@since			1.9.9.8.2 as rs_wpss_get_url()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 */
	static public function get_url( $safe = FALSE, $server_name = '' ) {
		if( empty( $server_name ) ) {
			$server_name = ( defined( 'WPSS_SERVER_NAME' ) ) ? WPSS_SERVER_NAME : @rs_wpss_get_server_name();
		}
		$url  = ( self::is_https() ) ? 'https://' : 'http://';
		$url .= $server_name.$_SERVER['REQUEST_URI'];
		return ( TRUE === $safe ) ? esc_url( $url ) : $url;
	}

	/**
	 *	Get IP address of current request
	 *	@dependencies	WP_SpamShield::sanitize_ip(), WP_SpamShield::is_valid_ip(), WP_SpamShield::is_google_ip(), WP_SpamShield::is_opera_ip(), WP_SpamShield::get_web_proxy()
	 *	@used by		...
	 *	@since			1.9.9.8.2 as rs_wpss_get_ip_addr()
	 *	@moved			1.9.9.8.2 to WPSS_Utils class
	 *	@moved			1.9.9.8.7 to WP_SpamShield class
	 */
	static public function get_ip_addr() {
		if( !empty( self::$ip_addr ) && (bool) @WP_SpamShield::is_valid_ip( self::$ip_addr ) ) { return self::$ip_addr; }
		self::$ip_addr = $ip_addr_default = !empty( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : self::$_ENV['REMOTE_ADDR'];
		self::$ip_addr = $ip_addr_default = WP_SpamShield::sanitize_ip( self::$ip_addr );
		if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$xff_addr = !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ? WP_SpamShield::sanitize_ip( $_SERVER['HTTP_X_FORWARDED_FOR'] ) : FALSE;
			$rem_addr = $ip_addr_default;
			/* Check for Google Chrome Data Compression Proxy (Chrome Data-Saver) and get Real IP */
			if( !empty( $_SERVER['HTTP_VIA'] ) && !empty( $rem_addr ) && !empty( $xff_addr ) && $rem_addr !== $xff_addr && '1.1 Chrome-Compression-Proxy' === $_SERVER['HTTP_VIA'] && WP_SpamShield::is_valid_ip( $xff_addr ) && WP_SpamShield::is_google_ip( $rem_addr ) ) { self::$ip_addr = $xff_addr; return $xff_addr; }
			/* Check for Opera Data Saver Proxy and get Real IP */
			if( !empty( $rem_addr ) && !empty( $xff_addr ) && $rem_addr !== $xff_addr && (bool) @WP_SpamShield::is_valid_ip( $xff_addr ) && (bool) @WP_SpamShield::is_opera_ip( $rem_addr ) ) { self::$ip_addr = $xff_addr; return $xff_addr; }
		}
		/* Check for web host proxies */
		$web_host_proxy = @WP_SpamShield::get_web_proxy( self::$ip_dns_params );
		if( !empty( $web_host_proxy ) && ( !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) || !empty( $_SERVER['HTTP_INCAP_CLIENT_IP'] ) || !empty( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] ) ) ) {
			if( 'Cloudflare' === $web_host_proxy && !empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
				self::$ip_addr = WP_SpamShield::sanitize_ip( $_SERVER['HTTP_CF_CONNECTING_IP'] );
			} elseif( 'Incapsula' === $web_host_proxy && !empty( $_SERVER['HTTP_INCAP_CLIENT_IP'] ) ) {
				self::$ip_addr = WP_SpamShield::sanitize_ip( $_SERVER['HTTP_INCAP_CLIENT_IP'] );
			} elseif( 'Sucuri CloudProxy' === $web_host_proxy && !empty( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] ) ) {
				self::$ip_addr = WP_SpamShield::sanitize_ip( $_SERVER['HTTP_X_SUCURI_CLIENTIP'] );
			}
		} elseif( class_exists( 'wfUtils' ) && (bool) @WPSS_Compatibility::is_plugin_active( 'wordfence' ) ) {
			self::$ip_addr = @wfUtils::getIP();
			self::$ip_addr = WP_SpamShield::sanitize_ip( self::$ip_addr );
		}
		self::$ip_addr = WP_SpamShield::sanitize_ip( self::$ip_addr );
		self::$ip_addr = ( WP_SpamShield::is_valid_ip( self::$ip_addr ) ) ? self::$ip_addr : $ip_addr_default;
		return !empty( self::$ip_addr ) ? self::$ip_addr : FALSE;
	}

	/**
	 *	Check if IP is a known Google IP
	 *	Currently IPv4 only
	 *	@dependencies	...
	 *	@used by		WP_SpamShield::get_ip_addr()
	 *	@since			1.7.8 as rs_wpss_is_google_ip()
	 *	@moved			1.9.9.8.2 to WPSS_Utils class
	 *	@moved			1.9.9.8.7 to WP_SpamShield class
	 */
	static public function is_google_ip( $ip ) {
		if( self::preg_match( "~^(64\.233\.1([6-8][0-9]|9[0-1])|66\.102\.([0-9]|1[0-5])|66\.249\.(6[4-9]|[7-8][0-9]|9[0-5])|72\.14\.(19[2-9]|2[0-4][0-9]|25[0-5])|74\.125\.([0-9]|[0-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])|209\.85\.(1(2[8-9]|[3-9][0-9])|2[0-4][0-9]|25[0-5])|216\.239\.(3[2-9]|[4-5][0-9]|6[0-3]))\.~", $ip ) ) { return TRUE; }
		return FALSE;
	}

	/**
	 *	Check if IP is an known Opera IP
	 *	Currently IPv4 only
	 *	@dependencies	...
	 *	@used by		WP_SpamShield::get_ip_addr()
	 *	@since			1.9.8.3 as rs_wpss_is_opera_ip()
	 *	@moved			1.9.9.8.2 to WPSS_Utils class
	 *	@moved			1.9.9.8.7 to WP_SpamShield class
	 */
	static public function is_opera_ip( $ip ) {
		if( self::preg_match( "~^(37\.228\.1(0[4-9]|1[01])|82\.145\.2(0[89]|1[0-9]|2[0-3])|91\.203\.9[6-9]|107\.167\.(9[6-9]|1([01][0-9]|2[0-6]))|141\.0\.([89]|1[0-5])|185\.26\.18[0-3]|195\.189\.14[23])\.~", $ip ) ) { return TRUE; }
		return FALSE;
	}

	/**
	 *  Detect and set correct End of Line character
	 *  For cross-platform compatibility, especially with certain server setups where PHP_EOL constant is not always set.
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::setup(), and others
	 *	@notes			Function must be defined in main class, not includes or child classes
	 *	@since			1.0.0
	 */
	static public function eol() {
		return ( !empty( $GLOBALS['is_IIS'] ) ) ? "\r\n" : "\n";
	}

	/**
	 *  Detect and set correct Directory Separator character
	 *  For cross-platform compatibility, especially with certain server setups where DIRECTORY_SEPARATOR constant is not always set.
	 *  Even if OS will interpret either correctly when receiving input from script,
	 *  	it is still best practice for correct processing of strings returned by OS.
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::setup(), ...
	 *	@notes			Function must be defined in main class, not includes or child classes
	 *	@since			1.0.0
	 */
	static public function ds() {
		return ( !empty( $GLOBALS['is_IIS'] ) ) ? '\\' : '/';
	}

	/**
	 *  Detect and set correct Path Separator character for cross-platform compatibility.
	 *  PATH_SEPARATOR is used in configuration settings (php.ini, etc.) and OS environment variables to separate paths
	 *  	Ex: 'open_basedir' and 'include_path' settings in php.ini use ":" to separate paths on ~nix systems.
	 *  	Ex: 'set_include_path' function will only be cross-platform compatible if PATH_SEPARATOR is used instead of ":" or ";".
	 *  For cross-platform compatibility, especially with certain server setups where DIRECTORY_SEPARATOR constant is not always set.
	 *  Even if OS will interpret either correctly when receiving input from script,
	 *  	it is still best practice for correct processing of strings returned by OS.
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::setup(), ...
	 *	@notes			Function must be defined in main class, not includes or child classes
	 *	@since			1.0.0
	 */
	static public function ps() {
		return ( !empty( $GLOBALS['is_IIS'] ) ) ? ';' : ':';
	}

	/**
	 *  Detect Request Method and enforce capitalization to prevent edge-case issues
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::setup(), ...
	 *	@notes			Function must be defined in main class, not includes or child classes. Use strtoupper() not WP_SpamShield::casetrans().
	 *	@since			1.9.15
	 */
	static public function request_method() {
		return ( !empty( $_SERVER['REQUEST_METHOD'] ) ) ? trim( strtoupper( $_SERVER['REQUEST_METHOD'] ) ) : '';
	}

	/**
	 *	Conditional alias for WPSS_Utils::get_web_host() function
	 *	If WPSS_Utils class is loaded, will call WPSS_Utils::get_web_host()
	 *	@dependencies	...
	 *	@since			1.9.9.8.7
	 */
	static public function get_web_host( $params = array() ) {
		if( NULL !== self::$web_host ) { return self::$web_host; }
		self::$web_host = NULL;
		if( defined( 'WPSS_INCL_DONE' ) && method_exists( 'WPSS_Utils', 'get_web_host' ) ) {
			self::$web_host = @WPSS_Utils::get_web_host( $params );
		}
		return self::$web_host;
	}

	/**
	 *	Conditional alias for WPSS_Utils::get_web_proxy() function
	 *	If WPSS_Utils class is loaded, will call WPSS_Utils::get_web_proxy()
	 *	@dependencies	...
	 *	@since			1.9.9.8.7
	 */
	static public function get_web_proxy( $params = array() ) {
		if( NULL !== self::$web_host_proxy ) { return self::$web_host_proxy; }
		self::$web_host_proxy = NULL;
		if( defined( 'WPSS_INCL_DONE' ) && method_exists( 'WPSS_Utils', 'get_web_proxy' ) ) {
			self::$web_host_proxy = @WPSS_Utils::get_web_proxy( $params );
		}
		return self::$web_host_proxy;
	}

	/**
	 *	Conditional alias for WPSS_Utils::sort_unique() function
	 *	If WPSS_Utils class is loaded, will call WPSS_Utils::sort_unique()
	 *	@dependencies	...
	 *	@since			... as rs_wpss_sort_unique()
	 *	@moved			1.9.9.9.4 to WP_SpamShield class
	 */
	static public function sort_unique( $arr = array() ) {
		if( defined( 'WPSS_INCL_DONE' ) && method_exists( 'WPSS_Utils', 'sort_unique' ) ) {
			return @WPSS_Utils::sort_unique( $arr );
		}
		if( empty( $arr ) || !is_array( $arr ) ) { return array(); }
		$arr_tmp = array_unique( $arr ); sort( $arr_tmp, SORT_REGULAR ); $new_arr = array_values( $arr_tmp );
		return $new_arr;
	}

	/**
	 *	Use this function instead of parse_url() for compatibility with PHP < 5.4.7. wp_parse_url() was added in WP ver 4.4
	 *	@dependencies	WP_SpamShield::is_wp_ver()
	 *	@used by		rs_wpss_get_domain(), rs_wpss_get_query_string()
	 *	@since			1.9.8.4 as rs_wpss_parse_url()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 */
	static public function parse_url( $url ) {
		return ( function_exists( 'wp_parse_url' ) && self::is_wp_ver( '4.4' ) ) ? wp_parse_url( $url ) : @parse_url( $url );
	}

	/**
	 *	Sanitize IP address input from $_SERVER[] vars, Forward DNS Lookups, etc.
	 *	Can extract IP address from a list of IP's, from forwarded data, remove port #, etc.
	 *	It is possible for $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_X_FORWARDED_FOR'], Forward DNS Lookups, etc., to return a list instead of a single IP
	 *	Run IP data through this function to sanitize before using in code
	 *	@dependencies	rs_wpss_substr_count(), WP_SpamShield::is_valid_ip()
	 *	@used by		...
	 *	@since			1.9.9.3 as rs_wpss_sanitize_ip()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 *	@return			string
	 */
	static public function sanitize_ip( $ip_in ) {
		if( empty( $ip_in ) && NULL === $ip_in ) { return $ip_in; }
		if( FALSE === strpos( $ip_in, '.' ) && FALSE === strpos( $ip_in, ':' ) ) { return self::sanitize_string( $ip_in ); }
		$ip_in	= trim( (string) $ip_in );
		if( '127.0.0.1' === $ip_in || '0:0:0:0:0:0:0:1' === $ip_in || '::1' === $ip_in || (bool) @self::is_valid_ip( $ip_in, TRUE ) ) { return $ip_in; }
		$fwd	= array( 'for', '=', '"', );
		$tmp	= str_replace( $fwd, '', $ip_in );
		$tmp	= strtok( $tmp, ', ;' ); strtok( '', '' );
		if( @rs_wpss_substr_count( $tmp, '.' ) > 2 ) { /* Only on IPv4, not IPv6 */
			$tmp = strtok( $tmp, ':' ); strtok( '', '' );
		}
		$ip_out	= trim( $tmp );
		$valid	= (bool) @self::is_valid_ip( $ip_out, TRUE );
		if( FALSE === $valid && TRUE === WP_DEBUG && TRUE === WPSS_DEBUG ) {
			@self::append_log_data( NULL, NULL, 'Error sanitizing IP address in method '.__CLASS__.'::'.__FUNCTION__.' | Original IP: '.$ip_in.' | Sanitized IP: '.$ip_out );
		}
		return ( TRUE === $valid ) ? $ip_out : self::sanitize_string( $ip_in );
	}

	/**
	 *	Check if string is a valid IP Address
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::sanitize_ip(), ...
	 *	@since			1.9.9.8.2
	 */
	static public function is_valid_ip( $ip, $incl_priv_res = FALSE, $ipv4_c_block = FALSE ) {
		if( empty( $ip ) || ( FALSE === strpos( $ip, '.' ) && FALSE === strpos( $ip, ':' ) ) ) { return FALSE; }
		if( !empty( $ipv4_c_block ) ) {
			if( self::preg_match( "~^".WPSS_RGX_IPCVAL."$~", $ip ) ) { return TRUE; } /* Valid C-Block check - checking for C-block: '123.456.78.' format */
		}
		if( function_exists( 'filter_var' ) ) {
			if( empty( $incl_priv_res ) ) { if( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) { return TRUE; } }
			elseif( filter_var( $ip, FILTER_VALIDATE_IP ) ) { return TRUE; }
			/* FILTER_FLAG_IPV4,FILTER_FLAG_IPV6,FILTER_FLAG_NO_PRIV_RANGE,FILTER_FLAG_NO_RES_RANGE */
		} elseif( self::preg_match( "~^".WPSS_RGX_IPVAL."$~", $ip ) && !self::preg_match( "~^192\.168\.~", $ip ) ) { return TRUE; }
		return FALSE;
	}

	/**
	 *	Get the amount of memory currently used by WordPress
	 *	@dependencies	WP_SpamShield::format_bytes()
	 *	@used by		...
	 *	@since			... as rs_wpss_wp_memory_used()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 */
	static public function wp_memory_used( $peak = FALSE, $raw = FALSE ) {
		if( TRUE === $peak && function_exists( 'memory_get_peak_usage' ) ) {
			$mem = memory_get_peak_usage( TRUE );
		} elseif( function_exists( 'memory_get_usage' ) ) {
			$mem = memory_get_usage();
		}
		return ( !empty( $mem ) && FALSE === $raw ) ? self::format_bytes( $mem ) : 0;
	}

	/**
	 *	Format number of bytes into KB, MB, GB, TB
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::wp_memory_used(), and others
	 *	@since			... as rs_wpss_format_bytes()
	 *	@moved			1.9.9.8.2 to WP_SpamShield class
	 *	@return			string
	 */
	static public function format_bytes( $size, $precision = 2 ) {
		if( !is_numeric( $size ) || empty( $size ) ) { return $size; }
		$base		= log( $size ) / log( 1024 );
		$base_floor = floor( $base );
		$suffixes	= array( '', 'k', 'M', 'G', 'T' );
		$suffix		= isset( $suffixes[$base_floor] ) ? $suffixes[$base_floor] : '';
		if( empty( $suffix ) ) { return $size; }
		$formatted	= round( pow( 1024, $base - $base_floor ), $precision ) . $suffix;
		return $formatted;
	}

	/**
	 *	Remove null bytes from a string
	 *	Similar to wp_kses_no_null() function.
	 *	@dependencies	none
	 *	@used by		WP_SpamShield::sanitize_string(), WP_SpamShield::sanitize_opt_string(), WP_SpamShield::append_log_data(), rs_wpss_log_data(), ...
	 *	@since			... as rs_wpss_filter_null()
	 *	@moved			1.9.15 to WP_SpamShield class
	 *	@modified		1.9.9.9.1	Added wp_kses_no_null() filters
	 */
	static public function filter_null( $str ) {
		$str = preg_replace( '~[\x00-\x08\x0B\x0C\x0E-\x1F]~', '', $str );
		$str = preg_replace( '~\\\\+0+~', '', $str );
		return str_replace( chr(0), '', $str );
	}

	/**
	 *	Sanitize a string. Much faster than sanitize_text_field()
	 *	@dependencies	WP_SpamShield::filter_null(), ...
	 *	@used by		...
	 *	@since			... as rs_wpss_sanitize_string()
	 *	@moved			1.9.15 to WP_SpamShield class
	 */
	static public function sanitize_string( $str ) {
		return self::filter_null( trim( addslashes( htmlentities( stripslashes( strip_tags( $str ) ) ) ) ) );
	}

	/**
	 *	Sanitize input from a form textarea field
	 *	Like 'sanitize_text_field()' but this preserves the line breaks
	 *	Use for contact forms or similar processes
	 *	WordPress core finally added similar function 'sanitize_textarea_field()' in 4.7.0, but this is still better.
	 *	@dependencies	...
	 *	@used_by		...
	 *	@since			1.9.7.8 as rs_wpss_sanitize_textarea()
	 *	@moved			1.9.15 to WP_SpamShield class
	 *	@modified		1.9.9.9.2	Added sanitize_textarea_field() if WP version is 4.7.0+. Backward compatible.
	 */
	static public function sanitize_textarea( $str ) {
		$str = str_replace( array( "\r\n", "\r", "\f", "\v", "\n\n\n", "\n\n" ), "\n", $str );
		$str = str_replace( '<'."\n", '&lt;'."\n", $str );
		$str = ( WP_SpamShield::is_wp_ver( '4.7' ) && function_exists( 'sanitize_textarea_field' ) ) ? sanitize_textarea_field( $str ) : implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $str ) ) );
		return $str;
	}

	/**
	 *	Sanitize an options string. ie. Contact Form Drop Down Menu...
	 *	@dependencies	WP_SpamShield::filter_null(), ...
	 *	@since			... as rs_wpss_sanitize_opt_string()
	 *	@moved			1.9.15 to WP_SpamShield class
	 */
	static public function sanitize_opt_string( $str ) {
		$str = self::filter_null( trim( addslashes( htmlentities( stripslashes( strip_tags( $str ) ), ENT_QUOTES, get_bloginfo( 'charset' ), FALSE ) ) ) );
		if( FALSE !== strpos( $str, "\'" ) || FALSE !== strpos( $str, '\"' ) ) {
			$str = preg_replace( "~\\+'~", '&rsquo;', $str );
			$str = preg_replace( "~\\+\"~", '', $str );
		}
		return $str;
	}

	/**
	 *	Drop in replacement for PHP function preg_match(), with built-in error correction
	 *	Disables error suppression when WP_DEBUG is enabled
	 *	Can use for both general purpose and for debugging
	 *	@dependencies	WP_SpamShield::append_log_data()
	 *	@used by		..., RSSD
	 *	@since			WPSS 1.9.9.8.7
	 */
	static public function preg_match( $pattern, $subject, &$matches = NULL, $flags = 0, $offset = 0 ) {
		$pattern_rev = ltrim( strrev( $pattern ), "eimsxuADJSUX" ); /* trim off PCRE Regex modifier flags */
		if( !is_string( $pattern ) || ( !is_string( $subject ) && !is_numeric( $subject ) ) || FALSE === strpos( $pattern, '~' ) || 0 !== strpos( $pattern, '~' )  || 0 !== strpos( $pattern_rev, '~' ) ) {
			@self::append_log_data( NULL, NULL, 'Error in regex pattern: '.$pattern );
			return FALSE;
		}
		return ( TRUE === WP_DEBUG ) ? preg_match( $pattern, $subject, $matches, $flags, $offset ) : @preg_match( $pattern, $subject, $matches, $flags, $offset );
	}

	/**
	 *	Replacement for PHP function define(), with built-in conditional check.
	 *	Define one or more named constants if not already set.
	 *	Input an associative array of $name/$value pair(s) to be defined as one or more constants.
	 *	If $pref is supplied, the constant(s) will be prefixed.
	 *	@dependencies	none
	 *	@param			array	$const	Array of name(string)/value(bool|string) pairs to define
	 *	@param			string	$pref	Prefix
	 *	@param			bool	$cond	Conditional? Default = TRUE
	 *	@used by		WP_SpamShield->__construct, WPSS_Security::early_post_intercept(), 
	 *	@since			WPSS 1.9.9.9.4
	 */
	static protected function define( $const = array(), $pref = NULL ) {
		if( empty( $const ) || !is_array( $const ) ) { return; }
		$pref = ( TRUE === $pref ) ? self::$pref : $pref;
		foreach( $const as $name => $value ) {
			$name = trim( $pref.$name );
			if( !defined( $name ) ) {
				define( $name, $value );
			}
		}
	}

	/**
	 *	Drop in replacement for PHP function constant(), with built-in error check.
	 *	The constant will be prefixed with class $pref variable.
	 *	@dependencies	none
	 *	@param			string	$name	The constant name.
	 *	@used by		WP_SpamShield::setup(), WP_SpamShield->__construct
	 *	@since			1.9.9.9.4
	 */
	static protected function constant( $name ) {
		$pref = self::$pref;
		return ( empty( $name ) || !is_string( $name ) || !defined( $pref.$name ) ) ? '' : constant( $pref.$name );
	}

	/**
	 *	Load hooks
	 *	@dependencies	none
	 *	@param			array	$hooks	Array of hook data to load
	 *	@used by		WP_SpamShield::setup()
	 *	@since			1.9.9.9.4
	 */
	static protected function load_hooks( $hooks ) {
		if( empty( $hooks ) || !is_array( $hooks ) ) { return; }
		foreach( $hooks as $i => $v ) {
			extract( $v );
			if( 'action' !== $t && 'filter' !== $t ) { continue; }
			add_filter( $h, $c, $p, $n );
		}
	}

	/**
	 *	Get boolean integer 0|1 from boolean (for saving options, etc.)
	 *	@dependencies	none
	 *	@param			bool	$b	Boolean to convert to boolean integer
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 *	@return			int|null
	 */
	static public function bool_to_int( $b ) {
		return ( is_bool( $b ) && TRUE === $b ) ? 1 : 0;
	}

	/**
	 *	Get boolean from boolean integer (for saving options, etc.)
	 *	@dependencies	none
	 *	@param			int		$i	Boolean integer to convert to boolean
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 *	@return			bool|null
	 */
	static public function int_to_bool( $i ) {
		if( empty( $i ) || !is_int( $i ) || ( 1 !== $i && 0 !== $i ) ) { return NULL; }
		return ( 1 === $i );
	}

	/**
	 *	Get boolean from boolean string (for testing options, etc.)
	 *	@dependencies	WP_SpamShield::casetrans(), ...
	 *	@param			string	$str	Boolean string to convert to boolean
	 *	@param			string	$key	(Optional) Specific key to check for
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 *	@return			bool|null
	 */
	static public function str_to_bool( $str, $key = NULL ) {
		$s		= WP_SpamShield::casetrans( 'lower', $str );
		$k		= WP_SpamShield::casetrans( 'lower', $key );
		$bools	= 
			array(
				'1'		=> '0',
				'true'	=> 'false',
				'on'	=> 'off',
				'yes'	=> 'no',
				'y'		=> 'n',
			);
		$boolsr	= array_flip( $bools );
		if( NULL !== $k && is_string( $k ) ) {
			return ( isset( $bools[$k] ) || isset( $boolsr[$k] ) );
		}
		if( !is_string( $s ) || ( !isset( $bools[$s] ) && !isset( $boolsr[$s] ) ) ) { return NULL; }
		return ( isset( $bools[$s] ) || isset( $boolsr[$s] ) );
	}

	/**
	 *	Check if int is boolean int (for testing options, etc.)
	 *	@dependencies	none
	 *	@param			int		$i	Int to check
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 *	@return			bool
	 */
	static public function is_int_bool( $i ) {
		$itb = self::int_to_bool( $i );
		return ( is_bool( $itb ) );
	}

	/**
	 *	Check if string is boolean string (for testing options, etc.)
	 *	@dependencies	none
	 *	@param			string	$str	String to check
	 *	@used by		...
	 *	@since			1.9.9.9.4
	 *	@return			bool
	 */
	static public function is_str_bool( $str, $key = NULL ) {
		$stb = self::str_to_bool( $str, $key );
		return ( is_bool( $stb ) );
	}

	/**
	 *	Adds data to the error log for debugging.
	 *	Only runs when debugging, with `WP_DEBUG` & `WPSS_DEBUG`.
	 *	Format:
	 * 		WP_SpamShield::append_log_data( $var_name, $var_val, [$str = FALSE, $line = NULL, $func = NULL, $meth = NULL, $class = NULL, $file = NULL] );
	 * 		WP_SpamShield::append_log_data( $var_name, $var_val, [$str = FALSE, $line = __LINE__, $func = __FUNCTION__, $meth = __METHOD__, $class = __CLASS__, $file = __FILE__] );
	 *	Example:
	 * 		@WP_SpamShield::append_log_data( '$var_name', $var_val, $string_in_lieu_of_env_data );
	 * 		@WP_SpamShield::append_log_data( '$var_name', $var_val, FALSE, __LINE__, __FUNCTION__, __METHOD__, __CLASS__, __FILE__ );
	 *	@dependencies	WP_SpamShield::get_ip_addr(), WP_SpamShield::get_url(), WP_SpamShield::wp_memory_used()
	 *	@used by		...
	 *	@since			... as rs_wpss_append_log_data()
	 *	@moved			1.9.9.8.7 to WP_SpamShield class
	 */
	static public function append_log_data( $var_name = NULL, $var_val = '', $str = NULL, $line = NULL, $func = NULL, $meth = NULL, $class = NULL, $file = NULL ) {
		if( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG ) {
			$log_arr	= array( 'Line' => $line, 'Function' => $func, 'Method' => $meth, 'Class' => $class, 'File' => $file, 'MEM USED' => @self::wp_memory_used(), 'VER' => WPSS_VERSION, );
			$log_str	= 'WP-SpamShield DEBUG: ['. (string) @self::get_ip_addr() .']['. (string) @self::get_url() .'] ';
			if( !empty( $var_name ) ) {
				if( is_bool( $var_val ) ) {
					$fl = '[B]'; $var_v = ( !empty( $var_val ) ) ? 'TRUE' : 'FALSE';
				} elseif( is_string( $var_val ) || is_numeric( $var_val ) || is_null( $var_val ) ) {
					$fl = '[S]'; $var_v = (string) $var_val;
				} elseif( is_array( $var_val ) ) {
					$fl = '[A]'; $var_v = print_r( $var_val, TRUE );
				} elseif( is_object( $var_val ) ) {
					$fl = '[O]'; $var_v = print_r( $var_val, TRUE );
				} else {
					$fl = '[X]'; $var_v = print_r( $var_val, TRUE );
				}
				$log_str .= $fl.$var_name.': '.$var_v;
			} else {
				$log_str .= (string) $str;
			}
			foreach ( $log_arr as $k => $v ) {
				$log_str .= ( !empty( $v ) ) ? ' | '.$k.': '.$v : '';
			}
			$log_str  = trim( (string) @self::filter_null( $log_str ) ); /* Filter out null bytes to prevent issues */
			@error_log( $log_str, 0 ); /* Logs to debug.log */
		}
	}

	/**
	 *	Builds log data for the blocked spam log for debugging.
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			1.9.17
	 */
	static public function build_log_data( $arr, $data = '' ) {
		if( empty( $arr ) || !is_array( $arr ) ) { return $data; }
		$thick_line	= str_repeat( '═', 100 );
		$thin_line	= str_repeat( '─', 100 );
		foreach( $arr as $k => $v ) {
			$add_line	= ( FALSE !== strpos( $v, '%%{END_GROUP}%%' ) ) ? WPSS_EOL.$thin_line : '';
			$debug_only	= ( FALSE !== strpos( $v, '%%{DEBUG_ONLY}%%' ) );
			$skip_empty	= ( FALSE !== strpos( $v, '%%{NO_OUTPUT_EMPTY}%%' ) );
			$multiline	= ( FALSE !== strpos( $v, '%%{MULTILINE}%%' ) );
			$v			= str_replace( array( '%%{END_GROUP}%%', '%%{DEBUG_ONLY}%%', '%%{NO_OUTPUT_EMPTY}%%', '%%{MULTILINE}%%', ), '', $v );
			if( TRUE === $debug_only && !WP_SpamShield::is_debug() && ( TRUE !== WP_DEBUG || TRUE !== WPSS_DEBUG ) ) { continue; }
			if( TRUE === $skip_empty && empty( $v ) ) { continue; }
			if( TRUE === $multiline ) {
				$data .= $k.': '.WPSS_EOL.$v.$add_line.WPSS_EOL; continue;
			}
			$data .= str_pad( $k.':', 24 ) ."['".$v."']".$add_line.WPSS_EOL;
		}
		return $data;
	}

	/**
	 *	Check HTTP Status - Returns 3-digit response code
	 *	@dependencies	WP_SpamShield::get_headers(), 
	 *	@since			1.9.9.8.8
	 */
	static public function get_http_status( $url = NULL ) {
		return self::get_headers( $url, 'status' );
	}

	/**
	 *	Drop-in replacement for native PHP function get_headers(), with a few tweaks
	 *	Get HTTP Headers of a URL
	 *	Can return an array of headers, an associative array of headers, status code, or all
	 *	@usage			"WP_SpamShield::get_headers( $url )" mimics behavior of native PHP "get_headers( $url )"
	 *	@param			$url, 	$type	default|assoc|status|all
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			... as rs_wpss_get_http_status()
	 *	@modified		1.9.9.8.8	Switched from PHP get_headers() to WP HTTP API: wp_remote_head() for compatibility
	 */
	static public function get_headers( $url = NULL, $type = 'default' ) {
		$response	= wp_remote_head( $url );
		if( is_wp_error( $response ) || empty( $response['headers'] ) ) {
			return ( 'status' === $type ) ? 200 : array();
		}
		$headers	= ( self::is_wp_ver( '4.6' )					)	? $response['headers']->getAll()	: $response['headers']	;
		$code		= ( !empty( $response['response']['code'] )		)	? $response['response']['code']		: ''					;
		foreach( $headers as $k => $v ) {
			if( is_array( $v ) ) { unset( $headers[$k] ); }
		}
		if( 'assoc' === $type ) {
			/* Return associative array */
			return $headers;
		} else {
			/* Return numeric array (standard headers array) */
			$std_headers = array();
			foreach( $headers as $k => $v ) {
				$std_headers[] = $k.': '.$v;
			}
			$headers = $std_headers;
			if( 'status' === $type || 'all' === $type ) {
				$status		= ( !empty( $code ) && ( is_numeric( $code ) || is_string( $code ) ) && self::preg_match( "~^[1-5][0-9]{2}$~", $code ) ) ? $code : 200;
				if( 'status' === $type ) { return $status; }
				$hdr_data	= compact( 'headers', 'status' );
				return $hdr_data;
			}
			return $headers;
		}
	}

	/* Common Functions - Required for Setup - END */


	/* Common Functions - Not Required for Setup - BEGIN */

	/**
	 *	Check if email address belongs to an admin or superadmin
	 *	@dependencies	...
	 *	@since			1.9.9.9
	 */
	static public function is_email_admin( $email ) {
		if( empty( $email ) || !is_email( $email ) ) { return FALSE; }
		if( is_multisite() ) {
			$blog_id			= get_current_blog_id();
			$admin_email 		= get_blog_option( $blog_id, 'admin_email' );
			$superadmin_email	= get_site_option( 'admin_email' );
		} else {
			$admin_email 		= get_option( 'admin_email' );
			$superadmin_email	= NULL;
		}
		if( $email === $admin_email || $email === $superadmin_email ) { return TRUE; }
		$user					= get_user_by( 'email', $email );
		return ( empty( $user ) || !is_object( $user ) || !isset( $user->ID ) || !user_can( $user->ID, 'manage_options' ) ) ? FALSE : TRUE;
	}

	/* Common Functions - Not Required for Setup - END */


	/* Class Admin Functions - BEGIN */

	function activation() {
		global $wpss_init_spam_count;
		$spamshield_options = WP_SpamShield::get_option();
		self::deprecated_options_check( TRUE );
		$installed_ver = $spamshield_options['wpss_version'];
		$admin_email = get_option( 'admin_email' );
		$this->upgrade_check( $installed_ver, TRUE );
		$this->update_admin_status();
		$this->approve_previous_users();

		/* Manually set spam count */
		if( !empty( $wpss_init_spam_count ) ) { update_option( 'spamshield_count', (int) $wpss_init_spam_count ); }

		/* Only run installation if not installed already */
		if( empty( $installed_ver ) || empty( $spamshield_options ) ) {
			/**
			 *	Upgrade from old version
			 *	Import existing WP-SpamFree Options, only on first activation, if old plugin is active
			 */
			$old_version = 'wp-spamfree/wp-spamfree.php';
			$old_version_active	= WPSS_Compatibility::is_plugin_active( $old_version, FALSE );
			$wpsf_installed_ver	= get_option( 'wp_spamfree_version' );
			if( !empty( $wpsf_installed_ver ) && empty( $installed_ver ) && !empty( $old_version_active ) ) {
				$spamfree_options	= get_option( 'spamfree_options' );
			}

			/* Set Initial Options */
			if( !empty( $spamshield_options ) ) {
				$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT );
				foreach( $wpss_options_default as $d => $v ) { if( !isset( $spamshield_options[$d] ) ) { $spamshield_options[$d] = $v; } }
				if( !isset( $spamshield_options['install_date'] ) ) { $spamshield_options['install_date'] = date( WPSS_DATE_BASIC ); }
				self::update_option( array( 'log_key' => rs_wpss_get_log_key( FALSE ), 'wpss_version' => WPSS_VERSION ), FALSE );
			} elseif( !empty( $spamfree_options ) ) {
				$spamshield_options = $spamfree_options;
				$wpss_options_default = unserialize( WPSS_OPTIONS_DEFAULT );
				foreach( $wpss_options_default as $d => $v ) { if( !isset( $spamshield_options[$d] ) ) { $spamshield_options[$d] = $v; } }
				self::update_option( array( 'install_date' => date('Y-m-d'), 'log_key' => rs_wpss_get_log_key( FALSE ), 'wpss_version' => WPSS_VERSION ), FALSE );
				$notice_text = __( 'You have successfully upgraded from WP-SpamFree to WP-SpamShield. You will now experience improved security, page load speed, and spam-blocking power. All your old settings have been imported, and your contact forms will continue to work without you having to do anything else. You can safely remove the outdated WP-SpamFree plugin.', 'wp-spamshield' );
				$new_admin_notice = array( 'style' => 'updated', 'notice' => $notice_text );
				update_option( 'spamshield_admin_notices', $new_admin_notice );
			} else { /* $spamshield_options must be empty for the defaults to get set...otherwise it will update_option() with the current value */
				/* DEFAULTS */
				$spamshield_options = unserialize( WPSS_OPTIONS_DEFAULT );
				self::update_option( array( 'form_message_recipient' => $admin_email, 'install_date' => date('Y-m-d'), 'log_key' => rs_wpss_get_log_key( FALSE ), 'wpss_version' => WPSS_VERSION ), FALSE );
			}

			if( empty( $spamshield_options['form_message_recipient'] ) || !is_email( $spamshield_options['form_message_recipient'] ) ) {
				$spamshield_options['form_message_recipient'] = $admin_email;
			}
			$spam_count = rs_wpss_count();
			if( empty( $spam_count ) ) { update_option( 'spamshield_count', 0 ); }
			self::update_option( $spamshield_options );
			/**
			 *  Reset Log and Initialize .htaccess
			 */
			$admin_ips = get_option( 'spamshield_admins' );
			if( !empty( $admin_ips ) ) { rs_wpss_log_reset( $admin_ips ); } else { rs_wpss_log_reset( NULL, FALSE, FALSE, TRUE ); /* Create log file if it doesn't exist */ }
			/* Require Author Names and Emails on Comments - Added 1.1.7 */
			update_option( 'require_name_email', '1' );
			/**
			 *  Set 'default_role' to 'subscriber' for security - Added 1.3.7 - Disabled 1.9.0.6
			 *  update_option('default_role', 'subscriber');
			 *  Turn on Comment Moderation
			 *  update_option('comment_moderation', 1);
			 *  update_option('moderation_notify', 1);
			 *  Compatibility Checks
			 */
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { rs_wpss_admin_jp_fix(); }
			rs_wpss_admin_ao_fix();
			rs_wpss_admin_fscf_fix();

			/* Ensure Correct Permissions of JS file */
			$inst_file_test_3 = WPSS_PLUGIN_JS_PATH.WPSS_DS.'jscripts.php';
			@clearstatcache();
			$inst_file_test_3_perm = WPSS_PHP::fileperms( $inst_file_test_3 );
			if( $inst_file_test_3_perm < 644 || ! @is_readable( $inst_file_test_3 ) ) {
				WPSS_PHP::chmod( $inst_file_test_3, 664 );
			}
			/* Set Secure Permissions for Includes Folders */
			WPSS_PHP::chmod( WPSS_PLUGIN_INCL_PATH, 750 );
			WPSS_PHP::chmod( WPSS_PLUGIN_PATH.WPSS_DS.'lib', 750 );
		}

		/* Check Installation Status */
		$this->check_install_status( TRUE );
		if( TRUE === WPSS_IP_BAN_CLEAR ) { WPSS_Security::clear_ip_ban(); }
	}

	function deactivation() {
		rs_wpss_log_reset();
		$upd_options = array( 'spamshield_wpssmid_cache' => array() );
		foreach( $upd_options as $option => $val ) { update_option( $option, $val ); }
		$del_options = array( 'spamshield_admin_notices', 'spamshield_install_status', 'spamshield_warning_status', 'spamshield_regalert_status', );
		foreach( $del_options as $i => $option ) { delete_option( $option ); }
		self::delete_option( array( 'admin_notices', 'host_ns_bl', 'install_status', 'regalert_status', 'ubl_cache', 'warning_status', 'wpssmid_cache', ) );
		rs_wpss_purge_nonces();
		if( TRUE === WPSS_IP_BAN_CLEAR ) { WPSS_Security::clear_ip_ban(); }
	}

	/**
	 *	Updation function
	 *	Triggers `upgrade_check()` which runs post-upgrade maintenance tasks, including `purge_cache()`
	 *	@dependencies	WP_SpamShield::get_option(), WP_SpamShield::deprecated_options_check(), WP_SpamShield->upgrade_check(), WPSS_Security::clear_ip_ban(), ...
	 *	@since			1.9.12
	 */
	function updation( $type = array() ) {
		$this->upgrade_check();
		$this->purge_cache();
	}

	/**
	 *	Register updation hook. Similar to `register_activation_hook()` and `register_deactivation_hook()`
	 *	Adds action hook to be fired after auto-updates and bulk upgrades are complete.
	 *  @since			1.9.12
	 */
	static public function register_updation_hook( $hook ) {
		add_action( 'wpss_updates_complete', $hook, 1000, 1 );
	}

	/**
	 *	Create the updation hook
	 *  @since			1.9.12
	 */
	function create_updation_hook() {
		if( ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) || rs_wpss_is_installing() || ( defined( 'AUTOMATIC_UPDATER_DISABLED' ) && AUTOMATIC_UPDATER_DISABLED ) ) { return FALSE; }
		add_action( 'pre_auto_update', array( $this, 'pre_auto_update' ), 1000, 3 );
		add_action( 'automatic_updates_complete', array( $this, 'updates_complete' ), 1000, 1 );
		add_action( 'upgrader_process_complete', array( $this, 'updates_complete' ), 1000, 2 );
		add_action( 'after_db_upgrade', array( $this, 'add_updation_hook' ), 1000, 0 );
		add_action( 'activated_plugin', array( $this, 'add_updation_hook' ), 1000, 0 );
		add_action( 'deactivated_plugin', array( $this, 'add_updation_hook' ), 1000, 0 );
	}

	/**
	 *	Fires immediately before queued auto-updates start
	 *	See: wp-admin/includes/class-wp-automatic-updater.php
	 *	@param			string		$type		The type of update being checked: 'core', 'theme', 'plugin', or 'translation'.
	 *	@param			object		$item		The update offer.
	 *	@param			string		$context	The filesystem context (a path) against which filesystem access and status should be checked.
	 *	@used by		WP_SpamShield::create_updation_hook()
	 *  @since			1.9.12
	 */
	function pre_auto_update( $type = NULL, $item = array(), $context = NULL ) {
		if( empty( $type ) || !is_string( $type ) ) { return; }
		global $wpss_auto_update_type;
		$wpss_auto_update_type = ( empty( $wpss_auto_update_type ) || !is_array( $wpss_auto_update_type ) ) ? array() : $wpss_auto_update_type;
		$wpss_auto_update_type[$type] = TRUE;
	}

	/**
	 *	Creates hook that fires immediately after queued auto-updates and bulk upgrades complete.
	 *	See: wp-admin/includes/class-wp-automatic-updater.php
	 *	@dependencies	...
	 *	@used by		WP_SpamShield::create_updation_hook()
	 *  @since			1.9.12
	 */
	function updates_complete( $results = NULL, $data = array() ) {
		remove_action( 'automatic_updates_complete', array( $this, 'updates_complete' ), 1000 );
		remove_action( 'upgrader_process_complete', array( $this, 'updates_complete' ), 1000 );
		global $wpss_auto_update_type;
		$type		= ( !empty( $wpss_auto_update_type ) && is_array( $wpss_auto_update_type ) ) ? $wpss_auto_update_type : '';
		$type		= ( empty( $type ) && !empty( $data ) && is_array( $data ) && !empty( $data['type'] ) ) ? array( $data['type'] => TRUE, ) : $type;
		$allowed	= array( 'core', 'theme', 'plugin', 'translation', );
		/**
		 *	The dynamic portion of the hook name, `$t`, refers to the type of update/upgrade completed. 
		 *	Can be 'core', 'theme', 'plugin', or 'translation'. ( `$allowed` )
		 */
		foreach( $type as $t => $v ) {
			if( empty( $t ) || !is_string( $t ) || !WPSS_PHP::in_array( $t, $allowed ) || did_action( 'wpss_updates_complete_'.$t ) ) { continue; }
			do_action( 'wpss_updates_complete_'.$t, $t );
		}
		if( !did_action( 'wpss_updates_complete' ) ) {
			do_action( 'wpss_updates_complete', $type );
		}
	}

	/**
	 *	Add hook that fires immediately after updates such as DB upgrades, and plugins activated/deactivated.
	 *	See: wp-admin/includes/class-wp-automatic-updater.php
	 *	@dependencies	...
	 *	@used by		WP_SpamShield::create_updation_hook()
	 *  @since			1.9.12
	 */
	function add_updation_hook() {
		if( !did_action( 'wpss_updates_complete' ) ) {
			do_action( 'wpss_updates_complete' );
		}
	}

	/**
	 *	Add filter and action hooks that fire immediately after WordPress has determined the request is a 404, but before headers are served.
	 *	This can be used to add functionality or change HTTP status from 404 before headers are served.
	 *	@hook			filter|status_header|10000
	 *	@dependencies	none
	 *	@used by		rs_wpss_mod_status_header()
	 *  @since			1.9.15
	 */
	function add_404_hook( $status_header, $code, $description, $protocol ) {
		if( 404 != $code ) { return $status_header; }
		remove_filter( 'status_header', array( $this, 'add_404_hook' ), 10000 );
		$status_header = apply_filters( 'wpss_filter_404', $status_header, $code, $description, $protocol );
		if( !did_action( 'wpss_doing_404' ) ) {
			do_action( 'wpss_doing_404' );
		}
		return $status_header;
	}

	function create_admin_page() {
		if( rs_wpss_is_admin_sproc( TRUE ) ) { return; }
		add_options_page( 'WP-SpamShield ' . __( 'Settings' ), 'WP-SpamShield', 'manage_options', WPSS_PLUGIN_NAME, array( $this, 'plugin_admin_page' ) );
	}

	/**
	 *	Check if we're on a WP-SpamShield admin page
	 *	@dependencies	...
	 *	@since			1.9.9.9.4
	 */
	static protected function is_admin_page() {
		return ( FALSE !== strpos( WPSS_THIS_URL, WPSS_PLUGIN_ADMIN_URL ) );
	}

	function network_admin_remove_plugin( $all ) {
		/**
		 *	Remove plugin from Network Admin Plugins list to prevent Network Activation
		 *	@since 1.9.7.5
		 */
		global $current_screen;
		if( $current_screen->is_network ) { unset( $all[WPSS_PLUGIN_BASENAME] ); }
		return $all;
	}

	/**
	 *	Add "Settings" link to the plugin action links (left side) on Dashboard Plugins page
	 *	Before other links
	 *	@dependencies	...
	 *	@since			...
	 */
	function action_links( $actions, $file, $plugin_data, $status ) {
		if( rs_wpss_is_admin_sproc( TRUE ) ) { return $actions; }
		unset( $actions['edit'] ); /* Remove Edit link - Added 1.9.7.5 */
		$new_actions = array(
			'<a href="options-general.php?page='.WPSS_PLUGIN_NAME.'">' . __( 'Settings' ) . '</a>',
		);
		$actions = array_merge( $new_actions, $actions );
		return $actions;
	}

	/**
	 *  Add links on Dashboard Plugins page, in plugin meta (right side)
	 *  After other links
	 *  @dependencies	...
	 *  @since			1.0.0
	 */
	function meta_links( $links, $file, $plugin_data, $status ) {
		if( rs_wpss_is_admin_sproc( TRUE ) ) { return $links; }
		global $wpss_meta_links;
		if( empty( $wpss_meta_links ) || !is_array( $wpss_meta_links ) ) { $wpss_meta_links = array(); }
		if( empty( $wpss_meta_links[$file] ) || !is_array( $wpss_meta_links[$file] ) ) { $wpss_meta_links[$file] = array(); }
		if( WPSS_PLUGIN_BASENAME === $file ) {
			$links[] = '<a href="'. rs_wpss_append_url( WPSS_HOME_URL ) .'" target="_blank" rel="external" >' . rs_wpss_doc_txt() . '</a>';
			$links[] = '<a href="'. rs_wpss_append_url( WPSS_SUPPORT_URL ) .'" target="_blank" rel="external" >' . __( 'Support', 'wp-spamshield' ) . '</a>';
			if( rs_wpss_count() >= 2000 ) {
				$links[] = '<a href="'.WPSS_WP_RATING_URL.'" title="' . __( 'Let others know by giving it a good rating on WordPress.org!', 'wp-spamshield' ) . '" target="_blank" rel="external" >' . __( 'Rate the Plugin', 'wp-spamshield' ) . '</a>';
			}
			$links[] = '<a href="'.WPSS_DONATE_URL.'" target="_blank" rel="external" >' . __( 'Donate', 'wp-spamshield' ) . '</a>';
		}
		$disable_security_alerts = self::get_option( 'disable_security_alerts' );
		$slug = trim( dirname( $file ), '/' );
		if( !empty( $slug ) && is_super_admin() && empty( $disable_security_alerts ) && ( empty( $_GET['plugin_status'] ) || 'all' === $_GET['plugin_status'] || 'active' === $_GET['plugin_status'] || 'inactive' === $_GET['plugin_status'] || 'upgrade' === $_GET['plugin_status'] ) ) { /* Superadmin only / Don't add on MU or drop-in page */
			$title = __( 'Security check provided by WP-SpamShield. Data provided by WPScan Vulnerability Database.', 'wp-spamshield' ); /* TO DO: TRANSLATE */
			$links[] = '<a href="https://wpvulndb.com/plugins/'.$slug.'" target="_blank" rel="external" title="'.$title.'" >' . __( 'Security Check', 'wp-spamshield' ) . '</a>'; /* TO DO: Translate */
		}
		return $links;
	}

	/**
	 *	Add WP-SpamShield links to the row of links for each comment on the edit comments page
	 *	After other links
	 *	@param			array		$actions	An array of comment actions. Default actions include: Approve', 'Unapprove', 'Edit', 'Reply', 'Spam', 'Delete', and 'Trash'.
	 *	@param			WP_Comment	$comment	The comment object.
	 *	@hook			filter|comment_row_actions|200
	 *	@dependencies	...
	 *	@since			1.9.9.8.8
	 */
	function comment_edit_links( $actions, $comment ) {
		if( rs_wpss_is_admin_sproc( TRUE ) || !rs_wpss_is_user_admin() ) { return $actions; }
		$enhanced_comment_blacklist = WP_SpamShield::get_option( 'enhanced_comment_blacklist' );
		if( empty( $enhanced_comment_blacklist ) ) { return $actions; }
		if( user_can( $comment->user_id, 'edit_comment' ) ) { return $actions; }
		$ip = $comment->comment_author_IP;
		$blacklist_url = esc_url( WP_SpamShield::blacklist_url( $ip ) );
		$actions['blacklist']	= ( rs_wpss_blacklist_check( NULL, NULL, NULL, NULL, $ip, NULL, $ip ) ) ? __( 'IP Blacklisted', 'wp-spamshield' ) : '<a href="' . $blacklist_url . '" title="' . __( 'Blacklist the IP Address:', 'wp-spamshield' ) . ' ' . $ip . '">' . __( 'Blacklist', 'wp-spamshield' ) . '</a>';
		if( TRUE === WP_DEBUG ) {
			$hide_extra_data = WP_Spamshield::get_option( 'hide_extra_data' );
			if( empty( $hide_extra_data ) ) {
				$actions['abuseipdb'] = '<a href="https://www.abuseipdb.com/check/' . $ip . '" target="_blank" rel="external noopener noreferrer" >' . __( 'AbuseIPDB', 'wp-spamshield' ) . '</a>';
				$actions['iplookup'] = '<a href="http://ipaddressdata.com/' . $ip . '" target="_blank" rel="external noopener noreferrer" >' . __( 'IP Address Lookup', 'wp-spamshield' ) . '</a>';
			}
		}
		return $actions;
	}

	/**
	 *	Get the blacklist URL for the given IP address
	 *	@dependencies	...
	 *	@since			1.9.9.8.8
	 */
	static public function blacklist_url( $ip, $comment_id = NULL ) {
		$plugin_admin_url	= WPSS_PLUGIN_ADMIN_URL;
		$ip_nodot			= str_replace( array( '.', ':' ), '', $ip );
		$nonce_action		= 'blacklist_ip_'.$ip;
		$nonce_name			= 'bl'.$ip_nodot.'tkn';
		$nonce				= rs_wpss_create_nonce( $nonce_action, $nonce_name );
		$query_args			= array(
			'wpss_action'	=> 'blacklist_ip',
			'bl_ip'			=> $ip,
			$nonce_name		=> $nonce,
		);
		/**
		 *	@since	1.9.9.9.4	Added $comment_id to delete comment when IP is blacklisted
		 */
		if( !empty( $comment_id ) ) {
			$query_args['c'] = $comment_id;
		}
		return add_query_arg( $query_args, $plugin_admin_url );
	}

	function settings_css() {
		$css_handle = 'wpss-admin';
		wp_register_style( $css_handle, WPSS_PLUGIN_CSS_URL.'/'.$css_handle.'.css', NULL, WPSS_VERSION );
		wp_enqueue_style( $css_handle );
	}

	function approve_previous_users() {
		/**
		 *  Check # of users and DB size before running
		 *  TO DO: When implementing, ADD: error message, and then schedule cron jobs to handle in small chunks over 24 hours
		 */
		$user_ids = get_users( array( 'blog_id' => '', 'fields' => 'ID' ) );
		if( count( $user_ids ) >= 1000 ) { return FALSE; }
		$db_size = self::get_db_size();
		if( $db_size >= ( 100 * 1024 * 1024 ) ) { return FALSE; }
		foreach( $user_ids as $user_id ) {
			update_user_meta( $user_id, 'wpss_new_user_approved', TRUE );
			update_user_meta( $user_id, 'wpss_new_user_email_sent', TRUE );
		}
		self::update_option( array( 'init_user_approve_run' => TRUE ) );
	}

	function dashboard_counter() {
		if( rs_wpss_is_admin_sproc( TRUE ) ) { return; }
		$spam_count_raw = rs_wpss_count();
		$spamshield_options = WP_SpamShield::get_option();
		$current_date = date( WPSS_DATE_BASIC );
		if( empty( $spamshield_options['install_date'] ) ) {
			$install_date = $current_date;
			$spamshield_options['install_date'] = $install_date;
			self::update_option( $spamshield_options );
		} else { $install_date = $spamshield_options['install_date']; }
		$num_days_inst	= rs_wpss_date_diff($install_date, $current_date); if( $num_days_inst < 1 ) { $num_days_inst = 1; }
		$spam_count		= $spam_count_raw; if( $spam_count < 1 ) { $spam_count = 1; }
		$avg_blk_dly	= round( $spam_count / $num_days_inst );
		$avg_blk_dly_d	= rs_wpss_number_format( $avg_blk_dly );
		if( rs_wpss_is_user_admin() ) {
			$spam_stat_incl_link = ' (<a href="options-general.php?page='.WPSS_PLUGIN_NAME.'"">' . __( 'Settings' ) . '</a>)</p>'.WPSS_EOL;
			$spam_stat_url = 'options-general.php?page='.WPSS_PLUGIN_NAME;
			$spam_stat_href_attr = '';
		} else {
			$spam_stat_incl_link = '';
			$spam_stat_url = WPSS_HOME_URL;
			$spam_stat_href_attr = 'target="_blank" rel="external"';
		}

		if( empty( $spam_count_raw ) ) {
			echo '<p>' . __( 'No comment spam attempts have been detected yet.', 'wp-spamshield' ) . $spam_stat_incl_link;
		} else {
			echo '<p>'."<img src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAABMElEQVR4Ae3WJVhEURAF4Fdwd5eGZ7Q3pKB9GwXpCQpuBXd3bfSGu7u7+3DC4PAEt/t9/8qVOeuzgqIRW9IBTcJ7DxS1hgkg1g1W71FYA3KAYBUCYAqIxYGWkoJeEALxMArESnldHY6A7jmHRoiEIPAVC9gDYqeQAkb31k1uAkTsiQUQFIqsv0tA4kcHJP0HfPl7kMW3De7jOUdYuyn0yA7PH4gFHADBLuzfx/MLYM579e7juXQgsYAtIBFboC1yPk0qoPeNAZ1wIRaQ+cYAggbxH7tXBmA+Ggj8pH5RS5QGYM6Z11vk9oAx2QH4VPHagezegI12sC4WwPs8eP4KbJV2M1MYfSZAg9dVPLcGZm9pm1VAbBr0oQYI2t6r6UcAsSu+Vr33PwsjGIBpMJZ77BqB/vSrID7csgAAAABJRU5ErkJggg==' alt='' width='24' height='24' style='border-style:none;vertical-align:middle;padding-right:7px;' />".' <a href="'.$spam_stat_url.'" '.$spam_stat_href_attr.'>WP-SpamShield</a> '.sprintf( __( 'has blocked <strong> %s </strong> spam.', 'wp-spamshield' ), rs_wpss_number_format( $spam_count_raw ) ).'</p>'.WPSS_EOL;
			if( $avg_blk_dly >= 2 ) {
				echo "<p><img src='data:image/png;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAUUAAAALAAAAAABAAEAAAICRAEAOw==' alt='' width='24' height='24' style='border-style:none;vertical-align:middle;padding-right:7px;' /> " . __( 'Average spam blocked daily', 'wp-spamshield' ) . ": <strong>".$avg_blk_dly_d."</strong></p>".WPSS_EOL;
			}
		}
	}

	function plugin_admin_page() {
		if( !is_admin() || rs_wpss_is_admin_sproc( TRUE ) ) { return; }
		require_once( WPSS_PLUGIN_INCL_PATH.WPSS_DS.'admin-settings.php' );
	}

	function settings_ver_ftr() {
		echo 'Version '.WPSS_VERSION;
		if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || !empty( $GLOBALS['wpss_jit_compat_mode'] ) ) {
			echo "<br />".WPSS_EOL. WPSS_Func::upper( __( 'Compatibility Mode', 'wp-spamshield' ) ); /* TRANSLATE */
		}
		if( self::is_debug() ) {
			$wpss_proc_data = get_option( 'spamshield_procdat' );
			if( !isset( $wpss_proc_data['avg2_wpss_proc_time'] ) && isset( $wpss_proc_data['avg_wpss_proc_time'] ) ) {
				$wpss_proc_data['avg2_wpss_proc_time'] = $wpss_proc_data['avg_wpss_proc_time'];
			} elseif( !isset( $wpss_proc_data['avg2_wpss_proc_time'] ) ) { $wpss_proc_data['avg2_wpss_proc_time'] = 0; }
			$wpss_proc_data_avg2_wpss_proc_time_disp = rs_wpss_number_format( $wpss_proc_data['avg2_wpss_proc_time'], 6 );
			echo "<br />".WPSS_EOL.'Avg WPSS Proc Time: '.$wpss_proc_data_avg2_wpss_proc_time_disp.' seconds';
		}
	}

	static public function new_admin_notice( $notice_text, $callback ) {
		$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
		update_option( 'spamshield_admin_notices', $new_admin_notice );
		add_action( 'admin_notices', $callback );
		@WP_SpamShield::append_log_data( NULL, NULL, $notice_text );
	}

	static public function new_network_admin_notice( $notice_text, $callback ) {
		$new_admin_notice = array( 'style' => 'error notice is-dismissible', 'notice' => $notice_text );
		update_option( 'spamshield_admin_notices', $new_admin_notice );
		add_action( 'network_admin_notices', $callback );
		@WP_SpamShield::append_log_data( NULL, NULL, $notice_text );
	}

	function admin_notices() {
		$admin_notices = get_option( 'spamshield_admin_notices' );
		if( !empty( $admin_notices ) ) {
			$style 	= $admin_notices['style']; /* 'error', 'updated', 'is-dismissible', 'updated notice is-dismissible' */
			$notice	= $admin_notices['notice'];
			echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
		}
		delete_option( 'spamshield_admin_notices' );
	}

	function admin_cpn_notices() {
		/* Admin Custom Plugin Notices */
		global $current_user; $current_user = wp_get_current_user();
		$notices = get_user_meta( $current_user->ID, 'wpss_nag_notices', TRUE );
		if( !empty( $notices ) ) {
			$nid			= $notices['nid'];
			$style			= $notices['style']; /* 'error', 'updated', 'is-dismissible', 'updated notice is-dismissible' */
			$spam_count		= rs_wpss_count();
			$timenow		= time();
			$avg_blk_dly_d	= rs_wpss_number_format( $this->spam_blocked_daily() );
			$est_hrs_ret_d	= rs_wpss_number_format( $this->est_hrs_returned( $spam_count ) );
			$url			= WP_SpamShield::get_url( TRUE );
			$query_args		= rs_wpss_get_query_args( $url );
			$query_str		= '?' . http_build_query( array_merge( $query_args, array( 'wpss_hide_cpn' => '1', 'nid' => $nid ) ) );
			$query_str_con	= 'QUERYSTRING';
			$spamcount_con	= 'SPAMCOUNT';
			$spamdaily_con	= 'SPAMDAILY';
			$spamhours_con	= 'SPAMHOURS';
			$notice			= str_replace( array( $query_str_con, $spamcount_con, $spamdaily_con, $spamhours_con ), array( $query_str, rs_wpss_number_format( $spam_count ), $avg_blk_dly_d, $est_hrs_ret_d ), $notices['notice'] );
			echo '<div class="'.$style.'"><p>'.$notice.'</p></div>';
		}
	}

	function check_cpn_notices() {
		/* Check Custom Plugin Notices */
		global $current_user; $current_user = wp_get_current_user();
		$status			= get_user_meta( $current_user->ID, 'wpss_nag_status', TRUE );
		if( !empty( $status['currentnag'] ) ) { add_action( 'admin_notices', array( $this, 'admin_cpn_notices' ) ); return; }
		if( !is_array( $status ) ) { $status = array(); update_user_meta( $current_user->ID, 'wpss_nag_status', $status ); }
		$timenow		= time();
		$spam_count		= rs_wpss_count();
		$avg_blk_dly	= $this->spam_blocked_daily();
		$est_hrs_ret	= $this->est_hrs_returned( $spam_count );
		$query_str_con	= 'QUERYSTRING';
		$spamcount_con	= 'SPAMCOUNT';
		$spamdaily_con	= 'SPAMDAILY';
		$spamhours_con	= 'SPAMHOURS';
		/* Reminders */
		if( empty( $status['currentnag'] ) && ( empty( $status['lastnag'] ) || $status['lastnag'] <= $timenow - 1209600 ) ) {
			if( empty( $status['vote'] ) && $spam_count >= 2000 ) { /* TO DO: TRANSLATE */
				$nid = 'n01'; $style = 'updated';
				$avg_d_txt = '';
				if( $avg_blk_dly >= 2 ) {
					$avg_d_txt = ' '. sprintf( __( 'That\'s <strong> %s </strong> spam a day that you don\'t have to worry about.', 'wp-spamshield' ), $spamdaily_con );
				}
				$notice_text = sprintf( __( 'WP-SpamShield has blocked <strong> %s </strong> spam!', 'wp-spamshield' ), $spamcount_con ) .$avg_d_txt.'</p><p>'. __( 'If you like how WP-SpamShield protects your site from spam, would you take a moment to give it a rating on WordPress.org?', 'wp-spamshield' ) .'</p><p>'. sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.WPSS_WP_RATING_URL.'" target="_blank" rel="external" ', __( 'Yes, I\'d like to rate it!', 'wp-spamshield' ) ) .' &mdash; '.  sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.$query_str_con.'" ', __( 'I already did!', 'wp-spamshield' ) );
				$status['currentnag'] = TRUE; $status['vote'] = FALSE;
			} elseif( empty( $status['donate'] ) && $est_hrs_ret >= 40 ) { /* TO DO: TRANSLATE */
				$nid = 'n02'; $style = 'updated';
				$notice_text = __( 'Happy with WP-SpamShield?', 'wp-spamshield' ) .' '. sprintf( __( 'This plugin has saved you <strong> %s </strong> hours of managing spam. You\'re welcome!', 'wp-spamshield' ), $spamhours_con ) .'</p><p>'. __( 'WP-SpamShield is provided for free.', 'wp-spamshield' ) . ' ' . __( 'If you like the plugin, consider a donation to help further its development.', 'wp-spamshield' ) .'</p><p>'. sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.WPSS_DONATE_URL.'" target="_blank" rel="external" ', __( 'Yes, I\'d like to donate!', 'wp-spamshield' ) ) .' &mdash; '. sprintf( '<strong><a href=%1$s>%2$s</a></strong>', '"'.$query_str_con.'" ', __( 'I already did!', 'wp-spamshield' ) );
				$status['currentnag'] = TRUE; $status['donate'] = FALSE;
			}
		}
		/* Warnings */
		/* TO DO: Add Warnings - about plugin conflicts, configuration issues, and missing PHP functions */
		if( !empty( $status['currentnag'] ) ) {
			add_action( 'admin_notices', array( $this, 'admin_cpn_notices' ) );
			$new_cpn_notice = array( 'nid' => $nid, 'style' => $style, 'notice' => $notice_text );
			update_user_meta( $current_user->ID, 'wpss_nag_notices', $new_cpn_notice );
			update_user_meta( $current_user->ID, 'wpss_nag_status', $status );
		}
	}

	function hide_cpn_notices() {
		/* Hide Custom Plugin Notices */
		if( rs_wpss_is_admin_sproc( TRUE ) || !rs_wpss_is_user_admin() || rs_wpss_is_doing_ajax() ) { return; }
		$cpns_codes		= array( 'n01' => 'vote', 'n02' => 'donate', ); /* CPN Status Codes */
		if( !isset( $_GET['wpss_hide_cpn'], $_GET['nid'], $cpns_codes[$_GET['nid']] ) || $_GET['wpss_hide_cpn'] != '1' ) { return; }
		global $current_user; $current_user = wp_get_current_user();
		$status			= get_user_meta( $current_user->ID, 'wpss_nag_status', TRUE );
		$timenow		= time();
		$url			= WP_SpamShield::get_url( TRUE );
		$query_args		= rs_wpss_get_query_args( $url ); unset( $query_args['wpss_hide_cpn'],$query_args['nid'] );
		$query_str		= http_build_query( $query_args ); if( $query_str != '' ) { $query_str = '?'.$query_str; }
		$redirect_url	= rs_wpss_fix_url( $url, TRUE, TRUE ) . $query_str;
		$status['currentnag'] = FALSE; $status['lastnag'] = $timenow; $status[$cpns_codes[$_GET['nid']]] = TRUE;
		update_user_meta( $current_user->ID, 'wpss_nag_status', $status );
		update_user_meta( $current_user->ID, 'wpss_nag_notices', array() );
		wp_redirect( $redirect_url );
		exit;
	}

	function check_requirements() {
		global $wpss_requirements_checked; if( !empty( $wpss_requirements_checked ) ) { return; }
		if( rs_wpss_is_session_active() && isset( $_SESSION['wpss_requirements_checked_'.WPSS_HASH] ) && 1 * HOUR_IN_SECONDS > ( time() - $_SESSION['wpss_requirements_checked_'.WPSS_HASH] ) ) { $wpss_requirements_checked = TRUE; return; }
		if( rs_wpss_is_admin_sproc( TRUE ) || rs_wpss_is_doing_ajax() ) { return; }
		if( is_multisite() && current_user_can( 'manage_network' ) ) {
			/* Check for pending admin notices */
			$admin_notices = get_option( 'spamshield_admin_notices' );
			if( !empty( $admin_notices ) ) { add_action( 'network_admin_notices', array( $this, 'admin_notices' ) ); }
			/* Make sure not network activated */
			if( is_plugin_active_for_network( WPSS_PLUGIN_BASENAME ) ) {
				deactivate_plugins( WPSS_PLUGIN_BASENAME, TRUE, TRUE );
				$notice_text = __( 'Plugin deactivated. WP-SpamShield is not available for network activation.', 'wp-spamshield' ); /* TO DO: Fix translation. */
				self::new_network_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
		}
		if( rs_wpss_is_user_admin() ) {
			/* Check for deprecated options */
			self::deprecated_options_check();
			/* Check if plugin has been upgraded */
			$this->upgrade_check();
			/* Check for outdated WordPress versions with known security flaws */
			$this->insecure_wordpress_check();
			/* Check for pending admin notices */
			$admin_notices = get_option('spamshield_admin_notices');
			if( !empty( $admin_notices ) ) { add_action( 'admin_notices', array( $this, 'admin_notices' ) ); }
			/* Run Code Integrity Check */
			if( !$this->integrity_check() ) {
				deactivate_plugins( WPSS_PLUGIN_BASENAME );
				$notice_text = '<p>' . __('Plugin <strong>deactivated</strong>.') . ' ' . sprintf( __( 'Code integrity check failed. Please uninstall and delete your current copy of the plugin, download a fresh copy from an official source, and reinstall. <a href=%1$s>More Information</a>', 'wp-spamshield' ), '"'. rs_wpss_append_url( 'https://www.redsandmarketing.com/plugins/wp-spamshield/?wpss=security_note#wpss_security_note' ) .'" target="_blank" rel="external" ' )  . '</p>'; /* TO DO: TRANSLATE - Added 1.9.9.4 */
				self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
			/* Make sure user is not running site in incompatible web hosting environments */
			if( WPSS_Filters::host_ns_blacklist_chk() ) {
				deactivate_plugins( WPSS_PLUGIN_BASENAME );
				$notice_text = '<p>' . __('Plugin <strong>deactivated</strong>.') . ' ' . sprintf( __( 'Your web hosting environment does not meet the compatibility requirements. <strong>Your web host may have one or more characteristics of a high-risk web host.</strong> <a href=%1$s>More Information</a>', 'wp-spamshield' ), '"'. rs_wpss_append_url( WPSS_RSM_URL.'recommended-web-hosts/?err=not_compatible' ) .'" target="_blank" rel="external" ' ) . '</p>'; /* TO DO: TRANSLATE - Added 1.9.9.4 */
				self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
			/* Make sure user has minimum required WordPress version, in order to prevent issues */
			$wpss_wp_version = WPSS_WP_VERSION; /* Convert constant to variable so it can be checked with empty() in PHP < 5.5 */
			if( !empty( $wpss_wp_version ) && version_compare( $wpss_wp_version, WPSS_REQUIRED_WP_VERSION, '<' ) ) { /* Be sure to check with empty() first */
				deactivate_plugins( WPSS_PLUGIN_BASENAME );
				$notice_text = sprintf( __( 'Plugin deactivated. WordPress Version %s required. Please upgrade WordPress to the latest version.', 'wp-spamshield' ), WPSS_REQUIRED_WP_VERSION ); /* TO DO: Fix translation. */
				self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
			/* Make sure user has minimum required PHP version, in order to prevent issues */
			$wpss_php_version = PHP_VERSION; /* Convert constant to variable so it can be checked with empty() in PHP < 5.5 */
			if( !empty( $wpss_php_version ) && version_compare( $wpss_php_version, WPSS_REQUIRED_PHP_VERSION, '<' ) ) { /* Be sure to check with empty() first */
				deactivate_plugins( WPSS_PLUGIN_BASENAME );
				$notice_text = '<p>' . __('Plugin <strong>deactivated</strong>.') . ' ' . str_replace( 'WordPress', 'WP-SpamShield', sprintf( __('Your server is running PHP version %1$s but WordPress %2$s requires at least %3$s.'), PHP_VERSION, WPSS_VERSION, WPSS_REQUIRED_PHP_VERSION ) ) . ' ' . sprintf( __( 'We are no longer supporting PHP 5.2, as it reached its End of Life (no longer supported by the PHP team) <a href=%2$s>in 2011</a>, and there are known security, performance, and compatibility issues. We are phasing out support for PHP 5.3 as it reached End of Life in 2014.</p><p>The version of PHP running on your server is <em>extremely out of date</em>. You should upgrade your PHP version as soon as possible.</p><p>If you need help with this, please contact your web hosting company and ask them to switch your PHP version to 5.5, 5.6, or higher. Please see the <a href=%4$s>plugin documentation</a> and <a href=%5$s>changelog</a> if you have further questions.', 'wp-spamshield' ), WPSS_REQUIRED_PHP_VERSION, '"http://php.net/archive/2011.php#id2011-08-23-1" target="_blank" rel="external" ', $wpss_php_version, '"'. rs_wpss_append_url( WPSS_HOME_URL.'?wpss=requirements&src='.WPSS_VERSION.'-php-notice#wpss_requirements' ) .'" target="_blank" rel="external" ', '"'. rs_wpss_append_url( WPSS_HOME_URL.'changelog/?ver=182&src='.WPSS_VERSION.'-php-notice#ver_182' ) .'" target="_blank" rel="external" ' ) . '</p>'; /* TO DO: NEEDS TRANSLATION - Added 1.8.2, Updated 1.9.7.4 */
				self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
			/* Check for configuration issues */
			$wpss_wp_config_error = $this->check_wp_config_status(); /* TO DO: Make version for Network Admins when we add full Multisite compatibility (Network Activation) */
			if( !empty( $wpss_wp_config_error ) ) {
				deactivate_plugins( WPSS_PLUGIN_BASENAME );
				$correct_error = sprintf( __( 'Please check your <a href="%1$s">settings</a> and correct the error.', 'wp-spamshield' ), WPSS_ADMIN_URL.'/options-general.php' );
				$notice_text = '<p>' . sprintf( '%1$s <strong>%2$s %3$s</strong> %4$s', __('Plugin <strong>deactivated</strong>.'), __( 'There is an error in your WordPress configuration.', 'wp-spamshield' ), $wpss_wp_config_error, $correct_error ) . '</p>'; /* TO DO: NEEDS TRANSLATION - Added 1.9.7.1 */
				self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
			/**
			 *  TO DO:
			 *  - Add check for .htaccess capabilities: AllowOverride All 
			 */
			/* Make sure user's site has .htaccess capability & is not on standalone Nginx, in order to prevent issues */
			global $is_apache, $is_IIS, $is_iis7, $is_nginx;
			/* Standalone Nginx */
			if( !empty( $is_nginx ) && empty( $is_apache ) ) {
				deactivate_plugins( WPSS_PLUGIN_BASENAME );
				$notice_text = '<p>' . __('Plugin <strong>deactivated</strong>.') . ' ' . sprintf( __( 'Your server is running standalone Nginx but WP-SpamShield requires either an Apache or Apache/Nginx hybrid setup. Your site does not meet the plugin\'s <a href=%1$s>minimum requirements</a>.', 'wp-spamshield' ), '"'. rs_wpss_append_url( 'https://www.redsandmarketing.com/plugins/wp-spamshield/?wpss=requirements#wpss_requirements' ) .'" target="_blank" rel="external" ' ) . '</p>'; /* TO DO: TRANSLATE - Added 1.9.9.8.2 */
				self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) ); return FALSE;
			}
			/* Check for Incompatible Plugins and Plugins with Duplicated Functionality - warn if missing */

			/* Check for Required PHP Functions - warn if missing */
			$this->check_cpn_notices();
			/* Security Check - See if (extremely) old version of plugin still active */
			$old_version = 'wp-spamfree/wp-spamfree.php';
			$old_version_active = WPSS_Compatibility::is_plugin_active( $old_version, FALSE );
			if( !empty( $old_version_active ) ) {
				/**
				 *  Not safe to keep old version active due to unpatched security hole(s), broken PHP, and lack of maintenance.
				 *  For security reasons, deactivate old version.
				 */
				deactivate_plugins( $old_version );
				/* Clean up database */
				$del_options = array( 'wp_spamfree_version', 'spamfree_count', 'spamfree_options' );
				foreach( $del_options as $i => $option ) { delete_option( $option ); }
				/**
				 *  Good to go!
				 *  Since WP-SpamShield takes over 100% of old version's responsibilities, there is no loss of functionality, only improvements.
				 *  Site speed will improve and server load will now drop dramatically.
				 */
			}
			/* Compatibility Checks */
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { rs_wpss_admin_jp_fix(); }
			rs_wpss_admin_ao_fix();
			rs_wpss_admin_fscf_fix();
		}
		if( rs_wpss_is_session_active() ) {
			$_SESSION['wpss_requirements_checked_'.WPSS_HASH] = time();
		}
		$wpss_requirements_checked = TRUE;
	}

	function upgrade_check( $installed_ver = NULL, $activation = FALSE ) {
		if( empty( $installed_ver ) ) { $installed_ver = self::get_option( 'wpss_version' ); }
		if( $installed_ver !== WPSS_VERSION ) { /* Plugin has been upgraded */
			$upd_options = array( 'spamshield_wpssmid_cache' => array() );
			foreach( $upd_options as $option => $val ) { update_option( $option, $val ); }
			$upd_wpss_options = array();
			if( FALSE === $activation ) { $upd_wpss_options['wpss_version'] = WPSS_VERSION; }
			if( !empty( $upd_wpss_options ) ) { self::update_option( $upd_wpss_options ); }
			$del_options = array( 'spamshield_install_status', 'spamshield_warning_status', 'spamshield_regalert_status' );
			foreach( $del_options as $i => $option ) { delete_option( $option ); }
			self::delete_option( array( 'ubl_cache', ) );
			rs_wpss_purge_nonces();
			$this->update_admin_status();
			WPSS_Compatibility::upgrade_conflict_check();
			$this->purge_cache();
		}
	}

	/**
	 *  TO DO: PRE-CHECKS
	 */

	function check_install_status( $activation = FALSE ) {
		/**
		 *  Check Installation Status and Do Compatibility Pre-Checks
		 *  @since 1.9.1
		 */
		/* Check for incompatible web hosting environments */
		if( WPSS_Filters::host_ns_blacklist_chk() ) { update_option( 'spamshield_install_status', 'incorrect' ); return FALSE; }
		/* Integrity Checks */
		if( !$this->integrity_check() ) { update_option( 'spamshield_install_status', 'incorrect' ); return FALSE; }
		/* Install Status */
		$wpss_inst_status_option = get_option( 'spamshield_install_status' );
		if( !empty( $wpss_inst_status_option ) && $wpss_inst_status_option === 'correct' ) { return TRUE; }
		elseif( !empty( $wpss_inst_status_option ) && $wpss_inst_status_option === 'incorrect' ) { return FALSE; }
		else {
			global $is_apache, $is_IIS, $is_iis7, $is_nginx;
			$inst_plugins_get_test_1	= WPSS_PLUGIN_NAME;			/* 'wp-spamshield' - Checking for 'options-general.php?page='.WPSS_PLUGIN_NAME */
			$inst_file_test_0 			= WPSS_PLUGIN_FILE_PATH;	/* '/public_html/wp-content/plugins/wp-spamshield/wp-spamshield.php' */
			$inst_file_test_3 			= WPSS_PLUGIN_JS_PATH.WPSS_DS.'jscripts.php';
			$inst_file_test_3_perm		= WPSS_PHP::fileperms( $inst_file_test_3 );
			if( $inst_file_test_3_perm < 644 || ! @is_readable( $inst_file_test_3 ) ) { WPSS_PHP::chmod( $inst_file_test_3, 664 ); }
			@clearstatcache();
			$get_page = ( !empty( $_GET['page'] ) ) ? $_GET['page'] : '';
			$jscripts_status = rs_wpss_jscripts_403_fix();
			if( !empty( $GLOBALS['wpss_modify_advanced'] ) ) { $this->purge_cache(); }
			extract( $jscripts_status ); $GLOBALS['wpss_jit_compat_mode'] = $compat_mode;
			$jscripts_blocked = ( FALSE !== $blocked && TRUE !== $compat_mode );
			$js_url = WPSS_PLUGIN_JS_URL.'/jscripts-ftr-min.js';
			$js_status = WP_SpamShield::get_http_status( $js_url );
			if( ( $inst_plugins_get_test_1 === $get_page || TRUE === $activation ) && @file_exists( $inst_file_test_0 ) && @file_exists( $inst_file_test_3 ) && FALSE === $jscripts_blocked && $js_status != 500 && FALSE === $is_nginx ) {
				update_option( 'spamshield_install_status', 'correct' ); return TRUE;
			} else {
				update_option( 'spamshield_install_status', 'incorrect' ); return FALSE;
			}
		}
	}

	function integrity_check() {
		/**
		 *	SECURITY - Check for Code Integrity
		 *	@since 1.9.9.7
		 *	Will be expanding this over time
		 *	TO DO:
		 *		- Add File checksums (md5 hashes)
		 *		- Add Option checksums (md5 hashes)
		 */
		if( 'wp-spamshield' !== WPSS_PLUGIN_NAME ) { return FALSE; }
		if( !self::preg_match( "~^([0-9]{1}\.){1,4}[0-9]{1,2}([a-z][0-9]{1,2})?$~", WPSS_VERSION ) ) { return FALSE; }
		return TRUE; /* PASSED */
	}

	function insecure_wordpress_check() {
		/**
		 *  SECURITY - Check for WordPress versions with known security vulnerabilies.
		 *  TO DO: Move to WPSS_Security class
		 *  @since 1.9.7.8
		 */
		$disable_security_alerts = self::get_option( 'disable_security_alerts' );
		if( !is_super_admin() || !empty( $disable_security_alerts ) ) { return FALSE; }
		$current	= get_site_transient( 'wpss_iswpv_check' );
		/* Invalidate the transient when WPSS_WP_VERSION changes */
		if ( is_object( $current ) && isset( $current->version_checked ) && WPSS_WP_VERSION != $current->version_checked ) { $current = FALSE; }
		if ( !is_object( $current ) ) {
			$current					= new stdClass;
			$current->updates			= array();
			$current->version_checked	= WPSS_WP_VERSION;
		}
		if ( isset( $current->last_checked, $current->version_checked ) && 1 * WEEK_IN_SECONDS > ( time() - $current->last_checked ) && $current->version_checked == WPSS_WP_VERSION ) { return; }
		$current->last_checked	= time();
		set_site_transient( 'wpss_iswpv_check', $current );
		if( self::preg_match( "~^([0-9]\.)+[0-9]+\-RC[0-9]+\-~i", WPSS_WP_VERSION ) ) { return NULL; }
		$wpv		= str_replace( '.', '', WPSS_WP_VERSION );
		$url		= 'https://wpvulndb.com/api/v2/wordpresses/'.$wpv;
		$inf		= 'https://wpvulndb.com/wordpresses/'.$wpv;
		$wps		= 'https://wpvulndb.com/';
		$http_args	= array(
			'timeout'				=> 10,
			'decompress'			=> FALSE,
			'httpversion'			=> '1.1',
			'sslverify'				=> TRUE,
			'limit_response_size'	=> 50 * KB_IN_BYTES,					/* Security: Always limit size of returned data. */
			'reject_unsafe_urls'	=> TRUE,
			'user-agent'			=> WPSS_Security::privacy_ua( TRUE ),	/* Security/Privacy: Remove WP version and site URL from UA. (WP default UA includes these.) */
			'headers'				=> array(),
		);
		$resp		= wp_remote_get( $url, $http_args );
		$data		= ( !is_wp_error( $resp ) ) ? json_decode( wp_remote_retrieve_body( $resp ), TRUE ) : '';
		$vulns		= ( !empty( $data[WPSS_WP_VERSION]['vulnerabilities'] ) ) ? $data[WPSS_WP_VERSION]['vulnerabilities'] : '';
		$vnum		= ( is_array( $vulns ) ) ? count( $vulns ) : 0;
		if( empty( $vnum ) ) { return FALSE; }

		$vc_url		= 'https://api.wordpress.org/core/version-check/1.7/';
		$vc_resp	= wp_remote_get( $vc_url, $http_args );
		$vc_data	= ( !is_wp_error( $vc_resp ) ) ? json_decode( wp_remote_retrieve_body( $vc_resp ), TRUE ) : '';
		$latest		= ( !empty( $vc_data['offers'][0]['version'] ) ) ? (string) $vc_data['offers'][0]['version'] : '';
		if( $latest === WPSS_WP_VERSION ) {
			return FALSE;
		} else {
			$notice_text = sprintf( __( 'Insecure WordPress version detected. Your site is running WordPress version %1$s, which has %2$s known security vulnerabilities. You should upgrade WordPress as soon as possible. <a href=%3$s>More Information</a>', 'wp-spamshield' ), WPSS_WP_VERSION, $vnum, '"'.$inf.'" target="_blank" rel="external" ' ); /* TO DO: TRANSLATE */
		}
		$notice_text  = '<p><strong>' . __( 'SECURITY ALERT', 'wp-spamshield' ) . ':</strong> '.$notice_text.'</p>'; /* TO DO: TRANSLATE */
		$notice_text .= '<p>' . sprintf( __( 'Security alert provided by <a href=%1$s>WP-SpamShield</a>. Data provided by <a href=%2$s>WPScan Vulnerability Database</a>.', 'wp-spamshield' ), '"'.WPSS_HOME_URL.'" target="_blank" rel="external" ', '"'.$wps.'" target="_blank" rel="external" ' ) . '</p>'; /* TO DO: TRANSLATE */
		if( is_multisite() ) {
			self::new_network_admin_notice( $notice_text, array( $this, 'admin_notices' ) );
		} else {
			self::new_admin_notice( $notice_text, array( $this, 'admin_notices' ) );
		}
		return TRUE;
	}

	function disable_plugin_edit() {
		/**
		 *  Disable editing of the plugin through the Admin Plugin Editor
		 *  TO DO: Move to Code Integrity class
		 *  @since 1.9.7.5
		 */
		global $pagenow;
		if( $pagenow === 'plugin-editor.php' && ( ( !empty( $_GET['file'] ) && 0 === strpos( $_GET['file'], WPSS_PLUGIN_NAME ) ) || ( !empty( $_POST['plugin'] ) && 0 === strpos( $_POST['plugin'], WPSS_PLUGIN_NAME ) ) ) ) {
			self::wp_die();
		}
	}

	function check_wp_config_status() {
		/**
		 *  Check WordPress Config Status
		 *  @since 1.9.7.1
		 *  TO DO:
		 *  - Add checks specific to Multisite when we add full Multisite compatibility (Network Activation)
		 *  - Move to WPSS_Compatibility class
		 */
		if( is_multisite() ) { return FALSE; }		/* Exempt Multisite to prevent issues with domain mapping */
		$site_url	= get_option( 'siteurl' );		/* WordPress Address (URL)	- site_url() - The location where WordPress core files reside. */
		$home_url	= get_option( 'home' );			/* Site Address (URL)		- home_url() - The URL people should use to get to the site. */
		$site_dom	= rs_wpss_get_domain( $site_url );
		$home_dom	= rs_wpss_get_domain( $home_url );
		/* if( !is_multisite() && !empty( $site_dom ) && !empty( $home_dom ) && $site_dom !== $home_dom ) { return __( 'Website domains do not match.', 'wp-spamshield' ); } // USE LATER */
		if( !empty( $site_dom ) && !empty( $home_dom ) && $site_dom !== $home_dom ) { return __( 'Website domains do not match.', 'wp-spamshield' ); } /* Domain Mismatch */
		return FALSE;
	}

	function editor_add_quicktags() {
		 /**
		  *  Add Contact Form Quicktag to WordPress Editor
		  *  @since 1.8.3
		  *  Modified in 1.9.9.6 for WP 4.6+
		  */
		if( rs_wpss_is_admin_sproc( TRUE ) || !current_user_can( 'edit_pages' ) ) { return; }
		global $pagenow; $current_screen = get_current_screen();
		$post_type = !empty( $current_screen->post_type ) ? $current_screen->post_type : get_post_type();
		if( !WP_SpamShield::is_wp_ver('4.6') && ( 'edit' !== $current_screen->parent_base || ( !empty( $post_type ) && 'page' !== $post_type ) ) ) { return; }
		if( ( !empty( $post_type ) && 'page' !== $post_type ) || ( 'edit.php' !== $pagenow && 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) ) { return; } // New Pages Work, but not Existing Pages, and correctly not New Posts
		if( !wp_script_is( 'quicktags' ) ) { wp_enqueue_script( 'quicktags' ); }
		$qtags_hook = WP_SpamShield::is_wp_ver('4.6') ? 'admin_footer-'.$pagenow : 'admin_print_footer_scripts';
		add_action( $qtags_hook, array($this,'print_quicktag_script'), 999 );
	}

	function print_quicktag_script() {
		 /**
		  *  Print Quicktag to Admin Footer
		  *  @since 1.9.9.6
		  */
		$qtags_script = WPSS_EOL.'<script type=\'text/javascript\'>'.WPSS_EOL.'/* <![CDATA[ */'.WPSS_EOL.'QTags.addButton(\'wpss_cf\',\'WPSS '.__( 'Contact Form', 'wp-spamshield' ).'\',\'[spamshieldcontact]\',\'\',\'\',\'WP-SpamShield '.__( 'Contact Form', 'wp-spamshield' ).'\',999);'.WPSS_EOL.'/* ]]> */'.WPSS_EOL.'</script>';
		echo $qtags_script;
	}

	function num_days_inst() {
		$spamshield_options = WP_SpamShield::get_option();
		$current_date	= date( WPSS_DATE_BASIC );
		$install_date	= empty( $spamshield_options['install_date'] ) ? $current_date : $spamshield_options['install_date'];
		$num_days_inst	= rs_wpss_date_diff($install_date, $current_date); if( $num_days_inst < 1 ) { $num_days_inst = 1; }
		return $num_days_inst;
	}

	function spam_blocked_daily() {
		$num_days_inst	= $this->num_days_inst();
		$spam_count		= rs_wpss_count(); if( $spam_count < 1 ) { $spam_count = 1; }
		return round( $spam_count / $num_days_inst );
	}

	function est_hrs_returned( $spam_count ) {
		if( empty( $spam_count ) ) { $spam_count = rs_wpss_count(); }
		return round( $spam_count / WPSS_SPH );
	}

	static public function mail( $to, $subject, $message, $headers = '', $attachments = NULL, $submission_type = '' ) {
		/**
		 *  This is a wrapper for the wp_mail() function and removes *some* data leakage that happens in the PHP mail process.
		 *  PHP-generated emails (especially on shared hosts) can leak data in the headers and potentially reveal sensitive path info, as well as the main username for a website.
		 *  For more secure mail, users should use SMTP so there isn't data leakage as with the PHP-generated emails.
		 *  @since 1.9.5.5
		 */

		/* Obscure */
		$orig_server	= $_SERVER;
		$orig_env		= $_ENV;
		$orig_ip		= $_SERVER['REMOTE_ADDR'];
		$obsc_ip		= WPSS_SERVER_ADDR;
		$orig_script	= $_SERVER['SCRIPT_FILENAME'];
		$orig_docroot	= $_SERVER['DOCUMENT_ROOT'];
		$orig_requri	= $_SERVER['REQUEST_URI'];

		$obscure = array(
			'REMOTE_ADDR' => $obsc_ip, 'HTTP_X_FORWARDED_FOR' => $obsc_ip, 'HTTP_X_FORWARDED' => $obsc_ip, 'HTTP_FORWARDED_FOR' => $obsc_ip, 'HTTP_FORWARDED' => $obsc_ip, 'HTTP_X_REAL_IP' => $obsc_ip, 'DOCUMENT_ROOT' => '/', 'PHP_SELF' => '/', 'REQUEST_URI' => '/', 'SCRIPT_FILENAME' => '/', 'SCRIPT_NAME' => '/', 'REDIRECT_URL' => '/', 'PHPRC' => '/', 'HTTP_REFERER' => '', 
		);
		foreach( $_SERVER as $k => $v )	{
			if( isset( $obscure[$k] ) )	{ $_SERVER[$k] = $obscure[$k]; }
			if( $v === $orig_ip )		{ $_SERVER[$k] = $obsc_ip; }
			if( !is_string( $v ) )		{ continue; }
			if( FALSE !== strpos( $v, $orig_script ) || FALSE !== strpos( $v, $orig_docroot ) || FALSE !== strpos( $v, $orig_requri ) )	{ $_SERVER[$k] = '/'; $_ENV[$k] = '/'; }
		}
		foreach( $_ENV as $k => $v )	{
			if( isset( $obscure[$k] ) )	{ $_ENV[$k] = $obscure[$k]; }
			if( $v === $orig_ip )		{ $_ENV[$k] = $obsc_ip; }
			if( !is_string( $v ) )		{ continue; }
			if( FALSE !== strpos( $v, $orig_script ) || FALSE !== strpos( $v, $orig_docroot ) || FALSE !== strpos( $v, $orig_requri ) )	{ $_ENV[$k] = '/'; $_SERVER[$k] = '/'; }
		}

		if( 'contact' === $submission_type ) {
			/* PHPMailer Options */
			add_action( 'phpmailer_init', array( __CLASS__, 'phpmailer_config' ), 1000 );
		}
		/* Mail */
		$sent = @wp_mail( $to, $subject, $message, $headers, $attachments );
		/* Restore */
		$_SERVER = $orig_server; $_ENV = $orig_env;
		return $sent;
	}

	/**
	 *	Set some PHPMailer options
	 *	@dependencies	WP_SpamShield::get_option()
	 *	@used by		WP_SpamShield::mail()
	 *	@since			...
	 *	@return			void
	 */
	static public function phpmailer_config( &$phpmailer ) {
		if( self::get_option( 'form_mail_encode' ) ) {
			$phpmailer->Encoding	= 'base64'; /* Encode if option enabled - NOTE: Using SMTP plugins may disable/override this feature */
		}
		$phpmailer->Priority	= 1;
		$phpmailer->XMailer		= ' ';	/* Remove X-Mailer header */
		$phpmailer->LE			= "\r\n";
	}

	/**
	 *	Gets new option, and deletes deprecated option if it exists
	 *	This was added to help reduce the number of rows in the options table, help clean up the DB, and optimize speed.
	 *	WP-SpamShield Options API
	 *	@dependencies	none
	 *	@used by		...
	 *	@since			1.9.5.5
	 *	@return			void
	 */
	static public function deprecated_options_check( $activation = FALSE ) {
		global $spamshield_options,$wpss_deprecated_options_checked;
		if( !empty( $wpss_deprecated_options_checked ) ) { return; }
		if( empty( $spamshield_options ) ) {
			$spamshield_options = get_option( 'spamshield_options' );
		}
		$wpss_dep_options			= array(
			/* 'new_index'			=> 'original_option', */
			'ip_ban_disable'		=> 'spamshield_ip_ban_disable',
			'ubl_cache'				=> 'spamshield_ubl_cache',
			'ubl_cache_disable'		=> 'spamshield_ubl_cache_disable',
			'wpss_version'			=> 'wp_spamshield_version',
			/*
			'install_status'		=> 'spamshield_install_status',
			'warning_status'		=> 'spamshield_warning_status',
			'regalert_status'		=> 'spamshield_regalert_status',
			'last_admin'			=> 'spamshield_last_admin',
			'wpss_admins'			=> 'spamshield_admins',
			'reg_count'				=> 'spamshield_reg_count',
			'spam_count'			=> 'spamshield_count',
			'wpssmid_cache'			=> 'spamshield_wpssmid_cache',
			'wpss_procdat'			=> 'spamshield_procdat',
			*/
			);
		$updated = FALSE;
		foreach( $wpss_dep_options as $new => $original ) {
			if( !isset( $spamshield_options[$new] ) ) {
				/* Check for deprecated */
				$depr_option = get_option( $original );
				if( !empty( $depr_option ) ) {
					$spamshield_options[$new] = $depr_option;
					delete_option( $original );
					$updated = TRUE;
				}
			}
		}
		if( FALSE === $activation && TRUE === $updated ) { self::update_option( $spamshield_options ); }
		$wpss_deprecated_options_checked = TRUE;
	}

	/**
	 *  Differs from update_option() in that it only takes an associative array with option name/value pair(s) and can update multiple options at once.
	 *  @dependencies	none
	 *  @used by		...
	 *  @since			1.9.5.5
	 */
	static public function get_option( $option = 'all' ) {
		global $spamshield_options;
		if( empty( $spamshield_options ) ) {
			$spamshield_options = get_option( 'spamshield_options' );
			rs_wpss_update_session_data( $spamshield_options );
		}
		if( 'all' === $option ) {
			return $spamshield_options;
		}
		if( !isset( $spamshield_options[$option] ) ) {
			$options_default = unserialize( WPSS_OPTIONS_DEFAULT );
			if( isset( $options_default[$option] ) ) {
				$spamshield_options[$option] = $options_default[$option];
				update_option( 'spamshield_options', $spamshield_options );
			}
		}
		return ( isset( $spamshield_options[$option] ) ) ? $spamshield_options[$option] : '';
	}

	/**
	 *  Differs from update_option() in that it only takes an associative array with option name/value pair(s) and can update multiple options at once.
	 *  @dependencies	none
	 *  @used by		...
	 *  @since			1.9.5.5
	 */
	static public function update_option( $arr, $update = TRUE ) {
		global $spamshield_options;
		if( empty( $spamshield_options ) ) {
			$spamshield_options = get_option( 'spamshield_options' );
		}
		foreach ( $arr as $option => $value ) { $spamshield_options[$option] = $value; }
		/* TO DO: Add ... */
		if( TRUE === $update ) { update_option( 'spamshield_options', $spamshield_options ); }
	}

	/**
	 *  Differs from delete_option() in that it only takes a numeric array with option name(s) and can delete multiple options at once.
	 *  @dependencies	none
	 *  @used by		...
	 *  @since			1.9.9.4
	 */
	static public function delete_option( $arr, $update = TRUE ) {
		global $spamshield_options;
		if( empty( $spamshield_options ) ) {
			$spamshield_options = get_option( 'spamshield_options' );
		}
		foreach ( $arr as $i => $option ) { unset ( $spamshield_options[$option] ); }
		/* TO DO: Add ... */
		if( TRUE === $update ) { update_option( 'spamshield_options', $spamshield_options ); }
	}

	/**
	 *	Wrapper for wp_die() with better formatting.
	 *	@dependencies	...
	 *	@used by		...
	 *	@since			...
	 */
	static public function wp_die( $error_msg = NULL, $cust_error_txt = FALSE, $status_code = '403' ) {
		remove_filter( 'wpss_filter_404', 'rs_wpss_mod_status_header', 100 );
		$error_txt	= '<strong>'.rs_wpss_error_txt().':</strong> ';
		$error_txt	= ( !empty( $cust_error_txt ) && !empty( $error_msg ) ) ? '' : $error_txt;
		$error_msg	= empty( $error_msg ) ? __( 'Sorry, you are not allowed to access this page.' ) : $error_msg;
		$error_str	= $error_txt.$error_msg.WPSS_EOL;
		$args		= array( 'response' => $status_code );
		wp_die( $error_str, '', $args );
	}

	/**
	 *	Check if current page is WP-SpamShield Support URL
	 *	@dependencies	rs_wpss_fix_url()
	 *	@since			1.9.9.8.8
	 */
	static public function is_support_url() {
		/**
		 *	Remove query string from URL
		 */
		return ( WPSS_SUPPORT_URL === rs_wpss_fix_url( WPSS_THIS_URL, FALSE, TRUE ) );
	}

	/**
	 *	Check if current request is Customizer Preview
	 *	@dependencies	WP_SpamShield::is_wp_ver()
	 *	@since			1.9.9.9.4
	 */
	static public function is_customize_preview() {
		return ( self::is_wp_ver( '4.0' ) && is_customize_preview() );
	}

	/**
	 *	Check if current request is a 404
	 *	@dependencies	WP_SpamShield::get_http_status()
	 *	@since			... as rs_wpss_is_404()
	 *	@moved			1.9.9.9.4 to WP_SpamShield class
	 */
	static public function is_404( $get_header = FALSE ) {
		if( TRUE === $get_header ) {
			return ( 404 == self::get_http_status( WPSS_THIS_URL ) );
		} else {
			return ( !empty( $GLOBALS['wp_query'] ) && is_404() );
		}
	}

	/**
	 *	Check if current request is /wp-admin/admin-post.php
	 *	@dependencies	none
	 *	@since			1.9.9.9.4
	 */
	static public function is_admin_post() {
		return ( is_admin() && 'admin-post.php' === $GLOBALS['pagenow'] );
	}

	/**
	 *	Check if current request is an XML POST request
	 *	@dependencies	...
	 *	@since			1.9.12
	 */
	static public function is_xml_request() {
		if( self::constant( 'XML_POST_REQUEST' ) ) { return TRUE; }
		if( 'POST' !== WPSS_REQUEST_METHOD ) { return FALSE; }
		global $HTTP_RAW_POST_DATA; if( !isset( $HTTP_RAW_POST_DATA ) ) { $HTTP_RAW_POST_DATA = WPSS_Security::get_raw_post_data(); }
		$HRPD			= trim( stripslashes( $HTTP_RAW_POST_DATA ) );
		$content_type	= ( !empty( $_SERVER['CONTENT_TYPE'] ) ) ? trim( WPSS_Func::lower( $_SERVER['CONTENT_TYPE'] ) ) : '';
		$xml_prolog_rgx = "<\?xml\s+version\=['\"]1\.0['\"](?:\s+encoding\=['\"](utf\-(8|16|32)|iso\-8859\-[1-9]|windows\-125[0-7])['\"])?\s*\?>";
		if( 0 === strpos( $content_type, 'text/xml' ) || self::preg_match( "~".$xml_prolog_rgx."~isU", $HRPD ) ) {
			self::define( array( 'XML_POST_REQUEST' => TRUE ), TRUE );
		}
		return FALSE;
	}

	/**
	 * Get WordPress Database Size
	 * @since 1.9.9.3
	 * @return string
	 */
	static public function get_db_size() {
		global $wpdb; $db_size = 0;
		$query = $wpdb->get_results( "SHOW table STATUS", ARRAY_A );
		foreach( $query as $row ) {
			$db_size += $row['Data_length'] + $row['Index_length'];
		}
		return $db_size;
	}

	/* Class Admin Functions - END */


	/* Other Functions - BEGIN */

	function update_admin_status( $user_id = NULL, $admin_ips = NULL ) {
		if( empty( $user_id ) ){ global $current_user; $current_user = wp_get_current_user(); $user_id = $current_user->ID; }
		if( empty( $user_id ) ){ return; }
		$admin_status = rs_wpss_is_user_admin() ? TRUE : FALSE;
		update_user_meta( $user_id, 'wpss_admin_status', $admin_status );
		$add_admin_ip = !empty( $admin_status ) ? TRUE : FALSE;
		rs_wpss_update_user_ip( $user_id, $add_admin_ip, $admin_ips );
		rs_wpss_remove_expired_admins( $admin_ips );
	}

	/**
	 *  Automatically purge multiple types of caches
	 *  @dependencies	...
	 *  @used by		WP_SpamShield::upgrade_check()
	 *  @since			1.9.6.2
	 *  @modified		1.9.7.5, 1.9.12
	 */
	protected function purge_cache() {
		global $wpss_purge_cache_complete,$wpss_cache_check,$wp_fastest_cache;
		$auto_cache_purge = self::get_option( 'auto_purge_cache' );
		if( empty( $auto_cache_purge ) || !empty( $wpss_purge_cache_complete ) ) { return FALSE; }

		/* Flush WordPress Object Cache */
		@wp_cache_flush();

		/* Check if cache plugins or server-side caching active */
		if( empty( $wpss_cache_check ) ) { $wpss_cache_check = rs_wpss_check_cache_status(); }
		if( $wpss_cache_check['cache_check_status'] === 'ACTIVE' ) {

			/* WP Super Cache */
			if( WPSS_Compatibility::is_plugin_active( 'wp-super-cache/wp-cache.php' ) && function_exists( 'wp_cache_clear_cache' ) ) {
				@wp_cache_clear_cache();
			}
			/* WP Fastest Cache */
			if( ( WPSS_Compatibility::is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ) || WPSS_Compatibility::is_plugin_active( 'wp-fastest-cache-premium/wpFastestCachePremium.php' ) ) && !empty( $wp_fastest_cache ) && method_exists( $wp_fastest_cache, 'deleteCache' ) ) {
				$wp_fastest_cache->deleteCache( TRUE );
			}
			/* Autoptimize */
			if( WPSS_Compatibility::is_plugin_active( 'autoptimize' ) && method_exists( 'autoptimizeCache', 'clearall' ) ) {
				@autoptimizeCache::clearall();
			}

			/* WP Engine Hosting */
			$web_host = WPSS_Utils::get_web_host( WPSS_Utils::get_ip_dns_params() );
			if( !empty( $web_host ) && 'WP Engine' === $web_host ) {
				if( method_exists( 'WpeCommon', 'purge_memcached' ) ) {
					@WpeCommon::purge_memcached();
				}
				if( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) ) {
					@WpeCommon::clear_maxcdn_cache();
				}
				if( method_exists( 'WpeCommon', 'purge_varnish_cache' ) ) {
					@WpeCommon::purge_varnish_cache();
				}
			}

			/* W3 Total Cache // No longer recommended, but included for legacy support */
			if( WPSS_Compatibility::is_plugin_active( 'w3-total-cache' ) && function_exists( 'w3tc_pgcache_flush' ) ) {
				@w3tc_pgcache_flush();
			}

			/* Add other Cache & Minification Plugins... */

		}
		rs_wpss_clear_ubl_cache();
		rs_wpss_purge_nonces();
		self::deprecated_options_check();
		if( TRUE === WPSS_IP_BAN_CLEAR ) { WPSS_Security::clear_ip_ban(); }
		$wpss_purge_cache_complete = TRUE;
	}

	/* Other Functions - END */



	/* Output Functions - BEGIN */

	function insert_head_js() {
		/**
		 *  This JavaScript is purposely NOT enqueued. It's not coded "improperly". This is done exactly like it is for a very good reason.
		 *  It needs to NOT be modified by any other plugin.
		 *  The JS file is really a dynamically generated hybrid script that uses both server-side and client-side code so it requires the PHP functionality.
		 *  "But couldn't that be done by..." Stop right there...No, it cannot.
		 *  @dependencies	rs_wpss_is_user_logged_in(), rs_wpss_is_admin_sproc(), WPSS_Compatibility::is_plugin_active(), rs_wpss_is_session_active(), WP_SpamShield::get_ip_addr()
		 *  @since			1.0.0
		 */
		if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { return; }
		if( empty( $GLOBALS['wpss_output_head_js'] ) && ( ( !is_admin() && rs_wpss_is_user_logged_in() ) || !rs_wpss_is_user_logged_in() ) && !rs_wpss_is_admin_sproc() ) {
			echo WPSS_EOL;
			global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
			$js_url = WPSS_PLUGIN_JS_URL.'/jscripts.php';
			if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize' ); }
			if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; }
			echo $ao_noop_open."<script type='text/javascript' src='". $js_url ."'></script>".$ao_noop_close." ".WPSS_EOL;
			if( rs_wpss_is_session_active() ) {
				if( !empty( $_SESSION['wpss_user_ip_init_'.WPSS_HASH] ) ) { $_SESSION['wpss_user_ip_init_'.WPSS_HASH] = WP_SpamShield::get_ip_addr(); }
			}
			$GLOBALS['wpss_output_head_js'] = TRUE;
		}
	}

	/**
	 *  Insert WP-SpamShield JS into footer. This adds essential hidden fields to the relevant forms via jQuery. (REF2XJS and FVFJS)
	 *  @dependencies	rs_wpss_is_user_logged_in(), rs_wpss_is_admin_sproc(), rs_wpss_get_key_values(), WP_SpamShield::get_option(), rs_wpss_comments_open(), rs_wpss_is_3p_register_page(), WPSS_Compatibility::is_plugin_active(), WPSS_Compatibility::footer_js()
	 *  @since			1.8.9.9
	 */
	static public function insert_footer_js( $ret = FALSE ) {
		$js = '';
		if( empty( $GLOBALS['wpss_output_footer_js'] ) && ( ( !is_admin() && rs_wpss_is_user_logged_in() ) || !rs_wpss_is_user_logged_in() ) && !rs_wpss_is_admin_sproc()  ) {
			/* REF2XJS and FVFJS code */
			$wpss_key_values 	= rs_wpss_get_key_values();
			$wpss_js_key 		= $wpss_key_values['wpss_js_key'];
			$wpss_js_val 		= $wpss_key_values['wpss_js_val'];
			$comment_min_length = self::get_option('comment_min_length');
			$comment_min_length = ( !empty( $comment_min_length ) && is_int( $comment_min_length ) ) ? $comment_min_length : '15';
			$cm_var = $cm_str = $bp_str = '';
			if( rs_wpss_comments_open() ) {
				$cm_var			= 'cm4S="form[action=\''.WPSS_COMMENTS_POST_URL.'\']";'.WPSS_EOL;
				$cm_str			= ', "+cm4S+"';
			}
			if( class_exists( 'BuddyPress' ) ) {
				$bp_single		= rs_wpss_is_3p_register_page() ? ', #signup_form' : '';
				$bp_str			= ', #buddypress #signup_form, #buddypress #register-page #signup_form, .buddypress #signup_form'.$bp_single;
			}
			$cf7_str			= defined( 'WPCF7_VERSION' ) ? ', .wpcf7-form' : '';
			$gf_str				= class_exists( 'GFForms' ) ? ', .gform_wrapper form' : '';
			$tpr_str			= rs_wpss_is_3p_register_page() ? ', .login-form.register-form' : '';
			$js = WPSS_EOL;
			global $wpss_ao_active; $ao_noop_open = $ao_noop_close = '';
			if( empty( $wpss_ao_active ) ) { $wpss_ao_active = WPSS_Compatibility::is_plugin_active( 'autoptimize' ); }
			if( !empty( $wpss_ao_active ) ) { $ao_noop_open = '<!--noptimize-->'; $ao_noop_close = '<!--/noptimize-->'; } /* Add noptimize tags if Autoptimize is active */
			$js .= $ao_noop_open.'<script type=\'text/javascript\'>'.WPSS_EOL.'/* <![CDATA[ */'.WPSS_EOL.WPSS_REF2XJS.'=escape(document[\'referrer\']);'.WPSS_EOL.'hf4N=\''.$wpss_js_key.'\';'.WPSS_EOL.'hf4V=\''.$wpss_js_val.'\';'.WPSS_EOL.$cm_var.'jQuery(document).ready(function($){'.'var e="#commentform, .comment-respond form, .comment-form'.$cm_str.', #lostpasswordform, #registerform, #loginform, #login_form'.$tpr_str.', #wpss_contact_form'.$cf7_str.$gf_str.$bp_str;
			$js .= WPSS_Compatibility::footer_js();
			$js .= '";$(e).submit(function(){$("<input>").attr("type","hidden").attr("name","'.WPSS_REF2XJS.'").attr("value",'.WPSS_REF2XJS.').appendTo(e);';
			if( FALSE === WPSS_COMPAT_MODE && !defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { $js .= '$("<input>").attr("type","hidden").attr("name",hf4N).attr("value",hf4V).appendTo(e);'; }
			$js .= 'return true;});';
			if( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) ) { $js .= 'var h="form[method=\'post\']";$(h).submit(function(){$("<input>").attr("type","hidden").attr("name",hf4N).attr("value",hf4V).appendTo(h);return true;});'; }
			else { $js .= '$("#comment").attr({minlength:"'.$comment_min_length.'",maxlength:"15360"})'; }
			$js .= '});'.WPSS_EOL.'/* ]]> */'.WPSS_EOL.'</script>'.$ao_noop_close." ".WPSS_EOL;
			$GLOBALS['wpss_output_footer_js'] = TRUE;
		}
		if( TRUE === $ret ) { return $js; } else { echo $js; }
	}

	function enqueue_scripts() {
		if( ( ( !is_admin() && rs_wpss_is_user_logged_in() ) || !rs_wpss_is_user_logged_in() ) && !rs_wpss_is_admin_sproc() ) {
			$js_file = ( TRUE === WPSS_COMPAT_MODE || defined( 'WPSS_SOFT_COMPAT_MODE' ) || current_user_can( 'moderate_comments' ) ) ? 'jscripts-ftr2-min.js' : 'jscripts-ftr-min.js';
			$js_handle = 'wpss-jscripts-ftr'; $js_url = WPSS_PLUGIN_JS_URL.'/'.$js_file; $js_ver = NULL; /* '1.0', WPSS_VERSION */
			wp_register_script( $js_handle, $js_url, array( 'jquery' ), $js_ver, TRUE );
			wp_enqueue_script( $js_handle );
		}
		/**
		 *	if( is_admin() && !rs_wpss_is_admin_sproc() ) {
		 *		// Nothing yet //
		 *	}
		 */
	}

	/* Output Functions - END */

}

/**
 *	WP_SpamShield CLASS - END
 */



class WPSS_Filters extends WP_SpamShield {

	/**
	 *	WP-SpamShield Filters Class
	 *	@since 1.9.9.8.2
	 */

	/* Initialize Class Variables */
	static private		$bl_anchortxt		= array();
	static private		$bl_domain			= array();
	static private		$bl_tld				= array();
	static private		$bl_host_ns			= array();
	static private		$bl_email			= array();
	static private		$bl_anchortxt_lite	= array();
	static private		$bl_spam_domain		= array();
	static private		$bl_sm_domain		= array();
	static private		$bl_sm_ext_domain	= array();
	static private		$bl_urlshort		= array();
	static private		$bl_referrer		= array();
	static private		$bl_cf_content		= array();
	static private		$bl_iapdat			= array();

	/**
	 *	Constructor
	 *	@dependencies	...
	 *	@since			...
	 */
	function __construct() {
		/**
		 *	Do nothing...for now
		 */
	}

	/**
	 *	Author Keyword Blacklist Check
	 *	Use for testing Comment Author, Contact Form, New User Registrations, and anywhere else you need to test an author name.
	 *	This list assembled based on statistical analysis of common anchor text spam keyphrases.
	 *	Script creates all the necessary alphanumeric and linguistic variations to effectively test.
	 *	$haystack_type can be 'author' (default), 'content', ('user_login'|'first_name'|'last_name'|'display_name') (sub-type of 'author', since 1.9.9.9.9)
	 *	@dependencies	WPSS_Compatibility::is_plugin_active(), WPSS_Filters::get_blacklist_data(), WPSS_BL::anchortxt(), WPSS_BL::anchortxt_lite(), rs_wpss_get_domain(), WPSS_Filters::get_rgx_ptrn(), rs_wpss_count_words(), rs_wpss_regexify(), rs_wpss_parse_links(), 
	 *	@used by		rs_wpss_contact_form(), rs_wpss_comment_content_filter(), rs_wpss_check_new_user(), 
	 *	@since			... as rs_wpss_anchortxt_blacklist_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function anchortxt_blacklist_chk( $haystack = NULL, $get_list_arr = FALSE, $haystack_type = 'author', $url = NULL ) {
		$spamshield_options	= WP_SpamShield::get_option();
		$wpss_cak_active = ( !empty( $spamshield_options['allow_comment_author_keywords'] ) ) ? 1 : 0;	/* Check if Comment Author Name Keywords are allowed (Equivalent to old CommentLuv plugin being active ) */
		$blacklisted_keyphrases = rs_wpss_rbkmd( WPSS_BL::anchortxt(), 'de', TRUE );
		$blacklisted_keyphrases_lite = rs_wpss_rbkmd( WPSS_BL::anchortxt_lite(), 'de', TRUE );
		if( $haystack_type === 'author' && ( !empty( $wpss_cak_active ) || empty( $url ) ) ) {
			$blacklisted_keyphrases = $blacklisted_keyphrases_lite;
		}
		if( !empty( $get_list_arr ) ) {
			if( $haystack_type === 'content' ) { return $blacklisted_keyphrases_lite; } else { return $blacklisted_keyphrases; }
		}
		/* Goes after array */
		if( empty( $haystack ) ) { return FALSE; }
		$blacklist_status = FALSE;
		$haystack_type = ( FALSE !== strpos( $haystack_type, '_name' ) ) ? 'author' : $haystack_type;
		if( $haystack_type === 'author' || $haystack_type === 'user_login' ) {
			$haystack = trim( $haystack );
			if( !empty( $wpss_cak_active ) ) {
				$blacklisted_keyphrases = $blacklisted_keyphrases_lite;
			}
			/* Check 0: Test Registration Usernames - Since 1.9.9.9.9 */
			if( $haystack_type === 'user_login' ) {
				$user_login_is_email = ( is_email( $haystack ) );
				if( TRUE === $user_login_is_email && self::email_blacklist_chk( $haystack ) ) { return TRUE; }
				$haystack = preg_replace( "~([^a-z0-9])~i", ' ', $haystack );
			}
			/* Check 1: Testing for URLs and author domain in author name */
			if( $haystack_type === 'author' && parent::preg_match( "~^https?~i", $haystack ) ) { return TRUE; }
			if( !empty( $url ) ) {
				$author_email_domain = rs_wpss_get_domain( $url, TRUE );
				$author_email_domain_rgx = self::get_rgx_ptrn( $author_email_domain, '', 'N' );
				if( parent::preg_match( $author_email_domain_rgx, $haystack ) ) { return TRUE; }
			}
			/* Check 2: Testing for max # words in author name, more than 7 (or 10) is fail */
			$author_words = rs_wpss_count_words( $haystack );
			$word_max = 7; /* Default */
			if( !empty( $user_login_is_email ) || !empty( $wpss_cak_active ) ) { $word_max = 10; } /* User login is email, or CAK active */
			if( $author_words > $word_max ) { return TRUE; }
			/* Check 3: Testing for Odd Characters in author name */
			$odd_char_rgx = "~[\@\*]+~"; /* Default */
			if( !empty( $wpss_cak_active ) ) { $odd_char_rgx = "~(\@{2,}|\*)+~"; } /* CAK active */
			if( parent::preg_match( $odd_char_rgx, $haystack ) ) { return TRUE; }
			/**
			 *  Check 4: Testing for *author name* surrounded by asterisks
			 *  Check 5: Testing for numbers and cash references ('1000','$5000', etc) in author name
			 */
			if( empty( $wpss_cak_active ) && parent::preg_match( "~(^|[\s\.]+|\b\s+\b)(\$([0-9]+)([0-9,\.]+)?|([0-9]+)([0-9,\.]{3,})|([0-9]{3,}))([\s]+|\b\s+\b|$)~", $haystack ) ) { return TRUE; }
			/* Final Check: The Blacklist - This is where Magic Parsing function comes from */
			$haystack = preg_replace( "~([a-z0-9])(?:[_;'\.\:\-\+])([a-z0-9])~i", "$1 $2", $haystack );
			foreach( $blacklisted_keyphrases as $i => $blacklisted_keyphrase ) {
				$blacklisted_keyphrase_rgx = rs_wpss_regexify( $blacklisted_keyphrase );
				$regex_check_phrase = self::get_rgx_ptrn( $blacklisted_keyphrase_rgx, '', 'authorkw' );
				if( parent::preg_match( $regex_check_phrase, $haystack ) ) { return TRUE; }
			}
		} elseif( $haystack_type === 'content' ) {
			/**
			 *  Parse content for links with Anchor Text
			 *  Test 1: Coming Soon
			 *  For possible use later - from old filter: ((payday|students?|title|onli?ne|short([\s\.\-_]*)term)([\s\.\-_]*)loan|cash([\s\.\-_]*)advance)
			 *  Final Check: The Blacklist
			 */
			$anchor_text_phrases = rs_wpss_parse_links( $haystack, 'anchor_text' );
			foreach( $anchor_text_phrases as $a => $anchor_text_phrase ) {
				foreach( $blacklisted_keyphrases_lite as $i => $blacklisted_keyphrase ) {
					$blacklisted_keyphrase_rgx = rs_wpss_regexify( $blacklisted_keyphrase );
					$regex_check_phrase = self::get_rgx_ptrn( $blacklisted_keyphrase_rgx, '', 'authorkw' );
					if( parent::preg_match( $regex_check_phrase, $anchor_text_phrase ) ) { return TRUE; }
				}
			}
		}
		return $blacklist_status;
	}

	/**
	 *  Anchor Text Link Spam URL Check
	 *  Check Anchor Text Links in comment content for links to common spam URLs
	 *  $urls - an array of URLs parsed from anchor text links
	 *  If $urls is string, will convert to array
	 *  @dependencies	WPSS_Filters::urlshort_blacklist_chk(), WPSS_Filters::long_url_chk(), WPSS_Filters::social_media_url_chk(), WPSS_Filters::misc_spam_url_chk(), 
	 *  @used by		rs_wpss_contact_form(), rs_wpss_pingback_pre_filter(), rs_wpss_trackback_content_filter(), rs_wpss_comment_content_filter(), 
	 *  @since			... as rs_wpss_at_link_spam_url_chk()
	 *  @modified		1.5 Added @param $comment_type for trackbacks
	 *  @moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function at_link_spam_url_chk( $urls = NULL, $comment_type = NULL ) {
		$blacklist_status = FALSE;
		if( empty( $urls ) ) { return FALSE; }
		if( is_string( $urls ) ) {
			$urls_arr 	= array();
			$urls_arr[]	= $urls;
			$urls 		= $urls_arr;
		}
		foreach( $urls as $u => $url ) {
			if( self::urlshort_blacklist_chk( $url, '', $comment_type ) || self::long_url_chk( $url ) || self::social_media_url_chk( $url, $comment_type ) || self::misc_spam_url_chk( $url ) ) {
				/* Shortened URLs, Long URLs, Social Media, Other common spam URLs */
				return TRUE;
			}
		}
		return $blacklist_status;
	}

	/**
	 *  Detect if website visitor is a bad robot.
	 *	@dependencies	WP_SpamShield::casetrans(), rs_wpss_get_user_agent(), rs_wpss_count_words(), rs_wpss_get_http_accept(), rs_wpss_is_xmlrpc(), rs_wpss_is_doing_rest(), rs_wpss_is_doing_cron(), rs_wpss_is_doing_ajax(), rs_wpss_is_ajax_request(), rs_wpss_is_local_request(), WPSS_Filters::skiddie_ua_check(), WPSS_Filters::revdns_filter(), 
	 *	@used by		rs_wpss_contact_form(), rs_wpss_misc_form_spam_check(), WPSS_Security::early_post_intercept(), 
	 *  @since			... as rs_wpss_bad_robot_blacklist_chk()
	 *  @moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function bad_robot_blacklist_chk( $type = 'comment', $status = NULL, $ip = NULL, $rev_dns = NULL, $author = NULL, $email = NULL ) {
		$wpss_error_code			= $ns_val = '';
		$blacklisted				= FALSE;
		/* IP / PROXY INFO - BEGIN */
		global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info );
		/* IP / PROXY INFO - END */
		$rev_dns					= WPSS_Func::lower( trim( $rev_dns ) );
		$user_agent_lc				= rs_wpss_get_user_agent( TRUE, TRUE );
		$user_agent_word_count		= rs_wpss_count_words( $user_agent_lc );
		$http_accept				= rs_wpss_get_http_accept( TRUE, TRUE );
		$http_accept_language		= rs_wpss_get_http_accept( TRUE, TRUE, TRUE );
		$http_accept_encoding		= rs_wpss_get_http_accept( TRUE, TRUE, FALSE, TRUE );
		switch ( $type ) {
			/* TO DO: Add types for EPC, PWR, and XML-RPC */
			case 'register':
				$pref = 'R-'; $ns_val = 'NS3'; break;
			case 'contact':
				$pref = 'CF-'; $ns_val = 'NS2'; break;
			case 'trackback':
				$pref = ''; $linkback = TRUE; break;
			case 'pingback':
				$pref = ''; $linkback = TRUE; break;
			case 'misc form':
				$pref = 'MSC-'; break;
			case 'early post check':
				$pref = 'EPC-'; break;
			case 'password reset':
				$pref = 'PWR-'; break;
			case 'contact form 7':
				$pref = 'CF7-'; break;
			case 'gravity forms':
				$pref = 'GF-'; break;
			case 'jetpack form':
				$pref = 'JP-'; break;
			case 'ninja forms':
				$pref = 'NF-'; break;
			case 'mailchimp form':
				$pref = 'MCF-'; break;
			case 'guestbook form':
				$pref = 'GBK-'; break;
			default:
				$pref = ''; $ns_val = 'NS1';
		}

		if( !empty( $_SERVER['WPSS_SEC_THREAT'] ) || !empty( $_SERVER['BHCS_SEC_THREAT'] ) ) {
			$wpss_error_code .= ' '.$pref.'HTA1000'; $blacklisted = TRUE; $status = '3';
			$bad_robot_filter_data = array( 'status' => $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
			return $bad_robot_filter_data;
		}

		/**
		 *  REF2XJS
		 *  This case only happens if bots scrape/spam the form. Nice try guys.
		 */
		$post_ref2xjs 		= !empty( $_POST[WPSS_REF2XJS] ) ? trim( $_POST[WPSS_REF2XJS] ) : '';
		$post_ref2xjs_lc	= WPSS_Func::lower( $post_ref2xjs );
		$ref2xjs_lc			= WPSS_Func::lower( WPSS_REF2XJS );
		if( !empty( $post_ref2xjs ) && strpos( $post_ref2xjs_lc, $ref2xjs_lc ) !== FALSE ) {
			$wpss_error_code .= ' '.$pref.'REF-2-1023'; $blacklisted = TRUE;
		}

		if( 'trackback' !== $type && 'pingback' !== $type && !rs_wpss_is_xmlrpc() && !rs_wpss_is_doing_rest() && !rs_wpss_is_doing_cron() && !rs_wpss_is_doing_ajax() && !rs_wpss_is_ajax_request() && !rs_wpss_is_local_request() ) {

			/* HA / HAL / HAE - Robots */
			if( empty( $http_accept ) ) {
				$wpss_error_code .= ' '.$pref.'HA1001'; $blacklisted = TRUE;
			}
			/* HA1002 - REMOVED 1.9.0.3 */
			if( $http_accept === '*' ) {
				$wpss_error_code .= ' '.$pref.'HA1003'; $blacklisted = TRUE;
			}
			if( !empty( $_SERVER['WPSS_HA1004'] ) || $http_accept === '*/*' ) { /* && !rs_wpss_is_ajax_request() */
				$wpss_error_code .= ' '.$pref.'HA1004'; $blacklisted = TRUE;
			}
			$http_accept_mod_1 = preg_replace( "~([\s\;]+)~", ",", $http_accept );
			$http_accept_elements = explode( ',', $http_accept_mod_1 );
			$http_accept_elements_count = count($http_accept_elements);
			$i = 0;
			/* The following line to prevent exploitation: */
			$i_max = 20;
			while( $i < $http_accept_elements_count && $i < $i_max ) {
				if( !empty( $http_accept_elements[$i] ) ) {
					if( $http_accept_elements[$i] === '*' ) {
						$wpss_error_code .= ' '.$pref.'HA1010'; $blacklisted = TRUE; break;
					}
				}
				++$i;
			}
			if( empty( $http_accept_language ) ) {
				$wpss_error_code .= ' '.$pref.'HAL1001'; $blacklisted = TRUE;
			}
			if( $http_accept_language === '*' ) {
				$wpss_error_code .= ' '.$pref.'HAL1002'; $blacklisted = TRUE;
			}
			$http_accept_language_mod_1 = preg_replace( "~([\s\;]+)~", ",", $http_accept_language );
			$http_accept_language_elements = explode( ',', $http_accept_language_mod_1 );
			$http_accept_language_elements_count = count($http_accept_language_elements);
			$i = 0;
			/* The following line to prevent exploitation: */
			$i_max = 20;
			while( $i < $http_accept_language_elements_count && $i < $i_max ) {
				if( !empty( $http_accept_language_elements[$i] ) ) {
					if( $http_accept_language_elements[$i] === '*' && strpos( $user_agent_lc, 'links (' ) !== 0 ) {
						$wpss_error_code .= ' '.$pref.'HAL1010'; $blacklisted = TRUE; break;
					}
				}
				++$i;
			}
			/* HAL1005 - NOT IMPLEMENTED */
		}

		if( $http_accept_language === 'en-US,*' && $http_accept_encoding === 'gzip' ) { /* PhantomJS Sig */
			$wpss_error_code .= ' '.$pref.'HALE1001'; $blacklisted = TRUE;
		}

		/**
		 *  USER-AGENT
		 *  Add Blacklisted User-Agent Function - Note 1.4
		 */

		/* UA1001 - REMOVED 1.9.8.7 - WPSS_Filters::skiddie_ua_check() now includes this */

		if( self::skiddie_ua_check( $user_agent_lc, $type ) ) {
			$wpss_error_code .= ' '.$pref.'UA1004'; $blacklisted = TRUE;
		}

		/* BXR1060 - TEMP DISABLED 1.9.13 */

		$human_form = ( !empty( $pref ) && 'MSC-' !== $pref && 'EPC-' !== $pref && !rs_wpss_is_xmlrpc() && !rs_wpss_is_doing_rest() && !rs_wpss_is_doing_cron() && !rs_wpss_is_installing() && !rs_wpss_is_cli() && !rs_wpss_is_local_request() ); /* Forms that *unmistakably* require human interaction to submit. */
		if( TRUE === $human_form && 'PWR-' !== $pref && rs_wpss_invalid_browser_footprint() ) { /* TO DO: Next version -- remove "'PWR-' !== $pref" */
			$wpss_error_code .= ' '.$pref.'IBF1010'; $blacklisted = TRUE;
		}

		if( !rs_wpss_is_local_request() && !rs_wpss_is_doing_rest() ) {
			/* REVDNS */
			$rev_dns_filter_data 	 = self::revdns_filter( $type, $status, $ip, $rev_dns, $author, $email );
			$revdns_blacklisted 	 = $rev_dns_filter_data['blacklisted'];
			if( !empty( $revdns_blacklisted ) ) {
				$wpss_error_code 	.= $rev_dns_filter_data['error_code']; $blacklisted = TRUE;
			}
		}

		if( !empty( $blacklisted ) ) { $status = '3'; $_SERVER['WPSS_SEC_THREAT'] = TRUE; } /* Was 2, changed to 3 - V1.8.4 */
		$bad_robot_filter_data = array( 'status' => $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		return $bad_robot_filter_data;
	}

	/**
	 *	Split camel case into words
	 *	@dependencies	...
	 *	@since			1.9.9.9.4
	 */
	static public function camel_space( $str, $glue = ' ' ) {
		if( FALSE !== strpos( $str, $glue ) ) { return $str; }
		$parts = preg_split( "~((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))~", $str );
		return implode( $glue, $parts );
	}

	/**
	 *	Contact Form Content Blacklist Check
	 *	Use for the message content of any contact form
	 *	@dependencies	WPSS_Filters::get_blacklist_data(), WPSS_BL::cf_content(), WPSS_Filters::get_rgx_ptrn(), 
	 *	@used by		rs_wpss_contact_form(), rs_wpss_misc_form_spam_check(), rs_wpss_cf7_spam_check(), rs_wpss_gf_spam_check(), 
	 *	@since			... as rs_wpss_cf_content_blacklist_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function cf_content_blacklist_chk( $haystack = NULL, $get_list_arr = FALSE ) {
		$blacklisted_content = rs_wpss_rbkmd( WPSS_BL::cf_content(), 'de', TRUE );
		if( !empty( $get_list_arr ) ) { return $blacklisted_content; }
		/* Goes after array */
		$blacklist_status = FALSE;
		if( empty( $haystack ) ) { return FALSE; }
		if( self::lang_mismatch( $haystack ) || self::lang_mismatch_pct( $haystack ) ) { $blacklist_status = TRUE; return TRUE; }
		$blacklisted_content_rgx = self::get_rgx_ptrn( $blacklisted_content, '', 'red_str' );
		$blacklisted_content_rgx = str_replace( array( 'email', 'disclaimer', '2007', '\s+the\s+', '\s+an\s+', '\s+a\s+', ',', ), array( 'e?mail', '(disclaimer|p\.?s\.?)', '20[0-9]{2}', '\s+(the\s+)?', '\s+(an?\s+)?', '\s+(an?\s+)?', ',?', ), $blacklisted_content_rgx );
		if( parent::preg_match( $blacklisted_content_rgx, $haystack ) ) { $blacklist_status = TRUE; }
		return $blacklist_status;
	}

	/**
	 *	Contact Form Link Spam URL Check
	 *	Check Anchor Text Links in message content for links to shortened URLs
	 *	$haystack is contact form message content
	 *	@dependencies	rs_wpss_parse_email(), rs_wpss_parse_links(), WPSS_Filters::urlshort_blacklist_chk(), 
	 *	@used by		rs_wpss_contact_form(), 
	 *	@since			... as rs_wpss_cf_link_spam_url_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function cf_link_spam_url_chk( $haystack = NULL, $email = NULL ) {
		$blacklist_status = FALSE;
		if( empty( $haystack ) || empty( $email ) ) { return FALSE; }
		$email_domain = rs_wpss_parse_email( $email, 'domain' );
		$extracted_urls = rs_wpss_parse_links( $haystack, 'url' );
		foreach( $extracted_urls as $u => $url ) {
			if( self::urlshort_blacklist_chk( $url, $email_domain ) ) { return TRUE; }
		}
		return $blacklist_status;
	}

	/**
	 *	Domain Blacklist Check
	 *	@dependencies	rs_wpss_rbkmd(), WPSS_BL::domain(), WPSS_Filters::spammy_domain_chk(), rs_wpss_ubl_cache(), WPSS_Filters::get_rgx_ptrn(), 
	 *	@used by		rs_wpss_contact_form(), WPSS_Filters::link_blacklist_chk(), rs_wpss_pingback_pre_filter(), rs_wpss_trackback_content_filter(), rs_wpss_comment_content_filter(), WPSS_Filters::email_blacklist_chk(), 
	 *  @since			... as rs_wpss_spammy_domain_chk()
	 *  @moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function domain_blacklist_chk( $domain = NULL, $get_list_arr = FALSE, $rev_dns = FALSE ) {
		$blacklisted_domains = rs_wpss_rbkmd( WPSS_BL::domain(), 'de', TRUE );
		if( TRUE === $get_list_arr ) {
			return ( WPSS_SITE_DOMAIN === $domain ) ? array() : $blacklisted_domains;
		}
		/* Goes after array */
		$blacklist_status = FALSE;
		if( empty( $domain ) || WPSS_SITE_DOMAIN === $domain ) { return FALSE; }
		/* Check if Spammy Domain - Do not run if domain is a Reverse DNS */
		if( TRUE !== $rev_dns && self::spammy_domain_chk( $domain ) ) { rs_wpss_ubl_cache( 'set' ); return TRUE; }
		/* Other Checks */
		$regex_phrases_other_checks = array(
			/* Payday Loan Spammers - Keywords in Domain */
			"~((payday|short-?term|instant|quick|fast|speedy|(personal\-?)?cash|credit)\-?loans?|(cash|payday)\-?advance|(instant|quick|fast|speedy)\-?cash)~i",
			/* Scrapers/Scraping Service Spammers */
			"~scrap(er?|ing|[y1i])~i",
			/* Offshore IT Spammers */
			"~info(tech|soft)".WPSS_RGX_TLD."$~i",
			/* SEO/Internet Marketing/Lead Spammers */
			"~(agency|best|buy|cheap|discover|exclusive|feed|get|give|local|marketing|more|pay\-?per|reputation\-?management|se[mo]|web)[0-9a-z\.\-]*(lead|prospect)s?~i",
			"~(^(1\-?)?(se[mo]|india)(\-|[a-z0-9]*(india|delhi|mumbai|chennai)|local|rank|services?|web)|((india|delhi|mumbai|chennai)[a-z0-9]*|local|rank|services?|web|\-)(se[mo]|india)[\.\-$])~i",
			/* Disposable Email */
			"~((\d+day|\d+hour|abyss|alias|anon(ym?(ous)?)?|(anti|de|no)?spam|catch|clean|destruct(ing)?|devnull|discard|dispose?a?(ble)?|drop|dumpy?|easy|explode|fake[drs]?|(get)?air|grandm?as?|guer+il+a|hide|inat[eo]r|incog(nito)?|instant|[lf]ast|minute[ndrs]?|proxs?(y|ie)|recon|smash|tempo?(ra((ry|rio)?|ire|l|nea)?)?|throwaway|\w{0,3}trash(can)?|yop)[\-\.]?([eoiy]?[\-\.]?mail|amil|(in)?box|address)|([eoiy]?[\-\.]?mail|amil|(in)?box|address)[\-\.]?(\d+day|\d+hour|abyss|alias|anon(ym?(ous)?)?|(anti|de|no)?spam|catch|clean|destruct(ing)?|devnull|discard|dispose?a?(ble)?|drop|dumpy?|easy|explode|fake[drs]?|(get)?air|grandm?as?|guer+il+a|hide|inat[eo]r|incog(nito)?|instant|[lf]ast|minute[ndrs]?|proxs?(y|ie)|recon|smash|tempo?(ra((ry|rio)?|ire|l|nea)?)?|throwaway|\w{0,3}trash(can)?|yop))~i",
			/* Misc */
			"~^((ww[w0-9]|m)\.)?whereto(buy|get)cannabisoil~i",
			"~(cheap|quick)".WPSS_RGX_TLD."$~i",
			"~(plan\-?cul|ton\-?plan\-?q)~i",
			"~^((ww[w0-9]|m)\.)?(buy|get|play|hack|unlock)\-[a-z0-9\.\-]+\-(online|now|quick|fast)".WPSS_RGX_TLD."$~i",
			"~^(accounts|cloud|code|developers|docs|feedproxy|firebase|get|groups|hangouts|images|mail|news|photos|picasa|sites|talkgadget|translate)\.google".WPSS_RGX_TLD."$~i",
		);
		/**
		 *	Block punycode domains if site locale/lang is en_xx (English)
		 *	Spam/Security risk - Actively used in spam / phishing / hacks.
		 *	Punycode domains mask nature of website (like URL shorteners), or imitate another site (homograph attacks).
		 */
		if( ( rs_wpss_is_lang_en_us() || rs_wpss_is_lang_t7() ) && 0 !== strpos( WPSS_SITE_DOMAIN, 'xn--' ) && FALSE === strpos( WPSS_SITE_DOMAIN, '.xn--' ) ) {
			$regex_phrases_other_checks[] = "~(^xn\-\-[a-z0-9_\-]+".WPSS_RGX_TLD."|\.xn\-\-[a-z0-9]+)$~i";
		}
		foreach( $regex_phrases_other_checks as $i => $regex_check_phrase ) {
			if( parent::preg_match( $regex_check_phrase, $domain ) ) { rs_wpss_ubl_cache( 'set' ); return TRUE; }
		}
		/* Final Check - The Blacklist...takes longest once blacklist is populated, so put last */
		foreach( $blacklisted_domains as $i => $blacklisted_domain ) {
			$regex_check_phrase = self::get_rgx_ptrn( $blacklisted_domain, '', 'domain' );
			if( parent::preg_match( $regex_check_phrase, $domain ) ) { rs_wpss_ubl_cache( 'set' ); return TRUE; }
		}
		return $blacklist_status;
	}

	/**
	 *	Email Blacklist Check
	 *	@dependencies	WPSS_Filters::get_blacklist_data(), WPSS_BL::email(), rs_wpss_sanitize_gmail(), WPSS_Filters::domain_blacklist_chk(), WPSS_Filters::get_rgx_ptrn(), WPSS_Filters::ubl_cache(), 
	 *	@used by		rs_wpss_is_valid_email(), rs_wpss_contact_form(), rs_wpss_comment_content_filter(), rs_wpss_misc_form_spam_check(), rs_wpss_cf7_spam_check(), rs_wpss_gf_spam_check(), rs_wpss_check_new_user(),  
	 *	@since			... as rs_wpss_email_blacklist_chk()
	 *	@modified		1.9.9.9		Added email whitelist check
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function email_blacklist_chk( $email = NULL, $get_eml_list_arr = FALSE, $get_pref_list_arr = FALSE, $get_str_list_arr = FALSE, $get_str_rgx_list_arr = FALSE ) {
		$email = ( is_string( $email ) ) ? WPSS_Func::lower( $email ) : '';
		$blacklisted_emails	= rs_wpss_rbkmd( WPSS_BL::email(), 'de', TRUE );
		$email_whitelisted	= rs_wpss_whitelist_check( $email, NULL, TRUE );
		if( TRUE === $get_eml_list_arr ) {
			return ( TRUE === $email_whitelisted ) ? array() : $blacklisted_emails;
		}
		$blacklisted_email_prefixes =
			array(
				/* The beginning part of the email */
				"anonymous@", "nope@", "fuckyou@", "root@", "seo@", "spam@", "spambot@", "spammer@", "suckadick@", "yourmom@", 
			);
		if( TRUE === $get_pref_list_arr ) { return $blacklisted_email_prefixes; }

		$blacklisted_email_strings =
			array(
				/* Red-flagged strings that occur anywhere in the email address */
				".seo@gmail.com", ".bizapps@gmail.com", ".com@gmail.com", 
			);
		if( TRUE === $get_str_list_arr ) { return $blacklisted_email_strings; }

		$blacklisted_email_strings_rgx =
			array(
				/* Custom regex strings that occur in the email address */
				"spinfilel?namesdat", "^name\-[0-9]{5}\@g(oogle)?mail".WPSS_RGX_TLD."$", 
				"(^|\.|[a-z]+)((market(ing|er)s?|business|seo|web(master)?)((design(er|ing)?|experts?|manager|services?|consult(ant|ing)|(india|delhi|mumbai|chennai)).*)?)\@".WPSS_RGX_FREEMAIL."$", 
				".*((payday|short-?term|instant|(personal-?)?cash|credit)-?loans?|(cash|payday)-?advance|quick-?cash).*\@".WPSS_RGX_FREEMAIL."$", 
				"(dr.*(temple|shrine|home|cent(er|re)|curer?)(curer?)?|.*((\D|\b)6{3}(\D|\b)|il+uminati|(healing|individual|love|powerful|solution)?(healing|powerful|spell(caster)?|e?spiritu?(uale?)?)|(healing|powerful|spell(caster)?|e?spiritu?(uale?)?)(temple|shrine|home|cent(er|re))?).*)\@".WPSS_RGX_FREEMAIL."$", 
				"^(dr|prophet|(temple|shrine|home|cent(er|re)))\.*[a-z\.]+\.*(healing|herb(al)?[\w\-\.\+]*(solutions?)?|natur(e|al)[\w\-\.\+]*(solutions?|remed(y|ies))?|great)[\w\-\.\+]*(temple|shrine|home|cent(er|re))[\w\-\.\+]*\@".WPSS_RGX_FREEMAIL."$", 
				"^(dr|prophet)\.*[a-z\.]+[\w\-\.\+]*(temple|shrine|home|cent(er|re))\@".WPSS_RGX_FREEMAIL."$", 
			);
		if( TRUE === $get_str_rgx_list_arr ) { return $blacklisted_email_strings_rgx; }

		/* Goes after all arrays */
		$blacklist_status = FALSE;
		if( empty( $email ) || TRUE === $email_whitelisted ) { return FALSE; }
		$g_san_email = rs_wpss_sanitize_gmail( $email );
		$blacklisted_domains = self::domain_blacklist_chk( '', TRUE );

		/* Gmail sanitization check */
		$regex_phrase_g_san_arr = array();
		foreach( $blacklisted_emails as $i => $blacklisted_email ) {
			$regex_phrase_g_san_arr[] = self::get_rgx_ptrn( $blacklisted_email, '', 'email_addr' );
		}
		foreach( $regex_phrase_g_san_arr as $i => $regex_phrase ) {
			if( parent::preg_match( $regex_phrase, $g_san_email ) ) { return TRUE; }
		}

		$n = 0; /* Tests 1-3 - Don't add to UBL Cache */
		$t = 0; /* Total */
		$regex_phrase_arr = array();
		foreach( $blacklisted_email_prefixes as $i => $blacklisted_email_prefix ) {
			$regex_phrase_arr[] = self::get_rgx_ptrn( $blacklisted_email_prefix, '', 'email_prefix' );
			++$n; ++$t;
		}
		foreach( $blacklisted_email_strings as $i => $blacklisted_email_string ) {
			$regex_phrase_arr[] = self::get_rgx_ptrn( $blacklisted_email_string, '', 'red_str' );
			++$n; ++$t;
		}
		foreach( $blacklisted_email_strings_rgx as $i => $blacklisted_email_string_rgx ) {
			$regex_phrase_arr[] = self::get_rgx_ptrn( $blacklisted_email_string_rgx, '', 'rgx_str' );
			++$n; ++$t;
		}
		foreach( $blacklisted_domains as $i => $blacklisted_domain ) {
			$regex_phrase_arr[] = self::get_rgx_ptrn( $blacklisted_domain, '', 'email_domain' );
			++$t;
		}
		foreach( $regex_phrase_arr as $i => $regex_phrase ) {
			if( parent::preg_match( $regex_phrase, $email ) ) {
				if( $i > $n ) { rs_wpss_ubl_cache( 'set' ); }
				return TRUE;
			}
		}
		return $blacklist_status;
	}

	/**
	 *	Security - Misc Exploit URL Check
	 *	Check ALL links for common exploit URLs
	 *	$urls - an array of URLs parsed from comment or message content
	 *	If $urls is string, will convert to array (so can be used for Comment Author URL or Contact Form Website)
	 *	@dependencies	rs_wpss_get_query_string(), 
	 *	@used by		rs_wpss_contact_form(), rs_wpss_trackback_content_filter(), rs_wpss_comment_content_filter(), 
	 *	@since			1.4 as rs_wpss_exploit_url_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function exploit_url_chk( $urls = NULL ) {
		$blacklist_status = FALSE;
		if( empty( $urls ) ) { return FALSE; }
		if( is_string( $urls ) ) {
			$urls_arr 	= array();
			$urls_arr[]	= $urls;
			$urls 		= $urls_arr;
		}
		foreach( $urls as $u => $url ) {
			$query_str = rs_wpss_get_query_string( $url );
			if( parent::preg_match( "~/phpinfo\.ph(p[0-9]*|tml)\?~i", $url ) ) {
				/* phpinfo.php Redirect - Used in XSS */
				return TRUE;
			} elseif( parent::preg_match( "~^(https?\:/+)?([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/?~i", $url ) ) {
				/**
				 *	IP Address URLs
				 *	Normal people (and Trackbacks/Pingbacks) don't post IP addresses as their website address in a comment
				 *	Dangerous because users have no idea what website they are clicking through to
				 *	Likely a Phishing site or XSS
				 */
				return TRUE;
			} elseif( !empty( $query_str ) && parent::preg_match( "~(\.\.(\/|%2f)|(boot|php|user)\.ini|(ftp|https?)(\:|%3a)|mosconfig_[a-z_]{1,21}(\=|%3d)|base64_(en|de)code.*\(.*\)|[\[\]\(\)\{\}\<\>\|\"\';\?\*\$]|%22|%24|%27|%2a|%3b|%3c|%3e|%3f|%5b|%5d|%7b|%7c|%7d|%0|%a|%b|%c|%d|%e|%f|127\.0|globals|(en|de)code|localhost|loopback|request|select|insert|union|declare)~i", $query_str ) ) { /* Check Query String */
				/**
				 *  Dangerous Exploit URLs - XSS, SQL injection, or other
				 *  Test Query String - This covers a number of SQL Injection and other exploits
				 *	TO DO: Add more rules
				 */
				return TRUE;
			} elseif( parent::preg_match( "~([\[\]\(\)\{\}\<\>\|\"\';\*\$]|%22|%24|%27|%2a|%3b|%3c|%3e|%3f|%5b|%5d|%7b|%7c|%7d)~i", $url ) ) { /* Check Query String */
				/**
				 *  Dangerous Exploit URLs - XSS, SQL injection, or other
				 *  Test URL - no reason these would occur in a normal URL - they're not legal in a URL, but we've seen them in a lot of spam URL submissions
				 */
				return TRUE;
			}
		}
		return $blacklist_status;
	}

	/**
	 *	FCrDNS Check
	 *	Forward-confirmed reverse DNS test (FCrDNS)
	 *	For verification, require boolean TRUE value. All other results are fail.
	 *	If verification fails, check NULL/FALSE values for more info (Blatant Fail vs Unverified/Undetermined)
	 *	@dependencies	...
	 *	@since			1.9.9.9.4
	 *	@since			1.9.15		Always returns boolean unless $format === TRUE
	 *	@return			bool|null	TRUE for Pass, FALSE for Blatant Fail, NULL for Unverified/Undetermined (PTR record not configured or misconfigured)
	 */
	static public function fcrdns( $ip, $rev_dns = NULL, $fwd_dns = NULL, $format = FALSE ) {
		$rev_dns	= ( empty( $rev_dns ) || 'localhost' === $rev_dns || '.' === $rev_dns ) ? rs_wpss_get_reverse_dns( $ip ) : $rev_dns;
		$rev_dns	= ( empty( $rev_dns ) || 'localhost' === $rev_dns || '.' === $rev_dns ) ? $ip : $rev_dns;
		$fwd_dns	= ( $rev_dns === $ip )	? $ip : $fwd_dns;
		$fwd_dns	= ( empty( $fwd_dns ) )	? rs_wpss_get_forward_dns( $rev_dns ) : $fwd_dns;
		$result		= ( $rev_dns === $ip )	? NULL : ( $fwd_dns === $ip  && WP_SpamShield::is_valid_ip( $fwd_dns ) && !WP_SpamShield::is_valid_ip( $rev_dns ) );
		$result		= ( TRUE === $format )	? self::fcrdns_format( $result ) : ( TRUE === $result );
		return $result;
	}

	/**
	 *	FCrDNS Format
	 *	Input the raw FCrDNS result and get formatted, human-readable result
	 *	@dependencies	...
	 *	@since			1.9.9.9.4
	 *	@return			string
	 */
	static public function fcrdns_format( $fcrdns ) {
		if( TRUE === $fcrdns ) {
			return 'PASS'; /* PASS - 'Verified' - DNS round-trip is verified - Any other value is a FAIL */
		} elseif( FALSE === $fcrdns ) {
			return 'FAIL'; /* FAIL - Verification blatantly failed - Possibly spoofed or misconfigured */
		} elseif( NULL === $fcrdns ) {
			return 'FAIL'; /* FAIL - Unverified/Undetermined - Still a FAIL - PTR records not configured, or misconfigured */
		} else {
			return 'FAIL'; /* FAIL - Catch-all fail condition - Should never happen, but included as fail-safe */
		}
	}

	/**
	 *	Get blacklist data
	 *	@dependencies	...
	 *	@since			1.9.9.9.4
	 *	@return			array
	 */
	static private function get_blacklist_data( $blacklist, $caller ) {
		$bl = 'bl_'.$blacklist;
		if( !is_string( $blacklist ) || !method_exists( 'WPSS_BL', $blacklist ) ) {
			return FALSE;
		}
		$data = self::$$bl = ( !empty( self::$$bl ) && is_array( self::$$bl ) ) ? self::$$bl : '';
		$data = self::$$bl = ( !empty( $data ) && is_array( $data ) ) ? $data : self::rubikode( WPSS_BL::$blacklist(), __METHOD__, 'de', TRUE );
		return $data;
	}

	/**
	 *	Instantly get auto-generated PCRE RegEx pattern from an array or string
	 *	Extremely useful for PCRE RegEx functions
	 *	Used in many WP-SpamShield anti-spam functions
	 *	@dependencies	...
	 *	@since			... as rs_wpss_get_regex_phrase()
	 *	@moved			1.9.9.9.4 to WPSS_Filters class
	 */
	static public function get_rgx_ptrn( $input, $custom_delim = NULL, $flag = "N" ) {
		$flag_rgx_arr = array(
			"N" 			=> "(^|[\s\.]+|\b\s+(.*\s+)?\b)(X)([\s\.\:,\!\?\@\/]+|\b\s+(.*\s+)?\b|$)",
			"S" 			=> "^(X)([\s\.\:,\!\?\@\/]+|\b\s+(.*\s+)?\b|$)",
			"E" 			=> "(^|[\s\.]+|\b\s+(.*\s+)?\b)(X)$",
			"W" 			=> "^(X)$",
			"email_addr"	=> "^(X)$",
			"email_prefix" 	=> "^(X)",
			"email_domain" 	=> "\@((ww[w0-9]|m)\.)?(X)$",
			"domain" 		=> "(^|\.)((ww[w0-9]|m)\.)?(X)$",	/* Updated in 1.9.9.8.2 to include subdomains */
			"ns" 			=> "(^|\.)(X)$",
			"tld" 			=> "\.(X)$",
			"authorkw"		=> "(^|[\s\.]+|\b\s+(.*\s+)?\b)(X)(\b\s+(.*\s+)?\b|\s+|$)",
			"atxtwrap"		=> "(<\s*a\s+[a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*\s*href|\[(url|link))\s*\=\s*['\"]?\s*(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*['\"]?\s*[a-z0-9\-_\.\?\='\"\:;\(\)\{\}\s]*\s*(>|\])([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*\s+)?(X)([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*\s+)?(<|\[)\s*\/\s*a\s*(>|(url|link)\])",
			/**
			 *	REFERENCE: Parse full html links with this:
			 *	$parse_links_rgx = "~(<\s*a\s+[a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*\s*href|\[(url|link))\s*\=\s*['\"]?\s*(https?\:/+[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s*['\"]?\s*[a-z0-9\-_\.\?\='\"\:;\(\)\{\}\s]*\s*(>|\])([a-z0-9àáâãäåçèéêëìíîïñńņňòóôõöùúûü\-_\/\.\?\&\=\~\@\%\+\#\:;\!,'\(\)\{\}\s]*)(<|\[)\s*\/\s*a\s*(>|(url|link)\])~iu";
			 */
			"linkwrap"		=> "(<\s*a\s+([a-z0-9\-_\.\?\='\"\:\(\)\{\}\s]*)\s*href|\[(url|link))\s*\=\s*(['\"])?\s*https?\:/+((ww[w0-9]|m)\.)?(X)/?([a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]*)(['\"])?(>|\])",
			"httplinkwrap"	=> "(^|\b)https?\:/+((ww[w0-9]|m)\.)?(X)/?([a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]*)",
			/**
			 *	REFERENCE: Parse stripped http links with this:
			 *	$search_http_rgx ="~\s+(https?\://[a-z0-9\-_\/\.\?\&\=\~\@\%\+\#\:]+)\s+~i";
			 */
			"red_str"		=> "(X)", /* Red-flagged string */
			"rgx_str"		=> "(X)", /* Regex-ready string */
		);
		if( is_array( $input ) ) {
			$rgx_flag = $flag_rgx_arr[$flag];
			$rgx_ptrn_pre_arr = array();
			foreach( $input as $i => $val ) {
				if( $flag === "rgx_str" || $flag === "authorkw" || $flag === "atxtwrap" || $flag === "tld" ) { $val_reg_pre = $val; } /* Variable must come in prepped for regex (preg_quoted) */
				else { $val_reg_pre = rs_wpss_preg_quote( $val ); }
				$rgx_ptrn_pre_arr[] = $val_reg_pre;
			}
			$rgx_ptrn_pre_str 		= implode( "|", $rgx_ptrn_pre_arr );
			$rgx_ptrn_str 			= preg_replace( "~X~", $rgx_ptrn_pre_str, $rgx_flag );
			if( !empty( $custom_delim ) ) {  $delim = $custom_delim; } else { $delim = "~"; }
			$rgx_ptrn = $delim.$rgx_ptrn_str.$delim."iu"; /* UTF-8 enabled */
			if( $flag === "email_addr" || $flag === "red_str" ) {
				$rgx_ptrn = str_replace( '@gmail\.', '@g(oogle)?mail\.', $rgx_ptrn );
			}
		} elseif( is_string( $input ) ) {
			$val = $input;
			$rgx_flag = $flag_rgx_arr[$flag];
			if( $flag === "rgx_str" || $flag === "authorkw" || $flag === "atxtwrap" || $flag === "tld" ) { $val_reg_pre = $val; } /* Variable must come in prepped for regex (preg_quoted) */
			else { $val_reg_pre = rs_wpss_preg_quote( $val ); }
			$rgx_ptrn_str 	= preg_replace( "~X~", $val_reg_pre, $rgx_flag );
			if( !empty( $custom_delim ) ) {  $delim = $custom_delim; } else { $delim = "~"; }
			$rgx_ptrn = $delim.$rgx_ptrn_str.$delim."iu"; /* UTF-8 enabled */
			if( $flag === "email_addr" || $flag === "red_str" ) {
				$rgx_ptrn = str_replace( '@gmail\.', '@g(oogle)?mail\.', $rgx_ptrn );
			}
		} else { return $input; }
		return $rgx_ptrn;
	}

	/**
	 *  Web Host NS Blacklist Check
	 *	@dependencies	WP_SpamShield::get_option(), rs_wpss_get_ns(), rs_wpss_rbkmd(), WPSS_BL::host_ns(), WPSS_Filters::get_rgx_ptrn(), WP_SpamShield::update_option(), 
	 *	@used by		WPSS_Filters::domain_blacklist_chk(), 
	 *  @since			1.9.9.8.1 as rs_wpss_host_ns_blacklist_chk()
	 *  @moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function host_ns_blacklist_chk( $domain = WPSS_SITE_DOMAIN ) {
		$host_ns_bl = WP_SpamShield::get_option( 'host_ns_bl' );
		if( !empty( $host_ns_bl ) && ( 'YES' === $host_ns_bl || 'NO' === $host_ns_bl ) ) { return ( 'YES' === $host_ns_bl ) ? TRUE : FALSE; }
		$ns = rs_wpss_get_ns( $domain ); if( empty( $ns ) ) { return FALSE; }
		$blacklisted_host_ns = rs_wpss_rbkmd( WPSS_BL::host_ns(), 'de', TRUE );

		/* The Blacklist */
		foreach( $ns as $i => $n ) {
			foreach( $blacklisted_host_ns as $i => $bl_ns ) {
				$regex_check_phrase = self::get_rgx_ptrn( $bl_ns, '', 'ns' );
				if( parent::preg_match( $regex_check_phrase, $n ) ) { WP_SpamShield::update_option( array( 'host_ns_bl' => 'YES' ) ); return TRUE; }
			}
		}
		WP_SpamShield::update_option( array( 'host_ns_bl' => 'NO' ) );
		return FALSE;
	}

	/**
	 *	Check if URL has utm_ element(s) in query string (advertising URLs)
	 *	@dependencies	WP_SpamShield::preg_match(), rs_wpss_get_query_string()
	 *	@used by		..., 
	 *	@since			1.9.9.9.4
	 */
	static public function is_utm_url( $url ) {
		return ( parent::preg_match( "~(^|\?|\&|%26|%3F)(utm_(source|medium|campaign|term|content))(\=|%3D|$)~i", rs_wpss_get_query_string( $url ) ) );
	}

	/**
	 *	Language mismatch filter
	 *	@dependencies	... 
	 *	@used by		...
	 *	@since			1.9.9.8.8
	 */
	static public function lang_mismatch( $haystack ) {
		return ( ( rs_wpss_is_lang_en_us() || rs_wpss_is_lang_t7() ) && rs_wpss_strlen( $haystack ) >= 50 && !parent::preg_match( "~[a-z0-9]+~iu", $haystack ) );
	}

	/**
	 *	Language mismatch percent filter
	 *	Input string and percent, min 10%, max 90%
	 *	@dependencies	... 
	 *	@used by		...
	 *	@since			1.9.9.8.8
	 */
	static public function lang_mismatch_pct( $haystack, $pct = 40 ) {
		if( ( !rs_wpss_is_lang_en_us() && !rs_wpss_is_lang_t7() ) ) { return FALSE; }
		$pct = ( !is_numeric( $pct ) ) ? 40 : (int) $pct;
		if( $pct < 10 ) { $pct = 10; } elseif( $pct > 90 ) { $pct = 90; }
		$pct_dec		= ( $pct / 100 );
		$haystack_len	= rs_wpss_strlen( $haystack ); if( $haystack_len < 50 ) { return FALSE; }
		$nonlat_chr		= preg_replace( "~[\p{Common}\p{Latin}]~iu", '', $haystack ); if( empty( $nonlat_chr ) ) { return FALSE; }
		$nonlat_num		= rs_wpss_strlen( $nonlat_chr );
		$latin_num		= (int) ( $haystack_len - $nonlat_num ); if( 0 === $latin_num ) { return TRUE; }
		$limit_min		= (int) round( $pct_dec * $haystack_len );
		return ( $latin_num < $limit_min );
	}

	/**
	 *	Link Blacklist Check
	 *	$haystack can be any body of content you want to search for links to blacklisted domains
	 *	$type can be 'domain', 'url', or 'urlshort' depending on what kind of check you need to do. 'domain' is faster, hence it's the default
	 *	@dependencies	rs_wpss_parse_links(), WPSS_Filters::domain_blacklist_chk(), 
	 *	@used by		rs_wpss_comment_content_filter(), 
	 *	@since			... as rs_wpss_link_blacklist_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function link_blacklist_chk( $haystack = NULL, $type = 'domain' ) {
		$blacklist_status = FALSE;
		if( empty( $haystack ) ) { return FALSE; }
		$extracted_domains = rs_wpss_parse_links( $haystack, 'domain' );
		foreach( $extracted_domains as $d => $domain ) {
			if( self::domain_blacklist_chk( $domain ) ) { return TRUE; }
		}
		return $blacklist_status;
	}

	/**
	 *	Excessively Long URL Check
	 *	To prevent obfuscated & exploit URL's
	 *	@dependencies	rs_wpss_strlen(), 
	 *	@used by		WPSS_Filters::at_link_spam_url_chk(), rs_wpss_contact_form(), 
	 *	@since			1.3.8 as rs_wpss_long_url_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function long_url_chk( $url = NULL ) {
		$blacklist_status = FALSE;
		if( empty( $url ) || !is_string( $url ) || FALSE !== strpos( $url, WPSS_SITE_DOMAIN ) ) { return FALSE; }
		$url_lim = 140;
		$url_len = rs_wpss_strlen( $url );
		if( $url_len > $url_lim ) { return TRUE; }
		return $blacklist_status;
	}

	/**
	 *	Spam Domain URL Check
	 *	To prevent author url and anchor text links to spam domains
	 *	@dependencies	WPSS_Filters::get_blacklist_data(), WPSS_BL::spam_domain(), WPSS_Filters::get_rgx_ptrn(), 
	 *	@used by		WPSS_Filters::at_link_spam_url_chk(), 
	 *	@since			1.3.8 as rs_wpss_misc_spam_url_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function misc_spam_url_chk( $url = NULL ) {
		$spam_domains = rs_wpss_rbkmd( WPSS_BL::spam_domain(), 'de', TRUE );
		/* Goes after array */
		$blacklist_status = FALSE;
		if( empty( $url ) || !is_string( $url ) || FALSE !== strpos( $url, WPSS_SITE_DOMAIN ) ) { return FALSE; }
		$domain = rs_wpss_get_domain( $url );
		if( self::is_utm_url( $url ) ) { return TRUE; }
		$regex_phrases_other_checks =
			array(
				"~^https?\://(([a-z0-9]+|www)\.)?google".WPSS_RGX_TLD."/maps/~i", "~(/members?/|/user/?profile/)~i", "~([a-z0-9]+)porn([0-9]+)\.([a-z0-9\-]+)".WPSS_RGX_TLD."~i", "~shopsquareone".WPSS_RGX_TLD."/stores/~i", "~yellowpages".WPSS_RGX_TLD."/([a-z0-9\-]+)/~i", "~seo-?(services?-?)?(new-?york|ny|los\-?angeles|boston|chicago|houston|san\-?francisco)~i", 
			);
		foreach( $regex_phrases_other_checks as $i => $regex_check_phrase ) {
			if( parent::preg_match( $regex_check_phrase, $url ) ) { return TRUE; }
		}
		$regex_phrase = self::get_rgx_ptrn( $spam_domains, '', 'domain' );
		if( parent::preg_match( $regex_phrase, $domain ) ) { return TRUE; }
		/* When $regex_phrase exceeds a certain size, switch this to run smaller groups or run each domain individually */
		return $blacklist_status;
	}

	/**
	 *  Referrer Blacklist Check
	 *  Certain referrers result in spam 100% of the time
	 *  @dependencies	rs_wpss_rbkmd(), WPSS_BL::referrer(), rs_wpss_get_referrer(), rs_wpss_get_domain(), WPSS_Filters::get_rgx_ptrn(), 
	 *  @used by		WPSS_Filters::rs_wpss_ubl_cache(), 
	 *  @since			1.8 as rs_wpss_referrer_blacklist_chk()
	 *  @moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function referrer_blacklist_chk( $url = NULL ) {
		$referrer_domains = rs_wpss_rbkmd( WPSS_BL::referrer(), 'de', TRUE );
		/* Goes after array */
		$blacklist_status = FALSE;
		if( empty( $url ) || !is_string( $url ) ) { $url = rs_wpss_get_referrer( FALSE, TRUE, TRUE ); }
		if( empty( $url ) || !is_string( $url ) || FALSE !== strpos( $url, WPSS_SITE_DOMAIN ) ) { return FALSE; }
		$domain = rs_wpss_get_domain( $url );
		$regex_phrases_other_checks = array(
			"~nksmart\.com/tools/website\-opener\.php$~i", "~rygist\.com/multiple\-url\-opener\.php$~i",
			);
		foreach( $regex_phrases_other_checks as $i => $regex_check_phrase ) {
			if( parent::preg_match( $regex_check_phrase, $url ) ) { return TRUE; }
		}
		$regex_phrase = self::get_rgx_ptrn( $referrer_domains, '', 'domain' );
		if( parent::preg_match( $regex_phrase, $domain ) ) { return TRUE; }
		/* When $regex_phrase exceeds a certain size, switch this to run smaller groups or run each domain individually */
		return $blacklist_status;
	}

	/**
	 *	Reverse DNS Filter
	 *	@dependencies	rs_wpss_ip_proxy_info(), WP_SpamShield::casetrans(), rs_wpss_is_xmlrpc(), rs_wpss_is_doing_cron(), rs_wpss_is_local_request(), WPSS_Filters::ubl_cache(), rs_wpss_is_pingback(), rs_wpss_is_local_request(), WP_SpamShield::is_valid_ip(), rs_wpss_domain_exists(), 
	 *	@used by		rs_wpss_contact_form(), rs_wpss_pingback_pre_filter(), rs_wpss_trackback_content_filter(), rs_wpss_comment_content_filter(), bad_robot_blacklist_chk(), 
	 *	@since			... as rs_wpss_revdns_filter()
	 *	@modified		1.9.9.8.2	Added WPSS_Filters::domain_blacklist_chk() - REVDA-10500-BL
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function revdns_filter( $type = 'comment', $status = NULL, $ip = NULL, $rev_dns = NULL, $author = NULL, $email = NULL ) {
		$wpss_error_code	= '';
		$blacklisted		= $linkback = FALSE;
		if( WP_SpamShield::is_support_url() ) { /* Only run filter if not on support URL - @since 1.9.9.8.2 */
			return array( 'status' => $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		}
		$allow_proxy_users	= WP_SpamShield::get_option( 'allow_proxy_users' );
		global $wpss_ip_proxy_info; if( empty( $wpss_ip_proxy_info ) ) { $wpss_ip_proxy_info = rs_wpss_ip_proxy_info(); }
		extract( $wpss_ip_proxy_info ); /* IP / PROXY INFO */
		if( TRUE === $use_masked_ip && WP_SpamShield::is_valid_ip( $masked_ip ) ) { $rev_dns = rs_wpss_get_reverse_dns( $masked_ip ); }
		$rev_dns = WPSS_Func::lower( trim( $rev_dns ) );
		if( !empty( $author ) ) { $author = WPSS_Func::lower( $author ); }
		if( !empty( $email ) ) 	{ $author = WPSS_Func::lower( $email ); }
		switch ( $type ) {
			/* TO DO: Add types for EPC, XML-RPC, JSON/REST */
			case 'register':
				$pref = 'R-'; break;
			case 'contact':
				$pref = 'CF-'; break;
			case 'trackback':
				$pref = 'T-'; $linkback = TRUE; break;
			case 'pingback':
				$pref = 'P-'; $linkback = TRUE; break;
			case 'misc form':
				$pref = 'MSC-'; break;
			case 'early post check':
				$pref = 'EPC-'; break;
			case 'password reset':
				$pref = 'PWR-'; break;
			case 'contact form 7':
				$pref = 'CF7-'; break;
			case 'gravity forms':
				$pref = 'GF-'; break;
			case 'jetpack form':
				$pref = 'JP-'; break;
			case 'ninja forms':
				$pref = 'NF-'; break;
			case 'mailchimp form':
				$pref = 'MCF-'; break;
			case 'guestbook form':
				$pref = 'GBK-'; break;
			default:
				$pref = '';
		}

		/* Test Reverse DNS Hosts */

		/**
		 *	Consistent worst offenders in cybersecurity attacks and spam
		 *	Bad ASNs / Bulletproof web hosts / Dirty proxies / etc.
		 *	Data tracked over 3-5+ years, and kept current
		 */
		$banned_servers_worst = array(
			"REVD-WO-1001"	=> "~^(".WPSS_RGX_IPSTR.")?host\.colocrossing".WPSS_RGX_TLD."$~",							/* ASN 36352, ASN 55286, ASN  20278 - ColoCrossing */
			"REVD-WO-1002"	=> "~^".WPSS_RGX_IPSTR."\.reverse\-dns([a-z\-]+)?$~",										/* ASN 20278 - ColoCrossing / Nexeon Technologies */
			"REVD-WO-1003"	=> "~^(".WPSS_RGX_IPSTR."static|unassigned)\.(quadranet|greencloudvps)".WPSS_RGX_TLD."$~",	/* ASN 8100 - Quadranet / GreenCloudVPS */
			"REVD-WO-1004"	=> "~^".WPSS_RGX_IPSTR."rdns\.scalabledns".WPSS_RGX_TLD."$~",								/* ASN 18978 - Enzu */
			"REVD-WO-1005"	=> "~\.(ipvnow|ubiquity(servers)?)".WPSS_RGX_TLD."$~",										/* ASN 15003 - Nobis / Ubiquity */
			"REVD-WO-1006"	=> "~^unassigned\.psychz".WPSS_RGX_TLD."$~",												/* ASN 40676 - Psychz */
			"REVD-WO-1201"	=> "~\.(port0\.org|unsecu\.re|babylon\.network|members\.linode\.com|static\.host|static\.contabo\.net|privacyrepublic\.org|dataclub\.biz|multisec\.no|coldhak\.com|worldstream\.nl|reverse\.serversub\.com|cloudatcost\.com|voxility\.net|deltahost\.com\.ua)$~",	/* A lot of malicious traffic from Tor nodes on these */
			"REVD-WO-1202"	=> "~(^|[\.\-])d[o0][\.\-]?n[o0]?t[\.\-]tr+y+[\.\-](t[o0]|2)[\.\-]f[uv]c?k+(ed)?[\.\-][uv][s5]+~",
			"REVD-WO-1203"	=> "~(^|\.)seo[0-9]*\.[a-z\-]+[0-9]*".WPSS_RGX_TLD."$~",
			"REVD-WO-1204"	=> "~(^|[\.\-])([s5]h[e3]l+b[o0]x+|[\w\-]*(h4c?k|r00t|fvck)[\w\-]*)".WPSS_RGX_TLD."$~",
			"REVD-WO-1205"	=> "~(research|scan|probe|benign)(.*)\.eecs\.umich\.edu$~",
			"REVD-WO-1205"	=> "~(^|\.)(censys|scans|zmap|shodan)\.io$~",
		);

		$banned_servers = array(
			/**
			 *  Web servers should not post comments/contact forms/register/use login pages
			 *  Regex rules to stop the worst of the worst offenders
			 *	Legit VPNs/Proxies do not use these servers
			 */
			"REVD1025"		=> "~(^|\.)(opentransfer\.com|secureserver\.net|clients\.your-server\.de|rover\-host\.com|arkada\.rovno\.ua|hosting\.reg\.ru|faust\.net\.ua)$~",	/* Keep "(^|\.)" at beginning, not "(^|[\.\-])" */
			"REVD1026"		=> "~(^|[\.\-])((r?d)?ns|ip)\.softwiseonline\.com$~",
			"REVD1027"		=> "~(^|[\.\-])s([a-z0-9]+)\.websitehome\.co\.uk$~",
			"REVD1028"		=> "~(^|[\.\-])(h(ost?(ed|ing)?(\-by)?)?|vm?)[0-9]+\.server[0-9]+\.vpn(999|2buy)\.com$~",
			"REVD1029"		=> "~(^|[\.\-])(ip[x\.\-])?".WPSS_RGX_IPSTR."(((r?d)?ns|ip)\.(3e\.vc|as[0-9]{4,}\.net|cloudradium\.com|continuumdatacenters\.com|dafeature\.com|mach9servers\.com|micfo\.com|mydnsprovider\.com|purewebtech\.net|racklot\.com|servebyte\.com|smtp[0-9]+\.zhaoshengzixun\.com)|static\.(hostnoc\.net|dimenoc\.com|reverse\.(softlayer\.com|queryfoundry\.net))|ip\.idealhosting\.net\.tr|chunkhost\.com|(rev\.)?poneytelecom\.eu|ipvnow\.com|customer-(rethinkvps|incero)\.com|unknown\.steephost\.(net|com))$~",
			"REVD1030"		=> "~(^|[\.\-])(r?d)?ns([0-9]{1,3})\.(webmasters|rootleveltech)".WPSS_RGX_TLD."$~",
			"REVD1031"		=> "~(^|[\.\-])server[0-9]*\.(shadowbrokers?|junctionmethod|[a-z]+\d*(serv(er|re)|datacent(er|re))s?|([a-z0-9\-]+))".WPSS_RGX_TLD."$~",
			"REVD1032"		=> "~(^|[\.\-])(".WPSS_RGX_IPSTR.")?hosted\-[bn]y\.((?!choopa|reliablesite|vultr)[a-z0-9\-\.])".WPSS_RGX_TLD."$~",		/* Web servers generally starting with "hosted-by", and variations - Kills some PIA (Choopa/reliablesite) */
			"REVD1033"		=> "~(^|[\.\-])([a-z]{2}[0-9]*[x\.\-])?[0-9]{1,3}([x\.\-][0-9]{1,3}){3}([x\.\-]([a-z]{2,3}[x\.\-]){1,2}([a-z\-]+)[x\.\-]([0-9]+))?\.compute(\-[0-9]+)?\.amazonaws".WPSS_RGX_TLD."$~",
			"REVD1034"		=> "~(^|[\.\-])([a-z]+)([0-9]{1,3})\.(guarmarr\.com|startdedicated\.com)$~",
			"REVD1035"		=> "~(^|[\.\-])h?([a-z0-9\-\.]+)\.rev\.(sprintdatacenter|rootvps)\.pl$~",
			"REVD1036"		=> "~^ns([0-9]+)\.ovh".WPSS_RGX_TLD."$~", /* OVH also provides ISP services, so do not block those */
			"REVD1037"		=> "~(^|[\.\-])(static|clients?)[x\.\-]".WPSS_RGX_IPSTR."(clients?\.your-server\.de|customers\.filemedia\.net|hostwindsdns\.com)$~",
			"REVD1038"		=> "~(^|[\.\-])([0-9]{1,3}[x\.\-][0-9]{1,3}[x\.\-][0-9]{1,3}[x\.\-][0-9]{1,3}|ks[0-9]+)[x\.\-]kimsufi".WPSS_RGX_TLD."$~",
			"REVD1039"		=> "~(^|[\.\-])".WPSS_RGX_IPSTR."static[\.\-]reverse\.([a-z]+\-cloud\.serverhub|lstn)".WPSS_RGX_TLD."$~",
			"REVD1040"		=> "~(^|[\.\-])unassigned\.userdns".WPSS_RGX_TLD."$~",
			"REVD1041"		=> "~(^|[\.\-])h(ost?(ed|ing)?(\-by)?)?(".WPSS_RGX_IPSTR."|[0-9]+\.)((rackcentre|host)\.redstation|hostmonster|lotosus|sale24news|serverdedicati\.aruba|static\.arubacloud)".WPSS_RGX_TLD."$~",
			"REVD1042"		=> "~(^|[\.\-])".WPSS_RGX_IPSTR."(([a-z]{2,}\.)?googleusercontent|hostvenom|contina|techserverdns|tailormadeservers)".WPSS_RGX_TLD."$~",
			"REVD1043"		=> "~^[a-z]+[0-9]*\.zeehostbox\.com$~",
			"REVD1044"		=> "~^(h[\.\-])?".WPSS_RGX_IPSTR."keyweb\.de$~",
			"REVD1045"		=> "~(^|[\.\-])no[\.\-]r?dns[x\.\-]yet\.ukservers\.com$~",
			"REVD1046"		=> "~(^|[\.\-])[a-z0-9\-\.]+\.(r?dns|[a-z]{3,6})\.10+tb".WPSS_RGX_TLD."$~",
			"REVD1047"		=> "~(^|[\.\-])we\.love\.servers\.at\.ioflood".WPSS_RGX_TLD."$~",
			"REVD1048"		=> "~(^|[\.\-])[a-z]+\.(highspeedinternetsecurity\.com|uaservers\.net)$~",
			"REVD1049"		=> "~(^|[\.\-])".WPSS_RGX_IPSTR."(datacent(er|re)s?[0-9]*.)~",
			"REVD1050"		=> "~\.(10+tb".WPSS_RGX_TLD.")$~",
			"REVD1051"		=> "~(^|[\.\-])([a-z]+[0-9]+\.|".WPSS_RGX_IPSTR."(ip\.)?)(servers?[a-z0-9\-]+|[a-z0-9\-]+servers?)".WPSS_RGX_TLD."$~",
			"REVD1052"		=> "~(^|[\.\-])".WPSS_RGX_IPSTR."(ip\.)?(host(ed|ing)?[a-z0-9\-]+|[a-z0-9\-]+host(ed|ing)?)".WPSS_RGX_TLD."$~",
			"REVD1053"		=> "~(^|\.)no\-hostname\.~",
		);

		$banned_linkback_servers = array(
			/* ISP's Should not send linkbacks, ever ( Linkbacks: Pingbacks, Trackbacks, etc. ) */
			"REVD2101"		=> "~(^|[\.\-])c\-".WPSS_RGX_IPSTR."hsd[0-9]+\.[a-z]{2,}\.comcast".WPSS_RGX_TLD."$~",
			"REVD2102"		=> "~".WPSS_RGX_IPSTR."(static[x\.\-])?[a-z]+(a?dsl|broadband|cable(modem)?|dhcp([x\.\-]dyn(amic)?)?|dial(up)?|dynamic|mobile)".WPSS_RGX_TLD."$~",
			"REVD2103"		=> "~(^|[\.\-])host".WPSS_RGX_IPSTR."range[0-9]+\-[0-9]+\.btcentralplus".WPSS_RGX_TLD."$~",
			"REVD2104"		=> "~(^|[\.\-])ip".WPSS_RGX_IPSTR."[a-z]{2,}\.[a-z]{2,}\.cox\.net$~",
			"REVD2105"		=> "~(^|[\.\-])".WPSS_RGX_IPSTR."(([a-z]+\.)?(a?dsl|broadband|cable(modem)?|dhcp([x\.\-]dyn(amic)?)?|dial(up)?|dynamic|gprs|mobile))\.kyivstar".WPSS_RGX_TLD."$~",
			"REVD2106"		=> "~(^|[\.\-])([0-9]{1,3}[x\.\-]){2}(a?dsl|broadband|cable(modem)?|dhcp([x\.\-]dyn(amic)?)?|dial(up)?|dynamic|mobile)\.[a-z]{4,}".WPSS_RGX_TLD."$~",
			"REVD2107"		=> "~(^|[\.\-])abs\-static\-".WPSS_RGX_IPSTR."aircel".WPSS_RGX_TLD."$~",
			"REVD2108"		=> "~(^|[\.\-])".WPSS_RGX_IPSTR."res\.bhn\.net$~",
			"REVD2109"		=> "~(^|[\.\-])cpc([0-9]{1,3})\-([a-z]{4})([0-9]{1,3})\-([0-9]{1,3})\-([0-9]{1,3})\-cust([0-9]{1,3})\.([0-9]{1,3})\-([0-9]{1,3})\.cable\.virginm".WPSS_RGX_TLD."$~",
			"REVD2110"		=> "~(^|[\.\-])(static\-)?([0-9]{1,3}[x\.\-]){1,4}tataidc".WPSS_RGX_TLD."$~",
			"REVD2111"		=> "~".WPSS_RGX_IPSTR."([a-z]{2,}\.(beamtele|(biz|res)\.rr)|asianet|bol|lightspeed\.[a-z]{5,6}\.sbcglobal|mtnl|reverse\.spectranet|static\-[a-z]+\.vsnl|vasaicable)".WPSS_RGX_TLD."$~",
			"REVD2112"		=> "~".WPSS_RGX_IPSTR."(a?dsl|broadband|cable(modem)?|dhcp([x\.\-]dyn(amic)?)?|dial(up)?|dynamic|mobile)\.ovh".WPSS_RGX_TLD."$~",
			"REVD2113"		=> "~(^|[\.\-])sol\-fttb\.".WPSS_RGX_IPSTR."\.sovam".WPSS_RGX_TLD."$~",
			"REVD2114"		=> "~(^|[\.\-])[a-f0-9]{8}\.virtua\.com\.br$~",
			"REVD2115"		=> "~(^|[\.\-])(a?dsl|broadband|cable(modem)?|dhcp([x\.\-]dyn(amic)?)?|dial(up)?|dynamic|mobile)[x\.\-]".WPSS_RGX_IPSTR."~",
			"REVD2116"		=> "~".WPSS_RGX_IPSTR."mtn\-?(nigeria|congo|cameroon|ghana|guinea[br]|liberia|sa|southafrica|sudan|syria|uae|ye(men)?|zambia|afghanistan)".WPSS_RGX_TLD."$~",
			"REVD2117"		=> "~".WPSS_RGX_IPSTR."broad(band)?\.([a-z]{2}\.)+dynamic\.~",
		);

		$banned_xmlrpc_servers = array(
			/* Banned XML-RPC Servers */
			"REVD-XR-3100"	=> "~\.opentransfer\.com$~",
		);

		$banned_tor_exit_nodes = array(
			/* Banned Tor Exit Nodes */
			"REVD-TXN-6100" => "~t[o0]r(\-|\.)?([e3]?x+(\-|\.)?[i1]t|r[e3]l[a4]y|[a-z]*pr[o0]x+(y|[i1][e3])|[a-z0-9\-]*n[o0]d[e3]|g[a4]t[e3]w[a4]y|[s5]rv|[s5][e3]rv[e3]r)~",
			"REVD-TXN-6101" => "~([e3]?x+(\-|\.)?[i1]t|r[e3]l[a4]y|[a-z]*pr[o0]x+(y|[i1][e3])|[a-z0-9\-]*n[o0]d[e3]|g[a4]t[e3]w[a4]y|[s5]rv|[s5][e3]rv[e3]r)[s5]*[0-9]*(\-|\.)?t[o0]r(\-|\.)~",
			"REVD-TXN-6102" => "~(^|\-|\.)t[o0]r([e3][a4]d[o0]r|[e3]r[o0])?(\-?[0-9]+)?(\-|\.)~",
			"REVD-TXN-6103" => "~(^|\-|\.)[e3]x+[i1]t\-?[0-9]*\.~",
			"REVD-TXN-6104" => "~^t[o0]r[s5]rv[a-z0-9]*\.~",
			"REVD-TXN-6106" => "~\.t[o0]r([a-z]*pr[o0]x+(y|[i1][e3])|[s5]rv|[s5][e3]rv[e3]r|r[e3]l[a4]y)[s5]*".WPSS_RGX_TLD."$~",
			"REVD-TXN-6107" => "~\.[i1]pr[e3]d[a4]t[o0]r".WPSS_RGX_TLD."$~",
			"REVD-TXN-6108" => "~\.privintl\.org$~",
			"REVD-TXN-6109" => "~^v[e3]kt[o0]rt[0-9]+\.[a-z]{2,}[s5][e3]rv[e3]r[s5]*[a-z0-9\-]*".WPSS_RGX_TLD."$~",
			"REVD-TXN-6110" => "~^exit\.[a-z0-9]+\.linode\.rm\.wtf$~",
		);

		$banned_vpns_proxy_block = array(
			/* Banned VPNs When Proxy Blocking Enabled */
			"REVD-VPX-1001"	=> "~\.(ipvanish|hidehost)".WPSS_RGX_TLD."$~",
			"REVD-VPX-1002"	=> "~secured\-by\.zenmate".WPSS_RGX_TLD."$~",
			"REVD-VPX-1003"	=> "~\.(secret|tcp)vpn".WPSS_RGX_TLD."$~",
			"REVD-VPX-1004"	=> "~(^|[\.\-])(".WPSS_RGX_IPSTR.")?hosted\-[bn]y\.[a-z0-9\-\.]".WPSS_RGX_TLD."$~",	
			"REVD-VPX-1069"	=> "~(^|[\.\-])(server|srv|n[o0]d[e3]|host((ed|ing)([x\.\-][a-z]{2})?)?|rev(erse)?|vm|vps|dedi(cated?|[0-9]+)?|un(assigned|known|specified|setptr)|[a-z]*onsole[a-z]*|c(ontrol)?panel|(web)?mail|smtp|imap|pop|ns|dns|rdns|no\.r?dns\.yet|(ns|asn?)[0-9]{4,6}|seo|robot|pr[o0]x+(y|[i1][e3])|relay|vpn|[a4]n[o0]nym[i1](ty|z([e3]r?|[i1]ng))([x\.\-]pr[o0]x+(y|[i1][e3]))?|priv(acy)?)[s5]?([0-9]{1,3})?\.([a-z0-9\-\.]+)".WPSS_RGX_TLD."$~", 
		);

		$banned_proxies_proxy_block = array(
			/* Banned Proxies When Proxy Blocking Enabled */
			"REVD-PXY-6069"	=> "~(^|[\.])(pr[o0]x+(y|[i1][e3])|relay|vpn|[a4]n[o0]nym[i1](ty|z([e3]r?|[i1]ng))(\-pr[o0]x+(y|[i1][e3]))?|priv(acy)?)[s5]?([0-9]{1,3})?\.([a-z0-9\-\.]+)".WPSS_RGX_TLD."$~", 
		);

		/**
		 *	Check TOR Exit Nodes - FIRST
		 */
		foreach( $banned_tor_exit_nodes as $error_code => $regex_phrase ) {
			if( rs_wpss_is_local_request() ) { break; }
			if( parent::preg_match( $regex_phrase, $rev_dns ) ) {
				$wpss_error_code .= ' '.$pref.$error_code; $blacklisted = TRUE;
				$_SERVER['WPSS_SEC_THREAT'] = $_SERVER['WPSS_TOR_EXIT_NODE'] = $_SERVER['X_TOR_EXIT_NODE'] = TRUE;
				if( !defined( 'WPSS_TOR_EXIT_NODE' ) ) { define( 'WPSS_TOR_EXIT_NODE', TRUE ); }
				rs_wpss_ubl_cache( 'set' ); if( TRUE === WPSS_IP_BAN_ENABLE ) { WPSS_Security::ip_ban(); }
				return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
			}
		}

		if( TRUE === $linkback ) {
			$banned_servers = array_merge( $banned_servers_worst, $banned_linkback_servers );
		} elseif( rs_wpss_is_xmlrpc() ) {
			$banned_servers = array_merge( $banned_servers_worst, $banned_linkback_servers, $banned_xmlrpc_servers );
		} elseif( empty( $allow_proxy_users ) ) {
			$banned_servers = array_merge( $banned_servers_worst, $banned_servers, $banned_vpns_proxy_block, $banned_proxies_proxy_block );
		} else {
			$banned_servers = array_merge( $banned_servers_worst, $banned_servers );
		}

		if( TRUE === $linkback && FALSE === strpos( $rev_dns, 'www.dynamic.' ) && parent::preg_match( "~(^dynamic([x\.\-][0-9]){2,4}|\.dynamic\.)~i", $rev_dns ) ) {
			$wpss_error_code .= ' '.$pref.'REVD2000'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		} elseif( TRUE === $linkback && FALSE === strpos( $rev_dns, 'www.broadband.' ) && parent::preg_match( "~(^broadband([x\.\-][0-9]){2,4}|\.broadband\.)~i", $rev_dns ) ) {
			$wpss_error_code .= ' '.$pref.'REVD2001'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		} elseif( TRUE === $linkback && FALSE === strpos( $rev_dns, 'www.dialup.' ) && parent::preg_match( "~(^dialup([x\.\-][0-9]){2,4}|\.dialup\.)~i", $rev_dns ) ) {
			$wpss_error_code .= ' '.$pref.'REVD2002'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		} elseif( TRUE === $linkback && ( FALSE !== strpos( $rev_dns, '.dsl.dyn.' ) || FALSE !== strpos( $rev_dns, '.dyn.dsl.' ) ) ) {
			$wpss_error_code .= ' '.$pref.'REVD2010'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		} elseif( $type === 'comment' || $type === 'register' || $type === 'contact' || ( !rs_wpss_is_doing_cron() && !rs_wpss_is_local_request() ) ) {
			foreach( $banned_servers as $error_code => $regex_phrase ) {
				if( parent::preg_match( $regex_phrase, $rev_dns ) ) {
					$wpss_error_code .= ' '.$pref.$error_code; $blacklisted = TRUE;
					if( rs_wpss_is_xmlrpc() ) { rs_wpss_ubl_cache( 'set' ); }
					return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
				}
			}
		}

		if( empty( $blacklisted ) && !empty( $author ) && !empty( $email ) && ( $type === 'comment' || $type === 'register' ) ) {
			/* The 8's Pattern - from relakks.com - Anonymous surfing, powered by bots */
			if( parent::preg_match( "~^anon-([0-9]+)-([0-9]+)\.relakks\.com$~", $rev_dns ) && parent::preg_match( "~^([a-z]{8})$~", $author ) && parent::preg_match( "~^([a-z]{8})\@([a-z]{8})\.com$~", $email ) ) {
				/* anon-###-##.relakks.com spammer pattern */
				$wpss_error_code .= ' '.$pref.'REVDA-1050'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
			}
			/* The 8's - also coming from from the likes of rackcentre.redstation.net.uk */
		}

		/**
		 *	Check for spoofed/misconfigured Reverse DNS entries - common with hackers
		 *	When Miscellaneous Form Spam Prevention enabled, protects against XML-RPC brute force amplification attacks
		 *	Note: Run $rev_dns through WP_SpamShield::is_valid_ip() because Rev DNS could be IP
		 */
		if( empty( $blacklisted ) && !rs_wpss_is_pingback() && !rs_wpss_is_local_request() && '[Verified]' !== $fcrdns && rs_wpss_is_xmlrpc() && !WP_SpamShield::is_valid_ip( $rev_dns ) && !rs_wpss_domain_exists( $rev_dns ) ) {
			$wpss_error_code .= ' '.$pref.'REVDA-1060'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		}

		/**
		 *	Blacklisted Domains Check on Reverse DNS
		 */
		if( self::domain_blacklist_chk( $rev_dns, NULL, TRUE ) ) {
			$wpss_error_code .= ' '.$pref.'REVDA-10500-BL'; $blacklisted = TRUE; return array( 'status' => '3', 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
		}

		return array( 'status' => !empty( $wpss_error_code ) ? '3' : $status, 'error_code' => $wpss_error_code, 'blacklisted' => $blacklisted );
	}

	/**
	 *	Rubikode string modification/translation function
	 *	@dependencies	WP_SpamShield::casetrans()
	 *	@used by		WPSS_Filters::get_blacklist_data(), ...
	 *	@since			... as rs_wpss_rbkmod()
	 *	@moved			1.9.9.9.4 to WPSS_Filters class
	 *	@param			string|array	$dat				Data string/array to modify
	 *	@param			string			$cal				Caller / calling method
	 *	@param			string			$typ	'en'|'de'	Type of mod: Encode/Decode
	 *	@param			bool			$exp				Explode?
	 *	@param			bool			$imp				Implode?
	 *	@param			string|null		$cas				Case transformation type
	 *	@param			string			$del				Regex delimiter
	 *	@return			string|array
	 */
	static public function rubikode( $dat, $cal = NULL, $typ = 'en', $exp = FALSE, $imp = FALSE, $cas = NULL, $del = '~' ) {
		if( !is_string( $dat ) && !is_array( $dat ) ) { return $dat; }
		$lft = '.!:;1234567890|abcdefghijklmnopqrstuvwxyz{}()<>~@#$%^&*?,_-+= \/';
		$rgt = 'ghiJVWXyz@#$%^&*?,_-+=1234567890ABCdefGHIjklMNOpqrSTUvwxYZabcDEF';
		$del = ( !is_string( $del ) || strlen( $del ) !== 1 ) ? '~' : $del;
		$dat = ( TRUE === $imp && is_array( $dat ) ) ? implode( $del, $dat ) : $dat;
		$dat = ( $typ === 'en'	) ? strtr( $dat, $lft, $rgt ) : strtr( $dat, $rgt, $lft );
		$dat = ( !empty( $cas ) ) ? WP_SpamShield::casetrans( $cas, $dat ) : $dat;
		$dat = ( TRUE === $exp ) ? explode( $del, $dat ) : $dat;
		return $dat;
	}

	/**
	 *	Set comment field max lengths
	 *	@dependencies	...
	 *	@since			1.9.9.9.4
	 */
	static public function set_comment_fields_max_lengths( $lengths ) {
		$lengths['comment_content'] = 15 * KB_IN_BYTES;
		return $lengths;
	}

	/**
	 *	Undisguised User-Agents commonly used by script kiddies to attack/spam WordPress.
	 *	There is no reason for a human or Trackback/Pingback/XML-RPC to use one of these UA strings.
	 *	@dependencies	rs_wpss_get_user_agent(), rs_wpss_get_browser(), rs_wpss_is_doing_rest(), rs_wpss_is_xmlrpc(), rs_wpss_count_words(), 
	 *	@used by		rs_wpss_precheck_pingback_spam(), rs_wpss_trackback_content_filter(), rs_wpss_comment_content_filter(), WPSS_Filters::bad_robot_blacklist_chk(), WPSS_Security::early_post_intercept(), 
	 *	@since			1.7.9 as rs_wpss_skiddie_ua_check()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function skiddie_ua_check( $user_agent_lc = NULL, $type = NULL ) {
		global $wpss_skiddie_ua; if( isset( $wpss_skiddie_ua ) && is_bool( $wpss_skiddie_ua ) ) { return $wpss_skiddie_ua; }
		$wpss_skiddie_ua = FALSE;
		if( empty( $user_agent_lc ) ) { $user_agent_lc = rs_wpss_get_user_agent( TRUE, TRUE ); }
		if( empty( $user_agent_lc ) ) { $wpss_skiddie_ua = TRUE; return TRUE; }

		if( $type === 'pingback' && 0 !== strpos( $user_agent_lc, 'the incutio xml-rpc php library -- wordpress/' ) ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; $wpss_skiddie_ua = TRUE; return TRUE; }

		/* The Short List */
		if( parent::preg_match( "~(anonymous|bandit|col+ector|copier|download|exploit|extractor|f[uv]ck|grab+er|h[a4]ck|madfox|of+line|phantomjs|scan+er|script|siphon|slimerjs|strip+er|sucker|wordpress\s*hash\s*grab+er|zgrab/|mobilesafari/[0-9]+\.[0-9]+\s+cfnetwork/[0-9]+\.[0-9]+\.[0-9]+\s+darwin/[0-9]+(\.[0-9]+)*|^mozilla\s*[1-4](\.[0-9]+)?|firefox/([1-2]?[0-9]|3[0-9]|x)(\.[x0-9]+)+|chrome/([1-2]?[0-9]|3[0-9]|x)(\.[x0-9]+)+|msie\s*[1-9](\.[0-9]+)?;|windows\s*(nt\s*5\.0|9[58]|2000)|(\s*|/)20(0[0-9])[0-9]{4}|^mozilla/[1-5]\.[0-9]+\s*\(compatible;\s*msie\s*[1-9](\.[0-9]+)?;\s*win(dows)?\s*(nt\s*[0-5]\.[0-9]+|xp|me|9[0-9a-z]+|2000)|windows\s*nt.*macintosh|chrome/.*firefox/|mozilla/5\.0.*mozilla/5\.0|^\}|\<\/?script([\s]+)?\>|\<\?(.*)\?\>)~i", $user_agent_lc ) || ( !rs_wpss_is_doing_rest() && parent::preg_match( "~(^php/|clshttp|curl|fetch|htdig|http_get_vars|http\-?client|httplib|httrack|iopus\-|jakarta|java|larbin|libcurl|libweb|libwww|lwp(\-trivial|\:\:simple)|mechanize|nutch|phpcrawl|pycurl|python|ruby|synapse|wget|winhttprequest)~i", $user_agent_lc ) ) ) {
			$_SERVER['WPSS_SEC_THREAT'] = $wpss_skiddie_ua = TRUE; return TRUE;
		}
		/* TO DO: Add Long List */

		if( 'pingback' === $type || 'trackback' === $type ) {
			global $is_chrome, $is_waterfox, $is_firefox, $is_iphone, $is_macIE, $is_winIE, $is_IE, $is_edge, $is_gecko, $is_opera, $is_safari, $is_lynx, $is_NS4;
			rs_wpss_get_browser();
			if( wp_is_mobile() || $is_waterfox || $is_firefox || $is_chrome || $is_IE || $is_gecko || $is_opera || $is_safari || $is_iphone || $is_lynx || $is_NS4 ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; $wpss_skiddie_ua = TRUE; return TRUE; }
		}

		if( FALSE === strpos( $user_agent_lc, 'wp-android/' ) && FALSE === strpos( $user_agent_lc, 'wp-iphone/' ) && rs_wpss_is_xmlrpc() ) {
			if( parent::preg_match( "~\b(chrome|firefox|internet\s*explorer|iphone|lynx|midori|mozilla|netscape|safari|trident|waterfox|webkit)\b~i", $user_agent_lc ) ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; $wpss_skiddie_ua = TRUE; return TRUE; }
		}

		if( (
			$type === 'comment' || $type === 'contact' || $type === 'register' || $type === 'password reset' || $type === 'guestbook form' 
			||
			$type === 'contact form 7' || $type === 'gravity forms' || $type === 'jetpack form' || $type === 'ninja forms' || $type === 'mailchimp form' 
		) && !rs_wpss_is_doing_rest() && !rs_wpss_is_xmlrpc() ) {
			$user_agent_word_count = rs_wpss_count_words( $user_agent_lc );
			if( !empty( $user_agent_word_count ) && $user_agent_word_count < 3 ) { $_SERVER['WPSS_SEC_THREAT'] = TRUE; $wpss_skiddie_ua = TRUE; return TRUE; }
		}

		return $wpss_skiddie_ua;
	}

	/**
	 *	Social Media URL Check
	 *	To prevent author url and anchor text links to spam social media profiles
	 *	@dependencies	WPSS_Filters::get_blacklist_data(), WPSS_BL::sm_domain(), WPSS_BL::sm_ext_domain(), rs_wpss_get_domain(), rs_wpss_preg_quote(), rs_wpss_fix_url(), WPSS_Filters::get_rgx_ptrn(), 
	 *	@used by		WPSS_Filters::at_link_spam_url_chk(), 
	 *	@since			1.3.8 as rs_wpss_social_media_url_chk()
	 *	@modified		1.5.0	Added @param $comment_type for trackbacks
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function social_media_url_chk( $url = NULL, $comment_type = NULL ) {
		$social_media_domains = rs_wpss_rbkmd( WPSS_BL::sm_domain(), 'de', TRUE );
		$social_media_domains_ext = rs_wpss_rbkmd( WPSS_BL::sm_ext_domain(), 'de', TRUE );
		/* Goes after array */
		$blacklist_status = FALSE;
		if( empty( $url ) || !is_string( $url ) || FALSE !== strpos( $url, WPSS_SITE_DOMAIN ) ) { return FALSE; }
		if( $comment_type === 'trackback' ) { $social_media_domains = $social_media_domains_ext; }
		$domain = rs_wpss_get_domain( $url );
		$domain_rgx = rs_wpss_preg_quote( $domain );
		$url = rs_wpss_fix_url( $url );
		/* See if link points to domain root (ie. facebook.com) */
		if( $comment_type !== 'trackback' && parent::preg_match( "~^https?\://".$domain_rgx."/?$~iu", $url ) ) { return FALSE; }
		$regex_phrase = self::get_rgx_ptrn( $social_media_domains, '', 'domain' );
		if( parent::preg_match( $regex_phrase, $domain ) ) { return TRUE; }
		/* When $regex_phrase exceeds a certain size, switch this to run smaller groups or run each domain individually */
		return $blacklist_status;
	}

	/**
	 *	Check for domains that exist only for spammy, low-quality SEO
	 *	Excessive keyword-stuffing w/hyphens
	 *	@dependencies	WP_SpamShield::casetrans(), rs_wpss_strlen(), rs_wpss_substr_count(), WP_SpamShield::get_ip_addr(), WPSS_Utils::get_ipv4_dcba(), WPSS_Filters::get_blacklist_data(), WPSS_BL::tld(), WPSS_Filters::get_rgx_ptrn(), 
	 *	@used by		WPSS_Filters::domain_blacklist_chk(), 
	 *	@since			1.9.9.8.1 as rs_wpss_spammy_domain_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function spammy_domain_chk( $domain ) {
		if( empty( $domain ) || !is_string( $domain ) || WPSS_SITE_DOMAIN === $domain ) { return FALSE; }
		$domain		= WPSS_Func::lower( $domain );
		$dom_len	= rs_wpss_strlen( $domain );
		$hyphens	= rs_wpss_substr_count( $domain, '-' );
		$ip			= WP_SpamShield::get_ip_addr();
		$ip_dcba	= @WPSS_Utils::get_ipv4_dcba( $ip );
		$ip_rev		= strrev( $ip );
		$ip_str		= str_replace( array( '.', '::', ':' ), '-', $ip );
		$dom_rgx	= "~^([01a]\-|(?:1\-)?(?:8(?:00|44|55|66|77|88)\-))[^-]+~i";
		if( $hyphens >= 2 && FALSE === strpos( $domain, '--' ) && FALSE === strpos( $domain, $ip_dcba ) && FALSE === strpos( $domain, $ip ) && FALSE === strpos( $domain, $ip_rev ) && !parent::preg_match( "~[x\.\-](a?dsl|broadband|cable(modem)?|dhcp([x\.\-]dyn(amic)?)?|dial(up)?|mobile|pool|cluster|defen[cs]e)[x\.\-]~i", $domain ) && ! @WPSS_Utils::substr_ipv4_blocks( $ip, $domain ) ) {
			if( parent::preg_match( $dom_rgx, $domain ) ) { return TRUE; }
			$dom_els = explode( '.', $domain ); /* Domain elements */
			foreach( $dom_els as $i => $e ) {
				$l = rs_wpss_strlen( $e );
				if( $l < 17 || FALSE === strpos( $e, '-' ) ) { continue; }
				if( FALSE !== strpos( $e, $ip_str ) ) { continue; }
				if( !parent::preg_match( "~[a-z]+~i", $e ) ) { continue; }
				$h = rs_wpss_substr_count( $e, '-' );
				$p = 100 * ( $h / $l );
				if( $p >= 11 ) { return TRUE; }
			}
		}
		/* Spam TLDs Based on Statistical Data */
		$blacklisted_tlds = rs_wpss_rbkmd( WPSS_BL::tld(), 'de', TRUE );
		$regex_check_phrase = self::get_rgx_ptrn( implode( '|', $blacklisted_tlds ), '', 'tld' );
		if( parent::preg_match( $regex_check_phrase, $domain ) ) { return TRUE; }
		return FALSE;
	}

	/**
	 *	URL Shortener Blacklist Check
	 *	Dangerous because users have no idea what website they are clicking through to
	 *	Modified in 1.5 - Added @param $comment_type for trackbacks
	 *	@dependencies	WPSS_Filters::get_blacklist_data(), WPSS_BL::urlshort(), rs_wpss_fix_url(), rs_wpss_get_domain(), rs_wpss_preg_quote(), WPSS_Filters::get_rgx_ptrn(), 
	 *	@used by		rs_wpss_contact_form(), WPSS_Filters::at_link_spam_url_chk(), WPSS_Filters::cf_link_spam_url_chk(), 
	 *	@since			... as rs_wpss_urlshort_blacklist_chk()
	 *	@moved			1.9.9.8.2 to WPSS_Filters class
	 */
	static public function urlshort_blacklist_chk( $url = NULL, $email_domain = NULL, $comment_type = NULL  ) {
		$url_shorteners = rs_wpss_rbkmd( WPSS_BL::urlshort(), 'de', TRUE );
		/* Goes after array */
		$blacklist_status = FALSE;
		if( empty( $url ) || !is_string( $url ) || FALSE !== strpos( $url, WPSS_SITE_DOMAIN ) ) { return FALSE; }
		$url = rs_wpss_fix_url( $url );
		$domain = rs_wpss_get_domain( $url );
		if( empty( $domain ) ) { return FALSE; }
		$domain_rgx = rs_wpss_preg_quote( $domain );
		/* See if link points to domain root or legit corporate page (ie. bitly.com) */
		if( $comment_type !== 'trackback' && parent::preg_match( "~^https?\://".$domain_rgx."(/|.*([a-z0-9\-]+/[a-z0-9\-]{4,}.*|[a-z0-9\-]{4,}\.[a-z]{3,4}))?$~iu", $url ) ) { return FALSE; }
		/* Shortened URL check begins */
		$regex_phrase = self::get_rgx_ptrn( $url_shorteners, '', 'domain' );
		/* Consider adding regex for 2-letter domains with 2-letter extensions ( "aa.xx" ) */
		if( $email_domain !== $domain && parent::preg_match( $regex_phrase, $domain ) ) { return TRUE; }
		return $blacklist_status;
	}

}



class WPSS_Promo_Links {

	static public function promo_text( $int ) {
		/**
		 *  Text to display in counter widget and promo links.
		 *  Also used in link title attribute.
		 */
		$promo_txt = array(
			/* Showing key numbers to avoid having to count manually when implementing */
			0 	=> __( 'SPAM BLOCKED', 'wp-spamshield' ),
			1	=> __( 'BY WP-SPAMSHIELD', 'wp-spamshield' ),
			2	=> 'WP-SpamShield ' . __( 'Spam Plugin for WordPress', 'wp-spamshield' ),
			3	=> 'WP-SpamShield ' . __( 'WordPress Anti-Spam Plugin', 'wp-spamshield' ),
			4	=> 'WP-SpamShield - ' . __( 'WordPress Spam Filter', 'wp-spamshield' ),
			5	=> 'WP-SpamShield - ' . __( 'WordPress Anti-Spam', 'wp-spamshield' ),
			6	=> __( 'Spam Blocked by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Anti-Spam Plugin', 'wp-spamshield' ),
			7	=> __( 'Spam Blocked by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Spam Plugin', 'wp-spamshield' ),
			8	=> __( 'Spam Blocked by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Spam Blocker', 'wp-spamshield' ),
			9	=> __( 'Protected by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Spam Filter', 'wp-spamshield' ),
			10	=> __( 'Protected by WP-SpamShield', 'wp-spamshield' ) . ' - ' . __( 'WordPress Anti Spam Plugin', 'wp-spamshield' ),
			11	=> 'WP-SpamShield - ' . __( 'WordPress Anti-Spam Plugin', 'wp-spamshield' ),
			12	=> 'WP-SpamShield - ' . __( 'WordPress Anti Spam Plugin', 'wp-spamshield' ),
			);
		return $promo_txt[$int];
	}

	static public function contact_promo_link( $int ) {
		/* Promo link for contact page if user opts in */
		$cf_title = __( 'WP-SpamShield Contact Form for WordPress', 'wp-spamshield' );
		$cf_txt = __( 'Contact Form', 'wp-spamshield' );
		$url = self::promo_link_url();
		if( empty( $url ) ) { $url = WPSS_HOME_URL; }
		$link_template = '<a href="'.$url.'" title="'.$cf_title.'" >X1X2X</a>';
		$promo_link_data = array(
			/* Showing key numbers to avoid having to count manually when implementing */
			0	=> __( 'Contact Form Powered by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Contact Form', 'wp-spamshield' ),
			1	=> __( 'Powered by WP-SpamShield Contact Form', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Contact Form', 'wp-spamshield' ),
			2	=> __( 'Contact Form Powered by WP-SpamShield', 'wp-spamshield' ) . '|' . 'WP-SpamShield',
			);
		$promo_link_arr = explode( '|', $promo_link_data[$int] );
		if( !rs_wpss_is_lang_en_us() ) {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[0], $link_template );
			$promo_link_phrase = $promo_link_ahref;
		} else {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[1], $link_template );
			$promo_link_phrase = str_replace( $promo_link_arr[1], $promo_link_ahref, $promo_link_arr[0] );
		}
		$promo_link_html = '<p style="font-size:9px;clear:both;">'.$promo_link_phrase.'</p>';
		return $promo_link_html;
	}

	static public function comment_promo_link( $int ) {
		/* Promo link for comments box if user opts in */
		$comment_title = __( 'WP-SpamShield WordPress Anti-Spam Plugin', 'wp-spamshield' );
		$url = self::promo_link_url();
		if( empty( $url ) ) { $url = WPSS_HOME_URL; }
		$link_template = '<a href="'.$url.'" title="'.$comment_title.'" >X1X2X</a>';
		$promo_link_data = array(
			/* Showing key numbers to avoid having to count manually when implementing */
			0	=> __( 'WordPress Anti-Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'WordPress Anti-Spam', 'wp-spamshield' ),
			1	=> __( 'WordPress Anti-Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . 'WP-SpamShield',
			2	=> __( 'WordPress Anti Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'WordPress Anti Spam', 'wp-spamshield' ),
			3	=> __( 'Comments Protected by WP-SpamShield Anti-Spam', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Anti-Spam', 'wp-spamshield' ),
			4	=> __( 'Comments Protected by WP-SpamShield Spam Plugin', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Spam Plugin', 'wp-spamshield' ),
			5	=> __( 'Comments Protected by WP-SpamShield Spam Filter', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Spam Filter', 'wp-spamshield' ),
			6	=> __( 'Comments Protected by WP-SpamShield Spam Blocker', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield Spam Blocker', 'wp-spamshield' ),
			7	=> __( 'Comments Protected by WP-SpamShield for WordPress', 'wp-spamshield' ) . '|' . __( 'WP-SpamShield for WordPress', 'wp-spamshield' ),
			8	=> __( 'Spam Blocking by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Spam Blocking', 'wp-spamshield' ),
			9	=> __( 'Anti-Spam by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Anti-Spam', 'wp-spamshield' ),
			10	=> __( 'Comment Spam Blocking by WP-SpamShield', 'wp-spamshield' ) . '|' . __( 'Comment Spam Blocking', 'wp-spamshield' ),
			);
		$promo_link_arr = explode( '|', $promo_link_data[$int] );
		if( !rs_wpss_is_lang_en_us() ) {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[0], $link_template );
			$promo_link_phrase = $promo_link_ahref;
		} else {
			$promo_link_ahref = str_replace( 'X1X2X', $promo_link_arr[1], $link_template );
			$promo_link_phrase = str_replace( $promo_link_arr[1], $promo_link_ahref, $promo_link_arr[0] );
		}
		$promo_link_html = '<p style="font-size:9px;clear:both;">'.$promo_link_phrase.'</p>';
		return $promo_link_html;
	}

	static public function promo_link_url() {
		/**
		 *  In the plugin promo links, sometimes use plugin homepage link, sometime use WP.org link to make sure both get visited
		 *  4 to 1 plugin homepage to WP.org (which more people link to anyway)
		 */
		$sip5c = '0';
		$sip5c = substr(WPSS_SERVER_ADDR, 4, 1); /* Server IP 5th Char */
		$gplu_code = array( '0' => 0, '1' => 1, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 1, '.' => 0 );
		$urls = array( WPSS_HOME_URL, WPSS_WP_URL );
		if( WP_SpamShield::preg_match( "~^[0-9\.]$~", $sip5c ) ) { $k = $gplu_code[$sip5c]; } else { $k = 0; }
		$url = $urls[$k];
		if( empty( $url ) ) { $url = WPSS_HOME_URL; }
		return $url;
	}

}



/**
 *  Deprecata
 */

function rs_wpss_append_log_data( $str = NULL, $rsds_only = FALSE, $spec_func = NULL ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::append_log_data()' );
	if( FALSE === WP_DEBUG || FALSE === WPSS_DEBUG ) { return FALSE; }
	@WP_SpamShield::append_log_data( NULL, NULL, $str ); return TRUE;
}

function rs_wpss_casetrans( $type, $str ) {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WP_SpamShield::casetrans()' );
	return WP_SpamShield::casetrans( $type, $str );
}

function rs_wpss_ds() {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WP_SpamShield::ds()' );
	return WP_SpamShield::ds();
}

function rs_wpss_eol() {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WP_SpamShield::eol()' );
	return WP_SpamShield::eol();
}

function rs_wpss_filter_null( $str ) {
	_deprecated_function( __FUNCTION__, '1.9.15', 'WP_SpamShield::filter_null()' );
	return WP_SpamShield::filter_null( $str );
}

function rs_wpss_format_bytes( $size, $precision = 2 ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::format_bytes()' );
	return WP_SpamShield::format_bytes( $size, $precision );
}

function rs_wpss_get_http_status( $url = NULL ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.8', 'WP_SpamShield::get_headers()' );
	return WP_SpamShield::get_headers( $url, 'all' );
}

function rs_wpss_get_ip_addr() {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::get_ip_addr()' );
	return WP_SpamShield::get_ip_addr();
}

function rs_wpss_get_real_ip() {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::get_ip_addr()' );
	return WP_SpamShield::get_ip_addr();
}

function rs_wpss_get_regex_phrase( $input, $custom_delim = NULL, $flag = "N" ) {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WPSS_Filters::get_rgx_ptrn()' );
	return WPSS_Filters::get_rgx_ptrn( $input, $custom_delim, $flag );
}

function rs_wpss_get_reverse_dns_ip( $domain ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.8', 'rs_wpss_get_forward_dns()' );
	return rs_wpss_get_forward_dns( $domain );
}

function rs_wpss_get_url( $safe = FALSE ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.6', 'WP_SpamShield::get_url()' );
	return WP_SpamShield::get_url( $safe );
}

function rs_wpss_get_web_host() {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::get_web_host()' );
	return WP_SpamShield::get_web_host();
}

function rs_wpss_is_404( $get_header = FALSE ) {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WP_SpamShield::is_404()' );
	return WP_SpamShield::is_404( $get_header );
}

function rs_wpss_is_google_ip( $ip ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.7', 'WP_SpamShield::is_google_ip()' );
	return WP_SpamShield::is_google_ip( $ip );
}

function rs_wpss_is_https() {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::is_https()' );
	return WP_SpamShield::is_https();
}

function rs_wpss_is_opera_ip( $ip ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.7', 'WP_SpamShield::is_opera_ip()' );
	return WP_SpamShield::is_opera_ip( $ip );
}

function rs_wpss_is_php_ver( $ver ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::is_php_ver()' );
	return WP_SpamShield::is_php_ver( $ver );
}

function rs_wpss_is_ssl() {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::is_https()' );
	return WP_SpamShield::is_https();
}

function rs_wpss_is_valid_ip( $ip, $incl_priv_res = FALSE, $ipv4_c_block = FALSE ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::is_valid_ip()' );
	return WP_SpamShield::is_valid_ip( $ip, $incl_priv_res, $ipv4_c_block );
}

function rs_wpss_is_wp_ver( $ver ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::is_wp_ver()' );
	return WP_SpamShield::is_wp_ver( $ver );
}

function rs_wpss_json_encode( $data, $options = 0, $depth = 512 ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WPSS_PHP::json_encode()' );
    return WPSS_PHP::json_encode( $data, $options, $depth );
}

function rs_wpss_parse_url( $url ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::parse_url()' );
    return WP_SpamShield::parse_url( $url );
}

function rs_wpss_ps() {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WP_SpamShield::ps()' );
	return WP_SpamShield::ps();
}

function rs_wpss_sort_unique( $arr = array() ) {
	_deprecated_function( __FUNCTION__, '1.9.9.9.4', 'WP_SpamShield::sort_unique()' );
	return WP_SpamShield::sort_unique( $arr );
}

function rs_wpss_sanitize_ip( $ip_in ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::sanitize_ip()' );
	return WP_SpamShield::sanitize_ip( $ip_in );
}

function rs_wpss_sanitize_opt_string( $str ) {
	_deprecated_function( __FUNCTION__, '1.9.15', 'WP_SpamShield::sanitize_opt_string()' );
	return WP_SpamShield::sanitize_opt_string( $str );
}

function rs_wpss_sanitize_string( $str ) {
	_deprecated_function( __FUNCTION__, '1.9.15', 'WP_SpamShield::sanitize_string()' );
	return WP_SpamShield::sanitize_string( $str );
}

function rs_wpss_sanitize_textarea( $str ) {
	_deprecated_function( __FUNCTION__, '1.9.15', 'WP_SpamShield::sanitize_textarea()' );
	return WP_SpamShield::sanitize_textarea( $ip_in );
}

function rs_wpss_wp_memory_used( $peak = FALSE, $raw = FALSE ) {
	_deprecated_function( __FUNCTION__, '1.9.9.8.2', 'WP_SpamShield::wp_memory_used()' );
    return WP_SpamShield::wp_memory_used( $peak, $raw );
}



/**
 *	Get things started...
 */
$WP_SpamShield = new WP_SpamShield();



/* PLUGIN - END */
