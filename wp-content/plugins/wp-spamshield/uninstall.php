<?php
/**
 *  WP-SpamShield - uninstall.php
 *  Version: 1.9.9.8.8
 *
 *  This script uninstalls WP-SpamShield and removes all options and traces of its existence.
 */

if( !defined( 'ABSPATH' ) || !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	if( !headers_sent() ) { @header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden',TRUE,403); @header('X-Robots-Tag: noindex',TRUE); }
	die( 'ERROR: This plugin requires WordPress and will not function if called directly.' );
}

if( !defined( 'WPSS_DEBUG' ) )	 				{ define( 'WPSS_DEBUG', FALSE ); }
if( !defined( 'WPSS_CONTENT_DIR_PATH' ) ) 		{ define( 'WPSS_CONTENT_DIR_PATH', WP_CONTENT_DIR ); }
if( !defined( 'WPSS_PLUGIN_PATH' ) ) 			{ define( 'WPSS_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) ); }
if( !defined( 'WPSS_DS' ) ) 					{ $wpss_ds	= ( defined( 'DIRECTORY_SEPARATOR' ) && ( '/' === DIRECTORY_SEPARATOR || '\\' === DIRECTORY_SEPARATOR ) ) ? DIRECTORY_SEPARATOR : rs_wpss_un_ds(); define( 'WPSS_DS', $wpss_ds ); }


function rs_wpss_uninstall_plugin() {
	/* Delete Options */
	$del_options = array( 'wp_spamshield_version', 'spamshield_options', 'spamshield_widget_settings', 'spamshield_last_admin', 'spamshield_admins', 'spamshield_admin_notices', 'spamshield_count', 'spamshield_reg_count', 'spamshield_procdat', 'spamshield_install_status', 'spamshield_warning_status', 'spamshield_regalert_status', 'spamshield_nonces', 'spamshield_wpssmid_cache', 'spamshield_ubl_cache', 'spamshield_ubl_cache_disable', 'spamshield_ip_ban_disable', 'spamshield_ip_ban', 'spamshield_whitelist_keys', 'ak_count_pre', 'spamshield_init_user_approve_run' );
	foreach( $del_options as $i => $option ) { delete_option( $option ); } /* TO DO: When Network Activation enabled, add Multisite - delete_site_option() */
	/* Delete Transients */
	$del_trans = array( 'wpss_iswpv_check', );
	foreach( $del_trans as $i => $transient ) { delete_transient( $transient ); delete_site_transient( $transient ); }
	/* Unregister Widgets */
	$unreg_widgets = array( 'WP_SpamShield_Counter_LG', 'WP_SpamShield_Counter_CG', 'WP_SpamShield_End_Blog_Spam' );
	foreach( $unreg_widgets as $i => $widget ) { unregister_widget( $widget ); }
	/* Clean Up Widget Options */
	$all_widgets = get_option('sidebars_widgets');
	foreach( $all_widgets as $i => $s ) {
		if( is_array( $s ) ) {
			foreach( $s as $k => $v ) {
				if( FALSE !== strpos( $v, 'spamshield' ) ) { unset( $all_widgets[$i][$k] ); }
			}
			$all_widgets[$i] = array_values( $all_widgets[$i] );
		}
	}
	update_option( 'sidebars_widgets', $all_widgets );
	/* Delete Orphaned Options */
	$all_options = wp_load_alloptions();
	foreach( $all_options  as $option => $value ) { 
		if( FALSE !== strpos( $option, 'spamshield' ) ) { delete_option( $option ); } /* TO DO: When Network Activation enabled, add Multisite - delete_site_option() */
	}
	/* Delete User Meta */
	$del_user_meta = array( 'wpss_user_ip', 'wpss_admin_status', 'wpss_new_user_approved', 'wpss_new_user_email_sent', 'wpss_cpn_status', 'wpss_cpn_notices', 'wpss_nag_status', 'wpss_nag_notices', );
	$user_ids = get_users( array( 'blog_id' => '', 'fields' => 'ID' ) );
	foreach ( $user_ids as $user_id ) { foreach( $del_user_meta as $i => $key ) { delete_user_meta( $user_id, $key ); } }
	/* Clear Banned IP Info */
	rs_wpss_uninstall_ip_ban_htaccess();
}

function rs_wpss_un_ds() {
	global $is_IIS; return !empty( $is_IIS ) ? '\\' : '/';
}

function rs_wpss_uninstall_ip_ban_htaccess() {
	/**
	 *  Clear banned IP info from .htaccess during uninstall.
	 */
	$hta_bak_dir		= WPSS_CONTENT_DIR_PATH.WPSS_DS.'backup';
	$hta_wpss_bak_dir	= $hta_bak_dir.WPSS_DS.'wp-spamshield';
	$hta_file			= ABSPATH.WPSS_DS.'.htaccess';
	$hta_bak_file		= $hta_wpss_bak_dir.WPSS_DS.'original.htaccess';
	$wpss_index_file	= WPSS_PLUGIN_PATH.WPSS_DS.'index.php';
	$bak_dir_hta_file	= WPSS_PLUGIN_PATH.WPSS_DS.'lib'.WPSS_DS.'sec'.WPSS_DS.'.htaccess';
	$wpss_dirs			= array( $hta_wpss_bak_dir.WPSS_DS.'' );

	foreach( $wpss_dirs as $d => $dir ) {
		if( @is_dir( $wpss_dirs[$d] ) ) {
			$filelist = rs_wpss_un_scandir( $wpss_dirs[$d] );
			foreach( $filelist as $f => $filename ) {
				$file = $wpss_dirs[$d].$filename;
				if( @is_file( $file ) ){
					rs_wpss_un_chmod( $file, 775 ); @unlink( $file );
					if( @file_exists( $file ) ) { rs_wpss_un_chmod( $file, 644 ); @unlink( $file ); }
				}
			}
			rs_wpss_un_chmod( $wpss_dirs[$d], 775 ); @rmdir( $wpss_dirs[$d] );
			if( @file_exists( $wpss_dirs[$d] ) ) { rs_wpss_un_chmod( $wpss_dirs[$d], 755 ); @rmdir( $wpss_dirs[$d] ); }
		}
	}

	$wpss_files = array( $hta_bak_dir.WPSS_DS.'.htaccess', $hta_bak_dir.WPSS_DS.'index.php' );

	foreach( $wpss_files as $f => $file ) {
		if( @is_file( $file ) ){
			rs_wpss_un_chmod( $file, 775 ); @unlink( $file );
			if( @file_exists( $file ) ) { rs_wpss_un_chmod( $file, 644 ); @unlink( $file ); }
		}
	}

	$hta_contents = @file_get_contents( $hta_file );
	if( FALSE !== strpos( $hta_contents, '# BEGIN WP-SpamShield' ) && FALSE !== strpos( $hta_contents, '# END WP-SpamShield' ) ) {
		$hta_contents_mod = @preg_replace( "~".PHP_EOL."#\ BEGIN\ WP-SpamShield[\w\W]+#\ END\ WP-SpamShield".PHP_EOL."~i", '', $hta_contents );
		if( $hta_contents_mod !== $hta_contents ) {
			@file_put_contents( $hta_file, $hta_contents_mod, LOCK_EX );
		}
	}
}

function rs_wpss_un_scandir( $dir ) {
	if( empty( $dir ) || ! @is_string( $dir ) ) { return $dir; }
	@clearstatcache();
	$dot_files	= array( '..', '.' );
	$dir_listr	= (array) @scandir( $dir );
	$dir_list	= array_values( array_diff( $dir_listr, $dot_files ) );
	return $dir_list;
}

function rs_wpss_un_chmod( $file, $mode ) {
	@chmod( $file, octdec( $mode ) );
}

function rs_wpss_un_filter_null( $str ) {
	return str_replace( chr(0), '', $str );
}

function rs_wpss_un_append_log_data( $str = NULL, $rsds_only = FALSE ) {
	if ( TRUE === WP_DEBUG && TRUE === WPSS_DEBUG ) {
		$wpss_log_str = 'WP-SpamShield UN DEBUG: ';
		@error_log( rs_wpss_un_filter_null( $wpss_log_str ), 0 );
	}
}

rs_wpss_uninstall_plugin();

/**
 *  "...Then it's time I disappear..."
 */
